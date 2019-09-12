<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">系统设置</h3>
        </div>

        <div class="box" v-loading="loading" style="padding: 24px;">

            <el-form :model="data.user" label-width="80px" size="medium" style="max-width: 580px;">

                <el-form-item label="登陆用户">
                    <el-input v-model="data.user.username"></el-input>
                </el-form-item>

                <el-form-item label="所属联盟">
                    <el-select v-model="data.user.alliance_agent_id" placeholder="部门" style="width:100%">
                        <el-option v-for="item in group.alliance_agents" :key="item.key" :label="item.name" :value="item.id"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item label="所属部门">
                    <el-select v-model="data.user.department_id" placeholder="部门" style="width:100%">
                        <el-option v-for="item in group.departments" :key="item.key" :label="item.name" :value="item.id"></el-option>
                    </el-select>
                </el-form-item>
                
                <el-form-item label="登陆密码">
                    <el-input v-model="data.user.password" placeholder="不填写则为不修改"></el-input>
                </el-form-item>

                <el-form-item label="真实姓名">
                    <el-input v-model="data.user.nickname"></el-input>
                </el-form-item>

                <el-form-item label="对外艺名">
                    <el-input v-model="data.user.stagename"></el-input>
                </el-form-item>

                <el-form-item label="电话号码">
                    <el-input v-model="data.user.mobile"></el-input>
                </el-form-item>

                <el-form-item label="QQ 号码">
                    <el-input v-model="data.user.qq"></el-input>
                </el-form-item>

                <el-form-item label="状态">
                    <el-select v-model="data.user.state" placeholder="请选择状态" style="width:100%">
                        <el-option label="使用" value="1"></el-option>
                        <el-option label="停止" value="2"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item>
                    <el-button type="success" @click="putUser">确定</el-button>
                    <el-button>取消</el-button>
                </el-form-item>
            </el-form>
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
            id: this.$route.params.id,
            data: {
                user: {}
            },
        };
    },
    created: function () {
        
        this.group.page = window.location.pathname;

        this.getUser();
    },
    methods:{
        getUser: function()
        {
            var Th = this;
            Th.loading = true;
            Th.$api.get('admin/user.json', {}, function(data)
            {
                Th.data = data;
                
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },

        putUser: function()
        {
            var Th = this;
            
            Th.loading = true;
            
            Th.$api.put('admin/user.json', Th.data.user, function(data)
            {
                Th.loading = false;

                Th.$emit('message', 'success', '修改成功');

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
    },
}
</script>