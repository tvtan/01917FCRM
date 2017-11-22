    <div id="bank-deposit-books" class="hide">
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
      </div>
      <table class="table table table-striped table-bank-deposit-books-report">
         <thead>
            <tr>
               <th><?php echo _l('account_date'); ?></th>
               <th><?php echo _l('view_date'); ?></th>               
               <th><?php echo _l('code_noo'); ?></th>
               <th><?php echo _l('customers_suppliers'); ?></th>
               <th><?php echo _l('references'); ?></th>
               <th><?php echo _l('orders_explan'); ?></th>
               <th><?php echo _l('reciprocal_tk'); ?></th>
               <th><?php echo _l('have_'); ?></th>
               <th><?php echo _l('debit_'); ?></th>
               <th><?php echo _l('rest_'); ?></th>
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
               <td class="SPSN"></td>
               <td class="SPSC"></td>
               <td class="ST"></td>
            </tr>
         </tfoot>
      </table>
   </div>
