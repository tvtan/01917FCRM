<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$plan_status=array(
    "2"=>"Phiếu điều chỉnh kho",
    "1"=>"Phiếu điều chỉnh kho được xác nhận chọn để duyệt",
    "0"=>"Phiếu điều chỉnh kho chưa được xác nhận chọn để xác nhận"
);


$aColumns     = array(
    '1',
    'code',
    'name',
    '(SELECT fullname FROM tblstaff WHERE create_by=tblstaff.staffid)',
    'status',
    'CONCAT((SELECT fullname FROM tblstaff  WHERE user_head_id=tblstaff.staffid),",",(SELECT fullname FROM tblstaff  WHERE user_admin_id=tblstaff.staffid)) as confirm',
    'date'

);
$sIndexColumn = "id";
$sTable       = 'tblimports';
$where        = array(
    'AND rel_type="'.$rel_type.'"','AND canceled_at is null'
);
if($this->_instance->input->post()) {
    $filter_status = $this->_instance->input->post('filterStatus');
    if(is_numeric($filter_status)) {
        
        if($filter_status == 2)
        {
            $where = array('AND rel_type="'.$rel_type.'"','AND canceled_at is null');
            array_push($where, 'AND status='.$filter_status);
        }
        elseif($filter_status == 3)
            {
                $where = array('AND rel_type="'.$rel_type.'"');
                array_push($where, 'AND canceled_at is not null');
            }
        else {
            array_push($where, 'AND status<>2');
        }
    }
}
$join         = array(
    'LEFT JOIN tblstaff  ON tblstaff.staffid=tblimports.create_by'
);
$result       = data_tables_init($aColumns, $sIndexColumn, $sTable,$join, $where, array(
    'id',
    'prefix',
    'canceled_at',
    'tblstaff.fullname',
    'CONCAT(user_head_id,",",user_admin_id) as confirm_ids'
));
$output       = $result['output'];
$rResult      = $result['rResult'];
//var_dump($rResult);die();
// print_r($output);die();



$j=0;
foreach ($rResult as $aRow) {
    $row = array();
    $j++;
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == '1') {
            $_data=$j;
        }
        if ($aColumns[$i] == 'code') {
            $_data=$aRow['prefix'].$aRow['code'];
        }
        if ($aColumns[$i] == 'date') {
            $_data=_d($aRow['date']);
        }
        if ($aColumns[$i] == 'status') {
            $_data='<span class="inline-block label label-'.get_status_label($aRow['status'],$rel_type).'" task-status-table="'.$aRow['status'].'">' . format_status_adjustment($aRow['status'],false,true).'';
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
                $tooltip_status=$plan_status[$aRow['status']];
                if($rel_type=='internal')
                {
                    $replace='Phiếu nhập kho';
                    $tooltip_status=str_replace('Phiếu điều chỉnh', $replace, $tooltip_status);
                }
                elseif($rel_type=='return')
                {
                    $replace='Phiếu nhập kho';
                    $tooltip_status=str_replace('Phiếu điều chỉnh', $replace, $tooltip_status);
                }
                elseif($rel_type=='contract')
                {
                    $replace='Phiếu nhập kho';
                    $tooltip_status=str_replace('Phiếu điều chỉnh', $replace, $tooltip_status);
                }
                $_data.='<i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip" title="' . $tooltip_status . '"></i>
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
    $_data .= icon_btn('imports/detail_pdf/' . $aRow['id'].'?pdf=true', 'print', 'btn-default',array('target' => '_blank'));

    if ($aRow['create_by'] == get_staff_user_id() || is_admin() || has_permission('import_items', '', 'edit')) {
        
        if($aRow['status']!=2)
        {
            $_data .= icon_btn('imports/'.$rel_type.'_detail/'. $aRow['id'] , 'edit');
        }
        else
        {
            $_data .= icon_btn('imports/'.$rel_type.'_detail/'. $aRow['id'] , 'eye');
        }
        if($aRow['canceled_at'])
        {
            if(is_admin()) {
                $_data .=icon_btn('imports/restore_import/'. $aRow['id'] , 'refresh', 'btn-info restore-remind');
            }
            
        }
        else 
        {
            if(has_permission('import_items', '', 'delete')) {
                $_data.=icon_btn('imports/delete_import/'. $aRow['id'] , 'remove', 'btn-danger delete-remind');
            }
        }   
         $row[] =$_data;
    } else {
        $row[] = '';
    }
    $output['aaData'][] = $row;
}

