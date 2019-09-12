<template>   
<div class="content">
    <div class="title-box">
        <h3 class="title">我的素材</h3>
        <div class="search-box">
            <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                <el-form-item label="">
                    <el-input v-model="paramete.name" placeholder="名称"></el-input>
                </el-form-item>
                <el-form-item label="">
                    <el-select v-model="paramete.adsType" placeholder="类型">
                        <el-option
                            v-for="item in group.adtype"
                            :key="item.key"
                            :label="item.name"
                            :value="item.id">
                        </el-option>
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="success" @click="getPackages">查询</el-button>
                </el-form-item>
                <el-form-item>
                    <router-link to="/advertiser/package">
                        <el-button type="success"> + 添加素材</el-button>
                    </router-link>
                </el-form-item>
            </el-form>
        </div>
    </div>

    <div class="box" v-loading="loading">
        <el-table :data="data.packages" style="width: 100%">
            <el-table-column
                prop="name"
                label="名称"
                min-width="120">
            </el-table-column>
            <el-table-column
                label="素材一"
                min-width="160">
                <template slot-scope="scope">
                    <el-popover
                      placement="right"
                      width="200"
                      trigger="hover"
                      v-if="scope.row.advertiser_id!=1000">
                      <img v-for="pic in scope.row.picture1" :key="pic.key" :src="'//'+group.setting.matter_domain+'/'+pic.path" style="width:100%"/>
                      
                      <el-button type="text" size="small" slot="reference">{{scope.row.picture1.length}}张图片 点击查看</el-button>
                    </el-popover>
                    <span v-if="scope.row.advertiser_id==1000">系统素材不能查看</span>
                </template>
            </el-table-column>
            <el-table-column
                label="素材二"
                min-width="160">
                <template slot-scope="scope">
                    <el-popover
                      placement="right"
                      width="200"
                      trigger="hover"
                      v-if="scope.row.advertiser_id!=1000">
                      <img v-for="pic in scope.row.picture2" :key="pic.key" :src="'//'+group.setting.matter_domain+'/'+pic.path" style="width:100%"/>

                      <el-button type="text" size="small" slot="reference">{{scope.row.picture2.length}}张图片 点击查看</el-button>
                    </el-popover>
                    <span v-if="scope.row.advertiser_id==1000">系统素材不能查看</span>
                </template>
            </el-table-column>
            <el-table-column
                label="素材三"
                min-width="160">
                <template slot-scope="scope">
                    <el-popover
                      placement="right"
                      width="200"
                      trigger="hover"
                      v-if="scope.row.advertiser_id!=1000">
                      <img v-for="pic in scope.row.picture3" :key="pic.key" :src="'//'+group.setting.matter_domain+'/'+pic.path" style="width:100%"/>

                      <el-button type="text" size="small" slot="reference">{{scope.row.picture3.length}}张图片 点击查看</el-button>
                    </el-popover>
                    <span v-if="scope.row.advertiser_id==1000">系统素材不能查看</span>
                </template>
            </el-table-column>
            <el-table-column
                prop="adstype_id"
                label="类型"
                min-width="80">
                <template slot-scope="scope">
                    <span v-for="type in group.adtype" :key="type.key" v-if="type.id==scope.row.adstype_id">{{type.name}}</span>
                </template>
            </el-table-column>
            <el-table-column
                label="状态"
                min-width="80">
                <template slot-scope="scope">
                    <el-tag v-if="scope.row.state=='1'" type="success" size="small">正常</el-tag>
                    <el-tag v-if="scope.row.state=='2'" type="info" size="small">关闭</el-tag>
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
                width="60">
                <template slot-scope="scope">
                    <router-link :to="'/advertiser/package/'+scope.row.id" v-if="scope.row.advertiser_id!=1000">
                        <el-button type="text" size="small">编辑</el-button>
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
	name: 'user',
    data: function () {	
		return {
            group: Group,
            loading: true,
            paramete: {
                offset: 0,
                limit: 10,
            },
			data: {},  
		};
	},
	created: function () {
        
        this.group.page = '/advertiser/packages';

        this.getPackages();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getPackages: function() {
            
            var Th = this;
            
            Th.loading = true;

            Th.$api.get('advertiser/packages.json', Th.paramete, function(data){
                
                Th.data = data;
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getPackages();
        },
        //-------------------------------------列表分页-------------------------------------



        putPackage: function(id, state)
        {
            var Th = this;
            Th.loading = true;
            Th.$api.put('advertiser/package/item/'+id+'.json', {'state': state}, function(data){

                Th.$emit('message', 'success', '修改成功');

                Th.loading = false;
            
            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
	},
}
</script>