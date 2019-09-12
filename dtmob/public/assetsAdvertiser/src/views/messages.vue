<template>
    <div class="right">
        <div class="title-box">
            <h3 class="title">消息中心</h3>
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
                    min-width="400">
                </el-table-column>

                <el-table-column
                    prop="created_at"
                    label="时间"
                    min-width="200">
                </el-table-column>

                <el-table-column
                    v-bind:router="true"
                    fixed="right"
                    label="操作"
                    width="100">
                    <template slot-scope="scope">
                        <el-button type="text" size="small" @click="getMessage(scope.row)">查看</el-button>
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


        <!--弹窗我要充值-->
        <el-dialog title="查看消息" label-position="top" :visible.sync="show" width="480px">

            {{message.content}}

            <div slot="footer" class="dialog-footer">
                <el-button type="success" @click="show = false">关闭</el-button>
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
            show: false,
            paramete: {
                offset: 0,
                limit: 10,
            },
            message: {},
            data: {},
        };
    },
    created: function () {
        
        this.group.page = '/advertiser/messages';

        this.getMessages();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getMessages: function()
        {
            var Th = this;
            Th.loading = true;
            Th.$api.get('advertiser/messages.json', Th.paramete, function(data)
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

            Th.message = row;            
        },

    },
}
</script>