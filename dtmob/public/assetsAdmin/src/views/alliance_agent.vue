<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">联盟代理</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-input v-model="paramete.name" placeholder="名称"></el-input>
                    </el-form-item>

                    <el-form-item>
                        <el-input v-model="paramete.domain" placeholder="域名"></el-input>
                    </el-form-item>
                    
                    <el-form-item>
                        <el-button type="success" @click="getAllianceAgents">查询</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">
            <el-table :data="data.alliance_agents" style="width: 100%">
                
                <el-table-column
                    prop="name"
                    label="名称">
                </el-table-column>
                
                <el-table-column
                    prop="domain"
                    label="域名">
                </el-table-column>

                <el-table-column
                    prop="seo_title"
                    label="SEO标题">
                </el-table-column>

                <el-table-column
                    prop="pg_domain"
                    label="展示域名">
                </el-table-column>

                <el-table-column
                    prop="pc_domain"
                    label="点击域名">
                </el-table-column>

                <el-table-column
                    prop="pv_domain"
                    label="统计域名">
                </el-table-column>

                <el-table-column
                    prop="image_domain"
                    label="图片域名">
                </el-table-column>

                <el-table-column
                    fixed="right"
                    label="操作"
                    width="100">
                    <template slot-scope="scope">
                        <el-button type="text" size="medium" @click="getAllianceAgent(scope.row)">编辑</el-button>
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
        <el-dialog title="编辑联盟代理" :visible.sync="show" class="small_dialog">
            
            <el-form :model="alliance_agent" label-width="90px" size="medium" v-loading="loadingItem">

                <el-form-item label="名称">
                    <el-input v-model="alliance_agent.name"></el-input>
                </el-form-item>

                <el-form-item label="域名">
                    <el-input v-model="alliance_agent.domain"></el-input>
                </el-form-item>

                <el-form-item label="密钥">
                    <el-input v-model="alliance_agent.key"></el-input>
                </el-form-item>

                <el-form-item label="展示域名">
                    <el-input v-model="alliance_agent.pg_domain"></el-input>
                </el-form-item>

                <el-form-item label="点击域名">
                    <el-input v-model="alliance_agent.pc_domain"></el-input>
                </el-form-item>

                <el-form-item label="统计域名">
                    <el-input v-model="alliance_agent.pv_domain"></el-input>
                </el-form-item>

                <el-form-item label="图片域名">
                    <el-input v-model="alliance_agent.image_domain"></el-input>
                </el-form-item>

                <el-form-item label="SEO标题">
                    <el-input v-model="alliance_agent.seo_title"></el-input>
                </el-form-item>

                <el-form-item label="SEO关键词">
                    <el-input v-model="alliance_agent.seo_words"></el-input>
                </el-form-item>

                <el-form-item label="SEO描述">
                    <el-input v-model="alliance_agent.seo_description"></el-input>
                </el-form-item>

            </el-form>

            <div slot="footer" class="dialog-footer">
                <el-button @click="show = false">取 消</el-button>
                <el-button type="success" @click="putAllianceAgent">确 定</el-button>
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

            alliance_agent: {},

            paramete: {
                offset: 0,
                limit: 15,
            },
            data: {},
        };
    },
    created: function () {

        this.group.page = window.location.pathname;
        this.getAllianceAgents();
    },
    methods:{

        getAllianceAgents: function()
        {
            var Th = this;
            Th.loading = true;

            Th.$api.get('admin/alliance/agents.json', Th.paramete, function(data)
            {
                Th.data = data;
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {

            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getAllianceAgents();
        },
        
        getAllianceAgent: function(row)
        {
            var Th = this;
            Th.loadingItem = true;
            Th.show = true;

            Th.alliance_agent = row;
            Th.loadingItem = false;
        },

        putAllianceAgent: function()
        {
            var Th = this;
            Th.loadingItem = true;

            Th.$api.put('admin/alliance/agent/'+Th.alliance_agent.id+'.json', Th.alliance_agent, function(data)
            {
                Th.loadingItem = false;
                Th.show = false;
                Th.getAllianceAgents();

            }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
        }
    },
}
</script>