function billingApp() {
    return {
        // form state
        summaryName: "",
        departmentName: "",
        startDate: "",
        endDate: "",
        // expanded global rates (defaults kept/approximated from your prior values)
        rates: {
            reg_hr: 73.38,
            reg_ot: 73.78,
            np: 5.90,
            np_ot: 13.24,
            rest_rate: 120.00,
            rest_ot: 150.00,
            rest_np: 10.00,
            rest_np_ot: 15.00,
            reg_hol: 59.02,
            reg_hol_ot: 118.04,
            reg_hol_np: 20.00,
            reg_hol_np_ot: 40.00,
            unworked_reg: 0.00,
            spec_hol: 91.09
        },
        labels: {
            reg_hr: "Reg Hr Rate",
            reg_ot: "Reg OT Rate",
            np: "NP Rate",
            np_ot: "NP OT Rate",
            rest_rate: "Rest/Sun/SpecHol Rate",
            rest_ot: "Rest OT",
            rest_np: "Rest NP",
            rest_np_ot: "Rest NP OT",
            reg_hol: "Reg Hol Rate",
            reg_hol_ot: "Reg Hol OT",
            reg_hol_np: "Reg Hol NP",
            reg_hol_np_ot: "Reg Hol NP OT",
            unworked_reg: "Unworked Reg Day Rate",
            spec_hol: "Spec Hol Rate"
        },

        // employees list (each employee object may include `id` or `name`)
        employees: [],

        // optional external attendance records (user-provided structure option 1)
        // expected rows: { date: 'YYYY-MM-DD', employee_id: 123, name: 'John', hours: 8, type: 'work' }
        attendanceRecords: [],

        // days metadata: each entry is an object { type: 'work'|'reg_hol'|'spec_hol'|'np'|'hpnp'|'rest', threshold: number }
        daysMeta: [],

        // breakdown result (populated by breakdownByDays())
        breakdown: null,

        // undo/redo history
        history: [],
        redoStack: [],

        // debounce for history save
        debounceTimer: null,

        // for add multiple employees
        employeeToAdd: 1,

        // internal: storage key
        storageKey: 'billingState_v1',

        // ---------- Initialization ----------
        initKeyboard() {
            window.addEventListener('keydown', e => {
                if ((e.ctrlKey || e.metaKey) && e.key === 'z') {
                    e.preventDefault();
                    this.Undo();
                }
                if ((e.ctrlKey || e.metaKey) && e.key === 'y') {
                    e.preventDefault();
                    this.Redo();
                }
            });
        },

        // Load persisted state (if any)
        loadState() {
            try {
                const raw = localStorage.getItem(this.storageKey);
                if (raw) {
                    const parsed = JSON.parse(raw);
                    if (parsed.summaryName != null) this.summaryName = parsed.summaryName;
                    if (parsed.departmentName != null) this.departmentName = parsed.departmentName;
                    if (parsed.startDate != null) this.startDate = parsed.startDate;
                    if (parsed.endDate != null) this.endDate = parsed.endDate;
                    if (parsed.rates != null) this.rates = Object.assign({}, this.rates, parsed.rates);
                    if (Array.isArray(parsed.employees)) this.employees = parsed.employees;
                    if (Array.isArray(parsed.attendanceRecords)) this.attendanceRecords = parsed.attendanceRecords;
                    if (Array.isArray(parsed.history)) this.history = parsed.history;
                    if (Array.isArray(parsed.redoStack)) this.redoStack = parsed.redoStack;
                    if (Array.isArray(parsed.daysMeta)) this.daysMeta = parsed.daysMeta;
                } else {
                    this.persistDefaults();
                }

                // ensure daily arrays match date range length after load
                this.onDateRangeChange();

                // compute initial summaries from daily
                this.recomputeAllSummaries();
            } catch (err) {
                console.error('Failed to load billing state:', err);
            }
        },

        // Save full state to localStorage
        saveState() {
            try {
                const snapshot = {
                    summaryName: this.summaryName,
                    departmentName: this.departmentName,
                    startDate: this.startDate,
                    endDate: this.endDate,
                    rates: this.rates,
                    employees: this.employees,
                    attendanceRecords: this.attendanceRecords,
                    history: this.history,
                    redoStack: this.redoStack,
                    daysMeta: this.daysMeta
                };
                localStorage.setItem(this.storageKey, JSON.stringify(snapshot));
            } catch (err) {
                console.error('Failed to save billing state:', err);
            }
        },

        // set reasonable defaults on first use
        persistDefaults() {
            this.employees = [];
            this.history = [];
            this.redoStack = [];
            this.daysMeta = [];
            this.attendanceRecords = [];
            this.saveState();
        },

        // ---------- History (undo/redo) ----------
        saveHistory() {
            clearTimeout(this.debounceTimer);
            this.debounceTimer = setTimeout(() => {
                this.history.push({
                    employees: JSON.parse(JSON.stringify(this.employees)),
                    rates: JSON.parse(JSON.stringify(this.rates)),
                    attendanceRecords: JSON.parse(JSON.stringify(this.attendanceRecords)),
                    startDate: this.startDate,
                    endDate: this.endDate,
                    summaryName: this.summaryName,
                    departmentName: this.departmentName,
                    daysMeta: JSON.parse(JSON.stringify(this.daysMeta))
                });
                this.redoStack = [];
                this.saveState();
            }, 300);
        },

        Undo() {
            if (this.history.length > 0) {
                this.redoStack.push({
                    employees: JSON.parse(JSON.stringify(this.employees)),
                    rates: JSON.parse(JSON.stringify(this.rates)),
                    attendanceRecords: JSON.parse(JSON.stringify(this.attendanceRecords)),
                    startDate: this.startDate,
                    endDate: this.endDate,
                    summaryName: this.summaryName,
                    departmentName: this.departmentName,
                    daysMeta: JSON.parse(JSON.stringify(this.daysMeta))
                });

                const last = this.history.pop();
                this.employees = last.employees || [];
                this.rates = last.rates || this.rates;
                this.attendanceRecords = last.attendanceRecords || this.attendanceRecords || [];
                this.startDate = last.startDate || "";
                this.endDate = last.endDate || "";
                this.summaryName = last.summaryName || "";
                this.departmentName = last.departmentName || "";
                this.daysMeta = last.daysMeta || [];

                this.onDateRangeChange();
                this.recomputeAllSummaries();
                this.saveState();
            }
        },

        Redo() {
            if (this.redoStack.length > 0) {
                this.history.push({
                    employees: JSON.parse(JSON.stringify(this.employees)),
                    rates: JSON.parse(JSON.stringify(this.rates)),
                    attendanceRecords: JSON.parse(JSON.stringify(this.attendanceRecords)),
                    startDate: this.startDate,
                    endDate: this.endDate,
                    summaryName: this.summaryName,
                    departmentName: this.departmentName,
                    daysMeta: JSON.parse(JSON.stringify(this.daysMeta))
                });

                const next = this.redoStack.pop();

                this.employees = next.employees || [];
                this.rates = next.rates || this.rates;
                this.attendanceRecords = next.attendanceRecords || this.attendanceRecords || [];
                this.startDate = next.startDate || "";
                this.endDate = next.endDate || "";
                this.summaryName = next.summaryName || "";
                this.departmentName = next.departmentName || "";
                this.daysMeta = next.daysMeta || [];

                this.onDateRangeChange();
                this.recomputeAllSummaries();
                this.saveState();
            }
        },

        // ---------- Employees helpers ----------
        addMultipleEmployees() {
            if (this.employeeToAdd > 0) {
                for (let i = 0; i < this.employeeToAdd; i++) {
                    this.addEmployee();
                }
                this.employeeToAdd = 1;
            }
        },

        // Called when start or end dates change — updates days range and ensures employee daily arrays align.
        onDateRangeChange() {
            if (!this.startDate || !this.endDate) {
                this.daysMeta = [];
                this.employees.forEach(emp => {
                    emp.daily = [];
                    emp.dayOverrides = [];
                    emp.dayThresholds = [];
                });
                return;
            }

            const start = new Date(this.startDate);
            const end = new Date(this.endDate);
            if (isNaN(start) || isNaN(end) || start > end) {
                this.daysMeta = [];
                this.employees.forEach(emp => {
                    emp.daily = [];
                    emp.dayOverrides = [];
                    emp.dayThresholds = [];
                });
                return;
            }

            const days = this.daysRange();
            const newLen = days.length;

            if (!Array.isArray(this.daysMeta)) this.daysMeta = [];
            if (this.daysMeta.length < newLen) {
                for (let i = this.daysMeta.length; i < newLen; i++) {
                    this.daysMeta.push({ type: 'work', threshold: 8 });
                }
            } else if (this.daysMeta.length > newLen) {
                this.daysMeta.splice(newLen);
            }

            this.employees.forEach(emp => {
                if (!emp.daily) emp.daily = [];
                if (emp.daily.length < newLen) {
                    for (let k = emp.daily.length; k < newLen; k++) emp.daily.push(0);
                } else if (emp.daily.length > newLen) {
                    emp.daily.splice(newLen);
                }

                if (!Array.isArray(emp.dayOverrides)) emp.dayOverrides = [];
                if (emp.dayOverrides.length < newLen) {
                    for (let k = emp.dayOverrides.length; k < newLen; k++) emp.dayOverrides.push('');
                } else if (emp.dayOverrides.length > newLen) {
                    emp.dayOverrides.splice(newLen);
                }

                if (!Array.isArray(emp.dayThresholds)) emp.dayThresholds = [];
                if (emp.dayThresholds.length < newLen) {
                    for (let k = emp.dayThresholds.length; k < newLen; k++) emp.dayThresholds.push(null);
                } else if (emp.dayThresholds.length > newLen) {
                    emp.dayThresholds.splice(newLen);
                }
            });

            this.recomputeAllSummaries();
            this.saveHistory();
        },

        // 🔹 Generate day range
        daysRange() {
            if (!this.startDate || !this.endDate) return [];
            const start = new Date(this.startDate);
            const end = new Date(this.endDate);
            if (isNaN(start) || isNaN(end) || start > end) return [];
            const days = [];
            let current = new Date(start);
            while (current <= end) {
                // store ISO date string (YYYY-MM-DD) for stable matching and also short label
                const iso = current.toISOString().slice(0, 10);
                const label = current.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                days.push({ iso, label });
                current.setDate(current.getDate() + 1);
            }
            return days;
        },

        // helper to toggleEmpDayThreshold: copy global threshold to employee day threshold or clear it
        toggleEmpDayThreshold(empIndex, dIndex) {
            const emp = this.employees[empIndex];
            if (!emp) return;
            if (!Array.isArray(emp.dayThresholds)) emp.dayThresholds = [];
            if (emp.dayThresholds[dIndex] == null) {
                emp.dayThresholds.splice(dIndex, 1, (this.daysMeta[dIndex] && this.daysMeta[dIndex].threshold) || 8);
            } else {
                emp.dayThresholds.splice(dIndex, 1, null);
            }
            this.saveHistory();
            this.computeEmpSummary(emp);
            this.saveState();
        },

        dayTypeLabel(t) {
            const map = {
                work: 'Work',
                reg_hol: 'Reg Hol',
                spec_hol: 'Spec Hol',
                np: 'NP',
                hpnp: 'HPNP',
                rest: 'Rest/Sunday'
            };
            return map[t] || t || 'Work';
        },

        // return total hours for a specific day index across all employees
        dailyTotal(dIndex) {
            return this.employees.reduce((sum, emp) => {
                const v = Number((emp.daily && emp.daily[dIndex]) || 0);
                return sum + v;
            }, 0);
        },

        // NEW: grand total across all daily columns (sum of every employee's daily sum)
        grandDailyTotal() {
            return this.employees.reduce((sum, emp) => sum + this.sumDaily(emp), 0);
        },

        // sum an employee's daily array
        sumDaily(emp) {
            if (!emp || !emp.daily) return 0;
            return emp.daily.reduce((s, v) => s + (Number(v) || 0), 0);
        },

        // called when a daily input changes
        onDailyInput(emp, dIndex) {
            if (!emp.daily) emp.daily = [];
            if (emp.daily[dIndex] == null) emp.daily[dIndex] = 0;
            if (emp.daily[dIndex] < 0) emp.daily[dIndex] = 0;

            this.computeEmpSummary(emp);

            this.saveHistory();
            this.saveState();
        },

        // ---------- Core calculation helpers ----------
        // get effective numeric rate for a given field for an employee (considers emp.customRates when emp.useCustom is true)
        getEffectiveRate(emp, field) {
            const global = (this.rates[field] != null) ? Number(this.rates[field]) : 0;
            if (emp && emp.useCustom && emp.customRates && (emp.customRates[field] != null && emp.customRates[field] !== '')) {
                return Number(emp.customRates[field]);
            }
            return global;
        },

        // safe round to 2 decimals
        round2(v) {
            return Math.round((Number(v) || 0) * 100) / 100;
        },

        // compute pay for a set of hours segmented by category object { reg: hours, ot: hours, np: hours, ... }
        computePayFromSegments(emp, segments) {
            let total = 0;
            // mapping segment to rate key
            const map = {
                reg: 'reg_hr',
                ot: 'reg_ot',
                np: 'np',
                np_ot: 'np_ot',
                rest: 'rest_rate',
                rest_ot: 'rest_ot',
                rest_np: 'rest_np',
                rest_np_ot: 'rest_np_ot',
                reg_hol: 'reg_hol',
                reg_hol_ot: 'reg_hol_ot',
                reg_hol_np: 'reg_hol_np',
                reg_hol_np_ot: 'reg_hol_np_ot',
                spec_hol: 'spec_hol',
                unworked_reg: 'unworked_reg'
            };
            Object.keys(segments).forEach(seg => {
                const hours = Number(segments[seg] || 0);
                if (hours === 0) return;
                const rateKey = map[seg];
                const rate = this.getEffectiveRate(emp, rateKey);
                total += hours * rate;
            });
            return this.round2(total);
        },

        // Recomputes categories for a single employee from emp.daily & daysMeta
        // (keeps backwards-compatibility with prior computeEmpSummary but clearer and consistent)
        computeEmpSummary(emp) {
            const wasManual = !!emp.manualOverride;

            // ensure fields
            const hourFields = [
                'reg_hr','reg_ot','np','np_ot',
                'rest','rest_ot','rest_np','rest_np_ot',
                'reg_hol','reg_hol_ot','reg_hol_np','reg_hol_np_ot',
                'unworked_reg','spec_hol'
            ];
            hourFields.forEach(f => { emp[f] = Number(emp[f] || 0); });

            if (!wasManual) {
                hourFields.forEach(f => emp[f] = 0);
            }

            const daysCount = this.daysRange().length;
            if (!Array.isArray(emp.daily)) emp.daily = new Array(daysCount).fill(0);
            if (!Array.isArray(emp.dayOverrides)) emp.dayOverrides = new Array(daysCount).fill('');
            if (!Array.isArray(emp.dayThresholds)) emp.dayThresholds = new Array(daysCount).fill(null);

            for (let d = 0; d < daysCount; d++) {
                const hours = Number(emp.daily[d] || 0);
                if (hours <= 0) {
                    // If day has zero hours and day type is 'work' but unworked_reg should be incremented?
                    // We treat unworked_reg only if day type is work and the user expects an unworked marking.
                    const dtypeIfZero = emp.dayOverrides[d] && emp.dayOverrides[d] !== '' ? emp.dayOverrides[d] : (this.daysMeta[d] && this.daysMeta[d].type) || 'work';
                    if (dtypeIfZero === 'work' && (this.daysMeta[d] && this.daysMeta[d].unworked === true)) {
                        emp.unworked_reg += 1; // counting days; user can interpret as hours if desired
                    }
                    continue;
                }

                // day type priority: per-employee override -> global daysMeta
                const dtype = emp.dayOverrides[d] && emp.dayOverrides[d] !== '' ? emp.dayOverrides[d] : (this.daysMeta[d] && this.daysMeta[d].type) || 'work';
                const threshold = (emp.dayThresholds[d] != null) ? Number(emp.dayThresholds[d]) : ((this.daysMeta[d] && this.daysMeta[d].threshold) != null ? Number(this.daysMeta[d].threshold) : 8);

                if (wasManual) continue;

                // split based on dtype
                if (dtype === 'work') {
                    const reg = Math.min(threshold, hours);
                    const ot = Math.max(0, hours - threshold);
                    emp.reg_hr += reg;
                    emp.reg_ot += ot;
                } else if (dtype === 'np') {
                    const reg_np = Math.min(threshold, hours);
                    const ot_np = Math.max(0, hours - threshold);
                    emp.np += reg_np;
                    emp.np_ot += ot_np;
                } else if (dtype === 'hpnp') {
                    // treat hpnp as entirely NP OT (conservative)
                    emp.np_ot += hours;
                } else if (dtype === 'rest' || dtype === 'sunday' || dtype === 'rest_day') {
                    const regRest = Math.min(threshold, hours);
                    const otRest = Math.max(0, hours - threshold);
                    emp.rest += regRest;
                    emp.rest_ot += otRest;
                } else if (dtype === 'reg_hol') {
                    const r = Math.min(threshold, hours);
                    const o = Math.max(0, hours - threshold);
                    emp.reg_hol += r;
                    emp.reg_hol_ot += o;
                } else if (dtype === 'spec_hol') {
                    // spec holiday typically does not split into OT by default in this system
                    emp.spec_hol += hours;
                } else {
                    // fallback treat as work
                    const reg = Math.min(threshold, hours);
                    const ot = Math.max(0, hours - threshold);
                    emp.reg_hr += reg;
                    emp.reg_ot += ot;
                }
            }

            // ensure numeric rounding for all
            hourFields.forEach(f => { emp[f] = this.round2(emp[f]); });
        },

        // Recompute all employees' summaries
        recomputeAllSummaries() {
            this.employees.forEach(emp => {
                this.computeEmpSummary(emp);
            });
            this.saveState();
        },

        // Called when user toggles manual override on/off for an employee
        onManualOverrideToggle(emp) {
            if (!emp.manualOverride) {
                this.computeEmpSummary(emp);
            } else {
                ['reg_hr','reg_ot','np','np_ot','rest','rest_ot','rest_np','rest_np_ot','reg_hol','reg_hol_ot','reg_hol_np','reg_hol_np_ot','unworked_reg','spec_hol'].forEach(f => {
                    emp[f] = Number(emp[f] || 0);
                });
            }
            this.saveHistory();
            this.saveState();
        },

        // Called when user edits fields in the Employees table (previously allowed)
        onEmployeeTableInput(emp, field) {
            if (emp[field] == null) emp[field] = 0;
            if (emp[field] < 0) emp[field] = 0;
            this.saveHistory();
            this.saveState();
        },

        totalHours(emp) {
            return this.round2(
                (emp.reg_hr || 0) +
                (emp.reg_ot || 0) +
                (emp.np || 0) +
                (emp.np_ot || 0) +
                (emp.rest || 0) +
                (emp.rest_ot || 0) +
                (emp.rest_np || 0) +
                (emp.rest_np_ot || 0) +
                (emp.reg_hol || 0) +
                (emp.reg_hol_ot || 0) +
                (emp.reg_hol_np || 0) +
                (emp.reg_hol_np_ot || 0) +
                (emp.spec_hol || 0) +
                (emp.unworked_reg || 0)
            );
        },

        totalPay(emp) {
            // use computePayFromSegments mapping keys used by computeEmpSummary (note: field names for hours like 'rest' map to rate keys)
            const segments = {
                reg: emp.reg_hr || 0,
                ot: emp.reg_ot || 0,
                np: emp.np || 0,
                np_ot: emp.np_ot || 0,
                rest: emp.rest || 0,
                rest_ot: emp.rest_ot || 0,
                rest_np: emp.rest_np || 0,
                rest_np_ot: emp.rest_np_ot || 0,
                reg_hol: emp.reg_hol || 0,
                reg_hol_ot: emp.reg_hol_ot || 0,
                reg_hol_np: emp.reg_hol_np || 0,
                reg_hol_np_ot: emp.reg_hol_np_ot || 0,
                spec_hol: emp.spec_hol || 0,
                unworked_reg: emp.unworked_reg || 0
            };
            return this.computePayFromSegments(emp, segments);
        },

        columnTotal(field) {
            return this.round2(this.employees.reduce((sum, emp) => sum + (Number(emp[field] || 0)), 0));
        },

        categoryTotalPay(field) {
            return this.round2(this.employees.reduce((sum, emp) => {
                const hours = Number(emp[field] || 0);
                const rate = this.getEffectiveRate(emp, field);
                return sum + (hours * rate);
            }, 0));
        },

        grandTotal() {
            return this.round2(this.employees.reduce((sum, emp) => sum + this.totalPay(emp), 0));
        },

        grandTotalHours() {
            return this.round2(this.employees.reduce((sum, emp) => sum + this.totalHours(emp), 0));
        },

        currency(val) {
            return '₱' + (Number(val) || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        },

        addEmployee() {
            const daysCount = this.startDate && this.endDate ? this.daysRange().length : 0;
            const dailyInit = new Array(daysCount).fill(0);
            const customRatesInit = {
                reg_hr: null, reg_ot: null, np: null, np_ot: null,
                rest_rate: null, rest_ot: null, rest_np: null, rest_np_ot: null,
                reg_hol: null, reg_hol_ot: null, reg_hol_np: null, reg_hol_np_ot: null,
                unworked_reg: null, spec_hol: null
            };
            const emp = {
                id: null,
                name: "",
                reg_hr: 0,
                reg_ot: 0,
                np: 0,
                np_ot: 0,
                rest: 0,
                rest_ot: 0,
                rest_np: 0,
                rest_np_ot: 0,
                reg_hol: 0,
                reg_hol_ot: 0,
                reg_hol_np: 0,
                reg_hol_np_ot: 0,
                unworked_reg: 0,
                spec_hol: 0,
                useCustom: false,
                customRates: Object.assign({}, customRatesInit),
                daily: dailyInit,
                manualOverride: false,
                dayOverrides: new Array(daysCount).fill(''),
                dayThresholds: new Array(daysCount).fill(null)
            };
            this.employees.push(emp);
            this.computeEmpSummary(emp);
            this.saveHistory();
            this.saveState();
        },

        deleteEmployee(i) {
            this.employees.splice(i, 1);
            this.saveHistory();
            this.saveState();
        },

        // Reset only non-global-rate inputs. Keeps rates intact.
        resetExceptRates() {
            this.history.push({
                employees: JSON.parse(JSON.stringify(this.employees)),
                rates: JSON.parse(JSON.stringify(this.rates)),
                attendanceRecords: JSON.parse(JSON.stringify(this.attendanceRecords)),
                startDate: this.startDate,
                endDate: this.endDate,
                summaryName: this.summaryName,
                departmentName: this.departmentName,
                daysMeta: JSON.parse(JSON.stringify(this.daysMeta))
            });

            this.summaryName = "";
            this.departmentName = "";
            this.startDate = "";
            this.endDate = "";
            this.employees = [];
            this.daysMeta = [];
            this.attendanceRecords = [];
            this.redoStack = [];
            this.saveState();
        },

        // ---------- Breakdown by Days (Option C) ----------
        // Produces a breakdown object for each day in the range:
        // {
        //   date: 'YYYY-MM-DD', label: 'Oct 1', perEmployee: [{ id?, name, segments:{...}, pay, hours }], totals: { hours, pay }
        // }
        breakdownByDays() {
            const days = this.daysRange(); // returns [{iso, label}, ...]
            if (!days.length) {
                this.breakdown = [];
                return this.breakdown;
            }

            // Preprocess attendanceRecords into a map keyed by iso-date
            const attendanceMap = {};
            if (Array.isArray(this.attendanceRecords) && this.attendanceRecords.length) {
                this.attendanceRecords.forEach(rec => {
                    const iso = (rec.date || '').slice(0, 10);
                    if (!iso) return;
                    if (!attendanceMap[iso]) attendanceMap[iso] = [];
                    attendanceMap[iso].push(rec);
                });
            }

            // Helper to safely get employee matching attendance record
            const matchEmployeeForRecord = (rec) => {
                if (!rec) return null;
                // try match by id first (if both exist)
                if (rec.employee_id != null) {
                    const found = this.employees.find(e => String(e.id) === String(rec.employee_id));
                    if (found) return found;
                }
                // try match by name (case-insensitive)
                if (rec.name) {
                    const found = this.employees.find(e => (e.name || '').toLowerCase() === (rec.name || '').toLowerCase());
                    if (found) return found;
                }
                return null;
            };

            const breakdown = days.map((day, dIndex) => {
                const iso = day.iso;
                const label = day.label;

                // For this date, build per-employee segments
                const perEmployee = [];

                // method A: if attendanceRecords exist for this date, use them
                const recs = attendanceMap[iso] || [];

                if (recs.length > 0) {
                    // produce a map employeeKey -> combined hours by type
                    const empMap = {};
                    recs.forEach(rec => {
                        // expect rec: { date: 'YYYY-MM-DD', employee_id, name, hours, type }
                        const key = rec.employee_id != null ? `id:${rec.employee_id}` : `name:${(rec.name||'').toLowerCase()}`;
                        if (!empMap[key]) empMap[key] = { records: [], emp: matchEmployeeForRecord(rec) };
                        empMap[key].records.push(rec);
                    });

                    Object.keys(empMap).forEach(key => {
                        const entry = empMap[key];
                        const emp = entry.emp || { name: (entry.records[0].name || 'Unknown'), useCustom: false, customRates: {} };
                        // segment hours initialization (keys must match computeEmpSummary segments mapping)
                        const segments = {
                            reg: 0, ot: 0, np: 0, np_ot: 0,
                            rest: 0, rest_ot: 0, rest_np: 0, rest_np_ot: 0,
                            reg_hol: 0, reg_hol_ot: 0, reg_hol_np: 0, reg_hol_np_ot: 0,
                            spec_hol: 0, unworked_reg: 0
                        };

                        // combine multiple records for same employee same date into segments
                        entry.records.forEach(rec => {
                            const hours = Number(rec.hours || 0);
                            const type = rec.type || 'work';
                            // threshold fallback: prefer emp.dayThresholds if present at this dIndex, else daysMeta threshold else 8
                            let threshold = 8;
                            const dayMeta = this.daysMeta[dIndex];
                            if (dayMeta && dayMeta.threshold != null) threshold = Number(dayMeta.threshold);
                            // per-record may provide split info (e.g., rec.split:{reg:4, ot:2}) - support it if present
                            if (rec.split && typeof rec.split === 'object') {
                                Object.keys(rec.split).forEach(k => {
                                    if (segments[k] != null) segments[k] += Number(rec.split[k] || 0);
                                });
                            } else {
                                // no split provided: we decide based on type and threshold
                                if (type === 'work') {
                                    const reg = Math.min(threshold, hours);
                                    const ot = Math.max(0, hours - threshold);
                                    segments.reg += reg;
                                    segments.ot += ot;
                                } else if (type === 'np') {
                                    const reg_np = Math.min(threshold, hours);
                                    const ot_np = Math.max(0, hours - threshold);
                                    segments.np += reg_np;
                                    segments.np_ot += ot_np;
                                } else if (type === 'hpnp') {
                                    segments.np_ot += hours;
                                } else if (type === 'rest' || type === 'sunday' || type === 'rest_day') {
                                    const regR = Math.min(threshold, hours);
                                    const otR = Math.max(0, hours - threshold);
                                    segments.rest += regR;
                                    segments.rest_ot += otR;
                                } else if (type === 'reg_hol') {
                                    const r = Math.min(threshold, hours);
                                    const o = Math.max(0, hours - threshold);
                                    segments.reg_hol += r;
                                    segments.reg_hol_ot += o;
                                } else if (type === 'spec_hol') {
                                    segments.spec_hol += hours;
                                } else {
                                    // fallback
                                    const reg = Math.min(threshold, hours);
                                    const ot = Math.max(0, hours - threshold);
                                    segments.reg += reg;
                                    segments.ot += ot;
                                }
                            }
                        });

                        // compute totals and pay
                        const hours = Object.values(segments).reduce((s, v) => s + (Number(v) || 0), 0);
                        const pay = this.computePayFromSegments(emp, segments);

                        perEmployee.push({
                            id: emp.id || null,
                            name: emp.name || (entry.records[0].name || 'Unknown'),
                            segments: Object.keys(segments).reduce((acc, k) => { acc[k] = this.round2(segments[k]); return acc; }, {}),
                            hours: this.round2(hours),
                            pay: this.round2(pay)
                        });
                    });

                } else {
                    // method B: fallback to emp.daily + daysMeta
                    this.employees.forEach(emp => {
                        const hours = Number((emp.daily && emp.daily[dIndex]) || 0);
                        if (hours <= 0) {
                            // if user wants rows even with zero hours, we still include them with 0 values
                        }
                        const dtype = (emp.dayOverrides && emp.dayOverrides[dIndex]) ? emp.dayOverrides[dIndex] : (this.daysMeta[dIndex] && this.daysMeta[dIndex].type) || 'work';
                        const threshold = (emp.dayThresholds && emp.dayThresholds[dIndex] != null) ? Number(emp.dayThresholds[dIndex]) : ((this.daysMeta[dIndex] && this.daysMeta[dIndex].threshold) != null ? Number(this.daysMeta[dIndex].threshold) : 8);

                        // initialize segments
                        const segments = {
                            reg: 0, ot: 0, np: 0, np_ot: 0,
                            rest: 0, rest_ot: 0, rest_np: 0, rest_np_ot: 0,
                            reg_hol: 0, reg_hol_ot: 0, reg_hol_np: 0, reg_hol_np_ot: 0,
                            spec_hol: 0, unworked_reg: 0
                        };

                        if (hours > 0) {
                            if (dtype === 'work') {
                                const reg = Math.min(threshold, hours);
                                const ot = Math.max(0, hours - threshold);
                                segments.reg = reg;
                                segments.ot = ot;
                            } else if (dtype === 'np') {
                                const rnp = Math.min(threshold, hours);
                                const onp = Math.max(0, hours - threshold);
                                segments.np = rnp;
                                segments.np_ot = onp;
                            } else if (dtype === 'hpnp') {
                                segments.np_ot = hours;
                            } else if (dtype === 'rest' || dtype === 'sunday' || dtype === 'rest_day') {
                                const rr = Math.min(threshold, hours);
                                const ro = Math.max(0, hours - threshold);
                                segments.rest = rr;
                                segments.rest_ot = ro;
                            } else if (dtype === 'reg_hol') {
                                const rh = Math.min(threshold, hours);
                                const rho = Math.max(0, hours - threshold);
                                segments.reg_hol = rh;
                                segments.reg_hol_ot = rho;
                            } else if (dtype === 'spec_hol') {
                                segments.spec_hol = hours;
                            } else {
                                const reg = Math.min(threshold, hours);
                                const ot = Math.max(0, hours - threshold);
                                segments.reg = reg;
                                segments.ot = ot;
                            }
                        } else {
                            // zero hours -> maybe mark unworked_reg if desired
                            if (dtype === 'work' && (this.daysMeta[dIndex] && this.daysMeta[dIndex].unworked === true)) {
                                segments.unworked_reg = 1; // a day count
                            }
                        }

                        const empHours = Object.values(segments).reduce((s, v) => s + (Number(v) || 0), 0);
                        const pay = this.computePayFromSegments(emp, segments);

                        // add only if there is some activity or we want full listing
                        perEmployee.push({
                            id: emp.id || null,
                            name: emp.name || 'Unnamed',
                            segments: Object.keys(segments).reduce((acc, k) => { acc[k] = this.round2(segments[k]); return acc; }, {}),
                            hours: this.round2(empHours),
                            pay: this.round2(pay)
                        });
                    });
                }

                // per-day totals
                const totals = perEmployee.reduce((acc, p) => {
                    acc.hours += Number(p.hours || 0);
                    acc.pay += Number(p.pay || 0);
                    return acc;
                }, { hours: 0, pay: 0 });

                totals.hours = this.round2(totals.hours);
                totals.pay = this.round2(totals.pay);

                return { date: iso, label, perEmployee, totals };
            });

            this.breakdown = breakdown;
            // also recompute summaries so UI totals sync with breakdown
            this.recomputeAllSummaries();
            return breakdown;
        },

        // Helper: return HTML string of breakdown (simple table) - can be used to inject into a container
        renderBreakdownHtml() {
            if (!this.breakdown || !this.breakdown.length) return '<div>No breakdown available. Generate one first.</div>';
            let html = '<div class="overflow-auto">';
            this.breakdown.forEach(day => {
                html += `<h3 style="margin-top:16px">${day.label} — ${day.date} — ${this.currency(day.totals.pay)} / ${day.totals.hours} hrs</h3>`;
                html += '<table style="width:100%;border-collapse:collapse;margin-bottom:8px">';
                html += '<thead><tr><th style="border:1px solid #ddd;padding:6px;text-align:left">Employee</th><th style="border:1px solid #ddd;padding:6px">Hours</th><th style="border:1px solid #ddd;padding:6px">Pay</th></tr></thead><tbody>';
                day.perEmployee.forEach(p => {
                    html += `<tr><td style="border:1px solid #eee;padding:6px">${p.name}</td><td style="border:1px solid #eee;padding:6px;text-align:center">${p.hours}</td><td style="border:1px solid #eee;padding:6px;text-align:right">${this.currency(p.pay)}</td></tr>`;
                });
                html += '</tbody></table>';
            });
            html += '</div>';
            return html;
        },

        // Manual Save (keeps behavior for your Save button)
        manualSave() {
            this.recomputeAllSummaries();
            this.saveHistory();
            this.saveState();
            alert('Saved');
        }
    };
}
