
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                    <?php echo form_open($this->uri->uri_string()); ?>
                    <?php echo form_hidden('download_sample','true'); ?>
                    <button type="submit" class="btn btn-success">Download Sample</button>
                    <hr />
                    <?php echo form_close(); ?>
                       <?php $max_input = ini_get('max_input_vars');
                       if(($max_input>0 && isset($total_rows_post) && $total_rows_post >= $max_input)){ ?>
                        <div class="alert alert-warning">
                            Your hosting provider has PHP setting <b>max_input_vars</b> at <?php echo $max_input;?>.<br/>
                            Ask your hosting provider to increase the <b>max_input_vars</b> setting to <?php echo $total_rows_post;?> or higher or import less rows.
                        </div>
                        <?php } ?>
                        <?php

                            if(!isset($simulate) > 0) { ?>
                        <p>
                        Tài khoản không được trùng với danh mục đã có.
                        </p>
                        
                        </div>
                        <?php } ?>

                        
                        <div class="row">
                            <div class="col-md-4">
                                <?php if(isset($row_imported)) : ?>
                                <div class="alert alert-success">
                                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                    <strong><?php echo _l('category_import_success') . $row_imported; ?></strong>
                                </div>
                                <?php endif; ?>
                                <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'import_form')) ;?>
                                <?php echo form_hidden('leads_import','true'); ?>
                                <?php echo render_input('file_import','import_choose_file','','file'); ?>
                                <div class="form-group">
                                    <button type="button" class="btn btn-info import btn-import-submit"><?php echo _l('import'); ?></button>
                                </div>
                                <?php echo form_close(); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script src="<?php echo base_url('assets/plugins/jquery-validation/additional-methods.min.js'); ?>"></script>
<script>
    _validate_form($('#import_form'),{file_csv:{required:true,extension: "csv"},source:'required',status:'required'});
    $(function(){
     $('.btn-import-submit').on('click',function(){
       if($(this).hasClass('simulate')){
         $('#import_form').append(hidden_input('simulate',true));
       }
       $('#import_form').submit();
     });
    })
</script>
</body>
</html>