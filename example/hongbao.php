<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require_once __DIR__ . './../vendor/autoload.php';

use Hanson\Vbot\Foundation\Vbot;
use Hanson\Vbot\Message\Entity\Message;
use Hanson\Vbot\Message\Entity\RedPacket;
use Hanson\Vbot\Support\Console;

$robot = new Vbot([
    'tmp' => __DIR__ . '/./../tmp/',
]);

$robot->server->setMessageHandler(function ($message) {
    /** @var $message Message */
    if ($message instanceof RedPacket) {
        $nickname = account()->getAccount($message->from['UserName'])['NickName'];
        Console::log("收到来自 {$nickname} 的红包");
    }
});

$robot->server->run();
