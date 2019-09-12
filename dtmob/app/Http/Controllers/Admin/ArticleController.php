<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\Articles;
use App\Model\ArticlesDetail;
use App\Model\ArticlesCategory;

class ArticleController extends ApiController
{

    public function getArticles(Request $request)
    {
        self::Admin();

        //网站搜索
        $title = trim($request->input('title'));
        $category_id = trim($request->input('category_id'));
        $offset = trim($request->input('offset'));
        $limit = trim($request->input('limit'));
        if(empty($limit))
        {
            $limit = 10;
        }

        $articles = Articles::select('*');

        #联盟权限限制
        if(self::$user->alliance_agent_id!=config('other.alliance_agent_id')){
            $articles = $articles->where('alliance_agent_id', self::$user->alliance_agent_id);
        }

        if(!empty($title)){
            $articles = $articles->where('title', 'like', '%'.$title.'%');
        }
        if(!empty($category_id)){
            $articles = $articles->where('category_id', '=', $category_id);
        }

        $count = $articles->count();
        $articles = $articles->orderBy('sort', 'asc')->orderBy('id', 'desc')->offset($offset)->limit($limit)->get();

        $data = [
            'count'=>$count,
            'articles'=>$articles,
        ];

        return response()->json(['data'=>$data], 200);
    }

    public function getArticle(Request $request, $id)
    {
        self::Admin();

        if(empty($id))
            return response()->json(['message'=>'缺少参数'], 400);
        
        $article = Articles::where('id', '=', $id)->first();

        $detail = ArticlesDetail::where('article_id', '=', $article->id)->first(['content']);

        $article->content = $detail->content;

        if(empty($article))
            return response()->json(['message'=>'未找到数据'], 300);

        return response()->json(['data'=>['article'=>$article]], 200);

    }

    public function putArticle(Request $request, $id)
    {
        self::Admin();

        if(empty($id)){
            return response()->json(['message'=>'缺少参数'], 400);
        }

        $article = Articles::where('id', '=', $id)->first();

        if(empty($article)){
            return response()->json(['message'=>'未找到数据'], 300);
        }

        $present = $request->input();
        if(!empty($present['title'])){ $article->title = $present['title']; }
        if(!empty($present['alliance_agent_id'])){ $article->alliance_agent_id = $present['alliance_agent_id']; }
        if(!empty($present['category_id'])){ $article->category_id = $present['category_id']; }
        if(!empty($present['intro'])){ $article->intro = $present['intro']; }
        if(!empty($present['sort'])){ $article->sort = $present['sort']; }
        if(!empty($present['state'])){ $article->state = $present['state']; }
        if(!empty($present['content']))
        {
            $detail = ArticlesDetail::where('article_id', '=', $article->id)->first();
            $detail->content = $present['content'];
            $detail->save();
        }

        if($article->save()){
            return response()->json(['message'=>'修改成功'], 200);
        }else{
            return response()->json(['message'=>'修改失败'], 300);
        }
    }

    public function postArticle(Request $request)
    {
        self::Admin();

        $present = $request->input();

        if(empty($present['title'])){
            return response()->json(['message'=>'请输入标题'], 300);
        }
        if(empty($present['category_id'])){
            return response()->json(['message'=>'请选择类别'], 300);
        }
        if(empty($present['intro'])){
            return response()->json(['message'=>'请输入简介'], 300);
        }
        if(empty($present['sort'])){
            return response()->json(['message'=>'请输入排序'], 300);
        }
        if(empty($present['state'])){
            return response()->json(['message'=>'请选择状态'], 300);
        }
        if(empty($present['content'])){
            return response()->json(['message'=>'请输入文章内容'], 300);
        }

        $article = new Articles;
        $article->title = trim($present['title']);
        $article->alliance_agent_id = trim($present['alliance_agent_id']);
        $article->category_id = trim($present['category_id']);
        $article->intro = trim($present['intro']);
        $article->sort = trim($present['sort']);
        $article->state = trim($present['state']);

        if($article->save())
        {
            $detail = new ArticlesDetail;
            $detail->article_id = $article->id;
            $detail->content = $present['content'];
            $detail->save();
            
            return response()->json(['message'=>'添加成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'添加失败'], 300);
        }

    }

}