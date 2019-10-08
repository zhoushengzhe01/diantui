<template>   
<div class="content">
    <div class="title-box">
        <h3 class="title">管理首页</h3>
        <div class="search-box">
            <el-form :inline="true" :model="paramete2" class="demo-form-inline" size="mini">
                <el-form-item prop="region">
                    <span>推广地址：<b>http://{{group.domain}}/register?uid={{group.user.id}}</b></span>&nbsp;&nbsp;
                    
                    <el-select v-model="paramete2.myad_id" placeholder="切换广告">
                        <el-option label="全部数据" :value="0"></el-option>
                        <el-option v-for="item in group.adtype" :key="item.key" :label="item.name" :value="item.id"></el-option>
                    </el-select>

                    <el-badge is-dot class="item">
                        <el-button type="success">结算</el-button>
                    </el-badge>

                    <el-badge is-dot class="item">
                        <el-button type="success">充值</el-button>
                    </el-badge>

                    <el-badge is-dot class="item">
                        <el-button type="success">审核</el-button>
                    </el-badge>
                </el-form-item>
            </el-form>
        </div>
    </div>

    <div class="homePage">
    <!-- <el-row :gutter="24">
        <el-col :span="8">
            <div class="box">
                <h5>账户信息</h5>
                <div class="userinfo">
                    <div class="people">
                        <div class="people_header"></div>
                        <div class="people_body">&nbsp;&nbsp;&nbsp;</div>
                    </div>
                    <div class="user">
                        <div class="username">欢迎用户：点推</div>
                        <div class="logintime">登陆时间：<br/>2018-04-20 15:03:04</div>
                    </div>
                </div>
            </div>

        </el-col>
        <el-col :span="16">
            <div class="box" style="height: 200px;">
                <h5>广告情况</h5>
                <div class="earnings">
                    <div class="earning"><b>2000 元</b><br/><span>今日消耗</span></div>
                    <div class="earning"><b>2000 元</b><br/><span>站长费用</span></div>
                    <div class="earning"><b>30 条</b><br/><span>WAP广告</span></div>
                    <div class="earning"><b>10 条</b><br/><span>微信广告</span></div>
                </div>
            </div>
        </el-col>
    </el-row> -->

    

    <el-row :gutter="24">

        <el-col :span="12" v-for="item in adnumber" :key="item.key">
            <div class="box">
                <h5>{{item.name}}广告</h5>
                <br/>
                <el-table :data="item.number" style="width: 100%">
                    <el-table-column
                    prop="name"
                    label="类型">
                    </el-table-column>
    
                    <el-table-column
                    label="wap/安">
                        <template slot-scope="scope">
                        {{scope.row.wapandroid}} 条
                        </template>
                    </el-table-column>
    
                    <el-table-column
                    label="wap/ios">
                        <template slot-scope="scope">
                        {{scope.row.wapios}} 条
                        </template>
                    </el-table-column>
    
                    <el-table-column
                    label="微信/安">
                        <template slot-scope="scope">
                        {{scope.row.wechatandroid}} 条
                        </template>
                    </el-table-column>
    
                    <el-table-column
                    prop="wechatios"
                    label="微信/ios">
                        <template slot-scope="scope">
                        {{scope.row.wechatios}} 条
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </el-col>

    </el-row>

    <el-row :gutter="24">
        <el-col :span="12">
            <div class="box">
                <h5>展示分布</h5>
                <x-chart :id="id_pv" ref="chartpv"></x-chart>
            </div>
        </el-col>
        <el-col :span="12">
            <div class="box">
                <h5>IP 分布</h5>
                <x-chart :id="id_ip" ref="chartip"></x-chart>
            </div>
        </el-col>
        <el-col :span="12">
            <div class="box">
                <h5>点击分布</h5>
                <x-chart :id="id_pc" ref="chartpc"></x-chart>
            </div>
        </el-col>
        <el-col :span="12">
            <div class="box">
                <h5>小时成本</h5>
                <x-chart :id="id_mon" ref="chartmon"></x-chart>
            </div>
        </el-col>
    </el-row>

    <el-row :gutter="24">
        <el-col :span="24">
            <div class="box">
                <h5>媒介提成</h5>
                <br/>
                <el-table :data="services.services" style="width: 100%" >
                    <el-table-column
                        prop="nickname"
                        label="客服"
                        width="100">
                    </el-table-column>
    
                    <el-table-column
                        label="金额">
                        <template slot-scope="scope">
                            {{scope.row.earning_money}} 元<br/>
                            <el-progress :show-text="false" :stroke-width="6" :percentage="(scope.row.earning_money/services.max_money)*100" status="success"></el-progress>
                        </template>
                    </el-table-column>
                </el-table>
            </div>
        </el-col>
    </el-row>

    </div>
