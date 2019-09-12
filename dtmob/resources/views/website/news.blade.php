@extends('website.layout')

@section('content')
<script>
$(".header-menu li").eq(2).attr("class", "active");
$(".menu li").eq(2).attr("class", "active");
</script>

<!-- title -->
<div class="page-banner" style="background-image: url('{{$group->public}}images/page-banner-3.jpg')">
    <h5>公告中心</h5>
    <h6>announcement</h6>
</div>

<!--banner-->
<div class="news-page">
    <div class="container">
        <ul class="news">
            @foreach ($news as $key=>$val)
            <li>
                <a href="/new/{{$val->id}}" class="con">
                    <h5>{{$val->title}}</h5>
                    <h6>{{$val->intro}}</h6>
                    <p>{{$val->created_at}}</p>
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    <!--pages-->
    {!! $news->links() !!}
</div>

<!--copyright-->
<p class="alone-copy">&copy;2018 dtmob.cn &nbsp;点推网络 &nbsp;版权所有 &nbsp;意见邮件：dtmobcn@gmail.com &nbsp;投诉QQ：2322279938</p>
@endsection