<?php

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
