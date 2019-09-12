<template>
<div class="content">
    <div class="title-box">
        <h3 class="title">基本资料</h3>
    </div>
    <div class="box" v-loading="loading" style="padding: 24px;">
        <el-form ref="form" :model="data.user" label-width="80px" size="medium" style="max-width: 500px;">
            <el-form-item label="登录账号">
                <el-input v-model="data.user.username"></el-input>
            </el-form-item>
            <el-form-item label="真实姓名">
                <el-input v-model="data.user.nickname"></el-input>
            </el-form-item>
            <el-form-item label="联系手机">
                <el-input v-model="data.user.mobile"></el-input>
            </el-form-item>
            <el-form-item label="电子邮箱">
                <el-input v-model="data.user.email"></el-input>
            </el-form-item>
            <el-form-item label="QQ 号码">
                <el-input v-model="data.user.qq"></el-input>
            </el-form-item>
            <el-form-item>
                <el-button type="success" @click="putUser">确定</el-button>
                <el-button>取消</el-button>
            </el-form-item>
        </el-form>
    </div>

    <div class="title-box">
        <h3 class="title">修改密码</h3>
    </div>
    <div class="box" v-loading="loadingpasswd" style="padding: 24px;">
        <el-form ref="form" :model="data.passwd" label-width="80px" size="medium" style="max-width: 500px;">
            <el-form-item label="旧密码">
                <el-input type="password" v-model="data.passwd.oldpasswd"></el-input>
            </el-form-item>
            <el-form-item label="新密码">
                <el-input type="password" v-model="data.passwd.newpasswd"></el-input>
            </el-form-item>
            <el-form-item label="确认密码">
                <el-input type="password" v-model="data.passwd.newpasswd1"></el-input>
            </el-form-item>

            <el-form-item>
                <el-button type="success" @click="putPasswd">确定</el-button>
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
            loadingpasswd: false,
			data: {
                user: {},
                passwd: {},
            },
		};
	},
	created: function () {
        
        this.group.page = '/advertiser/user';

		this.getUser();
    },
    methods:{
		getUser: function() {
            
            var Th = this;

            Th.loading = true;
            
            Th.$api.get('advertiser/user.json', {}, function(data){
                
                Th.data.user = data.user;
                
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        putUser: function() {
            
            var Th = this;
            
            Th.loading = true;

            Th.$api.put('advertiser/user.json', Th.data.user, function(data)
            {
                Th.loading = false;
                
                Th.$emit('message', 'success', '修改成功');

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        putPasswd: function() {
            
            var Th = this;
            
            Th.loadingpasswd = true;

            Th.$api.put('advertiser/passwd.json', Th.data.passwd, function(data)
            {
                Th.loadingpasswd = false;

                Th.$emit('message', 'success', '修改成功');
                
                Th.data.passwd = {};

            }, function(type, message){ Th.loadingpasswd = false; Th.$emit('message', type, message); });
        },
	},
}
</script>