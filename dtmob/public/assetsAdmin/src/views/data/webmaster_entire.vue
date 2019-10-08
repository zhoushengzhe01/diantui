<template>
<div class="content">
    <div class="title-box">
        <h3 class="title">整体分析</h3>
        <div class="search-box">
            <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                <el-form-item label="" v-if="data.count">
                    {{data.click_count}} 条数据
                </el-form-item>
                <el-form-item>
                    <el-select v-model="paramete.sort" placeholder="流量排序">
                        <el-option label="消耗排序" value="1"></el-option>
                        <el-option label="最新排序" value="2"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-select v-model="paramete.grade" placeholder="流量等级">
                        <el-option label="低级" value="1"></el-option>
                        <el-option label="中级" value="2"></el-option>
                        <el-option label="高级" value="3"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-input v-model="paramete.url" placeholder="访问URL"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-input v-model="paramete.webmaster_id" placeholder="站长ID"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-input v-model="paramete.webmaster_ad_id" placeholder="广告ID"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-time-select v-model="paramete.date_time" :picker-options="{start: '00:00', step: '00:30', end: '23:30'}" placeholder="选择时间"></el-time-select>
                </el-form-item>
                
                <el-form-item>
                    <el-button type="success" @click="getClicks">查询</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>


    <div class="box" v-loading="loading">
        <el-row :gutter="24" v-if="data.ads">
            <el-col :span="24">
                <el-table border style="width: 100%" :data="data.ads">
                    <el-table-column label="广告信息">
                        <el-table-column
                            label="站长ID">
                            <template slot-scope="scope">
                                站:{{scope.row.webmaster_id}}<br/>
                                广:{{scope.row.id}}
                            </template>
                        </el-table-column>
                        
                        <el-table-column
                            label="站长名称">
                            <template slot-scope="scope">
                                <router-link target="_blank" style="white-space: nowrap;" :to="'/admin/webmaster/'+scope.row.webmaster_id">{{scope.row.username}}</router-link><br/>
                                <span v-for="item in group.flowpools" :key="item.key" v-if="item.id==scope.row.flow_pool_id">{{item.name}}</span>
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="联盟/客服">
                            <template slot-scope="scope">
                                <span v-for="item in group.alliance_agents" :key="item.key" v-if="item.id==scope.row.alliance_agent_id">{{item.name}}</span><br/>
                                <span v-for="item in group.services" :key="item.key" v-if="item.id==scope.row.service_id">{{item.nickname}}</span>
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="IP评分">
                            <template slot-scope="scope">
                                {{scope.row.ip_point_time}}<br/>
                                {{scope.row.ip_point}} 分
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="位置">
                            <template slot-scope="scope">
                                <span v-if="scope.row.position=='1' && scope.row.position_id==11">上</span>
                                <span v-if="scope.row.position=='2' && scope.row.position_id==11">下</span>
                                <span v-if="scope.row.position=='1' && scope.row.position_id==13">左</span>
                                <span v-if="scope.row.position=='2' && scope.row.position_id==13">右</span>
                                <br/>
                                {{scope.row.position_name}}
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="计费率">
                            <template slot-scope="scope">
                                {{scope.row.out_advertiser_price}}%<br/>{{scope.row.in_advertiser_price}}%
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="暗层/强跳">
                            <template slot-scope="scope">
                                {{scope.row.hid_height}}-{{scope.row.hid_height_chance}}%
                                <br/>
                                {{scope.row.compel_skip}}%
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="强点">
                            <template slot-scope="scope">
                                <span v-if="scope.row.compel_click=='1'">开启-{{scope.row.compel_chance}}%<br/>{{scope.row.compel_interval}}分钟</span>
                                <span v-if="scope.row.compel_click!='1'">关闭</span>
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="假关闭">
                            <template slot-scope="scope">
                                {{scope.row.false_close}}%<br>
                                {{scope.row.grade}} 级
                            </template>
                        </el-table-column>
                        
                        <el-table-column
                            label="状态">
                            <template slot-scope="scope">
                                <el-tag v-if="scope.row.state=='1'" type="success" size="small">正常</el-tag>
                                <el-tag v-if="scope.row.state=='2'" type="info" size="small">被封</el-tag>
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="操作"
                            min-width="80">
                            <template slot-scope="scope">
                                <router-link target="_blank" :to="'/admin/webmaster/ad/'+scope.row.id">编辑</router-link>
                                <router-link target="_blank" :to="'/admin/webmaster/earningclick/'+scope.row.id">点击</router-link><br/>
                                <router-link target="_blank" :to="'/admin/webmaster/earningday/'+scope.row.id">每天数据</router-link>
                            </template>
                        </el-table-column>
                    </el-table-column>
                </el-table>
                <div class="page-box">
                    <el-pagination
                    @current-change="pageChange"
                    layout="total, prev, pager, next, jumper"
                    :page-size="paramete.limit"
                    :total="data.count">
                    </el-pagination>
                </div>
            </el-col>
        </el-row>
        <br/>
        <br/>

        <el-row :gutter="24">
            <el-col :span="16">
                <el-table height="600" border style="width: 100%" :data="dataday.earnings">
                    <el-table-column label="点击来源">
                        <el-table-column
                            label="PV量/单价">
                            <template slot-scope="scope">
                                {{scope.row.pv_number}}&nbsp;&nbsp;&nbsp;{{ parseInt(scope.row.money / scope.row.pv_number * 10000)/10 }}<span class="info">/千</span>
                                <el-progress :show-text="false" :stroke-width="3" :percentage="scope.row.pv_number/dataday.max_pv_number*100" status="success"></el-progress>
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="IP量/点击">
                            <template slot-scope="scope">
                                {{scope.row.ip_number}}&nbsp;&nbsp;&nbsp;{{ parseInt(scope.row.money / scope.row.ip_number * 100000)/10 }}<span class="info">/万</span>
                                <el-progress :show-text="false" :stroke-width="3" :percentage="scope.row.ip_number/dataday.max_ip_number*100" status="success"></el-progress>
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="PC量/点击率">
                            <template slot-scope="scope">
                                {{scope.row.pc_number}}&nbsp;&nbsp;&nbsp;{{ parseInt(scope.row.pc_number / scope.row.pv_number * 10000)/100 }}%
                                <el-progress :show-text="false" :stroke-width="3" :percentage="scope.row.pc_number/dataday.max_pc_number*100" status="success"></el-progress>
                            </template>
                        </el-table-column>

                        <el-table-column
                            width="100"
                            label="总额(元)">
                            <template slot-scope="scope">
                                <div>{{scope.row.money}}<span class="info">元</span></div>
                            </template>
                        </el-table-column>

                        <el-table-column
                            prop="date"
                            width="100"
                            label="时间">
                        </el-table-column>

                        <el-table-column
                            v-bind:router="true"
                            fixed="right"
                            width="60"
                            label="操作">
                            <template slot-scope="scope">
                                <el-button type="text" size="medium" @click="getEarningHourChart(scope.row.webmaster_ad_id, scope.row.date)">小时</el-button>
                            </template>
                        </el-table-column>
                    </el-table-column>
                </el-table>
            </el-col>
            <el-col :span="8">
                <x-chart height="200" :id="id" ref="chart"></x-chart>
            </el-col>
        </el-row>
        <br/>
        <br/>

        <el-row :gutter="24">
            <el-col :span="8">
                <el-table height="250" border style="width: 100%" :data="click_source_data" v-loading="click_source_loading">
                    <el-table-column label="点击来源">
                        <el-table-column
                            label="名称"
                            prop="name"
                            min-width="120">
                        </el-table-column>
                        <el-table-column
                            label="数量"
                            prop="value">
                        </el-table-column>
                        <el-table-column
                            label="百分比"
                            prop="percent">
                        </el-table-column>
                    </el-table-column>
                </el-table>
            </el-col>
            <el-col :span="8">
                <el-table height="250" border style="width: 100%" :data="ipnumber_data" v-loading="ipnumber_loading">
                    <el-table-column label="同UVIP数量">
                        <el-table-column
                            label="版本"
                            prop="number"
                            min-width="120">
                        </el-table-column>
                        <el-table-column
                            label="数量"
                            prop="value">
                        </el-table-column>
                        <el-table-column
                            label="百分比"
                            prop="percent">
                        </el-table-column>
                    </el-table-column>
                </el-table>
            </el-col>
            <el-col :span="8">
                <el-table height="250" border style="width: 100%" :data="domain_data" v-loading="domain_loading">
                    <el-table-column label="广告域名">
                        <el-table-column
                            label="名称"
                            min-width="120">
                            <template slot-scope="scope">
                                <span style="white-space: nowrap;">{{scope.row.domain}}</span>
                            </template>
                        </el-table-column>
                        <el-table-column
                            label="数量"
                            prop="number">
                        </el-table-column>
                        <el-table-column
                            label="百分比"
                            prop="percent">
                        </el-table-column>
                        <el-table-column
                            label="操作">
                            <template slot-scope="scope">
                                <img :src="'https://baidurank.aizhan.com/api/mbr?domain='+scope.row.domain+'&style=images'"/>
                            </template>
                        </el-table-column>
                    </el-table-column>
                </el-table>
            </el-col>
        </el-row>
        <br/>
        <br/>
        
        <el-row :gutter="24">
            <el-col :span="8">
                <el-table height="200" border style="width: 100%" :data="weixin_wap_data" v-loading="weixin_wap_loading">
                    <el-table-column label="微信WAP比例">
                        <el-table-column
                            label="名称"
                            prop="name"
                            min-width="120">
                        </el-table-column>
                        <el-table-column
                            label="数量"
                            prop="value">
                        </el-table-column>
                        <el-table-column
                            label="百分比"
                            prop="percent">
                        </el-table-column>
                    </el-table-column>
                </el-table>
            </el-col>
            <el-col :span="8">
                <el-table height="200" border style="width: 100%" :data="ios_android_data" v-loading="ios_android_loading">
                    <el-table-column label="IOS安卓分布">
                        <el-table-column
                            label="名称"
                            prop="name"
                            min-width="120">
                        </el-table-column>
                        <el-table-column
                            label="数量"
                            prop="value">
                        </el-table-column>
                        <el-table-column
                            label="百分比"
                            prop="percent">
                        </el-table-column>
                    </el-table-column>
                </el-table>
            </el-col>
            <el-col :span="8">
                <el-table height="200" border style="width: 100%" :data="iframe_data" v-loading="iframe_loading">
                    <el-table-column label="嵌套iframe">
                        <el-table-column
                            label="名称"
                            prop="name"
                            min-width="120">
                        </el-table-column>
                        <el-table-column
                            label="数量"
                            prop="value">
                        </el-table-column>
                        <el-table-column
                            label="百分比"
                            prop="percent">
                        </el-table-column>
                    </el-table-column>
                </el-table>
            </el-col>
        </el-row>
        <br/>
        <br/>
            
        <el-row :gutter="24">
            <el-col :span="8">
                <el-table height="400" border style="width: 100%" :data="browser_data" v-loading="browser_loading">
                    <el-table-column label="浏览器分布">
                        <el-table-column
                            label="名称"
                            prop="name"
                            min-width="120">
                        </el-table-column>
                        <el-table-column
                            label="数量"
                            prop="value">
                        </el-table-column>
                        <el-table-column
                            label="百分比"
                            prop="percent">
                        </el-table-column>
                    </el-table-column>
                </el-table>
            </el-col>
            <el-col :span="8">
                <el-table height="400" border style="width: 100%" :data="jssystem_data" v-loading="jssystem_loading">
                    <el-table-column label="JS系统版本">
                        <el-table-column
                            label="名称"
                            prop="name"
                            min-width="120">
                        </el-table-column>
                        <el-table-column
                            label="数量"
                            prop="value">
                        </el-table-column>
                        <el-table-column
                            label="百分比"
                            prop="percent">
                        </el-table-column>
                    </el-table-column>
                </el-table>
            </el-col>
            <el-col :span="8">
                <el-table height="400" border style="width: 100%" :data="history_data" v-loading="history_loading">
                    <el-table-column label="历史数量">
                        <el-table-column
                            label="名称(个)"
                            prop="number"
                            min-width="120">
                        </el-table-column>
                        <el-table-column
                            label="数量"
                            prop="value">
                        </el-table-column>
                        <el-table-column
                            label="百分比"
                            prop="percent">
                        </el-table-column>
                    </el-table-column>
                </el-table>
            </el-col>
        </el-row>
        <br/>
        <br/>

        <el-row :gutter="24">
            <el-col :span="8">
                <el-table height="500" border style="width: 100%" :data="system_version_data" v-loading="system_version_loading">
                    <el-table-column label="系统版本">
                        <el-table-column
                            label="版本"
                            prop="version"
                            min-width="120">
                        </el-table-column>
                        <el-table-column
                            label="数量"
                            prop="value">
                        </el-table-column>
                        <el-table-column
                            label="百分比"
                            prop="percent">
                        </el-table-column>
                    </el-table-column>
                </el-table>
            </el-col>
            <el-col :span="8">
                <el-table height="500" border style="width: 100%" :data="interval_time_data" v-loading="interval_time_loading">
                    <el-table-column label="间隔时间">
                        <el-table-column
                            label="时间(秒)"
                            prop="second"
                            min-width="120">
                        </el-table-column>
                        <el-table-column
                            label="数量"
                            prop="value">
                        </el-table-column>
                        <el-table-column
                            label="百分比"
                            prop="percent">
                        </el-table-column>
                    </el-table-column>
                </el-table>
            </el-col>
            <el-col :span="8">
                <el-table height="500" border style="width: 100%" :data="screen_data" v-loading="screen_loading">
                    <el-table-column label="屏幕分辨率">
                        <el-table-column
                            label="分辨率"
                            prop="name"
                            min-width="120">
                        </el-table-column>
                        <el-table-column
                            label="数量"
                            prop="value">
                        </el-table-column>
                        <el-table-column
                            label="百分比"
                            prop="percent">
                        </el-table-column>
                    </el-table-column>
                </el-table>
            </el-col>
        </el-row>
        <br/>
        <br/>

        <el-row :gutter="24">
            <el-col :span="16">
                <div id="container" style="width:460px;height:860px"></div>
            </el-col>
            <el-col :span="8">
                <el-table height="400" border style="width: 100%" :data="city_data" v-loading="city_loading">
                    <el-table-column label="城市分布">
                        <el-table-column
                            label="城市"
                            prop="name"
                            min-width="120">
                        </el-table-column>
                        <el-table-column
                            label="数量"
                            prop="value">
                        </el-table-column>
                        <el-table-column
                            label="百分比"
                            prop="percent">
                        </el-table-column>
                    </el-table-column>
                </el-table>
            </el-col>
        </el-row>
    </div>
