<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
class Perfex_Base
{
    private $options = array();
    // Quick actions aide
    private $quick_actions = array();
    // Instance CI
    private $_instance;
    // Dynamic options
    private $dynamic_options = array('next_invoice_number', 'next_estimate_number');
    // Show or hide setup menu
    private $show_setup_menu = true;
    // Currently reminders
    private $available_reminders = array('customer', 'lead', 'estimate', 'invoice', 'proposal', 'expense');
    // Tables where currency id is used
    private $tables_with_currency = array();
    // Media folder
    private $media_folder;
    // Available languages
    private $available_languages = array();

    function __construct()
    {
        $this->_instance =& get_instance();
        $options = $this->_instance->db->get('tbloptions')->result_array();
        // Loop the options and store them in a array to prevent fetching again and again from database
        foreach ($options as $option) {
            $this->options[$option['name']] = $option['value'];
        }

        $this->tables_with_currency = do_action('tables_with_currency',array(
            array(
                'table' => 'tblinvoices',
                'field' => 'currency'
            ),
            array(
                'table' => 'tblexpenses',
                'field' => 'currency'
            ),
            array(
                'table' => 'tblproposals',
                'field' => 'currency'
            ),
            array(
                'table' => 'tblestimates',
                'field' => 'currency'
            ),
            array(
                'table' => 'tblclients',
                'field' => 'default_currency'
            )
        ));

        $this->media_folder = do_action('before_set_media_folder', 'media');

        foreach (list_folders(APPPATH . 'language') as $language) {
            if(is_dir(APPPATH.'language/'.$language)){
                array_push($this->available_languages, $language);
            }
        }

        do_action('app_base_after_construct_action');
    }
    /**
     * Return all available languages in the application/language folder
     * @return array
     */
    public function get_available_languages()
    {
        return $this->available_languages;
    }
    /**
     * Function that will parse table data from the tables folder for amin area
     * @param  string $table  table filename
     * @param  array  $params additional params
     * @return void
     */
    public function get_table_data($table, $params = array())
    {
        $hook_data = do_action('before_render_table_data', array(
            'table' => $table,
            'params' => $params
        ));
        foreach ($hook_data['params'] as $key => $val) {
            $$key = $val;
        }
        $table = $hook_data['table'];
        if (file_exists(VIEWPATH . 'admin/tables/my_' . $table . '.php')) {
            include_once(VIEWPATH . 'admin/tables/my_' . $table . '.php');
        } else {
            include_once(VIEWPATH . 'admin/tables/' . $table . '.php');
        }
        echo json_encode($output);
        die;
    }
    /**
     * All available reminders keys for the features
     * @return array
     */
    public function get_available_reminders_keys()
    {
        return $this->available_reminders;
    }
    /**
     * Get all db options
     * @return array
     */
    public function get_options()
    {
        return $this->options;
    }

    /**
     * Function that gets option based on passed name
     * @param  string $name
     * @return string
     */
    public function getMaxID($id,$table)
    {
        $table = trim($table);
        
        if (isset($id)) {
            $this->_instance->db->select_max($id);
            return $this->_instance->db->get($table)->row()->{$id};
        }
        return '';
    }

   public function getMaxIDCODE($id,$table,$where)
    {
        $table = trim($table);
        if (is_array($where)) {
        if (sizeof($where) > 0) {
            $this->_instance->db->where($where);
        }
        } else if (strlen($where) > 0) {
            $this->_instance->db->where($where);
        }
        if (isset($id)) {
            $this->_instance->db->select_max($id);
            return $this->_instance->db->get($table)->row()->{$id};
        }
        return '';
    }

    public function getRow($table,$where)
    {
        $table = trim($table);
        if (isset($where)) {
            $this->_instance->db->where($where);
            return $this->_instance->db->get($table)->row();
        }
        return false;
    }

    public function getProvince($id)
    {
        
        if (isset($id)) {
            $this->_instance->db->where('provinceid',$id);
            return $this->_instance->db->get('province')->row();
        }
        return false;
    }

    public function getDistrict($id)
    {        
        if (isset($id)) {
            $this->_instance->db->where('districtid',$id);
            return $this->_instance->db->get('district')->row();
        }
        return false;
    }
    public function getWard($id)
    {        
        if (isset($id)) {
            $this->_instance->db->where('wardid',$id);
            return $this->_instance->db->get('ward')->row();
        }
        return false;
    }

