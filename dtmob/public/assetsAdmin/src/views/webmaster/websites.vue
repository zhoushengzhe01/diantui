<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">网站管理</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-input v-model="paramete.webmaster_id" placeholder="站长ID"></el-input>
                    </el-form-item>

                    <el-form-item>
                        <el-input v-model="paramete.domain" placeholder="域名"></el-input>
                    </el-form-item>
                    
                    <el-form-item>
                        <el-button type="success" @click="getWebsites">查询</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">

            <el-table :data="data.websites" style="width: 100%">
                <el-table-column
                    prop="webmaster_id"
                    label="站长ID"
                    min-width="80">
                </el-table-column>

                <el-table-column
                    label="联盟"
                    min-width="100">
                    <template slot-scope="scope">
                        <span v-for="item in group.alliance_agents" :key="item.key" v-if="item.id==scope.row.alliance_agent_id">{{item.name}}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    prop="name"
                    label="名称"
                    min-width="120">
                </el-table-column>

                <el-table-column
                    prop="domain"
                    label="域名"
                    min-width="180">
                </el-table-column>

                <el-table-column
                    label="类型"
                    min-width="120">
                    <template slot-scope="scope">
                        <span v-for="item in group.categorys" :key="item.key" v-if="item.id==scope.row.category_id">{{item.name}}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    prop="icp"
                    label="备案号"
                    min-width="180">
                </el-table-column>

                <el-table-column
                    label="状态"
                    min-width="150">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.state=='0'" type="danger" size="small">待审</el-tag>
                        <el-tag v-if="scope.row.state=='1'" type="success" size="small">通过</el-tag>
                        <el-tag v-if="scope.row.state=='2'" type="info" size="small">拒绝</el-tag>
                        <a :href="'https://www.aizhan.com/cha/'+scope.row.domain" target="_blank"><img :src="'https://baidurank.aizhan.com/api/mbr?domain='+scope.row.domain+'&style=images'" /></a>
                    </template>
                </el-table-column>

                <el-table-column
                    prop="created_at"
                    label="时间"
                    min-width="180">
                </el-table-column>

                <el-table-column
                    v-bind:router="true"
                    fixed="right"
                    label="操作"
                    width="100">
                    <template slot-scope="scope">
                        <el-button type="text" size="medium" @click="getWebsite(scope.row)">编辑</el-button>
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
        <el-dialog title="编辑网站" :visible.sync="show" class="small_dialog">

             <el-form :model="website" label-width="80px" size="medium" v-loading="loadingItem">

                <el-form-item label="网站名称">
                    <el-input v-model="website.name"></el-input>
                </el-form-item>

                <el-form-item label="网站域名">
                    <el-input v-model="website.domain"><template slot="prepend">http://</template></el-input>
                </el-form-item>

                <el-form-item label="ICP 备案">
                    <el-input v-model="website.icp"></el-input>
                </el-form-item>

                <el-form-item label="网站分类">
                    <el-select v-model="website.category_id" placeholder="请选择活动区域" style="width:100%">
                        <el-option v-for="item in group.categorys" :key="item.key" :label="item.name" :value="item.id"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="审核状态">
                    <el-select v-model="website.state" placeholder="请选择活动区域" style="width:100%">
                        <el-option label="等待审核" value="0"></el-option>
                        <el-option label="审核通过" value="1"></el-option>
                        <el-option label="审核拒绝" value="2"></el-option>
                    </el-select>
                </el-form-item>
            </el-form>
            
            <div slot="footer" class="dialog-footer">
                <el-button @click="show = false">取 消</el-button>
                <el-button type="success" @click="putWebsite(website.id)">确 定</el-button>
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
            website: {},
            paramete: {
                offset: 0,
                limit: 15,
                webmaster_id: this.$api.getQueryString('webmaster_id'),
            },
            data: {},
        };
    },
    created: function () {
        
        this.group.page = '/admin/webmaster/websites';
        this.getWebsites();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getWebsites: function()
        {
            var Th = this;
            
            Th.loading = true;
            
            Th.$api.get('admin/webmaster/websites.json', Th.paramete, function(data)
            {
                Th.data = data;
                
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getWebsites();
        },
        //-------------------------------------列表分页-------------------------------------

        getWebsite: function(row)
        {
            var Th = this;

            Th.show = true;

            Th.loadingItem = false;

            Th.website = row;
        },

        putWebsite: function(id)
        {
            var Th = this;
            
            Th.loadingItem = true;
            
            Th.$api.put('admin/webmaster/website/'+id+'.json', Th.website, function(data)
            {

                Th.$emit('message', 'success', '修改成功');

                Th.show = false;

                Th.loadingItem = false;

            }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
        },
    },
}
</script>