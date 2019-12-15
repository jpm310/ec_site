<?php
 namespace ec;

 require_once dirname(__FILE__) . '/Bootstrap.class.php';

 use ec\Bootstrap;
 use ec\master\initMaster;
 use ec\model\PDODatabase;
 use ec\model\Session;

 //テンプレート指定
 $loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
 $twig = new \Twig_Environment($loader, [' cache ' => Bootstrap::CACHE_DIR]);

 $db = new PDODatabase(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME, Bootstrap::DB_TYPE);
 $ses = new Session($db);
 
 $ses->checkSession();

 $dataArr = [
  'mem_id'  => '' ,
  'family_name' => '' ,
  'first_name' => '' ,
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
$mem_data = $db->select_all_mem();
$memberInfo = [];

if ($_SESSION['user_id'] !== '') {
  foreach($mem_data as $val) {
    if($val['mem_id'] === $_SESSION['mem_id']) {
          $memberInfo = $val;
      }
    }
 }

 $emg = [
  'msg1' => '*ログインしてください。',
  'msg2' => '*カートに何も入っていません。'
];


// var_dump($memberInfo);
$context = [];
$context['dataArr'] = $dataArr;
$context['memArr'] = $memberInfo;
$context['emgArr'] = $emg;
$context['userId'] = $_SESSION['user_id'];

$sexArr = initMaster::getSex();
list($yearArr, $monthArr, $dayArr) = initMaster::getDate();

$context['sexArr'] = $sexArr;
$context['imageArr'] = $_FILES['picture'];
$context['yearArr'] = $yearArr;
$context['monthArr'] = $monthArr;
$context['dayArr'] = $dayArr;

$template = $twig->loadTemplate('mypage.html.twig');
$template->display($context);