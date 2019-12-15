<?php
 namespace ec;

 require_once dirname(__FILE__) . '/Bootstrap.class.php';

 use ec\Bootstrap;
 use ec\model\PDODatabase;
 use ec\model\Session;

 //テンプレート指定
 $loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
 $twig = new \Twig_Environment($loader, [' cache ' => Bootstrap::CACHE_DIR]);

 $db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
 $ses = new Session($db);
 
 $ses->checkSession();

$order_data = $db->select_check_order();
$orderInfo = [];

if ($_SESSION['user_id'] !== '') {
  foreach($order_data as $val) {
    if($val['user_id'] === $_SESSION['user_id']) {
          $orderInfo[] = $val;
      }
    }
 }
 // 合計金額が重複しないように重複した配列を削除
$sumPrice =array_unique($orderInfo);
 
$context = [];
 $context['orderArr'] = $orderInfo;
 $context['sumArr'] = $sumPrice;
 $context['userId'] = $_SESSION['user_id'];

$template = $twig->loadTemplate('check.myorder.html.twig');
$template->display($context);