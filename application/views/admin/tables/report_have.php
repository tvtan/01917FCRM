<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$plan_status=array(
    "1"=>"Đã được xác nhận",
    "0"=>"Chưa được xác nhận chọn để xác nhận"
);

$aColumns     = array(
    'tblreport_have.code_vouchers',
    'tblreport_have.receiver',
    '1',
    '(SELECT GROUP_CONCAT(tblreport_have_contract.contract) FROM tblreport_have_contract WHERE tblreport_have_contract.id_report_have=tblreport_have.id)',
    'tblreport_have.date_create',
    'tblreport_have.status',
    'tblreport_have.reason',
    'tblreport_have.staff_browse',
    'tblreport_have.id_staff'
);

$sIndexColumn = "id";
$sTable       = 'tblreport_have';

$where = array();
$order_by = 'tblreport_have.id ASC';
$order_by = '';
$status=$this->_instance->input->post('filterStatus');
if($status!="")
{
    array_push($where,' AND status='.$status);
}
$join             = array(
);
$additionalSelect = array(
    'tblreport_have.id',
    '(select tblstaff.fullname from tblstaff where tblstaff.staffid = tblreport_have.id_user_create) as creator',
);
$result           = data_tables_init($aColumns, $sIndexColumn, $sTable ,$join, $where, $additionalSelect, $order_by);
//var_dump($result);die();
$output           = $result['output'];
$rResult          = $result['rResult'];
foreach ($rResult as $aRow) {
    $row = array();
    $approval = false;
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if($aColumns[$i] == "tblreport_have.code_vouchers"){
            $_data = '<a href="'.admin_url('report_have/report_have/').$aRow['id'].'">'.$_data.'</a>';
        }
        if($aColumns[$i]=='tblreport_have.date_create')
        {
            $_data=_d($aRow[$aColumns[$i]]);
        }
        if($aColumns[$i] == "(SELECT GROUP_CONCAT(tblreport_have_contract.contract) FROM tblreport_have_contract WHERE tblreport_have_contract.id_report_have=tblreport_have.id)"){
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
        if($aColumns[$i]=='1')
        {
            $references=explode(',',$aRow['(SELECT GROUP_CONCAT(tblreport_have_contract.contract) FROM tblreport_have_contract WHERE tblreport_have_contract.id_report_have=tblreport_have.id)']);
            $_data = '';
            foreach ($references as $key => $refer) {
                $refer=explode('-', $refer);
                if(strlen($_data)>0) $_data.='</br>';
                if($refer[1]=='PO')
                {
                    $sale=getCodePSO($refer[0],'PO',true);
                    
                }
                else
                {
                    $sale=getCodePSO($refer[0],'SO',true);
                }
                $client=getClient($sale->customer_id);
                $_data.='<a target="_blank" href='.admin_url("clients/client/").$sale->customer_id.' >['.$client->code.'] - '.$client->company.'</a>';
            }
        }
        if($aColumns[$i] == 'tblreport_have.status') {
            if ($aRow['tblreport_have.status'] == 0) {
                $type = 'warning';
                $status = 'Chưa duyệt';
            } elseif ($aRow['tblreport_have.status'] == 1) {
                $type = 'info';
                $status = 'Đã xác nhận';
            } else {
                $type = 'success';
                $status = 'Đã duyệt';
            }
            $_data = '<span class="inline-block label label-' . $type . '" task-status-table="' . $aRow['tblreport_have.status'] . '">' . $status . '';
            if (has_permission('invoices', '', 'view') && has_permission('invoices', '', 'view_own')) {
                if ($aRow['tblreport_have.status'] != 2) {
                    $_data .= '<a href="javacript:void(0)" onclick="return var_status(' . $aRow['tblreport_have.status'] . ',' . $aRow['id'] . ')">
                    <i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" title="' . _l($plan_status[$aRow['tblreport_have.status']]) . '"></i>
                    ';
                } else {
                    $_data .= '<a href="javacript:void(0)">
                    <i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip" title="' . _l($plan_status[$aRow['tblreport_have.status']]) . '"></i>';
                }
            }
        }
        $array_user = ['tblreport_have.id_staff','tblreport_have.staff_browse'];
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
                      <li><a href="'.admin_url().'report_have/pdf/' . $aRow['id'].'?print=true" target="_blank">Liên 1</a></li>
                      <li><a href="#" target="_blank">Liên 2</a></li>
                      <li><a href="#" target="_blank">Liên 3</a></li>
                    </ul>
                 </div>
                ';
    $mleft30='mleft30';

    $options .= icon_btn('report_have/pdf/'. $aRow['id'] .'?print=true' , 'print', 'btn-default ', array('target'=>'_blank'));
    $options .= icon_btn('report_have/delete/'. $aRow['id'] , 'remove', 'btn-danger delete-remind',array(
            'data-toggle'=>'tooltip',
            'title'=>_l('delete'),
            'data-placement'=>'top'
            ));

    $row[] = $options;

    $output['aaData'][] = $row;
}
