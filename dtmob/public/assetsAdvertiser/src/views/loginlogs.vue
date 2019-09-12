<template>
    <div class="right">
        <div class="title-box">
            <h3 class="title">登录日志</h3>
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
                        <el-button type="success" @click="getLoginlogs">查询</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">

            <el-table :data="data.loginlogs" style="width: 100%">
                <el-table-column
                    fixed="left"
                    prop="ip"
                    label="登录IP"
                    min-width="130">
                </el-table-column>

                <el-table-column
                    prop="region"
                    label="省份"
                    min-width="80">
                </el-table-column>

                <el-table-column
                    prop="city"
                    label="城市"
                    min-width="80">
                </el-table-column>

                <el-table-column
                    prop="isp"
                    label="使用网络"
                    min-width="80">
                </el-table-column>


                <el-table-column
                    fixed="right"
                    prop="created_at"
                    label="时间"
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
            paramete: {
                offset: 0,
                limit: 10,
            },
            data: {},
        };
    },
    created: function () {
    
        this.group.page = '/advertiser/loginlogs';

        this.getLoginlogs();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getLoginlogs: function()
        {
            var Th = this;
            Th.loading = true;
            Th.$api.get('advertiser/loginlogs.json', Th.paramete, function(data)
            {
                Th.data = data;
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getLoginlogs();
        },
        //-------------------------------------列表分页-------------------------------------


    },
}
</script>