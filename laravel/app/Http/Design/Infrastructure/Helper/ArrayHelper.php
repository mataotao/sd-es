<?php
namespace App\Http\Design\Infrastructure\Helper;


class ArrayHelper
{
    /**
     * 把二维数组的key换成field
     * @param $array
     * @param $field
     * @return array
     */
    public static function arrayToArrayKey($array, $field, $group = false,$sort=false)
    {
        $arr = [];
        if (empty($array)) {
            return $arr;
        }
        if ($group === false) {
            foreach ($array as $v) {
                if (array_key_exists($field, $v)) {
                    $arr[$v[$field]] = $v;
                }
            }
        } else {
            foreach ($array as $key=>$v) {
                if (array_key_exists($field, $v)) {
                    if($sort==true){
                        $arr[$v[$field]][$key] = $v;
                    }else{
                        $arr[$v[$field]][] = $v;
                    }
                   
                }
            }
        }
        
        return $arr;
    }
    
    /**
     * 判断数组中key存不存在,不存在默认返回null,有则返回它本身的值
     * @param $key
     * @param $array
     * @param array $additional 附加参数
     * @param null $return 不存返回的参数
     * @return null|string
     */
    public static function arrayKeyExistsNull($key, $array, $additional = [], $return = null)
    {
        if (array_key_exists($key, $array)) {
            if (!empty($additional)) {
                $addArray = $array[$key];
                foreach ($additional as $add) {
                    $addArray = $addArray[$add];
                }
                
                return $addArray;
            }
            
            return $array[$key];
        } else {
            return $return;
        }
    }
}
