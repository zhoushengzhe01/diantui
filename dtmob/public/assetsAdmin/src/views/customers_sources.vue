<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">客户来源</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-input v-model="paramete.name" placeholder="来源名称"></el-input>
                    </el-form-item>

                    <el-form-item>
                        <el-button type="success" @click="getSources">查询</el-button>
                        <el-button type="success" @click="getSource('')">添加</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">

            <el-table :data="data.sources" style="width: 100%">
                <el-table-column
                    prop="id"
                    label="ID"
                    min-width="100">
                </el-table-column>
                
                <el-table-column
                    label="名称"
                    min-width="400">
                    <template slot-scope="scope">
                        <a :href="scope.row.src" target="_blank"><el-button type="text" size="medium">{{scope.row.name}}</el-button></a>
                    </template>
                </el-table-column>

                <el-table-column
                    prop="sort"
                    label="排序"
                    min-width="80">
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
                        <el-button type="text" size="medium" @click="getSource(scope.row)">编辑</el-button>
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

             <el-form :model="website" label-width="80px" size="medium" v-loading="loadingItem">

                <el-form-item label="来源名称">
                    <el-input v-model="sourceItem.name"></el-input>
                </el-form-item>

                <el-form-item label="来源排序">
                    <el-input v-model="sourceItem.sort"></el-input>
                </el-form-item>

                <el-form-item label="使用状态">
                    <el-select v-model="sourceItem.state" placeholder="使用状态" style="width:100%">
                        <el-option label="使用" value="1"></el-option>
                        <el-option label="禁止" value="2"></el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            
            <div slot="footer" class="dialog-footer">
                <el-button @click="show = false">取 消</el-button>
                <el-button type="success" @click="postSource">确 定</el-button>
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
            sourceItem: {},
            paramete: {
                offset: 0,
                limit: 15,
            },
            data: {},
        };
    },
    created: function () {
        
        this.group.page = '/admin/customers/sources';

        this.getSources();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getSources: function()
        {
            var Th = this;
            
            Th.loading = true;
            
            Th.$api.get('admin/customer/sources.json', Th.paramete, function(data)
            {
                Th.data = data;
                
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getSources();
        },
        //-------------------------------------列表分页-------------------------------------

        getSource: function(row)
        {
            var Th = this;

            Th.show = true;

            if(row.id)
            {
                Th.sourceItem = row;
            }
            else
            {
                Th.sourceItem = {};
            }

            Th.loadingItem = false;
        },

        postSource: function()
        {
            var Th = this;

            Th.loadingItem = true;

            if(Th.sourceItem.id)
            {
                Th.$api.put('admin/customer/source/'+Th.sourceItem.id+'.json', Th.sourceItem, function(data)
                {
                    Th.loadingItem = false;

                    Th.show = false;

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
            else
            {
                Th.$api.post('admin/customer/source.json', Th.sourceItem, function(data)
                {
                    Th.loadingItem = false;

                    Th.show = false;

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
        },
    },
}
</script>