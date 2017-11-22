    <div id="genernal-receivables-suppliers-debts-report" class="">
      <div class="row">
         <div class="clearfix"></div>
         <style>
            .dt-buttons .buttons-collection{
               display: none!important;
            }
         </style>
         <a href="<?=admin_url('reports/genernal_receivables_suppliers_debts_report_pdf')?>" class="btn mright5 btn-info pull-left display-block">Xuất Excel</a>
      </div>
      <table class="table table table-striped table-genernal-receivables-suppliers-debts-report">
         <thead>
            <tr>
               <th rowspan="2"><?php echo _l('suppliers_code'); ?></th>
               <th rowspan="2"><?php echo _l('suppliers_name'); ?></th>
               <th rowspan="2"><?php echo _l('tk_debt'); ?></th>
               <th colspan="2">SỐ DƯ ĐẦU KỲ</th>
               <th colspan="2">SỐ PHÁT SINH</th>
               <th colspan="2">SỐ CUỐI KỲ</th>
            </tr>
            <tr>
               <th><?php echo _l('debt_no'); ?></th>
               <th><?php echo _l('debt_co'); ?></th>
               <th><?php echo _l('incurred_debt_no'); ?></th>
               <th><?php echo _l('incurred_debt_co'); ?></th>
               <th><?php echo _l('surplus_debt_no'); ?></th>
               <th><?php echo _l('surplus_debt_co'); ?></th>
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
            </tr>
         </tfoot>
      </table>
   </div>
