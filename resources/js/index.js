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
        state: state || this.emptyChart,
        emptyChart: {
            type: (chartTypes && chartTypes[0]) || 'line',
            labels: [''],
            datasets: [{
                label: 'Dataset 1',
                data: [0],
                backgroundColor: (defaultColors && defaultColors[0]) || '#3b82f6',
                borderColor: (defaultColors && defaultColors[0]) || '#3b82f6',
            }]
        },
        chartTypes: chartTypes || ['line', 'bar', 'pie'],
        options: options || {},
        maxHeight: maxHeight || '400px',
        minHeight: minHeight || '200px',
        responsive: responsive !== false,
        maintainAspectRatio: maintainAspectRatio !== false,
        chart: null,
        chartUpdateTimeout: null,

        init() {
            // Initialize datasets with zeros if labels exist
            const labelsCount = this.getLabelsCount();

            if (labelsCount > 0) {
                this.state.datasets.forEach(dataset => {
                    if (!dataset.data || dataset.data.length === 0) {
                        dataset.data = Array(labelsCount).fill(0);
                    }
                });
            }
        },

        // Handle labels change
        handleLabelsChange() {
            this.updateChart();
        },

        // Handle dataset data change
        handleDatasetDataChange(datasetIndex) {
            this.updateChart();
        },

        // Get labels count
        getLabelsCount() {
            return this.parseLabels(this.state.labels).length;
        },

        addLabel() {
            if (!Array.isArray(this.state.labels)) {
                this.state.labels = [];
            }

            this.state.labels.push(`Étiquette ${this.state.labels.length + 1}`);
            this.state.datasets.forEach(dataset => {
                dataset.data.push(0);
            });

            // TODO: Focus on the new input after adding a label
            // this.$nextTick(() => {
            //     // Focus on the new input
            //     const inputs = this.$el.querySelectorAll('input[x-model*="state.labels*"]');
            //     if (inputs.length > 0) {
            //         inputs[inputs.length - 1].focus();
            //     }
            // });
        },

        removeLabel(index) {
            if (Array.isArray(this.state.labels) && this.state.labels.length > 1) {
                this.state.labels.splice(index, 1);

                this.state.datasets.forEach(dataset => {
                    if (Array.isArray(dataset.data)) {
                        dataset.data.splice(index, 1);
                    }
                });

                this.updateChart();
            }
        },

        addDataset() {
            if (!this.state.datasets) {
                this.state.datasets = [];
            }

            const nextIndex = this.state.datasets.length;
            const palette = Array.isArray(defaultColors) && defaultColors.length ? defaultColors : ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'];
            const colorIndex = nextIndex % palette.length;
            const labelsCount = this.getLabelsCount();

            const newDataset = {
                label: `Dataset ${nextIndex + 1}`,
                data: labelsCount > 0 ? Array(labelsCount).fill(0) : [],
                backgroundColor: palette[colorIndex],
                borderColor: palette[colorIndex],
            };

            this.state.datasets.push(newDataset);
            this.$nextTick(() => this.updateChart());
        },

        removeDataset(index) {
            if (this.state.datasets && this.state.datasets.length > 1) {
                this.state.datasets.splice(index, 1);
                this.$nextTick(() => this.updateChart());
            }
        },

        getChartData() {
            if (!this.state) {
                return { labels: [], datasets: [] };
            }

            let state = window.Alpine.raw(this.state)

            return { labels: state.labels, datasets: state.datasets };
        },

        parseLabels(labels) {
            if (!labels || !Array.isArray(labels)) return [];
            return labels.filter(label => label && String(label).trim());
        },

        hasValidData() {
            if (!this.state || !Array.isArray(this.state.labels) || !Array.isArray(this.state.datasets)) {
                return false;
            }

            const labels = this.parseLabels(this.state.labels);
            if (labels.length === 0 || this.state.datasets.length === 0) {
                return false;
            }

            return this.state.datasets.every(ds => Array.isArray(ds.data) && ds.data.length === labels.length);
        },

        capitalizeFirst(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        },

        //
        // Chart methods
        //

        initializeChart() {
            this.$nextTick(() => {
                this.createChart();
            });
        },

        refreshChart() {
            this.initializeChart();
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
                        animation: { duration: 0 },
                        plugins: { legend: { display: true } },
                    },
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
                    this.chart.update('none');
                } catch (error) {
                    console.error('Error updating chart:', error);
                }
            });
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
