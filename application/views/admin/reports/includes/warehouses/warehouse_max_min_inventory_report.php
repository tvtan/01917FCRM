    <div id="max-min-inventory-report" class="hide">
      <div class="row">
         <div class="clearfix"></div>
         
        <!--  <a href="<?=admin_url('reports/genernal_receivable_debts_report_pdf')?>" class="btn mright5 btn-info pull-left display-block">Xuất Excel</a> -->
      </div>
      <table class="table table table-striped table-max-min-inventory-report">
         <thead>
            <tr>
               <th class="text-center" rowspan="2"><?=_l('product_code')?></th>
               <th class="text-center" rowspan="2"><?=_l('item_name')?></th>
               <th class="text-center" rowspan="2"><?=_l('unit_name')?></th>
               <th class="text-center" rowspan="2"><?=_l('warehouse_status')?></th>
               <th class="text-center" colspan="3"><?=_l('max_min_term')?></th>
            </tr>
            <tr>
               <th class="text-center"><?php echo _l('quantity_in_warehouse_term'); ?></th>
               <th class="text-center"><?php echo _l('quantity_min_term'); ?></th>
               <th class="text-center"><?php echo _l('quantity_max_term'); ?></th>
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
            </tr>
         </tfoot>
      </table>
   </div>
