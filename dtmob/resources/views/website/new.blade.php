@extends('website.layout')

@section('content')
<script>
$(".header-menu li").eq(2).attr("class", "active");
$(".menu li").eq(2).attr("class", "active");
</script>

<div class="page-banner" style="background-image: url('{{$group->public}}images/page-banner-3.jpg')">
    <h5>公告中心</h5>
    <h6>announcement</h6>
</div>
<!--page-banner-->
<div class="news-page news-detail">
    <div class="container">
        <div class="con-area">
            <div class="top">
                <h6>{{$article->title}}</h6>
                <p>发布日期：{{$article->created_at}}</p>
            </div>

            <div class="con-con">
                {!!$article->content!!}
            </div>
            <div class="foot">
                <p>点推网络</p>
                <p>{{$article->created_at}}</p>
            </div>
        </div>

        <div class="foot-page">
            <li>
                <span class="np">上一篇：</span>
                @if (count($pro_new) > 0)
                    <a href="/new/{{$pro_new->id}}">{{$pro_new->title}}</a>
                @else
                    无
                @endif
            </li>
            <li>
                <span class="np">上一篇：</span>
                @if (count($next_new) > 0)
                    <a href="/new/{{$next_new->id}}">{{$next_new->title}}</a>
                @else
                    无
                @endif
            </li>
        </div>
    </div>
</div>
<!--news-page-->

<p class="alone-copy">&copy;2018 dtmob.cn &nbsp;点推网络 &nbsp;版权所有 &nbsp;意见邮件：dtmobcn@gmail.com &nbsp;投诉QQ：2322279938</p>
@endsection