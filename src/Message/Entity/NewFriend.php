<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hanson\Vbot\Message\Entity;

use Hanson\Vbot\Collections\ContactFactory;
use Hanson\Vbot\Message\MessageInterface;
use Hanson\Vbot\Support\Console;

class NewFriend extends Message implements MessageInterface
{
    public function __construct($msg)
    {
        $this->make();
        parent::__construct($msg);
    }

    public function make()
    {
        Console::debug('检测到新加好友，正在刷新好友列表...');
        (new ContactFactory())->makeContactList();
        Console::debug('好友更新成功！');
    }
}
