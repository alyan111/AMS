<script>
  company_name = '<?= company_name() ?>';
  saas_id = '<?= $this->session->userdata('saas_id') ?>';
  base_url = "<?= base_url(); ?>";
  date_format_js = "<?= system_date_format_js(); ?>";
  time_format_js = "<?= system_time_format_js(); ?>";
  currency_code = "<?= get_saas_currency('currency_code'); ?>";
  currency_symbol = "<?= get_saas_currency('currency_symbol'); ?>";
  theme_color = "<?= htmlspecialchars(theme_color()) ?>";
  ok = "<?= $this->lang->line('ok') ? htmlspecialchars($this->lang->line('ok')) : 'OK' ?>";
  progress = "<?= $this->lang->line('progress') ? htmlspecialchars($this->lang->line('progress')) : 'Progress' ?>";
  cancel = "<?= $this->lang->line('cancel') ? htmlspecialchars($this->lang->line('cancel')) : 'Cancel' ?>";
  subscribers = "<?= $this->lang->line('subscribers') ? htmlspecialchars($this->lang->line('subscribers')) : 'Subscribers' ?>";
  tasks = "<?= $this->lang->line('tasks') ? htmlspecialchars($this->lang->line('tasks')) : 'Tasks' ?>";
  days = "<?= $this->lang->line('days') ? $this->lang->line('days') : 'Days' ?>";
  wait = "<?= $this->lang->line('wait') ? $this->lang->line('wait') : "Wait..." ?>";
  create = "<?= $this->lang->line('create') ? htmlspecialchars($this->lang->line('create')) : 'Create' ?>";
  update = "<?= $this->lang->line('update') ? htmlspecialchars($this->lang->line('update')) : 'Update' ?>";
  start_timer = "<?= $this->lang->line('start_timer') ? $this->lang->line('start_timer') : 'Start Timer' ?>";
  stop_timer = "<?= $this->lang->line('stop_timer') ? $this->lang->line('stop_timer') : 'Stop Timer' ?>";
  completed = "<?= $this->lang->line('completed') ? htmlspecialchars($this->lang->line('completed')) : 'Completed' ?>";
  default_language_can_not_be_deleted = "<?= $this->lang->line('default_language_can_not_be_deleted') ? $this->lang->line('default_language_can_not_be_deleted') : "Default language can not be deleted." ?>";
  are_you_sure = "<?= $this->lang->line('are_you_sure') ? $this->lang->line('are_you_sure') : "Are you sure?" ?>";
  you_want_to_delete_this_notification = "<?= $this->lang->line('you_want_to_delete_this_notification') ? $this->lang->line('you_want_to_delete_this_notification') : "You want to delete this Notification?" ?>";
  you_want_to_delete_this_feature = "<?= $this->lang->line('you_want_to_delete_this_feature') ? $this->lang->line('you_want_to_delete_this_feature') : "You want to delete this Feature?" ?>";
  you_want_reject_this_offline_request_this_can_not_be_undo = "<?= $this->lang->line('you_want_reject_this_offline_request_this_can_not_be_undo') ? $this->lang->line('you_want_reject_this_offline_request_this_can_not_be_undo') : "You want reject this offline request? This can not be undo." ?>";
  you_want_accept_this_offline_request_this_can_not_be_undo = "<?= $this->lang->line('you_want_accept_this_offline_request_this_can_not_be_undo') ? $this->lang->line('you_want_accept_this_offline_request_this_can_not_be_undo') : "You want accept this offline request? This can not be undo." ?>";
  default_plan_can_not_be_deleted = "<?= $this->lang->line('default_plan_can_not_be_deleted') ? $this->lang->line('default_plan_can_not_be_deleted') : "Default plan can not be deleted." ?>";
  you_want_to_delete_this_plan_all_users_under_this_plan_will_be_added_to_the_default_plan = "<?= $this->lang->line('you_want_to_delete_this_plan_all_users_under_this_plan_will_be_added_to_the_default_plan') ? $this->lang->line('you_want_to_delete_this_plan_all_users_under_this_plan_will_be_added_to_the_default_plan') : "You want to delete this Plan? All users under this plan will be added to the Default Plan." ?>";
  you_want_to_delete_this_todo = "<?= $this->lang->line('you_want_to_delete_this_todo') ? $this->lang->line('you_want_to_delete_this_todo') : "You want to delete this ToDo?" ?>";
  you_want_to_delete_this_note = "<?= $this->lang->line('you_want_to_delete_this_note') ? $this->lang->line('you_want_to_delete_this_note') : "You want to delete this note?" ?>";
  you_want_to_delete_this_project_all_related_data_with_this_project_also_will_be_deleted = "<?= $this->lang->line('you_want_to_delete_this_project_all_related_data_with_this_project_also_will_be_deleted') ? $this->lang->line('you_want_to_delete_this_project_all_related_data_with_this_project_also_will_be_deleted') : "You want to delete this project? All related data with this project also will be deleted." ?>";
  you_want_to_delete_this_task_all_related_data_with_this_task_also_will_be_deleted = "<?= $this->lang->line('you_want_to_delete_this_task_all_related_data_with_this_task_also_will_be_deleted') ? $this->lang->line('you_want_to_delete_this_task_all_related_data_with_this_task_also_will_be_deleted') : "You want to delete this task? All related data with this task also will be deleted." ?>";
  you_want_to_delete_this_user_all_related_data_with_this_user_also_will_be_deleted = "<?= $this->lang->line('you_want_to_delete_this_user_all_related_data_with_this_user_also_will_be_deleted') ? $this->lang->line('you_want_to_delete_this_user_all_related_data_with_this_user_also_will_be_deleted') : "You want to delete this user? All related data with this user also will be deleted." ?>";
  you_want_to_upgrade_the_system_please_take_a_backup_before_going_further = "<?= $this->lang->line('you_want_to_upgrade_the_system_please_take_a_backup_before_going_further') ? $this->lang->line('you_want_to_upgrade_the_system_please_take_a_backup_before_going_further') : "You want to upgrade the system? Please take a backup before going further." ?>";
  you_want_to_delete_this_file = "<?= $this->lang->line('you_want_to_delete_this_file') ? $this->lang->line('you_want_to_delete_this_file') : "You want to delete this file?" ?>";
  you_want_to_activate_this_user = "<?= $this->lang->line('you_want_to_activate_this_user') ? $this->lang->line('you_want_to_activate_this_user') : "You want to activate this user?" ?>";
  you_want_to_deactivate_this_user_this_user_will_be_not_able_to_login_after_deactivation = "<?= $this->lang->line('you_want_to_deactivate_this_user_this_user_will_be_not_able_to_login_after_deactivation') ? $this->lang->line('you_want_to_deactivate_this_user_this_user_will_be_not_able_to_login_after_deactivation') : "You want to deactivate this user? This user will be not able to login after deactivation." ?>";
  something_wrong_try_again = "<?= $this->lang->line('something_wrong_try_again') ? $this->lang->line('something_wrong_try_again') : "Something wrong! Try again." ?>";
  you_want_to_delete_this_chat_this_can_not_be_undo = "<?= $this->lang->line('you_want_to_delete_this_chat_this_can_not_be_undo') ? $this->lang->line('you_want_to_delete_this_chat_this_can_not_be_undo') : "You want to delete this chat? This can not be undo." ?>";

  we_will_contact_you_for_further_process_of_payment_as_soon_as_possible_click_ok_to_confirm = "<?= $this->lang->line('we_will_contact_you_for_further_process_of_payment_as_soon_as_possible_click_ok_to_confirm') ? $this->lang->line('we_will_contact_you_for_further_process_of_payment_as_soon_as_possible_click_ok_to_confirm') : "We will contact you for further process of payment as soon as possible. Click OK to confirm." ?>";

  you_want_to_delete_this_language = "<?= $this->lang->line('you_want_to_delete_this_language') ? $this->lang->line('you_want_to_delete_this_language') : "You want to delete this Language?" ?>";

  you_want_to_delete_this_tax = "<?= $this->lang->line('you_want_to_delete_this_tax') ? $this->lang->line('you_want_to_delete_this_tax') : "You want to delete this Tax?" ?>";
  you_want_to_delete_this_invoice = "<?= $this->lang->line('you_want_to_delete_this_invoice') ? $this->lang->line('you_want_to_delete_this_invoice') : "You want to delete this Invoice?" ?>";
  you_will_be_logged_out_from_the_current_account = "<?= $this->lang->line('you_will_be_logged_out_from_the_current_account') ? htmlspecialchars($this->lang->line('you_will_be_logged_out_from_the_current_account')) : 'You will be logged out from the current account.' ?>";
  convert_invoice_to_estimate = "<?= $this->lang->line('convert_invoice_to_estimate') ? htmlspecialchars($this->lang->line('convert_invoice_to_estimate')) : 'Convert invoice to estimate' ?>";
  convert_estimate_to_invoice = "<?= $this->lang->line('convert_estimate_to_invoice') ? htmlspecialchars($this->lang->line('convert_estimate_to_invoice')) : 'Convert estimate to invoice' ?>";
