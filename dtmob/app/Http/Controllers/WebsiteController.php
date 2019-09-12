<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Helpers\Helper;
use App\Model\Message;
use App\Model\Users;
use App\Model\Articles;
use App\Model\ArticlesDetail;
use App\Model\Webmaster;
use App\Model\Advertiser;
use App\Model\AllianceAgent;
use App\Model\Agents;

use Session;

class WebsiteController extends Controller
{
    //首页
    public function getIndex(Request $request)
    {
        $data = Cache::remember('index_data', 1, function() {
            #联盟
            $alliance_agent = AllianceAgent::where('domain', 'like', '%'.$_SERVER['HTTP_HOST'].'%')->first();
            
            if(empty($alliance_agent))
            {
                die("缺少参数");
            }
            //公司动态
            $news = Articles::where('category_id', '=', '1')
                ->where('alliance_agent_id', $alliance_agent->id)
                ->where('state', '=', '1')->orderBy('id', 'desc')->paginate(3);

            $data = [
                'title' => '点推首页',
                'group' => self::$group,
                'news' => $news,
            ];

            return $data;
        });

        return view('website.index', $data);
    }

    //产品中心
    public function getProduct(Request $request)
    {
        $data = [
            'title' => '产品优势',
            'group'=>self::$group,
        ];
        return view('website.product', $data);
    }

    //公告中心
    public function getNews(Request $request)
    {
        #联盟
        $alliance_agent = AllianceAgent::where('domain', 'like', '%'.$_SERVER['HTTP_HOST'].'%')->first();
        
        if(empty($alliance_agent))
        {
            die("缺少数据");
        }

        $news = Articles::where('category_id', '=', '1')
            ->where('alliance_agent_id', $alliance_agent->id)
            ->where('state', '=', '1')->orderBy('id', 'desc')->paginate(15);

        $data = [
            'news'=>$news,
            'title'=>'公告中心',
            'group'=>self::$group,
        ];
        return view('website.news', $data);
    }

    //公告中心
    public function getNew(Request $request, $id)
    {
        if(empty($id))
        {
            die("缺少ID");
        }

        #联盟
        $alliance_agent = AllianceAgent::where('domain', 'like', '%'.$_SERVER['HTTP_HOST'].'%')->first();

        if(empty($alliance_agent))
        {
            die("缺少数据");
        }
        
        $article = Articles::select('articles.title', 'articles.intro', 'articles.created_at', 'articles_detail.content')
            ->where('articles.id', '=', $id)
            ->where('articles.state', '=', '1')
            ->where('alliance_agent_id', $alliance_agent->id)
            ->join('articles_detail', 'articles_detail.article_id', '=', 'articles.id')
            ->first();

        //上一篇
        $pro_new = Articles::where('id', '<', $id)->orderBy('id', 'desc')->first();
        //下一篇
        $next_new = Articles::where('id', '>', $id)->orderBy('id', 'asc')->first();
        
        $data = [
            'article'=>$article,
            'pro_new'=>$pro_new,
            'next_new'=>$next_new,
            'title'=>'公告中心',
            'group'=>self::$group,
        ];
        return view('website.new', $data);
    }

    //帮助中心
    public function getHelp(Request $request)
    {
        $data = [
            'title'=>'帮助中心',
            'group'=>self::$group,
        ];
        return view('website.help', $data);
    }

    //关于我们
    public function getAbout(Request $request)
    {
        $data = [
            'title'=>'关于我们',
            'group'=>self::$group,
        ];
        return view('website.about', $data);
    }

    //注册协议
    public function getProtocol(Request $request)
    {
        $data = [
            'title'=>'关于我们',
            'group'=>self::$group,
            'user_agent'=>$_SERVER['HTTP_USER_AGENT'],
        ];
        return view('website.protocol', $data);
    }

    //广告主登陆
    public function getLogin(Request $request)
    {
        $data = [
            'title'=>'用户登陆',
            'group'=>self::$group,
        ];
        return view('website.login', $data);
    }

