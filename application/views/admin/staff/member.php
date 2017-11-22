<?php init_head(); ?>
<style type="text/css">
.table-striped>tbody>tr:nth-of-type(odd) {
    background-color: #f5f5f5;
}
</style>
<div id="wrapper">
 <div class="content">
  <div class="row">
   <?php if(isset($member)){ ?>
   <div class="col-md-12">
    <div class="panel_s">
     <div class="panel-body no-padding-bottom">
      <?php $this->load->view('admin/staff/stats'); ?>
    </div>
  </div>
</div>
<div class="member">
  <?php echo form_hidden('isedit'); ?>
  <?php echo form_hidden('memberid',$member->staffid); ?>
</div>
<?php } ?>
<?php if(isset($member)){ ?>
<div class="col-md-12">
  <div class="panel_s">
   <div class="panel-body">
     <h4 class="no-margin"><?php echo $member->firstname . ' ' . $member->lastname; ?> <a href="#" onclick="small_table_full_view(); return false;" data-placement="left" data-toggle="tooltip" data-title="<?php echo _l('toggle_full_view'); ?>" class="toggle_view pull-right">
      <i class="fa fa-expand"></i></a></h4>
    </div>
  </div>
</div>
<?php } ?>
<?php echo form_open_multipart($this->uri->uri_string(),array('class'=>'staff-form','autocomplete'=>'off')); ?>
<div class="col-md-7" id="small-table">
  <div class="panel_s">
   <div class="panel-body">

     <ul class="nav nav-tabs" role="tablist">
      <li role="presentation" class="active">
       <a href="#tab_staff_profile" aria-controls="tab_staff_profile" role="tab" data-toggle="tab">
        <?php echo _l('staff_profile_string'); ?>
      </a>
    </li>
     <?php
     $_userid = get_staff_user_id();
     if($member->staffid!=$_userid) {
     ?>
        <li role="presentation">
         <a href="#tab_staff_permissions" aria-controls="tab_staff_permissions" role="tab" data-toggle="tab">
          <?php echo _l('staff_add_edit_permissions'); ?>
        </a>
      </li>
     <?php }?>
</ul>
<div class="tab-content">
  <div role="tabpanel" class="tab-pane active" id="tab_staff_profile">

   <?php if((isset($member) && $member->profile_image == NULL) || !isset($member)){ ?>
   <div class="form-group">
    <label for="profile_image" class="profile-image"><?php echo _l('staff_edit_profile_image'); ?></label>
    <input type="file" name="profile_image" class="form-control" id="profile_image">
  </div>
  <?php } ?>
  <?php if(isset($member) && $member->profile_image != NULL){ ?>
  <div class="form-group">
    <div class="row">
     <div class="col-md-9">
      <?php echo staff_profile_image($member->staffid,array('img','img-responsive','staff-profile-image-thumb'),'thumb'); ?>
    </div>
    <div class="col-md-3 text-right">
      <a href="<?php echo admin_url('staff/remove_staff_profile_image/'.$member->staffid); ?>"><i class="fa fa-remove"></i></a>
    </div>
  </div>
</div>
<?php } ?>

<?php $value = (isset($member) ? $member->staff_code : get_option('prefix_staff').$maxid) ?>
<?php $attrs = array('readonly'=>true); ?>
<?php echo render_input('staff_code','Mã nhân viên',$value,'text',$attrs); ?>

<?php $value = (isset($member) ? $member->fullname : ''); ?>
<?php $attrs = (isset($member) ? array() : array('autofocus'=>true)); ?>
<?php echo render_input('fullname','staff_add_edit_fulltname',$value,'text',$attrs); ?>

<?php $value = (isset($member) ? $member->email : ''); ?>
<?php echo render_input('email','staff_add_edit_email',$value,'email',array('autocomplete'=>'off')); ?>

<?php $value = (isset($member) ? $member->bank_account : ''); ?>
<?php echo render_input('bank_account','Tài khoản ngân hàng',$value); ?>

<?php $value = (isset($member) ? $member->internal_phone : ''); ?>
<?php echo render_input('internal_phone','Số máy nội bộ',$value); ?>

<?php $value = (isset($member) ? _d($member->date_work) : _d(date('Y-m-d'))); ?>
<?php echo render_date_input('date_work','Ngày bắt đầu làm việc',$value); ?>

<?php
$selected = '';
$genders=array(array('id'=>'1','place_work'=>'Trụ sở'),array('id'=>'2','place_work'=>'Chi nhánh'));
$selected=(isset($member) ? $member->palce_work : '');
?>
<?php echo render_select('place_work',$genders,array('id','place_work'),'Nơi công tác',$member->gender); ?>

<?php
$selected = '';
$genders=array(array('id'=>'1','gender'=>'Nam'),array('id'=>'2','gender'=>'Nữ'));
$selected=(isset($member) ? $member->gender : '');
?>
 <?php echo render_select('gender',$genders,array('id','gender'),'gender',$member->gender); ?>

<?php $value = (isset($member) ? _d($member->date_birth) : _d(date('Y-m-d'))); ?>
<?php echo render_date_input('date_birth','Ngày sinh',$value); ?>

<?php $value = (isset($member) ? $member->place_birth : ''); ?>
<?php echo render_input('place_birth','Nơi sinh',$value); ?>

