<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">广告消耗</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">

                    <el-form-item label="" v-if="data.all_money">
                        合计消耗：{{ Math.ceil(data.all_money*100)/100 }} 元
                    </el-form-item>

                    <el-form-item label="" v-if="data.all_out_money">
                        合计支出：{{ Math.ceil(data.all_out_money*100)/100 }} 元
                    </el-form-item>

                    <el-form-item>
                        <el-date-picker
                            value-format="yyyy-MM-dd"
                            type="date"
                            placeholder="开始时间"
                            v-model="paramete.start_date"
                            style="width: 100%;"
                        ></el-date-picker>
                    </el-form-item>

                    <el-form-item>
                        <el-date-picker
                            value-format="yyyy-MM-dd"
                            type="date"
                            placeholder="截至时间"
                            v-model="paramete.stop_date"
                            style="width: 100%;"
                        ></el-date-picker>
                    </el-form-item>

                    <el-form-item>
                        <el-input v-model="paramete.advertiser_id" placeholder="广告主id"></el-input>
                    </el-form-item>

                    <el-form-item>
                        <el-input v-model="paramete.advertiser_ad_id" placeholder="广告计划id"></el-input>
                    </el-form-item>
     
                    <el-form-item>
                        <el-button type="success" @click="getExpends">查询</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">

            <el-table :data="data.expends" style="width: 100%">
                <el-table-column
                    fixed="left"
                    prop="advertiser_ad_id"
                    label="广告ID"
                    min-width="80">
                </el-table-column>

                <el-table-column
                    label="联盟"
                    min-width="100">
                    <template slot-scope="scope">
                        <span v-for="item in group.alliance_agents" :key="item.key" v-if="item.id==scope.row.alliance_agent_id">{{item.name}}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    prop="title"
                    label="广告标题"
                    min-width="180">
                </el-table-column>

                <el-table-column
                    label="展示IP"
                    min-width="100">
                    <template slot-scope="scope">
                        <span style="color:#ccc">IP:</span>{{scope.row.ip_number}}
                    </template>
                </el-table-column>

                <el-table-column
                    label="广告展示"
                    min-width="120">
                    <template slot-scope="scope">
                        <span style="color:#ccc">PV:</span>{{scope.row.pv_number}}<br/>
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
                    label="支出"
                    min-width="120">
                    <template slot-scope="scope">
                        <span style="color:#ccc">耗:</span>{{ parseInt(scope.row.money*10)/10 }}元<br/>
                        <span style="color:#ccc">支:</span>{{ parseInt(scope.row.out_money*10)/10 }}元
                    </template>
                </el-table-column>

                <el-table-column
                    label="成本"
                    min-width="80">
                    <template slot-scope="scope">
                        <template v-if="scope.row.out_money!=0 && scope.row.money!=0">{{ parseInt(scope.row.out_money/scope.row.money * 1000)/10 }}%</template>
                        <template v-else>0%</template>
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
                    label="小时量"
                    width="100">
                    <template slot-scope="scope">
                        <el-button type="text" size="medium" @click="getExpendsHour(scope.row)">列表</el-button>
                    </template>
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


        <!--弹窗编辑-->
        <el-dialog title="小时量" :visible.sync="show" class="big_dialog">

             <el-table :data="datahour.expends" style="width: 100%" v-loading="loadinghour">
                
                <el-table-column
                    label="PV展示"
                    min-width="100">
                    <template slot-scope="scope">
                        {{scope.row.pv_number}} <span v-if="scope.row.hudong_pv_number">（{{scope.row.hudong_pv_number}}）</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="IP数量"
                    min-width="100">
                    <template slot-scope="scope">
                        {{scope.row.ip_number}} <span v-if="scope.row.hudong_pc_number">（{{scope.row.hudong_pc_number}}）</span>
                    </template>
                </el-table-column>
               
                <el-table-column
                    label="PC点击"
                    min-width="100">
                    <template slot-scope="scope">
                        {{scope.row.pc_number}} <span v-if="scope.row.hudong_pc_number">（{{scope.row.hudong_pc_number}}）</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="点击率"
                    min-width="80">
                    <template slot-scope="scope">
                        {{ parseInt(scope.row.pc_number / scope.row.pv_number * 10000)/100 }}%
                        <span v-if="scope.row.hudong_pv_number">（{{ parseInt(scope.row.hudong_pc_number / scope.row.hudong_pv_number * 10000)/100 }}%）</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="消耗"
                    min-width="120">
                    <template slot-scope="scope">
                        {{scope.row.money}} 元
                    </template>
                </el-table-column>

                <el-table-column
                    label="支出"
                    min-width="120">
                    <template slot-scope="scope">
                        {{scope.row.out_money}} 元
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
            loading: true,
            paramete: {
                offset: 0,
                limit: 15,
                advertiser_ad_id: this.$api.getQueryString('advertiser_ad_id'),
            },
            data: {},

            loadinghour: true,
            show: false,
            datahour: {},
            parametehour: {
                offset: 0,
                limit: 15,
            },
        };
    },
    created: function () {
        this.group.page = window.location.pathname;
        this.getExpends();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getExpends: function()
        {
            var Th = this;
            Th.loading = true;
            Th.$api.get('admin/advertiser/expends/day.json', Th.paramete, function(data)
            {
                Th.data = data;
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getExpends();
        },
        //-------------------------------------列表分页-------------------------------------


        getExpendsHour: function(row)
        {
            var Th = this;
            Th.loadinghour = true;
            Th.show = true;
            if(row)
            {
                Th.parametehour.date = row.date;
                Th.parametehour.advertiser_ad_id = row.advertiser_ad_id;
            }

            Th.$api.get('admin/advertiser/expends/hour/'+Th.parametehour.advertiser_ad_id+'.json', Th.parametehour, function(data)
            {
                Th.datahour = data;
                Th.loadinghour = false;

            }, function(type, message){ Th.loadinghour = false; Th.$emit('message', type, message); });
        },
        pageChangeHour: function(val) {
            this.parametehour.offset = parseInt(val-1) * parseInt(this.parametehour.limit);
            this.getExpendsHour('');
        },
    },
}
</script>