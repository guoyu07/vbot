<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hanson\Vbot\Message\Entity;

use Hanson\Vbot\Message\MediaInterface;
use Hanson\Vbot\Message\MediaTrait;
use Hanson\Vbot\Message\MessageInterface;
use Hanson\Vbot\Message\UploadAble;
use Hanson\Vbot\Support\FileManager;

class Voice extends Message implements MessageInterface, MediaInterface
{
    use UploadAble, MediaTrait;

    public static $folder = 'mp3';

    public function __construct($msg)
    {
        parent::__construct($msg);

        $this->make();
    }

    /**
     * 下载文件.
     *
     * @return mixed
     */
    public function download()
    {
        $url     = server()->baseUri . sprintf('/webwxgetvoice?msgid=%s&skey=%s', $this->msg['MsgId'], server()->skey);
        $content = http()->get($url);
        FileManager::download($this->msg['MsgId'] . '.mp3', $content, static::$folder);
    }

    public function make()
    {
        $this->download();
    }
}
