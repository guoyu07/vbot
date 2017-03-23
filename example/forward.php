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

$robot->server->setMessageHandler(function ($message) {
    if ($message instanceof Text) {
        /** @var $message Text */
        $contact = contact()->getUsernameById('hanson');
        Text::send($contact, $message);
    }
});

$robot->server->run();
