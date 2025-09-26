
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
                        } else {
                            // no saved state -> initialize routine
                            this.persistDefaults();
                        }

                        // ensure daily arrays match date range length after load
                        this.onDateRangeChange();

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
                            redoStack: this.redoStack
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
                            departmentName: this.departmentName
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
                            departmentName: this.departmentName
                        });

                        const last = this.history.pop();

                        // restore
                        this.employees = last.employees || [];
                        this.rates = last.rates || this.rates;
                        this.startDate = last.startDate || "";
                        this.endDate = last.endDate || "";
                        this.summaryName = last.summaryName || "";
                        this.departmentName = last.departmentName || "";

                        // ensure arrays match range
                        this.onDateRangeChange();

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
                            departmentName: this.departmentName
                        });

                        const next = this.redoStack.pop();

                        this.employees = next.employees || [];
                        this.rates = next.rates || this.rates;
                        this.startDate = next.startDate || "";
                        this.endDate = next.endDate || "";
                        this.summaryName = next.summaryName || "";
                        this.departmentName = next.departmentName || "";

                        this.onDateRangeChange();
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
                    const days = this.daysRange();
                    const newLen = days.length;

                    // After adjusting day arrays, ensure employee.daily arrays match length
                    this.employees.forEach(emp => {
                        if (!emp.daily) emp.daily = [];
                        if (emp.daily.length < newLen) {
                            for (let k = emp.daily.length; k < newLen; k++) emp.daily.push(0);
                        } else if (emp.daily.length > newLen) {
                            emp.daily.splice(newLen);
                        }
                        // IMPORTANT: per requirements, DO NOT compute category totals from daily.
                        // The category fields (reg_hr, ot, np, hpnp, reg_hol, spec_hol) are manual and remain unchanged.
                    });

                    this.saveHistory();
                },

                // 🔹 Generate day range between start and end date
                daysRange() {
                    if (!this.startDate || !this.endDate) return [];
                    const start = new Date(this.startDate);
                    const end = new Date(this.endDate);
                    const days = [];
                    let current = new Date(start);
                    while (current <= end) {
                        days.push(
                            current.toLocaleDateString('en-US', { month: 'short', day: 'numeric' })
                        );
                        current.setDate(current.getDate() + 1);
                    }

                    // make sure each emp.daily has correct length (use 0 default, manual breakdown)
                    this.employees.forEach(emp => {
                        if (!emp.daily) emp.daily = [];
                        if (emp.daily.length < days.length) {
                            for (let k = emp.daily.length; k < days.length; k++) emp.daily.push(0);
                        } else if (emp.daily.length > days.length) {
                            emp.daily.splice(days.length);
                        }
                    });

                    return days;
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
                    // Per requirements: daily inputs are manual and DO NOT auto-calculate category totals.
                    // We simply save history/state so the change persists and can be undone.
                    this.saveHistory();
                    this.saveState();
                },

                // Called when user edits fields in the Employees table (reg_hr, ot, etc)
                // IMPORTANT: per request, do NOT auto-distribute edited totals into daily.
                onEmployeeTableInput(emp, field) {
                    if (emp[field] == null) emp[field] = 0;
                    if (emp[field] < 0) emp[field] = 0;
                    // Save history so user can undo this change.
                    this.saveHistory();
                    // persist
                    this.saveState();
                },

                totalHours(emp) {
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
                    this.employees.push({
                        name: "",
                        reg_hr: 0,
                        ot: 0,
                        np: 0,
                        hpnp: 0,
                        reg_hol: 0,
                        spec_hol: 0,
                        useCustom: false,
                        customRates: {},
                        daily: dailyInit
                    });

                    // Do NOT compute category totals from daily (per requirements)
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
                        departmentName: this.departmentName
                    });

                    // Clear everything except rates
                    this.summaryName = "";
                    this.departmentName = "";
                    this.startDate = "";
                    this.endDate = "";
                    this.employees = [];
                    this.redoStack = [];

                    // persist cleared state but DO NOT push the cleared state into history immediately.
                    // This allows Undo to work correctly (Undo will restore the pushed snapshot above).
                    this.saveState();
                }
            };
        }

