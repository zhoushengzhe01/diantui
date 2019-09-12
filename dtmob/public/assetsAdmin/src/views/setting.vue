<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">系统设置</h3>
        </div>
        
        <div class="box" v-loading="loading" style="padding: 24px;">
            <el-form ref="form" :model="data.setting" label-width="100px" size="medium" style="max-width: 580px;">
                
                <el-form-item label="网站名称">
                    <el-input v-model="data.setting.title"></el-input>
                </el-form-item>

                <el-form-item label="网站域名">
                    <el-input v-model="data.setting.domain"></el-input>
                </el-form-item>

                <el-form-item label="网站地址">
                    <el-input v-model="data.setting.url"></el-input>
                </el-form-item>

                <el-form-item label="SEO 标题">
                    <el-input v-model="data.setting.seo_title"></el-input>
                </el-form-item>

                <el-form-item label="SEO关键词">
                    <el-input type="textarea" v-model="data.setting.seo_keywords"></el-input>
                </el-form-item>

                <el-form-item label="SEO 描述">
                    <el-input type="textarea" v-model="data.setting.seo_description"></el-input>
                </el-form-item>

                <el-form-item label="public目录">
                    <el-input v-model="data.setting.public"></el-input>
                </el-form-item>

                <el-form-item label="信息流域名">
                    <el-input v-model="data.setting.ad_info_domain"></el-input>
                </el-form-item>

                <el-form-item label="获取域名">
                    <el-input v-model="data.setting.ad_domain"></el-input>
                </el-form-item>

                <el-form-item label="点击域名">
                    <el-input v-model="data.setting.click_domain"></el-input>
                </el-form-item>

                <el-form-item label="展示域名">
                    <el-input v-model="data.setting.pv_domain"></el-input>
                </el-form-item>

                <el-form-item label="微信点击域名">
                    <el-input v-model="data.setting.wechat_click_domain"></el-input>
                </el-form-item>

                <el-form-item label="微信展示域名">
                    <el-input v-model="data.setting.wechat_pv_domain"></el-input>
                </el-form-item>

                <el-form-item label="图片域名">
                    <el-input v-model="data.setting.matter_domain"></el-input>
                </el-form-item>

                <el-form-item label="广告比例">
                    <el-slider show-input="true" v-model="data.setting.ad_ratio"></el-slider>
                </el-form-item>

                <el-form-item label="一反链接">
                    <el-input v-model="data.setting.one_return_url"></el-input>
                </el-form-item>

                <el-form-item label="二返屏蔽地区">
                    <el-input v-model="data.setting.one_return_disabled_region"></el-input>
                </el-form-item>

                <el-form-item label="二返链接">
                    <el-input v-model="data.setting.two_return_url"></el-input>
                </el-form-item>

                <el-form-item label="二返屏蔽地区">
                    <el-input v-model="data.setting.two_return_disabled_region"></el-input>
                </el-form-item>

                <el-form-item label="网站状态">
                    <el-switch
                        active-value="1"
                        inactive-value="2"
                        active-color="#13ce66"
                        inactive-color="#ff4949"
                        v-model="data.setting.state">
                    </el-switch>
                </el-form-item>

                <el-form-item label="关闭提示" v-if="data.setting.state=='2'">
                    <el-input type="textarea" v-model="data.setting.message"></el-input>
                </el-form-item>

                <el-form-item>
                    <el-button type="success" @click="putSetting">确定</el-button>
                    <el-button>取消</el-button>
                </el-form-item>
            </el-form>
        </div>


    </div>
</template>
<script>
export default {
    name: 'setting',
    data: function () { 
        return {
            group: Group,
            loading: true,
            id: this.$route.params.id,
            data: {
                setting: []
            },
        };
    },
    created: function () {
        
        this.group.page = window.location.pathname;

        this.getSetting();
    },
    methods:{
        getSetting: function()
        {
            var Th = this;
            Th.loading = true;
            Th.$api.get('admin/setting.json', {}, function(data)
            {
                Th.data = data;
                
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },

        putSetting: function()
        {
            var Th = this;
            
            Th.loading = true;
            
            Th.$api.put('admin/setting.json', Th.data.setting, function(data)
            {
                Th.loading = false;

                Th.$emit('message', 'success', '修改成功');

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
    },
}
</script>