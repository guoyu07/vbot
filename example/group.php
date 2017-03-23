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
    'tmp'   => __DIR__ . '/./../tmp/',
    'debug' => true,
]);

$robot->server->setCustomerHandler(function () {
    $group = group()->getGroupsByNickname('stackoverflow', true)->first();
    Text::send($group['UserName'], '测试' . \Carbon\Carbon::now()->toDateTimeString());
});

$robot->server->run();
