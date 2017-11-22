<?php if(isset($client)){ ?>
<h4 class="no-mtop bold"><?php echo _l('proposals'); ?></h4>
<hr />

<?php if(has_permission('proposals','','create')){ ?>
	<!-- <a href="<?php echo admin_url('proposals/proposal?rel_type=customer&rel_id='.$client->userid); ?>" class="btn btn-info mbot25<?php if($client->active == 0){echo ' disabled';} ?>"><?php echo _l('new_proposal'); ?></a> -->
<?php } ?>
<?php if(total_rows('tblproposals',array('rel_type'=>'customer','rel_id'=>$client->userid))> 0 && (has_permission('proposals','','create') || has_permission('proposals','','edit'))){ ?>
<!-- <a href="#" class="btn btn-info mbot25" data-toggle="modal" data-target="#sync_data_proposal_data"><?php echo _l('sync_data'); ?></a> -->
<?php $this->load->view('admin/proposals/sync_data',array('related'=>$client,'rel_id'=>$client->userid,'rel_type'=>'customer')); ?>
<?php } ?>
<?php
$table_data = array(	
 	_l('Mã phiếu báo giá'),
    _l('total_amount'),
    _l('Người tạo'),
    _l('Trạng thái'),
    _l('Được duyệt bởi'),
    _l('Ngày tạo'),
    _l('options')
 );
render_datatable($table_data,'proposals-client-profile');
?>
<?php } ?>
