<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Roles extends Admin_controller
{
    function __construct()
    {
        parent::__construct();
        // Model is autoloaded
    }
    /* List all staff roles */
    public function index()
    {
        if (!has_permission('roles', '', 'view')) {
            access_denied('roles');
        }
        if ($this->input->is_ajax_request()) {
            $this->perfex_base->get_table_data('roles');
        }
        $data['title'] = _l('all_roles');
        $this->load->view('admin/roles/manage', $data);
    }
    /* Add new role or edit existing one */
    public function role($id = '')
    {
        if (!has_permission('roles', '', 'view')) {
            access_denied('roles');
        }
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('roles', '', 'create')) {
                    access_denied('roles');
                }
                $id = $this->roles_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfuly', _l('role')));
                    redirect(admin_url('roles/role/' . $id));
                }
            } else {
                if (!has_permission('roles', '', 'edit')) {
                    access_denied('roles');
                }
                $success = $this->roles_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfuly', _l('role')));
                }
                redirect(admin_url('roles/role/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('role_lowercase'));
        } else {
            $data['role_permissions'] = $this->roles_model->get_role_permissions($id);
            $role                     = $this->roles_model->get($id);
            $data['role']             = $role;
            $title                    = _l('edit', _l('role_lowercase')) . ' ' . $role->name;
        }
        $data['permissions'] = $this->roles_model->get_permissions();
        $data['custom_permission'] = array(
            'view_account' => array(
                'id' => 28,
                'permissions' => array(
                    'have',
                )
            ),
            'customers' => array(
                'id' => 8,
                'permissions' => array(
                    'export',
                    'import',
                    'assign',
                    'lock',
                ),
            ),
            'items' => array(
                'id' => 19,
                'permissions' => array(
                    'update_price',
                    'update_price_buy',
                    'lock',
                ),
            ),
            'staff' => array(
                'id' => 7,
                'permissions' => array(
                    'assign',
                    'follow',
                ),
            ),
            'yeucaumuahang' => array(
                'id' => 30,
                'permissions' => array(
                    'approve_departments',
                    'approve_all',
                ),
            ),
            'hopdongmuahangnn' => array(
                'id' => 45,
                'permissions' => array(
                    'approve_departments',
                    'approve_all',
                ),
            ),
            'pomuahang' => array(
                'id' => 31,
                'permissions' => array(
                    'approve',
                ),
            ),
            'cocvachitrancc' => array(
                'id' => 32,
                'permissions' => array(
                    'approve_departments',
                    'approve_all',
                ),
            ),
            'hangtrave' => array(
                'id' => 33,
                'permissions' => array(
                    'approve',
                    'import_warehouse',
                    'change_accounts',
                ),
            ),
            'chuyenkho' => array(
                'id' => 34,
                'permissions' => array(
                    'approve',
                    'import_export_warehouse',
                ),
            ),
            'dieuchinhkho' => array(
                'id' => 35,
                'permissions' => array(
                    'approve',
                    'approve_storekeepers',
                ),
            ),
            'quote_items' => array(
                'id' => 36,
                'permissions' => array(
                    'approve',
                    'lock',
                ),
            ),

            'hopdong' => array(
                'id' => 37,
                'permissions' => array(
                    'approve',
                    'lock',
                ),
            ),
            'po' => array(
                'id' => 38,
                'permissions' => array(
                    'approve',
                    'lock',
                ),
            ),
            'so' => array(
                'id' => 39,
                'permissions' => array(
                    'approve',
                    'lock',
                ),
            ),
            'baoco' => array(
                'id' => 43,
                'permissions' => array(
                    'lock',
                ),
            ),
            'xuatkho' => array(
                'id' => 44,
                'permissions' => array(
                    'approve',
                ),
            ),
            'reports' => array(
                'id' => 3,
                'permissions' => array(
                    'view_report_sales',
                    'view_report_purchases',
                    'view_report_warehouses',
                    'view_report_debts',
                    'view_report_technical',
                    'view_report_customer_care',
                ),
            ),
        );
        $data['title']       = $title;
        $this->load->view('admin/roles/role', $data);
    }
    /* Delete staff role from database */
    public function delete($id)
    {
        if (!has_permission('roles', '', 'delete')) {
            access_denied('roles');
        }
        if (!$id) {
            redirect(admin_url('roles'));
        }
        $response = $this->roles_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('role_lowercase')));
        } else if ($response == true) {
            set_alert('success', _l('deleted', _l('role')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('role_lowercase')));
        }
        redirect(admin_url('roles'));
    }
}
