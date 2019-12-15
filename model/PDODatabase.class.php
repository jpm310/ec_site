<?php
namespace ec\model;

class PDODatabase
{
  public $dbh = null;
  private $db_host = '';
  private $db_user = '';
  private $db_pass = '';
  private $db_name = '';
  private $db_type = '';
  private $order = '';
  private $limit = '';
  private $offset = '';
  private $groupby = '';

  public function __construct($db_host, $db_user, $db_pass, $db_name, $db_type)
  {
    $this->dbh = $this->connectDB($db_host, $db_user, $db_pass, $db_name, $db_type);
    $this->db_host = $db_host;
    $this->db_user = $db_user;
    $this->db_pass = $db_pass;
    $this->db_name = $db_name;
    // SQL関連
    $this->order = '';
    $this->limit = '';
    $this->offset = '';
    $this->groupby = '';
  }

  private function connectDB($db_host, $db_user, $db_pass, $db_name, $db_type)
  {
    try {// 接続エラー発生→PDOExceptionオブジェクトがスローされる→例外処理をキャッチする

        switch ($db_type) {
        case 'mysql':
            $dsn = 'mysql:host=' . $db_host . ';dbname=' . $db_name;
            $dbh = new \PDO($dsn, $db_user, $db_pass);
            $dbh->query('SET NAMES utf8');
            break;

        case 'pgsql':
            $dsn = 'pgsql:dbname=' . $db_name . ' host=' . $db_host . ' port=5432';
            $dbh = new \PDO($dsn, $db_user, $db_pass);
            break;
        }
    } catch (\PDOException $e) {
            var_dump($e->getMessage());
            exit();
        }

        return $dbh;
    }

    public function setQuery($query = '', $arrVal = [])
    {
        $stmt = $this->dbh->prepare($query);
        $stmt->execute($arrVal);
    }
    public function selectQuery($query = '')
    {
        $stmt = $this->dbh->prepare($query);
        $res = $stmt->execute();
        if($res === false) {
            $this->catchError($stmt->errorInfo());
        }

        //データを連想配列に格納
        $data = [];
        while($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            array_push($data, $result);
        }
        return $data;
    }

    public function select($table, $column = '', $where = '', $arrVal = [])
    {
        $sql = $this->getSql('select', $table, $where, $column);

        $this->sqlLogInfo($sql, $arrVal);

        $stmt = $this->dbh->prepare($sql);
        $res = $stmt->execute($arrVal);
        if($res === false) {
            $this->catchError($stmt->errorInfo());
        }

        //データを連想配列に格納
        $data = [];
        while($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            array_push($data, $result);
        }
        return $data;
    }
    // レビューの会員情報と紐付けした情報取得
    public function selects()
    {

        $sql = "SELECT
        reviews.review_comment,
        reviews.review_value,
        reviews.review_item_id,
        reviews.review_mem_id,

        member.mem_id,
        member.user_id
      FROM
        reviews
      LEFT JOIN
        member
      ON
        reviews.review_mem_id = member.mem_id
      WHERE
        reviews.review_item_id ";
    

        $stmt = $this->dbh->prepare($sql);
        $res = $stmt->execute();
        if($res === false) {
            $this->catchError($stmt->errorInfo());
        }

        //データを連想配列に格納
        $data = [];
        while($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            array_push($data, $result);
        }
        return $data;
    }
    // 会員情報変更画面用の会員情報取得
    public function select_all_mem()
    {
        $sql = "SELECT
        member.mem_id,
        member.family_name,
        member.first_name, 
        member.user_id,
        member.password,
        member.sex,
        member.year,
        member.month,
        member.day,
        member.zip1,
        member.zip2,
        member.address,
        member.email,
        member.tel1,
        member.tel2,
        member.tel3,
        member.picture 
      FROM
        member ";

        $stmt = $this->dbh->prepare($sql);
        $res = $stmt->execute();
        if($res === false) {
            $this->catchError($stmt->errorInfo());
        }

        //データを連想配列に格納
        $data = [];
        while($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            array_push($data, $result);
        }
        return $data;
    }

    // 注文内容確認時の情報
    public function select_order()
    {
        $sql = "SELECT
        orders.order_id,
        orders.order_mem_id,
        
        member.mem_id,
        member.user_id,
        member.family_name,
        member.first_name,
        member.zip1,
        member.zip2,
        member.address,
        member.email,
        member.tel1,
        member.tel2,
        member.tel3
      FROM
        orders
      LEFT JOIN
        member
      ON
        orders.order_mem_id = member.mem_id";
      
    

        $stmt = $this->dbh->prepare($sql);
        $res = $stmt->execute();
        if($res === false) {
            $this->catchError($stmt->errorInfo());
        }
    }

