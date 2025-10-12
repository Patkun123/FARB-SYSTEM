    function billingApp() {
        return {
            // form state
            summaryName: "",
            departmentName: "",
            startDate: "",
            endDate: "",
            rates: {
                reg_hr: 73.38,
                ot: 73.78,
                np: 5.90,
                hpnp: 13.24,
                reg_hol: 59.02,
                spec_hol: 91.09
            },
            labels: {
                reg_hr: "Reg Hr Rate",
                ot: "OT Rate",
                np: "NP Rate",
                hpnp: "HPNP Rate",
                reg_hol: "Reg Hol Rate",
                spec_hol: "Spec Hol Rate"
            },

            // employees list
            employees: [],

            // days metadata: each entry is an object { type: 'work'|'reg_hol'|..., threshold: number }
            daysMeta: [],

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
                        // Only assign known properties to avoid injecting anything unexpected
                        if (parsed.summaryName != null) this.summaryName = parsed.summaryName;
                        if (parsed.departmentName != null) this.departmentName = parsed.departmentName;
                        if (parsed.startDate != null) this.startDate = parsed.startDate;
                        if (parsed.endDate != null) this.endDate = parsed.endDate;
                        if (parsed.rates != null) this.rates = parsed.rates;
                        if (Array.isArray(parsed.employees)) this.employees = parsed.employees;
                        if (Array.isArray(parsed.history)) this.history = parsed.history;
                        if (Array.isArray(parsed.redoStack)) this.redoStack = parsed.redoStack;
                        if (Array.isArray(parsed.daysMeta)) this.daysMeta = parsed.daysMeta;
                    } else {
                        // no saved state -> initialize routine
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
                this.saveState();
            },

            // ---------- History (undo/redo) ----------
            saveHistory() {
                // debounce pushes to avoid flooding
                clearTimeout(this.debounceTimer);
                this.debounceTimer = setTimeout(() => {
                    // push snapshot to history
                    this.history.push({
                        employees: JSON.parse(JSON.stringify(this.employees)),
                        rates: JSON.parse(JSON.stringify(this.rates)),
                        startDate: this.startDate,
                        endDate: this.endDate,
                        summaryName: this.summaryName,
                        departmentName: this.departmentName,
                        daysMeta: JSON.parse(JSON.stringify(this.daysMeta))
                    });
                    // clear redo when new action
                    this.redoStack = [];
                    // persist
                    this.saveState();
                }, 300);
            },

            Undo() {
                if (this.history.length > 0) {
                    // push current state into redo
                    this.redoStack.push({
                        employees: JSON.parse(JSON.stringify(this.employees)),
                        rates: JSON.parse(JSON.stringify(this.rates)),
                        startDate: this.startDate,
                        endDate: this.endDate,
                        summaryName: this.summaryName,
                        departmentName: this.departmentName,
                        daysMeta: JSON.parse(JSON.stringify(this.daysMeta))
                    });

                    const last = this.history.pop();

                    // restore
                    this.employees = last.employees || [];
                    this.rates = last.rates || this.rates;
                    this.startDate = last.startDate || "";
                    this.endDate = last.endDate || "";
                    this.summaryName = last.summaryName || "";
                    this.departmentName = last.departmentName || "";
                    this.daysMeta = last.daysMeta || [];

                    // ensure arrays match range
                    this.onDateRangeChange();

                    // recompute
                    this.recomputeAllSummaries();

                    // persist
                    this.saveState();
                }
            },

            Redo() {
                if (this.redoStack.length > 0) {
                    this.history.push({
                        employees: JSON.parse(JSON.stringify(this.employees)),
                        rates: JSON.parse(JSON.stringify(this.rates)),
                        startDate: this.startDate,
                        endDate: this.endDate,
                        summaryName: this.summaryName,
                        departmentName: this.departmentName,
                        daysMeta: JSON.parse(JSON.stringify(this.daysMeta))
                    });

                    const next = this.redoStack.pop();

                    this.employees = next.employees || [];
                    this.rates = next.rates || this.rates;
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
                    this.employeeToAdd = 1; // reset input
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
                days.push(
                    current.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
                );
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
                    // copy from global
                    emp.dayThresholds.splice(dIndex, 1, (this.daysMeta[dIndex] && this.daysMeta[dIndex].threshold) || 8);
                } else {
                    // unset
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
                    hpnp: 'HPNP'
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

                // recompute this employee's summary (only for non-manual fields)
                this.computeEmpSummary(emp);

                // Save history/state so the change persists and can be undone.
                this.saveHistory();
                this.saveState();
            },

            // Recomputes categories for a single employee from emp.daily & daysMeta
            computeEmpSummary(emp) {
                // If the entire employee is manually overridden, do not overwrite computed fields.
                const wasManual = !!emp.manualOverride;

                // If not manualOverride, recompute; if manualOverride, keep existing summary fields untouched.
                if (!wasManual) {
                    // reset computed fields
                    emp.reg_hr = 0;
                    emp.ot = 0;
                    emp.np = 0;
                    emp.hpnp = 0;
                    emp.reg_hol = 0;
                    emp.spec_hol = 0;
                } else {
                    // ensure fields exist to avoid NaN when summing
                    emp.reg_hr = Number(emp.reg_hr || 0);
                    emp.ot = Number(emp.ot || 0);
                    emp.np = Number(emp.np || 0);
                    emp.hpnp = Number(emp.hpnp || 0);
                    emp.reg_hol = Number(emp.reg_hol || 0);
                    emp.spec_hol = Number(emp.spec_hol || 0);
                    // If manual, we still may want to recompute categories that are zero? We'll respect manual entirely.
                }

                const daysCount = this.daysRange().length;
                if (!Array.isArray(emp.daily)) emp.daily = new Array(daysCount).fill(0);
                if (!Array.isArray(emp.dayOverrides)) emp.dayOverrides = new Array(daysCount).fill('');
                if (!Array.isArray(emp.dayThresholds)) emp.dayThresholds = new Array(daysCount).fill(null);

                for (let d = 0; d < daysCount; d++) {
                    const hours = Number(emp.daily[d] || 0);
                    // per-employee override has priority
                    const dtype = emp.dayOverrides[d] && emp.dayOverrides[d] !== '' ? emp.dayOverrides[d] : (this.daysMeta[d] && this.daysMeta[d].type) || 'work';
                    // threshold priority: emp.dayThresholds[d] -> daysMeta[d].threshold -> 8 default
                    const threshold = (emp.dayThresholds[d] != null) ? Number(emp.dayThresholds[d]) : ((this.daysMeta[d] && this.daysMeta[d].threshold) != null ? Number(this.daysMeta[d].threshold) : 8);

                    if (wasManual) {
                        // if manual override is active for the entire employee, skip adding to computed fields
                        continue;
                    }

                    if (dtype === 'work') {
                        const reg = Math.min(threshold, hours);
                        const ot = Math.max(0, hours - threshold);
                        emp.reg_hr += reg;
                        emp.ot += ot;
                    } else if (dtype === 'reg_hol') {
                        emp.reg_hol += hours;
                    } else if (dtype === 'spec_hol') {
                        emp.spec_hol += hours;
                    } else if (dtype === 'np') {
                        emp.np += hours;
                    } else if (dtype === 'hpnp') {
                        emp.hpnp += hours;
                    } else {
                        // fallback treat as work with threshold
                        const reg = Math.min(threshold, hours);
                        const ot = Math.max(0, hours - threshold);
                        emp.reg_hr += reg;
                        emp.ot += ot;
                    }
                }

                // Ensure numeric rounding
                emp.reg_hr = Number(emp.reg_hr || 0);
                emp.ot = Number(emp.ot || 0);
                emp.np = Number(emp.np || 0);
                emp.hpnp = Number(emp.hpnp || 0);
                emp.reg_hol = Number(emp.reg_hol || 0);
                emp.spec_hol = Number(emp.spec_hol || 0);
            },

            // Recompute all employees' summaries
            recomputeAllSummaries() {
                this.employees.forEach(emp => {
                    this.computeEmpSummary(emp);
                });
                // persist after compute (but don't push a history snapshot here)
                this.saveState();
            },

            // Called when user toggles manual override on/off for an employee
            onManualOverrideToggle(emp) {
                // if manualOverride was just enabled, leave summary fields as-is so the user can edit them.
                // if manualOverride was just disabled, recompute to restore auto-calculated values.
                if (!emp.manualOverride) {
                    // toggled OFF -> recompute to regenerate summary from daily
                    this.computeEmpSummary(emp);
                } else {
                    // toggled ON -> ensure fields exist and let user edit manually
                    emp.reg_hr = Number(emp.reg_hr || 0);
                    emp.ot = Number(emp.ot || 0);
                    emp.np = Number(emp.np || 0);
                    emp.hpnp = Number(emp.hpnp || 0);
                    emp.reg_hol = Number(emp.reg_hol || 0);
                    emp.spec_hol = Number(emp.spec_hol || 0);
                }
                this.saveHistory();
                this.saveState();
            },

            // Called when user edits fields in the Employees table (previously allowed)
            // Now summary fields are computed from daily unless manualOverride is true.
            onEmployeeTableInput(emp, field) {
                if (emp[field] == null) emp[field] = 0;
                if (emp[field] < 0) emp[field] = 0;
                this.saveHistory();
                this.saveState();
            },

            totalHours(emp) {
                // compute from computed summary fields (they are maintained by computeEmpSummary)
                return Number(
                    (emp.reg_hr || 0) +
                    (emp.ot || 0) +
                    (emp.np || 0) +
                    (emp.hpnp || 0) +
                    (emp.reg_hol || 0) +
                    (emp.spec_hol || 0)
                );
            },

            totalPay(emp) {
                const r = this.rates;
                const c = emp.useCustom ? emp.customRates : {};
                const getRate = (field) => emp.useCustom ? (c[field] != null ? c[field] : r[field]) : r[field];
                return (Number(emp.reg_hr || 0) * getRate('reg_hr')) +
                    (Number(emp.ot || 0) * getRate('ot')) +
                    (Number(emp.np || 0) * getRate('np')) +
                    (Number(emp.hpnp || 0) * getRate('hpnp')) +
                    (Number(emp.reg_hol || 0) * getRate('reg_hol')) +
                    (Number(emp.spec_hol || 0) * getRate('spec_hol'));
            },

            columnTotal(field) {
                return this.employees.reduce((sum, emp) => sum + (Number(emp[field] || 0)), 0);
            },

            categoryTotalPay(field) {
                return this.employees.reduce((sum, emp) => {
                    const hours = Number(emp[field] || 0);
                    const rate = emp.useCustom ? (emp.customRates[field] != null ? emp.customRates[field] : this.rates[field]) : this.rates[field];
                    return sum + (hours * rate);
                }, 0);
            },

            grandTotal() {
                return this.employees.reduce((sum, emp) => sum + this.totalPay(emp), 0);
            },

            grandTotalHours() {
                return this.employees.reduce((sum, emp) => sum + this.totalHours(emp), 0);
            },

            currency(val) {
                return '₱' + (Number(val) || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },

            addEmployee() {
                const daysCount = this.startDate && this.endDate ? this.daysRange().length : 0;
                // daily default is 0 (manual breakdown)
                const dailyInit = new Array(daysCount).fill(0);
                const emp = {
                    name: "",
                    reg_hr: 0,
                    ot: 0,
                    np: 0,
                    hpnp: 0,
                    reg_hol: 0,
                    spec_hol: 0,
                    useCustom: false,
                    customRates: {},
                    daily: dailyInit,
                    // new flags/arrays for overrides and manual editing
                    manualOverride: false,
                    dayOverrides: new Array(daysCount).fill(''), // '' means use global
                    dayThresholds: new Array(daysCount).fill(null) // null means use daysMeta threshold
                };
                this.employees.push(emp);

                // compute summary from daily (initially zeros)
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
                // push current snapshot to history so undo can restore
                this.history.push({
                    employees: JSON.parse(JSON.stringify(this.employees)),
                    rates: JSON.parse(JSON.stringify(this.rates)),
                    startDate: this.startDate,
                    endDate: this.endDate,
                    summaryName: this.summaryName,
                    departmentName: this.departmentName,
                    daysMeta: JSON.parse(JSON.stringify(this.daysMeta))
                });

                // Clear everything except rates
                this.summaryName = "";
                this.departmentName = "";
                this.startDate = "";
                this.endDate = "";
                this.employees = [];
                this.daysMeta = [];
                this.redoStack = [];

                // persist cleared state but DO NOT push the cleared state into history immediately.
                // This allows Undo to work correctly (Undo will restore the pushed snapshot above).
                this.saveState();
            },

            // Manual Save (keeps behavior for your Save button)
            manualSave() {
                // recompute to ensure sync
                this.recomputeAllSummaries();
                this.saveHistory();
                this.saveState();
                alert('Saved');
            }
        };
    }
