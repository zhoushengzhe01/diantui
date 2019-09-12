<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">广告素材</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-input v-model="paramete.advertiser_id" placeholder="站长ID"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-input v-model="paramete.id" placeholder="素材ID"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-select v-model="paramete.adstype_id" placeholder="广告类型">
                            <el-option v-for="item in group.adtype" :key="item.key" :label="item.name" :value="item.id"></el-option>
                        </el-select> 
                    </el-form-item>
                    <el-form-item>
                        <el-button type="success" @click="getPackages">查询</el-button>
                        <router-link :to="'/admin/advertiser/package'"><el-button type="success">添加素材</el-button></router-link>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">

            <el-table :data="data.packages" style="width: 100%">
                <el-table-column
                    prop="advertiser_id"
                    label="广告主ID"
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
                    min-width="150">
                </el-table-column>

                <el-table-column
                    label="所属类型"
                    min-width="100">
                    <template slot-scope="scope">
                        <span v-for="type in group.adtype" :key="type.key" v-if="type.id==scope.row.adstype_id">{{type.name}}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="图片一"
                    min-width="150">
                    <template slot-scope="scope">
                        <el-popover
                            placement="left"
                            width="200"
                            trigger="hover">
                            <img v-for="pic in scope.row.picture1" :key="pic.key" :src="'//'+group.setting.matter_domain+'/'+pic.path" style="max-height:58px; max-width:174px"/>
                            <el-button type="text" size="medium" slot="reference">{{scope.row.picture1.length}}张图片 点击查看</el-button>
                        </el-popover>
                    </template>
                </el-table-column>

                <el-table-column
                    label="图片二"
                    min-width="150">
                    <template slot-scope="scope">
                        <el-popover
                            placement="left"
                            width="200"
                            trigger="hover">
                            <img v-for="pic in scope.row.picture2" :key="pic.key" :src="'//'+group.setting.matter_domain+'/'+pic.path" style="max-height:58px; max-width:174px"/>
                            <el-button type="text" size="medium" slot="reference">{{scope.row.picture2.length}}张图片 点击查看</el-button>
                        </el-popover>
                    </template>
                </el-table-column>

                <el-table-column
                    label="图片三"
                    min-width="150">
                    <template slot-scope="scope">
                        <el-popover
                            placement="left"
                            width="200"
                            trigger="hover">
                            <img v-for="pic in scope.row.picture3" :key="pic.key" :src="'//'+group.setting.matter_domain+'/'+pic.path" style="max-height:58px; max-width:174px"/>
                            <el-button type="text" size="medium" slot="reference">{{scope.row.picture3.length}}张图片 点击查看</el-button>
                        </el-popover>
                    </template>
                </el-table-column>

                <el-table-column
                    label="状态"
                    min-width="100">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.state=='1'" type="success" size="small">正常</el-tag>
                        <el-tag v-if="scope.row.state=='2'" type="info" size="small">禁止</el-tag>
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
                    width="60">
                    <template slot-scope="scope">
                        <router-link :to="'/admin/advertiser/package/'+scope.row.id">
                        <el-button type="text" size="medium">编辑</el-button>
                        </router-link>
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

    </div>
</template>
<script>
export default {
    name: 'package',
    data: function () { 
        return {
            group: Group,
            loading: true,
            paramete: {
                offset: 0,
                limit: 15,
                id: this.$api.getQueryString('id'),
            },
            data: {},
        };
    },
    created: function () {
        
        this.group.page = window.location.pathname;

        this.getPackages();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getPackages: function()
        {
            var Th = this;
            Th.loading = true;
            Th.$api.get('admin/advertiser/packages.json', Th.paramete, function(data)
            {
                Th.data = data;

                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getPackages();
        },
        //-------------------------------------列表分页-------------------------------------

    },
}
</script>