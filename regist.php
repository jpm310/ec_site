<?php
namespace ec;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use ec\master\initMaster;
use ec\Bootstrap;
use ec\model\PDODatabase;

//テンプレート指定
$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [' cache ' => Bootstrap::CACHE_DIR]);

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);

//初期データを設定
$dataArr = [
    'family_name' => '' ,
    'first_name' => '' ,
    'family_name_kana' => '' ,
    'first_name_kana' => '' ,
    'user_id',
    'password' => '',
    'sex' => '' ,
    'year' => '' ,
    'month' => '' ,
    'day' => '' ,
    'zip1' => '' ,
    'zip2' => '' ,
    'address' => '' ,
    'email' => '' ,
    'tel1' => '' ,
    'tel2' => '' ,
    'tel3' => '' ,
    'picture' => ''
];

//エラーメッセージの定義、初期
$errArr = [];
foreach($dataArr as $key => $value) {
    $errArr[$key] = '';
}

//array($yearArr, $monthArr, $dayArr)
//静的クラス

list($yearArr, $monthArr, $dayArr) = initMaster::getDate();
//list:右辺の配列の要素を、左辺の変数に代入することができる
$sexArr = initMaster::getSex();

$context = [];

$context['yearArr'] = $yearArr;
$context['monthArr'] = $monthArr;
$context['dayArr'] = $dayArr;
$context['sexArr'] = $sexArr;
$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;

$template = $twig->loadTemplate('regist.html.twig');
$template->display($context);
