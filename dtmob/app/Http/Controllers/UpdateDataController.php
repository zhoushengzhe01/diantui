<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Model\Setting;
use App\Model\WebmasterAds;
use App\Model\Webmaster;
use App\Model\WebmasterWebsite;
use App\Model\Advertiser;
use App\Model\AdvertiserAds;
use App\Model\MatterPackage;
use App\Model\Alliance;
use App\Model\AllianceFlux;

class UpdateDataController extends Controller
{
    public function getData(Request $request)
    {
        $data = [];
        
        $data['setting'] = $this->setting($request);
        $data['webmasters'] = $this->webmasters($request);
        $data['webmasterads'] = $this->webmasterAds($request);
        $data['advertisers'] = $this->advertisers($request);
        $data['advertiserads'] = $this->advertiserAds($request);
        $data['matterpackages'] = $this->matterPackages($request);
        $data['alliances'] = $this->alliances($request);
        $data['alliancefluxs'] = $this->alliancefluxs($request);

        return $data;
    }
    //同步设置
    public function setting()
    {
        $result = Setting::select('id', 'key', 'value')->get();
        $data = [];
        foreach($result as $key=>$val){
            $data[$val['key']] = $val['value'];
        }
        return $data;
    }
    //同步站长
    public function webmasters()
    {
        $webmasters = Webmaster::where('state', '=', '1')->get();
        
        foreach($webmasters as $key=>$val)
        {
            $websites = [];

            if($val['is_limit_domain']=='1')
            {    
                $result = WebmasterWebsite::select('domain')->where('webmaster_id', '=', $val['id'])->get();
                
                foreach($result as $k=>$v)
                {
                    $websites[] = $v['domain'];
                }
            }

            $webmasters[$key]['websites'] = $websites;
        }

        return $webmasters;
    }
    //同步站长广告
    public function webmasterAds()
    {
        $data = WebmasterAds::where('state', '=', '1')->get();
        return $data;
    }
    //同步广告主
    public function advertisers()
    {
        $data = Advertiser::select('advertiser.*')
            ->join('advertiser_ads', 'advertiser_ads.advertiser_id', '=', 'advertiser.id')
            ->where('advertiser_ads.state', '=', '1')
            ->distinct('advertiser.id')
            ->get();

        return $data;
    }
    //同步广告主广告
    public function advertiserAds()
    {
        $data = AdvertiserAds::where('state', '=', '1')->get();

        return $data;
    }
    //同步素材
    public function matterPackages()
    {
        $data = MatterPackage::where('state', '=', '1')->get();

        foreach($data as $key=>$val)
        {
            $data[$key]['picture1'] = json_decode($val['picture1'], true);
            $data[$key]['picture2'] = json_decode($val['picture2'], true);
            $data[$key]['picture3'] = json_decode($val['picture3'], true);
        }

        return $data;
    }
    //联盟
    public function alliances()
    {
        $data = Alliance::where('state', '=', '1')->get();

        return $data;
    }
    //直链
    public function allianceFluxs()
    {
        $data = AllianceFlux::where('state', '=', '1')->get();

        return $data;
    }
    // 同步设置
    public function getSetting(Request $request)
    {
        $result = Setting::select('id', 'key', 'value')->get();

        $data = [];
        foreach($result as $key=>$val){
            $data[$val['key']] = $val['value'];
        }
        return $data;

        return response()->json(['data'=>$data], 200);
    }

    // 同步站长
    public function getWebmasters(Request $request)
    {
        $webmasters = Webmaster::where('state', '=', '1')->get();
        
        foreach($webmasters as $key=>$val)
        {

            $websites = [];

            if($val['is_limit_domain']=='1')
            {    
                $result = WebmasterWebsite::select('domain')->where('webmaster_id', '=', $val['id'])->get();
                
                foreach($result as $k=>$v)
                {
                    $websites[] = $v['domain'];
                }
            }

            $webmasters[$key]['websites'] = $websites;
        }

        return response()->json(['data'=>$webmasters], 200);
    }

    // 同步站长广告
    public function getWebmasterAds(Request $request)
    {
        $data = WebmasterAds::where('state', '=', '1')->get();

        return response()->json(['data'=>$data], 200);
    }

    // 同步广告主
    public function getAdvertisers(Request $request)
    {
        $data = Advertiser::select('advertiser.*')
            ->join('advertiser_ads', 'advertiser_ads.advertiser_id', '=', 'advertiser.id')
            ->where('advertiser_ads.state', '=', '1')
            ->distinct('advertiser.id')
            ->get();

        return response()->json(['data'=>$data], 200);
    }

    // 同步广告主广告
    public function getAdvertiserAds(Request $request)
    {
        $data = AdvertiserAds::where('state', '=', '1')->get();

        return response()->json(['data'=>$data], 200);
    }

    // 同步素材
    public function getMatterPackages(Request $request)
    {
        $data = MatterPackage::where('state', '=', '1')->get();

        foreach($data as $key=>$val)
        {
            $data[$key]['picture1'] = json_decode($val['picture1'], true);
            $data[$key]['picture2'] = json_decode($val['picture2'], true);
            $data[$key]['picture3'] = json_decode($val['picture3'], true);
        }
        return response()->json(['data'=>$data], 200);
    }


    //联盟
    public function getAlliances(Request $request)
    {
        $data = Alliance::where('state', '=', '1')->get();

        return response()->json(['data'=>$data], 200);
    }

    //直链
    public function getAllianceFluxs(Request $request)
    {
        $data = AllianceFlux::where('state', '=', '1')->get();

        return response()->json(['data'=>$data], 200);
    }
}