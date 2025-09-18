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
        chartUpdateTimeout: null,

        init() {
            this.initializeDatasets();
            this.initializeChart();
        },

        initializeDatasets() {
            const labelsCount = this.state.labels.length;

            if (labelsCount > 0) {
                this.state.datasets.forEach(dataset => {
                    if (!dataset.data || dataset.data.length === 0) {
                        dataset.data = Array(labelsCount).fill(0);
                    }
                });
            }
        },

        handleLabelsChange(labelIndex, value) {
			let chart = this.getChart();

            if (chart) {
                chart.data.labels[labelIndex] = value;
                chart.update('resize');
            }
        },

        handleDatasetDataChange(datasetIndex, labelIndex, value) {
			let chart = this.getChart();

            if (chart && chart.data.datasets[datasetIndex]) {
                chart.data.datasets[datasetIndex].data[labelIndex] = Number(value) || 0;
                chart.update('resize');
            }
        },

	    addLabel() {
		    if (!Array.isArray(this.state.labels)) this.state.labels = [];
		    if (!Array.isArray(this.state.datasets)) this.state.datasets = [];

		    const newLabel = `Étiquette ${this.state.labels.length + 1}`;

		    this.state.labels = [...this.state.labels, newLabel];

		    this.state.datasets = this.state.datasets.map(ds => ({
			    ...ds,
			    data: [...(Array.isArray(ds.data) ? ds.data : []), 0],
		    }));

		    const chart = this.getChart();
		    if (chart) {
			    chart.data.labels = [...this.state.labels];
			    chart.data.datasets = this.state.datasets.map(ds => ({ ...ds, data: [...ds.data] }));
			    chart.update('resize');
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

			let chart = this.getChart();

            if (chart) {
                chart.data.labels.splice(index, 1);
                chart.data.datasets.forEach(dataset => {
                    if (Array.isArray(dataset.data)) {
                        dataset.data.splice(index, 1);
                    }
                });

                chart.update('resize');
            }
        },

	    addDataset() {
		    const current = Array.isArray(this.state.datasets) ? this.state.datasets : [];
		    const palette = Array.isArray(defaultColors) && defaultColors.length
			    ? defaultColors
			    : ['#3b82f6', '#10b981', '#f59e0b', '#ef4444'];

		    const nextIndex = current.length;
		    const color = palette[nextIndex % palette.length];
		    const labelsCount = Array.isArray(this.state.labels) ? this.state.labels.length : 0;

		    const newDataset = {
			    label: `Dataset ${nextIndex + 1}`,
			    data: Array(labelsCount).fill(0),
			    backgroundColor: color,
			    borderColor: color,
		    };

		    this.state.datasets = [...current, newDataset];

		    const chart = this.getChart();

		    if (chart) {
			    chart.data.datasets = this.state.datasets.map(ds => ({ ...ds, data: [...ds.data] }));
			    chart.update('resize');
		    }
	    },

	    removeDataset(index) {
		    if (!Array.isArray(this.state.datasets) || this.state.datasets.length <= 1) return;

		    const copy = [...this.state.datasets];
		    copy.splice(index, 1);
		    this.state.datasets = copy;

		    const chart = this.getChart();

		    if (chart) {
			    chart.data.datasets = this.state.datasets.map(ds => ({ ...ds, data: [...ds.data] }));
			    chart.update('resize');
		    }
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

                if (! this.$refs.canvas) return;

                const allowedTypes = ['line','bar','pie','doughnut','polarArea','radar','scatter','bubble'];

                const state = window.Alpine.raw(this.state) || {};
                if (!state.type || !allowedTypes.includes(state.type)) state.type = 'line';
                if (!Array.isArray(state.labels)) state.labels = [''];
                if (!Array.isArray(state.datasets)) state.datasets = [];

                state.datasets = state.datasets.map(ds => {
                    const copy = { ...ds };
                    if (!Array.isArray(copy.data)) copy.data = Array(state.labels.length).fill(0);
                    if (copy.type && !allowedTypes.includes(copy.type)) delete copy.type;
                    return copy;
                });

                const existing = this.getChart();
                if (existing) existing.destroy();

	            Chart.defaults.animation.duration = 0

	            Chart.defaults.backgroundColor = getComputedStyle(
		            this.$refs.backgroundColorElement,
	            ).color

	            const borderColor = getComputedStyle(
		            this.$refs.borderColorElement,
	            ).color

	            Chart.defaults.borderColor = borderColor

	            Chart.defaults.color = getComputedStyle(
		            this.$refs.textColorElement,
	            ).color

	            Chart.defaults.font.family = getComputedStyle(this.$el).fontFamily

	            Chart.defaults.plugins.legend.labels.boxWidth = 12
	            Chart.defaults.plugins.legend.position = 'bottom'

	            const gridColor = getComputedStyle(
		            this.$refs.gridColorElement,
	            ).color

				let options = window.Alpine.raw(this.options);
	            options.responsive ??= window.Alpine.raw(this.responsive);
	            options.maintainAspectRatio ??= window.Alpine.raw(this.maintainAspectRatio);
	            options.animation ??= {};
				options.animation.duration ??= 0;
	            options.plugins ??= {};
	            options.plugins.legend ??= {};
				options.plugins.legend.display ??= true;

                new Chart(this.$refs.canvas, {
                    type: state.type,
                    data: {
                        labels: state.labels,
                        datasets: state.datasets
                    },
                    options: options,
	                plugins: window.filamentChartJsPlugins ?? [],
                })
            });
        },

	    getChart: function () {
		    if (! this.$refs.canvas) {
			    return null
		    }

		    return Chart.getChart(this.$refs.canvas)
	    },

        refreshChart() {
            this.initializeChart();
        },

        destroyChart() {
	        let chart = this.getChart();

            if (chart) {
                chart.destroy();
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