</script>

<!-- General JS Scripts -->

<?= get_footer_code() ?>
<script src="<?= base_url('assets2/vendor/global/global.min.js') ?>"></script>
<script src="<?= base_url('assets2/vendor/select2/js/select2.full.min.js') ?>"></script>
<script src="<?= base_url('assets2/js/plugins-init/select2-init.js'); ?>"></script>
<script src="<?= base_url('assets2/vendor/bootstrap-select/js/bootstrap-select.min.js') ?>"></script>

<script src="<?= base_url('assets2/vendor/moment/moment.min.js') ?>"></script>
<script src="<?= base_url('assets2/vendor/bootstrap-daterangepicker/daterangepicker.js') ?>"></script>
<script src="<?= base_url('assets2/vendor/clockpicker/js/bootstrap-clockpicker.min.js') ?>"></script>
<script src="<?= base_url('assets2/vendor/jquery-asColor/jquery-asColor.min.js') ?>"></script>
<script src="<?= base_url('assets2/vendor/jquery-asGradient/jquery-asGradient.min.js') ?>"></script>
<script src="<?= base_url('assets2/vendor/jquery-asColorPicker/js/jquery-asColorPicker.min.js') ?>"></script>
<script src="<?= base_url('assets/modules/bootstrap-timepicker/timepicker.js') ?>"></script>
<script src="<?= base_url('assets2/vendor/bootstrap-material-datetimepicker/js/bootstrap-material-datetimepicker.js') ?>"></script>
<script src="<?= base_url('assets2/vendor/pickadate/picker.js') ?>"></script>
<script src="<?= base_url('assets2/vendor/pickadate/picker.time.js') ?>"></script>
<script src="<?= base_url('assets2/js/plugins-init/pickadate-init.js') ?>"></script>
<script src="<?= base_url('assets2/vendor/apexchart/apexchart.js') ?>"></script>
<script src="<?= base_url('assets2/vendor/datatables/js/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets2/vendor/datatables/responsive/responsive.js') ?>"></script>
<script src="<?= base_url('assets2/vendor/sweetalert2/sweetalert2.min.js') ?>"></script>
<script src="<?= base_url('assets2/vendor/multiselect/multiselect.js') ?>"></script>
<script src="<?= base_url('assets2/vendor/introjs/intro.min.js') ?>"></script>
<script src="<?= base_url('assets2/vendor/toastr/js/toastr.min.js') ?>"></script>
<script src="<?= base_url('assets2/js/stisla.js') ?>"></script>
<script src="<?= base_url('assets2/js/custom.js') ?>"></script>
<script src="<?= base_url('assets2/js/custom.min.js') ?>"></script>
<script src="<?= base_url('assets2/js/dlabnav-init.js') ?>"></script>
<?php if ($this->session->flashdata('message') && $this->session->flashdata('message_type') == 'success') { ?>
  <script>
    toastr.success("<?= $this->session->flashdata('message'); ?>", "Success", {
      positionClass: "toast-top-right",
      timeOut: 5e3,
      closeButton: !0,
      debug: !1,
      newestOnTop: !0,
      progressBar: !0,
      preventDuplicates: !0,
      onclick: null,
      showDuration: "300",
      hideDuration: "1000",
      extendedTimeOut: "1000",
      showEasing: "swing",
      hideEasing: "linear",
      showMethod: "fadeIn",
      hideMethod: "fadeOut",
      tapToDismiss: !1
    })
    <?php $this->session->set_flashdata('message', null); ?>
  </script>
<?php } ?>
<script>
  function get_live_notifications() {

    var $notifications = [];
    var $unread_msg_count = [];
    var $unread_support_msg_count = [];
    var $whole_noti = '';
    var $new_noti = true;
    var $show_beep_for_msg = false;
    var $show_beep_for_support_msg = false;
    var $is_module_allowed = <?= is_module_allowed('chat') ? 1 : 0 ?>;
    var $is_admin = <?= $this->ion_auth->is_admin() ? 1 : 0 ?>;
    var $permissions_chat_view = <?= permissions('chat_view') ?>;
    var $permissions_notifications = <?= permissions('notification_view_all') ? permissions('notification_view_all') : 0 ?>;
    var $in_group3 = <?= $this->ion_auth->in_group(3) ? 1 : 0 ?>;

    $.ajax({
      type: "POST",
      url: base_url + 'notifications/get_live_notifications',
      dataType: "json",
      success: function(result) {
        console.log(result);
        if (result['error'] == false) {
          $notifications = result['data']['notifications'];
          $unread_msg_count = result['data']['unread_msg_count'];
          $unread_support_msg_count = result['data']['unread_support_msg_count'];
          if ($is_module_allowed && ($unread_msg_count.length || $unread_support_msg_count.length) && ($is_admin || $permissions_chat_view || $permissions_notifications || $in_group3)) {
            if ($unread_msg_count.length) {
              $show_beep_for_msg = true;
            }
            if ($unread_support_msg_count.length) {
              $show_beep_for_support_msg = true;
            }
          }

          var $new_support_message_received = '';
          if ($show_beep_for_support_msg) {
            $new_noti = false;
            $new_support_message_received = '<a href="<?= base_url('support') ?>" class="dropdown-item dropdown-item-unread"><figure class="dropdown-item-icon avatar avatar-m bg-primary text-white fa fa-question-circle"></figure><h6 class="dropdown-item-desc m-2"><?= $this->lang->line('new_support_message_received') ? htmlspecialchars($this->lang->line('new_support_message_received')) : 'New support message received' ?></h6></a>';
          }

          var $new_message = '';
          if ($show_beep_for_msg) {
            $new_noti = false;
            $new_message = '<li><a href="'+base_url+'chat'+'"><div class="timeline-panel"><div class="media me-2 media-primary"><i class="fa-solid fa-comment"></i></div><div class="media-body"><h6 class="mb-1"><?= $this->lang->line('new_message') ? $this->lang->line('new_message') : 'New Message' ?></h6> </div> </div></a></li>';

            // $new_message = '<a href="<?= base_url('chat') ?>" class="dropdown-item dropdown-item-unread"><h6 class="dropdown-item-desc m-2"></h6></a>';
          }

          if ($notifications.length) {
            $new_noti = false;
            $.each($notifications, function(key, notification) {


              var $profile = '';
              var $file_upload_path = '';

              if (typeof(notification['profile']) != "undefined" && notification['profile'] !== null && notification['profile'] !== '') {
                if (doesFileExist(base_url + 'assets/uploads/profiles/' + notification['profile'])) {
                  $file_upload_path = 'assets/uploads/f<?= $this->session->userdata('saas_id') ?>/profiles/' + notification['profile'];
                } else {
                  $file_upload_path = 'assets/uploads/profiles/' + notification['profile'];
                }
                $profile = '<div class="media me-2"><img alt="' + notification['first_name'] + ' ' + notification['last_name'] + '" width="50" src="' + base_url + '' + $file_upload_path + '"></div>';
              } else {
                $profile = '<div class="media me-2 media-primary">' + notification['first_name'].substr(0, 1) + '' + notification['last_name'].substr(0, 1) + '</div>';
              }
              $whole_noti += '<li><a href="'+notification["notification_url"]+'"><div class="timeline-panel"> ' + $profile + ' <div class="media-body"> <h6 class="mb-1">' + notification['notification'] + '</h6><small class="d-block">' + notification['created'] + '</small> </div> </div></a></li>';
            });

          } else {
            if ($new_noti) {
              $whole_noti = '<a class="dropdown-item dropdown-item-unread"><div class="dropdown-item-desc  ml-2"><?= $this->lang->line('no_new_notifications') ? $this->lang->line('no_new_notifications') : 'No new notifications.' ?></div></a>';
            }
          }

          if ($notifications || $show_beep_for_msg || $show_beep_for_support_msg) {
            $("#show_beep_new_live_notifications").addClass('beep');
          } else {
            $("#show_beep_new_live_notifications").removeClass('beep');
          }
          $("#new_live_notifications").html($new_support_message_received + ' ' + $new_message + ' ' + $whole_noti);

        } else {
          $("#show_beep_new_live_notifications").removeClass('beep');
          $("#new_live_notifications").html('<a class="dropdown-item dropdown-item-unread"><div class="dropdown-item-desc  ml-2"><?= $this->lang->line('no_new_notifications') ? $this->lang->line('no_new_notifications') : 'No new notifications.' ?></div></a>');
        }
      },
      error: function() {
        $("#show_beep_new_live_notifications").removeClass('beep');
        $("#new_live_notifications").html('<a class="dropdown-item dropdown-item-unread"><div class="dropdown-item-desc  ml-2"><?= $this->lang->line('no_new_notifications') ? $this->lang->line('no_new_notifications') : 'No new notifications.' ?></div></a>');
      }
    });
    setTimeout(get_live_notifications, 10000);

  }

  get_live_notifications();
</script>
<script>
  function showLoader() {
    $("#main-wrapper").removeClass("show");
    $("#loader").css("display", "flex");
  }

  // Function to hide the loader
  function hideLoader() {
    setTimeout(function() {
      $("#main-wrapper").addClass("show");
      $("#loader").css("display", "none");
    }, 400);
  }

  var ModelProgress = '<div class="loader-progress"><div class="overlay"></div><span class="loader2"></span></div>';
</script>
<script>
  function checkSprintDate() {
    $.ajax({
      type: "POST",
      url: base_url + 'backlog/complete_sprint_ascnr',
      data: {
        moveToBacklog: 1
      },
      dataType: "json",
      success: function(result) {
        // console.log(result);
      }
    });

  }
  checkSprintDate();
</script>

<script>
  $(document).ready(function() {
    <?php
    if (is_null($this->session->userdata('show_demo')) && $this->ion_auth->is_admin()) {
    ?>
      if (localStorage.getItem('tourStep') != 0) {
        $('#myModel').modal('show');
      }
      $(document).on('click', '#CancelGuide', function(e) {
        localStorage.setItem('tourStep', '0');
        $('#myModel').modal('hide');
      });
    <?php
    }
    ?>
  });
</script>