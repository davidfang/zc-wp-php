<?php
return [
    'adminEmail' => 'admin@example.com',
    'supportEmail' => 'support@example.com',
    'user.passwordResetTokenExpire' => 3600,
    'return.false' => ['status' => false, 'msg' => '请求失败'],
    'return.true' => ['status' => true, 'msg' => '请求成功'],
    'adminEmail' => 'admin@example.com',
    'user.AccessTokenExpire' => 3600 * 24 * 360,
    'imgHost' => '',//'http://localhost:8000',
    'transaction.rang' => [//交易字段取值范围
        'type' => [1, 2],// '类型：1即进单，2限价单',
        'direction' => [1, 2],// '方向：1买涨2买跌',
        'status' => [0, 1, 2],// '状态：0限价单还未生效，1已生效订单，2已结束订单',
        'close_type' => [0, 1, 2, 3],// '关闭类型：0未关闭，1用户人为关闭，2止损止盈触发关闭，3爆仓关闭',
    ],
    'transaction.config' => [//交易配置信息
        'spreads' => 5,//波动一个点的价值  交易点值
        'basicPoint' => 0.01,//交易基点  即价格波动最小单位
        'speed' => 15,//限价单，交易价格限制，不能优于当前价格的范围
        'lever' => 100,//交易杠杆比例
        'saving' => 10,//交易保全变动值  即交易剩余资金需要容纳价格波动量
    ],
    'goods_items' => [//交易产品信息
        1 => [
            'symbol' => 'sliver',
            'goods_item' => 1,
            'name' => '白银',
            'size' => 10,
            'unit' => 'g',
            'change' => 5],
        2 => [
            'symbol' => 'sliver',
            'goods_item' => 2,
            'name' => '白银',
            'size' => 100,
            'unit' => 'g',
            'change' => 50],
        3 => [
            'symbol' => 'sliver',
            'goods_item' => 3,
            'name' => '白银',
            'size' => 1000,
            'unit' => 'g',
            'change' => 500],
        4 => [
            'symbol' => 'crude',
            'goods_item' => 4,
            'name' => '原油',
            'size' => 10,
            'unit' => 'g',
            'change' => 5],
        5 => [
            'symbol' => 'crude',
            'goods_item' => 5,
            'name' => '原油',
            'size' => 100,
            'unit' => 'g',
            'change' => 50],
        6 => [
            'symbol' => 'crude',
            'goods_item' => 6,
            'name' => '原油',
            'size' => 1000,
            'unit' => 'g',
            'change' => 500],

    ],
];

