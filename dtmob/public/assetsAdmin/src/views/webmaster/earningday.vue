<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">数据报表</h3>
        </div>

        <div class="box" v-loading="loadingday">

            <el-table :data="dataday.earnings" style="width: 100%">
                <el-table-column
                    prop="webmaster_ad_id"
                    label="广告位ID"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    label="PV量/单价"
                    min-width="150">
                    <template slot-scope="scope">
                        {{scope.row.pv_number}} <span v-if="scope.row.hudong_pv_number">（{{scope.row.hudong_pv_number}}）</span>
                        &nbsp;&nbsp;&nbsp;{{ parseInt(scope.row.money / scope.row.pv_number * 10000)/10 }}<span class="info">/千</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="IP量/点击"
                    min-width="150">
                    <template slot-scope="scope">
                        {{scope.row.ip_number}} <span v-if="scope.row.hudong_pc_number">（{{scope.row.hudong_pc_number}}）</span>
                        &nbsp;&nbsp;&nbsp;{{ parseInt(scope.row.money / scope.row.ip_number * 100000)/10 }}<span class="info">/万</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="PC量/点击率"
                    min-width="150">
                    <template slot-scope="scope">
                        {{scope.row.pc_number}} <span v-if="scope.row.hudong_pc_number">（{{scope.row.hudong_pc_number}}）</span>
                        &nbsp;&nbsp;&nbsp;{{ parseInt(scope.row.pc_number / scope.row.pv_number * 10000)/100 }}%
                    </template>
                </el-table-column>

                <el-table-column
                    label="总额(元)"
                    min-width="100">
                    <template slot-scope="scope">
                        <div v-if="scope.row.state!='1' || group.user.department_id==3">{{scope.row.money}}<span class="info">元</span></div>
                        <div v-if="scope.row.state=='1' && group.user.department_id!=3"><input type="text" style="width:80px;height: 28px;" v-model="scope.row.money" @blur="putEarningDay(scope.row)"/></div>
                    </template>
                </el-table-column>

                <el-table-column
                    label="联盟广告"
                    min-width="100">
                    <template slot-scope="scope">
                        {{scope.row.alliance_money}}<span class="info">元</span>
                    </template>
                </el-table-column>

                <el-table-column
                    prop="date"
                    label="时间"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    label="提成"
                    min-width="60">
                    <template slot-scope="scope">
                        <el-switch
                            @change="putEarningDay(scope.row)"
                            v-model="scope.row.is_extract"
                            active-value="1"
                            inactive-value="0"
                            active-color="#13ce66"
                            inactive-color="#CCCCCC">
                        </el-switch>
                    </template>
                </el-table-column>

                <el-table-column
                    v-bind:router="true"
                    fixed="right"
                    label="小时量"
                    width="100">
                    <template slot-scope="scope">
                        <el-button type="text" size="medium" @click="getEarningHour(scope.row)">列表</el-button>
                        <el-button type="text" size="medium" @click="getEarningHourChart(scope.row)">图表</el-button>
                    </template>
                </el-table-column>
            </el-table>

            <div class="page-box">
                <el-pagination
                @current-change="pageChangeDay"
                layout="total, prev, pager, next"
                :page-size="parameteday.limit"
                :total="dataday.count">
                </el-pagination>
            </div>
        </div>



        <!--弹窗编辑-->
        <el-dialog title="小时量" :visible.sync="show" class="big_dialog">

             <el-table :data="datahour.earnings" style="width: 100%" v-loading="loadinghour">

                <el-table-column
                    label="PV量/单价"
                    min-width="150">
                    <template slot-scope="scope">
                        {{scope.row.pv_number}} <span v-if="scope.row.hudong_pv_number">（{{scope.row.hudong_pv_number}}）</span>
                        &nbsp;&nbsp;&nbsp;{{ parseInt(scope.row.money / scope.row.pv_number * 10000)/10 }}<span class="info">/千</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="IP量/单价"
                    min-width="150">
                    <template slot-scope="scope">
                        {{scope.row.ip_number}} <span v-if="scope.row.hudong_pc_number">（{{scope.row.hudong_pc_number}}）</span>
                        &nbsp;&nbsp;&nbsp;{{ parseInt(scope.row.money / scope.row.ip_number * 100000)/10 }}<span class="info">/万</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="PC量/点击率"
                    min-width="150">
                    <template slot-scope="scope">
                        {{scope.row.pc_number}} <span v-if="scope.row.hudong_pc_number">（{{scope.row.hudong_pc_number}}）</span>
                        &nbsp;&nbsp;&nbsp;{{ parseInt(scope.row.pc_number / scope.row.pv_number * 10000)/100 }}%
                    </template>
                </el-table-column>

                <el-table-column
                    label="总金额(元)"
                    min-width="100">
                    <template slot-scope="scope">
                        <div v-if="scope.row.state!='1'">{{scope.row.money}}<span class="info">元</span></div>
                        <div v-if="scope.row.state=='1'"><input type="text" style="width:70px;height: 26px;" v-model="scope.row.money" @blur="putEarningHour(scope.row)"/></div>
                    </template>
                </el-table-column>

                <el-table-column
                    label="联盟(元)"
                    min-width="100">
                    <template slot-scope="scope">
                        {{scope.row.alliance_money}}<span class="info">元</span>
                    </template>
                </el-table-column>
              
                <el-table-column
                    prop="time"
                    label="时间"
                    min-width="160">
                </el-table-column>

            </el-table>

            <div class="page-box">
                <el-pagination
                @current-change="pageChangeHour"
                layout="total, prev, pager, next"
                :page-size="parametehour.limit"
                :total="datahour.count">
                </el-pagination>
            </div>

            <div slot="footer" class="dialog-footer">
                <el-button @click="show = false">关 闭</el-button>
            </div>
        </el-dialog>


        <el-dialog title="小时量" :visible.sync="chartshow" class="big_dialog">
            <x-chart :id="id" ref="chart"></x-chart>
            <div slot="footer" class="dialog-footer">
                <el-button @click="show = false">关 闭</el-button>
            </div>
        </el-dialog>

    </div>