<?php $value = (isset($member) ? $member->permanent_residence : ''); ?>
<?php echo render_input('permanent_residence','Hộ khẩu thường trú',$value); ?>

<?php $value = (isset($member) ? $member->current_address : ''); ?>
<?php echo render_input('current_address','Nơi ở hiện nay',$value); ?>

<?php $value = (isset($member) ? $member->passport_id : ''); ?>
<?php echo render_input('passport_id','CMND',$value); ?>

<?php $value = (isset($member) ? $member->issued_by : ''); ?>
<?php echo render_input('issued_by','Nơi cấp',$value); ?>

<?php $value = (isset($member) ? _d($member->issued_on) : _d(date('Y-m-d'))); ?>
<?php echo render_date_input('issued_on','Ngày cấp',$value); ?>

<?php $value = (isset($member) ?$member->hobbies : ''); ?>
<?php echo render_input('hobbies','Sở thích',$value); ?>

<div class="col-md-6">
  <div class="form-group">
   <label for="facebook" class="control-label"><?php echo _l('Chiều cao'); ?></label>
   <input type="text" class="form-control" name="height" value="<?php if(isset($member)){echo $member->height;} ?>">
  </div>
</div>

<div class="col-md-6">
  <div class="form-group">
   <label for="facebook" class="control-label"><?php echo _l('Cân nặng'); ?></label>
   <input type="text" class="form-control" name="weight" value="<?php if(isset($member)){echo $member->weight;} ?>">
  </div>
</div>
<?php $value = (isset($member) ? $member->marial_status : ''); ?>
<div class="form-group">
   <label for="marial_status"  class="control-label"><?php echo _l('Tình trạng hôn nhân'); ?></label><br>
   <div class="radio radio-primary radio-inline">
      <input type="radio" name="marial_status" id="single" value="single" checked>
      <label for="single">Độc thân</label>
    </div>
    <div class="radio radio-primary radio-inline">
      <input type="radio" name="marial_status" id="married" value="married" <?php if($value=='married') echo "checked"; ?>   >
      <label for="single">Đã lập gia đình</label>
    </div>
    <div class="radio radio-primary radio-inline">
      <input type="radio" name="marial_status" id="divorced" value="divorced" <?php if($value=='divorced') echo "checked"; ?>  >
      <label for="single">Ly hôn</label>
    </div>
</div>

<?php $value = (isset($member) ? $member->emergency_contact : ''); ?>
<?php echo render_input('emergency_contact','Liên hệ khẩn cấp',$value); ?>

<div class="form-group">
 <label for="hourly_rate"><?php echo _l('Lương tháng'); ?></label>
 <div class="input-group">
  <input type="number" name="salary" value="<?php if(isset($member)){echo $member->salary;} else {echo 0;} ?>" id="salary" class="form-control">
  <span class="input-group-addon">
   <?php echo $base_currency->symbol; ?>
 </span>
</div>
</div>

<?php $value = (isset($member) ? $member->phonenumber : ''); ?>
<?php echo render_input('phonenumber','staff_add_edit_phonenumber',$value); ?>


<?php $value = (isset($member) ? $member->email_marketing : ''); ?>
<?php echo render_input('email_marketing','email_marketing',$value); ?>
<?php $value = (isset($member) ? $member->password_email_marketing : ''); ?>
<?php echo render_input('password_email_marketing','password_email_marketing',$value,'password'); ?>

<!-- <div class="form-group">
 <label for="facebook" class="control-label"><i class="fa fa-facebook"></i> <?php echo _l('staff_add_edit_facebook'); ?></label>
 <input type="text" class="form-control" name="facebook" value="<?php if(isset($member)){echo $member->facebook;} ?>">
</div>
<div class="form-group">
 <label for="linkedin" class="control-label"><i class="fa fa-linkedin"></i> <?php echo _l('staff_add_edit_linkedin'); ?></label>
 <input type="text" class="form-control" name="linkedin" value="<?php if(isset($member)){echo $member->linkedin;} ?>">
</div>
<div class="form-group">
 <label for="skype" class="control-label"><i class="fa fa-skype"></i> <?php echo _l('staff_add_edit_skype'); ?></label>
 <input type="text" class="form-control" name="skype" value="<?php if(isset($member)){echo $member->skype;} ?>">
</div>
<div class="form-group">
 <label for="default_language" class="control-label"><?php echo _l('localization_default_language'); ?></label>
 <select name="default_language" data-live-search="true" id="default_language" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
  <option value=""><?php echo _l('system_default_string'); ?></option>
  <?php foreach(list_folders(APPPATH .'language') as $language){
   $selected = '';
   if(isset($member)){
     if($member->default_language == $language){
       $selected = 'selected';
     }
   }
   ?>
   <option value="<?php echo $language; ?>" <?php echo $selected; ?>><?php echo ucfirst($language); ?></option>
   <?php } ?>
 </select>
</div> -->
<!-- <i class="fa fa-question-circle" data-toggle="tooltip" data-title="<?php echo _l('staff_email_signature_help'); ?>"></i>
<?php $value = (isset($member) ? $member->email_signature : ''); ?>
<?php echo render_textarea('email_signature','settings_email_signature',$value); ?>
<div class="form-group">
 <label for="direction"><?php echo _l('document_direction'); ?></label>
 <select class="selectpicker" data-none-selected-text="<?php echo _l('system_default_string'); ?>" data-width="100%" name="direction" id="direction">
  <option value="" <?php if(isset($member) && empty($member->direction)){echo 'selected';} ?>></option>
  <option value="ltr" <?php if(isset($member) && $member->direction == 'ltr'){echo 'selected';} ?>>LTR</option>
  <option value="rtl" <?php if(isset($member) && $member->direction == 'rtl'){echo 'selected';} ?>>RTL</option>
