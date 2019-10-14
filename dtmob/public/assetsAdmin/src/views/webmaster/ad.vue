<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">添加素材</h3>
            <div class="search-box">
                <el-button type="success" @click="getPriceLogs" size="mini">修改日志</el-button>
            </div>
        </div>

        <div class="box" v-loading="loading" style="padding: 24px;">
            <el-form ref="form" :model="data.webmasterad" label-width="80px" size="medium" style="max-width: 800px;" label-position="right">
                <el-form-item label="广告名称">
                    <el-input v-model="data.webmasterad.name"></el-input>
                </el-form-item>

                <el-form-item label="广告类型">
                    <el-radio-group v-model="data.webmasterad.position_id">
                        <el-radio v-for="item in group.adtype" :key="item.key" :label="item.id">{{item.name}}</el-radio>
                    </el-radio-group>
                </el-form-item>

                <el-form-item label="广告位置" v-if="isShow(data.webmasterad.position_id, [11])">
                    <el-radio-group v-model="data.webmasterad.position">
                        <el-radio label="1">顶部</el-radio>
                        <el-radio label="2">底部</el-radio>
                    </el-radio-group>
                </el-form-item>

                <el-form-item label="广告位置" v-if="isShow(data.webmasterad.position_id, [13])">
                    <el-radio-group v-model="data.webmasterad.position">
                        <el-radio label="1">左边</el-radio>
                        <el-radio label="2">右边</el-radio>
                    </el-radio-group>
                </el-form-item>

                <el-form-item label="计费方式">
                    <el-radio-group v-model="data.webmasterad.billing">
                        <el-radio label="CPC"></el-radio>
                        <el-radio label="CPM"></el-radio>
                    </el-radio-group>
                </el-form-item>

                <el-form-item label="广告比例" v-if="isShow(data.webmasterad.position_id, [11,12,13])">
                    <el-slider show-input="true" v-model="data.webmasterad.ad_ratio"></el-slider>
                </el-form-item>
                
                <!--自动调控-->
                <el-row class="box-item">
                    <div class="box-title">
                        <div class="title-item">自动调控模块</div>
                    </div>
                    <el-row v-if="isShow(data.webmasterad.position_id, [11,12,13,14])">
                        <el-col :span="24">
                            <el-form-item label="自动调节">
                                <el-radio-group v-model="data.webmasterad.is_auto_price" v-if="group.user.department_id!=3">
                                    <el-radio label="0">关闭</el-radio>
                                    <el-radio label="1">启动</el-radio>
                                </el-radio-group>
                                <el-radio-group v-model="data.webmasterad.is_auto_price" v-if="group.user.department_id==3" disabled>
                                    <el-radio label="0">关闭</el-radio>
                                    <el-radio label="1">启动</el-radio>
                                </el-radio-group>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="目标万IP" v-if="group.user.department_id!=3">
                                <el-input v-model="data.webmasterad.target_price" v-if="data.webmasterad.is_auto_price=='1'"></el-input>
                                <el-input v-model="data.webmasterad.target_price" v-if="data.webmasterad.is_auto_price=='0'" disabled></el-input>
                            </el-form-item>
                            <el-form-item label="目标万IP" v-if="group.user.department_id==3">
                                <el-input v-model="data.webmasterad.target_price" disabled></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="初始计费" v-if="group.user.department_id!=3">
                                <el-input v-model="data.webmasterad.init_price" v-if="data.webmasterad.is_auto_price=='1'"></el-input>
                                <el-input v-model="data.webmasterad.init_price" v-if="data.webmasterad.is_auto_price=='0'" disabled></el-input>
                            </el-form-item>
                            <el-form-item label="初始计费" v-if="group.user.department_id==3">
                                <el-input v-model="data.webmasterad.init_price" disabled></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-row v-if="isShow(data.webmasterad.position_id, [11,13])">
                        <el-col :span="12">
                            <el-form-item label="返回跳转">
                                <el-select v-model="data.webmasterad.return_skip" v-if="group.user.department_id!=3">
                                    <el-option label="不做处理" :value="0"></el-option>
                                    <el-option label="禁止跳转" :value="1"></el-option>
                                    <el-option label="广告返回跳转" :value="2"></el-option>
                                    <el-option label="全站返回跳转" :value="3"></el-option>
                                </el-select>
                                <el-select v-model="data.webmasterad.return_skip" v-if="group.user.department_id==3" disabled>
                                    <el-option label="不做处理" :value="0"></el-option>
                                    <el-option label="禁止跳转" :value="1"></el-option>
                                    <el-option label="广告返回跳转" :value="2"></el-option>
                                    <el-option label="全站返回跳转" :value="3"></el-option>
                                </el-select>
                            </el-form-item>    
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="返回次数" v-if="data.webmasterad.return_skip==2 || data.webmasterad.return_skip==3">
                                <el-input v-model="data.webmasterad.return_num" v-if="group.user.department_id!=3"></el-input>
                                <el-input v-model="data.webmasterad.return_num" v-if="group.user.department_id==3" disabled></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
                </el-row>

                <!--返回模块-->
                <el-row class="box-item" v-if="isShow(data.webmasterad.position_id, [11,13,14])">
                    <div class="box-title">
                        <div class="title-item">返回跳转模块</div>
                    </div>
                    <el-col :span="8">
                        <el-form-item label="直返跳转">
                            <el-radio-group v-model="data.webmasterad.click_return.state">
                                <el-radio :label="1">开启</el-radio>
                                <el-radio :label="0">关闭</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="概率">
                            <el-input v-model="data.webmasterad.click_return.chance" :disabled="data.webmasterad.click_return.state!='1'"><template slot="append">%</template></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="计费率">
                            <el-input v-model="data.webmasterad.return_chance"><template slot="append">%</template></el-input>
                        </el-form-item>
                    </el-col>
                    
                    
                    <el-col :span="8">
                        <el-form-item label="自反跳转">
                            <el-radio-group v-model="data.webmasterad.own_return.state">
                                <el-radio :label="1">开启</el-radio>
                                <el-radio :label="0">关闭</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="概率">
                            <el-input v-model="data.webmasterad.own_return.chance" :disabled="data.webmasterad.own_return.state!='1'"><template slot="append">%</template></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="次数">
                            <el-input v-model="data.webmasterad.own_return.number" :disabled="data.webmasterad.own_return.state!='1'"></el-input>
                        </el-form-item>
                    </el-col>

                    <el-col :span="8">
                        <el-form-item label="它返跳转">
                            <el-radio-group v-model="data.webmasterad.other_return.state">
                                <el-radio :label="1">开启</el-radio>
                                <el-radio :label="0">关闭</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="概率">
                            <el-input v-model="data.webmasterad.other_return.chance" :disabled="data.webmasterad.other_return.state!='1'"><template slot="append">%</template></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="8">
                        <el-form-item label="次数">
                            <el-input v-model="data.webmasterad.other_return.number" :disabled="data.webmasterad.other_return.state!='1'"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                
                <!--计费率模块-->
                <el-row class="box-item" v-if="isShow(data.webmasterad.position_id, [11,12,13,14])">
                    <div class="box-title">
                        <div class="title-item">广告计费模块</div>
                    </div>
                    <el-col :span="12">
                        <el-form-item label="广告计费">
                            <el-input v-model="data.webmasterad.in_advertiser_price" v-if="group.user.department_id!=3 && data.webmasterad.is_auto_price=='0'"><template slot="append">%</template></el-input>
                            <el-input v-model="data.webmasterad.in_advertiser_price" v-if="group.user.department_id==3 || data.webmasterad.is_auto_price=='1'" disabled><template slot="append">%</template></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="站长计费">
                            <el-input v-model="data.webmasterad.out_advertiser_price" v-if="group.user.department_id!=3 && data.webmasterad.is_auto_price=='0'"><template slot="append">%</template></el-input>
                            <el-input v-model="data.webmasterad.out_advertiser_price" v-if="group.user.department_id==3 || data.webmasterad.is_auto_price=='1'" disabled ><template slot="append">%</template></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="联盟计费">
                            <el-input v-model="data.webmasterad.out_alliance_price" v-if="group.user.department_id!=3 && data.webmasterad.is_auto_price=='0'"><template slot="append">%</template></el-input>
                            <el-input v-model="data.webmasterad.out_alliance_price" v-if="group.user.department_id==3 || data.webmasterad.is_auto_price=='1'" disabled><template slot="append">%</template></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12" v-if="isShow(data.webmasterad.position_id, [11,12,13])">
                        <el-form-item label="假关闭率">
                            <el-input v-model="data.webmasterad.false_close"><template slot="append">%</template></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                
                <!--暗层模块-->
                <el-row class="box-item" v-if="isShow(data.webmasterad.position_id, [11,12,13,14])">
                    <div class="box-title">
                        <div class="title-item">广告暗层模块</div>
                    </div>
                    <el-row class="box-item" v-if="isShow(data.webmasterad.position_id, [11,13])">
                        <el-col :span="12">
                            <el-form-item label="暗层高度">
                                <el-input v-model="data.webmasterad.hid_height"></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="暗层计费">
                                <el-input v-model="data.webmasterad.hid_height_chance"><template slot="append">%</template></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>

                    <el-row class="box-item" v-if="isShow(data.webmasterad.position_id, [12])">
                        <el-col :span="12">
                            <el-form-item label="上面暗层">
                                <el-input v-model="data.webmasterad.top_hid_height" v-if="group.user.department_id!=3"></el-input>
                                <el-input v-model="data.webmasterad.top_hid_height" v-if="group.user.department_id==3" disabled></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="暗层计费">
                                <el-input v-model="data.webmasterad.hid_height_chance" v-if="group.user.department_id!=3"><template slot="append">%</template></el-input>
                                <el-input v-model="data.webmasterad.hid_height_chance" v-if="group.user.department_id==3" disabled><template slot="append">%</template></el-input>
                            </el-form-item>
                        </el-col>
                        <el-col :span="12">
                            <el-form-item label="下面暗层">
                                <el-input v-model="data.webmasterad.bot_hid_height" v-if="group.user.department_id!=3"></el-input>
                                <el-input v-model="data.webmasterad.bot_hid_height" v-if="group.user.department_id==3" disabled></el-input>
                            </el-form-item>
                        </el-col>
                    </el-row>
                    <el-form-item label="暗层屏蔽">
                        <el-input v-model="data.webmasterad.hid_height_disabled_region" placeholder="暗层屏蔽地区，格式：浙江,湖北"></el-input>
                    </el-form-item>
                </el-row>
                

                <!--特效屏蔽模块-->
                <el-row class="box-item" v-if="isShow(data.webmasterad.position_id, [11,12,13])">
                    <div class="box-title">
                        <div class="title-item">JS特效模块</div>
                    </div>
                    <el-col :span="12">
                        <el-form-item label="JS 特效">
                            <el-select v-model="data.webmasterad.js_effects">
                                <el-option label="开启" value="1"></el-option>
                                <el-option label="关闭" value="0"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="顶部位置" v-if="isShow(data.webmasterad.position_id, [13])">
                            <el-input v-model="data.webmasterad.icons_top" placeholder="图标顶部位置 35%"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="24" >
                        <el-form-item label="特效屏蔽">
                            <el-input v-model="data.webmasterad.js_effects_disabled_region"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>

                <!--强点模块-->
                <el-row class="box-item" v-if="isShow(data.webmasterad.position_id, [11,13,14])">
                    <div class="box-title">
                        <div class="title-item">强点模块</div>
                    </div>
                    <el-col :span="24">
                        <el-form-item label="开启强点">
                            <el-radio-group v-model="data.webmasterad.compel_click">
                                <el-radio label="0">关闭</el-radio>
                                <el-radio label="1">开启</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-collapse-transition>
                        <el-form-item label="间隔时间" >
                            <el-input v-model="data.webmasterad.compel_interval" placeholder="间隔时间以小时为单位"></el-input>
                        </el-form-item>
                        </el-collapse-transition>
                    </el-col>
                    <el-col :span="12">
                        <el-collapse-transition>
                        <el-form-item label="出现概率" >
                            <el-input v-model="data.webmasterad.compel_chance" placeholder="强点出现概率"><template slot="append">%</template></el-input>
                        </el-form-item>
                        </el-collapse-transition>
                    </el-col>
                    <el-col :span="24">
                        <el-collapse-transition>
                        <el-form-item label="屏蔽地区">
                            <el-input v-model="data.webmasterad.click_disabled_region" placeholder="屏蔽强点地区，格式：浙江,湖北"></el-input>
                        </el-form-item>
                        </el-collapse-transition>
                    </el-col>
                </el-row>

                <!--展示屏蔽模块-->
                <template v-if="isShow(data.webmasterad.position_id, [11,13])">
                <el-row class="box-item">
                    <div class="box-title">
                        <div class="title-item">展示屏蔽模块</div>
                    </div>
                    <el-col :span="12">
                        <el-form-item label="展示屏蔽">
                            <el-radio-group v-model="data.webmasterad.is_ad_disabled">
                                <el-radio label="0">关闭</el-radio>
                                <el-radio label="1">开启</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="屏蔽地区">
                            <el-input v-model="data.webmasterad.ad_disabled_region" placeholder="展示屏蔽地区，格式：浙江,湖北"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>
                </template>

                <!--强跳模块-->
                <el-row class="box-item" v-if="isShow(data.webmasterad.position_id, [11,13,14])">
                    <div class="box-title">
                        <div class="title-item">强跳模块</div>
                    </div>
                    <el-col :span="12">
                        <el-form-item label="强制跳转">
                            <el-input v-model="data.webmasterad.compel_skip"><template slot="append">%</template></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="屏蔽地区">
                            <el-input v-model="data.webmasterad.skip_disabled_region" placeholder="强制跳转屏蔽地区，格式：浙江,湖北"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>

                <!--支口令模块-->
                <el-row class="box-item" v-if="isShow(data.webmasterad.position_id, [11,13,14])">
                    <div class="box-title">
                        <div class="title-item">支口令模块</div>
                    </div>
                    <el-col :span="12">
                        <el-form-item label="口令概率">
                            <el-input v-model="data.webmasterad.zhikouling"><template slot="append">%</template></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="屏蔽地区">
                            <el-input v-model="data.webmasterad.zhikouling_disabled_region" placeholder="强制跳转屏蔽地区，格式：浙江,湖北"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>

                <!--屏蔽广告模块-->
                <el-row class="box-item" v-if="isShow(data.webmasterad.position_id, [11,12,13,14])">
                    <div class="box-title">
                        <div class="title-item">屏蔽广告模块</div>
                    </div>
                    <el-col :span="12">
                        <el-form-item label="屏蔽广告">
                            <el-radio-group v-model="data.webmasterad.is_disabled_advertiser_ad">
                                <el-radio label="0">关闭</el-radio>
                                <el-radio label="1">开启</el-radio>
                            </el-radio-group>
                        </el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-collapse-transition>
                        <el-form-item label="屏蔽广告" >
                            <el-input v-model="data.webmasterad.disabled_advertiser_ad" placeholder="屏蔽广告，格式：1024|1036|2015"></el-input>
                        </el-form-item>
                        </el-collapse-transition>
                    </el-col>
                    <el-col :span="24">
                        <el-collapse-transition>
                        <el-form-item label="屏蔽分类" >
                            <el-select
                                style="width:100%"
                                v-model="data.webmasterad.disabled_ad_category"
                                multiple
                                filterable
                                allow-create
                                default-first-option
                                placeholder="请选择分类标签">
                                <el-option
                                v-for="item in group.adcategorys"
                                :key="item.id"
                                :label="item.name"
                                :value="item.id">
                                </el-option>
                            </el-select>
                        </el-form-item>
                        </el-collapse-transition>
                    </el-col>
                </el-row>
                
                <!--其他信息设置-->
                <el-row class="box-item">
                    <div class="box-title">
                        <div class="title-item">其他信息</div>
                    </div>
                    <el-col :span="12">
                        <el-form-item label="统计比例">
                            <el-input v-model="data.webmasterad.statis_code_ratio"><template slot="append">%</template></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="统计代码">
                            <el-input v-model="data.webmasterad.statis_code" placeholder="CNZZ统计代码"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="24">
                        <el-form-item label="CSS样式">
                            <el-input v-model="data.webmasterad.style" placeholder="CSS样式"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="样式类型">
                            <el-select v-model="data.webmasterad.style_type">
                                <el-option label="默认样式" :value="1"></el-option>
                                <el-option label="样式一" :value="2"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="广告尺寸">
                            <el-select v-model="data.webmasterad.ad_size">
                                <el-option label="640*200" value="1"></el-option>
                                <el-option label="640*150" value="2"></el-option>
                                <el-option label="640*100" value="3"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="广告状态">
                            <el-select v-model="data.webmasterad.state">
                                <el-option label="开启" value="1"></el-option>
                                <el-option label="停止" value="2"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="是否解析" v-if="data.webmasterad.position_id=='11'">
                            <el-select v-model="data.webmasterad.is_jiexi">
                                <el-option label="是" value="1"></el-option>
                                <el-option label="否" value="0"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>  
                <el-form-item>
                    <el-button type="success" @click="putWebmasterad">确定</el-button>
                    <el-button>取消</el-button>
                </el-form-item>
            </el-form>
        </div>


        <div class="title-box">
            <h3 class="title">备注列表</h3>
            <div class="search-box">
                <el-button type="success" @click="noteshow = true" size="mini">添加备注</el-button>
            </div>
        </div>
        <div class="box" v-loading="noteloading">

            <el-table :data="notedata.notes" style="width: 100%">
                <el-table-column
                    label="备注人"
                    min-width="100">
                    <template slot-scope="scope">
                        {{scope.row.username}}<br/>
                        广告id：{{scope.row.webmaster_ad_id}}
                    </template>
                </el-table-column>

                <el-table-column
                    label="等级修改"
                    min-width="100">
                    <template slot-scope="scope">
                        <span v-if="scope.row.grade>0">{{scope.row.grade}}级</span>
                        <span v-if="scope.row.grade<=0">无修改</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="备注内容"
                    min-width="700">
                    <template slot-scope="scope">
                        {{scope.row.note}}<br/>
                        <span class="info" v-if="scope.row.webmaster_ad_id && scope.row.webmaster_ad_id!=data.webmasterad.id">
                            计费:{{scope.row.in_advertiser_price}}/{{scope.row.out_advertiser_price}}
                            调控:{{scope.row.is_auto_price}}/{{scope.row.target_price}}
                            关闭:{{scope.row.false_close}}%
                            高度:{{scope.row.hid_height}}/{{scope.row.hid_height_chance}}
                            强跳:{{scope.row.compel_skip}}
                        </span>
                        <span class="success" v-if="scope.row.webmaster_ad_id && scope.row.webmaster_ad_id==data.webmasterad.id">
                            计费:{{scope.row.in_advertiser_price}}/{{scope.row.out_advertiser_price}}
                            调控:{{scope.row.is_auto_price}}/{{scope.row.target_price}}
                            关闭:{{scope.row.false_close}}%
                            高度:{{scope.row.hid_height}}/{{scope.row.hid_height_chance}}
                            强跳:{{scope.row.compel_skip}}
                        </span>
                        &nbsp;
                    </template>
                </el-table-column>
            
                <el-table-column
                    prop="created_at"
                    label="时间"
                    min-width="180">
                </el-table-column>

            </el-table>


            <div class="page-box">
                <el-pagination
                @current-change="pageChange"
                layout="total, prev, pager, next"
                :page-size="noteparamete.limit"
                :total="notedata.count">
                </el-pagination>
            </div>
            
            
        </div>

        <!--添加备注-->
        <el-dialog title="添加备注" :visible.sync="noteshow" class="small_dialog">
            <el-form ref="form" label-position="top" :model="noteitem" label-width="80px" size="small" v-loading="noteloading">
                <el-form-item label="等级修改">
                    <el-select v-model="noteitem.grade" style="width:100%">
                        <el-option label="不修改" :value="0"></el-option>
                        <el-option label="一级" :value="1"></el-option>
                        <el-option label="二级" :value="2"></el-option>
                        <el-option label="三级" :value="3"></el-option>
                        <el-option label="四级" :value="4"></el-option>
                        <el-option label="五级" :value="5"></el-option>
                    </el-select>
                </el-form-item>
                <el-form-item label="备注内容">
                    <el-input type="textarea" :rows="6" v-model="noteitem.note"></el-input>
                </el-form-item>
            </el-form>
            <div slot="footer" class="dialog-footer">
                <el-button @click="noteshow = false" size="small">取 消</el-button>
                <el-button type="success" @click="postNode" size="small">确 定</el-button>
            </div>
        </el-dialog>


        <!--修改日志-->
        <el-dialog title="修改日志" :visible.sync="pricelogshow" class="small_dialog">
            <el-table style="width: 100%" :data="pricelogdata.data" v-loading="pricelogloading">
                <el-table-column
                    label="操作人"
                    min-width="80">
                    <template slot-scope="scope">
                        <span style="white-space: nowrap;">{{scope.row.username}}</span>
                    </template>
                </el-table-column>
                <el-table-column
                    label="自动调节">
                    <template slot-scope="scope">
                        <span v-if="scope.row.is_auto_price == 0">关闭</span>
                        <span v-if="scope.row.is_auto_price == 1">启动</span>
                        {{scope.row.target_price}}/万
                    </template>
                </el-table-column>
                <el-table-column
                    label="计费率">
                    <template slot-scope="scope">
                        {{scope.row.in_advertiser_price}}/{{scope.row.out_advertiser_price}}
                    </template>
                </el-table-column>
                <el-table-column
                    label="暗层计费">
                    <template slot-scope="scope">
                        {{scope.row.hid_height_chance}}/{{scope.row.hid_height_chance}}
                    </template>
                </el-table-column>
                <el-table-column
                    label="时间"
                    min-width="120">
                    <template slot-scope="scope">
                        <span style="white-space: nowrap;" :title="scope.row.created_at">{{scope.row.created_at}}</span>
                    </template>
                </el-table-column>
            </el-table>

            <div class="page-box">
                <el-pagination
                @current-change="priceLogPageChange"
                layout="total, prev, pager, next"
                :page-size="pricelogparamete.limit"
                :total="pricelogdata.count">
                </el-pagination>
            </div>
        </el-dialog>

    </div>
