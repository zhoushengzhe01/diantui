<template>
    <div class="content">
        <div class="title-box">
            <h3 class="title">点击数据</h3>
            
            <div class="search-box">
                <el-button type="success" @click="distribution">查询</el-button>
            </div>
        </div>

        <div class="box" v-loading="loading">
            
            <el-table :data="data.clicks" style="width: 100%">

                <el-table-column
                    label="来源/地址"
                    min-width="500">
                    <template slot-scope="scope">
                        <span style="white-space: nowrap;"><span class="success copy" :data-clipboard-text="scope.row.source">复制</span> {{scope.row.source}}</span> 
                        <br/>
                        <span style="white-space: nowrap;"><span class="success copy" :data-clipboard-text="scope.row.url">复制</span> {{scope.row.url}}</span> 
                    </template>
                </el-table-column>

                <el-table-column
                    label="站长/广告"
                    min-width="100">
                    <template slot-scope="scope">
                        {{scope.row.webmaster_id}}
                        <br/>
                        {{scope.row.advertiser_id}}
                    </template>
                </el-table-column>

                <el-table-column
                    label="地区/IP"
                    min-width="120">
                    <template slot-scope="scope">
                        <span style="white-space: nowrap;" :title="scope.row.region + scope.row.city">{{scope.row.region}} {{scope.row.city}}</span> 
                        <br/>{{scope.row.ip}}
                    </template>
                </el-table-column>

                <el-table-column
                    label="终端/系统"
                    min-width="120">
                    <template slot-scope="scope">
                        {{scope.row.wx_wap}}-{{scope.row.system}}<br/>{{scope.row.refso}}
                    </template>
                </el-table-column>

                <el-table-column
                    label="屏幕/位置"
                    min-width="100">
                    <template slot-scope="scope">
                        屏:{{scope.row.screen}}<br/>位:{{scope.row.clickp}}
                    </template>
                </el-table-column>

                <el-table-column
                    label="时间"
                    min-width="100">
                    <template slot-scope="scope">
                        {{scope.row.created_at}}
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
        <el-dialog title="点击分布" :visible.sync="show" class="small_dialog">
            <canvas id="canvas" width="360" height="640" style="border: 1px solid #ccc;">

            </canvas>
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
            show: false,
            webmaster_ad_id: this.$route.params.webmaster_ad_id,
            paramete: {
                offset: 0,
                limit: 200,
            },
            data: {},
        };
    },
    created: function () {
        
        this.group.page = '/admin/webmaster/ads';

        this.getEarningClick();

        var Th = this;
        //这是复制内容
        var clipboard = new ClipboardJS('.copy');
        clipboard.on('success', function(e) {
            Th.$emit('message', 'success', '复制成功。');
            e.clearSelection();
        });
        clipboard.on('error', function(e) {
            Th.$emit('message', 'success', '复制失败');
        });

    },
    methods:{

        getEarningClick: function()
        {
            var Th = this;
            
            Th.loading = true;
            
            Th.$api.get('admin/webmaster/earning/click/'+Th.webmaster_ad_id+'.json', Th.paramete, function(data)
            {
                Th.data = data;
                Th.loading = false;

            }, function(type, message){ Th.loading = false; Th.$emit('message', type, message); });
        },
        pageChange: function(val) {
            this.paramete.offset = parseInt(val-1) * parseInt(this.paramete.limit);
            this.getEarningClick();
        },
        canvas: function(point) {
            let c=document.getElementById("canvas");
            let ctx=c.getContext("2d");
            ctx.clearRect(0,0,c.width,c.height);
            ctx.fillStyle="red";

            for (var key in point)
            {
                ctx.fillRect(point[key].x, point[key].y, 4, 4);
            }
        },
        distribution: function()
        {
            var Th = this;
            Th.show = true;

            setTimeout(function(){

                var point = {};
                for (var key in Th.data.clicks)
                {
                    var data = Th.data.clicks[key];
                    var screen = data.screen.split("*");
                    var clickp = data.clickp.split("*");
                    point[key] = [];
                    point[key].x = clickp[0]*(360/screen[0]);
                    point[key].y = clickp[1]*(640/screen[1]);
                }
                
                console.log(point);

                Th.canvas(point);
                
            }, 500);
            
        }
    },
}
</script>