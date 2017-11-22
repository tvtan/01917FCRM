<?php init_head(); ?>
<style type="text/css">
</style>
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
  <h4 class="bold no-margin"><?php echo (isset($item) ? _l('quote_edit') : _l('quote_add')); ?></h4>
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
            }
            else
            {
                $type='warning';
                $status='Phiếu mới';
            }

        ?>
        <div class="ribbon <?=$type?>"><span><?=$status?></span></div>
        <ul class="nav nav-tabs profile-tabs" role="tablist">
            <li role="presentation" class="">
                <a href="#" aria-controls="item_detail" role="tab" data-toggle="tab">
                </a>
            </li>
        </ul>
        <div class="tab-content">
            <div role="tabpanel" class="tab-pane " id="item_detail"></div>
        </div>
            <div class="row">
                
                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                    
                </div>
                
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3 _buttons">
                    <div class="pull-right">
                        <?php if( isset($item) ) { ?>
                        <a href="<?php echo admin_url('quotes/pdf/' . $item->id . '?print=true') ?>" target="_blank" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="In" aria-describedby="tooltip652034"><i class="fa fa-print"></i></a>
                        <a href="<?php echo admin_url('quotes/pdf/' . $item->id  ) ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Xem PDF"><i class="fa fa-file-pdf-o"></i></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            
            <?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'sales-form', 'autocomplete' => 'off')); ?>
                <div class="row">
                  <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">            
                    <?php
                    $attrs_not_select = array('data-none-selected-text' => _l('system_default_string'));
                    ?>
                    <div class="form-group">
                         <label for="number"><?php echo _l('quote_code'); ?></label>
                         <div class="input-group">
                          <span class="input-group-addon">
                          <?php $prefix =($item) ? $item->prefix : get_option('prefix_quote'); ?>
                            <?=$prefix?>
                            <?=form_hidden('prefix',$prefix)?>    
                            </span>
                            <?php 
                                if($item)
                                {
                                    $number=$item->code;
                                }
                                else
                                {
                                    $number=sprintf('%06d',getMaxID('id','tblquotes')+1);
                                }
                            ?>
                            <input type="text" name="code" class="form-control" id="code" value="<?=$number ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" readonly>
                          </div>
                    </div>

                    <?php $value = (isset($item) ? _d($item->date) : _d(date('Y-m-d')));?>
                    <?php echo render_date_input('date','view_date',$value,array('readonly'=>true)); ?>
                    
                    <?php
                    $default_name = (isset($item) ? $item->name : _l('quote_name'));
                    echo form_hidden('name', _l('quote_name'), $default_name);
                    ?>
                    <?php
                    $selected=(isset($item) ? $item->customer_id : '');
                    echo render_select3('customer_id',$customersCBO,array('userid','company','code,phonenumber,mobilephone_number,subtext'),'client',$selected,$frmattrs);
                    ?>

                    <div class="checkbox checkbox-primary">
                        <input type="checkbox" value="1" <?=$item->isVisibleTax==1?'checked':''?> name="isVisibleTax" id="isVisibleTax">
                        <label for="isVisibleTax" data-toggle="tooltip" data-original-title="" title=""><?=_l('display_tax')?></label>
                    </div>

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
                                    echo render_select('warehouse_name', $warehouses, array('warehouseid', 'warehouse'),'warehouse_name',$warehouse_type_id);
                                ?>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group mbot25">
                                    <label for="custom_item_select" class="control-label"><?=_l('item_name')?></label>
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

                        </div>
                        <div class="table-responsive s_table" style="overflow-x: auto;overflow-y: hidden;padding-bottom: 200px">
                            <table class="table items item-export no-mtop" border="">
                                <thead>
                                    <tr>
                                        <th><input type="hidden" id="itemID" value="" /></th>
                                        <th style="min-width: 200px" class="text-left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_name'); ?>"></i> <?php echo _l('item_name'); ?></th>
                                        <th style="min-width: 80px" class="text-left"><?php echo _l('item_unit'); ?></th>
                                        <th style="min-width: 100px" class="text-left"><?php echo _l('item_quantity'); ?></th>
                                        <th style="min-width: 100px" class="text-left"><?php echo _l('item_price'); ?></th>                                        
                                        <th style="min-width: 100px" class="text-left"><?php echo _l('tax'); ?></th>
                                        <th style="min-width: 100px" class="text-left"><?php echo _l('amount'); ?></th>
                                        <th style="min-width: 80px" class="text-left"><?php echo _l('discount').'(%)'; ?></th>
                                        <th style="min-width: 100px" class="text-left"><?php echo _l('discount_money'); ?></th>
                                        <th style="min-width: 100px" class="text-left"><?php echo _l('sub_amount'); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <tr class="main">
                                        <td><input type="hidden" id="itemID" value="" /></td>
                                        <td>
                                            <?php echo _l('item_name'); $totalQuantity=0;?>
                                        </td>
                                        <td>
                                            <input type="hidden" class="unitPrice" value="0" />
                                            <?php echo _l('item_unit'); ?>
                                        </td>

                                        <td>
                                            <input style="width: 100px" class="mainQuantity" type="number" min="1" value="1"  class="form-control" placeholder="<?php echo _l('item_quantity'); ?>">
                                        </td>
                                        
                                        <td>
                                            <?php echo _l('item_price'); ?>
                                        </td>
                                        
                                        <td>
                                            <?php echo _l('tax'); ?>
                                            <input type="hidden" id="tax" data-taxid="" data-taxrate="" value="" />
                                        </td>

                                        <td>
                                            0
                                        </td>
                                        
                                        <td>
                                            <input style="width: 100px" class="discount_percent" type="number" min="0" value="0"  class="form-control" placeholder="<?php echo _l(''); ?>">
                                        </td>
                                        <td>
                                            <input style="width: 100px" class="discount" type="number" min="0" value="0"  class="form-control" placeholder="<?php echo _l(''); ?>">
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
                                    // $totalPrice=0;
                                    if(isset($item) && count($item->items) > 0) {
                                        
                                        foreach($item->items as $value) {
                                        ?>
                                    <tr class="sortable item">
                                        <td>
                                            <input type="hidden" name="items[<?php echo $i; ?>][id]" value="<?php echo $value->product_id; ?>">
                                        </td>
                                        <td class="dragger"><?php echo $value->product_name; ?></td>
                                        <td><?php echo $value->unit_name; ?>
                                            <input type="hidden" class="unitPrice" name="items[<?php echo $i; ?>][unitPrice]" value="<?=$value->unit_cost?>">

                                        </td>
                                        <?php
                                        $err='';
                                            if($value->quantity>$value->warehouse_type->product_quantity)
                                            {
                                                $err='error';
                                                $style='border: 1px solid red !important';
                                            }
                                        ?>
                                        <td>
                                        <input style="width: 100px;" class="mainQuantity" type="number" name="items[<?php echo $i; ?>][quantity]" value="<?php echo $value->quantity; ?>">
                                        </td>
                                            
                                        <td><?php echo number_format(getUnitPrice($value->unit_cost,$value->tax_rate)); ?></td>
                                        
                                        <td><?php echo number_format($value->tax) ?>
                                            <input type="hidden" id="tax" data-taxrate="<?=$value->tax_rate?>" value="<?=$value->tax_id?>">
                                        </td>
                                        <td><?php echo number_format($value->sub_total); ?></td>
                                        <td>
                                            <input style="width: 100px;" name="items[<?=$i?>][discount_percent]" class="discount_percent" type="number" value="<?php echo $value->discount_percent; ?>">
                                        </td>
                                        <td>
                                            <input style="width: 100px;" name="items[<?=$i?>][discount]" class="discount" type="number" value="<?php echo $value->discount; ?>">
                                        </td>
                                        <td><?php echo number_format($value->amount) ?></td>
                                        <td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>
                                    </tr>
                                        <?php
                                            $totalPrice += $value->amount;
                                            $totalQuantity+=$value->quantity;

                                            $i++;
                                        }
                                        $discount=$item->discount;
                                        $adjustment=$item->adjustment;
                                        $grand_total=$item->total;
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-10 col-md-offset-2">
                            <table class="table text-right">
                                <tbody>
                                    <tr>
                                        <td><span class="bold"><?php echo _l('purchase_total_items_quantity'); ?> :</span>
                                        </td>
                                        <td colspan="2" class="total">
                                            <?php echo $totalQuantity ?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="bold"><?php echo _l('discount_percent_total'); ?> :</span>
                                            <div class="form-group">
                                                <div class="radio radio-success radio-inline">
                                                    <input type="radio" name="isDiscountAfter" value="0" checked id="isBefore">
                                                    <label for="administrator"><?=_l('discount_before_tax')?></label>
                                                </div>
                                                <div class="radio radio-success radio-inline">
                                                    <input type="radio" name="isDiscountAfter" <?=$item->isDiscountAfter===1?'checked':''?> value="1" id="isAfter">
                                                    <label for="administrator"><?=_l('discount_after_tax')?></label>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="input-group">
                                              <input type="text" name="discount_percent" id="discount_percent" min="0" class="form-control" placeholder="Phần trăm giảm giá" aria-describedby="basic-addon2" value="<?=$item->discount_percent?$item->discount_percent:0?>">
                                              <span class="input-group-addon" id="basic-addon2">%</span>
                                            </div>
                                        </td>
                                        <td class="discount_percent_total">
                                            <?=format_money($discount?$discount:0)?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="bold"><?php echo _l('adjustment_total'); ?> :</span>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                              <input type="number" name="adjustment" id="adjustment" class="form-control" placeholder="Giá trị điều chỉnh" aria-describedby="basic-addon2" value="<?=$adjustment?>">
                                              <!-- <span class="input-group-addon" id="basic-addon2"><?=($currency->symbol)?$currency->symbol:_l('VNĐ')?></span> -->
                                            </div>
                                        </td>
                                        <td class="adjustment_total">
                                            <?=format_money($adjustment?$adjustment:0)?>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="bold"><?php echo _l('purchase_total_price'); ?> :</span>
                                        </td>
                                        <td colspan="2" class="totalPrice">
                                            <?php echo number_format($grand_total) ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                    </div>
                    <!-- End Customize from invoice -->
                </div>
                  <button class="btn btn-info mtop20 only-save customer-form-submiter" style="margin-left: 15px">
                    <?php echo _l('submit'); ?>
                </button>
              </div>
            <?php echo form_close(); ?>
          

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
    _validate_form($('.sales-form'),{code:'required',date:'required',customer_id:'required'});
    
    var itemList = <?php echo json_encode($items);?>;
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
        if(!$('div #warehouse_name option:selected').length || $('div #warehouse_name option:selected').val() == '') {
            alert_float('danger', "Vui lòng chọn kho chứa sản phẩm!");
            return;
        }
        if( $('table.item-export tbody tr:gt(0)').find('input[value=' + $('tr.main').find('td:nth-child(1) > input').val() + ']').length ) {
            $('table.item-export tbody tr:gt(0)').find('input[value=' + $('tr.main').find('td:nth-child(1) > input').val() + ']').parent().find('td:nth-child(2) > input').focus();
            alert_float('danger', "Sản phẩm này đã được thêm, vui lòng lòng kiểm tra lại!");
            return;
        }
        if($('tr.main').find('td:nth-child(4) > input').val() > $('tr.main #warehouse_name option:selected').data('store')) {
            alert_float('danger', 'Kho ' + $('tr.main #select_warehouse option:selected').text() + '. Bạn đã nhập ' + $('tr.main').find('td:nth-child(4) > input').val() + ' là quá số lượng cho phép.');
            return;
        }
        var newTr = $('<tr class="sortable item"></tr>');
        
        var td1 = $('<td><input type="hidden" name="items[' + uniqueArray + '][id]" value="" /></td>');
        var td2 = $('<td class="dragger"></td>');
        var td3 = $('<td></td>');
        var unitPriceInput=$('<input type="hidden" class="unitPrice" name="items[' + uniqueArray + '][unitPrice]" value="" />')
        var td4 = $('<td><input style="width: 100px" class="mainQuantity" type="number" name="items[' + uniqueArray + '][quantity]" value="" /></td>');
        var td5 = $('<td></td>');        
        var td6 = $('<td><input type="hidden" id="tax" data-taxid="" data-taxrate="" value="" /></td>');
        var td7 = $('<td></td>');
        var td8 = $('<td><input style="width: 100px" name="items[' + uniqueArray + '][discount_percent]" class="discount_percent" type="number" min="0" value="0" placeholder=""></td>');
        var td9 = $('<td><input style="width: 100px" name="items[' + uniqueArray + '][discount]" class="discount" type="number" min="0" value="0" placeholder=""></td>');
        var td10 = $('<td></td>');

        td1.find('input').val($('tr.main').find('td:nth-child(1) > input').val());
        td2.text($('tr.main').find('td:nth-child(2)').text());
        td3.text($('tr.main').find('td:nth-child(3)').text());
        unitPriceInput.val($('tr.main').find('td:nth-child(3) > input').val());
        td3.append(unitPriceInput);
        td4.find('input').val($('tr.main').find('td:nth-child(4) > input').val());
        
        td5.text( $('tr.main').find('td:nth-child(5)').text());
        
        var inputTax=$('tr.main').find('td:nth-child(6) > input');
        td6.text( $('tr.main').find('td:nth-child(6)').text());
        td6.append(inputTax);
        td7.text( $('tr.main').find('td:nth-child(7)').text());
        td8.find('input').val($('tr.main').find('input.discount_percent').val());
        
        td9.find('input').val($('tr.main').find('input.discount').val());
        td10.text($('tr.main').find('td:nth-child(10)').text());
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

        newTr.append('<td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td');
        $('table.item-export tbody').append(newTr);
        $('#custom_item_select').selectpicker('toggle');
        total+=parseFloat($('tr.main').find('td:nth-child(4) > input').val());
        totalPrice += $('tr.main').find('td:nth-child(4) > input').val() * $('tr.main').find('td:nth-child(5)').text().replace(/\+/g, ' ');
        uniqueArray++;
        refreshTotal();
        refreshAll();
    };
    var refreshAll = () => {
        isNew = false;
        $('#btnAdd').hide();
        $('#custom_item_select').val('');
        $('#custom_item_select').selectpicker('refresh');
        var trBar = $('tr.main');
        
        trBar.find('td:first > input').val("");
        // trBar.find('td:nth-child(1) > input').val('');
        trBar.find('td:nth-child(2)').text("<?=_l('item_name')?>");
        var unitPriceInput=trBar.find('td:nth-child(3) > input');
        unitPriceInput.val(0);
        trBar.find('td:nth-child(3)').text("<?=_l('item_unit')?>");
        trBar.find('td:nth-child(3)').append(unitPriceInput);
        trBar.find('td:nth-child(4) > input').val('1');
        trBar.find('td:nth-child(5)').text("<?=_l('item_price')?>");
        trBar.find('td:nth-child(6)').text(0);
        trBar.find('td:nth-child(7)').text("<?=_l('tax')?>");
        trBar.find('td:nth-child(8) > input').val('0');
        trBar.find('td:nth-child(9) > input').val('0');
        trBar.find('td:nth-child(10)').text(0);
    };
    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        totalPrice -= current.find('td:nth-child(4) > input').val() * current.find('td:nth-child(5)').text().replace(/\,/g, '');
        total-=current.find('td:nth-child(4) > input').val();
        $(trItem).parent().parent().remove();
        
        refreshTotal();
    };
    var refreshTotal = () => {
        $('.total').text(formatNumberDec(total));
        var items = $('table.item-export tbody tr:gt(0)');
        var isDiscountAfter=$('input[name=isDiscountAfter]:checked').val();
        totalPrice = 0;
        if(isDiscountAfter==1)
        {
            $.each(items, (index,value)=>{
                totalPrice += parseFloat($(value).find('td:nth-child(10)').text().replace(/\,/g, ''));
            });
        }
        else
        {
            $.each(items, (index,value)=>{
                let quantity=parseFloat($(value).find('td:nth-child(4) input').val());
                if(isNaN(quantity)) quantity=0;
                let price=parseFloat($(value).find('td:nth-child(5)').text().replace(/\,/g, ''));
                if(isNaN(price)) quantity=0;
                totalPrice += quantity*price;
            });
        }
        var discount_percent=parseFloat($('#discount_percent').val());
        var discount=discount_percent*totalPrice/100;
        var adjustment=parseFloat($('#adjustment').val());
        if(isNaN(adjustment)) adjustment=0;
        var grand_total=totalPrice-discount+(adjustment);
        
        $('.discount_percent_total').text(formatNumberDec(discount));
        $('.totalPrice').text(formatNumberDec(grand_total));
    };

    var refreshQuantity = () => {
        var items = $('table.item-export tbody tr:gt(0)');
        total = 0;
        $.each(items, (index,value)=>{
            total += parseFloat($(value).find('input.mainQuantity').val());
        });
        $('.total').text(formatNumberDec(total));
    };


    $('#warehouse_name').change(function(e){
        $('table tr.sortable.item').remove();
        total=0;
        var warehouse_id=$(this).val();
        loadProductsInWarehouse(warehouse_id)
        refreshAll();
        refreshTotal();
    });
    $('#customer_id').change(function(e){
        var customer_id=$(this).val();
        var data={};
        data.customer_id=customer_id;
        var url=admin_url + 'clients/getClientByID';
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            var discount_percent=response.success.discount_percent;
            if(discount_percent==null) discount_percent=0;
            $('#discount_percent').val(discount_percent);
            refreshTotal();
        });
    });

    $('#custom_item_select').change((e)=>{
        var id = $(e.currentTarget).val();
        var itemFound = findItem(id);

        if(typeof(itemFound) != 'undefined') {
            var trBar = $('tr.main');
            
            trBar.find('td:first > input').val(itemFound.id);
            trBar.find('td:nth-child(2)').text(itemFound.name+' ('+itemFound.prefix+itemFound.code+')');
            var unitPriceInput=trBar.find('td:nth-child(3) > input');
            unitPriceInput.val(itemFound.price);
            trBar.find('td:nth-child(3)').text(itemFound.unit_name);
            trBar.find('td:nth-child(3)').append(unitPriceInput);
            trBar.find('td:nth-child(4) > input').val(1);
            trBar.find('td:nth-child(5)').text(formatNumberDec(getUnitPrice(itemFound.price,itemFound.tax_rate)));
            var inputTax = $('<input type="hidden" id="tax" data-taxrate="'+itemFound.tax_rate+'" value="'+itemFound.tax+'" />');
            trBar.find('td:nth-child(6)').text(formatNumberDec(getUnitPrice(itemFound.price,itemFound.tax_rate,false)));
            trBar.find('td:nth-child(6)').append(inputTax);
            trBar.find('td:nth-child(7)').text(formatNumberDec(itemFound.price));
            trBar.find('td:nth-child(8) > input').val(0);
            trBar.find('td:nth-child(9) > input').val(0);
            trBar.find('td:nth-child(10)').text(formatNumberDec(parseFloat(itemFound.price)));
            isNew = true;
            $('#btnAdd').show();
        }
        else {
            isNew = false;
            $('#btnAdd').hide();
        }
    });
    $('#select_warehouse').on('change', (e)=>{
        if($(e.currentTarget).val() != '') {
            $(e.currentTarget).parents('tr').find('input.mainQuantity').attr('data-store', $(e.currentTarget).find('option:selected').data('store'));
        }
    });

    
    $(document).on('keyup', '.mainQuantity', (e)=>{
        var currentQuantityInput = $(e.currentTarget);
        let elementToCompare;
        if(typeof(currentQuantityInput.attr('data-store')) == 'undefined' )
            elementToCompare = currentQuantityInput.parents('tr').find('input[data-store]');
        else
            elementToCompare = currentQuantityInput;
        // console.log(elementToCompare.val())
        if(parseInt(currentQuantityInput.val()) > parseInt(elementToCompare.attr('data-store'))){
            currentQuantityInput.attr("style", "width: 100px;border: 1px solid red !important");
            currentQuantityInput.attr('data-toggle', 'tooltip');
            currentQuantityInput.attr('data-trigger', 'manual');
            currentQuantityInput.attr('title', 'Số lượng vượt mức cho phép!');
            // $('[data-toggle="tooltip"]').tooltip();
            currentQuantityInput.off('focus', '**').off('hover', '**');
            currentQuantityInput.tooltip('fixTitle').focus(()=>$(this).tooltip('show')).hover(()=>$(this).tooltip('show'));
            // error flag
            currentQuantityInput.addClass('error');
            currentQuantityInput.focus();
        }
        else {
            currentQuantityInput.attr('title', 'OK!').tooltip('fixTitle').tooltip('show');
            currentQuantityInput.attr("style", "width: 100px;");
            // remove flag
            currentQuantityInput.removeClass('error');
            currentQuantityInput.focus();
        }        
        refreshQuantity();
        calculateTotal(e.currentTarget);
        refreshTotal();
    });
    var calculateTotal = (currentInput) => {
        currentInput = $(currentInput);   
        let quantity = currentInput.parents('tr').find('.mainQuantity');
        let quantityTd = quantity.parent();
        var unitPriceInput=currentInput.parents('tr').find('.unitPrice');
        let priceTd = quantityTd.find('+ td');

        let taxTd=priceTd.find('+ td');
        var inputTax=taxTd.find('input')
        var tax=getUnitPrice(unitPriceInput.val(),inputTax.attr('data-taxrate'),false)*quantity.val();
        if(isNaN(tax)) tax=0;
        taxTd.text(formatNumberDec(tax));
        taxTd.append(inputTax);

        let amountTd = taxTd.find('+ td');
        var amount=unitPriceInput.val()*quantity.val();
        if(isNaN(amount)) amount=0;
        amountTd.text(formatNumberDec(amount));

        let discountPercent=currentInput.parents('tr').find('.discount_percent');

        let discount=currentInput.parents('tr').find('.discount');
        var discountTd=discount.parent();
        var discountValue=amount*discountPercent.val()/100;
        discount.val(discountValue);

        let subTotalTd=discountTd.find('+ td');
        subTotalTd.text(formatNumberDec(amount-discountValue));
        refreshTotal();

    };
    $(document).on('keyup', '.discount', (e)=>{
        var currentDiscountInput = $(e.currentTarget);
        var discount_percent=currentDiscountInput.parents('td').prev().find('input');
        var tong=currentDiscountInput.parents('tr').find('.mainQuantity').parents().find('+ td + td +td').text().trim().replace(/\,|%/g, '');
        discount_percent.val(currentDiscountInput.val()*100/tong);
        calculateTotal(e.currentTarget);
    });
    $(document).on('keyup', '.discount_percent', (e)=>{
        var currentDiscountPercentInput = $(e.currentTarget);
        calculateTotal(e.currentTarget);
    });
    $(document).on('blur', '#adjustment', (e)=>{
        var currentInput = $(e.currentTarget);
        var adjustment=currentInput.val();
        if(!adjustment || isNaN(adjustment)) adjustment=0;
        $('.adjustment_total').text(formatNumberDec(adjustment));
        calculateTotal(e.currentTarget);
    });
    $(document).on('blur', '#discount_percent', (e)=>{
        var currentInput = $(e.currentTarget);
        calculateTotal(e.currentTarget);
    });
    $('#select_kindof_warehouse').change(function(e){
        var warehouse_type = $(e.currentTarget).val();
        var product = $(e.currentTarget).parents('tr').find('td:first input');
        if(warehouse_type != '' && product.val() != '') {
            loadWarehouses(warehouse_type,product.val()); 
        }
    });
    $('#warehouse_type').change(function(e){
        var warehouse_type = $(e.currentTarget).val();
        if(warehouse_type != '') {
            getWarehouses(warehouse_type); 
        }
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

    function getWarehouses(warehouse_type){
        var warehouse_id=$('#warehouse_name');
        warehouse_id.find('option:gt(0)').remove();
        warehouse_id.selectpicker('refresh');
        if(warehouse_id.length) {
            $.ajax({
                url : admin_url + 'warehouses/getWarehouses/' + warehouse_type ,
                dataType : 'json',
            })
            .done(function(data){  
                $.each(data, function(key,value){
                    warehouse_id.append('<option value="' + value.warehouseid +'">' + value.warehouse + '</option>');
                });

                warehouse_id.selectpicker('refresh');
            });
        }
    }
    function loadWarehouses(warehouse_type, filter_by_product,default_value=''){
        var warehouse_id=$('#select_warehouse');
        warehouse_id.find('option:gt(0)').remove();
        warehouse_id.selectpicker('refresh');
        if(warehouse_id.length) {
            $.ajax({
                url : admin_url + 'warehouses/getWarehouses/' + warehouse_type + '/' + filter_by_product,
                dataType : 'json',
            })
            .done(function(data){          
                $.each(data, function(key,value){
                    var stringSelected = "";
                    if(value.warehouseid == default_value) {
                        stringSelected = ' selected="selected"';
                    }
                    warehouse_id.append('<option data-store="'+value.items[0].product_quantity+'" value="' + value.warehouseid + '"'+stringSelected+'>' + value.warehouse + '(có '+value.items[0].product_quantity+')</option>');
                });
                warehouse_id.selectpicker('refresh');
            });
        }
    }
    $('.customer-form-submiter').on('click', (e)=>{
        if($('input.error').length) {
            e.preventDefault();
            alert('Giá trị không hợp lệ!');    
        }
        
    });
    function getTotalPrice()
    {
        var totalPrice=0;

        return totalPrice;
    }

    $('input[name=isDiscountAfter]').change(function(e){
        var isDiscountAfter=$(this).val();
        refreshTotal();
    });
    
</script>
</body>
</html>
