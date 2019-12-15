<?php

 namespace ec;

 require_once dirname(__FILE__) . '/Bootstrap.class.php';

 use ec\master\initMaster;
 use ec\model\Database;
 use ec\model\Common;

 //テンプレート指定
 $loader = new \Twig_Loader_Filesystem(Bootstrap::TEMPLATE_DIR);
 $twig = new \Twig_Environment($loader, [' cache ' => Bootstrap::CACHE_DIR]);

 $db = new Database(Bootstrap::DB_HOST, Bootstrap::DB_USER, Bootstrap::DB_PASS, Bootstrap::DB_NAME);
 $common = new Common();

 
 //モード判定(どの画面から来たかの判断)
 //登録画面から来た場合
 if(isset($_POST['confirm']) === true) {
   $mode = 'confirm';
  }
  // 戻る場合
  if(isset($_POST['back']) === true) {
    $mode = 'back';
  }
  //登録完了
  if(isset($_POST['complete']) === true) {
    $mode = 'complete';
  }
  
  // ファイルの保存
  if(!empty($_FILES['picture']['name'])) {
    $picture = date('YmdHis') . '.jpg';
    move_uploaded_file( $_FILES['picture']['tmp_name'], './images_upload/' . $picture);
  } 
  
  //ボタンのモードによって処理をかえる
switch($mode) {
  case 'confirm': //新規登録
                  //データを受け継ぐ
                  //↓この情報は入力には必要ない
        unset($_POST['confirm']);
        $dataArr = $_POST;
        $dataArr['picture'] = $picture;

        //この値を入れないでPOSTするとUndefinedとなるので未定義の場合は空白状態としてセットしておく
        if(isset($_POST['sex']) === false) {
          $dataArr['sex'] = "";
        } 
        //エラーメッセージの配列作成
        $errArr = $common->errorCheck($dataArr);
        $err_check = $common->getErrorFlg();
        //err_check = false →エラーがありますよ！
        //err_check = true →エラーがないですよ！
        //エラーが無ければconfirm.tpl　あるとregist.tpl
        $template = ($err_check === true) ? 'confirm.html.twig' : 'regist.html.twig';
        break;

        case 'back': //戻ってきた時
                     //ポストされたデータを元に戻すので、$dataArrにいれる
            $dataArr = $_POST;
            unset($dataArr['back']);

            //エラーも定義しておかないと、Undefinedエラーがでる
            foreach($dataArr as $key => $value) {
                $errArr[$key] = '';
            }

            $template = 'regist.html.twig';
            break;

        case 'complete': //登録完了
            $dataArr = $_POST;
             // ↓この情報はいらないので外しておく
             unset($dataArr['complete']);
             $column = '';
             $insData = '';

             //foreachの中でSQL文を作る
             $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
             foreach($dataArr as $key => $value) {
              $column .= $key . ',';
              $insData .= ($key === 'password') ? $db->str_quote($password). ',':$db->str_quote($value) . ',';
         

         }
         $query = "INSERT INTO member ("
                . $column
                . " regist_date "
                . " ) VALUES ( "
                . $insData
                . "      NOW() "
                . " ) ";
                echo $query;
              
              var_dump($_POST);
         $res = $db->execute($query);
         $db->close();
             
             if($res === true) {
                //登録成功時は完成ページへ
                
                header('Location:' . Bootstrap::ENTRY_URL . 'complete.php');
                exit($query);
             } else {
                //登録失敗時は登録画面に戻る
                $template = 'regist.html.twig';

                foreach($dataArr as $key => $value) {
                    $errArr[$key] = '';
                }
             }

             break;
}

$sexArr = initMaster::getSex();

$context['sexArr'] = $sexArr;
$context['imageArr'] = $picture;

list($yearArr, $monthArr, $dayArr) = initMaster::getDate();

$context['yearArr'] = $yearArr;
$context['monthArr'] = $monthArr;
$context['dayArr'] = $dayArr;

$context['dataArr'] = $dataArr;
$context['errArr'] = $errArr;
$template = $twig->loadTemplate($template);
$template->display($context);