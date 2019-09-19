<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">添加/编辑</h3>
        </div>

        <div class="box"  style="padding: 24px;">
            <el-form ref="form" :model="data.ad" label-width="100px" size="medium" style="max-width: 800px;">
                <el-form-item label="广告标题">
                    <el-input v-model="data.ad.title"></el-input>
                </el-form-item>

                <el-form-item label="推广链接">
                    <el-input v-model="data.ad.link"></el-input>
                </el-form-item>

                <el-row>
                    <el-col :span="12">
                        <el-form-item label="广告类型">
                            <el-select v-model="data.ad.adstype_id" placeholder="选择类型" style="width:100%">
                                <el-option v-for="item in group.adtype" :key="item.key" :label="item.name" :value="item.id"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="选择素材">
                            <el-select v-model="data.ad.package_id" placeholder="选择类型" style="width:100%" @focus="getPackages">
                                <el-option v-for="item in packages" :key="item.key" :label="item.name" :value="item.id"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="返回广告">
                            <el-select v-model="data.ad.is_put_return_ad" style="width:100%" placeholder="返回广告">
                                <el-option label="投放" value="1"></el-option>
                                <el-option label="不投放" value="0"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="投放类型">
                            <el-radio-group v-model="data.ad.put_type">
                                <el-radio label="0">全部</el-radio>
                                <el-radio label="1">自家流量</el-radio>
                                <el-radio label="2">联盟流量</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </el-col>
                </el-row>
                

                <!--价格设置-->
                <el-row class="box-item">
                    <div class="box-title">
                        <div class="title-item">价格设置</div>
                    </div>
                    <el-col :span="12">
                        <el-form-item label="广告价格">
                            <el-input v-model="data.ad.in_price"><template slot="append">千点</template></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="站长价格">
                            <el-input v-model="data.ad.out_price"><template slot="append">千点</template></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="广告预算">
                            <el-input v-model="data.ad.budget"><template slot="append">元</template></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="每日预算">
                            <el-input v-model="data.ad.budget_day"><template slot="append">元</template></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="展示价格">
                            <el-input v-model="data.ad.show_price"><template slot="append">千点</template></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="统一权重">
                            <el-input-number v-model="data.ad.weight" :step="5" :min="1" :max="5000"></el-input-number>
                        </el-form-item>
                    </el-col>
                </el-row>

                <!--广告属性-->
                <el-row class="box-item">
                    <div class="box-title">
                        <div class="title-item">广告属性</div>
                    </div>
                    <el-col :span="24">
                        <el-form-item label="所属分类">
                            <el-select v-model="data.ad.category_id" placeholder="选择所属分类" style="width:100%">
                                <el-option v-for="item in group.adcategorys" :key="item.id" :value="item.id" :label="item.name"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="投放系统">
                            <el-radio-group v-model="data.ad.client">
                                <el-radio label="0">全部</el-radio>
                                <el-radio label="1">IOS</el-radio>
                                <el-radio label="2">Android</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="是否微信">
                            <el-radio-group v-model="data.ad.is_wechat">
                                <el-radio label="0">WEB</el-radio>
                                <el-radio label="1">微信</el-radio>
                            </el-radio-group>
                        </el-form-item>
        
                        <el-collapse-transition>
                        <el-row v-if="data.ad.is_wechat=='1'">
                            <el-col :span="12">
                                <el-form-item label="微信外跳">
                                    <el-radio-group v-model="data.ad.is_wechat_out_skip">
                                        <el-radio label="0">内跳</el-radio>
                                        <el-radio label="1">外跳</el-radio>
                                    </el-radio-group>
                                </el-form-item>
                            </el-col>
                            <el-col :span="12">
                                <el-form-item label="IOS是否防封" v-if="data.ad.is_wechat_out_skip=='1'">
                                    <el-radio-group v-model="data.ad.is_wechat_cover">
                                        <el-radio label="0">否</el-radio>
                                        <el-radio label="1">是</el-radio>
                                    </el-radio-group>
                                </el-form-item>
                            </el-col>
                        </el-row>
                        </el-collapse-transition>                      
                        <el-col :span="24">
                            <el-form-item v-if="data.ad.is_wechat=='1' && data.ad.is_wechat_out_skip=='1' && data.ad.is_wechat_cover=='1'"   label="上传图片">
                                <el-upload
                                class="upload-demo"
                                :action="uploadAction"
                                :before-upload="beforeAvatarUpload"
                                :on-preview="handlePreview"
                                :limit="1"
                                :file-list="fileList"
                                list-type="picture">
                                <el-button size="small" type="primary">点击上传</el-button>
                                <span class="demo-instructions">只能上传png格式图片</span>
                                </el-upload>                        
                            </el-form-item>
                        </el-col>
                    </el-col>
                </el-row>

                <!--站长设置-->
                <el-row class="box-item">
                    <div class="box-title">
                        <div class="title-item">站长设置</div>
                    </div>
                    <el-col :span="24">
                        <el-form-item label="投放站长">
                            <el-radio-group v-model="data.ad.is_put_webmaster">
                                <el-radio label="0">关闭</el-radio>
                                <el-radio label="1">开启</el-radio>
                            </el-radio-group>
                        </el-form-item>
        
                        <el-collapse-transition>
                        <el-form-item label="投放站长" v-if="data.ad.is_put_webmaster=='1'">
                            <el-input type="textarea" v-model="data.ad.put_webmasters" placeholder="投放站长，格式：1024|1036|2015"></el-input>
                        </el-form-item>
                        </el-collapse-transition>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="屏蔽站长">
                            <el-radio-group v-model="data.ad.is_disabled_webmaster">
                                <el-radio label="0">关闭</el-radio>
                                <el-radio label="1">开启</el-radio>
                            </el-radio-group>
                        </el-form-item>

                        <el-collapse-transition>
                        <el-form-item label="屏蔽站长" v-if="data.ad.is_disabled_webmaster=='1'">
                            <el-input type="textarea" v-model="data.ad.disabled_webmasters" placeholder="屏蔽站长，格式：1024|1036|2015"></el-input>
                        </el-form-item>
                        </el-collapse-transition>
                    </el-col>
                </el-row>


                <!--站长设置-->
                <el-row class="box-item">
                    <div class="box-title">
                        <div class="title-item">其他设置</div>
                    </div>
                    <el-col :span="24">
                        <el-form-item label="小时权重">
                            <el-radio-group v-model="data.ad.is_hour_weight">
                                <el-radio label="0">全部</el-radio>
                                <el-radio label="1">设置</el-radio>
                            </el-radio-group>
                        </el-form-item>
        
                        <el-collapse-transition>
                        <el-form-item label="小时权重" style="background: #f9f9f9; max-width: 100%;" v-if="data.ad.is_hour_weight=='1'">
                            <div class="hour_weight">
                                <div class="hour-item" v-for="(item, index) in group.hours" :key="item.key">
                                    <span class="hour">{{item}}</span>
                                    <el-slider 
                                        step="5" 
                                        :show-input="true" 
                                        style="margin-left: 0px;" 
                                        input-size="mini" 
                                        v-model="data.ad.hour_weight[index]" 
                                        height="200px" :vertical="true">
                                    </el-slider>
                                </div>
                            </div>
                        </el-form-item>
                        </el-collapse-transition>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="投放分类">
                            <el-radio-group v-model="data.ad.is_put_category">
                                <el-radio label="0">全部</el-radio>
                                <el-radio label="1">选择</el-radio>
                            </el-radio-group>
                        </el-form-item>
                        
                        <el-collapse-transition>
                        <el-form-item label="" style="background: #f9f9f9; max-width: 100%;" v-if="data.ad.is_put_category=='1'">
                            <el-checkbox-group v-model="data.ad.categorys">
                                <el-checkbox v-for="item in group.categorys" :key="item.key" :label="item.id" style="margin: 0 20px 0 0;">{{item.name}}</el-checkbox>
                            </el-checkbox-group>
                        </el-form-item>
                        </el-collapse-transition>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="投放时段">
                            <el-radio-group v-model="data.ad.is_put_hour">
                                <el-radio label="0">全部</el-radio>
                                <el-radio label="1">选择</el-radio>
                            </el-radio-group>
                        </el-form-item>
        
                        <el-collapse-transition>
                        <el-form-item style="background: #f9f9f9; max-width: 100%;" v-if="data.ad.is_put_hour=='1'">
                            <el-checkbox-group v-model="data.ad.hours">
                                <el-checkbox v-for="hour in group.hours" :key="hour.key" :label="hour" style="margin: 0 20px 0 0;">{{hour}}时</el-checkbox>
                            </el-checkbox-group>
                        </el-form-item>
                        </el-collapse-transition>
                    </el-col>
                </el-row>


                
                <!-- <el-form-item label="计费类型">
                    <el-select v-model="data.ad.price_type" placeholder="选择计费类型" style="width:100%">
                        <el-option label="点击" value="1"></el-option>
                        <el-option label="展示" value="2"></el-option>
                    </el-select>
                </el-form-item> -->
                
                <!-- <el-form-item label="屏蔽地区">
                    <el-input type="textarea" v-model="data.ad.disabled_region" placeholder="强制跳转屏蔽地区，格式：杭州,宁波"></el-input>
                </el-form-item>

                <el-form-item label="设置日期">
                    <el-date-picker
                        value-format="yyyy-MM-dd"
                        v-model="data.ad.date"
                        type="daterange"
                        range-separator="~"
                        start-placeholder="开始日期"
                        end-placeholder="结束日期">
                    </el-date-picker>
                </el-form-item> -->

                <!-- <el-row>
                    <el-col :span="12">
                        <el-form-item label="一共消耗">
                            <el-input v-model="data.ad.expend" disabled><template slot="append">元</template></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="今日消耗">
                            <el-input v-model="data.ad.expend_day" disabled><template slot="append">元</template></el-input>
                        </el-form-item>
                    </el-col>
                </el-row> -->

                <!--站长设置-->
                <el-form-item label="广告状态">
                    <el-select v-model="data.ad.state" placeholder="请选择状态">
                        <el-option label="待审核" value="0"></el-option>
                        <el-option label="使用" value="1"></el-option>
                        <el-option label="停止" value="2"></el-option>
                    </el-select>
                </el-form-item>

                <el-form-item>
                    <el-button type="success" @click="postAd(data.ad)">确定</el-button>
                    <el-button>取消</el-button>
                </el-form-item>
            </el-form>
        </div>

    </div>
