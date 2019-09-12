@extends('website.layout')

@section('content')
<script>
$(".header-menu li").eq(4).attr("class", "active");
$(".menu li").eq(4).attr("class", "active");
</script>

<!--title-->
<div class="page-banner about-banner" style="background-image: url('{{$group->public}}images/page-banner-5.jpg')">
	<h5>点推网咯</h5>
	<h6>最值得信赖的移动平台</h6>
</div>

<!--introduce-->
<div class="about-page">
	<div class="container" style="max-width: 860px;">
		<p>点推网咯（下面简称点推）移动端广告投放平台，致力于为广告主和媒介主（开发者、自媒体）提供最优质的移动营销服务，使双方营销资源价值最大化。</p>
		<p>点推，整合了智能手机领域大量优质媒体及广告资源，构建起一个公平、诚信、高效的广告营销服务平台，为广告主提供精准，高效的产品、品牌推广服务，同时为媒介主创造丰厚的广告收益。</p>
		<p>点推运营团队拥有多年网络营销实战经验，塑造了多个网络知名品牌，培养了一批高水平的技术研发人员和一支专业的营销服务团队。</p>
		<p>点推，以专业化的营销队伍，依靠先进的技术，科学规范的管理，强大的硬件设施和雄厚资金，我们有信心和实力为广大媒介主和广告主提供优质服务。</p>
		<p style="text-align: right; margin-top: 40px;">热诚欢迎广大媒介主和广告主的加盟，共创美好未来!</p>
		<a href="#" class="join">加入我们</a>
	</div>
</div>

<p class="alone-copy">&copy;2018 dtmob.cn &nbsp;点推网络 &nbsp;版权所有 &nbsp;意见邮件：dtmobcn@gmail.com &nbsp;投诉QQ：2322279938</p>
@endsection