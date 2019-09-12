@extends('website.layout')

@section('content')
<script>$("header .annos").addClass('active');</script>

<div class="banner-annos">
    <div class="container">
        <div class="left-img"><img src="/website/images/left.png"></div>
        <div class="right-img"><img src="/website/images/right.png"></div>

        <div class="img-box"><img src="/website/images/home-title.png" alt=""></div>
    </div>
</div>

<div class="annos product">
    <div class="container">
        <div class="title"><img src="/website/images/anno-title.png" alt=""></div>
        <div class="annos-content">
            <div class="row">
                <ul class="left-menu">
                    <li><a href="/annos/1" class="@if ($category_id==1) active @endif">公司动态</a></li>
                    <li><a href="/annos/2" class="@if ($category_id==2) active @endif">行业新闻</a></li>
                </ul>

                <div class="tab-content right-menu">

                    <div class="title-title">@if ($category_id==1)公司动态@endif  @if ($category_id==2)行业新闻@endif</div>
                    <ul class="list">
                        @foreach ($news as $key=>$val)
                        <li><a href="/anno/{{$val->id}}"><b>{{$val->title}}</b></a> <span class="time">{{$val->created_at}}</span></li>
                        @endforeach
                    </ul>

                    {!! $news->links() !!}
                </div>

            </div>
        </div>
    </div>
</div>
@endsection