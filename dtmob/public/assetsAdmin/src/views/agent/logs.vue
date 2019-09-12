<template>
        <div class="content">
            <div class="title-box">
                <h3 class="title">代理登录</h3>
                <div class="search-box">
                    <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                        <el-form-item>
                            <el-input v-model="paramete.agent_id" placeholder="代理ID"></el-input>
                        </el-form-item>
                        <el-form-item>
                            <el-button type="success" @click="getLogs">查询</el-button>
                        </el-form-item>
                    </el-form>
                </div>
            </div>
        
            <div class="box" v-loading="loading">
                <el-table :data="data.logs" style="width: 100%">
                    <el-table-column
                        prop="agent_id"
                        label="代理ID"
                        min-width="80">
                    </el-table-column>

                    <el-table-column
                        label="联盟"
                        min-width="100">
                        <template slot-scope="scope">
                            <span v-for="item in group.alliance_agents" :key="item.key" v-if="item.id==scope.row.alliance_agent_id">{{item.name}}</span>
                        </template>
                    </el-table-column>
        
                    <el-table-column
                        prop="ip"
                        label="登录IP"
                        min-width="150">
                    </el-table-column>
        
                    <el-table-column
                        prop="region"
                        label="省份"
                        min-width="120">
                    </el-table-column>
                    
                    <el-table-column
                        prop="city"
                        label="城市"
                        min-width="120">
                    </el-table-column>

                    <el-table-column
                        prop="isp"
                        label="网络"
                        min-width="120">
                    </el-table-column>
        
                    <el-table-column
                        prop="date"
                        label="登录时间"
                        min-width="120">
                    </el-table-column>

                    <el-table-column
                        label="地址查询"
                        fixed="right"
                        min-width="120">
                        <template slot-scope="scope">
                            <a :href="'http://www.ip138.com/ips138.asp?ip='+scope.row.ip" target="_blank"><el-button type="text" size="medium">查询</el-button></a>
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
                    paramete: {
                        offset: 0,
                        limit: 15,
                        agent_id: this.$api.getQueryString('agent_id'),
                    },
                    data: {},
                };
            },
            created: function () {
                this.group.page = window.location.pathname;
                this.getLogs();
            },
            methods:{
                getLogs: function()
                {
                    var Th = this;
                    Th.loading = true;
                    Th.$api.get('admin/agent/logs.json', Th.paramete, function(data)
                    {
                        Th.data = data;
                        Th.loading = false;
        
                    }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
                },
                pageChange: function(val) {
                    this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
                    this.getLogs();
                },
            },
        }
        </script>