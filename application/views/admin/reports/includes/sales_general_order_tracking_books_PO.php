    <div id="general-order-tracking-book-report-PO" class="hide">
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
                  <h3 class="text-muted _total TC">
                     0   
                  </h3>
                  <span class="text-warning"><?=_l('Tiền cọc')?></span>
               </div>
            </div>
         </div>
         <div class="col-md-2 total-column">
            <div class="panel_s">
               <div class="panel-body">
                  <h3 class="text-muted _total TTSO">
                     0      
                  </h3>
                  <span class="text-success"><?=_l('Thanh toán SO')?></span>
               </div>
            </div>
         </div>
         <div class="col-md-2 total-column">
            <div class="panel_s">
               <div class="panel-body">
                  <h3 class="text-muted _total TT">
                     0      
                  </h3>
                  <span class="text-success"><?=_l('Tổng thanh toán')?></span>
               </div>
            </div>
         </div>
         <div class="col-md-2 total-column">
            <div class="panel_s">
               <div class="panel-body">
                  <h3 class="text-muted _total CL">
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
               <label for="PO_status_gen"><?php echo _l('report_invoice_status'); ?></label>
               <select name="PO_status_gen" class="selectpicker" data-width="100%">
                  <option value="" selected><?php echo _l('invoice_status_report_all'); ?></option>
                  <option value="1"><?=_l('Chưa duyệt')?></option>
                  <option value="2"><?=_l('Đã duyệt')?></option>
                  <option value="3"><?=_l('Chưa tạo đơn hàng')?></option>
                  <option value="4"><?=_l('Đang tạo đơn hàng')?></option>
                  <option value="5"><?=_l('Đã tạo đơn hàng')?></option>
               </select>
            </div>
         </div>
          <div class="col-md-3">
           <?php echo render_select('staff_general_order_PO[]',$staff,array('staffid','fullname'),'staff',array(),array('multiple'=>'multiple')) ?>
         </div>
         <div class="col-md-3">
           <?php 
           echo render_select('sale_area_PO', $sale_areas, array('id', 'name'), 'sale_area', !isset($client) ? $default_source_id : $client->address_area, array('data-none-selected-text' => _l('dropdown_non_selected_tex')));
           ?>
         </div>
         <div class="col-md-3">
           <?php 
           echo render_select('objects_group_PO', $objects_groups, array('id', 'name'), 'objects_group', !isset($client) ? $default_source_id : $client->address_area, array('data-none-selected-text' => _l('dropdown_non_selected_tex')));
           ?>
         </div>

         <div class="clearfix"></div>
         <!-- <style>
            .dt-buttons .buttons-collection{
               display: none!important;
            }
         </style> -->
         <div id="btn_excel">
         <a href="<?=admin_url('reports/general_order_tracking_book_report_PO_pdf')?>" class="btn mright5 btn-info pull-left display-block" >Xuất Excel</a>
      </div>
      </div>
      <table class="table table table-striped table-general-order-tracking-book-PO-report">
         <thead>
            <tr>
               <th><?php echo _l('view_date'); ?></th>
               <th><?php echo _l('code_noo'); ?></th>               
               <th><?php echo _l('customer_name'); ?></th>
               <th><?php echo _l('sale_quantity'); ?></th>
               <th><?php echo _l('sale_revenue'); ?></th>
               <th><?php echo _l('total_money_deposit'); ?></th>
               <th><?php echo _l('total_amount_payment_SO'); ?></th>
               <th><?php echo _l('total_amount_payment'); ?></th>
               <th><?php echo _l('total_amount_left'); ?></th>
               <th><?php echo _l('billers'); ?></th>
            </tr>
         </thead>
         <tbody></tbody>
         <tfoot>
            <tr>
               <td style="text-transform: uppercase;text-align: center;"></td>
               <td></td>
               <td></td>
               <td class="SL text-center"></td>
               <td class="DSB text-right"></td> 
               <td class="TC text-right"></td>
               <td class="TTSO text-right"></td>
               <td class="TT text-right"></td>
               <td class="CL text-right"></td>        
               <td></td>
            </tr>
         </tfoot>
      </table>
   </div>
