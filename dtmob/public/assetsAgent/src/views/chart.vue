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
        init: function(data) {
            
            //过滤数据
            var seriesData = [];
            var categories = [];

            for(var index in data)
            {
                seriesData[index] = data[index].money;
                categories[index] = data[index].date.slice(5);
            }

            this.drawing(seriesData, categories);
        },
        drawing: function(seriesData, categories)
        {
            HighCharts.chart(this.id, {
                chart: {
                    type: 'spline',
                },
                title: {
                    text: ''
                },
                subtitle: {
                    text: '数据来源: www.tdmob.cn',
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
                        color: '#4caf50',
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
                series: [{
                    name: '收益',
                    data: seriesData
                }]
            })
        }
    },
}
</script>