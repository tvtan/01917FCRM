<?php 
/**
 * Included in application/views/admin/sales/order_detail.php
 */
?>
<script type="text/javascript">
function receipt(client_id, sale_id,type='PO',isDeposit=false) 
{
	if (typeof(sale_id) == 'undefined') 
		{
		sale_id = '';
		}
		$.post(admin_url + 'sale_orders/receipt/' + client_id + '/' + sale_id+'/'+type+'/'+isDeposit).done(function(response) {
		$('#receipt_data').html(response);

		$('#receipt').modal({show:true,backdrop:'static'});
		// $('body').on('shown.bs.modal', '#receipt', function() {
		// var contactid = $(this).find('input[name="contactid"]').val();
		// if (sale_id == '') {
		// $('#receipt').find('input[name="firstname"]').focus();
		// }
		// });
		init_selectpicker();
		init_datepicker();
		$('#id_account_person').attr('disabled', 'disabled').parents('div.form-group').hide();
		$('[name="items[0][contract]"]').attr('name', 'items[0][sales]');
		// custom_fields_hyperlink();
		validate_receipt_form();
			$(document).on('change', '[name="receipts"][value="receipt"]:checked', function(e) {
				// Chọn phiếu thu
				// Ẩn
				$('#id_account_person').attr('disabled', 'disabled').parents('div.form-group').hide();
				$('#payment_mode').attr('disabled', 'disabled').parents('div.form-group').hide();
				// Hiện
				$('#address').removeAttr('disabled').parents('div.form-group').show();
				$('input[name$="[total]"]').removeAttr('disabled');
				$('#receiver').parents('div.form-group').find('label').text("<?=_l('_receiver')?>");				
				$('[name="items[0][contract]"]').attr('name', 'items[0][sales]');
				$('#code_vouchers').val($('.code_vouchers1').text());
			});
			$(document).on('change', '[name="receipts"][value="report_have"]:checked', function(e) {
				// Chọn báo có
				// Ẩn
				$('#address').attr('disabled', 'disabled').parents('div.form-group').hide();
				$('input[name$="[total]"]').attr('disabled', 'disabled');
				
				// Hiện
				$('#id_account_person').removeAttr('disabled').parents('div.form-group').show();
				$('#receiver').parents('div.form-group').find('label').text("<?=_l('__receiver_')?>");				
				$('[name="items[0][sales]"]').attr('name', 'items[0][contract]');
				$('input[name$="[discount]"]').attr('disabled','disabled');
				$('[name$="[tk_ck]"]').attr('disabled','disabled');
				$('#code_vouchers').val($('.code_vouchers2').text());
				$('#payment_mode').removeAttr('disabled').parents('div.form-group').show();
			});
			
			$('[name="receipts"][value="report_have"]:checked').trigger('change');
			$('[name="receipts"][value="receipt"]:checked').trigger('change');
			
		}).fail(function(error) {
		var response = JSON.parse(error.responseText);
		alert_float('danger', response.message);
	});

		$('body').on('hidden.bs.modal', '#receipt', function() {
		       $('#receipt_data').empty();
		     });
}

// function formatNumBerKU(id_input)
// {
// 	console.log(id_input);
//     key="";
//     money=$(id_input).val().replace(/[^\d\.\,\-]/g, '');
//     a=money.split(".");
//     $.each(a , function (index, value){
//         key=key+value;
//     });
//     $(id_input).val(formatNumber(key, '.', '.'));
// }

function validate_receipt_form() {
	var subtotal=$(['name="item[0][subtotal]"']);
 _validate_form('#receipt-form', {
   date_of_accounting: 'required',
   day_vouchers: 'required',
   day_vouchers: 'required',
	code_vouchers: 'required',
	id_client: 'required',
	subtotal: 'required',
	// address: 'required',
	receiver: 'required',
	id_account_person: 'required',
   
 });
}

function receiptSO(client_id, sale_id,type='PO',isDeposit=false) 
{
	if (typeof(sale_id) == 'undefined') 
		{
		sale_id = '';
		}
		$.post(admin_url + 'sales/receipt/' + client_id + '/' + sale_id+'/'+isDeposit).done(function(response) {
		$('#receipt_data').html(response);

		$('#receipt').modal({show:true,backdrop:'static'});
		// $('body').on('shown.bs.modal', '#receipt', function() {
		// var contactid = $(this).find('input[name="contactid"]').val();
		// if (sale_id == '') {
		// $('#receipt').find('input[name="firstname"]').focus();
		// }
		// });
		init_selectpicker();
		init_datepicker();
		$('#id_account_person').attr('disabled', 'disabled').parents('div.form-group').hide();
		$('[name="items[0][contract]"]').attr('name', 'items[0][sales]');
		// custom_fields_hyperlink();
		validate_receipt_form();
			$(document).on('change', '[name="receipts"][value="receipt"]:checked', function(e) {
				// Chọn phiếu thu
				// Ẩn
				$('#id_account_person').attr('disabled', 'disabled').parents('div.form-group').hide();
				$('#payment_mode').attr('disabled', 'disabled').parents('div.form-group').hide();
				// Hiện
				$('#address').removeAttr('disabled').parents('div.form-group').show();
				$('input[name$="[total]"]').removeAttr('disabled');
				$('#receiver').parents('div.form-group').find('label').text("<?=_l('_receiver')?>");				
				$('[name="items[0][contract]"]').attr('name', 'items[0][sales]');
				$('#code_vouchers').val($('.code_vouchers1').text());
			});
			$(document).on('change', '[name="receipts"][value="report_have"]:checked', function(e) {
				// Chọn báo có
				// Ẩn
				$('#address').attr('disabled', 'disabled').parents('div.form-group').hide();
				$('input[name$="[total]"]').attr('disabled', 'disabled');
				
				// Hiện
				$('#id_account_person').removeAttr('disabled').parents('div.form-group').show();
				$('#receiver').parents('div.form-group').find('label').text("<?=_l('__receiver_')?>");				
				$('[name="items[0][sales]"]').attr('name', 'items[0][contract]');
				$('input[name$="[discount]"]').attr('disabled','disabled');
				$('[name$="[tk_ck]"]').attr('disabled','disabled');
				$('#code_vouchers').val($('.code_vouchers2').text());
				$('#payment_mode').removeAttr('disabled').parents('div.form-group').show();
			});
			
			$('[name="receipts"][value="report_have"]:checked').trigger('change');
			$('[name="receipts"][value="receipt"]:checked').trigger('change');
			
		}).fail(function(error) {
		var response = JSON.parse(error.responseText);
		alert_float('danger', response.message);
	});
		
		$('body').on('hidden.bs.modal', '#receipt', function() {
		       $('#receipt_data').empty();
		     });
}

</script>