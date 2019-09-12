<template>   
<div class="content">
    <div class="title-box">
        <h3 class="title">奖励管理</h3>
        <div class="search-box">
            <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                <el-form-item label="">
                    <el-date-picker 
                        type="date" 
                        value-format="yyyy-MM-dd"
                        placeholder="选择日期" 
                        v-model="paramete.date"
                        ></el-date-picker>
                </el-form-item>
                <el-form-item>
                    <el-button type="success" @click="getRewards">查询</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>

    <div class="box" v-loading="loading">
        <el-table :data="data.rewards" style="width: 100%">
            <el-table-column
                prop="money"
                label="金额"
                min-width="300">
                <template slot-scope="scope">
                    {{scope.row.money}} 元
                </template>
            </el-table-column>
            
            <el-table-column
                prop="created_at"
                label="时间"
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
        
        this.group.page = '/webmaster/rewards';

        this.getRewards();

    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getRewards: function() {
            
            var Th = this;
            
            Th.loading = true;

            Th.$api.get('webmaster/rewards.json', Th.paramete, function(data){
                
                Th.data = data;
                
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getRewards();
        },
        //-------------------------------------列表分页-------------------------------------
	},
}
</script>