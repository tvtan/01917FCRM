<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$aColumns     = array(
    'tblitems.id',
    'tblitems.avatar',
    'tblitems.code',
    'tblitems.name',
    'tblitems.short_name',
    'tblitems.description',
    'tblitems.product_features',
    'tblitems.size',
    'tblitems.specification',
    'tblitems.weight',
    'tblitems.price',
    'tblunits.unit',
    'tblitems_groups.name',
    'tblitems.minimum_quantity',
    'tblitems.maximum_quantity',
    );
$sIndexColumn = "id";
$sTable       = 'tblitems';
$where = array(

);
$order_by = 'tblitems.id ASC';
$order_by = '';
$join             = array(
    'LEFT JOIN tbltaxes ON tbltaxes.id = tblitems.tax',     
    'LEFT JOIN tblitems_groups ON tblitems_groups.id = tblitems.group_id',
    'LEFT JOIN district ON district.districtid = tblitems.district_id',
    'LEFT JOIN tblunits ON tblitems.unit = tblunits.unitid',
    );
$additionalSelect = array(
    'tblitems.id',
    'tbltaxes.name', 
    'taxrate',
    'group_id',
    );
if($this->_instance->input->post()) {
    $filter_category_1 = $this->_instance->input->post('category_1');
    $filter_category_2 = $this->_instance->input->post('category_2');
    $filter_category_3 = $this->_instance->input->post('category_3');
    $filter_category_4 = $this->_instance->input->post('category_4');

    if(!is_null($filter_category_4) && $filter_category_4 != "") {
        // array_push($where, 'AND tblitems.category_id='.$filter_category_4);
        $result=[];
        $this->_instance->category_model->get_full_childs_id($filter_category_4, $result);
        $sum_where = 'AND (';
        foreach($result as $value) {
            if($sum_where != 'AND (')
                $sum_where.=' OR ';
            $sum_where .= 'tblitems.category_id='.$value;
        }
        $sum_where .= ')';
        array_push($where, $sum_where);
    }
    else if(!is_null($filter_category_3) && $filter_category_3 != "") {
        // array_push($where, 'AND tblitems.category_id='.$filter_category_3);
        $result=[];
        $this->_instance->category_model->get_full_childs_id($filter_category_3, $result);
        $sum_where = 'AND (';
        foreach($result as $value) {
            if($sum_where != 'AND (')
                $sum_where.=' OR ';
            $sum_where .= 'tblitems.category_id='.$value;
        }
        $sum_where .= ')';
        array_push($where, $sum_where);
    }
    else if(!is_null($filter_category_2) && $filter_category_2 != "") {
        // array_push($where, 'AND tblitems.category_id='.$filter_category_2);
        $result=[];
        $this->_instance->category_model->get_full_childs_id($filter_category_2, $result);
        $sum_where = 'AND (';
        foreach($result as $value) {
            if($sum_where != 'AND (')
                $sum_where.=' OR ';
            $sum_where .= 'tblitems.category_id='.$value;
        }
        $sum_where .= ')';
        array_push($where, $sum_where);
    }
    else if(!is_null($filter_category_1) && $filter_category_1 != "") {
        //array_push($where, 'AND tblitems.category_id='.$filter_category_1);
        $result=[];
        $this->_instance->category_model->get_full_childs_id($filter_category_1, $result);
        $sum_where = 'AND (';
        foreach($result as $value) {
            if($sum_where != 'AND (')
                $sum_where.=' OR ';
            $sum_where .= 'tblitems.category_id='.$value;
        }
        $sum_where .= ')';
        array_push($where, $sum_where);
    } 
}
// print_r($where);
// exit();
$result           = data_tables_init($aColumns, $sIndexColumn, $sTable ,$join, $where, $additionalSelect,$order_by);
$output           = $result['output'];
$rResult          = $result['rResult'];


// $currentPage = (

//     !is_null($this->_instance->input->post('start')) && !is_null($this->_instance->input->post('length')) ? 
//     $this->_instance->input->post('start') / $this->_instance->input->post('length') 
// : 0 ) + 1;
$currentPage=$this->_instance->input->post('start');
$currentall=$output['iTotalRecords'];

foreach ($rResult as $r=> $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];

        $array_link = ['tblitems.code', 'tblitems.name'];
        if(in_array($aColumns[$i],$array_link)){
            $_data = '<a href="'.admin_url('invoice_items/item/').$aRow['id'].'">'.$_data.'</a>';
        }
         if($aColumns[$i]=='tblitems.id') {
            $_data = ($currentall+1)-($currentPage+$r+1);
        }
        if($aColumns[$i] == 'tblitems.avatar' && file_exists($_data)) {
            $_data='<div class="preview_image" style="width: auto;">
                            <div class="display-block contract-attachment-wrapper img-'.$aRow['id'].'">
                                <div style="width:100px">
                                    <a href="'.(file_exists($_data) ? base_url($_data) : base_url('assets/images/preview_no_available.jpg')).'" data-lightbox="customer-profile" class="display-block mbot5">
                                        <div class="table-image">
                                            <img src="'.(file_exists($_data) ? base_url($_data) : base_url('assets/images/preview_no_available.jpg')).'" style="width: auto;height: 100%;" />
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>';
//            $_data = '<img src="'.base_url($_data).'" width="50px" />';
        }
        $format_number_column = ['tblitems.price','tblitems.minimum_quantity','tblitems.maximum_quantity'];
        if(in_array($aColumns[$i], $format_number_column)) {
            $_data = number_format($_data,0,',','.');
        }
        if($aColumns[$i] == 'tblitems.description') {
//            $_data = strlen(strip_tags($_data)) > 50 ? mb_substr(strip_tags($_data),0,50,'utf-8')."..." : $_data;
//            $_data =strip_tags($_data);
            $_data =$_data;
            // $_data = strlen($_data) > 50 ? substr($_data,0,50)."..." : $_data;
        }
        if($aColumns[$i] == 'tblitems.product_features') {
//            $_data = strlen(strip_tags($_data)) > 50 ? mb_substr(strip_tags($_data),0,50,'utf-8')."..." : $_data;
            $_data =$_data;
//            $_data =strip_tags($_data);
        }
        // if($aColumns[$i] == 'tblitems.price') {
        //     $_data = number_format($aRow['tblitems.price'],0,',','.');
        // }
        $row[] = $_data;
    }
    $options = '';
    if(has_permission('items','','edit')){
        $options .= icon_btn('invoice_items/item/' . $aRow['id'], 'pencil-square-o', 'btn-default');
    }
    if(has_permission('items','','delete')){
       $options .= icon_btn('invoice_items/delete/' . $aRow['id'], 'remove', 'btn-danger _delete');
   }
   $row[] = $options;

   $output['aaData'][] = $row;
}
