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
            <div class="anno-item">
                <h1>{{$article->title}}</h1>
                <div class="info">时间：{{$article->created_at}} &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 来源：<a href="/">点推联盟</a></div>
                <br/>
                <div class="anno_content">{!! $article->content !!}</div>
                <br/><br/>
            </div>
            <style>
                .anno-item h1{
                    
                }
                .new_content p{
                    line-height: 36px;
                    margin-bottom: 8px;
                }
                .new_content a{
                    color: #409eff;
                }
                .new_content b{
                    font-weight: 700;
                }
            </style>
        </div>
    </div>
</div>
@endsection