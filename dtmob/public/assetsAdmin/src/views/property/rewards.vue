<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">站长奖励</h3>
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
                        <el-button type="success" @click="getRewards">查询</el-button>
                        <el-button type="success" @click="getReward('')">添加</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">

            <el-table :data="data.rewards" style="width: 100%">
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
                    prop="money"
                    label="金额"
                    min-width="100">
                    <template slot-scope="scope">
                        {{scope.row.money}} 元
                    </template>
                </el-table-column>

                <el-table-column
                    prop="note"
                    label="说明"
                    min-width="400">
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
                        <el-button type="text" size="medium" @click="getReward(scope.row)">编辑</el-button>
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
        <el-dialog title="添加/编辑" :visible.sync="show" class="small_dialog">

             <el-form :model="reward" label-width="80px" size="medium" v-loading="loadingItem">

                <el-form-item label="站长ID">
                    <el-input v-if="!reward.id" v-model="reward.webmaster_id"></el-input>
                    <el-input v-if="reward.id" v-model="reward.webmaster_id" disabled></el-input>
                </el-form-item>

                <el-form-item label="金额">
                    <el-input v-if="!reward.id" v-model="reward.money"></el-input>
                    <el-input v-if="reward.id" v-model="reward.money" disabled></el-input>
                </el-form-item>

                <el-form-item label="说明">
                    <el-input v-model="reward.note"></el-input>
                </el-form-item>

            </el-form>
            
            <div slot="footer" class="dialog-footer">
                <el-button @click="show = false">取 消</el-button>
                <el-button type="success" @click="postReward(reward)">确 定</el-button>
            </div>
        </el-dialog>

    </div>
</template>
<script>
export default {
    name: 'reward',
    data: function () { 
        return {
            group: Group,
            loading: true,
            loadingItem: true,
            show: false,
            reward: {},
            paramete: {
                offset: 0,
                limit: 15,
            },
            data: {},
        };
    },
    created: function () {
        this.group.page = window.location.pathname;
        this.getRewards();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getRewards: function()
        {
            var Th = this;

            Th.loading = true;
            
            Th.$api.get('admin/rewards.json', Th.paramete, function(data)
            {
                Th.data = data;

                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        
        },
        pageChange: function(val) {
            
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);

            this.getRewards();
        },
        //-------------------------------------列表分页-------------------------------------

        getReward: function(row)
        {
            var Th = this;

            Th.show = true;
            if(row)
            {
                Th.reward = row;
            }
            else
            {
                Th.reward = {};
            }

            Th.loadingItem = false;
        },

        postReward: function(row)
        {
            
            var Th = this;

            Th.loadingItem = true;

            if(row.id)
            {
                Th.$api.put('admin/reward/'+row.id+'.json', row, function(data)
                {
                    Th.$emit('message', 'success', '修改成功');

                    Th.show = false;

                    Th.loadingItem = false;

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
            else
            {
                Th.$api.post('admin/reward.json', row, function(data)
                {
                    Th.$emit('message', 'success', '添加成功');

                    Th.show = false;

                    Th.getRewards();

                }, function(type, message){ Th.$emit('message', type, message); });
            }
        },

    },
}
</script>