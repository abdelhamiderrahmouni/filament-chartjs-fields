import Chart from 'chart.js/auto'
import 'chartjs-adapter-luxon'

export default function chartBuilder({
        state,
        chartTypes,
        options,
        maxHeight,
        minHeight,
        defaultColors,
        responsive,
        maintainAspectRatio
    }) {
    return {
        // Initialize state with proper defaults
        state: state || {
            type: (chartTypes && chartTypes[0]) || 'line',
            data: [
                ["labels", "Dataset Name 1", "Dataset Name 2", "Dataset Name 3"],
                ["label 1", 1, 3, 4]
            ],
            // datasets: [{
            //     label: 'Dataset 1',
            //     data: '',
            //     backgroundColor: (defaultColors && defaultColors[0]) || '#3b82f6',
            //     borderColor: (defaultColors && defaultColors[0]) || '#3b82f6',
            // }]
        },
        isPreviewVisible: false,
        chartTypes: chartTypes || ['line', 'bar', 'pie'],
        options: options || {},
        maxHeight: maxHeight || '400px',
        minHeight: minHeight || '200px',
        responsive: responsive !== false,
        maintainAspectRatio: maintainAspectRatio !== false,
        chart: null,
        chartUpdateTimeout: null,
        defaultView: 'flex', // 'flex' or 'grid'

        init() {
            //
        },

        initializeChart() {
            this.$nextTick(() => {
                this.createChart();
            });
        },

        // Toggle preview
        togglePreview() {
            this.isPreviewVisible = !this.isPreviewVisible;

            if (this.isPreviewVisible) {
                this.$nextTick(() => {
                    this.refreshChart();
                });
            }
        },

        changeDefaultView() {
            if (this.defaultView === 'flex') {
                this.$refs.chart_container.classList.add('grid', 'md:grid-cols-2');
                this.$refs.chart_container.classList.remove('flex', 'flex-col');
                this.defaultView = 'grid';
                return;
            }

            this.$refs.chart_container.classList.remove('grid', 'md:grid-cols-2');
            this.$refs.chart_container.classList.add('flex', 'flex-col');
            this.defaultView = 'flex';
        },

        // Get labels count
        getLabelsCount() {
            return this.parseLabels(this.state.data).length - 1;
        },

        getDatasetLength(dataset) {
            return this.parseData(dataset.data).length;
        },

        get newRow() {
            const labelsCount = this.getLabelsCount();
            const newRow = ['label ' + (this.state.data.length)];
            for (let i = 0; i < this.state.data[0].length - 1; i++) {
                newRow.push(0);
            }
            return newRow;
        },

        addRow (index, direction) {
            if (!Array.isArray(this.state.data)) {
                this.state.data = [];
            }

            if (direction === 'above') {
                this.state.data.splice(index, 0, this.newRow);
            } else {
                this.state.data.splice(index + 1, 0, this.newRow);
            }

            this.$nextTick(() => {
                // Focus on the new input
                const inputs = this.$el.querySelectorAll('input[x-model*="col"]');
                if (inputs.length > 0) {
                    if (direction === 'above') {
                        inputs[index].focus();
                    } else {
                        inputs[index + 1].focus();
                    }
                }
            });
        },

        removeRow(index) {
            if (Array.isArray(this.state) && this.state.length > 1) {
                this.state.splice(index, 1);
                this.updateChart();
            }
        },

        // Check if dataset data is invalid
        isDatasetInvalid(dataset) {
            const labelsCount = this.getLabelsCount();
            const dataCount = this.getDatasetLength(dataset);

            return labelsCount > 0 && dataCount > 0 && dataCount !== labelsCount;
        },

        createChart() {
            const canvas = this.$refs.canvas;
            if (!canvas) return;

            // Destroy existing chart
            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }

            // Only create chart if we have valid data
            if (!this.hasValidData()) return;

            try {
                this.chart = new Chart(canvas, {
                    type: this.state.type,
                    data: this.getChartData(),
                    options: {
                        ...this.options,
                        responsive: this.responsive,
                        maintainAspectRatio: this.maintainAspectRatio,
                        animation: {
                            duration: 0 // Disable animations to prevent issues
                        },
                        plugins: {
                            legend: {
                                display: true
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error creating chart:', error);
            }
        },

        updateChart() {
            this.$nextTick(() => {
                if (! this.hasValidData() && this.chart) {
                    this.chart.destroy();
                    this.chart = null;
                }

                if (!this.chart) {
                    this.createChart();
                    return;
                }

                try {
                    this.chart.data = this.getChartData();
                    this.createChart();
                } catch (error) {
                    console.error('Error updating chart:', error);
                    this.createChart();
                }
            });
        },

        refreshChart() {
            this.createChart();
        },

        getChartData() {
            if (!this.state) {
                return { labels: [], datasets: [] };
            }

            const labels = this.parseLabels(this.state.labels);

            const datasets = (this.state.datasets || []).map(dataset => {
                const data = this.parseData(dataset.data);
                return {
                    label: dataset.label || 'Dataset',
                    data: data,
                    backgroundColor: dataset.backgroundColor || '#3b82f6',
                    borderColor: dataset.borderColor || dataset.backgroundColor || '#3b82f6',
                    borderWidth: ['pie', 'doughnut'].includes(this.state.type) ? 0 : 2,
                    fill: this.state.type !== 'line',
                };
            });

            return { labels, datasets };
        },

        parseLabels(rows) {
            if (!rows || !Array.isArray(rows))
                return [];

            return rows.filter(row => row[0] && row[0].trim());
        },

        parseData(dataString) {
            if (!dataString || typeof dataString !== 'string') return [];

            return dataString.split(',')
                .map(value => parseFloat(value.trim()))
                .filter(value => !isNaN(value));
        },

        hasValidData() {
            if (!this.state || !this.state.labels || !this.state.datasets) {
                return false;
            }

            const labels = this.parseLabels(this.state.labels);

            const hasValidDatasets = this.state.datasets.some(dataset => {
                const data = this.parseData(dataset.data);
                return data.length > 0;
            });

            return labels.length > 0 && hasValidDatasets;
        },

        capitalizeFirst(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        },

        // Cleanup when component is destroyed
        destroy() {
            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }
            if (this.chartUpdateTimeout) {
                clearTimeout(this.chartUpdateTimeout);
            }
        }
    };
}
