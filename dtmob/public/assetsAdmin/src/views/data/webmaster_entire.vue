<template>
<div class="content">
    <div class="title-box">
        <h3 class="title">整体分析</h3>
        <div class="search-box">
            <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                <el-form-item>
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
                </el-form-item>
                <el-form-item>
                    <el-button type="success" @click="getClicks">查询</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>


    <div class="box" v-loading="loading">
        <el-row :gutter="24" v-if="data.ads">
            <el-col :span="16">
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
                            label="名称"
                            prop="position_name">
                        </el-table-column>

                        <el-table-column
                            label="位置">
                            <template slot-scope="scope">
                                <span v-if="scope.row.position=='1'">左飘/上飘</span>
                                <span v-if="scope.row.position=='2'">右飘/下飘</span>
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="计费率">
                            <template slot-scope="scope">
                                {{scope.row.out_advertiser_price}}/{{scope.row.in_advertiser_price}}
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="暗高度">
                            <template slot-scope="scope">
                                {{scope.row.hid_height}}
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="暗计费">
                            <template slot-scope="scope">
                                {{scope.row.hid_height_chance}}%
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="直返">
                            <template slot-scope="scope">
                                开启 <br/>
                                20%
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="点击率">
                            <template slot-scope="scope">
                                10 % <br/>
                                10 %
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="状态"
                            min-width="80">
                            <template slot-scope="scope">
                                <el-tag v-if="scope.row.state=='1'" type="success" size="small">正常</el-tag>
                                <el-tag v-if="scope.row.state=='2'" type="info" size="small">被封</el-tag>
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

            <el-col :span="8">
                <el-table height="250" border style="width: 100%" :data="domain_data" v-loading="domain_loading">
                    <el-table-column label="广告域名">
                        <el-table-column
                            label="名称"
                            prop="domain"
                            min-width="120">
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
                                <span @click="paramete.url=scope.row.domain">检测</span>
                            </template>
                        </el-table-column>
                    </el-table-column>
                </el-table>
                
                <!-- <el-table border style="width: 100%" :data="data.webmaster_ad">
                    <el-table-column label="广告信息">

                        <el-table-column
                            label="名称"
                            prop="position_name">
                        </el-table-column>

                        <el-table-column
                            label="位置">
                            <template slot-scope="scope">
                                <span v-if="scope.row.position=='1'">左飘/上飘</span>
                                <span v-if="scope.row.position=='2'">右飘/下飘</span>
                            </template>
                        </el-table-column>

                        <el-table-column
                            label="计费率">
                            <template slot-scope="scope">
                                {{scope.row.out_advertiser_price}}/{{scope.row.in_advertiser_price}}
                            </template>
                        </el-table-column>

                    </el-table-column>
                </el-table> -->
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
                <el-table height="250" border style="width: 100%" :data="city_data" v-loading="city_loading">
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
        <br/>
        <br/>

        <el-row :gutter="24">
            <el-col :span="8">
                <div id="container" style="width:460px;height:860px"></div>
            </el-col>
            <el-col :span="8">
                
            </el-col>
            <el-col :span="8">
                
            </el-col>
        </el-row>
    </div>
</div>
</template>
        
<script>
import HighCharts from 'highcharts'

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
                Th.getCity()

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
            Th.ipnumber_loading = true;
            Th.$api.get('admin/data/click/city.json', Th.paramete, function(data)
            {
                Th.city_data = data;
                Th.city_loading = false;

            }, function(type, message){ Th.city_loading = false; Th.$emit('message', type, message); });
        },
    },
}
</script>