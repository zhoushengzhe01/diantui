<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">数据报表</h3>
        </div>

        <div class="box" v-loading="loadingday">

            <el-table :data="dataday.expends" style="width: 100%">
                <el-table-column
                    prop="alliance_flux_id"
                    label="联盟ID"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    label="联盟"
                    min-width="100">
                    <template slot-scope="scope">
                        <span v-for="item in group.alliance_agents" :key="item.key" v-if="item.id==scope.row.alliance_agent_id">{{item.name}}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    prop="pv_number"
                    label="展示"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    prop="click_number"
                    label="点击"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    label="广告消耗"
                    min-width="110">
                    <template slot-scope="scope">
                        {{scope.row.in_money}} 元
                        
                    </template>
                </el-table-column>

                <el-table-column
                    label="联盟消耗"
                    min-width="110">
                    <template slot-scope="scope">
                        {{scope.row.out_money}} 元
                    </template>
                </el-table-column>

                <el-table-column
                    prop="click_number"
                    label="点击率"
                    min-width="100">
                    <template slot-scope="scope">
                        {{ parseInt(scope.row.click_number / scope.row.pv_number * 10000)/100 }}%
                    </template>
                </el-table-column>
              
                <el-table-column
                    prop="date"
                    label="时间"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    v-bind:router="true"
                    fixed="right"
                    label="操作"
                    width="60">
                    <template slot-scope="scope">
                        <el-button type="text" size="medium" @click="getExpendHour(scope.row)">小时量</el-button>
                    </template>
                </el-table-column>
            </el-table>

            <div class="page-box">
                <el-pagination
                @current-change="pageChangeDay"
                layout="total, prev, pager, next"
                :page-size="parameteday.limit"
                :total="dataday.count">
                </el-pagination>
            </div>
        </div>



        <!--弹窗编辑-->
        <el-dialog title="小时量" :visible.sync="show" class="big_dialog">

             <el-table :data="datahour.expends" style="width: 100%" v-loading="loadinghour">

                <el-table-column
                    prop="pv_number"
                    label="展示"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    prop="click_number"
                    label="点击"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    label="广告消耗"
                    min-width="110">
                    <template slot-scope="scope">
                        {{scope.row.in_money}} 元
                        
                    </template>
                </el-table-column>

                <el-table-column
                    label="联盟消耗"
                    min-width="110">
                    <template slot-scope="scope">
                        {{scope.row.out_money}} 元
                    </template>
                </el-table-column>

                <el-table-column
                    prop="click_number"
                    label="点击率"
                    min-width="110">
                    <template slot-scope="scope">
                        {{ parseInt(scope.row.click_number / scope.row.pv_number * 10000)/100 }}%
                    </template>
                </el-table-column>
              
                <el-table-column
                    prop="time"
                    label="时间"
                    min-width="160">
                </el-table-column>

            </el-table>

            <div class="page-box">
                <el-pagination
                @current-change="pageChangeHour"
                layout="total, prev, pager, next"
                :page-size="parametehour.limit"
                :total="datahour.count">
                </el-pagination>
            </div>

            <div slot="footer" class="dialog-footer">
                <el-button @click="show = false">关 闭</el-button>
            </div>
        </el-dialog>


    </div>
</template>
<script>
export default {
    name: 'recharges',
    data: function () { 
        return {
            group: Group,
            show: false,

            loadingday: true,
            loadinghour: true,

            dataday: {},
            datahour: {},
           
            parameteday: {
                offset: 0,
                limit: 15,
            },
            parametehour: {
                offset: 0,
                limit: 12,
            },
        };
    },
    created: function () {
        
        this.group.page = '/admin/alliance/flux/expends';

        this.getExpendDay();
    },
    methods:{

        getExpendDay: function()
        {

            var Th = this;
            
            Th.loadingday = true;
            
            Th.$api.get('admin/alliance/flux/expends/day.json', Th.parameteday, function(data)
            {
                Th.dataday = data;
                
                Th.loadingday = false;

            }, function(type, message){ Th.loadingday = false; Th.$emit('message', type, message); });
        },
        pageChangeDay: function(val) {
            this.parameteday.offset = parseInt(val-1) * parseInt(this.parameteday.limit);
            this.getExpendDay();
        },


        getExpendHour: function(row)
        {

            var Th = this;

            Th.loadinghour = true;

            Th.show = true;

            if(row)
            {
                Th.parametehour.alliance_flux_id = row.alliance_flux_id;
                Th.parametehour.date = row.date;
            }
            
            Th.$api.get('admin/alliance/flux/expends/hour.json', Th.parametehour, function(data)
            {
                Th.datahour = data;
                
                Th.loadinghour = false;

            }, function(type, message){ Th.loadinghour = false; Th.$emit('message', type, message); });
        },
        pageChangeHour: function(val) {
            this.parametehour.offset = parseInt(val-1) * parseInt(this.parametehour.limit);
            this.getExpendHour('');
        },

        
    },
}
</script>