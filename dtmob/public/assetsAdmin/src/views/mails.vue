<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">系统邮件</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-input v-model="paramete.title" placeholder="邮件标题"></el-input>
                    </el-form-item>

                    <el-form-item>
                        <el-input v-model="paramete.type" placeholder="邮件类型"></el-input>
                    </el-form-item>
                    
                    <el-form-item>
                        <el-button type="success" @click="getmails">查询</el-button>
                        <el-button type="success" @click="getMail('')">添加</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">

            <el-table :data="data.mails" style="width: 100%">
                <el-table-column
                    prop="id"
                    label="ID"
                    min-width="60">
                </el-table-column>

                <el-table-column
                    prop="title"
                    label="标题"
                    min-width="400">
                </el-table-column>

                <el-table-column
                    label="类型"
                    min-width="100">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.type=='1'" type="success" size="small">媒介</el-tag>
                        <el-tag v-if="scope.row.type=='2'" type="success" size="small">广告</el-tag>
                    </template>
                </el-table-column>

                <el-table-column
                    label="状态"
                    min-width="100">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.state=='1'" type="success" size="small">使用</el-tag>
                        <el-tag v-if="scope.row.state=='2'" type="info" size="small">禁止</el-tag>
                    </template>
                </el-table-column>

                <el-table-column
                    prop="created_at"
                    label="时间"
                    min-width="160">
                </el-table-column>

                <el-table-column
                    v-bind:router="true"
                    fixed="right"
                    label="操作"
                    width="100">
                    <template slot-scope="scope">
                        <el-button type="text" size="medium" @click="getMail(scope.row)">编辑</el-button>
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

             <el-form label-position="top" :model="mail" label-width="80px" size="medium" v-loading="loadingItem">

                <el-form-item label="邮件标题">
                    <el-input v-model="mail.title"></el-input>
                </el-form-item>

                <el-row>
                    <el-col :span="12">
                        <el-form-item label="邮件类型">
                            <el-select v-model="mail.type" placeholder="选择类型" style="width:90%">
                                <el-option label="媒介主" value="1"></el-option>
                                <el-option label="广告主" value="2"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="邮件状态">
                            <el-select v-model="mail.state" placeholder="选择状态" style="width:100%">
                                <el-option label="使用" value="1"></el-option>
                                <el-option label="停止" value="2"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-form-item label="邮件内容">
                    <div class="edit_container">
                    <quill-editor v-model="mail.content"
                        ref="myQuillEditor"
                        class="editer"
                        :options="editorOption" @ready="onEditorReady($event)">
                    </quill-editor>
                    </div>
                </el-form-item>
            </el-form>
            
            <div slot="footer" class="dialog-footer">
                <el-button @click="show = false">取 消</el-button>
                <el-button type="success" @click="postMail">确 定</el-button>
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
            show: false,
            mail: {},
            paramete: {
                offset: 0,
                limit: 15,
            },
            data: {},
        };
    },
    created: function () {
        
        this.group.page = '/admin/mails';

        this.getMails();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getMails: function()
        {
            var Th = this;
            
            Th.loading = true;
            
            Th.$api.get('admin/mails.json', Th.paramete, function(data)
            {
                Th.data = data;
                
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getMails();
        },
        //-------------------------------------列表分页-------------------------------------

        getMail: function(row)
        {
            var Th = this;
            
            Th.show = true;

            if(row.id)
            {
                Th.mail = row;
            }
            else
            {
                Th.mail = {
                    type: '2',
                    state: '1',
                }
            }

            Th.loadingItem = false;
        },

        postMail: function()
        {
            var Th = this;
            
            Th.loadingItem = true;

            if(Th.mail.id)
            {
                Th.$api.put('admin/mail/'+Th.mail.id+'.json', Th.mail, function(data)
                {

                    Th.$emit('message', 'success', '修改成功');

                    Th.show = false;

                    Th.loadingItem = false;

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
            else
            {
                Th.$api.post('admin/mail.json', Th.mail, function(data)
                {

                    Th.$emit('message', 'success', '添加成功');

                    Th.show = false;

                    Th.loadingItem = false;

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
            
        },
    },
}
</script>