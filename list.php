<?php
namespace ec;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use ec\Bootstrap;
use ec\model\PDODatabase;
use ec\model\Session;
use ec\model\Item;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new Session($db);
$itm = new Item($db);

// テンプレート指定
$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [' cache ' => Bootstrap::CACHE_DIR]);

// SessionKeyを見て、DBへの登録状態をチェックする
$ses->checkSession();
$ctg_id = (isset($_GET['ctg_id']) === true && preg_match('/^[0-9]+$/', $_GET['ctg_id']) === 1) ? $_GET['ctg_id'] : '' ;

// カテゴリーリスト(一覧)を取得する
$cateArr = $itm->getCategoryList();
// 商品リストを取得する
$dataArr = $itm->getItemList($ctg_id);


$context = [];
$context['cateArr'] = $cateArr;
$context['dataArr'] = $dataArr;
$context['userId'] = $_SESSION['user_id'];

// var_dump($context);
$template = $twig->loadTemplate('list.html.twig');
$template->display($context);