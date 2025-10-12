          function billSummaryApp() {
                        return {
                            summaries: [],
                            filteredSummaries: [],
                            selectedSummaries: [],
                            searchTerm: '',
                            totalAmount: 0,

                            init() {
                                fetch('{{ route("admin.billing.summaries") }}')
                                    .then(res => res.json())
                                    .then(data => {
                                        this.summaries = data.map(s => ({
                                            ...s,
                                            created_at: s.created_at || new Date().toISOString()
                                        }));
                                    });
                            },

                            formatDate(dateStr) {
                                if (!dateStr) return '-';
                                const date = new Date(dateStr);
                                return date.toLocaleDateString('en-PH', { year: 'numeric', month: 'short', day: 'numeric' });
                            },

                            filterSummaries() {
                                const term = this.searchTerm.toLowerCase();
                                this.filteredSummaries = this.summaries.filter(s =>
                                    s.summary_name.toLowerCase().includes(term) ||
                                    (s.department_name && s.department_name.toLowerCase().includes(term)) ||
                                    (s.start_date && this.formatDate(s.start_date).toLowerCase().includes(term)) ||
                                    (s.end_date && this.formatDate(s.end_date).toLowerCase().includes(term)) ||
                                    (s.created_at && this.formatDate(s.created_at).toLowerCase().includes(term))
                                );
                            },

                            selectSummary(summary) {
                                if (this.isAlreadySelected(summary.id)) return; // prevent duplicates
                                this.selectedSummaries.push({
                                    summary_id: summary.id,
                                    summary_name: summary.summary_name,
                                    department_name: summary.department_name,
                                    start_date: summary.start_date,
                                    end_date: summary.end_date,
                                    created_at: summary.created_at,
                                    total: parseFloat(summary.grand_total ?? 0)
                                });
                                this.searchTerm = '';
                                this.filteredSummaries = [];
                                this.computeTotal();
                            },

                            isAlreadySelected(id) {
                                return this.selectedSummaries.some(s => s.summary_id === id);
                            },

                            removeSummary(index) {
                                this.selectedSummaries.splice(index, 1);
                                this.computeTotal();
                            },

                            computeTotal() {
                                this.totalAmount = this.selectedSummaries.reduce((sum, s) => sum + (s.total || 0), 0);
                            }
                        }
                    }
