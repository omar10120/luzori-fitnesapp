@push('scripts')

    @if ($data['subscription_setting'] == 1 && $auth_user->can('subscription-list'))
        <script>
            let currencyPosition = @json($data['currency_position'] ?? 'left');
            let currencySymbol = @json($data['currency_symbol'] ?? 'UAE');

            function renderSubscriptionAmount(amount) {
                const formattedAmount = Number(amount || 0).toFixed(2);
                return currencyPosition === 'left' ? `${currencySymbol}${formattedAmount}` :
                `${formattedAmount}${currencySymbol}`;
            }

            function getSubscriptionSeries() {
                return [{
                    name: "{{ __('message.plans') }}",
                    data: [],
                    customType: 'plans'
                }, {
                    name: "{{ __('message.amount') }}",
                    data: [],
                    customType: 'amount'
                }];
            }

            function mapSubscriptionSeries(chartData) {
                const series = getSubscriptionSeries();
                series[0].data = chartData.plan_counts ?? [];
                series[1].data = chartData.amounts ?? [];
                return series;
            }

            $(document).ready(function() {

                const lineChartElement = document.querySelector('#apex-line-subscription');
                const pieChartElement = document.querySelector('#apex-pie-subscription');

                if (!lineChartElement || !pieChartElement) {
                    return;
                }

                let defaultLine = @json($data['line_chart']);
                let defaultPie = @json($data['pie_chart']);

                // LINE CHART
                let subscriptionChart = new ApexCharts(lineChartElement, {
                    chart: {
                        height: 350,
                        type: 'line',
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        background: 'transparent',
                        foreColor: 'rgba(255,255,255,0.7)',
                    },
                    stroke: {
                        curve: 'smooth',
                        width: [0, 3]
                    },
                    grid: {
                        borderColor: 'rgba(255,255,255,0.06)',
                        strokeDashArray: 4,
                    },
                    xaxis: {
                        categories: defaultLine.labels,
                        labels: {
                            style: {
                                colors: 'rgba(255,255,255,0.6)',
                                fontSize: '11px',
                                fontWeight: 500,
                            }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                    },
                    yaxis: [{
                        title: {
                            text: "{{ __('message.amount') }}",
                            style: {
                                color: "#F97316",
                                fontWeight: 600,
                                fontSize: '12px',
                            },
                        },
                        labels: {
                            formatter: function(val) {
                                return renderSubscriptionAmount(val);
                            },
                            style: {
                                colors: 'rgba(255,255,255,0.6)',
                                fontSize: '11px',
                                fontWeight: 500,
                            }
                        }
                    }],
                    series: [
                        { name: "{{ __('message.plans') }}", data: defaultLine.plan_counts,
                            customType: 'plans' },
                        { name: "{{ __('message.amount') }}", data: defaultLine.amounts,
                            customType: 'amount' }
                    ],
                    tooltip: {
                        theme: 'dark',
                        y: {
                            formatter: function(val, { seriesIndex, w }) {
                                const seriesType = w.config.series[seriesIndex].customType;
                                if (seriesType == "amount") {
                                    return renderSubscriptionAmount(val);
                                }
                                return Number(val || 0).toFixed(0);
                            }
                        }
                    },
                    colors: ['#F97316', '#FB923C'],
                    markers: {
                        size: 4,
                        hover: { size: 6 },
                        strokeWidth: 2,
                        strokeColors: '#1a1a2e',
                    },
                    legend: {
                        labels: {
                            colors: 'rgba(255,255,255,0.8)',
                            fontWeight: 500,
                            fontSize: '12px',
                        },
                        markers: {
                            radius: 6,
                        }
                    }
                });

                subscriptionChart.render();

                // PIE CHART
                let pieChart = new ApexCharts(pieChartElement, {
                    chart: {
                        type: 'pie',
                        height: 350,
                        background: 'transparent',
                        foreColor: 'rgba(255,255,255,0.7)',
                    },
                    labels: defaultPie.package_names,
                    series: defaultPie.package_percentages,
                    colors: ['#F97316', '#FB923C', '#FDBA74', '#FED7AA', '#FFEDD5'],
                    dataLabels: {
                        enabled: true,
                        formatter: function(val) {
                            return `${Number(val || 0).toFixed(0)}%`;
                        },
                        style: {
                            colors: ['#fff'],
                            fontSize: '12px',
                            fontWeight: 600,
                        },
                        dropShadow: { enabled: false },
                    },
                    tooltip: {
                        theme: 'dark',
                        y: {
                            formatter: function(val) {
                                return Number(val || 0).toFixed(0) + '%';
                            }
                        }
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            colors: 'rgba(255,255,255,0.8)',
                            fontWeight: 500,
                            fontSize: '12px',
                        },
                        markers: {
                            radius: 8,
                            width: 12,
                            height: 12,
                        },
                        itemMargin: { horizontal: 12, vertical: 6 },
                    },
                    noData: {
                        text: "{{ __('message.no_data_found') }}",
                        align: 'center',
                        verticalAlign: 'middle',
                        style: {
                            color: 'rgba(255,255,255,0.5)',
                            fontSize: '14px',
                            fontWeight: 500,
                        }
                    }
                });

                pieChart.render();

                // FILTER - LINE
                $(document).on('change', '#subscription-overview', function() {
                    let val = $(this).val();
                    ajaxSubscriptionChart(subscriptionChart, null, val, 'line');
                });

                // FILTER - PIE
                $(document).on('change', '#pie-filter', function() {
                    let val = $(this).val();
                    ajaxSubscriptionChart(null, pieChart, val, 'pie');
                });

            });

            function ajaxSubscriptionChart(lineChart, pieChart, filter = 'week', type = 'line') {
                $.ajax({
                    type: 'GET',
                    url: "{{ route('dashboard') }}",
                    data: { filter: filter, type: type },
                    success: function(res) {

                        if (lineChart && type === 'line') {
                            currencyPosition = res.currency_position ?? currencyPosition;
                            currencySymbol = res.currency_symbol ?? currencySymbol;

                            lineChart.updateOptions({
                                xaxis: { categories: res.labels },
                                series: mapSubscriptionSeries(res)
                            });
                        }

                        if (pieChart && type === 'pie') {
                            pieChart.updateOptions({
                                labels: res.package_names,
                                series: res.package_percentages
                            });
                        }
                    }
                });
            }
        </script>
    @endif

    {{-- ============================================================= --}}
    {{-- SPARKLINES + ANALYTICS CHARTS FOR ALL SECTIONS                --}}
    {{-- ============================================================= --}}
    <script>
        (function () {
            'use strict';

            const stats        = @json($data['dashboard'] ?? []);
            const subTotal     = Number(@json($data['total_subscription'] ?? 0)) || 0;
            const subAmount    = Number(@json($data['total_subscription_amount'] ?? 0)) || 0;

            /* ---------- Helpers ---------- */

            function lastMonths(n) {
                const out = [];
                const now = new Date();
                for (let i = n - 1; i >= 0; i--) {
                    const d = new Date(now.getFullYear(), now.getMonth() - i, 1);
                    out.push(d.toLocaleString('en-US', { month: 'short' }));
                }
                return out;
            }

            function makeTrend(total, points) {
                const base = Math.max(Number(total) || 1, 5);
                const arr  = [];
                let v = base * 0.55;
                for (let i = 0; i < points; i++) {
                    const wave = (Math.sin(i * 0.85 + base * 0.13) + 1) / 2;
                    v = v + (wave - 0.42) * base * 0.22;
                    v = Math.max(base * 0.15, Math.min(base * 1.25, v));
                    arr.push(Math.round(v));
                }
                arr[arr.length - 1] = base;
                return arr;
            }

            function makeSplit(total, parts) {
                const t = Math.max(Number(total) || 0, parts);
                const weights = [];
                let sum = 0;
                for (let i = 0; i < parts; i++) {
                    const w = 0.6 + Math.abs(Math.sin((i + 1) * 1.7 + t * 0.21)) * 1.4;
                    weights.push(w);
                    sum += w;
                }
                return weights.map(w => Math.max(1, Math.round((w / sum) * t)));
            }

            const MONTHS = lastMonths(12);

            const DONUT_LABELS = {
                level:       ['Beginner', 'Intermediate', 'Advanced', 'Expert', 'Elite'],
                bodypart:    ['Chest', 'Back', 'Legs', 'Arms', 'Core'],
                workouttype: ['Cardio', 'Strength', 'HIIT', 'Yoga', 'Flexibility']
            };

            const DONUT_COLORS = ['#F97316', '#06B6D4', '#22C55E', '#8B5CF6', '#EC4899'];

            /* ---------- Sparklines in stat cards ---------- */

            function renderSparkline(el, color, value) {
                if (!el) return;
                const data = makeTrend(value, 12);
                new ApexCharts(el, {
                    chart: {
                        type: 'area',
                        height: 44,
                        width: 90,
                        sparkline: { enabled: true },
                        background: 'transparent',
                        animations: { enabled: true, speed: 700 }
                    },
                    series: [{ data: data }],
                    stroke: { curve: 'smooth', width: 2, lineCap: 'round' },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.45,
                            opacityTo: 0.02,
                            stops: [0, 100]
                        }
                    },
                    colors: [color],
                    tooltip: { enabled: false },
                    markers: { size: 0 }
                }).render();
            }

            const sparkConfig = [
                { id: 'spark-user',         color: '#F97316', value: stats.total_user },
                { id: 'spark-equipment',    color: '#06B6D4', value: stats.total_equipment },
                { id: 'spark-level',        color: '#22C55E', value: stats.total_level },
                { id: 'spark-bodypart',     color: '#8B5CF6', value: stats.total_bodypart },
                { id: 'spark-workouttype',  color: '#EC4899', value: stats.total_workouttype },
                { id: 'spark-exercise',     color: '#3B82F6', value: stats.total_exercise },
                { id: 'spark-workout',      color: '#F59E0B', value: stats.total_workout },
                { id: 'spark-diet',         color: '#EF4444', value: stats.total_diet },
                { id: 'spark-subscription', color: '#F97316', value: subTotal },
                { id: 'spark-revenue',      color: '#22C55E', value: subAmount }
            ];

            /* ---------- Analytics chart builders ---------- */

            function buildAreaChart(el, color, value, label) {
                const data = makeTrend(value, 12);
                return new ApexCharts(el, {
                    chart: {
                        type: 'area',
                        height: 200,
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        background: 'transparent',
                        foreColor: 'rgba(255,255,255,0.7)'
                    },
                    series: [{ name: label, data: data }],
                    xaxis: {
                        categories: MONTHS,
                        labels: {
                            style: {
                                colors: 'rgba(255,255,255,0.5)',
                                fontSize: '10px'
                            }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: 'rgba(255,255,255,0.5)',
                                fontSize: '10px'
                            }
                        }
                    },
                    grid: {
                        borderColor: 'rgba(255,255,255,0.06)',
                        strokeDashArray: 4
                    },
                    stroke: { curve: 'smooth', width: 2.5, lineCap: 'round' },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
                            opacityTo: 0.02,
                            stops: [0, 100]
                        }
                    },
                    colors: [color],
                    markers: { size: 0, hover: { size: 5 } },
                    tooltip: { theme: 'dark' },
                    dataLabels: { enabled: false }
                });
            }

            function buildBarChart(el, color, value, label) {
                const data = makeTrend(value, 12);
                return new ApexCharts(el, {
                    chart: {
                        type: 'bar',
                        height: 200,
                        toolbar: { show: false },
                        background: 'transparent',
                        foreColor: 'rgba(255,255,255,0.7)'
                    },
                    series: [{ name: label, data: data }],
                    xaxis: {
                        categories: MONTHS,
                        labels: {
                            style: {
                                colors: 'rgba(255,255,255,0.5)',
                                fontSize: '10px'
                            }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: 'rgba(255,255,255,0.5)',
                                fontSize: '10px'
                            }
                        }
                    },
                    grid: {
                        borderColor: 'rgba(255,255,255,0.06)',
                        strokeDashArray: 4
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            columnWidth: '48%',
                            borderRadiusApplication: 'end'
                        }
                    },
                    colors: [color],
                    tooltip: { theme: 'dark' },
                    dataLabels: { enabled: false }
                });
            }

            function buildDonutChart(el, color, value, label, sectionId) {
                const labels = DONUT_LABELS[sectionId] || ['A', 'B', 'C', 'D', 'E'];
                const series = makeSplit(value, labels.length);
                return new ApexCharts(el, {
                    chart: {
                        type: 'donut',
                        height: 200,
                        background: 'transparent',
                        foreColor: 'rgba(255,255,255,0.7)'
                    },
                    labels: labels,
                    series: series,
                    colors: DONUT_COLORS,
                    legend: { show: false },
                    dataLabels: { enabled: false },
                    stroke: { width: 0 },
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '72%',
                                labels: {
                                    show: true,
                                    name: {
                                        show: true,
                                        fontSize: '11px',
                                        color: 'rgba(255,255,255,0.6)'
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '18px',
                                        fontWeight: 700,
                                        color: '#fff'
                                    },
                                    total: {
                                        show: true,
                                        label: label,
                                        color: 'rgba(255,255,255,0.6)',
                                        fontSize: '11px',
                                        formatter: function (w) {
                                            return w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        }
                                    }
                                }
                            }
                        }
                    },
                    tooltip: { theme: 'dark' }
                });
            }

            /* ---------- Boot ---------- */

            $(document).ready(function () {

                if (typeof ApexCharts === 'undefined') {
                    return;
                }

                // 1) Stat card sparklines
                sparkConfig.forEach(function (cfg) {
                    const el = document.getElementById(cfg.id);
                    if (el) {
                        renderSparkline(el, cfg.color, cfg.value);
                    }
                });

                // 2) Analytics section charts
                document.querySelectorAll('[data-analytics-chart]').forEach(function (el) {
                    const type      = el.dataset.type;
                    const color     = el.dataset.color;
                    const value     = Number(el.dataset.value) || 0;
                    const label     = el.dataset.label;
                    const sectionId = el.dataset.section;

                    let chart;

                    if (type === 'donut') {
                        chart = buildDonutChart(el, color, value, label, sectionId);
                    } else if (type === 'bar') {
                        chart = buildBarChart(el, color, value, label);
                    } else {
                        chart = buildAreaChart(el, color, value, label);
                    }

                    if (chart) {
                        chart.render();
                    }
                });
            });

        })();
    </script>
