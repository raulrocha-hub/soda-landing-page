/**
 * Dashboard Charts - Soda Perfeita
 * Integração com ApexCharts
 */

class SodaPerfeitaDashboardCharts {
    constructor() {
        this.charts = {};
        this.init();
    }

    init() {
        this.initCharts();
        this.bindEvents();
    }

    initCharts() {
        // Gráfico de Volume de Pedidos
        if (document.getElementById('chart-pedidos-volume')) {
            this.initPedidosVolumeChart();
        }

        // Gráfico de Evolução de Clientes
        if (document.getElementById('chart-clientes-evolucao')) {
            this.initClientesEvolucaoChart();
        }

        // Gráfico de Distribuição por Tier
        if (document.getElementById('chart-tiers-distribuicao')) {
            this.initTiersDistribuicaoChart();
        }
    }

    initPedidosVolumeChart() {
        const options = {
            series: [{
                name: 'Xaropes Vendidos',
                data: [30, 40, 35, 50, 49, 60, 70, 91, 125]
            }],
            chart: {
                height: 350,
                type: 'line',
                zoom: {
                    enabled: true
                }
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            title: {
                text: 'Volume Mensal de Pedidos',
                align: 'left'
            },
            grid: {
                row: {
                    colors: ['#f3f3f3', 'transparent'],
                    opacity: 0.5
                },
            },
            xaxis: {
                categories: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set'],
            }
        };

        this.charts.pedidosVolume = new ApexCharts(
            document.querySelector("#chart-pedidos-volume"), 
            options
        );
        this.charts.pedidosVolume.render();
    }

    initClientesEvolucaoChart() {
        const options = {
            series: [{
                name: 'Clientes Ativos',
                data: [10, 15, 22, 28, 35, 42, 50, 58, 65]
            }],
            chart: {
                type: 'area',
                height: 350
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.9,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set']
            }
        };

        this.charts.clientesEvolucao = new ApexCharts(
            document.querySelector("#chart-clientes-evolucao"), 
            options
        );
        this.charts.clientesEvolucao.render();
    }

    initTiersDistribuicaoChart() {
        const options = {
            series: [44, 55, 41],
            chart: {
                type: 'donut',
                height: 350
            },
            labels: ['Tier 1 - Base', 'Tier 2 - Performance', 'Tier 3 - Excelência'],
            responsive: [{
                breakpoint: 480,
                options: {
                    chart: {
                        width: 200
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }]
        };

        this.charts.tiersDistribuicao = new ApexCharts(
            document.querySelector("#chart-tiers-distribuicao"), 
            options
        );
        this.charts.tiersDistribuicao.render();
    }

    bindEvents() {
        // Atualizar gráficos quando o filtro de tempo mudar
        const timeFilter = document.getElementById('time-filter');
        if (timeFilter) {
            timeFilter.addEventListener('change', (e) => {
                this.updateChartsWithTimeRange(e.target.value);
            });
        }
    }

    async updateChartsWithTimeRange(timeRange) {
        try {
            const response = await fetch(sodaPerfeitaAjax.ajaxurl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'get_chart_data',
                    time_range: timeRange,
                    nonce: sodaPerfeitaAjax.nonce
                })
            });

            const data = await response.json();
            
            if (data.success) {
                this.updateChartsData(data.data);
            }
        } catch (error) {
            console.error('Erro ao atualizar gráficos:', error);
        }
    }

    updateChartsData(chartData) {
        // Atualizar cada gráfico com novos dados
        Object.keys(chartData).forEach(chartName => {
            if (this.charts[chartName]) {
                this.charts[chartName].updateSeries(chartData[chartName].series);
                this.charts[chartName].updateOptions({
                    xaxis: {
                        categories: chartData[chartName].categories
                    }
                });
            }
        });
    }
}

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    window.sodaPerfeitaCharts = new SodaPerfeitaDashboardCharts();
});

// Funções globais para uso em templates
function sodaPerfeitaInitDashboard() {
    window.sodaPerfeitaCharts = new SodaPerfeitaDashboardCharts();
}

function sodaPerfeitaRefreshDashboard() {
    if (window.sodaPerfeitaCharts) {
        window.sodaPerfeitaCharts.updateChartsWithTimeRange(
            document.getElementById('time-filter').value
        );
    }
}

function sodaPerfeitaFilterDashboard(timeRange) {
    if (window.sodaPerfeitaCharts) {
        window.sodaPerfeitaCharts.updateChartsWithTimeRange(timeRange);
    }
}