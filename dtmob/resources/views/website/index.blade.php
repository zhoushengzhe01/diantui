@extends('website.layout')

@section('content')
<script>
$(".header-menu li").eq(0).attr("class", "active");
$(".menu li").eq(0).attr("class", "active");
</script>
<div id="fullpage">
    <div class="section page-1">
        <div class="swiper-container" id="index-banner">
            <div class="swiper-wrapper">
                <div class="swiper-slide img-fluid slide-1" style="background-image: url('{{$group->public}}images/index-banner-1.jpg');background-position: 50% 100%;">
                    <h5>点推移动聚集移动广告新力量</h5>
                    <h6>提供国内最 <span>专业可靠</span> 营销平台</h6>
                    <figure><img src="{{$group->public}}images/index-banner-1-img.png" alt=""></figure>
                </div>
                <div class="swiper-slide img-fluid slide-2" style="background-image: url('{{$group->public}}images/index-banner-2.jpg');background-position: 50% 50%;">
                    <h5>专注移动互联网广告，助你快速实现价值</h5>
                    <h6>每日 <span>新增用户40%</span> </h6>
                    <h6>日曝光PV量 <span>5亿+</span> 次</h6>
                    <h6>覆盖全国 <span>上亿</span> 智能手机用户</h6>
                    <figure><img src="{{$group->public}}images/index-banner-3-img.png" alt=""></figure>
                </div>
                <div class="swiper-slide img-fluid slide-3" style="background-image: url('{{$group->public}}images/index-banner-3.jpg');background-position: 50% 100%;">
                    <h5>高效快捷的推广形式、最大化的广告效果</h5>
                    <h6>网站主最信赖的 <span>流量变现专家</span></h6>
                    <h6>广告主最亲睐的 <span>品牌营销平台</span></h6>
                    <figure><img src="{{$group->public}}images/index-banner-2-img.png" alt=""></figure>
                </div>
            </div>
            <div class="swiper-pagination"></div>
            <div class="swiper-button-prev"><i class="iconfont icon-fanhui"></i></div>
            <div class="swiper-button-next"><i class="iconfont icon-gengduo"></i></div>
        </div>
    </div>
  
    <div class="section page-2" style="background: url('{{$group->public}}images/page-2.jpg');">
        <h5 class="sj-head">产品<span>优势</span></h5>
        <h6 class="sm-head">Product Advantage</h6>
        <div class="container">
            <div class="row">
                <div class="col-xl-3 col-lg-3 col-6 list">
                    <figure> <img src="{{$group->public}}images/flip01.jpg" alt=""> </figure>
                    <h4>覆盖上亿智能手机用户</h4>
                </div>
                <div class="col-xl-3 col-lg-3 col-6 list">
                    <figure> <img src="{{$group->public}}images/flip02.jpg" alt=""> </figure>
                    <h4>多重广告解决方案</h4>
                </div>
                <div class="col-xl-3 col-lg-3 col-6 list">
                    <figure> <img src="{{$group->public}}images/flip03.jpg" alt=""> </figure>
                    <h4>广告资源丰富</h4>
                </div>
                <div class="col-xl-3 col-lg-3 col-6 list">
                    <figure> <img src="{{$group->public}}images/flip04.jpg" alt=""> </figure>
                    <h4>精准定位到用户行为投放</h4>
                </div>
            </div>
            <a href="" class="more">了解更多</a>
        </div>
    </div>

    <div class="section page-3" style="background: url('{{$group->public}}images/page-3.jpg');">
        <h5 class="sj-head">广告<span>形式</span></h5>
        <h6 class="sm-head">Advertising Type</h6>
        <div class="container">
            <div class="swiper-container" id="type-left">
                <div class="swiper-wrapper">
                    <div class="swiper-slide swiper-no-swiping">
                        <div class="con">
                            <h6>固定位广告</h6>
                            <p>固定位广告以固定形式显示在网页相应的广告位，使用图片、图文等形式来表现广告创意，吸引用户眼球，是一种主流的广告形式。极大的提高了用户在浏览网页时对广告的关注度，大大提升广告效果。</p>
                        </div>
                    </div>

                    <div class="swiper-slide swiper-no-swiping">
                        <div class="con">
                            <h6>插屏广告</h6>
                            <p>插屏广告是以图片的形式在应用操作暂停或结束时半屏弹出广告创意，能充分利用用户等待的时间，提升广告效果。</p>
                        </div>
                    </div>

                    <div class="swiper-slide swiper-no-swiping">
                        <div class="con">
                            <h6>横幅广告</h6>
                            <p>横幅广告也称Banner广告，将精彩的广告创意以图片的形式展现在手机页面底部，广告尺寸与网页高度融合，创意展现效果好，大大提高用户关注度。</p>
                        </div>
                    </div>

                    <div class="swiper-slide swiper-no-swiping">
                        <div class="con">
                            <h6>信息流广告</h6>
                            <p>信息流广告是指一种依据社交群体属性对用户喜好和特点进行智能推广的广告形式。其主要展现形式是穿插在信息之中。</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="swiper-container" id="type-banner">
                <div class="swiper-wrapper">
                    <div class="swiper-slide img-fluid slide-1 swiper-no-swiping" id="slide-1">
                        <figure>
                            <img src="{{$group->public}}images/type-1.jpg" alt="">
                        </figure>
                    </div>

                    <div class="swiper-slide img-fluid slide-2 swiper-no-swiping" id="slide-2">
                        <figure>
                            <img src="{{$group->public}}images/type-2.jpg" alt="">
                        </figure>
                    </div>

                    <div class="swiper-slide img-fluid slide-3 swiper-no-swiping" id="slide-3">
                        <figure>
                            <img src="{{$group->public}}images/type-3.jpg" alt="">
                        </figure>
                    </div>

                    <div class="swiper-slide img-fluid slide-4 swiper-no-swiping" id="slide-4">
                        <figure>
                            <img src="{{$group->public}}images/type-4.jpg" alt="">
                        </figure>
                    </div>
                </div>
            </div>

            <div class="swiper-pagination ts-nav"></div>
            <div class="swiper-button-prev ts-prev"><i class="iconfont icon-fanhui"></i></div>
            <div class="swiper-button-next ts-next"><i class="iconfont icon-gengduo"></i></div>
        </div>
    </div>
    
    <div class="section page-4">
        <h5 class="sj-head">公告<span>中心</span></h5>
        <h6 class="sm-head">Announcement</h6>
        <div class="en-font">NEWS</div>
        <figure><img src="{{$group->public}}images/news-cf.jpg" alt=""></figure>
        <div class="left-con">
            <h6 class="page-head">点推 <br>
            <span>最新动态</span></h6>
            <div class="border"></div>
            <p>关注最新的动态，了解最新的公告，对点推消息一网打尽。</p>
            <div class="line"></div>
        </div>
        <ul class="news-list">
            @foreach ($news as $key=>$val)
			<li> 
                <a href="/new/{{$val->id}}">
                    <h5>{{$val->title}}</h5>
                    <h6>{{$val->intro}}</h6>
                    <span>{{$val->created_at}}</span>
                </a> 
            </li>
            @endforeach
		</ul>
        <a href="#" class="more">了解更多</a>
    </div>
    
    <div class="section page-5" style="background: url('{{$group->public}}images/page-5.jpg');">
        <h5 class="sj-head">关于<span>点推</span></h5>
        <h6 class="sm-head">About</h6>
        <div class="container">
            <p class="jj">点推，整合了智能手机领域大量优质媒体及广告资源，构建起一个公平、诚信、高效的广告营销服务平台，为广告主提供精准，高效的产品、品牌推广服务，同时为媒介主创造丰厚的广告收益。</p>
            <h6 class="title">点推合作伙伴</h6>
            <ul class="co-list">
                <li><figure><img src="{{$group->public}}images/client-01.jpg" alt=""></figure></li>
                <li><figure><img src="{{$group->public}}images/client-02.jpg" alt=""></figure></li>
                <li><figure><img src="{{$group->public}}images/client-03.jpg" alt=""></figure></li>
                <li><figure><img src="{{$group->public}}images/client-04.jpg" alt=""></figure></li>
                <li><figure><img src="{{$group->public}}images/client-05.jpg" alt=""></figure></li>
                <li><figure><img src="{{$group->public}}images/client-06.jpg" alt=""></figure></li>
                <li><figure><img src="{{$group->public}}images/client-07.jpg" alt=""></figure></li>
                <li><figure><img src="{{$group->public}}images/client-08.jpg" alt=""></figure></li>
                <li><figure><img src="{{$group->public}}images/client-09.jpg" alt=""></figure></li>
                <li><figure><img src="{{$group->public}}images/client-10.jpg" alt=""></figure></li>
                <li><figure><img src="{{$group->public}}images/client-03.jpg" alt=""></figure></li>
                <li><figure><img src="{{$group->public}}images/client-06.jpg" alt=""></figure></li>
            </ul>
            <div class="swiper-container" id="index-partner">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div><img src="{{$group->public}}images/client-01.jpg" alt=""> </div>
                    </div>
                    <div class="swiper-slide">
                        <div> <img src="{{$group->public}}images/client-02.jpg" alt=""> </div>
                    </div>
                    <div class="swiper-slide">
                        <div> <img src="{{$group->public}}images/client-03.jpg" alt=""> </div>
                    </div>
                    <div class="swiper-slide">
                        <div> <img src="{{$group->public}}images/client-04.jpg" alt=""> </div>
                    </div>
                    <div class="swiper-slide">
                        <div> <img src="{{$group->public}}images/client-05.jpg" alt=""> </div>
                    </div>
                    <div class="swiper-slide">
                        <div> <img src="{{$group->public}}images/client-06.jpg" alt=""> </div>
                    </div>
                    <div class="swiper-slide">
                        <div> <img src="{{$group->public}}images/client-07.jpg" alt=""> </div>
                    </div>
                    <div class="swiper-slide">
                        <div> <img src="{{$group->public}}images/client-08.jpg" alt=""> </div>
                    </div>
                    <div class="swiper-slide">
                        <div> <img src="{{$group->public}}images/client-09.jpg" alt=""> </div>
                    </div>
                    <div class="swiper-slide">
                        <div> <img src="{{$group->public}}images/client-10.jpg" alt=""> </div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
            <a href="#" class="more">了解更多</a>
        </div>
    </div>

    <div class="section page-7" style="background: url('{{$group->public}}images/page-7.jpg');">
        <h5 class="sj-head">联系<span>我们</span></h5>
        <h6 class="sm-head">Customer Service</h6>
        <div class="con">
            <figure class="map"><img src="{{$group->public}}images/about-img.jpg" alt=""></figure>
            <div class="line"></div>
        </div>
        <div class="detail">
            <p>投诉邮箱：dtmobcn@gmain.com</p>
        </div>
        <p class="bottom-menu">
            <a href="/">点推首页</a>&nbsp; |&nbsp;
            <a href="/product">产品优势</a>&nbsp; |&nbsp;
            <a href="/news">公告中心</a>&nbsp; |&nbsp;
            <a href="/help">帮助中心</a>&nbsp; |&nbsp;
            <a href="/about">关于我们</a>
        </p>
    </div>
</div>
@endsection