    <div id="warehouse-import-report" class="hide">
      <div class="row">
         <div class="clearfix"></div>
        
         <div class='col-md-4'>
            <?php echo render_select('warehouse_invenroty_import',$warehouses,array('warehouseid','warehouse'),'Kho',array(),array('multiple'=>true)) ?>
         </div>
         <div class='col-md-4'>
            <?php echo render_select('product_category_import', $categories, array('id', 'category'), 'Danh mục cha'); ?>
         </div>
         <!-- <a href="<?=admin_url('reports/genernal_receivable_debts_report_pdf')?>" class="btn mright5 btn-info pull-left display-block">Xuất Excel</a> -->
      </div>
      <table class="table table table-striped table-warehouse-import-report">
         <thead>
            <tr>
               <th rowspan="2" class="text-center"><?php echo _l('view_date'); ?></th>
               <th rowspan="2" class="text-center"><?php echo _l('code_noo'); ?></th>
               <th rowspan="2" class="text-center"><?php echo _l('product_code'); ?></th>
               <th rowspan="2" class="text-center"><?php echo _l('product_name'); ?></th>
               <th rowspan="2" class="text-center"><?php echo _l('warehouse_to'); ?></th>
               <th colspan="2" class="text-center"><?php echo _l('quantity'); ?></th>
            </tr>
            <tr>
               <th class="text-center"><?php echo _l('product_quantity'); ?></th>
               <th class="text-center"><?php echo _l('product_quantity_net'); ?></th>
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
               <td class="quantity"></td>
               <td class="quantity_net"></td>
            </tr>
         </tfoot>
      </table>
   </div>
