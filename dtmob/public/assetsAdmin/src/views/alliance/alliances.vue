<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">联盟管理</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-input v-model="paramete.name" placeholder="联盟名称"></el-input>
                    </el-form-item>

                    <el-form-item>
                        <el-button type="success" @click="getAlliances">查询</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">

            <el-table :data="data.alliances" style="width: 100%">
                <el-table-column
                    label="名称"
                    min-width="120">
                    <template slot-scope="scope">
                        <a :href="scope.row.login_url" target="_blank"><el-button type="text" size="medium">{{scope.row.name}}</el-button></a>
                    </template>
                </el-table-column>

                <el-table-column
                    label="联盟"
                    min-width="100">
                    <template slot-scope="scope">
                        <span v-for="item in group.alliance_agents" :key="item.key" v-if="item.id==scope.row.alliance_agent_id">{{item.name}}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="广告类型"
                    min-width="80">
                    <template slot-scope="scope">
                        <span v-for="item in group.adtype" :key="item.key" v-if="item.id==scope.row.position_id">{{item.name}}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="位置"
                    min-width="80">
                    <template slot-scope="scope">
                        <span v-if="scope.row.position=='0'">无</span>
                        <span v-if="scope.row.position=='1'">顶飘</span>
                        <span v-if="scope.row.position=='2'">底飘</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="点击/千"
                    min-width="80">
                    <template slot-scope="scope">
                        {{scope.row.price}} 元
                    </template>
                </el-table-column>

                <el-table-column
                    label="展示/千"
                    min-width="80">
                    <template slot-scope="scope">
                        {{scope.row.price_cpm}} 元
                    </template>
                </el-table-column>

                <el-table-column
                    label="计费次数"
                    min-width="80">
                    <template slot-scope="scope">
                        {{scope.row.record_num}} 次
                    </template>
                </el-table-column>

                <el-table-column
                    label="状态"
                    min-width="80">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.state=='1'" type="success" size="small">使用</el-tag>
                        <el-tag v-if="scope.row.state=='2'" type="info" size="small">停止</el-tag>
                    </template>
                </el-table-column>

                <el-table-column
                    prop="account"
                    label="登录账号"
                    min-width="180">
                </el-table-column>

                <el-table-column
                    prop="password"
                    label="登录密码"
                    min-width="150">
                </el-table-column>

                <el-table-column
                    v-bind:router="true"
                    fixed="right"
                    label="操作"
                    width="100">
                    <template slot-scope="scope">
                        <router-link :to="'/admin/alliance/'+scope.row.id">
                            <el-button type="text" size="medium">编辑</el-button>
                        </router-link>

                        <router-link :to="'/admin/alliance/spendings?alliance_id='+scope.row.id">
                            <el-button type="text" size="medium">支出</el-button>
                        </router-link>
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
        
        this.getAlliances();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getAlliances: function()
        {
            var Th = this;
            
            Th.loading = true;
            
            Th.$api.get('admin/alliances.json', Th.paramete, function(data)
            {
                Th.data = data;
                
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getAlliances();
        },
        //-------------------------------------列表分页-------------------------------------
        
    },
}
</script>