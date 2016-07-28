<?php
return [
    'return.false' => ['status' => false, 'msg' => '请求失败'],
    'return.true' => ['status' => true, 'msg' => '请求成功'],
    'adminEmail' => 'admin@example.com',
    'user.AccessTokenExpire' => 3600 * 24 * 360,
    'imgHost' => 'http://localhost:8000',
    'transaction.rang' => [
        'type' => [1, 2],// '类型：1即进单，2限价单',
        'direction' => [1, 2],// '方向：1买涨2买跌',
        'status' => [0, 1, 2],// '状态：0限价单还未生效，1已生效订单，2已结束订单',
        'close_type' => [0, 1, 2, 3],// '关闭类型：0未关闭，1用户人为关闭，2止损止盈触发关闭，3爆仓关闭',
    ],
    'goods_items' => [//交易产品信息
        '白银' => [ 1=>[
            'goods_item' => 1,
            'name' => '白银',
            'size' => 10,
            'unit' => 'g',
            'change' => 0.5],
             2=>[
                'goods_item' => 2,
                'name' => '白银',
                'size' => 100,
                'unit' => 'g',
                'change' => 5],
             3=>[
                'goods_item' => 3,
                'name' => '白银',
                'size' => 1000,
                'unit' => 'g',
                'change' => 50]],
        '原油' => [ 4=>[
            'goods_item' => 4,
            'name' => '原油',
            'size' => 10,
            'unit' => 'g',
            'change' => 0.5],
             5=>[
                'goods_item' => 5,
                'name' => '原油',
                'size' => 100,
                'unit' => 'g',
                'change' => 5],
             6=>[
                'goods_item' => 6,
                'name' => '原油',
                'size' => 1000,
                'unit' => 'g',
                'change' => 50]],

    ],
];
