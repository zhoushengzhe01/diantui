<template>   
<div class="content">
    <div class="title-box">
        <h3 class="title">佣金报表</h3>
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
                <el-form-item label="">
                    <el-select v-model="paramete.position_id" placeholder="类型">
                        <el-option
                            v-for="item in adtype"
                            :key="item.key"
                            :label="item.name"
                            :value="item.id">
                        </el-option>
                    </el-select>
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
                prop="webmaster_ad_id"
                label="广告ID">
            </el-table-column>

            <el-table-column
                label="金额">
                <template slot-scope="scope">
                    {{scope.row.money}} 元
                </template>
            </el-table-column>

            <el-table-column
                prop="created_at"
                label="时间">
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
	name: 'earning',
    data: function () {	
		return {
            group: Group,
            loading: true,
            adtype: {},
            paramete: {
                offset: 0,
                limit: 10,
            },
			data: {},  
		};
	},
	created: function () {
        this.group.page = '/webmaster/earnings';
        
        this.getAdtype();
        this.getEarnings();
    },
    methods:{
        getAdtype: function() {
            var Th = this;
            Th.$api.get('webmaster/adtype.json', [], function(data){
                
                Th.adtype = data.adtype;

            }, function(type, message){ Th.$emit('message', type, message); });
        },
        getEarnings: function() {
            var Th = this;
            Th.loading = true;

            Th.$api.get('webmaster/earnings/day.json', Th.paramete, function(data){
                
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