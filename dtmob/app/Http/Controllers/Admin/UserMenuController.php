<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

use App\Model\Users;
use App\Model\UsersMenu;
use Hash;

class UserMenuController extends ApiController
{

    public function getMenus(Request $request)
    {
        self::Admin();

        //一级菜单
        $menus = UsersMenu::where('status', '=', '1')->where('pid', '=', '0')->orderBy('sort', 'asc')->get();

        $data = [];
        //二级菜单
        foreach($menus as $key=>$val)
        {
            $data[$key]['id'] = $val->id;
            $data[$key]['label'] = $val->name;

            $childrens = UsersMenu::where('status', '=', '1')->where('pid', '=', $val->id)->orderBy('sort', 'asc')->get();
            if( count($childrens) > 0 )
            {
                $data[$key]['children'] = [];

                foreach($childrens as $k=>$v)
                {
                    $data[$key]['children'][$k]['id'] = $v->id;
                    $data[$key]['children'][$k]['label'] = $v->name;
                }
            }
        }

        $data = [
            'menus'=>$data,
        ];
        
        return response()->json(['data'=>$data], 200);
    }

    //列表
    public function getUserMenus(Request $request)
    {
        self::Admin();

        //一级菜单
        $menus = UsersMenu::where('pid', '=', '0')->orderBy('sort', 'asc')->get();

        $result = [];
        //二级菜单
        foreach($menus as $key=>$val)
        {
            $result[] = $val;
            $lists = UsersMenu::where('pid', '=', $val->id)->orderBy('sort', 'asc')->get();
            if( count($lists)>0 )
            {
                foreach($lists as $k=>$v)
                {
                    $result[] = $v;
                }
            }
        }

        $data = [
            'menus'=>$result,
        ];

        return response()->json(['data'=>$data], 200);
    }

    //添加
    public function postUserMenu(Request $request)
    {
        self::Admin();

        $pid = intval($request->input('pid'));
        $name = trim($request->input('name'));
        $type = trim($request->input('type'));
        $icon = $request->input('icon');
        $url = trim($request->input('url'));
        $sort = trim($request->input('sort'));
        $method = trim($request->input('method'));
        $status = $request->input('status');
        

        if(empty($name)){
            return response()->json(['message'=>'请输入菜单名称'], 300);
        }
        if(empty($type)){
            return response()->json(['message'=>'请选择类型'], 300);
        }
        if(empty($url)){
            return response()->json(['message'=>'请输入url地址'], 300);
        }
        if(empty($method)){
            return response()->json(['message'=>'请选择请求方式'], 300);
        }

        $menu = new UsersMenu();
        $menu->pid = $pid;
        $menu->name = $name;
        $menu->type = $type;
        $menu->icon = $icon;
        $menu->url  = $url;
        $menu->sort  = $sort;
        $menu->method = $method;
        $menu->status = $status;

        if(!empty($menu->save()))
        {
            return response()->json(['message'=>'添加成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'添加失败'], 300);
        }
    }

    //修改
    public function putUserMenu(Request $request, $id)
    {
        self::Admin();

        if(empty($id)){
            return response()->json(['message'=>'错误输入口，缺少ID'], 400);
        }

        $pid = intval($request->input('pid'));
        $name = trim($request->input('name'));
        $type = trim($request->input('type'));
        $icon = $request->input('icon');
        $url = trim($request->input('url'));
        $sort = trim($request->input('sort'));
        $method = trim($request->input('method'));
        $status = $request->input('status');

        if(empty($name)){
            return response()->json(['message'=>'请输入菜单名称'], 300);
        }
        if(empty($type)){
            return response()->json(['message'=>'请选择类型'], 300);
        }
        if(empty($url)){
            return response()->json(['message'=>'请输入url地址'], 300);
        }
        if(empty($method)){
            return response()->json(['message'=>'请选择请求方式'], 300);
        }

        $menu = UsersMenu::where('id', '=', $id)->first();
        if(empty($menu)){
            return response()->json(['message'=>'找不到菜单信息'], 300);
        }

        $menu->pid = $pid;
        $menu->name = $name;
        $menu->type = $type;
        $menu->icon = $icon;
        $menu->url  = $url;
        $menu->sort  = $sort;
        $menu->method = $method;
        $menu->status = $status;

        if(!empty($menu->save()))
        {
            return response()->json(['message'=>'修改成功'], 200);
        }
        else
        {
            return response()->json(['message'=>'修改失败'], 300);
        }
    }

}
