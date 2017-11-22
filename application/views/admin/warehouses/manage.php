<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body _buttons">
                        <a href="<?php echo admin_url() . "invoice_items/summary" ?>" class="btn mright5 btn-info pull-left display-block"><?php echo _l('Danh sách sản phầm trong kho'); ?></a>
                        <a href="#" onclick="new_warehouse(); return false;" class="btn mright5 btn-info pull-left display-block"><?php echo _l('Thêm kho mới'); ?></a>
                        
                    </div>
                </div>
                <div class="panel_s">
                    <div class="panel-body">
                        
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            <?php echo render_select('product_category', $categories, array('id', 'category'), 'Danh mục cha'); ?>
                        </div>
                        <div class="col-xs-6 col-sm-6 col-md-6 col-lg-6">
                            <?php echo render_select('products', array(), array('id', 'category'), 'Sản phẩm'); ?>
                        </div>

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
                        <a href="<?php echo admin_url('warehouses/exportexcel'); ?>" class="btn mright5 btn-info pull-left display-block"><?php echo _l('Xuất Excel'); ?></a>
                        <?php render_datatable(array(
                            _l('id'),
                            _l('Mã kho'),
                            _l('Tên kho'),
                            _l('Địa chỉ'),
                            _l('Điện thoại'),
                            _l('options')
                        ),'warehouses'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="type" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('warehouses/add_warehouse'),array('id'=>'id_type')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('Sửa kho sản phẩm'); ?></span>
                    <span class="add-title"><?php echo _l('Thêm kho sản phẩm mới'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('code','Mã kho'); ?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_input('warehouse','Tên kho'); ?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_input('address','Địa chỉ'); ?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_input('phone','Điện thoại'); ?>
                    </div>
                    <div class="col-md-12">
                    

                    <div class="form-group" id="canSO">
                        <label for="warehouse_can_export"  class="control-label"><?php echo _l('Kho hàng có thể bán'); ?></label><br>
                        <div class="radio radio-primary radio-inline">
                            <input type="radio" name="warehouse_can_export" id="no" value="0"  />
                            <label for="no">Không thể bán</label>
                        </div>
                        <div class="radio radio-primary radio-inline">
                            <input type="radio" name="warehouse_can_export" id="yes" value="1" />
                            <label for="yes">Có thể bán</label>
                        </div>
                    </div>

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
    function view_detail(id) {
        var view_price=<?=json_encode($view_price)?>;
        $('#detail .modal-body .row').html("");
        $('#detail .modal-title').html("<span class=\"edit-title\"><?php echo _l('warehouse_info'); ?></span>");
        $.get('<?php echo admin_url("warehouses/modal_detail/")?>' + id + '?get=true', (data) => {
            $('#detail .modal-body .row').html(data.body);
            $('#detail .modal-title').html(data.header);
            let filterList_detail = {
                "detail_categories" : "[name='detail_categories']",
                "detail_products"  : "[name='detail_products']",
            }

            let tableinventory=initDataTable('.table-warehouse-detail', admin_url + 'warehouses/modal_detail/' + id, [1], [1], filterList_detail);
            if(view_price.class.length>0)
            {
                tableinventory.column(6).visible(false, false).columns.adjust();
            }

            $.each(filterList_detail, (index, obj) => {   
                $('select' + obj).on('change', () => {
                    $('.table-warehouse-detail').DataTable().ajax.reload();
                });
            });
            init_selectpicker();

        }, 'json');
        $('#detail').modal('show');
    }
    function view_init_department(id)
    {
        $('#type').modal('show');
        $('.add-title').addClass('hide');
        jQuery.ajax({
            type: "post",
            url:admin_url+"warehouses/get_row_warehouse/"+id,
            data: '',
            cache: false,
            success: function (data) {
                var json = JSON.parse(data);
//                if($data!="")
                {
                    $('input[id="yes"]').removeProp('checked');
                    $('input[id="yes"]').val(1);
                    $('input[id="no"]').prop('checked', 'checked');
                    $('input[id="no"]').val(0);
                    $('#code').val(json.code);
                    $('#warehouse').val(json.warehouse);
                    $('#address').val(json.address);
                    $('#phone').val(json.phone);
                    // if(id!=12)
                    // {
                    //     $('#canSO').removeClass('hide');
                    // }
                    // if(id==12)
                    // {
                    //     $('#canSO').addClass('hide');
                    // }
                    if(json.warehouse_can_export==1) {
                        $('input[id="yes"]').prop('checked', 'checked');
                    }
                    jQuery('#id_type').prop('action',admin_url+'warehouses/update_warehouse/'+id);
                }
            }
        });
    }
    var filterList = {
        "product_category"  : "[name='product_category']",
        "products"          : "[name='products']",
    };
    $(function(){
        initDataTable('.table-warehouses', window.location.href, [1], [1], filterList);
        $.each(filterList, (index, value) => {
            $('select' + value).on('change', () => {
                if(index == 'product_category') {
                    if($('#product_category').selectpicker('val') != '') {
                        $.ajax({
                            url : admin_url + 'warehouses/get_all_products/' + $('#product_category').selectpicker('val'),
                            method: 'post',
                            dataType: 'json',
                            success: (data) => {
                                $('#products option').remove();
                                $('#products').append('<option></option>');
                                data.forEach(obj => {
                                    console.log(obj);
                                    $('#products').append('<option value="' + obj.id + '">' + obj.name + '</option>');
                                });
                                $('#products').selectpicker('refresh');
                            },
                        });
                        
                    }
                }
                $('.table-warehouses').DataTable().ajax.reload();
            });
        });
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
            $('.table-warehouses').DataTable().ajax.reload();
            $('#type').modal('hide');
        });
        return false;
    }

    function new_warehouse(){
        $('#type').modal('show');
        $('.edit-title').addClass('hide');
        jQuery('#category').val('');
        jQuery('#id_type').prop('action',admin_url+'warehouses/add_warehouse');
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
