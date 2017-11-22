<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Sale_orders extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('sale_oders_model');
        $this->load->model('invoice_items_model');
        $this->load->model('clients_model');
        $this->load->model('warehouse_model');
        $this->load->model('receipts_model');
        $this->load->model('report_have_model');
    }
    

    public function index()
    {
        // var_dump(checkDeposit(221,'PO'));die;
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('sales');
        }
        $data['title'] = _l('sale_orders');
        $this->load->view('admin/sales/manage', $data);
    }

    public function list_sale_orders()
    {
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('sale_orders');
        }
    }


    public function sale_detail($id='') 
    {
        if (!has_permission('po', '', 'view') && !has_permission('po', '', 'view_own')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('po');
            }
        }
        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('po', '', 'create')) {
                    access_denied('po');
                }

                $data                 = $this->input->post();
                
                if(isset($data['items']) && count($data['items']) > 0)
                {
                    $id = $this->sale_oders_model->add($data);
                }
                
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('sale')));
                    redirect(admin_url('sale_orders'));
                }
            } else {

                if (!has_permission('po', '', 'edit')) {
                        access_denied('po');
                }
                $success = $this->sale_oders_model->update($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfuly', _l('sale')));
                    redirect(admin_url('sale_orders'));
                }
                else
                {
                    redirect(admin_url('sales/sale_detail/'.$id));
                }
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('sales_po'));
      
        } 
        else 
        {
            $data['item'] = $this->sale_oders_model->getSaleByID($id);
            if($data['item']->rel_id)
            {
                $data['khoa']=true;
            }
            
            $data['item_returns'] = $this->sale_oders_model->getReturnSaleItems($id);
            $i=0;
            foreach ($data['item']->items as $key => $value) {       
                $data['item']->items[$i]->warehouse_type=$this->warehouse_model->getWarehouseProduct($value->warehouse_id,$value->product_id);                
                $data['item']->items[$i]->exports=getAllSaleProductDetails($id,$value->product_id);
                $i++;
            }
            $data['isedit']='1';
            $data['warehouse_id'] = $data['item']->items[0]->warehouse_id;
            $data['warehouse_type_id']=$data['item']->items[0]->warehouse_type->kindof_warehouse;
           
            if(isset($data['item']->rel_id))
            {
                $data['isedit']=false;
            }
            if (!$data['item']) {
                blank_page('Sale Not Found');
            }
        }  

        $where_clients = 'tblclients.active=1';

        if (!has_permission('customers', '', 'view')) {
            $where_clients .= ' AND tblclients.userid IN (SELECT customer_id FROM tblcustomeradmins WHERE staff_id=' . get_staff_user_id() . ')';
        }

        $data['tk_no']=$this->receipts_model->get_table_where('tblaccounts','idAccountAttribute=1 or idAccountAttribute=3');
        $data['tk_co']=$this->receipts_model->get_table_where('tblaccounts','idAccountAttribute=2 or idAccountAttribute=3');
        $data['customer_id']= $this->input->get('customer_id');
        $data['warehouse_types']= $this->warehouse_model->getWarehouseTypes();
        $data['items']= $this->invoice_items_model->get_full('',$data['warehouse_id']);
        $data['warehouses']= $this->warehouse_model->getWarehouses();
        $data['addresses']= $this->clients_model->getClientAddress($data['item']->customer_id);
        $data['salers']= $this->staff_model->get('','',array('staffid <>'=>1));
        $data['customers']= $this->clients_model->get('',$where_clients);
        $data['customersCBO']=$this->clients_model->getClientsCBO();
        $data['title'] = $title;
        $this->load->view('admin/sales/order_detail', $data);
    }

    public function receipt($customer_id, $sale_id = '',$type='PO',$isDeposit=false)
    {
        if (!has_permission('receipts', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('receipts');
            }
        }
        if ($this->input->post() && !$this->input->is_ajax_request()) {
            $data                 = $this->input->post();
            $receipt_type=$data['receipts'];
            unset($data['receipts']);
            $id=$data['id_receipts'];
            unset($data['id_receipts']);
            if(!$id)
                $id=$data['id_report_have'];
            unset($data['id_report_have']);
            if(!$id) 
            {
                $id=$data['id_report_have'];
                unset($data['id_report_have']);
            }

            if($receipt_type=='receipt')
            {
                $this->receiptType($data,$id);
            }
            else
            {
                $this->reportHaveType($data,$id);
            }
        }
        else
        {
            $data['code_vouchers']=$this->receipts_model->get_vouchers();
            $data['code_vouchers2']=$this->report_have_model->get_vouchers();
            // $data['receipt']=checkDeposit($sale_id,$type);
            if(!$data['receipt']) unset($data['receipt']);
            $data['account_person']=$this->report_have_model->get_table_where('tblaccount_person');
            $data['sales']=$this->receipts_model->getAllSaleCodes($customer_id);
            $data['customer_id']=$customer_id;
            $data['sale_id']=$sale_id;
            $data['type']=$type;
            $data['isDeposit']=$isDeposit;
             if($data['receipt'])
             {
                $data['isDeposit']=$data['receipt']->isDeposit;
                $data['code_vouchers']=$data['receipt']->code_vouchers;
                $data['code_vouchers2']=$data['receipt']->code_vouchers;
             } 
            $data['title']=_l('receive_deposit_customer');
            $data['client']=$this->receipts_model->get_table_where('tblclients');
            $data['currencies']=$this->receipts_model->get_table_where('tblcurrencies');
            $data['tk_no']=$this->receipts_model->get_table_where('tblaccounts','idAccountAttribute=1 or idAccountAttribute=3');
            $data['tk_co']=$this->receipts_model->get_table_where('tblaccounts','idAccountAttribute=2 or idAccountAttribute=3');
            $data['tk_ck']=$this->receipts_model->get_table_where('tblaccounts');
            
            $this->load->view('admin/sales/receipt_modal',$data);
        }
    }

