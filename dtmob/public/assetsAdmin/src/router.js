import Vue from 'vue'
import Router from 	'vue-router'
import homePage from './views/home.vue'
//站长
import Webmasters from 			'./views/webmaster/webmasters.vue'
import Webmaster from 			'./views/webmaster/webmaster.vue'
import Webmasterads from 		'./views/webmaster/ads.vue'
import Webmasterad from 		'./views/webmaster/ad.vue'
import WebmasterWebsites from 	'./views/webmaster/websites.vue'
import WebmasterCategorys from 	'./views/webmaster/categorys.vue'
import WebmasterMoneys from 	'./views/webmaster/moneys.vue'
import WebmasterLoginlogs from 	'./views/webmaster/loginlogs.vue'
import WebmasterEarningday from 	'./views/webmaster/earningday.vue'
import WebmasterEarningClick from 	'./views/webmaster/earningclick.vue'
import WebmasterlowerEarnings from 	'./views/webmaster/lowerearnings.vue'

//统计
import StatIntervals from 	'./views/webmaster/stat_intervals.vue'
import StatLocations from 	'./views/webmaster/stat_locations.vue'
import StatRegions from 	'./views/webmaster/stat_regions.vue'
import StatScreens from 	'./views/webmaster/stat_screens.vue'

//联盟广告
import Alliances from 	'./views/alliance/alliances.vue'
import Alliance from 	'./views/alliance/alliance.vue'
import Spendings from 	'./views/alliance/spendings.vue'
import AllianceFluxs from 	'./views/alliance/fluxs.vue'
import AllianceFluxExpends from	'./views/alliance/fluxexpends.vue'

//广告主
import Advertisers from 		'./views/advertiser/advertisers.vue'
import Advertiser from 			'./views/advertiser/advertiser.vue'
import Advertiserads from 		'./views/advertiser/ads.vue'
import Advertiserad from 		'./views/advertiser/ad.vue'
import AdvertiserAdCategorys from	'./views/advertiser/adcategorys.vue'
import AdvertiserConsumes from 	'./views/advertiser/consumes.vue'
import AdvertiserPackages from 	'./views/advertiser/packages.vue'
import AdvertiserPackage from 	'./views/advertiser/package.vue'
import AdvertiserAgains from 	'./views/advertiser/agains.vue'
import AdvertiserLoginlogs from './views/advertiser/loginlogs.vue'

//代理
import Agents from 			'./views/agent/agents.vue'
import AgentEarnings from 	'./views/agent/earnings.vue'
import AgentLogs from 		'./views/agent/logs.vue'
import AgentMoneyLogs from 	'./views/agent/moneylogs.vue'

//财务
import PropertyTakemoneys from 	'./views/property/takemoneys.vue'
import PropertyRewards from 	'./views/property/rewards.vue'
import PropertyRecharges from 	'./views/property/recharges.vue'
import PropertyExtractServices from 	'./views/property/extract_service.vue'
import PropertyExtractBusines from 	'./views/property/extract_busine.vue'
import PropertyExpenditures from 	'./views/property/expenditures.vue'
import PropertyIncomes from 	'./views/property/incomes.vue'
import AgentMoneys from 	'./views/agent/moneys.vue'

import Users from 				'./views/users.vue'
import Departments from './views/departments.vue'
import Setting from './views/setting.vue'
import Messages from './views/messages.vue'

//文章
import Articles from './views/articles.vue'
import ArticleCategorys from './views/article_categorys.vue'

//客户管理
import Customers from './views/customers.vue'
import CustomersSources from './views/customers_sources.vue'
import CustomersMails from './views/customers_mails.vue'

//数据魔方
import PushLogs from './views/data/push_logs.vue'
import WebmasterEntire from './views/data/webmaster_entire.vue'
import WebmasterClicks from './views/data/webmaster_clicks.vue'

//联盟代理
import AllianceAgent from './views/alliance_agent.vue'

import Mails from './views/mails.vue'
import User from './views/user.vue'
import menus from './views/menus.vue'

Vue.use(Router)


