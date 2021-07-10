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

use CodeIgniter\Config\BaseConfig;

class Settings extends BaseConfig
{
    /**
     * Matches config value requests to its
     * corresponding Setting template.
     *
     * @param string $name
     *
     * @return mixed Null for non-existant templates
     */
    public function __get(string $name)
    {
        // Verify the template
        if ($setting = service('settings')->getTemplate($name)) {
            return $setting->content;
        }

        return null;
    }
}
