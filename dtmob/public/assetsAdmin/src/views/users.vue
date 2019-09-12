<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">员工管理</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-select v-model="paramete.department_id" placeholder="部门">
                            <el-option v-for="item in group.departments" :key="item.key" :label="item.name" :value="item.id"></el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item>
                        <el-input v-model="paramete.username" placeholder="用户名"></el-input>
                    </el-form-item>
                    
                    <el-form-item>
                        <el-button type="success" @click="getUsers">查询</el-button>
                        <el-button type="success" @click="getUser('')">添加</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">
            <el-table :data="data.users" style="width: 100%">

                <el-table-column
                    label="联盟"
                    min-width="100">
                    <template slot-scope="scope">
                        <span v-for="item in group.alliance_agents" :key="item.key" v-if="item.id==scope.row.alliance_agent_id">{{item.name}}</span>
                    </template>
                </el-table-column>
                

                <el-table-column
                    label="部门"
                    min-width="100">
                    <template slot-scope="scope">
                        <span v-for="item in group.departments" :key="item.key" v-if="item.id==scope.row.department_id">{{item.name}}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    prop="username"
                    label="用户名"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    prop="nickname"
                    label="真实姓名"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    prop="stagename"
                    label="艺名"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    prop="mobile"
                    label="电话"
                    min-width="150">
                </el-table-column>

                <el-table-column
                    prop="qq"
                    label="QQ"
                    min-width="150">
                </el-table-column>

                <el-table-column
                    label="状态"
                    min-width="100">
                    <template slot-scope="scope">
                        <el-switch
                            @change="postCategory(scope.row)"
                            v-model="scope.row.state"
                            active-value="1"
                            inactive-value="2"
                            active-color="#13ce66"
                            inactive-color="#ff4949">
                        </el-switch>
                    </template>
                </el-table-column>

                <el-table-column
                    prop="created_at"
                    label="时间"
                    min-width="160">
                </el-table-column>

                <el-table-column
                    fixed="right"
                    label="操作"
                    width="80">
                    <template slot-scope="scope">
                        <el-button type="text" size="medium" @click="getUser(scope.row)">编辑</el-button>
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

             <el-form :model="user" label-width="80px" size="medium" v-loading="loadingItem">

                <el-form-item label="登陆用户">
                    <el-input v-model="user.username"></el-input>
                </el-form-item>

                <el-form-item label="所属联盟">
                    <el-select v-model="user.alliance_agent_id" placeholder="联盟" style="width:100%">
                        <el-option v-for="item in group.alliance_agents" :key="item.key" :label="item.name" :value="item.id"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="所属部门">
                    <el-select v-model="user.department_id" placeholder="部门" style="width:100%">
                        <el-option v-for="item in group.departments" :key="item.key" :label="item.name" :value="item.id"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="登陆密码">
                    <el-input v-model="user.password" placeholder="不填写则为不修改"></el-input>
                </el-form-item>

                <el-form-item label="真实姓名">
                    <el-input v-model="user.nickname"></el-input>
                </el-form-item>

                <el-form-item label="对外艺名">
                    <el-input v-model="user.stagename"></el-input>
                </el-form-item>

                <el-form-item label="电话号码">
                    <el-input v-model="user.mobile"></el-input>
                </el-form-item>

                <el-form-item label="QQ 号码">
                    <el-input v-model="user.qq"></el-input>
                </el-form-item>

                <el-form-item label="状态">
                    <el-select v-model="user.state" placeholder="请选择状态" style="width:100%">
                        <el-option label="使用" value="1"></el-option>
                        <el-option label="停止" value="2"></el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            
            <div slot="footer" class="dialog-footer">
                <el-button @click="show = false">取 消</el-button>
                <el-button type="success" @click="postUser(user)">确 定</el-button>
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
            
            loadingItem: true,

            show: false,
            
            user: {},

            paramete: {
                offset: 0,
                limit: 15,
            },
            data: {},
        };
    },
    created: function () {
        
        this.group.page = window.location.pathname;

        this.getUsers();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getUsers: function()
        {
            var Th = this;

            Th.loading = true;
            
            Th.$api.get('admin/users.json', Th.paramete, function(data)
            {
                Th.data = data;

                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        
        },
        pageChange: function(val) {
            
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);

            this.getUsers();
        },
        //-------------------------------------列表分页-------------------------------------

        getUser: function(row)
        {
            var Th = this;

            Th.show = true;

            if(row)
            {
                Th.user = row;
            }
            else
            {
                Th.user = {};
            }

            Th.loadingItem = false;
        },

        postUser: function(row)
        {   
            var Th = this;

            Th.loadingItem = true;

            if(row.id)
            {
                Th.$api.put('admin/user/'+row.id+'.json', row, function(data)
                {
                    Th.$emit('message', 'success', '编辑成功');

                    Th.show = false;

                    Th.loadingItem = false;

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
            else
            {
                Th.$api.post('admin/user.json', row, function(data)
                {
                    Th.$emit('message', 'success', '添加成功');

                    Th.show = false;

                    Th.getUsers();

                    Th.loadingItem = false;

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
            
        },

    },

}
</script>