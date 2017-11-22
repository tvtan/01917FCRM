<?php
$dimensions = $pdf->getPageDimensions();

function mb_ucfirst($string, $encoding)
{
    return mb_convert_case($string, MB_CASE_TITLE, $encoding);
}
// $pdf->WatermarkText();
// Tag - used in BULK pdf exporter
if ($tag != '') {
    $pdf->SetFillColor(240, 240, 240);
    $pdf->SetDrawColor(245, 245, 245);
    $pdf->SetXY(0, 0);
    $pdf->SetFont($font_name, 'B', 15);
    $pdf->SetTextColor(0);
    $pdf->SetLineWidth(0.75);
    $pdf->StartTransform();
    $pdf->Rotate(-35, 109, 235);
    $pdf->Cell(100, 1, mb_strtoupper($tag, 'UTF-8'), 'TB', 0, 'C', '1');
    $pdf->StopTransform();
    $pdf->SetFont($font_name, '', $font_size-1);
    $pdf->setX(10);
    $pdf->setY(10);
}

$pdf_text_color_array = hex2rgb(get_option('pdf_text_color'));
if (is_array($pdf_text_color_array) && count($pdf_text_color_array) == 3) {
    $pdf->SetTextColor($pdf_text_color_array[0], $pdf_text_color_array[1], $pdf_text_color_array[2]);
}

$info_right_column = '';
$info_left_column  = '';

$items=$invoice->items;

$tk_no = "";
$tk_co="";
$accountNo=array();
$accountCo=array();
$warehouse_id=false;
foreach ($items as $item) 
{
    $warehouse_id=$item->warehouse_id;
    $warehouse_id_to=$item->warehouse_id_to;
    $accountNo[]=$item->tk_no;
    $accountCo[]=$item->tk_co;
}

$accountNo=array_unique($accountNo);
$accountCo=array_unique($accountCo);

foreach ($accountNo as $key => $account) {
    if(empty($tk_no))
    {  $tk_no .= '<br />' . get_code_tk($account)." ".format_money(get_value_tk_no(' tblimport_items','import_id',$invoice->id,$account));
    }
    else
    {
        $tk_no .= '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . get_code_tk($account)." ".format_money(get_value_tk_no(' tblimport_items','import_id',$invoice->id,$account));
    }
}

foreach ($accountCo as $key => $account) {
    if(empty($tk_co)){
    $tk_co .= '<br />' . get_code_tk($account)." ".format_money(get_value_tk_co(' tblimport_items','import_id',$invoice->id,$account));
    }
    else
    {
        $tk_co .= '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . get_code_tk($account)." ".format_money(get_value_tk_co(' tblimport_items','import_id',$invoice->id,$account));
    }
}
// var_dump(has_permission());
    // foreach ($items as $rom) {
    //     $tk_no .= '<br />' . get_code_tk($rom->tk_no);
    //     $tk_co = $tk_co . '<br />' . get_code_tk($rom->tk_co);
    //     $total += $rom->total;
    // }
    $tk_no = "Nợ: " . trim($tk_no, '<br />');
    $tk_co = "Có: " . trim($tk_co, '<br />');

    $mau = '<b align="center">Mẩu số 01-VT</b><br />
        <i style="font-weight: 100;font-size: 12px;">(Ban hành theo QĐ số 436/2016/QĐ-BTC<br /> Ngày 14/09/2016 của BTC)</i>
    ';
    $info_right = '
    <table style="float: right" >
        <tr>
            <td style="width: 60%" align="right"></td>
            <td style="width: 40%" align="left">' . $mau . '</td>
        </tr>
         <tr>
            <td style="width: 70%" align="right"></td>
            <td style="width: 30%" align="left">' . $tk_no . '</td>
        </tr>
        <tr>
            <td style="width: 70%" align="right"></td>
            <td style="width: 30%" align="left">' . $tk_co . '</td>
        </tr>
    </table>';


    
