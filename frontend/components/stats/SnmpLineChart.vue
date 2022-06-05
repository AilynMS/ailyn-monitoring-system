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
                        label: 'Respuesta',
                        backgroundColor: "#319795",
                        borderColor: "#81E6D9",
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
                        y: c.value > 0 ? c.value : 0,
                        x: this.$moment
                            .utc(c.time)
                            .format("YYYY-MM-DD HH:mm:ss"),
                    };
                });
            }


            this.addPlugin([Zoom]);
            this.renderChart(this.data, this.options);
        }
    }
}
</script>