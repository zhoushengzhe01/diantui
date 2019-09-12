<template>
<div class="content">
    <div class="title-box">
        <h3 class="title">管理首页</h3>
        <div class="search-box">
            <el-form :inline="true" :model="paramete2" class="demo-form-inline" size="mini">
                <el-form-item prop="region">
                    <el-select v-model="paramete2.myad_id" placeholder="切换广告" @change="getData">
                        <el-option label="全部数据" :value="0"></el-option>
                        <el-option v-for="item in myads" :key="item.key" :label="item.title" :value="item.id"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <router-link to="/advertiser/recharges">
                        <el-button size="mini" type="success">充值记录</el-button>
                    </router-link>
                    <router-link to="/advertiser/expends">
                        <el-button size="mini" type="success">数据报表</el-button>
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
                            <div class="username">欢迎用户：<br/>{{group.advertiser.username}}</div>
                            <div class="logintime">上次登陆时间：<br/>{{group.advertiser.updated_at}}</div>
                        </div>
                    </div>
                </div>

            </el-col>
            <el-col :span="16">
                <div class="box" style="height: 200px;">
                    <h5>消耗状况</h5>
                    <div class="earnings" v-if="data.week">
                        <div class="earning"><b>{{data.dayexpends.money}} 元</b><br/><span>今日消耗</span></div>
                        <div class="earning"><b>{{data.yesterday.money}} 元</b><br/><span>昨日消耗</span></div>
                        <div class="earning"><b>{{data.week.thisWeek.money}} 元</b><br/><span>本周消耗</span></div>
                        <div class="earning"><b>{{data.week.lastWeek.money}} 元</b><br/><span>上周消耗</span></div>
                    </div>
                </div>
            </el-col>
            
        </el-row>

        <el-row :gutter="24">
            <el-col :span="24">
                <div class="box">
                    <h5>两周消耗</h5>
                    <div class="earningchart" v-loading="loading">
                        <x-chart :id="id" ref="chart"></x-chart>
                    </div>
                </div>
            </el-col>
        </el-row>

        <el-row :gutter="24">
            <el-col :span="24">
                <div class="box">
                    <h5>小时消耗</h5>
                    <el-table v-loading="loadingTable" :data="expend.expends" style="width: 100%;">

                        <el-table-column
                            prop="ad_id"
                            label="广告ID"
                            min-width="80">
                        </el-table-column>

                        <el-table-column
                            prop="title"
                            label="名称"
                            min-width="150">
                        </el-table-column>

                        <el-table-column
                            prop="adstype_id"
                            label="类型"
                            min-width="80">
                            <template slot-scope="scope">
                                <span v-for="type in group.adtype" :key="type.key" v-if="type.id==scope.row.adstype_id">{{type.name}}</span>
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="计费方式"
                            min-width="80">
                            <template slot-scope="scope">
                                <span v-if="scope.row.price_type=='1'">CPC</span>
                                <span v-if="scope.row.price_type=='2'">CPM</span>
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="投放系统"
                            min-width="100">
                            <template slot-scope="scope">
                                <span v-if="scope.row.client=='0'" class="info">IOS+安卓</span>
                                <span v-if="scope.row.client=='1'" class="success">IOS</span>
                                <span v-if="scope.row.client=='2'" class="success">安卓</span>
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="终端"
                            min-width="80">
                            <template slot-scope="scope">
                                <span v-if="scope.row.is_wechat=='1'" class="success">微信</span>
                                <span v-if="scope.row.is_wechat=='0'" class="success">WAP</span>
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="有效数据"
                            min-width="120">
                            <template slot-scope="scope">
                                <span v-if="scope.row.price_type=='1'">{{Math.round(scope.row.money/scope.row.show_price*1000)}} <span class="info">点击</span></span>
                                <span v-if="scope.row.price_type=='2'">{{Math.round(scope.row.money/scope.row.cpm_price*1000)}} <span class="info">展示</span></span>
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="千次单价"
                            min-width="100">
                            <template slot-scope="scope">
                                <span v-if="scope.row.price_type=='1'">{{scope.row.show_price}} <span class="info">千点</span></span>
                                <span v-if="scope.row.price_type=='2'">{{scope.row.cpm_price}} <span class="info">千展</span></span>
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="消耗金额"
                            min-width="120">
                            <template slot-scope="scope">
                                {{Math.round(scope.row.money*100)/100}} 元
                            </template>
                        </el-table-column>

                        <el-table-column
                            prop="time"
                            label="日期"
                            min-width="100">
                        </el-table-column>

                    </el-table>
                    <div class="page-box">
                        <el-pagination
                        @current-change="pageChange"
                        layout="total, prev, pager, next"
                        :page-size="paramete.limit"
                        :total="expend.count">
                        </el-pagination>
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
            loadingTable: true,

            paramete2: {
                myad_id: 0,
            },
            myads: {},
            
            id: 'chart',
            data: {},

            paramete: {
                offset: 0,
                limit: 12,
            },
            expend: {},
        }
    },
    created: function () {
        this.group.page = '/advertiser';
        this.getData();
        this.getMyads();
        this.getExpends();
    },
    components: {
        XChart
    },
    
    methods:{
        //--------------------------------------图表数据--------------------------------------
        getData: function() {

            var Th = this;
            Th.loading = true;

            Th.$api.get('advertiser/data.json', {advertiser_ad_id: Th.paramete2.myad_id}, function(data){
                
                Th.data = data;
                Th.initialChart();
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        initialChart: function()
        {
            this.$refs.chart.init(this.data.weekexpends);
        },


        getExpends: function()
        {
            var Th = this;

            Th.loadingTable = true;
            Th.$api.get('advertiser/expends/hour.json', Th.paramete, function(data)
            {
                Th.expend = data;
                Th.loadingTable = false;

            }, function(type, message){ Th.loadingTable = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getExpends();
        },

        getMyads: function()
        {
            var Th = this;
            Th.$api.get('advertiser/ads.json', {}, function(data){

                Th.myads = data.ads;
            
            }, function(type, message){ Th.$emit('message', type, message); });
        },

    },
}

</script>