public function receiptType($data=array(),$id=NULL)
{

    if ($id == '') {
        if (!has_permission('receipts', '', 'create')) {
            access_denied('receipts');
        }       

        $_data=$data['items'];
        unset($data['items']);
        $data['id_staff']=get_staff_user_id();
        $id = $this->receipts_model->insert($data);
        if($id) 
        {
            $_id = $this->receipts_model->insert_receipts_contract($id, $_data);
            if($_id)
            {
                set_alert('success', _l('added_successfuly', _l('receipts')));
                redirect($_SERVER['HTTP_REFERER']);
            }
            else
            {
                set_alert('danger', _l('problem_adding'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        else
        {
            set_alert('danger', _l('problem_adding'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    } else {

        if (!has_permission('receipts', '', 'edit')) {
                access_denied('receipts');
        } 
        $_data['item']=$data['item'];
        $_data['items']=$data['items'];
        unset($data['item']);
        unset($data['items']);          

        $result=$this->receipts_model->update($id,$data);
        $_result=$this->receipts_model->update_receipts_cotract($id,$_data['item'],false);
        $__result=$this->receipts_model->insert_receipts_contract($id,$_data['items']);
        if($result||$_result||$__result)
        {
            set_alert('success', _l('updated_successfuly',_l('receipts')));
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
}
public function reportHaveType($data=array(),$id=NULL)
{
    if($id=="")
    {
        if (!has_permission('report_have', '', 'create')) {
            access_denied('report_have');
        }
        
        $_data=$data['items'];
        unset($data['items']);
        $id_client=$data['id_client'];
        unset($data['id_client']);
        $data['id_staff']=  get_staff_user_id();
        $id = $this->report_have_model->insert($data);
        if($id) {

            $_id = $this->report_have_model->insert_report_have_contract($id, $_data);
            if($_id)
            {
                set_alert('success', _l('added_successfuly', _l('report_have')));
                redirect($_SERVER['HTTP_REFERER']);
            }
            else
            {
                set_alert('danger', _l('problem_adding'));
                redirect($_SERVER['HTTP_REFERER']);
            }
        }
        else
        {
            set_alert('danger', _l('problem_adding'));
            redirect($_SERVER['HTTP_REFERER']);
        }
    }
    else
    {
        if (!has_permission('report_have', '', 'edit')) {
            access_denied('report_have');
        }
        $_data['item']=$data['item'];
        $_data['items']=$data['items'];
        unset($data['item']);
        unset($data['items']);

        $result=$this->report_have_model->update($id,$data);
        $_result=$this->report_have_model->update_report_have_cotract($id,$_data['item'],false);
        $__result=$this->report_have_model->insert_report_have_contract($id,$_data['items']);
       
        if($result||$_result||$__result)
        {
            set_alert('success', _l('updated_successfuly',_l('report_have')));
        }
        redirect($_SERVER['HTTP_REFERER']);
    }
}
    public function sale_output($id)
    {

        if (!has_permission('so', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('so');
            }
        }


        {            
            $data['item'] = $this->sale_oders_model->getSaleByID($id);
            $i=0;
            foreach ($data['item']->items as $key => $value) {    
                $warehouse=(is_array($this->warehouse_model->getWarehouseProduct($value->warehouse_id,$value->product_id))&& count($this->warehouse_model->getWarehouseProduct($value->warehouse_id,$value->product_id))==1)? ($this->warehouse_model->getWarehouseProduct($value->warehouse_id,$value->product_id)[0]) : ($this->warehouse_model->getWarehouseProduct($value->warehouse_id,$value->product_id));                
                $data['item']->items[$i]->warehouse_type=$warehouse;
                $data['item']->items[$i]->exports=getAllSaleProductDetails($id,$value->product_id);
                $i++;
            }
            if (!$data['item']) {
                blank_page('Export Not Found');
            }   
        }

        $data['warehouse_id'] = $data['item']->items[0]->warehouse_id;
        $where_clients = 'tblclients.active=1';

        if (!has_permission('customers', '', 'view')) {
            $where_clients .= ' AND tblclients.userid IN (SELECT customer_id FROM tblcustomeradmins WHERE staff_id=' . get_staff_user_id() . ')';
        }
        $data['addresses']= $this->clients_model->getClientAddress($data['item']->customer_id);
        $data['salers']= $this->staff_model->get('','',array('staffid <>'=>1));
        $data['warehouses']= $this->warehouse_model->getWarehouses();
        $data['receivers'] = $this->staff_model->get('','',array('staffid<>'=>1));
        
        $data['customers'] = $this->clients_model->get('', $where_clients);
        $data['items']= $this->invoice_items_model->get_full();
        $data['tk_no']=$this->receipts_model->get_table_where('tblaccounts','idAccountAttribute=1 or idAccountAttribute=3');
        $data['tk_co']=$this->receipts_model->get_table_where('tblaccounts','idAccountAttribute=2 or idAccountAttribute=3');

        $data['title'] = _l('Tạo phiếu bán hàng(SO)');        
        $this->load->view('admin/sales/order_export_detail', $data);
    }



    /* Get task data in a right pane */
    public function delete($id)
    {
        if (!$id) {
            die('Không tìm thấy mục nào');
        }

        $success    = $this->sale_oders_model->delete($id);
        $alert_type = 'warning';
        $message    = _l('Không thể xóa dữ liệu');
        if ($success) {
            $alert_type = 'success';
            $message    = _l('Xóa dữ liệu thành công');
        }
        echo json_encode(array(
            'alert_type' => $alert_type,
            'message' => $message
        ));

    }

    public function update_status()
    {
        if (!has_permission('po', '', 'approve')) {
            access_denied('po');
        }
        $id=$this->input->post('id');
        $status=$this->input->post('status');
        $staff_id=get_staff_user_id();
        $date=date('Y-m-d H:i:s');
        $data=array('status'=>$status);

        $inv=$this->sale_oders_model->getSaleByID($id);
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
        elseif(has_permission('po', '', 'approve'))
        {
            $data['status']+=1;
            $data['user_head_id']=$staff_id;
            $data['user_head_date']=$date;
        }

        $success=fale;
        
        if(is_admin() || has_permission('po', '', 'approve'))
        {
            $success=$this->sale_oders_model->update_status($id,$data);
        }
        if($success) {
            echo json_encode(array(
                'success' => $success,
                'message' => _l('Xác nhận phiếu thành công')
            ));
        }
        else
        {
            echo json_encode(array(
                'success' => $success,
                'message' => _l('Không thể cập nhật dữ liệu')
            ));
        }
        die;
    }

    public function test($order_id)
    {
        $this->sale_oders_model->createReturnItems($order_id);
    }

    
    public function pdf($id)
    {
        if (!has_permission('po', '', 'view') && !has_permission('po', '', 'view_own')) {
            access_denied('po');
        }
        if (!$id) {
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $invoice        = $this->sale_oders_model->getSaleByID($id);
        $invoice_number = $invoice->prefix.$invoice->code;

        $pdf            = sale_order_detail_pdf($invoice);
        $type           = 'D';
        if ($this->input->get('pdf') || $this->input->get('print')) {
            $type = 'I';
        }
        $pdf->Output(mb_strtoupper(slug_it($invoice_number)) . '.pdf', $type);
    }

    public function pdfSpecifications($id)
    {
        if (!has_permission('po', '', 'view') && !has_permission('po', '', 'view_own')) {
            access_denied('po');
        }
        if (!$id) {
            redirect($_SERVER["HTTP_REFERER"]);
        }
        $invoice        = $this->sale_oders_model->getSaleByID($id);
        $invoice_number = $invoice->prefix.$invoice->code;

        $pdf            = sale_detail_specifications_pdf($invoice);
        $type           = 'D';
        if ($this->input->get('pdf') || $this->input->get('print')) {
            $type = 'I';
        }
        $pdf->Output(mb_strtoupper(slug_it($invoice_number)) . '.pdf', $type);
    }

    public function getSaleProductDetail($product_id=NULL,$warehouse_id=NULL,$quantity=NULL)
    {
        if($this->input->post('product_id')) $product_id=$this->input->post('product_id');
        if($this->input->post('warehouse_id')) $warehouse_id=$this->input->post('warehouse_id');
        if($this->input->post('quantity')) $quantity=$this->input->post('quantity');
        $result=getSaleProductDetail($product_id,$warehouse_id,$quantity);
        echo json_encode($result);
    }
    
}