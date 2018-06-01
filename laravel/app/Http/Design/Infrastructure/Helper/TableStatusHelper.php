<?php

namespace App\Http\Design\Infrastructure\Helper;


class TableStatusHelper
{
    const ONE            = 1;
    const ALL            = 2;
    const LISTING_STATUS = [
        //朝向（1东2南3西4北）
        'to_ward'         => [
            [
                'key'  => 1,
                'name' => '东',
            ],
            [
                'key'  => 2,
                'name' => '南',
            ],
            [
                'key'  => 3,
                'name' => '西',
            ],
            [
                'key'  => 4,
                'name' => '北',
            ],
        ],
        //交易状态（1有效，2暂缓，3已租，4已售）
        'trade_status'    => [
            [
                'key'  => 1,
                'name' => '有效',
            ],
            [
                'key'  => 2,
                'name' => '暂缓',
            ],
            [
                'key'  => 3,
                'name' => '已租',
            ],
            [
                'key'  => 4,
                'name' => '已售',
            ],
        ],
        //来源(1 58 ,2 到访，3 朋友)
        'source'          => [
            [
                'key'  => 1,
                'name' => '58',
            ],
            [
                'key'  => 2,
                'name' => '到访',
            ],
            [
                'key'  => 3,
                'name' => '朋友',
            ],
        ],
        //现状（1空置，2自住，3再租）
        'now_status'      => [
            [
                'key'  => 1,
                'name' => '自住',
            ],
            [
                'key'  => 2,
                'name' => '空置',
            ],
            [
                'key'  => 3,
                'name' => '再租',
            ],
        ],
        //装修（1清水，2毛坯，3精装，4简装，5中装，6豪装）
        'decorate'        => [
            [
                'key'  => 1,
                'name' => '清水',
            ],
            [
                'key'  => 2,
                'name' => '毛坯',
            ],
            [
                'key'  => 3,
                'name' => '精装',
            ],
            [
                'key'  => 4,
                'name' => '简装',
            ],
            [
                'key'  => 5,
                'name' => '中装',
            ],
            [
                'key'  => 6,
                'name' => '豪装',
            ],
        ],
        //交易类型（1出租，2出售）
        'trade_type'      => [
            [
                'key'  => 1,
                'name' => '出租',
            ],
            [
                'key'  => 2,
                'name' => '出售',
            ],
        ],
        //公私盘（1公，2私）
        'public_private'  => [
            [
                'key'  => 1,
                'name' => '公盘',
            ],
            [
                'key'  => 2,
                'name' => '私盘',
            ],
        ],
        //产权（1 40年，2 70年）
        'property_rights' => [
            [
                'key'  => 1,
                'name' => '40年',
            ],
            [
                'key'  => 2,
                'name' => '70年',
            ],
        ],
        //付款（1全款，2按揭）
        'payment'         => [
            [
                'key'  => 1,
                'name' => '全款',
            ],
            [
                'key'  => 2,
                'name' => '按揭',
            ],
        ],
        //看房（1预约，2有钥匙）
        'checking'        => [
            [
                'key'  => 1,
                'name' => '预约',
            ],
            [
                'key'  => 2,
                'name' => '有钥匙',
            ],
        ],
        //是否有房产证1是2否
        'is_deed'         => [
            [
                'key'  => 1,
                'name' => '是',
            ],
            [
                'key'  => 2,
                'name' => '否',
            ],
        ],
    
    ];
    
    const CONTRACT_STATUS = [
        //交易类型（1出租，2出售）
        'trade_type' => [
            [
                'key'  => 1,
                'name' => '出租',
            ],
            [
                'key'  => 2,
                'name' => '出售',
            ],
        ],
        //合同状态（1有效，2无效）
        'contract_status'=>[
            [
                'key'  => 1,
                'name' => '有效',
            ],
            [
                'key'  => 2,
                'name' => '无效',
            ],
        ],
        //结佣状态（1已结佣，2未结佣）
        'commission_status'=>[
            [
                'key'  => 1,
                'name' => '已结佣',
            ],
            [
                'key'  => 2,
                'name' => '未结佣',
            ],
        ]
    ];
    
    public static function statusAll()
    {
        $listing = self::LISTING_STATUS;
        
        return compact('listing');
    }
    
    public static function statusOne($status)
    {
        return self::$status();
    }
    
    private static function listing()
    {
        return self::LISTING_STATUS;
    }
    
    private static function contract()
    {
        return self::CONTRACT_STATUS;
    }
}
