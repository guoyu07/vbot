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
    $whiteList = ['some remark name...', 'some remark name...'];
    $blackList = ['some remark name...', 'some remark name...'];
    contact()->each(function ($item, $username) use ($whiteList, $blackList) {
        // 发送白名单
        if ($item['RemarkName'] && in_array($item['RemarkName'], $whiteList, true)) {
            Text::send($username, $item['RemarkName'] . ' 新年快乐');
        }
        // 黑名单不发送
//        if($item['RemarkName'] && !in_array($item['RemarkName'], $blackList)){
//            Text::send($username, $item['RemarkName'] . ' 新年快乐');
//        }
        // 全部人发送
//        if($item['RemarkName']){
//            Text::send($username, $item['RemarkName'] . ' 新年快乐');
//        }
    });
    exit;
});

$robot->server->run();
