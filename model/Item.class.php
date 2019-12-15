<?php
namespace ec\model;

class Item
{
    public $cateArr = [];
    public $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }
    // カテゴリーリストの取得
    public function getCategoryList()
    {
        $table = ' category ';
        $col = ' ctg_id, category_name ';
        $res = $this->db->select($table, $col);
        return $res;
    }
    // 検索用の商品リスト取得
    public function getItemSearch($where)
    {
        $table = "item" . $where . " ORDER BY item_id ";
        $col = ' item_id, item_name, detail ';
        $res = $this->db->select($table, $col);
        return $res;
    }

    // 商品リストを取得する
    public function getItemList($ctg_id)
    {
        //カテゴリーによって表示させるアイテムをかえる
        $table = ' item ';
        $col = ' item_id, item_name, price, image, ctg_id ';
        $where = ($ctg_id !== '') ? ' ctg_id = ? ' : '';
        $arrVal = ($ctg_id !== '') ? [$ctg_id] : [] ;

        $res = $this->db->select($table, $col, $where, $arrVal);

        return ($res !== false && count($res) !== 0) ? $res : false;
    }

    // 商品の詳細情報を取得する
    public function getItemDetailData($item_id)
    {
        $table = ' item ';
        $col = ' item_id, item_name, detail, price, image, ctg_id ';

        $where = ($item_id !== '') ? ' item_id=? ' : '';
        // カテゴリーによって表示させるアイテムをかえる
        $arrVal = ($item_id !== '') ? [$item_id] : [] ;

        $res = $this->db->select($table, $col, $where, $arrVal);

        return ($res !== false && count($res) !== 0) ? $res : false;
    }

    public function fetch_reviews($items_id, $sql) {

            $res = $this->db->selects($items_id,  $sql);
            return ($res !== false && count($res) !== 0) ? $res : false;
        }
    public function fetch_mem($mem_id, $sql) {

            $res = $this->db->select_order($mem_id, $sql);
            return ($res !== false && count($res) !== 0) ? $res : false;
        }
    
    }