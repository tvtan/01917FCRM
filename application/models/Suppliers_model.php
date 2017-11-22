<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Suppliers_model extends CRM_Model
{
    
    function __construct()
    {
        parent::__construct();
    }

     /**
     * @param  integer ID
     * @return boolean
     * Delete client, also deleting rows from, dismissed client announcements, ticket replies, tickets, autologin, user notes
     */
    public function delete($id)
    {
        $affectedRows = 0;
        do_action('before_client_deleted', $id);

        $this->db->where('userid', $id);
        $this->db->delete('tblsuppliers');
        
        if ($this->db->affected_rows() > 0) {
            do_action('after_client_deleted');
            logActivity('Supplier Deleted [' . $id . ']');
            return true;
        }
        return false;
    }

    /**
     * @param  mixed $id supplier id (optional)
     * @param  integer $active (optional) get all active or inactive
     * @return mixed
     * Get supplier object based on passed supplierid if not passed supplierid return array of all clients
     */
    public function get($id = '', $where = array('tblsuppliers.active' => 1), $single_result_type = 'row')
    {
        $this->db->select(implode(',', prefixed_table_fields_array('tblsuppliers')));
        $this->db->join('tblcountries', 'tblcountries.country_id = tblsuppliers.country', 'left');
        if (is_numeric($id)) {
            $this->db->where('tblsuppliers.userid', $id);
            $client = $this->db->get('tblsuppliers')->$single_result_type();
            return $client;
        }

        $this->db->where($where);
        $this->db->order_by('company', 'asc');
        return $this->db->get('tblsuppliers')->result_array();
    }

        /**
     * @param array $_POST data
     * @param client_request is this request from the customer area
     * @return integer Insert ID
     * Add new client to database
     */
    public function add($data, $client_or_lead_convert_request = false)
    {
        $contact_data = array();
        foreach ($this->contact_data as $field) {
            if (isset($data[$field])) {
                $contact_data[$field] = $data[$field];
                // Phonenumber is also used for the company profile
                if ($field != 'phonenumber') {
                    unset($data[$field]);
                }
            }
        }
        // From customer profile register
        if (isset($data['contact_phonenumber'])) {
            $contact_data['phonenumber'] = $data['contact_phonenumber'];
            unset($data['contact_phonenumber']);
        }
        if (isset($data['passwordr'])) {
            unset($data['passwordr']);
        }
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }
        if (isset($data['groups_in'])) {
            $groups_in = $data['groups_in'];
            unset($data['groups_in']);
        }
        if (isset($data['country']) && $data['country'] == '' || !isset($data['country'])) {
            $data['country'] = 0;
        }
        if(!isset($data['show_primary_contact'])) {
            $data['show_primary_contact'] = 0;
        }
        if (isset($data['billing_country']) && $data['billing_country'] == '' || !isset($data['billing_country'])) {
            $data['billing_country'] = 0;
        }
        if (isset($data['default_currency']) && $data['default_currency'] == '' || !isset($data['default_currency'])) {
            $data['default_currency'] = 0;
        }
        if (isset($data['shipping_country']) && $data['shipping_country'] == '' || !isset($data['shipping_country'])) {
            $data['shipping_country'] = 0;
        }
        $data['datecreated'] = date('Y-m-d H:i:s');
        $data                = do_action('before_client_added', $data);
        $this->db->insert('tblsuppliers', $data);
        $userid = $this->db->insert_id();
        if ($userid) {
            if (isset($groups_in)) {
                foreach ($groups_in as $group) {
                    $this->db->insert('tblcustomergroups_in', array(
                        'customer_id' => $userid,
                        'groupid' => $group
                    ));
                }
            }
            do_action('after_client_added', $userid);
            $_new_client_log = $data['company'];
            if ($_new_client_log == '' && isset($contact_id)) {
                $_new_client_log = get_contact_full_name($contact_id);
            }

            $_is_staff = NULL;
            if (!is_client_logged_in() && is_staff_logged_in()) {
                $_new_client_log .= ' From Staff: ' . get_staff_user_id();
                $_is_staff = get_staff_user_id();
            }

            logActivity('New suppliers Created [' . $_new_client_log . ']', $_is_staff);
        }
        return $userid;
    }

        /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update client informations
     */
    public function update($data, $id, $client_request = false)
    {
        unset($data['DataTables_Table_0_length']);
        unset($data['DataTables_Table_1_length']);
        unset($data['onoffswitch']);
        if (isset($data['update_all_other_transactions'])) {
            $update_all_other_transactions = true;
            unset($data['update_all_other_transactions']);
        }
        $affectedRows = 0;
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }
        if (isset($data['groups_in'])) {
            $groups_in = $data['groups_in'];
            unset($data['groups_in']);
        }
        if (isset($data['country']) && $data['country'] == '' || !isset($data['country'])) {
            $data['country'] = 0;
        }
        if (isset($data['billing_country']) && $data['billing_country'] == '' || !isset($data['billing_country'])) {
            $data['billing_country'] = 0;
        }
        if (isset($data['default_currency']) && $data['default_currency'] == '' || !isset($data['default_currency'])) {
            $data['default_currency'] = 0;
        }
        if (isset($data['shipping_country']) && $data['shipping_country'] == '' || !isset($data['shipping_country'])) {
            $data['shipping_country'] = 0;
        }

        if(!isset($data['show_primary_contact'])) {
            $data['show_primary_contact'] = 0;
        }

        $_data = do_action('before_client_updated', array(
            'userid' => $id,
            'data' => $data
        ));
        $data  = $_data['data'];
        $this->db->where('userid', $id);
        $this->db->update('tblsuppliers', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            do_action('after_client_updated', $id);
        }
        if (isset($update_all_other_transactions)) {
            // Update all unpaid invoices
            $this->db->where('clientid', $id);
            $this->db->where('status !=', 2);

            // Update all estimates
            $this->db->where('clientid', $id);
        }

        if (!isset($groups_in)) {
            $groups_in = false;
        }
        if ($affectedRows > 0) {
            logActivity('Customer Info Updated [' . $data['company'] . ']');
            return true;
        }
        return false;
    }
    
}
