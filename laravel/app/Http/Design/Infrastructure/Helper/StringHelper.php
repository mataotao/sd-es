<?php
namespace App\Http\Design\Infrastructure\Helper;


class StringHelper
{
    /**
     * 转化/解码 html实体字符
     * @param $str
     * @param bool $type true转化 否则解码
     * @param int $style 默认 转化/解码双引号和单引号
     * @return string
     */
    public static function htmlentites($str, $type = true, $style = ENT_QUOTES)
    {
        if ($type == true) {
            $string = htmlentities($str, $style, 'UTF-8');
        } else {
            $string = html_entity_decode($str, $style, 'UTF-8');
        }
        
        return $string;
    }
}
