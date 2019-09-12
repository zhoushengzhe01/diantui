<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">发送邮件</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-input v-model="paramete.title" placeholder="邮件标题"></el-input>
                    </el-form-item>

                    <el-form-item>
                        <el-input v-model="paramete.user_id" placeholder="操作人ID"></el-input>
                    </el-form-item>
                    
                    <el-form-item>
                        <el-button type="success" @click="getCustomersMails">查询</el-button>
                        <el-button type="success" @click="getCustomersMail('')">添加</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">

            <el-table :data="data.mails" style="width: 100%">
                <el-table-column
                    prop="id"
                    label="客户ID"
                    min-width="80">
                    <template slot-scope="scope">
                        {{scope.row.customer_id}}：{{scope.row.mail_id}}
                    </template>
                </el-table-column>

                <el-table-column
                    prop="user_id"
                    label="操作人"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    prop="title"
                    label="标题"
                    min-width="300">
                </el-table-column>

                <el-table-column
                    prop="customer_mail"
                    label="客户邮箱"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    label="邮箱类型"
                    min-width="100">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.type=='1'" type="success" size="small">系统邮件</el-tag>
                        <el-tag v-if="scope.row.type=='2'" type="info" size="small">自定义邮件</el-tag>
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

                        <el-button type="text" size="medium" @click="postCustomersMail(scope.row)">编辑</el-button>
                        <el-button type="text" size="medium" @click="postCustomersMail(scope.row)">查看</el-button>
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
        <el-dialog title="发送邮件" :visible.sync="show" class="small_dialog">

             <el-form :model="mail" label-width="80px" size="medium" v-loading="loadingItem">

                <el-form-item label="邮箱标题">
                    <el-input v-model="mail.title"></el-input>
                </el-form-item>

                <el-form-item label="发送类型">
                    <el-select v-model="mail.type" placeholder="选择类型" style="width:100%">
                        <el-option label="系统邮件" value="1"></el-option>
                        <el-option label="自定义邮件" value="2"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="选择邮件" v-if="mail.type=='1'">
                    <el-select v-model="mail.mail_id" placeholder="选择邮件" style="width:100%">
                        <el-option v-for="item in mails" :key="item.key" :label="item.title" :value="item.id"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="邮件内容" v-if="mail.type=='2'">
                    <el-input v-model="mail.content" type="textarea" rows="10"></el-input>
                </el-form-item>

            </el-form>
            
            <div slot="footer" class="dialog-footer">
                <el-button @click="show = false">取 消</el-button>
                <el-button type="success" @click="postCustomersMail">确 定</el-button>
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
            customer_id: this.$route.params.customer_id,
            show: false,
            mails: {},
            mail: {},
            paramete: {
                offset: 0,
                limit: 15,
                type: '2'
            },
            data: {},
        };
    },
    created: function () {
        
        this.group.page = '/admin/customers/mails';

        this.getMails();

        this.getCustomersMails();
    },
    methods:{
        getMails: function()
        {
            var Th = this;
            
            Th.$api.get('admin/mails.json', {offset: 0, limit: 50, type: '2'}, function(data)
            {
                Th.mails = data.mails;

            }, function(type, message){Th.$emit('message', type, message); });
        },

        //-------------------------------------列表分页-------------------------------------
        getCustomersMails: function()
        {
            var Th = this;
            
            Th.loading = true;
            
            Th.$api.get('admin/customer/mails.json', Th.paramete, function(data)
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

        getCustomersMail: function(row)
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

        postCustomersMail: function()
        {
            var Th = this;
            
            Th.loadingItem = true;

            Th.$api.post('admin/customer/mail/'+Th.customer_id+'.json', Th.mail, function(data)
            {

                Th.$emit('message', 'success', '添加成功');

                Th.show = false;

                Th.loadingItem = false;

            }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
        },
    },
}
</script>