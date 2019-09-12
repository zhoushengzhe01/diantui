<template>   
<div class="content">
    <div class="title-box">
        <h3 class="title">我的网站</h3>
        <div class="search-box">
            <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                <el-form-item label="">
                    <el-input v-model="paramete.domain" placeholder="输入域名"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="success" @click="getWebsites">查询</el-button>
                </el-form-item>
                <el-form-item>
                    <el-button type="success" @click="getWebsite('')">添加网站</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>

    <div class="box" v-loading="loading">
        <el-table :data="data.websites" style="width: 100%">
            <el-table-column
                prop="name"
                label="名称"
                min-width="120">
            </el-table-column>

            <el-table-column
                prop="domain"
                label="域名"
                min-width="120">
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
                min-width="200">
            </el-table-column>

            <el-table-column
                label="状态"
                min-width="120">
                <template slot-scope="scope">
                    <el-tag v-if="scope.row.state=='0'" type="warning" size="mini">等待审核</el-tag>
                    <el-tag v-if="scope.row.state=='1'" type="success" size="mini">审核通过</el-tag>
                    <el-tag v-if="scope.row.state=='2'" type="danger" size="mini">审核拒绝</el-tag>
                </template>
            </el-table-column>

            <el-table-column
                label="时间"
                prop="created_at"
                min-width="160">
            </el-table-column>

            <el-table-column
                v-bind:router="true"
                fixed="right"
                label="操作"
                width="100">
                <template slot-scope="scope">
                    <el-button type="text" size="small" @click="getWebsite(scope.row)">编辑</el-button>
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

    <el-dialog title="添加/编辑" :visible.sync="show" width="400px">
        <el-form ref="form" :model="website" label-width="80px" size="small" v-loading="itemloading">
            <el-form-item label="网站名称">
                <el-input v-model="website.name"></el-input>
            </el-form-item>
            <el-form-item label="网站域名">
                <el-input v-model="website.domain"></el-input>
            </el-form-item>
            <el-form-item label="网站备案">
                <el-input v-model="website.icp"></el-input>
            </el-form-item>
            <el-form-item label="所属分类">
                <el-select v-model="website.category_id" placeholder="请选择网站所属分类" style="width:100%;">
                    <el-option v-for="item in group.categorys" :key="item.key" :label="item.name" :value="item.id"></el-option>
                </el-select>
            </el-form-item>
        </el-form>

        <div slot="footer" class="dialog-footer">
            <el-button @click="show = false">取 消</el-button>
            <el-button type="success" @click="postWebsite(website.id)">确 定</el-button>
        </div>
    </el-dialog>

</div>
</template>

<script>
export default {
	name: 'user',
    data: function () {	
		return {
            group: Group,
            loading: true,
            itemloading: false,
            show: false,
            paramete: {
                offset: 0,
                limit: 10,
            },
			data: {},
            website: {},
		};
	},
	created: function () {
        
        this.group.page = '/webmaster/websites';

        this.getWebsites();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getWebsites: function() {
            
            var Th = this;
            
            Th.loading = true;

            Th.$api.get('webmaster/websites.json', Th.paramete, function(data){
                
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

            if(row)
            {
                Th.website = row;
            }
            else
            {
                Th.website = {};
            }

            Th.show = true;
        },


        postWebsite: function(id)
        {
            var Th = this;
            if(id)
            {
                Th.$api.put('webmaster/website/'+id+'.json', Th.website, function(data){
                    
                    Th.$emit('message', 'success', '修改成功');

                    Th.website = {};

                    Th.getWebsites();

                    Th.show = false;

                }, function(type, message){ Th.$emit('message', type, message); });
            }
            else
            {
                Th.$api.post('webmaster/website.json', Th.website, function(data){
                    
                    Th.$emit('message', 'success', '添加成功');

                    Th.website = {};

                    Th.getWebsites();

                    Th.show = false;

                }, function(type, message){ Th.$emit('message', type, message); });
            }
        },

	},
}
</script>