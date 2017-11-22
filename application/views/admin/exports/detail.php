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
  <h4 class="bold no-margin"><?php echo (isset($item) ? _l('edit_export_order') : _l('add_export_order')); ?></h4>
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
            <li role="presentation" class="active">
                <a href="#item_detail" aria-controls="item_detail" role="tab" data-toggle="tab">
                    <?php echo _l('export_detail'); ?>
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
                        <a href="<?php echo admin_url('exports/pdf/' . $item->id . '?print=true') ?>" target="_blank" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="In" aria-describedby="tooltip652034"><i class="fa fa-print"></i></a>
                        <a href="<?php echo admin_url('exports/pdf/' . $item->id  ) ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Xem PDF"><i class="fa fa-file-pdf-o"></i></a>
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
                         <label for="number"><?php echo _l('export_code'); ?></label>
                         <div class="input-group">
                          <span class="input-group-addon">
                          <?php $prefix =($item) ? $item->prefix : get_option('prefix_export'); ?>
                            <?=$prefix?>
                            <?php echo form_hidden('rel_type', (($item->rel_type)? $item->rel_type :'export_warehouse_transfer')); ?>
                            <?=form_hidden('prefix',$prefix)?>    
                            </span>
                            <?php 
                                if($item)
                                {
                                    $number=$item->code;
                                }
                                else
                                {
                                    $number=sprintf('%06d',getMaxID('id','tblexports')+1);
                                }
                            ?>
                            <input type="text" name="code" class="form-control" id="code" value="<?=$number ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" readonly>
                          </div>
                    </div>

                    <?php
                    $value= ( isset($item) ? $item->rel_code : ''); 
                    $attrs = array('readonly'=>true);
                    if(!empty('rel_id') || !empty('rel_code'))
                    {
                        $frmattrs['style']="display: none;";
                    }
                    ?>
                    <?php echo render_input( 'rel_code', _l("sale_code"),$value,'text',$attrs,$frmattrs); ?>

                    <?php $value = (isset($item) ? _d($item->date) : _d(date('Y-m-d')));?>
                    <?php echo render_date_input('date','view_date',$value); ?>
                    
                    <?php
                    $default_name = (isset($item) ? $item->name : _l('export_name'));
                    echo form_hidden('name', _l('export_name'), $default_name);
                    ?>

                    <?php
                    $selected=(isset($item) ? $item->customer_id : '');
                    if($item->rel_id)
                    {
                        $arr=array('disabled'=>true);
                        echo form_hidden('customer_id',$selected);
                    }
                    echo render_select3('customer_id',$customersCBO,array('userid','company','phonenumber,mobilephone_number,subtext'),'client',$selected,$arr);
                    ?>

                    <?php
                    $selected=(isset($item) ? $item->receiver_id : '');
                    echo render_select('receiver_id',$receivers,array('staffid','fullname'),'staffs',$selected); 
                    ?>

                    <!-- <?php
                    $selected=(isset($item) ? $warehouse_id : '');
                    echo render_select('warehouse_id',$warehouses,array('warehouseid','warehouse'),'warehouse_name',$selected); 
                    ?> -->

                    <?php 
                    $reason = (isset($item) ? $item->reason : "");
                    echo render_textarea('reason', 'note', $reason, array(), array(), '', 'tinymce');
                    ?>
                </div>

                
                
                
                <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                    <!-- Cusstomize from invoice -->
                    <div class="panel-body mtop10">
                    <?php if(!empty($item->rel_id) || !empty($item->rel_code)){ $display='style="display: none;"';  }?>
                        <div class="row"  >
                        <div class="col-md-4" style="display: none;">
                            <?php 
                                if($item->rel_id)
                                    {
                                        $arr=array('disabled'=>true);
                                        echo form_hidden('warehouse_type',$warehouse_type_id);
                                        echo form_hidden('warehouse_name',$warehouse_id);
                                    }
                            ?>
                        </div>
                            <div class="col-md-4">
                                <?php 
                                    echo render_select('warehouse_name', $warehouses, array('warehouseid', 'warehouse'),'warehouse_name',$warehouse_id,$arr);
                                ?>
                            </div>
                            <div class="col-md-4" <?=$display?>>
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
                        
                            <div class="col-md-5 text-right show_quantity_as_wrapper">
                                
                            </div>
                        </div>
                        <div class="table-responsive s_table" style="overflow-x: auto;overflow-y: hidden;">
                            <table class="table items item-export no-mtop">
                                <thead>
                                    <tr>
                                        <th><input type="hidden" id="itemID" value="" /></th>
                                        <th style="min-width: 200px" class="text-left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_name'); ?>"></i> <?php echo _l('item_name'); ?></th>
                                        <th style="min-width: 80" class="text-left"><?php echo _l('item_unit'); ?></th>
                                        <th style="min-width: 100px" class="text-left"><?php echo _l('item_quantity'); ?></th>
                                        <th style="min-width: 100px" class="text-left"><?php echo _l('quantity_net_'); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <tr class="main" <?=$display?> >
                                        <td><input type="hidden" id="itemID" value="" /></td>
                                        <td>
                                            <?php echo _l('item_name'); ?>
                                        </td>
                                        <td>
                                            <input type="hidden" id="item_unit" value="" />
                                            <?php echo _l('item_unit'); ?>
                                        </td>

                                        <td>
                                            <input style="width:100px" class="mainQuantity" type="number" min="1" value="1"  class="form-control" placeholder="<?php echo _l('item_quantity'); ?>">
                                        </td>

                                        <td>
                                            <input style="width:100px" class="mainQuantityNet" type="number" min="1" value="1"  class="form-control" placeholder="<?php echo _l('item_quantity'); ?>">
                                        </td>
                                        
                                        <td>
                                            <button style="display:none" id="btnAdd" type="button" onclick="createTrItem(); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button>
                                        </td>
                                    </tr>
                                    <?php
                                    
                                    $i=0;
                                    $totalQuantity=0;
                                    if(isset($item) && count($item->items) > 0) {
                                        
                                        foreach($item->items as $value) {
                                        ?>
                                    <tr class="sortable item">
                                        <td>
                                            <input type="hidden" name="items[<?php echo $i; ?>][id]" value="<?php echo $value->product_id; ?>">
                                        </td>
                                        <td class="dragger"><?php echo $value->product_name; ?></td>
                                        <td><?php echo $value->unit_name; ?></td>
                                        <?php
                                        $err='';
                                            if($value->quantity_net>$value->warehouse_type->product_quantity)
                                            {
                                                $err='error';
                                                $style='border: 1px solid red !important';
                                            }
                                            $data_store=$value->warehouse_type->product_quantity;
                                        ?>
                                        <td>
                                            <?php 
                                            $maxQ=getMaxQuanitySOExport($id,$value->product_id)+$value->quantity;
                                            $strminmax='min="1" max="'.$maxQ.'"';
                                            if($maxQ==0)
                                            {
                                                $strminmax='readonly';
                                            }
                                            ?>
                                        <input style="width: 100px;" <?=$strminmax?>  class="mainQuantity" type="number" name="items[<?php echo $i; ?>][quantity]" value="<?php echo $value->quantity; ?>">
                                        </td>

                                        <td>
                                            <?php 
                                            $quantity=($value->quantity_net?$value->quantity_net:$value->quantity); 
                                            ?>
                                        <input style="width: 100px;" <?=$strminmax?>  class="mainQuantityNet" type="number" name="items[<?php echo $i; ?>][quantity_net]" value="<?php echo $quantity; ?>">
                                        </td>
                                         
                                        <td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>
                                    </tr>
                                        <?php
                                            $totalPrice += $value->amount;
                                            $totalQuantity+=$value->quantity;
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
                                        <td colspan="2" class="total">
                                            <?php echo $totalQuantity ?>
                                        </td>
                                    </tr>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- End Customize from invoice -->
                </div>
                
                <?php if(isset($item) && $item->status != 2 || !isset($item)) { ?>
                <?php } ?>
                  <button class="btn btn-info mtop20 only-save customer-form-submiter" style="margin-left: 15px">
                    <?php echo _l('submit'); ?>
                </button>
                
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

    _validate_form($('.sales-form'),{code:'required',date:'required',customer_id:'required',receiver_id:'required'});
    
    var itemList = <?php echo json_encode($items);?>;

    $('#warehouse_name').change(function(e){
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
        if(parseFloat($('tr.main').find('input.mainQuantity').val())>parseFloat($('tr.main').find('input.mainQuantity').attr('data-store')))
        {
            alert_float('danger','Sản phẩm bạn nhập là ['+$('tr.main').find('input.mainQuantity').val()+'] lớn hơn số lượng trong kho ['+$('tr.main').find('input.mainQuantity').attr('data-store')+'], vui lòng lòng kiểm tra lại!');
            return;
        }
        var newTr = $('<tr class="sortable item"></tr>');
        
        var td1 = $('<td><input type="hidden" name="items[' + uniqueArray + '][id]" value="" /></td>');
        var td2 = $('<td class="dragger"></td>');
        var td3 = $('<td></td>');
        var td4 = $('<td><input style="width:100px" class="mainQuantity" type="number" name="items[' + uniqueArray + '][quantity]" value="" /></td>');  
        var td5 = $('<td><input style="width:100px" class="mainQuantityNet" type="number" name="items[' + uniqueArray + '][quantity_net]" value="" /></td>');        
        

        td1.find('input').val($('tr.main').find('td:nth-child(1) > input').val());
        td2.text($('tr.main').find('td:nth-child(2)').text());
        td3.text($('tr.main').find('td:nth-child(3)').text());
        td4.find('input').val($('tr.main').find('td:nth-child(4) > input').val());
        td4.find('input').attr('data-store',$('tr.main').find('td:nth-child(4) > input').attr('data-store'));
        td5.find('input').val($('tr.main').find('td:nth-child(5) > input').val());
        td5.find('input').attr('data-store',$('tr.main').find('td:nth-child(4) > input').attr('data-store'));
        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);

        newTr.append('<td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td');
        $('table.item-export tbody').append(newTr);

        total+=parseFloat($('tr.main').find('td:nth-child(5) > input').val());
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
        trBar.find('td:nth-child(2)').text('Tên hàng hóa');
        trBar.find('td:nth-child(3)').text('Đơn vị tính');
        trBar.find('td:nth-child(4) > input').val('1');
        trBar.find('td:nth-child(4) > input').removeAttr('data-store');
        trBar.find('td:nth-child(5) > input').val('1');
        trBar.find('td:nth-child(5) > input').removeAttr('data-store');
    };
    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        total-=current.find('td:nth-child(5) > input').val();
        $(trItem).parent().parent().remove();
        refreshTotal();
    };
    var refreshTotal = () => {
         $('.total').text(formatNumber(total));
    };
    var refreshQuantity = () => {
        var items = $('table.item-export tbody tr:gt(0)');
        total = 0;
        $.each(items, (index,value)=>{
            total += parseFloat($(value).find('input.mainQuantityNet').val());
        });
        $('.total').text(formatNumber(total));
    };
    $('#custom_item_select').change((e)=>{
        var id = $(e.currentTarget).val();
        var itemFound = findItem(id);
        var warehouse_id=$('#select_warehouse');
        warehouse_id.find('option:gt(0)').remove();
        warehouse_id.selectpicker('refresh');

        if(typeof(itemFound) != 'undefined') {
            var trBar = $('tr.main');
            
            trBar.find('td:first > input').val(itemFound.id);
            trBar.find('td:nth-child(2)').text(itemFound.name+' ('+itemFound.prefix+itemFound.code+')');
            trBar.find('td:nth-child(3)').text(itemFound.unit_name);
            trBar.find('td:nth-child(4) > input').val(1);
            trBar.find('td:nth-child(5) > input').val(1);
            
            isNew = true;
            $('#btnAdd').show();
        }
        else {
            isNew = false;
            $('#btnAdd').hide();
        }
    });
    
    $(document).on('keyup', '.mainQuantity,.mainQuantityNet', (e)=>{
        var currentQuantityInput = $(e.currentTarget);
        let elementToCompare;
        if(typeof(currentQuantityInput.attr('data-store')) == 'undefined' )
            elementToCompare = currentQuantityInput.parents('tr').find('input[aria-label!="Search"]:last');
        else
            elementToCompare = currentQuantityInput;
        if(parseInt(currentQuantityInput.val()) > parseInt(elementToCompare.attr('data-store'))) {
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
        refreshTotal();
    });

    $(document).on('change', '.mainQuantity', (e)=>{
        var currentQuantityInput = $(e.currentTarget);
        quantityNetInput=$(e.currentTarget).parents('tr').find('input.mainQuantityNet');
        quantityNetInput.val(currentQuantityInput.val());
    });


    $('.customer-form-submiter').on('click', (e)=>{
        if($('input.error').length) {
            e.preventDefault();
            alert_float('warning','Giá trị không hợp lệ!');    
        }
    });

    $(document).on('change', '#warehouse_name,#custom_item_select',function(e){
        var warehouse_id=$('#warehouse_name').val();
        var product_id=$('#custom_item_select').val();
        if(warehouse_id.length && product_id.length) {
            $.ajax({
                url : admin_url + 'warehouses/getProductQuantity/' + warehouse_id + '/' + product_id,
                dataType : 'json',
            })
            .done(function(data){
            var quantityMax=data.product_quantity;
            if(isNaN(parseFloat(quantityMax))) quantityMax=0;          
               $('#warehouse_name option:selected').attr('data-store',quantityMax);
               $('tr.main').find('input.mainQuantity').attr('data-store',quantityMax);
               $('tr.main').find('input.mainQuantityNet').attr('data-store',quantityMax);
            });
        }
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
    
</script>
</body>
</html>
