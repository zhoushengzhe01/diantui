<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">广告主管理</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-input v-model="paramete.agent_id" placeholder="代理ID"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-input v-model="paramete.id" placeholder="广告主ID"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-input v-model="paramete.username" placeholder="会员名"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-input v-model="paramete.qq" placeholder="QQ号码"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="success" @click="getAdvertisers">查询</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">

            <el-table :data="data.advertisers" style="width: 100%">
                <el-table-column
                    prop="id"
                    label="ID"
                    min-width="60">
                </el-table-column>

                <el-table-column
                    label="联盟"
                    min-width="60">
                    <template slot-scope="scope">
                        <span v-for="item in group.alliance_agents" :key="item.key" v-if="item.id==scope.row.alliance_agent_id">{{item.name}}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="商务"
                    min-width="60">
                    <template slot-scope="scope">
                        <span v-for="item in group.busines" :key="item.key" v-if="item.id==scope.row.busine_id">{{item.nickname}}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    prop="username"
                    label="用户名"
                    min-width="180">
                </el-table-column>

                <el-table-column
                    prop="nickname"
                    label="联系人"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    prop="qq"
                    label="QQ号码"
                    min-width="120">
                </el-table-column>

                <el-table-column
                    label="余额"
                    min-width="100">
                    <template slot-scope="scope">
                        {{scope.row.money}} 元
                    </template>
                </el-table-column>

                <el-table-column
                    label="状态"
                    min-width="80">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.state=='1'" type="success" size="small">正常</el-tag>
                        <el-tag v-if="scope.row.state=='2'" type="success" size="small">被封</el-tag>
                    </template>
                </el-table-column>
                <el-table-column
                    label="所属代理"
                    min-width="100">
                    <template slot-scope="scope">
                        <span v-if="scope.row.agent_id">{{scope.row.agent_id}}</span>
                        <span v-if="!scope.row.agent_id" class="info">无</span>
                    </template>
                </el-table-column>
                <el-table-column
                    prop="created_at"
                    label="注册时间"
                    min-width="200">
                </el-table-column>
                
                <el-table-column
                    v-bind:router="true"
                    fixed="right"
                    label="操作"
                    width="150">
                    <template slot-scope="scope">
                        <router-link target="_blank" :to="'/admin/advertiser/'+scope.row.id">编辑</router-link>
                        <el-button type="text" @click="advertiserLogin(scope.row)">登录</el-button>
                        <router-link target="_blank" :to="'/admin/advertiser/ad?advertiser_id='+scope.row.id">新增广告</router-link>
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
            },
            data: {},
        };
    },
    created: function () {
        
        this.group.page = window.location.pathname;

        this.getAdvertisers();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getAdvertisers: function()
        {
            var Th = this;
            
            Th.loading = true;

            Th.$api.get('admin/advertisers.json', Th.paramete, function(data)
            {
                Th.data = data;

                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getAdvertisers();
        },
        //登录广告主
        advertiserLogin: function(row){
            var Th = this;
            Th.loading = true;
            Th.$api.post('admin/advertiser/login.json', {'advertiser_id':row.id}, function(data)
            {
                Th.$emit('message', 'success', data.message);
                Th.loading = false;
                setTimeout(function(){
                    window.open(data.url, '_blank');
                }, 1000);
            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        }

    },
}
</script>