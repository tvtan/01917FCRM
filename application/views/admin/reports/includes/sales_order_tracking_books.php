    <div id="order-tracking-book-report" class="hide">
      <div class="row">
         <div class="col-md-2 total-column">
            <div class="panel_s">
               <div class="panel-body">
                  <h3 class="text-muted _total SL">
                     0   
                  </h3>
                  <span class="text-muted"><?=_l('Số lượng')?></span>
               </div>
            </div>
         </div>
         <div class="col-md-2 total-column">
            <div class="panel_s">
               <div class="panel-body">
                  <h3 class="text-muted _total DSB">
                     0      
                  </h3>
                  <span class="text-info"><?=_l('Doanh số bán')?></span>
               </div>
            </div>
         </div>
      </div>
      <hr />
      <div class="row">
         <div class="col-md-3">
            <div class="form-group">
               <label for="SO_status"><?php echo _l('report_invoice_status'); ?></label>
               <select name="SO_status" class="selectpicker"  data-width="100%">
                  <option value="" selected><?php echo _l('invoice_status_report_all'); ?></option>
                  <option value="1"><?= _l('Chưa duyệt')?></option>
                  <option value="2"><?= _l('Đã duyệt')?></option>
                  <option value="3"><?= _l('Chưa tạo phiếu xuất')?></option>
                  <option value="4"><?= _l('Đang tạo phiếu xuất')?></option>
                  <option value="5"><?= _l('Đã tạo phiếu xuất')?></option>
                  <option value="6"><?= _l('Giao hàng')?></option>
                  <!-- <option value="7"><?= _l('Thanh toán')?></option> -->
               </select>
            </div>
         </div>
          <div class="col-md-3">
           <?php echo render_select('staff_tracking_book_SO_detail[]',$staff,array('staffid','fullname'),'staff',array(),array('multiple'=>'multiple')) ?>
         </div>

         <div class="col-md-3">
           <?php 
           echo render_select('sale_area_SO_detail', $sale_areas, array('id', 'name'), 'sale_area', !isset($client) ? $default_source_id : $client->address_area, array('data-none-selected-text' => _l('dropdown_non_selected_tex')));
           ?>
         </div>
         <div class="col-md-3">
           <?php 
           echo render_select('objects_group_SO_detail', $objects_groups, array('id', 'name'), 'objects_group', !isset($client) ? $default_source_id : $client->address_area, array('data-none-selected-text' => _l('dropdown_non_selected_tex')));
           ?>
         </div>

         <div class="clearfix"></div>
         <!-- <style>
            .dt-buttons .buttons-collection{
               display: none!important;
            }
         </style> -->
         <a href="<?=admin_url('reports/order_tracking_book_report_pdf')?>" class="btn mright5 btn-info pull-left display-block">Xuất Excel</a>
      </div>
      <table class="table table table-striped table-order-tracking-book-report">
         <thead>
            <tr>
               <th class="text-center"><?php echo _l('view_date'); ?></th>
               <th class="text-center"><?php echo _l('account_date'); ?></th>               
               <th class="text-center"><?php echo _l('code_noo'); ?></th>
               <th class="text-center"><?php echo _l('orders_explan'); ?></th>
               <th class="text-center"><?php echo _l('product_code'); ?></th>
               <th class="text-center"><?php echo _l('product_name'); ?></th>
               <th class="text-center"><?php echo _l('unit_name'); ?></th>
               <th class="text-center"><?php echo _l('quantity'); ?></th>
               <th class="text-center"><?php echo _l('unit_cost'); ?></th>
               <th class="text-center"><?php echo _l('item_dicount_percent'); ?></th>
               <th class="text-center"><?php echo _l('invoice_dicount_percent'); ?></th>
               <th class="text-center"><?php echo _l('sale_revenue'); ?></th>
               <th class="text-center"><?php echo _l('billers'); ?></th>
            </tr>
         </thead>
         <tbody></tbody>
         <tfoot>
           <!--  <tr>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td></td>
               <td class="SL"></td>
               <td></td>
               <td></td>
               <td></td>
               <td class="DSB"></td>
               <td></td>
            </tr> -->
         </tfoot>
      </table>
   </div>