export default new Router({
	mode: 'history',
	routes: [{
			path: '/admin',
		 	component: homePage
		},{	//站长
			path: '/admin/webmasters',
			component: Webmasters
		},{
			name: 'package',
			path: '/admin/webmaster/ads',
			component: Webmasterads
		},{
			name: 'package',
			path: '/admin/webmaster/ad/:id',
			component: Webmasterad
		},{
			path: '/admin/webmaster/websites',
			component: WebmasterWebsites
		},{
			path: '/admin/webmaster/categorys',
			component: WebmasterCategorys
		},{
			path: '/admin/webmaster/moneys',
			component: WebmasterMoneys
		},{
			path: '/admin/webmaster/loginlogs',
			component: WebmasterLoginlogs
		},{
			path: '/admin/webmaster/earningday/:webmaster_ad_id',
			component: WebmasterEarningday
		},{
			path: '/admin/webmaster/earningclick/:webmaster_ad_id',
			component: WebmasterEarningClick
		},{
			path: '/admin/webmaster/:id',
			component: Webmaster
		},{
			path: '/admin/webmaster/lower/earnings',
			component: WebmasterlowerEarnings
		},{	//统计
			path: '/admin/stat/intervals/:webmaster_ad_id',
			component: StatIntervals
		},{
			path: '/admin/stat/locations/:webmaster_ad_id',
			component: StatLocations
		},{
			path: '/admin/stat/regions/:webmaster_ad_id',
			component: StatRegions
		},{
			path: '/admin/stat/screens/:webmaster_ad_id',
			component: StatScreens
		},{
			path: '/admin/alliance/agents',
			component: AllianceAgent
		},{	//联盟
			path: '/admin/alliance/fluxs',
			component: AllianceFluxs
		},{
			path: '/admin/alliance/flux/expends',
			component: AllianceFluxExpends
		},{
			path: '/admin/alliances',
			component: Alliances
		},{
			path: '/admin/alliance/spendings',
			component: Spendings
		},{
			path: '/admin/alliance/:id',
			component: Alliance
		},{	//广告主
			path: '/admin/advertisers',
			component: Advertisers
		},{
			path: '/admin/advertiser/ad/categorys',
			component: AdvertiserAdCategorys
		},{
			path: '/admin/advertiser/ads',
			component: Advertiserads
		},{
			path: '/admin/advertiser/ad/:id',
			component: Advertiserad
		},{
			path: '/admin/advertiser/ad',
			component: Advertiserad
		},{
			path: '/admin/advertiser/consumes',
			component: AdvertiserConsumes
		},{
			path: '/admin/advertiser/packages',
			component: AdvertiserPackages
		},{
			path: '/admin/advertiser/package/:id',
			component: AdvertiserPackage
		},{
			path: '/admin/advertiser/package',
			component: AdvertiserPackage
		},{
			path: '/admin/advertiser/loginlogs',
			component: AdvertiserLoginlogs
		},{
			path: '/admin/advertiser/agains',
			component: AdvertiserAgains
		},{
			path: '/admin/advertiser/:id',
			component: Advertiser
		},{	//代理
			path: '/admin/agents',
			component: Agents
		},{
			path: '/admin/agent/earnings',
			component: AgentEarnings
		},{
			path: '/admin/agent/logs',
			component: AgentLogs
		},{
			path: '/admin/agent/moneylogs',
			component: AgentMoneyLogs
		},{
			path: '/admin/agent/moneys',
			component: AgentMoneys
		},{	//财务
			path: '/admin/property/takemoneys',
			component: PropertyTakemoneys
		},{
			path: '/admin/property/rewards',
			component: PropertyRewards
		},{
			path: '/admin/property/recharges',
			component: PropertyRecharges
		},{
			path: '/admin/property/extract/services',
			component: PropertyExtractServices
		},{
			path: '/admin/property/extract/busines',
			component: PropertyExtractBusines
		},{
			path: '/admin/property/expenditures',
			component: PropertyExpenditures
		},{
			path: '/admin/property/incomes',
			component: PropertyIncomes
		},{	//会员
			path: '/admin/users',
			component: Users
		},{
			path: '/admin/departments',
			component: Departments
		},{
			path: '/admin/setting',
			component: Setting
		},{
			path: '/admin/messages',
			component: Messages
		},{
			path: '/admin/articles',
			component: Articles
		},{
			path: '/admin/article/categorys',
			component: ArticleCategorys
		},{
			path: '/admin/customers',
			component: Customers
		},{
			path: '/admin/customers/sources',
			component: CustomersSources
		},{
			path: '/admin/customers/mails/:customer_id',
			component: CustomersMails
		},{
			path: '/admin/data/push_logs',
			component: PushLogs
		},{
			path: '/admin/data/webmaster_entire',
			component: WebmasterEntire
		},{
			path: '/admin/data/webmaster_clicks',
			component: WebmasterClicks
		},{
			path: '/admin/mails',
			component: Mails
		},{
			path: '/admin/user',
			component: User
		},{
			path: '/admin/menus',
			component: menus
		}]
})
