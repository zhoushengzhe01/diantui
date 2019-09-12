import Vue from 'vue'
import Router from 'vue-router'
import homePage from './views/home.vue'
import lowersPage from './views/lowers.vue'
import earningsPage from './views/earnings.vue'
import moneysPage from './views/moneys.vue'
import userPage from './views/user.vue'
import logsPage from './views/logs.vue'
import linkPage from './views/link.vue'

Vue.use(Router)

export default new Router({
	mode: 'history',
	routes: [
		{
			name: 'home',
			path: '/agent',
		 	component: homePage
		},
		{
			name: 'lowers',
			path: '/agent/lowers',	//下线列表
			component: lowersPage
		},
		{
			name: 'ad',
			path: '/agent/earnings',//收益报表
			component: earningsPage	
		},
		{
			name: 'ad',
			path: '/agent/moneys',//代理提现
			component: moneysPage	
		},
		{
			name: 'myad',
			path: '/agent/user',	//个人信息
			component: userPage
		},
		{
			name: 'logs',
			path: '/agent/logs',	//登录日志
			component: logsPage
		},
		{
			name: 'myad',
			path: '/agent/link',	//推广链接
			component: linkPage
		}
  	]
})
