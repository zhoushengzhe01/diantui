<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">添加素材</h3>
        </div>

        <div class="box" v-loading="loading" style="padding: 24px;">
            <el-form ref="form" :model="data.advertiser" label-width="80px" size="medium" style="max-width: 680px;">
                <el-form-item label="所属联盟">
                    <el-select v-model="data.advertiser.alliance_agent_id" placeholder="联盟" style="width:100%">
                        <el-option v-for="item in group.alliance_agents" :key="item.key" :label="item.name" :value="item.id"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="用户名">
                    <el-input v-model="data.advertiser.username" disabled></el-input>
                </el-form-item>
                <el-form-item label="公司名">
                    <el-input v-model="data.advertiser.company"></el-input>
                </el-form-item>
                <el-row>
                    <el-col :span="12">
                        <el-form-item label="代理ID">
                            <el-input v-model="data.advertiser.agent_id"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="代理名称">
                            <el-input v-model="data.advertiser.agent_name" disabled></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
            
                <el-form-item label="联系人">
                    <el-input v-model="data.advertiser.nickname"></el-input>
                </el-form-item>
                <el-row>
                    <el-col :span="12">
                        <el-form-item label="账户余额">
                            <el-input v-model="data.advertiser.money" disabled><template slot="append">元</template></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="登录密码">
                            <el-input type="password" v-model="data.advertiser.password" placeholder="不填写则不修改"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-form-item label="电子邮箱">
                    <el-input v-model="data.advertiser.email"></el-input>
                </el-form-item>
                <el-form-item label="手机号码">
                    <el-input v-model="data.advertiser.mobile"></el-input>
                </el-form-item>
                <el-form-item label="QQ 号码">
                    <el-input v-model="data.advertiser.qq"></el-input>
                </el-form-item>
                <el-row>
                    <el-col :span="12">
                        <el-form-item label="投放协议">
                            <el-select v-model="data.advertiser.is_signa" disabled>
                                <el-option label="已签" value="1"></el-option>
                                <el-option label="未签" value="0"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="对接商务">
                            <el-select v-model="data.advertiser.busine_id" placeholder="选择所属客服">
                                <el-option v-for="item in users" :key="item.key" :label="item.nickname" :value="item.id"></el-option>
                            </el-select> 
                        </el-form-item>
                    </el-col>
                </el-row>
                <el-form-item label="签署公司" v-if="data.advertiser.is_signa=='1'">
                    <el-input v-model="data.advertiser.signa_company" disabled></el-input>
                </el-form-item>
                <el-form-item label="签署人" v-if="data.advertiser.is_signa=='1'">
                    <el-input v-model="data.advertiser.signa_name" disabled></el-input>
                </el-form-item>
                <el-form-item label="签署时间" v-if="data.advertiser.is_signa=='1'">
                    <el-input v-model="data.advertiser.signa_time" disabled></el-input>
                </el-form-item>
                <el-form-item label="协议编号" v-if="data.advertiser.is_signa=='1'">
                    <el-input v-model="data.advertiser.signa_number" disabled></el-input>
                </el-form-item>
                <el-form-item label="状态">
                    <el-select v-model="data.advertiser.state" placeholder="请选择状态">
                        <el-option label="使用" value="1"></el-option>
                        <el-option label="停止" value="2"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-button type="success" @click="putAdvertiser">确定</el-button>
                    <el-button>取消</el-button>
                </el-form-item>
            </el-form>
        </div>


    </div>
</template>
<script>
export default {
    name: 'advertiser',
    data: function () { 
        return {
            group: Group,
            loading: true,
            id: this.$route.params.id,
            users: {},
            data: {
                advertiser: {}
            },
        };
    },
    created: function () {
        
        this.group.page = '/admin/advertisers';

        this.getAdvertiser();
        this.getUsers();
    },
    methods:{
        getAdvertiser: function()
        {
            var Th = this;
            Th.loading = true;
            Th.$api.get('admin/advertiser/'+Th.id+'.json', {}, function(data)
            {
                Th.data = data;
                
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },

        getUsers: function()
        {
            var Th = this;
            Th.loading = true;
            Th.$api.get('admin/users.json', {department_id: 4}, function(data)
            {
                Th.users = data.users;
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },

        putAdvertiser: function()
        {
            var Th = this;
            
            Th.loading = true;
            
            Th.$api.put('admin/advertiser/'+Th.id+'.json', Th.data.advertiser, function(data)
            {
                Th.loading = false;

                Th.$emit('message', 'success', '修改成功');
                Th.$router.push({path:'/admin/advertisers'});

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
    },
}
</script>