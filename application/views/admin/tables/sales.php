<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$plan_status=array(
    "2"=>"Đơn đặt hàng",
    "1"=>"Đơn đặt hàng được xác nhận chọn để duyệt đơn đặt hàng",
    "0"=>"Đơn đặt hàng chưa được xác nhận chọn để xác nhận"
);

$aColumns     = array(
    '1',
    'tblsales.code',  
    'rel_code',  
    'company',
    '2',
    'total',
    '4',
    '5',
    'saler_id',
    'tblsales.create_by',
    'status',
    'CONCAT((SELECT fullname FROM tblstaff  WHERE user_head_id=tblstaff.staffid),",",(SELECT fullname FROM tblstaff  WHERE user_admin_id=tblstaff.staffid)) as confirm',
    'date'

);
if($client)
{
    $aColumns     = array(
        'tblsales.code',
        'rel_code',
        '6',
        'total',
        '7',
        '4',
        '5',
        'status',
        'CONCAT((SELECT fullname FROM tblstaff  WHERE user_head_id=tblstaff.staffid),",",(SELECT fullname FROM tblstaff  WHERE user_admin_id=tblstaff.staffid)) as confirm',
        'date'

    );
}
$sIndexColumn = "id";
$sTable       = 'tblsales';
$where        = array(
    
);
if($client)
{
    array_push($where, ' AND customer_id='.$client);
}
if(!empty($order_id))
{
    $where[]='AND rel_id="'.$order_id.'"';
}

if(!has_permission('so','','view')){
    array_push($where,'AND tblsales.create_by='.get_staff_user_id());
}

if($this->_instance->input->post()) {
    $filter_status = $this->_instance->input->post('filterStatus');
    if(is_numeric($filter_status)) {
        
        if($filter_status == 2)
            array_push($where, 'AND status='.$filter_status);
        elseif($filter_status == 5)
            array_push($where, 'AND export_status=2');
        elseif($filter_status == 4)
            array_push($where, 'AND export_status=1');
        elseif($filter_status == 3)
            array_push($where, 'AND export_status=0');
        else {
            array_push($where, 'AND status<>2');
        }
    }
}
$join         = array(
    'LEFT JOIN tblstaff  ON tblstaff.staffid=tblsales.create_by',
    'LEFT JOIN tblclients  ON tblclients.userid=tblsales.customer_id'
);

$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'id',
    'prefix',
    'rel_id',
    'export_status',
    'tblstaff.fullname',
    'CONCAT(user_head_id,",",user_admin_id) as confirm_ids',
    '(SELECT fullname FROM tblstaff WHERE tblsales.create_by=tblstaff.staffid) as creater',
    'customer_id',
    'tblclients.phonenumber as phonenumber',
    'tblclients.mobilephone_number as mobilephone_number',
    'tblclients.code as customer_code',
    'rel_type'
));
$output       = $result['output'];
$rResult      = $result['rResult'];
// return $rResult; die()



