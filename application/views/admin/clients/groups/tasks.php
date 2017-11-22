  <h4 class="no-mtop bold"><?php echo _l('Chăm sóc khách hàng'); ?></h4>
</hr>
<div class="row">
		<div class="col-md-12">
		        <div class="clearfix"></div>
		        <div class="panel_s">
		            <?php render_datatable(array(
		                _l('create_by'),
		                _l('start_date'),
		                _l('note'),
		                _l('options')
		            ),'care_of'); ?>
		        </div>
		    </div>
		</div>
</div>
