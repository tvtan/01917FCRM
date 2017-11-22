<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Warehouse_model extends CRM_Model
{
    private $statuses;
    function __construct()
    {
        parent::__construct();
    }
    public function get_products($id_category) {
        $this->db->where('category_id', $id_category);
        return $this->db->get('tblitems')->result_array();
    }
    function get_full($id_warehouse) {
        if(is_numeric($id_warehouse)) {
            $this->db->where('warehouseid', $id_warehouse);
            $warehouse = $this->db->get('tblwarehouses')->row();
            if($warehouse) {
                $this->db->from('tblwarehouses_products');
                $this->db->where('warehouse_id', $warehouse->warehouseid);
                $this->db->join('tblitems', 'product_id = tblitems.id' ,'left');
                $warehouse->detail = $this->db->get()->result();

                return $warehouse;
            }
        }
        return false;
    }
    function get_products_in_warehouse($id_warehouse) {
        if(is_numeric($id_warehouse)) {
            $this->db->where('warehouse_id', $id_warehouse);
            $this->db->join('tblitems', 'tblitems.id = tblwarehouses_products.product_id', 'left');
            return $this->db->get('tblwarehouses_products')->result_array();
        }
        return false;
    }
    /**
     * Get task by id
     * @param  mixed $id task id
     * @return object
     */
    public function get_roles()
    {
        $is_admin = is_admin();
        $roles = $this->db->get('tblroles')->result_array();
        return $roles;
    }

    public function getWarehouseTypes($id = '')
    {
        $this->db->select('tbl_kindof_warehouse.*');
        $this->db->from('tbl_kindof_warehouse');
        if (is_numeric($id)) 
        {
            $this->db->where('id', $id);
            return $this->db->get()->row();
        }
        else 
        {
            return $this->db->get()->result_array();
        }

        return false;
    }

    public function getWarehouses($id = '',$incluedAll=true)
    {
        
        $this->db->select('tblwarehouses.*,tbl_kindof_warehouse.name as kindof_warehouse_name');
        $this->db->join('tbl_kindof_warehouse', 'tbl_kindof_warehouse.id = tblwarehouses.kindof_warehouse', 'left');
        $this->db->from('tblwarehouses');
        $this->db->order_by('tblwarehouses.order ASC');
        if($incluedAll==false)
        {
            $this->db->where('warehouseid <>', 12);
        }
        // echo "<pre>";
        // var_dump($this->db->get()->result_array());die;
        if (is_numeric($id)) 
        {
            $this->db->where('warehouseid', $id);
            return $this->db->get()->row();
        }
        else 
        {   
            return $this->db->get()->result_array();
        }

        return false;
    }

    public function getWarehouseProduct($warehouse_id = '',$product_id = '', $includeDoesntContain = false)
    {
        // var_dump($product_id);die();
        $this->db->select('tblwarehouses.*,tbl_kindof_warehouse.name as kindof_warehouse_name,tblwarehouses_products.product_quantity, tblitems.maximum_quantity');
        $this->db->join('tbl_kindof_warehouse', 'tbl_kindof_warehouse.id = tblwarehouses.kindof_warehouse', 'left');
        $this->db->join('tblwarehouses_products', 'tblwarehouses_products.warehouse_id = tblwarehouses.warehouseid', 'left');
        $this->db->join('tblitems', 'tblitems.id = tblwarehouses_products.product_id', 'left');
        $this->db->from('tblwarehouses');
        if (is_numeric($product_id)) 
        {
            $this->db->where('product_id', $product_id);
        }
        $result = array();
        if (is_numeric($warehouse_id)) 
        {
            $this->db->where('warehouseid', $warehouse_id);
            $result = $this->db->get()->row();
        }
        else 
        {
            $result = $this->db->get()->result_array();
        }
        if(!isset($result) || count($result) == 0) {
            $this->db->select('tblwarehouses.*,tbl_kindof_warehouse.name as kindof_warehouse_name, (select 0 ) as product_quantity,, tblitems.maximum_quantity');
            $this->db->where('warehouseid', $warehouse_id);
            $this->db->join('tbl_kindof_warehouse', 'tbl_kindof_warehouse.id = tblwarehouses.kindof_warehouse', 'left');
            $this->db->join('tblitems', 'tblitems.id='.$product_id, 'right');
            $result = $this->db->get('tblwarehouses')->row();
        }
        return $result;
    }

    public function getWarehousesByType($warehouse_type = '', $filter_product='', $includeDoesntContain = false,$result_type='row')
    {

        $this->db->select('tblwarehouses.*');
        $this->db->from('tblwarehouses');
        $this->db->where('tblwarehouses.kindof_warehouse', $warehouse_type);
        if (is_numeric($warehouse_type) && is_numeric($filter_product) && $filter_product > 0) 
        {
            // Khi cần các kho không chứa thì sẽ không lọc
            
            if(!$includeDoesntContain){
                $this->db->join('tblwarehouses_products', 'tblwarehouses_products.warehouse_id=tblwarehouses.warehouseid');
                $this->db->where('tblwarehouses_products.product_id', $filter_product);
            }
            
            $warehouses = $this->db->get()->result();
            
            foreach($warehouses as $warehouse) {
                $this->db->where('warehouse_id', $warehouse->warehouseid);
                $this->db->where('product_id', $filter_product);
                $this->db->join('tblitems', 'tblitems.id = tblwarehouses_products.product_id', 'left');
                $warehouse->items = $this->db->get('tblwarehouses_products')->result();
                if(count($warehouse->items) == 0 && $includeDoesntContain) {
                    $warehouse->items = $this->db->get_where('tblitems', array('id' => $filter_product))->result();
                $warehouse->items[0]->product_quantity = 0;
                }
            }
        }
        else {
            $warehouses = $this->db->get()->result();
        }
        return $warehouses;
    }

    public function getQuantityProductInWarehouses($warehouse_id, $product_id)
    {
        if(is_numeric($warehouse_id) && is_numeric($product_id))
        {
            //Ton tai trong kho
            $this->db->select('*');
            $this->db->from('tblwarehouses_products');
            $this->db->join('tblitems', 'tblitems.id = tblwarehouses_products.product_id', 'left');
            $this->db->where('tblwarehouses_products.product_id', $product_id);
            $this->db->where('tblwarehouses_products.warehouse_id', $warehouse_id);
            $result=$this->db->get()->row();
            //Ko chua trong kho
            if(empty($result))
            {
                $this->db->select('*');
                $this->db->from('tblitems');
                $this->db->join('tblwarehouses_products', 'tblwarehouses_products.product_id = tblitems.id', 'left');
                $this->db->where('tblitems.id', $product_id);
                // $this->db->where('tblwarehouses_products.warehouse_id', $warehouse_id);
                $result=$this->db->get()->row();
            }
            if($result)
            {
                $this->db->select_sum('product_quantity');
                $result->total_quantity=$this->db->get_where('tblwarehouses_products',array('product_id'=>$product_id))->row()->product_quantity;
            }
            return $result;
        }
        return false;
    }

    public function getProductQuantity($warehouse_id = '', $product_id='')
    {
        $this->db->select('tblwarehouses_products.*');
        // $this->db->select('tblwarehouses_products.product_quantity');
        $this->db->where('tblwarehouses_products.warehouse_id', $warehouse_id);
        $this->db->where('tblwarehouses_products.product_id', $product_id);
        $product=$this->db->get('tblwarehouses_products')->row();
        if($product)
            return $product;
        else return false;
    }

    public function getProductsByWarehouseID($warehouse_id = '')
    {
        $this->db->select('tblwarehouses_products.id,tblwarehouses_products.product_id,tblwarehouses_products.product_quantity,tblwarehouses_products.warehouse_id,tblitems.name,tblitems.code');
        $this->db->from('tblwarehouses_products');
        $this->db->join('tblitems', 'tblitems.id = tblwarehouses_products.product_id', 'left');
        $this->db->where('tblwarehouses_products.warehouse_id', $warehouse_id);
        // $this->db->group_by('tblwarehouses_products.product_id');
        if (is_numeric($warehouse_id)) 
        {
            $products = $this->db->get()->result();
            if($products)
            {
                return $products;
            }
        }
        return false;
    }

    public function getWarehousesByType2($warehouse_type = '')
    {

        $this->db->select('tblwarehouses.*');
        $this->db->from('tblwarehouses');
        $this->db->where('tblwarehouses.kindof_warehouse', $warehouse_type);
        if (is_numeric($warehouse_type)) 
        {
            $warehouses = $this->db->get()->result_array();
            return $warehouses;
        }
        return false;
    }

    public function getWarehousesArrayByType($warehouse_type = '')
    {
        $this->db->select('tblwarehouses.*');
        $this->db->from('tblwarehouses');
        $this->db->where('tblwarehouses.kindof_warehouse', $warehouse_type);
        {
            $warehouses = $this->db->get()->result_array();
        }
        return $warehouses;
    }

    public function add_warehouse($data)
    {
        if (is_admin()) {
            $this->db->insert('tblwarehouses',$data);
            if ($this->db->affected_rows() >0) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function update_warehouse($data_vestion,$id)
    {
        if (is_admin()) {
            // var_dump($data_vestion);die();
            $this->db->where('warehouseid',$id);
            $this->db->update('tblwarehouses',$data_vestion);
            if ($this->db->affected_rows() >0) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function delete_warehouse($id)
    {
        if (is_admin()) {
            $this->db->where('warehouseid', $id);
            $this->db->delete('tblwarehouses');
            if ($this->db->affected_rows() > 0) {
                return true;
            }
            return false;
        }
        return false;
    }
    public function get_row_warehouse($id)
    {
        if (is_admin()) {
            $this->db->select('tblwarehouses.*');
            $this->db->where('tblwarehouses.warehouseid', $id);
            return $this->db->get('tblwarehouses')->row();
        }
    }

//    public function get_call_logs_and_staff($id, $where = array())
//    {
//        $this->db->where('c.ID', $id);
//        $this->db->join('tblstaff s', 'c.assigned = s.staffid', 'left');
//        $this->db->select('s.*');
//        $this->db->from('tblcall_logs c');
////        $task = $this->db->get('tblcall_logs')->row();
////        $task->assignees       = $this->get_task_assignees($id);
//        return $this->db->get()->result_array();
//    }
//    public function get_task_assignees($id)
//    {
//        $this->db->select('id,tblstafftaskassignees.staffid as assigneeid,assigned_from,firstname,lastname');
//        $this->db->from('tblstafftaskassignees');
//        $this->db->join('tblstaff', 'tblstaff.staffid = tblstafftaskassignees.staffid', 'left');
//        $this->db->where('taskid', $id);
//        return $this->db->get()->result_array();
//    }
//    public function get_all_assignees()
//    {
//        $this->db->select('*');
//        $this->db->from('tblstaff');
////        $this->db->join('tblstaff', 'tblstaff.staffid = tblstafftaskassignees.staffid', 'left');
////        $this->db->where('taskid', $id);
//        return $this->db->get()->result_array();
//    }
//

//
//
//    public function remove_assignee($id, $taskid)
//    {
//        $task = $this->get($taskid);
//        $this->db->where('id', $id);
//        $assignee_data = $this->db->get('tblstafftaskassignees')->row();
//        $this->db->where('id', $id);
//        $this->db->delete('tblstafftaskassignees');
//        if ($this->db->affected_rows() > 0) {
//            if ($task->rel_type == 'project') {
//                $this->projects_model->log_activity($task->rel_id, 'project_activity_task_assignee_removed', $task->name . ' - ' . get_staff_full_name($assignee_data->staffid), $task->visible_to_client);
//            }
//            return true;
//        }
//        return false;
//    }
//
//
//
//    public function update($data,$id)
//    {
//        if (is_admin()) {
//            $this->db->where('ID', $id);
//            if(!isset($data['checkout']))
//            {
//                $data['checkout']=0;
//            }
//            $data['assigned']=implode(',',$data['assigned']);
//            $this->db->update('tblcall_logs',$data);
//            if ($this->db->affected_rows() >0) {
////                logActivity('Reminder Deleted [' . ucfirst($reminder->rel_type) . 'ID: ' . $reminder->id . ' Description: ' . $reminder->description . ']');
//                return true;
//            }
//            return false;
//        }
//        return false;
//    }
//    public function add($data,$idlead)
//    {
//        if (is_admin()) {
//            if(!isset($data['checkout']))
//            {
//                $data['checkout']=0;
//            }
//            $data['id_lead']=$idlead;
//            $data['assigned']=implode(',',$data['assigned']);
//            $this->db->insert('tblcall_logs',$data);
//            if ($this->db->affected_rows() >0) {
//                return true;
//            }
//            return false;
//        }
//        return false;
//    }
}
