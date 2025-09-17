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


    let chart = null;

    return {
        state: state || baseEmptyChart,
        chartTypes: chartTypes || ['line', 'bar', 'pie'],
        options: options || {},
        maxHeight: maxHeight || '400px',
        minHeight: minHeight || '200px',
        responsive: responsive !== false,
        maintainAspectRatio: maintainAspectRatio !== false,
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

        handleLabelsChange(labelIndex, value) {
            if (chart) {
                chart.data.labels[labelIndex] = value;
                chart.update('none');
            }
        },

        handleDatasetDataChange(datasetIndex, labelIndex, value) {
            if (chart && chart.data.datasets[datasetIndex]) {
                chart.data.datasets[datasetIndex].data[labelIndex] = Number(value) || 0;
                chart.update('none');
            }
        },

        getLabelsCount() {
            return this.parseLabels(this.state.labels).length;
        },

        addLabel() {
            if (!Array.isArray(this.state.labels)) {
                this.state.labels = [];
            }
            if (!Array.isArray(this.state.datasets)) {
                this.state.datasets = [];
            }

            const newLabel = `Étiquette ${this.state.labels.length + 1}`;

            this.state.labels = [...this.state.labels, newLabel];

            this.state.datasets = this.state.datasets.map(dataset => {
                if (!dataset) return dataset;

                const data = Array.isArray(dataset.data) ? dataset.data : [];
                return { ...dataset, data: [...data, 0] };
            });

            if (chart) {
                chart.data.labels.push(newLabel);
                chart.data.datasets.map(dataset => {
                    if (!dataset) return dataset;

                    dataset.data.push(0);

                    return dataset;
                });

                chart.update('none');
            }
        },

        removeLabel(index) {
            if (Array.isArray(this.state.labels) && this.state.labels.length > 1) {
                this.state.labels.splice(index, 1);

                this.state.datasets.forEach(dataset => {
                    if (Array.isArray(dataset.data)) {
                        dataset.data.splice(index, 1);
                    }
                });
            }

            if (chart) {
                chart.data.labels.splice(index, 1);
                chart.data.datasets.forEach(dataset => {
                    if (Array.isArray(dataset.data)) {
                        dataset.data.splice(index, 1);
                    }
                });

                chart.update('none');
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

            if (chart) {
                chart.data.datasets.push({
                    ...newDataset,
                    data: [...newDataset.data],
                });

                chart.update('none');
            }
        },

        removeDataset(index) {
            if (!Array.isArray(this.state.datasets) || this.state.datasets.length <= 1) return;

            this.state.datasets.splice(index, 1);

            if (chart) {
                chart.data.datasets = this.state.datasets.map(ds => ({
                    ...ds,
                    data: Array.isArray(ds.data) ? [...ds.data] : []
                }));
                chart.update('none');
            }
        },

        parseLabels(labels) {
            if (!labels || !Array.isArray(labels)) return [];
            return labels.filter(label => label && String(label).trim());
        },

        capitalizeFirst(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        },

        //
        // Chart methods
        //

        initializeChart() {
            this.$nextTick(() => {
                this.destroyChart();

                const canvas = this.$refs.canvas;
                if (!canvas) return;

                let state = window.Alpine.raw(this.state);

                chart = new Chart(canvas, {
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
                })
            });
        },

        refreshChart() {
            this.initializeChart();
        },

        destroyChart() {
            if (chart) {
                chart.destroy();
                chart = null;
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
