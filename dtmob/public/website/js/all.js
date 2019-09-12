/*
* project : meces
*
* */


$(function() {
    if($('#fullpage').length>0) {
        $('#fullpage').fullpage({
            verticalCentered: true,
            navigation: true,//是否显示导航，默认为false
            navigationPosition: 'right',//导航小圆点的位置

            normalScrollElements: '.NSE',//避免自动滚动，
            anchors: ['page1', 'page2', 'page3', 'page4','page5','page6','page7'],//anchors定义锚链接，
            navigationTooltips: ['page1', 'page2', 'page3', 'page4','page5','page6','page7'],//导航小圆点的提示
            afterLoad: function(anchorLink, index){
                if(index == 1){
                    $('.info1').delay(800).fadeIn(100).addClass("fadeInUp animated");
                    $('.info2').fadeIn(100).addClass("bounceIn animated");
                    $('.info3').delay(1000).fadeIn(100).addClass("fadeInDown animated");
                    $('.info4').delay(1200).fadeIn(100).addClass("bounce animated infinite");
                }
                if(index == 2){
                    $('.page-2').css({opacity: '1'});
                    $('.page-2 .list').css({top:'0',opacity:'1'});
                    $('.page-2 .more').css({top:'0',opacity:'1'});
                }

                if(index == 3){
                    $('.page-3').css({margin:'0',opacity:'1'});
                    $('.page-3 .con-list').css({margin:'0',opacity:'1'});
                    $('.page-3 #type-banner').css({margin:'0',opacity:'1'});
                }

                if(index == 4){
                    $('.page-4').css({opacity: '1'});
                }

                if(index == 5){
                    $('.page-5').css({margin:'0',opacity:'1'});
                    $('.page-5 .jj').css({top:'0',opacity:'1'});
                    $('.page-5 .title').css({top:'0',opacity:'1'});
                    $('.page-5 .co-list').css({top:'0',opacity:'1'});
                    $('.page-5 .more').css({top:'0',opacity:'1'});
                }

                if(index == 6){
                    $('.page-6').css({margin:'0',opacity:'1'});
                    $('.page-6 .con').css({margin:'0',opacity:'1'});
                }

                if(index == 7){
                    $('.page-7').css({opacity: '1'});
                    $('.page-7 .con').css({opacity:'1'});
                    $('.page-7 .detail').css({opacity:'1'});
                }

            },
            onLeave: function(index, direction){
                if(index == 1){
                    $('.info1').fadeOut().removeClass("fadeInUp animated");
                    $('.info2').fadeOut().removeClass("bounceIn animated");
                    $('.info3').fadeOut().removeClass("fadeInDown animated");
                    $('.info4').fadeOut().removeClass("bounce animated infinite");
                }
                if(index == 2){
                    $('.info5').fadeOut().removeClass("fadeInUp animated");
                    $('.info6').fadeOut().removeClass("fadeInDown animated");
                    $('.info7').fadeOut().removeClass("bounce animated infinite");
                    $('.info8').fadeOut().removeClass("bounce animated infinite");
                    $('.info9').fadeOut().removeClass("bounce animated infinite");
                    $('.info10').fadeOut().removeClass("fadeInUp animated");
                    $('.info11').fadeOut().removeClass("fadeInDown animated");
                    $('.info12').fadeOut().removeClass("pulse animated infinite");
                    $('.info13').fadeOut().removeClass("flash animated ");
                    $('.info14').fadeOut().removeClass("flash animated ");
                    $('.info15').fadeOut().removeClass("flash animated ");
                    $('.info16').fadeOut().removeClass("flash animated ");
                    $('.info4').fadeOut().removeClass("bounce animated ");
                }
                if(index == 3){
                    $('.info17').fadeOut().removeClass("fadeInDown animated");
                    $('.info18').fadeOut().removeClass("pulse animated ");
                    $('.info19').fadeOut().removeClass("pulse animated ");
                    $('.info20').fadeOut().removeClass("pulse animated ");
                    $('.info21').fadeOut().removeClass("pulse animated ");
                    $('.info22').fadeOut().removeClass("pulse animated ");
                    $('.info4').fadeOut().removeClass("bounce animated infinite");
                }
                if(index == 4){
                    $('.info23').fadeOut().removeClass("fadeInDown animated");
                    $('.info24').fadeOut().removeClass("bounce animated ");
                    $('.info25').fadeOut().removeClass("bounce animated ");
                    $('.info26').fadeOut().removeClass("bounce animated ");
                    $('.info27').fadeOut().removeClass("bounce animated ");
                    $('.info28').fadeOut().removeClass("fadeInUp animated");
                    $('.info29').fadeOut().removeClass("fadeInDown animated");
                    $('.info4').fadeOut().removeClass("bounce animated infinite");
                }
            }
        });
    }

    if($('#index-banner').length>0){
        var swiper = new Swiper('#index-banner', {
            spaceBetween: 0,
            loop:true,
            effect:'fade',
            autoplay:{
                delay: 5000
            },
            pagination :{
                el: '.swiper-pagination',
                clickable :true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });

    }

    if($('#type-banner').length>0){
        var typebanner = new Swiper('#type-banner', {
            slidesPerView: 3,
            spaceBetween: 0,
            centeredSlides: true,
            effect: 'coverflow',
            autoplay:{
                delay:4000,
            },
            coverflowEffect: {
                rotate: 50,
                stretch: 0,
                depth: 100,
                modifier: 1,
                slideShadows : true,
            },
            loop:true,
            pagination: {
                el: '.ts-nav',
                clickable: true,
            },
            navigation: {
                nextEl: '.ts-next',
                prevEl: '.ts-prev',
            },
        });
    }

    if($('#type-left').length>0){
        var typeleft = new Swiper('#type-left', {
            spaceBetween: 0,
            slidesPerView: 1,
            loop:true,
            spaceBetween:30,
            autoplay:{
                delay:4000,
            },
            pagination: {
                el: '.ts-nav',
                clickable: true,
            },
            navigation: {
                nextEl: '.ts-next',
                prevEl: '.ts-prev',
            },
        });

    }




    if($('#index-partner').length>0){
        var partner = new Swiper('#index-partner', {
            slidesPerView: 1,
            slidesPerColumn: 1,
            spaceBetween: 30,
            preventClicks : false,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            autoplay:{
                delay:3000
            }
        });
    }


    $(".ti").bind("click", function() {
        var b = $(this).next(".con");
        if (b.is(":visible")) {
            b.slideUp(200);
        } else {
            b.slideDown(200);
        }
    })



    if($('.header').length>0){
        $(".menu-button").click(function(){
            $('.header-menu').slideToggle(300);
            $('.mb-bg').fadeToggle(200);
        });

        $(".mb-bg").click(function(){
            $('.header-menu').slideUp(300);
            $('.mb-bg').fadeOut(200);
        });

    }

    if($('#top').length>0){
        $("#top").click(function () {
            var speed=800;//滑动的速度
            $('body,html').animate({ scrollTop: 0 }, speed);
            return false;
        });
    }

    if($('#advertisers').length>0){
        var swiper = new Swiper('#advertisers', {
            slidesPerView: 1,
            spaceBetween: 30,
            loop: true,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    }



    // 窗口变化
    $(window).resize(function(){

        var ww = $(window).width();

        // DEVICE : Tablets
        if (ww>1300) {


            $('#fullpage').fullpage({
                onLeave: function(index,nextIndex,direction){
                    if(nextIndex==2){
                        $(".page-2").css({opacity:"1"});
                    }
                }
            });

            if($('.page-1').length>0) {
                $('.page-1').ready(function (){
                    $('.page-1').css({opacity: '1'});
                });
            }


            // if($('.page-2').length>0) {
            //     $('.page-2').mouseover(function (){
            //         $('.page-2').css({margin:'0',opacity:'1'});
            //         $('.page-2 .list').css({top:'0',opacity:'1'});
            //         $('.page-2 .more').css({top:'0',opacity:'1'});
            //     });
            //
            // }


            // if($('.page-3').length>0) {
            //     $('.page-3').mouseover(function (){
            //         $('.page-3').css({margin:'0',opacity:'1'});
            //         $('.page-3 .con-list').css({margin:'0',opacity:'1'});
            //         $('.page-3 #type-banner').css({margin:'0',opacity:'1'});
            //     });
            // }


            //
            // if($('.page-4').length>0) {
            //     $('.page-4').mouseover(function (){
            //         $('.page-4').css({opacity: '1'});
            //     });
            // }
            //
            //
            // if($('.page-5').length>0) {
            //     $('.page-5').mouseover(function (){
            //         $('.page-5').css({margin:'0',opacity:'1'});
            //         $('.page-5 .jj').css({top:'0',opacity:'1'});
            //         $('.page-5 .title').css({top:'0',opacity:'1'});
            //         $('.page-5 .co-list').css({top:'0',opacity:'1'});
            //         $('.page-5 .more').css({top:'0',opacity:'1'});
            //     });
            // }
            //
            // if($('.page-6').length>0) {
            //     $('.page-6').mouseover(function (){
            //         $('.page-6').css({margin:'0',opacity:'1'});
            //         $('.page-6 .con').css({margin:'0',opacity:'1'});
            //     });
            // }
            //
            // if($('.page-7').length>0) {
            //     $('.page-7').mouseover(function (){
            //         $('.page-7').css({opacity: '1'});
            //         $('.page-7 .con').css({opacity:'1'});
            //         $('.page-7 .detail').css({opacity:'1'});
            //     });
            // }
        }

        if (ww>=768 && ww<=1024) {


            if($("#com-re-sw").length>0) {
                //co
                kk.params.slidesPerView = 3;
            }

        }

        if(ww < 768){
            $.fn.fullpage.setAutoScrolling(false);
            $('#index-banner .swiper-slide').removeClass('swiper-no-swiping');
        } else {
            //$.fn.fullpage.setAutoScrolling(true);
            $('#index-banner .swiper-slide').addClass('swiper-no-swiping');
        }

        // DEVICE : SmartPhone
        if (ww<768) {

            if($('#fullpage').length>0) {
                $('#fullpage').fullpage({
                    autoScrolling: false,
                });

            }



            if($(".honor").length>0){
                honor.params.slidesPerView = 1;
            }


            if($("#type-banner").length>0){
                typebanner.params.slidesPerView = 1;

            }

        }

        if($("#index-partner").length>0){
            partner.update();
        }

        if($("#type-banner").length>0){
            typebanner.update();
        }


    });

    $(window).trigger('resize');

});









