<?php

namespace App\Http\Design\Infrastructure\Library;


use Predis\Client;

class Redis
{
    private static $RedisCluster = null;
    const KEY_PREFIX = "";
    
    /**
     * 实例化redis集群
     * @return null|\RedisCluster|string
     */
    private static function getRedisCluster()
    {
        $servers = [
            'tcp://119.23.237.167:7000',
            'tcp://119.23.237.167:7001',
            'tcp://119.23.237.167:7002',
        ];
        if (self::$RedisCluster === null) {
            self::$RedisCluster = new Client($servers, ['cluster' => 'redis']);
        }
        
        
        return self::$RedisCluster;
    }
    
    /**
     * @param $key
     * @param bool $type true加锁其余解锁
     * @return int
     */
    public static function lock($key, $type = true)
    {
        $redis = self::getRedisCluster();
        $key   = self::combinationKey($key);
        if ($type == true) {
            $status = $redis->setnx($key, time());
            //防止死锁
            if ($status) {
                $redis->setex($key, 30, time());
            } else {
                $time = $redis->getset($key, time());
                if ($time + 30 < time()) {
                    $redis->del($key);
                    $status = self::lock($key);
                }
            }
        } else {
            $status = $redis->del($key);
        }
        
        return $status;
    }
    
    /**
     * 组合完整的key
     * @param $key
     * @return string
     */
    private static function combinationKey($key)
    {
        $prefix = self::KEY_PREFIX;
        if (!empty($prefix)) {
            $combinationKey = $prefix . ":" . $key;
        } else {
            $combinationKey = $key;
        }
        
        
        return $combinationKey;
    }
    
    
    /**
     * k=>v 设置key
     * @param $key
     * @param $value
     * @param int $time 0 是不设置过期时间
     * @return mixed
     */
    public static function set($key, $value, $time = 0)
    {
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        if ($time !== 0) {
            $res = $redis->setex($key, $time, $value);
        } else {
            $res = $redis->set($key, $value);
        }
        var_dump($res);exit;
        
        return $res;
    }
    
    
    /**
     * k=>v 获取key的值
     * @param $key
     * @return mixed
     */
    public static function get($key)
    {
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        $value = $redis->get($key);
        
        return $value;
    }
    
    /**
     * 删除redis缓存
     *
     * k=>v 获取key的值
     * @param $key
     * @return mixed
     */
    public static function del($key)
    {
        if (empty($key)) return;
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        $value = $redis->del($key);
        
        return $value;
    }
    
    /**
     * zset 写
     * @param $key
     * @param $data
     * @param $score
     * @param $time
     * @return int
     */
    public static function zAdd($key, $data, $score, $time = 0)
    {
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        $exist = 0;
        if ($time != 0) {
            $exist = $redis->exists($key);
        }
        $res = $redis->zadd($key, $score, $data);
        
        if ($time != 0 && $exist == 0) {
            $redis->expire($key, $time);
        }
        
        return $res;
    }
    
    /**
     * 序集成员按score值递增(从小到大)次序排列。
     * @param $key
     * @param $start
     * @param $stop
     * @return mixed
     */
    public static function zRange($key, $start, $stop)
    {
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        
        return $redis->zrange($key, $start, $stop);
    }
    
    
    /**
     * 序集成员按score值递增(从大到小)次序排列。
     * @param $key
     * @param $start
     * @param $stop
     * @return mixed
     */
    public static function zRevRange($key, $start, $stop)
    {
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        
        return $redis->zrevrange($key, $start, $stop);
    }
    
    /**
     * 返回有序集key中，成员member的score值。
     * @param $key
     * @param $member
     * @return mixed
     */
    public static function zScore($key, $member)
    {
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        
        return $redis->zscore($key, $member);
    }
    
    public static function zRem($key, $member)
    {
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        
        return $redis->zrem($key, $member);
    }
    
    
    /**
     * 返回有序集key中，所有score值介于min和max之间(包括等于min或max)的成员。有序集成员按score值递增(从小到大)次序排列。
     * @param $key
     * @param $min
     * @param $max
     * @return mixed
     */
    public static function zRangeByScore($key, $min, $max)
    {
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        
        return $redis->zrangebyscore($key, $min, $max);
    }
    
