<?php
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//官网路由
Route::get('/', 'WebsiteController@getIndex');
Route::get('index', 'WebsiteController@getIndex');
Route::get('product', 'WebsiteController@getProduct');
Route::get('news', 'WebsiteController@getNews');
Route::get('new/{id}', 'WebsiteController@getNew');
Route::get('help', 'WebsiteController@getHelp');
Route::get('about', 'WebsiteController@getAbout');
Route::get('protocol', 'WebsiteController@getProtocol');
Route::get('login', 'WebsiteController@getLogin');
Route::get('register', 'WebsiteController@getRegister');
Route::get('adtype', function(){ return view('adtype'); });
Route::get('en', function(){ return view('en'); });
Route::get('admin/login', 'WebsiteController@getLoginAdmin');

//API数据同步输出
Route::get('data/getdata.json', 'UpdateDataController@getData');
Route::get('data/setting.json', 'UpdateDataController@getSetting');
Route::get('data/webmasters.json', 'UpdateDataController@getWebmasters');
Route::get('data/webmaster/ads.json', 'UpdateDataController@getWebmasterAds');
Route::get('data/advertisers.json', 'UpdateDataController@getAdvertisers');
Route::get('data/advertiser/ads.json', 'UpdateDataController@getAdvertiserAds');
Route::get('data/matter/packages.json', 'UpdateDataController@getMatterPackages');
Route::get('data/alliances.json', 'UpdateDataController@getAlliances');
Route::get('data/alliance_fluxs.json', 'UpdateDataController@getAllianceFluxs');
Route::post('data/pv.json', 'UploadDataController@uploadPV');
Route::post('data/pc.json', 'UploadDataController@uploadPC');

Route::get('articles/{category_id}.json', 'WebsiteController@getArticles');
Route::get('article/{category_id}/{id}.json', 'WebsiteController@getArticle');

//站长后台路由
Route::group(['prefix' => 'webmaster', 'namespace' => 'Webmaster'], function (){
    //登陆注册
    Route::post('login.json', 'AuthController@postLogin');
    Route::post('register.json', 'AuthController@postRegister');
    Route::put('logout.json', 'AuthController@putLogout');

    #初始数据
    Route::get('initial.json', 'InitialController@getInitial');

    Route::get('data.json', 'DataController@getData');

    Route::get('websites.json', 'WebsiteController@getWebsites');
    Route::get('website/{id}.json', 'WebsiteController@getWebsite');
    Route::put('website/{id}.json', 'WebsiteController@putWebsite');
    Route::post('website.json', 'WebsiteController@postWebsite');

    Route::get('adtype.json', 'AdsController@getAdtype');
    Route::get('myads.json', 'AdsController@getMyads');
    Route::get('myad/{id}.json', 'AdsController@getMyad');
    Route::put('myad/{id}.json', 'AdsController@putMyad');
    Route::post('myad.json', 'AdsController@postMyad');

    Route::get('earnings/{type}.json', 'MoneyController@getEarnings');
    Route::get('rewards.json', 'MoneyController@getRewards');
    Route::get('moneys.json', 'MoneyController@getMoneys');

    Route::get('user.json', 'UserController@getUser');
    Route::get('bank.json', 'UserController@getBank');
    Route::put('user.json', 'UserController@putUser');
    Route::put('bank.json', 'UserController@putBank');
    Route::put('passwd.json', 'UserController@putPasswd');

    Route::get('messages.json', 'MessageController@getMessages');
    Route::get('message/{id}.json', 'MessageController@getMessage')->where('id', '[0-9]+');
    
    Route::get('lowers.json', 'LowerEarningController@getLowers');
    Route::get('lower_earnings.json', 'LowerEarningController@getLowerEarnings');

    
    Route::get('/', function(){
        return view('webmaster');
    });
    
    Route::any('{all}', function(){
        return view('webmaster');
    })->where('all', '.*');

});

