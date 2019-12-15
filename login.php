<?php
namespace ec;

require_once dirname(__FILE__) . '/Bootstrap.class.php';

$loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
$twig = new \Twig_Environment($loader, [' cache ' => Bootstrap::CACHE_DIR]);

$error['login'] = '';
try {
    $db = new \PDO('mysql:dbname=ec_site_db;host=localhost; charset=utf8', 'root', 'root');
  } catch(PDOException $e){
      echo 'DB接続エラー:' . $e->getMessage();
  }
session_start();
if($_COOKIE['user_id'] !== '') {
    $account = $_COOKIE['user_id'];
}

if (!empty($_POST)) {
    $account = $_POST['user_id'];
    
  if ($_POST['user_id'] != '' && $_POST['password'] !== '') {
      $login = $db->prepare('SELECT * FROM member WHERE user_id=?');
      $login->execute(array($_POST['user_id']));
      $member = $login->fetch(\PDO::FETCH_ASSOC);

    // パスワードチェック
  }
    if($member) {
      $_SESSION['mem_id'] = $member['mem_id'];
      $_SESSION['time'] = time();
      $_SESSION['user_id'] = $_POST['user_id'];
      if ($_POST['save'] === 'on') {
        setcookie('user_id', $_POST['user_id'], time()+60*60*24*14);
      }
      if(password_verify($_POST['password'], $member['password'])){
        
      header('Location: list.php');
        exit();
      
    }else {
         if(isset($_POST['send']) === true)
          $error['login'] = 'failed';
    }
      } else {
        if(isset($_POST['send']) === true)
          $error['login'] = 'blank';
      }
      
      
}
$emg = [
  'msg1' => '*ユーザーIDとパスワードを入力してください。',
  'msg2' => '*ユーザーIDまたはパスワードに誤りがあります。ご登録の情報を正しく入力してください。'
];

$context = [];
$context['sendArr'] = $_POST['send'];
$context['userIdArr'] = $_POST['user_id'];
$context['passwordArr'] = $_POST['password'];
$context['errArr'] = $error['login'];
$context['emgArr'] = $emg;

$template = $twig->loadTemplate('login.html.twig');
$template->display($context);