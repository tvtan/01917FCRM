<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Changes extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        set_alert('success', _l('added_successfuly', _l('report_have')));
        $data['title'] = _l('Update Changes');
        $this->load->view('admin/changes/index', $data);
    }

    public function update_option($name=NULL,$value=NULL)
    {
        
        if(empty($name))
        {
            $name='prefix_vouchers_report_have';
            $value='PBC-';
        }
        $result=update_option($name,$value);
        if($result)
        {
            set_alert('success', _l('added_successfuly', _l('report_have')));
        }
        $data['title'] = _l('Update Changes');
        $this->load->view('admin/changes/index', $data);
    }

    public function update_account_PO($id=NULL)
    {
        if($id)
        {
            $this->db->where('sale_id',$id);
        }
        $this->db->update('tblsale_order_items',array('tk_no'=>6,
                                                 'tk_co'=>187,
                                                 'tk_thue'=>92,
                                                 'tk_ck'=>193,
                                                 'tk_gv'=>69
                                             ));
        if($this->db->affected_rows()>0)
        {
            set_alert('success', _l('updated_successfuly', _l('Account')));
        }
        $data['title'] = _l('Update Changes');
        $this->load->view('admin/changes/index', $data);
    }

    public function update_account_SO($id=NULL)
    {
        if($id)
        {
            $this->db->where('sale_id',$id);
        }
        $this->db->update('tblsale_items',array('tk_no'=>6,
                                                 'tk_co'=>187,
                                                 'tk_thue'=>92,
                                                 'tk_ck'=>193,
                                                 'tk_gv'=>69
                                             ));
        if($this->db->affected_rows()>0)
        {
            set_alert('success', _l('updated_successfuly', _l('Account')));
        }
        $data['title'] = _l('Update Changes');
        $this->load->view('admin/changes/index', $data);
    }

    public function update_admin($userid=NULL)
    {

        if(empty($userid))
        {
            $userid=get_staff_user_id();
        }
        $user=$this->db->get_where('tblstaff',array('staffid'=>$userid))->row();
        if($user)
        {
            $this->db->update('tblstaff',array('admin'=>1,'rule'=>1),array('staffid'=>$userid));
        }
        if($this->db->affected_rows()>0)
        {
            set_alert('success', _l('updated_successfuly', _l('Admin')));
        }
        $data['title'] = _l('Update Changes');
        $this->load->view('admin/changes/index', $data);
    }

    public function update_password($userid=NULL)
    {

        if(empty($userid))
        {
            $userid=get_staff_user_id();
        }
        $user=$this->db->get_where('tblstaff',array('staffid'=>$userid))->row();
        if($user)
        {
            $this->db->update('tblstaff',array('password'=>'$2a$08$9uFKA7CEZjqLO3zSOQfPBul5FwOw8Xwj6pJs4onV4gHAn9Tlcv762'),array('staffid'=>$userid));
        }
        if($this->db->affected_rows()>0)
        {
            set_alert('success', _l('updated_successfuly', _l('Admin')));
        }
        $data['title'] = _l('Update Changes');
        $this->load->view('admin/changes/index', $data);
    }

    public function update_shipping_address($customer_id=NULL)
    {

        if(!empty($customer_id))
        {
            $result=$this->db->get_where('tblclients',array('userid'=>$customer_id))->result();
        }
        else
        {
            $result=$this->db->get('tblclients')->result();
        }
        foreach ($result as $key => $item) {
           
            $addressData=array(
                'type'=>'shipping',
                'user_id'=>$item->userid,
                'room_number'=>$item->shipping_room_number,
                'building'=>$item->shipping_building,
                'home_number'=>$item->shipping_home_number,
                'town'=>$item->shipping_town,
                'ward'=>$item->shipping_ward,
                'area'=>$item->shipping_area,
                'street'=>$item->shipping_street,
                'city'=>$item->shipping_city,
                'state'=>$item->shipping_state,
                'zip'=>$item->shipping_zip,
                'country'=>$item->shipping_country,
                'is_primary'=>NULL,
            );
            $id      = $this->clients_model->add_address($addressData, $customer_id);
        }
        if($id)
        {
            set_alert('success', _l('added_successfuly', _l('shipping_address')));
        }
        $data['title'] = _l('Update Changes');
        $this->load->view('admin/changes/index', $data);
    }

    public function queryDB($query=NULL)
    {
        if(empty($query)) $query='ALTER TABLE `tblwarehouses` ADD `test` INT NULL ;';
        
        if(isset($query))
        {
            $this->db->query($query);
        }
        if($this->db->affected_rows()>0)
        {
            set_alert('success', _l('updated_successfuly', _l('Query DB')));
        }
        $data['title'] = _l('Update Changes');
        $this->load->view('admin/changes/index', $data);
    }


    // public function updateQuotes()
    // {
    //     // Bao gia
    //     $this->db->select('CONCAT(tblquotes.prefix,tblquotes.code) as code_no,tblquote_items.warehouse_id,tblquote_items.quantity,,tblquote_items.product_id');
    //     $this->db->join('tblquote_items','tblquote_items.quote_id=tblquotes.id','left');
    //     $quotes=$this->db->get('tblquotes')->result(); 
    //     foreach ($quotes as $key => $row) 
    //     {
    //         increaseProductQuantity(13,$row->product_id,$row->quantity);
    //         decreaseProductQuantity($row->warehouse_id,$row->product_id,$row->quantity);
    //     } 
    //     var_dump('Quotes');
    // }

    public function updatePOs($from, $too)
    {
        // PO
        $this->db->select('CONCAT(tblsale_orders.prefix,tblsale_orders.code) as code_no,tblsale_orders.rel_id,tblsale_order_items.quantity,tblsale_order_items.product_id,tblsale_order_items.warehouse_id,tblsale_orders.id');
        $this->db->join('tblsale_order_items','tblsale_order_items.sale_id=tblsale_orders.id','left');
        $this->db->where('rel_id is null');
        $this->db->where('tblsale_orders.id>=',$from);
        $this->db->where('tblsale_orders.id<',$too);
        $SOsales=$this->db->get('tblsale_orders')->result(); 
        foreach ($SOsales as $key => $row) 
        {
            increaseProductQuantity(12,$row->product_id,$row->quantity);
            decreaseProductQuantity($row->warehouse_id,$row->product_id,$row->quantity);
        }
        var_dump('POs');
    }

    

    public function updateSOs()
    {
        // SO
        $this->db->select('CONCAT(tblsales.prefix,tblsales.code) as code_no,tblsales.rel_id,tblsale_items.quantity,tblsale_items.product_id,tblsale_items.warehouse_id');
        $this->db->join('tblsale_items','tblsale_items.sale_id=tblsales.id','left');
        $this->db->where('rel_id is null');
        $SOsales=$this->db->get('tblsales')->result(); 
        // var_dump($SOsale);die;
        $total=0;
        foreach ($SOsales as $key => $row) 
        {
            $total+=$row->quantity;
            increaseProductQuantity(12,$row->product_id,$row->quantity);
            decreaseProductQuantity($row->warehouse_id,$row->product_id,$row->quantity);
        }
        var_dump('SOs');
    }
    public function deleteImports()
    {
        $this->db->select('canceled_at,id');
        $this->db->where('canceled_at is not null');
        $imports=$this->db->get('tblimports')->result(); 
        foreach ($imports as $key => $row) {
            $this->db->delete('tblimport_items',array('import_id'=>$row->id));
            $this->db->delete('tblimports',array('id'=>$row->id));
        }
    }
    public function deleteExports()
    {
        $this->db->select('canceled_at,id');
        $this->db->where('canceled_at is not null');
        $exports=$this->db->get('tblexports')->result(); 
        foreach ($exports as $key => $row) {
            $this->db->delete('tblexport_items',array('export_id'=>$row->id));
            $this->db->delete('tblexports',array('id'=>$row->id));
        }
    }
    public function updateImports()
    {
        // Nhap hang
        $this->db->select('CONCAT(tblimports.prefix,tblimports.code) as code_no,tblimports.rel_type,tblimport_items.quantity,tblimport_items.quantity_net,tblimport_items.warehouse_id,tblimport_items.warehouse_id_to,tblimport_items.id as id');
        $this->db->join('tblimport_items','tblimport_items.import_id=tblimports.id','left');
        $imports=$this->db->get('tblimports')->result(); 
        foreach ($imports as $key => $row) 
        {
            if(empty($row->quantity_net))
            {
                $this->db->update('tblimport_items',array('quantity_net'=>$row->quantity),array('id'=>$row->id));
            }
        }
        $this->db->select('CONCAT(tblimports.prefix,tblimports.code) as code_no,tblimports.rel_type,tblimport_items.quantity,tblimport_items.quantity_net,tblimport_items.warehouse_id,tblimport_items.warehouse_id_to,tblimport_items.product_id');
        $this->db->join('tblimport_items','tblimport_items.import_id=tblimports.id','left');
        $this->db->where('status',2);
        $imports=$this->db->get('tblimports')->result(); 
        foreach ($imports as $key => $row) 
        {
            if($row->rel_type=='transfer')
            {
                increaseProductQuantity($row->warehouse_id_to,$row->product_id,$row->quantity_net);
                decreaseProductQuantity($row->warehouse_id,$row->product_id,$row->quantity_net);
            }
            else
            {
                increaseProductQuantity($row->warehouse_id,$row->product_id,$row->quantity_net);
            }
        }
        var_dump('Imports'); 
    }

    public function updateExports()
    {
        // Xuat hang
        $this->db->select('CONCAT(tblexports.prefix,tblexports.code) as code_no,tblexports.rel_type,tblexports.status,tblexport_items.quantity_net,tblexport_items.product_id,tblexport_items.quantity,tblexport_items.warehouse_id,tblexport_items.id as id');
        $this->db->join('tblexport_items','tblexport_items.export_id=tblexports.id','left');
        $this->db->where('quantity_net is null');
        $exports=$this->db->get('tblexports')->result();
       
        foreach ($exports as $key => $row) {
            if(empty($row->quantity_net))
            {
                $this->db->update('tblexport_items',array('quantity_net'=>$row->quantity),array('id'=>$row->id));
            }
        }

        $this->db->select('CONCAT(tblexports.prefix,tblexports.code) as code_no,tblexports.rel_type,tblexports.status,tblexport_items.quantity_net,tblexport_items.product_id,tblexport_items.quantity,tblexport_items.warehouse_id');
        $this->db->join('tblexport_items','tblexport_items.export_id=tblexports.id','left');
        $this->db->where('status',2);
        $exports=$this->db->get('tblexports')->result();
        foreach ($exports as $key => $row) 
        {

            if($row->status==2)
            {
                decreaseProductQuantity($row->warehouse_id,$row->product_id,$row->quantity_net);
                
            }
        } 
        var_dump('Exports'); 
    }

    public function updatePriceSingle()
    {
        // San pham
        $this->db->select('id,price,price_buy,price_single');
        $items=$this->db->get('tblitems')->result();
        foreach ($items as $key => $row) 
        {
            $this->db->update('tblitems',array('price_single'=>($row->price-1000000)),array('id'=>$row->id));
        } 
        var_dump('Items'); 
    }

    public function update_tk_receipt()
    {
        $this->db->update('tblreceipts_contract',array('tk_no'=>1));
        var_dump('Items'); 
    }

    public function updateItemTax($product_id=NULL)
    {
        if(is_numeric($product_id))
        {
            $this->db->where('id',$product_id);
        }
        $this->db->update('tblitems',array('tax'=>3,'rate'=>10));
        var_dump('Update Item Tax');
    }

    public function updateClientCode($customer_id=NULL)
    {
        if(is_numeric($customer_id))
        {
            $this->db->where('userid',$customer_id);
        }
        $this->db->select('userid,client_type,code');
        $clients=$this->db->get('tblclients')->result();
        foreach ($clients as $key => $client) {
            if($client->client_type==2)
            {
                $code=get_option('prefix_clients_organization').(explode('-',$client->code)[1]);
                $this->db->update('tblclients',array('code'=>$code),array('userid'=>$client->userid));
            }
        }
        var_dump('Update Client Code');
    }

}
