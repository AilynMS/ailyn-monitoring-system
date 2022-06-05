<template>
    <div class="stats-wrapper" v-if="records">
        <Breadcrumb 
            :urls="[
                { path: 'snmp', name: 'Verificaciones SNMP'}, 
                { path: `snmp/${records.id}/estadisticas`, name: `${records.name ? records.name : 'Estadísticas'}`, last: true}]"/>

        <section class="head-wrapper flex md:flex-row flex-col mt-10">
            <div class="head_ flex flex-col w-full my-auto">
                <h1 class="text-3xl text-gray-900">{{ records ? records.name : '' }}</h1>
                <p class="text" v-if="records">Estadísticas</p>
            </div>

            <div class="datepicker md:my-auto mt-4 flex w-full items-start justify-start sm:justify-end sm:items-end">
                <Date-picker
                    placeholder="Fecha inicio"
                    v-model="dateFilteredStart"
                    @close="switchFiter()"
                    @clear="getData()"
                    value-type="format"
                    class="mr-3 shadow-sm"
                    type="datetime">
                </Date-picker>

                <Date-picker
                    placeholder="Fecha fin"
                    v-model="dateFilteredEnd"
                    @close="switchFiter()"
                    @clear="getData()"
                    value-type="format"
                    class="shadow-sm"
                    type="datetime">
                </Date-picker>
            </div>
        </section>

        <div v-if="chartInfoOK.length" class="chartWrapper bg-white rounded-lg shadow-lg py-10 mt-5 relative">
            <SnmpLineChart 
                v-if="chartInfoOK"
                :chartInfoOK="chartInfoOK" 
                :chartInfoFail="chartInfoFail"
                :userInfo="records"/>
        </div>
    </div>

    <div class="w-full h-full mt-40 flex items-center justify-center" v-else>
        <LoadingEffect size="md" />
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import BandWidthSNMP from '~/components/stats/snmp/bandWidth.vue';
import DateHandler from '~/core/stats/date-handler.js';

export default {
    layout: 'client',
    middleware: 'auth',

    components: {
        BandWidthSNMP
    },

    head() {
        return {
            title: `Estadísticas | AilynMS`
        }
    },

    computed: {
        ...mapGetters(['getTenant']),
        
        idParam() {
            return this.$route.params.id;
        }
    },

    data() {
        return {
            records: null,
            chartInfoOK: [],
            chartInfoFail: [],
            dateFilteredStart: null,
            dateFilteredEnd: null
        }
    },

    async mounted() {
        this.getTime();
        this.getRecords();
        this.getData();
    },

    destroyed() {
        this.chartInfoOK = [];
    },
 
    methods: {
        switchFiter() {
            if(this.dateFilteredStart !== null & this.dateFilteredEnd !== null) {
                return this.getData();
            }
        },
 
        getRecords() {
            this.$axios.$get(`${this.getTenant.slug}/verifications/snmp-verifications/${this.idParam}`)
                .then(res => this.records = res.data.snmp_verification)
                .catch(error => error);
        },

        getData() {
            this.clearData();

            this.$axios.$get(`${this.getTenant.slug}/verifications/snmp-verifications/${this.idParam}/get-data?start_date=${this.dateFilteredStart || 0}&end_date=${this.dateFilteredEnd || 0}`)
                .then(res => {                     
                    if(this.records.data_type === 'bandwidth_out' || this.records.data_type === 'bandwidth_in') {
                        this.getBandWidthInfo(res.data.result);
                    }

                    if(this.records.data_type === 'cpu_use') {
                        this.getCPUInfo(res.data.result);
                    }

                    if(this.records.data_type === 'memory_free') {
                        this.getMemoryInfo(res.data.result);
                    }
                })
                .catch(error => console.log(error));
        },

        getBandWidthInfo(data = []){
            let index = 0;

            while(index <= data.length - 1) {
                index++;
                let calcValue = data[index].value - data[index + 2].value;
                let calcDate = new Date(data[index].time).getTime() - new Date(data[index+2].time).getTime();
                
                this.chartInfoOK.push({
                    value: calcValue / calcDate * 8,
                    time: data[index].time
                });
            }
        },

        getMemoryInfo(data = []) {
            return this.chartInfoOK = data;
        },

        getCPUInfo(data = []) {
            return this.chartInfoOK = data;
        },

        clearData() {
            this.chartInfoOK = [];
            return this.chartInfoFail= [];
        },

        getTime() {
            const newDate = new DateHandler().getHours();
            this.dateFilteredStart = newDate.initialHour;
            return this.dateFilteredEnd = newDate.lastHour;
        }
    }
}
</script>