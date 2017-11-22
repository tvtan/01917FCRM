 <?php if(isset($client)){ ?>
<h4 class="no-mtop bold"><?php echo _l('contracts_invoices_tab'); ?></h4>
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
<hr />
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
<hr />

<?php
$table_data = array(
    _l('#'),
    _l('code_noo'),
    _l('view_date'),
    _l('total_amount'),
    _l('total_money_deposit'),
    _l('total_amount_payment'),
    _l('total_amount_left'),
    _l('billers')
  );
array_push($table_data,_l('options'));
render_datatable($table_data, 'sales-list'); ?>

<?php } ?> 

