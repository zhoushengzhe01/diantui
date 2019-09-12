<template>
<div class="content">
    <div class="title-box">
        <h3 class="title">每日报表</h3>
        <div class="search-box">
            <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">

                <el-form-item placeholder="选择日期">
                    <el-date-picker
                        value-format="yyyy-MM-dd"
                        type="date"
                        placeholder="选择日期"
                        v-model="paramete.date"
                        style="width: 100%;"
                    ></el-date-picker>
                </el-form-item>
            
                <el-form-item label="">
                    <el-select v-model="paramete.advertiser_ad_id" placeholder="我的广告">
                        <el-option
                            v-for="item in ads"
                            :key="item.key"
                            :label="item.title"
                            :value="item.id"
                            :disabled="item.disabled">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="success" @click="getExpends">查询</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>

    <div class="box" v-loading="loading">

        <el-table :data="data.expends" style="width: 100%">

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
                label="投放终端"
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
                prop="date"
                label="日期"
                min-width="100">
            </el-table-column>

            <el-table-column
                v-bind:router="true"
                fixed="right"
                label="操作"
                width="100">
                <template slot-scope="scope">
                    <el-button type="text" size="small" @click="getHourExpends(scope.row)">小时量</el-button>
                </template>
            </el-table-column>
        </el-table>

        <div class="page-box">
            <el-pagination
            @current-change="pageChange"
            layout="total, prev, pager, next"
            :page-size="paramete.limit"
            :total="data.count">
            </el-pagination>
        </div>
    </div>


    <!--小时量弹出-->
    <el-dialog title="小时数据" label-position="top" :visible.sync="show" width="600px">

        <div v-loading="hourLoading">
            <el-table :data="hourdata.expends" style="width: 100%">
                <el-table-column
                    label="广告类型"
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
                    label="有效数据"
                    min-width="100">
                    <template slot-scope="scope">
                        <span v-if="scope.row.price_type=='1'">点击量 {{Math.round(scope.row.money/scope.row.show_price*1000)}}</span>
                        <span v-if="scope.row.price_type=='2'">展示量 {{Math.round(scope.row.money/scope.row.cpm_price*1000)}}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="千次价格"
                    min-width="100">
                    <template slot-scope="scope">
                        <span v-if="scope.row.price_type=='1'">{{scope.row.show_price}} 元</span>
                        <span v-if="scope.row.price_type=='2'">{{scope.row.cpm_price}} 元</span>
                    </template>
                </el-table-column>

                <el-table-column
                    prop="date"
                    label="消耗金额"
                    min-width="100">
                    <template slot-scope="scope">
                        {{Math.round(scope.row.money*100)/100}} 元
                    </template>
                </el-table-column>

                <el-table-column
                    prop="time"
                    label="时间"
                    min-width="100">
                </el-table-column>
            </el-table>

            <div class="page-box">
                <el-pagination
                @current-change="getHourExpends(hourParamete)"
                layout="total, prev, pager, next"
                :page-size="paramete.limit"
                :total="hourdata.count">
                </el-pagination>
            </div>
        </div>

        <div slot="footer" class="dialog-footer">
            <el-button type="success" @click="show = false">关闭</el-button>
        </div>
    
    </el-dialog>

  </div>
</template>
<script>
export default {
    name: 'expends',
    data: function () { 
        return {
            group: Group,
            loading: true,
            ads: {},
            type: 'day',
            paramete: {
                offset: 0,
                limit: 10,
            },
            data: {},

            hourParamete: {
                offset: 0,
                limit: 6,
            },
            hourdata: {},
            hourLoading: true,
            show: false,
        };
    },
    created: function () {
        
        this.group.page = '/advertiser/expends';

        this.getExpends();

        this.getAds();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getExpends: function()
        {
            var Th = this;
            
            Th.loading = true;

            Th.$api.get('advertiser/expends/day.json', Th.paramete, function(data)
            {
                Th.data = data;

                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val)
        {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getExpends();
        },
        //-------------------------------------列表分页-------------------------------------

        //-------------------------------------列表分页-------------------------------------
        getHourExpends: function(row)
        {
            var Th = this;
            Th.hourLoading = true;
            Th.show = true;
            Th.hourParamete.date = row.date;
            Th.hourParamete.advertiser_ad_id = row.ad_id;
            Th.$api.get('advertiser/expends/hour.json', Th.hourParamete, function(data)
            {
                Th.hourdata = data;
                Th.hourLoading = false;

            }, function(type, message){ Th.hourLoading = false; Th.$emit('message', type, message); });
        },
        pageHourChange: function(val)
        {
            this.hourParamete.offset = parseInt(val-1) * parseInt(this.hourParamete.limit);
            this.getHourExpends(this.hourParamete);
        },
        //-------------------------------------列表分页-------------------------------------


        getAds: function()
        {
            var Th = this;
            Th.$api.get('advertiser/ads.json',Th.data.paramete, function(data)
            {
                Th.ads = data.ads;

            }, function(type, message){ Th.$emit('message', type, message); });
        },

    },
}
</script>