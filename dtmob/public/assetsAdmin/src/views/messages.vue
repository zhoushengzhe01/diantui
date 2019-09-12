<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">网站公告</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-input v-model="paramete.title" placeholder="标题"></el-input>
                    </el-form-item>
                    
                    <el-form-item>
                        <el-button type="success" @click="getMessages">查询</el-button>
                        <el-button type="success" @click="getMessage('')">添加</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">

            <el-table :data="data.messages" style="width: 100%">

                <el-table-column
                    prop="id"
                    label="ID"
                    min-width="80">
                </el-table-column>

                <el-table-column
                    prop="title"
                    label="标题"
                    min-width="600">
                </el-table-column>

                <el-table-column
                    prop="recommend"
                    label="推荐"
                    min-width="80">
                    <template slot-scope="scope">
                        <span v-if="scope.row.recommend=='1'">是</span>
                        <span v-if="scope.row.recommend=='0'">否</span>
                    </template>
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
                            @change="putMessage(scope.row)"
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
                        <el-button type="text" size="medium" @click="getMessage(scope.row)">编辑</el-button>
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

             <el-form :model="message" label-width="80px" size="medium" v-loading="loadingItem">

                <el-form-item label="公告标题">
                    <el-input v-model="message.title"></el-input>
                </el-form-item>

                <el-form-item label="公告内容">
                    <el-input type="textarea" rows="5" v-model="message.content"></el-input>
                </el-form-item>

                <el-form-item label="是否推荐">
                    <el-select v-model="message.recommend" placeholder="请选择状态" style="width:100%">
                        <el-option label="是" value="1"></el-option>
                        <el-option label="否" value="0"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="状态">
                    <el-select v-model="message.state" placeholder="请选择状态" style="width:100%">
                        <el-option label="使用" value="1"></el-option>
                        <el-option label="停止" value="2"></el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            
            <div slot="footer" class="dialog-footer">
                <el-button @click="show = false">取 消</el-button>
                <el-button type="success" @click="putMessage(message)">确 定</el-button>
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
            
            loading: true,
            
            loadingItem: true,

            show: false,
            
            message: {},

            paramete: {
                offset: 0,
                limit: 15,
            },
            data: {},
        };
    },
    created: function () {
        
        this.group.page = window.location.pathname;

        this.getMessages();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getMessages: function()
        {
            var Th = this;

            Th.loading = true;
            
            Th.$api.get('admin/messages.json', Th.paramete, function(data)
            {
                Th.data = data;

                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        
        },
        pageChange: function(val) {
            
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);

            this.getMessages();
        },
        //-------------------------------------列表分页-------------------------------------

        getMessage: function(row)
        {
            var Th = this;

            Th.show = true;

            if(row)
            {
                Th.message = row;
            }
            else
            {
                Th.message = {

                    recommend: '0',
                    
                    state: '1',
                };
            }
            

            Th.loadingItem = false;
        },

        putMessage: function(row)
        {
            var Th = this;

            Th.loadingItem = true;

            if(row.id)
            {
                Th.$api.put('admin/message/'+row.id+'.json', row, function(data)
                {
                    Th.$emit('message', 'success', '编辑成功');

                    Th.show = false;

                    Th.loadingItem = false;

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
            else
            {
                Th.$api.post('admin/message.json', row, function(data)
                {
                    Th.$emit('message', 'success', '添加成功');

                    Th.show = false;

                    Th.getMessages();

                    Th.loadingItem = false;

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
            
        },

    },

}
</script>