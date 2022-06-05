<template>
    <div class="stats-wrapper" v-if="records">
        <Breadcrumb 
            :urls="[
                { path: 'web', name: 'Verificaciones Web'}, 
                { path: `web/${records.id}/estadisticas`, name: `${records.name ? records.name : 'Estadísticas'}`, last: true}]"/>

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

        <div v-if="chartInfoOK" class="chartWrapper bg-white rounded-lg shadow-lg py-10 mt-5 relative">
            <WebLineChart 
                v-if="chartInfoOK" 
                :chartInfoOK="chartInfoOK" 
                :chartInfoFail="chartInfoFail"
                :userInfo="records && records.web_verification"/>
        </div>
    </div>

    <div class="w-full h-full mt-40 flex items-center justify-center" v-else>
        <LoadingEffect size="md" />
    </div>
</template>

<script>
import { mapGetters } from 'vuex';
import DateHandler from '~/core/stats/date-handler.js';

export default {
    layout: 'client',
    middleware: 'auth',

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

    mounted() {
        this.getTime();
        this.getRecords();
        this.getData();
    },

    methods: {
        switchFiter() {
            if(this.dateFilteredStart !== null & this.dateFilteredEnd !== null) {
                return this.getData();
            }
        },

        getRecords() {
            this.$axios.$get(`${this.getTenant.slug}/verifications/web-verifications/${this.idParam}`)
                .then(res => this.records = res.data.web_verification)
                .catch(error => error);
        },

        getData() {
            this.clearData();

            return this.$axios.$get(`${this.getTenant.slug}/verifications/web-verifications/${this.idParam}/get-data?start_date=${this.dateFilteredStart || 0}&end_date=${this.dateFilteredEnd || 0}`)
                .then(res => {
                    this.chartInfoOK = res.data.result.filter(res => {
                        return this.records.response_codes.includes(res.status)
                    });

                    this.chartInfoFail = res.data.result.filter(res => {
                        return !this.records.response_codes.includes(res.status)
                    });
                })
                .catch(error => error);
        },

        clearData() {
            this.chartInfoOK = null;
            return this.chartInfoFail= null;
        },
        
        getTime() {
            const newDate = new DateHandler().getHours();
            this.dateFilteredStart = newDate.initialHour;
            return this.dateFilteredEnd = newDate.lastHour;
        }
    }
}
</script>