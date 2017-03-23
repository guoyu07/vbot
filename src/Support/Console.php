<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hanson\Vbot\Support;

use Carbon\Carbon;
use PHPQRCode\QRcode;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class Console
{
    const INFO    = 'INFO';
    const WARNING = 'WARNING';
    const ERROR   = 'ERROR';

    /**
     * 输出字符串.
     *
     * @param $str
     * @param string $level
     */
    public static function log($str, $level = 'INFO')
    {
        echo '[' . Carbon::now()->toDateTimeString() . ']' . "[{$level}] " . $str . PHP_EOL;
    }

    /**
     * debug 模式下调试输出.
     *
     * @param $str
     */
    public static function debug($str)
    {
        if (server()->config['debug']) {
            static::log($str, 'DEBUG');
        }
    }

    /**
     * 控制台显示二维码
     *
     * @param $text
     */
    public static function showQrCode($text)
    {
        $output = new ConsoleOutput();
        static::initQrcodeStyle($output);

        if (System::isWin()) {
            $pxMap = ['<whitec>mm</whitec>', '<blackc>  </blackc>'];
            system('cls');
        } else {
            $pxMap = ['<whitec>  </whitec>', '<blackc>  </blackc>'];
            system('clear');
        }

        $text   = QRcode::text($text);

        $length = strlen($text[0]);

        foreach ($text as $line) {
            $output->write($pxMap[0]);
            for ($i = 0; $i < $length; $i++) {
                $type = substr($line, $i, 1);
                $output->write($pxMap[$type]);
            }
            $output->writeln($pxMap[0]);
        }
    }

    /**
     * 初始化二维码style.
     *
     * @param OutputInterface $output
     */
    private static function initQrcodeStyle(OutputInterface $output)
    {
        $style = new OutputFormatterStyle('black', 'black', ['bold']);
        $output->getFormatter()->setStyle('blackc', $style);
        $style = new OutputFormatterStyle('white', 'white', ['bold']);
        $output->getFormatter()->setStyle('whitec', $style);
    }
}
