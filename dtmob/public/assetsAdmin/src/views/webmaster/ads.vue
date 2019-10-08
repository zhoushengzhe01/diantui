<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">站长广告</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-input v-model="paramete.id" placeholder="广告ID"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-input v-model="paramete.webmaster_id" placeholder="站长ID"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-input v-model="paramete.username" placeholder="站长名字"></el-input>
                    </el-form-item>
                    <el-form-item>
                        <el-select v-model="paramete.position_id" placeholder="广告类型">
                            <el-option label="选择全部" value=""></el-option>
                            <el-option v-for="item in group.adtype" :key="item.key" :label="item.name" :value="item.id"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-select v-model="paramete.service_id" placeholder="客服查询">
                            <el-option label="全部客服" value=""></el-option>
                            <el-option v-for="item in group.services" :key="item.key" :label="item.nickname" :value="item.id"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-select v-model="paramete.flow_pool_id" placeholder="流量池查询">
                            <el-option label="选择全部" value=""></el-option>
                            <el-option v-for="item in group.flowpools" :key="item.key" :label="item.name" :value="item.id"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-select v-model="paramete.orderby" placeholder="广告排序">
                            <el-option label="计费率A-Z" value="in_advertiser_price:asc"></el-option>
                            <el-option label="计费率Z-A" value="in_advertiser_price:desc"></el-option>
                            <el-option label="流量值A-Z" value="wave:asc"></el-option>
                            <el-option label="流量值Z-A" value="wave:desc"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-select v-model="paramete.grade" placeholder="等级筛选">
                            <el-option label="一级" :value="1"></el-option>
                            <el-option label="二级" :value="2"></el-option>
                            <el-option label="三级" :value="3"></el-option>
                            <el-option label="四级" :value="4"></el-option>
                            <el-option label="五级" :value="5"></el-option>
                        </el-select>
                    </el-form-item>
                    <el-form-item>
                        <el-button type="success" @click="getWebmasterads">查询</el-button>
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
            </el-table>
        </div>

        <div class="box" v-loading="loading">
            <el-table :data="data.ads" style="width: 100%">
                <el-table-column
                    label="站长ID"
                    min-width="80">
                    <template slot-scope="scope">
                        站:{{scope.row.webmaster_id}}<br/>
                        广:{{scope.row.id}}
                    </template>
                </el-table-column>

                <el-table-column
                    label="站长名字"
                    min-width="120">
                    <template slot-scope="scope">
                        <router-link target="_blank" style="white-space: nowrap;" :to="'/admin/webmaster/'+scope.row.webmaster_id">{{scope.row.username}}</router-link><br/>
                        <span v-for="item in group.flowpools" :key="item.key" v-if="item.id==scope.row.flow_pool_id">{{item.name}}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="广告类型"
                    min-width="80">
                    <template slot-scope="scope">
                        <span v-if="scope.row.position_id == 11">
                            {{scope.row.position == 1 ? '顶':'底'}}-{{scope.row.position_name}}<br/>
                        </span>
                        <span v-else-if="scope.row.position_id == 13">
                            {{scope.row.position == 1 ? '左':'右'}}-图标<br/>
                        </span>
                        <span v-else>
                            {{scope.row.position_name}}<br/>
                        </span>
                        {{scope.row.billing}}&nbsp;
                        <span v-if="scope.row.is_stat=='1'">检</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="计费率"
                    min-width="80">
                    <template slot-scope="scope">
                        {{scope.row.out_advertiser_price}}/{{scope.row.in_advertiser_price}}<br/>
                        <span class="success" v-if="scope.row.is_disabled_advertiser_ad=='1'">限广告</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="暗层/强跳"
                    min-width="100">
                    <template slot-scope="scope">
                        {{scope.row.hid_height}}-{{scope.row.hid_height_chance}}%
                        <br/>
                        {{scope.row.compel_skip}}%
                    </template>
                </el-table-column>

                <el-table-column
                    label="强点"
                    min-width="100">
                    <template slot-scope="scope">
                        <span v-if="scope.row.compel_click=='1'">开启-{{scope.row.compel_chance}}%<br/>{{scope.row.compel_interval}}分钟</span>
                        <span v-if="scope.row.compel_click!='1'">关闭</span>
                    </template>
                </el-table-column>

                <el-table-column label="前天">
                    <el-table-column
                        label="总量"
                        min-width="180">
                        <template slot-scope="scope">
                            <span v-if="scope.row.day[0]">
                                <span class="info">点:</span>{{scope.row.day[0].pc_number}}&nbsp;&nbsp;
                                <span class="info">展:</span>{{scope.row.day[0].pv_number}}
                                <br/>
                                {{scope.row.day[0].money}}<span class="info">元</span>
                            </span>
                            <span v-if="!scope.row.day[0]" class="info">无数据</span>
                        </template>
                    </el-table-column>
                    <el-table-column
                        label="单价"
                        min-width="150">
                        <template slot-scope="scope">
                            <span style="color:#f56c6c" v-if="scope.row.day[0]">
                                {{ parseInt(scope.row.day[0].money / scope.row.day[0].ip_number * 100000)/10 }}<span class="info">/IP</span>&nbsp;&nbsp;
                                {{ parseInt(scope.row.day[0].money / scope.row.day[0].pv_number * 10000)/10 }}<span class="info">/PV</span>
                                <br/>
                                {{ parseInt(scope.row.day[0].pc_number / scope.row.day[0].pv_number * 10000)/100 }}<span class="info">%点</span>
                            </span>
                            <span v-if="!scope.row.day[0]" class="info">无数据</span>
                        </template>
                    </el-table-column>
                </el-table-column>
                
                <el-table-column label="昨天">
                    <el-table-column
                        label="总量"
                        min-width="180">
                        <template slot-scope="scope">
                            <span v-if="scope.row.day[1]">
                                <span class="info">点:</span>{{scope.row.day[1].pc_number}}&nbsp;&nbsp;
                                <span class="info">展:</span>{{scope.row.day[1].pv_number}}
                                <br/>
                                {{scope.row.day[1].money}}<span class="info">元</span>
                            </span>
                            <span v-if="!scope.row.day[1]" class="info">无数据</span>
                        </template>
                    </el-table-column>
                    <el-table-column
                        label="单价"
                        min-width="150">
                        <template slot-scope="scope">
                            <span style="color:#f56c6c" v-if="scope.row.day[1]">
                            {{ parseInt(scope.row.day[1].money / scope.row.day[1].ip_number * 100000)/10 }}<span class="info">/IP</span>&nbsp;&nbsp;
                            {{ parseInt(scope.row.day[1].money / scope.row.day[1].pv_number * 10000)/10 }}<span class="info">/PV</span>
                            <br/>
                            {{ parseInt(scope.row.day[1].pc_number / scope.row.day[1].pv_number * 10000)/100 }}<span class="info">%点</span>
                            </span>
                            <span v-if="!scope.row.day[1]" class="info">无数据</span>
                        </template>
                    </el-table-column>
                </el-table-column>

                <el-table-column
                    label="自动调控"
                    min-width="80">
                    <template slot-scope="scope">
                        <span class="success" v-if="scope.row.is_auto_price=='1'">开启</span>
                        <span class="info" v-if="scope.row.is_auto_price=='0'">关闭</span>
                        <br/>
                        <span v-if="scope.row.is_auto_price=='1'">{{scope.row.target_price}}<span class="info">/万IP</span></span>
                    </template>
                </el-table-column>
                
                <el-table-column label="今天">
                    <el-table-column
                        label="总量"
                        min-width="180">
                        <template slot-scope="scope">
                            <span v-if="scope.row.day[2]">
                                <span class="info">点:</span>{{scope.row.day[2].pc_number}}&nbsp;&nbsp;
                                <span class="info">展:</span>{{scope.row.day[2].pv_number}}
                                <br/>
                                {{scope.row.day[2].money}}<span class="info">元</span>
                            </span>
                            <span v-if="!scope.row.day[2]" class="info">无数据</span>
                        </template>
                    </el-table-column>
                    <el-table-column
                        label="单价"
                        min-width="160">
                        <template slot-scope="scope">
                            <span style="color:#f56c6c" v-if="scope.row.day[2]">
                                {{ parseInt(scope.row.day[2].money / scope.row.day[2].ip_number * 100000)/10 }}<span class="info">/IP</span>&nbsp;&nbsp;
                                {{ parseInt(scope.row.day[2].money / scope.row.day[2].pv_number * 10000)/10 }}<span class="info">/PV</span>
                                <br/>
                                {{ parseInt(scope.row.day[2].pc_number / scope.row.day[2].pv_number * 10000)/100 }}<span class="info">%</span>
                                <span class="info" style="color: rgb(245, 108, 108)" v-if="scope.row.wave > 10">↑{{Math.abs(scope.row.wave)}}%</span>
                                <span class="info" style="color:#67c23a" v-if="scope.row.wave < -10">↓{{Math.abs(scope.row.wave)}}%</span>
                                <span class="info" style="color:#cccccc" v-if="scope.row.wave <= 10 && scope.row.wave >= -10">↓{{Math.abs(scope.row.wave)}}%</span>
                            </span>
                            <span v-if="!scope.row.day[2]" class="info">无数据</span>
                        </template>
                    </el-table-column>
                </el-table-column>

                <el-table-column
                    label="客服"
                    min-width="60">
                    <template slot-scope="scope">
                        <span v-for="item in group.alliance_agents" :key="item.key" v-if="item.id==scope.row.alliance_agent_id">{{item.name}}</span>
                        <br/>
                        <span v-for="item in group.services" :key="item.key" v-if="item.id==scope.row.service_id">{{item.nickname}}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="等级"
                    min-width="60">
                    <template slot-scope="scope">
                        {{scope.row.grade}} 级
                    </template>
                </el-table-column>

                <el-table-column
                    label="IP评估"
                    min-width="100">
                    <template slot-scope="scope">
                        {{scope.row.ip_point_time}}
                        <br>
                        {{scope.row.ip_point}} 分
                    </template>
                </el-table-column>

                <el-table-column
                    label="状态"
                    min-width="80">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.state=='1'" type="success" size="small">正常</el-tag>
                        <el-tag v-if="scope.row.state=='2'" type="info" size="small">被封</el-tag>
                    </template>
                </el-table-column>

                <el-table-column
                    fixed="right"
                    label="操作"
                    width="135">
                    <template slot-scope="scope">
                        <router-link target="_blank" :to="'/admin/webmaster/ad/'+scope.row.id">编辑</router-link>
                        <el-dropdown>
                            <span class="el-dropdown-link">更多操作<i class="el-icon-arrow-down el-icon--right"></i></span>
                            <el-dropdown-menu slot="dropdown">
                                <el-dropdown-item><router-link target="_blank" :to="'/admin/webmaster/earningday/'+scope.row.id">每天数据</router-link></el-dropdown-item>
                                <el-dropdown-item><router-link target="_blank" :to="'/admin/webmaster/earningclick/'+scope.row.id">用户点击</router-link></el-dropdown-item>
                            </el-dropdown-menu>
                        </el-dropdown>
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
                limit: 20,
                webmaster_id: this.$api.getQueryString('webmaster_id'),
            },
            data: {},
        };
    },
    created: function () {
        
        this.group.page = '/admin/webmaster/ads';

        this.getWebmasterads();
    },
    methods:{
        //-------------------------------------列表分页-------------------------------------
        getWebmasterads: function()
        {
            var Th = this;
            Th.loading = true;
            Th.$api.get('admin/webmaster/ads.json', Th.paramete, function(data)
            {
                Th.data = data;

                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getWebmasterads();
        },
        //-------------------------------------列表分页-------------------------------------

    },
}
</script>