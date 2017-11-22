<?php init_head(); ?>
<div id="wrapper">
 <div class="content">
   <div class="row">

  <div class="col-md-12">
   <div class="panel_s">
     <div class="panel-body">

        <?php if (isset($item)) { ?>
        <?php echo form_hidden('isedit'); ?>
        <?php echo form_hidden('itemid', $item->id); ?>
      <div class="clearfix"></div>
        <?php 
        } ?>
        <!-- Product information -->
        

  <h4 class="bold no-margin"><?php echo (isset($item) ? (($item->status==2)?_l('Xem phiếu phiếu nhập hàng'):_l('Sửa phiếu phiếu nhập hàng')) : _l('Tạo phiếu phiếu nhập hàng')); ?></h4>
  <hr class="no-mbot no-border" />
  <div class="row">
    <div class="additional"></div>
    <div class="col-md-12">
        <?php
         if(isset($item))
            {
                if($item->status==0)
                {
                    $type='warning';
                    $status='Chưa duyệt';
                }
                elseif($item->status==1)
                {
                    $type='info';
                    $status='Đã xác nhận';
                }
                else
                {
                    $type='success';
                    $status='Đã duyệt';
                }
                // if($item->invoice_status==1)
                // {
                //     $type='success';
                //     $status='Đã lập hóa đơn';
                //     $style='style="font-size: 8px"';
                // }
            }
            else
            {
                $type='warning';
                $status='Phiếu mới';
            }

        ?>
        <div class="ribbon <?=$type?>"><span><?=$status?></span></div>
        <ul class="nav nav-tabs profile-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#item_detail" aria-controls="item_detail" role="tab" data-toggle="tab">
                    <?php echo _l('Chi tiết phiếu phiếu nhập hàng'); ?>
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="item_detail">
            <div class="row">
                
                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                    
                </div>
                
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 _buttons">
                    <div class="pull-right">
                        <?php if( isset($item) ) { ?>
                        <a href="<?php echo admin_url('imports/detail_pdf/' . $item->id . '?print=true') ?>" target="_blank" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="In" aria-describedby="tooltip652034"><i class="fa fa-print"></i></a>
                        <a href="<?php echo admin_url('imports/detail_pdf/' . $item->id  ) ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Xem PDF"><i class="fa fa-file-pdf-o"></i></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            
            <?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'client-form', 'autocomplete' => 'off')); ?>
                <div class="row">
                  <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">            
                    <?php
                    $attrs_not_select = array('data-none-selected-text' => _l('system_default_string'));
                    ?>
                    <div class="form-group">
                         <label for="number"><?php echo _l('code_noo'); ?></label>
                         <div class="input-group">
                          <span class="input-group-addon">
                          <?php $prefix =($item) ? $item->prefix : get_option('prefix_import_contract'); ?>
                            <?=$prefix?>
                            <?php echo form_hidden('rel_type', 'contract'); ?>
                            <?=form_hidden('prefix',$prefix)?>    
                            </span>
                            <?php 
                                if($item)
                                {
                                    $number=$item->code;
                                }
                                else
                                {
                                    $number=sprintf('%06d',getMaxID('id','tblimports')+1);
                                }
                            ?>
                            <input type="text" name="code" class="form-control" value="<?=$number ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" readonly>
                          </div>
                    </div>

                    <?php $value = (isset($item) ? _d($item->date) : _d(date('Y-m-d')));?>
                    <?php echo render_date_input('date','view_date',$value); ?>

                    <?php $value = (isset($item) ? _d($item->account_date) : _d(date('Y-m-d')));?>
                    <?php echo render_date_input('account_date','account_date',$value); ?>

                    <?php $selected = (isset($item) ? $item->supplier_id : ''); ?>
                    <?php echo render_select('supplier_id',$suppliers,array('userid','company'),'suppliers',$selected); ?>

                    <?php $selected = (isset($item) ? $item->rel_id : ''); ?>
                    <?php echo render_select('rel_id',$contracts,array('id',array('prefix','code')),'contracts',$selected); ?>

                    <?php
                    $default_name = (isset($item) ? $item->name : "Phiếu nhập kho");
                    echo render_input('name', _l('import_name'), $default_name);
                    ?>

                    

                    <?php 
                    $reason = (isset($item) ? $item->reason : "");
                    echo render_textarea('reason', 'note', $reason, array(), array(), '', 'tinymce');
                    ?>
                </div>

                
                
                
                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                    <!-- Cusstomize from invoice -->
                    <div class="panel-body mtop10">
                        <div class="row">
                            <div class="col-md-4">
                            <?php
                            $selected=(isset($item) ? $warehouse_id : '');
                            echo render_select('warehouse_id',$warehouses,array('warehouseid','warehouse'),'warehouse_name',$selected); 
                            ?>
                            </div>
                            <div class="col-md-4"  style="display: none;">
                                <div class="form-group mbot25">
                                <label for="custom_item_select"><?=_l('item_name')?></label>
                                    <select class="selectpicker no-margin" data-width="100%" id="custom_item_select" data-none-selected-text="<?php echo _l('add_item'); ?>" data-live-search="true">
                                        <option value=""></option>

                                        <?php foreach ($items as $product) { ?>
                                        <option value="<?php echo $product['id']; ?>" data-subtext="">(<?php echo $product['code']; ?>) <?php echo $product['name']; ?></option>
                                        <?php 
                                        } ?>

                                    <!-- <?php if (has_permission('items', '', 'create')) { ?>
                                    <option data-divider="true"></option>
                                    <option value="newitem" data-content="<span class='text-info'><?php echo _l('new_invoice_item'); ?></span>"></option>
                                    <?php } ?> -->
                                    </select>
                                </div>
                            </div>
                        
                            <div class="col-md-5 text-right show_quantity_as_wrapper">
                                
                            </div>
                        </div>                        

                        <div class="table-responsive s_table" style="overflow-x: auto;overflow-y: hidden;padding-bottom: 100px">
                            <table class="table items item-purchase no-mtop">
                                <thead>
                                    <tr>
                                        <th><input type="hidden" id="itemID" value="" /></th>
                                        <th style="min-width: 200px" class="text-left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_name'); ?>"></i> <?php echo _l('item_name'); ?></th>
                                        <th style="max-width: 100px;min-width: 100px;<?php if(!has_permission('view_account','','have')){ ?>display: none;<?php } ?>" class="text-left"><?php echo _l('tk_no_1561'); ?></th>
                                        <th style="max-width: 100px;min-width: 100px;<?php if(!has_permission('view_account','','have')){ ?>display: none;<?php } ?>" class="text-left"><?php echo _l('tk_co_331'); ?></th>
                                        <th style="min-width: 80px" class="text-left"><?php echo _l('exchange_rate'); ?></th>
                                        <th style="min-width: 80px" class="text-left"><?php echo _l('item_quantity'); ?></th>
                                        <th style="min-width: 80px" class="text-left"><?php echo _l('item_price_buy'); ?></th>
                                        <th style="min-width: 80px" class="text-left"><?php echo _l('into_money'); ?></th>
                                        <th style="min-width: 200px" class="text-left"><?php echo _l('tax'); ?></th>
                                        <th style="min-width: 80px" class="text-left"><?php echo _l('discount'); ?></th>
                                        <th class="text-left"><?php echo _l('purchase_price'); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <tr class="main" style="display: none;">
                                        <td><input type="hidden" id="itemID" value="" /></td>
                                        <td>
                                            <?php echo _l('item_name'); ?>
                                        </td>
                                        <!-- TKno -->
                                        <td>
                                            <?php
                                            $accountAttribute = array();
                                            if(!has_permission('view_account','have')){ 
                                                $accountAttribute['style'] = "display: none;";
                                            }
                                            $selected=(isset($item) ? $item->tk_no : '');
                                            echo render_select('tk_no',$accounts_no,array('idAccount','accountCode','accountName'),'',$selected, array(), $accountAttribute); 
                                            ?>
                                        </td>
                                        <!-- TKCo -->

                                        <td>
                                            <?php
                                            $selected=(isset($item) ? $item->tk_co : '');
                                            echo render_select('tk_co',$accounts_co,array('idAccount','accountCode','accountName'),'',$selected, array(), $accountAttribute); 
                                            ?>
                                        </td>
                                        <td>
                                            <input type="hidden" id="item_unit" value="" />
                                            <?php echo _l('item_warehouse'); ?>
                                        </td>

                                        <td>
                                            <input class="mainQuantity" type="number" min="1" value="1"  class="form-control" placeholder="<?php echo _l('item_quantity'); ?>">
                                        </td>
                                        
                                        <td>
                                            <?php echo _l('item_price_buy'); ?>
                                        </td>
                                        <td>
                                            0
                                        </td>
                                        <td>
                                            <button style="display:none" id="btnAdd" type="button" onclick="createTrItem(); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button>
                                        </td>
                                    </tr>
                                    <?php
                                    $i=0;
                                    $totalPrice=0;
                                    $totalQuantity=0;
                                    if(isset($item) && count($item->items) > 0) {
                                        
                                        foreach($item->items as $value) {
                                        ?>
                                    <tr class="sortable item">
                                        <td>
                                            <input type="hidden" name="items[<?php echo $i; ?>][id]" value="<?php echo $value->product_id; ?>">
                                        </td>
                                        <td class="dragger"><?php echo $value->product_name; ?></td>
                                        <!-- TK NO -->
                                        <td>
                                            <?php
                                            $selected=(isset($value) ? $value->tk_no : '');
                                            echo render_select('items['.$i.'][tk_no]',$accounts_no,array('idAccount','accountCode','accountName'),'',$selected, array(), $accountAttribute); 
                                            ?>
                                        </td>
                                        <!-- TK CO -->
                                        <td>
                                            <?php
                                            $selected=(isset($value) ? $value->tk_co : '');
                                            echo render_select('items['.$i.'][tk_co]',$accounts_co,array('idAccount','accountCode','accountName'),'',$selected, array(), $accountAttribute); 
                                            ?>
                                        </td>
                                        <td>
                                            <?=number_format($value->exchange_rate)?>
                                        </td>
                                        <?php
                                            // $max=$value->quantity-$value->quantity_net;
                                        // var_dump($value);
                                            $max=$value->quantity;
                                         ?>
                                        <td><input class="mainQuantity" type="number" min="0" max="<?=$max?>" name="items[<?php echo $i; ?>][quantity_net]" value="<?php echo $value->quantity_net; ?>"></td>

                                        <?php 
                                            $price_buy=$value->exchange_rate*$value->unit_cost;
                                            $sub_total=$price_buy*$value->quantity;
                                            $tax=$sub_total*$value->tax_rate/100;
                                            $discount=$sub_total*$value->discount_percent/100;
                                            $amount=$value->sub_total+$tax-$discount;
                                         ?>

                                        <td>
                                            <?php echo number_format($price_buy); ?>
                                            <input type="hidden" name="items[<?=$i?>][quantity]" value="<?=$value->quantity?>" />
                                            <input type="hidden" class="exchange_rate" name="items[<?=$i?>][exchange_rate]" value="<?=$value->exchange_rate?>" />
                                            <input type="hidden" class="price_buy" name="items[<?=$i?>][price_buy]" value="<?=$value->unit_cost?>" />
                                            <input type="hidden" class="tax_rate" name="items[<?=$i?>][tax_rate]" value="<?=$value->tax_rate?>" />
                                            <input type="hidden" class="discount_percent" name="items[<?=$i?>][discount_percent]" value="<?=$value->discount_percent?>" />

                                        </td>
                                        <td><?php echo number_format($sub_total); ?></td>
                                        <td><?php echo number_format($tax); ?></td>
                                        <td><?php echo number_format($discount); ?></td>
                                        <td><?php echo number_format($amount); ?></td>
                                        <td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>
                                    </tr>
                                        <?php
                                            $totalPrice += $amount;
                                            $totalQuantity += $value->quantity;
                                            $i++;
                                        }
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-8 col-md-offset-4">
                            <table class="table text-right">
                                <tbody>
                                    <tr>
                                        <td><span class="bold"><?php echo _l('purchase_total_items_quantity'); ?> :</span>
                                        </td>
                                        <td class="total">
                                            <?php echo $totalQuantity ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="bold"><?php echo _l('purchase_total_price'); ?> :</span>
                                        </td>
                                        <td class="totalPrice">
                                            <?php echo number_format($totalPrice) ?> VND
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <!-- End Customize from invoice -->
                </div>
                
                <?php if(isset($item) && $item->status != 2 || !isset($item)) { ?>
                  <button class="btn btn-info mtop20 only-save customer-form-submiter" style="margin-left: 15px">
                    <?php echo _l('submit'); ?>
                </button>
                <?php } ?>
              </div>
            <?php echo form_close(); ?>
            </div>
        </div>

      </div>

        <!-- END PI -->        
  </div>
</div>
</div>
</div>
</div>
</div>
<?php init_tail(); ?>
<script>
    _validate_form($('.client-form'),{code:'required',warehouse_id:'required'});
    

    var itemList = <?php echo json_encode($items);?>;

    $('#supplier_id').change(function(e){
        $('table tr.item').remove();
        total=0;
        var supplier_id=$(this).val();
        loadContractsBySuppID(supplier_id)
        refreshAll();
        refreshTotal();
    }); 

    function loadContractsBySuppID(supplier_id){
        var rel_id=$('#rel_id');
        rel_id.find('option:gt(0)').remove();
        rel_id.selectpicker('refresh');
        if(rel_id.length) {
            $.ajax({
                url : admin_url + 'purchase_contracts/getContractsBySuppID/' + supplier_id,
                dataType : 'json',
            })
            .done(function(data){         
                $.each(data, function(key,value){

                    rel_id.append('<option value="' + value.id + '">'+ value.code + '</option>');
                });
                rel_id.selectpicker('refresh');
            });
        }
    } 

    $('#rel_id').change(function(e){
        $('table.item-purchase tr.item').remove();
        total=0;
        var contract_id=$(this).val();
        loadAllItemsByContractID(contract_id)
        // refreshAll();
        // refreshTotal();
    }); 

    function loadAllItemsByContractID(contract_id){
        $('table.item-purchase tbody tr.sortable.item').remove();
        if(contract_id) {
            $.ajax({
                url : admin_url + 'purchase_contracts/getAllItemsByContractID/' + contract_id,
                dataType : 'json',
            })
            .done(function(data){ 
                total=0;
                var row=0;
                $.each(data, function(key,value){
                    
                    var maxQ=value.product_quantity-value.entered_quantity;
                    if(maxQ>0){
                    row++;
                    var newTr = $('<tr class="item"></tr>');
                    var td1 = $('<td class="dragger"><input type="hidden" name="items[' + uniqueArray + '][id]" value="'+value.product_id+'" /></td>');
                    var td2 = $('<td>'+value.name+'</td>');
                    var td3 = $('<td></td>');
                    var selectTd3 = $('tr.main').find('td:nth-child(3) select').clone();
                    selectTd3.val(value.tk_no);
                    selectTd3.removeAttr('id');
                    var tk_no='items['+uniqueArray+'][tk_no]';
                    selectTd3.attr('name',tk_no);
                    td3.append(selectTd3);

                    var td4 = $('<td></td>');
                    var selectTd4 = $('tr.main').find('td:nth-child(4) select').clone();
                    selectTd4.val(value.tk_co);
                    selectTd4.removeAttr('id');
                    var tk_co='items['+uniqueArray+'][tk_co]';
                    selectTd4.attr('name',tk_co);
                    td4.append(selectTd4);

                    

                    var price_buy=parseFloat(value.price_buy)*parseFloat(value.exchange_rate);
                    if(isNaN(parseFloat(price_buy)))
                    {
                        price_buy=0;
                    }
                    var sub_total=parseFloat(maxQ)*price_buy;
                    if(isNaN(parseFloat(sub_total)))
                    {
                        sub_total=0;
                    }
                    var tax=sub_total*value.taxrate/100;
                    if(isNaN(parseFloat(tax)))
                    {
                        tax=0;
                    }
                    var discount=sub_total*value.discount/100;
                    if(isNaN(parseFloat(discount)))
                    {
                        discount=0;
                    }
                    var amount=sub_total+tax-discount;
                    // console.log(value);
                    
                    

                    var td5 = $('<td>'+formatNumber(value.exchange_rate)+'</td>');
                    var td6 = $('<td><input type="hidden" name="items['+uniqueArray+'][quantity]" value="'+value.product_quantity+'" /><input type="number" min="0" max="'+maxQ+'" class="mainQuantity" name="items['+uniqueArray+'][quantity_net]" value="'+maxQ+'" /></td>');
                    var td7 = $('<td><input type="hidden" class="exchange_rate" name="items['+uniqueArray+'][exchange_rate]" value="'+value.exchange_rate+'" /><input type="hidden" class="price_buy" name="items['+uniqueArray+'][price_buy]" value="'+value.price_buy+'" /><input type="hidden" class="tax_rate" name="items['+uniqueArray+'][tax_rate]" value="'+value.taxrate+'" /><input type="hidden" class="discount_percent" name="items['+uniqueArray+'][discount_percent]" value="'+value.discount_percent+'" />'+formatNumber(price_buy)+'</td>');
                    
                    
                    
                    var td8 = $('<td>'+formatNumber(sub_total)+'</td>');
                    var td9 = $('<td>'+formatNumber(tax)+'</td>');
                    var td10 = $('<td>'+formatNumber(discount)+'</td>');
                    var td11 = $('<td>'+formatNumber(amount)+'</td>');
                    $('#warehouse_id').selectpicker('val',value.warehouse_id);
                    $('#warehouse_id').selectpicker('refresh');
                    newTr.append(td1);
                    newTr.append(td2);
                    newTr.append(td3);
                    newTr.append(td4);
                    newTr.append(td5);
                    newTr.append(td6);
                    newTr.append(td7);
                    newTr.append(td8);
                    newTr.append(td9);
                    newTr.append(td10);
                    newTr.append(td11);
                    $('table.item-purchase tbody').append(newTr);
                }
                    uniqueArray++;
                    total++;
                    
                });
                
                $('.selectpicker').selectpicker('refresh');
                refreshTotal();
            });
        }
    }

    $('#warehouse_id').change(function(e){
        $('table tr.sortable.item').remove();
        total=0;
        var warehouse_id=$(this).val();
        loadProductsInWarehouse(warehouse_id)
        refreshAll();
        refreshTotal();
    });

    function loadProductsInWarehouse(warehouse_id){
        var product_id=$('#custom_item_select');
        product_id.find('option:gt(0)').remove();
        product_id.selectpicker('refresh');
        if(product_id.length) {
            $.ajax({
                url : admin_url + 'warehouses/getProductsInWH/' + warehouse_id,
                dataType : 'json',
            })
            .done(function(data){         
                $.each(data, function(key,value){

                    product_id.append('<option data-store="'+value.product_quantity+'" value="' + value.product_id + '">'+'('+ value.code +') '  + value.name + '</option>');
                });
                product_id.selectpicker('refresh');
            });
        }
    }


    //format currency
    function formatNumber(nStr, decSeperate=".", groupSeperate=",") {
        nStr += '';
        x = nStr.split(decSeperate);
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + groupSeperate + '$2');
        }
        return x1 + x2;
    }

    var findItem = (id) => {
        var itemResult;
        $.each(itemList, (index,value) => {
            if(value.id == id) {
                itemResult = value;
                return false;
            }
        });
        return itemResult;
    };
    var total = <?php echo $totalQuantity ?>;
    var totalPrice = <?php echo $totalPrice ?>;
    var uniqueArray = <?php echo $i ?>;
    var isNew = false;
    var createTrItem = () => {
        if(!isNew) return;
        if( $('table.item-purchase tbody tr:gt(0)').find('input[value=' + $('tr.main').find('td:nth-child(1) > input').val() + ']').length ) {
            $('table.item-purchase tbody tr:gt(0)').find('input[value=' + $('tr.main').find('td:nth-child(1) > input').val() + ']').parent().find('td:nth-child(2) > input').focus();
            alert('Sản phẩm này đã được thêm, vui lòng lòng kiểm tra lại!');
            return;
        }
        var newTr = $('<tr class="sortable item"></tr>');
        
        var td1 = $('<td><input type="hidden" name="items[' + uniqueArray + '][id]" value="" /></td>');
        var td2 = $('<td class="dragger"></td>');

        var td3 = $('<td></td>');
        var td4 = $('<td></td>');

        var td5 = $('<td></td>');
        var td6 = $('<td><input class="mainQuantity" type="number" name="items[' + uniqueArray + '][quantity]" value="" /></td>');
        var td7 = $('<td></td>');
        var td8 = $('<td></td>');

        td1.find('input').val($('tr.main').find('td:nth-child(1) > input').val());
        td2.text($('tr.main').find('td:nth-child(2)').text());

        var selectTd3 = $('tr.main').find('td:nth-child(3) select').clone();
        selectTd3.val($('tr.main').find('td:nth-child(3) select').selectpicker('val'));
        selectTd3.removeAttr('id');
        var tk_no='items['+uniqueArray+'][tk_no]';
        selectTd3.attr('name',tk_no);
        td3.append(selectTd3);


        var selectTd4 = $('tr.main').find('td:nth-child(4) select').clone();
        selectTd4.val($('tr.main').find('td:nth-child(4) select').selectpicker('val'));
        selectTd4.removeAttr('id');
        var tk_co='items['+uniqueArray+'][tk_co]';
        selectTd4.attr('name',tk_co);
        td4.append(selectTd4);

        td5.text($('tr.main').find('td:nth-child(5)').text());
        td6.find('input').val($('tr.main').find('td:nth-child(6) > input').val());
        
        td7.text( $('tr.main').find('td:nth-child(7)').text() );
        td8.text( $('tr.main').find('td:nth-child(8)').text() );
        
        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td6);
        newTr.append(td7);
        newTr.append(td8);

        newTr.append('<td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td');
        $('table.item-purchase tbody').append(newTr);
        total+=parseFloat($('tr.main').find('td:nth-child(6) > input').val());
        totalPrice += $('tr.main').find('td:nth-child(4) > input').val() * $('tr.main').find('td:nth-child(5)').text().replace(/\+/g, ' ');
        uniqueArray++;
        // selectpicker
        newTr.find('.selectpicker').selectpicker('refresh');
        refreshTotal();
        // refreshAll();
    };
    var refreshAll = () => {
        isNew = false;
        $('#btnAdd').hide();
        $('#custom_item_select').val('');
        $('#custom_item_select').selectpicker('refresh');
        var trBar = $('tr.main');
        //console.log(trBar.find('td:nth-child(2) > input'));
        
        trBar.find('td:first > input').val("");
        trBar.find('td:nth-child(2) > input').val('');
        trBar.find('td:nth-child(3) > input').val(1);
        trBar.find('td:nth-child(4) > input').val('');
        trBar.find('td:nth-child(5) > textarea').text('');


    };
    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        totalPrice -= current.find('td:nth-child(4) > input').val() * current.find('td:nth-child(5)').text().replace(/\,/g, '');
        total-=current.find('td:nth-child(4) > input').val();
        $(trItem).parent().parent().remove();
        
        refreshTotal();
    };
    var refreshTotal = () => {
        $('.total').text(formatNumber(total));
        var items = $('table.item-purchase tbody tr:gt(0)');
        totalPrice = 0;
        $.each(items, (index,value)=>{
            totalPrice += parseFloat($(value).find('td:nth-child(11)').text().trim().replace(/\,/g, ''));
        });
        $('.totalPrice').text(formatNumber(totalPrice));
    };
    var refreshQuantity = () => {
        var items = $('table.item-purchase tbody tr:gt(0)');
        total = 0;
        $.each(items, (index,value)=>{
            total += parseFloat($(value).find('input.mainQuantity').val());
        });

        $('.total').text(formatNumber(total));
    };
    $('#custom_item_select').change((e)=>{
        var id = $(e.currentTarget).val();
        var itemFound = findItem(id);
        if(typeof(itemFound) != 'undefined') {
            var trBar = $('tr.main');
            //console.log(trBar.find('td:nth-child(2) > input'));
            
            trBar.find('td:first > input').val(itemFound.id);
            trBar.find('td:nth-child(2)').text(itemFound.name);

            trBar.find('td:nth-child(5)').text(itemFound.unit_name);
            trBar.find('td:nth-child(5) > input').val(itemFound.unit);
            trBar.find('td:nth-child(6) > input').val(1);
            trBar.find('td:nth-child(7)').text(formatNumber(itemFound.price_buy));
            trBar.find('td:nth-child(8)').text(  formatNumber(itemFound.price_buy * 1) );
            isNew = true;
            $('#btnAdd').show();
        }
        else {
            isNew = false;
            $('#btnAdd').hide();
        }
    });
    $(document).on('keyup', '.mainQuantity,.quantity',(e)=>{
        
        var currentQuantityInput = $(e.currentTarget);
             
        var Giatd = currentQuantityInput.parent().find(' + td');
        var Tong = Giatd.find(' + td');  
        var taxtd = currentQuantityInput.parent().find(' + td + td + td');
        var discounttd = currentQuantityInput.parent().find(' + td + td + td + td');
        var amounttd = currentQuantityInput.parent().find(' + td + td + td + td + td');
        
        var Gia=Giatd.text().trim().replace(/\,/g, '');
        var Tygia=Giatd.find('input.exchange_rate').val();
        var discount_percent=Giatd.find('input.discount_percent').val();
        var tax_rate=Giatd.find('input.tax_rate').val();

        var tong=Gia * currentQuantityInput.val();
        var tax=tong * tax_rate/100;
        var discount=tong * discount_percent/100;
        var amount=tong + tax - discount;

        Tong.text(formatNumber(tong));
        taxtd.text(formatNumber(tax));
        discounttd.text(formatNumber(discount));
        amounttd.text(formatNumber(amount));
        refreshQuantity();
        refreshTotal();
    });
    $('#warehouse_type').change(function(e){
      var warehouse_type = $(e.currentTarget).val();
      // $('table tr.sortable.item').remove();
      loadWarehouses(warehouse_type,'');
    });
    function loadWarehouses(warehouse_type,default_value=''){
        var warehouse_id=$('#warehouse_id');
        warehouse_id.find('option').remove()
        warehouse_id.selectpicker("refresh");
        if(warehouse_id != 0 && warehouse_id != '') {
        $.ajax({
          url : admin_url + 'warehouses/getWarehouses/' + warehouse_type,
          dataType : 'json',
        })
        .done(function(data){          
          warehouse_id.find('option').remove();
          warehouse_id.append('<option value=""></option>');
          $.each(data, function(key,value){
            var stringSelected = "";
            if(value.warehouseid == default_value) {
              stringSelected = ' selected="selected"';
            }
            warehouse_id.append('<option value="' + value.warehouseid + '"'+stringSelected+'>' + value.warehouse + '</option>');
          });
          warehouse_id.selectpicker('refresh');
        });
      }
    }

    $('.customer-form-submiter').on('click', function(e){
        var warehouse_id=$('#warehouse_id').val();
        var tk=$('select[name^="items"]');
        if(!warehouse_id)
        {
            alert_float('danger', "Vui lòng chọn kho chứa sản phẩm!");
            e.preventDefault(); 
        }
        $.each(tk, function(key,value){
        if($(value).val()=='')
        {
            alert_float('danger', "Vui lòng chọn tài khoản hạch toán!");
            e.preventDefault();
            
            return;
        }
        });
    });
        
</script>
</body>
</html>
