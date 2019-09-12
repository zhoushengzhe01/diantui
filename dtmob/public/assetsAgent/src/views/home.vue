<template>
<div class="content">

    <div class="title-box">
        <h3 class="title">管理首页</h3>
        <div class="search-box">
            <el-form :inline="true" :model="myads" class="demo-form-inline" size="mini">
                <el-form-item>
                    <router-link to="/agent/myads">
                        <el-button size="mini" type="success">获取广告</el-button>
                    </router-link>
                    <router-link to="/agent/expends">
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
                            <div class="username">欢迎代理：<br/>{{group.agent.username}}</div>
                            <div class="logintime">上次登陆时间：<br/>{{group.agent.updated_at}}</div>
                        </div>
                    </div>
                </div>
            </el-col>
            <el-col :span="16">
                <div class="box" style="height: 200px;" v-if="earning">
                    <h5>收益状况</h5>
                    <div class="earnings">
                        <div class="earning"><b>{{earning.lower_count}} 个</b><br/><span>下线数量</span></div>
                        <div class="earning"><b>{{earning.yesterdayEarnings}} 元</b><br/><span>昨日收益</span></div>
                        <div class="earning"><b>{{earning.thisWeekEarnings}} 元</b><br/><span>本周提成</span></div>
                        <div class="earning"><b>{{earning.add_lower_count}} 个</b><br/><span>今日新增</span></div>
                    </div>
                </div>
            </el-col>
        </el-row>

        <el-row :gutter="24">
            <el-col :span="24">
                <div class="box">
                    <h5>两周收益</h5>
                    <div class="earningchart" v-loading="loading">
                        <x-chart :id="id" ref="chart"></x-chart>
                    </div>
                </div>
            </el-col>
        </el-row>
    </div>
    
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
            id: 'chart',
            earning: {},
        }
    },
    created: function (){
        this.group.page = '/agent';
        this.getEarnings();
    },
    components: {
        XChart
    },
    methods:{
        initialChart: function(){
            this.$refs.chart.init(this.earning.earnings);
        },
        getEarnings: function(){
            var Th = this;
            Th.loading = true;
            Th.$api.get('agent/earnings_by_day.json', Th.paramete, function(data)
            {
                Th.earning = data;
                Th.loading = false;
                Th.initialChart();

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getEarnings();
        },
    },
}
</script>