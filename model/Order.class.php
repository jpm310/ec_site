<?php
namespace ec\model;

class Order
{
    private $db = null;

    public function __construct($db = null)
    {
        $this->db = $db;
    }

    // カートに登録する(必要な情報は、誰が$customer_no、何を($item_id))
    public function insOrderData($userid,$sumPrice, $time)
    {
        $table = ' orders ';
        $insData = [
          'user_id' => $userid,
          'sumPrice' => $sumPrice,
          'regist_date' => $time
        ];
        return $this->db->insert($table, $insData);
    }

    public function insOrderDetailData($customer_no, $item_id)
    {
        $table = ' order_details ';
        $insData = [
            'customer_no' => $customer_no,
            'item_id' => $item_id,

        ];
        return $this->db->insert($table, $insData);
    }

    public function delAllCartData($customer_no)
    {
      $table = ' cart ';
      $insData = ['delete_flg' => 1];
      $where = ' customer_no = ? ';
      $arrWhereVal = [$customer_no];

      return $this->db->update($table, $insData, $where, $arrWhereVal);
    }

}