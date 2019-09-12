<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">联盟支出</h3>
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

                    <el-form-item>
                        <el-select v-model="paramete.alliance_id" placeholder="选择联盟">
                            <el-option v-for="item in group.alliances" :key="item.key" :label="item.name" :value="parseInt(item.id)"></el-option>
                        </el-select>
                    </el-form-item>
                    
                    <el-form-item>
                        <el-button type="success" @click="getSpendings">查询</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">

            <el-table :data="data.spendings" style="width: 100%">
                <el-table-column
                    fixed="left"
                    prop="name"
                    label="名称"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    label="计费次数"
                    min-width="100">
                    <template slot-scope="scope">
                        {{scope.row.record_num}} 次
                    </template>
                </el-table-column>

                <el-table-column
                    label="价格"
                    min-width="100">
                    <template slot-scope="scope">
                        {{scope.row.price}} 元
                    </template>
                </el-table-column>

                <el-table-column
                    label="点击次数"
                    min-width="120">
                    <template slot-scope="scope">
                    点 {{scope.row.click_number}}
                    </template>
                </el-table-column>

                <el-table-column
                    label="点击次数"
                    min-width="120">
                    <template slot-scope="scope">
                    展 {{scope.row.pv_number}}
                    </template>
                </el-table-column>

                <el-table-column
                    label="点击率"
                    min-width="120">
                    <template slot-scope="scope">
                    {{ parseInt(scope.row.click_number / scope.row.pv_number * 10000)/100}}%
                    </template>
                </el-table-column>

                <el-table-column
                    label="支出"
                    min-width="100">
                    <template slot-scope="scope">
                    {{scope.row.expend}} 元
                    </template>
                </el-table-column>

                <el-table-column
                    fixed="right"
                    prop="created_at"
                    label="时间"
                    min-width="160">
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
                limit: 24,
                alliance_id: this.$api.getQueryString('alliance_id'),
            },
            data: {},
        };
    },
    created: function ()
    {
        this.group.page = window.location.pathname;

        this.getSpendings();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getSpendings: function()
        {
            var Th = this;
            
            Th.loading = true;

            Th.$api.get('admin/alliance/spendings.json', Th.paramete, function(data)
            {
                Th.data = data;
            
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getSpendings();
        },
        //-------------------------------------列表分页-------------------------------------
    },
}
</script>