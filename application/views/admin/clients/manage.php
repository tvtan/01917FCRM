<?php init_head(); ?>
<!-- <style>
    .dt-buttons .buttons-collection{
        display: none!important;
    }
</style> -->
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <?php if (has_permission('customers','','import')) { ?>
                                <a href="<?php echo admin_url('clients/client'); ?>" class="btn btn-info mright5 test pull-left display-block">
                                    <?php echo _l('new_client'); ?></a>
                                <a href="<?php echo admin_url('clients/import'); ?>" class="btn btn-info pull-left display-block mright5">
                                    <?php echo _l('import_customers'); ?></a>
                            <?php } ?>
                            <div class="visible-xs">
                                <div class="clearfix"></div>
                            </div>
                            <div class="btn-group pull-right btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
                                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <i class="fa fa-filter" aria-hidden="true"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-left" style="width:300px;">
                                    <li class="active"><a href="#" data-cview="all" onclick="dt_custom_view('','.table-clients',''); return false;"><?php echo _l('customers_sort_all'); ?></a></li>
                                    <li class="divider"></li>
                                    <?php if(count($groups) > 0){ ?>
                                        <li class="dropdown-submenu pull-left groups">
                                            <a href="#" tabindex="-1"><?php echo _l('customer_groups'); ?></a>
                                            <ul class="dropdown-menu dropdown-menu-left">
                                                <?php foreach($groups as $group){ ?>
                                                    <li><a href="#" data-cview="customer_group_<?php echo $group['id']; ?>" onclick="dt_custom_view('customer_group_<?php echo $group['id']; ?>','.table-clients','customer_group_<?php echo $group['id']; ?>'); return false;"><?php echo $group['name']; ?></a></li>
                                                <?php } ?>
                                            </ul>
                                        </li>
                                        <div class="clearfix"></div>
                                        <li class="divider"></li>
                                    <?php } ?>
                                    <li class="dropdown-submenu pull-left invoice">
                                        <a href="#" tabindex="-1"><?php echo _l('invoices'); ?></a>
                                        <ul class="dropdown-menu dropdown-menu-left">
                                            <?php foreach($invoice_statuses as $status){ ?>
                                                <li>
                                                    <a href="#" data-cview="invoices_<?php echo $status; ?>" data-cview="1" onclick="dt_custom_view('invoices_<?php echo $status; ?>','.table-clients','invoices_<?php echo $status; ?>'); return false;"><?php echo _l('customer_have_invoices_by',format_invoice_status($status,'',false)); ?></a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </li>
                                    <div class="clearfix"></div>
                                    <li class="divider"></li>
                                    <li class="dropdown-submenu pull-left estimate">
                                        <a href="#" tabindex="-1"><?php echo _l('estimates'); ?></a>
                                        <ul class="dropdown-menu dropdown-menu-left">
                                            <?php foreach($estimate_statuses as $status){ ?>
                                                <li>
                                                    <a href="#" data-cview="estimates_<?php echo $status; ?>" onclick="dt_custom_view('estimates_<?php echo $status; ?>','.table-clients','estimates_<?php echo $status; ?>'); return false;">
                                                        <?php echo _l('customer_have_estimates_by',format_estimate_status($status,'',false)); ?>
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </li>
                                    <div class="clearfix"></div>
                                    <li class="divider"></li>
                                    <li class="dropdown-submenu pull-left project">
                                        <a href="#" tabindex="-1"><?php echo _l('projects'); ?></a>
                                        <ul class="dropdown-menu dropdown-menu-left">
                                            <?php foreach($project_statuses as $status){ ?>
                                                <li>
                                                    <a href="#" data-cview="projects_<?php echo $status; ?>" onclick="dt_custom_view('projects_<?php echo $status; ?>','.table-clients','projects_<?php echo $status; ?>'); return false;">
                                                        <?php echo _l('customer_have_projects_by',_l('project_status_'.$status)); ?>
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </li>
                                    <div class="clearfix"></div>
                                    <li class="divider"></li>
                                    <li class="dropdown-submenu pull-left proposal">
                                        <a href="#" tabindex="-1"><?php echo _l('proposals'); ?></a>
                                        <ul class="dropdown-menu dropdown-menu-left">
                                            <?php foreach($proposal_statuses as $status){ ?>
                                                <li>
                                                    <a href="#" data-cview="proposals_<?php echo $status; ?>" onclick="dt_custom_view('proposals_<?php echo $status; ?>','.table-clients','proposals_<?php echo $status; ?>'); return false;">
                                                        <?php echo _l('customer_have_proposals_by',format_proposal_status($status,'',false)); ?>
                                                    </a>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </li>
                                    <div class="clearfix"></div>
                                    <?php if(count($contract_types) > 0) { ?>
                                        <li class="divider"></li>
                                        <li class="dropdown-submenu pull-left contract_types">
                                            <a href="#" tabindex="-1"><?php echo _l('contract_types'); ?></a>
                                            <ul class="dropdown-menu dropdown-menu-left">
                                                <?php foreach($contract_types as $type){ ?>
                                                    <li>
                                                        <a href="#" data-cview="contract_type_<?php echo $type['id']; ?>" onclick="dt_custom_view('contract_type_<?php echo $type['id']; ?>','.table-clients','contract_type_<?php echo $type['id']; ?>'); return false;">
                                                            <?php echo _l('customer_have_contracts_by_type',$type['name']); ?>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </li>
                                    <?php } ?>
                                    <?php if(count($customer_admins) > 0 && (has_permission('customers','','create') || has_permission('customers','','edit'))){ ?>
                                        <li class="divider"></li>
                                        <li class="dropdown-submenu pull-left responsible_admin">
                                            <a href="#" tabindex="-1"><?php echo _l('responsible_admin'); ?></a>
                                            <ul class="dropdown-menu dropdown-menu-left">
                                                <?php foreach($customer_admins as $cadmin){ ?>
                                                    <li>
                                                        <a href="#" data-cview="responsible_admin_<?php echo $cadmin['staff_id']; ?>" onclick="dt_custom_view('responsible_admin_<?php echo $cadmin['staff_id']; ?>','.table-clients','responsible_admin_<?php echo $cadmin['staff_id']; ?>'); return false;">
                                                            <?php echo get_staff_full_name($cadmin['staff_id']); ?>
                                                        </a>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <?php if(has_permission('customers','','view') || have_assigned_customers()) {
                            $where_summary = '';
                            if(!has_permission('customers','','view')){
                                $where_summary = ' AND userid IN (SELECT customer_id FROM tblcustomeradmins WHERE staff_id='.get_staff_user_id().')';
                            }
                            ?>
                            <hr />
                            <div class="row mbot15">
                                <div class="col-md-12">
                                    <h3 class="text-success no-margin"><?php echo _l('customers_summary'); ?></h3>
                                </div>
                                <div class="col-md-2 col-xs-6 border-right">
                                    <h3 class="bold"><?php echo total_rows('tblclients',($where_summary != '' ? substr($where_summary,5) : '')); ?></h3>
                                    <span class="text-dark"><?php echo _l('customers_summary_total'); ?></span>
                                </div>
                                <div class="col-md-2 col-xs-6 border-right">
                                    <h3 class="bold"><?php echo total_rows('tblclients','active=1'.$where_summary); ?></h3>
                                    <span class="text-success"><?php echo _l('active_customers'); ?></span>
                                </div>
                                <div class="col-md-2 col-xs-6 border-right">
                                    <h3 class="bold"><?php echo total_rows('tblclients','active=0'.$where_summary); ?></h3>
                                    <span class="text-danger"><?php echo _l('inactive_active_customers'); ?></span>
                                </div>
                                <div class="col-md-2 col-xs-6 border-right">
                                    <h3 class="bold"><?php echo total_rows('tblcontacts','active=1'.$where_summary); ?></h3>
                                    <span class="text-info"><?php echo _l('customers_summary_active'); ?></span>
                                </div>
                                <div class="col-md-2  col-xs-6 border-right">
                                    <h3 class="bold"><?php echo total_rows('tblcontacts','active=0'.$where_summary); ?></h3>
                                    <span class="text-danger"><?php echo _l('customers_summary_inactive'); ?></span>
                                </div>
                                <div class="col-md-2 col-xs-6">
                                    <h3 class="bold"><?php echo total_rows('tblcontacts','last_login LIKE "'.date('Y-m-d').'%"'.$where_summary); ?></h3>
                                    <span class="text-muted"><?php echo _l('customers_summary_logged_in_today'); ?></span>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <div class="_filters _hidden_inputs hidden">
                    <?php
                    foreach($groups as $group){
                        echo form_hidden('customer_group_'.$group['id']);
                    }
                    foreach($contract_types as $type){
                        echo form_hidden('contract_type_'.$type['id']);
                    }
                    foreach($invoice_statuses as $status){
                        echo form_hidden('invoices_'.$status);
                    }
                    foreach($estimate_statuses as $status){
                        echo form_hidden('estimates_'.$status);
                    }
                    foreach($project_statuses as $status){
                        echo form_hidden('projects_'.$status);
                    }
                    foreach($proposal_statuses as $status){
                        echo form_hidden('proposals_'.$status);
                    }
                    foreach($customer_admins as $cadmin){
                        echo form_hidden('responsible_admin_'.$cadmin['staff_id']);
                    }
                    ?>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <?php
                            if (has_permission('customers', '', 'delete')) 
                            {
                            ?>
                                
                            
                        <a href="#" data-toggle="modal" data-target="#customers_bulk_action" class="btn btn-info mbot15"><?php echo _l('bulk_actions'); ?></a>
                        <div class="modal fade bulk_actions" id="customers_bulk_action" tabindex="-1" role="dialog">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                                    </div>
                                    <div class="modal-body">
                                        <?php if(has_permission('customers','','delete')){ ?>
                                            <div class="checkbox checkbox-danger">
                                                <input type="checkbox" name="mass_delete" id="mass_delete">
                                                <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                                            </div>
                                            <hr class="mass_delete_separator" />
                                        <?php } ?>
                                        <div id="bulk_change">
                                            <?php echo render_select('move_to_groups_customers_bulk[]',$groups,array('id','name'),'customer_groups','', array('multiple'=>true),array(),'','',false); ?>
                                            <p class="text-danger"><?php echo _l('bulk_action_customers_groups_warning'); ?></p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                                        <a href="#" class="btn btn-info" onclick="customers_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                                    </div>
                                </div><!-- /.modal-content -->
                            </div><!-- /.modal-dialog -->
                        </div><!-- /.modal -->
                            <?php
                                }
                            ?>
                        <div class="clearfix"></div>
                        <?php if(has_permission('customers','','export') || is_admin()){ ?>
                        <a href="<?php echo admin_url('clients/exportexcel'); ?>" class="btn mright5 btn-info pull-left display-block"><?php echo _l('Xuất Excel'); ?></a>
                        <?php }?>
                        <?php
                        $table_data = array();
                        $_table_data = array(
                            _l('order_no'),
                            _l('clients_code'),
                            _l('clients_list_company'),
                            _l('clients_list_phone'),
                            _l('clients_phone'),
                            _l('clients_address'),
                            _l('company_primary_email'),
                            _l('birthday'),
                            _l('client_type'),
                            _l('source_approach'),
                            _l('contact_primary'),
                            _l('create_by'),
                            _l('als_staff'),
                            _l('customer_groups'),
                        );
                        foreach($_table_data as $_t){
                            array_push($table_data,$_t);
                        }
                        array_unshift($table_data,'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="clients"><label></label></div>');

                        $custom_fields = get_custom_fields('customers',array('show_on_table'=>1));
                        foreach($custom_fields as $field){
                            array_push($table_data,$field['name']);
                        }

                        $table_data = do_action('customers_table_columns',$table_data);

                        $_op = _l('options');

                        array_push($table_data, $_op);
                        render_datatable($table_data,'clients');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>

<script>
    var CustomersServerParams = {};
    $.each($('._hidden_inputs._filters input'),function(){
        CustomersServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
    });
    var headers_clients = $('.table-clients').find('th');
    var not_sortable_clients = (headers_clients.length - 1);
    let clientTableAPI = initDataTableFixedHeader('.table-clients', 
        window.location.href, 
        [not_sortable_clients, 1, 0, not_sortable_clients-2, not_sortable_clients-3, 8], 
        [not_sortable_clients, 1, 0, not_sortable_clients-2, not_sortable_clients-3, 8], 
        CustomersServerParams,
        <?php echo do_action('customers_table_default_order',json_encode(array(2,'DESC'))); ?>, 
        {
            leftColumns: 4,
            rightColumns: 1
        });

    // console.log(clientTableAPI);
    $('div.dataTables_scrollBody').on('scroll', function(e) {
        // your code
        // console.log(e);
    });
    function customers_bulk_action(event) {
        var r = confirm(confirm_action_prompt);
        if (r == false) {
            return false;
        } else {
            var mass_delete = $('#mass_delete').prop('checked');
            var ids = [];
            var data = {};
            if(mass_delete == false || typeof(mass_delete) == 'undefined'){
                data.groups = $('select[name="move_to_groups_customers_bulk[]"]').selectpicker('val');
                if (data.groups.length == 0) {
                    data.groups = 'remove_all';
                }
            } else {
                data.mass_delete = true;
            }
            var rows = $('.table-clients').find('tbody tr');
            $.each(rows, function() {
                var checkbox = $($(this).find('td').eq(0)).find('input');
                if (checkbox.prop('checked') == true) {
                    ids.push(checkbox.val());
                }
            });
            data.ids = ids;
            $(event).addClass('disabled');
            setTimeout(function(){
                $.post(admin_url + 'clients/bulk_action', data).done(function() {
                    window.location.reload();
                });
            },50);
        }
    }
    function init_client_modal_data(id, url) {
        if (typeof(id) == 'undefined') {
            id = '';
        }
        var _url = admin_url + 'clients/modal/' + id;
        if (typeof(url) != 'undefined') {
            _url = url;
        }
        // get the current hash
        var hash = window.location.hash;
        // clean the modal
        // $('.lead-modal .modal-content').html('');
        $.get(_url, function(response) {
            $('.lead-modal .modal-content').html(response.data);
            $('#lead_reminder_modal').html(response.reminder_data);
            $('.lead-modal').modal({
                show: true,
                backdrop: 'static'
            });
            init_selectpicker();
            init_form_reminder();
            init_datepicker();
            init_color_pickers();
            validate_lead_form(lead_profile_form_handler);

            if (hash == '#tab_lead_profile' || hash == '#attachments' || hash == '#lead_notes') {
                window.location.hash = hash;
            }
            if (id != '') {

                if (typeof(Dropbox) != 'undefined') {
                    document.getElementById("dropbox-chooser-lead").appendChild(Dropbox.createChooseButton({
                        success: function(files) {
                            $.post(admin_url + 'leads/add_external_attachment', {
                                files: files,
                                lead_id: id,
                                external: 'dropbox'
                            }).done(function() {
                                init_lead_modal_data(id);
                            });
                        },
                        linkType: "preview",
                        extensions: allowed_files.split(','),
                    }));
                }

                if (typeof(leadAttachmentsDropzone) != 'undefined') {
                    leadAttachmentsDropzone.destroy();
                }

                leadAttachmentsDropzone = new Dropzone("#lead-attachment-upload", {
                    addRemoveLinks: false,
                    dictDefaultMessage: drop_files_here_to_upload,
                    dictFallbackMessage: browser_not_support_drag_and_drop,
                    dictRemoveFile: remove_file,
                    dictMaxFilesExceeded: you_can_not_upload_any_more_files,
                    sending: function(file, xhr, formData) {
                        formData.append("leadid", id);
                    },
                    acceptedFiles: allowed_files,
                    error: function(file, response) {
                        alert_float('danger', response);
                    },
                    success: function(file) {
                        if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                            init_lead_modal_data(id);
                        }
                    }
                });

                $('body').find('.nav-tabs a[href="' + window.location.hash + '"]').tab('show');
                $('#lead-latest-activity').html($('#lead_activity').find('.feed-item:last-child .text').html());
            }

        }, 'json').fail(function(data) {
            $('.lead-modal').modal('hide');
            alert_float('danger', data.responseText);
        });
    }
</script>
</body>
</html>
<style type="text/css">
  .table-clients tr td:nth-child(4){
    max-width: 200px;
    white-space: inherit;
    min-width: 200px;
  }
  .modal-content{
    width: 1200px;
    right: 100px;
  }

</style>