// $info_right_column=$info_right_column .= '<a href="' . admin_url('#') . '" style="color:#4e4e4e;text-decoration:none;"><b> ' . date('Y-m-d H:i:s') . '</b></a>';

    $invoice_info = '';
    $invoice_info = '<b>' . get_option('invoice_company_name') . '</b><br />';
    $invoice_info .= _l('address') . ': ' . get_option('invoice_company_address') . '<br/>';
    // if (get_option('invoice_company_city') != '') {
    //     $invoice_info .= get_option('invoice_company_city') . ', ';
    // }
    if (get_option('company_vat') != '') {
        $invoice_info .= _l('vat_no') . ': ' . get_option('company_vat') . '<br/>';
    }
    $invoice_info .= get_option('invoice_company_country_code') . ' ';
    $invoice_info .= get_option('invoice_company_postal_code') . ' ';
    $invoice_info .= _l('company_bank_account') . get_option('company_contract_blank_account') . '<br />';
    if (get_option('invoice_company_phonenumber') != '') {
        $invoice_info .= _l('Tel') . ': ' . get_option('invoice_company_phonenumber') . '  ';
    }
    if (get_option('invoice_company_faxnumber') != '') {
        $invoice_info .= _l('Fax') . ': ' . get_option('invoice_company_faxnumber') . '  ';
    }
    if (get_option('main_domain') != '') {
        $invoice_info .= _l('Website') . ': ' . get_option('main_domain');
    }


// $pdf->writeHTMLCell((true ? ($dimensions['wk']) - ($dimensions['lm'] * 2) : ($dimensions['wk'] / 2) - $dimensions['lm']), '', '', 20, $invoice_info, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);

$pdf->writeHTMLCell(200, '', '', 25, $info_right, 0, 0, false, true, ('R'), true);

$pdf->ln(13);
// Set Head
if($invoice->rel_type=='adjustment')
{
    $plan_name=_l('adjustments');
}
if($invoice->rel_type=='internal')
{
    $plan_name=_l('internals');
}
if($invoice->rel_type=='return')
{
    $plan_name=_l('returns');
}
if($invoice->rel_type=='contract')
{
    $plan_name=_l('importfromcontract');
}

if($invoice->rel_type=='transfer')
{
    $plan_name=_l('importfromtranfer');
}



$pdf->SetFont($font_name, 'B', 20);
$pdf->Cell(0, 0, mb_strtoupper($plan_name, 'UTF-8') , 0, 1, 'C', 0, '', 0);

$pdf->SetFont($font_name, '', $font_size-1);
$pdf->writeHTMLCell('', '', '', '', '<i>'.getStrDate($invoice->date).'</i>', 0, 1, false, true, 'C', true);

$pdf->SetFont($font_name, '', $font_size-1);
$pdf->writeHTMLCell('', '', '', '', _l('no').$invoice_number, 0, 1, false, true, 'C', true);
$pdf->ln(5);

// //Set detail
// $pdf->SetFont($font_name, '', $font_size-1);
// $pdf->Cell(0, 0, _l('Mã phiếu: ').$invoice_number , 0, 1, 'L', 0, '', 0);
// $pdf->ln(4);

$pdf->SetFont($font_name, '', $font_size-1);

if($invoice->rel_type=='transfer')
{
    $deliver_name=get_staff_full_name($invoice->deliver_id);
    $pdf->writeHTMLCell('', '', '', '', _l('deliver_name').': '.'<b>'.($deliver_name?$deliver_name:_l('dot_blank')).'</b>', 0, 1, false, true, 'L', true);
}
else
{
    $pdf->writeHTMLCell('', '', '', '', _l('Họ tên người giao hàng: ').'<b>'.($invoice->deliver_name?$invoice->deliver_name:_l('dot_blank')).'</b>', 0, 1, false, true, 'L', true);
}
$pdf->ln(1);
if($invoice->rel_type!='transfer')
{
    $contract=getContractByImportID($invoice->id);
    $strHD='';
    if($contract) $strHD=_l('blank10').$contract->code._l('blank10').mb_strtolower(getStrDate($contract->date_create),'UTF-8')._l(' của ').$contract->supplier_name;
    $pdf->writeHTMLCell('', '', '', '', _l('Theo HĐ số ').($strHD?$strHD:_l('dot_blank')) , 0, 1, false, true, 'L', true);
    $pdf->ln(1);
}

