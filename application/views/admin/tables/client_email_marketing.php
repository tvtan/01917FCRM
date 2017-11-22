<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$plan_status=array(
    "1"=>"Đã được xác nhận",
    "0"=>"Chưa được xác nhận chọn để xác nhận"
);
$aColumns     = array(
    'tblemail_send.email',
    'time',
    'template',
    'file',
    'addedfrom',
    'date_send',
    'subject'
);

$sIndexColumn = "id";
$sTable       = 'tblemail_send';

$where = array();
$order_by = 'tblemail_send.id ASC';
$order_by = '';
$status=$this->_instance->input->post('filterStatus');
$join             = array(
);
array_push($join, 'LEFT JOIN tbllog_email_send ON tbllog_email_send.id=tblemail_send.id_log');
if($email_client)
{
    array_push($where,' AND tblemail_send.email="'.$email_client.'"');
    $additionalSelect = array();
    $result           = data_tables_init($aColumns, $sIndexColumn, $sTable ,$join, $where, $additionalSelect, $order_by);
    $output           = $result['output'];
    $rResult          = $result['rResult'];
    var_dump($rResult);die;
}
else
{
    $rResult=array();
    // $output=array("draw"=>1,
    //               "iTotalRecords"=>0,
    //               "iTotalDisplayRecords"=>0,
    //               "aaData"=>array()
    // ); 
}


foreach ($rResult as $aRow) {
    $row = array();
    $approval = false;
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if($aColumns[$i] == "time"||$aColumns[$i] == "date_send"){
            $_data = _d($aRow[$aColumns[$i]]);
        }
        if($aColumns[$i] == "addedfrom")
        {
            if($_data != '0') {
                $_data = '<a href="' . admin_url('profile/' . $_data) . '">' . staff_profile_image($_data, array(
                        'staff-profile-image-small mright5'
                    ), 'small', array(
                        'data-toggle' => 'tooltip',
                        'data-title' => $aRow["addedfrom"],
                    )) . '</a>';
            }
            else {
                $_data = "";
            }
        }
        $row[] = $_data;

    }

    $output['aaData'][] = $row;
}
