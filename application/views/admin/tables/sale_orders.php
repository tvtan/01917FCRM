<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$plan_status=array(
    "2"=>"Đơn đặt hàng",
    "1"=>"Đơn đặt hàng được xác nhận chọn để duyệt đơn đặt hàng",
    "0"=>"Đơn đặt hàng chưa được xác nhận chọn để xác nhận"
);

$aColumns     = array(
    '1',
    'tblsale_orders.code',
    'rel_code',
    'company',
    '2',
    'total',
    '3',
    '4',
    '5',
    'saler_id',
    '(SELECT fullname FROM tblstaff WHERE tblsale_orders.create_by=tblstaff.staffid)',
    'status',
    'CONCAT((SELECT fullname FROM tblstaff  WHERE user_head_id=tblstaff.staffid),",",(SELECT fullname FROM tblstaff  WHERE user_admin_id=tblstaff.staffid)) as confirm',
    'date'

);
$sIndexColumn = "id";
$sTable       = 'tblsale_orders';
$where        = array(
    // 'AND rel_type="'.$rel_type.'"',
);
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
if(!has_permission('po','','view')){
    array_push($where,'AND tblsale_orders.create_by='.get_staff_user_id());
}
$join         = array(
    'LEFT JOIN tblstaff  ON tblstaff.staffid=tblsale_orders.create_by',
    'LEFT JOIN tblclients  ON tblclients.userid=tblsale_orders.customer_id',
    // 'LEFT JOIN tblsales  ON tblsales.rel_id=tblsale_orders.id'
);
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable,$join, $where, array(
    'id',
    'prefix',
    'export_status',
    'rel_id',
    'tblstaff.fullname',
    'CONCAT(user_head_id,",",user_admin_id) as confirm_ids',
    'customer_id',
    'tblclients.phonenumber as phonenumber',
    'tblclients.mobilephone_number as mobilephone_number',
    'tblclients.code as customer_code',
    'rel_type'
));
$output       = $result['output'];
$rResult      = $result['rResult'];
//var_dump($rResult);die();


