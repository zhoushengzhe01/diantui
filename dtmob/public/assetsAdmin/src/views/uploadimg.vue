<template>
<div class='content uploadimg'>
    <el-dialog title="上传图片" :visible.sync="isOpen" width="680px">

        <el-row class="tac">
            <el-col :span="4">
                <el-menu
                :default-active="paramete.sizeNum"
                class="el-menu-vertical-demo"
              
                style="height:520px">
                    <el-menu-item index="1" @click="Switch('1')">
                        <span>640X100像素</span>
                    </el-menu-item>
                    <el-menu-item index="2" @click="Switch('2')">
                        <span>640X150像素</span>
                    </el-menu-item>
                    <el-menu-item index="3" @click="Switch('3')">
                        <span>640X200像素</span>
                    </el-menu-item>
                    <el-menu-item index="4" @click="Switch('4')">
                        <span>640X250像素</span>
                    </el-menu-item>
                    <el-menu-item index="5" @click="Switch('5')">
                        <span>100X100像素</span>
                    </el-menu-item>
                    <el-menu-item index="6" @click="Switch('6')">
                        <span>250X250像素</span>
                    </el-menu-item>
                    <el-menu-item index="7" @click="Switch('7')">
                        <span>300X250像素</span>
                    </el-menu-item>
                </el-menu>
            </el-col>
            <el-col :span="20">
                <div class="images-box selected-box">
                    <div class="image-item" v-for="(val,index) in selectImgs" :key="val.key">
                        <img v-bind:src="'//'+group.setting.matter_domain+'/'+val.path"/>
                        <span class="delete-btn" @click="selectImgs.splice(index,1)"> <i class="el-dialog__close el-icon el-icon-close"></i> </span>
                    </div>
                    <span v-if="selectImgs.length<1">无图片，请选择下面图片...</span>
                </div>
                <div class="images-box" style="background: #fff;" v-loading="loading">
                    <el-upload
                        class="image-item" style="padding: 0px; border: none;"
                        action="/admin/advertiser/matter.json"
                        :data="{sizeNum: paramete.sizeNum, advertiser_id: paramete.advertiser_id, _token: token}"
                        list-type="picture-card"
                        :show-file-list="false"
                        :on-error="uploadError"
                        :on-success="uploadSuccess"
                        >
                        <i class="el-icon-plus"></i>
                    </el-upload>

                    <div class="image-item" v-for="val in data.matters" :key="val.key" @click="selectImgItem(val)"><img v-bind:src="'//'+group.setting.matter_domain+'/'+val.path"/></div>
                </div>

                <div class="page-box">
                    <el-pagination
                    @current-change="pageChange"
                    layout="total, prev, pager, next"
                    :page-size="paramete.limit"
                    :total="data.count">
                    </el-pagination>
                </div>
            </el-col>
        </el-row>

        <div slot="footer" class="dialog-footer">
            <el-button @click="isOpen = false">取 消</el-button>
            <el-button type="success" @click="confirmImg">确 定</el-button>
        </div>
      
    </el-dialog>
</div>
</template>

<script>
export default {
    name: 'uploadimg',
    data: function () {
        return {
            group: Group,
            token: Token,
            loading: false,
            isOpen: false,
            selectImgs: [],
            paramete: {
                offset: 0,
                limit: 14,
                advertiser_id: '1',    //当前尺寸
                sizeNum: '1',    //当前尺寸
            },
            data: {},
        };
    },
    methods:{
        /**
         * 尺寸号1：640*100
         * 尺寸号2：640*150
         * 尺寸号3：640*200
         * 尺寸号4：640*250
         * 尺寸号5：100*100
         * 尺寸号6：250*250
         * 尺寸号7：300*250
         **/
        init: function(sizeNum, Imgs, advertiser_id)
        {
            this.paramete.sizeNum = sizeNum;
            this.paramete.advertiser_id = advertiser_id;
            this.isOpen = true;
            this.data = {};

            this.selectImgs = Imgs;
            this.getMatters();

            if(Imgs.length>0)
            {
                this.selectImgs = Imgs;
            }else{
                this.selectImgs = [];
            }
        },
        //菜单切换
        Switch: function(sizeNum){
            this.paramete.sizeNum = sizeNum;
            this.getMatters();
        },
        //-----------------------选择图片----------------------------------
        selectImgItem: function(image)
        {
            if(this.selectImgs.length > 3)
            {
                this.$message({
                    showClose: true,
                    message: '最多只能选四个',
                    type: 'warning'
                });
            }else
            {
                this.selectImgs.push({
                    path: image.path,
                    size: image.size,
                    width: image.width,
                    height: image.height,
                });
            }
        },
        confirmImg: function()
        {
            this.$emit('complete', this.selectImgs);
            this.isOpen = false;
            this.data = {};
        },
        //-----------------------选择图片----------------------------------



        //-----------------------列表以及分页-------------------------------
        getMatters: function()
        {
            var Th = this;
            
            Th.loading = true;

            Th.$api.get('admin/advertiser/matters.json', Th.paramete, function(data) {
                
                Th.data = data;
                
                Th.loading = false;

            }, function(type, message){
                
                Th.$message({ showClose: true, message: message, type: type });

                Th.isOpen = false;

                Th.loading = false;
            });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getMatters();
        },
        //-----------------------列表以及分页-------------------------------
        



        //--------------------------上传回掉方法----------------------------
        uploadError: function(err, file, fileList)
        {
            var data = eval('(' + err.message + ')');
            this.$message({
                showClose: true,
                message: data.message,
                type: 'error'
            });
        },
        uploadSuccess: function(response, file, fileList)
        {
            this.getMatters();
        },
        //--------------------------上传回掉方法----------------------------
        
    }
};
</script>