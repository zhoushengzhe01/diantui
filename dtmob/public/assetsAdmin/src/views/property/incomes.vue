<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">广告收益</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">

                    <el-form-item label="" v-if="data.all_income">
                        合计：{{ Math.ceil(data.all_income.money*100)/100 }} 元
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
                        <el-button type="success" @click="getIncomes">查询</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">
            <el-table :data="data.incomes" style="width: 100%">
                <el-table-column
                    prop="date"
                    label="时间"
                    min-width="150">
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
                    min-width="150">
                </el-table-column>

                <el-table-column
                    prop="pc_number"
                    label="点击"
                    min-width="150">
                </el-table-column>

                <el-table-column
                    prop="ip_number"
                    label="IP数"
                    min-width="150">
                </el-table-column>

                <el-table-column
                    label="金额"
                    min-width="150">
                    <template slot-scope="scope">
                        {{ Math.ceil(scope.row.money*100)/100 }} 元
                    </template>
                </el-table-column>

            </el-table>

        </div>

    </div>
</template>
<script>
export default {
    name: 'earning_services',
    data: function () { 
        return {
            group: Group,
            loading: true,
            paramete: {},
            data: {},
        };
    },
    created: function () {

        this.getIncomes();
    },
    methods:{
        getIncomes: function()
        {
            var Th = this;
            Th.loading = true;

            Th.$api.get('admin/property/incomes.json', Th.paramete, function(data)
            {
                Th.data = data;
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
    },
}
</script>