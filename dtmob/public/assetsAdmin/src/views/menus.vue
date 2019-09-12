<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">菜单管理</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-button type="success" @click="getMenu('')">添加菜单</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">
            
            <el-table :data="data.menus" style="width: 100%">

                <el-table-column
                    label="名称"
                    min-width="150">
                    <template slot-scope="scope">
                        <b v-if="scope.row.pid == '0'"><i :class="scope.row.icon"></i> {{scope.row.name}}</b>
                        <b v-if="scope.row.pid != '0' && scope.row.type == 'menu'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{scope.row.name}}</b>
                        <span v-if="scope.row.pid != '0' && scope.row.type != 'menu'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{scope.row.name}}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    prop="type"
                    label="类型"
                    min-width="80">
                    <template slot-scope="scope">
                        {{scope.row.type}}
                    </template>
                </el-table-column>

                <el-table-column
                    prop="method"
                    label="Method"
                    min-width="80">
                </el-table-column>

                <el-table-column
                    prop="url"
                    label="地址"
                    min-width="320">
                </el-table-column>

                <el-table-column
                    label="状态"
                    min-width="80">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.status=='1'" type="success" size="small">正常</el-tag>
                        <el-tag v-if="scope.row.status=='0'" type="info" size="small">停止</el-tag>
                    </template>
                </el-table-column>

                <el-table-column
                    prop="sort"
                    label="排序"
                    min-width="80">
                </el-table-column>

                <el-table-column
                    fixed="right"
                    label="操作"
                    width="80">
                    <template slot-scope="scope">
                        <el-button type="text" size="medium" @click="getMenu(scope.row)">编辑</el-button>
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

             <el-form :model="menu" label-width="80px" size="medium" v-loading="loadingItem">

                <el-form-item label="所属分类">
                    <el-select v-model="menu.pid" placeholder="所属分类" style="width:100%">
                        <el-option label="顶级分类" :value="0"></el-option>
                        <el-option v-for="item in data.menus" :key="item.key" :label="item.name" :value="item.id" v-if="item.pid=='0'"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="菜单名称">
                    <el-input v-model="menu.name"></el-input>
                </el-form-item>

                <el-form-item label="菜单类型">
                    <el-select v-model="menu.type" placeholder="请选择类型" style="width:100%">
                        <el-option label="菜单" value="menu"></el-option>
                        <el-option label="接口" value="api"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="菜单图标" v-if="menu.type=='menu' && menu.pid=='0'">
                    <el-input v-model="menu.icon"></el-input>
                </el-form-item>

                <el-form-item label="菜单URL">
                    <el-input v-model="menu.url"></el-input>
                </el-form-item>

                <el-form-item label="菜单排序">
                    <el-input v-model="menu.sort"></el-input>
                </el-form-item>

                <el-form-item label="Method">
                    <el-select v-model="menu.method" placeholder="请选择Method" style="width:100%">
                        <el-option label="GET" value="get"></el-option>
                        <el-option label="POST" value="post"></el-option>
                        <el-option label="PUT" value="put"></el-option>
                        <el-option label="DELETE" value="delete"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="菜单状态">
                    <el-select v-model="menu.status" placeholder="请选择状态" style="width:100%">
                        <el-option label="使用" value="1"></el-option>
                        <el-option label="停止" value="0"></el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            
            <div slot="footer" class="dialog-footer">
                <el-button @click="show = false">取 消</el-button>
                <el-button type="success" @click="postMenu(menu)">确 定</el-button>
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
            menu: {},
            paramete: {
                offset: 0,
                limit: 15,
            },
            data: {},
        };
    },
    created: function () {
        
        this.group.page = window.location.pathname;
        this.getMenus();
    },
    methods:{
        //列表
        getMenus: function()
        {
            var Th = this;
            Th.loading = true;
            Th.$api.get('admin/user/menus.json', Th.paramete, function(data)
            {
                Th.data = data;
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getMenus();
        },

        //获取单个
        getMenu: function(row)
        {
            var Th = this;
            Th.show = true;
            if(row){
                Th.menu = row;
            }else{
                Th.menu = {
                    pid: 0,
                    type: 'menu',
                    sort: 50,
                    status: '1',
                    method: 'get',
                };
            }
            Th.loadingItem = false;
        },
        //编辑/添加
        postMenu: function(row)
        {
            var Th = this;
            Th.loadingItem = true;
            if(row.id)
            {
                Th.$api.put('admin/user/menu/'+row.id+'.json', row, function(data)
                {
                    Th.$emit('message', 'success', '编辑成功');
                    Th.show = false;
                    Th.getMenus();
                    Th.loadingItem = false;
                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
            else
            {
                Th.$api.post('admin/user/menu.json', row, function(data)
                {
                    Th.$emit('message', 'success', '添加成功');
                    Th.show = false;
                    Th.getMenus();
                    Th.loadingItem = false;
                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
        },
    },
}
</script>