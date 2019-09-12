<template>   
    <div class="content">
        <div class="title-box">
            <h3 class="title">推广链接</h3>
            <div class="search-box">
                <el-form :inline="true" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-button type="success">查询</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box">
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <el-form :inline="true" :model="paramete" class="demo-form-inline" style="width:450px; margin: 0 auto;">
                <el-form-item label="">
                    <el-input v-model="like" id="like" style="width: 300px;"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="success" @click="copyUrl">点击复制</el-button>
                </el-form-item>
            </el-form>
            <br/>
            <br/>
            <br/>
            <br/>
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
            like: "http://dtmob.cn/register?aid="+Group.agent.id,
            data: {},
        };
    },
    created: function () {
        this.group.page = '/agent/logs';
    },
    methods:{
        copyUrl: function() {
            var url=document.getElementById("like");
            url.select();
            document.execCommand("Copy");
            Th.$emit('message', '200', '已复制好，可贴粘。');
        },
    },
}
</script>