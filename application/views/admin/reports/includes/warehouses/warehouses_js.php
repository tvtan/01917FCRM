<script type="text/javascript">

$(function() {
  $('select[name="warehouse_invenroty"]').on('change', function() {
      var row=$(this).val();
      var td_show=[];
        $('.table-inventory-report tbody tr td').removeClass('alert-danger');
        $.each(row,function(i,v){
          td_show.push($('.row_'+v).attr('rol'));
        })
        var fulltr=$('.table-inventory-report tbody tr');
        $.each(fulltr,function(i,v){
          $.each(td_show,function(ii,vv){
            $(v).find('td:nth-child('+vv+')').addClass('alert-danger');
            console.log($(v).find('td:nth-child('+vv+')'));
          })
        });
  });
})


  var report_from = $('input[name="report-from"]');
 var report_to = $('input[name="report-to"]');
 var report_customers = $('#customers-report');
 var report_customers_groups = $('#customers-group');
 var report_from_choose = $('#report-time');
 var date_range = $('#date-range');
 var fnServerParams = {
   "report_months": '[name="months-report"]',
   "report_from": '[name="report-from"]',
   "report_to": '[name="report-to"]',
   "report_currency": '[name="currency"]',   
   "years_report": '[name="years_report"]',
   "warehouse_invenroty": '[name="warehouse_invenroty"]',
    "product_category": '[name="product_category"]',
    "warehouse_invenroty_import": '[name="warehouse_invenroty_import"]',
    "product_category_import": '[name="product_category_import"]',
     "warehouse_invenroty_transfer": '[name="warehouse_invenroty_transfer"]',
    "product_category_transfer": '[name="product_category_transfer"]',
    "warehouse_invenroty_export": '[name="warehouse_invenroty_export"]',
    "product_category_export": '[name="product_category_export"]',
    "warehouse_invenroty_PO": '[name="warehouse_invenroty_PO"]',
    "product_category_PO": '[name="product_category_PO"]',
    "warehouse_invenroty_SO": '[name="warehouse_invenroty_SO"]',
    "product_category_SO": '[name="product_category_SO"]',
    "warehouse_invenroty_Quote": '[name="warehouse_invenroty_Quote"]',
    "product_category_Quote": '[name="product_category_Quote"]',
 }

 report_from.on('change', function() {
     var val = $(this).val();
     var report_to_val = report_to.val();
     if (val != '') {
       report_to.attr('disabled', false);
       if (report_to_val != '') {
         gen_reports();
       }
     } else {
       report_to.attr('disabled', true);
     }
   });

 $(function() {
   $('select[name="product_category"],select[name="warehouse_invenroty"],select[name="product_category_import"],select[name="warehouse_invenroty_import"],select[name="product_category_transfer"],select[name="warehouse_invenroty_transfer"],select[name="product_category_export"],select[name="warehouse_invenroty_export"],select[name="product_category_PO"],select[name="warehouse_invenroty_PO"],select[name="product_category_SO"],select[name="warehouse_invenroty_SO"],select[name="product_category_Quote"],select[name="warehouse_invenroty_Quote"]').on('change', function() {
     gen_reports();
   });
 });

   report_to.on('change', function() {
     var val = $(this).val();
     if (val != '') {
       gen_reports();
     }
   });

   $('select[name="months-report"]').on('change', function() {
     var val = $(this).val();
     report_to.attr('disabled', true);
     report_to.val('');
     report_from.val('');
     if (val == 'custom') {
       date_range.addClass('fadeIn').removeClass('hide');
       return;
     } else {
       if (!date_range.hasClass('hide')) {
         date_range.removeClass('fadeIn').addClass('hide');
       }
     }
     gen_reports();
   });
  function init_report(e, type) {
  $('#report_tiltle').text($(e).text());   
  var report_wrapper = $('#report');
   if (report_wrapper.hasClass('hide')) {
     report_wrapper.removeClass('hide');
   }
  $('#stock-card-report').addClass('hide');
  $('#detail-goods-book-report').addClass('hide');
  $('#warehouse-sumary-report').addClass('hide');
  $('#warehouse-import-report').addClass('hide');
  $('#inventory-report').addClass('hide');
  $('#max-min-inventory-report').addClass('hide');
  $('select[name="months-report"]').selectpicker('val', '');
  $('#warehouse-transfer-report').addClass('hide');
  $('#warehouse-export-report').addClass('hide');
  $('#warehouse-PO-report').addClass('hide');
  $('#warehouse-SO-report').addClass('hide');
  $('#warehouse-Quote-report').addClass('hide');
       // Clear custom date picker
       report_to.val('');
       report_from.val('');
       $('#currency').removeClass('hide');
       if (type != 'total-income' && type != 'payment-modes' && type!='inventory-report') {
         report_from_choose.removeClass('hide');
       }

       if (type =='order-tracking-monthly-report') {
         report_year_choose.removeClass('hide');
         report_from_choose.addClass('hide');
       }
       if (type=='inventory-report') {
         report_from_choose.addClass('hide');
       }

       if (type == 'stock-card-report') {
         $('#stock-card-report').removeClass('hide');
       }
      if (type == 'detail-goods-book-report') {
         $('#detail-goods-book-report').removeClass('hide');
       }
       if (type == 'warehouse-sumary-report') {
         $('#warehouse-sumary-report').removeClass('hide');
       }
       if (type == 'warehouse-import-report') {
         $('#warehouse-import-report').removeClass('hide');
       }
       if (type == 'inventory-report') {
         $('#inventory-report').removeClass('hide');
       }
       if (type == 'max-min-inventory-report') {
         $('#max-min-inventory-report').removeClass('hide');
       }
       if (type == 'warehouse-transfer-report') {
         $('#warehouse-transfer-report').removeClass('hide');
       }
       if (type == 'warehouse-export-report') {
         $('#warehouse-export-report').removeClass('hide');
       }
       if (type == 'warehouse-PO-detail-report') {
         $('#warehouse-PO-report').removeClass('hide');
       }
       if (type == 'warehouse-SO-detail-report') {
         $('#warehouse-SO-report').removeClass('hide');
       }
       if (type == 'warehouse-Quote-report') {
         $('#warehouse-Quote-report').removeClass('hide');
       }
      gen_reports();
    }
  // Main generate report function
   function gen_reports() { 
     if (!$('#stock-card-report').hasClass('hide')) { 
       stock_card_report();
     }
     if (!$('#detail-goods-book-report').hasClass('hide')) {
       detail_goods_book_report();
     }
     if (!$('#warehouse-sumary-report').hasClass('hide')) {
       warehouse_sumary_report();
     }
     if (!$('#warehouse-import-report').hasClass('hide')) {
       warehouse_import_report();
     }
     if (!$('#inventory-report').hasClass('hide')) {
       inventory_report();
     }
     if (!$('#max-min-inventory-report').hasClass('hide')) {
       warehouse_max_min_inventory_report();
     }
     if (!$('#warehouse-transfer-report').hasClass('hide')) {
       warehouse_transfer_report();
     }
      if (!$('#warehouse-export-report').hasClass('hide')) {
       warehouse_export_report();
     }
     if (!$('#warehouse-PO-report').hasClass('hide')) {
       warehouse_PO_report();
     }
     if (!$('#warehouse-SO-report').hasClass('hide')) {
       warehouse_SO_report();
     }
     if (!$('#warehouse-Quote-report').hasClass('hide')) {
       warehouse_Quote_report();
     }
  }
  function stock_card_report() {
    if ($.fn.DataTable.isDataTable('.table-stock-card-report')) {
     $('.table-stock-card-report').DataTable().destroy();
    }
     initDataTableFixedHeader('.table-stock-card-report', admin_url + 'reports/stock_card_report', false, false, fnServerParams, [0, 'ASC']);
  }
  function detail_goods_book_report() {
    if ($.fn.DataTable.isDataTable('.table-detail-goods-book-report')) {
     $('.table-detail-goods-book-report').DataTable().destroy();
    }
     initDataTableFixedHeader('.table-detail-goods-book-report', admin_url + 'reports/detail_goods_book_report', false, false, fnServerParams, [0, 'ASC']);
  }
  function warehouse_sumary_report() {
    if ($.fn.DataTable.isDataTable('.table-warehouse-sumary-report')) {
     $('.table-warehouse-sumary-report').DataTable().destroy();
    }
     initDataTableFixedHeader('.table-warehouse-sumary-report', admin_url + 'reports/warehouse_sumary_report', false, false, fnServerParams, [0, 'ASC']);
   }

   function inventory_report() {
    if ($.fn.DataTable.isDataTable('.table-inventory-report')) {
     $('.table-inventory-report').DataTable().destroy();
    }
     initDataTableFixedHeader('.table-inventory-report', admin_url + 'reports/inventory_report', false, false, fnServerParams, [0, 'DESC']);
   }

   function warehouse_max_min_inventory_report() {

    if ($.fn.DataTable.isDataTable('.table-max-min-inventory-report')) {
     $('.table-max-min-inventory-report').DataTable().destroy();
    }
     initDataTableFixedHeader('.table-max-min-inventory-report', admin_url + 'reports/warehouse_max_min_inventory_report', false, false, fnServerParams, [0, 'ASC']);
   }

   function warehouse_import_report() {
    if ($.fn.DataTable.isDataTable('.table-warehouse-import-report')) {
     $('.table-warehouse-import-report').DataTable().destroy();
    }
     initDataTableFixedHeader('.table-warehouse-import-report', admin_url + 'reports/warehouse_import_report', false, false, fnServerParams, [0, 'ASC']);
   }

   function warehouse_transfer_report() {
    if ($.fn.DataTable.isDataTable('.table-warehouse-transfer-report')) {
     $('.table-warehouse-transfer-report').DataTable().destroy();
    }
     initDataTableFixedHeader('.table-warehouse-transfer-report', admin_url + 'reports/warehouse_transfer_report', false, false, fnServerParams, [0, 'ASC']);
   }

   function warehouse_export_report() {
    if ($.fn.DataTable.isDataTable('.table-warehouse-export-report')) {
     $('.table-warehouse-export-report').DataTable().destroy();
    }
     initDataTableFixedHeader('.table-warehouse-export-report', admin_url + 'reports/warehouse_export_report', false, false, fnServerParams, [0, 'ASC']);
   }
  function warehouse_PO_report() {
    if ($.fn.DataTable.isDataTable('.table-warehouse-PO-report')) {
     $('.table-warehouse-PO-report').DataTable().destroy();
    }
     initDataTableFixedHeader('.table-warehouse-PO-report', admin_url + 'reports/warehouse_PO_report', false, false, fnServerParams, [0, 'ASC']);
   }

   function warehouse_SO_report() {
    if ($.fn.DataTable.isDataTable('.table-warehouse-SO-report')) {
     $('.table-warehouse-SO-report').DataTable().destroy();
    }
     initDataTableFixedHeader('.table-warehouse-SO-report', admin_url + 'reports/warehouse_SO_report', false, false, fnServerParams, [0, 'ASC']);
   }

   function warehouse_Quote_report() {
    if ($.fn.DataTable.isDataTable('.table-warehouse-Quote-report')) {
     $('.table-warehouse-Quote-report').DataTable().destroy();
    }
     initDataTableFixedHeader('.table-warehouse-Quote-report', admin_url + 'reports/warehouse_Quote_report', false, false, fnServerParams, [0, 'ASC']);
   }