$warehouse=getWarehouseByID($warehouse_id);
$strWH=_l('blank10')._l('blank10')._l('blank10')._l('blank10');
if($warehouse)
{
    $strWH=_l('blank10').'<b>'.$warehouse->code.' - '.$warehouse->warehouse.'</b>'._l('blank10');
}
if($invoice->rel_type=='transfer')
{
    $warehouse_to=getWarehouseByID($warehouse_id_to);
    $strWHTo=_l('blank10').'<b>'.$warehouse_to->code.' - '.$warehouse_to->warehouse.'</b>'._l('blank10');
    // var_dump($invoice);die;
    $receiver=getClient($invoice->receiver_id);
    $strReceiver=mb_ucfirst($receiver->company,'UTF-8')._l('blank10')._l('SĐT: ').($receiver->phonenumber?$receiver->phonenumber:_l('dot_blank'));
    // var_dump($strReceiver);die;
    if($invoice->is_staff==1)
    {
        $receiver=getStaff($invoice->receiver_id);
        $strReceiver=mb_ucfirst($receiver->fullname,'UTF-8')._l('blank10')._l('SĐT: ').($receiver->phonenumber?$receiver->phonenumber:_l('dot_blank'));
    }
}
if($invoice->rel_type=='transfer')
{
    $pdf->writeHTMLCell('', '', '', '', _l('Xuất tại kho ').$strWH._l('Địa điểm')._l('blank10').$warehouse->address, 0, 1, false, true, 'L', true);
    $pdf->ln(1);
    $pdf->writeHTMLCell('', '', '', '', _l('Nhập tại kho ').$strWHTo._l('Địa điểm')._l('blank10').$warehouse->address, 0, 1, false, true, 'L', true);
    $pdf->ln(1);
}
else
{
    $pdf->writeHTMLCell('', '', '', '', _l('Nhập tại kho ').$strWH._l('Địa điểm')._l('blank10').$warehouse->address, 0, 1, false, true, 'L', true);
    $pdf->ln(1);
}

$pdf->writeHTMLCell('', '', '', '', _l('Người nhận hàng: ').($strReceiver?$strReceiver:_l('dot_blank')), 0, 1, false, true, 'L', true);

$pdf->SetFont($font_name, '', $font_size-1);
$pdf->Ln(3);

$tblhtml = '
<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1px">
    <tr height="30" bgcolor="' . get_option('pdf_table_heading_color') . '" style="color:' . get_option('pdf_table_heading_text_color') . ';">
        <th scope="col"  width="10%" align="center">STT</th>
        <th scope="col"  width="20%" align="center">' . _l('Tên hàng hóa') . '</th>
        <th scope="col"  width="15%" align="center">' . _l('Mã số') . '</th>
        <th scope="col"  width="15%" align="center">' . _l('Đơn vị tính') . '</th>
        <th scope="col"  width="20%" align="center">' . _l('Số lượng (Chứng từ)') . '</th>
        <th scope="col"  width="20%" align="center">' . _l('Số lượng (Thực nhập)') . '</th>';
$tblhtml .= '</tr>';
$tblhtml.='<tr bgcolor="' . get_option('pdf_table_heading_color') . '" style="color:' . get_option('pdf_table_heading_text_color') . ';">';
$tblhtml.='<th align="center">A</th>';
$tblhtml.='<th align="center">B</th>';
$tblhtml.='<th align="center">C</th>';
$tblhtml.='<th align="center">D</th>';
$tblhtml.='<th align="center">1</th>';
$tblhtml.='<th align="center">2</th>';
$tblhtml.='</tr>';



