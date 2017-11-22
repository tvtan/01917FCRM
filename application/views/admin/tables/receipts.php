<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$plan_status=array(
    "1"=>"Đã được xác nhận",
    "0"=>"Chưa được xác nhận chọn để xác nhận"
);

$aColumns     = array(
    'tblreceipts.code_vouchers',
    'tblreceipts.receiver',
    'tblreceipts.id_client',
    '(SELECT GROUP_CONCAT(tblreceipts_contract.sales) FROM tblreceipts_contract WHERE tblreceipts_contract.id_receipts=tblreceipts.id)',
    'tblreceipts.date_create',
    'tblreceipts.reason',
    '6',
    'tblreceipts.staff_browse',
    'tblreceipts.id_staff'
);

$sIndexColumn = "id";
$sTable       = 'tblreceipts';

$where = array();
$order_by = 'tblreceipts.id ASC';
$order_by = '';
$status=$this->_instance->input->post('filterStatus');
if($status!="")
{
    array_push($where,' AND status='.$status);
}
$join             = array(
);
$additionalSelect = array(
    'tblreceipts.id',
    '(select tblstaff.fullname from tblstaff where tblstaff.staffid = tblreceipts.id_user_create) as creator',
    '(select CONCAT("[",tblclients.code,"] - ",tblclients.company) from tblclients where tblclients.userid = tblreceipts.id_client) as company',
);
$result           = data_tables_init($aColumns, $sIndexColumn, $sTable ,$join, $where, $additionalSelect, $order_by);

$output           = $result['output'];
$rResult          = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = array();
    $approval = false;
    for ($i = 0; $i < count($aColumns); $i++) {
        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            $_data = $aRow[strafter($aColumns[$i], 'as ')];
        } else {
            $_data = $aRow[$aColumns[$i]];
        }
        if($aColumns[$i] == "tblreceipts.code_vouchers"){
            $_data = '<a target="_blank" href="'.admin_url('receipts/receipts/').$aRow['id'].'">'.$_data.'</a>';
        }
        if($aColumns[$i] == "tblreceipts.id_client"){
            $_data = '<a target="_blank" href="'.admin_url('clients/client/').$aRow['tblreceipts.id_client'].'">'.$aRow['company'].'</a>';
        }

        if($aColumns[$i] == "(SELECT GROUP_CONCAT(tblreceipts_contract.sales) FROM tblreceipts_contract WHERE tblreceipts_contract.id_receipts=tblreceipts.id)"){
            $references=explode(',',$aRow[$aColumns[$i]]);
            $_data = '';
            foreach ($references as $key => $refer) {
                $refer=explode('-', $refer);
                if(strlen($_data)>0) $_data.='</br>';
                if($refer[1]=='PO')
                {
                    $type='sale_orders';
                    $_data.='<a target="_blank" href='.admin_url($type."/sale_detail/").$refer[0].' >'.getCodePSO($refer[0],'PO').'</a>';
                }
                else
                {
                    $type='sales';
                    $_data.='<a target="_blank" href='.admin_url($type."/sale_detail/").$refer[0].' >'.getCodePSO($refer[0],'SO').'</a>';
                }
                
            }
        }
        
        if($aColumns[$i] == "tblreceipts.date_create"){
            $_data = _d($aRow['tblreceipts.date_create']);
        }
        if($aColumns[$i] == "6"){
           $_total= get_table_where('tblreceipts_contract','id_receipts='.$aRow['id']);
            if($_total!=array())
            {
                $total=0;
                foreach($_total as $r)
                {
                    $total+=$r['subtotal'];
                }
                $_data=_format_number($total);
            }
            else
            {
                $_data="";
            }
        }
        if($aColumns[$i] == 'tblreceipts.status') {
            if ($aRow['tblreceipts.status'] == 0) {
                $type = 'warning';
                $status = 'Chưa duyệt';
            } elseif ($aRow['tblreceipts.status'] == 1) {
                $type = 'info';
                $status = 'Đã xác nhận';
            } else {
                $type = 'success';
                $status = 'Đã duyệt';
            }
            $_data = '<span class="inline-block label label-' . $type . '" task-status-table="' . $aRow['tblreceipts.status'] . '">' . $status . '';
            if (has_permission('invoices', '', 'view') && has_permission('invoices', '', 'view_own')) {
                if ($aRow['tblreceipts.status'] != 2) {
                    $_data .= '<a href="javacript:void(0)" onclick="return var_status(' . $aRow['tblreceipts.status'] . ',' . $aRow['id'] . ')">
                    <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" title="' . _l($plan_status[$aRow['tblreceipts.status']]) . '"></i>
                    ';
                } else {
                    $_data .= '<a href="javacript:void(0)">
                    <i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip" title="' . _l($plan_status[$aRow['tblreceipts.status']]) . '"></i>';
                }
            }
        }
        $array_user = ['tblreceipts.id_staff','tblreceipts.staff_browse'];
        if(in_array($aColumns[$i],$array_user)) {
            if($_data != '0') {
                $_data = '<a href="' . admin_url('profile/' . $_data) . '">' . staff_profile_image($_data, array(
                        'staff-profile-image-small mright5'
                    ), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => $aRow["creator"],
                    )) . '</a>';
            }
            else {
                $_data = "";
            }
        }
        $row[] = $_data;
    }
    $options = '';
    $options.='<div class="dropdown" style="position: absolute;">
                    <a class="dropdown-toggle btn btn-default btn-icon" data-toggle="dropdown"><i class="fa fa-print"></i></a>
                    <ul class="dropdown-menu">
                      <li class="dropdown-header">LIÊN</li>
                      <li><a href="'.admin_url().'receipts/pdf/' . $aRow['id'].'?print=true" target="_blank">Liên 1</a></li>
                      <li><a href="'.admin_url().'receipts/pdf/' . $aRow['id'].'?print=true&combo=2" target="_blank">Liên 2</a></li>
                      
                    </ul>
                 </div>
                ';
    $mleft30='mleft30';
   $options .= icon_btn('receipts/pdf/'. $aRow['id'] .'?print=true' , 'print', 'btn-default ', array('target'=>'_blank'));
    $options .= icon_btn('receipts/delete/'. $aRow['id'] , 'remove', 'btn-danger delete-remind',array(
            'data-toggle'=>'tooltip',
            'title'=>_l('delete'),
            'data-placement'=>'top'
            ));
    $row[] = $options;

    $output['aaData'][] = $row;
}
