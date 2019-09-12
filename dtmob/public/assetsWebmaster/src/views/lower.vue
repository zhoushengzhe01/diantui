<template>   
    <div class="content">
        <div class="tip">
            <h3>下线提成说明</h3>
            <p>1、每位站长推荐站长，来点推投放广告将获得收益<b style="color: red;">2%</b>提成返现</p>
            <p>2、结算时间在晚上凌晨3点，结算上一天的提成收益。</p>
            <p>3、努力吧兄弟 ！！！</p>
            <p style="font-weight: 700;color: #333;margin-top: 10px;">推广地址：<span style="color: red;">http://{{group.domain}}/register?wid={{group.webmaster.id}}</span></p>
        </div>
            
        <div class="title-box">
            <h3 class="title">下线链接</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-date-picker 
                            type="date" 
                            value-format="yyyy-MM-dd"
                            placeholder="选择日期" 
                            v-model="paramete.date"
                            ></el-date-picker>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="success" @click="getLowerEarnings">查询</el-button>
                    </el-form-item>
                    <el-badge :value="data.lower_count" class="item" type="warning">
                        <el-button size="mini" @click="getLowers()">我的下线</el-button>
                    </el-badge>
                </el-form>
            </div>
        </div>
        
        <div class="box" v-loading="loading">
            <el-table :data="data.lower_earnings" style="width: 100%">
                <el-table-column
                    prop="username"
                    label="下线会员">
                </el-table-column>
                <el-table-column
                    label="返点比例">
                    <template slot-scope="scope">
                        {{scope.row.return_point}} %
                    </template>
                </el-table-column>
                <el-table-column
                    label="流水金额">
                    <template slot-scope="scope">
                        {{scope.row.flowing_money}} 元
                    </template>
                </el-table-column>
                <el-table-column
                    label="返现金额">
                    <template slot-scope="scope">
                        {{scope.row.money}} 元
                    </template>
                </el-table-column>
                <el-table-column
                    prop="date"
                    label="日期">
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

        <el-dialog title="我的下线" :visible.sync="show" v-loading="lower_loading">
            <el-table class="center" :data="lower_date.lowers" style="width: 100%">
                <el-table-column
                    prop="username"
                    label="会员名称">
                </el-table-column>

                <el-table-column
                    prop="created_at"
                    label="注册时间">
                </el-table-column>
            </el-table>

            <div class="page-box">
                <el-pagination
                @current-change="pageChangeLowers"
                layout="total, prev, pager, next"
                :page-size="lower_paramete.limit"
                :total="lower_date.count">
                </el-pagination>
            </div>
        </el-dialog>

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

            lower_loading: true,
            lower_paramete: {
                offset: 0,
                limit: 10,
            },
            show: false,
            lower_date: {},
            data: {},  
        };
    },
    created: function () {

        this.group.page = '/webmaster/lower';
        
        this.getLowerEarnings();
    },
    methods:{
        getLowerEarnings: function() {
            var Th = this;
            Th.loading = true;
            Th.$api.get('webmaster/lower_earnings.json', Th.paramete, function(data){

                Th.data = data;
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getLowerEarnings();
        },
        
        
        getLowers: function()
        {
            var Th = this;
            Th.show = true;
            Th.lower_loading = true;
            Th.$api.get('webmaster/lowers.json', Th.lower_paramete, function(data){

                Th.lower_date = data;
                Th.lower_loading = false;

            }, function(type, message){ Th.lower_loading = false; Th.$emit('message', type, message); });
        },
        pageChangeLowers: function(val) {
            this.lower_paramete.offset = parseInt(val-1) * parseInt(this.lower_paramete.limit);
            this.getLowers();
        },
    },
}
</script>