// Items
$tblhtml .= '<tbody>';
$quantity=0;
$quantity_to=0;
$rowspan=count($invoice->items);
for ($i=0; $i < count($invoice->items) ; $i++) {
    // $categories=get_product_category($invoice->items[$i]->product_id);
    $product_name=$invoice->items[$i]->product_name;
    $quantity+=$invoice->items[$i]->quantity;
    $quantity_to+=$invoice->items[$i]->quantity_net;
    $tblhtml.='<tr>';
    $tblhtml.='<td align="center">'.($i+1).'</td>';
    $tblhtml.='<td >'.$product_name.'</td>';
    $tblhtml.='<td >'.$invoice->items[$i]->prefix.$invoice->items[$i]->short_name.'</td>';
    $tblhtml.='<td align="center">'.strip_tags($invoice->items[$i]->unit_name).'</td>';
    $tblhtml.='<td align="center">'._format_number($invoice->items[$i]->quantity).'</td>';
    $tblhtml.='<td align="center">'._format_number($invoice->items[$i]->quantity_net).'</td>';
    $tblhtml.='</tr>';

    

}

    $row=8;
    if($invoice->rel_type=='contract') $row=6;
    for ($j=$i; $j <=$row ; $j++) { 
        $tblhtml.='<tr>';
        $tblhtml.='<td align="center">'.($j+1).'</td>';
        $tblhtml.='<td></td>';
        $tblhtml.='<td></td>';
        $tblhtml.='<td align="center"></td>';
        $tblhtml.='<td align="right"></td>';
        $tblhtml.='<td align="right"></td>';
        $tblhtml.='</tr>';
    }

    $tblhtml.='<tr>';
    $tblhtml.='<td colspan="4" align="right"><b>'._l('Tổng giá trị').'</b></td>';
    $tblhtml.='<td align="center">'._format_number($quantity).'</td>';
    $tblhtml.='<td align="center">'._format_number($quantity_to).'</td>';
    $tblhtml.='</tr>';


$tblhtml .= '</tbody>';
$tblhtml .= '</table>';

           // var_dump($tblhtml);die;

// $pdf->writeHTML('<table></table>', true, false, false, false, '');
$pdf->writeHTML($tblhtml, true, false, false, false, '');

// $pdf->writeHTML($table, true, false, false, false, '');

// var_dump(expression);die;
$certificate_root=($invoice?$invoice->prefix.$invoice->code:_l('dot_blank'));
$strmoney='<ul>';
// $strmoney.='<li>'._l('str_money').'<i>'.$CI->numberword->convert($relsult,get_option('default_currency')).'</i>'.'</li>';
$strmoney.='<li>'._l('certificate_root').($certificate_root ? '<b>'._l('blank10').$certificate_root.'</b>' : _l('blank___')).'</li>';;
$strmoney.='</ul>';
$pdf->writeHTML($strmoney, false, false, false, false, 'L');
$pdf->Ln(3);

$pdf->SetFont($font_name, '', $font_size-1);
$pdf->writeHTMLCell('', '', '', '', '<i>'.getStrDate($invoice->date).'</i>', 0, 1, false, true, 'R', true);

$pdf->Ln(3);
$table = "<table style=\"width: 100%;text-align: center\" border=\"0\">
        <tr>
            <td><b>" . mb_ucfirst(_l('creater'), "UTF-8") . "</b></td>
            <td><b>" . mb_ucfirst(_l('deliver'), "UTF-8") . "</b></td>
            <td><b>" . mb_ucfirst(_l('warehouseman'), "UTF-8") . "</b></td>
            <td><b>" . mb_ucfirst(_l('chief_accountant'), "UTF-8") . "</b></td>
        </tr>
        <tr>
            <td>(ký, ghi rõ họ tên)</td>
            <td>(ký, ghi rõ họ tên)</td>
            <td>(ký, ghi rõ họ tên)</td>
            <td>(ký, ghi rõ họ tên)</td>
        </tr>
        <tr>
            <td style=\"height: 100px\" colspan=\"3\"></td>
        </tr>
        <tr>
            <td>" . mb_ucfirst($invoice->creater,"UTF-8") . "</td>
            <td>" . mb_ucfirst($invoice->deliver_name,"UTF-8") . "</td>
            <td>" . mb_ucfirst($invoice->warehouseman,"UTF-8") . "</td>
            <td>" . mb_ucfirst($invoice->chief_accountant,"UTF-8") . "</td>
        </tr>

</table>";
$pdf->writeHTML($table, true, false, false, false, '');
$pdf->WatermarkText();