$('.table-stock-card-report').on('draw.dt', function() {
    var invoiceReportsTable = $(this).DataTable();
    row_header();
});

function row_header()
{
    var class_tr=$('.alert-header');
    $.each(class_tr,function(index,value){
        var data=$(value).find('td').first().html();
        $(value).find('td:eq(5), td:eq(6), td:eq(7)').remove();
        $(value).find('td:eq(2), td:eq(3), td:eq(4)').attr('colspan', 2);
    })
}
$('.table-warehouse-SO-report').on('draw.dt', function() {
     var paymentReceivedReportsTable = $(this).DataTable();
     var sums = paymentReceivedReportsTable.ajax.json().sums;
     $(this).find('tfoot').addClass('bold');
     $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?>");
     $(this).find('tfoot td.quantity').html(sums.quantity);
     $(this).find('tfoot td.quantity').addClass('text-center');
   });

$('.table-warehouse-PO-report').on('draw.dt', function() {
     var paymentReceivedReportsTable = $(this).DataTable();
     var sums = paymentReceivedReportsTable.ajax.json().sums;
     $(this).find('tfoot').addClass('bold');
     $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?>");
     $(this).find('tfoot td.quantity').html(sums.quantity);
     $(this).find('tfoot td.quantity').addClass('text-center');
   });
