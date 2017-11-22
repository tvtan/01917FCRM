<?php
$dimensions = $pdf->getPageDimensions();

$pdf->printHeader(-20);

function mb_ucfirst($string, $encoding)
{
    return mb_convert_case($string, MB_CASE_TITLE, $encoding);
}
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
    $pdf->SetFont($font_name, '', $font_size);
    $pdf->setX(10);
    $pdf->setY(10);
}

$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP+10, PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM-5);

if (is_array($pdf_text_color_array) && count($pdf_text_color_array) == 3) {
    $pdf->SetTextColor($pdf_text_color_array[0], $pdf_text_color_array[1], $pdf_text_color_array[2]);
}

$info_right_column = '';
$info_left_column  = '';

// $info_right_column=get_option('invoice_company_city').', '._l('date_',date('d'))._l('month_',date('m'))._l('date_',date('Y'));

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

$pdf->ln(27);
// Set Head
$plan_name=_l('als_quotation');

$pdf->SetFont($font_name, 'B', 20);
$pdf->Cell(0, 0, mb_strtoupper($plan_name, 'UTF-8') , 0, 1, 'C', 0, '', 0);

//Set code
$pdf->SetFont($font_name, 'I', $font_size);
$pdf->Cell(0, 0, _l('code_no').($invoice_number) , 0, 1, 'C', 0, '', 0);
// $pdf->ln(4);
//Set date
$pdf->SetFont($font_name, 'I', $font_size);
$pdf->Cell(0, 0, _l('view_date').': '._d($invoice->date) , 0, 1, 'C', 0, '', 0);
$pdf->ln(4);




// Get Y position for the separation
// $y            = $pdf->getY();

// .'<i>'.$customer->contact_title.'</i>: '