    public function getWHTIDByWHID($warehouse_id)
    {        
        if (isset($warehouse_id)) {
            $this->_instance->db->where('warehouseid',$warehouse_id);
            return $this->_instance->db->get('tblwarehouses')->row()->kindof_warehouse;
        }
        return false;
    }
// Increase Quanity Product In Warehouse
    public function increaseProductQuantity($warehouse_id,$product_id,$quantity)
    {     
        if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity)) {
            $product=$this->_instance->db->get_where('tblwarehouses_products',array('product_id'=>$product_id,'warehouse_id'=>$warehouse_id))->row();
            if($product)
            {
                $total_quantity=$quantity+$product->product_quantity;
                $this->_instance->db->update('tblwarehouses_products',array('product_quantity'=>$total_quantity),array('id'=>$product->id));
            }
            else
            {
                $data=array(
                    'product_id'=>$product_id,
                    'warehouse_id'=>$warehouse_id,
                    'product_quantity'=>$quantity
                    );
                $this->_instance->db->insert('tblwarehouses_products',$data);
            }
            if($this->_instance->db->affected_rows()>0) 
                return true;
        }
        return false;
    }

// Decrease Quanity Product In Warehouse
    public function decreaseProductQuantity($warehouse_id,$product_id,$quantity)
    {     
        if (isset($product_id) && isset($warehouse_id) && is_numeric($quantity)) {
            $product=$this->_instance->db->get_where('tblwarehouses_products',array('product_id'=>$product_id,'warehouse_id'=>$warehouse_id))->row();
            if($product)
            {
                $total_quantity=$product->product_quantity-$quantity;
                $this->_instance->db->update('tblwarehouses_products',array('product_quantity'=>$total_quantity),array('id'=>$product->id));
            }
            else
            {
                $data=array(
                    'product_id'=>$product_id,
                    'warehouse_id'=>$warehouse_id,
                    'product_quantity'=>$quantity*(-1)
                    );
                $this->_instance->db->insert('tblwarehouses_products',$data);
            }
            if($this->_instance->db->affected_rows()>0) 
                return true;
        }
        return false;
    }

    public function getClient($id,$address_type=NULL)
    {        
        if (isset($id) && empty($address_type)) {
            $this->_instance->db->where('userid',$id);
            return $this->_instance->db->get('tblclients')->row();
        }
        elseif(isset($id) && isset($address_type))
        {

            //1: DChi KH
            //2: DChi giao hang
            $address=array();
            $client=$this->_instance->db->where('userid',$id)->get('tblclients')->row();
         
            if($address_type==1)
            {
                 $address[]=$client->address_room_number ? _l('Số phòng '.$client->address_room_number) : '' ;
                $address[]=$client->address_building ? _l('Tòa nhà '.$client->address_building) : '' ;
                $address[]=$client->address_home_number ? _l($client->address_home_number) : '' ;
                $address[]=$client->address ? _l('Đường '.$client->address) : '' ;
                $address[]=$client->address_town ? _l($client->address_town) : '' ;
                $ward=getWard($client->address_ward);
                $address[]=$client->address_ward ? _l("$ward->type ".$ward->name) : '' ;
                $district=getDistrict($client->state);
                $address[]=$client->state ? _l("$district->type ".$district->name) : '' ;
                $province=getProvince($client->city);
                $address[]=$client->city ? _l("$province->type ".$province->name) : '' ;
            }
            if($address_type==2)
            {
                $address[]=$client->shipping_room_number ? _l('Số phòng '.$client->shipping_room_number) : '' ;
                $address[]=$client->shipping_building ? _l('Tòa nhà '.$client->shipping_building) : '' ;
                $address[]=$client->shipping_home_number ? _l($client->shipping_home_number) : '' ;
                $address[]=$client->shipping_street ? _l('Đường '.$client->shipping_street) : '' ;
                $address[]=$client->shipping_town ? _l($client->shipping_town) : '' ;
                $ward=getWard($client->shipping_ward);
                $address[]=$client->address_ward ? _l("$ward->type ".$ward->name) : '' ;
                $district=getDistrict($client->shipping_state);
                $address[]=$client->state ? _l("$district->type ".$district->name) : '' ;
                $province=getProvince($client->shipping_city);
                $address[]=$client->city ? _l("$province->type ".$province->name) : '' ;
            }

            foreach ($address as $key => $value) {
                if(empty($value) || empty(trim($value))) unset($address[$key]);
            }
            return implode(', ', $address);
        }
        return false;
    }

    


    public function getSupllier($id,$address_type=NULL)
    {        
        if (isset($id) && empty($address_type)) {
            $this->_instance->db->where('userid',$id);
            return $this->_instance->db->get('tblsuppliers')->row();
        }
        elseif(isset($id) && isset($address_type))
        {

            //1: DChi KH
            //2: DChi giao hang
            $address=array();
            $client=$this->_instance->db->where('userid',$id)->get('tblsuppliers')->row();
            if($address_type==1)
            {
                $address[]=$client->address_home_number ? _l('Số '.$client->address_home_number) : '' ;
                $address[]=$client->address ? _l('Đường '.$client->address) : '' ;
                $address[]=$client->address_town ? _l('Khu phố/thôn/ấp '.$client->address_town) : '' ;
                $ward=getWard($client->address_ward);
                $address[]=$client->address_ward ? _l("$ward->type ".$ward->name) : '' ;
                $district=getDistrict($client->state);
                $address[]=$client->state ? _l("$district->type ".$district->name) : '' ;
                $province=getProvince($client->city);
                $address[]=$client->city ? _l("$province->type ".$province->name) : '' ;
            }
            elseif($address_type==2)
            {
                $address[]=$client->shipping_home_number ? _l('Số '.$client->shipping_home_number) : '' ;
                $address[]=$client->shipping_street ? _l('Đường '.$client->shipping_street) : '' ;
                $address[]=$client->shipping_town ? _l('Khu phố/thôn/ấp '.$client->shipping_town) : '' ;
                $ward=getWard($client->shipping_ward);
                $address[]=$client->address_ward ? _l("$ward->type ".$ward->name) : '' ;
                $district=getDistrict($client->shipping_state);
                $address[]=$client->state ? _l("$district->type ".$district->name) : '' ;
                $province=getProvince($client->shipping_city);
                $address[]=$client->city ? _l("$province->type ".$province->name) : '' ;
            }
            foreach ($address as $key => $value) {
                if(empty($value) || empty(trim($value))) unset($address[$key]);
            }
            return implode(', ', $address);
        }
        return false;
    }

    public function getWareHouse($id)
    {        
        if (isset($id)) {
            $this->_instance->db->where('warehouseid',$id);
            return $this->_instance->db->get('tblwarehouses')->row();
        }
        return false;
    }
    /**
     * Function that gets option based on passed name
     * @param  string $name
     * @return string
     */
    public function get_option($name)
    {

        if ($name == 'number_padding_invoice_and_estimate') {
            $name = 'number_padding_prefixes';

        }

        $name = trim($name);
        
        if (isset($this->options[$name])) {
           
            if (in_array($name, $this->dynamic_options)) {
                $this->_instance->db->where('name', $name);
                return $this->_instance->db->get('tbloptions')->row()->value;
            } else {
                 
                return $this->options[$name];
            }
        }
        return '';
    }
    public function check_option($name) {
        if ($name == 'number_padding_invoice_and_estimate') {
            $name = 'number_padding_prefixes';

        }

        $name = trim($name);
        
        if (isset($this->options[$name])) {
            if (in_array($name, $this->dynamic_options)) {
                $this->_instance->db->where('name', $name);
                return true;
            } else {
                return true;
            }
        }
        return false;
    }

    /**
     * Add new quick action data
     * @param array $item
     */
    public function add_quick_actions_link($item = array())
    {
        $this->quick_actions[] = $item;
    }
    /**
     * Quick actions data set from admin_controller.php
     * @return array
     */
    public function get_quick_actions_links()
    {
        $this->quick_actions = do_action('before_build_quick_actions_links', $this->quick_actions);
        return $this->quick_actions;
    }
    /**
     * Predefined contact permission
     * @return array
     */
    public function get_contact_permissions()
    {
        $permissions = array(
            array(
                'id' => 1,
                'name' => _l('customer_permission_invoice'),
                'short_name' => 'invoices'
            ),
            array(
                'id' => 2,
                'name' => _l('customer_permission_estimate'),
                'short_name' => 'estimates'
            ),
            array(
                'id' => 3,
                'name' => _l('customer_permission_contract'),
                'short_name' => 'contracts'
            ),
            array(
                'id' => 4,
                'name' => _l('customer_permission_proposal'),
                'short_name' => 'proposals'
            ),
            array(
                'id' => 5,
                'name' => _l('customer_permission_support'),
                'short_name' => 'support'
            ),
            array(
                'id' => 6,
                'name' => _l('customer_permission_projects'),
                'short_name' => 'projects'
            )
        );
        return do_action('get_contact_permissions', $permissions);
    }
    /**
     * Aside.php will set the menu visibility here based on few conditions
     * @param int $total_setup_menu_items total setup menu items shown to the user
     */
    public function set_setup_menu_visibility($total_setup_menu_items)
    {
        if ($total_setup_menu_items == 0) {
            $this->show_setup_menu = false;
        } else {
            $this->show_setup_menu = true;
        }
    }
    /**
     * Check if should the script show the setup menu or not
     * @return boolean
     */
    public function show_setup_menu()
    {
        return do_action('show_setup_menu',$this->show_setup_menu);
    }
    /**
     * Return tables that currency id is used
     * @return array
     */
    public function get_tables_with_currency()
    {
        return do_action('tables_with_currencies',$this->tables_with_currency);
    }
    /**
     * Return the media folder name
     * @return string
     */
    public function get_media_folder()
    {
        return do_action('get_media_folder',$this->media_folder);
    }
}
