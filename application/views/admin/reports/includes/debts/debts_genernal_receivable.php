    <div id="genernal-receivables-debts-report" class="">
      <div class="row">
         <div class="clearfix"></div>
         <style>
            .dt-buttons .buttons-collection{
               display: none!important;
            }
         </style>
         <a href="<?=admin_url('reports/genernal_receivable_debts_report_pdf')?>" class="btn mright5 btn-info pull-left display-block">Xuất Excel</a>
      </div>
      <table class="table table table-striped table-genernal-receivables-debts-report">
         <thead>
            <tr>
               <th class="text-center" rowspan="2"><?php echo _l('customer_code'); ?></th>
               <th class="text-center" rowspan="2"><?php echo _l('customer_name'); ?></th>
               <th class="text-center" rowspan="2"><?php echo _l('tk_debt'); ?></th>
               <th class="text-center" colspan="2">SỐ DƯ ĐẦU KỲ</th>
               <th class="text-center" colspan="2">SỐ PHÁT SINH</th>
               <th class="text-center" colspan="2">SỐ CUỐI KỲ</th>
            </tr>
            <tr>
               <th class="text-center"><?php echo _l('debt_no'); ?></th>
               <th class="text-center"><?php echo _l('debt_co'); ?></th>
               <th class="text-center"><?php echo _l('incurred_debt_no'); ?></th>
               <th class="text-center"><?php echo _l('incurred_debt_co'); ?></th>
               <th class="text-center"><?php echo _l('surplus_debt_no'); ?></th>
               <th class="text-center"><?php echo _l('surplus_debt_co'); ?></th>
            </tr>
         </thead>
         <tbody></tbody>
         <tfoot>
            <tr>
               <td></td>
               <td></td>
               <td></td>
               <td class="text-right f1"></td>
               <td class="text-right f2"></td>
               <td class="text-right f3"></td>
               <td class="text-right f4"></td>
               <td class="text-right f5"></td>
               <td class="text-right f6"></td>
            </tr>
         </tfoot>
      </table>
   </div>
