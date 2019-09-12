<template>   
<div class="content">
    <div class="tip">
        <h3>投放的注意事项（<span>违反以下投放规则的情况将被扣发佣金</span>）</h3>
        <p>1、如果未经同意使用iframe调用广告，我们视为作弊行为处理，将不再结算佣金。</p> 
        <p>2、不得将代码放在空白网页或没有任何实际内容的网页里！</p> 
        <p>3、平台将自动根据您投放的量及质量指数进行系统评级，等级的高低决定您广告佣金收入。</p> 
        <p>4、努力做的最好，杜绝一切作弊行为，发现作弊将扣发佣金永不合作！且行且珍惜！！！</p> 
        <p>5、涉黄站点如有查到一律停止合作！</p> 
    </div>

    <div class="title-box">
        <h3 class="title">我的广告</h3>
        <div class="search-box">
            <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                <el-form-item label="">
                    <el-input v-model="paramete.name" placeholder="名称"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="success" @click="getMyads">查询</el-button>
                </el-form-item>
                <el-form-item>
                    <router-link to="/webmaster/myad">
                        <el-button type="success">创建广告</el-button>
                    </router-link>
                </el-form-item>
            </el-form>
        </div>
    </div>

    <div class="box" v-loading="loading">

        <el-table :data="data.myads" style="width: 100%">
            <el-table-column
                prop="id"
                label="广告id"
                min-width="120">
            </el-table-column>
            
            <el-table-column
                prop="name"
                label="广告名称"
                min-width="150">
            </el-table-column>

            <el-table-column
                prop="position_name"
                label="广告类型"
                min-width="150">
            </el-table-column>

            <el-table-column
                prop="position"
                label="广告位置"
                min-width="150">
                <template slot-scope="scope">
                    <span v-if="scope.row.position_id=='11' && scope.row.position=='1'">顶漂</span>
                    <span v-if="scope.row.position_id=='11' && scope.row.position=='2'">底漂</span>
                    <span v-if="scope.row.position_id=='12'">无</span>
                    <span v-if="scope.row.position_id=='14'">无</span>
                    <span v-if="scope.row.position_id=='13' && scope.row.position=='1'">左漂</span>
                    <span v-if="scope.row.position_id=='13' && scope.row.position=='2'">右漂</span>
                </template>
            </el-table-column>

            <el-table-column
                prop="billing"
                label="计费方式"
                min-width="150">
            </el-table-column>

            <el-table-column
                label="创建"
                prop="created_at"
                min-width="200">
            </el-table-column>

            <el-table-column
                v-bind:router="true"
                fixed="right"
                label="操作"
                width="120">
                <template slot-scope="scope">
                    <el-button type="text" size="small" @click="getCode(scope.row)">获取代码</el-button>
                    <router-link :to="'/webmaster/myad/'+scope.row.id">
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

    <!--获取代码-->
    <el-dialog title="获取代码" :visible.sync="show" width="600px">

        <el-form label-position="left" label-width="80px" :model="code" size="small" class="getcode">
            
            <el-form-item label="广告名称">
                <el-input v-model="code.name" disabled></el-input>
            </el-form-item>

            <el-form-item label="代码长短">
                <el-radio-group v-model="code.length">
                    <el-radio :label="1">短代码</el-radio>
                    <el-radio :label="2" v-if="code.position_id!=12">长代码</el-radio>
                </el-radio-group>
            </el-form-item>

            <el-form-item label="代码类型">
                <el-radio-group v-model="code.type">
                    <el-radio :label="1">Html代码</el-radio>
                    <el-radio :label="2">JS &nbsp;代码</el-radio>
                </el-radio-group>
            </el-form-item>

            <!--横幅，小图标，返回-->
            <div v-if="code.position_id!=12">
                <el-form-item label="广告代码" v-if="code.length==2">
                    <textarea rows="3" cols="20" v-if="code.type==1"><script>;(function() { var script=document.createElement('script'); script.type='text/javascript'; script.charset='UTF-8'; script.async=true; script.src='https://{{group.setting.pg_domain}}/{{code.url}}/{{code.id}}?time='+Math.random(); document.body.appendChild(script); })();</script></textarea>
                    <textarea rows="3" cols="20" v-if="code.type==2">;(function() { var script=document.createElement('script'); script.type='text/javascript'; script.charset='UTF-8'; script.async = true; script.src='https://{{group.setting.pg_domain}}/{{code.url}}/{{code.id}}?time='+Math.random(); document.body.appendChild(script); })();</textarea>
                </el-form-item>

                <!--短代码-->
                <el-form-item label="广告代码" v-if="code.length==1">
                    <textarea rows="3" cols="20" v-if="code.type==1"><script type='text/javascript' charset='UTF-8' async src='https://{{group.setting.pg_domain}}/{{code.url}}/{{code.id}}'></script></textarea>
                    <textarea rows="3" cols="20" v-if="code.type==2">document.writeln("<script type='text/javascript' charset='UTF-8' async src='https:\/\/{{group.setting.pg_domain}}\/{{code.url}}\/{{code.id}}'><\/script>")</textarea>
                </el-form-item>
            </div>

            <!--固定位-->
            <div v-if="code.position_id==12">
                <!--短代码-->
                <el-form-item label="广告代码" v-if="code.length==1">
                    <textarea rows="3" cols="20" v-if="code.type==1"><script type='text/javascript' charset='UTF-8' src='https://{{group.setting.pg_domain}}/{{code.url}}/{{code.id}}'></script></textarea>
                    <textarea rows="3" cols="20" v-if="code.type==2">document.writeln("<script type='text/javascript' charset='UTF-8' src='https:\/\/{{group.setting.pg_domain}}\/{{code.url}}\/{{code.id}}'><\/script>")</textarea>
                </el-form-item>
            </div>
            
        </el-form>
        
        <div slot="footer" class="dialog-footer">
            <el-button @click="show = false">关闭</el-button>
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
            show: false,
            paramete: {
                offset: 0,
                limit: 10,
            },
			data: {},
            myad: {},
            form: {},

            code: {
                type: 1,
                length: 1,
            },
		};
	},
	created: function () {
        
        this.group.page = '/webmaster/myads';

        this.getMyads();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getMyads: function() {
            
            var Th = this;
            
            Th.loading = true;

            Th.$api.get('webmaster/myads.json', Th.paramete, function(data){
                
                Th.data = data;

                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getMyads();
        },
        //-------------------列表分页-------------------------------------


        getCode: function(row)
        {
            var Th = this;

            Th.code.id = row.id;
            Th.code.position_id = row.position_id;
            Th.code.name = row.name;
            Th.code.url = row.url;

            Th.show = true;
        },

	},
}
</script>