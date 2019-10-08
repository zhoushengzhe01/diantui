<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">流量池管理</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-input v-model="paramete.name" placeholder="流量池名称"></el-input>
                    </el-form-item>
                    
                    <el-form-item>
                        <el-button type="success" @click="getFlowpools">查询</el-button>
                        <el-button type="success" @click="getFlowpool('')">添加流量池</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">

            <el-table :data="data.flowpools" style="width: 100%">
                <el-table-column
                    prop="id"
                    label="ID"
                    min-width="120">
                </el-table-column>
                
                <el-table-column
                    prop="name"
                    label="名称"
                    min-width="180">
                </el-table-column>

                <el-table-column
                    prop="sort"
                    label="排序"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    label="状态"
                    min-width="100">
                    <template slot-scope="scope">
                        <el-switch
                            @change="postFlowpool(scope.row)"
                            v-model="scope.row.state"
                            active-value="1"
                            inactive-value="2"
                            active-color="#13ce66"
                            inactive-color="#ff4949">
                        </el-switch>
                    </template>
                </el-table-column>

                <el-table-column
                    v-bind:router="true"
                    fixed="right"
                    label="操作"
                    width="100">
                    <template slot-scope="scope">
                        <el-button type="text" size="medium" @click="getFlowpool(scope.row)">编辑</el-button>
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

             <el-form :model="flowpool" label-width="80px" size="medium" v-loading="loadingItem">

                <el-form-item label="流量池名称">
                    <el-input v-model="flowpool.name"></el-input>
                </el-form-item>

                <el-form-item label="流量池排序">
                    <el-input v-model="flowpool.sort"></el-input>
                </el-form-item>

                <el-form-item label="审核状态">
                    <el-select v-model="flowpool.state" placeholder="请选择状态" style="width:100%">
                        <el-option label="使用" value="1"></el-option>
                        <el-option label="禁止" value="2"></el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            
            <div slot="footer" class="dialog-footer">
                <el-button @click="show = false">取 消</el-button>
                <el-button type="success" @click="postFlowpool(flowpool)">确 定</el-button>
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
            flowpool: {},
            paramete: {
                offset: 0,
                limit: 15,
            },
            data: {},
        };
    },
    created: function () {
        
        this.group.page = window.location.pathname;

        this.getFlowpools();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getFlowpools: function()
        {
            var Th = this;
            
            Th.loading = true;
            
            Th.$api.get('admin/alliance/flowpools.json', Th.paramete, function(data)
            {
                Th.data = data;
                
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getFlowpools();
        },
        //-------------------------------------列表分页-------------------------------------

        getFlowpool: function(row)
        {
            var Th = this;

            Th.show = true;

            if(row)
            {
                Th.flowpool = row;
            }
            else
            {
                Th.flowpool = {sort: 50, state: '1'}
            }

            Th.loadingItem = false;
        },

        postFlowpool: function(row)
        {
            var Th = this;
            
            Th.loadingItem = true;
            
            if(row.id)
            {
                Th.$api.put('admin/alliance/flowpool/'+row.id+'.json', row, function(data)
                {

                    Th.$emit('message', 'success', '修改成功');

                    Th.show = false;

                    Th.loadingItem = false;

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
            else
            {
                Th.$api.post('admin/alliance/flowpool.json', row, function(data)
                {

                    Th.$emit('message', 'success', '添加成功');

                    Th.show = false;

                    Th.loadingItem = false;

                    Th.getFlowpools();

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
            
        },
    },
}
</script>