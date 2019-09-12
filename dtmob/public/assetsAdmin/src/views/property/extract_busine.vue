<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">商务提成</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
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
                        <el-button type="success" @click="getEarningBusines">查询</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">
            <el-table :data="data.busines" style="width: 100%">
                <el-table-column
                    prop="nickname"
                    label="商务名称"
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
                    label="业绩金额"
                    min-width="700">
                    <template slot-scope="scope">
                        {{scope.row.earning_money}} 元<br/>
                        <el-progress :show-text="false" :stroke-width="6" :percentage="(scope.row.earning_money/data.max_money)*100" status="success"></el-progress>
                    </template>
                </el-table-column>
                
                <el-table-column
                    label="提成金额"
                    min-width="100">
                    <template slot-scope="scope">
                        {{scope.row.money}} 元
                    </template>
                </el-table-column>

                <el-table-column
                    label="提成金额"
                    min-width="80">
                    <template slot-scope="scope">
                        {{ Math.ceil((scope.row.money/scope.row.earning_money)*10000)/100 }} %
                    </template>
                </el-table-column>

                <el-table-column
                    fixed="right"
                    label="操作"
                    width="80">
                    <template slot-scope="scope">
                        <el-button type="text" size="medium" @click="getEarningBusine(scope.row)">详细数据</el-button>
                    </template>
                </el-table-column>
            </el-table>

        </div>


        <!--弹窗编辑-->
        <el-dialog title="业绩详情" :visible.sync="show" class="small_dialog">

            <el-table :data="dataday.busine" style="width: 100%">
                <el-table-column
                    prop="date"
                    label="时间"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    label="业绩金额"
                    min-width="250">
                    <template slot-scope="scope">
                        {{scope.row.earning_money}} 元<br/>
                        <el-progress :show-text="false" :stroke-width="6" :percentage="(scope.row.earning_money/dataday.max_money)*100" status="success"></el-progress>
                    </template>
                </el-table-column>

                <el-table-column
                    label="提成"
                    min-width="80">
                    <template slot-scope="scope">
                        {{ parseInt(scope.row.earning_money*1)/100 }} 元
                    </template>
                </el-table-column>

            </el-table>

        </el-dialog>

    </div>
</template>
<script>
export default {
    name: 'earning_services',
    data: function () { 
        return {
            group: Group,
            
            loading: true,
            loadingItem: true,

            show: false,
            takemoney: {},

            paramete: {},
            data: {},
            dataday: {},
        };
    },
    created: function () {

        this.getEarningBusines();

    },
    methods:{
        getEarningBusines: function()
        {
            var Th = this;
            Th.loading = true;

            Th.$api.get('admin/earning/busines.json', Th.paramete, function(data)
            {
                Th.data = data;
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        
        },

        getEarningBusine: function(row)
        {
            var Th = this;
            
            Th.loadingItem = true;
            Th.show = true;
            
            Th.$api.get('admin/earning/busine/'+row.id+'.json', Th.paramete, function(data)
            {
                Th.dataday = data;
                Th.loadingItem = false;
                
            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
    },
}
</script>