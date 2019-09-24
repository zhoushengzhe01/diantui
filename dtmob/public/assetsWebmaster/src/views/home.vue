<template>
<div class="content">

    <div class="title-box">
        <h3 class="title">管理首页</h3>
        <div class="search-box">
            <el-form :inline="true" :model="myads" class="demo-form-inline" size="mini">
                <el-form-item>
                    <router-link to="/webmaster/myads">
                        <el-button size="mini" type="success">获取广告</el-button>
                    </router-link>
                    <router-link to="/webmaster/expends">
                        <el-button size="mini" type="success">我的提现</el-button>
                    </router-link>
                </el-form-item>
            </el-form>
        </div>
    </div>

    <div class="homePage">
        <el-row :gutter="24">
            <el-col :span="8">
                <div class="box">
                    <h5>账户信息</h5>
                    <div class="userinfo">
                        <div class="people">
                            <div class="people_header"></div>
                            <div class="people_body">&nbsp;&nbsp;&nbsp;</div>
                        </div>
                        <div class="user">
                            <div class="username">欢迎用户：<br/>{{group.webmaster.username}}</div>
                            <div class="logintime">上次登陆时间：<br/>{{group.webmaster.updated_at}}</div>
                        </div>
                    </div>
                </div>
            </el-col>
            <el-col :span="16">
                <div class="box" style="height: 200px;">
                    <h5>收益状况</h5>
                    <div class="earnings" v-if="data.earnings">
                        <div class="earning">
                            <div class="title">今日收益</div>
                            <div class="con">{{data.todayEarning}} 元</div>
                            <div class="info">10分钟更新</div>
                        </div>
                        <div class="earning">
                            <div class="title">昨日收益</div>
                            <div class="con">{{data.yesterdayEarning}} 元</div>
                            <div class="info">00:30后为准</div>
                        </div>
                        <div class="earning">
                            <div class="title">下线数量</div>
                            <div class="con">{{data.lowerCount}} 个</div>
                            <div class="info">2%下线提成</div>
                        </div>
                        <div class="earning">
                            <div class="title">下线收益</div>
                            <div class="con">{{data.lowerEarning}} 元</div>
                            <div class="info">昨天收益</div>
                        </div>
                    </div>
                </div>
            </el-col>
        </el-row>

        <el-row :gutter="24">
            <el-col :span="12">
                <div class="box">
                    <h5>广告收益</h5>
                    <div class="earningchart" v-loading="loading">
                        <x-chart :id="id_earning" ref="chartearning"></x-chart>
                    </div>
                </div>
            </el-col>
            <el-col :span="12">
                <div class="box">
                    <h5>下线收益</h5>
                    <div class="earningchart" v-loading="loading">
                        <x-chart :id="id_lower_earning" ref="chartlowerearning"></x-chart>
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>

    <el-dialog
        title="通知"
        class="domain_update_message"
        :visible.sync="domain_update_message"
        width="500px"
        center>
        <br/>
        <p style="font-size: 16px;">广告域名由原来 "<b style="font-weight: 700;">{{group.setting.old_pg_domain}}</b>" 更换成 "<b style="font-weight: 700;">{{group.setting.pg_domain}}</b>"</p>
        <br/>
        <p style="font-size: 16px;text-align: right;">请流量主前往更新广告代码。</p>
        <br/>
        <span slot="footer" class="dialog-footer">
            <el-button @click="updateDomain()">我已更换</el-button>
            <router-link to="/webmaster/myads">
                <el-button type="primary" >前往获取</el-button>
            </router-link>
        </span>
    </el-dialog>

</div>
</template>
<script>

import XChart from './chart.vue'

export default {
    name: 'home',
    data: function () { 
        return {
            group: Group,
            loading: true,
            loadingTable: true,

            myads: {
                myad_id: 0,
            },

            domain_update_message: false,

            id_earning: 'chart_earning',
            id_lower_earning: 'chart_lower_earning',
            data: {},

            paramete: {
                offset: 0,
                limit: 12,
            },
            earning: {},
        }
    },
    created: function () {
    
        this.group.page = '/webmaster';

        this.getMyads();
        this.domainUpdateMessage();

        //this.getExpends();

    },
    components: {
        XChart
    },
    
    methods:{
        domainUpdateMessage: function()
        {
            var Th = this;
            if(Th.group.webmaster.pg_domain_update_time < Th.group.setting.pg_domain_update_time)
            {
                Th.domain_update_message = true;
            }
        },

        updateDomain: function()
        {
            var Th = this;
            Th.loading = true;

            Th.group.webmaster.pg_domain_update_time = 'update';
            
            Th.$api.put('webmaster/user.json', Th.group.webmaster, function(data)
            {
                Th.loading = false;
                Th.domain_update_message = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        
        getMyads: function()
        {

            var Th = this;

            Th.$api.get('webmaster/myads.json', {}, function(data){
                
                Th.myads.data = data.myads;
                Th.getData();

            }, function(type, message){ Th.$emit('message', type, message); });

        },

        //--------------------------------------图表数据--------------------------------------
        getData: function() {
            var Th = this;
            Th.loading = true;
            Th.$api.get('webmaster/data.json', {myad_id: Th.myads.myad_id}, function(data){
                
                Th.data = data;
                Th.initialChart();
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        initialChart: function()
        {
            this.$refs.chartearning.init(this.data.earnings);
            this.$refs.chartlowerearning.init(this.data.lowerEarnings);
        },



        getExpends: function()
        {
            var Th = this;
            Th.loadingTable = true;
            Th.$api.get('webmaster/earnings/hour.json', Th.paramete, function(data)
            {
                Th.earning = data;
                Th.loadingTable = false;

            }, function(type, message){ Th.loadingTable = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getExpends();
        },

        
        
    },
}
</script>