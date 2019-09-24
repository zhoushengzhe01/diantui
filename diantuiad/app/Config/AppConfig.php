<?php
namespace app\Config;


class AppConfig 
{
    public static function init()
    {
        return [
            
            'root' => '/home/wwwroot/diantuiad',
            'token' => 'M0NH3J1H7KL7GWGHAMH89AVZ73BRWQHGKPZ',
            'allowSystem' => ['Android', 'iPad', 'iPhone'],

            'memcache' => [
                'host'=>'127.0.0.1',
                'port'=>'11211',
                'time'=>600 + rand(0, 60),
                'isP'=>false,
            ],
            
            'redis' => [
                'host' => '127.0.0.1',  
                'port' => '6379',
                'time' => 3600,
            ],

            //后缀
            'suffix' => ['com', 'net', 'cn', 'org', 'asia', 'tel', 'tv', 'cc', 'co', 'name', 'so', 'biz', 'info', 'tw', 'in', 'ws', 'eu', 'me', 'us', 'tv', 'pw', 'ma', 'la', 'top', 'tk', 'xyz', 'xin', 'wiki', 'club', 'ml', 'cm', 'gq', 'win', 'video', 'admin', 'hk', 'mobi', 'ren', 'cf', 'ga', 'bid', 'fm', 'wang', 'dev', 'vip', 'live', 'fun', 'run', 'shop', 'at', 'news', 'today', 'life', 'cool', 'space', 'city', 'rocks', 'store', 'world', 'ink', 'best', 'men', 'ooo', 'fashion', 'studio', 'email', 'cloud',  'center', 'at', 'network', 'website', 'one', 'cafe', 'space', 'style', 'guru', 'digital', 'design', 'buzz', 'group', 'kim', 'red', 'pro', 'work', 'de', 'ge', 'monster', 'xn--6qq986b3xl', 'xn--fiqs8s', 'xn--czr694b', 'xn--3ds443g', 'xn--ses554g', 'xn--hxt814e', 'xn--imr513n', 'xn--fiq228c5hs', 'xn--55qx5d', 'xn--io0a7i', 'xn--3bst00m'],
            
            'twosuffix' => ['com.cn', 'net.cn', 'org.cn', 'gov.cn', 'org.uk', 'ltd.uk', 'plc.uk', 'me.uk', 'co.uk', 'sd.cn', 'ln.cn', 'bj.cn', 'yn.cn', 'gs.cn', 'gd.cn', 'zj.cn', 'he.cn', 'tw.cn', 'gz.cn', 'ha.cn', 'jl.cn', 'sh.cn', 'qh.cn', 'gx.cn', 'ah.cn', 'sx.cn', 'fj.cn', 'hk.cn', 'xz.cn', 'hb.cn', 'hl.cn', 'tj.cn', 'nx.cn', 'hi.cn', 'jx.cn', 'nm.cn', 'ac.cn', 'mo.cn', 'sn.cn', 'hn.cn', 'js.cn', 'cq.cn', 'xj.cn', 'sc.cn', ],
        ];
    }

    // 调用用的
    public static function get($position)
    {
        //初始
        $value = self::init();

        $array = explode('.', $position);

        foreach($array as $k=>$v)
        {
            if( empty($value[$v]) )
            {
                return false;
            }
            else
            {
                $value = $value[$v];
            }
        }

        return $value;
    }
}

