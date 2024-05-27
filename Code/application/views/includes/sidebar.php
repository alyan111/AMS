<!--**********************************
    Nav header start
***********************************-->
<div class="nav-header">
  <a href="<?= base_url() ?>" class="brand-logo">
    <img width="60" height="50" src="<?= base_url('assets/uploads/logos/'.favicon()) ?>" alt="">
    <div class="brand-title">
      <img width="180" height="50" src="<?= base_url('assets/uploads/logos/'.half_logo()) ?>" alt="">
    </div>
  </a>
  <div class="nav-control">
    <div class="hamburger">
      <span class="line"></span><span class="line"></span><span class="line"></span>
    </div>
  </div>
</div>
<!--**********************************
    Nav header end
***********************************-->

<!--**********************************
	Header start
***********************************-->
<div class="header">
  <div class="header-content">
    <nav class="navbar navbar-expand">
      <div class="collapse navbar-collapse justify-content-between">
        <div class="header-left">
          <div class="dashboard_bar">
            <?= htmlspecialchars($main_page) ?>
          </div>
        </div>
        <ul class="navbar-nav header-right">
          <?php if (!is_saas_admin() && !is_client() && is_module_allowed('timesheet')) { ?>
            <li class="nav-item">
              <a class="nav-link " href="<?= base_url('projects/timesheet') ?>">
                <i class="fa-regular fa-clock text-primary"></i>
              </a>
            </li>
          <?php
          } ?>
          <li class="nav-item dropdown notification_dropdown">
            <a class="nav-link bell-link" href="<?= base_url('chat') ?>">
              <i class="fa-regular fa-envelope text-primary"></i>
            </a>
          </li>
          <?php
          if (is_module_allowed('notifications')) {
            echo get_notifications_live2();
          } ?>
          <li class="nav-item dropdown header-profile">
            <a class="nav-link" href="javascript:void(0);" role="button" data-bs-toggle="dropdown">
              <?php if (isset($current_user->profile) && !empty($current_user->profile)) {
                if (file_exists('assets/uploads/profiles/' . $current_user->profile)) {
                  $file_upload_path = 'assets/uploads/profiles/' . $current_user->profile;
                } else {
                  $file_upload_path = 'assets/uploads/f' . $this->session->userdata('saas_id') . '/profiles/' . $current_user->profile;
                }
              ?>
                <img src="<?= base_url($file_upload_path) ?>" width="56" alt="">
              <?php } else { ?>
                <div class="d-flex align-items-center flex-wrap">
                  <ul class="kanbanimg me-3">
                    <li><span><?= mb_substr(htmlspecialchars($current_user->first_name), 0, 1, "utf-8") . '' . mb_substr(htmlspecialchars($current_user->last_name), 0, 1, "utf-8") ?></span></li>
                  </ul>
                </div>
              <?php } ?>
            </a>
            <div class="dropdown-menu dropdown-menu-end">
              <?php if ($this->ion_auth->is_admin()) : ?>
                <?php $my_plan = get_current_plan(); ?>
                <a href="<?= base_url('plans') ?>" class="dropdown-item ai-icon">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" class="text-danger" xmlns="http://www.w3.org/2000/svg" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M18 8.5V8.35417C18 6.50171 16.4983 5 14.6458 5H9.5C7.567 5 6 6.567 6 8.5C6 10.433 7.567 12 9.5 12H14.5C16.433 12 18 13.567 18 15.5C18 17.433 16.433 19 14.5 19H9.42708C7.53436 19 6 17.4656 6 15.5729V15.5M12 3V21" />
                  </svg>
                  <span class="ms-2 text-danger"><?= $my_plan['title'] ?> </span>
                </a>
              <?php endif ?>
              <?php if (!is_saas_admin()) : ?>
                <a href="<?= base_url('users/profile') ?>" class="dropdown-item ai-icon">
                  <svg id="icon-user1" xmlns="http://www.w3.org/2000/svg" class="text-primary" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                  </svg>
                  <span class="ms-2">Profile </span>
                </a>
              <?php endif ?>
              <a href="<?= base_url('auth/logout') ?>" class="dropdown-item ai-icon">
                <svg id="icon-logout" xmlns="http://www.w3.org/2000/svg" class="text-danger" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                  <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                  <polyline points="16 17 21 12 16 7"></polyline>
                  <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                <span class="ms-2">Logout </span>
              </a>
            </div>
          </li>
        </ul>
      </div>
    </nav>
  </div>
