<template>
        <div class="content">
            <div class="title-box">
                <h3 class="title">点击位置</h3>
            </div>
    
            <div class="box" v-loading="loading">
    
                <el-table :data="data.locations" style="width: 100%">
                    <el-table-column
                        prop="webmaster_ad_id"
                        label="广告位ID"
                        min-width="80">
                    </el-table-column>
    
                    <el-table-column
                        prop="location"
                        label="点击位置"
                        min-width="120">
                    </el-table-column>
    
                    <el-table-column
                        prop="pc"
                        label="点击数量"
                        min-width="100">
                    </el-table-column>
    
                    <el-table-column
                        label="占比"
                        min-width="300">
                        <template slot-scope="scope">
                            <el-progress :text-inside="true" :stroke-width="18" :percentage="parseInt(scope.row.pc/data.pc*100)"></el-progress>
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
    
        </div>
    </template>
    <script>
    export default {
        name: 'recharges',
        data: function () { 
            return {
                group: Group,
                loading: true,
                webmaster_ad_id: this.$route.params.webmaster_ad_id,
                paramete: {
                    offset: 0,
                    limit: 15,
                },
                data: {},
            };
        },
        created: function () {
            
            this.group.page = '/admin/webmaster/ads';
    
            this.getWebsites();
        },
        methods:{
            //-------------------------------------列表分页-------------------------------------
            getWebsites: function()
            {
                var Th = this;
                
                Th.loading = true;
                
                Th.$api.get('admin/stat/locations/'+Th.webmaster_ad_id+'.json', Th.paramete, function(data)
                {
                    Th.data = data;
                    
                    Th.loading = false;
    
                }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
            },
            pageChange: function(val) {
                this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
                this.getWebsites();
            },
            //-------------------------------------列表分页-------------------------------------
        },
    }
    </script>