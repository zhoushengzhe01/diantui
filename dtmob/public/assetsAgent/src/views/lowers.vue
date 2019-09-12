<template>   
    <div class="content">
        <div class="title-box">
            <h3 class="title">我的下线</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item label="">
                        <el-input v-model="paramete.username" placeholder="名称"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="success" @click="getLowers">查询</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">
            <el-table class="center" :data="data.lowers" style="width: 100%">
                <el-table-column
                    prop="username"
                    label="用户名"
                    min-width="200">
                </el-table-column>
    
                <el-table-column
                    prop="nickname"
                    label="真实姓名"
                    min-width="200">
                </el-table-column>

                <el-table-column
                    prop="created_at"
                    label="注册时间"
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
        this.group.page = '/agent/lowers';
        this.getLowers();
    },
    methods:{
        getLowers: function() {
            var Th = this;
            Th.loading = true;
            Th.$api.get('agent/lowers.json', Th.paramete, function(data){
                
                Th.data = data;
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getLowers();
        },
    },
}
</script>