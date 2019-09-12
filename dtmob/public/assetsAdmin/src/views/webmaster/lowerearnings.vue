<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">下线收益</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-input v-model="paramete.webmaster_id" placeholder="站长ID"></el-input>
                    </el-form-item>

                    <el-form-item>
                        <el-input v-model="paramete.lower_webmaster_id" placeholder="下线ID"></el-input>
                    </el-form-item>
                    
                    <el-form-item>
                        <el-button type="success" @click="getLowerEarnings">查询</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">

            <el-table :data="data.lowerearnings" style="width: 100%">
                <el-table-column
                    prop="webmaster_id"
                    label="站长ID"
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
                    prop="lower_webmaster_id"
                    label="下线ID"
                    min-width="120">
                </el-table-column>

                <el-table-column
                    label="反点"
                    min-width="80">
                    <template slot-scope="scope">
                        {{scope.row.return_point}} %
                    </template>
                </el-table-column>

                <el-table-column
                    prop="flowing_money"
                    label="流水"
                    min-width="150">
                    <template slot-scope="scope">
                        {{scope.row.flowing_money}} 元
                    </template>
                </el-table-column>

                <el-table-column
                    label="返现金额"
                    min-width="150">
                    <template slot-scope="scope">
                        {{scope.row.money}} 元
                    </template>
                </el-table-column>

                <el-table-column
                    prop="date"
                    label="日期"
                    min-width="180">
                </el-table-column>

                <el-table-column
                    label="状态"
                    min-width="100">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.state=='0'" type="info" size="small">异常</el-tag>
                        <el-tag v-if="scope.row.state=='1'" type="success" size="small">正常</el-tag>
                    </template>
                </el-table-column>

                <el-table-column
                    prop="created_at"
                    label="操作时间"
                    min-width="180">
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
            loadingItem: true,
            show: false,
            website: {},
            paramete: {
                offset: 0,
                limit: 15,
            },
            data: {},
        };
    },
    created: function() {
        this.group.page = window.location.pathname;
        this.getLowerEarnings();
    },
    methods:{
        getLowerEarnings: function()
        {
            var Th = this;
            Th.loading = true;
            
            Th.$api.get('admin/webmaster/lower/earnings.json', Th.paramete, function(data)
            {
                Th.data = data;
                Th.loading = false;
            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getLowerEarnings();
        },
    },
}
</script>