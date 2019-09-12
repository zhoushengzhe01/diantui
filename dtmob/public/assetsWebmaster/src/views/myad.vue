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
        <h3 class="title">创建编辑</h3>
    </div>

    <div class="box" v-loading="loading" style="padding: 24px;">
        <el-form ref="form" :model="data.package" label-width="80px" size="medium" style="max-width: 500px;">
            <el-form-item label="广告名称">
                <el-input v-model="data.myad.name"></el-input>
            </el-form-item>
            
            <el-form-item label="广告类型">
                <el-radio-group v-model="data.myad.position_id">
                    <el-radio
                        v-for="item in adtype"
                        :key="item.key"
                        :label="item.id"
                    >{{item.name}}</el-radio>
                </el-radio-group>
            </el-form-item>

            <el-form-item label="广告位置" v-if="data.myad.position_id=='11' || data.myad.position_id=='13'">
                <el-radio-group v-model="data.myad.position" v-if="data.myad.position_id=='11'">
                    <el-radio label="2">底漂</el-radio>
                    <el-radio label="1">顶漂</el-radio>
                </el-radio-group>
                
                <el-radio-group v-model="data.myad.position" v-if="data.myad.position_id=='13'">
                    <el-radio label="2">右漂</el-radio>
                    <el-radio label="1">左漂</el-radio>
                </el-radio-group>
            </el-form-item>

            <el-form-item label="计费方式">
                <el-radio-group v-model="data.myad.billing">
                    <el-radio label="CPC">CPC</el-radio>
                    <!-- <el-radio label="CPM">CPV</el-radio> -->
                </el-radio-group>
            </el-form-item>

            <el-form-item>
                <el-button type="success" @click="postMyad">确定</el-button>
                <el-button>取消</el-button>
            </el-form-item>
        </el-form>
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
            id: this.$route.params.id,
            adtype: {},
            data: {
                myad: {
                    name: '',
                    position_id: 11,
                    position: '2',
                    billing: 'CPC',
                },
            },
		};
    },
	created: function () {
        this.group.page = '/webmaster/myads';
        this.getAdtype();
    },
    methods:{
        getAdtype: function() {
            var Th = this;
            Th.loading = true;
            Th.$api.get('webmaster/adtype.json', [], function(data){
                
                Th.adtype = data.adtype;

                //编辑获取
                if(Th.id){
                    Th.getMyad();
                }else{
                    Th.loading = false;
                }

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        getMyad: function()
        {
            var Th = this;
            Th.loading = true;
            Th.$api.get('webmaster/myad/'+Th.id+'.json' ,[] , function(data){

                Th.data = data;
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        postMyad: function()
        {
            var Th = this;
            
            Th.loading = true;

            if(this.id)
            {
                Th.$api.put('webmaster/myad/'+Th.id+'.json', Th.data.myad, function(data){

                    Th.$emit('message', 'success', '修改成功');

                    Th.$router.push({path:'/webmaster/myads'});

                    Th.loading = false;

                }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
            }
            else
            {
                Th.$api.post('webmaster/myad.json', Th.data.myad, function(data){

                    Th.$emit('message', 'success', '添加成功');

                    Th.$router.push({path:'/webmaster/myads'});

                    Th.loading = false;

                }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
                
            }
        },
	},
}
</script>