<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">编辑素材</h3>
        </div>

        <div class="box" v-loading="loading" style="padding: 24px;">
            <el-form ref="form" :model="data.package" label-width="80px" size="medium">
                
                <el-form-item label="素材名称">
                    <el-input v-model="data.package.name"></el-input>
                </el-form-item>

                <el-form-item label="广告主ID">
                    <el-input v-model="data.package.advertiser_id"></el-input>
                </el-form-item>

                <el-form-item label="素材类型">
                    <el-select v-model="data.package.adstype_id" placeholder="选择广告类型" style="width:100%">
                        <el-option v-for="item in group.adtype" :key="item.key" :label="item.name" :value="item.id"></el-option>
                    </el-select> 
                </el-form-item>

                <el-collapse-transition>
                <div v-if="data.package.adstype_id==11">
                    <el-form-item label="素材类型" style="max-width: 100%;">
                        <div class="images-box">
                            <div class="image-item" v-for="val in data.package.picture1" :key="val.key"><img v-bind:src="'//'+group.setting.matter_domain+'/'+val.path"/></div>
                            <div class="selectBut" @click="selectImgInit('3', 'picture1')"> <i class="el-icon-plus"></i> </div>
                            <span class="tishi">640X200像素</span>
                        </div>
                    </el-form-item>
                    <el-form-item label="素材类型" style="max-width: 100%;">
                        <div class="images-box">
                            <div class="image-item" v-for="val in data.package.picture2" :key="val.key"><img v-bind:src="'//'+group.setting.matter_domain+'/'+val.path"/></div>
                            <div class="selectBut" @click="selectImgInit('2', 'picture2')"> <i class="el-icon-plus"></i> </div>
                            <span class="tishi">640X150像素</span>
                        </div>
                    </el-form-item>
                    <el-form-item label="素材类型" style="max-width: 100%;">
                        <div class="images-box">
                            <div class="image-item" v-for="val in data.package.picture3" :key="val.key"><img v-bind:src="'//'+group.setting.matter_domain+'/'+val.path"/></div>
                            <div class="selectBut" @click="selectImgInit('1', 'picture3')"> <i class="el-icon-plus"></i> </div>
                            <span class="tishi">640X100像素</span>
                        </div>
                    </el-form-item>
                </div>

                <div v-if="data.package.adstype_id==12">
                    <el-form-item label="素材类型">
                        <div class="images-box">
                            <div class="image-item" v-for="val in data.package.picture1" :key="val.key"><img v-bind:src="'//'+group.setting.matter_domain+'/'+val.path"/></div>
                            <div class="selectBut" @click="selectImgInit('3', 'picture1')"> <i class="el-icon-plus"></i> </div>
                            <span class="tishi">640X200像素</span>
                        </div>
                    </el-form-item>
                    <el-form-item label="素材类型">
                        <div class="images-box">
                            <div class="image-item" v-for="val in data.package.picture2" :key="val.key"><img v-bind:src="'//'+group.setting.matter_domain+'/'+val.path"/></div>
                            <div class="selectBut" @click="selectImgInit('2', 'picture2')"> <i class="el-icon-plus"></i> </div>
                            <span class="tishi">640X150像素</span>
                        </div>
                    </el-form-item>
                    <el-form-item label="素材类型">
                        <div class="images-box">
                            <div class="image-item" v-for="val in data.package.picture3" :key="val.key"><img v-bind:src="'//'+group.setting.matter_domain+'/'+val.path"/></div>
                            <div class="selectBut" @click="selectImgInit('1', 'picture3')"> <i class="el-icon-plus"></i> </div>
                            <span class="tishi">640X100像素</span>
                        </div>
                    </el-form-item>
                </div>

                <div v-if="data.package.adstype_id==13">
                    <el-form-item label="素材类型">
                        <div class="images-box">
                            <div class="image-item" v-for="val in data.package.picture1" :key="val.key"><img v-bind:src="'//'+group.setting.matter_domain+'/'+val.path"/></div>
                            <div class="selectBut" @click="selectImgInit('5', 'picture1')"> <i class="el-icon-plus"></i> </div>
                            <span class="tishi">160X160像素</span>
                        </div>
                    </el-form-item>
                    <el-form-item label="百度素材">
                        <div class="images-box">
                            <div class="image-item" v-for="val in data.package.picture2" :key="val.key"><img v-bind:src="'//'+group.setting.matter_domain+'/'+val.path"/></div>
                            <div class="selectBut" @click="selectImgInit('5', 'picture2')"> <i class="el-icon-plus"></i> </div>
                            <span class="tishi">160X160像素</span>
                        </div>
                    </el-form-item>
                </div>

                <div v-if="data.package.adstype_id==14">
                    <el-form-item label="素材类型">
                        <div class="images-box">
                            <div class="image-item" v-for="val in data.package.picture1" :key="val.key"><img v-bind:src="'//'+group.setting.matter_domain+'/'+val.path"/></div>
                            <div class="selectBut" @click="selectImgInit('8', 'picture1')"> <i class="el-icon-plus"></i> </div>
                            <span class="tishi">640X320像素</span>
                        </div>
                    </el-form-item>
                </div>
                </el-collapse-transition>

                <el-form-item label="排序">
                    <el-input v-model="data.package.qq"></el-input>
                </el-form-item>

                <el-form-item label="状态">
                    <el-switch
                        active-value="1"
                        inactive-value="2"
                        active-color="#13ce66"
                        inactive-color="#ff4949"
                        v-model="data.package.state">
                    </el-switch>
                </el-form-item>

                <el-form-item>
                    <el-button type="success" @click="putPackage">确定</el-button>
                    <el-button>取消</el-button>
                </el-form-item>
            </el-form>
        </div>
        
        <uploadimg @complete="complete" ref="uploadimg"></uploadimg>
    </div>
