<?php
/* 
 * ファイルパス : /Applications/MAMP/htdocs/DT/ec/bootstrap.class.php
 * ファイル名 : Bootstrap.class.php (設定に関するプログラム)
 */
namespace ec;

date_default_timezone_set('Asia/Tokyo');

require_once dirname(__FILE__) . './../vendor/autoload.php';

class Bootstrap
{
  const DB_HOST = 'localhost';

  const DB_NAME = 'ec_site_db';

  const DB_USER = 'ec_site_user';

  const DB_PASS = 'ec_site_pass';

  const DB_TYPE = 'mysql';

  const APP_DIR = '/Applications/MAMP/htdocs/DT/';

  const TEMPLATE_DIR = self::APP_DIR . 'templates/ec/';

  // const CACHE_DIR = false;
  const CACHE_DIR = self::APP_DIR . 'templates_c/ec/';

  const APP_URL = 'http://localhost:8888/DT/';

  const ENTRY_URL = self::APP_URL . 'ec/';
  
  const REGIST_URL = self::APP_URL . 'ec/controller/member_regist';

  public static function loadClass($class)
  {
    $path = str_replace('\\', '/', self::APP_DIR . $class . '.class.php');
    require_once $path;
  }
}

//これを実行しないとオートローダーとして動かない
spl_autoload_register([
    'ec\Bootstrap', 
    'loadClass'
]);
