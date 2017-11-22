<?php init_head(); ?>
<div id="wrapper" class="customer_profile">
 <div class="content">
   <div class="row">

  <div class="col-md-12">
   <div class="panel_s">
     <div class="panel-body">
<!--        --><?php //if (isset($purchase_suggested)) { ?>
<!--        --><?php //echo form_hidden('isedit'); ?>
<!--        --><?php //echo form_hidden('itemid', $purchase_suggested->id); ?>
      <div class="clearfix"></div>
<!--        --><?php //
//    } ?>
        <!-- Product information -->
        

          <h4 class="bold no-margin"><?php echo (isset($heading) ? $heading : ''); ?></h4>
  <hr class="no-mbot no-border" />
    <div class="row">
    <div class="additional"></div>
    <div class="col-md-12">
        <?php
         if(isset($vote))
            {
                if($vote->status==0)
                {
                    $type='warning';
                    $status='Chưa duyệt';
                }
                elseif($vote->status==1)
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
                $status='Đề xuất mới';
            }

        ?>
        <div class="ribbon <?=$type?>"><span><?=$status?></span></div>
        <ul class="nav nav-tabs profile-tabs" role="tablist">
            <li role="presentation" class="active">
                <a href="#item_detail" aria-controls="item_detail" role="tab" data-toggle="tab">
                    <?php echo _l('purchase_suggested_information'); ?>
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
                        <?php if( isset($vote) ) { ?>
                        <a href="<?php echo admin_url('votes/pdf/' . $vote->id . '?print=true') ?>" target="_blank" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="In" aria-describedby="tooltip652034"><i class="fa fa-print"></i></a>
                        <a href="<?php echo admin_url('votes/pdf/' . $vote->id  ) ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Xem PDF"><i class="fa fa-file-pdf-o"></i></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            
            <?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'client-form', 'autocomplete' => 'off', 'novalidate' => 'novalidate')); ?>
                <div class="row">
                  <div class="col-xs-9 col-sm-9 col-md-9 col-lg-9">
                      <?php $selected_supplier = (isset($vote) ? $vote->id_supplier : "");?>
                      <?php echo  render_select('id_supplier',$supplier,array('userid','company','supplier_code'),_('lblsupplier'),$selected_supplier); ?>

                      <?php
                          $receiver = (isset($vote) ? $vote->receiver : "");
                          echo  render_input('receiver', _l('receiver'), $receiver);
                      ?>
                      <?php
                          $address = (isset($vote) ? $vote->address : "");
                          echo  render_input('address', _l('address'), $address);
                      ?>
                      <?php
                          $reason = (isset($vote) ? $vote->reason : "");
                          echo  render_input('reason', _l('votesreason'), $reason);
                      ?>
                    <?php
                        $default_date = ( isset($vote) ? _d($vote->date_create) : _d(date('Y-m-d')));
                        echo render_date_input( 'date_create', 'project_datecreated' , $default_date , 'date');
                    ?>
                    
                </div>
                
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                    <?php
                    $default_date = ( isset($vote) ? _d($vote->date_of_accounting) : _d(date('Y-m-d')));
                    echo render_date_input( 'date_of_accounting', 'date_of_accounting' , $default_date , 'date_of_accounting');
                    ?>
                    <?php
                    $default_date = ( isset($vote) ? _d($vote->day_vouchers) : _d(date('Y-m-d')));
                    echo render_date_input( 'day_vouchers', 'day_vouchers' , $default_date , 'day_vouchers');
                    ?>
                    <?php
                    $code_vouchers = (isset($vote) ? $vote->code_vouchers : $code_vouchers);
                    echo  render_input('code_vouchers', _l('code_vouchers'),$code_vouchers,'text',$code_vouchers);
                    ?>
                </div>
                

                <!-- Edited -->
                <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                    <!-- Cusstomize from invoice -->
                    <div class="panel-body mtop10">
                        <?php $disabled='disabled';?>
                        <?php $readonly='';$display="";?>
                    <?php if(($vote->status!=0)&&isset($vote)){ $display='style="display: none;"'; $readonly='readonly';  }?>
                        <div class="table-responsive s_table">
                            <table class="table items item-export no-mtop">
                                <thead>
                                    <tr>
                                        <th><input type="hidden" id="itemID" value="" /></th>
                                        <th width="" class="text-left"><?php echo _l('Diển giải'); ?></th>
                                        <th width="" style="<?php if(!has_permission('view_account','','have')){ ?>display: none;<?php } ?>" class="text-left"><?php echo _l('tk_no'); ?></th>
                                        <th width="" style="<?php if(!has_permission('view_account','','have')){ ?>display: none;<?php } ?>" class="text-left"><?php echo _l('tk_co'); ?></th>
                                        <th width="" class="text-left"><?php echo _l('contract_buy'); ?></th>
                                        <th width="" class="text-left"><?php echo _l('contract_ban'); ?></th>
                                        <th width="" class="text-left"><?php echo _l('currencies'); ?></th>
                                        <th width="" class="text-left"><?php echo _l('total_money'); ?>(VND)</th>
                                        <th></th>
                                        
                                    </tr>
                                </thead>
                                
                                <tbody>
                                    <tr class="main" <?=$display?> >
                                        <td><input type="hidden" id="itemID" value="" /></td>
                                       <td style="padding-top: 8px;"><div class="form-group"><input type="text" id="note" class="form-control" value=""></div></td>
                                       <td style="<?php if(!has_permission('view_account','','have')){ ?>display: none;<?php } ?>">
                                           <select class="selectpicker" data-live-search="true" id="tk_no" data-width="100%" data-none-selected-text="<?php  echo _l('tk_no')?>">
                                               <?php if($tk_no){?>
                                                   <option></option>
                                                   <?php foreach($tk_no as $rom){?>
                                                       <option value="<?=$rom['idAccount']?>" data-subtext="<?=$rom['accountName']?>"><?=$rom['accountCode']?></option>
                                                   <?php }?>
                                               <?php }?>

                                           </select>
                                       </td>
                                       <td style="<?php if(!has_permission('view_account','','have')){ ?>display: none;<?php } ?>">
                                           <select class="selectpicker" data-live-search="true" id="tk_co" data-width="100%" data-none-selected-text="<?php  echo _l('tk_co')?>">
                                               <?php if($tk_co){?>
                                                   <option></option>
                                                   <?php foreach($tk_co as $rom){?>
                                                       <option value="<?=$rom['idAccount']?>" data-subtext="<?=$rom['accountName']?>"><?=$rom['accountCode']?></option>
                                                   <?php }?>
                                               <?php }?>

                                           </select>
                                       </td>
                                       <td>
                                            <select class="selectpicker" data-live-search="true" id="purchase_contracts" data-width="100%" data-none-selected-text="<?php echo _l('contract_buy')?>">
                                                <?php if($purchase_contracts){?>
                                                    <option></option>
                                                    <?php foreach($purchase_contracts as $rom){?>
                                                            <option value="<?=$rom['id']?>"><?=$rom['code']?></option>
                                                    <?php }?>
                                                <?php }?>

                                            </select>
                                       </td>
                                       <td>
                                           <select class="selectpicker" data-live-search="true" id="contract" data-width="100%" data-none-selected-text="<?php echo _l('contract_ban'); ?>">
                                               <?php if($contract){?>
                                                   <option></option>
                                                   <?php foreach($contract as $rom){?>
                                                       <option value="<?=$rom['id']?>"><?=$rom['fullcode']?></option>
                                                   <?php }?>
                                               <?php }?>

                                           </select>
                                       </td>
                                        <td>
                                           <select class="selectpicker" data-live-search="true" id="currencies" disabled data-width="100%" data-none-selected-text="<?php echo _l('currencies'); ?>">
                                               <?php if($currencies){?>
                                                   <option></option>
                                                   <?php foreach($currencies as $rom){?>
                                                       <option value="<?=$rom['id']?>"><?=$rom['name']?></option>
                                                   <?php }?>
                                               <?php }?>

                                           </select>
                                       </td>
                                       <td>
                                           <div class="form-group">
                                               <input type="text" id="total" class="form-control" value="">
                                           </div>
                                       </td>
                                       <td>
                                           <button style="" id="btnAdd" type="button" onclick="createTrItem(); return false;" class="btn pull-right btn-info"><i class="fa fa-check"></i></button>
                                       </td>
                                    </tr>
                                    <?php
                                    $i=0;
                                    $totalPrice=0;
                                    
                                    if(isset($votes) && count($votes) > 0) {
                                        
                                        foreach($votes as $value) {
                                        ?>
                                    <tr class="sortable item">
                                        <td  class="dragger">
                                            <input type="hidden" name="item[<?php echo $i; ?>][id]" value="<?php echo $value['id']; ?>">
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input <?=$readonly?> type="text" name="item[<?php echo $i ?>][note]" id="note" class="form-control" value="<?php echo $value['note']; ?>">
                                            </div>
                                        <td>
                                            <select class="selectpicker" data-live-search="true" name="item[<?php echo $i ?>][tk_no]" data-width="100%" data-none-selected-text="<?php  echo _l('tk_no')?>">
                                                <?php if($tk_no){?>
                                                    <option></option>
                                                    <?php foreach($tk_no as $rom){?>
                                                        <?php $selected="";?>
                                                        <?php if($rom['idAccount']==$value['tk_no']){$selected='selected';}?>
                                                        <option value="<?=$rom['idAccount']?>" data-subtext="<?=$rom['accountName']?>" <?=$selected?>><?=$rom['accountCode']?></option>
                                                    <?php }?>
                                                <?php }?>

                                            </select>
                                        </td>
                                        <td>
                                            <select class="selectpicker" data-live-search="true" name="item[<?php echo $i ?>][tk_co]" data-width="100%" data-none-selected-text="<?php  echo _l('tk_co')?>">
                                                <?php if($tk_co){?>
                                                    <option></option>
                                                    <?php foreach($tk_co as $rom){?>
                                                        <?php $selected="";?>
                                                        <?php if($rom['idAccount']==$value['tk_co']){$selected='selected';}?>
                                                        <option value="<?=$rom['idAccount']?>" data-subtext="<?=$rom['accountName']?>" <?=$selected?>><?=$rom['accountCode']?></option>
                                                    <?php }?>
                                                <?php }?>

                                            </select>
                                        </td>
                                            
                                        <td>
                                            <select class="selectpicker" data-live-search="true" name="item[<?php echo $i ?>][purchase_contracts]" <?=$disabled?> data-width="100%" data-none-selected-text="<?php echo _l('contract_buy')?>">
                                                <?php if($purchase_contracts){?>
                                                    <option></option>
                                                    <?php foreach($purchase_contracts as $rom){?>
                                                        <?php $selected="";?>
                                                        <?php if($rom['id']==$value['purchase_contracts']){$selected='selected';}?>
                                                        <option value="<?=$rom['id']?>" <?=$selected?>><?=$rom['code']?></option>
                                                    <?php }?>
                                                <?php }?>

                                            </select>
                                        </td>
                                        <td>
                                            <select class="selectpicker" data-live-search="true" name="item[<?php echo $i ?>][contract]" data-width="100%" <?=$disabled?> data-none-selected-text="<?php echo _l('contract_ban'); ?>">
                                                <?php if($contract){?>
                                                    <option></option>
                                                    <?php foreach($contract as $rom){?>
                                                        <?php $selected="";?>
                                                        <?php if($rom['id']==$value['contract']){$selected='selected';}?>
                                                        <option value="<?=$rom['id']?>" <?=$selected?>><?=$rom['fullcode']?></option>
                                                    <?php }?>
                                                <?php }?>

                                            </select>
                                        </td>
                                        <td>
                                            <select class="selectpicker" data-live-search="true" name="item[<?php echo $i ?>][currencies]" data-width="100%" disabled data-none-selected-text="<?php echo _l('currencies'); ?>">
                                                <?php if($currencies){?>
                                                    <option></option>
                                                    <?php foreach($currencies as $rom){?>
                                                        <?php $selected="";?>
                                                        <?php if($rom['id']==$value['currencies']){$selected='selected';}?>
                                                        <option value="<?=$rom['id']?>" <?=$selected?>><?=$rom['name']?></option>
                                                    <?php }?>
                                                <?php }?>

                                            </select>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input <?=$readonly?> type="text" name="item[<?php echo $i ?>][total]" class="mainQuantity form-control" value="<?=_format_number($value['total'])?>">
                                            </div>
                                        </td>
                                        <td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>
                                    </tr>
                                        <?php
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
                                        <td><span class="bold"><?php echo _l('purchase_total_items'); ?> :</span>
                                        </td>
                                        <td class="total">
                                            <?php echo $i ?>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- End Customize from invoice -->
                </div>
                <!-- End edited -->
                
                <?php if(isset($vote) && $vote->status == 0 || !isset($vote)) { ?>
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
    var itemList = <?php echo json_encode($products);?>;

    var item_votes_contract = <?php echo json_encode($votes_contract);?>;
    console.log(item_votes_contract);
    var itemList_votes_contract_purchase = <?php echo json_encode($votes_contract_purchase);?>;

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

    var findItem = (id,type) => {
        var itemResult;
        if(type==1)
        {
            $.each(item_votes_contract, (index,value) => {
                console.log(value.sum_contract);
                if(id == index) {
                    itemResult = value;
                    return false;
                }
            });
        }
        else
        {
            $.each(itemList_votes_contract_purchase, (index,value) => {
                if(id == index) {
                    itemResult = value;
                    return false;
                }
            });
        }
        return itemResult;
    }

    var total = <?php echo $i ?>;
    var totalPrice = <?php echo $totalPrice ?>;
    var uniqueArray = <?php echo $i ?>;
    var isNew = false;
	// Remove select name
	$('#select_kindof_warehouse').removeAttr('name');
	$('#select_warehouse').removeAttr('name');
    $('#select_currency').removeAttr('name');
    var createTrItem = () => {

//        if(!isNew) return;
        if(!$('tr.main #tk_no option:selected').length || $('tr.main #tk_no option:selected').val() == '') {
            alert_float('danger', "Vui lòng chọn tài khoản nợ!");
            return;
        }
        if(!$('tr.main #tk_co option:selected').length || $('tr.main #tk_co option:selected').val() == '') {
            alert_float('danger', "Vui lòng chọn tài khoản có!");
            return;
        }
        if($('tr.main').find('td:nth-child(4) > input').val() > $('tr.main #select_warehouse option:selected').data('store')) {
            alert_float('danger', 'Kho ' + $('tr.main #select_warehouse option:selected').text() + '. Bạn đã nhập ' + $('tr.main').find('td:nth-child(4) > input').val() + ' là quá số lượng cho phép.');
            return;
        }
        var newTr = $('<tr class="sortable item"></tr>');

        var td1 = $('<td><input type="hidden" name="items[' + uniqueArray + '][id]" value="" /></td>');
        var td2 = $('<td><div class="form-group"><input type="text" name="items[' + uniqueArray + '][note]" value="" class="form-control" /></div></td>');
        var td3 = $('<td></td>');
        var td4 = $('<td></td>');
        var td5 = $('<td></td>');
        var td6 = $('<td></td>');
        var td7 = $('<td></td>');
        var td8 = $('<td><div class="form-group"><input  class="mainQuantity form-control"  name="items[' + uniqueArray + '][total]" value="" /></div></td>');

        td1.find('input').val($('tr.main').find('td:nth-child(1) input').val());
        td2.find('input').val($('tr.main').find('td:nth-child(2) input').val());


        let tk_no = $('tr.main').find('td:nth-child(3)').find('select').clone();
        tk_no.attr('name', 'items[' + uniqueArray + '][tk_no]');
        tk_no.removeAttr('id').val($('tr.main').find('td:nth-child(3)').find('select').selectpicker('val'));
        td3.append(tk_no);


        let tk_co = $('tr.main').find('td:nth-child(4)').find('select').clone();
        tk_co.attr('name', 'items[' + uniqueArray + '][tk_co]');
        tk_co.removeAttr('id').val($('tr.main').find('td:nth-child(4)').find('select').selectpicker('val'));
        td4.append(tk_co);

        let purchase_contracts = $('tr.main').find('td:nth-child(5)').find('select').clone();
        purchase_contracts.attr('name', 'items[' + uniqueArray + '][purchase_contracts]');
        purchase_contracts.attr('disabled', 'disabled');
        purchase_contracts.removeAttr('id').val($('tr.main').find('td:nth-child(5)').find('select').selectpicker('val'));
        td5.append(purchase_contracts);


        let contract = $('tr.main').find('td:nth-child(6)').find('select').clone();
        contract.attr('name', 'items[' + uniqueArray + '][contract]');
        contract.attr('disabled', 'disabled');
        contract.removeAttr('id').val($('tr.main').find('td:nth-child(6)').find('select').selectpicker('val'));
        td6.append(contract);


        let currencies = $('tr.main').find('td:nth-child(7)').find('select').clone();
        currencies.attr('name', 'items[' + uniqueArray + '][currencies]');
        currencies.removeAttr('id').val($('tr.main').find('td:nth-child(7)').find('select').selectpicker('val'));

        td7.append(currencies);

        td8.find('input').val($('tr.main').find('td:nth-child(8) input').val());
        console.log($('tr.main').find('td:nth-child(8) input').val());

		newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td6);
        newTr.append(td7);
        newTr.append(td8);

        newTr.append('<td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td');
        $('table.item-export tbody').append(newTr);
        total++;
        totalPrice += $('tr.main').find('td:nth-child(4) > input').val() * $('tr.main').find('td:nth-child(5)').text().replace(/\+/g, ' ');
        uniqueArray++;
        refreshTotal();
        refreshAll();
    };
    var refreshAll = () => {
        isNew = false;
        var trBar = $('tr.main');
        trBar.find('td:first > input').val("");
        trBar.find('td:nth-child(1) input').val('');
        trBar.find('td:nth-child(2) input').val('');
        trBar.find('td:nth-child(5) select').val('').selectpicker('refresh');
        trBar.find('td:nth-child(6) select').val('').selectpicker('refresh');
        trBar.find('td:nth-child(7) select').val('').selectpicker('refresh');
        trBar.find('td:nth-child(8) input').val('');

    };
    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        totalPrice -= current.find('td:nth-child(4) > input').val() * current.find('td:nth-child(5)').text().replace(/\,/g, '');
        $(trItem).parent().parent().remove();
        total--;
        refreshTotal();
    };
    var refreshTotal = () => {
        total=0;
        var mainquantity=$('.mainQuantity');
        $.each(mainquantity, function(key,value){
            total++;
        })
        $('.total').text(formatNumber(total));
        var items = $('table.item-export tbody tr:gt(0)');
        
		$('.selectpicker').selectpicker('refresh').removeAttr('disabled');
	};
    $('#custom_item_select').change((e)=>{
        var id = $(e.currentTarget).val();
        var itemFound = findItem(id);

        $('#select_kindof_warehouse').val('');
        $('#select_kindof_warehouse').selectpicker('refresh');
		$('#select_currency').find('option:first').attr('selected', 'selected');
        $('#select_currency').selectpicker('refresh');
        var warehouse_id=$('#select_warehouse');
        warehouse_id.find('option:gt(0)').remove();
        warehouse_id.selectpicker('refresh');

        if(typeof(itemFound) != 'undefined') {
            var trBar = $('tr.main');
            
            trBar.find('td:first > input').val(itemFound.id);
            trBar.find('td:nth-child(2)').text(itemFound.name+' ('+itemFound.prefix+itemFound.code+')');
            trBar.find('td:nth-child(3)').text(itemFound.unit_name);
            trBar.find('td:nth-child(3) > input').val(itemFound.unit);
            trBar.find('td:nth-child(4) > input').val(1);

            trBar.find('td:nth-child(5)');
            trBar.find('td:nth-child(6)');
            trBar.find('td:nth-child(7)');
            trBar.find('td:nth-child(8)');
            trBar.find('td:nth-child(10)').text(itemFound.tax_rate + " %");
            trBar.find('td:nth-child(11)');
            isNew = true;
            $('#btnAdd').show();
        }
        else {
            isNew = false;
            $('#btnAdd').hide();
        }
    });
    $('#purchase_contracts').change((e)=>{
        var id = $(e.currentTarget).val();
        var trBar = $('tr.main');
        $('#contract').val('');
        $('#contract').selectpicker('refresh');
        var itemFound = findItem(id,2);
        console.log(itemFound.currency);
        trBar.find('td:nth-child(7) select').val(itemFound.currency).selectpicker('refresh');
        trBar.find('td:nth-child(8) input').val(formatNumber(itemFound.sum_contract));
    });

    $('#contract').change((e)=>{
        var trBar = $('tr.main');
        $('#purchase_contracts').val('');
        $('#purchase_contracts').selectpicker('refresh');
        var id = $(e.currentTarget).val();
        var itemFound = findItem(id,1);
        trBar.find('td:nth-child(7) select').val(itemFound.currency).selectpicker('refresh');
        trBar.find('td:nth-child(8) input').val(formatNumber(itemFound.sum_contract));
    });
    $('#select_warehouse').on('change', (e)=>{
        if($(e.currentTarget).val() != '') {
            $(e.currentTarget).parents('tr').find('input.mainQuantity').attr('data-store', $(e.currentTarget).find('option:selected').data('store'));
        }
    });
	var calculateTotal = (currentInput) => {
        refreshTotal();
	};
	$(document).on('keyup', '.mainQuantity', (e)=>{
		var currentPriceBuyInput = $(e.currentTarget);
        var new_current=formatNumber(currentPriceBuyInput.val().replace(/\,|%/g, ''));
        currentPriceBuyInput.val(new_current);
	});
    $('#select_kindof_warehouse').change(function(e){
        var warehouse_type = $(e.currentTarget).val();
        var product = $(e.currentTarget).parents('tr').find('td:first input');
        if(warehouse_type != '' && product.val() != '') {
            loadWarehouses(warehouse_type,product.val()); 
        }
    });
    function loadWarehouses(warehouse_type, filter_by_product,default_value=''){
        var warehouse_id=$('#select_warehouse');
        warehouse_id.find('option:gt(0)').remove();
        warehouse_id.selectpicker('refresh');
        if(warehouse_id.length) {
            $.ajax({
                url : admin_url + 'warehouses/getWarehouses/' + warehouse_type + '/' + filter_by_product + '/true',
                dataType : 'json',
            })
            .done(function(data){
				console.log(data);
                $.each(data, function(key,value){
                    var stringSelected = "";
                    if(value.warehouseid == default_value) {
                        stringSelected = ' selected="selected"';
                    }
					warehouse_id.append('<option data-store="'+(value.items[0].maximum_quantity - value.items[0].product_quantity)+'" value="' + value.warehouseid + '"'+stringSelected+'>' + value.warehouse + '(nhập tối đa '+(value.items[0].maximum_quantity - value.items[0].product_quantity)+')</option>');
                });
                warehouse_id.selectpicker('refresh');
            });
        }
    }
    $('.client-form').on('submit', (e)=>{
        if($('input.error').length > 0) {
            e.preventDefault();
            alert_float('danger', 'Giá trị không hợp lệ!');    
        }
        
    });
</script>
</body>
</html>