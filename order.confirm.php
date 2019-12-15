<?php
 namespace ec;

 require_once dirname(__FILE__) . '/Bootstrap.class.php';

 use ec\Bootstrap;
 use ec\model\PDODatabase;
 use ec\model\Session;
 use ec\model\Item;
 use ec\model\Cart;
 use ec\model\Order;

 $db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
 $ses = new Session($db);
 $itm = new Item($db);
 $cart = new Cart($db);
 $order = new Order($db);

 //テンプレート指定
 $loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
 $twig = new \Twig_Environment($loader, [' cache ' => Bootstrap::CACHE_DIR]);
 $ses->checkSession();
 $totalData = $cart->getEachSumPrice();

 $mem_data = [
  'mem_id' => '',
  'user_id' => '',
  'family_name' => '',
  'first_name' => '',
  'zip1' => '',
  'zip2' => '',
  'address' => '',
  'email' => '',
  'tel1' => '',
  'tel2' => '',
  'tel3' => ''
];
$mem_data = $db->select_mem();
 $memberInfo = [];

if ($_SESSION['sumPrice'] && $_SESSION['user_id'] !== '') {
 
 foreach($mem_data as $val) {
   if($val['mem_id'] === $_SESSION['mem_id']) {
         $memberInfo = $val;
     }
   }
}

// orderテーブルに挿入する配列を作成

  $userid = $_SESSION['user_id'];
  $sumPrice = $_SESSION['sumPrice'];
  $customer_no = $_SESSION['customer_no'];
  $time =  date("Y/m/d H:i:s");

// viewの変数を作成
$dataArr['sumPrice'] = $_SESSION['sumPrice'];
$dataArr['price1'] = $_SESSION['price'];
$dataArr['order_mem_id'] = $_SESSION['mem_id'];
// var_dump($orderData);

if($_POST["cmd"] === "commit_order") {
  $res = $order->insOrderData($userid,$sumPrice, $time);
  // 登録に失敗した場合、エラーページを表示する
  if($res === false) {
     echo "商品購入に失敗しました。";
     exit();
  }
  
  $order_id = $db->dbh->lastInsertId("order_id");
  
  foreach($totalData as $val) {
    $sql = " INSERT INTO order_details( item_name, price, num, detail_order_id ) ";
		$sql.= " values(?, ?, ?, ? ) " ;
		$stmt = $db->dbh->prepare( $sql );
		$res = $stmt->execute(
      array(
        $val["item_name"],
				$val["price"],
				$val["num"],
				$order_id
			)
		);
  }
  // unset( $_SESSION["cart"] );
	// $is_order_done 変数は、画面上に「注文が完了しました」
	// メッセージを表示するために使用する。
	$is_order_done = 1;
}
  if($is_order_done === 1) {
     header('Location:' . Bootstrap::ENTRY_URL . 'order.complete.php');
     $res = $order->delAllCartData($customer_no);
     unset($_SESSION['dataArr']);
     exit($query);
   }

  $emg = [
    'msg1' => '*ログインしてください。',
    'msg2' => '*カートに何も入っていません。'
  ];



$context = [];
$context['memArr'] = $memberInfo;
$context['emgArr'] = $emg;
$context['totalData'] = $totalData;
$context['userId'] = $_SESSION['user_id'];
$context['sumPrice'] = $_SESSION['sumPrice'];
$context['dataArr'] = $_SESSION['dataArr'];

$template = $twig->loadTemplate('order.confirm.html.twig');
$template->display($context);