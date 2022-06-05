<div class="chart-container" style="position: relative; height:40vh; width:80vw">
</div>

<script>
import { Line } from 'vue-chartjs';
import Zoom from 'chartjs-plugin-zoom';

export default {
    name: 'WeblineChart',
    extends: Line,

    props: {
        chartInfoOK: {
            required: true
        },
        chartInfoFail: {
            required: true
        },
        userInfo: {
            required: true
        }
    },

    data() {
        return {
            data: {
                datasets: [
                    {
                        labels: [],
                        label: 'Respuestas correctas',
                        backgroundColor: "#319795",
                        borderColor: "#81E6D9",
                        data: [],
                        fill: true
                    },
                    {
                        labels: [],
                        label: 'Respuestas incorrectas',
                        backgroundColor: "#E53E3E",
                        borderColor: "#FEB2B2",
                        data: [],
                        fill: true
                    }
                ]
            },

            options: {
                plugins: {
                    zoom: {
                        pan: {
                            enabled: true,
                            speed: 15,
                            mode: 'x'
                        },
                        zoom: {
                            enabled: true,
                            mode: 'x'
                        }
                    }
                },
                tooltips: {
                        enabled: true,
                        mode: 'single',
                        callbacks: {
                                label: function (tooltipItems, data) {
                                    let regIndex = null;
                                    let arrayPosition = 0;

                                    regIndex = data.datasets[0].data.findIndex(data => {
                                        return data.x === tooltipItems.xLabel
                                    });

                                    if(regIndex !== -1) arrayPosition = 0;

                                    if(!regIndex || regIndex == -1) {
                                        regIndex = data.datasets[1].data.findIndex(data => {
                                            return data.x === tooltipItems.xLabel
                                        });

                                        arrayPosition = 1;                                    
                                    }
                                    
                                    return `Respuesta: ${data.datasets[arrayPosition].data[regIndex].f} (${tooltipItems.yLabel} ms)`;
                                }
                        }
                },

                scales: {
                    xAxes: [
                        {
                            type: "time",
                            time: {
                                minUnit: "minute",
                            },
                            distribution: "series",
                            parser: true,
                        },
                    ],
                    yAxes: [{
                        id: 'A',
                        ticks: {
                            min: 0
                        },
                    }],
                },
                responsive: true,
                maintainAspectRatio: false,
                legend: { display: true }
            }
        }
    },

    mounted() {
        this.initialize();
    },

    updated() {
        this.initialize();
    },

    methods: {
        initialize() {
            const { datasets } = this.data;
            const { chartInfoOK, chartInfoFail, userInfo } = this;

            if(this.chartInfoOK) {
                datasets[0].data = chartInfoOK.map(c => {
                    return {
                        f: 'is_alive',
                        x: this.$moment
                            .utc(c.time)
                            .format("YYYY-MM-DD HH:mm:ss"),
                        y: c.duration
                    };
                });
            }

            if(chartInfoFail) {
                datasets[1].data = chartInfoFail.map(c => {
                    return {
                        f: 'is_not_alive',
                        x: this.$moment
                            .utc(c.time)
                            .tz("America/Bogota")
                            .format("YYYY-MM-DD HH:mm:ss"),
                        y: c.duration
                    };
                });                
            }

            this.addPlugin([Zoom]);
            this.renderChart(this.data, this.options);
        }
    }
}
</script>