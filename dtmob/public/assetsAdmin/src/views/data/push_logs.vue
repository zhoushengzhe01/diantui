<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">推送日志</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-select v-model="paramete.state" placeholder="推送状态">
                            <el-option label="成功" value="1"></el-option>
                            <el-option label="失败" value="2"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="success" @click="getPushLogs">查询</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>
        
        <div class="box" v-loading="loading">
            <el-table :data="data.pushlogs" style="width: 100%">
                <el-table-column
                    label="推送数量"
                    min-width="150">
                    <template slot-scope="scope">
                        {{scope.row.push_number}} 条
                    </template>
                </el-table-column>
                <el-table-column
                    label="耗时"
                    min-width="150">
                    <template slot-scope="scope">
                        {{scope.row.push_time}} 秒
                    </template>
                </el-table-column>
                <el-table-column
                    label="IP地址"
                    min-width="150">
                    <template slot-scope="scope">
                        {{scope.row.ip}}
                    </template>
                </el-table-column>
                <el-table-column
                    label="状态"
                    min-width="150">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.state=='1'" type="success" size="small">成功</el-tag>
                        <el-tag v-if="scope.row.state=='2'" type="info" size="small">失败</el-tag>
                    </template>
                </el-table-column>
                <el-table-column
                    prop="created_at"
                    label="推送时间"
                    min-width="200">
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
        this.getPushLogs();
    },
    methods:{
        getPushLogs: function()
        {
            var Th = this;
            Th.loading = true;
            Th.$api.get('admin/data/push_logs.json', Th.paramete, function(data)
            {
                Th.data = data;
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getPushLogs();
        },
    },
}
</script>