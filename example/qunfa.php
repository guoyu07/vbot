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
use Hanson\Vbot\Support\Console;

$robot = new Vbot([
    'tmp'   => __DIR__ . '/./../tmp/',
    'debug' => true,
]);

$robot->server->setCustomerHandler(function () {
    contact()->each(function ($item, $username) {
        $word = '新年快乐';
        Console::log("send to username: $username  nickname:{$item['NickName']}");
        Text::send($username, $word);
        sleep(2);
    });
    exit;
});

$robot->server->run();
