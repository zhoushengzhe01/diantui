@extends('website.layout')

@section('content')
<script>
$(".header-menu li").eq(3).attr("class", "active");
$(".menu li").eq(3).attr("class", "active");
</script>
<!--title-->
<div class="page-banner" style="background-image: url('{{$group->public}}images/page-banner-4.jpg')">
    <h5>帮助中心</h5>
</div>

<!--banner-->
<div class="help-center container">
    <div class="con-area">
        <div class="ques-con">
            <div class="left">
                <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">账户问题</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">广告主问题</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" id="pills-contact-tab" data-toggle="pill" href="#pills-contact" role="tab" aria-controls="pills-contact" aria-selected="false">媒介主问题</a>
                    </li>
                </ul>
            </div>

            <div class="right">
                <div class="tab-content" id="pills-tabContent">
                    <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                        <h5 class="title">账户问题</h5>

                        <div class="help-list">
                            <li class="list">
                                <a href="javascript:void(0)" class="ti">
                                    <h6>- 如何注册点推账户？</h6>
                                </a>

                                <div class="con">
                                    <p>(1)点击 http://www.dtmob.cn 【首页】右上角【注册】按钮，即可进入注册页面</p>

                                    <p>(2) 在注册页面中填写相关信息，点击”接受协议并提交”。</p>

                                    <p>(3) 登录填写的邮箱，打开邮件中的验证链接来完成邮箱的验证。邮箱验证后，您就可以使用点推网络提供的各种服务了。如果您在数分钟之内没有收到我们的邮件，请及时联系我们。</p>
                                </div>
                            </li>

                            <li class="list">
                                <a href="javascript:void(0)" class="ti">
                                    <h6>- 忘记密码时怎么办？</h6>
                                </a>

                                <div class="con">
                                    <p>(1) 点击 http://www.dtmob.cn 页面右上角的"登录"，在登录页面中点击"找回密码"。</p>

                                    <p>(2) 输入您注册时填写的邮箱地址，点击"提交"按钮。</p>

                                    <p>(3) 登录您的邮箱，打开邮件中的重置密码链接，然后在页面中完成账户密码的重置。如果您在重置密码过程中遇到任何问题，请及时联系我们。</p>
                                </div>
                            </li>

                            <li class="list">
                                <a href="javascript:void(0)" class="ti">
                                    <h6>- 如何修改密码？</h6>
                                </a>

                                <div class="con">
                                    <p>(1) 登录点推网络用户后台，然后点击导航栏中的"个人设置"。</p>

                                    <p>(2) 点击"修改密码"选项卡，输入新密码及确认密码。</p>

                                    <p>(3) 点击"修改密码"按钮，以保存修改结果。</p>
                                </div>
                            </li>

                            <li class="list">
                                <a href="javascript:void(0)" class="ti">
                                    <h6>- 如何填写、修改账户信息？</h6>
                                </a>

                                <div class="con">
                                    <p>(1) 登录点推网络用户后台，然后点击导航栏中的"个人设置"。</p>

                                    <p>(2) 点击"更新资料"选项卡，然后在选项卡中点击"编辑我的信息"按钮。</p>

                                    <p>(3) 在页面修改相关信息，并点击"保存信息"。</p>
                                </div>
                            </li>

                            <li class="list">
                                <a href="javascript:void(0)" class="ti">
                                    <h6>- 个人账户和公司账户有什么不同？</h6>
                                </a>

                                <div class="con">
                                    <p>个人账户和公司账户主要是税收政策的不同；其他的创建应用、应用管理等都是一样的。</p>
                                </div>
                            </li>

                            <li class="list">
                                <a href="javascript:void(0)" class="ti">
                                    <h6>- 如何转换账户角色？</h6>
                                </a>

                                <div class="con">
                                    <p>如需转换账户角色（开发者/广告主），请与客服人员联系。</p>
                                </div>
                            </li>

                            <li class="list">
                                <a href="javascript:void(0)" class="ti">
                                    <h6>- 被冻结的账户是否还能登录？</h6>
                                </a>

                                <div class="con">
                                    <p>目前被冻结的账户无法再登录点推网络广告平台，您如果对此有什么异议可向客服人员询问。</p>
                                </div>
                            </li>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                        <h5 class="title">广告主问题</h5>

                        <li class="list">
                            <a href="javascript:void(0)" class="ti">
                                <h6>- 点推网络的广告投放到哪些媒体上？</h6>
                            </a>

                            <div class="con">
                                <p>广告主的广告将直接投放到点推网络所有的合作媒体上，这些媒体包括全国性、行业性、地方性的各媒体网站，点推网络的合作媒体一直在不断增加，覆盖各个地区与行业，超大的访问量，注定超大的浏览点击率，宣传效果可见一斑。</p>
                            </div>
                        </li>

                        <li class="list">
                            <a href="javascript:void(0)" class="ti">
                                <h6>- 点推的广告样式有哪些？</h6>
                            </a>

                            <div class="con">
                                <p>（1）横幅广告</p>

                                <p>（2）固定位广告（图片、图文、网摘）</p>

                                <p>（3）插屏广告</p>

                                <p>（4）全屏广告</p>

                                <p>（5）信息流广告</p>
                            </div>
                        </li>

                        <li class="list">
                            <a href="javascript:void(0)" class="ti">
                                <h6>- 广告如何计费？</h6>
                            </a>

                            <div class="con">
                                <p>本平台对于广告的收费分为每千次展示收费和单次点击收费，每千次展示收费和单次点击收费的基本标准是根据广告主创建的广告内容（是否有文字、图标、图片）、制定的投放目标（覆盖范围、精准程度、投放时间段）而给定的，即提供一个最低定价和一般定价区间，具体定价由广告主用户自主设定。</p>
                            </div>
                        </li>


                        <li class="list">
                            <a href="javascript:void(0)" class="ti">
                                <h6>- 如果做到广告精准投放？</h6>
                            </a>

                            <div class="con">
                                <p>本广告主用户创建广告时，可以填写投放目标信息，而开发者用户提交应用时会填写应用的相关信息，那么平台就会自动地将广告按照广告主制定的投放目标推送到跟该目标相符合的应用程序中，从而实现精准投放。其精准的程序包括广告投放到的应用类型、手机系统、手机品牌、地区、投放的时间段乃至使用手机的用户类别等。例如广告主指定投放目标时将广告投放到网络游戏中，那么该广告就将只出现在网络游戏中，不会出现在其他游戏或软件中。</p>
                            </div>
                        </li>


                        <li class="list">
                            <a href="javascript:void(0)" class="ti">
                                <h6>- 平台支持怎样的付款方式？</h6>
                            </a>

                            <div class="con">
                                <p>广告主用户可以通过网银转账、充值的方式向本平台的广告账户充入一定的金额，该金额将完全用于广告支出，平台会根据广告的展示千次数和点击次数对广告余额进行扣费。</p>
                            </div>
                        </li>


                        <li class="list">
                            <a href="javascript:void(0)" class="ti">
                                <h6>- 如何查看广告效果？</h6>
                            </a>

                            <div class="con">
                                <p>广告主用户可以登录平台，进入广告主管理后台，查看“广告列表”查看广告统计，则可以看到自己的广告在某段时期或时间内的展示次数、点击次数和点击率等。</p>
                            </div>
                        </li>


                        <li class="list">
                            <a href="javascript:void(0)" class="ti">
                                <h6>- 如何控制广告费用？</h6>
                            </a>

                            <div class="con">
                                <p>为了控制广告费用，广告主用户可以制定“广告预算”，预算中包括每天的广告费用预算、每千次展示的价格和单次点击的价格，并且可以设定投放的时间段，从而来控制广告的支出。</p>
                            </div>
                        </li>


                        <li class="list">
                            <a href="javascript:void(0)" class="ti">
                                <h6>- 账户余额是否支持退款？</h6>
                            </a>

                            <div class="con">
                                <p>支持的，不过必须过去3个月内没有新的消耗，且该笔款项没有开过发票，与您的专属客服联系，申请退款即可。</p>
                            </div>
                        </li>
                    </div>

                    <div class="tab-pane fade" id="pills-contact" role="tabpanel" aria-labelledby="pills-contact-tab">
                        <h5 class="title">网站主问题</h5>

                        <li class="list">
                            <a href="javascript:void(0)" class="ti">
                                <h6>- 支持的应用类型有哪些？</h6>
                            </a>

                            <div class="con">
                                <p>目前支持Android、iOS类应用以及wap网站；接下来将支持WP7类应用，敬请期待。</p>
                            </div>
                        </li>

                        <li class="list">
                            <a href="javascript:void(0)" class="ti">
                                <h6>- 是否支持积分墙类广告？</h6>
                            </a>

                            <div class="con">
                                <p>支持。</p>
                            </div>
                        </li>

                        <li class="list">
                            <a href="javascript:void(0)" class="ti">
                                <h6>- 平台的计费方式有哪些？</h6>
                            </a>
                            <div class="con">
                                <p>点推网络支持主流的计费方式，包括 CPC、CPA、CPM。</p>
                            </div>
                        </li>
                        <li class="list">
                            <a href="javascript:void(0)" class="ti">
                                <h6>- 怎样才能提款，提款有什么限制？</h6>
                            </a>
                            <div class="con">
                                <p>只要完成财务信息设置并通过身份审核，且账户余额达到200元便可系统自动提款，如果站长打算终止合作，但是账户余额满50元并未满200可以联系客服手动提款。提款金额必须小于或等于后台“可提款余额”的金额，且必须为正整数。</p>
                            </div>
                        </li>
                        <li class="list">
                            <a href="javascript:void(0)" class="ti">
                                <h6>- 提款申请的结算周期是？</h6>
                            </a>
                            <div class="con">
                                <p>我们每周审核上周一至上周日的提款申请，款项于周三汇出；每月最后一个星期三处理上周及之前的媒介提款请求并打款。如遇节假日则顺延处理（具体见相应公告）。</p>
                            </div>
                        </li>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!--copyright-->
<p class="alone-copy">&copy;2018 dtmob.cn &nbsp;点推网络 &nbsp;版权所有 &nbsp;意见邮件：dtmobcn@gmail.com &nbsp;投诉QQ：2322279938</p>
@endsection