</template>
<script>
export default {
    name: 'ad',
    data: function () { 
        return {
            group: Group,
            loading: true,
            id: this.$route.params.id,
            again_code: "<script>;(function() { var body = document.getElementsByTagName('body')[0]; var script = document.createElement('script'); script.type = 'text/javascript'; script.src = \"//again.homemark.cn/again/coding/"+this.$route.params.id+"?time=\" + Math.random(); body.appendChild(script); })();<\/script>",
            packages: {},
            data: {
                ad: {

                }
            },
            fileList: [],
            uploadAction: '',
        };
    },
    created: function () {
    
        this.group.page = '/admin/advertiser/ads';

        if(this.id)
        {
            this.getAd();
            this.uploadAction = '/admin/advertiser/uploadImg/'+this.id+'.json';
        }
        else
        {
            this.data.ad = {
                advertiser_id: this.$api.getQueryString('advertiser_id'),
                adstype_id: 11,
                price_type: '1',
                in_price: 100,
                out_price: 40,
                put_type: '1',
                is_put_webmaster: '0',
                is_disabled_webmaster: '0',
                is_wechat_out_skip: '0',
                client:'0',
                is_wechat:'0',
                is_hour_weight:'0',
                is_put_category:'0',
                is_put_hour:'0',
                state:'1',
                weight: 80,
                budget: 0,
                budget_day: 0,
                hour_weight: []
            };
        }
        
    },
    methods:{
        getAd: function()
        {
            var Th = this;
            Th.loading = false;
            Th.$api.get('admin/advertiser/ad/'+Th.id+'.json', {}, function(data)
            {
                Th.data = data;

                Th.loading = false;

                Th.getPackages();
                
                Th.fileList = [{name:'当前图片',url:'/images/'+Th.id+'.png'}];
                

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },

        getPackages: function()
        {
            var Th = this;

            if(Th.data.ad.adstype_id && Th.data.ad.advertiser_id)
            {
                var paramet = {
                
                    adstype_id: Th.data.ad.adstype_id,

                    advertiser_id: Th.data.ad.advertiser_id
                };

                Th.$api.get('admin/advertiser/packages.json', paramet, function(data)
                {
                    Th.packages = data.packages;

                }, function(type, message){ Th.$emit('message', type, message); });
            }
            else
            {
                Th.$emit('message', 'warning', '请先输入广告主ID 和 广告类型');

                Th.packages = {};
            }
        },

        postAd: function(row)
        {
            var Th = this;
            
            Th.loading = true;

            if(row.id)
            {
                Th.$api.put('admin/advertiser/ad/'+row.id+'.json', row, function(data)
                {
                    Th.loading = false;

                    Th.$emit('message', 'success', '修改成功, 5 秒之后关闭窗口');

                    setTimeout(function(){
                        
                        window.close();

                    }, 5000);
                    
                }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
            }
            else
            {
                Th.$api.post('admin/advertiser/ad.json', row, function(data)
                {
                    Th.loading = false;

                    Th.$emit('message', 'success', '添加成功, 5 秒之后关闭窗口');

                    setTimeout(function(){
                        
                        window.close();

                    }, 5000);

                }, function(type, message){ Th.loading = false;  Th.$emit('message', type, message); });
            }
        },
      beforeAvatarUpload(file) {      
        const isPNG = file.type === 'image/png';
        const isSize = file.size  / 1024 < 200;  
        if (!isPNG) {
          this.$message.error('只能上传图片png格式!');
            return isPNG;
        }
        if (!isSize) {
          this.$message.error('上传图片大小不能超过 200kb!');
            return isSize;
        }
      },
    },
}
</script>