</template>
<script>
import XChart from './../chart.vue'

export default {
    name: 'recharges',
    data: function () { 
        return {
            group: Group,
            show: false,
            chartshow: false,

            webmaster_ad_id: this.$route.params.webmaster_ad_id,

            loadingday: true,
            loadinghour: true,
            loadinghourchart: true,
            id: 'chart',

            dataday: {},
            datahour: {},
           
            parameteday: {
                offset: 0,
                limit: 15,
            },
            parametehour: {
                offset: 0,
                limit: 12,
            },
        };
    },
    created: function () {
        
        this.group.page = '/admin/webmaster/ads';

        this.getEarningDay();
    },
    components: {
        XChart
    },
    methods:{
        getEarningDay: function()
        {
            var Th = this;
            Th.loadingday = true;
            
            Th.$api.get('admin/webmaster/earning/day/'+Th.webmaster_ad_id+'.json', Th.parameteday, function(data)
            {
                Th.dataday = data;
                Th.loadingday = false;

            }, function(type, message){ Th.loadingday = false; Th.$emit('message', type, message); });
        },
        pageChangeDay: function(val) {
            this.parameteday.offset = parseInt(val-1) * parseInt(this.parameteday.limit);
            this.getEarningDay();
        },

        //修改
        putEarningDay: function(row)
        {
            var Th = this;
            Th.loadingday = true;

            Th.$api.put('admin/webmaster/earning/day/'+row.id+'.json', row, function(data)
            {
                Th.$emit('message', 'success', '编辑成功');
                Th.loadingday = false;

            }, function(type, message){ Th.loadingday = false; Th.$emit('message', type, message); });
        },

        

        getEarningHour: function(row)
        {
            var Th = this;

            Th.loadinghour = true;
            Th.show = true;

            if(row){
                Th.parametehour.date = row.date;
            }

            Th.$api.get('admin/webmaster/earning/hour/'+Th.webmaster_ad_id+'.json', Th.parametehour, function(data)
            {
                Th.datahour = data;
                Th.loadinghour = false;

            }, function(type, message){ Th.loadinghour = false; Th.$emit('message', type, message); });
        },
        pageChangeHour: function(val) {
            this.parametehour.offset = parseInt(val-1) * parseInt(this.parametehour.limit);
            this.getEarningHour('');
        },

        //修改
        putEarningHour: function(row)
        {
            var Th = this;
            Th.loadinghour = true;

            Th.$api.put('admin/webmaster/earning/hour/'+row.id+'.json', row, function(data)
            {
                Th.$emit('message', 'success', '编辑成功');
                Th.loadinghour = false;

            }, function(type, message){ Th.loadinghour = false; Th.$emit('message', type, message); });
        },


        getEarningHourChart: function(row) {

            var Th = this;

            Th.chartshow = true;
            Th.loadinghourchart = true;

            Th.$api.get('admin/webmaster/earning/hour/chart/'+Th.webmaster_ad_id+'.json', { date: row.date, type: 2}, function(data){

                Th.initialHourChart(data);
                Th.loadinghourchart = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        initialHourChart: function(data)
        {  

            var today = [];
            var yesterday = [];

            //今天
            for(var index in data.today)
            {
                today[index] = data.today[index].pv_number;
            }

            //昨天
            for(var index in data.yesterday)
            {
                yesterday[index] = data.yesterday[index].pv_number;
            }

            var seriesData = [{
                color: '#CCCCCC',
                name: '昨天',
                data: yesterday,
            }, {
                color: '#53d192',
                name: '今日',
                data: today,
            }];


            this.$refs.chart.init(data.default, seriesData);
        },
        
    },

}
</script>