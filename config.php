<?php

// get an api key from https://developer.riotgames.com
define('API_KEY', '');
// default region for api requests
// regions fully supported: na, euw, eune
define('REGION', 'na');

error_reporting(0);

set_time_limit(0);

/**
 * Basic class with system's configuration data.
 */
class config
{
    /**
     * Configuration data.
     *
     * @static
     *
     * @var array
     */
    private static $_data = [
        'db_user'         => 'lol-replay-parse',
        'db_pass'         => '',
        'db_host'         => 'localhost',
        'db_name'         => 'lol_replay_parser',
        'db_table_prefix' => '',
    ];

    /**
     * Private construct to avoid object initializing.
     */
    private function __construct()
    {
    }

    public static function init()
    {
        self::$_data['base_path'] = dirname(__FILE__).DIRECTORY_SEPARATOR.'includes';
        $db = db::obtain(self::get('db_host'), self::get('db_user'), self::get('db_pass'), self::get('db_name'), self::get('db_table_prefix'));
        if (!$db->connect_pdo()) {
            die('Could Not Connect To Database!');
        }
    }

    /**
     * Get configuration parameter by key.
     *
     * @param string $key data-array key
     *
     * @return null
     */
    public static function get($key)
    {
        if (isset(self::$_data[$key])) {
            return self::$_data[$key];
        }
    }
}

config::init();

function __autoload($class)
{
    scan(config::get('base_path'), $class);
}

function scan($path, $class)
{
    $ignore = ['.', '..'];
    $dh = opendir($path);
    while (false !== ($file = readdir($dh))) {
        if (!in_array($file, $ignore)) {
            if (is_dir($path.DIRECTORY_SEPARATOR.$file)) {
                scan($path.DIRECTORY_SEPARATOR.$file, $class);
            } else {
                if ($file === 'class.'.$class.'.php') {
                    require_once $path.DIRECTORY_SEPARATOR.$file;

                    return;
                }
            }
        }
    }
    closedir($dh);
}
