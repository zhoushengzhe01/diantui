<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">客户管理</h3>
            <div class="search-box">
                <el-form :inline="true" :model="parameteCustomer" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-input v-model="parameteCustomer.user_id" placeholder="会员ID"></el-input>
                    </el-form-item>

                    <el-form-item>
                        <el-input v-model="parameteCustomer.company" placeholder="公司名称"></el-input>
                    </el-form-item>

                    <el-form-item>
                        <el-input v-model="parameteCustomer.mobile" placeholder="手机号码"></el-input>
                    </el-form-item>

                    <el-form-item>
                        <el-input v-model="parameteCustomer.email" placeholder="邮箱"></el-input>
                    </el-form-item>

                    <el-form-item>
                        <el-input v-model="parameteCustomer.qq" placeholder="QQ号码"></el-input>
                    </el-form-item>

                    <el-form-item>
                        <el-button type="success" @click="getCustomers">查询</el-button>
                        <el-button type="success" @click="getCustomer('')">添加</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">
            <el-table :data="customers.customers" style="width: 100%">
                <el-table-column
                    prop="username"
                    label="员工"
                    min-width="60">
                </el-table-column>

                <el-table-column
                    label="来源"
                    min-width="100">
                    <template slot-scope="scope">
                        <span v-for="item in sources" :key="item.key" v-if="scope.row.source_id==item.id">{{item.name}}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="公司名称"
                    min-width="100">
                    <template slot-scope="scope">
                        <a v-if="scope.row.website" :href="scope.row.website" target="_blank"><el-button type="text" size="medium"><i class="fa fa-desktop"></i></el-button></a>
                        {{scope.row.company}}
                    </template>
                </el-table-column>

                <el-table-column
                    prop="contact"
                    label="联系人"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    prop="mobile"
                    label="联系方式"
                    min-width="180">
                    <template slot-scope="scope">
                        <span v-if="scope.row.mobile">电话:{{scope.row.mobile}}<br/></span>
                        <span v-if="scope.row.email">邮箱:{{scope.row.email}}<br/></span>
                        <span v-if="scope.row.qq">QQ号:{{scope.row.qq}}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    prop="level"
                    label="等级"
                    min-width="60">
                </el-table-column>

                <el-table-column
                    prop="created_at"
                    label="时间"
                    min-width="160">
                </el-table-column>

                <el-table-column
                    fixed="right"
                    label="操作"
                    width="150">
                    <template slot-scope="scope">
                        <el-button type="text" size="medium" @click="getCustomer(scope.row)">编辑</el-button>
                        <el-button type="text" size="medium" @click="getNotes(scope.row.id)">备注</el-button>
                        <el-button type="text" size="medium" @click="getNote(scope.row.id)">添加</el-button>

                        <router-link :to="'/admin/customers/mails/'+scope.row.id">
                            <el-button type="text" size="medium">邮件</el-button>
                        </router-link>
                    </template>
                </el-table-column>
            </el-table>

            <div class="page-box">
                <el-pagination
                @current-change="pageCustomerChange"
                layout="total, prev, pager, next"
                :page-size="parameteCustomer.limit"
                :total="customers.count">
                </el-pagination>
            </div>
        </div>


        <!--弹窗备注-->
        <el-dialog title="客户备注" :visible.sync="showNote" class="small_dialog">

            <el-table :data="notes.notes" style="width: 100%">
                <el-table-column
                    prop="username"
                    label="备注人"
                    min-width="80">
                </el-table-column>

                <el-table-column
                    prop="note"
                    label="备注内容"
                    min-width="350">
                </el-table-column>

                <el-table-column
                    prop="created_at"
                    label="添加"
                    min-width="100">
                </el-table-column>
            </el-table>

            <div class="page-box">
                <el-pagination
                @current-change="pageNoteChange"
                layout="total, prev, pager, next"
                :page-size="parameteNote.limit"
                :total="notes.count">
                </el-pagination>
            </div>
        </el-dialog>



        <!--弹窗编辑-->
        <el-dialog title="添加/编辑" :visible.sync="showItem" class="small_dialog">
            
            <el-form :model="customer" label-width="80px" size="medium" v-loading="loadingItem">

                <el-form-item label="客户来源">
                    <el-select v-model="customer.source_id" placeholder="选择来源" style="width:100%">
                        <el-option v-for="item in sources" :key="item.key" :label="item.name" :value="item.id"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="公司名称">
                    <el-input v-model="customer.company"></el-input>
                </el-form-item>

                <el-form-item label="公司官网">
                    <el-input v-model="customer.website"></el-input>
                </el-form-item>

                <el-form-item label="联系人">
                    <el-input v-model="customer.contact"></el-input>
                </el-form-item>

                <el-form-item label="联系电话">
                    <el-input v-model="customer.mobile"></el-input>
                </el-form-item>

                <el-form-item label="联系邮箱">
                    <el-input v-model="customer.email"></el-input>
                </el-form-item>

                <el-form-item label="联系 QQ">
                    <el-input v-model="customer.qq"></el-input>
                </el-form-item>

                <el-form-item label="客户等级">
                    <el-input v-model="customer.level"></el-input>
                </el-form-item>
            </el-form>

            <div slot="footer" class="dialog-footer">
                <el-button @click="showItem = false">取 消</el-button>
                <el-button type="success" @click="postCustomer">确 定</el-button>
            </div>
        </el-dialog>

        <!--弹窗备注-->
        <el-dialog title="添加备注" :visible.sync="showNoteItem" class="small_dialog">
            
            <el-form :model="note" size="medium" v-loading="loadingNoteItem">
                <el-input v-model="note.note" type="textarea" rows="5"></el-input>
            </el-form>

            <div slot="footer" class="dialog-footer">
                <el-button @click="showNoteItem = false">取 消</el-button>
                <el-button type="success" @click="postNote">确 定</el-button>
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
            showItem: false,
            showNote: false,
            showNoteItem: false,

            loading: true,
            loadingItem: true,
            loadingNote: true,
            loadingNoteItem: true,

            notes: {},
            note: {},
            customers: {},
            customer: {},
            sources: {},

            parameteCustomer: {
                offset: 0,
                limit: 15,
            },
            parameteNote: {
                offset: 0,
                limit: 15,
            },
        };
    },
    created: function () {
        
        this.group.page = '/admin/customers';

        this.getCustomers();

        this.getSources();


    },
    methods:{

        getSources: function()
        {
            var Th = this;

            Th.$api.get('admin/customer/sources.json', {}, function(data)
            {
                Th.sources = data.sources;

            }, function(type, message){ Th.$emit('message', type, message); });
        },


        getCustomers: function()
        {
            var Th = this;

            Th.loading = true;

            Th.$api.get('admin/customers.json', Th.parameteCustomer, function(data)
            {

                Th.customers = data;

                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageCustomerChange: function(val) {

            this.parameteCustomer.offset = parseInt(val-1) * parseInt(this.parameteCustomer.limit);
            
            this.getCustomers();
        },


        getCustomer: function(row)
        {
            var Th = this;

            Th.showItem = true;

            if(row.id)
            {
                Th.customer = row;
            }
            else
            {
                Th.customer = {};
            }

            Th.loadingItem = false;
        },

        postCustomer: function()
        {
            var Th = this;

            Th.loadingItem = true;

            if(Th.customer.id)
            {
                Th.$api.put('admin/customer/'+Th.customer.id+'.json', Th.customer, function(data)
                {
                    Th.loadingItem = false;

                    Th.showItem = false;

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
            else
            {
                Th.$api.post('admin/customer.json', Th.customer, function(data)
                {
                    Th.loadingItem = false;

                    Th.showItem = false;

                    Th.getCustomers();

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
        },






        getNotes: function(customer_id)
        {
            var Th = this;

            Th.loadingNote = true;

            Th.showNote = true;

            if(customer_id)
            {
                Th.parameteNote.customer_id = customer_id;
            }

            Th.$api.get('admin/customer/notes.json', Th.parameteNote, function(data)
            {

                Th.notes = data;

                Th.loadingNote = false;

            }, function(type, message){ Th.loadingNote = false; Th.$emit('message', type, message); });
        },
        pageNoteChange: function(val) {

            this.parameteNote.offset = parseInt(val-1) * parseInt(this.parameteNote.limit);
            
            this.getNotes('');
        },
        


        getNote: function(customer_id)
        {
            var Th = this;

            Th.showNoteItem = true;

            Th.note = {customer_id: customer_id};

            Th.loadingNoteItem = false;
        },

        postNote: function()
        {
            var Th = this;

            Th.loadingNoteItem = true;

            Th.$api.post('admin/customer/note.json', Th.note, function(data)
            {
                Th.loadingNoteItem = false;

                Th.showNoteItem = false;

            }, function(type, message){ Th.loadingNoteItem = false; Th.$emit('message', type, message); });
        },


        
    },
}
</script>