</select>
</div> -->
<div class="form-group">
 <?php if(count($departments) > 0){ ?>
 <label for="departments"><?php echo _l('staff_add_edit_departments'); ?></label>
 <?php } ?>
 <?php foreach($departments as $department){ ?>
 <div class="checkbox checkbox-primary">
  <?php
  $checked = '';
  if(isset($member)){
    foreach ($staff_departments as $staff_department) {
      if($staff_department['departmentid'] == $department['departmentid']){
        $checked = ' checked';
      }
    }
  }
  ?>
  <input type="checkbox" id="dep_<?php echo $department['departmentid']; ?>" name="departments[]" value="<?php echo $department['departmentid']; ?>"<?php echo $checked; ?>>
  <label for="dep_<?php echo $department['departmentid']; ?>"><?php echo $department['name']; ?></label>
</div>
<?php } ?>
</div>

<?php $rel_id = (isset($member) ? $member->staffid : false); ?>
<?php echo render_custom_fields('staff',$rel_id); ?>
<?php if (is_admin()){ ?>
<div class="row">
  <div class="col-md-12">
   <hr />
   <div class="checkbox checkbox-primary">
    <?php
//    $isadmin = '';
//    if(isset($member)) {
//      if($member->staffid == get_staff_user_id() || is_admin($member->staffid)){
//        $isadmin = ' checked';
//      }
//    }
    ?>
<!--    <input type="checkbox" name="administrator" id="administrator" --><?php //echo $isadmin; ?><!-->
<!--    <label for="administrator">--><?php //echo _l('staff_add_edit_administrator'); ?><!--</label>-->
  </div>
</div>
    
</div>
<?php } ?>
<?php if(!isset($member)){ ?>
<?php if(total_rows('tblemailtemplates',array('slug'=>'new-staff-created','active'=>0)) == 0){ ?>
<div class="checkbox checkbox-primary">
 <input type="checkbox" name="send_welcome_email" id="send_welcome_email" checked>
 <label for="send_welcome_email"><?php echo _l('staff_send_welcome_email'); ?></label>
</div>
<?php } ?>
<?php } ?>
<div class="clearfix form-group"></div>
<label for="password" class="control-label"><?php echo _l('staff_add_edit_password'); ?></label>
<div class="input-group">
 <input type="password" class="form-control password" name="password" autocomplete="off">
 <span class="input-group-addon">
  <a href="#password" class="show_password" onclick="showPassword('password'); return false;"><i class="fa fa-eye"></i></a>
</span>
<span class="input-group-addon">
  <a href="#" class="generate_password" onclick="generatePassword(this);return false;"><i class="fa fa-refresh"></i></a>
