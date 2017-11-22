<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body _buttons">
                        <a href="#" onclick="new_warehouse(); return false;" class="btn mright5 btn-info pull-left display-block"><?php echo _l('Thêm loại kho mới'); ?></a>

                        <a href="<?php echo admin_url() . 'warehouses'?> " class="btn btn-info pull-left display-block"><?php echo _l('Trở lại Kho'); ?></a>
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        <style>
                            .dt-buttons .buttons-collection{
                                display: none!important;
                            }
                        </style>
                        <div class="clearfix"></div>
                        <a href="<?php echo admin_url('kind_of_warehouse/exportexcel'); ?>" class="btn mright5 btn-info pull-left display-block"><?php echo _l('Xuất Excel'); ?></a>
                        <?php render_datatable(array(
                            _l('id'),
                            _l('Tên loại kho'),
                            _l('options'),
                        ),'kind-of-warehouses'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="type" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('kind_of_warehouses/add'),array('id'=>'id_type')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('Sửa loại kho'); ?></span>
                    <span class="add-title"><?php echo _l('Thêm loại kho'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('name','Tên loại kho'); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal fade lead-modal" id="detail" tabindex="-1" role="dialog"  >
    <div class="modal-dialog" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('warehouse_info'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">

                </div>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php init_tail(); ?>
<script>
    function view_init_department(id)
    {
        $('#type').modal('show');
        $('.add-title').addClass('hide');
        jQuery.ajax({
            type: "post",
            url:admin_url+"kind_of_warehouse/get_row/"+id,
            data: '',
            cache: false,
            success: function (data) {
                var json = JSON.parse(data);
//                if($data!="")
                {
                    $('#name').val(json.name);
                    jQuery('#id_type').prop('action',admin_url+'kind_of_warehouse/update/'+id);
                }
            }
        });
    }

    $(function(){
        initDataTable('.table-kind-of-warehouses', window.location.href, [1], [1]);
        _validate_form($('form'),{code:'required',warehouse:'required',address:'required',phone:'required'},manage_contract_types);
        $('#type').on('hidden.bs.modal', function(event) {
            $('#additional').html('');
            $('#type input').val('');
            $('.add-title').removeClass('hide');
            $('.edit-title').removeClass('hide');
        });
    });

    // $('body').on('click', '.delete-reminder', function() {
    //     var r = confirm(confirm_action_prompt);
    //     alert($.fn.DataTable.isDataTable('.table-warehouses'));
    //     if (r == false) {
    //         return false;
    //     } else {
    //         $.get($(this).attr('href'), function(response) {
    //             alert_float(response.alert_type, response.message);
    //             // Looop throug all availble reminders table to reload the data
    //             $.each(available_reminders_table, function(i, table) {
    //                 alert(table);
    //                 if ($.fn.DataTable.isDataTable(table)) {
    //                     $('body').find(table).DataTable().ajax.reload();
    //                 }
    //             });
    //         }, 'json');
    //     }
    //     return false;
    // });
    
    function manage_contract_types(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if(response.success == true){
                alert_float('success',response.message);
            }
            $('.table-kind-of-warehouses').DataTable().ajax.reload();
            $('#type').modal('hide');
        });
        return false;
    }

    function new_warehouse(){
        $('#type').modal('show');
        $('.edit-title').addClass('hide');
        jQuery('#category').val('');
        jQuery('#id_type').prop('action',admin_url+'kind_of_warehouse/add');
    }
    function edit_type(invoker,id){
        var name = $(invoker).data('name');
        $('#additional').append(hidden_input('id',id));
        $('#type input[name="category"]').val(name);
        $('#type').modal('show');
        $('.add-title').addClass('hide');
    }

    

</script>
</body>
</html>
