    <div id="detail-goods-book-report" class="hide">
      <div class="row">
         <div class="clearfix"></div>
         
         <!-- <a href="<?=admin_url('reports/genernal_receivable_debts_report_pdf')?>" class="btn mright5 btn-info pull-left display-block">Xuất Excel</a> -->
      </div>
      <table class="table table table-striped table-detail-goods-book-report">
         <thead>
            <tr>
               <th class="text-center" rowspan="2"><?=_l('item_name')?></th>
               <th class="text-center" rowspan="2"><?=_l('view_date')?></th>
               <th class="text-center" rowspan="2"><?=_l('code_noo')?></th>
               <th class="text-center" rowspan="2"><?=_l('orders_explan')?></th>
               <th class="text-center" rowspan="2"><?=_l('unit_name')?></th>
               <th class="text-center" rowspan="2"><?=_l('unit_cost')?></th>
               <th class="text-center" colspan="2"><?=_l('import')?></th>
               <th class="text-center" colspan="2"><?=_l('export')?></th>
               <th class="text-center" colspan="2"><?=_l('report_revenue')?></th>
            </tr>
            <tr>
               <th class="text-center"><?php echo _l('report_quantity'); ?></th>
               <th class="text-center"><?php echo _l('report_amount'); ?></th>
               <th class="text-center"><?php echo _l('report_quantity'); ?></th>
               <th class="text-center"><?php echo _l('report_amount'); ?></th>
               <th class="text-center"><?php echo _l('report_quantity'); ?></th>
               <th class="text-center"><?php echo _l('report_amount'); ?></th>
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
               <td></td>
               <td></td>
               <td></td>
               <td></td>
            </tr>
         </tfoot>
      </table>
   </div>
<style type="text/css">
   .alert-warning {
    background-color: #fcf8e3!important;
}
</style>