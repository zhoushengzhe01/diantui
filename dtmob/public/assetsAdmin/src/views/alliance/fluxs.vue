<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">联盟流量</h3>
            <div class="search-box">
                <el-form :inline="true" :model="paramete" class="demo-form-inline" size="mini">
                    <el-form-item>
                        <el-input v-model="paramete.name" placeholder="联盟名称"></el-input>
                    </el-form-item>

                    <el-form-item>
                        <el-select v-model="paramete.myad_id" placeholder="切换广告">
                            <el-option label="全部数据" :value="0"></el-option>
                            <el-option v-for="item in group.adtype" :key="item.key" :label="item.name" :value="item.id"></el-option>
                        </el-select>
                    </el-form-item>

                    <el-form-item>
                        <el-button type="success" @click="getFluxs">查询</el-button>
                    </el-form-item>

                    <el-form-item>
                        <el-button type="success" @click="getFlux('')">添加</el-button>
                    </el-form-item>
                </el-form>
            </div>
        </div>

        <div class="box" v-loading="loading">

            <el-table :data="data.fluxs" style="width: 100%">
                <el-table-column
                    label="名称"
                    min-width="80">
                    <template slot-scope="scope">
                        <a :href="scope.row.url" target="_blank"><el-button type="text" size="medium">{{scope.row.name}}</el-button></a>
                    </template>
                </el-table-column>

                <el-table-column
                    label="联盟"
                    min-width="100">
                    <template slot-scope="scope">
                        <span v-for="item in group.alliance_agents" :key="item.key" v-if="item.id==scope.row.alliance_agent_id">{{item.name}}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    label="价格"
                    min-width="80">
                    <template slot-scope="scope">
                        {{scope.row.price}} 元
                    </template>
                </el-table-column>

                <el-table-column
                    label="计费次数"
                    min-width="60">
                    <template slot-scope="scope">
                        {{scope.row.record_num}} 次
                    </template>
                </el-table-column>

                <el-table-column
                    label="广告类型"
                    min-width="80">
                    <template slot-scope="scope">
                        <span v-for="item in group.adtype" :key="item.key" v-if="item.id==scope.row.adstype_id">{{item.name}}</span>
                    </template>
                </el-table-column>

                <el-table-column
                    prop="account"
                    label="账号"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    prop="password"
                    label="密码"
                    min-width="100">
                </el-table-column>

                <el-table-column
                    label="推广密钥"
                    min-width="150">
                    <template slot-scope="scope">
                        {{scope.row.sign}}
                    </template>
                </el-table-column>

                <el-table-column
                    label="连接"
                    min-width="100">
                    <template slot-scope="scope">
                        <el-tooltip class="item" effect="dark" :content="'http://flux.zhxpg.cn/tuiguang?sign='+scope.row.sign" placement="bottom-end">
                            <el-button size="mini">推广连接</el-button>
                        </el-tooltip>
                    </template>
                </el-table-column>

                <el-table-column
                    label="状态"
                    min-width="60">
                    <template slot-scope="scope">
                        <el-tag v-if="scope.row.state=='1'" type="success" size="small">使用</el-tag>
                        <el-tag v-if="scope.row.state=='2'" type="info" size="small">停止</el-tag>
                    </template>
                </el-table-column>

                <el-table-column
                    v-bind:router="true"
                    fixed="right"
                    label="操作"
                    width="60">
                    <template slot-scope="scope">
                        <el-button type="text" size="medium" @click="getFlux(scope.row)">编辑</el-button>
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

        <!--弹窗编辑-->
        <el-dialog title="添加/编辑" :visible.sync="show" class="small_dialog">

            <el-form :model="flux" label-width="80px" size="medium" v-loading="loadingItem">

                <el-form-item label="联盟名称">
                    <el-input v-model="flux.name"></el-input>
                </el-form-item>

                <el-form-item label="登陆地址">
                    <el-input v-model="flux.url"></el-input>
                </el-form-item>

                <el-row>
                    <el-col :span="12">
                        <el-form-item label="登陆账号">
                            <el-input v-model="flux.account"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="登陆密码">
                            <el-input v-model="flux.password"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-form-item label="推广密钥">
                    <el-input v-model="flux.sign"></el-input>
                </el-form-item>

                <el-row>
                    <el-col :span="12">
                        <el-form-item label="广告ID">
                            <el-input v-model="flux.advertiser_ad_id"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="计费次数">
                            <el-input v-model="flux.record_num"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row>
                    <el-col :span="12">
                        <el-form-item label="支出价格">
                            <el-input v-model="flux.out_price_ratio"></el-input>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="消耗价格">
                            <el-input v-model="flux.in_price_ratio"></el-input>
                        </el-form-item>
                    </el-col>
                </el-row>

                <el-row>
                    <el-col :span="12">
                        <el-form-item label="广告类型">
                            <el-select v-model="flux.adstype_id" style="width:100%">
                                <el-option v-for="item in group.adtype" :key="item.key" :label="item.name" :value="item.id"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                    <el-col :span="12">
                        <el-form-item label="状态">
                            <el-select v-model="flux.state" style="width:100%">
                                <el-option label="开启" value="1"></el-option>
                                <el-option label="关闭" value="2"></el-option>
                            </el-select>
                        </el-form-item>
                    </el-col>
                </el-row>
                
            </el-form>
            
            <div slot="footer" class="dialog-footer">
                <el-button @click="show = false">取 消</el-button>
                <el-button type="success" @click="postFlux">确 定</el-button>
            </div>
        </el-dialog>

    </div>
</template>
<script>
export default {
    name: 'recharges',
    data: function () { 
        return {
            
            group: Group,
            loading: true,
            loadingItem: true,
            show: false,
            paramete: {
                offset: 0,
                limit: 15,
            },
            
            flux: {},
            data: {},
        };
    },
    created: function () {
        
        this.group.page = window.location.pathname;
        
        this.getFluxs();
    },
    methods:{
        getFluxs: function()
        {
            var Th = this;
            
            Th.loading = true;
            
            Th.$api.get('admin/alliance/fluxs.json', Th.paramete, function(data)
            {
                Th.data = data;
                
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getFluxs();
        },


        getFlux: function(row)
        {
            var Th = this;

            Th.show = true;

            Th.loadingItem = true;

            if(row)
            {
                Th.flux = row;

                Th.loadingItem = false;
            }
            else
            {
                Th.flux = {};

                Th.loadingItem = false;
            }
            
        },

        postFlux: function()
        {
            var Th = this;

            Th.loadingItem = true;

            if(Th.flux.id)
            {
                Th.$api.put('admin/alliance/flux/'+Th.flux.id+'.json', Th.flux, function(data)
                {   
                    Th.loadingItem = false;

                    Th.$emit('message', 'success', '修改成功');

                    Th.show = false;

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
            else
            {
                Th.$api.post('admin/alliance/flux.json', Th.flux, function(data)
                {   
                    Th.loadingItem = false;

                    Th.$emit('message', 'success', '添加成功');

                    Th.show = false;

                }, function(type, message){ Th.loadingItem = false; Th.$emit('message', type, message); });
            }
        },

    },

}
</script>