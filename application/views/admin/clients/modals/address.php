<!-- Modal Address -->
<div class="modal fade" id="address_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <?php echo form_open('admin/clients/address/'.$customer_id.'/'.$address_id,array('id'=>'address-form','autocomplete'=>'off')); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $title; ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <?php echo form_hidden('address_id',$address_id); ?>
                            
                            <?php $countries = get_all_countries();
                                $customer_default_country = get_option('customer_default_country');
                                $selected = (isset($address) ? $address->country : $customer_default_country);
                                echo render_select('addressS_country', $countries, array('country_id', array('short_name')), 'clients_country', $selected, array('data-none-selected-text' => _l('dropdown_non_selected_tex')));
                            ?>

                            <?php
                                $default_source_id = 0;
                                if (!isset($address) && count($areas) > 0) {
                                    $default_source_id = $areas[0]['id'];
                                }
                                echo render_select('addressS_area', $areas, array('id', 'name'), 'client_area', !isset($address) ? $default_source_id : $address->area, array('data-none-selected-text' => _l('dropdown_non_selected_tex')));
                            ?>

                            <?php $value = (isset($address) ? $address->city : ''); ?>
                            <?php echo render_select('addressS_city', get_all_province(), array('provinceid', 'name'), 'client_city', $value, array('data-none-selected-text' => _l('dropdown_non_selected_tex'))); ?>

                            <?php $value = (isset($address) ? $address->state : ''); ?>
                            <?php echo render_select('addressS_state', array(), array('districtid', 'name'), 'client_district', $value, array('data-none-selected-text' => _l('dropdown_non_selected_tex'))); ?>


                            <?php $value = (isset($address) ? $address->ward : ''); ?>
                            <?php echo render_select('addressS_ward', array(), array('districtid', 'name'), 'client_ward', $value, array('data-none-selected-text' => _l('dropdown_non_selected_tex'))); ?>
                            <?php $value = (isset($address) ? $address->town : ''); ?>
                            <?php echo render_input('addressS_town', 'client_address_town', $value, 'text', array()); ?>

                        </div>
                        <div class="col-md-6">
                          
                            <?php $value = (isset($address) ? $address->building : ''); ?>
                            <?php echo render_input('addressS_building', 'client_address_building', $value, 'text', array()); ?>

                             
                            <?php $value = (isset($address) ? $address->room_number : ''); ?>
                            <?php echo render_input('addressS_room_number', 'client_address_room_number', $value, 'text', array()); ?>
                            <?php $value = (isset($address) ? $address->home_number : ''); ?>
                            <?php echo render_input('addressS_home_number', 'client_address_home_number', $value, 'text', array()); ?>
                            <?php $value = (isset($address) ? $address->street : ''); ?>
                            <?php echo render_input('addressS_street', 'client_street', $value, 'text', array()); ?>
                            <?php $value = (isset($address) ? $address->zip : ''); ?>
                            <?php echo render_input('addressS_zip', 'client_postal_code', $value, 'text', array()); ?>
                           
                            

                            <div class="checkbox checkbox-primary mtop40">
                                <input type="checkbox" name="is_primary" id="address_primary" <?php if((!isset($address) && total_rows('tblcontacts',array('is_primary'=>1,'userid'=>$customer_id)) == 0) || (isset($address) && $address->is_primary == 1)){echo 'checked';}; ?> <?php if((isset($address) && total_rows('tblcontacts',array('is_primary'=>1,'userid'=>$customer_id)) == 1 && $address->is_primary == 1)){echo 'disabled';} ?>>
                                <label for="is_primary">
                                    <?php echo _l( 'address_primary'); ?>
                                </label>
                            </div>

                        </div>
                
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info" data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off" data-form="#address-form"><?php echo _l('submit'); ?></button>
    </div>
    <?php echo form_close(); ?>
</div>
</div>
</div>
<script type="text/javascript">
default_city  = '<?php echo isset($address) ? $address->city : 0 ?>';
default_state = '<?php echo isset($address) ? $address->state : 0 ?>';                
default_ward  = '<?php echo isset($address) ? $address->ward : 0?>';  
loadFromCityM(default_city,default_state, default_ward);
</script>