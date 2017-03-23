<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hanson\Vbot\Message\Entity;

use Hanson\Vbot\Message\MediaInterface;
use Hanson\Vbot\Message\MessageInterface;
use Hanson\Vbot\Message\UploadAble;
use Hanson\Vbot\Support\FileManager;

class File extends Message implements MessageInterface, MediaInterface
{
    use UploadAble;

    public $title;

    public static $folder = 'file';

    public function __construct($msg)
    {
        parent::__construct($msg);

        $this->make();
    }

    public function make()
    {
        $array = (array) simplexml_load_string($this->msg['Content'], 'SimpleXMLElement', LIBXML_NOCDATA);

        $info = (array) $array['appmsg'];

        $this->title = $info['title'];

        $this->download();
    }

    public function download()
    {
        $url     = server()->fileUri . '/webwxgetmedia';
        $content = http()->get($url, [
            'sender'            => $this->msg['FromUserName'],
            'mediaid'           => $this->msg['MediaId'],
            'filename'          => $this->msg['FileName'],
            'fromuser'          => myself()->username,
            'pass_ticket'       => server()->passTicket,
            'webwx_data_ticket' => static::getTicket(),
        ]);
        FileManager::download($this->msg['FileName'], $content, static::$folder);
    }
}
