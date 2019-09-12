<template>
<div class="content">
    <div class="title-box">
        <h3 class="title">基本资料</h3>
        <div class="search-box">
            <el-form :inline="true" class="demo-form-inline" size="mini">
                <el-form-item>
                    <el-button type="success" @click="show = true">修改密码</el-button>
                </el-form-item>
            </el-form>
        </div>
    </div>
    <div class="box" v-loading="userLoading" style="padding: 24px;">
        <el-form ref="form" :model="data.user" label-width="80px" size="medium" style="max-width: 500px;">
            <el-form-item label="登录账号">
                <el-input v-model="data.user.username" :disabled="true"></el-input>
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
        <h3 class="title">收款账户</h3>
    </div>
    <div class="box" v-loading="bankLoading" style="padding: 24px;">
        
        <el-form ref="form" :model="data.bank" label-width="80px" size="medium" style="max-width: 500px;">
            
            <el-form-item label="收款银行" prop="region">
                <el-select v-model="data.bank.bankname" placeholder="请选择银行" style="width:100%">
                    <el-option v-for="item in group.banks" :key="item.key" :label="item.name" :value="item.name"></el-option>
                </el-select>
            </el-form-item>
            
            <el-form-item label="开户行">
                <el-input v-model="data.bank.branch"></el-input>
            </el-form-item>
            
            <el-form-item label="银行账号">
                <el-input v-model="data.bank.accountid"></el-input>
            </el-form-item>
            
            <el-form-item label="开户姓名">
                <el-input v-model="data.bank.account"></el-input>
            </el-form-item>

            <el-form-item>
                <el-button type="success" @click="putBank">确定</el-button>
                <el-button>取消</el-button>
            </el-form-item>
        
        </el-form>
    </div>


    <el-dialog title="修改密码" :visible.sync="show" width="400px">

        <el-form ref="form" :model="data.passwd" label-width="80px" v-loading="passwdLoading" size="medium" style="max-width: 500px;">
            <el-form-item label="旧密码">
                <el-input type="password" v-model="data.passwd.oldpasswd"></el-input>
            </el-form-item>
            <el-form-item label="新密码">
                <el-input type="password" v-model="data.passwd.newpasswd"></el-input>
            </el-form-item>
            <el-form-item label="确认密码">
                <el-input type="password" v-model="data.passwd.newpasswd1"></el-input>
            </el-form-item>
        </el-form>

        <div slot="footer" class="dialog-footer">
            <el-button @click="show = false">取 消</el-button>
            <el-button type="success" @click="putPasswd">确 定</el-button>
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
            userLoading: false,
            bankLoading: false,
            passwdLoading: false,
            show: false,
            data: {
                user: {},
                passwd: {},
                bank:{},
            },
        };
    },
    created: function () {
        
        this.group.page = '/webmaster/user';

        this.getUser();
        
        this.getBank();
    },
    methods:{
        getUser: function() {
            
            var Th = this;

            Th.userLoading = true;

            Th.$api.get('webmaster/user.json', {}, function(data){
            
                Th.data.user = data.user;
            
                Th.userLoading = false;
            
            }, function(type, message){ Th.userLoading = false; Th.$emit('message', type, message); });
        },
        putUser: function() {
            
            var Th = this;
            
            Th.userLoading = true;
            
            Th.$api.put('webmaster/user.json', Th.data.user, function(data)
            {
            
                Th.userLoading = false;

                Th.$emit('message', 'success', '修改成功');

            }, function(type, message){ Th.userLoading = false; Th.$emit('message', type, message); });
        },

        getBank: function() {
            
            var Th = this;

            Th.bankLoading = true;

            Th.$api.get('webmaster/bank.json', {}, function(data){
            
                Th.data.bank = data.bank;
            
                Th.bankLoading = false;
            
            }, function(type, message){ Th.bankLoading = false; Th.$emit('message', type, message); });
        },
        putBank: function() {
            
            var Th = this;
            
            Th.bankLoading = true;
            
            Th.$api.put('webmaster/bank.json', Th.data.bank, function(data)
            {
            
                Th.bankLoading = false;
            
                Th.$emit('message', 'success', '修改成功');

            }, function(type, message){ Th.bankLoading = false; Th.$emit('message', type, message); });
        },


        putPasswd: function() {
            
            var Th = this;
            
            Th.passwdLoading = true;
            
            Th.$api.put('webmaster/passwd.json', Th.data.passwd, function(data)
            {
            
                Th.passwdLoading = false;

                Th.$emit('message', 'success', '修改成功');
            
                Th.data.passwd = {};

            }, function(type, message){ Th.passwdLoading = false; Th.$emit('message', type, message); });
        },
    },
}
</script>