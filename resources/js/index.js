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

    const baseEmptyChart = {
        type: (chartTypes && chartTypes[0]) || 'line',
        labels: [''],
        datasets: [{
            label: 'Dataset 1',
            data: [0],
            backgroundColor: (defaultColors && defaultColors[0]) || '#3b82f6',
            borderColor: (defaultColors && defaultColors[0]) || '#3b82f6',
        }]
    }

    return {
        state: state || baseEmptyChart,
        chartTypes: chartTypes || ['line', 'bar', 'pie'],
        options: options || {},
        maxHeight: maxHeight || '400px',
        minHeight: minHeight || '200px',
        responsive: responsive !== false,
        maintainAspectRatio: maintainAspectRatio !== false,
        chart: null,
        chartUpdateTimeout: null,

        init() {
            this.initializeDatasets();
            this.initializeChart();
        },

        initializeDatasets() {
            const labelsCount = this.getLabelsCount();

            if (labelsCount > 0) {
                this.state.datasets.forEach(dataset => {
                    if (!dataset.data || dataset.data.length === 0) {
                        dataset.data = Array(labelsCount).fill(0);
                    }
                });
            }
        },

        handleLabelsChange(labelIndex) {
            this.refreshChart();
        },

        handleDatasetDataChange(datasetIndex, labelIndex) {
            this.refreshChart();
        },

        getLabelsCount() {
            return this.parseLabels(this.state.labels).length;
        },

        addLabel() {
            // Destroy existing chart to avoid Chart.js
            // array listeners reacting to mutations
            this.destroyChart();

            if (!Array.isArray(this.state.labels)) {
                this.state.labels = [];
            }
            if (!Array.isArray(this.state.datasets)) {
                this.state.datasets = [];
            }

            this.state.labels = [...this.state.labels, `Étiquette ${this.state.labels.length + 1}`];

            this.state.datasets = this.state.datasets.map(dataset => {
                if (!dataset) return dataset;

                const data = Array.isArray(dataset.data) ? dataset.data : [];
                return { ...dataset, data: [...data, 0] };
            });

            this.refreshChart();
        },

        removeLabel(index) {
            this.destroyChart();

            if (Array.isArray(this.state.labels) && this.state.labels.length > 1) {
                this.state.labels.splice(index, 1);

                this.state.datasets.forEach(dataset => {
                    if (Array.isArray(dataset.data)) {
                        dataset.data.splice(index, 1);
                    }
                });
            }

            this.refreshChart()
        },

        addDataset() {
            this.destroyChart();

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
            this.refreshChart()
        },

        removeDataset(index) {
            this.destroyChart();

            if (this.state.datasets && this.state.datasets.length > 1) {
                this.state.datasets.splice(index, 1);
            }

            this.refreshChart()
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

            this.destroyChart();

            if (!this.hasValidData()) return;

            let state = window.Alpine.raw(this.state)

            try {
                this.chart = new Chart(canvas, {
                    type: state.type,
                    data: {
                        labels: state.labels,
                        datasets: state.datasets
                    },
                    options: {
                        ...window.Alpine.raw(this.options),
                        responsive: window.Alpine.raw(this.responsive),
                        maintainAspectRatio: window.Alpine.raw(this.maintainAspectRatio),
                        animation: { duration: 0 },
                        plugins: { legend: { display: true } },
                    },
                });
            } catch (error) {
                console.error('Error creating chart:', error);
            }
        },

        destroyChart() {
            if (this.chart) {
                this.chart.destroy();
                this.chart = null;
            }
        },

        destroy() {
            this.destroyChart();

            if (this.chartUpdateTimeout) {
                clearTimeout(this.chartUpdateTimeout);
            }
        }
    };
}
