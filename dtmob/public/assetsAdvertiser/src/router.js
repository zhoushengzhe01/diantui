import Vue from 'vue'
import Router from 'vue-router'
import homePage from './views/home.vue'
import userPage from './views/user.vue'
import packagesPage from './views/packages.vue'
import packagePage from './views/package.vue'
import adsPage from './views/ads.vue'
import adPage from './views/ad.vue'
import ExpendsPage from './views/expends.vue'
import RechargesPage from './views/recharges.vue'
import loginlogsPage from './views/loginlogs.vue'
import Messages from './views/messages.vue'



Vue.use(Router)

export default new Router({
	mode: 'history',
	routes: [
		{
			name: 'home',
			path: '/advertiser',
		 	component: homePage
		},
		{
			name: 'user',
			path: '/advertiser/user',
			component: userPage
		},
		{
			name: 'package',
			path: '/advertiser/packages',
			component: packagesPage
		},
		{
			name: 'package',
			path: '/advertiser/package',
			component: packagePage
		},
		{
			name: 'package',
			path: '/advertiser/package/:id',
			component: packagePage
		},
		{
			path: '/advertiser/ads',
			component: adsPage
		},
		{
			path: '/advertiser/ad',
			component: adPage
		},
		{
			path: '/advertiser/ad/:id',
			component: adPage
		},
		{
			path: '/advertiser/expends',
			component: ExpendsPage
		},
		{
			path: '/advertiser/recharges',
			component: RechargesPage
		},
		{
			path: '/advertiser/loginlogs',
			component: loginlogsPage
		},
		{
			path: '/advertiser/messages',
			component: Messages
		},
		
		
		
  	]
})
