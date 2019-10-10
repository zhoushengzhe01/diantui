<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">站长提现</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    
                    <el-form-item label="" v-if="data.all_money">
                        合计：{{ Math.ceil(data.all_money*100)/100 }} 元
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
                        <el-input v-model="paramete.webmaster_id" placeholder="站长ID"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-input v-model="paramete.username" placeholder="站长用户名"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-input v-model="paramete.bank_account" placeholder="开户人"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-input v-model="paramete.bank_branch" placeholder="所属支行"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-input v-model="paramete.bank_card" placeholder="银行卡号"></el-input>
                    </el-form-item>
                    
                    <el-form-item>
                        <el-button type="success" @click="getTakemoneys">查询</el-button>
                    </el-form-item>

                    <el-form-item>
                        <el-button type="success" @click="exportTakemoney">查询导出</el-button>
                    </el-form-item>

                    <el-form-item>
                        <el-button type="success" @click="showMoney=true">手动提现</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">

            <el-table :data="data.takemoneys" style="width: 100%">
                <el-table-column
                    prop="webmaster_id"
                    label="站长ID"
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
                
                <el-table-column
                    label="账号"
                    min-width="80">
                    <template slot-scope="scope">
                        <el-tag size="small" type="danger" v-if="scope.row.webmaster_state=='2'">封号</el-tag>
                        <el-tag size="small" type="success" v-if="scope.row.webmaster_state=='1'">正常</el-tag>
                    </template>
                </el-table-column>

                <el-table-column
                    label="状态"
                    min-width="100">
                    <template slot-scope="scope">
                        <el-switch
                            v-if="scope.row.state=='1' || scope.row.state=='2'"
                            @change="putTakemoney(scope.row)"
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
                        <el-button type="text" size="medium" @click="getTakemoney(scope.row)">编辑</el-button>
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
                <el-form-item label="站长ID">
                    <el-input v-model="money.webmaster_id"></el-input>
                </el-form-item>
                <el-form-item label="提现金额">
                    <el-input v-model="money.money"></el-input>
                </el-form-item>
            </el-form>

            <div slot="footer" class="dialog-footer">
                <el-button @click="showMoney = false">取 消</el-button>
                <el-button type="success" @click="postTakemoney()">确 定</el-button>
            </div>
        </el-dialog>


        <!--弹窗编辑-->
        <el-dialog title="添加/编辑" :visible.sync="show" class="small_dialog">

             <el-form :model="takemoney" label-width="80px" size="medium" v-loading="loadingItem">
                
                <el-form-item label="银行名称">
                    <el-input v-model="takemoney.bank_name"></el-input>
                </el-form-item>

                <el-form-item label="所在支行">
                    <el-input v-model="takemoney.bank_branch"></el-input>
                </el-form-item>

                <el-form-item label="银行卡号">
                    <el-input v-model="takemoney.bank_card"></el-input>
                </el-form-item>

                <el-form-item label="收款人名">
                    <el-input v-model="takemoney.bank_account"></el-input>
                </el-form-item>

                <el-form-item label="提现金额">
                    <el-input v-model="takemoney.money"><template slot="append">元</template></el-input>
                </el-form-item>

                <el-form-item label="打款状态">
                    <el-select v-model="takemoney.state" placeholder="请选择状态" style="width:100%">
                        <el-option label="未结算" value="1"></el-option>
                        <el-option label="已结算" value="2"></el-option>
                        <el-option label="拒绝" value="3"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="拒绝理由" v-if="takemoney.state=='3'">
                    <el-input type="textarea" rows="2" v-model="takemoney.message"></el-input>
                </el-form-item>
            </el-form>
            
            <div slot="footer" class="dialog-footer">
                <el-button @click="show = false">取 消</el-button>
                <el-button type="success" @click="putTakemoney(takemoney)">确 定</el-button>
            </div>
        </el-dialog>

    </div>
</template>
<script>
export default {
    name: 'takemoney',
    data: function () { 
        return {
            group: Group,
            loading: true,
            loadingItem: true,
            loadingMoney: false,
            show: false,
            showMoney: false,
            takemoney: {},
            money: {},
            paramete: {
                offset: 0,
                limit: 15,
                start_date: '',
                stop_date: '',
            },
            data: {},
        };
    },
    created: function () {
        
        this.group.page = window.location.pathname;

        this.getTakemoneys();
    },
    methods:{
        getTakemoneys: function()
        {
            var Th = this;
            Th.loading = true;
            Th.$api.get('admin/takemoneys.json', Th.paramete, function(data)
            {
                Th.data = data;
                Th.loading = false;
            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        
        },
        pageChange: function(val) {
            
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getTakemoneys();
        },
        getTakemoney: function(row)
        {
            var Th = this;
            Th.show = true;
            Th.takemoney = row;
            Th.loadingItem = false;
        },
        putTakemoney: function(row)
        {
            var Th = this;
            Th.loadingItem = true;
            Th.$api.put('admin/takemoney/'+row.id+'.json', row, function(data)
            {
                Th.$emit('message', 'success', '编辑成功');
                Th.show = false;
                Th.loadingItem = false;
            }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
        },
        postTakemoney: function()
        {
            var Th = this;
            Th.loadingMoney = true;
            Th.$api.post('admin/takemoney.json', Th.money, function(data)
            {
                Th.$emit('message', 'success', '提现成功');
                Th.showMoney = false;
                Th.loadingMoney = false;
            }, function(type, message){ Th.loadingMoney = false; Th.$emit('message', type, message); });
        },
        exportTakemoney: function()
        {
            var Th = this;
            if(Th.paramete.start_date==undefined || Th.paramete.stop_date==undefined){
                Th.$emit('message', 'warning', '请选择起止时间');
            }else{
                window.location.href="/admin/takemoney/export.json?start_date="+Th.paramete.start_date+"&stop_date="+Th.paramete.stop_date;
            }
        },
    },
}
</script>