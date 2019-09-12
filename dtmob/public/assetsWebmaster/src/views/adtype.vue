<template>   
<div class="content">
    <div class="title-box">
        <h3 class="title">推荐广告</h3>
    </div>

    <div class="box" v-loading="loading">
        <el-table :data="data.adtype" style="width: 100%">
            <el-table-column
                prop="name"
                label="广告类型"
                min-width="150">
            </el-table-column>
            
            <el-table-column
                prop="type"
                label="计费类型"
                min-width="150">
            </el-table-column>

            <el-table-column
                label="计费单位"
                min-width="150">
                <template slot-scope="scope">
                    {{scope.row.units}}/次
                </template>
            </el-table-column>

            <el-table-column
                v-bind:router="true"
                fixed="right"
                label="操作"
                width="100">
                <template slot-scope="scope">
                    <el-button type="text" size="small" v-if="scope.row.state=='1'" @click="$router.push({path:'/webmaster/myad'})" >创建广告位</el-button>
                    <el-button type="text" size="small" v-if="scope.row.state=='0'" disabled >创建广告位</el-button>
                </template>
            </el-table-column>
        </el-table>
        <br/>
        <br/>
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
        this.group.page = '/webmaster/adtype';
        this.getAdtype();
    },
    methods:{
        getAdtype: function() {
            var Th = this;
            Th.loading = true;
            Th.$api.get('webmaster/adtype.json', [], function(data){
                Th.data = data;
                Th.loading = false;
            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
	},
}
</script>