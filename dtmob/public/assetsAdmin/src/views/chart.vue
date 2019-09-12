<template>
    <div class="x-bar">
        <div
        :id="id">
        </div>
    </div>
</template>

<script>
import HighCharts from 'highcharts'

export default {
    // 验证类型
    props: {
        id: {
            type: String
        },
    },
    mounted() {
        
    },
    methods:{
        init: function(categories, seriesData) {

            this.drawing(categories, seriesData);
        },
        drawing: function(categories, seriesData)
        {
            HighCharts.chart(this.id, {
                chart: {
                    type: 'spline',
                },
                title: {
                    text: ''
                },
                subtitle: {
                    text: '数据来源: ' + window.location.host,
                    style: { "font-size": "12px", "color": "#999999"}
                },
                xAxis: {
                    categories: categories
                },
                yAxis: {
                    title: {
                        text: ''
                    }
                },
                plotOptions: {
                    series: {
                        lineWidth: 1,
                        label: {
                            connectorAllowed: false
                        },
                        marker: {
                            height: 1,
                            width: 1,
                            lineWidth: 0,
                            radius: 3,
                        },
                    }
                },
                series: seriesData
            })
        }
    },
}
</script>