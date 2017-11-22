    <div id="inventory-report" class="hide">
      <div class="row">
         <div class="clearfix"></div>
         
         <div class='col-md-4'>
            <?php echo render_select('warehouse_invenroty',$warehouses,array('warehouseid','warehouse'),'Kho',array(),array('multiple'=>true)) ?>
         </div>
         <div class='col-md-4'>
            <?php echo render_select('product_category', $categories, array('id', 'category'), 'Danh mục cha'); ?>
         </div>
         <div class='col-md-4'><?php echo render_date_input('time_inventory','Thời gian')?></div>
         <!-- <a href="<?=admin_url('reports/genernal_receivable_debts_report_pdf')?>" class="btn mright5 btn-info pull-left display-block">Xuất Excel</a> -->
      </div>
       <table class="table table table-striped table-inventory-report">
         <thead>
            <tr class="bold" style="text-align: center;font-weight: bold;">
               <th style="text-align: center;"><?php echo _l('item_code'); ?></th>
               <th style="text-align: center;"><?php echo _l('item_name'); ?></th>
               <?php foreach($warehouses as $key=> $warehouse) {?>
                  <th style="text-align: center;" rol="<?=$key+3?>" class="row_<?=$warehouse['warehouseid']?>"><?=$warehouse['warehouse'];?></th>
               <?php }?>
               <th style="text-align: center;"><?php echo _l('Hàng có thể bán'); ?></th>
            </tr>
         </thead>
         <tbody>
         </tbody>
        
      </table> 
      <!-- <?php
         $summary_columns = array(
           _l('item_code'),
           _l('item_name'),
           );
         foreach($warehouses as $warehouse) {
           $summary_columns[] = $warehouse['warehouse'];
         }
         $summary_columns[] = _l('Hàng có thể bán');
         render_datatable($summary_columns,
           'inventory-report'); 
           ?> -->
   </div>
