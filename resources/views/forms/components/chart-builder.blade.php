@php
use Filament\Support\Facades\FilamentView;
@endphp
<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        @if (FilamentView::hasSpaMode())
            x-load="visible || event (ax-modal-opened)"
        @else
            x-load
        @endif
        x-load-src="{{ \Filament\Support\Facades\FilamentAsset::getAlpineComponentSrc('filament-chartjs-fields', 'abdelhamiderrahmouni/filament-chartjs-fields') }}"
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
        class="fi-chart-builder bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-4"
        x-ref="chart_builder"
        x-cloak>

        <div class="layout-control">
            <button type="button" x-on:click="togglePreview" aria-label="Grid layout">
                <svg class="w-4 h-4" x-show="isPreviewVisible" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                </svg>
                <svg class="w-4 h-4" x-show="! isPreviewVisible" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.98 8.223A10.477 10.477 0 0 0 1.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.451 10.451 0 0 1 12 4.5c4.756 0 8.773 3.162 10.065 7.498a10.522 10.522 0 0 1-4.293 5.774M6.228 6.228 3 3m3.228 3.228 3.65 3.65m7.894 7.894L21 21m-3.228-3.228-3.65-3.65m0 0a3 3 0 1 0-4.243-4.243m4.242 4.242L9.88 9.88" />
                </svg>
            </button>
            <button type="button" x-show="isPreviewVisible && defaultView === 'grid'" x-on:click="changeDefaultView" aria-label="List layout">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" /><path d="M4 12l16 0" /></svg>
            </button>
            <button type="button" x-show="isPreviewVisible && defaultView === 'flex'" x-on:click="changeDefaultView" aria-label="Grid layout">
                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /><path d="M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" /></svg>
            </button>
            <div class="flex items-center gap-2" x-show="isPreviewVisible">
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

        <div class="fi-chart-container flex flex-col gap-6" x-ref="chart_container">
            <!-- Chart Configurator Form -->
            <div class="space-y-4">
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
                    <div class="space-y-2" x-show="state.data && state.data.length > 0">
                        <template x-for="(row, rowIndex) in state.data" :key="rowIndex">
                            <div class="">
                                <template x-if="rowIndex === 0">
                                    <div class="flex items-center">
                                        <template x-for="(col, colIndex) in row" :key="colIndex">
                                            <div class="flex">
                                                <template x-if="colIndex === 0">
                                                    <div class="">
                                                        {{ __("Labels") }}
                                                    </div>
                                                </template>
                                                <template x-if="colIndex > 0">
                                                    <div class="flex">
                                                        <input type="text"
                                                               x-model="col"
                                                               @input.debounce.500ms="updateChart()"
                                                               class="w-full border-none p-0 bg-transparent text-sm focus:ring-0"
                                                               placeholder="Dataset label">

                                                        <div class="flex">
                                                            <button type="button"
                                                                    @click="addColumn(colIndex, 'left')"
                                                                    class="text-gray-500 hover:text-gray-700 focus:outline-none text-sm p-1">
                                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M14 4h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-14a1 1 0 0 1 1 -1z" /><path d="M5 12l4 0" /><path d="M7 10l0 4" /></svg>
                                                            </button>
                                                            <button type="button"
                                                                    @click="addColumn(colIndex, 'right')"
                                                                    class="text-gray-500 hover:text-gray-700 focus:outline-none text-sm p-1">
                                                                <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M6 4h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-14a1 1 0 0 1 1 -1z" /><path d="M15 12l4 0" /><path d="M17 10l0 4" /></svg>
                                                            </button>
                                                            <button type="button"
                                                                    @click="removeColumn(colIndex)"
                                                                    x-show="row && row.length > 1"
                                                                    class="text-danger-500 hover:text-danger-700 focus:outline-none text-sm p-1">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <template x-if="rowIndex > 0">
                                    <div class="flex items-center gap-2">
                                        <template x-for="(col, colIndex) in (row || [])" :key="colIndex">
                                            <div class="flex-1 fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-white dark:bg-white/5 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-2 fi-fo-text-input overflow-hidden ring-gray-950/10 dark:ring-white/20 [&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-600 dark:[&:not(:has(.fi-ac-action:focus))]:focus-within:ring-primary-500">
                                                <div class="fi-input-wrp-input min-w-0 flex-1">
                                                    <input x-bind:type="colIndex === 0 ? 'text' : 'number'"
                                                           x-model="col"
                                                           @input.debounce.500ms="updateChart()"
                                                           class="fi-input px-4 py-1.5 block w-full border-none text-sm text-gray-950 placeholder:text-gray-400 focus:ring-0 disabled:text-gray-500 disabled:[-webkit-text-fill-color:theme(colors.gray.500)] disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.400)] dark:text-white dark:placeholder:text-gray-500 dark:disabled:text-gray-400 dark:disabled:[-webkit-text-fill-color:theme(colors.gray.400)] dark:disabled:placeholder:[-webkit-text-fill-color:theme(colors.gray.500)] sm:text-sm sm:leading-6 bg-white/0"
                                                           :placeholder="colIndex === 0 ? `Label ${colIndex + 1}` : `Data ${colIndex + 1}`">
                                                </div>
                                            </div>
                                        </template>
                                        <button type="button"
                                                @click="addRow(rowIndex, 'above')"
                                                class="text-gray-500 hover:text-gray-700 focus:outline-none text-sm p-1">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M4 18v-4a1 1 0 0 1 1 -1h14a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-14a1 1 0 0 1 -1 -1z" /><path d="M12 9v-4" /><path d="M10 7l4 0" /></svg>
                                        </button>
                                        <button type="button"
                                                @click="addRow(rowIndex, 'bellow')"
                                                class="text-gray-500 hover:text-gray-700 focus:outline-none text-sm p-1">
                                            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M20 6v4a1 1 0 0 1 -1 1h-14a1 1 0 0 1 -1 -1v-4a1 1 0 0 1 1 -1h14a1 1 0 0 1 1 1z" /><path d="M12 15l0 4" /><path d="M14 17l-4 0" /></svg>
                                        </button>
                                        <button type="button"
                                                @click="removeRow(rowIndex)"
                                                x-show="state && state.length > 1"
                                                class="text-danger-500 hover:text-danger-700 focus:outline-none text-sm p-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Chart Preview -->
            <div x-show="isPreviewVisible" class="w-full">
                <div class="w-full flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Aperçu</h3>
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
    </div>
</x-dynamic-component>
