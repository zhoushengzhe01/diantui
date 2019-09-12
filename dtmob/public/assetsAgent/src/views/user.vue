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
            show: false,
            data: {
                user: {},
                bank:{},
            },
        };
    },
    created: function () {
        this.group.page = '/agent/user';
        this.getUser();
        this.getBank();
    },
    methods:{
        getUser: function() {
            
            var Th = this;
            Th.userLoading = true;
            Th.$api.get('agent/user.json', {}, function(data){

                Th.data.user = data.user;
                Th.userLoading = false;
            }, function(type, message){ Th.userLoading = false; Th.$emit('message', type, message); });
        },
        putUser: function() {
            
            var Th = this;
            Th.userLoading = true;
            Th.$api.put('agent/user.json', Th.data.user, function(data){

                Th.userLoading = false;
                Th.$emit('message', 'success', '修改成功');
            }, function(type, message){ Th.userLoading = false; Th.$emit('message', type, message); });
        },

        getBank: function() {
            
            var Th = this;
            Th.bankLoading = true;
            Th.$api.get('agent/bank.json', {}, function(data){
            
                Th.data.bank = data.bank;
                Th.bankLoading = false;
            }, function(type, message){ Th.bankLoading = false; Th.$emit('message', type, message); });
        },
        putBank: function() {
            
            var Th = this;
            Th.bankLoading = true;
            
            Th.$api.put('agent/bank.json', Th.data.bank, function(data){
            
                Th.bankLoading = false;
                Th.$emit('message', 'success', '修改成功');
            }, function(type, message){ Th.bankLoading = false; Th.$emit('message', type, message); });
        },

    },
}
</script>