</div>
</template>

<script>
import XChart from './chart.vue'

export default {
    name: 'user',
    data: function () { 
        return {
            group: Group,

            paramete2: {
                myad_id: 0,
            },
            myads: {},

            id_pv: 'chartpv',
            id_pc: 'chartpc',
            id_ip: 'chartip',
            id_mon: 'chartmon',
            
            webmasterEarning: {},
            parameteWebmaster: {},
            loadingWebmaster: true,

            services: {},
            adnumber: [],
        };
    },
    created: function () {
        
        this.group.page = "/admin";

        this.getWebmasterEarning();

        this.getEarningServices();

        this.getAdnumber();
        
    },
    components: {
        XChart
    },
    methods:{
        getEarningServices: function()
        {
            var Th = this;

            Th.$api.get('admin/earning/services.json', Th.paramete, function(data)
            {
                Th.services = data;

            }, function(type, message){ Th.$emit('message', type, message); });
        
        },
        getAdnumber: function()
        {
            var Th = this;

            Th.$api.get('admin/advertiser/ad/number.json', Th.paramete, function(data)
            {
                Th.adnumber = data;

            }, function(type, message){ Th.$emit('message', type, message); });
        
        },
        getWebmasterEarning: function() {

            var Th = this;

            Th.loadingWebmaster = true;

            Th.$api.get('admin/home/webmaster/earning/hour.json', Th.parameteWebmaster, function(data){

                Th.initWebmasterEarningPv(data);

                Th.initWebmasterEarningPc(data);
                
                Th.initWebmasterEarningIp(data);

                Th.initWebmasterEarningMon(data);

                Th.loadingWebmaster = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        initWebmasterEarningPv: function(data)
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

            this.$refs.chartpv.init(data.default, seriesData);
        },
        initWebmasterEarningPc: function(data)
        {  
            var today = [];
            var yesterday = [];

            //今天
            for(var index in data.today)
            {
                today[index] = data.today[index].pc_number;
            }

            //昨天
            for(var index in data.yesterday)
            {
                yesterday[index] = data.yesterday[index].pc_number;
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
            
            this.$refs.chartpc.init(data.default, seriesData);
        },

        initWebmasterEarningIp: function(data)
        {  
            var today = [];
            var yesterday = [];

            //今天
            for(var index in data.today)
            {
                today[index] = data.today[index].ip_number;
            }

            //昨天
            for(var index in data.yesterday)
            {
                yesterday[index] = data.yesterday[index].ip_number;
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
            
            this.$refs.chartip.init(data.default, seriesData);
        },

        initWebmasterEarningMon: function(data)
        {  
            var today = [];
            var yesterday = [];

            //今天
            for(var index in data.today)
            {
                today[index] = data.today[index].money;
            }

            //昨天
            for(var index in data.yesterday)
            {
                yesterday[index] = data.yesterday[index].money;
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
            
            this.$refs.chartmon.init(data.default, seriesData);
        },
    },
}
</script>