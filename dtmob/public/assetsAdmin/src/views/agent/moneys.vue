<template>
<div class="content">
    <div class="title-box">
        <h3 class="title">代理提现</h3>
        <div class="search-box">
            <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                <el-form-item>
                    <el-input v-model="paramete.agent_id" placeholder="代理ID"></el-input>
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
                    <el-button type="success" @click="getMoneys">查询</el-button>
                </el-form-item>

                <el-form-item>
                    <el-button type="success" @click="showMoney=true">手动提现</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>

    <div class="box" v-loading="loading">
        <el-table :data="data.moneys" style="width: 100%">
            <el-table-column
                prop="agent_id"
                label="代理"
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
                label="类型"
                min-width="80">
                <template slot-scope="scope">
                    <span v-if="scope.row.type=='1'">日结</span>
                    <span v-if="scope.row.type=='2'">周结</span>
                </template>
            </el-table-column>

            <el-table-column
                prop="bank_name"
                label="银行名称"
                min-width="100">
            </el-table-column>

            <el-table-column
                prop="bank_account"
                label="收款人"
                min-width="100">
            </el-table-column>

            <el-table-column
                prop="bank_card"
                label="银行账号"
                min-width="300">
                <template slot-scope="scope">
                    {{scope.row.bank_card}}<br/>
                    {{scope.row.bank_branch}}
                </template>
            </el-table-column>

            <el-table-column
                label="余额"
                min-width="100">
                <template slot-scope="scope">
                    {{scope.row.money}} 元
                </template>
            </el-table-column>

            <!-- <el-table-column
                label="账号"
                min-width="80">
                <template slot-scope="scope">
                    <el-tag size="small" type="danger" v-if="scope.row.webmaster_state=='2'">封号</el-tag>
                    <el-tag size="small" type="success" v-if="scope.row.webmaster_state=='1'">正常</el-tag>
                </template>
            </el-table-column> -->

            <el-table-column
                label="状态"
                min-width="100">
                <template slot-scope="scope">
                    <el-switch
                        v-if="scope.row.state=='1' || scope.row.state=='2'"
                        @change="putMoney(scope.row)"
                        v-model="scope.row.state"
                        active-value="1"
                        inactive-value="2"
                        active-color="#13ce66"
                        inactive-color="#CCCCCC">
                    </el-switch>
                    <el-tag v-if="scope.row.state=='3'" type="info" size="small">退回</el-tag>
                </template>
            </el-table-column>

            <el-table-column
                prop="created_at"
                label="时间"
                min-width="180">
            </el-table-column>

            <el-table-column
                fixed="right"
                label="操作"
                width="80">
                <template slot-scope="scope">
                    <el-button type="text" size="medium" @click="getMoney(scope.row)">编辑</el-button>
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
    <el-dialog title="手动提现" :visible.sync="showMoney" class="small_dialog" width="350px">

        <el-form :model="money" label-width="80px" size="medium" v-loading="loadingMoney">
            <el-form-item label="代理ID">
                <el-input v-model="money.agent_id"></el-input>
            </el-form-item>
            <el-form-item label="提现金额">
                <el-input v-model="money.money"></el-input>
            </el-form-item>
        </el-form>

        <div slot="footer" class="dialog-footer">
            <el-button @click="showMoney = false">取 消</el-button>
            <el-button type="success" @click="postMoney()">确 定</el-button>
        </div>
    </el-dialog>


    <!--弹窗编辑-->
    <el-dialog title="添加/编辑" :visible.sync="show" class="small_dialog">

        <el-form :model="money" label-width="80px" size="medium" v-loading="loadingItem">

            <el-form-item label="银行名称">
                <el-input v-model="money.bank_name"></el-input>
            </el-form-item>

            <el-form-item label="所在支行">
                <el-input v-model="money.bank_branch"></el-input>
            </el-form-item>

            <el-form-item label="银行卡号">
                <el-input v-model="money.bank_card"></el-input>
            </el-form-item>

            <el-form-item label="收款人名">
                <el-input v-model="money.bank_account"></el-input>
            </el-form-item>

            <el-form-item label="提现金额">
                <el-input v-model="money.money"><template slot="append">元</template></el-input>
            </el-form-item>

            <el-form-item label="打款状态">
                <el-select v-model="money.state" placeholder="请选择状态" style="width:100%">
                    <el-option label="未结算" value="1"></el-option>
                    <el-option label="已结算" value="2"></el-option>
                    <el-option label="拒绝" value="3"></el-option>
                </el-select>
            </el-form-item>

            <el-form-item label="拒绝理由" v-if="money.state=='3'">
                <el-input type="textarea" rows="2" v-model="money.message"></el-input>
            </el-form-item>
        </el-form>
       
        <div slot="footer" class="dialog-footer">
            <el-button @click="show = false">取 消</el-button>
            <el-button type="success" @click="putMoney(money)">确 定</el-button>
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
            loadingItem: true,
            loadingMoney: false,
            show: false,
            showMoney: false,
            money: {},
            paramete: {
                offset: 0,
                limit: 15,
            },
            data: {},
        };
    },
    created: function () {
        this.group.page = window.location.pathname;
        this.getMoneys();
    },
    methods:{
        //列表
        getMoneys: function()
        {
            var Th = this;
            Th.loading = true;
            Th.$api.get('admin/agent/moneys.json', Th.paramete, function(data)
            {
                Th.data = data;
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getMoneys();
        },
        //获取
        getMoney: function(row)
        {
            var Th = this;
            Th.show = true;
            Th.money = row;
            Th.loadingItem = false;
        },
        postMoney: function()
        {
            var Th = this;
            Th.loadingMoney = true;
            Th.$api.post('admin/agent/money.json', Th.money, function(data)
            {
                Th.$emit('message', 'success', '提现成功');
                Th.showMoney = false;
                Th.loadingMoney = false;
            }, function(type, message){ Th.loadingMoney = false; Th.$emit('message', type, message); });
        },
        //编辑
        putMoney: function(row)
        {
            var Th = this;
            Th.loadingItem = true;

            Th.$api.put('admin/agent/money/'+row.id+'.json', row, function(data)
            {
                Th.$emit('message', 'success', '编辑成功');
                Th.loadingItem = Th.show = false;
                Th.getMoneys();

            }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
        },
    },
}
</script>