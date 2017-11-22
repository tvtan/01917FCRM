<?php
defined('BASEPATH') OR exit('No direct script access allowed');
// SO
$aColumnsSO     = array(
    '1',
    'CONCAT(tblsales.prefix,tblsales.code) as code',  
    'tblsales.date as date',  
    'tblsales.total as total',
    'null',
    '2',
    '3',
    'saler_id'
);
$sIndexColumnSO = "id";
$sTableSO       = 'tblsales';
$whereSO        = array(
    'AND rel_id is null'
);
if($customer_id)
{
    array_push($whereSO, ' AND customer_id='.$customer_id);
}

$joinSO         = array();

$resultSO       = data_tables_init($aColumnsSO, $sIndexColumnSO, $sTableSO, $joinSO, $whereSO, array(
    'id',
    '2 as type'
));
$outputSO       = $resultSO['output'];
$rResultSO      = $resultSO['rResult'];

// PO
$aColumns     = array(
    '1',
    'CONCAT(tblsale_orders.prefix,tblsale_orders.code) as code',  
    'tblsale_orders.date as date',  
    'tblsale_orders.total as total',
    'null',
    '2',
    '3',
    'saler_id'
);
$sIndexColumn = "id";
$sTable       = 'tblsale_orders';
$where        = array(
    
);
if($customer_id)
{
    array_push($where, ' AND customer_id='.$customer_id);
}

$join         = array();

$result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'id',
    '1 as type'
));
$output       = $result['output'];
$rResult      = $result['rResult'];
$rResult=array_merge($rResult,$rResultSO);

$footer_data=array(
        'total'=>0,
        'deposit'=>0,
        'payment'=>0,
        'left'=>0
    );


$aColumnsG=array(
        '1',
        'code',
        'date',
        'total',
        'deposit',
        '2',
        '3',
        'saler_id'
    );
$j=0;
foreach ($rResult as $aRow) {
    $row = array();
    $j++;
    $payment=0;
    for ($i = 0; $i < count($aColumnsG); $i++) {
        $_data = $aRow[$aColumnsG[$i]];
        if ($aColumnsG[$i] == '1') {
            $_data=$j;
        }
        if ($i == 1) {
            if($aRow['type']==1)
                $_data='<a href='.admin_url("sale_orders/sale_detail/").$aRow['id'].' >'.$aRow['code'].'</a>';
            else
                $_data='<a href='.admin_url("sales/sale_detail/").$aRow['id'].' >'.$aRow['code'].'</a>';
            
        }
        if ($aColumnsG[$i] == 'date') {
            $_data=_d($aRow['date']);
        }
        if ($aColumnsG[$i] == 'total') {
            $footer_data['total']+=$aRow['total'];
            $_data=format_money($aRow['total']);
        }
        
        if ($aColumnsG[$i] == '2') {
            if($aRow['type']==1)
            {
                $payment=getTotalMoneyReceiveFromCustomerPO($aRow['id']);
                $_data=format_money($payment);
            }
            else
            {
                $payment=getTotalMoneyReceiveFromCustomer($aRow['id'],'SO');
                $_data=format_money();
            }
            $footer_data['payment']+=$payment;
        }

        if ($aColumnsG[$i] == '3') {
            $_data=format_money($aRow['total']-$payment);
            $footer_data['left']+=$aRow['total']-$payment;
        }
        if ($aColumnsG[$i] == 'deposit') {
            $_data='';
            if($aRow['type']==1)
            {
                $deposit=getDepositPayment($aRow['id']);
                $_data=format_money($deposit);
                $footer_data['deposit']+=$deposit;
            }
            
        }
        if ($aColumnsG[$i] == 'saler_id') {
            $_data=get_staff_full_name($aRow['saler_id']);
        }

        $row[] = $_data;
    }

    $_data='';
    if (is_admin() || has_permission('so','','view') || has_permission('so','','view_own')) 
    {
        if(is_admin() || has_permission('so','','view') || has_permission('so','','view_own'))
        if($aRow['type']==1)
            $control='sale_orders';
        else
            $control='sales';
        $_data .= icon_btn($control.'/pdf/' . $aRow['id'].'?pdf=true', 'print', 'btn-default',array('target' => '_blank','data-toggle'=>'tooltip',
            'title'=>_l('dt_button_print'),
            'data-placement'=>'top'));
        
        if(is_admin() || has_permission('so','','edit'))
        {            
            $_data .= icon_btn($control.'/sale_detail/'. $aRow['id'] , 'eye','btn-default',array(
            'data-toggle'=>'tooltip',
            'title'=>_l('edit'),
            'data-placement'=>'top'
            ));
        }
        
        $row[] =$_data;
    } else {
        $row[] = '';
    }
    $output['aaData'][] = $row;
}
foreach ($footer_data as $key => $total) {
    $footer_data[$key] = _format_number($total);
}
$output['sums'] = $footer_data;
// die;