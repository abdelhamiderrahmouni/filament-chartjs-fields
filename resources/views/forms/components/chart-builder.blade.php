<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div  x-load
          x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('forms-chart-builder') }}"
          x-data="chartBuilder({
            state: $wire.$entangle('{{ $getStatePath() }}'),
            chartTypes: @js($getChartTypes()),
            options: @js($getOptions()),
            maxHeight: '{{ $getMaxHeight() }}',
            minHeight: '{{ $getMinHeight() }}',
            defaultColors: @js($getDefaultColors()),
            responsive: @js($isResponsive()),
            maintainAspectRatio: @js($shouldMaintainAspectRatio())
        })"
          x-init="initializeChart()"
          class="fi-chart-builder grid"
          x-ref="chart_builder"
          x-cloak>

        <!-- Chart Configurator Form -->
        <div class="space-y-4">
            <div class="layout-control">
                <button type="button" x-on:click="$refs.chart_builder.classList.add('flex', 'flex-col', 'gap-6'); $refs.chart_builder.classList.remove('grid')" aria-label="List layout">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M4 12l16 0" /></svg>
                </button>
                <button type="button" x-on:click="$refs.chart_builder.classList.remove('flex', 'flex-col', 'gap-6'); $refs.chart_builder.classList.add('grid')" aria-label="Grid layout">
                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /></svg>
                </button>
            </div>
            <div class="relative bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex justify-between items-center mb-4">
                    <div class="w-full space-y-2">
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">
                            Type
                        </label>
                        <select x-model="state.type"
                                @change="updateChart()"
                                class="w-full block rounded-md border-gray-300 dark:bg-gray-800 dark:border-gray-700 focus:border-primary-500 focus:ring focus:ring-primary-200 focus:ring-opacity-50 text-sm">
                            <template x-for="type in chartTypes" :key="type">
                                <option :value="type" x-text="capitalizeFirst(type)"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <!-- Labels Input -->
                <div class="space-y-2 mb-4">
                    <div class="flex items-center gap-2">
                        <div class="grid items-center gap-2" x-bind:style="`grid-template-columns: 2fr repeat(${(state.datasets || []).length}, minmax(0, 1fr));`">
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">
                                Étiquettes
                            </label>
                            <template x-for="(dataset, datasetIndex) in (state.datasets || [])" x-bind:key="datasetIndex">
                                <label class="relative">
                                    <input type="text" x-model="dataset.label" class="max-w-full border-none p-0 focus:ring-0 text-sm font-medium text-gray-600 dark:text-gray-400" />

                                    <div class="absolute right-0 top-0 h-full flex items-center gap-1 bg-white dark:bg-gray-800">
                                        <button type="button"
                                                @click="removeDataset(datasetIndex)"
                                                x-show="state.datasets && state.datasets.length > 1"
                                                class="text-red-500 hover:text-red-700 focus:outline-none text-sm p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </label>
                            </template>
                        </div>
                        <div>
                            <div class="w-4 h-4" x-show="state.labels.length > 1"></div>
                        </div>
                    </div>
                    <div class="space-y-2" x-show="state.labels && state.labels.length > 0">
                        <template x-for="(label, labelIndex) in (state.labels || [])" x-bind:key="labelIndex">
                            <div class="flex items-center gap-2">
                                <div class="grid items-center gap-2" x-bind:style="`grid-template-columns: 2fr repeat(${(state.datasets || []).length}, minmax(0, 1fr));`">
                                    <div class="flex-1 fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-2 fi-fo-text-input overflow-hidden ring-gray-950/10 dark:ring-white/20 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-600 dark:[&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-500">
                                        <div class="fi-input-wrp-input min-w-0 flex-1">
                                            <input type="text"
                                                   x-model="state.labels[labelIndex]"
                                                   @input.debounce.500ms="handleLabelsChange()"
                                                   class="fi-input px-4 py-1.5 block w-full border-none text-sm text-gray-950 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 bg-white/0"
                                                   :placeholder="`Label ${labelIndex + 1}`">
                                        </div>
                                    </div>
                                    <template x-for="(dataset, datasetIndex) in (state.datasets || [])" :key="datasetIndex">
                                        <div>
                                            {{-- <div class="flex items-center justify-between mb-2 gap-x-2">
                                                    <span class="w-full text-sm font-medium text-gray-700 dark:text-gray-300">
                                                        <input type="text"
                                                               x-model="dataset.label"
                                                               @input.debounce.500ms="updateChart()"
                                                               class="w-full border-none p-0 bg-transparent text-sm focus:ring-0"
                                                               placeholder="Dataset label">
                                                    </span>
                                                    <button type="button"
                                                            @click="removeDataset(datasetIndex)"
                                                            x-show="state.datasets && state.datasets.length > 1"
                                                            class="text-red-500 hover:text-red-700 focus:outline-none text-sm">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                    </button>
                                                </div>--}}

                                            <div class="flex flex-col gap-1">
                                                <div class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-2 fi-fo-text-input overflow-hidden ring-gray-950/10 dark:ring-white/20 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-600 dark:[&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-500">
                                                    <div class="fi-input-wrp-input min-w-0">
                                                        <input type="number"
                                                               x-model="dataset.data[labelIndex]"
                                                               @input.debounce.500ms="handleDatasetDataChange(datasetIndex, labelIndex)"
                                                               class="fi-input px-4 py-1.5 block w-full border-none text-sm text-gray-950 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 bg-white/0">
                                                    </div>
                                                </div>
                                            </div>

                                            {{-- <div class="flex gap-2">
                                                <div class="flex-1 flex flex-col gap-1">
                                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                                        Couleur de fond
                                                    </label>
                                                    <div class="flex items-center gap-1">
                                                        <input type="color"
                                                               x-model="dataset.backgroundColor"
                                                               @input="updateChart()"
                                                               class="color-input flex-shrink-0 rounded border border-gray-300 dark:border-gray-600 cursor-pointer">
                                                        <div class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-2 fi-fo-text-input overflow-hidden ring-gray-950/10 dark:ring-white/20 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-600 dark:[&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-500">
                                                            <div class="fi-input-wrp-input min-w-0 flex-1">
                                                                <input type="text"
                                                                       x-model="dataset.backgroundColor"
                                                                       @input.debounce.500ms="updateChart()"
                                                                       class="fi-input px-2 py-0.5 block w-full border-none text-sm text-gray-950 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 bg-white/0"
                                                                       placeholder="e.g. 10, 20, 30, 40">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="flex-1 flex flex-col gap-1" x-show="state.type && !['pie', 'doughnut'].includes(state.type)">
                                                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">
                                                        Couleur de bordure
                                                    </label>
                                                    <div class="flex items-center gap-1">
                                                        <input type="color"
                                                               x-model="dataset.borderColor"
                                                               @input="updateChart()"
                                                               class="color-input flex-shrink-0 rounded border border-gray-300 dark:border-gray-600 cursor-pointer">
                                                        <div class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-2 fi-fo-text-input overflow-hidden ring-gray-950/10 dark:ring-white/20 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-600 dark:[&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-500">
                                                            <div class="fi-input-wrp-input min-w-0 flex-1">
                                                                <input type="text"
                                                                       x-model="dataset.borderColor"
                                                                       @input.debounce.500ms="updateChart()"
                                                                       class="fi-input px-2 py-0.5 block w-full border-none text-sm text-gray-950 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 bg-white/0"
                                                                       placeholder="e.g. 10, 20, 30, 40">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div> --}}
                                        </div>
                                    </template>
                                </div>
                                <div class="">
                                    <button type="button"
                                            @click="removeLabel(labelIndex)"
                                            x-show="state.labels && state.labels.length > 1"
                                            class="text-red-500 hover:text-red-700 focus:outline-none text-sm p-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </template>
                        <div class="flex items-center justify-center">
                            <button type="button" @click="addLabel()"
                                    class="inline-flex items-center font-medium text-primary-600 text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Ajouter une étiquette
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Datasets Configuration -->
                <div>
                    <div class="flex justify-between">
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">
                            Données
                        </label>

                        <div>
                            <button type="button" x-on:click="$refs.datasets_container.classList.add('flex', 'flex-col', 'gap-6'); $refs.datasets_container.classList.remove('datasets-container')" aria-label="List layout">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M4 12l16 0" /></svg>
                            </button>
                            <button type="button" x-on:click="$refs.datasets_container.classList.remove('flex', 'flex-col', 'gap-6'); $refs.datasets_container.classList.add('datasets-container')" aria-label="Grid layout">
                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /></svg>
                            </button>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="datasets-container" x-ref="datasets_container" x-show="state.datasets && state.datasets.length > 0">

                        </div>

                        <div x-show="!state.datasets || state.datasets.length === 0"
                             class="text-center py-6 text-gray-500 dark:text-gray-400">
                            <svg class="w-16 h-16 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <p class="text-sm">Aucun données configuré</p>
                            <p class="text-xs">Cliquez sur « Ajouter des données » pour commencer</p>
                        </div>

                        <div class="w-full flex justify-center items-center">
                            <button type="button" @click="addDataset()"
                                    class="inline-flex items-center font-medium text-primary-600 text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                                Ajouter des données
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart Preview -->
        <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Aperçu</h3>
                <div class="flex items-center gap-2">
                    <button type="button"
                            @click="refreshChart()"
                            class="p-1.5 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 rounded focus:outline-none focus:ring-2 focus:ring-gray-500"
                            title="Rafraîchir le graphique">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                    <span class="text-xs text-gray-500 dark:text-gray-400" x-text="state.type ? state.type.toUpperCase() : 'CHART'"></span>
                </div>
            </div>

            <div wire:ignore
                 class="relative"
                 :style="`min-height: ${minHeight};`">
                <div x-show="hasValidData()" class="w-full h-full">
                    <canvas x-ref="canvas" class="w-full h-full"></canvas>
                </div>

                <div x-show="!hasValidData()"
                     class="flex items-center justify-center h-64 text-gray-400 dark:text-gray-500">
                    <div class="text-center">
                        <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <p class="text-sm font-medium">Aucune donnée de graphique</p>
                        <p class="text-xs">Ajoutez des étiquettes et des données pour voir votre graphique</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dynamic-component>
