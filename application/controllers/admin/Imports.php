<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Imports extends Admin_controller
{

    function __construct()
    {
        
        parent::__construct();
        $this->load->model('imports_model');
        $this->load->model('invoice_items_model');
        $this->load->model('warehouse_model');
        $this->load->model('accounts_model');
        $this->load->model('purchase_contacts_model');
        $this->load->model('suppliers_model');
    
    }
    public function index($id=NULL) {
        // var_dump(has_permission('view_account',24,'have'));die;
        $this->imports_model->updateWarehouse($id);
    }

    public function imp_return() 
    {
        // if (!has_permission('import_items', '', 'view')) {
        //     access_denied('customers');
        // }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('adjustments',array('rel_type'=>'return'));
        }
        $data['title'] = _l('importfromreturn');
        $this->load->view('admin/imports/returns/returns', $data);
    }

    public function return_detail($id='') 
    {
        // if (!has_permission('import_items', '', 'view')) {
        //     if ($id != '' && !is_customer_admin($id)) {
        //         access_denied('import_items');
        //     }
        // }
        if ($this->input->post() && !$this->input->is_ajax_request()) {
            $data                 = $this->input->post();

            if ($id == '') {
                if (!has_permission('chuyenkho', '', 'create')) {
                    access_denied('chuyenkho');
                }

                
                if(isset($data['items']) && count($data['items']) > 0)
                {
                    $id = $this->imports_model->add($data);
                }
                
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('importfromreturn')));
                    redirect(admin_url('imports/imp_return'));
                }
            } else {

                if (!has_permission('chuyenkho', '', 'edit')) {
                        access_denied('chuyenkho');
                }
                $success = $this->imports_model->update($data, $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfuly', _l('importfromreturn')));
                    redirect(admin_url('imports/imp_return'));
                }
                else
                {
                    redirect(admin_url('imports/return_detail/'.$id));
                }
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('importfromreturn'));

        } else {
            $data['item'] = $this->imports_model->getImportByID($id);
            $data['warehouse_id']=$data['item']->items[0]->warehouse_id;

            if (!$data['item']) {
                blank_page('Purchase Not Found');
            }
        }

        $where_clients = 'tblclients.active=1';

        if (!has_permission('customers', '', 'view')) {
            $where_clients .= ' AND tblclients.userid IN (SELECT customer_id FROM tblcustomeradmins WHERE staff_id=' . get_staff_user_id() . ')';
        }
        $data['accounts_no'] = $this->accounts_model->get_tk_no();
        $data['accounts_co'] = $this->accounts_model->get_tk_co();
        $data['suppliers']=$this->suppliers_model->get();
        $data['customers'] = $this->clients_model->get('', $where_clients);
        $data['contracts']=$this->purchase_contacts_model->get();
        $data['items']= $this->invoice_items_model->get_full('');    
        $data['warehouse_types']= $this->imports_model->getWarehouseTypes();
        $data['warehouses']= $this->warehouse_model->getWarehouses();
        if($data['warehouse_id'])
            $data['warehouses']= $this->warehouse_model->getWarehousesByType2(getWHTIDByWHID($data['warehouse_id']));
        
        $data['title'] = $title;
        $this->load->view('admin/imports/returns/detail', $data);
    }

    public function imp_transfer() 
    {
        // if (!has_permission('import_items', '', 'view')) {
        //     access_denied('customers');
        // }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('adjustments',array('rel_type'=>'transfer'));
        }
        $data['title'] = _l('importfromtranfer');
        $this->load->view('admin/imports/transfers/transfers', $data);
    }

    public function transfer_detail($id='') 
    {
        if (!has_permission('view_account', '24', 'have')) {
            $data['view_account']=array('style'=>'style="display: none;"','class'=>'hide');
        }
        if (!has_permission('view_price', '24', '')) {
            $data['view_price']=array('style'=>'style="display: none;"','class'=>'hide');
        }
        if ($this->input->post() && !$this->input->is_ajax_request()) {
            $data                 = $this->input->post();            
            if ($id == '') {
                if (!has_permission('chuyenkho', '', 'create')) {
                    access_denied('chuyenkho');
                }

                
                if(isset($data['items']) && count($data['items']) > 0)
                {
                    $id = $this->imports_model->add($data);
                }
                
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('importfromtranfer')));
                    redirect(admin_url('imports/imp_transfer'));
                }
            } else {

                if (!has_permission('chuyenkho', '', 'edit')) {
                        access_denied('chuyenkho');
                }
                $success = $this->imports_model->update($data, $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfuly', _l('importfromtranfer')));
                    redirect(admin_url('imports/imp_transfer'));
                }
                else
                {
                    redirect(admin_url('imports/transfer_detail/'.$id));
                }
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('importfromtranfer'));
            $data['receivers']=$this->staff_model->getStaffReceivers();

        } else {
            $data['item'] = $this->imports_model->getImportByID($id);
            foreach($data['item']->items as $key=> $vla)
            {
                $this->db->select('entered_price');
                $this->db->where('warehouse_id',$vla->warehouse_id);
                $this->db->where('product_id',$vla->product_id);
                $view_entered_price=$this->db->get('tblimport_items')->result_array();
                $data['item']->items[$key]->array_entered_price=json_encode($view_entered_price);
            }

            
            $data['receivers']=$this->clients_model->getClientReceivers();
            if($data['item']->is_staff!=2)
            {
                $data['receivers']=$this->staff_model->getStaffReceivers();
            }
            
           
            $data['warehouse_id']=$data['item']->items[0]->warehouse_id;

            $data['warehouse_id_to']=$data['item']->items[0]->warehouse_id_to;

            if (!$data['item']) {
                blank_page('Purchase Not Found');
            }
        }
        $data['delivers']=$this->staff_model->getStaffReceivers();
        $data['accounts_no'] = $this->accounts_model->get_tk_no();
        $data['accounts_co'] = $this->accounts_model->get_tk_co();
        $data['suppliers']=$this->suppliers_model->get();
        $data['contracts']=$this->purchase_contacts_model->get();
        $data['items']= $this->invoice_items_model->get_full('');
        $data['money']=get_table_where('tblwarehouse_product_details');
        $data['warehouse_types']= $this->imports_model->getWarehouseTypes();
        $data['warehouses']= $this->warehouse_model->getWarehouses();
        
        $data['title'] = $title;
        $this->load->view('admin/imports/transfers/detail', $data);
    }

    public function get_entered_price($warehouse_id,$id)
    {
        $this->db->select('distinct(entered_price)');
        $this->db->where('warehouse_id',$warehouse_id);
        $this->db->where('product_id',$id);
        $result=$this->db->get('tblwarehouse_product_details')->result_array();
        echo json_encode($result);

    }

    public function teest($id=82,$product_id=162,$warehouse_id=1,$quantity=155,$entered_price=555,$date='2017-10-19')
    {

        var_dump(increaseWarehouseProductDetail($id,$product_id,$warehouse_id,$quantity,$entered_price,$date));
    }


    public function imp_contract() 
    {
        // if (!has_permission('import_items', '', 'view')) {
        //     access_denied('customers');
        // }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('adjustments',array('rel_type'=>'contract'));
        }
        $data['title'] = _l('importfromcontract');
        $this->load->view('admin/imports/contracts/contracts', $data);
    }

    public function contract_detail($id='') 
    {
        if ($this->input->post() && !$this->input->is_ajax_request()) {
            $data                 = $this->input->post();
            if ($id == '') {
                if (!has_permission('chuyenkho', '', 'create')) {
                    access_denied('chuyenkho');
                }
                if(isset($data['items']) && count($data['items']) > 0)
                {
                    $id = $this->imports_model->add($data);
                }
                
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('importwhfromcontract')));
                    redirect(admin_url('imports/imp_contract'));
                }
            } else {

                if (!has_permission('chuyenkho', '', 'edit')) {
                        access_denied('chuyenkho');
                }
                $success = $this->imports_model->update($data, $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfuly', _l('importwhfromcontract')));
                    redirect(admin_url('imports/imp_contract'));
                }
                else
                {
                    redirect(admin_url('imports/contract_detail/'.$id));
                }
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('adjustments'));
            if (!has_permission('import_items', '', 'create')) {
                access_denied('import_items');
            }
        } else {
            $data['item'] = $this->imports_model->getImportByID($id);
            $data['warehouse_id']=$data['item']->items[0]->warehouse_id;
            $data['warehouse_type']=$this->warehouse_model->getWarehouses($data['warehouse_id'])->kindof_warehouse;

            foreach($data['item']->items as $key=>$value) {
            $warehouse_id=$value->warehouse_id;
            $data['item']->items[$key]->warehouse_type = $this->warehouse_model->getQuantityProductInWarehouses($value->warehouse_id,$value->product_id);
            }

            if (!$data['item']) {
                blank_page('Purchase Not Found');
            }
        }

        // print_r($data['item']->items);die;

        $data['accounts_no'] = $this->accounts_model->get_tk_no();
        $data['accounts_co'] = $this->accounts_model->get_tk_co();
        $data['suppliers']=$this->suppliers_model->get();
        $data['contracts']=$this->purchase_contacts_model->get();
        $data['items']= $this->invoice_items_model->get_full('');    
        $data['warehouse_types']= $this->imports_model->getWarehouseTypes();
        $data['warehouses']= $this->warehouse_model->getWarehouses();

        $data['title'] = $title;
        $this->load->view('admin/imports/contracts/detail', $data);
    }

    public function imp_adjustment() 
    {
        if (!has_permission('import_items', '', 'view')) {
            access_denied('customers');
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('adjustments',array('rel_type'=>'adjustment'));
        }
        $data['title'] = _l('Điều chỉnh kho hàng');
        $this->load->view('admin/imports/adjustments/adjustment', $data);
    }



    public function adjustment_detail($id='') 
    {
        if (!has_permission('import_items', '', 'view')) {
            access_denied('import_items');
        }
        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('import_items', '', 'create')) {
                    access_denied('import_items');
                }

                $data                 = $this->input->post();
                if(isset($data['items']) && count($data['items']) > 0)
                {
                    $id = $this->imports_model->add($data);
                }
                
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('adjustments')));
                    redirect(admin_url('imports/imp_adjustment'));
                }
            } else {
                if (!has_permission('import_items', '', 'edit')) {
                    access_denied('import_items');
                }
                $success = $this->imports_model->update($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfuly', _l('adjustments')));
                    redirect(admin_url('imports/imp_adjustment'));
                }
                else
                {
                    redirect(admin_url('imports/adjustment_detail/'.$id));
                }
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('adjustments'));

        } else {
            $data['item'] = $this->imports_model->getImportByID($id);
            $data['warehouse_id']=$data['item']->items[0]->warehouse_id;
            $data['warehouse_type']=$this->warehouse_model->getWarehouses($data['warehouse_id'])->kindof_warehouse;

            if (!$data['item']) {
                blank_page('Purchase Not Found');
            }
        }

        $data['accounts_no'] = $this->accounts_model->get_tk_no();
        $data['accounts_co'] = $this->accounts_model->get_tk_co();
        $data['items']= $this->invoice_items_model->get_full('');    
        $data['warehouse_types']= $this->imports_model->getWarehouseTypes();
        $data['warehouses']= $this->warehouse_model->getWarehouses();

        $data['title'] = $title;
        $this->load->view('admin/imports/adjustments/detail', $data);
    }

    public function getReceivers($is_staff=1)
    {
        if(is_numeric($is_staff))
        {
            $receivers=$this->clients_model->getClientReceivers();
            if($is_staff==1)
            {
                $receivers=$this->staff_model->getStaffReceivers();
            }
            echo json_encode($receivers);
        }
    }



    public function imp_internal() 
    {
        if (!has_permission('import_items', '', 'view')) {
            access_denied('customers');
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('adjustments',array('rel_type'=>'internal'));
        }
        $data['title'] = _l('Điều chỉnh kho hàng nội địa');
        $this->load->view('admin/imports/internals/internal', $data);
    }

    public function internal_detail($id='') 
    {
        // var_dump(has_permission('view_price', '', 'view'));die;
        if (!has_permission('import_items', '', 'view')) {
            access_denied('import_items');
        }
        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('import_items', '', 'create')) {
                    access_denied('import_items');
                }

                $data                 = $this->input->post();
                if(isset($data['items']) && count($data['items']) > 0)
                {
                    $id = $this->imports_model->add($data);
                }
                
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('internals')));
                    redirect(admin_url('imports/imp_internal'));
                }
            } else {

                if (!has_permission('import_items', '', 'edit')) {
                        access_denied('import_items');
                }
                $success = $this->imports_model->update($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfuly', _l('internals')));
                    redirect(admin_url('imports/imp_internal'));
                }
                else
                {
                    redirect(admin_url('imports/internal_detail/'.$id));
                }
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('internals'));

        } else {
            $data['item'] = $this->imports_model->getImportByID($id);
            
            $data['warehouse_id']=$data['item']->items[0]->warehouse_id;
            $data['warehouse_type']=$this->warehouse_model->getWarehouses($data['warehouse_id'])->kindof_warehouse;
            if (!$data['item']) {
                blank_page('Purchase Not Found');
            }
        }

        $data['accounts_no'] = $this->accounts_model->get_tk_no();
        $data['accounts_co'] = $this->accounts_model->get_tk_co();
        $data['delivers'] = $this->staff_model->get('','',array('staffid<>'=>1));
        $data['items']= $this->invoice_items_model->get_full('');    
        $data['warehouse_types']= $this->imports_model->getWarehouseTypes();
        $data['warehouses']= $this->warehouse_model->getWarehouses();
        $data['title'] = $title;
        $this->load->view('admin/imports/internals/detail', $data);
    }

    /* Get task data in a right pane */
    public function delete_import($id)
    {
        if (!$id) {
            die('Không tìm thấy mục nào');
        }

        $success    = $this->imports_model->cancel_warehouses_adjustment($id);
        $alert_type = 'warning';
        $message    = _l('unsuccessfull_cancel');
        if ($success) {
            $alert_type = 'success';
            $message    = _l('successfull_cancel');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));

    }

    public function restore_import($id)
    {
        if (!$id) {
            die('Không tìm thấy mục nào');
        }

        $success    = $this->imports_model->restore_warehouses_adjustment($id);
        $alert_type = 'warning';
        $message    = _l('unsuccessfull_restore');
        if ($success) {
            $alert_type = 'success';
            $message    = _l('successfull_restore');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));

    }

    public function warehouses_overview() {
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('warehouses_products');
        }
        $data['title'] = _l('Tổng quan kho');        
        $this->load->view('admin/imports/warehouse_products', $data);
    }

    public function update_status()
    {
        $id=$this->input->post('id');
        $status=$this->input->post('status');
        $type = $this->input->post('type');
        $staff_id=get_staff_user_id();
        $date=date('Y-m-d H:i:s');
        $data=array('status'=>$status);
        $inv=$this->imports_model->getImportByID($id);
        if(is_admin() && $status==0)
        {
            $data['user_head_id']=$staff_id;
            $data['user_head_date']=$date;

            $data['user_admin_id']=$staff_id;
            $data['user_admin_date']=$date;

            $data['status']=2;
        }
        elseif(is_admin() && $status==1)
        {
            $data['status']=2;
            if($inv->user_head_id==NULL || $inv->user_head_id=='')
            {
                $data['user_head_id']=$staff_id;
                $data['user_head_date']=$date;
            }
            if($inv->user_admin_id==NULL || $inv->user_admin_id=='')
            {
                $data['user_admin_id']=$staff_id;
                $data['user_admin_date']=$date;
            }
        }
        elseif(can_update_staff($inv->create_by))
        {
            $data['status']+=1;
            $data['user_head_id']=$staff_id;
            $data['user_head_date']=$date;
        }

        $success=false;
        if(!$type) {
            exit(json_encode(array(
                'success' => false,
                'result'=>true,
                'message' => _l('Không có quyền xác nhận!')
            )));
        }
        else {
            if(is_admin()) {
                $success=$this->imports_model->update_status($id,$data);
            }
            else if($type=='transfers' && has_permission('chuyenkho', '', 'approve')) {
                $success=$this->imports_model->update_status($id,$data);
            }
            else if($type=='adjustment' && has_permission('dieuchinhkho', '', 'approve')) {
                $success=$this->imports_model->update_status($id,$data);
            }
            // else if($type=='contracts' && has_permission('import_items', '', 'edit')) {
                // $success=$this->imports_model->update_status($id,$data);
            // }
            else if($type=='internals' && has_permission('import_items', '', 'approve')) {
                $success=$this->imports_model->update_status($id,$data);
            }
            else if($type=='returns' && has_permission('hangtrave', '', 'approve')) {
                $success=$this->imports_model->update_status($id,$data);
            }
            else {
                exit(json_encode(array(
                    'success' => false,
                    'result'=>true,
                    'message' => _l('Không có quyền xác nhận!')
                )));
            }
        }
        
        
        if($success) {
            echo json_encode(array(
                'success' => $success,
                'result'=>true,
                'message' => _l('Xác nhận phiếu thành công')
            ));
        }
        else
        {
            echo json_encode(array(
                'success' => $success,
                'result'=>true,
                'message' => _l('Không thể cập nhật dữ liệu')
            ));
        }
        die;
    }
    
    public function detail_pdf($id)
    {
        if (!has_permission('import_items', '', 'view') && !has_permission('import_items', '', 'view_own')) {
            access_denied('import_items');
        }
        if (!$id) {
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $invoice        = $this->imports_model->getImportByID($id);
        $invoice_number = $invoice->prefix.$invoice->code;
        
// var_dump($invoice);die;
        $pdf            = import_detail_pdf($invoice);
        
        $type           = 'D';
        if ($this->input->get('pdf') || $this->input->get('print')) {
            $type = 'I';
        }
        $pdf->Output(mb_strtoupper(slug_it($invoice_number)) . '.pdf', $type);
    }
    
}