    // マイページの注文履歴の情報
    public function select_check_order()
    {
        $sql = "SELECT
        orders.order_id,
        orders.user_id,
        orders.sumPrice,
        orders.regist_date,

        order_details.item_name,
        order_details.price,
        order_details.num,
        order_details.detail_order_id

      FROM
        orders
      LEFT JOIN
        order_details
      ON
        orders.order_id = order_details.detail_order_id";
      
        $stmt = $this->dbh->prepare($sql);
        $res = $stmt->execute();
        if($res === false) {
            $this->catchError($stmt->errorInfo());
        }

        //データを連想配列に格納
        $data = [];
        while($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            array_push($data, $result);
        }
        return $data;
    }
    
    // 商品購入確認画面の会員情報取得(order.confirm.php)
    public function select_mem()
    {
        $sql = "SELECT
        member.mem_id,
        member.user_id,
        member.family_name,
        member.first_name,
        member.zip1,
        member.zip2,
        member.address,
        member.email,
        member.tel1,
        member.tel2,
        member.tel3
      FROM
        member ";

        $stmt = $this->dbh->prepare($sql);
        $res = $stmt->execute();
        if($res === false) {
            $this->catchError($stmt->errorInfo());
        }

        //データを連想配列に格納
        $data = [];
        while($result = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            array_push($data, $result);
        }
        return $data;
    }

    public function count($table, $where = '', $arrVal = [])
    {
        $sql = $this->getSql('count', $table, $where);

        $this->sqlLogInfo($sql, $arrVal);
        $stmt = $this->dbh->prepare($sql);

        $res = $stmt->execute($arrVal);

        if ($res === false) {
            $this->catchError($stmt->errorInfo());
        }

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        return intval($result['NUM']);
    }

    private function getSql($type, $table, $where = '', $column = '')
    {
        switch ($type) {
            case 'select':
                $columnKey = ($column !== '') ? $column : "*";
                break;
            case 'count':
                $columnKey = ' COUNT(*) AS NUM ';
                break;
            default:
                break;
        }

        $whereSQL = ($where !== '') ? ' WHERE  ' . $where : '';
        $other = $this->groupby . "  " . $this->order . "  " . $this->limit . "  " . $this->offset;

        // sql文の作成
        $sql = " SELECT " . $columnKey . " FROM " . $table . $whereSQL . $other;

        return $sql;
    }

    public function insert($table, $insData = [])
    {

        $insDataKey = [];
        $insDataVal = [];
        $preCnt = [];

        $columns = '';
        $preSt = '';

        foreach ($insData as $col => $val) {
            $insDataKey[] = $col;
            $insDataVal[] = $val;
            $preCnt[] = '?';
        }

        $columns = implode(",", $insDataKey);
        $preSt = implode(",", $preCnt);

        $sql = " INSERT INTO "
            . $table
            . " ("
            . $columns
            . ") VALUES ("
            . $preSt
            . ") ";

        $this->sqlLogInfo($sql, $insDataVal);

        $stmt = $this->dbh->prepare($sql);
        $res = $stmt->execute($insDataVal);

        if ($res === false) {
            $this->catchError($stmt->errorInfo());
        }

        return $res;
    }

    public function update($table, $insData = [], $where, $arrWhereVal = [])
    {
        $arrPreSt = [];
        foreach ($insData as $col => $val) {
            $arrPreSt[] = $col . " =? ";
        }
        $preSt = implode(',', $arrPreSt);
    
        // sql文の作成
        $sql = " UPDATE "
            . $table
            . " SET "
            . $preSt
            . " WHERE "
            . $where;
    
        $updateData = array_merge(array_values($insData), $arrWhereVal);
        $this->sqlLogInfo($sql, $updateData);
    
        $stmt = $this->dbh->prepare($sql);
        $res = $stmt->execute($updateData);

        if ($res === false) {
            $this->catchError($stmt->errorInfo());
        }
        return $res;
    }

    public function getLastId()
    {
        return $this->dbh->lastInsertId();
    }

    private function catchError($errArr = [])
    {
        $errMsg = (!empty($errArr[2]))? $errArr[2]:"";
        die("SQLエラーが発生しました。" . $errArr[2]);
    }

    private function makeLogFile()
    {
        $logDir = dirname(__DIR__) . "/logs";
        if (!file_exists($logDir)) {
            mkdir($logDir, 777);
        }
        $logPath = $logDir . '/shopping.log';
        if (!file_exists($logPath)) {
            touch($logPath);
        }
        return $logPath;
    }

    private function sqlLogInfo($str, $arrVal = [])
    {
        $logPath = $this->makeLogFile();
        $logData = sprintf("[SQL_LOG:%s]: %s [%s]\n", date('Y-m-d H:i:s'), $str, implode(",", $arrVal));
        error_log($logData, 3, $logPath);
    }

}