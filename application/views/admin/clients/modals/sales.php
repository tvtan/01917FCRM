<div class="row">
    <div class="col-md-12">
        <?php 
            $sales_id = [];
            if (empty(getSOIds($client->userid))) {
                $totalReceive = 0;
            }else{
                foreach (getSOIds($client->userid) as $value) {
                    $sales_id[] = $value->id;
                }
                $totalReceive = getTotalMoneyReceiveFromCustomerBySalesId($sales_id, 'SO');
            }

         ?>

        <div class="row">
           <div class="col-md-3 total-column">
              <div class="panel_s">
                 <div class="panel-body">
                    <h3 class="text-muted _total total">
                       <?php echo format_money(getTotalMoneyClient($client->userid)->total) ; ?>    
                    </h3>
                    <span class="text-muted">Tổng giá trị</span>
                 </div>
              </div>
           </div>
           <div class="col-md-3 total-column">
              <div class="panel_s">
                 <div class="panel-body">
                    <h3 class="text-muted _total deposit">
                       0      
                    </h3>
                    <span class="text-info">Tiền cọc</span>
                 </div>
              </div>
           </div>
           <div class="col-md-3 total-column">
              <div class="panel_s">
                 <div class="panel-body">
                    <h3 class="text-muted _total payment">
                       <?php echo format_money($totalReceive) ?>    
                    </h3>
                    <span class="text-warning">Thanh toán</span>
                 </div>
              </div>
           </div>
           <div class="col-md-3 total-column">
              <div class="panel_s">
                 <div class="panel-body">
                    <h3 class="text-muted _total left">
                       0      
                    </h3>
                    <span class="text-success">Còn lại</span>
                 </div>
              </div>
           </div>
           
        </div>

        <div class="clearfix"></div>
        <a target="_blank" href="<?php echo admin_url('sale_orders/sale_detail/?customer_id='.$client->userid); ?>"  class="btn btn-info mbot25<?php if($client->active == 0){echo ' disabled';} ?>"><?php echo _l('new_project'); ?></a>
        <div class="panel_s">
            <?php render_datatable(array(
                _l('#'),
                _l('code_noo'),
                _l('view_date'),
                _l('total_amount'),
                _l('total_money_deposit'),
                _l('total_amount_payment'),
                _l('total_amount_left'),
                _l('billers'),
                _l('options')
            ),'sales-list'); ?>
        </div>
    </div>
</div>
<script>
  function create_sales_lead(client){
    initDataTable('.table-sales-list', admin_url+'sales/get_sales_lead/'+client, [1], [1],'');
  }
$('.table-sales-list').on('draw.dt', function() {
     var proposalsReportTable = $(this).DataTable();
     var sums = proposalsReportTable.ajax.json().sums;
     $('.text-muted.total').text(sums.total);
     $('.text-muted.deposit').text(sums.deposit);
     $('.text-muted.payment').text(sums.payment);
     $('.text-muted.left').text(sums.left);
   });

</script>