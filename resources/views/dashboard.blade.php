<!DOCTYPE html>
<html lang="en" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }" x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trackora Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eef9ff',
                            100: '#d8f1ff',
                            200: '#b9e7ff',
                            300: '#89daff',
                            400: '#52c4ff',
                            500: '#2aa7ff',
                            600: '#1489f8',
                            700: '#0d6fe4',
                            800: '#1259b9',
                            900: '#154c91',
                            950: '#122f58',
                        },
                        accent: {
                            50: '#fff8ed',
                            100: '#ffefd4',
                            200: '#ffdba8',
                            300: '#ffc171',
                            400: '#ff9c38',
                            500: '#ff7f11',
                            600: '#f06307',
                            700: '#c74a08',
                            800: '#9e3b0f',
                            900: '#7f3310',
                            950: '#451706',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        [x-cloak] { display: none !important; }
        .stat-card {
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
        }
        .gradient-border {
            position: relative;
        }
        .gradient-border::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0d6fe4, #2aa7ff, #ff7f11);
            border-radius: 0.75rem 0.75rem 0 0;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        .animate-delay-1 { animation-delay: 0.1s; }
        .animate-delay-2 { animation-delay: 0.2s; }
        .animate-delay-3 { animation-delay: 0.3s; }
        .animate-delay-4 { animation-delay: 0.4s; }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        ::-webkit-scrollbar-track {
            background: transparent;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: #475569;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
        .dark ::-webkit-scrollbar-thumb:hover {
            background: #64748b;
        }
    </style>
</head>
<body class="bg-slate-50 dark:bg-slate-900 min-h-screen transition-colors duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
        <!-- Header -->
        <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8 gap-4">
            <div class="flex items-center gap-4">
                <img src="{{ asset('vendor/trackora/logo.png') }}" alt="Trackora" class="h-12 sm:h-14 w-auto">
            </div>
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-4 w-full lg:w-auto">
                <!-- Theme Toggle -->
                <button @click="darkMode = !darkMode"
                        class="p-2.5 rounded-xl bg-white dark:bg-slate-800 shadow-sm border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all duration-200"
                        :title="darkMode ? 'Switch to light mode' : 'Switch to dark mode'">
                    <svg x-show="!darkMode" class="w-5 h-5 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                    </svg>
                    <svg x-show="darkMode" x-cloak class="w-5 h-5 text-slate-300" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                    </svg>
                </button>

                <!-- Period Selector -->
                <form method="GET" class="flex items-center gap-2">
                    <select name="period" id="period" onchange="this.form.submit()"
                            class="rounded-xl border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 shadow-sm focus:border-primary-500 focus:ring-primary-500 text-sm py-2.5 px-4 transition-colors duration-200">
                        <option value="7" {{ $period == 7 ? 'selected' : '' }}>Last 7 days</option>
                        <option value="30" {{ $period == 30 ? 'selected' : '' }}>Last 30 days</option>
                        <option value="90" {{ $period == 90 ? 'selected' : '' }}>Last 90 days</option>
                        <option value="365" {{ $period == 365 ? 'selected' : '' }}>Last year</option>
                    </select>
                </form>

                <!-- Export Button -->
                <a href="{{ route('trackora.export', ['period' => $period]) }}"
                   class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white text-sm font-medium rounded-xl shadow-sm shadow-primary-500/25 hover:shadow-md hover:shadow-primary-500/30 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Export CSV
                </a>
            </div>
        </header>

        @if(session('success'))
            <div class="mb-6 bg-emerald-50 dark:bg-emerald-900/30 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 px-4 py-3 rounded-xl animate-fade-in">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
            <!-- Total Visits -->
            <div class="stat-card gradient-border bg-white dark:bg-slate-800 rounded-xl shadow-sm shadow-slate-200/50 dark:shadow-slate-900/50 p-5 sm:p-6 opacity-0 animate-fade-in">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Total Visits</p>
                        <p class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-white mt-1">{{ number_format($totalVisits) }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-primary-50 dark:bg-primary-900/30">
                        <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Unique Visitors -->
            <div class="stat-card gradient-border bg-white dark:bg-slate-800 rounded-xl shadow-sm shadow-slate-200/50 dark:shadow-slate-900/50 p-5 sm:p-6 opacity-0 animate-fade-in animate-delay-1">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Unique Visitors</p>
                        <p class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-white mt-1">{{ number_format($uniqueVisitors) }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/30">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Today's Visits -->
            <div class="stat-card gradient-border bg-white dark:bg-slate-800 rounded-xl shadow-sm shadow-slate-200/50 dark:shadow-slate-900/50 p-5 sm:p-6 opacity-0 animate-fade-in animate-delay-2">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Today's Visits</p>
                        <p class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-white mt-1">{{ number_format($todayVisits) }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-accent-50 dark:bg-accent-900/30">
                        <svg class="w-6 h-6 text-accent-600 dark:text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Today's Unique -->
            <div class="stat-card gradient-border bg-white dark:bg-slate-800 rounded-xl shadow-sm shadow-slate-200/50 dark:shadow-slate-900/50 p-5 sm:p-6 opacity-0 animate-fade-in animate-delay-3">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-500 dark:text-slate-400">Today's Unique</p>
                        <p class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-white mt-1">{{ number_format($todayUniqueVisitors) }}</p>
                    </div>
                    <div class="p-3 rounded-xl bg-violet-50 dark:bg-violet-900/30">
                        <svg class="w-6 h-6 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm shadow-slate-200/50 dark:shadow-slate-900/50 p-5 sm:p-6 mb-8 opacity-0 animate-fade-in animate-delay-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-2">
                <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Visitor Trends</h2>
                <div class="flex items-center gap-4 text-sm">
                    <span class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-primary-500"></span>
                        <span class="text-slate-600 dark:text-slate-400">Total Visits</span>
                    </span>
                    <span class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                        <span class="text-slate-600 dark:text-slate-400">Unique Visitors</span>
                    </span>
                </div>
            </div>
            <div class="relative" style="height: 300px;">
                <canvas id="visitorChart"></canvas>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4 sm:gap-6 mb-8">
            <!-- Top Pages -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm shadow-slate-200/50 dark:shadow-slate-900/50 p-5 sm:p-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-2 rounded-lg bg-primary-50 dark:bg-primary-900/30">
                        <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Top Pages</h2>
                </div>
                <div class="space-y-3">
                    @forelse($topPages as $page)
                        <div class="flex justify-between items-center group">
                            <span class="text-slate-600 dark:text-slate-300 truncate max-w-[70%] text-sm group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">/{{ $page->page_visited }}</span>
                            <span class="text-slate-800 dark:text-slate-200 font-semibold text-sm bg-slate-100 dark:bg-slate-700 px-2.5 py-1 rounded-lg">{{ number_format($page->visits) }}</span>
                        </div>
                    @empty
                        <p class="text-slate-400 dark:text-slate-500 text-sm text-center py-4">No data available</p>
                    @endforelse
                </div>
            </div>

            <!-- Browsers -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm shadow-slate-200/50 dark:shadow-slate-900/50 p-5 sm:p-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-900/30">
                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Browsers</h2>
                </div>
                <div class="space-y-3">
                    @forelse($browserStats as $browser)
                        <div class="flex justify-between items-center group">
                            <span class="text-slate-600 dark:text-slate-300 text-sm group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $browser->browser }}</span>
                            <span class="text-slate-800 dark:text-slate-200 font-semibold text-sm bg-slate-100 dark:bg-slate-700 px-2.5 py-1 rounded-lg">{{ number_format($browser->count) }}</span>
                        </div>
                    @empty
                        <p class="text-slate-400 dark:text-slate-500 text-sm text-center py-4">No data available</p>
                    @endforelse
                </div>
            </div>

            <!-- Platforms -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm shadow-slate-200/50 dark:shadow-slate-900/50 p-5 sm:p-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-2 rounded-lg bg-violet-50 dark:bg-violet-900/30">
                        <svg class="w-4 h-4 text-violet-600 dark:text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Platforms</h2>
                </div>
                <div class="space-y-3">
                    @forelse($platformStats as $platform)
                        <div class="flex justify-between items-center group">
                            <span class="text-slate-600 dark:text-slate-300 text-sm group-hover:text-violet-600 dark:group-hover:text-violet-400 transition-colors">{{ $platform->platform }}</span>
                            <span class="text-slate-800 dark:text-slate-200 font-semibold text-sm bg-slate-100 dark:bg-slate-700 px-2.5 py-1 rounded-lg">{{ number_format($platform->count) }}</span>
                        </div>
                    @empty
                        <p class="text-slate-400 dark:text-slate-500 text-sm text-center py-4">No data available</p>
                    @endforelse
                </div>
            </div>

            <!-- Devices -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm shadow-slate-200/50 dark:shadow-slate-900/50 p-5 sm:p-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-2 rounded-lg bg-accent-50 dark:bg-accent-900/30">
                        <svg class="w-4 h-4 text-accent-600 dark:text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Devices</h2>
                </div>
                <div class="space-y-3">
                    @forelse($deviceStats as $device)
                        <div class="flex justify-between items-center group">
                            <span class="text-slate-600 dark:text-slate-300 text-sm group-hover:text-accent-600 dark:group-hover:text-accent-400 transition-colors">{{ $device->device_type }}</span>
                            <span class="text-slate-800 dark:text-slate-200 font-semibold text-sm bg-slate-100 dark:bg-slate-700 px-2.5 py-1 rounded-lg">{{ number_format($device->count) }}</span>
                        </div>
                    @empty
                        <p class="text-slate-400 dark:text-slate-500 text-sm text-center py-4">No data available</p>
                    @endforelse
                </div>
            </div>

            <!-- Countries -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm shadow-slate-200/50 dark:shadow-slate-900/50 p-5 sm:p-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-2 rounded-lg bg-sky-50 dark:bg-sky-900/30">
                        <svg class="w-4 h-4 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Top Countries</h2>
                </div>
                <div class="space-y-3">
                    @forelse($countryStats as $country)
                        <div class="flex justify-between items-center group">
                            <span class="text-slate-600 dark:text-slate-300 text-sm group-hover:text-sky-600 dark:group-hover:text-sky-400 transition-colors">{{ $country->country }}</span>
                            <span class="text-slate-800 dark:text-slate-200 font-semibold text-sm bg-slate-100 dark:bg-slate-700 px-2.5 py-1 rounded-lg">{{ number_format($country->count) }}</span>
                        </div>
                    @empty
                        <p class="text-slate-400 dark:text-slate-500 text-sm text-center py-4">No data available</p>
                    @endforelse
                </div>
            </div>

            <!-- Referrers -->
            <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm shadow-slate-200/50 dark:shadow-slate-900/50 p-5 sm:p-6">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-2 rounded-lg bg-rose-50 dark:bg-rose-900/30">
                        <svg class="w-4 h-4 text-rose-600 dark:text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Top Referrers</h2>
                </div>
                <div class="space-y-3">
                    @forelse($referrerStats as $referrer)
                        <div class="flex justify-between items-center group">
                            <span class="text-slate-600 dark:text-slate-300 truncate max-w-[70%] text-sm group-hover:text-rose-600 dark:group-hover:text-rose-400 transition-colors">{{ $referrer->referrer }}</span>
                            <span class="text-slate-800 dark:text-slate-200 font-semibold text-sm bg-slate-100 dark:bg-slate-700 px-2.5 py-1 rounded-lg">{{ number_format($referrer->count) }}</span>
                        </div>
                    @empty
                        <p class="text-slate-400 dark:text-slate-500 text-sm text-center py-4">No data available</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Visitors Table -->
        <div class="bg-white dark:bg-slate-800 rounded-xl shadow-sm shadow-slate-200/50 dark:shadow-slate-900/50 p-5 sm:p-6 mb-8">
            <div class="flex items-center gap-2 mb-6">
                <div class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-900/30">
                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-slate-800 dark:text-white">Recent Visitors</h2>
            </div>
            <div class="overflow-x-auto -mx-5 sm:-mx-6 px-5 sm:px-6">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-slate-700">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">IP Address</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Page</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden sm:table-cell">Browser</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden md:table-cell">Device</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden lg:table-cell">Location</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Time</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                        @forelse($recentVisitors as $visitor)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/50 transition-colors">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-slate-800 dark:text-slate-200 font-medium">{{ $visitor->ip_address }}</span>
                                        @if($visitor->is_unique)
                                            <span class="px-2 py-0.5 text-xs font-medium bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 rounded-full">New</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300 truncate max-w-[150px]">/{{ $visitor->page_visited }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300 hidden sm:table-cell">{{ $visitor->browser }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300 hidden md:table-cell">{{ $visitor->device_type }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-slate-600 dark:text-slate-300 hidden lg:table-cell">
                                    @if($visitor->city && $visitor->country)
                                        {{ $visitor->city }}, {{ $visitor->country }}
                                    @elseif($visitor->country)
                                        {{ $visitor->country }}
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-slate-500 dark:text-slate-400">{{ $visitor->created_at->diffForHumans() }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-400 dark:text-slate-500">No visitors recorded yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-6">
                {{ $recentVisitors->links() }}
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
            <form method="POST" action="{{ route('trackora.purge') }}" onsubmit="return confirm('Are you sure you want to purge old records?');">
                @csrf
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-xl shadow-sm shadow-amber-500/25 hover:shadow-md hover:shadow-amber-500/30 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Purge Old Records
                </button>
            </form>
            <form method="POST" action="{{ route('trackora.clear') }}" onsubmit="return confirm('Are you sure you want to clear ALL visitor records? This cannot be undone.');">
                @csrf
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white text-sm font-medium rounded-xl shadow-sm shadow-red-500/25 hover:shadow-md hover:shadow-red-500/30 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Clear All Records
                </button>
            </form>
        </div>

        <!-- Footer -->
        <footer class="mt-12 pt-6 border-t border-slate-200 dark:border-slate-700 text-center">
            <p class="text-sm text-slate-500 dark:text-slate-400">
                Powered by <span class="font-medium text-primary-600 dark:text-primary-400">Trackora</span> - A Powerful Laravel Package
            </p>
        </footer>
    </div>

    <script>
        // Visitor Chart with dark mode support
        const ctx = document.getElementById('visitorChart').getContext('2d');
        const dailyStats = @json($dailyStats);

        function getChartColors() {
            const isDark = document.documentElement.classList.contains('dark');
            return {
                primary: isDark ? '#60a5fa' : '#2aa7ff',
                primaryBg: isDark ? 'rgba(96, 165, 250, 0.1)' : 'rgba(42, 167, 255, 0.1)',
                secondary: isDark ? '#34d399' : '#10b981',
                secondaryBg: isDark ? 'rgba(52, 211, 153, 0.1)' : 'rgba(16, 185, 129, 0.1)',
                grid: isDark ? 'rgba(148, 163, 184, 0.1)' : 'rgba(148, 163, 184, 0.2)',
                text: isDark ? '#94a3b8' : '#64748b'
            };
        }

        let colors = getChartColors();

        const chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dailyStats.map(s => s.date),
                datasets: [
                    {
                        label: 'Total Visits',
                        data: dailyStats.map(s => s.total),
                        borderColor: colors.primary,
                        backgroundColor: colors.primaryBg,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: colors.primary,
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2
                    },
                    {
                        label: 'Unique Visitors',
                        data: dailyStats.map(s => s.unique_count),
                        borderColor: colors.secondary,
                        backgroundColor: colors.secondaryBg,
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: colors.secondary,
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: colors.text === '#94a3b8' ? '#1e293b' : '#fff',
                        titleColor: colors.text === '#94a3b8' ? '#fff' : '#1e293b',
                        bodyColor: colors.text === '#94a3b8' ? '#cbd5e1' : '#64748b',
                        borderColor: colors.text === '#94a3b8' ? '#334155' : '#e2e8f0',
                        borderWidth: 1,
                        cornerRadius: 8,
                        padding: 12,
                        displayColors: true,
                        boxPadding: 4
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: colors.text,
                            font: {
                                size: 11
                            }
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: colors.grid,
                            drawBorder: false
                        },
                        ticks: {
                            color: colors.text,
                            font: {
                                size: 11
                            }
                        }
                    }
                }
            }
        });

        // Update chart colors when dark mode changes
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    colors = getChartColors();
                    chart.data.datasets[0].borderColor = colors.primary;
                    chart.data.datasets[0].backgroundColor = colors.primaryBg;
                    chart.data.datasets[0].pointHoverBackgroundColor = colors.primary;
                    chart.data.datasets[1].borderColor = colors.secondary;
                    chart.data.datasets[1].backgroundColor = colors.secondaryBg;
                    chart.data.datasets[1].pointHoverBackgroundColor = colors.secondary;
                    chart.options.scales.x.ticks.color = colors.text;
                    chart.options.scales.y.ticks.color = colors.text;
                    chart.options.scales.y.grid.color = colors.grid;
                    chart.update();
                }
            });
        });

        observer.observe(document.documentElement, { attributes: true });
    </script>
</body>
</html>