</div>
<!--**********************************
	Header end 
***********************************-->

<!--**********************************
    Sidebar start
***********************************-->
<div class="dlabnav">
  <div class="dlabnav-scroll">
    <ul class="metismenu" id="menu">
      <li><a href="<?= base_url() ?>" aria-expanded="false">
          <i class="fas fa-home"></i>
          <span class="nav-text">Dashboard</span>
        </a>
      </li>
      <?php if (($this->ion_auth->is_admin() || permissions('attendance_view_all') || permissions('attendance_view_selected') || permissions('attendance_view') || permissions('leaves_view') || permissions('biometric_request_view') || permissions('plan_holiday_view')) && !is_saas_admin() && (is_module_allowed('leaves') || is_module_allowed('attendance') || is_module_allowed('biometric_missing'))) { ?>
        <li>
          <a class="has-arrow" href="javascript:void(0);" aria-expanded="false">
            <i class="fas fa-fingerprint"></i>
            <span class="nav-text">AMS</span>
          </a>
          <ul aria-expanded="false">
            <?php if (($this->ion_auth->is_admin() || permissions('attendance_view_all') || permissions('attendance_view_selected')) && is_module_allowed('attendance')) { ?>
              <li><a href="<?= base_url('attendance') ?>">Attendance</a></li>
            <?php } ?>
            <?php if (is_module_allowed('attendance') && permissions('attendance_view') && !permissions('attendance_view_all') && !permissions('attendance_view_selected')) { ?>
              <li><a href="<?= base_url('attendance/user_attendance') ?>">Attendance</a></li>
            <?php } ?>
            <?php if (is_module_allowed('leaves') && ($this->ion_auth->is_admin() || permissions('leaves_view'))) { ?>
              <li><a href="<?= base_url('leaves') ?>">Leave Requests</a></li>
            <?php } ?>
            <?php if (is_module_allowed('biometric_missing') && ($this->ion_auth->is_admin() || permissions('biometric_request_view'))) { ?>
              <li><a href="<?= base_url('biometric_missing') ?>">Biometric Requests</a></li>
            <?php } ?>
            <?php if (($this->ion_auth->is_admin() || permissions('plan_holiday_view')) && is_module_allowed('attendance')) { ?>
              <li><a href="<?= base_url('holiday') ?>">Plan Holidays</a></li>
            <?php } ?>
          </ul>
        </li>
      <?php } ?>

      <!-- <?php if (($this->ion_auth->is_admin() || is_client() || permissions('project_view') || permissions('task_view') || permissions('gantt_view') || permissions('calendar_view')) && !is_saas_admin() && (is_module_allowed('projects') || is_module_allowed('tasks') || is_module_allowed('timesheet') || is_module_allowed('gantt') || is_module_allowed('calendar'))) { ?>
        <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
            <i class="fas fa-list"></i>
            <span class="nav-text"><?= $this->lang->line('pms') ? $this->lang->line('pms') : 'PMS' ?></span>
          </a>
          <ul aria-expanded="false">
            <?php if (is_module_allowed('projects') && ($this->ion_auth->is_admin() || permissions('project_view'))) { ?>
              <li><a href="<?= base_url('projects/list') ?>"><?= $this->lang->line('projects') ? $this->lang->line('projects') : 'Projects' ?></a></li>
            <?php } ?>

            <?php if (is_module_allowed('tasks') && ($this->ion_auth->is_admin() || permissions('task_view'))) { ?>
              <li><a href="<?= base_url('projects/tasks') ?>"><?= $this->lang->line('tasks') ? $this->lang->line('tasks') : 'Tasks' ?></a></li>
            <?php } ?>

            <?php if (is_module_allowed('timesheet') && ($this->ion_auth->is_admin() || permissions('task_view')) && !is_client()) { ?>
              <li><a href="<?= base_url('projects/timesheet') ?>"><?= $this->lang->line('timesheet') ? $this->lang->line('timesheet') : 'Timesheet' ?></a></li>
            <?php } ?>

            <?php if (($this->ion_auth->is_admin() || permissions('gantt_view')) && is_module_allowed('gantt')) { ?>
              <li><a href="<?= base_url('projects/gantt') ?>"><?= $this->lang->line('gantt') ? $this->lang->line('gantt') : 'Gantt' ?></a></li>
            <?php } ?>

            <?php if ($this->ion_auth->is_admin() || permissions('calendar_view')) { ?>
              <li><a href="<?= base_url('projects/calendar') ?>"><?= $this->lang->line('calendar') ? $this->lang->line('calendar') : 'Calendar' ?></a></li>
            <?php } ?>
          </ul>
        </li>
      <?php
            }
      ?> -->

      <?php if (($this->ion_auth->is_admin() || is_client() || permissions('project_view') || permissions('task_view') || permissions('gantt_view') || permissions('calendar_view')) && !is_saas_admin() && (is_module_allowed('projects'))) { ?>
        <li <?= (strpos(current_url(), 'projects/detail/') !== false ||
              strpos(current_url(), 'backlog/project/') !== false ||
              strpos(current_url(), 'board/tasks/') !== false) ? 'class="mm-active ms-hover"' : ''; ?>>
          <a href="<?= base_url('projects') ?>" aria-expanded="false">
            <i class="fas fa-bezier-curve"></i>
            <span class="nav-text"><?= $this->lang->line('pms') ? $this->lang->line('pms') : 'PMS' ?></span>
          </a>
        </li>
      <?php
      }
      ?>

      <?php if (is_saas_admin()) { ?>
        <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
            <i class="fas fa fa-dollar-sign"></i>
            <span class="nav-text"><?= $this->lang->line('subscription') ? htmlspecialchars($this->lang->line('subscription')) : 'Subscription' ?></span>
          </a>
          <ul aria-expanded="false">
            <li><a href="<?= base_url('plans') ?>"><?= $this->lang->line('subscription_plans') ? $this->lang->line('subscription_plans') : 'Plans' ?></a></li>
            <li><a href="<?= base_url('plans/orders') ?>"><?= $this->lang->line('orders') ? $this->lang->line('orders') : 'Orders' ?></a></li>
            <li><a href="<?= base_url('plans/offline-requests') ?>"><?= $this->lang->line('offline_requests') ? $this->lang->line('offline_requests') : 'Offline Requests' ?></a></li>
            <li><a href="<?= base_url('users/saas') ?>"><?= $this->lang->line('subscribers') ? htmlspecialchars($this->lang->line('subscribers')) : 'Subscribers' ?></a></li>
          </ul>
        </li>

        <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
            <i class="fas fa-puzzle-piece"></i>
            <span class="nav-text"><?= $this->lang->line('frontend') ? $this->lang->line('frontend') : 'Frontend' ?></span>
          </a>
          <ul aria-expanded="false">
            <li><a href="<?= base_url('front/landing') ?>"><?= $this->lang->line('general') ? $this->lang->line('general') : 'General' ?></a></li>
            <li><a href="<?= base_url('front/features') ?>"><?= $this->lang->line('features') ? $this->lang->line('features') : 'Features' ?></a></li>
            <li><a href="<?= base_url('front/about') ?>"><?= $this->lang->line('about') ? $this->lang->line('about') : 'About Us' ?></a></li>
            <li><a href="<?= base_url('front/saas-privacy-policy') ?>"><?= $this->lang->line('privacy_policy') ? $this->lang->line('privacy_policy') : 'Privacy Policy' ?></a></li>
            <li><a href="<?= base_url('front/saas-terms-and-conditions') ?>"><?= $this->lang->line('terms_and_conditions') ? $this->lang->line('terms_and_conditions') : 'Terms and Conditions' ?></a></li>

            <li><a href="<?= base_url('front/saas-guide') ?>"><?= $this->lang->line('guide') ? $this->lang->line('guide') : 'Guide' ?></a></li>

          </ul>
        </li>

        <li><a href="<?= base_url('users') ?>" aria-expanded="false">
            <i class="fas fa-user-tie"></i>
            <span class="nav-text"><?= $this->lang->line('saas_admins') ? $this->lang->line('saas_admins') : 'SaaS Admins' ?></span>
          </a>
        </li>
      <?php
      } ?>

      <?php if (($this->ion_auth->is_admin() || permissions('user_view')) && !is_saas_admin() && is_module_allowed('team_members')) { ?>
        <li><a href="<?= base_url('users') ?>" aria-expanded="false">
            <i class="fas fa-user"></i>
            <span class="nav-text">Employees</span>
          </a>
        </li>

        <?php if (is_module_allowed('projects') && ($this->ion_auth->is_admin() || permissions('client_view'))) { ?>
          <li><a href="<?= base_url('users/client') ?>" aria-expanded="false">
              <i class="fas fa-handshake"></i>
              <span class="nav-text">Clients</span>
            </a>
          </li>
        <?php } ?>
        <li><a href="<?= base_url('events') ?>" aria-expanded="false">
            <i class="fas fa-calendar"></i>
            <span class="nav-text"><?= $this->lang->line('events') ? $this->lang->line('events') : 'Notice Board' ?></span>
          </a>
        </li>
      <?php } ?>

      <?php if (($this->ion_auth->is_admin() || permissions('chat_view')) && !is_saas_admin() && is_module_allowed('chat')) { ?>
        <li><a href="<?= base_url('chat') ?>" aria-expanded="false">
            <i class="fa-solid fa-message"></i>
            <span class="nav-text">Chat</span>
          </a>
        </li>
      <?php } ?>

      <?php if (($this->ion_auth->is_admin() || permissions('todo_view')) && !is_saas_admin() && is_module_allowed('todo')) { ?>
        <li><a href="<?= base_url('todo') ?>" aria-expanded="false">
            <i class="fa-solid fa-list-check"></i>
            <span class="nav-text">Todo</span>
          </a>
        </li>
      <?php } ?>
      <?php if (($this->ion_auth->is_admin() || permissions('notes_view')) && !is_saas_admin() && is_module_allowed('notes')) { ?>
        <li><a href="<?= base_url('notes') ?>" aria-expanded="false">
            <i class="fa-solid fa-file-invoice"></i>
            <span class="nav-text">Notes</span>
          </a>
        </li>
      <?php } ?>
      <?php if ($this->ion_auth->is_admin()) { ?>
        <li><a class="nav-link" href="<?= base_url('plans') ?>"><i class="fas fa-dollar-sign"></i>
            <span class="nav-text">Plans</span>
          </a>
        </li>
      <?php } ?>
      <?php if (($this->ion_auth->is_admin() || permissions('reports_view')) && is_module_allowed('reports')) { ?>
        <li><a class="has-arrow " href="javascript:void()" aria-expanded="false">
            <i class="fas fa-chart-line"></i>
            <span class="nav-text">Reports</span>
          </a>
          <ul aria-expanded="false">
            <?php if ($this->ion_auth->is_admin() || permissions('attendance_view_all') || permissions('attendance_view_selected')) { ?>
              <li><a href="<?= base_url('reports/attendance') ?>">Attendance</a></li>
            <?php } ?>
            <?php if ($this->ion_auth->is_admin() || permissions('leaves_view_all') || permissions('leaves_view_selected')) { ?>
              <li><a href="<?= base_url('reports/leaves') ?>">Leaves</a></li>
            <?php } ?>
          </ul>
        </li>
      <?php } ?>
      <?php
      if ($this->ion_auth->is_admin() || is_saas_admin() || permissions('general_view') || permissions('company_view') || permissions('leave_type_view') || permissions('device_view') || permissions('departments_view') || permissions('shift_view') || permissions('time_schedule_view')) { ?>
        <li id=""><a class="has-arrow " href="javascript:void()" aria-expanded="false" id="GuideStep1">
            <i class="fa-solid fa-gear"></i>
            <span class="nav-text">Settings</span>
          </a>
          <ul aria-expanded="false">
            <?php if (is_saas_admin()) { ?>
              <li><a href="<?= base_url('settings') ?>"><?= $this->lang->line('general') ? $this->lang->line('general') : 'General' ?></a></li>
              <li><a href="<?= base_url('settings/seo') ?>"><?= $this->lang->line('seo') ? $this->lang->line('seo') : 'SEO' ?></a></li>
              <li><a href="<?= base_url('settings/payment') ?>"><?= $this->lang->line('payment_gateway') ? $this->lang->line('payment_gateway') : 'Payment Gateway' ?></a></li>
              <li><a href="<?= base_url('settings/logins') ?>"><?= $this->lang->line('social_login') ? htmlspecialchars($this->lang->line('social_login')) : 'Social Login' ?></a></li>
              <li><a href="<?= base_url('settings/email') ?>"><?= $this->lang->line('email') ? $this->lang->line('email') : 'Email' ?></a></li>
              <li><a href="<?= base_url('settings/email-templates') ?>"><?= $this->lang->line('email_templates') ? $this->lang->line('email_templates') : 'Email Templates' ?></a></li>
              <!-- <li><a href="<?= base_url('languages') ?>"><?= $this->lang->line('languages') ? $this->lang->line('languages') : 'Languages' ?></a></li> -->
              <li><a href="<?= base_url('settings/update') ?>"><?= $this->lang->line('update') ? $this->lang->line('update') : 'Update' ?></a></li>
              <li><a href="<?= base_url('settings/recaptcha') ?>"><?= $this->lang->line('google_recaptcha') ? $this->lang->line('google_recaptcha') : 'Google reCAPTCHA' ?></a></li>
              <li><a href="<?= base_url('settings/custom-code') ?>"><?= $this->lang->line('custom_code') ? $this->lang->line('custom_code') : 'Custom Code' ?></a></li>
            <?php
            } else {
            ?>
              <?php if ($this->ion_auth->is_admin() || permissions('company_view')) { ?>
                <li><a href="<?= base_url('settings/company') ?>"><?= $this->lang->line('company') ? $this->lang->line('company') : 'Company' ?></a></li>
              <?php } ?>

              <?php if (($this->ion_auth->is_admin() || change_permissions('')) && is_module_allowed('user_roles')) { ?>
                <li><a id="GuideStep2" href="<?= base_url('settings/roles') ?>"><?= $this->lang->line('roles') ? $this->lang->line('roles') : 'Roles' ?></a></li>
              <?php } ?>

              <?php if (($this->ion_auth->is_admin() || change_permissions('')) && is_module_allowed('user_permissions')) { ?>
                <li><a href="<?= base_url('settings/roles-permissions') ?>"><?= $this->lang->line('roles_permissions') ? $this->lang->line('roles_permissions') : 'Permissions' ?></a></li>
              <?php } ?>

              <?php if (($this->ion_auth->is_admin() || permissions('leaves_edit')) && is_module_allowed('leaves')) { ?>
                <li><a href="<?= base_url('settings/leaves') ?>"><?= $this->lang->line('leave_type') ? $this->lang->line('leave_type') : 'Leave Type' ?></a></li>
              <?php } ?>

              <?php if (($this->ion_auth->is_admin() || permissions('leaves_edit')) && is_module_allowed('leaves')) { ?>
                <li><a href="<?= base_url('settings/hierarchy') ?>"><?= $this->lang->line('hierarchy') ? $this->lang->line('hierarchy') : 'Leave Approval Hierarchy' ?></a></li>
              <?php } ?>

              <?php if (($this->ion_auth->is_admin() || permissions('shift_view')) && is_module_allowed('shifts')) { ?>
                <li><a href="<?= base_url('settings/shift') ?>">Shifts</a></li>
              <?php } ?>

              <?php if (($this->ion_auth->is_admin() || permissions('device_view')) && is_module_allowed('biometric_machine')) { ?>
                <li><a href="<?= base_url('settings/device_config') ?>"><?= $this->lang->line('device_config') ? $this->lang->line('device_config') : 'Device Configuration' ?></a></li>
              <?php } ?>

              <?php if (($this->ion_auth->is_admin() || permissions('departments_view')) && is_module_allowed('departments')) { ?>
                <li><a href="<?= base_url('settings/departments') ?>"><?= $this->lang->line('departments') ? $this->lang->line('departments') : 'Departments' ?></a></li>
              <?php } ?>
              <?php if (($this->ion_auth->is_admin() || permissions('leaves_edit')) && (is_module_allowed('leaves') && is_module_allowed('leaves'))) { ?>
                <li><a class="nav-link" href="<?= base_url('settings/policies') ?>"><?= $this->lang->line('applied_policy') ? $this->lang->line('applied_policy') : 'Applied Policy' ?></a></li>
              <?php } ?>


            <?php } ?>
          </ul>
        </li>
      <?php
      }
      ?>
      <?php if (is_module_allowed('support') && ($this->ion_auth->is_admin() || is_saas_admin() || permissions('support_view'))) { ?>
        <li><a href="<?= base_url('support') ?>" aria-expanded="false">
            <i class="fa-solid fa-circle-info"></i>
            <span class="nav-text">Support</span>
          </a>
        </li>
      <?php } ?>

    </ul>
    <div class="copyright">
      <p class="fs-12 text-center">Made with <span class="heart"></span> by Airnet Technologies</p>
    </div>
  </div>
</div>
<!--**********************************
    Sidebar end
***********************************-->