</span>
</div>
<?php if(isset($member)){ ?>
<p class="text-muted"><?php echo _l('staff_add_edit_password_note'); ?></p>
<?php if($member->last_password_change != NULL){ ?>
<?php echo _l('staff_add_edit_password_last_changed'); ?>: <?php echo time_ago($member->last_password_change); ?>
<?php } } ?>
</div>

    <?php
    $_userid = get_staff_user_id();
    if($member->staffid!=$_userid) {
    ?>
        
        <div role="tabpanel" class="tab-pane" id="tab_staff_permissions">
              <div class="checkbox checkbox-primary">
                <?php
                $checked = '';
                if(isset($member)) {
                  if($member->is_not_staff == 1){
                    $checked = ' checked';
                  }
                }
                ?>
                <input type="checkbox" value="1" name="is_not_staff" id="is_not_staff" <?php echo $checked; ?>>
                <label for="is_not_staff" data-toggle="tooltip"><?php echo _l('is_not_staff_member'); ?></label>
              </div>
              <div class="form-group">
                <div class="radio radio-primary radio-inline">
                <input type="radio" name="rule" value="2" onchange="review_nhanvien(this.value)" <?php if($member->rule==2) echo "checked";?>>
                <label for="administrator"><?php echo _l('rule_admin'); ?></label>
                </div>
                <div class="radio radio-primary radio-inline">
                <input type="radio" name="rule" value="3" onchange="review_nhanvien(this.value)" <?php if($member->rule==3) echo "checked";?>>
                <label for="administrator"><?php echo _l('rule_staff'); ?></label>
                </div>
                <div class="radio radio-primary radio-inline">
                <input type="radio" name="rule" value="4" onchange="review_nhanvien(this.value)" <?php if($member->rule==4) echo "checked";?>>
                <label for="administrator"><?php echo _l('rule_not_staff'); ?></label>
                </div>
            </div>

            <?php $selected = (isset($member) ? $member->position_id : ''); ?>
            <?php echo render_select('position_id',$positions,array('positionid','name'),'Chức vụ',$selected,array(),array()); ?>

              <?php
              do_action('staff_render_permissions');
              $selected = '';
              foreach($roles as $role){
                if(isset($member)){
                  if($member->role == $role['roleid']){
                    $selected = $role['roleid'];
                  }
                } else {
                 $default_staff_role = get_option('default_staff_role');
                 if($default_staff_role == $role['roleid'] ){
                   $selected = $role['roleid'];
                 }
               }
             }
             ?>
             <?php echo render_select('role',$roles,array('roleid','name'),'staff_add_edit_role',$selected,array('onchange'=>'get_code_staff(this.value,'.$member->staffid.')')); ?>
             <?php $selected_staff_manager = (isset($member) ? $member->staff_manager : ''); ?>
            <?php echo render_select('staff_manager[]',$staff_manager,array('staffid','name'),'Quản lý trực tiếp',$selected_staff_manager,array('multiple'=>true),array()); ?>

             <hr />
             <h4 class="font-medium mbot15 bold"><?php echo _l('staff_add_edit_permissions'); ?></h4>
             <div class="table-responsive">
               <table class="table table-bordered roles no-margin table-striped">
                <thead>
                 <tr>
                  <th class="bold"><?php echo _l('permission'); ?></th>
                  <th class="text-center bold"><?php echo _l('permission_view'); ?> (<?php echo _l('permission_global'); ?>)</th>
                  <th class="text-center bold"><?php echo _l('permission_view_own'); ?></th>
                  <th class="text-center bold"><?php echo _l('permission_create'); ?></th>
                  <th class="text-center bold"><?php echo _l('permission_edit'); ?></th>
                  <th class="text-center text-danger bold"><?php echo _l('permission_delete'); ?></th>
                  <th class="text-center text-danger bold">Quyền thuộc tính riêng</th>
                </tr>
              </thead>
              <tbody>
               <?php
               if(isset($member)){
                 $is_admin = is_admin($member->staffid);
               }
               $conditions = get_permission_conditions();
               foreach($permissions as $permission){
                 $permission_condition = $conditions[$permission['shortname']];
                 ?>
                 <tr data-id="<?php echo $permission['permissionid']; ?>">
                  <td class="bold">
                   <?php

                   ?>
                   <?php echo $permission['name']; ?>
                 </td>
                 <td class="text-center">
                   <?php if($permission_condition['view'] == true){
                    $statement = '';
                    if(isset($is_admin) && $is_admin || isset($member) && has_permission($permission['shortname'],$member->staffid,'view_own') ){
                      $statement = 'disabled';
                    }
                    else if(isset($member) && has_permission($permission['shortname'],$member->staffid,'view')){
                      $statement = 'checked';
                    }
                    ?>
                    <?php
                    if(isset($permission_condition['help'])){
                      echo '<i class="fa fa-question-circle text-danger" data-toggle="tooltip" data-title="'.$permission_condition['help'].'"></i>';
                    }
                    ?>
                     <?php if(has_rule($permission['shortname'],$member->staffid,'view')){?>
                            <div class="checkbox">
                             <input type="checkbox" data-can-view <?php echo $statement; ?> name="view[]" value="<?php echo $permission['permissionid']; ?>">
                             <label></label>
                           </div>
                        <?php } ?>
                   <?php } ?>
                 </td>
                 <td class="text-center">
                  <?php if($permission_condition['view_own'] == true){
                   $statement = '';
                   if(isset($is_admin) && $is_admin || isset($member) && has_permission($permission['shortname'],$member->staffid,'view')){
                     $statement = 'disabled';
                   } else if(isset($member) && has_permission($permission['shortname'],$member->staffid,'view_own')){
                     $statement = 'checked';
                   }
                   ?>
                   <?php if(has_rule($permission['shortname'],$member->staffid,'view_own')){?>
                       <div class="checkbox">
                         <input type="checkbox" <?php echo $statement; ?> data-shortname="<?php echo $permission['shortname']; ?>" data-can-view-own name="view_own[]" value="<?php echo $permission['permissionid']; ?>">
                         <label></label>
                       </div>
                   <?php }?>
                   <?php } else if($permission['shortname'] == 'customers'){
                     echo '<i class="fa fa-question-circle mtop15" data-toggle="tooltip" data-title="'._l('permission_customers_based_on_admins').'"></i>';
                   } else if($permission['shortname'] == 'projects'){
                     echo '<i class="fa fa-question-circle mtop25" data-toggle="tooltip" data-title="'._l('permission_projects_based_on_assignee').'"></i>';
                   } else if($permission['shortname'] == 'tasks'){
                     echo '<i class="fa fa-question-circle mtop25" data-toggle="tooltip" data-title="'._l('permission_tasks_based_on_assignee').'"></i>';
                   } else if($permission['shortname'] == 'payments'){
                     echo '<i class="fa fa-question-circle mtop15" data-toggle="tooltip" data-title="'._l('permission_payments_based_on_invoices').'"></i>';
                   } ?>
                 </td>

                 <td  class="text-center">
                  <?php if($permission_condition['create'] == true){
                   $statement = '';
                   if(isset($is_admin) && $is_admin){
                     $statement = 'disabled';
                   } else if(isset($member) && has_permission($permission['shortname'],$member->staffid,'create')){
                     $statement = 'checked';
                   }
                   ?>
                        <?php if(has_rule($permission['shortname'],$member->staffid,'create')){?>
                           <div class="checkbox">
                             <input type="checkbox" data-shortname="<?php echo $permission['shortname']; ?>" data-can-create <?php echo $statement; ?> name="create[]" value="<?php echo $permission['permissionid']; ?>">
                             <label></label>
                           </div>
                        <?php } ?>
                   <?php } ?>
                 </td>
                 <td  class="text-center">
                  <?php if($permission_condition['edit'] == true){
                   $statement = '';
                   if(isset($is_admin) && $is_admin){
                     $statement = 'disabled';
                   } else if(isset($member) && has_permission($permission['shortname'],$member->staffid,'edit')){
                     $statement = 'checked';
                   }
                   ?>
                     <?php if(has_rule($permission['shortname'],$member->staffid,'edit')){?>
                       <div class="checkbox">
                         <input type="checkbox" data-shortname="<?php echo $permission['shortname']; ?>" data-can-edit <?php echo $statement; ?> name="edit[]" value="<?php echo $permission['permissionid']; ?>">
                         <label></label>
                       </div>
                      <?php }?>
                   <?php } ?>
                 </td>
                 <td class="text-center">
                    <?php if ($permission_condition['delete'] == true) {
                      $statement = '';
                      if (isset($is_admin) && $is_admin) {
                        $statement = 'disabled';
                      }
                      else if (isset($member) && has_permission($permission['shortname'], $member->staffid, 'delete')) {
                        $statement = 'checked';
                      }
                      ?>
                        <?php if (has_rule($permission['shortname'], $member->staffid, 'delete')) { ?>
                            <div class="checkbox checkbox-danger">
                              <input type="checkbox" data-shortname="<?php echo $permission['shortname']; ?>" data-can-delete <?php echo $statement; ?> name="delete[]" value="<?php echo $permission['permissionid']; ?>">
                              <label></label>
                            </div>
                        <?php 
                      } ?>
                    <?php 
                  } ?>
                  </td>
                 <td class="">
                 <?php
                  foreach($custom_permission[$permission['shortname']]['permissions'] as $value) {
                    $statement = '';
                    if(isset($is_admin) && $is_admin){
                      $statement = 'disabled';
                    } else if(isset($member) && has_permission($permission['shortname'],$member->staffid, $value)){
                      $statement = 'checked';
                    }
                    if(has_rule($permission['shortname'],$member->staffid, $value)){?>
                      <div class="checkbox checkbox-danger">
                        <input data-what="<?=$value?> <?=$permission['id']?>" type="checkbox" data-shortname="<?php echo $permission['shortname']; ?>" data-can-delete <?php echo $statement; ?> name="custompermission[<?=$value?>][]" value="<?php echo $permission['permissionid']; ?>">
                        <label><?=_l("custompermission_can_".$value)?></label>
                      </div>
                  <?php
                    }
                  }
                 ?>
                  
                </td>
              </tr>
              <?php } ?>
            </tbody>
            </table>
          </div>
        </div>
    <?php }?>
    
    </div>