    /**
     * 返回有序集key中，所有score值介于min和max之间(包括等于min或max)的成员。有序集成员按score值递增(从大到小)次序排列。
     * @param $key
     * @param $min
     * @param $max
     * @return mixed
     */
    public static function zRevRangeByScore($key, $min, $max)
    {
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        
        return $redis->zrevrangebyscore($key, $max, $min);
    }
    
    /**
     * 返回集合key中的所有成员。
     * @param $key
     * @return mixed
     */
    public static function sMembers($key)
    {
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        
        return $redis->smembers($key);
    }
    
    /**
     * 查找符合给定模式的key。
     * @param $key
     * @return mixed
     */
    public static function keys($key)
    {
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        
        return $redis->keys($key);
    }
    
    /**
     * 检查给定key是否存在
     * @param $key
     * @return mixed
     */
    public static function exists($key)
    {
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        
        return $redis->exists($key);
    }
    
    /**
     * 返回key所储存的值的类型
     * @param $key
     * @return mixed
     */
    public static function type($key)
    {
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        
        return $redis->type($key);
    }
    
    /**
     * 返回哈希表key中，所有的域和值。
     * @param $key
     * @return mixed
     */
    public static function hGetAll($key)
    {
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        
        return $redis->hgetall($key);
    }
    
    /**
     * 返回哈希表key中的所有域。
     * @param $key
     * @return mixed
     */
    public static function hKeys($key)
    {
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        
        return $redis->hkeys($key);
    }
    
    
    /**
     * 返回哈希表key中的所有值。
     * @param $key
     * @return mixed
     */
    public static function hValS($key)
    {
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        
        return $redis->hvals($key);
    }
    
    /**移除集合key中的一个或多个member元素，不存在的member元素会被忽略。
     * @param $key
     * @param $member
     * @return int
     */
    public static function sRem($key, $member)
    {
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        
        return $redis->srem($key, $member);
    }
    
    
    /**
     *
     * 多keys 多值批量操作 同一个key 只能一条命令
     *
     * @param  $data
     *            [
     *            'testmhset1'=>['call'=>'hset','args'=>[1,1]],
     *            'testmhset2'=>['call'=>'set','args'=>[1]],
     *            'testmhset3'=>['call'=>'zadd','args'=>[1,1]],
     *            'testmhset4'=>['call'=>'hget','args'=>[1]],
     *            'testmhset5'=>['call'=>'hgetall','args'=>[]]
     *            ]
     * @return array
     */
    public static function gpipe($data)
    {
        $redis = self::getRedisCluster();
        
        return $redis->gpipe($data);
    }
    
    /**
     * 将一个member元素加入到集合key当中，已经存在于集合的member元素将被忽略
     * @param $key
     * @param $value
     * @param int $time 过期时间 0永久
     * @return int 成功插入返回1 如果已经存在返回0
     */
    public static function sAdd($key, $value, $time = 0)
    {
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        
        $res = $redis->sadd($key, $value);
        if ($time != 0) {
            $redis->expire($key, $time);
        }
        
        return $res;
    }
    
    /**
     * 将值value插入到列表key的表尾。
     * @param $key
     * @param $value
     * @param int $time
     * @return int
     */
    public static function rPush($key, $value, $time = 0)
    {
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        
        $res = $redis->rpush($key, $value);
        if ($time != 0) {
            $redis->expire($key, $time);
        }
        
        return $res;
    }
    
    /**
     * 移除并返回列表key的头元素。
     * @param $key
     * @return int
     */
    public static function lPop($key)
    {
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        
        $res = $redis->lpop($key);
        
        return $res;
    }
    
    
    /**
     * 批量zadd 同一个键
     * @param $key
     * @param $data array ['val'=>'score']
     * @return array|void
     */
    public static function pipeZAdd($key, $data)
    {
        $key   = self::combinationKey($key);
        $redis = self::getRedisCluster();
        $redis->multi(\Redis::PIPELINE);
        foreach ($data as $val => $score) {
            $redis->zadd($key, $score, $val);
        }
        
        return $redis->exec();
    }
    
}