$('.table-warehouse-transfer-report').on('draw.dt', function() {
     var paymentReceivedReportsTable = $(this).DataTable();
     var sums = paymentReceivedReportsTable.ajax.json().sums;
     $(this).find('tfoot').addClass('bold');
     $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?>");
     $(this).find('tfoot td.quantity').html(sums.quantity);
     $(this).find('tfoot td.quantity').addClass('text-center');
     $(this).find('tfoot td.quantity_net').html(sums.quantity_net);
     $(this).find('tfoot td.quantity_net').addClass('text-center');
   });
$('.table-warehouse-export-report').on('draw.dt', function() {
     var paymentReceivedReportsTable = $(this).DataTable();
     var sums = paymentReceivedReportsTable.ajax.json().sums;
     $(this).find('tfoot').addClass('bold');
     $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?>");
     $(this).find('tfoot td.quantity').html(sums.quantity);
     $(this).find('tfoot td.quantity').addClass('text-center');
     $(this).find('tfoot td.quantity_net').html(sums.quantity_net);
     $(this).find('tfoot td.quantity_net').addClass('text-center');
   });
$('.table-warehouse-import-report').on('draw.dt', function() {
     var paymentReceivedReportsTable = $(this).DataTable();
     var sums = paymentReceivedReportsTable.ajax.json().sums;
     $(this).find('tfoot').addClass('bold');
     $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?>");
     $(this).find('tfoot td.quantity').html(sums.quantity);
     $(this).find('tfoot td.quantity').addClass('text-center');
     $(this).find('tfoot td.quantity_net').html(sums.quantity_net);
     $(this).find('tfoot td.quantity_net').addClass('text-center');
   });

$('.table-warehouse-Quote-report').on('draw.dt', function() {
     var paymentReceivedReportsTable = $(this).DataTable();
     var sums = paymentReceivedReportsTable.ajax.json().sums;
     $(this).find('tfoot').addClass('bold');
     $(this).find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?>");
     $(this).find('tfoot td.quantity').html(sums.quantity);
     $(this).find('tfoot td.quantity').addClass('text-center');
   });

</script>