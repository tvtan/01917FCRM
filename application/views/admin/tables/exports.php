<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$plan_status=array(
    "2"=>"Phiếu xuất kho",
    "1"=>"Phiếu xuất kho được xác nhận chọn để duyệt đơn đặt hàng",
    "0"=>"Phiếu xuất kho chưa được xác nhận chọn để xác nhận"
);

$aColumns     = array(
    '1',
    'tblexports.code',
    'rel_code',
    'company',
    'total',
    '(SELECT fullname FROM tblstaff WHERE tblexports.create_by=tblstaff.staffid)',
    'status',
    // 'delivery_status',
    'CONCAT((SELECT fullname FROM tblstaff  WHERE user_head_id=tblstaff.staffid),",",(SELECT fullname FROM tblstaff  WHERE user_admin_id=tblstaff.staffid)) as confirm',
    'date'
);
$sIndexColumn = "id";
$sTable       = 'tblexports';
$where        = array(
    'AND canceled_at is null',
);

//fillter
if($this->_instance->input->post()) {
    $filter_status = $this->_instance->input->post('filterStatus');
    if(is_numeric($filter_status)) {
        
        if($filter_status == 2)
            array_push($where, 'AND status='.$filter_status);
        elseif($filter_status == 3)
            array_push($where, 'AND delivery_code is null');
        elseif($filter_status == 4)
            array_push($where, 'AND delivery_code<>"'.NULL.'"');
        elseif($filter_status == 5)
        {
            $where=array();
            array_push($where, 'AND canceled_at<>"'.NULL.'"');
        }            
        else {
            array_push($where, 'AND status<>2');
        }
    }
}

if(!empty($sale_id))
{
    $where[]='AND rel_id="'.$sale_id.'"';
}
$join         = array(
    'LEFT JOIN tblstaff  ON tblstaff.staffid=tblexports.create_by',
    'LEFT JOIN tblclients  ON tblclients.userid=tblexports.customer_id'
);
// print_r($where);
// exit();
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable,$join, $where, array(
    'id',
    'rel_id',
    'prefix',
    'delivery_code',
    'tblstaff.fullname',
    'canceled_at',
    'CONCAT(user_head_id,",",user_admin_id) as confirm_ids'
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
        if ($aColumns[$i] == 'rel_code') {
            $_data='<a href="'.admin_url('sales/sale_detail/'.$aRow['rel_id']).'">'.$aRow['rel_code'].'</a>';
        }
        if ($aColumns[$i] == 'tblexports.code') {
            $_data='<a href="'.admin_url('exports/export_detail/'.$aRow['id']).'">'.$aRow['prefix'].$aRow['tblexports.code'].'</a>';
        }
        if ($aColumns[$i] == 'date') {
            $_data=_d($aRow['date']);
        }
        if ($aColumns[$i] == 'total') {
            $_data=format_money($aRow['total']);
        }

        if ($aColumns[$i] == 'status') {
            $_data='<span class="inline-block label label-'.get_status_label($aRow['status']).'" task-status-table="'.$aRow['status'].'">' . format_status_export($aRow['status'],false,true).'';
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
        if ($aColumns[$i] == 'delivery_status'){
            $_data='<span class="inline-block label label-success" task-status-table="2">Đã duyệt<a href="javacript:void(0)">
                    <i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip" title="Đề xuất mua"></i>
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
    if ($aRow['tblexports.create_by'] == get_staff_user_id() || is_admin()) {
        $_data .= icon_btn('exports/pdf/' . $aRow['id'].'?pdf=true', 'print', 'btn-default',array('target' => '_blank','data-toggle'=>'tooltip',
            'title'=>_l('print_export'),
            'data-placement'=>'top'));
        // if(isset($aRow['delivery_code']))
        // {            
        //     $_data .= icon_btn('exports/pdf/' . $aRow['id'].'?pdf=true&type=delivery', 'print', 'btn-default',array('target' => '_blank','data-toggle'=>'tooltip',
        //     'title'=>_l('print_delivery'),
        //     'data-placement'=>'top'));
        // } 

        if($aRow['status']!=2)
        {            
            $_data .= icon_btn('exports/export_detail/'. $aRow['id'] , 'edit', 'btn-default',array('data-toggle'=>'tooltip',
            'title'=>_l('edit'),
            'data-placement'=>'top'));
        }  
        else
        {   $type='file-o';
            $view=_l('view_delivery');
            if(empty($aRow['delivery_code']))     
            {
                $type='plus-square-o';
                $view=_l('create_delivery');
            }
            //Tao Phieu Giao hang
            $_data .= icon_btn('exports/sale_delivery/'. $aRow['id'] , $type, 'btn-default',array('data-toggle'=>'tooltip',
            'title'=>$view,
            'data-placement'=>'top'));
            $_data .= icon_btn('exports/export_detail/'. $aRow['id'] , 'eye', 'btn-default',array('data-toggle'=>'tooltip',
            'title'=>_l('view'),
            'data-placement'=>'top'));
        }
        if(empty($aRow['canceled_at']))
        {
            $_data.=icon_btn('exports/delete/'. $aRow['id'] , 'remove', 'btn-danger delete-remind',array('data-toggle'=>'tooltip',
            'title'=>_l('cancel'),
            'data-placement'=>'top'));
        }
        else
        {
            $_data.=icon_btn('exports/restore/'. $aRow['id'] , 'refresh', 'btn-info restore-remind',array('data-toggle'=>'tooltip',
            'title'=>_l('restore'),
            'data-placement'=>'top'));
        }      
        

        $row[] =$_data;
    } else {
        $row[] = '';
    }

    // if(!empty($aRow['rel_id'])){
    //     if(total_rows('tblsales',array('id'=>$aRow['rel_id']))==0){
    //         $row['DT_RowClass'] = 'alert-danger';
    //     }
    // }

    $output['aaData'][] = $row;
}