$j=0;
foreach ($rResult as $aRow) {
    $row = array();
    $j++;
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == '1') {
            $_data=$j;
        }
        if ($aColumns[$i] == 'tblsales.code') {
            $_data='<a href='.admin_url("sales/sale_detail/").$aRow['id'].' >'.$aRow['prefix'].$aRow['tblsales.code'].'</a>';
            
        }
        if ($aColumns[$i] == 'company') {
            $_data='<a href='.admin_url("clients/client/").$aRow['customer_id'].' >['.$aRow['customer_code'].'] - '.$aRow['company'].'</a>';
            
        }
        if ($aColumns[$i] == 'rel_code') {
            if($aRow['rel_type']=='sale_order')
                $_data='<a href='.admin_url("sale_orders/sale_detail/").$aRow['rel_id'].' >'.$aRow['rel_code'].'</a>';
            if($aRow['rel_type']=='quote')
                $_data='<a href='.admin_url("quotes/quote_detail/").$aRow['rel_id'].' >'.$aRow['rel_code'].'</a>';
        }
        if ($aColumns[$i] == '6') {
            if ($aRow['rel_code']) {
                $_data='<a href='.admin_url("sale_orders/sale_detail/").$aRow['rel_id'].' >'.$aRow['rel_code'].'</a>';
            }else{
                $_data='<a href='.admin_url("sales/sale_detail/").$aRow['id'].' >'.$aRow['prefix'].$aRow['tblsales.code'].'</a>';
            }
            
        }

        if ($aColumns[$i] == 'date') {
            $_data=_d($aRow['date']);
        }
        if ($aColumns[$i] == 'total') {
            $_data=format_money($aRow['total']);
        }
        
        if ($aColumns[$i] == '7') {
            if ($aRow['rel_code']) {
                $_data=format_money(getDepositPayment($aRow['rel_id']));
            }else{
                $_data=0;
            }
            
        }

        if ($aColumns[$i] == '2') {
            $_data="";
            $number=explode(',',$aRow['mobilephone_number']);
            $number[]=$aRow['phonenumber'];
            foreach ($number as $value) {
                $_data.='<span class="label label-warning mleft5 inline-block">'.$value.'</span>';
            }
        }
        // if ($aColumns[$i] == '3') {
        //     $_data=format_money(checkDeposit($aRow['id'],'SO')->subtotal);
        // }
        if ($aColumns[$i] == '4') {
            $_data=format_money(getTotalMoneyReceiveFromCustomer($aRow['id'],'SO'));
        }

        if ($aColumns[$i] == '5') {
            $_data=format_money($aRow['total']-getTotalMoneyReceiveFromCustomer($aRow['id'],'SO'));
        }
        if ($aColumns[$i] == 'saler_id') {
            $_data=get_staff_full_name($aRow['saler_id']);
        }

        if ($aColumns[$i] == 'status') {
            $_data='<span class="inline-block label label-'.get_status_label($aRow['status']).'" task-status-table="'.$aRow['status'].'">' . format_status_sale($aRow['status'],false,true).'';
            if(has_permission('invoices', '', 'view') && has_permission('invoices', '', 'view_own'))
            {
                if($aRow['status']!=2){
                    $_data.='<a href="javacript:void(0)" onclick="var_status('.$aRow['status'].','.$aRow['id'].')">';
                }
                else
                {
                    $_data.='<a href="javacript:void(0)">';
                }
            }
            else {
                if($aRow['status']==0) {
                    $_data .= '<a href="javacript:void(0)" onclick="var_status(' . $aRow['status'] . ',' . $aRow['id'] . ')">';
                }
                else
                {
                    $_data .= '<a href="javacript:void(0)">';
                }
            }
                $_data.='<i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip" title="' . _l( $plan_status[$aRow['status']]) . '"></i>
                    </a>
                </span>';
        }
        if ($aColumns[$i] == 'tblsales.create_by') {
            $_data='<a href='.admin_url("profile/").$aRow['tblsales.create_by'].' >'.staff_profile_image($aRow['tblsales.create_by'], array(
                        'staff-profile-image-small mright5'
                    ), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => $aRow['creater']
                    )).'</a>';
        }
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow['confirm'];
            $confirms=array_unique(explode(',', $_data));
            $confirm_ids=array_unique(explode(',', $aRow['confirm_ids']));
            $_data            = '';
            $result = '';
            $as = 0;
            for ($x=0; $x < count($confirms); $x++) { 
                if($confirms[$x]!='')
                {
                    $_data .= '<a href="' . admin_url('profile/' . $confirm_ids[$x]) . '">' . staff_profile_image($confirm_ids[$x], array(
                        'staff-profile-image-small mright5'
                    ), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => $confirms[$x]
                    )) . '</a>';
                }
            }
        }
        $row[] = $_data;
    }
    $_data='';
    if ($aRow['tblsales.create_by'] == get_staff_user_id() || is_admin() || has_permission('so','','view') || has_permission('so','','view_own') || has_permission('so','','create') || has_permission('so','','edit') || has_permission('so','','delete')) 
    {

        $_data .= icon_btn('sales/pdf/' . $aRow['id'].'?pdf=true', 'print', 'btn-default',array('target' => '_blank','data-toggle'=>'tooltip',
            'title'=>_l('dt_button_print'),
            'data-placement'=>'top'));
        // Bang ke thong so ky thuat
        // if($aRow['tblsale_orders.create_by'] == get_staff_user_id() || is_admin() || has_permission('po','','view') || has_permission('po','','view_own'))
        $_data .= icon_btn('sales/pdfSpecifications/' . $aRow['id'].'?pdf=true', 'pinterest', 'btn-default',array('target' => '_blank','data-toggle'=>'tooltip',
            'title'=>_l('dt_button_print_specifications'),
            'data-placement'=>'top'));
        if (has_permission('report_have','','create') || has_permission('receipts','','create')) 
        {
            if(isCompletedPaymentPO($aRow['rel_id'])!==true)
            {
                if(isCompletedPaymentSO($aRow['id'])!==true)
                {
                    // Thanh toan hang hoa
                    $isDeposit=true;
                    $type='plus';
                    $title=_l('new_receipt');
                    $_data .= icon_btn('#' , $type,'btn-info',array(
                        'onclick'=>"receiptSO(".$aRow['customer_id'].",".$aRow['id'].",'PO',".$isDeposit."); return false;",
                        'data-toggle'=>'tooltip',
                        'title'=>$title,
                        'data-placement'=>'top'
                        ));
                }
            }
        }
        
        if(has_permission('export_items','','create'))
        {
            if($aRow['status']==2)
            {           
                if($aRow['export_status']!=2) 
                {
                    //Tao Phieu xuat kho
                    $_data .= icon_btn('exports/sale_output/'. $aRow['id'] , 'file-o','btn-default',array(
                'data-toggle'=>'tooltip',
                'title'=>_l('create_export'),
                'data-placement'=>'top'
                ));
                }
            }
        }
        
        if(has_permission('export_items','','view') || has_permission('export_items','','view_own'))
        {
            // list SO export
            if($aRow['export_status']!=0)
            {
            $_data .= icon_btn('exports/'. $aRow['id'] , 'list','btn-default',array(
                'data-toggle'=>'tooltip',
                'title'=>_l('export_list'),
                'data-placement'=>'top'
                ));
            }            
        }
        
        

        if(has_permission('so','','edit'))
        {            
            $_data .= icon_btn('sales/sale_detail/'. $aRow['id'] , 'edit','btn-default',array(
            'data-toggle'=>'tooltip',
            'title'=>_l('edit'),
            'data-placement'=>'top'
            ));
        }
        if(has_permission('so','','delete'))   
        {   
        $_data.=icon_btn('sales/delete/'. $aRow['id'] , 'remove', 'btn-danger delete-remind',array(
            'data-toggle'=>'tooltip',
            'title'=>_l('delete'),
            'data-placement'=>'top'
            ));
        }
        $row[] =$_data;
    } else {
        $row[] = '';
    }
    $output['aaData'][] = $row;
}
// die;