    <div id="warehouse-Quote-report" class="hide">
      <div class="row">
         <div class="clearfix"></div>
         
         <div class='col-md-4'>
            <?php echo render_select('warehouse_invenroty_Quote',$warehouses,array('warehouseid','warehouse'),'Kho',array(),array('multiple'=>true)) ?>
         </div>
         <div class='col-md-4'>
            <?php echo render_select('product_category_Quote', $categories, array('id', 'category'), 'Danh mục cha'); ?>
         </div>
         <!-- <a href="<?=admin_url('reports/genernal_receivable_debts_report_pdf')?>" class="btn mright5 btn-info pull-left display-block">Xuất Excel</a> -->
      </div>
      <table class="table table table-striped table-warehouse-Quote-report">
         <thead>
            <tr>
               <th class="text-center"><?php echo _l('view_date'); ?></th>
               <th class="text-center"><?php echo _l('code_noo'); ?></th>
               <th class="text-center"><?php echo _l('product_code'); ?></th>
               <th class="text-center"><?php echo _l('product_name'); ?></th>
               <th class="text-center"><?php echo _l('warehouse_name'); ?></th>
               <th class="text-center"><?php echo _l('product_quantity'); ?></th>
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
            </tr>
         </tfoot>
      </table>
   </div>
