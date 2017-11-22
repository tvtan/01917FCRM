<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns = array('1', '2');
$sIndexColumn = "id";
$sTable = 'tbladdress';
$join = array();

$custom_fields = get_custom_fields('contacts',array('show_on_table'=>1));

$i = 0;

$where = array();
$where = array('WHERE user_id='.$client_id);

// Fix for big queries. Some hosting have max_join_limit
if(count($custom_fields) > 4){
    @$this->_instance->db->query('SET SQL_BIG_SELECTS=1');
}

$result = data_tables_init($aColumns,$sIndexColumn,$sTable,$join,$where,array('tbladdress.id as id','user_id','is_primary'));

$output = $result['output'];
$rResult = $result['rResult'];
$total_client_contacts = total_rows('tbladdress',array('user_id'=>$client_id));
foreach ( $rResult as $key =>$aRow )
{

    $row = array();
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
        if(strpos($aColumns[$i],'as') !== false && !isset($aRow[ $aColumns[$i] ])){
            $_data = $aRow[ strafter($aColumns[$i],'as ')];
        } else {
            $_data = $aRow[ $aColumns[$i] ];
        }
        if($aColumns[$i]=='1')
        {
            $_data=$key+1;
        }
        if($aColumns[$i]=='2')
        {
            $_data=getClientAddress2($client_id,$aRow['id']);
        }
        


        $row[] = $_data;
    }
    $options = '';
    $options .= icon_btn('#','pencil-square-o','btn-default',array('onclick'=>'address('.$aRow['user_id'].','.$aRow['id'].');return false;'));
    if(has_permission('customers','','delete') || is_customer_admin($aRow['user_id']))
    {
         if($aRow['is_primary'] == 0 || ($aRow['is_primary'] == 1 && $total_client_contacts == 1)){
            $options .= icon_btn('clients/delete_address/'.$aRow['user_id'].'/'.$aRow['id'],'remove','btn-danger _delete');
        }
    }

    $row[] = $options;
    $output['aaData'][] = $row;
}
