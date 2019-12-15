<?php
namespace ec;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

use ec\Bootstrap;
use ec\model\Database;
use ec\model\Session;

//テンプレート指定
$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [' cache ' => Bootstrap::CACHE_DIR]);

$db = new Database(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);
$ses = new Session($db);

//初期データを設定
$dataArr = [
    'review_comment' => '' ,
    'review_item_id' => '' ,
    'review_mem_id' => '' ];

    $emg = [
  'msg1' => '*レビュー投稿にはログインする必要があります。',
  'msg2' => '*レビューの内容を入力してください。'
];

if(isset($_POST['send']) === true)
          $error['send'] = 'blank';

$context = [];

$context['dataArr'] = $dataArr;
$context['emgArr'] = $emg;
$context['userId'] = $_SESSION['user_id'];
$context['review_item_id'] = $_SESSION['items'];
$context['review_mem_id'] = $_SESSION['mem_id'];
$context['dataArr']['review_comment'] = $_POST['review_comment'];
$context['dataArr']['review_item_id'] = $_SESSION['items'];
$context['dataArr']['review_mem_id'] = $_SESSION['mem_id'];
$context['errArr'] = $error['send'];
// var_dump($context);
// var_dump($_SESSION);

$dataArr['review_comment'] = $_POST['review_comment'];
$dataArr['review_item_id'] = $_SESSION['itemData'];
$dataArr['review_mem_id'] = $_SESSION['mem_id'];

//foreachの中でSQL文を作る

if(isset($_POST['send']) === true && $_POST['review_comment'] !== '') {
$column = '';
$insData = '';

foreach($dataArr as $key => $value) {
 $column .= $key . ',';

 $insData .= ($key === 'review_item_id' && $key === 'review_mem_id') ? $db->quote($value) . ',':$db->str_quote($value) . ',';
}

$query = "INSERT INTO reviews ("
   . $column
   . " regist_date "
   . " ) VALUES ( "
   . $insData
   . "      NOW() "
   . " ) ";
 
$res = $db->execute($query);
$db->close();

if($res === true) {
   header('Location:' . Bootstrap::ENTRY_URL . 'list.php');
   exit($query);
   unset($_POST['review_comment']);
 }
}

$template = $twig->loadTemplate('review.html.twig');
$template->display($context);
