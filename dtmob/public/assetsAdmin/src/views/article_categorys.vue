<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">分类管理</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-input v-model="paramete.title" placeholder="标题"></el-input>
                    </el-form-item>
                    
                    <el-form-item>
                        <el-button type="success" @click="getCategorys">查询</el-button>
                        <el-button type="success" @click="getCategory('')">添加</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">
            <el-table :data="data.categorys" style="width: 100%">
                <el-table-column
                    prop="title"
                    label="名称"
                    min-width="400">
                </el-table-column>

                <el-table-column
                    prop="sort"
                    label="排序"
                    min-width="60">
                </el-table-column>

                <el-table-column
                    prop="created_at"
                    label="时间"
                    min-width="160">
                </el-table-column>

                <el-table-column
                    label="状态"
                    min-width="80">
                    <template slot-scope="scope">
                        <el-switch
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
                        <el-button type="text" size="medium" @click="getCategory(scope.row)">编辑</el-button>
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
            
            <el-form :model="category" label-width="45px" size="medium" v-loading="loadingItem">

                <el-form-item label="标题">
                    <el-input v-model="category.title"></el-input>
                </el-form-item>

                <el-row>
                    <el-col :span="12">
                        <el-form-item label="状态">
                            <el-select v-model="category.state" placeholder="请选择状态" style="width:100%">
                                <el-option label="使用" value="1"></el-option>
                                <el-option label="停止" value="2"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="排序">
                            <el-input v-model="category.sort"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>

            <div slot="footer" class="dialog-footer">
                <el-button @click="show = false">取 消</el-button>
                <el-button type="success" @click="postCategory">确 定</el-button>
            </div>
        </el-dialog>

    </div>
</template>
<script>
export default {
    name: 'Message',
    data: function () { 
        return {
            group: Group,
            show: false,

            loading: true,
            loadingItem: true,

            category: {},

            paramete: {
                offset: 0,
                limit: 15,
            },
            data: {},
        };
    },
    created: function () {
        
        this.group.page = window.location.pathname;

        this.getCategorys();
    },
    methods:{

        getCategorys: function()
        {
            var Th = this;

            Th.loading = true;

            Th.$api.get('admin/article/categorys.json', Th.paramete, function(data)
            {

                Th.data = data;

                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        
        },
        pageChange: function(val) {
            
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);

            this.getArticles();
        },


        getCategory: function(row)
        {
            var Th = this;

            Th.loadingItem = true;

            Th.show = true;

            if(row.id)
            {
                Th.category = row; 
            }
            else
            {
                Th.category = {};
            }

            Th.loadingItem = false;
        },

        postCategory: function()
        {
            var Th = this;

            Th.loadingItem = true;

            if(Th.category.id)
            {
                Th.$api.put('admin/article/category/'+Th.category.id+'.json', Th.category, function(data)
                {
                    Th.loadingItem = false;

                    Th.show = false;

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
            else
            {
                Th.$api.post('admin/article/category.json', Th.category, function(data)
                {
                    Th.loadingItem = false;

                    Th.show = false;

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
        }
    },
}
</script>