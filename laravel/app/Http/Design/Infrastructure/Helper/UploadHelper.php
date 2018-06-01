<?php

namespace App\Http\Design\Infrastructure\Helper;


class UploadHelper
{
    public static function upload($size = 1, $fileName = 'file')
    {
        
        $tmpName = $_FILES[$fileName]['tmp_name'];
        if (!$tmpName) {
            $status = MessageHelper::ERROR;
            $msg    = '上传文件不存在';
            
            return compact('status', 'msg');
        }
        
        $realName = $_FILES[$fileName]['name'];
        $ext      = explode('.', $realName);
        
        $nowFileName = date('Y-m-d') . '-' . rand(11111, 9999999) . '-' . '6' . '.' . $ext[1];
        
        
        $fileSize = filesize($tmpName);
        if ($fileSize / 1024 / 1024 > $size) {
            $status = MessageHelper::ERROR;
            $msg    = "请上传不超过{$size}M大小的";
            
            return compact('status', 'msg');
        }
        $destination = self::initDestination();
        $destination = "{$destination}/$nowFileName";
        if ($destination === false) {
            $status = MessageHelper::ERROR;
            $msg    = "上传失败";
            
            return compact('status', 'msg');
        }
        $upload = move_uploaded_file($tmpName, $destination);
        if ($upload === true) {
            $status = MessageHelper::SUCCESS;
            $msg    = $destination;
            
            return compact('status', 'msg');
        } else {
            $status = MessageHelper::ERROR;
            $msg    = "上传失败";
            
            return compact('status', 'msg');
        }
    }
    
    private static function initDestination()
    {
        $directory = "upload";
        $year      = date("Y");
        $mouth     = date("m");
        $day       = date("d");
        $path      = "{$directory}/{$year}/{$mouth}/{$day}/";
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
        
        return $path;
    }
}