$j=0;
foreach ($rResult as $aRow) {
    $row = array();
    $j++;
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == '1') {
            $_data=$j;
        }
        if ($aColumns[$i] == 'tblsale_orders.code') {
            $code=$aRow['prefix'].$aRow['tblsale_orders.code'];
            $_data=$_data='<a href="'.admin_url('sale_orders/sale_detail/'.$aRow['id']).'">'. $code . '</a>';
        }
        if ($aColumns[$i] == 'date') {
            $_data=_d($aRow['date']);
        }
        if ($aColumns[$i] == 'total') {
            $_data=format_money($aRow['total']);
        }
        if ($aColumns[$i] == 'company') {
            $_data='<a href='.admin_url("clients/client/").$aRow['customer_id'].' >['.$aRow['customer_code'].'] - '.$aRow['company'].'</a>';
        }
        if ($aColumns[$i] == '2') {
            $_data="";
            $number=explode(',',$aRow['mobilephone_number']);
            $number[]=$aRow['phonenumber'];
            foreach ($number as $value) {
                $_data.='<span class="label label-warning mleft5 inline-block">'.$value.'</span>';
            }
        }
        if ($aColumns[$i] == '3') {
            $_data=format_money(getDepositPayment($aRow['id']));
        }
        if ($aColumns[$i] == '4') {
            $_data=format_money(getTotalMoneyReceiveFromCustomerPO($aRow['id']));
        }
        if ($aColumns[$i] == '5') {
            $_data=format_money($aRow['total']-getTotalMoneyReceiveFromCustomerPO($aRow['id']));
        }
        if ($aColumns[$i] == 'saler_id') {
            $_data=get_staff_full_name($aRow['saler_id']);
        }
        if ($aColumns[$i] == 'rel_code') {
            if($aRow['rel_type']=='contract')
                $_data='<a href="'.admin_url('contracts/contract/'.$aRow['rel_id']).'">'. $aRow['rel_code'] . '</a>';
            if($aRow['rel_type']=='quote')
                $_data='<a href="'.admin_url('quotes/quote_detail/'.$aRow['rel_id']).'">'. $aRow['rel_code'] . '</a>';
        }
        if ($aColumns[$i] == 'status') {
            $_data='<span class="inline-block label label-'.get_status_label($aRow['status']).'" task-status-table="'.$aRow['status'].'">' . format_status_sale($aRow['status'],false,true).'';
            if(has_permission('invoices', '', 'view') && has_permission('invoices', '', 'view_own'))
            {
                if($aRow['status']!=2){
                    $_data.='<a href="javacript:void(0)" onclick="var_status_order('.$aRow['status'].','.$aRow['id'].')">';
                }
                else
                {
                    $_data.='<a href="javacript:void(0)">';
                }
            }
            else {
                if($aRow['status']==0) {
                    $_data .= '<a href="javacript:void(0)" onclick="var_status_order(' . $aRow['status'] . ',' . $aRow['id'] . ')">';
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
    if ($aRow['tblsale_orders.create_by'] == get_staff_user_id() || is_admin() || has_permission('po','','view') || has_permission('po','','view_own') || has_permission('po','','create') || has_permission('po','','edit') || has_permission('po','','delete')) 
    {
        if($aRow['tblsale_orders.create_by'] == get_staff_user_id() || is_admin() || has_permission('po','','view') || has_permission('po','','view_own'))
        $_data .= icon_btn('sale_orders/pdf/' . $aRow['id'].'?pdf=true', 'print', 'btn-default',array('target' => '_blank','data-toggle'=>'tooltip',
            'title'=>_l('dt_button_print'),
            'data-placement'=>'top'));
    // Bang ke thong so ky thuat
        // if($aRow['tblsale_orders.create_by'] == get_staff_user_id() || is_admin() || has_permission('po','','view') || has_permission('po','','view_own'))
        $_data .= icon_btn('sale_orders/pdfSpecifications/' . $aRow['id'].'?pdf=true', 'pinterest', 'btn-default',array('target' => '_blank','data-toggle'=>'tooltip',
            'title'=>_l('dt_button_print_specifications'),
            'data-placement'=>'top'));

        if($aRow['status']==2 && $aRow['export_status']!=2 && has_permission('so','','create'))
        {           
            //Tao Phieu xuat kho
            $_data .= icon_btn('sale_orders/sale_output/'. $aRow['id'] , 'exchange','btn-default',array(
            'data-toggle'=>'tooltip',
            'title'=>_l('create_sale'),
            'data-placement'=>'top'
            ));           
            
        }
        if(has_permission('report_have','','create') || has_permission('receipts','','create'))
        {
            if(lockDeposit($aRow['id'])==false)
            {
            // Thanh toan coc
            $isDeposit=true;
            $type='plus';
            $title=_l('new_receipt_deposit');
            $_data .= icon_btn('#' , $type,'btn-info',array(
                'onclick'=>"receipt(".$aRow['customer_id'].",".$aRow['id'].",'PO',".$isDeposit."); return false;",
                'data-toggle'=>'tooltip',
                'title'=>$title,
                'data-placement'=>'top'
                ));
            }
        }

        if(has_permission('so','','view') || has_permission('so','','view_own'))
        {
            // list SO export
            if($aRow['export_status']!=0)
            {
            $_data .= icon_btn('sales/'. $aRow['id'] , 'list','btn-default',array(
                'data-toggle'=>'tooltip',
                'title'=>_l('sale_list'),
                'data-placement'=>'top'
                ));
            }
        }
        
        if(has_permission('po','','edit'))
        {            
            $_data .= icon_btn('sale_orders/sale_detail/'. $aRow['id'] , 'edit','btn-default',array(
            'data-toggle'=>'tooltip',
            'title'=>_l('edit'),
            'data-placement'=>'top'
            ));
        }
        if(has_permission('po','','delete'))
        {    
         $_data.=icon_btn('sale_orders/delete/'. $aRow['id'] , 'remove', 'btn-danger delete-remind',array(
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

