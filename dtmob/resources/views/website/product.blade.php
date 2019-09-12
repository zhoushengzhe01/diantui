@extends('website.layout')

@section('content')
<script>
$(".header-menu li").eq(1).attr("class", "active");
$(".menu li").eq(1).attr("class", "active");
</script>

<!--title-->
<div class="page-banner" style="background-image: url('{{$group->public}}images/page-banner-2.jpg')">
    <h5>产品优势</h5>
</div>

<!--banner-->
<div class="advertisers">
    <div class="top">
        <h5 class="sj-head">广告主</h5>
        <p>创新高效的互联网广告营销技术 <br> 只能精准投放 助力广告主轻松赢在起跑线</p>
    </div>
    <!-- Swiper -->
    <div class="swiper-container" id="advertisers">
        <div class="swiper-wrapper">
            <div class="swiper-slide">
                <h6 class="mo">计费模式</h6>
                <div class="row">
                    <div class="col-xl-4 col-12 list">
                        <div class="shadow">
                            <h5>CPM</h5>
                            <div class="con">
                                <h6>Cost Per Mille</h6>
                                <p>指的是广告投放过程中，按广告每千次被展现来计算广告费用</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-12 list">
                        <div class="shadow">
                            <h5>CPC</h5>
                            <div class="con">
                                <h6>Cost Per Click</h6>
                                <p>指的是广告投放过程中，会根据广告备用点击的次数计算广告费用</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-12 list">
                        <div class="shadow">
                            <h5>CPS</h5>
                            <div class="con">
                                <h6>Cost Per Sales</h6>
                                <p>指的是以实际销售产品数量来计算广告费用的广告，精准的流量带来最佳转化</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="swiper-slide">
                <h6 class="mo">点推优势</h6>
                <div class="row">
                    <div class="col-xl-4 col-12 list">
                        <div class="shadow">
                            <h5>效果监控</h5>
                            <div class="con">
                                <p>通过实时数据分析广告投放效果，从而对广告进行灵活性调整，保证广告投放至最合适的网站达到最优的广告效果</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-12 list">
                        <div class="shadow">
                            <h5>精准投放</h5>
                            <div class="con">
                                <p>定向投放可包括时间定向、地区定向、终端定向、浏览器定向、网站类型定向、人群定向等操作</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-12 list">
                        <div class="shadow">
                            <h5>节约成本</h5>
                            <div class="con">
                                <p>过万的各种类型的网站资源，迅速帮助广告主提升品牌知名度，创造广告效益，帮助企业网站提升访问量，提升网站收益</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Pagination -->
        <div class="swiper-pagination"></div>
        <!-- Arrows -->
        <div class="swiper-button-prev"><i class="iconfont icon-fanhui"></i></div>
        <div class="swiper-button-next"><i class="iconfont icon-gengduo"></i></div>
    </div>
    <a href="" class="register">我是广告主，立即注册投放广告</a>
</div>

<!--advertisers-->
<div class="medium-main">
    <div class="top">
        <h5 class="sj-head">网站主</h5>
        <p>多样的广告形式 助推高额的收入回报 <br> 一对一贴心服务 随时随地查看收益状况</p>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-xl-6 col-lg-6 col-12 list">
                <div class="left">
                    <figure><img src="{{$group->public}}images/advan-icon-1.png" alt=""></figure>
                    <p>丰富的广告资源</p>
                </div>
                <div class="right">点推移动与海量优质广告主合作，并严格把控广告质量，确保协调一致的用户体验。众多的广告主及高质量的广告形式，保证了广告的点击率，提升站长的收入。</div>                </div>
            <div class="col-xl-6 col-lg-6 col-12 list">
                <div class="left">
                    <figure><img src="{{$group->public}}images/advan-icon-2.png" alt=""></figure>
                    <p>详细的数据统计</p>
                </div>
                <div class="right">点推移动与海量优质广告主合作，并严格把控广告质量，确保协调一致的用户体验。众多的广告主及高质量的广告形式，保证了广告的点击率，提升站长的收入。</div>
            </div>
            <div class="col-xl-6 col-lg-6 col-12 list">
                <div class="left">
                    <figure><img src="{{$group->public}}images/advan-icon-3.png" alt=""></figure>
                    <p>强大的技术力量</p>
                </div>
                <div class="right">点推网络拥有业内领先的技术团队，对广告平台系统拥有独特的技术优势。强大的技术力量保证了系统的正常运行，同时能给站长的日常技术问题提供专业的解决方案。</div>
            </div>
            <div class="col-xl-6 col-lg-6 col-12 list">
                <div class="left">
                    <figure><img src="{{$group->public}}images/advan-icon-4.png" alt=""></figure>
                    <p>贴心周到的服务</p>
                </div>
                <div class="right">点推网络拥有专业的运营客服团队，对新提交的网站进行快速审核。对于站长广告费用提现审核及时响应快速到帐。同时，如果站长有什么建议或意见我们也会及时跟进反馈。</div>
            </div>
        </div>
    </div>
    <a href="" class="register">我有媒介平台，立即注册赚钱</a>
</div>


<p class="alone-copy">&copy;2018 dtmob.cn &nbsp;点推网络 &nbsp;版权所有 &nbsp;意见邮件：dtmobcn@gmail.com &nbsp;投诉QQ：2322279938</p>
@endsection