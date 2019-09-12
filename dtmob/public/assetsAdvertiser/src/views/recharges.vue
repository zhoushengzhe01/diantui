<template>
    <div class="right">
        <div class="title-box">
            <h3 class="title">充值记录</h3>
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
                        <el-button type="success" @click="getRecharges">查询</el-button>
                    
                        <!-- <el-button type="success" @click="getRecharge('')">我要充值</el-button> -->
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">

            <el-table :data="data.recharges" style="width: 100%">
                <el-table-column
                    prop="id"
                    label="ID"
                    min-width="80">
                </el-table-column>

                <el-table-column
                    prop="outname"
                    label="汇款人姓名"
                    min-width="120">
                </el-table-column>

                <el-table-column
                    prop="money"
                    label="充值金额"
                    min-width="120">
                </el-table-column>

                <el-table-column
                    prop="message"
                    label="说明"
                    min-width="120">
                </el-table-column>

                <el-table-column
                    label="状态"
                    min-width="120">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.state=='2'" type="success" size="small">等待汇款</el-tag>
                        <el-tag v-if="scope.row.state=='3'" type="success" size="small">等待确认</el-tag>
                        <el-tag v-if="scope.row.state=='4'" type="success" size="small">充值完成</el-tag>
                    </template>

                </el-table-column>

                <el-table-column
                    prop="created_at"
                    label="时间"
                    min-width="200">
                </el-table-column>

                <el-table-column
                    v-bind:router="true"
                    fixed="right"
                    label="操作"
                    width="100">
                    <template slot-scope="scope">
                        <el-button type="text" size="small" @click="getRecharge(scope.row.id)">充值进度</el-button>
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


        <!--弹窗我要充值-->
        <el-dialog title="账户充值" label-position="top" :visible.sync="show" width="480px">

            <el-steps :active="parseInt(step)" simple style="line-height: 0px;">
                <el-step title="申请" icon="el-icon-edit"></el-step>
                <el-step title="汇款" icon="el-icon-upload"></el-step>
                <el-step title="等待" icon="el-icon-picture"></el-step>
                <el-step title="完成" icon="el-icon-picture"></el-step>
            </el-steps>
            <br/>

            <div v-if="step=='1'">
                <el-form ref="form" :model="recharge" size="medium">
                    <el-form-item label="出款人姓名">
                        <el-input placeholder="张某某" v-model="recharge.outname"></el-input>
                    </el-form-item>
                    <el-form-item label="充值金额">
                        <el-input placeholder="10000" v-model="recharge.money">
                            <template slot="append">元</template>
                        </el-input>
                    </el-form-item>
                    <el-form-item label="充值说明">
                        <el-input placeholder="会员充值" v-model="recharge.message"></el-input>
                    </el-form-item>
                </el-form>
            </div>

            <div v-if="step=='2'">
                <el-form ref="form" :model="recharge" size="medium">
                    <el-form-item label="汇款银行">
                        <el-input disabled value="中国银行"></el-input>
                    </el-form-item>
                    <el-form-item label="汇款账户">
                        <el-input disabled value="8888 8888 8888 8888"></el-input>
                    </el-form-item>
                    <el-form-item label="收款人">
                        <el-input disabled value="张某某"></el-input>
                    </el-form-item>
                </el-form>
            </div>

            <div v-if="step=='3'">
                <el-alert
                    title="等待财务后台确认"
                    type="warning"
                    center
                    :closable="false"
                    show-icon>
                </el-alert>
            </div>

            <div v-if="step=='4'">
                <el-alert
                    title="等待财务后台确认"
                    type="success"
                    center
                    :closable="false"
                    show-icon>
                </el-alert>
            </div>

            <div slot="footer" class="dialog-footer">
                <el-button type="success" v-if="step=='1'" @click="postRecharge">确定</el-button>
                <el-button type="success" v-if="step=='2'" @click="marine">我已汇款</el-button>
                <el-button type="success" v-if="step=='3' || step=='4'" @click="show = false">关闭</el-button>
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
            show: false,
            step: '1',
            paramete: {
                offset: 0,
                limit: 10,
            },
            recharge: {
                message: '会员充值',
                state: '1',
            },
            data: {},
        };
    },
    created: function () {
        
        this.group.page = '/advertiser/recharges';

        this.getRecharges();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getRecharges: function()
        {
            var Th = this;
            
            Th.loading = true;

            Th.$api.get('advertiser/recharges.json', Th.paramete, function(data)
            {
                Th.data = data;

                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getRecharges();
        },
        //-------------------------------------列表分页-------------------------------------

        getRecharge: function(id)
        {
            var Th = this;
            Th.show = true;

            if(id)
            {
                Th.$api.get('advertiser/recharge/'+id+'.json', [], function(data)
                {
                    Th.recharge = data.recharge;

                    Th.step = data.recharge.state;

                }, function(type, message){ Th.$emit('message', type, message); });
            }
            else
            {
                Th.recharge = {message: '会员充值', state: '1'};
                Th.step = '1';
            }
            
        },

        postRecharge: function()
        {
            var Th = this;
            
            Th.$api.post('advertiser/recharge.json', Th.recharge, function(data)
            {
                Th.recharge = data;
                
                Th.step = '2';

            }, function(type, message){ Th.$emit('message', type, message); });
        },
        putRecharge: function()
        {
            var Th = this;

            Th.$api.put('advertiser/recharge/'+Th.recharge.id+'.json', Th.recharge, function(data)
            {
                Th.recharge = data;

                Th.step = data.state;

            }, function(type, message){ Th.$emit('message', type, message); });
        },
        marine: function()
        {
            this.recharge.state = '3';
            this.putRecharge();
        },

    },
}
</script>