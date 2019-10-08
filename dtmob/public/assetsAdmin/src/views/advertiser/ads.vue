<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">广告管理</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-input v-model="paramete.id" placeholder="广告id"></el-input>
                    </el-form-item>

                    <el-form-item>
                        <el-input v-model="paramete.advertiser_id" placeholder="广告主id"></el-input>
                    </el-form-item>
                    
                    <el-form-item>
                        <el-input v-model="paramete.username" placeholder="广告主名字"></el-input>
                    </el-form-item>

                    <el-form-item>
                        <el-select v-model="paramete.state" placeholder="广告状态" style="width:100px;">
                            <el-option label="开启" value="1"></el-option>
                            <el-option label="关闭" value="2"></el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item>
                        <el-select v-model="paramete.is_wechat" placeholder="微信和WAP" style="width:100px;">
                            <el-option label="WAP广告" value="0"></el-option>
                            <el-option label="微信广告" value="1"></el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item>
                        <el-select v-model="paramete.client" placeholder="系统类型" style="width:100px;">
                            <el-option label="IOS" value="1"></el-option>
                            <el-option label="Android" value="2"></el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item>
                        <el-select v-model="paramete.adstype_id" placeholder="选择类型" style="width:120px;">
                            <el-option v-for="item in group.adtype" :key="item.key" :label="item.name" :value="item.id"></el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item>
                        <el-select v-model="paramete.flowpool" placeholder="流量池查询">
                            <el-option label="选择全部" value=""></el-option>
                            <el-option v-for="item in group.flowpools" :key="item.key" :label="item.name" :value="item.id"></el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item>
                        <el-select v-model="paramete.busine_id" placeholder="商务查询">
                            <el-option label="全部商务" value=""></el-option>
                            <el-option v-for="item in group.busines" :key="item.key" :label="item.nickname" :value="item.id"></el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item>
                        <el-button type="success" @click="getAds">查询</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">
            <el-table :data="data.all_earning" style="width: 100%">
                <el-table-column
                    prop="name"
                    label="时间"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    prop="pv_number"
                    label="展示量"
                    min-width="160">
                </el-table-column>
                
                <el-table-column
                    prop="pc_number"
                    label="点击量"
                    min-width="160">
                </el-table-column>
                
                <el-table-column
                    prop="ip_number"
                    label="IP数量"
                    min-width="160">
                </el-table-column>
                
                <el-table-column
                    label="消耗"
                    min-width="160">
                    <template slot-scope="scope">
                        {{scope.row.money}} 元
                    </template>
                </el-table-column>
                
                <el-table-column
                    label=""
                    min-width="160">
                    <template slot-scope="scope">                  
                        <a href="" @click.prevent="exportAdvertiser(scope.row.s_date)">导出产品</a>
                    </template>
                </el-table-column>
            
            </el-table>
        
        </div>

        <div class="box" v-loading="loading">
            <el-table :data="data.ads" style="width: 100%">
                <el-table-column
                    label="ID"
                    min-width="60">
                    <template slot-scope="scope">
                        <router-link target="_blank" :to="'/admin/advertiser/'+scope.row.advertiser_id">{{scope.row.advertiser_id}}</router-link>
                        <br/>{{scope.row.id}}
                    </template>
                </el-table-column>

                <el-table-column
                    label="联盟"
                    min-width="100">
                    <template slot-scope="scope">
                        <span v-for="item in group.alliance_agents" :key="item.key" v-if="item.id==scope.row.alliance_agent_id">{{item.name}}</span><br/>
                        <span v-for="item in group.flowpools" :key="item.key" v-if="scope.row.flowpool.indexOf(item.id) >= 0">{{item.name}}</span><br/>
                    </template>
                </el-table-column>

                <el-table-column
                    label="商务"
                    min-width="100">
                    <template slot-scope="scope">
                        <router-link target="_blank" :to="'/admin/advertiser/ad/'+scope.row.advertiser_id">{{scope.row.username}}</router-link> <br/>
                        <span v-for="item in group.busines" :key="item.key" v-if="item.id==scope.row.busine_id">{{item.nickname}}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="标题"
                    min-width="100">
                    <template slot-scope="scope">
                        <router-link target="_blank" style="white-space: nowrap;" :to="'/admin/advertiser/packages?id='+scope.row.package_id">{{scope.row.title}}</router-link>
                        <br/>
                        <span class="info">权重:</span>
                        <span v-if="scope.row.is_hour_weight=='0'">{{scope.row.weight}}</span>
                        <span v-if="scope.row.is_hour_weight=='1'">时段</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="类型"
                    min-width="100">
                    <template slot-scope="scope">
                        <span v-if="scope.row.is_put_return_ad==1">返回</span>
                        <span v-for="item in group.adtype" :key="item.key" v-if="item.id==scope.row.adstype_id && scope.row.is_put_return_ad!=1">{{item.name}}</span>
                        -<span v-if="scope.row.is_wechat=='0'">wap</span><span v-if="scope.row.is_wechat=='1'">微信</span>
                        <br/>
                        <span v-if="scope.row.client=='0'" class="info">IOS-Android</span>
                        <span v-if="scope.row.client=='1'" class="info">IOS</span>
                        <span v-if="scope.row.client=='2'" class="info">Android</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="价格"
                    min-width="80">
                    <template slot-scope="scope">
                        <span class="info">站:</span>{{scope.row.out_price}} 元<br/>
                        <span class="info">告:</span>{{scope.row.in_price}} 元
                    </template>
                </el-table-column>
                
                <el-table-column
                    label="限制"
                    min-width="80">
                    <template slot-scope="scope">
                        <span v-if="scope.row.is_put_hour=='0'" class="info">时:不限</span>
                        <span v-if="scope.row.is_put_hour=='1'" style="color:#67c23a">时:限时</span>
                        <br/>
                        <span v-if="scope.row.is_put_webmaster=='0'" class="info">站:不限</span>
                        <span v-if="scope.row.is_put_webmaster=='1'" style="color:#67c23a">站:限站</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="类型"
                    min-width="60">
                    <template slot-scope="scope">
                        <span v-if="scope.row.put_type=='0'" class="info">全部</span>
                        <span v-if="scope.row.put_type=='1'" class="info">自家<br/>流量</span>
                        <span v-if="scope.row.put_type=='2'" class="info">联盟<br/>流量</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="分类"
                    min-width="60">
                    <template slot-scope="scope">
                        <span v-for="item in group.adcategorys" :key="item.key" v-if="item.id==scope.row.category_id">{{item.name}}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="前天消耗"
                    min-width="200">
                    <template slot-scope="scope">
                        <span v-if="scope.row.day[0]">
                            <span class="info">展:</span>{{scope.row.day[0].pv_number}}&nbsp;
                            <span class="info">点:</span>{{scope.row.day[0].pc_number}}
                            <br/>
                            <span class="info">耗:</span>{{ parseInt(scope.row.day[0].money*10)/10 }} 元
                            <span class="info" v-if="scope.row.day[0].pc_number!=0 && scope.row.day[0].pv_number!=0">({{ parseInt(scope.row.day[0].pc_number / scope.row.day[0].pv_number * 10000)/100 }}%)</span>
                        </span>
                        <span v-if="!scope.row.day[0]" class="info">无数据</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="昨天消耗"
                    min-width="200">
                    <template slot-scope="scope">
                        <span v-if="scope.row.day[1]">
                            <span class="info">展:</span>{{scope.row.day[1].pv_number}}&nbsp;
                            <span class="info">点:</span>{{scope.row.day[1].pc_number}}
                            <br/>
                            <span class="info">耗:</span>{{ parseInt(scope.row.day[1].money*10)/10 }} 元
                            <span class="info" v-if="scope.row.day[1].pc_number!=0 && scope.row.day[1].pv_number!=0">({{ parseInt(scope.row.day[1].pc_number / scope.row.day[1].pv_number * 10000)/100 }}%)</span>
                        </span>
                        <span v-if="!scope.row.day[1]" class="info">无数据</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="今天消耗"
                    min-width="200">
                    <template slot-scope="scope">
                        <span v-if="scope.row.day[2]">
                            <span class="info">展:</span>{{scope.row.day[2].pv_number}}&nbsp;
                            <span class="info">点:</span>{{scope.row.day[2].pc_number}}
                            <br/>
                            <span class="info">耗:</span>{{ parseInt(scope.row.day[2].money*10)/10 }} 元
                            <span class="info" v-if="scope.row.day[2].pc_number!=0 && scope.row.day[2].pv_number!=0">({{ parseInt(scope.row.day[2].pc_number / scope.row.day[2].pv_number * 10000)/100 }}%)</span>
                        </span>
                        <span v-if="!scope.row.day[2]" class="info">无数据</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="状态"
                    min-width="60">
                    <template slot-scope="scope">
                        <el-switch
                            @change="putAds(scope.row)"
                            v-model="scope.row.state"
                            active-value="1"
                            inactive-value="2"
                            active-color="#13ce66"
                            inactive-color="#CCCCCC">
                        </el-switch>
                    </template>
                </el-table-column>

                <el-table-column
                    v-bind:router="true"
                    fixed="right"
                    label="操作"
                    width="120">
                    <template slot-scope="scope">
                        <router-link target="_blank" :to="'/admin/advertiser/ad/'+scope.row.id">编辑</router-link>
                        <router-link target="_blank" :to="'/admin/advertiser/consumes?advertiser_ad_id='+scope.row.id">详细</router-link>
                        <a :href="scope.row.link" target="_blank">连接</a>
                    </template>
                </el-table-column>
            </el-table>

            <div class="page-box">
                <el-pagination
                @current-change="pageChange"
                layout="total, prev, pager, next"
                :page-size="paramete.limit"
                :total="data.count">
                </el-pagination>
            </div>
        </div>

    </div>
</template>
<script>
export default {
    name: 'recharges',
    data: function () { 
        return {
            group: Group,
            loading: true,
            paramete: {
                offset: 0,
                limit: 15,
            },
            data: {},
        };
    },
    created: function () {
        
        this.group.page = window.location.pathname;

        this.getAds();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getAds: function()
        {
            var Th = this;
            Th.loading = true;
            Th.$api.get('admin/advertiser/ads.json', Th.paramete, function(data)
            {
                Th.data = data;
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getAds();
        },
        //-------------------------------------列表分页-------------------------------------

        putAds: function(row)
        {
            var Th = this;

            Th.loading = true;

            Th.$api.put('admin/advertiser/ad/'+row.id+'.json', row, function(data)
            {
                Th.$emit('message', 'success', '编辑成功');

                Th.loading = false;

            }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
        },
        
        exportAdvertiser: function(date) {          
            window.location.href="/admin/advertiser/export.json?s_date="+date;
        }

    },
}
</script>