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
            labels: [],
            datasets: [{
                label: 'Dataset 1',
                data: '',
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
                    if (!dataset.data || dataset.data.trim() === '') {
                        dataset.data = Array(labelsCount).fill(0).join(', ');
                    }
                });
            }
        },

        initializeChart() {
            this.$nextTick(() => {
                this.createChart();
            });
        },

        // Handle labels change
        handleLabelsChange() {
            this.syncDatasetsWithLabels();
            this.updateChart();
        },

        // Handle dataset data change
        handleDatasetDataChange(datasetIndex) {
            this.updateChart();
        },

        // Sync datasets data with labels count
        syncDatasetsWithLabels() {
            const labelsCount = this.getLabelsCount();

            if (labelsCount === 0) return;

            this.state.datasets.forEach(dataset => {
                const currentData = this.parseData(dataset.data);

                if (currentData.length === 0) {
                    // Fill with zeros if no data
                    dataset.data = Array(labelsCount).fill(0).join(', ');
                }

                if (currentData.length < labelsCount) {
                    // Append zeros if data is shorter than labels
                    const zerosToAdd = labelsCount - currentData.length;
                    const newZeros = Array(zerosToAdd).fill(0);
                    dataset.data = [...currentData, ...newZeros].join(', ');
                }
            });
        },

        // Get labels count
        getLabelsCount() {
            return this.parseLabels(this.state.labels).length;
        },

        getDatasetLength(dataset) {
            return this.parseData(dataset.data).length;
        },

        addLabel() {
            if (!Array.isArray(this.state.labels)) {
                this.state.labels = [];
            }

            this.state.labels.push(`Étiquette ${this.state.labels.length + 1}`);
            this.state.datasets.forEach(dataset => {
                const wasString = typeof dataset.data === 'string';
                let dataArr = wasString
                    ? this.parseData(dataset.data)
                    : Array.isArray(dataset.data) ? dataset.data.slice() : [];

                const newIndex = this.state.labels.length - 1;
                while (dataArr.length <= newIndex) dataArr.push(0);
                dataArr[newIndex] = 0;

                dataset.data = wasString ? dataArr.join(', ') : dataArr;
            });

            this.$nextTick(() => {
                // Focus on the new input
                const inputs = this.$el.querySelectorAll('input[x-model*="state.labels*"]');
                if (inputs.length > 0) {
                    inputs[inputs.length - 1].focus();
                }
            });
        },

        removeLabel(index) {
            if (Array.isArray(this.state.labels) && this.state.labels.length > 1) {
                this.state.labels.splice(index, 1);
                this.syncDatasetsWithLabels();
                this.updateChart();
            }
        },

        addDataset() {
            if (!this.state.datasets) {
                this.state.datasets = [];
            }

            const nextIndex = this.state.datasets.length;
            const colorIndex = nextIndex % defaultColors.length;
            const labelsCount = this.getLabelsCount();

            const newDataset = {
                label: `Dataset ${nextIndex + 1}`,
                data: labelsCount > 0 ? Array(labelsCount).fill(0).join(', ') : '',
                backgroundColor: defaultColors[colorIndex],
                borderColor: defaultColors[colorIndex],
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

        parseLabels(labels) {
            if (!labels || !Array.isArray(labels))
                return [];

            return labels.filter(label => label && label.trim());
        },

        parseData(dataString) {
            if (!dataString || typeof dataString !== 'string') return [];

            return dataString.split(',')
                .map(value => parseFloat(value.trim()))
                .filter(value => !isNaN(value));
        },

        hasValidData() {
            console.log('Checking for valid data...', this.state, this.state?.labels, this.state?.datasets);

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
