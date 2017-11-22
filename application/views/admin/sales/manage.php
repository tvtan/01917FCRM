<?php init_head(); ?>
<style type="text/css">
    .table-sales tr td:nth-child(4){
    max-width: 200px;
    white-space: inherit;
    min-width: 200px;
  }
  .table-sales tr td:nth-child(3){
    max-width: 80px;
    white-space: inherit;
    min-width: 80px;
  }
</style>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <!-- <div class="panel_s">
                    <div class="panel-body">                   
                    <h4 class="bold no-margin"><?=_l('sale_orders')?></h4>
                    </div>
                </div> -->
                <div class="clearfix"></div>
                <div class="panel_s">
                    <div class="panel-body">
                        <ul class="nav nav-tabs profile-tabs" role="tablist">
                            <?php
                                $activePO=true;
                                $activeSO=false;
                                if(!empty($order_id) || $isSO)
                                {
                                    $activePO=false;
                                    $activeSO=true;
                                }
                            ?>
                            <?php
                            if (has_permission('po', '', 'view') || has_permission('po', '', 'view_own')) {
                            ?>
                            <li role="presentation" <?=($activePO? 'class="active"' : '')?>>
                                <a href="#sale_PO" aria-controls="sale_PO" role="tab" data-toggle="tab">
                                    <?php echo _l( 'sale_PO'); ?>
                                </a>
                            </li>
                            <?php
                            }
                            ?>
                            <?php
                            if (has_permission('so', '', 'view') || has_permission('so', '', 'view_own')) { 
                            ?>
                            <li role="presentation" <?=($activeSO? 'class="active"' : '')?>>
                                <a href="#sale_SO" aria-controls="sale_SO" role="tab" data-toggle="tab">
                                    <?php echo _l( 'sale_SO'); ?>
                                </a>
                            </li>                            
                            <?php
                            }
                            ?>
                        </ul>
                        <div class="tab-content">
                            <?php
                            if (has_permission('po', '', 'view') || has_permission('po', '', 'view_own')) {
                            ?>
                            <div role="tabpanel" class="tab-pane <?=($activePO? 'active' : '')?>" id="sale_PO">
                                <div class="row">
                                    <div class="col-md-12">
                                        <a href="<?=admin_url('sale_orders/sale_detail')?>" class="btn btn-info pull-left display-block mbot15"><?php echo _l('add_sale_porder'); ?></a>
                                        <div class="clearfix"></div>
                                        <div class="panel_s">
                                            <div class="panel-body">
                                            <input type="hidden" id="filterStatus" value="" />
                                            <div data-toggle="btns" class="btn-group mbot15">
                                                <button style=" font-size: 11px;" type="button" id="btnDatatableFilterAll" data-toggle="tab" class="btn btn-info active">Tất cả</button>
                                                <button style=" font-size: 11px;" type="button" id="btnDatatableFilterNotApproval" data-toggle="tab" class="btn btn-info">Chưa duyệt</button>
                                                <button style=" font-size: 11px;" type="button" id="btnDatatableFilterApproval" data-toggle="tab" class="btn btn-info">Đã duyệt</button>
                                                <button style=" font-size: 11px;" type="button" id="btnDatatableFilterNotCreateOrder" data-toggle="tab" class="btn btn-info">Chưa tạo đơn hàng</button>
                                                <button style=" font-size: 11px;" type="button" id="btnDatatableFilterCreatingOrder" data-toggle="tab" class="btn btn-info">Đang tạo đơn hàng</button>
                                                <button style=" font-size: 11px;" type="button" id="btnDatatableFilterCreateOrder" data-toggle="tab" class="btn btn-info">Đã tạo đơn hàng</button>
                                            </div>
                                                <?php render_datatable(array(
                                                    _l('#'),
                                                    _l('Mã đơn hàng'),
                                                    _l('Mã tham chiếu'),
                                                    _l('Khách hàng'),
                                                    _l('_phone'),
                                                    _l('total_amount'),
                                                    _l('total_amount_deposit'),
                                                    _l('total_amount_payment'), 
                                                    _l('total_amount_left'),                                      
                                                    _l('salers'),
                                                    _l('Người tạo'),
                                                    _l('Trạng thái'),
                                                    _l('Được duyệt bởi'),
                                                    _l('Ngày tạo'),
                                                    _l('options')
                                                ),'sale_orders'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            }
                            ?>
                            <?php
                            if (has_permission('so', '', 'view') || has_permission('so', '', 'view_own')) { 
                            ?>
                            <div role="tabpanel" class="tab-pane <?=($activeSO? 'active' : '')?>" id="sale_SO">
                                <div class="row">
                                    <div class="col-md-12">
                                        <a href="<?=admin_url('sales/sale_detail')?>" class="btn btn-info pull-left display-block mbot15"><?php echo _l('add_sale_order_'); ?></a>
                                        <div class="clearfix"></div>
                                        <div class="panel_s">
                                            <div class="panel-body">
                                            <input type="hidden" id="filterStatusSale" value="" />
                                            <div data-toggle="btn" class="btn-group mbot15">
                                                <button style=" font-size: 11px;" type="button" id="btnDatatableFilterSaleAll" data-toggle="tab" class="btn btn-info active">Tất cả</button>
                                                <button style=" font-size: 11px;" type="button" id="btnDatatableFilterSaleNotApproval" data-toggle="tab" class="btn btn-info">Chưa duyệt</button>
                                                <button style=" font-size: 11px;" type="button" id="btnDatatableFilterSaleApproval" data-toggle="tab" class="btn btn-info">Đã duyệt</button>
                                                <button style=" font-size: 11px;" type="button" id="btnDatatableFilterSaleNotCreateExport" data-toggle="tab" class="btn btn-info">Chưa tạo phiếu xuất</button>
                                                <button style=" font-size: 11px;" type="button" id="btnDatatableFilterSaleCreatingExport" data-toggle="tab" class="btn btn-info">Đang tạo phiếu xuất</button>
                                                <button style=" font-size: 11px;" type="button" id="btnDatatableFilterSaleCreateExport" data-toggle="tab" class="btn btn-info">Đã tạo phiếu xuất</button>
                                            </div>
                                                <?php render_datatable(array(
                                                    _l('#'),
                                                    _l('Mã đơn hàng'),
                                                    _l('Mã tham chiếu'),
                                                    _l('Khách hàng'),
                                                    _l('_phone'),
                                                    _l('total_amount'),
                                                    // _l('total_amount_deposit'),
                                                    _l('total_amount_payment'), 
                                                    _l('total_amount_left'),                                      
                                                    _l('salers'),
                                                    _l('Người tạo'),
                                                    _l('Trạng thái'),
                                                    _l('Được duyệt bởi'),
                                                    _l('Ngày tạo'),
                                                    _l('options')
                                                ),'sales'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php
                            }
                            ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/sales/sales_js'); ?>
<div id="receipt_data"></div>
<?php init_tail(); ?>
<script type="text/javascript">
    let tableSale,tableSaleOrder;
    $(function(){
        
         $('[data-toggle="btn"] .btn').on('click', function(){
            var $this = $(this);
            $this.parent().find('.active').removeClass('active');
            $this.addClass('active');
        });
        $('#btnDatatableFilterSaleAll').click(() => {
            $('#filterStatusSale').val('');
            $('#filterStatusSale').change();
        });
        $('#btnDatatableFilterSaleNotApproval').click(() => {
            $('#filterStatusSale').val(1);
            $('#filterStatusSale').change();
        });
        $('#btnDatatableFilterSaleApproval').click(() => {
            $('#filterStatusSale').val(2);
            $('#filterStatusSale').change();
        });
        $('#btnDatatableFilterSaleNotCreateExport').click(() => {
            $('#filterStatusSale').val(3);
            $('#filterStatusSale').change();
        });
        $('#btnDatatableFilterSaleCreatingExport').click(() => {
            $('#filterStatusSale').val(4);
            $('#filterStatusSale').change();
        });
        $('#btnDatatableFilterSaleCreateExport').click(() => {
            $('#filterStatusSale').val(5);
            $('#filterStatusSale').change();
        });
        var filterList = {
            'filterStatus' : '[id="filterStatusSale"]',
        };
        var headers_sales = $('.table-sales').find('th');
        var not_sortable_sales = (headers_sales.length - 1);
        tableSale = initDataTableFixedHeader('.table-sales', window.location.href, 
        [not_sortable_sales], [not_sortable_sales],
        filterList, 
        [1,'DESC'],
        {
            leftColumns: 4,
            rightColumns: 1
        });
        $.each(filterList, (filterIndex, filterItem) => {
            $('input' + filterItem).on('change', () => {
                tableSale.ajax.reload();
            });
        });
        $('.dataTables_scrollHeadInner').removeClass('table');
        $('.dataTables_scrollBody').removeClass('table');

        $('[data-toggle="btns"] .btn').on('click', function(){
            var $this = $(this);
            $this.parent().find('.active').removeClass('active');
            $this.addClass('active');
        });
        $('#btnDatatableFilterAll').click(() => {
            $('#filterStatus').val('');
            $('#filterStatus').change();
        });
        $('#btnDatatableFilterNotApproval').click(() => {
            $('#filterStatus').val(1);
            $('#filterStatus').change();
        });
        $('#btnDatatableFilterApproval').click(() => {
            $('#filterStatus').val(2);
            $('#filterStatus').change();
        });
        $('#btnDatatableFilterNotCreateOrder').click(() => {
            $('#filterStatus').val(3);
            $('#filterStatus').change();
        });
        $('#btnDatatableFilterCreatingOrder').click(() => {
            $('#filterStatus').val(4);
            $('#filterStatus').change();
        });
        $('#btnDatatableFilterCreateOrder').click(() => {
            $('#filterStatus').val(5);
            $('#filterStatus').change();
        });
        var filterList = {
            'filterStatus' : '[id="filterStatus"]',
        };
        var headers_sale_orders = $('.table-sale_orders').find('th');
        var not_sortable_sale_orders = (headers_sale_orders.length - 1);
        tableSaleOrder = initDataTableFixedHeader('.table-sale_orders', admin_url+'sale_orders/list_sale_orders', 
        [not_sortable_sale_orders], [not_sortable_sale_orders],
        filterList,
        [1,'DESC'],
        {
            leftColumns: 4,
            rightColumns: 1
        });
        $(document).on('click', '[href="#sale_PO"]', function() {
            tableSaleOrder
            .columns.adjust()
            .fixedColumns().relayout();
            // tableSaleOrder.columns.adjust().draw();
        });
        $(document).on('click', '[href="#sale_SO"]', function() {
            tableSale.ajax
            .columns.adjust()
            .fixedColumns().relayout();
            // tableSale.columns.adjust().draw();
        });
        $.each(filterList, (filterIndex, filterItem) => {
            $('input' + filterItem).on('change', () => {
                tableSaleOrder.ajax.reload();
            });
        });
    });

    function var_status(status,id)
    {
        // alert("<?=admin_url()?>sales/update_status")
        dataString={id:id,status:status};
        jQuery.ajax({
            type: "post",
            url:"<?=admin_url()?>sales/update_status",
            data: dataString,
            cache: false,
            success: function (response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    tableSale.ajax.reload();
                    alert_float('success', response.message);
                }
                return false;
            }
        });

    }
    function var_status_order(status,id)
    {
        // alert("<?=admin_url()?>sales/update_status")
        dataString={id:id,status:status};
        jQuery.ajax({
            type: "post",
            url:"<?=admin_url()?>sale_orders/update_status",
            data: dataString,
            cache: false,
            success: function (response) {
                response = JSON.parse(response);
                if (response.success == true) {
                    tableSaleOrder.ajax.reload();
                    alert_float('success', response.message);
                }
                return false;
            }
        });

    }
//     function view_init_adjustment(id)
//     {
//         $('#adjustment_type').modal('show');
//         $('.add-title').addClass('hide');
//         jQuery.ajax({
//             type: "post",
//             url:admin_url+"units/get_row_unit/"+id,
//             data: '',
//             cache: false,
//             success: function (data) {
//                 var json = JSON.parse(data);
// //                if($data!="")
//                 {
//                     $('#unit').val(json.unit);
//                     jQuery('#id_type').prop('action',admin_url+'units/update_unit/'+id);
//                 }
//             }
//         });
//     }
    $('body').on('click', '.delete-remind', function() {
        var r = confirm(confirm_action_prompt);
        
        if (r == false) {
            return false;
        } else {
            $.get($(this).attr('href'), function(response) {
                alert_float(response.alert_type, response.message);
                // TA Custom
                tableSale.ajax.reload();
                tableSaleOrder.ajax.reload();
            }, 'json');
        }
        return false;
    });
</script>
<style type="text/css">
 .table-sale_orders tr td:nth-child(4){
    max-width: 200px;
    white-space: inherit;
    min-width: 200px;
  }
  .table-sales tr td:nth-child(4){
    max-width: 200px;
    white-space: inherit;
    min-width: 200px;
  }
</style>