//代理后台数据
Route::get('agent/login', 'WebsiteController@getLoginAgent');
Route::group(['prefix' => 'agent', 'namespace' => 'Agent'], function (){

    Route::post('login.json', 'AuthController@postLogin');
    Route::put('logout.json', 'AuthController@putLogout');

    #初始数据
    Route::get('initial.json', 'InitialController@getInitial');

    Route::get('lowers.json', 'LowersController@getLowers');
    Route::get('earnings.json', 'EarningsController@getEarnings');
    Route::get('user.json', 'UserController@getUser');
    Route::put('user.json', 'UserController@putUser');
    Route::get('bank.json', 'UserController@getBank');
    Route::put('bank.json', 'UserController@putBank');
    Route::get('logs.json', 'LogsController@getLogs');
    Route::get('moneys.json', 'MoneyController@getMoneys');
    Route::get('earnings_by_day.json', 'DataController@getEarningsByday');

    Route::get('/', function(){
        return view('agent');
    });
    Route::any('{all}', function(){
        return view('agent');
    })->where('all', '.*');
});

//广告主后台路由
Route::group(['prefix'=>'advertiser', 'namespace'=>'Advertiser'], function (){

    Route::post('login.json', 'AuthController@postLogin');
    Route::post('register.json', 'AuthController@postRegister');
    Route::put('logout.json', 'AuthController@putLogout');
    Route::get('data.json', 'DataController@getData');

    #初始数据
    Route::get('initial.json', 'InitialController@getInitial');

    //类型
    Route::get('adspositions', 'AdspositionApiController@getAdspositions');

    //素材包
    Route::get('packages.json', 'PackageController@getPackages');
    Route::get('package/{id}.json', 'PackageController@getPackage');
    Route::put('package/{id}.json', 'PackageController@putPackage');
    Route::put('package/item/{id}.json', 'PackageController@putPackageItem');
    Route::post('package', 'PackageController@postPackage');

    //媒体中心
    Route::get('matters.json', 'MatterController@getMatters');
    Route::post('matter.json', 'MatterController@postMatter');

    //广告
    Route::get('adinfo.json', 'AdsController@getAdinfo');  //公开的广告ID
    Route::get('ads.json', 'AdsController@getAds');
    Route::get('ad/{id}.json', 'AdsController@getAd');
    Route::post('ad.json', 'AdsController@postAd');
    Route::put('ad/{id}.json', 'AdsController@putAd');

    Route::get('expends/{tyep}.json', 'ExpendController@getExpends');
    Route::get('recharges.json', 'RechargeController@getRecharges');
    Route::get('recharge/{id}.json', 'RechargeController@getRecharge');
    Route::put('recharge/{id}.json', 'RechargeController@putRecharge');
    Route::post('recharge.json', 'RechargeController@postRecharge');

    Route::get('loginlogs.json', 'UserController@getLoginlogs');
    Route::get('user.json', 'UserController@getUser');
    Route::put('user.json', 'UserController@putUser');
    Route::put('passwd.json', 'UserController@putPasswd');

    Route::get('messages.json', 'MessageController@getMessages');
    Route::get('message/{id}.json', 'MessageController@getMessage')->where('id', '[0-9]+');;

    Route::get('/', function(){
        return view('advertiser');
    });
    Route::any('{all}', function(){
        return view('advertiser');
    })->where('all', '.*');
});

