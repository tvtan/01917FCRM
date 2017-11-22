<?php init_head(); ?>
<style>
   .dt-buttons .buttons-collection{
       display: none!important;
   }
    .lb-nav.lb-prev{display: none;}
   .lb-nav.lb-next{display: none;}
   .table-invoice-items tr td:nth-child(4){
    max-width: 300px;
    white-space: inherit;
  }
  .table-invoice-items tr td:nth-child(6){
    max-width: 200px;
    white-space: inherit;
  }
  .table-invoice-items tr td:nth-child(7){
    max-width: 200px;
    white-space: inherit;
  }
  .table-responsive{
    overflow-x: hidden !important;
  }
</style>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <?php if(has_permission('items','','create')){ ?>
        <div class="panel_s">
          <div class="panel-body _buttons">
            
            <a href="<?php echo admin_url('invoice_items/item') ?>" class="btn btn-info pull-left"><?php echo _l('new_invoice_item'); ?></a>
            <a href="#" class="btn btn-info pull-left mleft5" data-toggle="modal" data-target="#groups"><?php echo _l('item_groups'); ?></a>
            <?php if(is_admin()){ ?>
            <a href="<?php echo admin_url('invoice_items/import'); ?>" class="btn btn-info pull-left mleft5 display-block">
            <?php echo _l('import_items'); ?>
            </a>
            <?php } ?>
            
          </div>

        </div>
        <?php } ?>
        <div class="panel_s">
            <div class="panel-body">
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                    <?php 
                        echo render_select('category_1', $category_1, array('id', 'category'), 'Danh mục cấp 1');
                    ?>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                    <?php 
                        echo render_select('category_2', array(), array('id', 'category'), 'Danh mục cấp 2');
                    ?>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                    <?php 
                        echo render_select('category_3', array(), array('id', 'category'), 'Danh mục cấp 3');
                    ?>
                </div>
                <div class="col-xs-3 col-sm-3 col-md-3 col-lg-3">
                    <?php 
                        echo render_select('category_4', array(), array('id', 'category'), 'Danh mục cấp 4');
                    ?>
                </div>
            </div>
        </div>
        <div class="panel_s">
          <div class="panel-body">

            <div class="clearfix"></div>
              <a href="<?php echo admin_url('invoice_items/exportexcel'); ?>" class="btn btn-default btn-default-dt-options"><?php echo _l('Xuất Excel'); ?></a>
              <div class="table-responsive">
                    <?php render_datatable(array(
                      "STT",
                      _l('item_avatar'),
                      _l('item_code'),
                      _l('item_name'),
                      _l('item_short_name'),
                      _l('item_description'),
                      _l('product_features'),
                      _l('size'),
                      _l('specification'),
                      _l('weight'),
                      _l('item_price'),
                      _l('item_unit'),
                      _l('item_group_id'),
                      _l('minimum_quantity'),
                      _l('maximum_quantity'),
                      _l('actions'),
                      ),
                      'invoice-items'); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <?php $this->load->view('admin/invoice_items/item'); ?>
  
  <div class="modal fade" id="groups" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h4 class="modal-title" id="myModalLabel">
            <?php echo _l('item_groups'); ?>
          </h4>
        </div>
        <div class="modal-body">
          <?php if(has_permission('items','','create')){ ?>
          <div class="input-group">
            <input type="text" name="item_group_name" id="item_group_name" class="form-control" placeholder="<?php echo _l('item_group_name'); ?>">
            <span class="input-group-btn">
              <button class="btn btn-info p7" type="button" id="new-item-group-insert"><?php echo _l('new_item_group'); ?></button>
            </span>
          </div>
          <hr />
          <?php } ?>
          <div class="row">
           <div class="container-fluid">
            <table class="table table-striped dt-table table-items-groups" data-order-col="0" data-order-type="asc">
              <thead>
                <tr>
                  <th><?php echo _l('item_group_name'); ?></th>
                  <th><?php echo _l('options'); ?></th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($items_groups as $group){ ?>
                <tr data-group-row-id="<?php echo $group['id']; ?>">
                  <td data-order="<?php echo $group['name']; ?>">
                    <span class="group_name_plain_text"><?php echo $group['name']; ?></span>
                    <div class="group_edit hide">
                     <div class="input-group">
                      <input type="text" class="form-control">
                      <span class="input-group-btn">
                        <button class="btn btn-info p7 update-item-group" type="button"><?php echo _l('submit'); ?></button>
                      </span>
                    </div>
                  </div>
                </td>
                <td align="right">
                  <?php if(has_permission('items','','edit')){ ?><button type="button" class="btn btn-default btn-icon edit-item-group"><i class="fa fa-pencil-square-o"></i></button><?php } ?>
                  <?php if(has_permission('items','','delete')){ ?><a href="<?php echo admin_url('invoice_items/delete_group/'.$group['id']); ?>" class="btn btn-danger btn-icon delete-item-group _delete"><i class="fa fa-remove"></i></a><?php } ?></td>
                </tr>
                <?php } ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
