<?php init_head(); ?>
<style type="text/css">
  .item-purchase .ui-sortable tr td input {
    width: 80px;
  }
</style>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<?php
			echo form_open($this->uri->uri_string(), array('id' => 'purchase-form', 'class' => '_transaction_form invoice-form'));
			if (isset($invoice)) {
				echo form_hidden('isedit');
			}
			?>
			<div class="col-md-12">
				<div class="panel_s">
				 	<div class="additional"></div>
				 	<div class="panel-body">
				 	<?php 
						$type = '';
						if (!isset($purchase))
							$type = 'warning';
						elseif ($purchase->status == 0)
							$type = 'warning';
						elseif ($purchase->status == 1)
							$type = 'info';
						elseif ($purchase->status == 2)
							$type = 'success';

						?>
				 		<div class="ribbon <?= $type ?>" project-status-ribbon-2="">
				 			<?php 
								if (isset($purchase))
									{
									$status = format_purchase_status($purchase->status, '', false);
								}
								else
									{
									$status = format_purchase_status(-1, '', false);
								}
								?>
				 			<span><?= $status ?></span>
						 </div>
						 <?php 
							if (isset($purchase))
							{ ?>
						<div class="form-group" style="margin-top: 25px">
						    <a href="<?php echo admin_url('purchases/pdf/' . $purchase->id . '?print=true'); ?>" target = '_blank' class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('print'); ?>" data-placement="bottom"><i class="fa fa-print"></i></a>
						    <a href="<?php echo admin_url('purchases/pdf/' . $purchase->id); ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('view_pdf'); ?>" data-placement="bottom"><i class="fa fa-file-pdf-o"></i></a>
						    <!-- <a href="#" class="invoice-send-to-client btn-with-tooltip btn btn-default" data-toggle="tooltip" title="<?php echo $_tooltip; ?>" data-placement="bottom"><span data-toggle="tooltip" data-title="<?php echo $_tooltip_already_send; ?>"><i class="fa fa-envelope"></i></span></a> -->
						</div>
						<?php 
							}
						?>
						<h4 class="bold no-margin font-medium">
						     <?php echo _l('Thông tin kế hoạch'); ?>
						   </h4>
						   <hr />
				 		<div class="col-md-6">
				 			<?php if(!$purchase){ ?>
						   <div class="form-group">
				                 <label for="number"><?php echo _l('Số kế hoạch'); ?></label>
				                 <div class="input-group">
				                  <span class="input-group-addon">
				                    <?php echo get_option('prefix_purchase_plan'); ?></span>
									<?php 
											$number = sprintf('%06d', getMaxID('id', 'tblpurchase_plan') + 1);
										?>
				                    <input type="text" name="number" class="form-control" value="<?= $number ?>" data-isedit="<?php echo $isedit; ?>" data-original-number="<?php echo $data_original_number; ?>" readonly>
				                  </div>
			                </div>
			                <?php } else
			                { 
			                	$value = (isset($purchase) ? ($purchase->code) : '');
			                	echo render_input('number', _l('Số kế hoạch'), $value,'text',array('readonly'=>true));
			                 } ?>

			                <?php $value = (isset($purchase) ? _d($purchase->date) : _d(date('Y-m-d'))); ?>
                  			<?php echo render_date_input('date', 'Ngày kế hoạch', $value); ?>

                  			<?php
							$value = (isset($purchase) ? $purchase->name : _l('purchase_name'));
							echo render_input('name', _l('Tên kế hoạch'), $value);
							?>

		                    

		                    <!-- <?php
							$status = array(array('id' => 0, 'text' => 'Chưa duyệt'), array('id' => 1, 'text' => 'Đã duyệt'));
							$value = (isset($purchase) ? $purchase->status : "0");
							echo render_select('status', $status, array('id', 'text'), 'Trạng thái', $value, array(), array(), '', '', false);
							?> -->
				 		</div>
						
						<div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
							<?php 
							$value = (isset($purchase) ? $purchase->reason : "");
							echo render_textarea('reason', 'Lý do', $value, array(), array(), '', 'tinymce');
							?>
						</div>
						

						
						<!-- Edited -->
						<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
							<!-- Cusstomize from invoice -->
							<div class="panel-body mtop10">
							<?php if(!empty($item->rel_id) || !empty($item->rel_code)){ $display='style="display: none;"';  }?>
								<div class="row">
									<div class="col-md-4">
										<?php
										$selected=(isset($warehouse_id)? $warehouse_id:'');
								         echo render_select('warehouse_id', $warehouses, array('warehouseid', 'warehouse'),'warehouse_name',$selected);
										?>
									</div>

									<div class="col-md-4" <?=$display?>>
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
								<div class="table-responsive s_table">
									<table class="table items item-export no-mtop">
										<thead>
											<tr>
												<th><input type="hidden" id="itemID" value="" /></th>
												<th width="" class="text-left"><i class="fa fa-exclamation-circle" aria-hidden="true" data-toggle="tooltip" data-title="<?php echo _l('item_name'); ?>"></i> <?php echo _l('item_name'); ?></th>
												<th width="" class="text-left"><?php echo _l('item_unit'); ?></th>
												<th width="" class="text-left"><?php echo _l('item_quantity'); ?></th>
												
												<th width="" class="text-left"><?php echo _l('Tiền tệ'); ?></th>
												<th width="" class="text-left"><?php echo _l('item_price_buy'); ?></th>
												<th width="" class="text-left"><?php echo _l('purchase_total_price'); ?></th>
												<th width="" class="text-left"><?php echo _l('tax'); ?></th>
												<th width="" class="text-left"><?php echo _l('moneytax'); ?></th>
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
													<input style="width: 100px" class="mainQuantity" type="number" min="1" value="1"  class="form-control" placeholder="<?php echo _l('item_quantity'); ?>">
												</td>
												
												<td>
													<?php
													echo render_select('select_currency', $currencies, array('id', 'name'), '', '', array(), array(), '', '', false);
												?>
												</td>
												<td>
													<input style="width: 100px" step="0.01" class="mainPriceBuy" type="number" value=""  class="form-control" placeholder="<?php echo _l('item_price_buy'); ?>">
												</td>
												<td>
													0
												</td>
												<td>
													0 %
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
											if(isset($purchase) && count($purchase->items) > 0) {
												
												foreach($purchase->items as $value) {
													
												?>
											<tr class="sortable item">
												<td>
													<input type="hidden" name="item[<?php echo $i; ?>][id]" value="<?php echo $value['product_id']; ?>">
												</td>
												<td class="dragger"><?php echo $value['name']; ?></td>
												<td><?php echo $value['unit_name']; ?>
													<input type="hidden" data-store="<?=$value['warehouse_type']->maximum_quantity-$value['warehouse_type']->total_quantity ?>" name="item[<?=$i?>][warehouse]" value="<?=$value['warehouse_id']?>">
												</td>
												<?php
												$err='';
												$style='';
													if($value['quantity_required']>$value['warehouse_type']->maximum_quantity)
													{
														$err='error';
														$style='border: 1px solid red !important';
													}
												?>
												<td>
												<input style="width: 100px; <?=$style?>" class="mainQuantity <?=$err?>" type="number" name="item[<?php echo $i; ?>][quantity]" value="<?php echo $value['quantity_required']; ?>">
												</td>
												<td>
													<?php echo render_select('item['.$i.'][currency]', $currencies, array('id', 'name'), '', $value['currency_id']); ?>
												</td>
												<td>
													<input style="width: 100px" class="mainPriceBuy" name="item[<?php echo $i ?>][price_buy]" step="0.01" type="number" value="<?php echo $value['price_buy'] ?>"  class="form-control" placeholder="<?php echo _l('item_price_buy'); ?>">
												</td>
												<td>
													<?php echo number_format($value['price_buy']*$value['quantity_required']) ?>
												</td>
												<td>
													<?php echo ($value['tax_rate']) ?> %
												</td>
												<td>
													<?php echo number_format((($value['tax_rate']*($value['price_buy']*$value['quantity_required']))/100)) ?>
												</td>
												<td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td>
												
											</tr>
												<?php
													// $totalPrice += $value['price_buy']*$value['quantity_required'];
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
							<button class="btn-tr btn btn-info mtop10 mright20 text-right pull-right purchase-form-submit">
						      <?php echo _l('submit'); ?>
						    </button>
				 		</div>
				 	</div>
			 	</div>
			</div>
			
			<?php echo form_close(); ?>
			<?php $this->load->view('admin/invoice_items/item'); ?>
		</div>
	</div>
</div>
<?php init_tail(); ?>
<script>
	$(function(){
		// validate_invoice_form();
		_validate_form($('purchase-form'), {
        date: "required",
        warehouse_id: "required",
        number: "required"
    });
	    // Init accountacy currency symbol
	    init_currency_symbol();
	});
</script>
</body>
</html>
<script type="text/javascript">
	var itemList = <?php echo json_encode($items);?>;
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
    var total = <?php echo $i ?>;
    var totalPrice = <?php echo $totalPrice ?>;
    var uniqueArray = <?php echo $i ?>;
    var isNew = false;
	// Remove select name
	$('#select_kindof_warehouse').removeAttr('name');
	$('#select_warehouse').removeAttr('name');
	$('#select_currency').removeAttr('name');
    var createTrItem = () => {
        if(!isNew) return;
        if(!$('#warehouse_id option:selected').length || $('#warehouse_id option:selected').val() == '') {
            alert_float('danger', "Vui lòng chọn kho chứa sản phẩm!");
            return;
        }
		if(!$('tr.main #select_currency option:selected').length || $('tr.main #select_currency option:selected').val() == '') {
            alert_float('danger', "Vui lòng chọn tiền tệ cho sản phẩm!");
            return;
        }
		if($.trim($('tr.main .mainPriceBuy').val()) == '') {
            alert_float('danger', "Vui lòng chọn giá nhập cho sản phẩm!");
            return;
        }
        if( $('table.item-export tbody tr:gt(0)').find('input[value=' + $('tr.main').find('td:nth-child(1) > input').val() + ']').length ) {
            $('table.item-export tbody tr:gt(0)').find('input[value=' + $('tr.main').find('td:nth-child(1) > input').val() + ']').parent().find('td:nth-child(2) > input').focus();
            alert_float('danger', "Sản phẩm này đã được thêm, vui lòng lòng kiểm tra lại!");
            return;
        }
        var maxQ=$('#warehouse_id option:selected').attr('data-store');
        var strMaxQ='(' + 'Nhập tối đa ' + maxQ + ')';
        if($('tr.main').find('td:nth-child(4) > input').val() > $('#warehouse_id option:selected').attr('data-store')) {
            alert_float('danger', 'Kho ' + $('#warehouse_id option:selected').text() + strMaxQ + '. Bạn đã nhập ' + $('tr.main').find('td:nth-child(4) > input').val() + ' là quá số lượng cho phép.');
            return;
        }

        var newTr = $('<tr class="sortable item"></tr>');
        
        var td1 = $('<td><input type="hidden" name="item[' + uniqueArray + '][id]" value="" /></td>');
        var td2 = $('<td class="dragger"></td>');
        var td3 = $('<td></td>');
        var td4 = $('<td><input style="width: 100px" class="mainQuantity" type="number" name="item[' + uniqueArray + '][quantity]" value="" /></td>');
        var td5 = $('<td></td>');
        var td6 = $('<td></td>');
        var td7 = $('<td></td>');
        var td8 = $('<td></td>');
		var td9 = $('<td></td>');

        td1.find('input').val($('tr.main').find('td:nth-child(1) > input').val());
        td2.text($('tr.main').find('td:nth-child(2)').text());
        td3.text($('tr.main').find('td:nth-child(3)').text());
        td3.append('<input type="hidden" data-store="'+$('#warehouse_id option:selected').attr('data-store')+'" name="item[' + uniqueArray + '][warehouse]" value="'+$('#warehouse_id option:selected').val()+'" />');
		td4.find('input').val($('tr.main').find('td:nth-child(4) > input').val());
        
        let objCurrency = $('tr.main').find('td:nth-child(5)').find('select').clone(); 
		objCurrency.attr('name', 'item[' + uniqueArray + '][currency]');
		objCurrency.removeAttr('id').val($('tr.main').find('td:nth-child(5)').find('select').selectpicker('val'));

		td5.append(objCurrency);
		let objPriceBuy = $('tr.main').find('td:nth-child(6)').find('input').clone(); 
		objPriceBuy.attr('name', 'item[' + uniqueArray + '][price_buy]');
		objPriceBuy.removeAttr('id');
		td6.append(objPriceBuy);
		td7.append($('tr.main').find('td:nth-child(7)').text());
		td8.append($('tr.main').find('td:nth-child(8)').text());
		td9.append($('tr.main').find('td:nth-child(9)').text());

		newTr.append(td1);
        newTr.append(td2);
        newTr.append(td3);
        newTr.append(td4);
        newTr.append(td5);
        newTr.append(td6);
        newTr.append(td7);
        newTr.append(td8);
		newTr.append(td9);
        newTr.append('<td><a href="#" class="btn btn-danger pull-right" onclick="deleteTrItem(this); return false;"><i class="fa fa-times"></i></a></td');
        $('table.item-export tbody').append(newTr);
        total++;
        totalPrice += $('tr.main').find('td:nth-child(4) > input').val() * $('tr.main').find('td:nth-child(5)').text().replace(/\+/g, ' ');
        uniqueArray++;
        refreshTotal();
        // refreshAll();
    };
    var refreshAll = () => {
        isNew = false;
        $('#btnAdd').hide();
        $('#custom_item_select').val('');
        $('#custom_item_select').selectpicker('refresh');
        var trBar = $('tr.main');
        
        trBar.find('td:first > input').val("");
        trBar.find('td:nth-child(2) > input').val('');
        trBar.find('td:nth-child(3) > input').val(1);
        trBar.find('td:nth-child(4) > input').val('');
        trBar.find('td:nth-child(5) > textarea').text('');


    };
    var deleteTrItem = (trItem) => {
        var current = $(trItem).parent().parent();
        totalPrice -= current.find('td:nth-child(4) > input').val() * current.find('td:nth-child(5)').text().replace(/\,/g, '');
        $(trItem).parent().parent().remove();
        total--;
        refreshTotal();
    };
    var refreshTotal = () => {
        $('.total').text(formatNumber(total));
        var items = $('table.item-export tbody tr:gt(0)');
        
		$('.selectpicker').selectpicker('refresh');
	};
    $('#custom_item_select').change((e)=>{
        var id = $(e.currentTarget).val();
        var itemFound = findItem(id);
            		
        console.log(itemFound)
		$('#select_currency').find('option:first').attr('selected', 'selected');
        $('#select_currency').selectpicker('refresh');

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
            trBar.find('td:nth-child(8)').text(itemFound.tax_rate + " %");

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
            $(e.currentTarget).parents('tr').find('input.mainQuantity').attr('data-store', $(e.currentTarget).find('option:selected').attr('data-store'));
        }
    });
    $('#warehouse_id,#custom_item_select').on('change', function(e){
    	var warehouse_id=$('#warehouse_id').val();
    	var product_id=$('#custom_item_select').val();
    	var maxquanitty=0;
    	//Change option warehouse
        if($.isNumeric(warehouse_id) && $.isNumeric(product_id)) {
            $.ajax({
                url : admin_url + 'warehouses/getQuantityPIW/' + warehouse_id + '/' + product_id,
                dataType : 'json',
            })
            .done(function(data){

            	if(data!=null && data!=false)
            	{
            		maxquanitty=data.maximum_quantity-data.total_quantity;
            		$('#warehouse_id option:selected').attr('data-store',maxquanitty);	
            	}
            	//Change data-store mainQuantity Input
				$('table tbody tr.main').find('input.mainQuantity').attr('data-store',maxquanitty);
            });
        }
    });
	var calculateTotal = (currentInput) => {
		currentInput = $(currentInput);		
		let soLuong = currentInput.parents('tr').find('.mainQuantity'); 
		let gia = currentInput.parents('tr').find('.mainPriceBuy'); 
		let tdTong = gia.parent().find(' + td');
		let tdtax = gia.parent().find(' + td + td');
		let tdmoneytax = gia.parent().find(' + td + td +td');
		let tong = String(soLuong.val()).replace(/\,/g, '') * String(gia.val()).replace(/\,/g, '');
		let vartax=$(tdtax).html().replace(/\,|%/g, '');
		tdTong.text(formatNumber( tong ) );
		tdmoneytax.text(formatNumber( (tong*vartax) / 100 ));

        refreshTotal();
	};
	$(document).on('keyup', '.mainPriceBuy', (e)=>{
		var currentPriceBuyInput = $(e.currentTarget);
		calculateTotal(e.currentTarget);
	});
    $(document).on('keyup', '.mainQuantity', (e)=>{
        var currentQuantityInput = $(e.currentTarget);
        let elementToCompare;
        if(typeof(currentQuantityInput.attr('data-store')) == 'undefined' )
            elementToCompare = currentQuantityInput.parents('tr').find('input[data-store]');
        else
            elementToCompare = currentQuantityInput;
		
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
        calculateTotal(e.currentTarget);
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
    $('#purchase-form').on('submit', (e)=>{
        if($('input.error').length > 0) {
            e.preventDefault();
            alert_float('danger', 'Giá trị không hợp lệ!');    
        }
        
    });

</script>