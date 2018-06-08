<?php

return [
    //单redis配置
    'redis_address'  => 'tcp://120.79.8.245:6379',
    //rabbit ram 配置
    'rabbit_address' => [
        'host'      => '192.168.0.169',
        'port'      => 5673,
        'user_name' => 'myuser',
        'password'      => 'mypass',
    ],
    //redisCluster配置
    'redis_cluster_address'  => [
        'tcp://119.23.237.167:7000',
        'tcp://119.23.237.167:7001',
        'tcp://119.23.237.167:7002',
    ],
];