    //注册
    public function getRegister(Request $request)
    {
        //客户推荐
        $uid = trim($request->input('uid'));

        //站长推荐
        $wid = trim($request->input('wid'));
        if(!empty($wid)){
            $webmaster = Webmaster::where('id', '=', $wid)->first();
            if(!empty($webmaster)){
                $uid = $webmaster->service_id;
            }
        }
        //代理推荐
        $aid = trim($request->input('aid'));
        if(!empty($aid)){
            $agent = Agents::where('id', '=', $aid)->first();
            if(!empty($agent)){
                $uid = $agent->busine_id;
            }
        }

        //识别商务
        $user = Users::where('id', '=', $uid)->where('state', '=', '1')->first();
        if(!empty($user)){
            $department_id = $user->department_id;
        }else{
            $department_id = 0;
        }

        //商务查找
        $services = Users::where('department_id', '=', '3')->where('state', '=', '1')->get()->toArray();
        //媒介查找
        $busines = Users::where('department_id', '=', '4')->where('state', '=', '1')->get()->toArray();
        shuffle($services);
        shuffle($busines);
        $data = [
            'services'=>$services,
            'busines'=>$busines,
            'department_id'=>$department_id,
            'uid'=>$uid,
            'title'=>'用户注册',
            'webmaster_id'=>$wid,
            'agent_id'=>$aid,
            'group'=>self::$group,
        ];
        return view('website.register', $data);
    }

    //后台登陆
    public function getLoginAgent(Request $request)
    {
        $data = [
            'title' => '代理登录',
            'group' => self::$group,
        ];
        
        return view('website.loginagent', $data);
    }

    //后台登陆
    public function getLoginAdmin(Request $request)
    {
        $data = [
            'title' => '后台登录',
            'group' => self::$group,
        ];
        
        return view('website.loginadmin', $data);
    }

    //获取列表
    public function getArticles(Request $request, $category_id)
    {
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        #联盟
        $alliance_agent = AllianceAgent::where('domain', 'like', '%'.$_SERVER['HTTP_HOST'].'%')->first();
        if(empty($alliance_agent)){
            return response()->json(['message'=>'找不到数据1'], 300);
        }

        $articles = Articles::where('state', '1')
            ->where('category_id', $category_id)
            ->where('alliance_agent_id', $alliance_agent->id);

        $count = $articles->count();
        $articles = $articles->orderBy('sort', 'asc')->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        foreach($articles as $k=>$v)
        {
            $articles_detail = ArticlesDetail::where('article_id', '=', $v->id)->first();
            $articles[$k]['content'] = $articles_detail->content;
        }
        
        $data = [
            'count'=>$count,
            'articles'=>$articles,
        ];

        return response()->json(['data'=>$data], 200);
    }

    //获取列表
    public function getArticle(Request $request, $category_id, $id)
    {
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        #联盟
        $alliance_agent = AllianceAgent::where('domain', 'like', '%'.$_SERVER['HTTP_HOST'].'%')->first();
        if(empty($alliance_agent)){
            return response()->json(['message'=>'找不到数据'], 300);
        }

        $article = Articles::where('state', '1')
            ->where('category_id', $category_id)
            ->where('alliance_agent_id', $alliance_agent->id)
            ->where('id', $id)
            ->orderBy('id', 'desc')
            ->first();


        if(empty($article)){
            return response()->json(['message'=>'找不到数据'], 300);
        }

        $articles_detail = ArticlesDetail::where('article_id', '=', $article->id)->first();
        $article['content'] = $articles_detail->content;

        //上一个
        $previou_article = Articles::where('state', '1')->where('id', '<', $id)->where('category_id', $category_id)->where('alliance_agent_id', $alliance_agent->id)->orderBy('id', 'desc')->first();
        //下一个
        $next_article = Articles::where('state', '1')->where('id', '>', $id)->where('category_id', $category_id)->where('alliance_agent_id', $alliance_agent->id)->orderBy('id', 'asc')->first();
        
        $data = [
            'article'=>$article,
            'previou_article'=>$previou_article,
            'next_article'=>$next_article,
        ];

        return response()->json(['data'=>$data], 200);
    }
}