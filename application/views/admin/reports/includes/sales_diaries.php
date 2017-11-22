    <div id="diaries-report" class="hide">
      <div class="row">
         
           <div class="col-md-4">
           <?php echo render_select('staff_diaries[]',$staff,array('staffid','fullname'),'staff',array(),array('multiple'=>'multiple')) ?>
         </div>

         <div class="clearfix"></div>
         <!-- <style>
            .dt-buttons .buttons-collection{
               display: none!important;
            }
         </style> -->
         <a href="<?=admin_url('reports/diaries_report_pdf')?>" class="btn mright5 btn-info pull-left display-block">Xuất Excel</a>
      </div>
      <table class="table table table-striped table-diaries-report">
         <thead>
            <tr>
               <th><?php echo _l('view_date'); ?></th>
               <th><?php echo _l('account_date'); ?></th>               
               <th><?php echo _l('code_noo'); ?></th>
               <th><?php echo _l('invoice_date'); ?></th>
               <th><?php echo _l('invoice_no'); ?></th>
               <th><?php echo _l('orders_explan'); ?></th>
               <th><?php echo _l('total_revenue'); ?></th>
               <th><?php echo _l('goods_revenue'); ?></th>
               <th><?php echo _l('others_revenue'); ?></th>
               <th><?php echo _l('discount'); ?></th>
               <th><?php echo _l('returns_value'); ?></th>
               <th><?php echo _l('net_revenue'); ?></th>
               <th><?php echo _l('customer_name'); ?></th>
               <th><?php echo _l('billers'); ?></th>
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
               <td class="TDT"></td>
               <td class="DTHH"></td>
               <td class="DTK"></td>
               <td class="CK"></td>
               <td class="GTTV"></td>
               <td class="DTT"></td>
               <td></td>
               <td></td>
            </tr>
         </tfoot>
      </table>
   </div>