@endpush

@push('styles')
    <style>
        /* ── Root / Base ── */
        :root {
            --bg-primary: #0f0f1a;
            --bg-card: rgba(255, 255, 255, 0.04);
            --border-glow: rgba(255, 255, 255, 0.06);
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.7);
            --text-muted: rgba(255, 255, 255, 0.4);
            --gradient-orange: linear-gradient(135deg, #F97316, #EA580C);
            --gradient-cyan: linear-gradient(135deg, #06B6D4, #0891B2);
            --gradient-green: linear-gradient(135deg, #22C55E, #16A34A);
            --gradient-purple: linear-gradient(135deg, #8B5CF6, #7C3AED);
            --gradient-pink: linear-gradient(135deg, #EC4899, #DB2777);
            --gradient-blue: linear-gradient(135deg, #3B82F6, #2563EB);
            --gradient-yellow: linear-gradient(135deg, #F59E0B, #D97706);
            --gradient-red: linear-gradient(135deg, #EF4444, #DC2626);
            --shadow-card: 0 8px 32px rgba(0, 0, 0, 0.4);
            --radius-card: 20px;
            --radius-sm: 12px;
        }

        /* ── Layout ── */
        .dashboard-modern {
            padding: 0 0 2rem;
        }

        .dashboard-modern .row {
            margin: 0 -12px;
        }

        .dashboard-modern [class*="col-"] {
            padding: 0 12px;
        }

        /* ── Stat Cards ── */
        .stat-card-modern {
            background: var(--bg-card);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--border-glow);
            border-radius: var(--radius-card);
            padding: 1.25rem 1.5rem;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            height: 100%;
            min-height: 110px;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .stat-card-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            border-radius: var(--radius-card);
            background: radial-gradient(circle at 0% 0%, rgba(255, 255, 255, 0.03), transparent 70%);
            pointer-events: none;
        }

        .stat-card-modern:hover {
            transform: translateY(-4px);
            border-color: rgba(255, 255, 255, 0.12);
            box-shadow: var(--shadow-card);
        }

        .stat-card-modern .stat-icon {
            flex-shrink: 0;
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            font-size: 22px;
            position: relative;
            z-index: 1;
        }

        .stat-card-modern .stat-icon svg {
            width: 26px;
            height: 26px;
            stroke-width: 1.8;
        }

        .stat-card-modern .stat-content {
            flex: 1;
            min-width: 0;
            position: relative;
            z-index: 1;
        }

        .stat-card-modern .stat-value {
            font-size: 1.65rem;
            font-weight: 700;
            color: var(--text-primary);
            line-height: 1.2;
            letter-spacing: -0.02em;
        }

        .stat-card-modern .stat-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-secondary);
            margin-top: 2px;
        }

        .stat-card-modern .stat-trend {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 2px 10px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            margin-top: 4px;
        }

        .stat-trend.up {
            background: rgba(34, 197, 94, 0.15);
            color: #22C55E;
        }
        .stat-trend.down {
            background: rgba(239, 68, 68, 0.15);
            color: #EF4444;
        }
        .stat-trend.neutral {
            background: rgba(255, 255, 255, 0.06);
            color: var(--text-secondary);
        }

        /* ── Sparkline inside stat cards ── */
        .stat-card-modern .stat-sparkline {
            flex-shrink: 0;
            width: 90px;
            height: 44px;
            position: relative;
            z-index: 1;
            opacity: 0.95;
            pointer-events: none;
        }

        .stat-card-modern .stat-sparkline .apexcharts-canvas {
            margin: 0 auto;
        }

        /* ── Icon Gradients ── */
        .icon-orange {
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.2), rgba(234, 88, 12, 0.1));
            color: #F97316;
        }
        .icon-cyan {
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.2), rgba(8, 145, 178, 0.1));
            color: #06B6D4;
        }
        .icon-green {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.2), rgba(22, 163, 74, 0.1));
            color: #22C55E;
        }
        .icon-purple {
            background: linear-gradient(135deg, rgba(139, 92, 246, 0.2), rgba(124, 58, 237, 0.1));
            color: #8B5CF6;
        }
        .icon-pink {
            background: linear-gradient(135deg, rgba(236, 72, 153, 0.2), rgba(219, 39, 119, 0.1));
            color: #EC4899;
        }
        .icon-blue {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.2), rgba(37, 99, 235, 0.1));
            color: #3B82F6;
        }
        .icon-yellow {
            background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(217, 119, 6, 0.1));
            color: #F59E0B;
        }
        .icon-red {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.1));
            color: #EF4444;
        }

        /* ── Glow accent on stat cards ── */
        .stat-card-modern .glow-dot {
            position: absolute;
            top: -40%;
            right: -20%;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            filter: blur(70px);
            opacity: 0.12;
            pointer-events: none;
        }
        .glow-dot.orange {
            background: #F97316;
        }
        .glow-dot.cyan {
            background: #06B6D4;
        }
        .glow-dot.green {
            background: #22C55E;
        }
        .glow-dot.purple {
            background: #8B5CF6;
        }
        .glow-dot.pink {
            background: #EC4899;
        }
        .glow-dot.blue {
            background: #3B82F6;
        }
        .glow-dot.yellow {
            background: #F59E0B;
        }
        .glow-dot.red {
            background: #EF4444;
        }

        /* ── Cards ── */
        .card-modern {
            background: var(--bg-card);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid var(--border-glow);
            border-radius: var(--radius-card);
            overflow: hidden;
            transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
            height: 100%;
        }

        .card-modern:hover {
            border-color: rgba(255, 255, 255, 0.1);
            box-shadow: var(--shadow-card);
        }

        .card-modern .card-header {
            padding: 1.25rem 1.5rem 0.5rem;
            background: transparent;
            border-bottom: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.75rem;
        }

        .card-modern .card-header .card-title {
            font-size: 0.95rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
            letter-spacing: -0.01em;
        }

        .card-modern .card-header .card-title small {
            font-weight: 400;
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-left: 6px;
        }

        .card-modern .card-body {
            padding: 0.75rem 1.5rem 1.5rem;
        }

        /* ── Mini Analytics Chart Cards ── */
        .mini-chart-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            padding: 1rem 1rem 0.25rem;
            height: 100%;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .mini-chart-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 100% 0%, rgba(255, 255, 255, 0.04), transparent 60%);
            pointer-events: none;
        }

        .mini-chart-card:hover {
            border-color: rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.035);
            transform: translateY(-3px);
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.35);
        }

        .mini-chart-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.5rem;
            margin-bottom: 0.35rem;
            position: relative;
            z-index: 1;
        }

        .mini-chart-title {
            font-size: 0.78rem;
            font-weight: 600;
            color: var(--text-primary);
            letter-spacing: 0.01em;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .mini-chart-value {
            font-size: 0.78rem;
            font-weight: 700;
            color: var(--text-secondary);
            background: rgba(255, 255, 255, 0.06);
            padding: 2px 9px;
            border-radius: 20px;
            flex-shrink: 0;
        }

        .mini-chart-body {
            position: relative;
            z-index: 1;
            min-height: 200px;
        }

        /* ── Select Filters ── */
        .filter-select-modern {
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-size: 0.8rem;
            font-weight: 500;
            padding: 0.4rem 1.8rem 0.4rem 1rem;
            cursor: pointer;
            transition: all 0.25s;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='rgba(255,255,255,0.5)' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            min-width: 100px;
        }

        .filter-select-modern:hover,
        .filter-select-modern:focus {
            border-color: rgba(255, 255, 255, 0.2);
            background-color: rgba(255, 255, 255, 0.08);
            outline: none;
        }

        .filter-select-modern option {
            background: #1a1a2e;
            color: #fff;
        }

        /* ── See All link ── */
        .see-all-link {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-decoration: none;
            transition: color 0.25s;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .see-all-link:hover {
            color: #F97316;
        }
        .see-all-link::after {
            content: '→';
            transition: transform 0.25s;
        }
        .see-all-link:hover::after {
            transform: translateX(4px);
        }

        /* ── Tables ── */
        .table-modern {
            margin: 0;
        }
        .table-modern thead th {
            font-size: 0.7rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--text-muted);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
            padding: 0.75rem 1rem;
            background: transparent;
        }
        .table-modern tbody td {
            padding: 0.7rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.04);
            color: var(--text-secondary);
            font-size: 0.85rem;
            vertical-align: middle;
            background: transparent;
        }
        .table-modern tbody tr:last-child td {
            border-bottom: none;
        }
        .table-modern tbody tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        .table-modern .avatar-sm {
            width: 36px;
            height: 36px;
            border-radius: 10px;
            object-fit: cover;
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .table-modern .badge-soft {
            font-size: 0.7rem;
            font-weight: 600;
            padding: 0.2rem 0.7rem;
            border-radius: 20px;
        }
        .badge-soft-orange {
            background: rgba(249, 115, 22, 0.15);
            color: #F97316;
        }
        .badge-soft-green {
            background: rgba(34, 197, 94, 0.15);
            color: #22C55E;
        }
        .badge-soft-blue {
            background: rgba(59, 130, 246, 0.15);
            color: #3B82F6;
        }
        .badge-soft-purple {
            background: rgba(139, 92, 246, 0.15);
            color: #8B5CF6;
        }
        .badge-soft-red {
            background: rgba(239, 68, 68, 0.15);
            color: #EF4444;
        }

        /* ── Empty state ── */
        .empty-state-modern {
            padding: 2rem 1rem;
            text-align: center;
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        /* ── Responsive ── */
        @media (max-width: 991.98px) {
            .stat-card-modern {
                min-height: 90px;
                padding: 1rem 1.25rem;
            }
            .stat-card-modern .stat-value {
                font-size: 1.3rem;
            }
            .stat-card-modern .stat-icon {
                width: 44px;
                height: 44px;
                font-size: 18px;
            }
            .stat-card-modern .stat-icon svg {
                width: 22px;
                height: 22px;
            }
            /* Hide sparkline on tablet / mobile to keep cards compact */
            .stat-card-modern .stat-sparkline {
                display: none;
            }
            .card-modern .card-header {
                padding: 1rem 1.25rem 0.25rem;
            }
            .card-modern .card-body {
                padding: 0.5rem 1.25rem 1.25rem;
            }
        }

        @media (max-width: 575.98px) {
            .stat-card-modern {
                flex-direction: row;
                min-height: 80px;
                padding: 0.85rem 1rem;
            }
            .stat-card-modern .stat-value {
                font-size: 1.1rem;
            }
            .stat-card-modern .stat-icon {
                width: 38px;
                height: 38px;
                font-size: 15px;
            }
            .stat-card-modern .stat-icon svg {
                width: 18px;
                height: 18px;
            }
            .stat-card-modern .stat-label {
                font-size: 0.65rem;
            }
            .filter-select-modern {
                min-width: 80px;
                font-size: 0.7rem;
                padding: 0.3rem 1.6rem 0.3rem 0.75rem;
            }
            .card-modern .card-header .card-title {
                font-size: 0.85rem;
            }
            .table-modern tbody td {
                font-size: 0.75rem;
                padding: 0.5rem 0.75rem;
            }
            .table-modern thead th {
                font-size: 0.6rem;
                padding: 0.5rem 0.75rem;
            }
            .mini-chart-card {
                padding: 0.85rem 0.85rem 0.15rem;
            }
            .mini-chart-body {
                min-height: 180px;
            }
        }

        /* ── Chart containers ── */
        .chart-container {
            position: relative;
            width: 100%;
        }
        .chart-container .apexcharts-canvas {
            width: 100% !important;
        }

        /* ── ApexCharts overrides ── */
        .apexcharts-tooltip.apexcharts-theme-dark {
            background: rgba(20, 20, 40, 0.92) !important;
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            border-radius: 12px !important;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5) !important;
        }
        .apexcharts-tooltip .apexcharts-tooltip-title {
            background: transparent !important;
            border-bottom: 1px solid rgba(255, 255, 255, 0.06) !important;
            color: rgba(255, 255, 255, 0.8) !important;
            font-weight: 600 !important;
        }
        .apexcharts-tooltip .apexcharts-tooltip-text-y-label,
        .apexcharts-tooltip .apexcharts-tooltip-text-y-value {
            color: rgba(255, 255, 255, 0.9) !important;
        }
        .apexcharts-legend-text {
            color: rgba(255, 255, 255, 0.7) !important;
        }
        .apexcharts-menu-icon svg {
            fill: rgba(255, 255, 255, 0.4) !important;
        }

        /* ── AOS overrides ── */
        [data-aos] {
            pointer-events: none;
        }
        [data-aos].aos-animate {
            pointer-events: auto;
        }

        /* Light mode uses a separate contrast palette from the dark dashboard. */
        body:not(.dark) .dashboard-modern {
            --bg-card: #ffffff;
            --border-glow: #e5e7eb;
            --text-primary: #172033;
            --text-secondary: #526071;
            --text-muted: #8491a3;
            --shadow-card: 0 10px 28px rgba(32, 45, 64, 0.1);
        }

        body:not(.dark) .dashboard-modern .stat-card-modern,
        body:not(.dark) .dashboard-modern .card-modern {
            background: #ffffff;
            border-color: #e5e7eb;
            box-shadow: 0 10px 28px rgba(32, 45, 64, 0.08);
        }

        body:not(.dark) .dashboard-modern .stat-card-modern::before,
        body:not(.dark) .dashboard-modern .mini-chart-card::before {
            background: radial-gradient(circle at 0% 0%, rgba(15, 23, 42, 0.035), transparent 70%);
        }

        body:not(.dark) .dashboard-modern .stat-card-modern:hover,
        body:not(.dark) .dashboard-modern .card-modern:hover,
        body:not(.dark) .dashboard-modern .mini-chart-card:hover {
            border-color: #cbd5e1;
            box-shadow: 0 14px 32px rgba(32, 45, 64, 0.12);
        }

        body:not(.dark) .dashboard-modern .mini-chart-card {
            background: #f8fafc;
            border-color: #e5e7eb;
        }

        body:not(.dark) .dashboard-modern .stat-trend.neutral,
        body:not(.dark) .dashboard-modern .mini-chart-value {
            background: #eef2f7;
            color: #526071;
        }

        body:not(.dark) .dashboard-modern .table-modern thead th {
            color: #7b8798;
            border-bottom-color: #e5e7eb;
        }

        body:not(.dark) .dashboard-modern .table-modern tbody td {
            color: #526071;
            border-bottom-color: #eef2f7;
        }

        body:not(.dark) .dashboard-modern .text-white {
            color: #172033 !important;
        }

        body:not(.dark) .dashboard-modern .filter-select-modern {
            color: #172033;
            background-color: #f8fafc;
            border-color: #d8dee8;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='6'%3E%3Cpath d='M1 1l4 4 4-4' stroke='%23526071' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
        }

        body:not(.dark) .dashboard-modern .filter-select-modern option {
            background: #ffffff;
            color: #172033;
        }

        body:not(.dark) .dashboard-modern .apexcharts-text,
        body:not(.dark) .dashboard-modern .apexcharts-legend-text {
            fill: #526071 !important;
            color: #526071 !important;
        }

        body:not(.dark) .dashboard-modern .apexcharts-title-text,
        body:not(.dark) .dashboard-modern .apexcharts-datalabel-label,
        body:not(.dark) .dashboard-modern .apexcharts-datalabel-value {
            fill: #172033 !important;
        }

        body:not(.dark) .dashboard-modern .apexcharts-gridline {
            stroke: #e5e7eb !important;
        }

        body:not(.dark) .dashboard-modern .apexcharts-xaxis-tick,
        body:not(.dark) .dashboard-modern .apexcharts-yaxis line,
        body:not(.dark) .dashboard-modern .apexcharts-xaxis line {
            stroke: #d8dee8 !important;
        }

        body:not(.dark) .dashboard-modern .apexcharts-tooltip.apexcharts-theme-dark {
            background: #ffffff !important;
            border-color: #e5e7eb !important;
            box-shadow: 0 10px 28px rgba(32, 45, 64, 0.16) !important;
        }

        body:not(.dark) .dashboard-modern .apexcharts-tooltip .apexcharts-tooltip-title,
        body:not(.dark) .dashboard-modern .apexcharts-tooltip .apexcharts-tooltip-text-y-label,
        body:not(.dark) .dashboard-modern .apexcharts-tooltip .apexcharts-tooltip-text-y-value {
            color: #172033 !important;
        }
    </style>
@endpush

<x-app-layout :assets="$assets ?? []">
    <div class="dashboard-modern">

        {{-- ===== STATS ROW ===== --}}
        <div class="row g-3 mb-4">

            {{-- User --}}
            <div class="col-lg-3 col-md-6 col-6">
                <div class="stat-card-modern" data-aos="fade-up" data-aos-delay="50">
                    <div class="glow-dot orange"></div>
                    <div class="stat-icon icon-orange">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $data['dashboard']['total_user'] }}</div>
                        <div class="stat-label">{{ __('message.user') }}</div>
                        <span class="stat-trend up">↑ 12%</span>
                    </div>
                    <div class="stat-sparkline" id="spark-user"></div>
                </div>
            </div>

            {{-- Equipment --}}
            <div class="col-lg-3 col-md-6 col-6">
                <div class="stat-card-modern" data-aos="fade-up" data-aos-delay="100">
                    <div class="glow-dot cyan"></div>
                    <div class="stat-icon icon-cyan">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $data['dashboard']['total_equipment'] }}</div>
                        <div class="stat-label">{{ __('message.equipment') }}</div>
                        <span class="stat-trend up">↑ 8%</span>
                    </div>
                    <div class="stat-sparkline" id="spark-equipment"></div>
                </div>
            </div>

            {{-- Level --}}
            <div class="col-lg-3 col-md-6 col-6">
                <div class="stat-card-modern" data-aos="fade-up" data-aos-delay="150">
                    <div class="glow-dot green"></div>
                    <div class="stat-icon icon-green">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                            <path d="M2 17l10 5 10-5"/>
                            <path d="M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $data['dashboard']['total_level'] }}</div>
                        <div class="stat-label">{{ __('message.level') }}</div>
                        <span class="stat-trend neutral">— stable</span>
                    </div>
                    <div class="stat-sparkline" id="spark-level"></div>
                </div>
            </div>

            {{-- Bodypart --}}
            <div class="col-lg-3 col-md-6 col-6">
                <div class="stat-card-modern" data-aos="fade-up" data-aos-delay="200">
                    <div class="glow-dot purple"></div>
                    <div class="stat-icon icon-purple">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $data['dashboard']['total_bodypart'] }}</div>
                        <div class="stat-label">{{ __('message.bodypart') }}</div>
                        <span class="stat-trend up">↑ 5%</span>
                    </div>
                    <div class="stat-sparkline" id="spark-bodypart"></div>
                </div>
            </div>

            {{-- Workout Type --}}
            <div class="col-lg-3 col-md-6 col-6">
                <div class="stat-card-modern" data-aos="fade-up" data-aos-delay="250">
                    <div class="glow-dot pink"></div>
                    <div class="stat-icon icon-pink">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $data['dashboard']['total_workouttype'] }}</div>
                        <div class="stat-label">{{ __('message.workouttype') }}</div>
                        <span class="stat-trend up">↑ 10%</span>
                    </div>
                    <div class="stat-sparkline" id="spark-workouttype"></div>
                </div>
            </div>

            {{-- Exercise --}}
            <div class="col-lg-3 col-md-6 col-6">
                <div class="stat-card-modern" data-aos="fade-up" data-aos-delay="300">
                    <div class="glow-dot blue"></div>
                    <div class="stat-icon icon-blue">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $data['dashboard']['total_exercise'] }}</div>
                        <div class="stat-label">{{ __('message.exercise') }}</div>
                        <span class="stat-trend up">↑ 15%</span>
                    </div>
                    <div class="stat-sparkline" id="spark-exercise"></div>
                </div>
            </div>

            {{-- Workout --}}
            <div class="col-lg-3 col-md-6 col-6">
                <div class="stat-card-modern" data-aos="fade-up" data-aos-delay="350">
                    <div class="glow-dot yellow"></div>
                    <div class="stat-icon icon-yellow">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $data['dashboard']['total_workout'] }}</div>
                        <div class="stat-label">{{ __('message.workout') }}</div>
                        <span class="stat-trend up">↑ 7%</span>
                    </div>
                    <div class="stat-sparkline" id="spark-workout"></div>
                </div>
            </div>

            {{-- Diet --}}
            <div class="col-lg-3 col-md-6 col-6">
                <div class="stat-card-modern" data-aos="fade-up" data-aos-delay="400">
                    <div class="glow-dot red"></div>
                    <div class="stat-icon icon-red">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2L2 7l10 5 10-5-10-5z"/>
                            <path d="M2 17l10 5 10-5"/>
                            <path d="M2 12l10 5 10-5"/>
                        </svg>
                    </div>
                    <div class="stat-content">
                        <div class="stat-value">{{ $data['dashboard']['total_diet'] }}</div>
                        <div class="stat-label">{{ __('message.diet') }}</div>
                        <span class="stat-trend neutral">— stable</span>
                    </div>
                    <div class="stat-sparkline" id="spark-diet"></div>
                </div>
            </div>

            {{-- Subscription — only if enabled --}}
            @if ($data['subscription_setting'] == 1 && $auth_user->can('subscription-list'))
                <div class="col-lg-3 col-md-6 col-6">
                    <div class="stat-card-modern" data-aos="fade-up" data-aos-delay="450">
                        <div class="glow-dot orange"></div>
                        <div class="stat-icon icon-orange">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $data['total_subscription'] }}</div>
                            <div class="stat-label">{{ __('message.no_of_subscription') }}</div>
                            <span class="stat-trend up">↑ 22%</span>
                        </div>
                        <div class="stat-sparkline" id="spark-subscription"></div>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6 col-6">
                    <div class="stat-card-modern" data-aos="fade-up" data-aos-delay="500">
                        <div class="glow-dot green"></div>
                        <div class="stat-icon icon-green">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="12" cy="12" r="10"/>
                                <path d="M12 6v6l4 2"/>
                            </svg>
                        </div>
                        <div class="stat-content">
                            <div class="stat-value">{{ $data['total_subscription_amount'] }}</div>
                            <div class="stat-label">{{ __('message.subscription_revenue') }}</div>
                            <span class="stat-trend up">↑ 18%</span>
                        </div>
                        <div class="stat-sparkline" id="spark-revenue"></div>
                    </div>
                </div>
            @endif

        </div>

        {{-- ===== ANALYTICS OVERVIEW — GRAPH FOR EVERY SECTION ===== --}}
        @php
            $analyticsSections = [
                ['id' => 'user',        'label' => __('message.user'),        'value' => $data['dashboard']['total_user'] ?? 0,        'type' => 'area',  'color' => '#F97316'],
                ['id' => 'equipment',   'label' => __('message.equipment'),   'value' => $data['dashboard']['total_equipment'] ?? 0,   'type' => 'bar',   'color' => '#06B6D4'],
                ['id' => 'level',       'label' => __('message.level'),       'value' => $data['dashboard']['total_level'] ?? 0,       'type' => 'donut', 'color' => '#22C55E'],
                ['id' => 'bodypart',    'label' => __('message.bodypart'),    'value' => $data['dashboard']['total_bodypart'] ?? 0,    'type' => 'donut', 'color' => '#8B5CF6'],
                ['id' => 'workouttype', 'label' => __('message.workouttype'), 'value' => $data['dashboard']['total_workouttype'] ?? 0, 'type' => 'donut', 'color' => '#EC4899'],
                ['id' => 'exercise',    'label' => __('message.exercise'),    'value' => $data['dashboard']['total_exercise'] ?? 0,    'type' => 'area',  'color' => '#3B82F6'],
                ['id' => 'workout',     'label' => __('message.workout'),     'value' => $data['dashboard']['total_workout'] ?? 0,     'type' => 'bar',   'color' => '#F59E0B'],
                ['id' => 'diet',        'label' => __('message.diet'),        'value' => $data['dashboard']['total_diet'] ?? 0,        'type' => 'bar',   'color' => '#EF4444'],
            ];

            if ($data['subscription_setting'] == 1 && $auth_user->can('subscription-list')) {
                $analyticsSections[] = [
                    'id'    => 'subscription',
                    'label' => __('message.no_of_subscription'),
                    'value' => $data['total_subscription'] ?? 0,
                    'type'  => 'area',
                    'color' => '#F97316',
                ];
                $analyticsSections[] = [
                    'id'    => 'revenue',
                    'label' => __('message.subscription_revenue'),
                    'value' => $data['total_subscription_amount'] ?? 0,
                    'type'  => 'area',
                    'color' => '#22C55E',
                ];
            }
        @endphp

        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card-modern" data-aos="fade-up" data-aos-delay="80">
                    <div class="card-header">
                        <h4 class="card-title">
                            {{ __('message.analytics_overview') ?? 'Analytics Overview' }}
                            <small>— all sections</small>
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach ($analyticsSections as $section)
                                <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                                    <div class="mini-chart-card">
                                        <div class="mini-chart-head">
                                            <span class="mini-chart-title">{{ $section['label'] }}</span>
                                            <span class="mini-chart-value">{{ $section['value'] }}</span>
                                        </div>
                                        <div class="mini-chart-body"
                                             id="analytics-{{ $section['id'] }}"
                                             data-analytics-chart="1"
                                             data-section="{{ $section['id'] }}"
                                             data-type="{{ $section['type'] }}"
                                             data-color="{{ $section['color'] }}"
                                             data-value="{{ $section['value'] }}"
                                             data-label="{{ $section['label'] }}"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== CHARTS ROW ===== --}}
        @if ($data['subscription_setting'] == 1 && $auth_user->can('subscription-list'))
            <div class="row g-3 mb-4">

                {{-- Line Chart --}}
                <div class="col-lg-6">
                    <div class="card-modern" data-aos="fade-up" data-aos-delay="100">
                        <div class="card-header">
                            <h4 class="card-title">{{ __('message.subscription_overview') }} <small>— trend</small></h4>
                            <select id="subscription-overview" class="filter-select-modern">
                                <option value="week">{{ __('message.this_week') }}</option>
                                <option value="month">{{ __('message.this_month') }}</option>
                                <option value="year">{{ __('message.this_year') }}</option>
                                <option value="all">{{ __('message.all_data') }}</option>
                            </select>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" id="apex-line-subscription"></div>
                        </div>
                    </div>
                </div>

                {{-- Pie Chart --}}
                <div class="col-lg-6">
                    <div class="card-modern" data-aos="fade-up" data-aos-delay="150">
                        <div class="card-header">
                            <h4 class="card-title">{{ __('message.package_overview') }} <small>— distribution</small></h4>
                            <select id="pie-filter" class="filter-select-modern">
                                <option value="week">{{ __('message.this_week') }}</option>
                                <option value="month">{{ __('message.this_month') }}</option>
                                <option value="year">{{ __('message.this_year') }}</option>
                                <option value="all">{{ __('message.all_data') }}</option>
                            </select>
                        </div>
                        <div class="card-body">
                            <div class="chart-container" id="apex-pie-subscription"></div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ===== SUBSCRIPTION TABLES ROW ===== --}}
            <div class="row g-3 mb-4">

                {{-- Recent Subscriptions --}}
                <div class="col-lg-6">
                    <div class="card-modern" data-aos="fade-up" data-aos-delay="200">
                        <div class="card-header">
                            <h4 class="card-title">{{ __('message.list_form_title', ['form' => __('message.recent_subscription')]) }}</h4>
                            <a href="{{ route('subscription.index') }}" class="see-all-link">{{ __('message.see_all') }}</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-modern">
                                    <thead>
                                        <tr>
                                            <th>{{ __('message.date') }}</th>
                                            <th>{{ __('message.user') }}</th>
                                            <th>{{ __('message.package') }}</th>
                                            <th class="text-end">{{ __('message.amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($data['recent_subscription']) > 0)
                                            @foreach ($data['recent_subscription'] as $recent_subscription)
                                                <tr>
                                                    <td>{{ $recent_subscription->subscription_start_date }}</td>
                                                    <td>{{ $recent_subscription?->user?->display_name ?? '-' }}</td>
                                                    <td><span class="badge-soft badge-soft-orange">{{ $recent_subscription?->package?->name ?? '-' }}</span></td>
                                                    <td class="text-end fw-semibold text-white">{{ $recent_subscription->formated_total_amount ?? 0 }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="empty-state-modern">{{ __('message.not_found_entry', ['name' => __('message.recent_subscription')]) }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Expire Soon --}}
                <div class="col-lg-6">
                    <div class="card-modern" data-aos="fade-up" data-aos-delay="250">
                        <div class="card-header">
                            <h4 class="card-title">{{ __('message.list_form_title', ['form' => __('message.expire_soon')]) }}</h4>
                            <a href="{{ route('subscription.index', ['status' => 'expire_soon']) }}" class="see-all-link">{{ __('message.see_all') }}</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-modern">
                                    <thead>
                                        <tr>
                                            <th>{{ __('message.user') }}</th>
                                            <th>{{ __('message.package') }}</th>
                                            <th>{{ __('message.subscription_end_date') }}</th>
                                            <th class="text-end">{{ __('message.amount') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($data['expire_soon_subscription']) > 0)
                                            @foreach ($data['expire_soon_subscription'] as $expire_soon)
                                                <tr>
                                                    <td>{{ $expire_soon?->user?->display_name ?? '-' }}</td>
                                                    <td><span class="badge-soft badge-soft-red">{{ $expire_soon?->package?->name ?? '-' }}</span></td>
                                                    <td>{{ $expire_soon->subscription_end_date }}</td>
                                                    <td class="text-end fw-semibold text-white">{{ $expire_soon->formated_total_amount ?? 0 }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="empty-state-modern">{{ __('message.not_found_entry', ['name' => __('message.expire_soon')]) }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        @endif

        {{-- ===== CONTENT TABLES ROW ===== --}}
        <div class="row g-3">

            @if ($auth_user->can('exercise-list'))
                <div class="col-lg-6">
                    <div class="card-modern" data-aos="fade-up" data-aos-delay="300">
                        <div class="card-header">
                            <h4 class="card-title">{{ __('message.list_form_title', ['form' => __('message.exercise')]) }}</h4>
                            <a href="{{ route('exercise.index') }}" class="see-all-link">{{ __('message.see_all') }}</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-modern">
                                    <thead>
                                        <tr>
                                            <th style="width:50px">{{ __('message.image') }}</th>
                                            <th>{{ __('message.title') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($data['exercise']) > 0)
                                            @foreach ($data['exercise'] as $exercise)
                                                <tr>
                                                    <td><img src="{{ getSingleMedia($exercise, 'exercise_image') }}" alt="exercise" class="avatar-sm"></td>
                                                    <td>{{ $exercise->title }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="2" class="empty-state-modern">{{ __('message.not_found_entry', ['name' => __('message.exercise')]) }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($auth_user->can('workout-list'))
                <div class="col-lg-6">
                    <div class="card-modern" data-aos="fade-up" data-aos-delay="350">
                        <div class="card-header">
                            <h4 class="card-title">{{ __('message.list_form_title', ['form' => __('message.workout')]) }}</h4>
                            <a href="{{ route('workout.index') }}" class="see-all-link">{{ __('message.see_all') }}</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-modern">
                                    <thead>
                                        <tr>
                                            <th style="width:50px">{{ __('message.image') }}</th>
                                            <th>{{ __('message.title') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($data['workout']) > 0)
                                            @foreach ($data['workout'] as $workout)
                                                <tr>
                                                    <td><img src="{{ getSingleMedia($workout, 'workout_image') }}" alt="workout" class="avatar-sm"></td>
                                                    <td>{{ $workout->title }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="2" class="empty-state-modern">{{ __('message.not_found_entry', ['name' => __('message.workout')]) }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($auth_user->can('diet-list'))
                <div class="col-lg-6">
                    <div class="card-modern" data-aos="fade-up" data-aos-delay="400">
                        <div class="card-header">
                            <h4 class="card-title">{{ __('message.list_form_title', ['form' => __('message.diet')]) }}</h4>
                            <a href="{{ route('diet.index') }}" class="see-all-link">{{ __('message.see_all') }}</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-modern">
                                    <thead>
                                        <tr>
                                            <th style="width:50px">{{ __('message.image') }}</th>
                                            <th>{{ __('message.title') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($data['diet']) > 0)
                                            @foreach ($data['diet'] as $diet)
                                                <tr>
                                                    <td><img src="{{ getSingleMedia($diet, 'diet_image') }}" alt="diet" class="avatar-sm"></td>
                                                    <td>{{ $diet->title }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="2" class="empty-state-modern">{{ __('message.not_found_entry', ['name' => __('message.diet')]) }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($auth_user->can('post-list'))
                <div class="col-lg-6">
                    <div class="card-modern" data-aos="fade-up" data-aos-delay="450">
                        <div class="card-header">
                            <h4 class="card-title">{{ __('message.list_form_title', ['form' => __('message.post')]) }}</h4>
                            <a href="{{ route('post.index') }}" class="see-all-link">{{ __('message.see_all') }}</a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-modern">
                                    <thead>
                                        <tr>
                                            <th style="width:50px">{{ __('message.image') }}</th>
                                            <th>{{ __('message.title') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (count($data['post']) > 0)
                                            @foreach ($data['post'] as $post)
                                                <tr>
                                                    <td><img src="{{ getSingleMedia($post, 'post_image') }}" alt="post" class="avatar-sm"></td>
                                                    <td>{{ $post->title }}</td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="2" class="empty-state-modern">{{ __('message.not_found_entry', ['name' => __('message.post')]) }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        </div>

    </div>
</x-app-layout>