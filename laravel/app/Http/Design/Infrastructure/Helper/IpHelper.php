<?php
namespace App\Http\Design\Infrastructure\Helper;


class IpHelper
{
    public static function getClientIp($type = 0)
    {
        $type = $type ? 1 : 0;
        static $ip = null;
        if ($ip !== null) return $ip[$type];
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $arr = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $pos = array_search('unknown', $arr);
            if (false !== $pos) unset($arr[$pos]);
            $ip = trim($arr[0]);
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        // IP地址合法验证
        $long = sprintf("%u", ip2long($ip));
        $ip   = $long ? [
            $ip,
            $long,
        ] : [
            '0.0.0.0',
            0,
        ];
        
        return $ip[$type];
    }
}