</template>
<script>
import uploadimg from './../../views/uploadimg.vue';

export default {
    name: 'webmaster',
    data: function () {
        return {
            group: Group,
            loading: true,
            id: this.$route.params.id,
            data: {
                package: {
                    adstype_id: '',
                    state: '0',
                    picture1: {},
                    picture2: {},
                    picture3: {},
                },
            },
        };
    },
    created: function () {
        
        this.group.page = window.location.pathname;

        if(this.id)
        {
            this.getPackage();
        }else
        {
            this.loading = false;
        }

    },
    components: {
        uploadimg,
    },
    methods:{
        getPackage: function()
        {
            var Th = this;
            
            Th.loading = true;

            Th.$api.get('admin/advertiser/package/'+Th.id+'.json', {}, function(data)
            {
                Th.data = data;
                
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },

        putPackage: function()
        {
            var Th = this;

            Th.loading = true;

            if(this.id)
            {
                Th.$api.put('admin/advertiser/package/'+Th.id+'.json', Th.data.package, function(data){

                    Th.$emit('message', 'success', '修改成功');

                    Th.loading = false;

                    Th.$router.push({path:'/admin/advertiser/packages'});

                }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
            }
            else
            {
                Th.$api.post('admin/advertiser/package.json', Th.data.package, function(data){
                    
                    Th.$emit('message', 'success', '添加成功');

                    Th.loading = false;

                    Th.$router.push({path:'/admin/advertiser/packages'});

                }, function(type, message){ Th.loading=false; Th.$emit('message', type, message); });
                
            }

        },

        //-------------------------------------选择图片-------------------------------------
        selectImgInit: function(value, index)
        {
            this.index = index;

            this.$refs.uploadimg.init(value, this.data.package[this.index], this.data.package.advertiser_id);
        },
        complete: function(data)
        {
            this.data.package[this.index] = data;
        },
        //-------------------------------------选择图片-------------------------------------
    },
}
</script>