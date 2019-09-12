<template>
<div class="content">
    <div class="title-box">
        <h3 class="title">代理管理</h3>
        <div class="search-box">
            <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                <el-form-item>
                    <el-input v-model="paramete.id" placeholder="代理ID"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-input v-model="paramete.username" placeholder="用户名"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="success" @click="getAgents">查询</el-button>
                </el-form-item>
                <el-form-item>
                    <el-button type="success" @click="getAgent('')">添加代理</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>

    <div class="box" v-loading="loading">
        <el-table :data="data.agents" style="width: 100%">
            <el-table-column
                prop="id"
                label="代理ID"
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
                prop="username"
                label="用户名"
                min-width="150">
            </el-table-column>

            <el-table-column
                prop="nickname"
                label="真实姓名"
                min-width="120">
            </el-table-column>

            <el-table-column
                prop="return_point"
                label="返点"
                min-width="120">
            </el-table-column>

            <el-table-column
                prop="email"
                label="邮箱"
                min-width="120">
            </el-table-column>

            <el-table-column
                prop="qq"
                label="QQ号码"
                min-width="120">
            </el-table-column>

            <el-table-column
                prop="money"
                label="账户余额"
                min-width="120">
                <template slot-scope="scope">
                    {{scope.row.money}} 元
                </template>
            </el-table-column>
            
            <el-table-column
                label="代理状态"
                min-width="100">
                <template slot-scope="scope">
                    <el-tag v-if="scope.row.state=='1'" type="success" size="small">正常</el-tag>
                    <el-tag v-if="scope.row.state=='2'" type="info" size="small">停止</el-tag>
                </template>
            </el-table-column>

            <el-table-column
                prop="created_at"
                label="注册时间"
                min-width="200">
            </el-table-column>

            <el-table-column
                v-bind:router="true"
                fixed="right"
                label="操作"
                width="120">
                <template slot-scope="scope">
                    <el-button type="text" size="medium" @click="getAgent(scope.row)">编辑</el-button>
                    <router-link :to="'/admin/agent/earnings?agent_id='+scope.row.id">收益</router-link>
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

        <el-form :model="agent" label-width="80px" size="medium" v-loading="loadingItem">
            
            <el-form-item label="所属联盟">
                <el-select v-model="agent.alliance_agent_id" placeholder="联盟" style="width:100%">
                    <el-option v-for="item in group.alliance_agents" :key="item.key" :label="item.name" :value="item.id"></el-option>
                </el-select>
            </el-form-item>
            
            <el-form-item label="用户名称">
                <el-input v-model="agent.username"></el-input>
            </el-form-item>

            <el-form-item label="真实姓名">
                <el-input v-model="agent.nickname"></el-input>
            </el-form-item>

            <el-form-item label="返点比例">
                <el-input v-model="agent.return_point"></el-input>
            </el-form-item>
            
            <el-form-item label="输入密码">
                <el-input v-model="agent.password"></el-input>
            </el-form-item>

            <el-form-item label="邮箱地址">
                <el-input v-model="agent.email"></el-input>
            </el-form-item>

            <el-form-item label="电话号码">
                <el-input v-model="agent.mobile"></el-input>
            </el-form-item>

            <el-form-item label="QQ 号码">
                <el-input v-model="agent.qq"></el-input>
            </el-form-item>

            <el-row>
                <el-col :span="12">
                    <el-form-item label="对接商务">
                        <el-select v-model="agent.busine_id" placeholder="选择所属商务">
                            <el-option v-for="item in users" :key="item.key" :label="item.nickname" :value="item.id"></el-option>
                        </el-select> 
                    </el-form-item>
                </el-col>
                <el-col :span="12">
                    <el-form-item label="代理状态">
                        <el-select v-model="agent.state">
                            <el-option label="开启" value="1"></el-option>
                            <el-option label="停止" value="2"></el-option>
                        </el-select>
                    </el-form-item>
                </el-col>
            </el-row>

        </el-form>

        <div slot="footer" class="dialog-footer">
            <el-button @click="show = false" :disabled="loadingItem">取 消</el-button>
            <el-button type="success" @click="putAgent()" :disabled="loadingItem">确 定</el-button>
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
            loadingItem: false,
            paramete: {
                offset: 0,
                limit: 15,
            },
            agent: {},
            data: {},
            show: false,
        };
    },
    created: function () {
        this.group.page = window.location.pathname;
        this.getAgents();
    },
    methods:{
        getAgents: function()
        {
            var Th = this;
            Th.loading = true;
            Th.$api.get('admin/agent/agents.json', Th.paramete, function(data)
            {
                Th.data = data;
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getAgents();
        },
        
        getAgent: function(row)
        {
            var Th = this;
            
            Th.getUsers();

            if(row){
                Th.agent = row;

            }else{
                Th.agent = {state: '1'};
            }
            Th.show = true;
        },
        putAgent: function()
        {
            var Th = this;
            Th.loadingItem = true;
            if(Th.agent.id)
            {
                Th.$api.put('admin/agent/agent/'+Th.agent.id+'.json', Th.agent, function(data)
                {
                    Th.$emit('message', 'success', '编辑成功');
                    Th.show = Th.loadingItem = false;
                    Th.getAgents();

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
            else
            {
                Th.$api.post('admin/agent/agent.json', Th.agent, function(data)
                {
                    Th.$emit('message', 'success', '添加成功');
                    Th.show = Th.loadingItem = false;
                    Th.getAgents();

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
        },
        getUsers: function()
        {
            var Th = this;
            Th.loadingItem = true;
            Th.$api.get('admin/users.json', {department_id: 4}, function(data)
            {
                Th.users = data.users;
                Th.loadingItem = false;

            }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
        },
    },
}
</script>