    <div id="warehouse-sumary-report" class="hide">
      <div class="row">
         <div class="clearfix"></div>
     
        <!--  <a href="<?=admin_url('reports/genernal_receivable_debts_report_pdf')?>" class="btn mright5 btn-info pull-left display-block">Xuất Excel</a> -->
      </div>
      <table class="table table table-striped table-warehouse-sumary-report">
         <thead>
            <tr>
               <th class="text-center" rowspan="2"><?=_l('product_code')?></th>
               <th class="text-center" rowspan="2"><?=_l('item_name')?></th>
               <th class="text-center" rowspan="2"><?=_l('unit_name')?></th>
               <th class="text-center" colspan="2"><?=_l('begin_term')?></th>
               <th class="text-center" colspan="2"><?=_l('import_term')?></th>
               <th class="text-center" colspan="2"><?=_l('export_term')?></th>
               <th class="text-center" colspan="2"><?=_l('end_term')?></th>
            </tr>
            <tr>
               <th class="text-center"><?php echo _l('quantity_term'); ?></th>
               <th class="text-center"><?php echo _l('value_term'); ?></th>
               <th class="text-center"><?php echo _l('quantity_term'); ?></th>
               <th class="text-center"><?php echo _l('value_term'); ?></th>
               <th class="text-center"><?php echo _l('quantity_term'); ?></th>
               <th class="text-center"><?php echo _l('value_term'); ?></th>
               <th class="text-center"><?php echo _l('quantity_term'); ?></th>
               <th class="text-center"><?php echo _l('value_term'); ?></th>
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
            </tr>
         </tfoot>
      </table>
   </div>