//Set detail
$pdf->SetFont($font_name, '', $font_size);
$strPhone=$customer->phonenumber?$customer->phonenumber:($customer->mobilephone_number?$customer->mobilephone_number:_l('dot_blank'));
$strCompany=($invoice->customer_name?'<b>'.$invoice->customer_name.'</b>'._l('blank10'):_l('dot_blank'))._l('Email: ').($customer->email?$customer->email._l('blank10'):_l('dot_blank'))._l('SĐT: ').$strPhone;
if($customer->client_type==1)
{
    $pdf->writeHTMLCell(0, '', '', '', _l('dear').$strCompany, 0, 1, false, true, 'L', true);
}
else 
{
    $pdf->writeHTMLCell(0, '', '', '', _l('dear').'<b>'.$customer->primary_contact.'<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$strCompany.'</b>', 0, 1, false, true, 'L', true);
}
$pdf->writeHTMLCell(0, '', '', '', _l('address').': '.getClient($invoice->customer_id,1), 0, 1, false, true, 'L', true);

$pdf->ln(2);

$pdf->SetFont($font_name, '', $font_size);
$pdf->writeHTMLCell(0, '', '', '', '<div style="padding-left: 100px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'._l('first_1st').'</div>', 0, 1, false, true, 'L', true);
$pdf->ln(2);
if($invoice->isVisibleTax==1)
{
    $colSP=3;
    $colDecr=5;
}
// The Table
$pdf->Ln(5);
$tblhtml = '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1px">
    <tr height="30" bgcolor="' . get_option('pdf_table_heading_color') . '" style="color:' . get_option('pdf_table_heading_text_color') . ';">
        <th width="5%" scope="col"   align="center" valign="middle">STT</th>
        <th width="'.(15-$colSP).'%" scope="col"   align="center" valign="middle">' . _l('Sản phẩm') . '</th>
        <th width="'.(20-$colDecr).'%" scope="col"   align="center" valign="middle">' . _l('Chức năng/Đặc tính/Quy cách') . '</th>
        <th width="9%" scope="col"   align="center" valign="middle">' . _l('Hình ảnh') . '</th>
        <th width="8%" scope="col"   align="center" valign="middle">' . _l('Kích thước') . '</th>
        <th width="6%" scope="col"  align="center" valign="middle">' . _l('Số lượng').'</th>
        <th width="6%" scope="col"  align="center" valign="middle">' . _l('Đơn vị tính'). '</th>
        <th width="9%" scope="col"  align="center" valign="middle">' . _l('Đơn giá') . '</th>';
if($invoice->isVisibleTax==1)
{
    $tblhtml .='<th width="9%" scope="col"  align="center" valign="middle">' . _l('Thuế') . '</th>';
}
$tblhtml .='<th width="8%" scope="col"  align="center" valign="middle">' . _l('Khuyến mãi') . '</th>
        <th width="13%" scope="col"  align="center" valign="middle">' . _l('Giá trị'). '</th>';
$tblhtml .= '</tr>';

// var_dump($invoice);die;
// Items


$tblhtml .= '<tbody>';
$grand_total=0;
$quantity_total=0;
$total_discount=0;
$total_tax=0;
for ($i=0; $i < count($invoice->items) ; $i++) { 
    $grand_total+=$invoice->items[$i]->amount;
    $quantity_total+=$invoice->items[$i]->quantity;
    $total_discount+=$invoice->items[$i]->dicount;
    $total_tax+=$invoice->items[$i]->tax;
    
    $img='';

    if($invoice->items[$i]->image)
    {
        $img=FCPATH .$invoice->items[$i]->image;
    }
    // var_dump(strip_tags($invoice->items[$i]->product_features));die;
    $biena=htmlspecialchars(strip_tags($invoice->items[$i]->product_features),ENT_QUOTES);
    $tblhtml.='<tr>';
    $tblhtml.='<td align="center">'.($i+1).'</td>';
    $tblhtml.='<td align="left">'.$invoice->items[$i]->product_name.'</td>';
    $tblhtml.='<td align="left">'.$biena.'</td>';
    $tblhtml.='<td align="center" >'.'<img width="100px" src="' .$img.'" style="padding: 5px">'.'</td>';
    $tblhtml.='<td align="left">'.$invoice->items[$i]->size.'</td>';
    $tblhtml.='<td align="center">'._format_number($invoice->items[$i]->quantity).'</td>';
    $tblhtml.='<td align="center">'.$invoice->items[$i]->unit_name.'</td>';
    $tblhtml.='<td align="right">'.format_money($invoice->items[$i]->unit_cost).'</td>';
    if($invoice->isVisibleTax==1)
    {
        $tblhtml.='<td align="right">'.format_money($invoice->items[$i]->tax).'</td>';
    }
    $tblhtml.='<td align="right">'.format_money($invoice->items[$i]->dicount).'</td>';
    $tblhtml.='<td align="right">'.format_money($invoice->items[$i]->amount).'</td>';
    $tblhtml.='</tr>';
}
    $tblhtml.='<tr>';
    $tblhtml.='<td colspan="5" align="center"><b>'.mb_strtoupper(_l('total'),'UTF-8').'</b></td>';
    $tblhtml.='<td align="center"><b>'._format_number($quantity_total).'</b></td>';
    $tblhtml.='<td align="right"></td>';
    $tblhtml.='<td align="right"></td>';
    if($invoice->isVisibleTax==1)
    {
        $tblhtml.='<td align="right">'.format_money($total_tax).'</td>';
    }
    $tblhtml.='<td align="right">'.format_money($total_discount).'</td>';
    $tblhtml.='<td align="right"><b>'.format_money($invoice->total,get_option('default_currency')).'</b></td>';
    $tblhtml.='</tr>';

$tblhtml .= '</tbody>';
$tblhtml .= '</table>';

$tblhtml = str_replace('- ', '<br /> - ', $tblhtml);

$pdf->writeHTML($tblhtml, true, false, false, false, '');



if (get_option('total_to_words_enabled') == 1) {
    // Set the font again to normal like the rest of the pdf
    $pdf->SetFont($font_name, '', $font_size);
    $strmoney='<ul>';
    $strmoney.='<li>'._l('str_money').'<i>'.$CI->numberword->convert($invoice->total, get_option('default_currency')).'</i>'.'</li>';
    $strmoney.='<li>'._l('certificate_root')._l('blank10').$invoice_number.'</li>';
    $strmoney.='</ul>';
    $pdf->writeHTMLCell(0, '', '', '', $strmoney, 0, 1, false, true, 'L', true);
}

$pdf->ln(3);
$pdf->SetFont($font_name, '', $font_size);
$pdf->writeHTMLCell(0, '', '', '', '<b>'.mb_strtoupper(_l('sumary_note').': </b>', 'UTF-8').(($invoice->reason)? $invoice->reason : _l('sumary_detail_html')), 0, 1, false, true, 'L', true);
$pdf->ln(3);
$department=$invoice->department;
if($invoice->create_by==1)
{
    $department=_l('directer_department');
}
$strshortD='NV';
foreach (explode(' ',mb_ucfirst($department,"UTF-8")) as $key => $value) {
    $strshortD.=mb_substr($value,0,1,'utf-8');
}
// var_dump($invoice);die;

$table = "<div><table style=\"width: 100%;text-align: center\" border=\"0\">
        <tr>
            <td>" . mb_ucfirst('', "UTF-8") . "</td>
            <td><b>" . mb_strtoupper(get_option('invoice_company_name'), "UTF-8") . "</b></td>
        </tr>
        <tr  style=\"margin-bottom:200px\">
            <td>" . mb_ucfirst('', "UTF-8") . "</td>
            <td><i>" . mb_ucfirst('Phòng '.$department, "UTF-8") . "</i></td>
        </tr>
        <tr>
            <td style=\"height: 100px\" colspan=\"3\"></td>
        </tr>
        <tr>
            <td>" . mb_ucfirst('', "UTF-8") . "</td>
            <td>". $strshortD.': '.mb_ucfirst($invoice->creater, "UTF-8") ."</td>
        </tr>
        
</table></div>";
$pdf->writeHTML($table, true, false, false, false, '');



