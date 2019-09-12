<template>   
    <div class="content">
        <div class="title-box">
            <h3 class="title">我的提款</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item label="">
                        <el-input v-model="paramete.username" placeholder="名称"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="success" @click="getEarnings">查询</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">
            <el-table class="center" :data="data.earnings" style="width: 100%">

                <el-table-column
                    prop="bank_name"
                    label="银行名称"
                    min-width="120">
                </el-table-column>

                <el-table-column
                    prop="bank_card"
                    label="银行账户"
                    min-width="200">
                </el-table-column>

                <el-table-column
                    prop="bank_account"
                    label="收款人"
                    min-width="120">
                </el-table-column>
                
                <el-table-column
                    prop="money"
                    label="提现金额"
                    min-width="120">                
                </el-table-column>
    
                <el-table-column
                    label="状态"
                    min-width="100">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.state=='1'" type="warning" size="mini">未结算</el-tag>
                        <el-tag v-if="scope.row.state=='2'" type="success" size="mini">已结算</el-tag>
                        <el-tag v-if="scope.row.state=='3'" type="danger" size="mini">退回</el-tag>
                    </template>
                </el-table-column>

                <el-table-column
                    label="时间"
                    prop="created_at"
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
    name: 'user',
    data: function () {	
        return {
            group: Group,
            loading: true,
            paramete: {
                offset: 0,
                limit: 10,
            },
            data: {},
        };
    },
    created: function () {
        this.group.page = '/agent/moneys';
        this.getEarnings();
    },
    methods:{
        getEarnings: function() {
            var Th = this;
            Th.loading = true;
            Th.$api.get('agent/moneys.json', Th.paramete, function(data){

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