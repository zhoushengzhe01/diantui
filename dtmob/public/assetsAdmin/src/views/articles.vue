<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">文章管理</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-input v-model="paramete.title" placeholder="标题"></el-input>
                    </el-form-item>

                    <el-form-item v-if="categorys">
                        <el-select v-model="paramete.category_id" placeholder="请选择状态" style="width:100%">
                            <el-option label="全部分类" value="0"></el-option>
                            <el-option v-for="item in categorys" :key="item.key" :label="item.title" :value="item.id"></el-option>
                        </el-select>
                    </el-form-item>
                    
                    <el-form-item>
                        <el-button type="success" @click="getArticles">查询</el-button>
                        <el-button type="success" @click="getArticle('')">添加</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">
            <el-table :data="data.articles" style="width: 100%">
                
                <el-table-column
                    label="联盟"
                    min-width="100">
                    <template slot-scope="scope">
                        <span v-for="item in group.alliance_agents" :key="item.key" v-if="item.id==scope.row.alliance_agent_id">{{item.name}}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    prop="title"
                    label="标题"
                    min-width="300">
                </el-table-column>

                <el-table-column
                    label="分类"
                    min-width="100">
                    <template slot-scope="scope">
                        <span v-for="item in categorys" :key="item.key" v-if="scope.row.category_id==item.id">{{item.title}}</span>
                    </template>
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
                        <el-button type="text" size="medium" @click="getArticle(scope.row)">编辑</el-button>
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
            
            <el-form :model="article" label-width="45px" size="medium" v-loading="loadingItem">

                <el-form-item label="联盟">
                    <el-select v-model="article.alliance_agent_id" placeholder="联盟" style="width:100%">
                        <el-option v-for="item in group.alliance_agents" :key="item.key" :label="item.name" :value="item.id"></el-option>
                    </el-select>
                </el-form-item>
                
                <el-form-item label="标题">
                    <el-input v-model="article.title"></el-input>
                </el-form-item>

                <el-form-item label="联盟">
                    <el-select v-model="article.alliance_agent_id" placeholder="联盟" style="width:100%">
                        <el-option v-for="item in group.alliance_agents" :key="item.key" :label="item.name" :value="item.id"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="分类">
                    <el-select v-model="article.category_id" placeholder="请选择状态" style="width:100%">
                        <el-option v-for="item in categorys" :key="item.key" :label="item.title" :value="item.id"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="简介">
                    <el-input v-model="article.intro" type="textarea" rows="2"></el-input>
                </el-form-item>

                <el-form-item label="内容">
                    <div class="edit_container">
                    <quill-editor v-model="article.content"
                        ref="myQuillEditor"
                        class="editer"
                        :options="editorOption" @ready="onEditorReady($event)">
                    </quill-editor>
                    </div>
                </el-form-item>

                <el-row>
                    <el-col :span="12">
                        <el-form-item label="状态">
                            <el-select v-model="article.state" placeholder="请选择状态" style="width:100%">
                                <el-option label="使用" value="1"></el-option>
                                <el-option label="停止" value="2"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="排序">
                            <el-input v-model="article.sort"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
            </el-form>

            <div slot="footer" class="dialog-footer">
                <el-button @click="show = false">取 消</el-button>
                <el-button type="success" @click="postArticle">确 定</el-button>
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

            categorys: {},
            article: {
                state: '1',
                sort: 50,
            },

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

        this.getArticles();
    },
    methods:{

        getCategorys: function()
        {
            var Th = this;
            
            Th.$api.get('admin/article/categorys.json', {}, function(data)
            {
                Th.categorys = data.categorys;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        
        },


        getArticles: function()
        {
            var Th = this;

            Th.loading = true;

            Th.$api.get('admin/articles.json', Th.paramete, function(data)
            {

                Th.data = data;

                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);

            this.getArticles();
        },


        getArticle: function(row)
        {
            var Th = this;

            Th.loadingItem = true;

            Th.show = true;

            if(row.id)
            {
                Th.$api.get('admin/article/'+row.id+'.json', {}, function(data)
                {
                    Th.article = data.article;

                    Th.loadingItem = false;

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
            else
            {
                Th.article = { state: '1', sort: 50}

                Th.loadingItem = false;
            }
        },

        postArticle: function()
        {
            var Th = this;

            Th.loadingItem = true;

            if(Th.article.id)
            {
                Th.$api.put('admin/article/'+Th.article.id+'.json', Th.article, function(data)
                {
                    Th.loadingItem = false;

                    Th.show = false;

                    Th.getArticles();

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
            else
            {
                Th.$api.post('admin/article.json', Th.article, function(data)
                {
                    Th.loadingItem = false;

                    Th.show = false;

                    Th.getArticles();

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
        }
    },
}
</script>