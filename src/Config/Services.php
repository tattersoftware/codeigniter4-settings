<?php

/**
 * This file is part of Tatter Settings.
 *
 * (c) 2021 Tatter Software
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Tatter\Settings\Config;

use Config\Services as BaseService;
use Tatter\Settings\Settings;

class Services extends BaseService
{
    public static function settings(bool $getShared = true)
    {
        if ($getShared) {
            return static::getSharedInstance('settings');
        }

        return new Settings();
    }
}
