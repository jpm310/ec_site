<?php
namespace ec;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use ec\Bootstrap;
use ec\model\PDODatabase;
use ec\model\Session;
use ec\model\Item;
use ec\model\Cart;

$db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
$ses = new Session($db);
$itm = new Item($db);
$cart = new Cart($db);

//テンプレート指定
$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [' cache ' => Bootstrap::CACHE_DIR]);

//セッションに、セッションIDを設定する
$ses->checkSession();

// item_idを取得する
$item_id = (isset($_GET['item_id']) === true && preg_match('/^\d+$/', $_GET['item_id']) === 1) ? $_GET['item_id'] : '';
$_SESSION['itemData'] = $item_id;
// item_idが取得できていない場合、商品一覧へ遷移させる
if($item_id === '') {
   header('Location: ' . Bootstrap::ENTRY_URL. 'list.php');
}

// 口コミデータをそのデータに紐づくユーザー情報を取得する
// $items_id = $_GET['id'];
$reviews_data = [];
$reviews_data = $itm->fetch_reviews($items_id, $sql);
// var_dump($reviews_data);
// カテゴリーリスト(一覧)を取得する
$cateArr = $itm->getCategoryList();

// 商品情報を取得する
$itemData = $itm->getItemDetailData($item_id);


$context = [];
$context['cateArr'] = $cateArr;
$context['itemData'] = $itemData[0];
$context['reviewArr'] = $reviews_data;
$context['userId'] = $_SESSION['user_id'];
$context['dataArr'] = $_SESSION['dataArr'];

$template = $twig->loadTemplate('detail.html.twig');
$template->display($context);
