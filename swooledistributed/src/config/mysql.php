<?php
/**
 * Created by PhpStorm.
 * User: zhangjincheng
 * Date: 16-7-15
 * Time: 下午4:49
 */
$config['mysql']['enable'] = true;
$config['mysql']['active'] = 'test';
$config['mysql']['test']['host'] = 'sql.fengniaozhiku.com';
$config['mysql']['test']['port'] = '3306';
$config['mysql']['test']['user'] = 'lixin';
$config['mysql']['test']['password'] = 'root3306';
$config['mysql']['test']['database'] = 'aaa_fengniao_game';
$config['mysql']['test']['charset'] = 'utf8';
$config['mysql']['asyn_max_count'] = 10;

return $config;