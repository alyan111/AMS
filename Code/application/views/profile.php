<?php $this->load->view('includes/head'); ?>
</head>
<body class="sidebar-mini">
  <div id="app">
    <div class="main-wrapper">
      <?php $this->load->view('includes/navbar'); ?>
      <!-- Main Content -->
      <div class="main-content">
        <section class="section">
          <div class="section-header">
            <div class="section-header-back">
              <a href="javascript:history.go(-1)" class="btn btn-icon"><i class="fas fa-arrow-left"></i></a>
            </div>
            <h1>
            <?=$this->lang->line('profile')?$this->lang->line('profile'):'Profile'?>
            </h1>
            <div class="section-header-breadcrumb">
              <div class="breadcrumb-item active"><a href="<?=base_url()?>"><?=$this->lang->line('dashboard')?$this->lang->line('dashboard'):'Dashboard'?></a></div>
              <div class="breadcrumb-item"><?=$this->lang->line('profile')?$this->lang->line('profile'):'Profile'?></div>
            </div>
          </div>
          <div class="section-body">
            <div class="row">

              <div class="col-md-12">
                <div class="card card-primary profile-widget" id="profile-card">
                  <div class="profile-widget-header mb-0">  
                    <span class="avatar-item mb-0"> 
                    <?php
                      if(isset($profile_user['profile']) && !empty($profile_user['profile'])){
                        
                        if(file_exists('assets/uploads/profiles/'.$profile_user['profile'])){
                          $file_upload_path = 'assets/uploads/profiles/'.$profile_user['profile'];
                        }else{
                          $file_upload_path = 'assets/uploads/f'.$this->session->userdata('saas_id').'/profiles/'.$profile_user['profile'];
                        }
                    ?>       
                      <img alt="image" src="<?=base_url($file_upload_path)?>" class="rounded-circle profile-widget-picture">

                    <?php }else{ ?>
                      <figure class="user-avatar avatar avatar-xl rounded-circle profile-widget-picture" data-initial="<?=htmlspecialchars($profile_user['short_name'])?>"></figure>
                    <?php } ?>
                    </span> 
                    <div class="profile-widget-items">

                      <?php if(!$this->ion_auth->in_group(3)){ ?>
                      <div class="profile-widget-item">
                        <div class="profile-widget-item-label"><?=$this->lang->line('projects')?$this->lang->line('projects'):'Projects'?></div>
                        <div class="profile-widget-item-value"><span class="badge badge-secondary"><?=htmlspecialchars($profile_user['projects_count'])?></span></div>
                      </div>
                      <?php } ?>

                      <?php if(!$this->ion_auth->in_group(3)){ ?>  
                      <div class="profile-widget-item">
                        <div class="profile-widget-item-label"><?=$this->lang->line('tasks')?$this->lang->line('tasks'):'Tasks'?></div>
                        <div class="profile-widget-item-value"><span class="badge badge-secondary"><?=htmlspecialchars($profile_user['tasks_count'])?></span></div>
                      </div>
                      <?php } ?>

                      <?php if($this->ion_auth->is_admin()){
                        $my_plan = get_current_plan(); ?>
                        <div class="profile-widget-item">
                          <div class="profile-widget-item-label"><?=$this->lang->line('plan')?$this->lang->line('plan'):'Plan'?></div>
                          <div class="profile-widget-item-value"><span class="badge badge-primary"><?=$my_plan['title']?></span></div>
                        </div>
                      <?php } ?>

                      <div class="profile-widget-item">
                        <div class="profile-widget-item-label"><?=$this->lang->line('status')?$this->lang->line('status'):'Status'?></div>
                        <div class="profile-widget-item-value"><?=htmlspecialchars($profile_user['active'])==1?'<span class="badge badge-success">'.($this->lang->line('active')?$this->lang->line('active'):'Active').'</span>':'<span class="badge badge-danger">'.($this->lang->line('deactive')?$this->lang->line('deactive'):'Deactive').'</span>'?></div>
                      </div>
                    </div>
                  </div>

                  <form action="<?=base_url('auth/edit-user')?>" id="profile-form" method="post" class="needs-validation" novalidate="">
                    <div class="card-body">
                        <div class="row">                             
                          <div class="form-group col-md-6 col-12">
                            <label><?=$this->lang->line('first_name')?$this->lang->line('first_name'):'First Name'?><span class="text-danger">*</span></label>
                            <input type="hidden" name="update_id" value="<?=htmlspecialchars($profile_user['id'])?>">
                            <input type="hidden" name="old_profile_pic" value="<?=htmlspecialchars($profile_user['profile'])?>">
                            <input type="hidden" name="groups" value="<?=htmlspecialchars($profile_user['group_id'])?>">
                            <input type="hidden" name="active" value="<?=htmlspecialchars($profile_user['active'])?>">
                            <input type="hidden" name="device" value="<?=htmlspecialchars($profile_user['device'])?>">
                            <input type="hidden" name="finger_config" value="<?=htmlspecialchars($profile_user['finger_config'])?>">
                            <input type="hidden" name="short_name" value="<?=htmlspecialchars($profile_user['short_name'])?>">
                            <input type="hidden" name="department" value="<?=htmlspecialchars($profile_user['department'])?>">
                            <input type="hidden" name="role" value="<?=htmlspecialchars($profile_user['role'])?>">
                            <input type="hidden" name="type" value="<?=htmlspecialchars($profile_user['shift_id'])?>">
                            <input type="hidden" name="status" value="<?=htmlspecialchars($profile_user['status'])?>">
                            <input type="hidden" name="employee_id" value="<?=htmlspecialchars($profile_user['employee_id'])?>">
                            <input type="text" name="first_name" class="form-control" value="<?=htmlspecialchars($profile_user['first_name'])?>" required="">
                          </div>
                          <div class="form-group col-md-6 col-12">
                            <label><?=$this->lang->line('last_name')?$this->lang->line('last_name'):'Last Name'?><span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" value="<?=htmlspecialchars($profile_user['last_name'])?>" required="">
                          </div>
                        </div>
                        <div class="row">
                          <div class="form-group col-md-6 col-12">
                            <label><?=$this->lang->line('email')?$this->lang->line('email'):'Email'?><span class="text-danger">*</span></label>
                            <input type="email" class="form-control" value="<?=htmlspecialchars($profile_user['email'])?>" required=""  readonly disabled> 
                          </div>
                          <div class="form-group col-md-6 col-12">
                            <label><?=$this->lang->line('phone')?$this->lang->line('phone'):'Phone'?></label>
                            <input type="tel" name="phone" class="form-control" value="<?=htmlspecialchars($profile_user['phone'])?>">
                          </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                            <label><?=$this->lang->line('password')?$this->lang->line('password'):'Password'?> <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="right" title="<?=$this->lang->line('leave_password_and_confirm_password_empty_for_no_change_in_password')?$this->lang->line('leave_password_and_confirm_password_empty_for_no_change_in_password'):'Leave Password and Confirm Password empty for no change in Password.'?>"></i></label>
                            <input type="text" name="password" class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                            <label><?=$this->lang->line('confirm_password')?$this->lang->line('confirm_password'):'Confirm Password'?> <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="right" title="<?=$this->lang->line('leave_password_and_confirm_password_empty_for_no_change_in_password')?$this->lang->line('leave_password_and_confirm_password_empty_for_no_change_in_password'):'Leave Password and Confirm Password empty for no change in Password.'?>"></i></label>
                            <input type="text" name="password_confirm" class="form-control">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-md-6">
                            <label><?=$this->lang->line('user_profile')?$this->lang->line('user_profile'):'User Profile'?> <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="right" title="<?=$this->lang->line('leave_empty_for_no_changes')?$this->lang->line('leave_empty_for_no_changes'):"Leave empty for no changes."?>"></i></label>
                                <div class="custom-file mt-1">
                                    <input type="file" name="profile" class="custom-file-input" id="profile">
                                    <label class="custom-file-label" for="profile"><?=$this->lang->line('profile')?$this->lang->line('profile'):'Profile'?></label>
                                </div>
                            </div>
                            <div class="form-group col-md-6">
                                <label><?=$this->lang->line('cnic')?$this->lang->line('cnic'):'CNIC'?> </label>
                                <input type="text" name="cnic" class="form-control" value="<?=htmlspecialchars($profile_user['cnic'])?>">
                              </div>
                        </div>
                        <div class="row">
                              <div class="form-group col-md-6">
                                <label><?=$this->lang->line('father_name')?$this->lang->line('father_name'):'Father Name'?></label>
                                <input type="text" name="father_name" class="form-control" value="<?=htmlspecialchars($profile_user['father_name'])?>">
                              </div>
                              <div class="form-group col-md-6">
                                  <label><?=$this->lang->line('department')?$this->lang->line('type'):'Department'?></label>
                                  <input type="text" class="form-control" value="<?=htmlspecialchars($profile_user['department_name'])?>" readonly>
                              </div>
                          </div>
                          <div class="row">
                              <div class="form-group col-md-6">
                                <label><?=$this->lang->line('desgnation')?$this->lang->line('desgnation'):'Designation'?></label>
                                <input type="text" name="desgnation" class="form-control" value="<?=htmlspecialchars($profile_user['desgnation'])?>" readonly>
                              </div>
                              <div class="form-group col-md-6">
                                <label><?=$this->lang->line('gender')?$this->lang->line('gender'):'Gender'?><span class="text-danger">*</span></label>
                                <select name="gender" class="form-control select2" id="genderSelect">
                                  <option value="male">Male</option>
                                  <option value="female">Female</option>
                                  <option value="other">Other</option>
                                </select>
                              </div>
                          </div>
                          <div class="row">
                              <div class="form-group col-md-6">
                                <label><?=$this->lang->line('emg_person')?$this->lang->line('emg_person'):'Emergency Person'?> </label>
                                <input type="text" name="emg_person" class="form-control" value="<?=htmlspecialchars($profile_user['emg_person'])?>">
                              </div>
                              <div class="form-group col-md-6">
                                <label><?=$this->lang->line('emg_number')?$this->lang->line('emg_number'):'Emergency Number'?></label>
                                <input type="text" name="emg_number" class="form-control" value="<?=htmlspecialchars($profile_user['emg_number'])?>">
                              </div>
                          </div>
                          <div class="row">
                            <div class="form-group col-md-6">
                                <label><?=$this->lang->line('join_date')?$this->lang->line('join_date'):'Join Date'?><span class="text-danger">*</span></label>
                                <input type="text" name="join_date" class="form-control datepicker" value="<?=htmlspecialchars($profile_user['join_date'])?>" readonly>
                              </div>
                              <div class="form-group col-md-6">
                              <label><?=$this->lang->line('date_of_birth')?$this->lang->line('date_of_birth'):'Date of Birth'?></label>
                                <input type="text" name="date_of_birth" class="form-control datepicker"  value="<?=htmlspecialchars($profile_user['date_of_birth'])?>">
                              </div>
                          </div>
                          <div class="row">
                              <div class="form-group col-md-6">
                                <label><?=$this->lang->line('address')?$this->lang->line('address'):'Address'?><span class="text-danger">*</span></label>
                                <textarea name="address" class="form-control" style="height:100px;" name="address" rows="10"  ><?=htmlspecialchars($profile_user['address'])?></textarea>
                              </div>
                          </div>
                    </div>
                    <div class="card-footer text-right">
                      
                      <?php if(!is_saas_admin()){ ?>
                      <input type="hidden" id="update_id" value="<?=$this->session->userdata('user_id')?>">
                      
                      <?php  } ?>
                      
                      <button class="btn btn-primary savebtn"><?=$this->lang->line('save_changes')?$this->lang->line('save_changes'):'Save Changes'?></button>
                    </div>
                    <div class="result"></div>
                  </form>
                </div>
              </div>
            </div>    
          </div>
        </section>
      </div>
    
    <?php $this->load->view('includes/footer'); ?>
    </div>
  </div>

<?php $this->load->view('includes/js'); ?>
</body>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Get the select element
        var genderSelect = document.getElementById("genderSelect");

        // Get the value of htmlspecialchars($profile_user['gender'])
        var profileGender = "<?php echo htmlspecialchars($profile_user['gender']); ?>";

        // Loop through each option to find the one with the matching value
        for (var i = 0; i < genderSelect.options.length; i++) {
            if (genderSelect.options[i].value === profileGender) {
                // Set the selected attribute for the matching option
                genderSelect.options[i].selected = true;
                break;
            }
        }
    });
</script>
</html>
