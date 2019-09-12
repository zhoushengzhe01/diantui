<template>
<div class="content">
    <div class="title-box">
        <h3 class="title">添加广告</h3>
        <div class="search-box">
            <el-form :inline="true" class="demo-form-inline" size="mini">
                <el-form-item>
                    <router-link to="/advertiser/ads">
                        <el-button type="success">返回列表</el-button>
                    </router-link>
                </el-form-item>
            </el-form>
        </div>
    </div>

    <div class="box" v-loading="loading" style="padding: 24px;">
        <el-form ref="form" :model="data.package" label-width="100px" size="medium" style="max-width: 740px;">

            <el-form-item label="广告标题">
                <el-input v-model="data.ad.title"></el-input>
            </el-form-item>

            <el-form-item label="推广链接">
                <el-input v-model="data.ad.link"></el-input>
            </el-form-item>
            
            <el-form-item label="广告类型">
                <el-radio-group v-model="data.ad.adstype_id" fill="#4caf50">
                    <el-radio
                        v-for="item in group.adtype" 
                        :key="item.key"
                        :label="item.id"
                    >{{item.name}}</el-radio>
                </el-radio-group>
            </el-form-item>
            
            <el-form-item label="计费方式">
                <el-radio-group v-model="data.ad.price_type" fill="#4caf50">
                    <el-radio label="1">CPC</el-radio>
                    <!-- <el-radio label="2">CPM</el-radio> -->
                </el-radio-group>
            </el-form-item>

            <el-form-item label="投放系统">
                <el-radio-group v-model="data.ad.client" fill="#4caf50">
                    <el-radio label="0">全部</el-radio>
                    <el-radio label="1">Ios</el-radio>
                    <el-radio label="2">Android</el-radio>
                </el-radio-group>
            </el-form-item>

            <el-form-item label="是否微信广告">
                <el-radio-group v-model="data.ad.is_wechat" fill="#4caf50">
                    <el-radio label="0">wap广告</el-radio>
                    <el-radio label="1">微信广告</el-radio>
                </el-radio-group>
            </el-form-item>

            <el-form-item label="设置预算/天">
                <el-input placeholder="输入明天预算，空代表物限制" v-model="data.ad.budget_day" style="width:100%;max-width:350px">
                    <template slot="append">元</template>
                </el-input>
            </el-form-item>

            <el-form-item label="设置总预算">
                <el-input placeholder="输入次广告总预算，空代表物限制" v-model="data.ad.budget" style="width:100%;max-width:350px">
                    <template slot="append">元</template>
                </el-input>
            </el-form-item>

            <el-form-item label="选择素材包">

                <el-select v-model="data.ad.package_id" filterable placeholder="输入关键词搜索" style="width:350px;">
                    <el-option
                        v-for="item in packages"
                        :key="item.value"
                        :label="item.name"
                        :value="item.id">
                    </el-option>
                </el-select>

                <router-link to="/advertiser/package">
                    <el-button type="success">添加素材</el-button>
                </router-link>
            </el-form-item>

            <!-- <el-form-item label="设置投放日期">
                <el-date-picker
                    value-format="yyyy-MM-dd"
                    v-model="data.ad.date"
                    type="daterange"
                    range-separator="~"
                    start-placeholder="开始日期"
                    end-placeholder="结束日期">
                </el-date-picker>
            </el-form-item> -->


            <!-- <el-form-item label="投放时间限制">
                <el-radio-group v-model="data.ad.is_put_hour" fill="#4caf50">
                    <el-radio label="0">不限制</el-radio>
                    <el-radio label="1">选择时间</el-radio>
                </el-radio-group>
            </el-form-item> -->

            <el-collapse-transition>
            <!-- <el-form-item label="" style="background: #f6f6f6;" v-if="data.ad.is_put_hour=='1'">
                <el-checkbox @change="selectHour" style="margin: 0 20px 0 0;">全选</el-checkbox><br/>
                <el-checkbox-group v-model="data.ad.hours">
                    <el-checkbox v-for="hour in group.hours" :label="hour" :key="hour" style="margin: 0 20px 0 0;">{{hour}}时</el-checkbox>
                </el-checkbox-group>
            </el-form-item> -->
            </el-collapse-transition>
            
            <!-- <el-form-item label="投放类型限制">
                <el-radio-group v-model="data.ad.is_put_category" fill="#4caf50">
                    <el-radio label="0">不限制</el-radio>
                    <el-radio label="1">选择投放类型</el-radio>
                </el-radio-group>
            </el-form-item> -->

            <el-collapse-transition>
            <!-- <el-form-item label="" style="background: #f6f6f6;" v-if="data.ad.is_put_category=='1'">
                <el-checkbox @change="selectCategory" style="margin: 0 20px 0 0;">全选</el-checkbox><br/>
                <el-checkbox-group v-model="data.ad.categorys">
                    <el-checkbox v-for="hour in group.categorys" :label="hour.id" :key="hour.id" style="margin: 0 20px 0 0;">{{hour.name}}</el-checkbox>
                </el-checkbox-group>
            </el-form-item> -->
            </el-collapse-transition>

            <el-form-item>
                <el-button type="success" @click="postAd">确定</el-button>
                <el-button>取消</el-button>
            </el-form-item>
        </el-form>
    </div>


</div>
</template>

<script>
export default {
	name: 'package',
    data: function () {	
		return {
            group: Group,
            loading: true,
            id: this.$route.params.id,
            packages: {},
			data: {
                ad: {
                    adstype_id: 11,
                    price_type: '1',
                    client: '0',
                    is_wechat: '0',
                    is_put_hour: '0',
                    hours: [],
                    is_put_category: '0',
                    categorys: [],
                    budget_day: '0',
                    budget: '0',
                    state: '2',
                },
            },
		};
    },
	created: function () {
        
        this.group.page = '/advertiser/ads';

        this.getPackages();
        
        if(this.id)
        {
            this.getAd();
        }else
        {
            this.loading = false;
        }
    },
    methods:{
        getAd: function()
        {
            var Th = this;

            Th.$api.get('advertiser/ad/'+Th.id+'.json', {}, function(data){

                Th.data.ad = data.ad;
            
                Th.loading = false;
            
            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        postAd: function()
        {
            var Th = this;
            if(this.id)
            {
                Th.$api.put('advertiser/ad/'+Th.id+'.json', Th.data.ad, function(data){
                    
                    Th.$emit('message', 'success', '修改成功');

                    Th.$router.push({path:'/advertiser/ads'})

                }, function(type, message){ Th.$emit('message', type, message); });
            }
            else
            {
                Th.$api.post('advertiser/ad.json', Th.data.ad, function(data){
                    
                    Th.$emit('message', 'success', '添加成功');

                    Th.$router.push({path:'/advertiser/ads'});

                }, function(type, message){ Th.$emit('message', type, message); });
            }
        },
        getPackages: function()
        {
            var Th = this;
            if(Th.data.ad.adstype_id)
            {
                Th.$api.get('advertiser/packages.json', {}, function(data){

                    Th.packages = data.packages;
                
                }, function(type, message){ Th.$emit('message', type, message); });
            }
        },
        selectHour: function(value)
        {
            if(value)
                this.data.ad.hours = this.group.hours;
            else
                this.data.ad.hours = [];
        },
        selectCategory: function(value)
        {
            if(value)
            {
                var Th = this; 
                
                var categorys = [];

                $.each(this.group.categorys, function(index, value){

                    categorys.push(value.id);
                
                });

                Th.data.ad.categorys = categorys;
            }
            else
            {
                this.data.ad.categorys = [];
            }
        },
	},
}
</script>