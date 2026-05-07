<x-layouts.app>
    <x-slot name="title">Jewellery Size Guide - {{ config('app.name') }}</x-slot>

    @push('meta')
        <meta name="description" content="Jewellery size guide at {{ config('app.name') }}. Find your perfect ring size, bangle size, necklace length and bracelet fit.">
        <link rel="canonical" href="{{ url('/size-guide') }}">
    @endpush

    <!-- Breadcrumb -->
    <div class="bg-neutral-50 border-b border-neutral-100">
        <div class="container mx-auto px-4 py-3">
            <x-breadcrumb :items="[['label' => 'Size Guide', 'url' => null]]" />
        </div>
    </div>

    <section class="py-10 sm:py-14">
        <div class="container mx-auto px-4">
            <div class="max-w-4xl mx-auto">

                <!-- Header -->
                <div class="text-center mb-10">
                    <h1 class="text-2xl sm:text-3xl font-bold text-neutral-900 mb-3">Jewellery Size Guide</h1>
                    <p class="text-sm text-neutral-600 max-w-xl mx-auto">Find your perfect fit. Use the charts below to determine your ring size, bangle size, necklace length and more.</p>
                </div>

                <!-- Tab Navigation -->
                <div x-data="{ activeTab: 'rings' }" class="space-y-8">
                    <div class="flex flex-wrap justify-center gap-2 sm:gap-3">
                        <button @click="activeTab = 'rings'" :class="activeTab === 'rings' ? 'bg-[#202a40] text-white shadow-lg shadow-[#202a40]/25' : 'bg-white text-neutral-600 hover:bg-neutral-50 border border-neutral-200'" class="px-4 py-2 text-sm font-medium rounded-lg transition-all">
                            Rings
                        </button>
                        <button @click="activeTab = 'bangles'" :class="activeTab === 'bangles' ? 'bg-[#202a40] text-white shadow-lg shadow-[#202a40]/25' : 'bg-white text-neutral-600 hover:bg-neutral-50 border border-neutral-200'" class="px-4 py-2 text-sm font-medium rounded-lg transition-all">
                            Bangles
                        </button>
                        <button @click="activeTab = 'necklaces'" :class="activeTab === 'necklaces' ? 'bg-[#202a40] text-white shadow-lg shadow-[#202a40]/25' : 'bg-white text-neutral-600 hover:bg-neutral-50 border border-neutral-200'" class="px-4 py-2 text-sm font-medium rounded-lg transition-all">
                            Necklaces
                        </button>
                        <button @click="activeTab = 'bracelets'" :class="activeTab === 'bracelets' ? 'bg-[#202a40] text-white shadow-lg shadow-[#202a40]/25' : 'bg-white text-neutral-600 hover:bg-neutral-50 border border-neutral-200'" class="px-4 py-2 text-sm font-medium rounded-lg transition-all">
                            Bracelets
                        </button>
                    </div>

                    <!-- Ring Size Chart -->
                    <div x-show="activeTab === 'rings'" x-transition>
                        <div class="bg-white rounded-xl border border-neutral-100 overflow-hidden mb-6">
                            <div class="px-5 py-4 border-b border-neutral-100">
                                <h2 class="text-lg font-bold text-neutral-900">Ring Size Chart (Indian Standard)</h2>
                                <p class="text-xs text-neutral-500 mt-1">Measure the inner diameter of a ring that fits you, or use a string around your finger.</p>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-neutral-50">
                                            <th class="px-4 py-3 text-left font-semibold text-neutral-700">Indian Size</th>
                                            <th class="px-4 py-3 text-left font-semibold text-neutral-700">US Size</th>
                                            <th class="px-4 py-3 text-left font-semibold text-neutral-700">Inner Diameter (mm)</th>
                                            <th class="px-4 py-3 text-left font-semibold text-neutral-700">Inner Circumference (mm)</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-neutral-100">
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">5</td><td class="px-4 py-2.5 text-neutral-600">3</td><td class="px-4 py-2.5 text-neutral-600">14.0</td><td class="px-4 py-2.5 text-neutral-600">44.0</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">6</td><td class="px-4 py-2.5 text-neutral-600">3.5</td><td class="px-4 py-2.5 text-neutral-600">14.4</td><td class="px-4 py-2.5 text-neutral-600">45.2</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">7</td><td class="px-4 py-2.5 text-neutral-600">4</td><td class="px-4 py-2.5 text-neutral-600">14.8</td><td class="px-4 py-2.5 text-neutral-600">46.5</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">8</td><td class="px-4 py-2.5 text-neutral-600">4.5</td><td class="px-4 py-2.5 text-neutral-600">15.2</td><td class="px-4 py-2.5 text-neutral-600">47.8</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">9</td><td class="px-4 py-2.5 text-neutral-600">5</td><td class="px-4 py-2.5 text-neutral-600">15.6</td><td class="px-4 py-2.5 text-neutral-600">49.0</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">10</td><td class="px-4 py-2.5 text-neutral-600">5.5</td><td class="px-4 py-2.5 text-neutral-600">16.0</td><td class="px-4 py-2.5 text-neutral-600">50.3</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">11</td><td class="px-4 py-2.5 text-neutral-600">6</td><td class="px-4 py-2.5 text-neutral-600">16.5</td><td class="px-4 py-2.5 text-neutral-600">51.8</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">12</td><td class="px-4 py-2.5 text-neutral-600">6.5</td><td class="px-4 py-2.5 text-neutral-600">16.9</td><td class="px-4 py-2.5 text-neutral-600">53.1</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">13</td><td class="px-4 py-2.5 text-neutral-600">7</td><td class="px-4 py-2.5 text-neutral-600">17.3</td><td class="px-4 py-2.5 text-neutral-600">54.4</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">14</td><td class="px-4 py-2.5 text-neutral-600">7.5</td><td class="px-4 py-2.5 text-neutral-600">17.7</td><td class="px-4 py-2.5 text-neutral-600">55.6</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">15</td><td class="px-4 py-2.5 text-neutral-600">8</td><td class="px-4 py-2.5 text-neutral-600">18.1</td><td class="px-4 py-2.5 text-neutral-600">56.9</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">16</td><td class="px-4 py-2.5 text-neutral-600">8.5</td><td class="px-4 py-2.5 text-neutral-600">18.5</td><td class="px-4 py-2.5 text-neutral-600">58.1</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">17</td><td class="px-4 py-2.5 text-neutral-600">9</td><td class="px-4 py-2.5 text-neutral-600">18.9</td><td class="px-4 py-2.5 text-neutral-600">59.4</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">18</td><td class="px-4 py-2.5 text-neutral-600">9.5</td><td class="px-4 py-2.5 text-neutral-600">19.4</td><td class="px-4 py-2.5 text-neutral-600">60.9</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">19</td><td class="px-4 py-2.5 text-neutral-600">10</td><td class="px-4 py-2.5 text-neutral-600">19.8</td><td class="px-4 py-2.5 text-neutral-600">62.2</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">20</td><td class="px-4 py-2.5 text-neutral-600">10.5</td><td class="px-4 py-2.5 text-neutral-600">20.2</td><td class="px-4 py-2.5 text-neutral-600">63.5</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">21</td><td class="px-4 py-2.5 text-neutral-600">11</td><td class="px-4 py-2.5 text-neutral-600">20.6</td><td class="px-4 py-2.5 text-neutral-600">64.7</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">22</td><td class="px-4 py-2.5 text-neutral-600">11.5</td><td class="px-4 py-2.5 text-neutral-600">21.0</td><td class="px-4 py-2.5 text-neutral-600">66.0</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">23</td><td class="px-4 py-2.5 text-neutral-600">12</td><td class="px-4 py-2.5 text-neutral-600">21.4</td><td class="px-4 py-2.5 text-neutral-600">67.2</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">24</td><td class="px-4 py-2.5 text-neutral-600">12.5</td><td class="px-4 py-2.5 text-neutral-600">21.8</td><td class="px-4 py-2.5 text-neutral-600">68.5</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- How to Measure Ring Size -->
                        <div class="bg-white rounded-xl border border-neutral-100 p-6">
                            <h3 class="text-base font-bold text-neutral-900 mb-4">How to Measure Your Ring Size</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                                <div>
                                    <div class="w-8 h-8 rounded-lg bg-[#202a40]/10 flex items-center justify-center mb-2 text-sm font-bold text-[#202a40]">1</div>
                                    <h4 class="text-sm font-semibold text-neutral-900 mb-1">String Method</h4>
                                    <p class="text-xs text-neutral-600 leading-relaxed">Wrap a thin string or paper strip around the base of your finger. Mark where it overlaps, then measure the length in mm.</p>
                                </div>
                                <div>
                                    <div class="w-8 h-8 rounded-lg bg-[#202a40]/10 flex items-center justify-center mb-2 text-sm font-bold text-[#202a40]">2</div>
                                    <h4 class="text-sm font-semibold text-neutral-900 mb-1">Existing Ring</h4>
                                    <p class="text-xs text-neutral-600 leading-relaxed">Place a ring that fits well on a ruler. Measure the inner diameter in mm and match it with the chart above.</p>
                                </div>
                                <div>
                                    <div class="w-8 h-8 rounded-lg bg-[#202a40]/10 flex items-center justify-center mb-2 text-sm font-bold text-[#202a40]">3</div>
                                    <h4 class="text-sm font-semibold text-neutral-900 mb-1">Best Practices</h4>
                                    <p class="text-xs text-neutral-600 leading-relaxed">Measure at the end of the day when fingers are slightly larger. Avoid measuring when cold. If between sizes, choose the larger one.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Bangle Size Chart -->
                    <div x-show="activeTab === 'bangles'" x-transition>
                        <div class="bg-white rounded-xl border border-neutral-100 overflow-hidden mb-6">
                            <div class="px-5 py-4 border-b border-neutral-100">
                                <h2 class="text-lg font-bold text-neutral-900">Bangle Size Chart (Indian Standard)</h2>
                                <p class="text-xs text-neutral-500 mt-1">Bangle sizes in India are measured by the inner diameter in inches.</p>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-neutral-50">
                                            <th class="px-4 py-3 text-left font-semibold text-neutral-700">Indian Size</th>
                                            <th class="px-4 py-3 text-left font-semibold text-neutral-700">Inner Diameter (inches)</th>
                                            <th class="px-4 py-3 text-left font-semibold text-neutral-700">Inner Diameter (mm)</th>
                                            <th class="px-4 py-3 text-left font-semibold text-neutral-700">Typical Fit</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-neutral-100">
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">2.2</td><td class="px-4 py-2.5 text-neutral-600">2.125</td><td class="px-4 py-2.5 text-neutral-600">54</td><td class="px-4 py-2.5 text-neutral-600">Extra Small</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">2.4</td><td class="px-4 py-2.5 text-neutral-600">2.250</td><td class="px-4 py-2.5 text-neutral-600">57</td><td class="px-4 py-2.5 text-neutral-600">Small</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">2.6</td><td class="px-4 py-2.5 text-neutral-600">2.375</td><td class="px-4 py-2.5 text-neutral-600">60</td><td class="px-4 py-2.5 text-neutral-600">Medium</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">2.8</td><td class="px-4 py-2.5 text-neutral-600">2.500</td><td class="px-4 py-2.5 text-neutral-600">64</td><td class="px-4 py-2.5 text-neutral-600">Large</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">2.10</td><td class="px-4 py-2.5 text-neutral-600">2.625</td><td class="px-4 py-2.5 text-neutral-600">67</td><td class="px-4 py-2.5 text-neutral-600">Extra Large</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">2.12</td><td class="px-4 py-2.5 text-neutral-600">2.750</td><td class="px-4 py-2.5 text-neutral-600">70</td><td class="px-4 py-2.5 text-neutral-600">XXL</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl border border-neutral-100 p-6">
                            <h3 class="text-base font-bold text-neutral-900 mb-4">How to Measure Your Bangle Size</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                <div>
                                    <div class="w-8 h-8 rounded-lg bg-[#202a40]/10 flex items-center justify-center mb-2 text-sm font-bold text-[#202a40]">1</div>
                                    <h4 class="text-sm font-semibold text-neutral-900 mb-1">Ruler Method</h4>
                                    <p class="text-xs text-neutral-600 leading-relaxed">Close your fingers together and bring your thumb to your little finger. Measure the widest part of your hand across the knuckles with a ruler.</p>
                                </div>
                                <div>
                                    <div class="w-8 h-8 rounded-lg bg-[#202a40]/10 flex items-center justify-center mb-2 text-sm font-bold text-[#202a40]">2</div>
                                    <h4 class="text-sm font-semibold text-neutral-900 mb-1">Existing Bangle</h4>
                                    <p class="text-xs text-neutral-600 leading-relaxed">Place a well-fitting bangle on a flat surface. Measure the inner diameter with a ruler and match with the chart.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Necklace Length Chart -->
                    <div x-show="activeTab === 'necklaces'" x-transition>
                        <div class="bg-white rounded-xl border border-neutral-100 overflow-hidden mb-6">
                            <div class="px-5 py-4 border-b border-neutral-100">
                                <h2 class="text-lg font-bold text-neutral-900">Necklace Length Guide</h2>
                                <p class="text-xs text-neutral-500 mt-1">Choose the right necklace length based on where you want it to sit.</p>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-neutral-50">
                                            <th class="px-4 py-3 text-left font-semibold text-neutral-700">Length</th>
                                            <th class="px-4 py-3 text-left font-semibold text-neutral-700">Style Name</th>
                                            <th class="px-4 py-3 text-left font-semibold text-neutral-700">Sits At</th>
                                            <th class="px-4 py-3 text-left font-semibold text-neutral-700">Best For</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-neutral-100">
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">14"</td><td class="px-4 py-2.5 text-neutral-600">Collar</td><td class="px-4 py-2.5 text-neutral-600">Tight around the neck</td><td class="px-4 py-2.5 text-neutral-600">Off-shoulder, strapless tops</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">16"</td><td class="px-4 py-2.5 text-neutral-600">Choker</td><td class="px-4 py-2.5 text-neutral-600">Base of the neck</td><td class="px-4 py-2.5 text-neutral-600">Crew necks, casual wear</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">18"</td><td class="px-4 py-2.5 text-neutral-600">Princess</td><td class="px-4 py-2.5 text-neutral-600">Just below the collarbone</td><td class="px-4 py-2.5 text-neutral-600">Most versatile &mdash; works with everything</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">20"</td><td class="px-4 py-2.5 text-neutral-600">Matinee</td><td class="px-4 py-2.5 text-neutral-600">Above the bust</td><td class="px-4 py-2.5 text-neutral-600">Business and casual wear</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">22"</td><td class="px-4 py-2.5 text-neutral-600">Matinee</td><td class="px-4 py-2.5 text-neutral-600">Top of the bust</td><td class="px-4 py-2.5 text-neutral-600">Pendants and layering</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">24"</td><td class="px-4 py-2.5 text-neutral-600">Opera</td><td class="px-4 py-2.5 text-neutral-600">Centre of the bust</td><td class="px-4 py-2.5 text-neutral-600">High necklines, formal wear</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">30"</td><td class="px-4 py-2.5 text-neutral-600">Rope</td><td class="px-4 py-2.5 text-neutral-600">Below the bust</td><td class="px-4 py-2.5 text-neutral-600">Layering, can be doubled up</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl border border-neutral-100 p-6">
                            <h3 class="text-base font-bold text-neutral-900 mb-3">How to Choose</h3>
                            <p class="text-xs text-neutral-600 leading-relaxed">Measure an existing necklace you like, or use a string around your neck to your desired length. For pendants, add 2-4 inches to your usual chain length so the pendant sits nicely.</p>
                        </div>
                    </div>

                    <!-- Bracelet Size Chart -->
                    <div x-show="activeTab === 'bracelets'" x-transition>
                        <div class="bg-white rounded-xl border border-neutral-100 overflow-hidden mb-6">
                            <div class="px-5 py-4 border-b border-neutral-100">
                                <h2 class="text-lg font-bold text-neutral-900">Bracelet Size Guide</h2>
                                <p class="text-xs text-neutral-500 mt-1">Measure your wrist to find the right bracelet size.</p>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="bg-neutral-50">
                                            <th class="px-4 py-3 text-left font-semibold text-neutral-700">Wrist Size (inches)</th>
                                            <th class="px-4 py-3 text-left font-semibold text-neutral-700">Wrist Size (cm)</th>
                                            <th class="px-4 py-3 text-left font-semibold text-neutral-700">Bracelet Length</th>
                                            <th class="px-4 py-3 text-left font-semibold text-neutral-700">Fit</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-neutral-100">
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">5.5" - 6"</td><td class="px-4 py-2.5 text-neutral-600">14 - 15.2</td><td class="px-4 py-2.5 text-neutral-600">6.5" - 7"</td><td class="px-4 py-2.5 text-neutral-600">Small</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">6" - 6.5"</td><td class="px-4 py-2.5 text-neutral-600">15.2 - 16.5</td><td class="px-4 py-2.5 text-neutral-600">7" - 7.5"</td><td class="px-4 py-2.5 text-neutral-600">Medium</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">6.5" - 7"</td><td class="px-4 py-2.5 text-neutral-600">16.5 - 17.8</td><td class="px-4 py-2.5 text-neutral-600">7.5" - 8"</td><td class="px-4 py-2.5 text-neutral-600">Large</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">7" - 7.5"</td><td class="px-4 py-2.5 text-neutral-600">17.8 - 19</td><td class="px-4 py-2.5 text-neutral-600">8" - 8.5"</td><td class="px-4 py-2.5 text-neutral-600">Extra Large</td></tr>
                                        <tr class="hover:bg-neutral-50/50"><td class="px-4 py-2.5 font-medium text-neutral-900">7.5" - 8"</td><td class="px-4 py-2.5 text-neutral-600">19 - 20.3</td><td class="px-4 py-2.5 text-neutral-600">8.5" - 9"</td><td class="px-4 py-2.5 text-neutral-600">XXL</td></tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="bg-white rounded-xl border border-neutral-100 p-6">
                            <h3 class="text-base font-bold text-neutral-900 mb-3">How to Measure Your Wrist</h3>
                            <p class="text-xs text-neutral-600 leading-relaxed">Wrap a flexible tape measure (or string) snugly around your wrist just below the wrist bone. Add 0.5" to 1" for a comfortable fit. For a loose fit, add 1" to 1.5".</p>
                        </div>
                    </div>
                </div>

                <!-- General Tips -->
                <div class="mt-10 bg-[#506282]/10 rounded-xl border border-[#506282]/20 p-6">
                    <h3 class="text-sm font-bold text-[#506282] mb-2">Sizing Tips</h3>
                    <ul class="text-xs text-[#506282] space-y-1.5 leading-relaxed">
                        <li>Finger sizes can vary by up to half a size throughout the day &mdash; measure in the evening for the most accurate reading.</li>
                        <li>Temperature affects finger size: fingers swell in heat and shrink in cold.</li>
                        <li>If you're between two sizes, choose the larger size for comfort.</li>
                        <li>For wider bands (6mm+), go up half a size from your standard measurement.</li>
                        <li>Not sure about your size? Our team is happy to help &mdash; <a href="{{ route('contact') }}" class="underline font-medium hover:text-[#506282]">contact us</a> for assistance.</li>
                    </ul>
                </div>

            </div>
        </div>
    </section>
</x-layouts.app>
