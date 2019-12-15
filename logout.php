<?php
namespace ec;

require_once dirname(__FILE__) . '/Bootstrap.class.php';


// セッションを開始
session_start();
// セッションを破棄
$_SESSION = array();
session_destroy();

setcookie('user_id', '', time()-3600);
setcookie('password', '', time()-3600);

header('Location: list.php');
exit();

?>