<template>
<div class="content">
    <div class="title-box">
        <h3 class="title">代理收益</h3>
        <div class="search-box">
            <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                <el-form-item>
                    <el-input v-model="paramete.agent_id" placeholder="代理ID"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-date-picker
                        value-format="yyyy-MM-dd"
                        type="date"
                        placeholder="开始时间"
                        v-model="paramete.start_date"
                        style="width: 100%;"
                    ></el-date-picker>
                </el-form-item>
                <el-form-item>
                    <el-date-picker
                        value-format="yyyy-MM-dd"
                        type="date"
                        placeholder="截至时间"
                        v-model="paramete.stop_date"
                        style="width: 100%;"
                    ></el-date-picker>
                </el-form-item>
                <el-form-item>
                    <el-button type="success" @click="getEarnings">查询</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>

    <div class="box" v-loading="loading">
        <el-table :data="data.earnings" style="width: 100%">
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
                prop="advertiser_id"
                label="广告主ID"
                min-width="80">
            </el-table-column>

            <el-table-column
                label="充值金额"
                min-width="150">
                <template slot-scope="scope">
                    {{scope.row.flowing_money}} 元
                </template>
            </el-table-column>

            <el-table-column
                label="返点比例"
                min-width="120">
                <template slot-scope="scope">
                    {{scope.row.return_point}} %
                </template>
            </el-table-column>

            <el-table-column
                label="返点金额"
                min-width="120">
                <template slot-scope="scope">
                    {{scope.row.money}} 元
                </template>
            </el-table-column>

            <el-table-column
                prop="created_at"
                label="日期"
                min-width="120">
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
                limit: 30,
                agent_id: this.$api.getQueryString('agent_id'),
            },
            data: {},
        };
    },
    created: function () {
        this.group.page = window.location.pathname;
        this.getEarnings();
    },
    methods:{
        getEarnings: function()
        {
            var Th = this;
            Th.loading = true;
            Th.$api.get('admin/agent/earnings.json', Th.paramete, function(data)
            {
                Th.data = data;
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getEarnings();
        },
    },
}
</script>