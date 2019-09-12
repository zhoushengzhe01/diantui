<template>
<div class="content">
    <div class="title-box">
        <h3 class="title">添加素材</h3>
        <div class="search-box">
            <el-form :inline="true" class="demo-form-inline" size="mini">
                <el-form-item>
                    <router-link to="/advertiser/package">
                        <el-button type="success"> + 添加素材</el-button>
                    </router-link>
                </el-form-item>
            </el-form>
        </div>
    </div>

    <div class="box" v-loading="loading" style="padding: 24px;">
        <el-form ref="form" :model="data.package" label-width="80px" size="medium" style="max-width: 740px;">
            <el-form-item label="素材名称">
                <el-input v-model="data.package.name"></el-input>
            </el-form-item>
            <el-form-item label="投放类型">
                <el-select v-model="data.package.adstype_id" placeholder="请选择投放类型">
                    <el-option
                        v-for="item in group.adtype"
                        :key="item.key"
                        :label="item.name"
                        :value="item.id">
                    </el-option>
                </el-select>
            </el-form-item>
            <!--横幅上传图片-->
            <el-collapse-transition>
            <div v-if="data.package.adstype_id==11">
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

            <!-- <el-form-item label="素材状态">
                <el-switch
                    active-value="1"
                    inactive-value="0"
                    active-color="#13ce66"
                    inactive-color="#ff4949"
                    v-model="data.package.state">
                </el-switch>
            </el-form-item> -->
            <el-form-item label="描述说明">
                <el-input type="textarea" v-model="data.package.note"></el-input>
            </el-form-item>

            <el-form-item>
                <el-button type="success" @click="postPackage">确定</el-button>
                <el-button>取消</el-button>
            </el-form-item>
        </el-form>
    </div>

    <uploadimg @complete="complete" ref="uploadimg"></uploadimg>
</div>
</template>

<script>
import uploadimg from './../views/uploadimg.vue';

export default {
	name: 'package',
    data: function () {	
		return {
            group: Group,
            loading: true,
            id: this.$route.params.id,
            index: null,    //图片KEY
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
    components: {
		uploadimg,
  	},
	created: function () {

        this.group.page = '/advertiser/packages';

        if(this.id)
        {
            this.getPackage();
        }else
        {
            this.loading = false;
        }
    },
    methods:{
        //------------------------------------数据获取和修改---------------------------------
        getPackage: function()
        {
            var Th = this;
            
            Th.$api.get('advertiser/package/'+Th.id+'.json' ,[] , function(data){
            
                Th.data = data;

                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.Err(type, message) });
        },
        postPackage: function()
        {
            var Th = this;
            
            Th.loading = true;

            if(this.id)
            {
                Th.$api.put('advertiser/package/'+Th.id+'.json', Th.data.package, function(data){

                    Th.$emit('message', 'success', '修改成功');

                    Th.$router.push({path:'/advertiser/packages'});

                    Th.loading = false;

                }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
            }
            else
            {
                Th.$api.post('advertiser/package', Th.data.package, function(data){
                    
                    Th.$emit('message', 'success', '添加成功');

                    Th.$router.push({path:'/advertiser/packages'});

                    Th.loading = false;

                }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
                
            }
        },
        //------------------------------------数据获取和修改---------------------------------



        
        //-------------------------------------选择图片-------------------------------------
        selectImgInit: function(value, index)
        {
            this.index = index;

            this.$refs.uploadimg.init(value, this.data.package[this.index]);
        },
        complete: function(data)
        {
            this.data.package[this.index] = data;
        },
        //-------------------------------------选择图片-------------------------------------
	},
}
</script>