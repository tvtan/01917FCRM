<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Receipts_model extends CRM_Model
{
    function __construct()
    {
        parent::__construct();
    }
    public function get_table_where($table,$where=array())
    {
        if($where!=array())
        {
            $this->db->where($where);
        }
        return $this->db->get($table)->result_array();
    }
    public function get_table_id($table,$where="")
    {
        if($where!="")
        {
            $this->db->where($where);
        }
        return $this->db->get($table)->row();
    }
    public function get_contract()
    {
        $this->db->select('id,concat(prefix,code) as fullcode');
        return $this->db->get('tblcontracts')->result_array();
    }
    public function insert($data)
    {
        $data['date_create']=to_sql_date($data['date_create']);
        $data['date_of_accounting']=to_sql_date($data['date_of_accounting']);
        $data['day_vouchers']=to_sql_date($data['day_vouchers']);
        $this->db->insert('tblreceipts',$data);
        $id=$this->db->insert_id();
        if($id){
            return $this->db->insert_id();
        }
        return false;
    }
    public function insert_receipts_contract($id,$data)
    {
        foreach($data as $_data)
        {

            unset($_data['id']);
            $_data['total']=str_replace(',','',$_data['total']);
            $_data['discount']=str_replace(',','',$_data['discount']);
            $_data['subtotal']=str_replace(',','',$_data['subtotal']);
            $_data['id_receipts']=$id;
            $this->db->insert('tblreceipts_contract',$_data);
        }
        $id=$this->db->insert_id();
        if($id){
            return $this->db->insert_id();
        }
        return false;

    }
    public function update_status($id,$data)
    {
        $this->db->where('id',$id);
        $this->db->update('tblreceipts',$data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }
    public function get_index_receipts($id="")
    {
        $this->db->where('id',$id);
        return $this->db->get('tblreceipts')->row();
    }
    public function index_receipts_contract($id_receipts="")
    {
        $this->db->where('id_receipts',$id_receipts);
        return $this->db->get('tblreceipts_contract')->result_array();
    }
    public function update($id="",$data=array())
    {
        $data['date_create']=to_sql_date($data['date_create']);
        $data['date_of_accounting']=to_sql_date($data['date_of_accounting']);
        $data['day_vouchers']=to_sql_date($data['day_vouchers']);

        $this->db->where('id',$id);
        $this->db->update('tblreceipts',$data);
        if($this->db->affected_rows() > 0){
            return true;
        }
        return false;
    }
    public function update_receipts_cotract($id_receipts,$data=array(),$isNotDeposit=true)
    {
        $ass=0;
        if($isNotDeposit)
        {
            $_array_id=array();
            foreach($data as $rom)
            {
                $_array_id[]=$rom['id'];
            }
            $this->db->where('id_receipts',$id_receipts);
            $this->db->where_not_in('id',$_array_id);
            $this->db->delete('tblreceipts_contract');
            if($this->db->affected_rows() > 0){
                $ass++;
            }
        }

        foreach($data as $rom)
        {
            $id=$rom['id'];
            unset($rom['id']);
            $rom['total']=str_replace(',','',$rom['total']);
            $rom['discount']=str_replace(',','',$rom['discount']);
            $rom['subtotal']=str_replace(',','',$rom['subtotal']);
            $this->db->where('id',$id);
            $this->db->update('tblreceipts_contract',$rom);
            if($this->db->affected_rows() > 0){
                $ass++;
            }
        }
        if($ass > 0){
            return true;
        }
        return false;
    }
    public function get_data_pdf($id)
    {
        $this->db->select('tblreceipts.*,sum(tblreceipts_contract.total) as sum_total');
        $this->db->where('tblreceipts.id',$id);
        $this->db->join('tblreceipts_contract','tblreceipts_contract.id_receipts=tblreceipts.id','left');
        return $this->db->get('tblreceipts')->row();
    }
    public function get_sales_receipts($id_client="")
    {
        $getreceipts=$this->db->get('tblreceipts_contract')->result_array();
        $array_data=array();
        foreach($getreceipts as $rom)
        {
            $array_data[]=$rom['sales'];
        }
        $this->db->select('tblsales.*');
        $this->db->where('tblsales.customer_id',$id_client);
        if($array_data!=array())
        {
            $this->db->where_not_in('id',$array_data);
        }
        return $this->db->get('tblsales')->result_array();
    }

    public function getAllSaleCodes($id_client=NULL)
    {
        $this->db->select('CONCAT(id,"-PO") as id,CONCAT(prefix,code) as code',false);
        $POsales=$this->db->get_where('tblsale_orders',array('customer_id'=>$id_client))->result_array();
        $this->db->select('CONCAT(id,"-SO") as id,CONCAT(prefix,code) as code',false);
        $SOsales=$this->db->get_where('tblsales',array('customer_id'=>$id_client,'rel_id'=>NULL))->result_array();
        return array_merge($POsales,$SOsales);
    }

    // public function get_vouchers($field='id')
    // {
    //     $this->db->select_max('id');
    //     $id_max = $this->db->get('tblreceipts')->row()->id;
    //     $row=$this->db->get_where('tblreceipts',array('id'=>$id_max))->row();var_dump($row);die;
    //     $id=substr($row->code_vouchers, strlen(get_option('prefix_vouchers_receipts')));
    //     $last_id = strlen($id + 1);
    //     $max_code = 6;
    //     $n = $max_code - $last_id;
    //     $_code = "";
    //     if ($n > 0) {
    //         for ($i = 0; $i < $n; $i++) {
    //             $_code .= 0;
    //         }
    //     }
    //     return $last_code = get_option('prefix_vouchers_receipts') . $_code . ($id+1);
    // }

    public function get_vouchers()
    {
        $this->db->select_max('id');
        $id_max = $this->db->get('tblreceipts')->row();
        $last_id = strlen(($id_max->id) + 1);
        $max_code = 6;
        $n = $max_code - $last_id;
        $_code = "";
        if ($n > 0) {
            for ($i = 0; $i < $n; $i++) {
                $_code .= 0;
            }
        }
        return $last_code = get_option('prefix_vouchers_receipts') . $_code . ($id_max->id + 1);
    }


    public function delete($id)
    {
        if($this->db->delete('tblreceipts',array('id'=>$id)) && $this->db->delete('tblreceipts_contract',array('id_receipts'=>$id)));
        if ($this->db->affected_rows() > 0) {
            return true;
        }
        return false;
    }
}
