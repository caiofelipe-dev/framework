<?php

namespace Fmk;

class Initialize
{
    public static string $configs_path = __DIR__ . DIRECTORY_SEPARATOR . "configs" . DIRECTORY_SEPARATOR;
    public static function run()
    {
        self::loadConfigs();
        self::loadHelpers();
        // Load application helpers and custom rules (app folder)
        self::loadAppHelpers();
        self::loadAppRules();
    }

    private static function loadConfigs()
    {
        
        $constantes = require self::$configs_path . 'constants.php';
        self::createConstants($constantes);
        defined('DATABASE_DRIVERS') || define('DATABASE_DRIVERS',require self::$configs_path.'database_drivers.php');
    }

    public static function createConstants($constants)
    {
        foreach ($constants as $key => $constante) {
            $exp = '(\$[a-zA-Z0-9_]{1,})';
            if (preg_match_all($exp, $constante, $match)) {
                foreach ($match[0] as $combinacao) {
                    $preconst = constant(str_replace('$', '', $combinacao));
                    $constante = str_replace($combinacao, $preconst, $constante);
                }
            }
            defined($key) || define($key, $constante);
        }
    }

    public static function loadHelpers()
    {
        $helpers_path = __DIR__ . DIRECTORY_SEPARATOR . "helpers" . DIRECTORY_SEPARATOR;
        $files = glob($helpers_path . '*.php');
        foreach ($files as $file) {
            require_once $file;
        }
    }

    /**
     * Load helpers from the application `app/helpers` folder if present
     */
    public static function loadAppHelpers()
    {
        $app_helpers = (defined('APP_PATH') ? constant('APP_PATH') : __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app') . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR;
        if (is_dir($app_helpers)) {
            foreach (glob($app_helpers . '*.php') as $file) {
                require_once $file;
            }
        }
    }

    /**
     * Ensure application rules config is available for Validate facade
     */
    public static function loadAppRules()
    {
        // No direct require here; Validate will merge framework rules with app/configs/rules.php via Config::get
        // But if there are rule class files under app/Rules we can require them to be available (optional)
        $app_rules_path = (defined('APP_PATH') ? constant('APP_PATH') : __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'app') . DIRECTORY_SEPARATOR . 'Rules' . DIRECTORY_SEPARATOR;
        if (is_dir($app_rules_path)) {
            foreach (glob($app_rules_path . '*.php') as $file) {
                require_once $file;
            }
        }
    }

   
}