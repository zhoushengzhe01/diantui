<template>
        <div class="content">
            <div class="title-box">
                <h3 class="title">推送日志</h3>
                <div class="search-box">
                    <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                        <el-form-item>
                            <el-form-item>
                                <el-input v-model="paramete.webmaster_ad_id" placeholder="站长广告ID"></el-input>
                            </el-form-item>
                            <el-form-item>
                                <el-input v-model="paramete.source_key" placeholder="来源关键词"></el-input>
                            </el-form-item>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="success" @click="getEarningClicks">查询</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
            
            <div class="box" v-loading="loading">
                <el-table :data="data.earning_clicks" style="width: 100%" default-expand-all="true">
                    <el-table-column type="expand" >
                        <template slot-scope="scope">
                            <br/>
                            <span>来源：{{scope.row.source}}</span>
                            <br/>
                            <br/>
                            <span>访问：{{scope.row.url}}</span>
                            <br/>
                            <br/>
                            <span>UserAgent：{{scope.row.user_agent}}</span>
                            <br/>
                        </template>
                    </el-table-column>
                    <el-table-column
                        prop="webmaster_id"
                        label="站长id"
                        min-width="60">
                    </el-table-column>
                    <el-table-column
                        prop="myads_id"
                        label="广告id"
                        min-width="60">
                    </el-table-column>
                    <el-table-column
                        prop="ip"
                        label="IP地址"
                        min-width="120">
                    </el-table-column>
                    <el-table-column
                        prop="system"
                        label="系统"
                        min-width="80">
                    </el-table-column>
                    <el-table-column
                        label="间隔"
                        min-width="60">
                        <template slot-scope="scope">
                            {{scope.row.time}} 秒
                        </template>
                    </el-table-column>
                    <el-table-column
                        label="来源"
                        min-width="80">
                        <template slot-scope="scope">
                            {{scope.row.click_source}}
                        </template>
                    </el-table-column>
                    <el-table-column
                        prop="clickp"
                        label="点击位置"
                        min-width="100">
                    </el-table-column>
                    <el-table-column
                        prop="screen"
                        label="屏幕尺寸"
                        min-width="100">
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
    
        </div>
    </template>
    <script>
    export default {
        name: 'recharges',
        data: function () { 
            return {
                group: Group,
                loading: false,
                paramete: {
                    offset: 0,
                    limit: 100,
                },
                data: {},
            };
        },
        created: function () {
            this.group.page = window.location.pathname;
        },
        methods:{
            getEarningClicks: function()
            {
                var Th = this;
                Th.loading = true;
                Th.$api.get('admin/data/webmaster_clicks.json', Th.paramete, function(data)
                {
                    Th.data = data;
                    Th.loading = false;
    
                }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
            },
            pageChange: function(val) {
                this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
                this.getEarningClicks();
            },
        },
    }
    </script>