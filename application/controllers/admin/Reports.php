<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Reports extends Admin_controller
{
    private $_instance;
    function __construct()
    {
        parent::__construct();
        if (!has_permission('reports', '', 'view_report_sales')) 
        {
            access_denied('reports');
        }
        $this->_instance =& get_instance();
        $this->load->model('reports_model');
    }
    /* No access on this url */
    public function index()
    {
        // var_dump(has_permission('reports', '', 'view_report_sales'));die();
        // if (!has_permission('reports', '', 'view')) 
        // {
        //     access_denied('reports');
        // }
        redirect(site_url('admin'));
    }
   
    /* See knowledge base article reports*/
    public function knowledge_base_articles()
    {
        $this->load->model('knowledge_base_model');
        $data['groups'] = $this->knowledge_base_model->get_kbg();
        $data['title']  = _l('kb_reports');
        $this->load->view('admin/reports/knowledge_base_articles', $data);
    }
    /*
    public function tax_summary(){
       $this->load->model('taxes_model');
       $this->load->model('payments_model');
       $this->load->model('invoices_model');
       $data['taxes'] = $this->db->query("SELECT DISTINCT taxname,taxrate FROM tblitemstax WHERE rel_type='invoice'")->result_array();
        $this->load->view('admin/reports/tax_summary',$data);
    }*/
    /* Rerport leads conversions */
    public function leads()
    {
        $type = 'leads';
        if ($this->input->get('type')) {
            $type                       = $type . '_' . $this->input->get('type');
            $data['leads_staff_report'] = json_encode($this->reports_model->leads_staff_report());
        }
        $this->load->model('leads_model');
        $data['statuses']               = $this->leads_model->get_status();
        $data['leads_this_week_report'] = json_encode($this->reports_model->leads_this_week_report());
        $data['leads_sources_report']   = json_encode($this->reports_model->leads_sources_report());
        $data['chart_js_assets']   = true;
        $this->load->view('admin/reports/' . $type, $data);
    }
    /* Sales reportts */
    public function sales()
    {
        // if (is_using_multiple_currencies()) {
        //     $this->load->model('currencies_model');
        //     $data['currencies'] = $this->currencies_model->get();

        // }
        if (!has_permission('reports', '', 'view_report_sales')) 
        {
            access_denied('reports');
        }
        $this->load->model('sales_model');
        $this->load->model('sale_oders_model');
        $this->load->model('invoices_model');
        $this->load->model('estimates_model');
        $this->load->model('proposals_model');
        $data['sale_SO_statuses']      = $this->sales_model->get_statuses();
        // var_dump($data['sale_SO_statuses']);die;
        $data['invoice_statuses']      = $this->invoices_model->get_statuses();
        $data['estimate_statuses']     = $this->estimates_model->get_statuses();
        $data['payments_years']        = $this->reports_model->get_distinct_payments_years();
        $data['estimates_sale_agents'] = $this->estimates_model->get_sale_agents();

        $data['invoices_sale_agents']  = $this->invoices_model->get_sale_agents();
        $data['staff']=get_table_where('tblstaff',array('active'=>1));


        $data['proposals_sale_agents']  = $this->proposals_model->get_sale_agents();
        $data['proposals_statuses'] = $this->proposals_model->get_statuses();
        $data['order_years'] = $this->sale_oders_model->getYears();
        $data['MONTHS']=[
                    "Tháng 1", "Tháng 2", "Tháng 3", "Tháng 4", "Tháng 5", "Tháng 6", "Tháng 7", "Tháng 8", "Tháng 9", "Tháng 10", "Tháng 11", "Tháng 12"
                ];
        $data['areas']  = $this->clients_model->get_area();
        $data['sale_areas']  = get_table_where('tblsale_areas');
        $data['objects_groups']  = get_table_where('tblobjects_groups');
        $data['PO_status_stats']   = json_encode($this->reports_model->PO_status_stats());
        $data['SO_status_stats']   = json_encode($this->reports_model->SO_status_stats());;
        $data['chart_js_assets']   = true;
        $data['title']                 = _l('sales_reports');
        $this->load->view('admin/reports/sales', $data);
    }

    /* Purchases reports */
    public function purchases()
    {
        if (!has_permission('reports', '', 'view_report_purchases')) 
        {
            access_denied('reports');
        }
        $data['title']                 = _l('purchases_reports');
        $this->load->view('admin/reports/purchases', $data);
    }

    /* Warehouse reports */
    public function warehouses()
    {
         if (!has_permission('reports', '', 'view_report_warehouses')) 
        {
            access_denied('reports');
        }
        $this->load->model('warehouse_model');
        $this->load->model('category_model');
        $data['categories'] = [];
        $this->category_model->get_by_id(0,$data['categories']);
        $data['warehouses']   = $this->warehouse_model->getWarehouses('', true);
        $data['title']                 = _l('warehouses_reports');
        $this->load->view('admin/reports/warehouses', $data);
    }

    /* Debts reports */
    public function debts()
    {
        if (!has_permission('reports', '', 'view_report_debts')) 
        {
            access_denied('reports');
        }
        $data['title']                 = _l('debts_reports');
        $this->load->view('admin/reports/debts', $data);
    }

    /* Customer report */
    public function customers_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $select = array(
                'CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company',
                '(SELECT COUNT(clientid) FROM tblinvoices WHERE tblinvoices.clientid = tblclients.userid AND status != 5)',
                '(SELECT SUM(subtotal) FROM tblinvoices WHERE tblinvoices.clientid = tblclients.userid AND status != 5)',
                '(SELECT SUM(total) FROM tblinvoices WHERE tblinvoices.clientid = tblclients.userid AND status != 5)'
            );

            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                $i = 0;
                foreach ($select as $_select) {
                    if ($i !== 0) {
                        $_temp = substr($_select, 0, -1);
                        $_temp .= ' ' . $custom_date_select . ')';
                        $select[$i] = $_temp;
                    }
                    $i++;
                }
            }

            $by_currency     = $this->input->post('report_currency');
            $currency        = $this->currencies_model->get_base_currency();
            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);
            if ($by_currency) {
                $i = 0;
                foreach ($select as $_select) {
                    if ($i !== 0) {
                        $_temp = substr($_select, 0, -1);
                        $_temp .= ' AND currency =' . $by_currency . ')';
                        $select[$i] = $_temp;
                    }
                    $i++;
                }
                $currency        = $this->currencies_model->get($by_currency);
                $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);
            }
            $aColumns     = $select;
            $sIndexColumn = "userid";
            $sTable       = 'tblclients';
            $where        = array();

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, array(), $where, array(
                'userid'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];
            $x       = 0;
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if ($i == 0) {
                        $_data = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                    } else if ($aColumns[$i] == $select[2] || $aColumns[$i] == $select[3]) {
                        if ($_data == null) {
                            $_data = 0;
                        }
                        $_data = format_money($_data, $currency_symbol);
                    }
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }
            echo json_encode($output);
            die();
        }
    }

    public function payments_received()
    {

        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $this->load->model('payment_modes_model');
            $online_modes = $this->payment_modes_model->get_online_payment_modes(true);
            $select       = array(
                'tblinvoicepaymentrecords.id',
                'tblinvoicepaymentrecords.date',
                'invoiceid',
                'CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company',
                'paymentmode',
                'transactionid',
                'note',
                'amount'
            );
            $where        = array(
                'AND status != 5'
            );

            $custom_date_select = $this->get_where_report_period('tblinvoicepaymentrecords.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblinvoicepaymentrecords';
            $join         = array(
                'JOIN tblinvoices ON tblinvoices.id = tblinvoicepaymentrecords.invoiceid',
                'LEFT JOIN tblclients ON tblclients.userid = tblinvoices.clientid',
                'LEFT JOIN tblinvoicepaymentsmodes ON tblinvoicepaymentsmodes.id = tblinvoicepaymentrecords.paymentmode'
            );

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'number',
                'clientid',
                'tblinvoicepaymentsmodes.name',
                'tblinvoicepaymentsmodes.id as paymentmodeid',
                'paymentmethod'
            ));

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $footer_data['total_amount'] = 0;
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if ($aColumns[$i] == 'paymentmode') {
                        $_data = $aRow['name'];
                        if (is_null($aRow['paymentmodeid'])) {
                            foreach ($online_modes as $online_mode) {
                                if ($aRow['paymentmode'] == $online_mode['id']) {
                                    $_data = $online_mode['name'];
                                }
                            }
                        }
                        if (!empty($aRow['paymentmethod'])) {
                            $_data .= ' - ' . $aRow['paymentmethod'];
                        }
                    } else if ($aColumns[$i] == 'tblinvoicepaymentrecords.id') {
                        $_data = '<a href="' . admin_url('payments/payment/' . $_data) . '" target="_blank">' . $_data . '</a>';
                    } else if ($aColumns[$i] == 'tblinvoicepaymentrecords.date') {
                        $_data = _d($_data);
                    } else if ($aColumns[$i] == 'invoiceid') {
                        $_data = '<a href="' . admin_url('invoices/list_invoices/' . $aRow[$aColumns[$i]]) . '" target="_blank">' . format_invoice_number($aRow['invoiceid']) . '</a>';
                    } else if ($i == 3) {
                        $_data = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                    } else if ($aColumns[$i] == 'amount') {
                        $footer_data['total_amount'] += $_data;
                        $_data = format_money($_data, $currency_symbol);
                    }

                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
            }

            $footer_data['total_amount'] = format_money($footer_data['total_amount'], $currency_symbol);
            $output['sums']              = $footer_data;
            echo json_encode($output);
            die();
        }
    }
    public function proposals_report(){
        if($this->input->is_ajax_request()){

            $this->load->model('currencies_model');
            $this->load->model('proposals_model');

            $select = array(
                'id',
                'subject',
                'proposal_to',
                'date',
                'open_till',
                'subtotal',
                'total',
                'total_tax',
                '1',
                'discount_total',
                'adjustment',
                'status'
            );

            $where              = array();
            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('proposal_status')) {
                $statuses  = $this->input->post('proposal_status');
                $_statuses = array();
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $status);
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            if ($this->input->post('proposals_sale_agents')) {
                $agents  = $this->input->post('proposals_sale_agents');
                $_agents = array();
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $agent);
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND assigned IN (' . implode(', ', $_agents) . ')');
                }
            }


            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }

            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblproposals';
            $join         = array();

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'rel_id',
                'rel_type',
                'discount_percent'
            ));

            $output  = $result['output'];
            $rResult = $result['rResult'];

            $x       = 0;
            $footer_data = array(
                'total' => 0,
                'subtotal' => 0,
                'total_tax' => 0,
                'discount_total' => 0,
                'adjustment' => 0
            );

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if ($i == 'id' || $aColumns[$i] == 'subject') {
                         $_data = '<a href="'.admin_url('proposals/list_proposals/'.$aRow['id']).'" target="_blank">' .
                         ($aColumns[$i] == 'id' ? format_proposal_number($aRow['id']) : $_data) . '</a>';
                    } else if ($aColumns[$i] == 'total' || $aColumns[$i] == 'subtotal' || $aColumns[$i] == 'total_tax' || $aColumns[$i] == 'discount_total' || $aColumns[$i] == 'adjustment') {
                        if ($_data == null) {
                            $_data = 0;
                        }
                        $footer_data[$aColumns[$i]] += $_data;
                        $_data = format_money($_data, $currency_symbol);
                    } else if ($aColumns[$i] == '1') {
                        $_data = $this->get_report_tax_breakdown_column('proposals', $aRow['id'], $_data, $currency_symbol);
                    } else if ($aColumns[$i] == 'status') {
                        $_data = format_proposal_status($aRow['status']);
                    } else if ($aColumns[$i] == 'date' || $aColumns[$i] == 'open_till') {
                        $_data = _d($_data);
                    } else if($aColumns[$i] == 'proposal_to'){
                           if(!empty($_data)){
                              if(!empty($aRow['rel_id']) && $aRow['rel_id'] != 0){
                                if($aRow['rel_type'] == 'lead'){
                                  $_data = '<a href="#" onclick="init_lead('.$aRow['rel_id'].');return false;" target="_blank" data-toggle="tooltip" data-title="'._l('lead').'">'.$_data.'</a>'. '<span class="hide">'._l('lead').'</span>';
                              } else if($aRow['rel_type'] == 'customer'){
                                  $_data = '<a href="'.admin_url('clients/client/'.$aRow['rel_id']).'" target="_blank" data-toggle="tooltip" data-title="'._l('client').'">'.$_data.'</a>' . '<span class="hide">'._l('client').'</span>';
                              }
                          }
                      }
                    }
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }
    public function estimates_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $this->load->model('estimates_model');

            $select = array(
                'id',
                'CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company',
                'invoiceid',
                'YEAR(date)',
                'date',
                'expirydate',
                'subtotal',
                'total',
                'total_tax',
                '1',
                'discount_total',
                'adjustment',
                'reference_no',
                'status'
            );

            $where              = array();
            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('estimate_status')) {
                $statuses  = $this->input->post('estimate_status');
                $_statuses = array();
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $status);
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            if ($this->input->post('sale_agent_estimates')) {
                $agents  = $this->input->post('sale_agent_estimates');
                $_agents = array();
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $agent);
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND sale_agent IN (' . implode(', ', $_agents) . ')');
                }
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {
                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }
            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblestimates';
            $join         = array(
                'JOIN tblclients ON tblclients.userid = tblestimates.clientid'
            );

            $result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'userid',
                'clientid',
                'discount_percent'
            ));

            $output  = $result['output'];
            $rResult = $result['rResult'];
            $x       = 0;

            $footer_data = array(
                'total' => 0,
                'subtotal' => 0,
                'total_tax' => 0,
                'discount_total' => 0,
                'adjustment' => 0
            );

            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if ($i == 1) {
                        $_data = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                    } else if ($aColumns[$i] == 'total' || $aColumns[$i] == 'subtotal' || $aColumns[$i] == 'total_tax' || $aColumns[$i] == 'discount_total' || $aColumns[$i] == 'adjustment') {
                        if ($_data == null) {
                            $_data = 0;
                        }
                        $footer_data[$aColumns[$i]] += $_data;
                        $_data = format_money($_data, $currency_symbol);

                    } else if ($aColumns[$i] == '1') {
                        $_data = $this->get_report_tax_breakdown_column('estimates', $aRow['id'], $_data, $currency_symbol);
                    } else if ($aColumns[$i] == 'id') {
                        $_data = '<a href="' . admin_url('estimates/list_estimates/' . $aRow['id']) . '" target="_blank">' . format_estimate_number($aRow['id']) . '</a>';
                    } else if ($aColumns[$i] == 'status') {
                        $_data = format_estimate_status($aRow['status']);
                    } else if ($aColumns[$i] == 'date' || $aColumns[$i] == 'expirydate') {
                        $_data = _d($_data);
                    } else if ($aColumns[$i] == 'invoiceid') {
                        if ($_data == NULL) {
                            $_data = '';
                        } else {
                            $_data = '<a href="' . admin_url('invoices/list_invoices/' . $_data) . '" target="_blank">' . format_invoice_number($_data) . '</a>';
                        }
                    }
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }
            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
            }
            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();

        }
    }
    private function get_where_report_period($field = 'date')
    {
        $months_report      = $this->input->post('report_months');
        $custom_date_select = '';
        if ($months_report != '') {
            if (is_numeric($months_report)) {
                $minus_months       = date('Y-m-d', strtotime("-$months_report MONTH"));
                $custom_date_select = 'AND (' . $field . ' BETWEEN "' . $minus_months . '" AND "' . date('Y-m-d') . '")';
            } else if ($months_report == 'custom') {
                $from_date = to_sql_date($this->input->post('report_from'));
                $to_date   = to_sql_date($this->input->post('report_to'));
                if ($from_date == $to_date) {
                    $custom_date_select = 'AND ' . $field . ' = "' . $from_date . '"';
                } else {
                    $custom_date_select = 'AND (' . $field . ' BETWEEN "' . $from_date . '" AND "' . $to_date . '")';
                }
            }
        }
        return $custom_date_select;
    }
    private function get_report_tax_breakdown_column($type, $id, $_data, $currency_symbol)
    {
        if ($type == 'estimates') {
            $table = 'tblestimates';
            $items = $this->estimates_model->get_estimate_items($id);
        } else if($type == 'proposals') {
            $items = $this->proposals_model->get_proposal_items($id);
            $table = 'tblproposals';
        } else {
            $items = $this->invoices_model->get_invoice_items($id);
            $table = 'tblinvoices';
        }

        $taxes             = array();
        $_calculated_taxes = array();
        $multiple_taxes    = false;
        $one_tax           = false;
        foreach ($items as $item) {
            if ($type == 'estimates') {
                 $item_taxes = get_estimate_item_taxes($item['id']);
            } else if($type == 'proposals') {
                 $item_taxes = get_proposal_item_taxes($item['id']);
            } else {
                 $item_taxes = get_invoice_item_taxes($item['id']);
            }

            if (count($item_taxes) > 0) {
                foreach ($item_taxes as $tax) {
                    $calc_tax     = 0;
                    $tax_not_calc = false;
                    if (!in_array($tax['taxname'], $_calculated_taxes)) {
                        array_push($_calculated_taxes, $tax['taxname']);
                        $tax_not_calc = true;
                    }
                    if ($tax_not_calc == true) {
                        $taxes[$tax['taxname']]          = array();
                        $taxes[$tax['taxname']]['total'] = array();
                        array_push($taxes[$tax['taxname']]['total'], (($item['qty'] * $item['rate']) / 100 * $tax['taxrate']));
                        $taxes[$tax['taxname']]['tax_name'] = $tax['taxname'];
                        $taxes[$tax['taxname']]['taxrate']  = $tax['taxrate'];
                    } else {
                        array_push($taxes[$tax['taxname']]['total'], (($item['qty'] * $item['rate']) / 100 * $tax['taxrate']));
                    }
                }
            }
        }
        $_tax = '';
        $this->db->select('discount_type,discount_percent');
        $this->db->where('id', $id);
        $necessary_data = $this->db->get($table)->row();
        $count          = count($taxes);
        if ($count >= 1) {
            if ($count == 1) {
                $one_tax = true;
            }
            foreach ($taxes as $tax) {
                $total = array_sum($tax['total']);
                if ($necessary_data->discount_percent != 0 && $necessary_data->discount_type == 'before_tax') {
                    $total_tax_calculated = ($total * $necessary_data->discount_percent) / 100;
                    $total                = ($total - $total_tax_calculated);
                }
                $_tax_name = explode('|', $tax['tax_name']);
                $_tax .= '<b>' . $_tax_name[0] . '(' . _format_number($tax['taxrate']) . '%)</b> - ' . format_money($total, $currency_symbol) . ' | ';
            }
            $_tax = mb_substr($_tax, 0, -2);
        } else if ($count == 0) {
            $_data = 0;
            $_tax  = format_money($_data, $currency_symbol);
        }

        if ($one_tax == true) {
            if (strrpos($_tax, "|") !== false)
                $_tax = mb_substr($_tax, 0, -2);
        }

        return $_tax;
    }
    public function invoices_report()
    {
        if ($this->input->is_ajax_request()) {
            $this->load->model('currencies_model');
            $this->load->model('invoices_model');

            $select = array(
                'id',
                'CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company',
                'YEAR(date)',
                'date',
                'duedate',
                'subtotal',
                'total',
                'total_tax',
                '1',
                'discount_total',
                'adjustment',
                '(SELECT SUM(amount) FROM tblinvoicepaymentrecords WHERE invoiceid = tblinvoices.id)',
                'status'
            );

            $where  = array(
                'AND status != 5'
            );

            $custom_date_select = $this->get_where_report_period();
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            if ($this->input->post('sale_agent_invoices')) {
                $agents  = $this->input->post('sale_agent_invoices');
                $_agents = array();
                if (is_array($agents)) {
                    foreach ($agents as $agent) {
                        if ($agent != '') {
                            array_push($_agents, $agent);
                        }
                    }
                }
                if (count($_agents) > 0) {
                    array_push($where, 'AND sale_agent IN (' . implode(', ', $_agents) . ')');
                }
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {

                $_temp = substr($select[11], 0, -1);
                $_temp .= ' AND currency =' . $by_currency . ')';
                $select[11] = $_temp;

                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }
            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);

            if ($this->input->post('invoice_status')) {
                $statuses  = $this->input->post('invoice_status');
                $_statuses = array();
                if (is_array($statuses)) {
                    foreach ($statuses as $status) {
                        if ($status != '') {
                            array_push($_statuses, $status);
                        }
                    }
                }
                if (count($_statuses) > 0) {
                    array_push($where, 'AND status IN (' . implode(', ', $_statuses) . ')');
                }
            }

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblinvoices';
            $join         = array(
                'JOIN tblclients ON tblclients.userid = tblinvoices.clientid'
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'userid',
                'clientid',
                'discount_percent'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];
            $x       = 0;

            $footer_data = array(
                'total' => 0,
                'subtotal' => 0,
                'total_tax' => 0,
                'discount_total' => 0,
                'adjustment' => 0,
                'amount_open' => 0
            );
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if ($i == 1) {
                        $_data = '<a href="' . admin_url('clients/client/' . $aRow['userid']) . '" target="_blank">' . $aRow['company'] . '</a>';
                    } else if ($aColumns[$i] == 'total' || $aColumns[$i] == 'subtotal' || $aColumns[$i] == 'total_tax' || $aColumns[$i] == 'discount_total' || $aColumns[$i] == 'adjustment') {
                        if ($_data == null) {
                            $_data = 0;
                        }
                        $footer_data[$aColumns[$i]] += $_data;
                        $_data = format_money($_data, $currency_symbol);

                    } else if ($aColumns[$i] == '1') {
                        $_data = $this->get_report_tax_breakdown_column('invoices', $aRow['id'], $_data, $currency_symbol);
                    } else if ($aColumns[$i] == $select[11]) {
                        $_amount_open = $aRow['total'] - $_data;
                        $footer_data['amount_open'] += $_amount_open;
                        $_data = format_money($_amount_open, $currency_symbol);
                    } else if ($aColumns[$i] == 'id') {
                        $_data = '<a href="' . admin_url('invoices/list_invoices/' . $aRow['id']) . '" target="_blank">' . format_invoice_number($aRow['id']) . '</a>';
                    } else if ($aColumns[$i] == 'status') {
                        $_data = format_invoice_status($aRow['status']);
                    } else if ($aColumns[$i] == 'date' || $aColumns[$i] == 'duedate') {
                        $_data = _d($_data);
                    }
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }
            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
            }


            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function general_order_tracking_book_report_PO_pdf(){
        $data=$this->general_order_tracking_book_report_PO(true)['aaData'];
        $colum=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA');
        $title_colum=array(
            _l('STT'),
            _l('view_date'),
            _l('code_noo'),
            _l('customer_name'),
            _l('sale_quantity'),
            _l('sale_revenue'),
            _l('total_money_deposit'),
            _l('total_amount_payment'),
            _l('total_amount_left'),
            _l('billers')
        );
        $data=$this->general_order_tracking_book_report_PO(true)['aaData'];
        
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setTitle('tiêu đề');

        $n=count($this->general_order_tracking_book_report_PO(true)['aaData'][0]);
        for($i=0;$i<$n;$i++)
        {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i])->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
        }

        $BStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            )
        );
        $objPHPExcel->getActiveSheet()->SetCellValue('A1','CÔNG TY TNHH DUDOFF VIỆT NAM');
        $objPHPExcel->getActiveSheet()->SetCellValue('A2','SỔ THEO DÕI TỔNG HỢP ĐƠN ĐẶT HÀNG (PO)')->getStyle('A2')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(100);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
        $objPHPExcel->getActiveSheet()->mergeCells('A2:N2');
        for($i=0;$i<$n;$i++) {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].'3', $title_colum[$i])->getStyle($colum[$i].'3')->applyFromArray($BStyle);
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
        }
        foreach($data as $key=>$value)
        {
            for($i=0;$i<$n;$i++)
            {
                if($i==0){
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].($key+4),($key+1));
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),strip_tags($value[$i]));
                }
                else
                {
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),strip_tags($value[$i]));
                }
            }
        }
        $objPHPExcel->getActiveSheet()->freezePane('A3');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="So_Theo_Doi_Tong_Hop_Don_Dat_Hang_Po.xls"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit();

    }
    public function general_order_tracking_book_report_PO($pdf=false)
    {
        
       if ($this->input->is_ajax_request()||$pdf==true)
        {
            $this->load->model('currencies_model');
            $this->load->model('invoices_model');
            $this->load->model('sales_model');

            $select = array(
                'tblsale_orders.date',
                'CONCAT(tblsale_orders.prefix,tblsale_orders.code) as sale_code',
                'tblclients.company',
                '(SELECT SUM(tblsale_order_items.quantity) FROM tblsale_order_items WHERE tblsale_order_items.sale_id=tblsale_orders.id ) as total_quantity',
                'tblsale_orders.total',
                '1',
                '2',
                '3',
                '4',
                'saler_id'
            );

            
            $where  = array(
                // 'AND status != 5'
            );


        
            $custom_date_select = $this->get_where_report_period('tblsale_orders.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {

                $_temp = substr($select[11], 0, -1);
                $_temp .= ' AND currency =' . $by_currency . ')';
                $select[11] = $_temp;

                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }
            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);

            if ($this->input->post('PO_status_gen')) {
                $statuses  = $this->input->post('PO_status_gen');
                if (count($statuses) > 0) {
                    if($statuses==1) array_push($where, 'AND status<>2');
                    if($statuses==2) array_push($where, 'AND status='.$statuses);
                    if($statuses==3) array_push($where, 'AND export_status='.'0');
                    if($statuses==4) array_push($where, 'AND export_status='.'1');
                    if($statuses==5) array_push($where, 'AND export_status='.'2');
                }

            }
            $filter=array();
            $staff=$this->input->post('staff_general_order_PO');
            if (count($staff) > 0) 
            {
                array_push($filter, 'AND saler_id IN  (' . implode(', ', $staff) . ')');
            }
            if (count($filter) > 0) {
                array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
            }

            if($this->input->post('address_area_PO'))
            {
                array_push($where, 'AND tblclients.address_area='.$this->input->post('address_area_PO'));
            }
            if($this->input->post('sale_area_PO'))
            {
                array_push($where, 'AND tblclients.sale_area='.$this->input->post('sale_area_PO'));
            }
            if($this->input->post('objects_group_PO'))
            {
                array_push($where, 'AND tblclients.objects_group='.$this->input->post('objects_group_PO'));
            }
            // $aColumns     = $select;
            // $sIndexColumn = "userid";
            // $sTable       = 'tblclients';
            // $join         = array(
            //     // 'LEFT JOIN tblclients ON tblclients.userid = tblsale_orders.customer_id'
            // );

            // $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(     
                
            // ));
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblsale_orders';
            $join         = array(
                'LEFT JOIN tblclients ON tblclients.userid = tblsale_orders.customer_id'
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(     
                'tblsale_orders.id as sale_id',
                'tblsale_orders.customer_id',
                '(SELECT SUM(tblsale_order_items.amount) FROM tblsale_order_items WHERE tblsale_order_items.reject_id=tblsale_orders.id ) as total_return_value',
                '(SELECT SUM(tblsale_order_items.discount) FROM tblsale_order_items WHERE tblsale_order_items.sale_id=tblsale_orders.id ) as total_discount_value',
                'tblclients.code as customer_code'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];


            $x       = 0;

            $footer_data = array(
                'SL' => 0,
                'DSB' => 0,
                'TC' => 0,
                'TTSO' => 0,
                'TT' => 0,
                'CL' => 0
            );
            foreach ($rResult as $aRow) {

                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if(strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]]) && strafter($aColumns[$i], 'as ')=='sale_code')
                    {
                        $_data = '<a target="_blank" href="' . admin_url('sale_orders/sale_detail/' . $aRow['sale_id']) . '" target="_blank">' . $aRow['sale_code'] . '</a>';
                    }
                    if($aColumns[$i]=='tblclients.company')
                    {
                        $_data='<a target="_blank" href="' . admin_url('clients/client/' . $aRow['customer_id']) . '" target="_blank">' . '['.$aRow['customer_code'].'] - '.$aRow['tblclients.company'] . '</a>';
                    }
                    if($aColumns[$i]=='tblsale_orders.date')
                    {
                        $_data=_d($aRow[$aColumns[$i]]);
                    }
                    if(strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]]) && strafter($aColumns[$i], 'as ')=='total_quantity')
                    {
                        $footer_data['SL']+=$aRow['total_quantity'];
                        $_data = "<p class='text-center'>"._format_number($aRow['total_quantity']).'<p>';
                    }
                    if($aColumns[$i]=='tblsale_orders.total')
                    {
                        $footer_data['DSB']+=$aRow[$aColumns[$i]];
                        $_data = "<p class='text-right'>".format_money($aRow[$aColumns[$i]]).'<p>';
                    }
                    if ($aColumns[$i] == '1') {
                        $_data="<p class='text-right'>".format_money(getDepositPayment($aRow['sale_id']))."<p>";
                        $footer_data['TC']+=getDepositPayment($aRow['sale_id']);
                    }
                    if ($aColumns[$i] == '2') {
                        $paymentSO=getTotalMoneyReceiveFromCustomerPO($aRow['sale_id'])-getDepositPayment($aRow['sale_id']);
                        $_data="<p class='text-right'>".format_money($paymentSO)."<p>";
                        $footer_data['TTSO']+=$paymentSO;
                    }
                    if ($aColumns[$i] == '3') {
                        $_data="<p class='text-right'>".format_money(getTotalMoneyReceiveFromCustomerPO($aRow['sale_id']))."<p>";
                        $footer_data['TT']+=getTotalMoneyReceiveFromCustomerPO($aRow['sale_id']);
                    }
                    if($aColumns[$i] == '4')
                    {
                        $_data="<p class='text-right'>".format_money($aRow['tblsale_orders.total']-getTotalMoneyReceiveFromCustomerPO($aRow['sale_id']))."<p>";
                        $footer_data['CL']+=$aRow['tblsale_orders.total']-getTotalMoneyReceiveFromCustomerPO($aRow['sale_id']);
                    }
                    if($aColumns[$i]=='saler_id')
                    {
                        $_data=get_staff_full_name($aRow['saler_id']);
                    }

                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
                $x++;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
                if($key=='SL')
                    $footer_data[$key] = _format_number($total);

            }

            $output['sums'] = $footer_data;
            if($pdf==false)
            {
                echo json_encode($output);
            }
            else
            {
                return $output;
            }
            die();
        }
    }


    public function general_order_tracking_book_report_pdf(){
        $colum=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA');
        $title_colum=array(
            _l('STT'),
            _l('view_date'),
            _l('code_noo'),
            _l('customer_name'),
            _l('sale_quantity'),
            _l('sale_revenue'),
            _l('net_revenue'),
            _l('delivered_quantity'),
            _l('rest_quantity'),
            _l('paid_payment'),
            _l('rest_payment')
            // _l('billers')
        );
        $data=$this->general_order_tracking_book_report(true)['aaData'];
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setTitle('tiêu đề');

        $n=count($this->general_order_tracking_book_report(true)['aaData'][0]);
        for($i=0;$i<$n;$i++)
        {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i])->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
        }

        $BStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            )
        );
        $objPHPExcel->getActiveSheet()->SetCellValue('A1','CÔNG TY TNHH DUDOFF VIỆT NAM');
        $objPHPExcel->getActiveSheet()->SetCellValue('A2','SỔ THEO DÕI TỔNG HỢP ĐƠN HÀNG BÁN (SO)')->getStyle('A2')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(100);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
        $objPHPExcel->getActiveSheet()->mergeCells('A2:N2');
        for($i=0;$i<$n;$i++) {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].'3', $title_colum[$i])->getStyle($colum[$i].'3')->applyFromArray($BStyle);
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
        }
        foreach($data as $key=>$value)
        {
            for($i=0;$i<$n;$i++)
            {
                if($i==0){
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].($key+4),($key+1));
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),strip_tags($value[$i]));
                }
                else
                {
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),strip_tags($value[$i]));
                }
            }
        }
        $__data=$this->general_order_tracking_book_report(true)['sums'];
        $sum_value=array('Tổng Cộng','','',$__data['SL'],$__data['DSB'],$__data['DTT'],$__data['DG'],$__data['CG'],$__data['DT'],$__data['CT']);
        for($i=0;$i<$n;$i++)
        {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].(count($data)+4),strip_tags($sum_value[$i]));
            }
            else
            {

                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].(count($data)+4),strip_tags($sum_value[$i]));
            }
        }
        $objPHPExcel->getActiveSheet()->freezePane('A3');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="So_Theo_Doi_Tong_Hop_Don_Hang_So.xls"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit();

    }
    public function general_order_tracking_book_report($pdf=false)
    {
        if ($this->input->is_ajax_request()||$pdf==true)
        {
            $this->load->model('currencies_model');
            $this->load->model('invoices_model');
            $this->load->model('sales_model');

            $select = array(
                'tblsales.date',
                'CONCAT(tblsales.prefix,tblsales.code) as sale_code',
                'rel_id',
                'tblclients.company',
                '(SELECT SUM(tblsale_items.quantity) FROM tblsale_items WHERE tblsale_items.sale_id=tblsales.id ) as total_quantity',
                'tblsales.total',
                '1',
                '4',
                '5',
                'saler_id'
            );

            
            $where  = array(
            );

            $custom_date_select = $this->get_where_report_period('tblsales.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {

                $_temp = substr($select[11], 0, -1);
                $_temp .= ' AND currency =' . $by_currency . ')';
                $select[11] = $_temp;

                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }
            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);

            if ($this->input->post('SO_status_gen')) {
                $statuses  = $this->input->post('SO_status_gen');
                if (count($statuses) > 0) {
                    if($statuses==1) array_push($where, 'AND status<>2');
                    if($statuses==2) array_push($where, 'AND status='.$statuses);
                    if($statuses==3) array_push($where, 'AND export_status='.'0');
                    if($statuses==4) array_push($where, 'AND export_status='.'1');
                    if($statuses==5) array_push($where, 'AND export_status='.'2');
                }

            }
            $filter=array();
            $staff=$this->input->post('staff_general_order_SO_gen');
            if (count($staff) > 0) 
            {
                array_push($filter, 'AND saler_id IN  (' . implode(', ', $staff) . ')');
            }
            if (count($filter) > 0) {
                array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
            }
            if($this->input->post('sale_area_SO_gen'))
            {
                array_push($where, 'AND tblclients.sale_area='.$this->input->post('sale_area_SO_gen'));
            }
            if($this->input->post('objects_group_SO_gen'))
            {
                array_push($where, 'AND tblclients.objects_group='.$this->input->post('objects_group_SO_gen'));
            }


            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblsales';
            $join         = array(
                'LEFT JOIN tblclients ON tblclients.userid = tblsales.customer_id'
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(     
                'tblsales.id as sale_id',
                'tblsales.customer_id',
                '(SELECT SUM(tblsale_items.amount) FROM tblsale_items WHERE tblsale_items.reject_id=tblsales.id ) as total_return_value',
                '(SELECT SUM(tblsale_items.discount) FROM tblsale_items WHERE tblsale_items.sale_id=tblsales.id ) as total_discount_value',
                '(SELECT SUM(tblsale_items.tax) FROM tblsale_items WHERE tblsale_items.sale_id=tblsales.id ) as total_tax_value',
                'tblsales.discount',
                'rel_code',
                'tblclients.code as customer_code'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];

            $x       = 0;

            // $footer_data = array(
            //     'total' => 0,
            //     'subtotal' => 0,
            //     'total_tax' => 0,
            //     'discount_total' => 0,
            //     'adjustment' => 0,
            //     'amount_open' => 0
            // );

            $footer_data = array(
                'SL' => 0,
                'DSB' => 0,
                'DTT' => 0,
                'DG' => 0,
                'CG' => 0,
                'DT' => 0,
                'CT' => 0,
            );
            
            foreach ($rResult as $aRow) {
                $total=0;      
                $item=getDeliverdQuantity($aRow['sale_id']);
                $payment=getTotalMoneyReceiveFromCustomer($aRow['sale_id'],'SO');
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if(strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]]) && strafter($aColumns[$i], 'as ')=='sale_code')
                    {
                        $_data = '<a href="' . admin_url('sales/sale_detail/' . $aRow['sale_id']) . '" target="_blank">' . $aRow['sale_code'] . '</a>';
                    }
                    if($aColumns[$i]=='tblclients.company')
                    {
                        $_data='<a target="_blank" href="' . admin_url('clients/client/' . $aRow['customer_id']) . '" target="_blank">' . '['.$aRow['customer_code'].'] - '.$aRow['tblclients.company'] . '</a>';
                    }
                    if($aColumns[$i]=='rel_id')
                    {
                        $_data = '<a href="' . admin_url('sale_orders/sale_detail/' . $aRow['rel_id']) . '" target="_blank">' . $aRow['rel_code'] . '</a>';
                    }
                    if($aColumns[$i]=='tblsales.date')
                    {
                        $_data=_d($aRow[$aColumns[$i]]);
                    }
                    if(strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]]) && strafter($aColumns[$i], 'as ')=='total_quantity')
                    {
                        $footer_data['SL']+=$aRow['total_quantity'];
                        $_data ="<p class='text-center'>"._format_number($aRow['total_quantity'])."</p>";
                    }
                    if($aColumns[$i]=='tblsales.total')
                    {
                        $total=$aRow[$aColumns[$i]];
                        $footer_data['DSB']+=$total;
                        $_data = "<p class='text-right'>".format_money($total)."</p>";
                    }

                    if($aColumns[$i]=='1')
                    {   $DTT=$total-$aRow['total_tax_value']-$aRow['total_return_value'];
                        $footer_data['DTT']+=$DTT;
                        $_data = "<p class='text-right'>".format_money($DTT)."</p>";
                    }
                    
                    if($aColumns[$i]=='2')
                    {   
                        $delivered_quantity=$item->delivery_quantity;
                        $footer_data['DG']+=$delivered_quantity;
                        $_data = _format_number($item->delivery_quantity);
                        if($_data== false) $_data=0;

                    }
                    if($aColumns[$i]=='3')
                    {   
                        $rest_quantity=$item->quantity-$item->delivery_quantity;
                        $footer_data['CG']+=$rest_quantity;
                        $_data = _format_number($rest_quantity);
                        if($_data==false) $_data=0;
                    }
                    if($aColumns[$i]=='4')
                    {   
                        $paid_payment=$payment;
                        $footer_data['DT']+=$paid_payment;
                        if(empty($_data)) "<p class='text-right'>0</p>";
                        $_data = "<p class='text-right'>".format_money($paid_payment)."</p>";
                        
                    }
                    if($aColumns[$i]=='5')
                    {   
                        $rest_payment=$aRow['tblsales.total']-$payment;
                        $footer_data['CT']+=$rest_payment;
                        if(empty($_data)) "<p class='text-right'>0</p>";
                        $_data = "<p class='text-right'>".format_money($rest_payment)."</p>";
                        
                    }
                    if($aColumns[$i]=='saler_id')
                    {
                        $_data=get_staff_full_name($aRow['saler_id']);
                    }

                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
                $x++;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
                if($key=='SL' || $key=='DG' || $key=='CG')
                    $footer_data[$key] = _format_number($total);

            }
            $output['sums'] = $footer_data;
            if($pdf==false)
            {
                echo json_encode($output);
            }
            else
            {
                return $output;
            }
            die();
        }
    }
    public function kiemtra($data=array(),$_key="",$count=0)
    {
        $key_moi=$_key;
        foreach ($data as $key => $value) {
            if($_key==$key)
            {
                $key_moi= $this->kiemtra($data,($_key+$count),($count+1));
            }
        }
        return $key_moi;
        
    }


    public function cash_funds_detailing_accounting_books_pdf(){
        $colum=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA');
        $title_colum=array(
            _l('STT'),
            _l('view_date'),
            _l('account_date'),
            _l('code_vouchers_receipts'),
            _l('code_vouchers_votes'),
            _l('orders_explan'),
            _l('reciprocal_tk'),
            _l('incurred_tk_no'),
            _l('incurred_tk_co'),
            _l('rest_tk'),
            _l('receiver_submitter')
        );
        $data=$this->cash_funds_detailing_accounting_books(true)['aaData'];
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setTitle('tiêu đề');

        $n=count($this->cash_funds_detailing_accounting_books(true)['aaData'][0]);
        for($i=0;$i<$n;$i++)
        {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i])->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
        }

        $BStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            )
        );
        $objPHPExcel->getActiveSheet()->SetCellValue('A1','CÔNG TY TNHH DUDOFF VIỆT NAM');
        $objPHPExcel->getActiveSheet()->SetCellValue('A2','SỐ KẾ TOÁN CHI TIẾT QUỸ TIỀN MẶT')->getStyle('A2')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(100);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
        $objPHPExcel->getActiveSheet()->mergeCells('A2:N2');
        for($i=0;$i<$n;$i++) {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].'3', $title_colum[$i])->getStyle($colum[$i].'3')->applyFromArray($BStyle);
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
        }
        foreach($data as $key=>$value)
        {
            for($i=0;$i<$n;$i++)
            {
                if($i==0){
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].($key+4),($key+1));
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),strip_tags($value[$i]));
                }
                else
                {
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),strip_tags($value[$i]));
                }
            }
        }
        $objPHPExcel->getActiveSheet()->freezePane('A3');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="So_Ke_Toan_Chi_Tiet_Quy_Tien_Mat.xls"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit();

    }
    public function cash_funds_detailing_accounting_books($pdf=false)
    {
        if ($this->input->is_ajax_request()||$pdf==true)
        {
            $this->load->model('currencies_model');
            $this->load->model('invoices_model');
            $this->load->model('sales_model');
            //Thu
            $select = array(
                'tblreceipts.date_of_accounting as date_of_accounting',
                'tblreceipts.day_vouchers as day_vouchers',
                'code_vouchers as code_vouchers_receipts',
                '1 as code_vouchers_votes',
                'tblreceipts.id_client as obj',
                '(SELECT GROUP_CONCAT(tblreceipts_contract.sales) FROM tblreceipts_contract WHERE tblreceipts_contract.id_receipts=tblreceipts.id) as referencies',
                'tblreceipts_contract.note as note',
                '4',
                '5',
                '6',
                '7',
                'receiver as receiver'
            );

            
            $where  = array(
                // 'AND status != 5'
            );

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblreceipts';
            $join         = array(
                'LEFT JOIN tblreceipts_contract ON tblreceipts_contract.id_receipts = tblreceipts.id',
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'tblreceipts_contract.tk_no',
                'tblreceipts_contract.tk_co',
                'tblreceipts_contract.subtotal as total',
                'tblreceipts.id as id',
                '(select CONCAT("[",tblclients.code,"] - ",tblclients.company) from tblclients where tblclients.userid = tblreceipts.id_client) as company',
                '1 as type'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];
            
            //Chi
            $selectVotes = array(
                'tblvotes.date_of_accounting as date_of_accounting',
                'tblvotes.day_vouchers as day_vouchers',
                '1 as code_vouchers_receipts',
                'code_vouchers as code_vouchers_votes',
                'tblvotes.id_supplier as obj',
                '(SELECT GROUP_CONCAT(tblvotes_contract.contract) FROM tblvotes_contract WHERE tblvotes_contract.id_votes=tblvotes.id) as referencies',
                'tblvotes_contract.note as note',
                '4',
                '5',
                '6',
                '7',
                'receiver as receiver'
            );

            
            $whereVotes  = array(
                // 'AND status != 5'
            );

            $aColumnsVotes     = $selectVotes;
            $sIndexColumnVotes = "id";
            $sTableVotes       = 'tblvotes';
            $joinVotes         = array(
                'LEFT JOIN tblvotes_contract ON tblvotes_contract.id_votes = tblvotes.id'
            );

            $resultVotes  = data_tables_init($aColumnsVotes, $sIndexColumnVotes, $sTableVotes, $joinVotes, $whereVotes, array(
                'tblvotes_contract.tk_no',
                'tblvotes_contract.tk_co',
                'tblvotes_contract.total',
                'tblvotes.id as id',
                '(select CONCAT("[",tblsuppliers.supplier_code,"] - ",tblsuppliers.company) from tblsuppliers where tblsuppliers.userid = tblvotes.id_supplier) as company',
                '2 as type'
            ));
            $outputVotes  = $resultVotes['output'];
            $rResultVotes = $resultVotes['rResult'];
            $output['iTotalRecords']=$output['iTotalRecords']+$outputVotes['iTotalRecords'];
            $output['iTotalDisplayRecords']=$output['iTotalDisplayRecords']+$outputVotes['iTotalDisplayRecords'];
            $x       = 0;

            $footer_data = array(
                'SPSN' => 0,
                'SPSC' => 0,
                'ST' => 0
            );
            $mang_moi=array();
            $mang_array=array_merge($rResult,$rResultVotes);
            $mang_array=$this->sortArr($mang_array);            
            
            $sodu=get_table_where('tblaccounts',array('accountCode'=>'111'),'','row')->amount;
            $aColumns=array(
                'date_of_accounting',
                'day_vouchers',
                'code_vouchers_receipts',
                'code_vouchers_votes',
                'customers_suppliers',
                'referencies',
                'note',
                '4',
                '5',
                '6',
                '7',
                'receiver'
            );
            $soducongdon=$sodu;
            foreach ($mang_array as $aRow) {

                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if($aColumns[$i]=='customers_suppliers')
                    {
                        if($aRow['type']==1)
                            $_data = '<a target="_blank" href="'.admin_url('clients/client/').$aRow['obj'].'">'.$aRow['company'].'</a>';
                        else
                            $_data = '<a target="_blank" href="'.admin_url('suppliers/supplier/').$aRow['obj'].'">'.$aRow['company'].'</a>';
                    }
                    if($aColumns[$i] == "referencies"){
                        $references=explode(',',$aRow[$aColumns[$i]]);
                        $_data = '';
                        foreach ($references as $key => $refer) {
                            $refer=explode('-', $refer);
                            if(strlen($_data)>0) $_data.='</br>';
                            if($refer[1]=='PO')
                            {
                                $type='sale_orders';
                                $_data.='<a target="_blank" href='.admin_url($type."/sale_detail/").$refer[0].' >'.getCodePSO($refer[0],'PO').'</a>';
                            }
                            else
                            {
                                $type='sales';
                                $_data.='<a target="_blank" href='.admin_url($type."/sale_detail/").$refer[0].' >'.getCodePSO($refer[0],'SO').'</a>';
                            }
                            
                        }
                    }
                    if($aColumns[$i]=='date_of_accounting' || $aColumns[$i]=='day_vouchers' )
                    {
                        $_data=_d($aRow[$aColumns[$i]]);
                    }
                    if($aColumns[$i]=='note')
                    {
                        $_data=strip_tags($aRow[$aColumns[$i]]);
                    }
                    if(($aColumns[$i]=='code_vouchers_receipts' || $aColumns[$i]=='code_vouchers_votes' ) && $aRow[$aColumns[$i]]==1)
                    {
                        $_data=NULL;
                    }
                    else if(($aColumns[$i]=='code_vouchers_receipts' || $aColumns[$i]=='code_vouchers_votes' ) && $aRow[$aColumns[$i]]!=1)
                    {
                        // get_option('prefix_vouchers_receipts')
                        // get_option('prefix_vouchers_votes')
                        if(substr($aRow[$aColumns[$i]], 0 ,2)=='PT')
                        {
                            $_data='<a href="' . admin_url('receipts/receipts/' . $aRow['id']) . '" target="_blank">' . $aRow['code_vouchers_receipts'] . '</a>';
                        }
                        else
                        {
                            $_data='<a href="' . admin_url('votes/votes/' . $aRow['id']) . '" target="_blank">' . $aRow['code_vouchers_votes'] . '</a>';
                        }
                    }

                    if( $aColumns[$i]=='4')
                    {
                        if(substr(get_code_tk($aRow['tk_no']),0,3)=='111')
                        {
                            $_data= get_code_tk($aRow['tk_co']);
                        }else
                        {
                             $_data= get_code_tk($aRow['tk_no']);
                        }
                    }

                    if( $aColumns[$i]=='5')
                    {
                        if(substr(get_code_tk($aRow['tk_no']),0,3)=='111')
                        {
                            $_data= $aRow['total'];
                        }else{
                            $_data='0';
                        }
                        $footer_data['SPSN']+=$_data;
                    }
                    if( $aColumns[$i]=='6'){
                    
                       
                        if(substr(get_code_tk($aRow['tk_co']),0,3)=='111')
                        {
                            $_data= $aRow['total'];
                        }else{
                            $_data='0';
                        }
                        $footer_data['SPSC']+=$_data;
                    }   

                    $row[] = $_data;
                }

                $soducongdon+=$row['8']-$row['9'];
                $row['8']=_format_number($row['8']);
                $row['9']=_format_number($row['9']);
                $row['10']=_format_number($soducongdon);
                $footer_data['ST']=$soducongdon;
               
                $output['aaData'][] = $row;
                $x++;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);

            }

            $output['sums'] = $footer_data;
            echo json_encode($output);
            die();
        }
    }

    public function bank_deposit_books()
    {
        if ($this->input->is_ajax_request()) 
        {
            $this->load->model('currencies_model');
            $this->load->model('invoices_model');
            $this->load->model('sales_model');
            // $by_currency = $this->input->post('report_currency');
            // if ($by_currency) {

            //     $_temp = substr($select[11], 0, -1);
            //     $_temp .= ' AND currency =' . $by_currency . ')';
            //     $select[11] = $_temp;

            //     $currency = $this->currencies_model->get($by_currency);
            //     array_push($where, 'AND currency=' . $by_currency);
            // } else {
            //     $currency = $this->currencies_model->get_base_currency();
            // }
            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);
            //Thu
            $select = array(
                'tblreport_have.date_of_accounting as date_of_accounting',
                'tblreport_have.day_vouchers as day_vouchers',
                'code_vouchers as code_vouchers',
                '11',
                '(SELECT GROUP_CONCAT(tblreport_have_contract.contract) FROM tblreport_have_contract WHERE tblreport_have_contract.id_report_have=tblreport_have.id) as referencies',
                'tblreport_have_contract.note as note',
                '4',
                '5',
                '6',
                '7'
            );

            
            $where  = array(
                // 'AND status != 5'
            );

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblreport_have';
            $join         = array(
                'LEFT JOIN tblreport_have_contract ON tblreport_have_contract.id_report_have = tblreport_have.id',
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'tblreport_have_contract.tk_no',
                'tblreport_have_contract.tk_co',
                'tblreport_have_contract.subtotal as total',
                'tblreport_have.id as id',
                '1 as type'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];


            //Chi
            $selectVotes = array(
                'tbldebit.date_of_accounting as date_of_accounting',
                'tbldebit.day_vouchers as day_vouchers',
                'code_vouchers as code_vouchers',
                '11',
                '(SELECT GROUP_CONCAT(tbldebit_contract.contract) FROM tbldebit_contract WHERE tbldebit_contract.id_debit=tbldebit.id) as referencies',
                'tbldebit_contract.note as note',
                '4',
                '5',
                '6',
                '7'
            );

            
            $whereVotes  = array(
                // 'AND status != 5'
            );

            $aColumnsVotes     = $selectVotes;
            $sIndexColumnVotes = "id";
            $sTableVotes       = 'tbldebit';
            $joinVotes         = array(
                'LEFT JOIN tbldebit_contract ON tbldebit_contract.id_debit = tbldebit.id'
            );

            $resultVotes  = data_tables_init($aColumnsVotes, $sIndexColumnVotes, $sTableVotes, $joinVotes, $whereVotes, array(
                'tbldebit_contract.tk_no',
                'tbldebit_contract.tk_co',
                'tbldebit_contract.total',
                'tbldebit.id as id',
                '2 as type'
            ));
            $outputVotes  = $resultVotes['output'];
            $rResultVotes = $resultVotes['rResult'];

            $output['iTotalRecords']=$output['iTotalRecords']+$outputVotes['iTotalRecords'];
            $output['iTotalDisplayRecords']=$output['iTotalDisplayRecords']+$outputVotes['iTotalDisplayRecords'];

            $x       = 0;

            $footer_data = array(
                'SPSN' => 0,
                'SPSC' => 0,
                'ST' => 0
            );
            $mang_moi=array();
            $mang_array=array_merge($rResult,$rResultVotes);
            $mang_array=$this->sortArr($mang_array);            
            // var_dump($mang_array);die();
            $sodu=get_table_where('tblaccounts',array('accountCode'=>'112'),'','row')->amount;
            $aColumns=array(
                'date_of_accounting',
                'day_vouchers',
                'code_vouchers',
                '11',
                'referencies',
                'note',
                '4',
                '5',
                '6',
                '7'
            );
            $soducongdon=$sodu;
            foreach ($mang_array as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if($aColumns[$i]=='date_of_accounting' || $aColumns[$i]=='day_vouchers' )
                    {
                        $_data=_d($aRow[$aColumns[$i]]);
                    }
                    if($aColumns[$i]=='note')
                    {
                        $_data=strip_tags($aRow[$aColumns[$i]]);
                    }
                    if($aColumns[$i] == "referencies"){
                        $references=explode(',',$aRow['referencies']);
                        $_data = '';
                        foreach ($references as $key => $refer) {
                            $refer=explode('-', $refer);
                            if(strlen($_data)>0) $_data.='</br>';
                            if($refer[1]=='PO')
                            {
                                $type='sale_orders';
                                $_data.='<a target="_blank" href='.admin_url($type."/sale_detail/").$refer[0].' >'.getCodePSO($refer[0],'PO').'</a>';
                            }
                            else
                            {
                                $type='sales';
                                $_data.='<a target="_blank" href='.admin_url($type."/sale_detail/").$refer[0].' >'.getCodePSO($refer[0],'SO').'</a>';
                            }
                        }
                    }
                    if($aColumns[$i]=='11')
                    {
                        $references=explode(',',$aRow['referencies']);
                        $_data = '';
                        foreach ($references as $key => $refer) {
                            $refer=explode('-', $refer);
                            if(strlen($_data)>0) $_data.='</br>';
                            if($refer[1]=='PO')
                            {
                                $sale=getCodePSO($refer[0],'PO',true);
                                
                            }
                            elseif($refer[1]=='SO')
                            {
                                $sale=getCodePSO($refer[0],'SO',true);
                            }
                            $client=getClient($sale->customer_id);
                            $_data.='<a target="_blank" href='.admin_url("clients/client/").$sale->customer_id.' >['.$client->code.'] - '.$client->company.'</a>';
                        }
                    }
                    if($aColumns[$i]=='code_vouchers')
                    {
                        // get_option('prefix_vouchers_receipts')
                        // get_option('prefix_vouchers_votes')
                        if(substr($aRow[$aColumns[$i]], 0 ,2)=='PT')
                        {
                            $_data='<a href="' . admin_url('report_have/report_have/' . $aRow['id']) . '" target="_blank">' . $aRow['code_vouchers'] . '</a>';
                        }
                        else
                        {
                            $_data='<a href="' . admin_url('debit/debit/' . $aRow['id']) . '" target="_blank">' . $aRow['code_vouchers'] . '</a>';
                        }
                    }

                    if( $aColumns[$i]=='4')
                    {
                        if(substr(get_code_tk($aRow['tk_no']),0,3)=='112')
                        {
                            $_data= get_code_tk($aRow['tk_co']);
                        }else
                        {
                             $_data= get_code_tk($aRow['tk_no']);
                        }
                    }

                     if( $aColumns[$i]=='5')
                    {
                        if(substr(get_code_tk($aRow['tk_no']),0,3)=='112')
                        {
                            $_data= $aRow['total'];
                        }else{
                            $_data='0';
                        }
                        $footer_data['SPSN']+=$_data;
                    }
                    if( $aColumns[$i]=='6'){
                    
                       
                        if(substr(get_code_tk($aRow['tk_co']),0,3)=='112')
                        {
                            $_data= $aRow['total'];
                        }else{
                            $_data='0';
                        }
                        $footer_data['SPSC']+=$_data;
                    }   

                    $row[] = $_data;
                }

                // var_dump($row);die;

                $soducongdon+=$row['7']-$row['8'];
                $row['7']=_format_number($row['7']);
                $row['8']=_format_number($row['8']);
                $row['9']=_format_number($soducongdon);
                $footer_data['ST']=$soducongdon;
               
                $output['aaData'][] = $row;
                $x++;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);

            }

            $output['sums'] = $footer_data;
            if($pdf==false)
            {
                echo json_encode($output);
            }
            else
            {
                return $output;
            }
            die();
        }
    }

    public function sortArr($arr=array(),$field='date_of_accounting')
    {
        if(is_array($arr))
        {
            for ($i=0; $i <(count($arr)-1) ; $i++) { 
                for ($j=$i+1; $j <=(count($arr)-1) ; $j++) { 
                    if(strtotime($arr[$i][$field]) > strtotime($arr[$j][$field]))
                    {
                        $temp=$arr[$i];
                        $arr[$i]=$arr[$j];
                        $arr[$j]=$temp;
                    }
                }
            }
            return $arr;
        }
        return false;
    }
     public function sortArrG($arr=array(),$field='product_id')
    {
        if(is_array($arr))
        {
            for ($i=0; $i <(count($arr)-1) ; $i++) { 
                for ($j=$i+1; $j <=(count($arr)-1) ; $j++) { 
                    if($arr[$i][$field] > $arr[$j][$field])
                    {
                        $temp=$arr[$i];
                        $arr[$i]=$arr[$j];
                        $arr[$j]=$temp;
                    }
                }
            }
            return $arr;
        }
        return false;
    }
    public function order_tracking_book_report_PO_pdf(){
        $colum=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA');
        $title_colum=array(
            _l('STT'),
            _l('view_date'),
            _l('account_date'),
            _l('code_noo'),
            _l('orders_explan'),
            _l('product_code'),
            _l('product_name'),
            _l('unit_name'),
            _l('quantity'),
            _l('unit_cost'),
            _l('sale_revenue'),
            _l('billers')
        );
        $data=$this->order_tracking_book_report_PO(true)['aaData'];
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setTitle('tiêu đề');

        $n=count($this->order_tracking_book_report_PO(true)['aaData'][0]);
        for($i=0;$i<$n;$i++)
        {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i])->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
        }

        $BStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            )
        );
        $objPHPExcel->getActiveSheet()->SetCellValue('A1','CÔNG TY TNHH DUDOFF VIỆT NAM');
        $objPHPExcel->getActiveSheet()->SetCellValue('A2','SỔ THEO DÕI CHI TIẾT ĐẶT HÀNG (PO)')->getStyle('A2')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(100);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
        $objPHPExcel->getActiveSheet()->mergeCells('A2:N2');
        for($i=0;$i<$n;$i++) {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].'3', $title_colum[$i])->getStyle($colum[$i].'3')->applyFromArray($BStyle);
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
        }
        foreach($data as $key=>$value)
        {
            for($i=0;$i<$n;$i++)
            {
                if($i==0){
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].($key+4),($key+1));
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),strip_tags($value[$i]));
                }
                else
                {
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),strip_tags($value[$i]));
                }
            }
        }

        $__data=$this->order_tracking_book_report_PO(true)['sums'];
        $sum_value=array('Tổng Cộng','','','','','','',$__data['SL'],'',$__data['DTB']);
        for($i=0;$i<$n;$i++)
        {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].(count($data)+4),strip_tags($sum_value[$i]));
            }
            else
            {

                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].(count($data)+4),strip_tags($sum_value[$i]));
            }
        }
        $objPHPExcel->getActiveSheet()->freezePane('A3');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="So_Theo_Doi_Chi_Tiet_Dat_Hang_Po.xls"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit();

    }
    public function order_tracking_book_report_PO($pdf=false)
    {
        if ($this->input->is_ajax_request()||$pdf==true)
        {
            $this->load->model('currencies_model');
            $this->load->model('invoices_model');
            $this->load->model('sales_model');

            $select = array(
                'tblsale_orders.date',
                'tblsale_orders.date_ht',                
                'CONCAT(tblsale_orders.prefix,tblsale_orders.code) as sale_code',
                'tblsale_orders.reason',
                'tblitems.code',
                'tblitems.name',
                'tblunits.unit',
                'tblsale_order_items.quantity',
                'tblsale_order_items.unit_cost',
                'tblsale_order_items.discount_percent',
                'tblsale_orders.discount_percent',
                'tblsale_order_items.amount',
                'saler_id'
            );

            
            $where  = array(
                // 'AND status != 5'
            );

            $custom_date_select = $this->get_where_report_period('tblsale_orders.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {

                $_temp = substr($select[11], 0, -1);
                $_temp .= ' AND currency =' . $by_currency . ')';
                $select[11] = $_temp;

                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }
            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);

            if ($this->input->post('PO_status')) {
                $statuses  = $this->input->post('PO_status');
                if (count($statuses) > 0) {
                    if($statuses==1) array_push($where, 'AND status<>2');
                    if($statuses==2) array_push($where, 'AND status='.$statuses);
                    if($statuses==3) array_push($where, 'AND export_status='.'0');
                    if($statuses==4) array_push($where, 'AND export_status='.'1');
                    if($statuses==5) array_push($where, 'AND export_status='.'2');
                }

            }

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblsale_orders';
            $join         = array(
                'LEFT JOIN tblclients ON tblclients.userid = tblsale_orders.customer_id',
                'LEFT JOIN tblsale_order_items ON tblsale_order_items.sale_id = tblsale_orders.id',
                'LEFT JOIN tblitems ON tblitems.id = tblsale_order_items.product_id',
                'LEFT JOIN tblunits ON tblunits.unitid = tblsale_order_items.unit_id'
            );
            $filter=array();
            $staff=$this->input->post('staff_order_PO');
            if (count($staff) > 0) 
            {
                array_push($filter, 'AND saler_id IN  (' . implode(', ', $staff) . ')');
            }
            if (count($filter) > 0) {
                array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
            }
            if($this->input->post('sale_area_PO_detail'))
            {
                array_push($where, 'AND tblclients.sale_area='.$this->input->post('sale_area_PO_detail'));
            }
            if($this->input->post('objects_group_PO_detail'))
            {
                array_push($where, 'AND tblclients.objects_group='.$this->input->post('objects_group_PO_detail'));
            }

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                // 'tblinvoices.prefix',
                // 'tblsale_orders.customer_id',                
                'tblsale_orders.id as sale_id',
                // 'tblinvoices.id as invoice_id'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];

            $x       = 0;

            // $footer_data = array(
            //     'total' => 0,
            //     'subtotal' => 0,
            //     'total_tax' => 0,
            //     'discount_total' => 0,
            //     'adjustment' => 0,
            //     'amount_open' => 0
            // );

            $footer_data = array(
                'SL' => 0,
                'DTB' => 0
            );
            
            foreach ($rResult as $aRow) {

                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if(strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]]) && strafter($aColumns[$i], 'as ')=='sale_code')
                    {
                        $_data = '<a href="' . admin_url('sale_orders/sale_detail/' . $aRow['sale_id']) . '" target="_blank">' . $aRow['sale_code'] . '</a>';
                    }
                    if($aColumns[$i]=='tblsale_orders.date_ht' || $aColumns[$i]=='tblsale_orders.date')
                    {
                        $_data=_d($aRow[$aColumns[$i]]);
                    }
                    if($aColumns[$i]=='tblsale_order_items.quantity')
                    {
                        $footer_data['SL']+=$aRow[$aColumns[$i]];
                        $_data = "<p class='text-center'>"._format_number($aRow['tblsale_order_items.quantity'])."</p>";
                    }
                    if($aColumns[$i]=='saler_id')
                    {
                        $_data=get_staff_full_name($aRow['saler_id']);
                    }
                    if($aColumns[$i]=='tblsale_order_items.unit_cost')
                    {
                        $_data = "<p class='text-right'>".format_money($aRow[$aColumns[$i]])."</p>";
                    }
                    if($aColumns[$i]=='tblsale_order_items.amount')
                    {
                        $subamount=$aRow['tblsale_order_items.amount'];
                        
                        if(!empty($aRow['tblsale_orders.discount_percent']))
                        {
                            $subamount=$subamount*(100-$aRow['tblsale_orders.discount_percent'])/100;
                        }
                        $_data = text_align(format_money($subamount),'right');
                        $footer_data['DTB']+=$subamount;
                    }
                    if($aColumns[$i]=='tblsale_order_items.discount_percent' || $aColumns[$i]=='tblsale_orders.discount_percent')
                    {
                        $_data = text_align(_format_number($aRow[$aColumns[$i]]).'&#37;');
                    }
                    

                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
                $x++;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
                if($key=='SL')
                    $footer_data[$key] = _format_number($total);

            }

            $output['sums'] = $footer_data;
            if($pdf==false)
            {
                echo json_encode($output);
            }
            else
            {
                return $output;
            }
            die();
        }
    }

    public function order_tracking_monthly_report_pdf(){
        $colum=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA');
        $title_colum=array(
            _l('STT'),
            "",
            "Tháng 1",
            "Tháng 2",
            "Tháng 3",
            "Tháng 4",
            "Tháng 5",
            "Tháng 6",
            "Tháng 7",
            "Tháng 8",
            "Tháng 9",
            "Tháng 10",
            "Tháng 11",
            "Tháng 12"
        );
        $data=$this->order_tracking_monthly_report(true)->aaData;

        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setTitle('tiêu đề');

        $n=count($this->order_tracking_monthly_report(true)->aaData[0]);
        for($i=0;$i<$n;$i++)
        {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i])->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
        }

        $BStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            )
        );
        $objPHPExcel->getActiveSheet()->SetCellValue('A1','CÔNG TY TNHH DUDOFF VIỆT NAM');
        $objPHPExcel->getActiveSheet()->SetCellValue('A2','ĐƠN ĐẶT HÀNG TRONG THÁNG (SO)')->getStyle('A2')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(100);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
        $objPHPExcel->getActiveSheet()->mergeCells('A2:N2');
        for($i=0;$i<$n;$i++) {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].'3', $title_colum[$i])->getStyle($colum[$i].'3')->applyFromArray($BStyle);
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
        }
        foreach($data as $key=>$value)
        {
            for($i=0;$i<$n;$i++)
            {
                if($i==0){
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].($key+4),($key+1));
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),strip_tags($value[$i]));
                }
                else
                {
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),strip_tags($value[$i]));
                }
            }
        }
        $objPHPExcel->getActiveSheet()->freezePane('A3');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Don_Dat_Hang_Trong_Thang_SO.xls"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit();

    }
    public function order_tracking_monthly_report($pdf=false)
    {
        if ($this->input->is_ajax_request()||$pdf==true)
        {
            $this->load->model('currencies_model');
            $this->load->model('invoices_model');
            $this->load->model('sale_oders_model');

            $months=array('01','02','03','04','05','06','07','08','09','10','11','12');
            //13 col
            //2 row

            $year=date('Y');
            if($this->input->post('years_report'))
                $year=$this->input->post('years_report');
            $aaData=array();
            $rowQ=array(_l('quantity'));
            $rowT=array(_l('revenue'));
            foreach ($months as $key => $month) {
                $month_detail=$this->sale_oders_model->getSaleSODetails($month,$year);
                var_dump($month_detail);die;
                $rowQ[]=_format_number($month_detail->quantity);
                $rowT[]=format_money($month_detail->grand_total);
            }
            $aaData[]=$rowQ;
            $aaData[]=$rowT;


            $result=new stdClass();
            $result->draw=1;
            $result->iTotalDisplayRecords=2;
            $result->iTotalRecords=2;
            $result->aaData=array();
            $result->aaData=$aaData;

            if($pdf==false)
            {
                echo json_encode($result);die;
            }
            else
            {
                return $result;
            }
            
        }
    }

    public function order_tracking_book_report_pdf(){
        $colum=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA');
        $title_colum=array(
            _l('STT'),
            _l('view_date'),
            _l('account_date'),
            _l('code_noo'),
            _l('orders_explan'),
            _l('product_code'),
            _l('product_name'),
            _l('unit_name'),
            _l('quantity'),
            _l('unit_cost'),
            _l('sale_revenue'),
            _l('delivered_quantity'),
            _l('rest_quantity'),
            _l('billers')
        );
        $data=$this->order_tracking_book_report(true)['aaData'];
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setTitle('tiêu đề');

        $n=count($this->order_tracking_book_report(true)['aaData'][0]);
        for($i=0;$i<$n;$i++)
        {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i])->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
        }

        $BStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            )
        );
        $objPHPExcel->getActiveSheet()->SetCellValue('A1','CÔNG TY TNHH DUDOFF VIỆT NAM');
        $objPHPExcel->getActiveSheet()->SetCellValue('A2','SỔ THEO DÕI CHI TIẾT ĐƠN BÁN HÀNG (SO)')->getStyle('A2')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(100);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
        $objPHPExcel->getActiveSheet()->mergeCells('A2:N2');
        for($i=0;$i<$n;$i++) {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].'3', $title_colum[$i])->getStyle($colum[$i].'3')->applyFromArray($BStyle);
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
        }
        foreach($data as $key=>$value)
        {
            for($i=0;$i<$n;$i++)
            {
                if($i==0){
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].($key+4),($key+1));
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),strip_tags($value[$i]));
                }
                else
                {
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),strip_tags($value[$i]));
                }
            }
        }
        $__data=$this->order_tracking_book_report(true)['sums'];
        $sum_value=array('Tổng Cộng','','','','','','',$__data['SL'],'',$__data['DSB'],$__data['DG'],$__data['CG']);
        for($i=0;$i<$n;$i++)
        {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].(count($data)+4),strip_tags($sum_value[$i]));
            }
            else
            {

                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].(count($data)+4),strip_tags($sum_value[$i]));
            }
        }
        $objPHPExcel->getActiveSheet()->freezePane('A3');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="So_Theo_Doi_Chi_Tiet_Đon_Ban_Hang_So.xls"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit();

    }
    public function order_tracking_book_report($pdf=false)
    {
        if ($this->input->is_ajax_request()||$pdf==true)
        {
            $this->load->model('currencies_model');
            $this->load->model('invoices_model');
            $this->load->model('sales_model');

            $select = array(
                'tblsales.date',
                'tblsales.account_date',                
                'CONCAT(tblsales.prefix,tblsales.code) as sale_code',
                'tblsales.reason',
                'tblitems.code',
                'tblitems.name',
                'tblunits.unit',
                'tblsale_items.quantity',
                'tblsale_items.unit_cost',
                'tblsale_items.discount_percent',
                'tblsales.discount_percent',
                'tblsale_items.amount',
                'saler_id'

            );

            
            $where  = array(
                // 'AND status != 5'
            );

            $custom_date_select = $this->get_where_report_period('tblsales.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {

                $_temp = substr($select[11], 0, -1);
                $_temp .= ' AND currency =' . $by_currency . ')';
                $select[11] = $_temp;

                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }
            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);

            if ($this->input->post('SO_status')) {
                $statuses  = $this->input->post('SO_status');
                if (count($statuses) > 0) {
                    if($statuses==1) array_push($where, 'AND status<>2');
                    if($statuses==2) array_push($where, 'AND status='.$statuses);
                    if($statuses==3) array_push($where, 'AND export_status='.'0');
                    if($statuses==4) array_push($where, 'AND export_status='.'1');
                    if($statuses==5) array_push($where, 'AND export_status='.'2');
                }

            }
            $filter=array();
            $staff=$this->input->post('staff_tracking_book_SO_detail');
            if (count($staff) > 0) 
            {
                array_push($filter, 'AND saler_id IN  (' . implode(', ', $staff) . ')');
            }
            if (count($filter) > 0) {
                array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
            }
            if($this->input->post('sale_area_SO_detail'))
            {
                array_push($where, 'AND tblclients.sale_area='.$this->input->post('sale_area_SO_detail'));
            }
            if($this->input->post('objects_group_SO_detail'))
            {
                array_push($where, 'AND tblclients.objects_group='.$this->input->post('objects_group_SO_detail'));
            }

            
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblsales';
            $join         = array(
                'LEFT JOIN tblclients ON tblclients.userid = tblsales.customer_id',
                'LEFT JOIN tblsale_items ON tblsale_items.sale_id = tblsales.id',
                'LEFT JOIN tblitems ON tblitems.id = tblsale_items.product_id',
                'LEFT JOIN tblunits ON tblunits.unitid = tblsale_items.unit_id'
            );
            // var_dump($where);die;
            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                // 'tblinvoices.prefix',
                'tblsale_items.product_id as product_id',                
                'tblsales.id as sale_id',
                // 'tblinvoices.id as invoice_id'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];

            $x       = 0;
            $footer_data = array(
                'SL' => 0,
                'DSB' => 0,
                'DG' => 0,
                'CG' => 0,
            );
            
            foreach ($rResult as $aRow) {
                $item=getDeliverdQuantity($aRow['sale_id'],$aRow['product_id']);
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if(strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]]) && strafter($aColumns[$i], 'as ')=='sale_code')
                    {
                        $_data = '<a href="' . admin_url('sales/sale_detail/' . $aRow['sale_id']) . '" target="_blank">' . $aRow['sale_code'] . '</a>';
                    }
                    if($aColumns[$i]=='tblsales.account_date' || $aColumns[$i]=='tblsales.date')
                    {
                        $_data=_d($aRow[$aColumns[$i]]);
                    }
                    if($aColumns[$i]=='tblsale_items.quantity')
                    {
                        $footer_data['SL']+=$aRow[$aColumns[$i]];
                        $_data = text_align(_format_number($aRow['tblsale_items.quantity']));
                    }
                    if($aColumns[$i]=='tblsale_items.unit_cost')
                    {
                        $_data = text_align(format_money($aRow[$aColumns[$i]]),'right');
                    }
                    if($aColumns[$i]=='tblsale_items.amount')
                    {
                        $subamount=$aRow['tblsale_items.amount'];
                        
                        if(!empty($aRow['tblsales.discount_percent']))
                        {
                            $subamount=$subamount*(100-$aRow['tblsales.discount_percent'])/100;
                        }
                        $_data = text_align(format_money($subamount),'right');
                        $footer_data['DSB']+=$subamount;
                    }
                    if($aColumns[$i]=='saler_id')
                    {
                        $_data = get_staff_full_name($aRow['saler_id']);
                    }
                    if($aColumns[$i]=='tblsale_items.discount_percent' || $aColumns[$i]=='tblsales.discount_percent')
                    {
                        $_data = text_align(_format_number($aRow[$aColumns[$i]]).'&#37;');
                    }
                    

                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
                if($key=='SL' || $key=='DG' || $key=='CG')
                    $footer_data[$key] = _format_number($total);

            }

            

            $output['sums'] = $footer_data;
            if($pdf==false)
            {
                echo json_encode($output);
            }
            else
            {
                return $output;
            }

            die();
        }
    }

    public function diaries_report($pdf=false)
    {
        if ($this->input->is_ajax_request()||$pdf==true)
        {
            $this->load->model('currencies_model');
            $this->load->model('invoices_model');
            $this->load->model('sales_model');

            $select = array(
                'tblsales.date',
                'tblsales.account_date',                
                'CONCAT(tblsales.prefix,tblsales.code) as sale_code',
                'tblinvoices.date',
                'tblinvoices.number',
                'tblsales.reason',
                '1',
                'tblsales.total',
                '2',
                'tblsales.discount',
                'tblsales.return_value',
                '5',
                'tblclients.company',
                'tblsales.saler_id'
            );

            
            $where  = array();
            $filter=array();
            $staff=$this->input->post('staff_diaries');
            $custom_date_select = $this->get_where_report_period('tblsales.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            if (count($staff) > 0) 
            {
                array_push($filter, 'AND tblsales.saler_id IN  (' . implode(', ', $staff) . ')');
            }
            if (count($filter) > 0) {
                array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
            }

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblsales';
            $join         = array(
                'JOIN tblclients ON tblclients.userid = tblsales.customer_id',
                'LEFT JOIN tblinvoices ON tblinvoices.rel_id = tblsales.id'
            );
            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'tblinvoices.prefix',
                'tblsales.customer_id',                
                'tblsales.id as sale_id',
                'tblinvoices.id as invoice_id',
                '(SELECT SUM(tax) FROM tblsale_items WHERE sale_id=tblsales.id) as total_tax_value'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];
            
            $x       = 0;
            $footer_data = array(
                'TDT' => 0,
                'DTHH' => 0,
                'DTK' => 0,
                'CK' => 0,
                'GTTV' => 0,
                'DTT' => 0
            );
            
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if(strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]]) && strafter($aColumns[$i], 'as ')=='sale_code')
                    {
                        $_data = '<a href="' . admin_url('sales/sale_detail/' . $aRow['sale_id']) . '" target="_blank">' . $aRow['sale_code'] . '</a>';
                    }
                    if($aColumns[$i]=='tblsales.account_date' || $aColumns[$i]=='tblsales.date' || $aColumns[$i]=='tblinvoices.date')
                    {
                        $_data=_d($aRow['tblsales.account_date']);
                    }
                    if($aColumns[$i]=='tblinvoices.number')
                    {
                        $code=$aRow['prefix'].str_pad($aRow['tblinvoices.number'], get_option('number_padding_prefixes'), '0', STR_PAD_LEFT);
                        if(empty($aRow['tblinvoices.number'])) $code=NULL;
                        $_data = '<a href="' . admin_url('invoices/#' . $aRow['invoice_id']) . '" target="_blank">' . $code . '</a>';
                    }
                    if($aColumns[$i]=='1')
                    {
                        $DTK=getTotalReceiptByClientID($aRow['customer_id'],$aRow['tblsales.date'],true);  
                        $TDT=$aRow['tblsales.total']+$DTK;
                        $footer_data['TDT']+=$TDT;
                        $_data = format_money($TDT);
                    }
                    if($aColumns[$i]=='tblsales.total')
                    {   
                        $footer_data['DTHH']+=$aRow['tblsales.total'];
                        $_data = format_money($aRow['tblsales.total']);
                    }
                    if($aColumns[$i]=='2')
                    {
                        $DTK=getTotalReceiptByClientID($aRow['customer_id'],$aRow['tblsales.date'],true);
                        $footer_data['DTK']+=$DTK;
                        $_data = format_money($DTK);
                    }
                    if($aColumns[$i]=='tblsales.discount')
                    {
                        $footer_data['CK']+=$aRow['tblsales.discount'];
                        $_data = format_money($aRow['tblsales.discount']);
                    }
                    if($aColumns[$i]=='tblsales.return_value')
                    {
                        $footer_data['GTTV']+=$aRow['tblsales.return_value'];
                        $_data = format_money($aRow['tblsales.return_value']);
                    }
                    if($aColumns[$i]=='tblsales.saler_id')
                    {
                        $_data=get_staff_full_name($aRow['tblsales.saler_id']);
                    }
                    if($aColumns[$i]=='5')
                    {
                        $DTK=getTotalReceiptByClientID($aRow['customer_id'],$aRow['tblsales.date'],true);   
                        $DTT=($aRow['tblsales.total']+$DTK)-$aRow['total_tax_value'];
                        $footer_data['DTT']+=$DTT;
                        $_data = format_money($DTT);
                    }


                    
                    $row[] = $_data;
                }

                $output['aaData'][] = $row;
                $x++;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
            }

            

            $output['sums'] = $footer_data;
            if($pdf==false)
            {
                echo json_encode($output);
            }
            else
            {
                return $output;
            }
            die();
        }
    }
    public function diaries_report_pdf(){
        $colum=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA');
        $title_colum=array(
                        _l('STT'),
                        _l('view_date'),
                        _l('account_date'),
                        _l('code_noo'),
                        _l('invoice_date'),
                        _l('invoice_no'),
                        _l('orders_explan'),
                        _l('total_revenue'),
                        _l('goods_revenue'),
                        _l('others_revenue'),
                        _l('discount'),
                        _l('returns_value'),
                        _l('net_revenue'),
                        _l('customer_name'),
                        _l('billers')
                    );
        $data=$this->diaries_report(true)['aaData'];
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setTitle('tiêu đề');

        $n=count($this->diaries_report(true)['aaData'][0]);
        for($i=0;$i<$n;$i++)
        {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i])->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
        }

        $BStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            )
        );
        $objPHPExcel->getActiveSheet()->SetCellValue('A1','CÔNG TY TNHH DUDOFF VIỆT NAM');
        $objPHPExcel->getActiveSheet()->SetCellValue('A2','SỔ NHẬT KÝ BÁN HÀNG')->getStyle('A2')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(100);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
        $objPHPExcel->getActiveSheet()->mergeCells('A2:N2');
        for($i=0;$i<$n;$i++) {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].'3', $title_colum[$i])->getStyle($colum[$i].'3')->applyFromArray($BStyle);
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
        }
        foreach($data as $key=>$value)
        {
            for($i=0;$i<$n;$i++)
            {
                if($i==0){
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].($key+4),($key+1));
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),strip_tags($value[$i]));
                }
                else
                {
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),strip_tags($value[$i]));
                }
            }
        }
        $__data=$this->diaries_report(true)['sums'];
        $sum_value=array('Tổng Cộng','','','','','',$__data['TDT'],$__data['DTHH'],$__data['DTK'],$__data['CK'],$__data['GTTV'],$__data['DTT']);
        for($i=0;$i<$n;$i++)
        {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].(count($data)+4),strip_tags($sum_value[$i]));
            }
            else
            {

                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].(count($data)+4),strip_tags($sum_value[$i]));
            }
        }
        $objPHPExcel->getActiveSheet()->freezePane('A3');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="So_Nhat_ky_Ban_Hang.xls"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit();

    }


    public function expenses($type = 'simple_report')
    {
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['currencies']    = $this->currencies_model->get();

        $data['title'] = _l('expenses_report');
        if ($type != 'simple_report') {
            $this->load->model('expenses_model');
            $data['categories'] = $this->expenses_model->get_category();
            $data['years']      = $this->expenses_model->get_expenses_years();

            if ($this->input->is_ajax_request()) {
                $aColumns = array(
                    'category',
                    'amount',
                    'tax',
                    '(SELECT taxrate FROM tbltaxes WHERE id=tblexpenses.tax)',
                    'amount as amount_with_tax',
                    'billable',
                    'date',
                    'CASE company WHEN "" THEN (SELECT CONCAT(firstname, " ", lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company',
                    'invoiceid',
                    'reference_no',
                    'paymentmode'
                );
                $join     = array(
                    'LEFT JOIN tblclients ON tblclients.userid = tblexpenses.clientid',
                    'LEFT JOIN tblexpensescategories ON tblexpensescategories.id = tblexpenses.category'
                );
                $where    = array();
                $filter   = array();
                include_once(APPPATH . 'views/admin/tables/includes/expenses_filter.php');
                if (count($filter) > 0) {
                    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
                }

                $by_currency = $this->input->post('currency');
                if ($by_currency) {
                    $currency = $this->currencies_model->get($by_currency);
                    array_push($where, 'AND currency=' . $by_currency);
                } else {
                    $currency = $this->currencies_model->get_base_currency();
                }
                $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);

                $sIndexColumn = "id";
                $sTable       = 'tblexpenses';
                $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                    'tblexpensescategories.name as category_name',
                    'tblexpenses.id',
                    'tblexpenses.clientid',
                    'currency'
                ));
                $output       = $result['output'];
                $rResult      = $result['rResult'];
                $this->load->model('currencies_model');
                $this->load->model('payment_modes_model');

                $footer_data = array(
                    'amount' => 0,
                    'total_tax' => 0,
                    'amount_with_tax' => 0
                );

                foreach ($rResult as $aRow) {
                    $row = array();
                    for ($i = 0; $i < count($aColumns); $i++) {
                        if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                            $_data = $aRow[strafter($aColumns[$i], 'as ')];
                        } else {
                            $_data = $aRow[$aColumns[$i]];
                        }
                        if ($aRow['tax'] != 0) {
                            $_tax = get_tax_by_id($aRow['tax']);
                        }
                        if ($aColumns[$i] == 'category') {
                            $_data = '<a href="' . admin_url('expenses/list_expenses/' . $aRow['id']) . '" target="_blank">' . $aRow['category_name'] . '</a>';
                        } else if ($aColumns[$i] == 'amount' || $i == 4) {

                            $total = $_data;
                            if ($i != 4) {
                                $footer_data['amount'] += $total;
                            } else {
                                if ($aRow['tax'] != 0 && $i == 4) {
                                    $total += ($total / 100 * $_tax->taxrate);
                                }
                                $footer_data['amount_with_tax'] += $total;
                            }

                            $_data = format_money($total, $currency_symbol);
                        } else if ($i == 7) {
                            $_data = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';
                        } else if ($aColumns[$i] == 'paymentmode') {
                            $_data = '';
                            if ($aRow['paymentmode'] != '0' && !empty($aRow['paymentmode'])) {
                                $payment_mode = $this->payment_modes_model->get($aRow['paymentmode'], array(), false, true);
                                if ($payment_mode) {
                                    $_data = $payment_mode->name;
                                }
                            }
                        } else if ($aColumns[$i] == 'date') {
                            $_data = _d($_data);
                        } else if ($aColumns[$i] == 'tax') {
                            if ($aRow['tax'] != 0) {
                                $_data = $_tax->name . ' - ' . _format_number($_tax->taxrate) . '%';
                            } else {
                                $_data = '';
                            }
                        } else if ($i == 3) {
                            if ($aRow['tax'] != 0) {
                                $total = ($total / 100 * $_tax->taxrate);
                                $_data = format_money($total, $currency_symbol);
                                $footer_data['total_tax'] += $total;
                            } else {
                                $_data = _format_number(0);
                            }
                        } else if ($aColumns[$i] == 'billable') {
                            if ($aRow['billable'] == 1) {
                                $_data = _l('expenses_list_billable');
                            } else {
                                $_data = _l('expense_not_billable');
                            }
                        } else if ($aColumns[$i] == 'invoiceid') {
                            if ($_data) {
                                $_data = '<a href="' . admin_url('invoices/list_invoices/' . $_data) . '">' . format_invoice_number($_data) . '</a>';
                            } else {
                                $_data = '';
                            }

                        }
                        $row[] = $_data;
                    }
                    $output['aaData'][] = $row;

                }

                foreach ($footer_data as $key => $total) {
                    $footer_data[$key] = format_money($total, $currency_symbol);
                }

                $output['sums'] = $footer_data;
                echo json_encode($output);
                die;
            }
            $this->load->view('admin/reports/expenses_detailed', $data);
        } else {
            if (!$this->input->get('year')) {
                $data['current_year'] = date('Y');
            } else {
                $data['current_year'] = $this->input->get('year');
            }

            $data['chart_js_assets']   = true;

            $data['export_not_supported'] = ($this->agent->browser() == 'Internet Explorer' || $this->agent->browser() == 'Spartan');

            $this->load->model('expenses_model');

            $data['chart_not_billable'] = json_encode($this->reports_model->get_stats_chart_data(_l('not_billable_expenses_by_categories'), array(
                'billable' => 0
            ), array(
                'backgroundColor' => 'rgba(252,45,66,0.4)',
                'borderColor' => '#fc2d42'
            ), $data['current_year']));

            $data['chart_billable'] = json_encode($this->reports_model->get_stats_chart_data(_l('billable_expenses_by_categories'), array(
                'billable' => 1
            ), array(
                'backgroundColor' => 'rgba(37,155,35,0.2)',
                'borderColor' => '#84c529'
            ), $data['current_year']));

            $data['expense_years'] = $this->expenses_model->get_expenses_years();
            $data['categories']    = $this->expenses_model->get_category();

            $this->load->view('admin/reports/expenses', $data);
        }
    }
    public function expenses_vs_income($year = '')
    {
        $_expenses_years = array();
        $_years          = array();
        $this->load->model('expenses_model');
        $expenses_years = $this->expenses_model->get_expenses_years();
        $payments_years = $this->reports_model->get_distinct_payments_years();
        foreach ($expenses_years as $y) {
            array_push($_years, $y['year']);
        }
        foreach ($payments_years as $y) {
            array_push($_years, $y['year']);
        }
        $_years                                  = array_map("unserialize", array_unique(array_map("serialize", $_years)));
        $data['years']                           = $_years;
        $data['chart_expenses_vs_income_values'] = json_encode($this->reports_model->get_expenses_vs_income_report($year));
        $data['title']                           = _l('als_expenses_vs_income');
        $data['chart_js_assets']   = true;
        $this->load->view('admin/reports/expenses_vs_income', $data);
    }

    /* Total income report / ajax chart*/
    public function total_income_report()
    {
        echo json_encode($this->reports_model->total_income_report());
    }
    public function report_by_payment_modes()
    {

        echo json_encode($this->reports_model->report_by_payment_modes());
    }
    public function report_by_customer_groups()
    {
        echo json_encode($this->reports_model->report_by_customer_groups());
    }
    /* Leads conversion monthly report / ajax chart*/
    public function leads_monthly_report($month)
    {
        echo json_encode($this->reports_model->leads_monthly_report($month));
    }
    public function get_tk_sales($id_client="",$colum="",$type=false,$date_start="",$date_end="")
    {
        // PO
        $this->db->select_sum('tblsale_order_items.amount');
        $this->db->join('tblsale_order_items','tblsale_order_items.sale_id=tblsale_orders.id','left');
        $this->db->where('tblsale_orders.customer_id',$id_client);
        if($date_start!="" && $date_end!="")
        {

            if($type==false)
            {
                $this->db->where('tblsale_orders.date < "'.$date_start.'"');
            }
            else
            {
                $this->db->where('tblsale_orders.date BETWEEN "'.$date_start.'" AND "'.$date_end.'"');
            }
        }
        $this->db->where('tblsale_order_items.'.$colum,6);
        $resultPO= $this->db->get('tblsale_orders')->row()->amount;

        $this->db->select('SUM(tblsale_orders.transport_fee) as transport_fee,SUM(tblsale_orders.installation_fee) as installation_fee,SUM(tblsale_orders.discount) as discount');
        $this->db->where('tblsale_orders.customer_id',$id_client);
        if($date_start!="" && $date_end!="")
        {

            if($type==false)
            {
                $this->db->where('tblsale_orders.date < "'.$date_start.'"');
            }
            else
            {
                $this->db->where('tblsale_orders.date BETWEEN "'.$date_start.'" AND "'.$date_end.'"');
            }
        }
        $resultPOO= $this->db->get('tblsale_orders')->row();
        $resultPO+=$resultPOO->transport_fee+$resultPOO->installation_fee-$resultPOO->discount;
        
        // SO
        $this->db->select_sum('tblsale_items.amount');
        $this->db->join('tblsale_items','tblsale_items.sale_id=tblsales.id','left');
        $this->db->where('tblsales.customer_id',$id_client);
        $this->db->where('tblsales.rel_id is null');
        if($date_start!=""&&$date_end!="")
        {
            if($type==false)
            {
                $this->db->where('tblsales.date < "'.$date_start.'"');
            }
            else
            {
                $this->db->where('tblsales.date BETWEEN "'.$date_start.'" AND "'.$date_end.'"');
            }
        }
        $this->db->where('tblsale_items.'.$colum,6);
        $resultSO= $this->db->get('tblsales')->row()->amount;
        $this->db->select('SUM(tblsales.transport_fee) as transport_fee,SUM(tblsales.installation_fee) as installation_fee,SUM(tblsales.discount) as discount');
        $this->db->where('tblsales.customer_id',$id_client);
        $this->db->where('tblsales.rel_id is null');
        if($date_start!=""&&$date_end!="")
        {
            if($type==false)
            {
                $this->db->where('tblsales.date < "'.$date_start.'"');
            }
            else
            {
                $this->db->where('tblsales.date BETWEEN "'.$date_start.'" AND "'.$date_end.'"');
            }
        }
        $resultSOO= $this->db->get('tblsales')->row();
        $resultSO+=$resultSOO->transport_fee+$resultSOO->installation_fee-$resultSOO->discount;
        if($resultPO || $resultSO)
        {
            return $resultPO+$resultSO;
        }
        return 0;
    }

    public function get_tk_returns($id_client="",$colum="tk_co",$type=false,$date_start="",$date_end="")
    {
        // Return items
        $this->db->select_sum('tblimport_items.sub_total');
        $this->db->join('tblimport_items','tblimport_items.import_id=tblimports.id','left');
        $this->db->where('tblimports.customer_id',$id_client);
        if($date_start!="" && $date_end!="")
        {

            if($type==false)
            {
                $this->db->where('tblimports.date < "'.$date_start.'"');
            }
            else
            {
                $this->db->where('tblimports.date BETWEEN "'.$date_start.'" AND "'.$date_end.'"');
            }
        }
        $this->db->where('tblimport_items.'.$colum,6);
        $result= $this->db->get('tblimports')->row()->amount;

        $this->db->select('SUM(tblimports.adjustment) as adjustment,SUM(tblimports.discount) as discount',false);
        $this->db->where('tblimports.customer_id',$id_client);
        if($date_start!="" && $date_end!="")
        {

            if($type==false)
            {
                $this->db->where('tblimports.date < "'.$date_start.'"');
            }
            else
            {
                $this->db->where('tblimports.date BETWEEN "'.$date_start.'" AND "'.$date_end.'"');
            }
        }
        $resultO= $this->db->get('tblimports')->row();
        
        if($result || $resultO)
        {
            $total=$result+$resultO->adjustment-$resultO->discount;
            
            return $total;
        }
        return 0;
    }


    public function genernal_receivable_debts_report_pdf(){
        $colum=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA');
        $title_colum=array(
            _l('STT'),
            _l('customer_code'),
            _l('customer_name'),
            _l('tk_debt'),
            _l('debt_no'),
            _l('debt_co'),
            _l('incurred_debt_no'),
            _l('surplus_debt_no'),
            _l('surplus_debt_co')
        );
        $data=$this->genernal_receivable_debts_report(true)['aaData'];
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setTitle('tiêu đề');

        $n=count($this->genernal_receivable_debts_report(true)['aaData'][0]);
        for($i=0;$i<$n;$i++)
        {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i])->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
        }

        $BStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            )
        );
        $objPHPExcel->getActiveSheet()->SetCellValue('A1','CÔNG TY TNHH DUDOFF VIỆT NAM');
        $objPHPExcel->getActiveSheet()->SetCellValue('A2','TỔNG CÔNG NỢ PHẢI THU (131)')->getStyle('A2')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(100);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
        $objPHPExcel->getActiveSheet()->mergeCells('A2:N2');
        for($i=0;$i<$n;$i++) {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].'3', $title_colum[$i])->getStyle($colum[$i].'3')->applyFromArray($BStyle);
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
        }
        foreach($data as $key=>$value)
        {
            for($i=0;$i<$n;$i++)
            {
                if($i==0){
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].($key+4),($key+1));
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),strip_tags($value[$i]));
                }
                else
                {
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),strip_tags($value[$i]));
                }
            }
        }
        $objPHPExcel->getActiveSheet()->freezePane('A3');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Tong_Cong_Phai_Thu_131.xls"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit();

    }
    public function genernal_receivable_debts_report($pdf=false)
    {
        if ($this->input->is_ajax_request()||$pdf==true)
        {
            $this->load->model('currencies_model');
            $this->load->model('invoices_model');
            $this->load->model('sales_model');
            
            //Thu
            $mounth_report=$this->input->post('report_months');
            if ($mounth_report != ''&&$mounth_report) {
                if (is_numeric($mounth_report)) {
                    $minus_months       = date('Y-m-d', strtotime("-$mounth_report MONTH"));
                    $start_date=$minus_months;
                    $start_end=date('Y-m-d');

                }
                else if ($mounth_report == 'custom') {
                    $start_date = to_sql_date($this->input->post('report_from'));
                    $start_end   = to_sql_date($this->input->post('report_to'));
                }
            }
            $select = array(
                'tblclients.code',
                'company',
                '2',
                '3',
                '4',
                '5',
                '6',
                '7',
                '8'
            );
            $this->db->distinct('customer_id');
            $this->db->select('customer_id');
            $clientsPO=$this->db->get('tblsale_orders')->result();
            $this->db->distinct('customer_id');
            $this->db->select('customer_id');
            $clientsSO=$this->db->get('tblsales')->result();
            $Clients=array_merge($clientsPO,$clientsSO);
            $arrClients=array();
            foreach ($Clients as $key => $Client) {
                $arrClients[]=$Client->customer_id;
            }
            $arrClients=array_unique($arrClients);
            $where  = array(
            );
            if($arrClients)
            {
                array_push($where, "AND tblclients.userid IN(".implode(',', $arrClients).")");
            }
            // array_push($where, "AND tblclients.userid IN(442,435,427)");
            $aColumns     = $select;
            $sIndexColumn = "userid";
            $sTable       = 'tblclients';
            $join         = array(
            );

            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'userid',
                'debt'
            ),'order by tblclients.code desc');
            $output  = $result['output'];
            $rResult = $result['rResult'];

            $x       = 0;

            $footer_data = array(
                'f1' => 0,
                'f2' => 0,
                'f3' => 0,
                'f4' => 0,
                'f5' => 0,
                'f6' => 0
            );
            
            foreach ($rResult as $aRow) {
                $row = array();
                $totalDebt=0;
                $active=false;
                for ($i = 0; $i < count($aColumns); $i++) {
                    $total=0;
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if($aColumns[$i]=='tblclients.code'||$aColumns[$i]=='company')
                    {
                        $_data='<a target="_blank" href="'.admin_url('clients/client/'.$aRow['userid']).'">'.$aRow[$aColumns[$i]].'</a>';
                    }
                    if($aColumns[$i]=='2')
                    {
                        $_data="<p class='text-center'>".get_code_tk(6)."</p>";
                    }
                    if($aColumns[$i]=='3')
                    {
                        $data=$aRow['debt'];
                        if($data<0)
                        {
                            $data=0;
                            $total=0;
                        }
                        if($start_date&&$start_end)
                        {
                            $data_tk=$this->get_tk_sales($aRow['userid'],'tk_no',false,$start_date,$start_end);
                            if($data_tk)
                            {
                                $total=$data_tk+$data;
                                $_data=_format_number($data_tk+$data);
                            }
                            else
                            {
                                $total=$data;
                                $_data=_format_number($data);
                            }
                        }
                        else
                        {
                            $total=$data;
                            $_data=_format_number($data);
                        }
                        $_data=text_align($_data,'right');
                        $footer_data['f1']+=$total;
                        $totalDebt=$total;
                        if(!empty($total))
                            $active=true;
                    }
                    if($aColumns[$i]=='4')
                    {
                        $total=0;
                        $data=$aRow['debt'];
                        if($data>0)
                        {
                            $data=0;
                            $total=0;
                        }
                        if($start_date&&$start_end)
                        {
                            $data_tk=$this->get_tk_sales($aRow['userid'],'tk_co',false,$start_date,$start_end);

                            if($data_tk)
                            {
                                $_data=_format_number($data_tk+($data*(-1)));
                                $total=$data_tk+($data*(-1));
                            }
                            else
                            {
                                $_data=_format_number($data*(-1));
                                $total=$data_tk+($data*(-1));
                            }
                        }
                        else
                        {
                            $_data=_format_number($data*(-1));
                            $total=($data*(-1));
                        }
                        $_data=text_align($_data,'right');
                        $footer_data['f2']+=$_data;
                        $totalDebt+=-$total;
                        if(!empty($total))
                            $active=true;
                    }
                    if($aColumns[$i]=='5')
                    {
                        $data_tk=$this->get_tk_sales($aRow['userid'],'tk_no',true,$start_date,$start_end);
                        if($data_tk)
                        {
                            $total=$data_tk;
                            $_data=_format_number($data_tk);
                        }
                        else
                        {
                            $total=0;
                            $_data=0;
                        }
                        $_data=text_align($_data,'right');
                        $footer_data['f3']+=$total;
                        $totalDebt+=$total;
                        if(!empty($total))
                            $active=true;
                    }
                    if($aColumns[$i]=='6')
                    {
                        $data_tk=getTotalCustomerPayment($aRow['userid'],6,$start_date,$start_end)+$this->get_tk_returns($aRow['userid'],'tk_co',true,$start_date,$start_end);
                        if($data_tk)
                        {
                            $_data=_format_number($data_tk);
                            $total=$data_tk;
                        }
                        else
                        {
                            $total=0;
                            $_data=0;
                        }
                        $_data=text_align($_data,'right');
                        $footer_data['f4']+=$total;
                        $totalDebt-=$total;
                        if(!empty($total))
                            $active=true;
                    }
                    if($aColumns[$i]=='7')
                    {
                        $total=0;
                        $_data=0;
                        if($totalDebt>0)
                        {
                            $total=$totalDebt;
                            $_data=_format_number($totalDebt);
                        }
                        $_data=text_align($_data,'right');
                        $footer_data['f5']+=$total;
                    }
                    if($aColumns[$i]=='8')
                    {
                        $total=0;
                        $_data=0;
                        if($totalDebt<0)
                        {
                            $total=$totalDebt*(-1);
                            $_data=_format_number($total);
                        }
                        $_data=text_align($_data,'right');
                        $footer_data['f6']+=$total;
                    }
                    
                    $row[] = $_data;
                    
                }

               if($active)
                {
                    $output['aaData'][] = $row;
                }
                $x++;
            } 

            $output['iTotalRecords']=count($output['aaData']);
                $output['iTotalDisplayRecords']=count($output['aaData']);
            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = _format_number($total);
            }
            $output['sums'] = $footer_data;
            if($pdf==false){
                echo json_encode($output);
            }
            else
            {
                return $output;
            }
            die();
        }
    }

    public function get_tk_import_suppliers($id_supplier="",$colum="",$type=false,$date_start="",$date_end="")
    {
        $this->db->select('sum(tblimport_items.sub_total) as cum_money');
        $this->db->join('tblimport_items','tblimport_items.import_id=tblimports.id','left');
        $this->db->where('tblimports.supplier_id',$id_supplier);
        if($date_start!=""&&$date_end!="")
        {
            if($type==false)
            {
                $this->db->where('tblimports.date < "'.$date_start.'"');
            }
            else
            {
                $this->db->where('tblimports.date BETWEEN "'.$date_start.'" AND "'.$date_end.'"');
            }
        }
        $this->db->where('tblimport_items.'.$colum,34);
        $result= $this->db->get('tblimports')->row();
        if($result)
        {
            return $result->cum_money;
        }
        return 0;
    }

    public function genernal_receivables_suppliers_debts_report_pdf(){
        $colum=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA');
        $title_colum=array(
            _l('STT'),
            _l('suppliers_code'),
            _l('suppliers_name'),
            _l('tk_debt'),
            _l('debt_no'),
            _l('debt_co'),
            _l('incurred_debt_no'),
            _l('incurred_debt_co'),
            _l('surplus_debt_no'),
            _l('surplus_debt_co')
        );
        $data=$this->genernal_receivables_suppliers_debts_report(true)['aaData'];
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setTitle('tiêu đề');

        $n=count($this->genernal_receivables_suppliers_debts_report(true)['aaData'][0]);
        for($i=0;$i<$n;$i++)
        {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i])->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
        }

        $BStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            )
        );
        $objPHPExcel->getActiveSheet()->SetCellValue('A1','CÔNG TY TNHH DUDOFF VIỆT NAM');
        $objPHPExcel->getActiveSheet()->SetCellValue('A2','TỔNG CÔNG NỢ NHÀ CUNG CẤP (331)')->getStyle('A2')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(100);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
        $objPHPExcel->getActiveSheet()->mergeCells('A2:N2');
        for($i=0;$i<$n;$i++) {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].'3', $title_colum[$i])->getStyle($colum[$i].'3')->applyFromArray($BStyle);
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
        }
        foreach($data as $key=>$value)
        {
            for($i=0;$i<$n;$i++)
            {
                if($i==0){
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].($key+4),($key+1));
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),strip_tags($value[$i]));
                }
                else
                {
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),strip_tags($value[$i]));
                }
            }
        }
        $objPHPExcel->getActiveSheet()->freezePane('A3');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Tong_Cong_No_Nha_Cung_Cap_331.xls"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit();

    }
    public function genernal_receivables_suppliers_debts_report($pdf=false)
    {
        if ($this->input->is_ajax_request()||$pdf==true)
        {
            $this->load->model('currencies_model');
            $this->load->model('invoices_model');
            $this->load->model('sales_model');

            //Thu
            $mounth_report=$this->input->post('report_months');
            if ($mounth_report != ''&&$mounth_report) {
                if (is_numeric($mounth_report)) {
                    $minus_months       = date('Y-m-d', strtotime("-$mounth_report MONTH"));
                    $start_date=$minus_months;
                    $start_end=date('Y-m-d');

                }
                else if ($mounth_report == 'custom') {
                    $start_date = to_sql_date($this->input->post('report_from'));
                    $start_end   = to_sql_date($this->input->post('report_to'));
                }
            }
            $select = array(
                'supplier_code',
                'tblsuppliers.company',
                '2',
                '3',
                '4',
                '5',
                '6',
                '7',
                '8'
            );


            $where  = array(
            );
            $aColumns     = $select;
            $sIndexColumn = "userid";
            $sTable       = 'tblsuppliers';
            $join         = array(
            );
            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'userid','debt'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];

            $x       = 0;

            $footer_data = array(
                'SPSN' => 0,
                'SPSC' => 0,
                'ST' => 0
            );

            foreach ($rResult as $aRow) {
                $row = array();
                $totalDebt=0;
                for ($i = 0; $i < count($aColumns); $i++) {
                    $total=0;
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if($aColumns[$i]=='supplier_code'||$aColumns[$i]=='company')
                    {
                        $_data='<a href="'.admin_url('suppliers/supplier/'.$aRow['userid']).'">'.$aRow[$aColumns[$i]].'</a>';
                    }
                    if($aColumns[$i]==2)
                    {
                        $_data=get_code_tk(34);
                    }
                    if($aColumns[$i]==3)
                    {
                        $data=$aRow['debt'];
                        $total=$data;
                        if($data>0)
                        {
                            $data=0;
                            $total=0;
                        }
                        if($start_date&&$start_end)
                        {
                            $data_tk=$this->get_tk_import_suppliers($aRow['userid'],'tk_no',false,$start_date,$start_end);
                            if($data_tk)
                            {
                                $_data=_format_number($data_tk+($data*(-1)));
                                $total=$data_tk+($data*(-1));
                            }
                            else
                            {
                                $_data=_format_number($data*(-1))   ;
                                $total=$data*(-1);
                            }
                        }
                        else
                        {
                            $_data=_format_number($data*(-1));
                            $total=$data*(-1);
                        }
                        $totalDebt=$total;
                    }
                    if($aColumns[$i]==4)
                    {
                        $data=$aRow['debt'];
                        if($data<0)
                        {
                            $data=0;
                            $total=0;
                        }
                        if($start_date&&$start_end)
                        {
                            $data_tk=$this->get_tk_import_suppliers($aRow['userid'],'tk_co',false,$start_date,$start_end);
                            if($data_tk)
                            {
                                $_data=_format_number($data_tk+$data);
                                $total=$data_tk+$data;
                            }
                            else
                            {
                                $_data=_format_number($data);
                                $total=$data;
                            }
                        }
                        else
                        {
                            $_data=_format_number($data);
                            $total=$data;
                        }
                        $totalDebt-=$total;
                    }
                    if($aColumns[$i]==5)
                    {
                        $data_tk=$this->get_tk_import_suppliers($aRow['userid'],'tk_no',true,$start_date,$start_end);
                        if($data_tk)
                        {
                            $_data=_format_number($data_tk);
                            $total=$data_tk;
                        }
                        else
                        {
                            $_data=0;
                            $total=0;
                        }
                        $totalDebt+=$total;
                    }
                    if($aColumns[$i]==6)
                    {
                        $data_tk=$this->get_tk_import_suppliers($aRow['userid'],'tk_co',true,$start_date,$start_end);
                        if($data_tk)
                        {
                            $_data=_format_number($data_tk);
                            $total=$data_tk;
                        }
                        else
                        {
                            $_data=0;
                            $total=0;
                        }
                        $totalDebt-=$total;
                    }
                    if($aColumns[$i]==7)
                    {
                        $_data=0;
                        if($totalDebt>0)
                        {
                            $_data=_format_number($totalDebt);
                        }
                    }
                    if($aColumns[$i]==8)
                    {
                        $_data=0;
                        if($totalDebt<0)
                        {
                            $_data=_format_number($totalDebt*(-1));
                        }
                    }
                    $row[] = $_data;
                }


                $output['aaData'][] = $row;
                $x++;
            }
            $output['sums'] = $footer_data;
            if($pdf==false)
            {
                echo json_encode($output);
            }
            else
            {
                return $output;
            }
            die();
        }
    }
   
    public function detailed_sales_contract_report($pdf=false)
    {
        if ($this->input->is_ajax_request()||$pdf==true)
        {
            $this->load->model('currencies_model');
            $this->load->model('invoices_model');
            $this->load->model('sales_model');
            $select = array(
                'tblcontracts.datestart',
                'CONCAT(tblcontracts.prefix,tblcontracts.code) as contract_code',                
                'tblclients.company',
                'tblitems.code',
                'tblitems.name',
                'tblunits.unit',
                'tblcontract_items.quantity',
                '1',
                '2',
                'tblcontract_items.amount',
                '3',
                '4'
            );

            
            $where  = array(
                // 'AND status != 5'
            );

            $custom_date_select = $this->get_where_report_period('tblcontracts.datestart');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {

                $_temp = substr($select[11], 0, -1);
                $_temp .= ' AND currency =' . $by_currency . ')';
                $select[11] = $_temp;

                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }
            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);

            // if ($this->input->post('SO_status_gen')) {
            //     $statuses  = $this->input->post('SO_status_gen');
            //     if (count($statuses) > 0) {
            //         if($statuses==1) array_push($where, 'AND status<>2');
            //         if($statuses==2) array_push($where, 'AND status='.$statuses);
            //         if($statuses==3) array_push($where, 'AND export_status='.'0');
            //         if($statuses==4) array_push($where, 'AND export_status='.'1');
            //         if($statuses==5) array_push($where, 'AND export_status='.'2');
            //     }

            // }
            
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblcontracts';
            $join         = array(
                'LEFT JOIN tblclients ON tblclients.userid = tblcontracts.client',
                'LEFT JOIN tblcontract_items ON tblcontract_items.contract_id = tblcontracts.id',
                'LEFT JOIN tblitems ON tblitems.id = tblcontract_items.product_id',
                'LEFT JOIN tblunits ON tblunits.unitid = tblcontract_items.unit_id'
            );
            // var_dump($where);die;
            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                // 'tblinvoices.prefix',
                'tblcontract_items.product_id as product_id',                
                'tblcontracts.id as contract_id',
                // 'tblinvoices.id as invoice_id'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];

            $x       = 0;
            $footer_data = array(
                'SL' => 0,
                'SLDG' => 0,
                'SLCL' => 0,
                'DSHD' => 0,
                'DSTH' => 0,
                'DSCL' => 0,
            );
            
            foreach ($rResult as $aRow) {
                $item=getDeliverdQuantityByContractID($aRow['contract_id'],$aRow['product_id']);
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if(strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]]) && strafter($aColumns[$i], 'as ')=='contract_code')
                    {
                        $_data = '<a href="' . admin_url('contracts/contract/' . $aRow['contract_id']) . '" target="_blank">' . $aRow['contract_code'] . '</a>';
                    }
                    if($aColumns[$i]=='tblcontracts.datestart')
                    {
                        $_data=_d($aRow[$aColumns[$i]]);
                    }
                    if($aColumns[$i]=='tblcontract_items.quantity')
                    {
                        $footer_data['SL']+=$aRow[$aColumns[$i]];
                        $_data = _format_number($aRow['tblcontract_items.quantity']);
                    }
                    if($aColumns[$i]=='tblcontract_items.amount')
                    {
                        if($aColumns[$i]=='tblcontract_items.amount')
                            $footer_data['DSHD']+=$aRow[$aColumns[$i]];
                        $_data = format_money($aRow[$aColumns[$i]]);
                    }
                    if($aColumns[$i]=='1')
                    {   
                        $delivered_quantity=$item->delivery_quantity;
                        $footer_data['SLDG']+=$delivered_quantity;
                        $_data = _format_number($delivered_quantity);
                        if($_data== false) $_data=0;

                    }
                    if($aColumns[$i]=='2')
                    {   
                        $rest_quantity=$aRow['tblcontract_items.quantity']-$item->delivery_quantity;
                        $footer_data['SLCL']+=$rest_quantity;
                        $_data = _format_number($rest_quantity);
                        if($_data==false) $_data=0;
                    }
                    if($aColumns[$i]=='3')
                    {   
                        $delivered_amount=getEffectuatedAmount($item->unit_cost,$item->delivery_quantity,$item->tax_rate);
                        $footer_data['DSTH']+=$delivered_amount;
                        $_data = format_money($delivered_amount);
                        if($_data== false) $_data=0;
                    }
                    if($aColumns[$i]=='4')
                    {   
                        $rest_amount=$aRow['tblcontract_items.amount']-getEffectuatedAmount($item->unit_cost,$item->delivery_quantity,$item->tax_rate);
                        $footer_data['DSCL']+=$rest_amount;
                        $_data = format_money($rest_amount);
                        if($_data== false) $_data=0;
                    }
                    

                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
                if($key=='SL' || $key=='SLDG' || $key=='SLCL')
                    $footer_data[$key] = _format_number($total);

            }

            

            $output['sums'] = $footer_data;
            if($pdf==false)
            {
                echo json_encode($output);
            }
            else
            {
                return $output;
            }

            die();
        }
    }

    public function detailed_sales_contract_report_pdf(){
        $colum=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA');
        $title_colum=array(
            _l('STT'),
            _l('contract_date'),
            _l('contract_code'),
            _l('customer_name'),
            _l('product_code'),
            _l('product_name'),
            _l('contract_unit'),
            _l('contract_quantity'),
            _l('contract_quantity_delivered'),
            _l('contract_quantity_rest'),
            _l('contract_sales'),
            _l('contract_sales_effectuated'),
            _l('contract_sales_rest')
        );
        $data=$this->detailed_sales_contract_report(true)['aaData'];
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setTitle('tiêu đề');

        $n=count($this->detailed_sales_contract_report(true)['aaData'][0]);
        for($i=0;$i<$n;$i++)
        {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i])->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
        }

        $BStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            )
        );
        $objPHPExcel->getActiveSheet()->SetCellValue('A1','CÔNG TY TNHH DUDOFF VIỆT NAM');
        $objPHPExcel->getActiveSheet()->SetCellValue('A2','TỔNG CÔNG NỢ NHÀ CUNG CẤP (331)')->getStyle('A2')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(100);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
        $objPHPExcel->getActiveSheet()->mergeCells('A2:N2');
        for($i=0;$i<$n;$i++) {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].'3', $title_colum[$i])->getStyle($colum[$i].'3')->applyFromArray($BStyle);
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
        }
        foreach($data as $key=>$value)
        {
            for($i=0;$i<$n;$i++)
            {
                if($i==0){
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].($key+4),($key+1));
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),strip_tags($value[$i]));
                }
                else
                {
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),strip_tags($value[$i]));
                }
            }
        }

        $__data=$this->detailed_sales_contract_report(true)['sums'];
        $sum_value=array('Tổng Cộng','','','','','',$__data['SL'],$__data['SLDG'],$__data['SLCL'],$__data['DSHD'],$__data['DSTH'],$__data['DSCL']);
        for($i=0;$i<$n;$i++)
        {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].(count($data)+4),strip_tags($sum_value[$i]));
            }
            else
            {

                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].(count($data)+4),strip_tags($sum_value[$i]));
            }
        }

        $objPHPExcel->getActiveSheet()->freezePane('A3');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Bao_Cao_Chi_Tiet_Hop_Dong_Ban_Hang.xls"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit();

    }

    public function sales_analysis_products_report($pdf=false)
    {
        if ($this->input->is_ajax_request()||$pdf==true)
        {
            $this->load->model('currencies_model');

            $select = array(
                'tblcategories.category',
                'tblitems.name',                
                'tblunits.unit',
                'tblsale_items.quantity',
                'tblsale_items.discount',
                'tblsale_items.sub_total',
                'tblsale_items.amount',
                '1',
                '2'
            );

            
            $where  = array(
                // 'AND status != 5'
            );

            $custom_date_select = $this->get_where_report_period('tblsales.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }

            $by_currency = $this->input->post('report_currency');
            if ($by_currency) {

                $_temp = substr($select[11], 0, -1);
                $_temp .= ' AND currency =' . $by_currency . ')';
                $select[11] = $_temp;

                $currency = $this->currencies_model->get($by_currency);
                array_push($where, 'AND currency=' . $by_currency);
            } else {
                $currency = $this->currencies_model->get_base_currency();
            }
            $currency_symbol = $this->currencies_model->get_currency_symbol($currency->id);

            // if ($this->input->post('SO_status_gen')) {
            //     $statuses  = $this->input->post('SO_status_gen');
            //     if (count($statuses) > 0) {
            //         if($statuses==1) array_push($where, 'AND status<>2');
            //         if($statuses==2) array_push($where, 'AND status='.$statuses);
            //         if($statuses==3) array_push($where, 'AND export_status='.'0');
            //         if($statuses==4) array_push($where, 'AND export_status='.'1');
            //         if($statuses==5) array_push($where, 'AND export_status='.'2');
            //     }

            // }
            
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblsales';
            $join         = array(
                'LEFT JOIN tblclients ON tblclients.userid = tblsales.customer_id',
                'LEFT JOIN tblsale_items ON tblsale_items.sale_id = tblsales.id',
                'LEFT JOIN tblitems ON tblitems.id = tblsale_items.product_id',
                'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id',
                'LEFT JOIN tblunits ON tblunits.unitid = tblsale_items.unit_id'
            );
            // var_dump($where);die;
            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                // 'tblinvoices.prefix',
                'tblsale_items.product_id as product_id',                
                'tblsales.id as sale_id',
                // 'tblinvoices.id as invoice_id'
            ));
            $output  = $result['output'];
            $rResult = $result['rResult'];

            $x       = 0;
            $footer_data = array(
                'SL' => 0,
                'DSB' => 0,
                'DG' => 0,
                'CG' => 0,
            );
            
            foreach ($rResult as $aRow) {
                $item=getDeliverdQuantity($aRow['sale_id'],$aRow['product_id']);
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }

                    if(strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]]) && strafter($aColumns[$i], 'as ')=='sale_code')
                    {
                        $_data = '<a href="' . admin_url('sales/sale_detail/' . $aRow['sale_id']) . '" target="_blank">' . $aRow['sale_code'] . '</a>';
                    }
                    if($aColumns[$i]=='tblsales.account_date' || $aColumns[$i]=='tblsales.date')
                    {
                        $_data=_d($aRow[$aColumns[$i]]);
                    }
                    if($aColumns[$i]=='tblsale_items.quantity')
                    {
                        $footer_data['SL']+=$aRow[$aColumns[$i]];
                        $_data = _format_number($aRow['tblsale_items.quantity']);
                    }
                    if($aColumns[$i]=='tblsale_items.sub_total' ||$aColumns[$i]=='tblsale_items.discount' || $aColumns[$i]=='tblsale_items.amount')
                    {
                        if($aColumns[$i]=='tblsale_items.amount')
                            $footer_data['DSB']+=$aRow[$aColumns[$i]];
                        $_data = format_money($aRow[$aColumns[$i]]);
                    }
                    // if($aColumns[$i]=='1')
                    // {   
                    //     $delivered_quantity=$item->delivery_quantity;
                    //     $footer_data['DG']+=$delivered_quantity;
                    //     $_data = _format_number($item->delivery_quantity);
                    //     if($_data== false) $_data=0;

                    // }
                    // if($aColumns[$i]=='2')
                    // {   
                    //     $rest_quantity=$item->quantity-$item->delivery_quantity;
                    //     $footer_data['CG']+=$rest_quantity;
                    //     $_data = _format_number($rest_quantity);
                    //     if($_data==false) $_data=0;
                    // }
                    

                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }

            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = format_money($total, $currency_symbol);
                if($key=='SL' || $key=='DG' || $key=='CG')
                    $footer_data[$key] = _format_number($total);

            }

            

            $output['sums'] = $footer_data;
            if($pdf==false)
            {
                echo json_encode($output);
            }
            else
            {
                return $output;
            }

            die();
        }
    }

    public function stock_card_report($pdf=false)
    {
        // if ($this->input->is_ajax_request()||$pdf==true)
        {

            $this->load->model('currencies_model');

            //Nhap kho
            $select = array(
                'tblitems.id as product_id',
                'tblimports.date as date',
                'CONCAT(tblimports.prefix,tblimports.code) as code',
                'tblimports.reason as reason',
                'tblunits.unit as unit',
                'tblimport_items.quantity_net as import_quantity',
                '0  as export_quantity',
                'null as exists_quantity'
            );
            $where  = array(
            );
            $custom_date_select = $this->get_where_report_period('tblimports.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblimports';
            $join         = array(
                'LEFT JOIN tblimport_items ON tblimport_items.import_id = tblimports.id', 
                'LEFT JOIN tblitems ON tblitems.id = tblimport_items.product_id', 
                'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id', 
                'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit', 
            );
            $order_by='order by product_id asc';
            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'tblitems.name',
                'tblitems.short_name',
                'tblitems.code as product_code',
                'tblcategories.category',
                'tblimports.id as import_id',
                'tblimports.rel_type',
                'warehouse_id',
                'warehouse_id_to',
            ),$order_by);
            $output  = $result['output'];
            $rResult = $result['rResult'];
            


            //Chuyen kho
            $selectT = array(
                'tblitems.id as product_id',
                'tblimports.date as date',
                'CONCAT(tblimports.prefix,tblimports.code) as code',
                'tblimports.reason as reason',
                'tblunits.unit as unit',
                '0 as import_quantity',
                'tblimport_items.quantity_net as export_quantity',
                'null as exists_quantity'
            );
            $whereT  = array(
                'AND tblimports.rel_type="' . 'transfer"'
            );
            $custom_date_select = $this->get_where_report_period('tblimports.date');
            if ($custom_date_select != '') {
                array_push($whereT, $custom_date_select);
            }
            $aColumnsT     = $selectT;
            $sIndexColumnT = "id";
            $sTableT       = 'tblimports';
            $joinT         = array(
                'LEFT JOIN tblimport_items ON tblimport_items.import_id = tblimports.id', 
                'LEFT JOIN tblitems ON tblitems.id = tblimport_items.product_id', 
                'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id', 
                'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit', 
            );
            $order_byT='order by product_id asc';
            $resultT  = data_tables_init($aColumnsT, $sIndexColumnT, $sTableT, $joinT, $whereT, array(
                'tblitems.name',
                'tblitems.short_name',
                'tblitems.code as product_code',
                'tblcategories.category',
                'tblimports.id as import_id',
                'tblimports.rel_type',
                'warehouse_id',
                'warehouse_id_to',
            ),$order_byT);
            $outputT  = $resultT['output'];
            $rResultT = $resultT['rResult'];
            

            //Xuất kho
            $selectE = array(
                'tblitems.id as product_id',
                'tblexports.date as date',
                'CONCAT(tblexports.prefix,tblexports.code) as code',
                'tblexports.reason as reason',
                'tblunits.unit as unit',
                '0 as import_quantity',
                'tblexport_items.quantity as export_quantity',
                'null as exists_quantity'
            );

            $whereE  = array(
            );
            $custom_date_select = $this->get_where_report_period('tblexports.date');
            if ($custom_date_select != '') {
                array_push($whereE, $custom_date_select);
            }
            $aColumns     = $selectE;
            $sIndexColumnE = "id";
            $sTableE       = 'tblexports';
            $joinE         = array(
                'LEFT JOIN tblexport_items ON tblexport_items.export_id = tblexports.id', 
                'LEFT JOIN tblitems ON tblitems.id = tblexport_items.product_id', 
                'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id', 
                'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit', 
            );
            $order_byE='order by product_id asc';
            $resultE  = data_tables_init($aColumns, $sIndexColumnE, $sTableE, $joinE, $whereE, array(
                'tblitems.name',
                'tblitems.short_name',
                'tblitems.code as product_code',
                'tblcategories.category',
                'tblexports.id as import_id',
                'tblexports.rel_type',
            ),$order_byE);
            $outputE  = $resultE['output'];
            $rResultE = $resultE['rResult'];
            //Merge Array
            $rResultG=array_merge($rResult,$rResultE);
            if(!empty($rResultT))
            {
                $rResultG=array_merge($rResultG,$rResultT);
            }

            $rResultG=$this->sortArrG($rResultG);
            $outputG=$output;
            $outputG['iTotalRecords']=$output['iTotalRecords']+$outputE['iTotalRecords']+$outputT['iTotalRecords'];
            $outputG['iTotalDisplayRecords']=$output['iTotalDisplayRecords']+$outputE['iTotalDisplayRecords']+$outputT['iTotalDisplayRecords'];


            $aColumnsG=array(
                '1',
                'date',
                'code',
                'reason',
                'unit',
                'import_quantity',
                'export_quantity',
                'exists_quantity'
            );

            $x       = 0;

            $product_name='';

            foreach ($rResultG as $key=> $aRow )
            {
                // if($this->input->post('length')!=-1 && $key==$this->input->post('length') && $pdf==true)
                // {
                //     break;
                // }
                if($key==0)
                {
                    
                    $sumFExistsQ=getStartInventory($aRow['product_id'],'',to_sql_date($this->input->post('report_from')));
                    $sumExistsQ=$this->sumExistsQ($aRow['product_id'],$rResultG,$key)+$sumFExistsQ;
                    $date=$aRow['product_id'];
                    $col=count($aColumnsG);
                    $row=array(
                        '#: '.$aRow['product_id'],
                        'Mã hàng: '.'<a target="_blank" href="'.admin_url("invoice_items/item/").$aRow['product_id'].'">'.$aRow['short_name'].'</a>',
                        'Sản phẩm: '.$aRow['category'],
                        'Tồn đầu kỳ: '._format_number($sumFExistsQ),
                        'Tồn cuối kỳ: '._format_number($sumExistsQ)
                    );
                    $row['DT_RowClass'] = 'alert-header bold';
                    for ($i=0 ; $i<count($aColumnsG) ; $i++ ){
                        $row[]="";
                    }
                    $outputG['aaData'][] = $row;
                }
                else
                {

                    if($date!=$aRow['product_id'])
                    {
                        
                        $sumFExistsQ=getStartInventory($aRow['product_id'],'',to_sql_date($this->input->post('report_from')));
                        $sumExistsQ=$this->sumExistsQ($aRow['product_id'],$rResultG,$key)+$sumFExistsQ;
                        $date=$aRow['product_id'];
                        $col=count($aColumnsG);
                        $row=array(
                                '#: '.$aRow['product_id'],
                                'Mã hàng: '.'<a target="_blank" href="'.admin_url("invoice_items/item/").$aRow['product_id'].'">'.$aRow['short_name'].'</a>',
                                'Sản phẩm: '.$aRow['category'],
                                'Tồn đầu kỳ: '._format_number($sumFExistsQ),
                                'Tồn cuối kỳ: '._format_number($sumExistsQ)
                            );
                        $row['DT_RowClass'] = 'alert-header bold';
                        for ($i=0 ; $i<count($aColumnsG) ; $i++ ){
                            $row[]="";
                        }
                        $row[]="";
                        $outputG['aaData'][] = $row;
                    }
                }


                    $row = array();
                    for ( $i=0 ; $i<count($aColumnsG) ; $i++ )
                    {
                        $_data=$aRow[$aColumnsG[$i]];
                        if($aColumnsG[$i]=='date' || $aColumnsG[$i]=='1')
                        {
                            $_data = _d($aRow['date']);
                        } 
                        if($aColumnsG[$i]=='import_quantity' || $aColumnsG[$i]=='export_quantity')
                        {
                            $_data = _format_number($aRow[$aColumnsG[$i]]);
                        } 
                        if($aColumnsG[$i]=='exists_quantity')
                        {
                            $sumFExistsQ+=$aRow['import_quantity']-$aRow['export_quantity'];
                            $_data = _format_number($sumFExistsQ);
                        } 

                        if($aColumnsG[$i]=='reason' && $aRow['rel_type']=='transfer' && $aRow['import_quantity']!=0)
                        {
                            $_data = ($aRow[$aColumnsG[$i]]?$aRow[$aColumnsG[$i]].'<br/> ':'').'Nhập hàng '.getWareHouse($aRow['warehouse_id_to'])->warehouse;
                        }

                        if($aColumnsG[$i]=='reason' && $aRow['rel_type']=='transfer' && $aRow['export_quantity']!=0)
                        {
                            $_data = ($aRow[$aColumnsG[$i]]?$aRow[$aColumnsG[$i]].'<br/> ':'').'Xuất hàng '.getWareHouse($aRow['warehouse_id'])->warehouse;
                        }
                       
                        if($aColumnsG[$i]=='code')
                        {
                            if($aRow['rel_type']=='internal')
                                $_data = '<a target="_blank" href="'.admin_url("imports/internal_detail/").$aRow['import_id'].'">'.$aRow['code'].'</a>';
                            elseif($aRow['rel_type']=='transfer')
                                $_data = '<a target="_blank" href="'.admin_url("imports/transfer_detail/").$aRow['import_id'].'">'.$aRow['code'].'</a>';
                            elseif($aRow['rel_type']=='return')
                                $_data = '<a target="_blank" href="'.admin_url("imports/return_detail/").$aRow['import_id'].'">'.$aRow['code'].'</a>';
                            elseif($aRow['rel_type']=='contract')
                                $_data = '<a target="_blank" href="'.admin_url("imports/contract_detail/").$aRow['import_id'].'">'.$aRow['code'].'</a>';
                            elseif($aRow['rel_type']=='adjustment')
                                $_data = '<a target="_blank" href="'.admin_url("imports/adjustment_detail/").$aRow['import_id'].'">'.$aRow['code'].'</a>';
                            elseif(strpos($aRow['rel_type'],'export')>=0)
                                $_data = '<a target="_blank" href="'.admin_url("exports/export_detail/").$aRow['import_id'].'">'.$aRow['code'].'</a>';
                        }                        
                        $row[] = $_data;
                    }

                    $outputG['aaData'][] = $row;
            }
            // $output['sums'] = $footer_data;

            if($pdf==false){
                echo json_encode($outputG);
            }
            else
            {
                return $outputG;
            }
            die();
        }
    }

    public function stock_card_report_pdf(){
        $colum=array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','AA');
        $title_colum=array(
            _l('stt'),
            _l('stock_card_date'),
            _l('view_date'),
            _l('code_noo'),
            _l('orders_explan'),
            _l('short_unit_name'),
            _l('import_quantity'),
            _l('export_quantity'),
            _l('revenue_quantity')
        );
        $data=$this->stock_card_report(true)['aaData'];
        include APPPATH . 'third_party/PHPExcel/PHPExcel.php';
        $this->load->library('PHPExcel');
        $objPHPExcel = new PHPExcel();
        $objPHPExcel->getActiveSheet()->setTitle('tiêu đề');

        $n=count($this->stock_card_report(true)['aaData'][1]);

        for($i=0;$i<$n;$i++)
        {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i])->setAutoSize(true);
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->getColumnDimension($colum[$i+1])->setAutoSize(true);
            }
        }

        $BStyle = array(
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'font'  => array(
                'bold'  => true,
                'background-color' => array('rgb' => '111112'),
                'size'  => 11,
                'name'  => 'Times New Roman'
            )
        );
        $objPHPExcel->getActiveSheet()->SetCellValue('A1','CÔNG TY TNHH DUDOFF VIỆT NAM');
        $objPHPExcel->getActiveSheet()->SetCellValue('A2','BÁO CÁO THẺ KHO')->getStyle('A2')->applyFromArray($BStyle);
        $objPHPExcel->getActiveSheet()->getStyle()->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(100);
        $objPHPExcel->getActiveSheet()->mergeCells('A1:N1');
        $objPHPExcel->getActiveSheet()->mergeCells('A2:N2');
        for($i=0;$i<$n;$i++) {
            if($i==0)
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].'3', $title_colum[$i])->getStyle($colum[$i].'3')->applyFromArray($BStyle);
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
            else
            {
                $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].'3', $title_colum[$i+1])->getStyle($colum[$i+1].'3')->applyFromArray($BStyle);
            }
        }
        $STT=1;
        foreach($data as $key=>$value)
        {
            // var_dump($colum[$i].($key+4).':'.$colum[$n].($key+4));die;
            if(isset($value['DT_RowClass']))
            {
                $objPHPExcel->getActiveSheet()->getStyle($colum[0].($key+4).':'.$colum[$n].($key+4))->applyFromArray(
                    array(
                        'fill' => array(
                            'type' => PHPExcel_Style_Fill::FILL_SOLID,
                            'color' => array('rgb' => 'F28A8C')
                        )
                    )
                );
                $objPHPExcel->getActiveSheet()->setCellValue($colum[0].($key+4),($STT));
                $STT++;
            }
            for($i=0;$i<$n;$i++)
            {
                
                if($i==0){
                    // $objPHPExcel->getActiveSheet()->setCellValue($colum[$i].($key+4),($key+1));
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),html_entity_decode(strip_tags($value[$i])));
                }
                else
                {
                    $objPHPExcel->getActiveSheet()->setCellValue($colum[$i+1].($key+4),html_entity_decode(strip_tags($value[$i])));
                    if($i==5)
                    {
                        $style = array(
                            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                            )
                        );
                        $objPHPExcel->getActiveSheet()->getStyle($colum[$i].($key+4))->applyFromArray($style);
                        // $sheet->getStyle("A1:B1")->applyFromArray($style);
                    }
                    if($i==6 || $i==7 || $i==8) 
                    {
                        $style = array(
                            'alignment' => array(
                                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_RIGHT,
                            )
                        );
                        $objPHPExcel->getActiveSheet()->getStyle($colum[$i].($key+4))->applyFromArray($style);
                        // $sheet->getStyle("A1:B1")->applyFromArray($style);
                    }
                }
            }
            
        } 


        $objPHPExcel->getActiveSheet()->freezePane('A3');
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel,'Excel5');
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Bao_Cao_The_Kho.xls"');
        header('Cache-Control: max-age=0');

        $objWriter->save('php://output');
        exit();

    }

    public function sumExistsQ($product_id,$items=array(),$index=0)
    {
        $total=0;
        if(is_numeric($product_id) && isset($items))
        {
            for ($i=$index; $i < count($items) ; $i++) { 
                $row=(object)$items[$i];
                if($row->product_id==$product_id)
                {
                    $total+=$row->import_quantity-$row->export_quantity;
                }
                if($row->product_id!=$product_id) break;
            }
        }
        return $total;
    }

    public function sumQuantity($product_id,$items=array(),$field='import_quantity',$index=0)
    {
        $total=0;
        if(is_numeric($product_id) && isset($items) && isset($field))
        {
            for ($i=$index; $i < count($items) ; $i++) { 
                $row=(object)$items[$i];
                if($row->product_id==$product_id)
                {
                    $total+=$row->{$field};
                }
                if($row->product_id!=$product_id) break;
            }
        }
        return $total;
    }

    public function sumMoney($product_id,$items=array(),$type='import',$field='sub_total',$index=0)
    {
        $total=0;
        if(is_numeric($product_id) && isset($items) && isset($field))
        {
            $arrI=array('internal','return','adjustment','transfer','contract');
            $arrE=array('export_sale_order','export_warehouse_transfer');
            for ($i=$index; $i < count($items) ; $i++) { 
                $row=(object)$items[$i];
                if($row->product_id==$product_id)
                {
                    if($type=='import' && in_array($row->rel_type, $arrI))
                    {
                        $total+=$row->{$field};
                    }
                    elseif($type=='export' && in_array($row->rel_type, $arrE))
                    {
                        $total+=$row->{$field};
                    }
                }
                if($row->product_id!=$product_id) break;
            }
        }
        return $total;
    }

    public function inventory_report($pdf=false)
    {
        $this->load->model('warehouse_model');
        $warehouses = $this->warehouse_model->getWarehouses('', true);

        $aColumns     = array(
            'tblitems.code',
            'tblitems.name',
            );
        foreach($warehouses as $key=> $warehouse) {
            $aColumns[] = '(select tblwarehouses_products.product_quantity as F_'.$warehouse['warehouseid'].'_'.$warehouse['warehouse_can_export'].' from tblwarehouses_products where tblwarehouses_products.product_id=tblitems.id and tblwarehouses_products.warehouse_id=' .  $warehouse['warehouseid'] . ') as T'.$warehouse['warehouseid'].$warehouse['warehouse_can_export'];
        }
        // echo "<pre>";
        // var_dump($this->input->post('product_category'));die();
        $sIndexColumn = "id";
        $sTable       = 'tblitems';
        $where = array(

        );
        if($this->input->post('product_category'))
        {
            array_push($where, 'AND tblcategories.id='.$this->input->post('product_category'));
        }
        if($this->input->post('warehouse_invenroty') && count($this->input->post('warehouse_invenroty'))>0)
        {
            array_push($where, 'AND tblwarehouses_products.warehouse_id IN (' . implode(', ', $this->input->post('warehouse_invenroty')) . ')');
        }
        $join             = array(
            'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id', 
            // 'LEFT JOIN tblwarehouses_products ON tblwarehouses_products.product_id = tblitems.id', 
            );
        $additionalSelect = array(
            'tblitems.id',
            'short_name'
            );
        $order_by='ORDER BY tblitems.id ASC';
        $group_by='GROUP BY tblitems.id';
        $result           = data_tables_init($aColumns, $sIndexColumn, $sTable ,$join, $where, $additionalSelect);
        $output           = $result['output'];
        $rResult          = $result['rResult'];
        $time_inventory=to_sql_date($this->input->post('time_inventory'));
        foreach ($rResult as $aRow) {

            $row = array();
            $sum_row = 0;
            for ($i = 0; $i < count($aColumns); $i++) {
                $_data = $aRow[$aColumns[$i]];
                if($aColumns[$i]=='tblitems.code')
                        {
                            $_data = '<a target="_blank" href="'.admin_url('invoice_items/item/'.$aRow['id']).'">'.$aRow['short_name'].'</a>';
                        }
                if($i>1) {
                    // $_data=$this->getInventoryQuantity($aRow['id'],$time_inventory,$warehouses[$i-2]['warehouseid']);
                    // var_dump($_data);die;
                    $_data = $aRow["T".$warehouses[$i-2]['warehouseid'].$warehouses[$i-2]['warehouse_can_export']];
                    if($_data == "")
                        $_data = 0;
                           
                    $flag = $warehouses[$i-2]['warehouse_can_export'];
                    if($flag == 1 && $_data != '')
                    {
                        $sum_row += $_data;
                        $_data = _format_number($_data);
                    }

                }
                
                $row[] = $_data;
            }
            $row[] = $sum_row;
            $options = '';
            
           $row[] = $options;

           $output['aaData'][] = $row;
        }
        echo json_encode($output);

    }

    public function detail_goods_book_report($pdf=false)
    {
        if ($this->input->is_ajax_request()||$pdf==true)
        {
            // Kho
            $select = array(
                'tblwarehouses.order as orderT',
                'code',
                'warehouse',
                'address',
                'map',
                'phone',
                'null as i_1',
                'null as i_2',
                'null as e_1',
                'null as e_2',
                'null as r_1',
                'null as r_2'
            );
            $where  = array(
            );

            $aColumns     = $select;
            $sIndexColumn = "warehouseid";
            $sTable       = 'tblwarehouses';
            $join         = array(
                
            );
            $order_by='order by tblwarehouses.order asc';
            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('warehouseid'),$order_by);
            $output  = $result['output'];
            $rResult = $result['rResult'];

            $iTotalRecords=0;
            $iTotalDisplayRecords=0;
            foreach ($rResult as $key => $warehouse) {
                
                $products=$this->getDataWHReportIETHH($warehouse['warehouseid']);
                $rResult[$key]['products']=$products;
                $iTotalRecords+=count($products);
                $iTotalDisplayRecords+=count($products);
            }
            $output['iTotalRecords']  = $iTotalRecords;
            $output['iTotalDisplayRecords']  = $iTotalDisplayRecords;

            $aColumnsG=array(
                    'product_name',
                    'date',
                    'code_no',
                    'explain',
                    'unit_name',
                    'unit_cost',
                    'i_1',
                    'i_2',
                    'e_1',
                    'e_2',
                    'r_1',
                    'r_2'
                );

            $footer_data = array(
                'SPSN' => 0,
                'SPSC' => 0,
                'ST' => 0
            );
            // for ($i = 0; $i < count($aColumns); $i++) {
            //         if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
            //             $_data = $aRow[strafter($aColumns[$i], 'as ')];
            //         } else {
            //             $_data = $aRow[$aColumns[$i]];
            //         }

            //         $row[] = $_data;
            //     }
            foreach ($rResult as $key=> $aRow) 
            {
                $GtotalQuantity=0;
                $GtotalPrice=0;
                //Head Kho
                $Hrow=array(
                    '#: '.($key+1),
                    'Mã kho: '.$aRow['code'],
                    'Tên kho : '.$aRow['warehouse']
                );
                $Hrow['DT_RowClass'] = 'alert-danger bold';
                for ($i=count($Hrow)-1 ; $i<=count($aRow) ; $i++ ){
                    $Hrow[]="";
                }
                $output['aaData'][] = $Hrow;
                // end Head kho
                if($aRow['products'])
                {   
                    foreach ($aRow['products'] as $keyI => $item) {
                        if($keyI==0 || $item['product_id']!=$aRow['products'][$keyI-1]['product_id'])
                        {
                            // Head Product
                            $FtotalQuantity=0;
                            $FtotalPrice=0;
                            $Prow=array(
                                '&#x25BA;'.'Mã hàng: '.$item['short_name'].'-'.$item['product_id'],
                                'Tên hàng : '.$item['category'],
                                '','',
                                format_money($item['product_price']),'','','','',''
                            );
                            $Prow['DT_RowClass'] = 'alert-warning bold';
                            
                            $FtotalPrice=$FtotalQuantity*$item['product_price'];
                            $Prow[]=_format_number($FtotalQuantity);
                            $Prow[]=_format_number($FtotalPrice);
                            $output['aaData'][] = $Prow;
                            // End Head Product

                           
                        }
                    

                        //+ Main Product 
                        $Mrow=array();
                        foreach ($aColumnsG as $keyG => $valueG) {
                            $arrI=array('internal','return','adjustment','transfer','contract');
                            $arrE=array('export_sale_order','export_warehouse_transfer');
                            $_data=$item[$valueG];
                            if($valueG=='product_name')
                            {
                                $_data=$item['category'];
                            }
                            if($valueG=='date')
                            {
                                $_data=_d($item['date']);
                            }
                            if($valueG=='code_no')
                            {
                                $_data=$item['code_no'];
                                if($item['rel_type']=='internal')
                                    $_data = '<a target="_blank" href="'.admin_url("imports/internal_detail/").$item['import_id'].'">'.$item['code_no'].'</a>';
                                elseif($item['rel_type']=='transfer')
                                    $_data = '<a target="_blank" href="'.admin_url("imports/transfer_detail/").$item['import_id'].'">'.$item['code_no'].'</a>';
                                elseif($item['rel_type']=='return')
                                    $_data = '<a target="_blank" href="'.admin_url("imports/return_detail/").$item['import_id'].'">'.$item['code_no'].'</a>';
                                elseif($item['rel_type']=='contract')
                                    $_data = '<a target="_blank" href="'.admin_url("imports/contract_detail/").$item['import_id'].'">'.$item['code_no'].'</a>';
                                elseif($item['rel_type']=='adjustment')
                                    $_data = '<a target="_blank" href="'.admin_url("imports/adjustment_detail/").$item['import_id'].'">'.$item['code_no'].'</a>';
                                elseif(strpos($item['rel_type'],'export')>=0)
                                    $_data = '<a target="_blank" href="'.admin_url("exports/export_detail/").$item['import_id'].'">'.$item['code_no'].'</a>';
                            }
                            if($valueG=='explain')
                            {
                                $_data=$item['reason'];
                            }
                            // '<div style="text-align: right;"'></div>
                            if($valueG=='unit_cost')
                            {
                                $_data=format_money($item['unit_cost']);
                            }
                           
                            if($valueG=='i_1' && in_array($item['rel_type'], $arrI))
                            {
                                $FtotalQuantity+=$item['quantity_net'];
                                $_data=_format_number($item['quantity_net']);
                            }
                            
                            if($valueG=='i_2' && in_array($item['rel_type'], $arrI))
                            {
                                $FtotalPrice+=$item['sub_total'];
                                $_data=format_money($item['sub_total']);
                            }
                            
                            if($valueG=='e_1' && in_array($item['rel_type'], $arrE))
                            {
                                $FtotalQuantity-=$item['quantity'];
                                $_data=_format_number($item['quantity']);
                            }
                            
                            if($valueG=='e_2' && in_array($item['rel_type'], $arrE))
                            {
                                $FtotalPrice-=$item['sub_total'];
                                $_data=format_money($item['sub_total']);
                            }
                            if($valueG=='r_1')
                            {
                                $_data=_format_number($FtotalQuantity);
                            }
                            if($valueG=='r_2')
                            {
                                $_data=format_money($FtotalPrice);
                            }

                            $Mrow[]=$_data;
                        }
                        $output['aaData'][] = $Mrow;
                        //_ Main Product 

                        if($item['product_id']!=$aRow['products'][$keyI+1]['product_id'])
                        {
                            // Foot Product
                            $Frow=array(
                                '&#x25BA;&para;'.'Cộng theo mã hàng',
                                '','','','','','','','',''
                            );
                            $Frow[]=_format_number($FtotalQuantity);
                            $Frow[]=_format_number($FtotalPrice);
                            $GtotalQuantity +=$FtotalQuantity;
                            $GtotalPrice +=$FtotalPrice;
                            $Frow['DT_RowClass'] = 'alert-warning bold';
                            $output['aaData'][] = $Frow;
                            // End Foot Product 
                        }
                        
                    }
                }
                //Foot Kho
                $Frow=array(
                    '&para;'.'Cộng theo mã kho',
                    '','','','','','','','',''
                );
                $Frow[]=_format_number($GtotalQuantity);
                $Frow[]=_format_number($GtotalPrice);
                $Frow['DT_RowClass'] = 'alert-danger bold';
                $output['aaData'][] = $Frow;
                // end Foot Kho
            }
            $output['sums'] = $footer_data;
            if($pdf==false)
            {
                echo json_encode($output);
            }
            else
            {
                return $output;
            }
            die();
        }
    }

    public function getDataWHReportIETHH($warehouse_id=NULL,$filters=array())
    {
        $arrResult=array();
        if(is_numeric($warehouse_id))
        {
            // Nhap
            $select = array(
                'tblimports.date as date',
                'CONCAT(tblimports.prefix,tblimports.code) as code_no',
                'tblimports.reason as reason',
                'tblitems.code as code',
                'tblitems.name as product_name',
                'tblitems.price as product_price',
                'tblunits.unit as unit_name',
                'tblitems.short_name as short_name',
                'tblcategories.category as category',
                'tblimports.rel_type as rel_type',
                'tblimport_items.quantity_net as quantity_net'
            );
            $where  = array(
            );

            if($warehouse_id)
            {
                array_push($where, 'AND (tblimport_items.warehouse_id='.$warehouse_id.' OR tblimport_items.warehouse_id_to='.$warehouse_id.')');
            }
            $custom_date_select = $this->get_where_report_period('tblimports.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblimports';
            $join         = array(
                'LEFT JOIN tblimport_items ON tblimport_items.import_id = tblimports.id', 
                'LEFT JOIN tblitems ON tblitems.id = tblimport_items.product_id', 
                'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id', 
                'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit', 
            );
            $order_by='order by product_id asc';
            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, prefixed_table_fields_array('tblimport_items'),$order_by);
            $output  = $result['output'];
            $imports = $result['rResult'];
            // Chuyen
            
            $select = array(
                'tblimports.date as date',
                'CONCAT(tblimports.prefix,tblimports.code) as code_no',
                'tblimports.reason as reason',
                'tblitems.code as code',
                'tblitems.name as product_name',
                'tblitems.price as product_price',
                'tblunits.unit as unit_name',
                'tblitems.short_name as short_name',
                'tblcategories.category as category',
                'tblimports.rel_type as rel_type',
                'tblimport_items.quantity_net as quantity'
            );
            $where  = array(
                'AND tblimports.rel_type="' . 'transfer"'
            );
            if($warehouse_id)
            {
                array_push($where, 'AND (tblimport_items.warehouse_id='.$warehouse_id.' OR tblimport_items.warehouse_id_to='.$warehouse_id.')');
            }
            $custom_date_select = $this->get_where_report_period('tblimports.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblimports';
            $join         = array(
                'LEFT JOIN tblimport_items ON tblimport_items.import_id = tblimports.id', 
                'LEFT JOIN tblitems ON tblitems.id = tblimport_items.product_id', 
                'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id', 
                'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit', 
            );
            $order_by='order by product_id asc';
            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, prefixed_table_fields_array('tblimport_items'),$order_by);
            $output  = $result['output'];
            $transfers = $result['rResult'];
            // Xuat
            
            $selectE = array(
                'tblexports.date as date',
                'CONCAT(tblexports.prefix,tblexports.code) as code_no',
                'tblexports.reason as reason',
                'tblitems.code as code',
                'tblitems.name as product_name',
                'tblitems.price as product_price',
                'tblunits.unit as unit_name',
                'tblitems.short_name as short_name',
                'tblcategories.category as category',
                'tblexports.rel_type as rel_type',
                'tblexport_items.quantity as export_quantity'
            );

            $whereE  = array(
            );
            if($warehouse_id)
            {
                array_push($whereE, 'AND tblexport_items.warehouse_id='.$warehouse_id);
            }
            $custom_date_select = $this->get_where_report_period('tblexports.date');
            if ($custom_date_select != '') {
                array_push($whereE, $custom_date_select);
            }
            $aColumns     = $selectE;
            $sIndexColumnE = "id";
            $sTableE       = 'tblexports';
            $joinE         = array(
                'LEFT JOIN tblexport_items ON tblexport_items.export_id = tblexports.id', 
                'LEFT JOIN tblitems ON tblitems.id = tblexport_items.product_id', 
                'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id', 
                'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit', 
            );
            $order_byE='order by product_id asc';
            $resultE  = data_tables_init($aColumns, $sIndexColumnE, $sTableE, $joinE, $whereE, prefixed_table_fields_array('tblexport_items'),$order_byE);
            $outputE  = $resultE['output'];
            $exports = $resultE['rResult'];
            // Tron mang
            // Chua tinh chuyen
            $arrResult=array_merge($imports,$exports);
            $arrResult=$this->sortArrG($arrResult,'product_id');
        }
        return $arrResult;
    }

    

    public function getDataWHReportIETHHGroup($warehouse_id=NULL,$filters=array())
    {
        $arrResult=array();
        if(is_numeric($warehouse_id))
        {
            // Nhap
            $select = array(
                'tblimports.date as date',
                'CONCAT(tblimports.prefix,tblimports.code) as code_no',
                'tblimports.reason as reason',
                'tblitems.code as code',
                'tblitems.name as product_name',
                'tblitems.price as product_price',
                'tblunits.unit as unit_name',
                'tblitems.short_name as short_name',
                'tblcategories.category as category',
                'tblimports.rel_type as rel_type',
                '(tblimport_items.quantity_net) as import_quantity'
            );
            $where  = array(
            );

            if($warehouse_id)
            {
                array_push($where, 'AND (tblimport_items.warehouse_id='.$warehouse_id.' OR tblimport_items.warehouse_id_to='.$warehouse_id.')');
            }
            $custom_date_select = $this->get_where_report_period('tblimports.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblimports';
            $join         = array(
                'LEFT JOIN tblimport_items ON tblimport_items.import_id = tblimports.id', 
                'LEFT JOIN tblitems ON tblitems.id = tblimport_items.product_id', 
                'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id', 
                'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit', 
            );
            $order_by='order by product_id asc';
            // $group_by='group by product_id,unit_cost';
            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, prefixed_table_fields_array('tblimport_items'),$order_by,$group_by);
            $output  = $result['output'];
            $imports = $result['rResult'];
            // var_dump($imports);die;
            // Chuyen
            
            $select = array(
                'tblimports.date as date',
                'CONCAT(tblimports.prefix,tblimports.code) as code_no',
                'tblimports.reason as reason',
                'tblitems.code as code',
                'tblitems.name as product_name',
                'tblitems.price as product_price',
                'tblunits.unit as unit_name',
                'tblitems.short_name as short_name',
                'tblcategories.category as category',
                'tblimports.rel_type as rel_type',
                '(tblimport_items.quantity_net) as export_quantity'
            );
            $where  = array(
                'AND tblimports.rel_type="' . 'transfer"'
            );
            if($warehouse_id)
            {
                array_push($where, 'AND (tblimport_items.warehouse_id='.$warehouse_id.' OR tblimport_items.warehouse_id_to='.$warehouse_id.')');
            }
            $custom_date_select = $this->get_where_report_period('tblimports.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblimports';
            $join         = array(
                'LEFT JOIN tblimport_items ON tblimport_items.import_id = tblimports.id', 
                'LEFT JOIN tblitems ON tblitems.id = tblimport_items.product_id', 
                'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id', 
                'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit', 
            );
            $order_by='order by product_id asc';
            // $group_by='group by product_id,unit_cost';
            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, prefixed_table_fields_array('tblimport_items'),$order_by,$group_by);
            $output  = $result['output'];
            $transfers = $result['rResult'];
            // Xuat
            
            $selectE = array(
                'tblexports.date as date',
                'CONCAT(tblexports.prefix,tblexports.code) as code_no',
                'tblexports.reason as reason',
                'tblitems.code as code',
                'tblitems.name as product_name',
                'tblitems.price as product_price',
                'tblunits.unit as unit_name',
                'tblitems.short_name as short_name',
                'tblcategories.category as category',
                'tblexports.rel_type as rel_type',
                '(tblexport_items.quantity) as export_quantity'
            );

            $whereE  = array(
            );
            if($warehouse_id)
            {
                array_push($whereE, 'AND tblexport_items.warehouse_id='.$warehouse_id);
            }
            $custom_date_select = $this->get_where_report_period('tblexports.date');
            if ($custom_date_select != '') {
                array_push($whereE, $custom_date_select);
            }
            $aColumns     = $selectE;
            $sIndexColumnE = "id";
            $sTableE       = 'tblexports';
            $joinE         = array(
                'LEFT JOIN tblexport_items ON tblexport_items.export_id = tblexports.id', 
                'LEFT JOIN tblitems ON tblitems.id = tblexport_items.product_id', 
                'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id', 
                'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit', 
            );
            $order_by='order by product_id asc';
            // $group_by='group by product_id,unit_cost';
            $resultE  = data_tables_init($aColumns, $sIndexColumnE, $sTableE, $joinE, $whereE, prefixed_table_fields_array('tblexport_items'),$order_byE);
            $outputE  = $resultE['output'];
            $exports = $resultE['rResult'];
            
            // $arrResult=$exports;
            // Tron mang
            // Chua tinh chuyen
            $arrResult=array_merge($imports,$exports);
            $arrResult=$this->sortArrG($arrResult,'product_id');
        }
        return $arrResult;
    }


    public function getDataWHReportIETHHG($warehouse_id=NULL,$filters=array())
    {
        $arrResult=array();
        if(is_numeric($warehouse_id))
        {
            //Nhap kho
            $select = array(
                'tblitems.id as product_id',
                'tblimports.date as date',
                'CONCAT(tblimports.prefix,tblimports.code) as code',
                'tblimports.reason as reason',
                'tblunits.unit as unit',
                'tblimport_items.quantity_net as import_quantity',
                '0  as export_quantity',
                'null as exists_quantity',
                'tblimport_items.sub_total as sub_total'
            );
            $where  = array(
            );
            if($warehouse_id)
            {
                array_push($where, 'AND (tblimport_items.warehouse_id='.$warehouse_id.' OR tblimport_items.warehouse_id_to='.$warehouse_id.')');
            }
            $custom_date_select = $this->get_where_report_period('tblimports.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblimports';
            $join         = array(
                'LEFT JOIN tblimport_items ON tblimport_items.import_id = tblimports.id', 
                'LEFT JOIN tblitems ON tblitems.id = tblimport_items.product_id', 
                'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id', 
                'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit', 
            );
            $order_by='order by product_id asc';
            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'tblitems.name',
                'tblitems.short_name',
                'tblitems.code as product_code',
                'tblcategories.category',
                'tblimports.id as import_id',
                'tblimports.rel_type',
                'warehouse_id',
                'warehouse_id_to',
            ),$order_by);
            $output  = $result['output'];
            $rResult = $result['rResult'];


            //Chuyen kho
            $selectT = array(
                'tblitems.id as product_id',
                'tblimports.date as date',
                'CONCAT(tblimports.prefix,tblimports.code) as code',
                'tblimports.reason as reason',
                'tblunits.unit as unit',
                '0 as import_quantity',
                'tblimport_items.quantity_net as export_quantity',
                'null as exists_quantity',
                'tblimport_items.sub_total as sub_total'
            );
            $whereT  = array(
                'AND tblimports.rel_type="' . 'transfer"'
            );
            if($warehouse_id)
            {
                array_push($whereT, 'AND (tblimport_items.warehouse_id='.$warehouse_id.' OR tblimport_items.warehouse_id_to='.$warehouse_id.')');
            }
            $custom_date_select = $this->get_where_report_period('tblimports.date');
            if ($custom_date_select != '') {
                array_push($whereT, $custom_date_select);
            }
            $aColumnsT     = $selectT;
            $sIndexColumnT = "id";
            $sTableT       = 'tblimports';
            $joinT         = array(
                'LEFT JOIN tblimport_items ON tblimport_items.import_id = tblimports.id', 
                'LEFT JOIN tblitems ON tblitems.id = tblimport_items.product_id', 
                'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id', 
                'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit', 
            );
            $order_byT='order by product_id asc';
            $resultT  = data_tables_init($aColumnsT, $sIndexColumnT, $sTableT, $joinT, $whereT, array(
                'tblitems.name',
                'tblitems.short_name',
                'tblitems.code as product_code',
                'tblcategories.category',
                'tblimports.id as import_id',
                'tblimports.rel_type',
                'warehouse_id',
                'warehouse_id_to',
            ),$order_byT);
            $outputT  = $resultT['output'];
            $rResultT = $resultT['rResult'];
            

            //Xuất kho
            $selectE = array(
                'tblitems.id as product_id',
                'tblexports.date as date',
                'CONCAT(tblexports.prefix,tblexports.code) as code',
                'tblexports.reason as reason',
                'tblunits.unit as unit',
                '0 as import_quantity',
                'tblexport_items.quantity as export_quantity',
                'null as exists_quantity',
                'tblexport_items.sub_total as sub_total'
            );

            $whereE  = array(
            );
            if($warehouse_id)
            {
                array_push($whereE, 'AND tblexport_items.warehouse_id='.$warehouse_id);
            }
            $custom_date_select = $this->get_where_report_period('tblexports.date');
            if ($custom_date_select != '') {
                array_push($whereE, $custom_date_select);
            }
            $aColumns     = $selectE;
            $sIndexColumnE = "id";
            $sTableE       = 'tblexports';
            $joinE         = array(
                'LEFT JOIN tblexport_items ON tblexport_items.export_id = tblexports.id', 
                'LEFT JOIN tblitems ON tblitems.id = tblexport_items.product_id', 
                'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id', 
                'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit', 
            );
            $order_byE='order by product_id asc';
            $resultE  = data_tables_init($aColumns, $sIndexColumnE, $sTableE, $joinE, $whereE, array(
                'tblitems.name',
                'tblitems.short_name',
                'tblitems.code as product_code',
                'tblcategories.category',
                'tblexports.id as import_id',
                'tblexports.rel_type',
            ),$order_byE);
            $outputE  = $resultE['output'];
            $rResultE = $resultE['rResult'];
            //Merge Array
            $arrResult=array_merge($rResult,$rResultE);
            if(!empty($rResultT))
            {
                $arrResult=array_merge($arrResult,$rResultT);
            }

            $arrResult=$this->sortArrG($arrResult);
        }
        return $arrResult;
    }

    public function warehouse_sumary_report($pdf=false)
    {
        if ($this->input->is_ajax_request()||$pdf==true)
        {
            // Kho
            $select = array(
                'tblwarehouses.order as orderT',
                'warehouse',
                'address',
                'null as f_1',
                'null as f_2',
                'null as i_1',
                'null as i_2',
                'null as e_1',
                'null as e_2',
                'null as r_1',
                'null as r_2'
            );
            $where  = array(
            );

            $aColumns     = $select;
            $sIndexColumn = "warehouseid";
            $sTable       = 'tblwarehouses';
            $join         = array(
                
            );
            $order_by='order by tblwarehouses.order asc';
            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('warehouseid','code'),$order_by);
            $output  = $result['output'];
            $rResult = $result['rResult'];

            $iTotalRecords=0;
            $iTotalDisplayRecords=0;
            foreach ($rResult as $key => $warehouse) {
                
                $products=$this->getDataWHReportIETHHG($warehouse['warehouseid']);
                $rResult[$key]['products']=$products;
            }
            $output['iTotalRecords']  = $iTotalRecords;
            $output['iTotalDisplayRecords']  = $iTotalDisplayRecords;

            $aColumnsG=array(
                    'product_code',
                    'product_name',
                    'unit_name',
                    'f_1',
                    'f_2',
                    'i_1',
                    'i_2',
                    'e_1',
                    'e_2',
                    'r_1',
                    'r_2'
                );

            $footer_data = array(
                'SPSN' => 0,
                'SPSC' => 0,
                'ST' => 0
            );
            $iTotalRecords=0;
            foreach ($rResult as $key=> $aRow) 
            {
                $FtotalQuantity=0;
                $FtotalPrice=0;
                $ItotalQuantity=0;
                $ItotalPrice=0;
                $EtotalQuantity=0;
                $EtotalPrice=0;
                $GtotalQuantity=0;
                $GtotalPrice=0;
                //Head Kho
                $Hrow=array(
                    '#: '.($key+1),
                    'Mã kho: '.$aRow['code'],
                    'Tên kho : '.$aRow['warehouse']
                );
                $Hrow['DT_RowClass'] = 'alert-danger bold';
                for ($i=count($Hrow)-1 ; $i<=count($aRow) ; $i++ ){
                    $Hrow[]="";
                }
                $output['aaData'][] = $Hrow;
                // end Head kho
                if($aRow['products'])
                {   
                    
                    foreach ($aRow['products'] as $keyI => $item) {
                        // Main Row
                        if($keyI==0 || $item['product_id']!=$aRow['products'][$keyI-1]['product_id'])
                        {
                            $iTotalRecords++;
                            $totalQuantity=0;
                            $totalPrice=0;
                            $Mrow=array();
                            foreach ($aColumnsG as $keyG => $valueG) {

                                $arrI=array('internal','return','adjustment','transfer','contract');
                                $arrE=array('export_sale_order','export_warehouse_transfer');
                                $_data=$item[$valueG];
                                // var_dump($aColumnsG);die;
                                if($valueG=='product_name')
                                {
                                    $_data=$item['category'];
                                }
                                if($valueG=='product_code')
                                {
                                    $_data=$item['short_name'];
                                }
                                if($valueG=='unit_name')
                                {
                                    $_data=$item['unit_name'];
                                }
                                $sumFExistsQ=getStartInventory($item['product_id'],'',to_sql_date($this->input->post('report_from')));
                                if($valueG=='f_1')
                                {
                                    $totalQuantity=$sumFExistsQ;
                                    $FItotalQuantity+=$sumFExistsQ;
                                    $_data=_format_number($sumFExistsQ);
                                }
                                // '<div style="text-align: right;"'></div>
                                if($valueG=='f_2')
                                {
                                    $amount=$sumFExistsQ*$item['product_price'];
                                    $totalPrice=$amount;
                                    $FtotalPrice+=$amount;
                                    $_data=format_money($amount);
                                }
                               
                                if($valueG=='i_1')
                                {
                                    $currrentQ=$this->sumQuantity($item['product_id'],$aRow['products'],'import_quantity',$keyI);
                                    $ItotalQuantity+=$currrentQ;
                                    $totalQuantity+=$currrentQ;
                                    $_data=_format_number($currrentQ);
                                }
                                
                                if($valueG=='i_2')
                                {
                                    $currrentF=$this->sumMoney($item['product_id'],$aRow['products'],'import','sub_total',$keyI);
                                    $totalPrice+=$currrentF;
                                    $ItotalPrice+=$currrentF;
                                    $_data=format_money($currrentF);
                                }
                                
                                if($valueG=='e_1')
                                {
                                    $currrentQ=$this->sumQuantity($item['product_id'],$aRow['products'],'export_quantity',$keyI);
                                    $totalQuantity-=$currrentQ;
                                    $EtotalQuantity+=$currrentQ;
                                    $_data=_format_number($currrentQ);
                                }
                                
                                if($valueG=='e_2')
                                {
                                    $currrentF=$this->sumMoney($item['product_id'],$aRow['products'],'export','sub_total',$keyI);
                                    $totalPrice-=$currrentF;
                                    $EtotalPrice+=$currrentF;
                                    $_data=format_money($currrentF);
                                }
                                if($valueG=='r_1')
                                {
                                    $GtotalQuantity+=$totalQuantity;
                                    $_data=_format_number($totalQuantity);
                                }
                                if($valueG=='r_2')
                                {
                                    $GtotalPrice+=$totalPrice;
                                    $_data=format_money($totalPrice);
                                }

                                $Mrow[]=$_data;
                            }
                            $output['aaData'][] = $Mrow;
                        }
                        // Main Row

                    }
                }
                //Foot Kho
                $Frow=array(
                    '&para;'.'Cộng theo mã kho',
                    '',''
                );
                $Frow[]=_format_number($FtotalQuantity);
                $Frow[]=format_money($FtotalPrice);
                $Frow[]=_format_number($ItotalQuantity);
                $Frow[]=format_money($ItotalPrice);
                $Frow[]=_format_number($EtotalQuantity);
                $Frow[]=format_money($EtotalPrice);
                $Frow[]=_format_number($GtotalQuantity);
                $Frow[]=format_money($GtotalPrice);
                $Frow['DT_RowClass'] = 'alert-danger bold';
                $output['aaData'][] = $Frow;
                // end Foot Kho
            }
            $output['iTotalRecords']  = $iTotalRecords;
            $output['iTotalDisplayRecords']  = $iTotalRecords;
            $output['sums'] = $footer_data;
            if($pdf==false)
            {
                echo json_encode($output);
            }
            else
            {
                return $output;
            }
            die();
        }
    }

                // 'LEFT JOIN tblitems ON tblitems.id = tblwarehouses_products.product_id',
                // 'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id',
                // 'LEFT JOIN tblwarehouses ON tblwarehouses.warehouseid = tblwarehouses_products.warehouse_id'
    public function warehouse_max_min_inventory_report($pdf=false)
    {
        if ($this->input->is_ajax_request()||$pdf==true)
        {
            $select = array(
                'tblwarehouses.order as orderT',
                'tblitems.name as product_name',
                'tblitems.code as product_code',
                'tblunits.unit as unit_name',
                'null as status',
                'tblwarehouses_products.product_quantity as product_quantity',
                'minimum_quantity as product_min',
                'maximum_quantity as product_max'
            );

            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblwarehouses_products';
            $where        = array();
            $join        = array(
                'LEFT JOIN tblitems ON tblitems.id = tblwarehouses_products.product_id',
                'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit',
                'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id',
                'LEFT JOIN tblwarehouses ON tblwarehouses.warehouseid = tblwarehouses_products.warehouse_id'
            );
            $order_by='ORDER BY tblwarehouses.order ASC,tblwarehouses_products.product_id ASC';
            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('product_id','warehouse_id,warehouse,tblwarehouses.code as warehouse_code,tblitems.short_name'),$order_by);
            $output  = $result['output'];
            $rResult = $result['rResult'];
            $x       = 0;
            $aColumnsG =array(
                    'product_code',
                    'product_name',
                    'unit_name',
                    'status',
                    'product_quantity',
                    'product_min',
                    'product_max',
            );

            foreach ($rResult as $key => $aRow) 
            {
                if($key==0 || $aRow['warehouse_id']!=$rResult[$key-1]['warehouse_id'])
                {
                    $row = array(
                        '&#x25BA;'.'Mã kho: '.$aRow['warehouse_code'],
                        'Tên kho: '.$aRow['warehouse'],
                        '','','','','',
                    );
                    $row['DT_RowClass']='alert-warning bold';
                    $output['aaData'][] = $row;
                }
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumnsG[$i], 'as') !== false && !isset($aRow[$aColumnsG[$i]])) {
                        $_data = $aRow[strafter($aColumnsG[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumnsG[$i]];
                    }
                    if($aColumnsG[$i]=='product_code')
                    {
                        $_data='<a target="_blank" href="'.admin_url('invoice_items/item/'.$aRow['product_id']).'">'.$aRow['short_name'].'</a>';
                    }
                    if($aColumnsG[$i]=='product_name')
                    {
                        $_data=$aRow['product_name'];
                    }
                    if($aColumnsG[$i]=='status')
                    {
                        if($aRow['product_quantity']<=$aRow['product_min'])
                        {
                            // $row['DT_RowClass']='alert alert-danger';
                            $_data='<span class="inline-block label label-danger">Sản phẩm thấp hơn mức tối thiểu</span></br><span class="inline-block label label-danger mtop5">Cần nhập thêm hàng</span>';
                        }
                        if($aRow['product_quantity']>$aRow['product_max'])
                        {
                            // $row['DT_RowClass']='alert alert-warning';
                            $_data='<span class="inline-block label label-warning">Sản phẩm cao hơn mức tối đa</span>';
                        }
                    }
                    if($aColumnsG[$i]=='product_quantity' || $aColumnsG[$i]=='product_min' || $aColumnsG[$i]=='product_max')
                    {
                        $_data=_format_number($aRow[$aColumnsG[$i]]);
                    }
                    
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }
        }
        if($pdf==false)
        {
            echo json_encode($output);
        }
        else
        {
            return $output;
        }
        die();
    }

    public function sum_quantity_import($product_id,$date="",$warehouse_id="")
    {
        $this->db->select('sum(quantity) as sum_quantity');
        if($date!="")
        {
            $this->db->where('date <="'.$date.'"');
        }
        $this->db->where('status',2);
        $this->db->join('tblimport_items','tblimport_items.import_id=tblimports.id')->where('product_id',$product_id);
        $this->db->where('tblimport_items.warehouse_id_to is null');
        if(is_numeric($warehouse_id))
        {
            $this->db->where('tblimport_items.warehouse_id',$warehouse_id);
        }
        return $this->db->get('tblimports')->row()->sum_quantity;
    }
    public function sum_quantity_export($product_id,$date="",$warehouse_id="")
    {
        $this->db->select('sum(quantity) as sum_quantity');
        if($date!="")
        {
            $this->db->where('date <="'.$date.'"');
        }
        $this->db->where('status',2);
        $this->db->join('tblexport_items','tblexport_items.export_id=tblexports.id')->where('product_id',$product_id);
        if(is_numeric($warehouse_id))
        {
            $this->db->where('tblexport_items.warehouse_id',$warehouse_id);
        }
        return $this->db->get('tblexports')->row()->sum_quantity;
    }
    public function sum_quantity_transfer($product_id,$date="",$warehouse_id="",$from=true)
    {
        
        $this->db->select('sum(quantity) as sum_quantity,sum(quantity_net) as sum_quantity_net');
        if($date!="")
        {
            $this->db->where('date <="'.$date.'"');
        }
        $this->db->where('status',2);
        $this->db->join('tblimport_items','tblimport_items.import_id=tblimports.id')->where('product_id',$product_id);
        if(is_numeric($warehouse_id) && $from==true)
        {
            $this->db->where('tblimport_items.warehouse_id',$warehouse_id);
        }
        else
        {
            $this->db->where('tblimport_items.warehouse_id_to',$warehouse_id);
        }
        $this->db->where('tblimport_items.warehouse_id_to is not null');
        if($from)
            return $this->db->get('tblimports')->row()->sum_quantity;
        else
            return $this->db->get('tblimports')->row()->sum_quantity_net;
    }
    public function getInventoryQuantity($product_id,$date="",$warehouse_id="")
    {
        $total=0;
        $import=$this->sum_quantity_import($product_id,$date,$warehouse_id);
        $transfer_from=$this->sum_quantity_transfer($product_id,$date,$warehouse_id,true);
        $transfer_to=$this->sum_quantity_transfer($product_id,$date,$warehouse_id,false);
        $export=$this->sum_quantity_export($product_id,$date,$warehouse_id);
        $total=$import-$export+$transfer_to-$transfer_from;
        return $total;
    }

    public function warehouse_import_report($pdf=false)
    {
        if ($this->input->is_ajax_request()||$pdf==true)
        {
            //Nhap kho
            $select = array(
                'tblimports.date as date',
                'CONCAT(tblimports.prefix,tblimports.code) as code',
                'tblitems.short_name as product_code',
                'tblitems.name as product_name',
                'tblwarehouses.warehouse as warehouse_name',
                'tblimport_items.quantity',
                'tblimport_items.quantity_net',
            );
            $where  = array(
                'AND tblimports.rel_type <> "transfer"'
            );
            if($this->input->post('product_category_import'))
            {
                array_push($where, 'AND tblcategories.id='.$this->input->post('product_category_import'));
            }
            $warehouse_id=$this->input->post('warehouse_invenroty_import');
            if($warehouse_id && count($warehouse_id)>0)
            {
                array_push($where, 'AND tblwarehouses.warehouseid IN (' . implode(', ', $warehouse_id) . ')');
            }
            $custom_date_select = $this->get_where_report_period('tblimports.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblimports';
            $join         = array(
                'LEFT JOIN tblimport_items ON tblimport_items.import_id = tblimports.id', 
                'LEFT JOIN tblitems ON tblitems.id = tblimport_items.product_id', 
                'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id', 
                'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit', 
                'LEFT JOIN tblwarehouses ON tblwarehouses.warehouseid = tblimport_items.warehouse_id', 
            );
            $order_by='order by product_id asc';
            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'tblcategories.category',
                'tblimports.id as import_id',
                'tblimports.rel_type',
                'warehouse_id',
                'warehouse_id_to',
            ),$order_by);
            $output  = $result['output'];
            $rResult = $result['rResult'];
            $footer_data=array(
                'quantity'=>0,
                'quantity_net'=>0
            );
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if($aColumns[$i]=='tblimports.date as date')
                    {
                        $_data = _d($aRow[strafter($aColumns[$i], 'as ')]);
                    }
                    if($aColumns[$i]=='CONCAT(tblimports.prefix,tblimports.code) as code')
                    {
                        if($aRow['rel_type']=='internal')
                            $_data = '<a target="_blank" href="'.admin_url("imports/internal_detail/").$aRow['import_id'].'">'.$aRow['code'].'</a>';
                        elseif($aRow['rel_type']=='transfer')
                            $_data = '<a target="_blank" href="'.admin_url("imports/transfer_detail/").$aRow['import_id'].'">'.$aRow['code'].'</a>';
                        elseif($aRow['rel_type']=='return')
                            $_data = '<a target="_blank" href="'.admin_url("imports/return_detail/").$aRow['import_id'].'">'.$aRow['code'].'</a>';
                        elseif($aRow['rel_type']=='contract')
                            $_data = '<a target="_blank" href="'.admin_url("imports/contract_detail/").$aRow['import_id'].'">'.$aRow['code'].'</a>';
                        elseif($aRow['rel_type']=='adjustment')
                            $_data = '<a target="_blank" href="'.admin_url("imports/adjustment_detail/").$aRow['import_id'].'">'.$aRow['code'].'</a>';
                        elseif(strpos($aRow['rel_type'],'export')>=0)
                            $_data = '<a target="_blank" href="'.admin_url("exports/export_detail/").$aRow['import_id'].'">'.$aRow['code'].'</a>';
                    } 
                    if($aColumns[$i]=='tblitems.name as product_name')
                    {
                        $_data = $aRow['category'];
                    }
                    if($aColumns[$i]=='tblimport_items.quantity')
                    {
                        $footer_data['quantity']+=$aRow[$aColumns[$i]];
                        $_data = '<p class="text-center">'._format_number($aRow[$aColumns[$i]]).'</p>';
                    }
                    if($aColumns[$i]=='tblimport_items.quantity_net')
                    {
                        $footer_data['quantity_net']+=$aRow[$aColumns[$i]];
                        $_data = '<p class="text-center">'._format_number($aRow[$aColumns[$i]]).'</p>';
                    }
                    
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }
            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = _format_number($total);
            }
            $output['sums'] = $footer_data;
        }
        if($pdf==false)
        {
            echo json_encode($output);
        }
        else
        {
            return $output;
        }
        die();
    }

    public function warehouse_transfer_report($pdf=false)
    {
        if ($this->input->is_ajax_request()||$pdf==true)
        {
            //Chuyen kho
            $select = array(
                'tblimports.date as date',
                'CONCAT(tblimports.prefix,tblimports.code) as code',
                'tblitems.short_name as product_code',
                'tblitems.name as product_name',
                '1',
                'tblimport_items.quantity',
                '2',
                'tblimport_items.quantity_net',
            );
            $where  = array(
                'AND tblimports.rel_type ="transfer"'
            );
            if($this->input->post('product_category_transfer'))
            {
                array_push($where, 'AND tblcategories.id='.$this->input->post('product_category_transfer'));
            }
            $warehouse_id=$this->input->post('warehouse_invenroty_transfer');
            if($warehouse_id && count($warehouse_id)>0)
            {
                array_push($where, 'AND tblimport_items.warehouse_id IN (' . implode(', ', $warehouse_id) . ')');
                array_push($where, 'OR tblimport_items.warehouse_id_to IN (' . implode(', ', $warehouse_id) . ')');
            }
            $custom_date_select = $this->get_where_report_period('tblimports.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblimports';
            $join         = array(
                'LEFT JOIN tblimport_items ON tblimport_items.import_id = tblimports.id', 
                'LEFT JOIN tblitems ON tblitems.id = tblimport_items.product_id', 
                'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id', 
                'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit', 
                'LEFT JOIN tblwarehouses ON tblwarehouses.warehouseid = tblimport_items.warehouse_id', 
            );
            $order_by='order by product_id asc';
            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'tblcategories.category',
                'tblimports.id as import_id',
                'tblimports.rel_type',
                'warehouse_id',
                'warehouse_id_to',
            ),$order_by);
            $output  = $result['output'];
            $rResult = $result['rResult'];
            $footer_data=array(
                'quantity'=>0,
                'quantity_net'=>0
            );
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if($aColumns[$i]=='tblimports.date as date')
                    {
                        $_data = _d($aRow[strafter($aColumns[$i], 'as ')]);
                    }
                    if($aColumns[$i]=='CONCAT(tblimports.prefix,tblimports.code) as code')
                    {
                        if($aRow['rel_type']=='internal')
                            $_data = '<a target="_blank" href="'.admin_url("imports/internal_detail/").$aRow['import_id'].'">'.$aRow['code'].'</a>';
                        elseif($aRow['rel_type']=='transfer')
                            $_data = '<a target="_blank" href="'.admin_url("imports/transfer_detail/").$aRow['import_id'].'">'.$aRow['code'].'</a>';
                        elseif($aRow['rel_type']=='return')
                            $_data = '<a target="_blank" href="'.admin_url("imports/return_detail/").$aRow['import_id'].'">'.$aRow['code'].'</a>';
                        elseif($aRow['rel_type']=='contract')
                            $_data = '<a target="_blank" href="'.admin_url("imports/contract_detail/").$aRow['import_id'].'">'.$aRow['code'].'</a>';
                        elseif($aRow['rel_type']=='adjustment')
                            $_data = '<a target="_blank" href="'.admin_url("imports/adjustment_detail/").$aRow['import_id'].'">'.$aRow['code'].'</a>';
                        elseif(strpos($aRow['rel_type'],'export')>=0)
                            $_data = '<a target="_blank" href="'.admin_url("exports/export_detail/").$aRow['import_id'].'">'.$aRow['code'].'</a>';
                    } 
                    if($aColumns[$i]=='tblitems.name as product_name')
                    {
                        $_data = $aRow['category'];
                    }
                    if($aColumns[$i]=='tblimport_items.quantity')
                    {
                        $footer_data['quantity']+=$aRow[$aColumns[$i]];
                        $_data = '<p class="text-center">'._format_number($aRow[$aColumns[$i]]).'</p>';
                    }
                    if($aColumns[$i]=='tblimport_items.quantity_net')
                    {
                        $footer_data['quantity_net']+=$aRow[$aColumns[$i]];
                        $_data = '<p class="text-center">'._format_number($aRow[$aColumns[$i]]).'</p>';
                    }
                    if($aColumns[$i]=='1')
                    {
                        $_data = getWarehouseByID($aRow['warehouse_id'])->warehouse;
                    }
                    if($aColumns[$i]=='2')
                    {
                        $_data = getWarehouseByID($aRow['warehouse_id_to'])->warehouse;
                    }
                    
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }
            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = _format_number($total);
            }
            $output['sums'] = $footer_data;
        }
        if($pdf==false)
        {
            echo json_encode($output);
        }
        else
        {
            return $output;
        }
        die();
    }
    public function warehouse_export_report($pdf=false)
    {
        if ($this->input->is_ajax_request()||$pdf==true)
        {
            //Xuat kho
            $select = array(
                'tblexports.date as date',
                'CONCAT(tblexports.prefix,tblexports.code) as code',
                'tblitems.short_name as product_code',
                'tblitems.name as product_name',
                'tblwarehouses.warehouse as warehouse_name',
                'tblexport_items.quantity',
                'tblexport_items.quantity_net',
            );
            $where  = array(
            );
            if($this->input->post('product_category_export'))
            {
                array_push($where, 'AND tblcategories.id='.$this->input->post('product_category_export'));
            }
            $warehouse_id=$this->input->post('warehouse_invenroty_export');
            if($warehouse_id && count($warehouse_id)>0)
            {
                array_push($where, 'AND tblwarehouses.warehouseid IN (' . implode(', ', $warehouse_id) . ')');
            }
            $custom_date_select = $this->get_where_report_period('tblexports.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblexports';
            $join         = array(
                'LEFT JOIN tblexport_items ON tblexport_items.export_id = tblexports.id', 
                'LEFT JOIN tblitems ON tblitems.id = tblexport_items.product_id', 
                'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id', 
                'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit', 
                'LEFT JOIN tblwarehouses ON tblwarehouses.warehouseid = tblexport_items.warehouse_id', 
            );
            $order_by='order by product_id asc';
            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'tblcategories.category',
                'tblexports.id as export_id',
                'tblexports.rel_type',
                'warehouse_id',
            ),$order_by);
            $output  = $result['output'];
            $rResult = $result['rResult'];
            $footer_data=array(
                'quantity'=>0,
                'quantity_net'=>0
            );
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if($aColumns[$i]=='tblexports.date as date')
                    {
                        $_data = _d($aRow[strafter($aColumns[$i], 'as ')]);
                    }
                    if($aColumns[$i]=='CONCAT(tblexports.prefix,tblexports.code) as code')
                    {
                        $_data = '<a target="_blank" href="'.admin_url("exports/export_detail/").$aRow['export_id'].'">'.$aRow['code'].'</a>';
                    } 
                    if($aColumns[$i]=='tblitems.name as product_name')
                    {
                        $_data = $aRow['category'];
                    }
                    if($aColumns[$i]=='tblexport_items.quantity')
                    {
                        $footer_data['quantity']+=$aRow[$aColumns[$i]];
                        $_data = '<p class="text-center">'._format_number($aRow[$aColumns[$i]]).'</p>';
                    }
                    if($aColumns[$i]=='tblexport_items.quantity_net')
                    {
                        $footer_data['quantity_net']+=$aRow[$aColumns[$i]];
                        $_data = '<p class="text-center">'._format_number($aRow[$aColumns[$i]]).'</p>';
                    }
                    
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }
            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = _format_number($total);
            }
            $output['sums'] = $footer_data;
        }
        if($pdf==false)
        {
            echo json_encode($output);
        }
        else
        {
            return $output;
        }
        die();
    }
    public function warehouse_PO_report($pdf=false)
    {
        if ($this->input->is_ajax_request()||$pdf==true)
        {
            //Xuat kho
            $select = array(
                'tblsale_orders.date as date',
                'CONCAT(tblsale_orders.prefix,tblsale_orders.code) as code',
                'tblitems.short_name as product_code',
                'tblitems.name as product_name',
                'tblwarehouses.warehouse as warehouse_name',
                'tblsale_order_items.quantity',
            );
            $where  = array(
            );
            if($this->input->post('product_category_PO'))
            {
                array_push($where, 'AND tblcategories.id='.$this->input->post('product_category_PO'));
            }
            $warehouse_id=$this->input->post('warehouse_invenroty_PO');
            if($warehouse_id && count($warehouse_id)>0)
            {
                array_push($where, 'AND tblwarehouses.warehouseid IN (' . implode(', ', $warehouse_id) . ')');
            }
            $custom_date_select = $this->get_where_report_period('tblsale_orders.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblsale_orders';
            $join         = array(
                'LEFT JOIN tblsale_order_items ON tblsale_order_items.sale_id = tblsale_orders.id', 
                'LEFT JOIN tblitems ON tblitems.id = tblsale_order_items.product_id', 
                'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id', 
                'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit', 
                'LEFT JOIN tblwarehouses ON tblwarehouses.warehouseid = tblsale_order_items.warehouse_id', 
            );
            $order_by='order by product_id asc';
            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'tblcategories.category',
                'tblsale_orders.id as sale_id',
                'tblsale_orders.rel_type',
                'warehouse_id',
            ),$order_by);
            $output  = $result['output'];
            $rResult = $result['rResult'];
            $footer_data=array(
                'quantity'=>0,
                'quantity_net'=>0
            );
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if($aColumns[$i]=='tblsale_orders.date as date')
                    {
                        $_data = _d($aRow[strafter($aColumns[$i], 'as ')]);
                    }
                    if($aColumns[$i]=='CONCAT(tblsale_orders.prefix,tblsale_orders.code) as code')
                    {
                        $_data = '<a target="_blank" href="'.admin_url("sale_orders/sale_detail/").$aRow['sale_id'].'">'.$aRow['code'].'</a>';
                    } 
                    if($aColumns[$i]=='tblitems.name as product_name')
                    {
                        $_data = $aRow['category'];
                    }
                    if($aColumns[$i]=='tblsale_order_items.quantity')
                    {
                        $footer_data['quantity']+=$aRow[$aColumns[$i]];
                        $_data = '<p class="text-center">'._format_number($aRow[$aColumns[$i]]).'</p>';
                    }
                    
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }
            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = _format_number($total);
            }
            $output['sums'] = $footer_data;
        }
        if($pdf==false)
        {
            echo json_encode($output);
        }
        else
        {
            return $output;
        }
        die();
    }

    public function warehouse_SO_report($pdf=false)
    {
        if ($this->input->is_ajax_request()||$pdf==true)
        {
            //Xuat kho
            $select = array(
                'tblsales.date as date',
                'CONCAT(tblsales.prefix,tblsales.code) as code',
                'tblitems.short_name as product_code',
                'tblitems.name as product_name',
                'tblwarehouses.warehouse as warehouse_name',
                'tblsale_items.quantity',
            );
            $where  = array(
            );
            if($this->input->post('product_category_SO'))
            {
                array_push($where, 'AND tblcategories.id='.$this->input->post('product_category_SO'));
            }
            $warehouse_id=$this->input->post('warehouse_invenroty_SO');
            if($warehouse_id && count($warehouse_id)>0)
            {
                array_push($where, 'AND tblwarehouses.warehouseid IN (' . implode(', ', $warehouse_id) . ')');
            }
            $custom_date_select = $this->get_where_report_period('tblsales.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblsales';
            $join         = array(
                'LEFT JOIN tblsale_items ON tblsale_items.sale_id = tblsales.id', 
                'LEFT JOIN tblitems ON tblitems.id = tblsale_items.product_id', 
                'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id', 
                'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit', 
                'LEFT JOIN tblwarehouses ON tblwarehouses.warehouseid = tblsale_items.warehouse_id', 
            );
            $order_by='order by product_id asc';
            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'tblcategories.category',
                'tblsales.id as sale_id',
                'tblsales.rel_type',
                'warehouse_id',
                'rel_id',
                'rel_code',
            ),$order_by);
            $output  = $result['output'];
            $rResult = $result['rResult'];
            $footer_data = array(
                'quantity' => 0,
                'quantity_net' => 0
            );
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if($aColumns[$i]=='tblsales.date as date')
                    {
                        $_data = _d($aRow[strafter($aColumns[$i], 'as ')]);
                    }
                    if($aColumns[$i]=='CONCAT(tblsales.prefix,tblsales.code) as code')
                    {
                        $_data = '<a target="_blank" href="'.admin_url("sales/sale_detail/").$aRow['sale_id'].'">'.$aRow['code'].($aRow['rel_id']?'('.$aRow['rel_code'].')':'').'</a>';
                    } 
                    if($aColumns[$i]=='tblitems.name as product_name')
                    {
                        $_data = $aRow['category'];
                    }
                    if($aColumns[$i]=='tblsale_items.quantity')
                    {
                        $footer_data['quantity']+=$aRow[$aColumns[$i]];
                        $_data = '<p class="text-center">'._format_number($aRow[$aColumns[$i]]).'</p>';
                    }
                    
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }
            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = _format_number($total);
            }
            $output['sums'] = $footer_data;
        }
        if($pdf==false)
        {
            echo json_encode($output);
        }
        else
        {
            return $output;
        }
        die();
    }

    public function warehouse_Quote_report($pdf=false)
    {
        if ($this->input->is_ajax_request()||$pdf==true)
        {
            //Xuat kho
            $select = array(
                'tblquotes.date as date',
                'CONCAT(tblquotes.prefix,tblquotes.code) as code',
                'tblitems.short_name as product_code',
                'tblitems.name as product_name',
                'tblwarehouses.warehouse as warehouse_name',
                'tblquote_items.quantity',
            );
            $where  = array(
            );
            if($this->input->post('product_category_Quote'))
            {
                array_push($where, 'AND tblcategories.id='.$this->input->post('product_category_Quote'));
            }
            $warehouse_id=$this->input->post('warehouse_invenroty_Quote');
            if($warehouse_id && count($warehouse_id)>0)
            {
                array_push($where, 'AND tblwarehouses.warehouseid IN (' . implode(', ', $warehouse_id) . ')');
            }
            $custom_date_select = $this->get_where_report_period('tblquotes.date');
            if ($custom_date_select != '') {
                array_push($where, $custom_date_select);
            }
            $aColumns     = $select;
            $sIndexColumn = "id";
            $sTable       = 'tblquotes';
            $join         = array(
                'LEFT JOIN tblquote_items ON tblquote_items.quote_id = tblquotes.id', 
                'LEFT JOIN tblitems ON tblitems.id = tblquote_items.product_id', 
                'LEFT JOIN tblcategories ON tblcategories.id = tblitems.category_id', 
                'LEFT JOIN tblunits ON tblunits.unitid = tblitems.unit', 
                'LEFT JOIN tblwarehouses ON tblwarehouses.warehouseid = tblquote_items.warehouse_id', 
            );
            $order_by='order by product_id asc';
            $result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
                'tblcategories.category',
                'tblquotes.id as sale_id',
                'warehouse_id',
            ),$order_by);
            $output  = $result['output'];
            $rResult = $result['rResult'];
            $footer_data = array(
                'quantity' => 0,
                'quantity_net' => 0
            );
            foreach ($rResult as $aRow) {
                $row = array();
                for ($i = 0; $i < count($aColumns); $i++) {
                    if (strpos($aColumns[$i], 'as') !== false && !isset($aRow[$aColumns[$i]])) {
                        $_data = $aRow[strafter($aColumns[$i], 'as ')];
                    } else {
                        $_data = $aRow[$aColumns[$i]];
                    }
                    if($aColumns[$i]=='tblquotes.date as date')
                    {
                        $_data = _d($aRow[strafter($aColumns[$i], 'as ')]);
                    }
                    if($aColumns[$i]=='CONCAT(tblquotes.prefix,tblquotes.code) as code')
                    {
                        $_data = '<a target="_blank" href="'.admin_url("sales/sale_detail/").$aRow['sale_id'].'">'.$aRow['code'].'</a>';
                    } 
                    if($aColumns[$i]=='tblitems.name as product_name')
                    {
                        $_data = $aRow['category'];
                    }
                    if($aColumns[$i]=='tblquote_items.quantity')
                    {
                        $footer_data['quantity']+=$aRow[$aColumns[$i]];
                        $_data = '<p class="text-center">'._format_number($aRow[$aColumns[$i]]).'</p>';
                    }
                    
                    $row[] = $_data;
                }
                $output['aaData'][] = $row;
                $x++;
            }
            foreach ($footer_data as $key => $total) {
                $footer_data[$key] = _format_number($total);
            }
            $output['sums'] = $footer_data;
        }
        if($pdf==false)
        {
            echo json_encode($output);
        }
        else
        {
            return $output;
        }
        die();
    }


}
