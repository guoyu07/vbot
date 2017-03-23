<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require_once __DIR__ . './../vendor/autoload.php';

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Entity\Text;

$robot = new Vbot([
    'tmp' => __DIR__ . '/./../tmp/',
]);

$flag = false;

$robot->server->setCustomerHandler(function () use (&$flag) {
    // RemarkName,代表的改用户在你通讯录的名字
    $contact = contact()->getUsernameByRemarkName('hanson');
    if ($contact === false) {
        echo '找不到你要的联系人，请确认联系人姓名';

        return;
    }
    if (!$flag) {
        Text::send($contact, '来轰炸吧');
        $flag = true;
    }

    Text::send($contact, '测试' . \Carbon\Carbon::now()->toDateTimeString());
});

$robot->server->run();