</template>
<script>
export default {
    name: 'webmasterad',
    data: function () { 
        return {
            group: Group,
            loading: true,
            id: this.$route.params.id,
            data: {
                webmasterad: {}
            },
            
            pricelogshow: false,
            pricelogloading: false,
            pricelogparamete: {
                offset: 0,
                limit: 20,
            },
            pricelogdata: {},

            noteloading: false,
            noteparamete: {
                offset: 0,
                limit: 10,
            },
            notedata: {},
            noteitem: {'grade':0},
            noteshow: false,

        };
    },
    created: function () {
        this.group.page = '/admin/webmaster/ads';
        this.getWebmasterad();
    },
    methods:{
        isShow: function(id,adtype)
        {
            for(var i in adtype){
                if(adtype[i]==id){
                    return true;
                }
            }
            return false;
        },
        getWebmasterad: function()
        {
            var Th = this;
            Th.loading = true;
            Th.$api.get('admin/webmaster/ad/'+Th.id+'.json', {}, function(data)
            {
                Th.data = data;
                Th.loading = false;
                Th.getNodes(Th.data.webmasterad.webmaster_id);

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },

        putWebmasterad: function()
        {
            var Th = this;
            
            Th.loading = true;
            
            Th.$api.put('admin/webmaster/ad/'+Th.id+'.json', Th.data.webmasterad, function(data)
            {
                Th.loading = false;

                Th.$emit('message', 'success', '修改成功，5 秒之后关闭窗口');
                
                setTimeout(function(){
                    
                    window.close();

                }, 5000);
            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },

        getNodes: function(id){
            var Th = this;
            Th.noteloading = true;
            Th.$api.get('admin/webmaster/notes/'+id+'.json', Th.noteparamete, function(data)
            {
                Th.notedata = data;
                Th.noteloading = false;

            }, function(type, message){ Th.noteloading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.noteparamete.offset = parseInt(val-1) * parseInt(this.noteparamete.limit);
            this.getNodes(Th.data.webmasterad.webmaster_id);
        },
        postNode: function(){
            var Th = this;
            Th.noteitem.webmaster_ad_id = Th.id;
            Th.$api.post('admin/webmaster/note/'+Th.data.webmasterad.webmaster_id+'.json', Th.noteitem, function(data)
            {
                Th.$emit('message', 'success', '备注成功');
                Th.getNodes(Th.data.webmasterad.webmaster_id);
                Th.noteshow = false;

            }, function(type, message){ Th.noteshow = false; Th.$emit('message', type, message); });
        },

        //获取价格日志
        getPriceLogs: function(){
            var Th = this;
            Th.pricelogloading = true;
            Th.pricelogshow = true;
            Th.$api.get('admin/webmaster/pricelogs/'+Th.id+'.json', Th.logparamete, function(data)
            {
                Th.pricelogdata = data;
                Th.pricelogloading = false;

            }, function(type, message){ Th.pricelogloading = false; Th.$emit('message', type, message); });

        },
        priceLogPageChange: function(val) {
            this.pricelogparamete.offset = parseInt(val-1) * parseInt(this.pricelogparamete.limit);
            this.getPriceLogs();
        },
    },
}
</script>