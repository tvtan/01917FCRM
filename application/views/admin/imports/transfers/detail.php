<?php init_head(); ?>
<style type="text/css">
        fieldset 
    {
        border: 1px solid #ddd !important;
        margin: 0;
        xmin-width: 0;
        padding: 10px;       
        position: relative;
        border-radius:4px;
        background-color:#f5f5f5;
        padding-left:10px!important;
    }   
    
        legend
        {
            font-size:14px;
            font-weight:bold;
            margin-bottom: 0px; 
            width: 35%; 
            border: 1px solid #ddd;
            border-radius: 4px; 
            padding: 5px 5px 5px 10px; 
            background-color: #ffffff;
        }
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
        <!-- Product information -->
        

  <h4 class="bold no-margin"><?php echo (isset($item) ? (($item->status==2)?_l('Xem phiếu chuyển kho'):_l('Sửa phiếu chuyển kho')) : _l('Tạo phiếu chuyển kho')); ?></h4>
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
                    <?php echo _l('Chi tiết phiếu chuyển kho'); ?>
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
                         <label for="code"><?php echo _l('code_noo'); ?></label>
                         <div class="input-group">
                          <span class="input-group-addon">
                          <?php $prefix =($item) ? $item->prefix : get_option('prefix_transfer'); ?>
                            <?=$prefix?>
                            <?php echo form_hidden('rel_type', 'transfer'); ?>
                            <?=form_hidden('prefix',$prefix)?>    
                            </span>
                            <?php 
                                if($item)
                                {
                                    $number=$item->code;
                                }
                                else
                                {
                                    $number=sprintf('%06d',getMaxIDCODE('code','tblimports',array('rel_type'=>'transfer'))+1);
                                }
                            ?>
                            <input type="text" name="code" class="form-control" value="<?=$number ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" readonly>
                          </div>
                    </div>

                    

                    <?php $value = (isset($item) ? _d($item->date) : _d(date('Y-m-d')));?>
                    <?php echo render_date_input('date','view_date',$value); ?>

                    <?php $value = (isset($item) ? _d($item->account_date) : _d(date('Y-m-d')));?>
                    <?php echo render_date_input('account_date','account_date',$value); ?>
                    
                    <?php
                    $selected=(isset($item) ? $item->deliver_id : '');
                    echo render_select('deliver_id',$delivers,array('id','name'),'deliver_name',$selected); 
                    ?>

                    <?php
                    $default_name = (isset($item) ? $item->name : "Phiếu chuyển kho");
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
                                <fieldset>
                                    <legend style="white-space: nowrap">Kho chuyển:</legend>
                                     
                                        <?php
                                        $totalQuantity=0;
                                        $selected=(isset($item) ? $warehouse_id : '');
                                        echo render_select('warehouse_id',$warehouses,array('warehouseid','warehouse'),'warehouse_name',$selected); 
                                        ?>
                                </fieldset>
                            </div>
                            <div class="col-md-4">
                                <fieldset>
                                    <legend>Kho nhận:</legend>
                                        
                                        <?php
                                        
                                        $selected=(isset($item) ? $warehouse_id_to : '');
                                        echo render_select('warehouse_id_to',$warehouses,array('warehouseid','warehouse'),'warehouse_name',$selected); 
                                        ?>
                                </fieldset>
                            </div>
                            <div class="col-md-4">
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
                                <div class="form-group mtop0">
                                    <div class="radio radio-primary radio-inline">
                                        <input type="radio" name="is_staff" value="1" checked onchange="viewReceivers(this.value)" >
                                        <label for="is_staff">Nhân viên</label>
                                    </div>
                                    <div class="radio radio-primary radio-inline">
                                        <input type="radio" name="is_staff" value="2" onchange="viewReceivers(this.value)" <?=$item->is_staff==2?'checked':''?>>
                                        <label for="is_staff">Khách hàng</label>
                                    </div>                                    
                                </div>
                                <?php
                                        
                                $selected=(isset($item) ? $item->receiver_id : '');
                                echo render_select('receiver_id',$receivers,array('id','name'),'',$selected); 
                                ?>
                            </div>
                            
                            <div class="col-md-5 text-right show_quantity_as_wrapper">

                            </div>
                        </div>
                        

                        <div class="table-responsive s_table mtop10" style="overflow-x: auto;overflow-y: hidden;min-height: 500px">
                            <table class="table items item-purchase no-mtop">
                                <thead>
                                    <tr>
                                        <th><input type="hidden" id="itemID" value="" /></th>
                                        <th style="min-width: 200px" class="text-left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_name'); ?>"></i> <?php echo _l('item_name'); ?></th>
                                        <th width="" class="text-left <?=$view_account['class']?>"><?php echo _l('tk_no'); ?></th>
                                        <th width="" class="text-left <?=$view_account['class']?>"><?php echo _l('tk_co'); ?></th>
                                        <!-- <th width="" class="text-left"><?php echo _l('entered_price'); ?></th> -->
                                        <th width="10%" class="text-left"><?php echo _l('item_unit'); ?></th>
                                        <th width="15%" class="text-left"><?php echo _l('item_quantity'); ?></th>
                                        <th style="min-width: 80px" class="text-left"><?php echo _l('quantity_net'); ?></th>
                                        <th width="10%" class="text-left <?=$view_price['class']?>"><?php echo _l('item_price'); ?></th>
                                        <th width="10%" class="text-left <?=$view_price['class']?>"><?php echo _l('purchase_total_price'); ?></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <tr class="main">
                                        <td><input type="hidden" id="itemID" value="" /></td>
                                        <td>
                                            <?php echo _l('item_name'); ?>
                                        </td>
                                        <!-- TKno -->
                                        <td class="<?=$view_account['class']?>">
                                            <?php
                                            $selected=(isset($item) ? $item->tk_no : '');
                                            echo render_select('tk_no',$accounts_no,array('idAccount','accountCode','accountName'),'',$selected); 
                                            ?>
                                        </td>
                                        <!-- TKCo -->

                                        <td class="<?=$view_account['class']?>">
                                            <?php
                                            $selected=(isset($item) ? $item->tk_co : '');
                                            echo render_select('tk_co',$accounts_co,array('idAccount','accountCode','accountName'),'',$selected); 
                                            ?>
                                        </td>
                                        <!-- <td>
                                            <input class="entered_price" type="number"  value="0"  class="form-control" placeholder="<?php echo _l('item_price'); ?>">
                                        </td> -->
                                        <td>
                                            <input type="hidden" id="item_unit" value="" />
                                            <?php echo _l('item_unit'); ?>
                                        </td>

                                        <td>
                                            <input style="width: 100%;" class="mainQuantity" type="number"  value="1"  class="form-control" placeholder="<?php echo _l('item_quantity'); ?>">
                                        </td>

                                        <td>
                                            <input style="width: 100%;" class="mainQuantityNet" type="number"  value="1"  class="form-control" placeholder="<?php echo _l('item_quantity'); ?>">
                                        </td>
                                        
                                        <td class="<?=$view_price['class']?>">
                                            <?php echo _l('item_price'); ?>
                                        </td>
                                        <td class="<?=$view_price['class']?>">
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
                                        <td class="<?=$view_account['class']?>">
                                            <?php
                                            $selected=(isset($value) ? $value->tk_no : '');
                                            echo render_select('items['.$i.'][tk_no]',$accounts_no,array('idAccount','accountCode','accountName'),'',$selected); 
                                            ?>
                                        </td>
                                        <!-- TK CO -->
                                        <td class="<?=$view_account['class']?>">
                                            <?php
                                            $selected=(isset($value) ? $value->tk_co : '');
                                            echo render_select('items['.$i.'][tk_co]',$accounts_co,array('idAccount','accountCode','accountName'),'',$selected); 
                                            ?>
                                        </td>
                                        <!-- <td>
                                            <input class="entered_price" type="number"  name="items[<?php echo $i; ?>][entered_price]" value="<?php echo $value->entered_price; ?>">
                                        </td> -->
                                        <td><?php echo $value->unit_name; ?></td>
                                        <?php
                                            $max=$value->quantity-$value->quantity_net;
                                         ?>
                                        <td><input style="width: 100%;" class="mainQuantity" type="number"  name="items[<?php echo $i; ?>][quantity]" value="<?php echo $value->quantity; ?>">
                                        </td>

                                        <td><input style="width: 100%;" class="mainQuantityNet" type="number"  name="items[<?php echo $i; ?>][quantity_net]" value="<?php echo $value->quantity_net; ?>">
                                        </td>
                                        <td class="<?=$view_price['class']?>"><?php echo number_format($value->unit_cost); ?></td>
                                        <td class="<?=$view_price['class']?>"><?php echo number_format($value->sub_total); ?></td>
                                        <td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>
                                    </tr>
                                        <?php
                                            $totalPrice += $value->sub_total;
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
                                    <tr class="<?=$view_price['class']?>">
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
                
                <!-- <?php if(isset($item) && $item->status != 2 || !isset($item)) { ?> -->
                <!-- <?php } ?> -->
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
    _validate_form($('.client-form'),{code:'required',warehouse_id:'required',deliver_id:'required'});
    var view_account=<?=json_encode($view_account)?>;
    var view_price=<?=json_encode($view_price)?>;

    var itemList = <?php echo json_encode($items);?>;

    
    $('#warehouse_id').change(function(e){
        $('table tr.sortable.item').remove();
        total=0;
        var warehouse_id=$(this).val();
        loadProductsInWarehouse(warehouse_id);
        refreshAll();
        refreshTotal();
        if(checkWarehouse())
        {
            alert_float('danger','Vui lòng chọn kho khác để chuyển kho');
            
        }
    });

    $('#warehouse_id_to').change(function(e){
        $('table tr.sortable.item').remove();
        total=0;
        var warehouse_id=$(this).val();
        loadProductsInWarehouse(warehouse_id);
        refreshAll();
        refreshTotal();
        if(checkWarehouse())
        {
            alert_float('danger','Vui lòng chọn kho khác để chuyển kho');
            $(e.currentTarget).selectpicker('toggle');
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

        var td3 = $('<td class="'+view_account.class+'"></td>');
        var td4 = $('<td class="'+view_account.class+'"></td>');

        // var td5 = $('<td><input style="width: 100%;" class="entered_price" min="0" type="number" name="items[' + uniqueArray + '][entered_price]" value="" /></td>');
        var td5 = $('<td></td>');
        var td6 = $('<td><input style="width: 100%;" class="mainQuantity" min="1" type="number" name="items[' + uniqueArray + '][quantity]" value="" /></td>');
        var td7 = $('<td><input style="width: 100%;" class="mainQuantityNet" min="1" type="number" name="items[' + uniqueArray + '][quantity_net]" value="" /></td>');
        var td8 = $('<td class="'+view_price.class+'"></td>');
        var td9 = $('<td class="'+view_price.class+'"></td>');

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

        // td5.find('input').val($('tr.main').find('td:nth-child(5) > input').val());

        td5.text($('tr.main').find('td:nth-child(5)').text());
        td6.find('input').val($('tr.main').find('td:nth-child(6) > input').val());
        td6.find('input').attr('max',$('tr.main').find('td:nth-child(6) > input').attr('max'));
        td7.find('input').val($('tr.main').find('td:nth-child(7) > input').val());
        td7.find('input').attr('max',$('tr.main').find('td:nth-child(7) > input').attr('max'));
        td8.text( $('tr.main').find('td:nth-child(8)').text() );
        td9.text( $('tr.main').find('td:nth-child(9)').text() );
        
        newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td6);
        newTr.append(td7);
        newTr.append(td8);
        newTr.append(td9);
        // newTr.append(td10);

        newTr.append('<td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td');
        $('table.item-purchase tbody').append(newTr);
        total+=parseFloat($('tr.main').find('td:nth-child(7) > input').val());
        
        uniqueArray++;
        refreshTotal();
        $('#custom_item_select').selectpicker('toggle');
        // selectpicker
        newTr.find('.selectpicker').selectpicker('refresh');
        // refreshAll();
    };
    var refreshAll = () => {
        isNew = false;
        $('#btnAdd').hide();
        $('#custom_item_select').val('');
        $('#custom_item_select').selectpicker('refresh');
        var trBar = $('tr.main');
        
        
        trBar.find('td:first > input').val("");
        trBar.find('td:nth-child(2)').text("Tên hàng hóa");
        trBar.find('td:nth-child(3) > select').val('').selectpicker('refresh');
        trBar.find('td:nth-child(4) > select').val('').selectpicker('refresh');
        // trBar.find('td:nth-child(5) > input').val('0');
        trBar.find('td:nth-child(5)').text('Đơn vị tính');
        trBar.find('td:nth-child(6) > input').val('1');
        trBar.find('td:nth-child(7) > input').val('1');
        trBar.find('td:nth-child(8)').text('Giá nhập');
        trBar.find('td:nth-child(9)').text('0');


    };
    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        total-=current.find('td:nth-child(7) > input').val();
        $(trItem).parent().parent().remove();
        
        refreshTotal();
    };
    var refreshTotal = () => {
        $('.total').text(formatNumber(total));
        var items = $('table.item-purchase tbody tr:gt(0)');
        totalPrice = 0;
        $.each(items, (index,value)=>{
            totalPrice += $(value).find('td:nth-child(7) > input').val() * $(value).find('td:nth-child(8)').text().replace(/\,/g, '');
        });


        
        $('.totalPrice').text(formatNumber(totalPrice));
    };
    var refreshQuantity = () => {
        var items = $('table.item-purchase tbody tr:gt(0)');
        total = 0;
        $.each(items, (index,value)=>{
            total += parseFloat($(value).find('input.mainQuantityNet').val());
        });

        $('.total').text(formatNumber(total));
    };
    $('#custom_item_select').change((e)=>{
        var id = $(e.currentTarget).val();
        var itemFound = findItem(id);
        var warehouse_id=$('#warehouse_id').val();
        $('#entered_price').html('').selectpicker("refresh");
            // $.ajax({
            //         url : admin_url + 'imports/get_entered_price/' +warehouse_id+'/'+ id,
            //         dataType : 'json',
            //     })
            //     .done(function(entered_price){
            //         if(entered_price)
            //         {
                        
            //             $.each($(entered_price), function( index, value ) {
            //                 $('#entered_price').append('<option value="'+value.entered_price+'">'+formatNumber(value.entered_price)+'</option>');
            //             })
            //             $('#entered_price').selectpicker("refresh");
            //         }
                    
            //     });

        // $('#entered_price').append('<option selected value="'+itemFound.price+'">'+formatNumber(itemFound.price)+'</option>');
        // $('#entered_price').selectpicker("refresh");

    getMaxProductQuantity(warehouse_id,id, function(data) {
            var maxquantity=data.product_quantity;
            
            if(typeof(itemFound) != 'undefined') {
                var trBar = $('tr.main');
                trBar.find('td:first > input').val(itemFound.id);
                trBar.find('td:nth-child(2)').text(itemFound.name);
                trBar.find('td:nth-child(3) select').val(107).selectpicker('refresh');
                trBar.find('td:nth-child(4) select').val(34).selectpicker('refresh');
                // trBar.find('td:nth-child(5) > input').val(itemFound.price);
                trBar.find('td:nth-child(5)').text(itemFound.unit_name);
                trBar.find('td:nth-child(5) > input').val(itemFound.unit);
                
                trBar.find('td:nth-child(6) > input').val(1);
                trBar.find('td:nth-child(6) > input').attr('max',maxquantity);
                trBar.find('td:nth-child(7) > input').val(1);
                trBar.find('td:nth-child(7) > input').attr('max',maxquantity);
                trBar.find('td:nth-child(8)').text(formatNumber(itemFound.price));
                trBar.find('td:nth-child(9)').text(formatNumber(itemFound.price * 1));
                isNew = true;
                $('#btnAdd').show();
            }
            else {
                isNew = false;
                $('#btnAdd').hide();
            }
        });
    });

    function getMaxProductQuantity(warehouse_id,product_id, callback){
        $.ajax({
          url : admin_url + 'warehouses/getProductQuantity/' +warehouse_id+'/'+ product_id,
          dataType : 'json',
        })
        .done(function(data){
            if(data) 
            {
                callback(data);
            }
            return 0;
        });
    }
    $(document).on('keyup', '.mainQuantity',(e)=>{
        
        var currentQuantityInput = $(e.currentTarget);
        var quantity_netTd=currentQuantityInput.parent().find(' + td');
        quantity_netTd.find('input').val(currentQuantityInput.val());
        var Giatd = quantity_netTd.find(' + td');
        var Gia=Giatd.text().replace(/\,/g, '');
        var Tong = Giatd.find(' + td');
        Tong.text(formatNumber(Gia * currentQuantityInput.val()));
        refreshQuantity();
        refreshTotal();
    });
    $(document).on('keyup', '.mainQuantityNet',(e)=>{
        
        var currentQuantityInput = $(e.currentTarget);
        var Giatd = currentQuantityInput.parent().find(' + td');
        var Gia=Giatd.text().replace(/\,/g, '');
        var Tong = Giatd.find(' + td');
        console.log(Tong)
        Tong.text(formatNumber(Gia * currentQuantityInput.val()));
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
        
        if($('input.error').length) {
            e.preventDefault();
            alert_float('danger', "Giá trị không hợp lệ!"); 
        }
        if(!warehouse_id)
        {
            alert_float('danger', "Vui lòng chọn kho chứa sản phẩm!");
            e.preventDefault(); 
        }
        var tk=$('select[name^="items"]');
        $.each(tk, function(key,value){
        if($(value).val()=='')
        {
            alert_float('danger', "Vui lòng chọn tài khoản hạch toán!");
            e.preventDefault();
            
            return;
        }
        });
    });

    function viewReceivers(is_staff=1)
    {
        var receiver_id=$('#receiver_id');
        receiver_id.find('option:gt(0)').remove();
        receiver_id.selectpicker('refresh');
        if(receiver_id.length) {
            $.ajax({
                url : admin_url + 'imports/getReceivers/' + is_staff,
                dataType : 'json',
            })
            .done(function(data){         
                $.each(data, function(key,value){

                    receiver_id.append('<option value="' + value.id + '">'+ value.name + '</option>');
                });
                receiver_id.selectpicker('refresh');
            });
        }
    }

    function checkWarehouse()
    {
        var warehouse_id=$('#warehouse_id').val();
        var warehouse_id_to=$('#warehouse_id_to').val();
        if(warehouse_id==warehouse_id_to)
        {
            return true;
        }
        return false;
    }
        
</script>
</body>
</html>
