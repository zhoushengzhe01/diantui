<template>
<div class="content">
    <div class="title-box">
        <h3 class="title">添加素材</h3>
    </div>

    <div class="box" v-loading="loading" style="padding: 24px;">
        <el-form ref="form" :model="data.alliance" label-width="80px" size="medium" style="max-width: 580px;">

            <el-form-item label="所属联盟">
                <el-select v-model="data.alliance.alliance_agent_id" placeholder="联盟" style="width:100%">
                    <el-option v-for="item in group.alliance_agents" :key="item.key" :label="item.name" :value="item.id"></el-option>
                </el-select>
            </el-form-item>
            
            <el-form-item label="联盟名字">
                <el-input v-model="data.alliance.name"></el-input>
            </el-form-item>

            <el-form-item label="联盟地址">
                <el-input v-model="data.alliance.login_url"></el-input>
            </el-form-item>

            <el-form-item label="联盟账号">
                <el-input v-model="data.alliance.account"></el-input>
            </el-form-item>

            <el-form-item label="联盟密码">
                <el-input v-model="data.alliance.password"></el-input>
            </el-form-item>

            <el-form-item label="计费次数">
                <el-input v-model="data.alliance.record_num"><template slot="append">次</template></el-input>
            </el-form-item>

            <el-form-item label="点击价格">
                <el-input v-model="data.alliance.price"><template slot="append">元</template></el-input>
            </el-form-item>

            <el-form-item label="展示价格">
                <el-input v-model="data.alliance.price_cpm"><template slot="append">元</template></el-input>
            </el-form-item>

            <el-form-item label="广告类型">
                <el-radio-group v-model="data.alliance.position_id">
                    <el-radio v-for="item in group.adtype" :key="item.key" :label="item.id">{{item.name}}</el-radio>                        
                </el-radio-group>
            </el-form-item>

            <el-form-item label="广告位置">
                <el-radio-group v-model="data.alliance.position">
                    <el-radio label="1">顶部</el-radio>
                    <el-radio label="2">底部</el-radio>                   
                </el-radio-group>
            </el-form-item>

            <el-form-item label="广告代码">
                <el-input type="textarea" rows="5" v-model="data.alliance.code"></el-input>
            </el-form-item>

            <el-form-item label="统计代码">
                <el-input type="textarea" rows="5" v-model="data.alliance.stat_code"></el-input>
            </el-form-item>

            <el-form-item label="状态">
                <el-switch
                    active-value="1"
                    inactive-value="2"
                    active-color="#13ce66"
                    inactive-color="#ff4949"
                    v-model="data.alliance.state">
                </el-switch>
            </el-form-item>

            <el-form-item>
                <el-button type="success" @click="putAlliance">确定</el-button>
                <el-button>取消</el-button>
            </el-form-item>
        </el-form>
    </div>
</div>
</template>
<script>
export default {
    name: 'alliance',
    data: function () { 
        return {
            group: Group,
            loading: true,
            id: this.$route.params.id,
            users: {},
            data: {
                alliance: {}
            },
        };
    },
    created: function () {
        
        this.group.page = '/admin/alliances';
        
        this.getAlliance();
    },
    methods:{
        getAlliance: function()
        {
            var Th = this;
            Th.loading = true;
            Th.$api.get('admin/alliance/'+Th.id+'.json', {}, function(data)
            {
                Th.data = data;
                
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },

        putAlliance: function()
        {
            var Th = this;
            
            Th.loading = true;
            
            Th.$api.put('admin/alliance/'+Th.id+'.json', Th.data.alliance, function(data)
            {
                Th.loading = false;

                Th.$emit('message', 'success', '修改成功');

                Th.$router.push({path:'/admin/alliances'});

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
    },
}
</script>