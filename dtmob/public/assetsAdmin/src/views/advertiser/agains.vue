<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">二次点击</h3>
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
                        <el-input v-model="paramete.advertiser_ad_id" placeholder="广告ID"></el-input>
                    </el-form-item>

                    <el-form-item>
                        <el-input v-model="paramete.pv_number" placeholder="大于PV"></el-input>
                    </el-form-item>
     
                    <el-form-item>
                        <el-button type="success" @click="getAgains">查询</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">

            <el-table :data="data.agains" style="width: 100%">
                <el-table-column
                    fixed="left"
                    prop="advertiser_ad_id"
                    label="广告ID"
                    min-width="80">
                </el-table-column>

                <el-table-column
                    prop="host"
                    label="域名"
                    min-width="120">
                </el-table-column>

                <el-table-column
                    label="PV曝光"
                    min-width="100">
                    <template slot-scope="scope">
                        <span style="color:#ccc">PV:</span>{{scope.row.pv_number}}
                    </template>
                </el-table-column>

                <el-table-column
                    label="PC点击"
                    min-width="120">
                    <template slot-scope="scope">
                        <span style="color:#ccc">PC:</span>{{scope.row.pc_number}}
                    </template>
                </el-table-column>

                <el-table-column
                    label="点击率"
                    min-width="80">
                    <template slot-scope="scope">
                        <template v-if="scope.row.pc_number!=0 && scope.row.pv_number!=0">{{ parseInt(scope.row.pc_number / scope.row.pv_number * 10000)/100 }}%</template>
                        <template v-else>0%</template>
                    </template>
                </el-table-column>
                
                <el-table-column
                    prop="date"
                    label="时间"
                    min-width="100">
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
                advertiser_ad_id: this.$api.getQueryString('advertiser_ad_id'),
            },
            data: {},
        };
    },
    created: function () {
        this.group.page = window.location.pathname;
        this.getAgains();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getAgains: function()
        {
            var Th = this;
            Th.loading = true;
            Th.$api.get('admin/advertiser/agains.json', Th.paramete, function(data)
            {
                Th.data = data;
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getAgains();
        },
        //-------------------------------------列表分页-------------------------------------
    },
}
</script>