</div>
</template>
        
<script>
import HighCharts from 'highcharts'
import XChart from './../chart.vue'

export default {
    name: 'recharges',
    data: function () { 
        return {
            paramete: {
                offset: 0,
                limit: 1,
            },
            loading: true,
            data: {},
            id: 'chart',

            dataday: {},

            group: Group,
            browser_data: [],
            browser_loading: true,

            system_version_data: [],
            system_version_loading: true,

            interval_time_data: [],
            interval_time_loading: true,

            history_data: [],
            history_loading: true,

            ipnumber_data: [],
            ipnumber_loading: true,

            click_source_data: [],
            click_source_loading: true,

            jssystem_data: [],
            jssystem_loading: true,

            screen_data: [],
            screen_loading: true,

            weixin_wap_data: [],
            weixin_wap_loading: true,

            ios_android_data: [],
            ios_android_loading: true,

            iframe_data: [],
            iframe_loading: true,

            domain_data: [],
            domain_loading: true,

            city_data: [],
            city_loading: true,
        };
    },
    created: function () {
        this.group.page = window.location.pathname;
        this.getClicks();
    },
    components: {
        XChart
    },
    methods:{
        getClicks: function()
        {
            var Th = this;
            Th.loading = true;
            Th.$api.get('admin/data/click/cache.json', Th.paramete, function(data)
            {
                Th.paramete.cache_key = data.cache_key;
                Th.loading = false;
                Th.data = data;

                
                Th.getWeixinWap();
                Th.getIosAndroid();
                Th.getIframe();
                Th.getBrowser();
                Th.getJssystem();
                Th.getHistory();
                Th.getSystemVersion();
                Th.getIntervalTime();
                Th.getScreen();
                Th.getClickSource();
                Th.getIpnumber();
                Th.getPosition();
                Th.getDomain();
                Th.getCity();
                Th.getEarningDay(data.ads[0].id);
                Th.getEarningHourChart(data.ads[0].id, '2019-09-30');

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getClicks();
        },
        getBrowser: function()
        {
            var Th = this;
            Th.browser_loading = true;
            Th.$api.get('admin/data/click/browser.json', Th.paramete, function(data)
            {
                Th.browser_data = data;
                Th.browser_loading = false;

            }, function(type, message){ Th.browser_loading = false; Th.$emit('message', type, message); });
        },
        getSystemVersion: function()
        {
            var Th = this;
            Th.system_version_loading = true;
            Th.$api.get('admin/data/click/system_version.json', Th.paramete, function(data)
            {
                Th.system_version_data = data;
                Th.system_version_loading = false;

            }, function(type, message){ Th.system_version_loading = false; Th.$emit('message', type, message); });
        },
        getIntervalTime: function()
        {
            var Th = this;
            Th.interval_time_loading = true;
            Th.$api.get('admin/data/click/interval_time.json', Th.paramete, function(data)
            {
                Th.interval_time_data = data;
                Th.interval_time_loading = false;

            }, function(type, message){ Th.interval_time_loading = false; Th.$emit('message', type, message); });
        },
        getHistory: function()
        {
            var Th = this;
            Th.history_loading = true;
            Th.$api.get('admin/data/click/history.json', Th.paramete, function(data)
            {
                Th.history_data = data;
                Th.history_loading = false;

            }, function(type, message){ Th.history_loading = false; Th.$emit('message', type, message); });
        },
        getIpnumber: function()
        {
            var Th = this;
            Th.ipnumber_loading = true;
            Th.$api.get('admin/data/click/ipnumber.json', Th.paramete, function(data)
            {
                Th.ipnumber_data = data;
                Th.ipnumber_loading = false;

            }, function(type, message){ Th.ipnumber_loading = false; Th.$emit('message', type, message); });
        },
        getClickSource: function()
        {
            var Th = this;
            Th.click_source_loading = true;
            Th.$api.get('admin/data/click/source.json', Th.paramete, function(data)
            {
                Th.click_source_data = data;
                Th.click_source_loading = false;

            }, function(type, message){ Th.click_source_loading = false; Th.$emit('message', type, message); });
        },
        getJssystem: function()
        {
            var Th = this;
            Th.jssystem_loading = true;
            Th.$api.get('admin/data/click/jssystem.json', Th.paramete, function(data)
            {
                Th.jssystem_data = data;
                Th.jssystem_loading = false;

            }, function(type, message){ Th.jssystem_loading = false; Th.$emit('message', type, message); });
        },
        getScreen: function()
        {
            var Th = this;
            Th.screen_loading = true;
            Th.$api.get('admin/data/click/screen.json', Th.paramete, function(data)
            {
                Th.screen_data = data;
                Th.screen_loading = false;

            }, function(type, message){ Th.screen_loading = false; Th.$emit('message', type, message); });
        },
        getWeixinWap: function()
        {
            var Th = this;
            Th.weixin_wap_loading = true;
            Th.$api.get('admin/data/click/weixin_wap.json', Th.paramete, function(data)
            {
                Th.weixin_wap_data = data;
                Th.weixin_wap_loading = false;

            }, function(type, message){ Th.weixin_wap_loading = false; Th.$emit('message', type, message); });
        },
        getIosAndroid: function()
        {
            var Th = this;
            Th.ios_android_loading = true;
            Th.$api.get('admin/data/click/ios_android.json', Th.paramete, function(data)
            {
                Th.ios_android_data = data;
                Th.ios_android_loading = false;

            }, function(type, message){ Th.ios_android_loading = false; Th.$emit('message', type, message); });
        },
        getIframe: function()
        {
            var Th = this;
            Th.iframe_loading = true;
            Th.$api.get('admin/data/click/iframe.json', Th.paramete, function(data)
            {
                Th.iframe_data = data;
                Th.iframe_loading = false;

            }, function(type, message){ Th.iframe_loading = false; Th.$emit('message', type, message); });
        },
        getDomain: function()
        {
            var Th = this;
            if(!Th.paramete.url)
            {
                Th.domain_loading = true;
                Th.$api.get('admin/data/click/domain.json', Th.paramete, function(data)
                {
                    Th.domain_data = data;
                    Th.domain_loading = false;

                }, function(type, message){ Th.domain_loading = false; Th.$emit('message', type, message); });
            }
        },
        getPosition: function()
        {
            var Th = this;
            Th.ios_android_loading = true;
            Th.$api.get('admin/data/click/click_position.json', Th.paramete, function(data)
            {
                var series_data = [];
                data.forEach(function(value,i){
                    series_data[i] = [value.width,value.height];
                });
                
                var chart = HighCharts.chart('container', {
                    chart: {
                        type: 'scatter',
                        zoomType: 'xy',
                        plotBorderColor: '#606266',
                        plotBorderWidth: 1,
                        plotBackgroundImage: '/website/images/iphone.jpg',
                    },
                    title: {
                        text: '点击分布'
                    },
                    xAxis: {
                        title: { text: '' },
                        min: -50,
                        max: 410,
                        opposite: true,
                    },
                    yAxis: {
                        title: { text: '' },
                        reversed: true,
                        min: -100,
                        max: 760,
                    },
                    plotOptions: {
                        scatter: {
                            tooltip: {
                                headerFormat: '<b>{series.name}</b><br>',
                                pointFormat: 'x:{point.x},  y:{point.y}'
                            }
                        }
                    },
                    series: [{
                        name: '点击分布',
                        color: 'rgba(119, 152, 191, .5)',
                        data: series_data,
                    }]
                });

            }, function(type, message){ Th.ios_android_loading = false; Th.$emit('message', type, message); });

        },
        getCity: function() 
        {
            var Th = this;
            Th.city_loading = true;
            Th.$api.get('admin/data/click/city.json', Th.paramete, function(data)
            {
                Th.city_data = data;
                Th.city_loading = false;

            }, function(type, message){ Th.city_loading = false; Th.$emit('message', type, message); });
        },
        getEarningDay: function(webmaster_ad_id)
        {
            var Th = this;
            Th.$api.get('admin/webmaster/earning/day/'+webmaster_ad_id+'.json', [], function(data)
            {
                Th.dataday = data;

            }, function(type, message){ Th.$emit('message', type, message); });
        },
        getEarningHourChart: function(webmaster_ad_id, date) {

            var Th = this;

            Th.$api.get('admin/webmaster/earning/hour/chart/'+webmaster_ad_id+'.json', { date: date, type: 2}, function(data){

                Th.initialHourChart(data);
                
            }, function(type, message){ Th.$emit('message', type, message); });
        },
        initialHourChart: function(data)
        {
            var today = [];
            var yesterday = [];

            //今天
            for(var index in data.today){
                today[index] = data.today[index].pv_number;
            }

            //昨天
            for(var index in data.yesterday){
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