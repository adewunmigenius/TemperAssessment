<template>
    <div class="chartElem">
        <div class="row">
            <highcharts class="chart" :options="chartOptions" :updateArgs="updateArgs"></highcharts>
        </div>
    </div>
</template>

<script>
    import {Chart} from 'highcharts-vue'
    export default {
        name: "UserDataInsight",
        components: {
            highcharts: Chart
        },
        data () {
            return {
                updateArgs: [true, true, {duration: 1000}],
                chartOptions: {
                    chart: {
                        type: 'spline',
                        height: '600px'
                    },
                    title: {
                        text: 'Visualize users Retention Percentage chart'
                    },
                    xAxis: {
                        title: {
                            text: 'Onboarding Percentage'
                        }},
                    yAxis: {
                        title: {
                            text: 'Percentage of User per Week'
                        }},
                    series: [],
                }
            }
        },
        created () {
            this.loadData();
        },
        watch: {
            animationDuration (newValue) {
                this.updateArgs[2].duration = Number(newValue)
            }
        },methods:{
            loadData(){
                axios.get('/user_data_insight').then(response => {
                    console.log(response.data.data)
                    this.chartOptions.series = response.data.data
                }).catch(err => {
                    alert('Failed to load Data from the Server')
                })
            }
        }
    }
</script>

<style scoped>
    .chartElem{
        /*position: absolute;*/
        /*top: 50%;*/
        /*transform: translateY(-50%);*/
        margin-top: 30px;
        width: 80%;
        /*height: 300px;*/
    }
</style>