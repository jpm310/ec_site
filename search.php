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

$keyword = $_POST['keyword'];
if(!empty($keyword)){
  $where = " WHERE ";
  $where.= "(item_name LIKE '%".$keyword."%') OR (detail LIKE '%".$keyword."%')  ";
}else{
  $emp =  "検索キーワードが入力されていません。";
}

$itemArr = $itm->getItemSearch($where);

$tempHtml = [];
if($itemArr) {
  $cnt = count($itemArr);
  $msg = $cnt."件の商品が該当します。";
  }else{
    $msg = "商品データがありません。";
}

$context = [];
$context['itemArr'] = $itemArr;
$context['empArr'] = $emp;
$context['msgArr'] = $msg;
$context['dataArr'] = $dataArr;
$context['userId'] = $_SESSION['user_id'];
$context['keywordArr'] = $keyword;

$template = $twig->loadTemplate('search.html.twig');
$template->display($context);