//总后台数据
Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function (){

    ##登录注销
    Route::post('login.json', 'AuthController@postLogin');
    Route::put('logout', 'AuthController@putLogout');
    ##后台登录前台
    Route::post('webmaster/login.json', 'LoginController@WebmasterLogin');
    Route::post('advertiser/login.json', 'LoginController@AdvertiserLogin');
    Route::post('agent/login.json', 'LoginController@AgentLogin');

    ##初始化数据
    Route::get('initial.json', 'InitialController@getInitial');

    ##站长开始
        Route::get('home/webmaster/earning/hour.json', 'IndexController@getWebmasterEarningHour');

        Route::get('webmasters.json', 'Webmaster\WebmasterController@getWebmasters');
        Route::get('webmaster/{id}.json', 'Webmaster\WebmasterController@getWebmaster')->where('id', '[0-9]+');
        Route::put('webmaster/{id}.json', 'Webmaster\WebmasterController@putWebmaster')->where('id', '[0-9]+');
        Route::get('webmaster/loginlogs.json', 'Webmaster\WebmasterController@getWebmasterLoginlogs');
        //银行
        Route::get('webmaster/banks.json', 'Webmaster\BankController@getBanks');
        Route::get('webmaster/bank/{webmaster_id}.json', 'Webmaster\BankController@getBank')->where('id', '[0-9]+');
        Route::put('webmaster/bank/{webmaster_id}.json', 'Webmaster\BankController@putBank')->where('id', '[0-9]+');
        //备注
        Route::get('webmaster/notes/{id}.json', 'Webmaster\NoteController@getNotes')->where('id', '[0-9]+');
        Route::post('webmaster/note/{id}.json', 'Webmaster\NoteController@postNote')->where('id', '[0-9]+');
        //广告
        Route::get('webmaster/ads.json', 'Webmaster\AdsController@getAds');
        Route::get('webmaster/ad/{id}.json', 'Webmaster\AdsController@getAd')->where('id', '[0-9]+');
        Route::put('webmaster/ad/{id}.json', 'Webmaster\AdsController@putAd')->where('id', '[0-9]+');
        //网站
        Route::get('webmaster/websites.json', 'Webmaster\WebsiteController@getWebsites');
        Route::get('webmaster/website/{id}.json', 'Webmaster\WebsiteController@getWebsite')->where('id', '[0-9]+');
        Route::put('webmaster/website/{id}.json', 'Webmaster\WebsiteController@putWebsite')->where('id', '[0-9]+');
        //收益
        Route::get('webmaster/earning/day/{webmaster_ad_id}.json', 'Webmaster\EarningController@getEarningDay')->where('webmaster_ad_id', '[0-9]+');
        Route::put('webmaster/earning/day/{id}.json', 'Webmaster\EarningController@putEarningDay');
        Route::get('webmaster/earning/hour/{webmaster_ad_id}.json', 'Webmaster\EarningController@getEarningHour')->where('webmaster_ad_id', '[0-9]+');
        Route::put('webmaster/earning/hour/{id}.json', 'Webmaster\EarningController@putEarningHour');
        Route::get('webmaster/earning/hour/chart/{webmaster_ad_id}.json', 'Webmaster\EarningController@getEarningHourChart')->where('webmaster_ad_id', '[0-9]+');
        Route::get('webmaster/earning/click/{webmaster_ad_id}.json', 'Webmaster\EarningController@getEarningClick')->where('webmaster_ad_id', '[0-9]+');
        //分类
        Route::get('webmaster/categorys.json', 'Webmaster\WebsiteCategoryController@getWebsiteCategorys');
        Route::get('webmaster/category/{id}.json', 'Webmaster\WebsiteCategoryController@getWebsiteCategory')->where('id', '[0-9]+');
        Route::put('webmaster/category/{id}.json', 'Webmaster\WebsiteCategoryController@putWebsiteCategory')->where('id', '[0-9]+');
        Route::post('webmaster/category.json', 'Webmaster\WebsiteCategoryController@postWebsiteCategory');
        Route::get('webmaster/moneys.json', 'Webmaster\MoneyController@getMoneys');
        //下线收益
        Route::get('webmaster/lower/earnings.json', 'Webmaster\LowerEarningController@getLowerEarnings');
    
    ##广告模块
        Route::get('advertisers.json', 'Advertiser\AdvertiserController@getAdvertisers');
        Route::get('advertiser/{id}.json', 'Advertiser\AdvertiserController@getAdvertiser')->where('id', '[0-9]+');
        Route::put('advertiser/{id}.json', 'Advertiser\AdvertiserController@putAdvertiser')->where('id', '[0-9]+');
        Route::get('advertiser/loginlogs.json', 'Advertiser\AdvertiserController@getLoginlogs');     
        //广告
        Route::get('advertiser/ads.json', 'Advertiser\AdController@getAds');
        Route::get('advertiser/ad/{id}.json', 'Advertiser\AdController@getAd')->where('id', '[0-9]+');
        Route::put('advertiser/ad/{id}.json', 'Advertiser\AdController@putAd')->where('id', '[0-9]+');
        Route::post('advertiser/ad.json', 'Advertiser\AdController@postAd');      
        Route::post('advertiser/uploadImg/{id}.json', 'Advertiser\AdController@uploadImg')->where('id', '[0-9]+');
        Route::get('advertiser/export.json','Advertiser\AdController@exportAdvertiser');
        //广告数量
        Route::get('advertiser/ad/number.json', 'Advertiser\AdController@getAdnumber');
        
        //广告分类
        Route::get('advertiser/ad/categorys.json', 'Advertiser\AdCategorysController@getCategorys');
        Route::get('advertiser/ad/category/{id}.json', 'Advertiser\AdCategorysController@getCategory');
        Route::put('advertiser/ad/category/{id}.json', 'Advertiser\AdCategorysController@putCategory');
        Route::post('advertiser/ad/category.json', 'Advertiser\AdCategorysController@postCategory');
        //素材包
        Route::get('advertiser/packages.json', 'Advertiser\PackageController@getPackages');
        Route::get('advertiser/package/{id}.json', 'Advertiser\PackageController@getPackage')->where('id', '[0-9]+');
        Route::put('advertiser/package/{id}.json', 'Advertiser\PackageController@putPackage')->where('id', '[0-9]+');
        Route::post('advertiser/package.json', 'Advertiser\PackageController@postPackage');
        //素材
        Route::get('advertiser/matters.json', 'Advertiser\MatterController@getMatters');
        Route::post('advertiser/matter.json', 'Advertiser\MatterController@postMatter');
        Route::post('advertiser/upload/image.json', 'Advertiser\MatterController@postUploadImg');
        //消耗
        Route::get('advertiser/expends/day.json', 'Advertiser\ExpendController@getExpendsDay');
        Route::get('advertiser/expends/hour/{id}.json', 'Advertiser\ExpendController@getExpendsHour');
    
        #联盟代理管理
        Route::get('alliance/agents.json', 'AllianceAgentController@getAllianceAgents');
        Route::put('alliance/agent/{id}.json', 'AllianceAgentController@putAllianceAgent');


    ##联盟
        Route::get('alliances.json', 'Alliance\AllianceController@getAlliances');
        Route::get('alliance/{id}.json', 'Alliance\AllianceController@getAlliance')->where('id', '[0-9]+');
        Route::put('alliance/{id}.json', 'Alliance\AllianceController@putAlliance')->where('id', '[0-9]+');
        Route::post('alliance.json', 'Alliance\AllianceController@postAlliance');
        //消耗
        Route::get('alliance/spendings.json', 'Alliance\AllianceController@getSpendings');
        //联盟流量
        Route::get('alliance/fluxs.json', 'Alliance\FluxController@getAllianceFluxs');
        Route::get('alliance/flux/{id}.json', 'Alliance\FluxController@getAllianceFlux')->where('id', '[0-9]+');
        Route::put('alliance/flux/{id}.json', 'Alliance\FluxController@putAllianceFlux')->where('id', '[0-9]+');
        Route::post('alliance/flux.json', 'Alliance\FluxController@postAllianceFlux');
        Route::get('alliance/flux/expends/day.json', 'Alliance\FluxExpendController@getExpendsDay');
        Route::get('alliance/flux/expends/hour.json', 'Alliance\FluxExpendController@getExpendsHour');

    ##流量池
        Route::get('alliance/flowpools.json', 'Alliance\FlowpoolController@getFlowpools');
        Route::get('alliance/flowpool/{id}.json', 'Alliance\FlowpoolController@getFlowpool');
        Route::post('alliance/flowpool.json', 'Alliance\FlowpoolController@postFlowpool');
        Route::put('alliance/flowpool/{id}.json', 'Alliance\FlowpoolController@putFlowpool');
    

    ##代理
        Route::get('agent/agents.json', 'Agent\AgentController@getAgents');
        Route::get('agent/agent/{id}.json', 'Agent\AgentController@getAgent');
        Route::put('agent/agent/{id}.json', 'Agent\AgentController@putAgent');
        Route::post('agent/agent.json', 'Agent\AgentController@postAgent');
        Route::get('agent/earnings.json', 'Agent\EarningController@getEarnings');
        Route::get('agent/logs.json', 'Agent\LogController@getLogs');
        Route::get('agent/money/logs.json', 'Agent\MoneyLogController@getMoneyLogs');

        Route::get('agent/moneys.json', 'Agent\MoneyController@getMoneys');
        Route::get('agent/money/{id}.json', 'Agent\MoneyController@getMoney');
        Route::put('agent/money/{id}.json', 'Agent\MoneyController@putMoney');
        Route::post('agent/money.json', 'Agent\MoneyController@postMoney');

    //财物管理
    Route::get('takemoneys.json', 'Property\TakemoneyController@getTakemoneys');
    Route::get('takemoney/{id}.json', 'Property\TakemoneyController@getTakemoney')->where('id', '[0-9]+');
    Route::put('takemoney/{id}.json', 'Property\TakemoneyController@putTakemoney')->where('id', '[0-9]+');
    Route::post('takemoney.json', 'Property\TakemoneyController@postTakemoney');
    Route::get('takemoney/export.json', 'Property\TakemoneyController@exportTakemoney');

    Route::get('rewards.json', 'Property\RewardController@getRewards');
    Route::get('reward/{id}.json', 'Property\RewardController@getReward')->where('id', '[0-9]+');
    Route::put('reward/{id}.json', 'Property\RewardController@putReward')->where('id', '[0-9]+');
    Route::post('reward.json', 'Property\RewardController@postReward');

    Route::get('recharges.json', 'Property\RechargeController@getRecharges');
    Route::get('recharge/{id}.json', 'Property\RechargeController@getRecharge')->where('id', '[0-9]+');
    Route::put('recharge/{id}.json', 'Property\RechargeController@putRecharge')->where('id', '[0-9]+');
    Route::post('recharge.json', 'Property\RechargeController@postRecharge')->where('id', '[0-9]+');

    Route::get('earning/services.json', 'EarningController@getServices');
    Route::get('earning/service/{id}.json', 'EarningController@getService');
    Route::get('earning/busines.json', 'EarningController@getBusines');
    Route::get('earning/busine/{id}.json', 'EarningController@getBusine');
    Route::get('property/expenditures.json', 'Property\ExpenditureController@getExpenditures');
    Route::get('property/incomes.json', 'Property\IncomeController@getIncomes');

    #会员管理
    Route::get('users.json', 'UserController@getUsers');
    Route::get('user/{id}.json', 'UserController@getUser')->where('id', '[0-9]+');
    Route::put('user/{id}.json', 'UserController@putUser')->where('id', '[0-9]+');
    Route::post('user.json', 'UserController@postUser');

    Route::get('departments.json', 'DepartmentController@getDepartments');
    Route::get('department/{id}.json', 'DepartmentController@getDepartment')->where('id', '[0-9]+');
    Route::put('department/{id}.json', 'DepartmentController@putDepartment')->where('id', '[0-9]+');
    Route::post('department.json', 'DepartmentController@postDepartment');

    

    #系统设置
    Route::get('setting.json', 'SettingController@getSetting');
    Route::put('setting.json', 'SettingController@putSetting');

    #消息中心
    Route::get('messages.json', 'MessageController@getMessages');
    Route::get('message/{id}.json', 'MessageController@getMessage')->where('id', '[0-9]+');
    Route::put('message/{id}.json', 'MessageController@putMessage')->where('id', '[0-9]+');
    Route::post('message.json', 'MessageController@postMessage');

    Route::get('articles.json', 'ArticleController@getArticles');
    Route::get('article/{id}.json', 'ArticleController@getArticle')->where('id', '[0-9]+');
    Route::put('article/{id}.json', 'ArticleController@putArticle')->where('id', '[0-9]+');
    Route::post('article.json', 'ArticleController@postArticle')->where('id', '[0-9]+');

    Route::get('article/categorys.json', 'ArticleCategoryController@getCategorys');
    Route::get('article/category/{id}.json', 'ArticleCategoryController@getCategory')->where('id', '[0-9]+');
    Route::put('article/category/{id}.json', 'ArticleCategoryController@putCategory')->where('id', '[0-9]+');
    Route::post('article/category/{id}.json', 'ArticleCategoryController@postCategory')->where('id', '[0-9]+');

    //个人资料
    Route::get('user.json', 'UserController@getMyUser');
    Route::put('user.json', 'UserController@putMyUser');

    //流量统计
    Route::get('stat/intervals/{id}.json', 'StatIntervalController@getIntervals')->where('id', '[0-9]+');
    Route::get('stat/locations/{id}.json', 'StatLocationController@getLocations')->where('id', '[0-9]+');
    Route::get('stat/regions/{id}.json', 'StatRegionController@getRegions')->where('id', '[0-9]+');
    Route::get('stat/screens/{id}.json', 'StatScreenController@getScreens')->where('id', '[0-9]+');

    //菜单列表
    Route::get('menus.json', 'UserMenuController@getMenus');
    Route::get('user/menus.json', 'UserMenuController@getUserMenus');
    Route::put('user/menu/{id}.json', 'UserMenuController@putUserMenu');
    Route::post('user/menu.json', 'UserMenuController@postUserMenu');

    //数据魔方
    Route::get('data/push_logs.json', 'Data\PushLogController@getPushLogs');
    Route::get('data/webmaster_clicks.json', 'Data\WebmasterClickController@getWebmasterClicks');

    Route::get('data/click/cache.json', 'Data\ClickCacheController@getCache');
    Route::get('data/click/browser.json', 'Data\ClickBrowserController@getBrowser');
    #Route::get('data/click/phone_brand.json', 'Data\ClickPhonebrandController@getPhonebrand');
    Route::get('data/click/system_version.json', 'Data\ClickSystemversionController@getSystemversion');
    Route::get('data/click/weixin_wap.json', 'Data\ClickWeixinwapController@getWeixinwap');
    Route::get('data/click/ios_android.json', 'Data\ClickIosandroidController@getIosandroid');
    Route::get('data/click/interval_time.json', 'Data\ClickIntervaltimeController@getIntervaltime');
    Route::get('data/click/iframe.json', 'Data\ClickIframeController@getIframe');
    Route::get('data/click/history.json', 'Data\ClickHistoryController@getHistory');
    Route::get('data/click/ipnumber.json', 'Data\ClickIpnumberController@getIpnumber');
    Route::get('data/click/source.json', 'Data\ClickSourceController@getClickSource');
    Route::get('data/click/jssystem.json', 'Data\ClickJssystemController@getJssystem');
    Route::get('data/click/screen.json', 'Data\ClickScreenController@getScreen');
    Route::get('data/click/domain.json', 'Data\ClickDomainController@getDomain');
    Route::get('data/click/city.json', 'Data\ClickCityController@getCity');

    Route::get('data/click/click_position.json', 'Data\ClickPositionController@getClickposition');
    #Route::get('data/click/baidu_keyword.json', 'Data\ClickBaidukeywordController@getBaidukeyword');
    #Route::get('data/click/sogou_keyword.json', 'Data\ClickSogoukeywordController@getSogoukeyword');
    #Route::get('data/click/sm_keyword.json', 'Data\ClickSmkeywordController@getSmkeyword');

    
    Route::get('/', function(){
        return view('admin');
    });
    Route::any('{all}', function(){
        return view('admin');
    })->where('all', '.*');

});