<button type="submit" class="btn btn-info pull-right mtop20"><?php echo _l('submit'); ?></button>
</div>
</div>
</div>
<?php echo form_close(); ?>
<?php if(isset($member)){ ?>
<div class="col-md-5 small-table-right-col">

<div class="panel_s">
  <div class="panel-body">
    <h4 class="bold no-margin font-medium">
     <?php echo _l('staff_attachments'); ?>
   </h4>
   <hr />
<?php echo form_open_multipart(admin_url('staff/upload_attachment/'.$member->staffid),array('class'=>'dropzone','id'=>'client-attachments-upload')); ?>
<input type="file" name="file" multiple />
<?php echo form_close(); ?>

   <div class="clearfix"></div>
   <hr />
   <!-- <div class="mbot15 usernote hide inline-block full-width">
    <?php echo form_open(admin_url('misc/add_note/'.$member->staffid . '/staff')); ?>
    <?php echo render_textarea('description','staff_add_edit_note_description','',array('rows'=>5)); ?>
    <button class="btn btn-info pull-right mbot15"><?php echo _l('submit'); ?></button>
    <?php echo form_close(); ?>
  </div> -->
  <div class="clearfix"></div>
  <div class="table-responsive mtop15">
    <table class="table dt-table" data-order-col="2" data-order-type="desc">
            <thead>
                <tr>
                    <th width="30%"><?php echo _l('customer_attachments_file'); ?></th>
                    <!-- <th><?php echo _l('customer_attachments_show_in_customers_area'); ?></th> -->
                    <th><?php echo _l('file_date_uploaded'); ?></th>
                    <th><?php echo _l('options'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($attachments as $type => $attachment){
                    $download_indicator = 'id';
                    $key_indicator = 'rel_id';
                    $upload_path = get_upload_path_by_type($type);
                    if($type == 'invoice'){
                        $url = site_url() .'download/file/sales_attachment/';
                        $download_indicator = 'attachment_key';
                    } else if($type == 'proposal'){
                        $url = site_url() .'download/file/sales_attachment/';
                        $download_indicator = 'attachment_key';
                    } else if($type == 'estimate'){
                        $url = site_url() .'download/file/sales_attachment/';
                        $download_indicator = 'attachment_key';
                    } else if($type == 'contract'){
                        $url = site_url() .'download/file/contract/';
                    } else if($type == 'lead'){
                        $url = site_url() .'download/file/lead_attachment/';
                    } else if($type == 'task'){
                        $url = site_url() .'download/file/taskattachment/';
                    } else if($type == 'ticket'){
                        $url = site_url() .'download/file/ticket/';
                        $key_indicator = 'ticketid';
                    } else if($type == 'customer'){
                        $url = site_url() .'download/file/client/';
                    } else if($type == 'expense'){
                        $url = site_url() .'download/file/expense/';
                        $download_indicator = 'rel_id';
                    }
                    ?>
                    <?php foreach($attachment as $_att){
                        ?>
                        <tr id="tr_file_<?php echo $_att['id']; ?>">
                            <td>
                             <?php
                             $path = $upload_path . $_att[$key_indicator] . '/' . $_att['file_name'];
                             $is_image = false;
                             if(!isset($_att['external'])) {
                                $attachment_url = $url . $_att[$download_indicator];
                                $is_image = is_image($path);
                                $img_url = site_url('download/preview_image?path='.protected_file_url_by_path($path).'&type='.$_att['filetype']);
                            } else if(isset($_att['external']) && !empty($_att['external'])){

                                if(!empty($_att['thumbnail_link'])){
                                    $is_image = true;
                                    $img_url = optimize_dropbox_thumbnail($_att['thumbnail_link']);
                                }

                                $attachment_url = $_att['external_link'];
                            }
                            if($is_image){
                                echo '<div class="preview_image">';
                            }
                            ?>
                            <a href="<?php if($is_image){ echo $img_url; } else {echo $attachment_url; } ?>"<?php if($is_image){ ?> data-lightbox="customer-profile" <?php } ?> class="display-block mbot5">
                                <?php if($is_image){ ?>
                                <div class="table-image">
                                   <img src="<?php echo $img_url; ?>">
                               </div>
                               <?php } else { ?>
                               <i class="<?php echo get_mime_class($_att['filetype']); ?>"></i> <?php echo $_att['file_name']; ?>
                               <?php } ?>

                           </a>
                           <?php if($is_image){
                            echo '</div>';
                        }
                        ?>
                    </td>
                    <!-- <td>
                        <div class="onoffswitch"<?php if($type != 'customer'){?> data-toggle="tooltip" data-title="<?php echo _l('customer_attachments_show_notice'); ?>" <?php } ?>>
                            <input type="checkbox" <?php if($type != 'customer'){echo 'disabled';} ?> id="<?php echo $_att['id']; ?>" data-id="<?php echo $_att['id']; ?>" class="onoffswitch-checkbox customer_file" data-switch-url="<?php echo admin_url(); ?>misc/toggle_file_visibility" <?php if(isset($_att['visible_to_customer']) && $_att['visible_to_customer'] == 1){echo 'checked';} ?>>
                            <label class="onoffswitch-label" for="<?php echo $_att['id']; ?>"></label>
                        </div>
                        <?php if($type == 'customer' && $_att['visible_to_customer'] == 1){
                            $file_visibility_message = '';
                            $total_shares = total_rows('tblcustomerfiles_shares',array('file_id'=>$_att['id']));

                            if($total_shares == 0){
                                $file_visibility_message = _l('file_share_visibility_notice');
                            } else {
                                $share_contacts_id = get_customer_profile_file_sharing(array('file_id'=>$_att['id']));
                                if(count($share_contacts_id) == 0){
                                    $file_visibility_message = _l('file_share_visibility_notice');
                                }
                            }
                            echo '<span class="text-warning'.(empty($file_visibility_message) || total_rows('tblcontacts',array('userid'=>$client->userid)) == 0 ? ' hide': '').'">'.$file_visibility_message.'</span>';
                            if(isset($share_contacts_id) && count($share_contacts_id) > 0){
                                $names = '';
                                $contacts_selected = '';
                                foreach($share_contacts_id as $file_share){
                                    $names.= get_contact_full_name($file_share['contact_id']) .', ';
                                    $contacts_selected .= $file_share['contact_id'].',';
                                }
                                if($contacts_selected != ''){
                                    $contacts_selected = substr($contacts_selected,0,-1);
                                }
                                if($names != ''){
                                    echo '<a href="#" onclick="do_share_file_contacts(\''.$contacts_selected.'\','.$_att['id'].'); return false;"><i class="fa fa-pencil-square-o"></i></a> ' . _l('share_file_with_show',mb_substr($names, 0,-2));
                                }
                            }
                        }
                        ?>
                    </td> -->
                    <td data-order="<?php echo $_att['dateadded']; ?>"><?php echo _dt($_att['dateadded']); ?></td>
                    <td>
                        <!-- <?php if(!isset($_att['external'])){ ?>
                        <button type="button" data-toggle="modal" data-file-name="<?php echo $_att['file_name']; ?>" data-filetype="<?php echo $_att['filetype']; ?>" data-path="<?php echo $path; ?>" data-target="#send_file" class="btn btn-info btn-icon"><i class="fa fa-envelope"></i></button>
                        <?php } else if(isset($_att['external']) && !empty($_att['external'])) {
                            echo '<a href="'.$_att['external_link'].'" class="btn btn-info btn-icon" target="_blank"><i class="fa fa-dropbox"></i></a>';
                        } ?> -->
                        <?php if($type == 'customer'){ ?>
                        <a href="<?php echo admin_url('staff/delete_attachment/'.$_att['rel_id'].'/'.$_att['id']); ?>"  class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
                        <?php } ?>
                    </td>
                    <?php } ?>
                </tr>
                <?php } ?>
            </tbody>
        </table>
</div>

</div>
</div>


 <div class="panel_s">
  <div class="panel-body">
    <h4 class="bold no-margin font-medium">
     <?php echo _l('staff_add_edit_notes'); ?>
   </h4>
   <hr />

   <a href="#" class="btn btn-success" onclick="slideToggle('.usernote'); return false;"><?php echo _l('new_note'); ?></a>
   <div class="clearfix"></div>
   <hr />
   <div class="mbot15 usernote hide inline-block full-width">
    <?php echo form_open(admin_url('misc/add_note/'.$member->staffid . '/staff')); ?>
    <?php echo render_textarea('description','staff_add_edit_note_description','',array('rows'=>5)); ?>
    <button class="btn btn-info pull-right mbot15"><?php echo _l('submit'); ?></button>
    <?php echo form_close(); ?>
  </div>
  <div class="clearfix"></div>
  <div class="table-responsive mtop15">
    <table class="table dt-table" data-order-col="2" data-order-type="desc">
     <thead>
      <tr>
       <th width="50%"><?php echo _l('staff_notes_table_description_heading'); ?></th>
       <th><?php echo _l('staff_notes_table_addedfrom_heading'); ?></th>
       <th><?php echo _l('staff_notes_table_dateadded_heading'); ?></th>
       <th><?php echo _l('options'); ?></th>
     </tr>
   </thead>
   <tbody>
    <?php foreach($user_notes as $note){ ?>
    <tr>
     <td width="50%">
      <div data-note-description="<?php echo $note['id']; ?>">
       <?php echo $note['description']; ?>
     </div>
     <div data-note-edit-textarea="<?php echo $note['id']; ?>" class="hide inline-block full-width">
       <textarea name="description" class="form-control" rows="4"><?php echo clear_textarea_breaks($note['description']); ?></textarea>
       <div class="text-right mtop15">
        <button type="button" class="btn btn-default" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><?php echo _l('cancel'); ?></button>
        <button type="button" class="btn btn-info" onclick="edit_note(<?php echo $note['id']; ?>);"><?php echo _l('update_note'); ?></button>
      </div>
    </div>
  </td>
  <td><?php echo $note['firstname'] . ' ' . $note['lastname']; ?></td>
  <td data-order="<?php echo $note['dateadded']; ?>"><?php echo _dt($note['dateadded']); ?></td>
  <td>
    <?php if($note['addedfrom'] == get_staff_user_id() || has_permission('staff','','delete')){ ?>
    <a href="#" class="btn btn-default btn-icon" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><i class="fa fa-pencil-square-o"></i></a>
    <a href="<?php echo admin_url('misc/delete_note/'.$note['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
    <?php } ?>
  </td>
</tr>
<?php } ?>
</tbody>
</table>
</div>

</div>
</div>


<div class="panel_s">
 <div class="panel-body">
  <h4 class="bold no-margin font-medium">
   <?php echo _l('task_timesheets'); ?> & <?php echo _l('als_reports'); ?>
 </h4>
 <hr />
 <?php echo form_open($this->uri->uri_string(),array('method'=>'GET')); ?>
 <?php echo form_hidden('filter','true'); ?>
 <div class="row">
   <div class="col-md-6">
    <select name="range" id="range" class="selectpicker" data-width="100%">
     <option value="this_month" <?php if(!$this->input->get('range') || $this->input->get('range') == 'this_month'){echo 'selected';} ?>><?php echo _l('staff_stats_this_month_total_logged_time'); ?></option>
     <option value="last_month" <?php if($this->input->get('range') == 'last_month'){echo 'selected';} ?>><?php echo _l('staff_stats_last_month_total_logged_time'); ?></option>
     <option value="this_week" <?php if($this->input->get('range') == 'this_week'){echo 'selected';} ?>><?php echo _l('staff_stats_this_week_total_logged_time'); ?></option>
     <option value="last_week" <?php if($this->input->get('range') == 'last_week'){echo 'selected';} ?>><?php echo _l('staff_stats_last_week_total_logged_time'); ?></option>
     <option value="period" <?php if($this->input->get('range') == 'period'){echo 'selected';} ?>><?php echo _l('period_datepicker'); ?></option>
   </select>
   <div class="row mtop15">
     <div class="col-md-12 period <?php if($this->input->get('range') != 'period'){echo 'hide';} ?>">
      <?php echo render_date_input('period-from','',$this->input->get('period-from')); ?>
    </div>
    <div class="col-md-12 period <?php if($this->input->get('range') != 'period'){echo 'hide';} ?>">
      <?php echo render_date_input('period-to','',$this->input->get('period-from')); ?>
    </div>
  </div>
</div>
<div class="col-md-2 text-right">
  <button type="submit" class="btn btn-success apply-timesheets-filters"><?php echo _l('apply'); ?></button>
</div>
</div>
<?php echo form_close(); ?>
<hr />
<table class="table dt-table">
 <thead>
  <th><?php echo _l('task'); ?></th>
  <th><?php echo _l('timesheet_start_time'); ?></th>
  <th><?php echo _l('timesheet_end_time'); ?></th>
  <th><?php echo _l('task_relation'); ?></th>
  <th><?php echo _l('staff_hourly_rate'); ?> (<?php echo _l('als_staff'); ?>)</th>
  <th><?php echo _l('time_h'); ?></th>
  <th><?php echo _l('time_decimal'); ?></th>
</thead>
<tbody>
  <?php
  $total_logged_time = 0;
  foreach($timesheets as $t){ ?>
  <tr>
   <td><a href="#" onclick="init_task_modal(<?php echo $t['task_id']; ?>); return false;"><?php echo $t['name']; ?></a></td>
   <td><?php echo strftime(get_current_date_format().' %H:%M', $t['start_time']); ?></td>
   <td><?php echo strftime(get_current_date_format().' %H:%M', $t['end_time']); ?></td>
   <td>
     <?php
     $rel_data   = get_relation_data($t['rel_type'], $t['rel_id']);
     $rel_values = get_relation_values($rel_data, $t['rel_type']);
     echo '<a href="' . $rel_values['link'] . '">' . $rel_values['name'].'</a>';
     ?>
   </td>
   <td><?php echo format_money($t['hourly_rate'],$base_currency->symbol); ?></td>
   <td>
    <?php echo '<b>'.seconds_to_time_format($t['end_time'] - $t['start_time']).'</b>'; ?>
  </td>
  <td>
    <?php
    $total_logged_time += $t['total'];
    echo '<b>'.sec2qty($t['total']).'</b>';
    ?>
  </td>
</tr>
<?php } ?>
</tbody>
<tfoot>
  <tr>
   <td></td>
   <td></td>
   <td></td>
   <td></td>
   <td align="right"><?php echo '<b>' . _l('total_by_hourly_rate') .':</b> '. format_money((sec2qty($total_logged_time) * $member->hourly_rate),$base_currency->symbol); ?></td>
   <td align="right">
     <?php echo '<b>'._l('total_logged_hours_by_staff') . ':</b> ' . seconds_to_time_format($total_logged_time); ?>
   </td>
   <td align="right">
     <?php echo '<b>'._l('total_logged_hours_by_staff') . ':</b> ' . sec2qty($total_logged_time); ?>
   </td>
 </tr>
</tfoot>
</table>
</div>
</div>
<div class="panel_s">
 <div class="panel-body">
   <h4 class="bold no-margin font-medium">
    <?php echo _l('projects'); ?>
  </h4>
  <hr />
  <div class="_filters _hidden_inputs hidden staff_projects_filter">
    <?php echo form_hidden('staff_id',$member->staffid); ?>
  </div>
  <?php render_datatable(array(
    _l('project_name'),
    _l('project_start_date'),
    _l('project_deadline'),
    _l('project_status'),
    ),'staff-projects'); ?>
  </div>
</div>
</div>
<?php } ?>
</div>
</div>
<?php init_tail(); ?>
<script>
 init_roles_permissions();
 $('select[name="role"]').on('change', function() {
  var roleid = $(this).val();
  init_roles_permissions(roleid, true);
});
 $('input[name="administrator"]').on('change',function(){
  var checked = $(this).prop('checked');
  if(checked == true){
   $('.roles').find('input').prop('disabled',true);
 } else {
   $('.roles').find('input').prop('disabled',false);
 }
});
 _validate_form($('.staff-form'),{
   firstname:'required',
   lastname:'required',
   username:'required',
   password: {
     required: {
       depends: function(element){
         return ($('input[name="isedit"]').length == 0) ? true : false
       }
     }
   },
   email: {
     required:true,
     email:true,
     remote:{
       url: site_url + "admin/misc/staff_email_exists",
       type:'post',
       data: {
         email:function(){
           return $('input[name="email"]').val();
         },
         memberid:function(){
           return $('input[name="memberid"]').val();
         }
       }

     }
   }
 });
 function get_code_staff(id,staff_id="")
      {
          rule=$('input[name="rule"]:checked').val();
          role=$('#role').val();
          if(role!="")
          {
              jQuery.ajax({
                  type: "post",
                  url: "<?=admin_url()?>staff/get_staff_role/"+role+'/'+rule,
                  data: '',
                  cache: false,
                  success: function (data) {
                      $('select[name=staff_manager\\[\\]]').html(data).selectpicker('refresh');
                  }
              });
          }


      }

  function review_nhanvien(code_check)
            {
                role=$('#role').val();
                if(role!="")
                {
                    jQuery.ajax({
                        type: "post",
                        url: "<?=admin_url()?>staff/get_staff_role/"+role+'/'+code_check,
                        data: '',
                        cache: false,
                        success: function (data) {
                          $('select[name=staff_manager\\[\\]]').html(data).selectpicker('refresh');

                        }
                    });
                }
            }
    Dropzone.options.clientAttachmentsUpload = false;
 var customer_id = $('input[name="userid"]').val();
 if ($('#client-attachments-upload').length > 0) {
   new Dropzone('#client-attachments-upload', {
     paramName: "file",
     dictDefaultMessage:drop_files_here_to_upload,
     dictFallbackMessage:browser_not_support_drag_and_drop,
     dictRemoveFile:remove_file,
     dictFileTooBig: file_exceds_maxfile_size_in_form,
     dictMaxFilesExceeded:you_can_not_upload_any_more_files,
     maxFilesize: max_php_ini_upload_size.replace(/\D/g, ''),
     addRemoveLinks: false,
     accept: function(file, done) {
       done();
     },
     acceptedFiles: allowed_files,
     error: function(file, response) {
       alert_float('danger', response);
     },
     success: function(file, response) {
      if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
        window.location.reload();
      }
    }
  });
 }
</script>
</body>
</html>