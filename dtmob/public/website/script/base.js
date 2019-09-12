/**
 * Created by Administrator on 2017/11/13.
 */
//---------------切换页面的点击事件-----------------
/**
 *
 * @param window
 * @param $
 * @param left 左边要跳转的页面
 * @param right 右边要跳转的页面
 */
function page_jump(window, $,left,right) {

    $('#left-page').on('mousedown', function () {
        $('#left-page').attr('src', 'images/left-active.png');
    });
    $('#left-page').on('mouseup', function () {
        $('#left-page').attr('src', 'images/left.png');
        window.location.href = left+'.html';

    });

    $('#right-page').on('mousedown', function () {
        $('#right-page').attr('src', 'images/right-active.png');
    });
    $('#right-page').on('mouseup', function () {
        $('#right-page').attr('src', 'images/right.png');
        window.location.href = right+'.html';
    });


}
function login_reg_css(isIndex,obj,name) {
    if(isIndex){
        //在index页面
        $('#nav2 .nav-title .log-reg a').removeClass('active');
        $(obj).addClass('active');
        $("html,body").animate({"scrollTop": 0});
        login_reg(name);
    }else{
        window.location.href='../../../application/index/view/index.html';
    }
}
/**
 * 登录注册盒子的显示和隐藏
 * @param name
 */
function login_reg(name) {
        $('.login-reg-box').hide();
        $('#' + name).show();
}
/**
 * 检查屏幕高度，如果小于890则固定为890
 *
 * @param flag 是否是index页面
 */
function screen_chk(flag) {
    var h=$(window).height();
    console.log(h)
    if(h<890){
        h=890;
    }
    if(flag){
        $('.bg').css('height',h+'px');
        //当时index页面时header也要变
        $('.header').css('height',h+'px');
    }else{
        $('body').css('height',h+'px');


    }


};
