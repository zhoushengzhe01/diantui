import Vue from 'vue'
import Router from 'vue-router'
import homePage from './views/home.vue'
import websitesPage from './views/websites.vue'
import adtypePage from './views/adtype.vue'
import myadsPage from './views/myads.vue'
import myadPage from './views/myad.vue'
import earningsPage from './views/earnings.vue'
import rewardsPage from './views/rewards.vue'
import expendsPage from './views/expends.vue'
import userPage from './views/user.vue'
import lowerPage from './views/lower.vue'


Vue.use(Router)

export default new Router({
	mode: 'history',
	routes: [
		{
			name: 'home',
			path: '/webmaster',
		 	component: homePage
		},
		{
			name: 'website',
			path: '/webmaster/websites',	//我的网站
			component: websitesPage
		},
		{
			name: 'ad',
			path: '/webmaster/adtype',
			component: adtypePage		//推荐广告
		},
		{
			name: 'myad',
			path: '/webmaster/myads',	//我的广告
			component: myadsPage
		},
		{
			name: 'myad',
			path: '/webmaster/myad',	//添加广告
			component: myadPage
		},
		{
			name: 'myad',
			path: '/webmaster/myad/:id',	//修改广告
			component: myadPage
		},
		{
			name: 'earning',
			path: '/webmaster/earnings',	//佣金
			component: earningsPage
		},
		{
			name: 'reward',
			path: '/webmaster/rewards',		//奖励
			component: rewardsPage
		},
		{
			name: 'expend',
			path: '/webmaster/expends',		//财物结算
			component: expendsPage
		},
		{
			name: 'expend',
			path: '/webmaster/news',		//消息中心
			component: expendsPage
		},
		{
			name: 'user',
			path: '/webmaster/user',		//个人信息
			component: userPage
		},
		{
			path: '/webmaster/lower',		//下限链接
			component: lowerPage
		},
  	]
})
