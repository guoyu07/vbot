<?php

/*
 * This file is part of PHP CS Fixer.
 * (c) pei.greet <pei.greet@qq.com>
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Hanson\Vbot\Collections;

class Account
{
    /**
     * @var Account
     */
    public static $instance = null;

    /**
     * create a single instance.
     *
     * @return Account
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new self();
        }

        return static::$instance;
    }

    /**
     * 根据username获取账号.
     *
     * @param $username
     *
     * @return mixed
     */
    public function getAccount($username)
    {
        $account = group()->get($username, null);

        $account = $account ?: contact()->get($username, null);

        $account = $account ?: official()->get($username, null);

        return $account ?: member()->get($username, []);
    }
}
