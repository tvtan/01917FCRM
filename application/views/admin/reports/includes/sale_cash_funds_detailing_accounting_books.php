    <div id="cash-funds-detailing-accounting-books" class="hide">
      <div class="row">
         <div class="col-md-4 total-column">
            <div class="panel_s">
               <div class="panel-body">
                  <h3 class="text-muted _total SPSN">
                     0   
                  </h3>
                  <span class="text-muted"><?=_l('Số phát sinh nợ')?></span>
               </div>
            </div>
         </div>
         <div class="col-md-4 total-column">
            <div class="panel_s">
               <div class="panel-body">
                  <h3 class="text-muted _total SPSC">
                     0      
                  </h3>
                  <span class="text-info"><?=_l('Số phát sinh có')?></span>
               </div>
            </div>
         </div>
         <div class="col-md-4 total-column">
            <div class="panel_s">
               <div class="panel-body">
                  <h3 class="text-muted _total ST">
                     0   
                  </h3>
                  <span class="text-warning"><?=_l('Số tồn')?></span>
               </div>
            </div>
         </div>
      </div>
      <hr />
      <div class="row">
         <div class="clearfix"></div>
         <!-- <style>
            .dt-buttons .buttons-collection{
               display: none!important;
            }
         </style> -->
         <a href="<?=admin_url('reports/cash_funds_detailing_accounting_books_pdf')?>" class="btn mright5 btn-info pull-left display-block">Xuất Excel</a>

      </div>
      <table class="table table table-striped table-cash-funds-detailing-accounting-books-report">
         <thead>
            <tr>
               <th><?php echo _l('account_date'); ?></th>
               <th><?php echo _l('view_date'); ?></th>               
               <th><?php echo _l('code_vouchers_receipts'); ?></th>
               <th><?php echo _l('code_vouchers_votes'); ?></th>
               <th><?php echo _l('customers_suppliers'); ?></th>
               <th><?php echo _l('references'); ?></th>
               <th><?php echo _l('orders_explan'); ?></th>
               <th><?php echo _l('reciprocal_tk'); ?></th>
               <th><?php echo _l('incurred_tk_no'); ?></th>
               <th><?php echo _l('incurred_tk_co'); ?></th>
               <th><?php echo _l('rest_tk'); ?></th>
               <th><?php echo _l('receiver_submitter'); ?></th>
            </tr>
         </thead>
         <tbody></tbody>
         <tfoot>
            <tr>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td class="SPSN"></td>
               <td class="SPSC"></td>
               <td class="ST"></td>
               <td></td>
            </tr>
         </tfoot>
      </table>
   </div>
