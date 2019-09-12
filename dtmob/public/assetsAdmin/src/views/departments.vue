<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">部门管理</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-input v-model="paramete.name" placeholder="名称"></el-input>
                    </el-form-item>
                    
                    <el-form-item>
                        <el-button type="success" @click="getDepartments">查询</el-button>
                        <el-button type="success" @click="getDepartment('')">添加</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">

            <el-table :data="data.departments" style="width: 100%">

                <el-table-column
                    prop="id"
                    label="ID"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    prop="name"
                    label="名称"
                    min-width="200">
                </el-table-column>

                <el-table-column
                    prop="created_at"
                    label="时间"
                    min-width="200">
                </el-table-column>

                <el-table-column
                    label="状态"
                    min-width="100">
                    <template slot-scope="scope">
                        <el-switch
                            @change="putDepartment(scope.row)"
                            v-model="scope.row.state"
                            active-value="1"
                            inactive-value="2"
                            active-color="#13ce66"
                            inactive-color="#ff4949">
                        </el-switch>
                    </template>
                </el-table-column>
   
                <el-table-column
                    fixed="right"
                    label="操作"
                    width="100">
                    <template slot-scope="scope">
                        <el-button type="text" size="medium" @click="getDepartment(scope.row)">编辑</el-button>
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
        <el-dialog title="添加/编辑" :visible.sync="show" class="big_dialog">
             <el-form :model="department" label-width="80px" size="medium" v-loading="loadingItem">
                <el-row>
                    <el-col :span="12">
                        <el-form-item label="名称">
                            <el-input v-model="department.name"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="状态">
                            <el-select v-model="department.state" placeholder="请选择状态" style="width:100%">
                                <el-option label="使用" value="1"></el-option>
                                <el-option label="停止" value="2"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-form-item label="权限">
                    <el-tree
                        :data="menus"
                        show-checkbox
                        default-expand-all
                        node-key="id"
                        ref="tree"
                        highlight-current
                        :default-checked-keys="default_checked"
                        :props="defaultProps">
                    </el-tree>
                </el-form-item>
                
            </el-form>
            
            <div slot="footer" class="dialog-footer">
                <el-button @click="show = false">取 消</el-button>
                <el-button type="success" @click="putDepartment(department)">确 定</el-button>
            </div>
        </el-dialog>

    </div>
</template>
<script>
export default {
    name: 'department',
    data: function () { 
        return {
            group: Group,
            
            loading: true,
            
            loadingItem: true,

            show: false,
            
            department: {},

            paramete: {
                offset: 0,
                limit: 15,
            },

            data: {},
            
            default_checked: [],

            menus: [],
        };
    },
    created: function () {
        
        this.group.page = window.location.pathname;

        this.getDepartments();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getDepartments: function()
        {
            var Th = this;

            Th.loading = true;
            
            Th.$api.get('admin/departments.json', Th.paramete, function(data)
            {
                Th.data = data;

                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },

        getMenus: function()
        {
            var Th = this;

            Th.loading = true;
            
            Th.$api.get('admin/menus.json', [], function(data)
            {
                Th.data = data;

                Th.menus = data.menus;

                Th.loadingItem = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },

        pageChange: function(val) {
            
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);

            this.getDepartments();
        },
        //-------------------------------------列表分页-------------------------------------

        getDepartment: function(row)
        {
            var Th = this;

            Th.show = true;

            if(row)
            {
                Th.department = row;
            }
            else
            {
                Th.department = {};
            }

            Th.default_checked = Th.department.rule;

            Th.getMenus();
        },

        putDepartment: function(row)
        {
            var Th = this;

            Th.loadingItem = true;

            row.menus = Th.$refs.tree.getCheckedKeys();

            if(row.id)
            {
                Th.$api.put('admin/department/'+row.id+'.json', row, function(data)
                {
                    Th.$emit('message', 'success', '编辑成功');

                    Th.show = false;

                    Th.loadingItem = false;

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
            else
            {
                Th.$api.post('admin/department.json', row, function(data)
                {
                    Th.$emit('message', 'success', '添加成功');

                    Th.show = false;

                    Th.getDepartments();

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
            
        },

    },

}
</script>