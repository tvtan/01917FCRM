    <div id="general-order-tracking-book-report" class="hide">
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
         <div class="col-md-2 total-column">
            <div class="panel_s">
               <div class="panel-body">
                  <h3 class="text-muted _total DTT">
                     0   
                  </h3>
                  <span class="text-warning"><?=_l('Doanh thu thuần')?></span>
               </div>
            </div>
         </div>
         <div class="col-md-2 total-column">
            <div class="panel_s">
               <div class="panel-body">
                  <h3 class="text-muted _total DT">
                     0      
                  </h3>
                  <span class="text-success"><?=_l('Đã thanh toán')?></span>
               </div>
            </div>
         </div>
         <div class="col-md-2 total-column">
            <div class="panel_s">
               <div class="panel-body">
                  <h3 class="text-muted _total CT">
                     0      
                  </h3>
                  <span class="text-success"><?=_l('Còn lại')?></span>
               </div>
            </div>
         </div>
      </div>
      <hr />
      <div class="row">

         <div class="col-md-3">
            <div class="form-group">
               <label for="SO_status_gen"><?php echo _l('report_invoice_status'); ?></label>
               <select name="SO_status_gen" class="selectpicker"  data-width="100%">
                  <option value="" selected><?php echo _l('invoice_status_report_all'); ?></option>
                  <option value="1"><?= _l('Chưa duyệt')?></option>
                  <option value="2"><?= _l('Đã duyệt')?></option>
                  <option value="3"><?= _l('Chưa tạo phiếu xuất')?></option>
                  <option value="4"><?= _l('Đang tạo phiếu xuất')?></option>
                  <option value="5"><?= _l('Đã tạo phiếu xuất')?></option>
                  <!-- <option value="6"><?= _l('Giao hàng')?></option> -->
                  <option value="7"><?= _l('Thanh toán')?></option>
               </select>
            </div>
         </div>
          <div class="col-md-3">
           <?php echo render_select('staff_general_order_SO_gen[]',$staff,array('staffid','fullname'),'staff',array(),array('multiple'=>'multiple')) ?>
         </div>
         <div class="col-md-3">
           <?php 
           echo render_select('sale_area_SO_gen', $sale_areas, array('id', 'name'), 'sale_area', !isset($client) ? $default_source_id : $client->address_area, array('data-none-selected-text' => _l('dropdown_non_selected_tex')));
           ?>
         </div>
         <div class="col-md-3">
           <?php 
           echo render_select('objects_group_SO_gen', $objects_groups, array('id', 'name'), 'objects_group', !isset($client) ? $default_source_id : $client->address_area, array('data-none-selected-text' => _l('dropdown_non_selected_tex')));
           ?>
         </div>

         <div class="clearfix"></div>
         <!-- <style>
            .dt-buttons .buttons-collection{
               display: none!important;
            }
         </style> -->
         <a href="<?=admin_url('reports/general_order_tracking_book_report_pdf')?>" class="btn mright5 btn-info pull-left display-block">Xuất Excel</a>
      </div>
      <table class="table table table-striped table-general-order-tracking-book-report">
         <thead>
            <tr>
               <th class="text-center"><?php echo _l('view_date'); ?></th>
               <th class="text-center"><?php echo _l('code_noo'); ?></th>  
               <th class="text-center"><?php echo _l('code_noo_PO'); ?></th>               
               <th class="text-center"><?php echo _l('customer_name'); ?></th>
               <th class="text-center"><?php echo _l('sale_quantity'); ?></th>
               <th class="text-center"><?php echo _l('sale_revenue'); ?></th>
               <th class="text-center"><?php echo _l('net_revenue'); ?></th>
               <th class="text-center"><?php echo _l('paid_payment'); ?></th>
               <th class="text-center"><?php echo _l('rest_payment'); ?></th>
               <th class="text-center"><?php echo _l('billers'); ?></th>
            </tr>
         </thead>
         <tbody></tbody>
         <tfoot>
            <tr>
               <td style="text-transform: uppercase;text-align: center;"></td>
               <td></td>
               <td></td>
               <td></td>
               <td class="SL text-center"></td>
               <td class="DSB text-right"></td> 
               <td class="DTT text-right"></td>
               <td class="DT text-right"></td>
               <td class="CT text-right"></td>        
               <td></td>
            </tr>
         </tfoot>
      </table>
   </div>
