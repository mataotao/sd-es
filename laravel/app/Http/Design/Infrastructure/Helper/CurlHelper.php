<?php
namespace App\Http\Design\Infrastructure\Helper;


class CurlHelper
{
    
    public static function url($url, $parameter)
    {
        return $url . http_build_query($parameter);
    }
    
    public static function curlPost($url, $data)
    {
        $ch = curl_init();
        if (empty(config('system.proxy'))) {
            curl_setopt($ch, CURLOPT_PROXY, config('system.proxy'));
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //设置为POST
        curl_setopt($ch, CURLOPT_POST, 1);
        //把POST的变量加上
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        $output = curl_exec($ch);
        curl_close($ch);
        
        return $output;
    }
    
    public static function curlGet($url)
    {
        $ch = curl_init();
        //设置选项，包括URL
        if (empty(config('system.proxy'))) {
            curl_setopt($ch, CURLOPT_PROXY, config('system.proxy'));
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);
        
        return $output;
    }
    
    public static function curlHttpsGet($upUrl)
    {
        $ch = curl_init();
        
        if (empty(config('system.proxy'))) {
            curl_setopt($ch, CURLOPT_PROXY, config('system.proxy'));
        }
        
        curl_setopt($ch, CURLOPT_URL, $upUrl);
        
        curl_setopt($ch, CURLOPT_HEADER, 0);
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); //不验证证书
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false); //不验证证书
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        
        $res = curl_exec($ch);
        
        return $res;
    }
    
    /**
     * 批量curl
     * @param $arrUrls ['var'=>'url']
     * @return array
     */
    public static function curlMultiGet($arrUrls)
    {
        $mh = curl_multi_init();
        
        $responsesKeyMap = [];
        
        $arrResponses = [];
        
        // 添加 Curl 批处理会话
        foreach ($arrUrls as $urlsKey => $strUrlVal) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $strUrlVal);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            if (empty(config('system.proxy'))) {
                curl_setopt($ch, CURLOPT_PROXY, config('system.proxy'));
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_multi_add_handle($mh, $ch);
            $strCh                   = (string)$ch;
            $responsesKeyMap[$strCh] = $urlsKey;
        }
        
        // 批处理执行
        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
            
        } while (CURLM_CALL_MULTI_PERFORM == $mrc);
        
        while ($active && CURLM_OK == $mrc) {
            
            if (-1 == curl_multi_select($mh)) {
                usleep(100);
            }
            
            do {
                
                $mrc = curl_multi_exec($mh, $active);
                
                if (CURLM_OK == $mrc) {
                    while ($multiInfo = curl_multi_info_read($mh)) {
                        $curl_info                              = curl_getinfo($multiInfo['handle']);
                        $curl_error                             = curl_error($multiInfo['handle']);
                        $curl_results                           = curl_multi_getcontent($multiInfo['handle']);
                        $strCh                                  = (string)$multiInfo['handle'];
                        $arrResponses[$responsesKeyMap[$strCh]] = compact('curl_info', 'curl_error', 'curl_results');
                        curl_multi_remove_handle($mh, $multiInfo['handle']);
                        curl_close($multiInfo['handle']);
                    }
                }
                
            } while (CURLM_CALL_MULTI_PERFORM == $mrc);
        }
        $map = [];
        foreach ($arrResponses as $key => $arrResponse) {
            $map[$key] = $arrResponse['curl_results'];
        }
        // 关闭资源
        curl_multi_close($mh);
        
        return $map;
        
    }
    
    public static function curlGetReptile($url)
    {
        $ch = curl_init();
        //设置选项，包括URL
        if (empty(config('system.proxy'))) {
            curl_setopt($ch, CURLOPT_PROXY, config('system.proxy'));
        }
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //执行并获取HTML文档内容
        $output = curl_exec($ch);
        //释放curl句柄
        curl_close($ch);
        $output = mb_convert_encoding($output, 'utf-8', 'GBK,UTF-8,ASCII');
        return $output;
    }
    
    /**
     * 批量curl
     * @param $arrUrlsData ['var'=>['url'=>$url,'data'=>$data]]
     * @return array
     */
    public static function curlMultiPost($arrUrlsData)
    {
        $mh = curl_multi_init();
        
        $responsesKeyMap = [];
        
        $arrResponses = [];
        
        // 添加 Curl 批处理会话
        foreach ($arrUrlsData as $urlsKey => $strUrlVal) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $strUrlVal['url']);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            if (empty(config('system.proxy'))) {
                curl_setopt($ch, CURLOPT_PROXY, config('system.proxy'));
            }
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //设置为POST
            curl_setopt($ch, CURLOPT_POST, 1);
            //把POST的变量加上
            curl_setopt($ch, CURLOPT_POSTFIELDS, $strUrlVal['data']);
            curl_multi_add_handle($mh, $ch);
            $strCh                   = (string)$ch;
            $responsesKeyMap[$strCh] = $urlsKey;
        }
        
        // 批处理执行
        $active = null;
        do {
            $mrc = curl_multi_exec($mh, $active);
            
        } while (CURLM_CALL_MULTI_PERFORM == $mrc);
        
        while ($active && CURLM_OK == $mrc) {
            
            if (-1 == curl_multi_select($mh)) {
                usleep(100);
            }
            
            do {
                
                $mrc = curl_multi_exec($mh, $active);
                
                if (CURLM_OK == $mrc) {
                    while ($multiInfo = curl_multi_info_read($mh)) {
                        $curl_info                              = curl_getinfo($multiInfo['handle']);
                        $curl_error                             = curl_error($multiInfo['handle']);
                        $curl_results                           = curl_multi_getcontent($multiInfo['handle']);
                        $strCh                                  = (string)$multiInfo['handle'];
                        $arrResponses[$responsesKeyMap[$strCh]] = compact('curl_info', 'curl_error', 'curl_results');
                        curl_multi_remove_handle($mh, $multiInfo['handle']);
                        curl_close($multiInfo['handle']);
                    }
                }
                
            } while (CURLM_CALL_MULTI_PERFORM == $mrc);
        }
        $map = [];
        foreach ($arrResponses as $key => $arrResponse) {
            $map[$key] = $arrResponse['curl_results'];
        }
        // 关闭资源
        curl_multi_close($mh);
        
        return $map;
        
    }
}
