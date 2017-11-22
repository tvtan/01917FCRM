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


$pdf->ln(27);
$pdf->SetFont($font_name, 'I', $font_size-1);
$pdf->Cell(0, 0, mb_ucfirst(get_option('invoice_company_city').', '.getStrDate(date('Y-m-d')),'UTF-8') , 0, 1, 'R', 0, '', 0);
$pdf->ln(5);
// Set Head
$plan_name=_l('items_specifications');

$pdf->SetFont($font_name, 'B', 20);
$pdf->Cell(0, 0, mb_strtoupper($plan_name, 'UTF-8') , 0, 1, 'C', 0, '', 0);

$pdf->ln(10);
// Get Y position for the separation
// $y            = $pdf->getY();

// .'<i>'.$customer->contact_title.'</i>: '


//Set detail
$pdf->SetFont($font_name, '', $font_size);
$strPhone=$customer->phonenumber?$customer->phonenumber:($customer->mobilephone_number?$customer->mobilephone_number:_l('dot_blank'));
$strCompany=($customer->company?'<b>'.$customer->company.'</b>'._l('blank10'):_l('dot_blank'))._l('Email: ').($customer->email?$customer->email._l('blank10'):_l('dot_blank'))._l('SĐT: ').$strPhone;
if($customer->client_type==1)
{
    $pdf->writeHTMLCell(0, '', '', '', _l('dear').$strCompany, 0, 1, false, true, 'L', true);
}
else 
{
    $strContact='';
    $contact=get_primary_contact($invoice->customer_id);
    if($contact)
    {
        $strContact=$contact->contact_title.' '.$contact->lastname;
    }
    $pdf->writeHTMLCell(0, '', '', '', _l('dear').'<b>'.$strContact.'</b>'.'<br/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$strCompany, 0, 1, false, true, 'L', true);
}
$pdf->writeHTMLCell(0, '', '', '', _l('address').': '.getClient($invoice->customer_id,1), 0, 1, false, true, 'L', true);

$pdf->ln(2);

$pdf->SetFont($font_name, '', $font_size);
$pdf->writeHTMLCell(0, '', '', '', '<div style="padding-left: 100px">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'._l('first_2sd').'</div>', 0, 1, false, true, 'L', true);
$pdf->ln(2);
// The Table
$pdf->Ln(5);
$tblhtml = '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1px">
    <tr height="30" bgcolor="' . get_option('pdf_table_heading_color') . '" style="color:' . get_option('pdf_table_heading_text_color') . ';">
        <th width="5%" scope="col"   align="center" valign="middle">STT</th>
        <th width="15%" scope="col"   align="center" valign="middle">' . _l('Sản phẩm') . '</th>
        <th width="20%" scope="col"   align="center" valign="middle">' . _l('Chức năng/Đặc tính/Quy cách') . '</th>
        <th width="40%" scope="col"   align="center" valign="middle">' . _l('Hình ảnh') . '</th>
        <th width="20%" scope="col"   align="center" valign="middle">' . _l('Công tác và vật dụng cần thiết để lắp đặt thiết bị') . '</th>';
$tblhtml .= '</tr>';

// Items


$tblhtml .= '<tbody>';
$grand_total=0;
$quantity_total=0;
for ($i=0; $i < count($invoice->items) ; $i++) { 
    $strImages='';

    if($invoice->items[$i]->images_product)
    {
        $images=explode(',', $invoice->items[$i]->images_product);
        foreach ($images as $keyI => $image) {
            if($keyI==2) break;
            $img='';
            
            $img=FCPATH .$image;
            if(file_exists($img))
            {
                $strImages.='<p style="text-align: center;"><img width="400px" src="' .$img.'" style="padding: 5px"></p>';
            }
        }
        
    }
    $product_features=htmlspecialchars(strip_tags($invoice->items[$i]->product_features),ENT_QUOTES);
    $item_others=htmlspecialchars(strip_tags($invoice->items[$i]->item_others),ENT_QUOTES);
    $tblhtml.='<tr>';
    $tblhtml.='<td align="center">'.($i+1).'</td>';
    $tblhtml.='<td align="left">'.$invoice->items[$i]->product_name.'</td>';
    $tblhtml.='<td align="left">'.$product_features.'</td>';
    $tblhtml.='<td align="center" >'.$strImages.'</td>';
    $tblhtml.='<td align="center">'.$item_others.'</td>';
    $tblhtml.='</tr>';
}

$tblhtml .= '</tbody>';
$tblhtml .= '</table>';

$tblhtml = str_replace('- ', '<br /> - ', $tblhtml);

$pdf->writeHTML($tblhtml, true, false, false, false, '');