<script src="https://cdn.datatables.net/fixedcolumns/3.2.3/js/dataTables.fixedColumns.min.js"></script>
<script>
    $(document).ready(()=>{
        $('.table-responsive').on('mousedown', (e) => {
//            alert('123');
        });

    });
  $(function(){
    var filterList = {
        "category_1" : "[name='category_1']",
        "category_2" : "[name='category_2']",
        "category_3" : "[name='category_3']",
        "category_4" : "[name='category_4']",
    };

    initDataTableFixedHeader('.table-invoice-items', window.location.href, [4], [4], filterList,[0,'DESC'], 
    {
        leftColumns: 4,
        rightColumns: 1
    });
    // $('.table-invoice-items').DataTable().destroy();
            
    $(document).ready(()=>{

        $('#category_1,#category_2,#category_3,#category_4').on('change', (e) => {
            var id = $(e.currentTarget).val();
            $(e.currentTarget).parents('.col-xs-3').nextAll().find('select[name^="category_"] option:gt(0)').remove();
            $(e.currentTarget).parents('.col-xs-3').nextAll().find('select[name^="category_"]').selectpicker('refresh');
            if(typeof(id) == 'undefined' || id == 0) return;
            jQuery.ajax({
                type: "post",
                url:admin_url+"categories/get_childs/"+id,
                data: '',
                cache: false,
                success: function (data) {
                    data = JSON.parse(data);
                    data.map(o => 
                            $(e.currentTarget).parents('.col-xs-3').next().find('select[name^="category_"]').append('<option value='+o.id+'>'+o.category+'</option>')
                    );
                    $(e.currentTarget).parents('.col-xs-3').next().find('select[name^="category_"]').selectpicker('refresh');
                },
            });
        });
    $.each(filterList, (key,value)=>{
        $('select' + value).on('change', () => {
          $('.table-invoice-items').DataTable().destroy();
            $('.table-invoice-items').DataTable().ajax.reload();
        });
    });
    

    });
    if(get_url_param('groups_modal')){
      // Set time out user to see the message
      setTimeout(function(){
       $('#groups').modal('show');
     },1000);
    }
    if(get_url_param('landtype_modal')){
      // Set time out user to see the message
      setTimeout(function(){
       $('#landtype').modal('show');
     },1000);
    }
    $('#new-item-landtype-insert').on('click',function(){
      var name = $('#name').val();
      var parentid = $('#parentid').val();
      // alert(name+'-'+parentid);
      // var data=array[name,parentid];
      if(name != ''){
        $.post(admin_url+'invoice_items/add_landtype',{name:name,parentid:parentid}).done(function(){
         window.location.href = admin_url+'invoice_items?landtype_modal=true';
       });
      }
    });

    $('#new-item-group-insert').on('click',function(){
      var group_name = $('#item_group_name').val();
      if(group_name != ''){
        $.post(admin_url+'invoice_items/add_group',{name:group_name}).done(function(){
         window.location.href = admin_url+'invoice_items?groups_modal=true';
       });
      }
    });

    $('body').on('click','.edit-item-group',function(){
      var tr = $(this).parents('tr'),
      group_id = tr.attr('data-group-row-id');
      tr.find('.group_name_plain_text').toggleClass('hide');
      tr.find('.group_edit').toggleClass('hide');
      tr.find('.group_edit input').val(tr.find('.group_name_plain_text').text());
    });

    $('body').on('click','.update-item-group',function(){
      var tr = $(this).parents('tr');
      var group_id = tr.attr('data-group-row-id');
      name = tr.find('.group_edit input').val();
      if(name != ''){
        $.post(admin_url+'invoice_items/update_group/'+group_id,{name:name}).done(function(){
         window.location.href = admin_url+'invoice_items';
       });
      }
    });
    $('body').on('click','.update-item-landtype',function(){
      var tr = $(this).parents('tr');
      var id = tr.attr('data-group-row-id');
      namee = tr.find('.group_edit input').val();
      if(namee != ''){
        $.post(admin_url+'invoice_items/update_landtype/'+id,{name:namee}).done(function(){
         window.location.href = admin_url+'invoice_items';
         // alert(window.location.href);
       });
      }
    });
  });
</script>

<script type="text/javascript">
    $(document).ready(function(){
        $('table thead').find('th:nth-child(6)').css('min-width','200px');
        $('table thead').find('th:nth-child(7)').css('min-width','200px');
        $('table tbody').css('cursor','pointer');
        var i = 0;
        $('table tbody').mousedown(function(e){
            e.preventDefault();
            $(document).mousemove(function(b){
                var scroll_table_left=$('.table-responsive').scrollLeft();
                pagescoll=((b.pageX)- (e.pageX))-20;
                console.log(pagescoll);
                $('.table-responsive').scrollLeft((scroll_table_left)+((pagescoll)));
            })
        });
        $(document).mouseup(function(e){
            $(document).unbind('mousemove');
        });
    });
//    $(document).ready(function(){
//        var i = 0;
//        $('th').mousedown(function(e){
//            e.preventDefault();
//            $(document).mousemove(function(b){
////                console.log(e.target).prop('style');
//                console.log(b.pageX+2);
////                $('.table-responsive').scrollLeft((b.pageX- e.pageX)+2);
//                $(this).find(e.target).css("min-width",(b.pageX- e.pageX)+2)
//            })
//        });
//        $(document).mouseup(function(e){
//            $(document).unbind('mousemove');
//        });
//    });
</script>
</body>
</html>
