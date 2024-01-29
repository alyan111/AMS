<?php $this->load->view('includes/header'); ?>
<style>

</style>
</head>

<body>

  <!--*******************
        Preloader start
    ********************-->
  <div id="preloader">
    <div class="lds-ripple">
      <div></div>
      <div></div>
    </div>
  </div>
  <!--*******************
        Preloader end
    ********************-->
  <?php $this->load->view('includes/sidebar'); ?>
  <!--**********************************
        Main wrapper start
    ***********************************-->
  <div id="main-wrapper">
    <div class="content-body default-height">
      <!-- row -->
      <div class="container-fluid">

        <div class="row">
          <div class="col-xl-2 col-sm-3 mt-2">
            <a href="#" id="modal-add-leaves" data-bs-toggle="modal" data-bs-target="#basicModal" class="btn btn-block btn-primary">+ ADD</a>
          </div>
          <div class="col-lg-12 mt-3">
            <div class="card">
              <div class="card-body">
                <div class="basic-form">
                  <form class="row">
                    <div class="col-lg-3 mb-3">
                      <select class="form-select" id="employee_id">
                        <option value=""><?= $this->lang->line('employee') ? $this->lang->line('employee') : 'Employee' ?></option>
                        <?php foreach ($system_users as $system_user) {
                          if ($system_user->saas_id == $this->session->userdata('saas_id') && $system_user->active == '1' && $system_user->finger_config == '1') { ?>
                            <option value="<?= $system_user->employee_id ?>"><?= htmlspecialchars($system_user->first_name) ?> <?= htmlspecialchars($system_user->last_name) ?></option>
                        <?php }
                        } ?>
                      </select>
                    </div>
                    <div class="col-lg-3 mb-3">
                      <select class="form-select" id="leave_type">
                        <option value=""><?= $this->lang->line('leave_type') ? $this->lang->line('leave_type') : 'Leave type' ?></option>
                        <?php foreach ($leaves_types as $leaves_type) : ?>
                          <option value="<?= $leaves_type["id"] ?>"><?= htmlspecialchars($leaves_type["name"]) ?></option>
                        <?php endforeach ?>
                      </select>
                    </div>
                    <div class="col-lg-3 mb-3">
                      <select class="form-select" id="status">
                        <option value="" selected>Status</option>
                        <option value="1">Approved</option>
                        <option value="3">Pending</option>
                        <option value="2">Rejected</option>
                      </select>
                    </div>
                    <div class="col-lg-3 mb-3">
                      <select class="form-select" id="dateFilter">
                        <option value="tmonth" selected>This Month</option>
                        <option value="lmonth">Last Month</option>
                        <option value="tyear">This Year</option>
                        <option value="lyear">last Year</option>
                      </select>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-body">
                <div class="table-responsive">
                  <table id="leave_list" class="table table-sm mb-0">
                    <thead>
                    </thead>
                    <tbody id="customers">
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- *******************************************
  Footer -->
    <?php $this->load->view('includes/footer'); ?>
    <!-- ************************************* *****
    Model forms
  ****************************************************-->
    <div class="modal fade" id="basicModal">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Create</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal">
            </button>
          </div>
          <form action="<?= base_url('leaves/create') ?>" method="POST" class="modal-part" id="modal-add-leaves-part" data-title="<?= $this->lang->line('create') ? $this->lang->line('create') : 'Create' ?>" data-btn="<?= $this->lang->line('create') ? $this->lang->line('create') : 'Create' ?>">
            <div class="modal-body">
              <?php if ($this->ion_auth->in_group(1) || permissions('leaves_view_all') || permissions('leaves_view_selected')) { ?>
                <div class="form-group mb-3">
                  <label class="required"><?= $this->lang->line('team_members') ? $this->lang->line('team_members') : 'Users' ?></label>
                  <select name="user_id_add" id="user_id_add" class="form-control select2">
                    <option value=""><?= $this->lang->line('select_users') ? $this->lang->line('select_users') : 'Select Users' ?></option>
                    <?php foreach ($system_users as $system_user) {
                      if ($system_user->saas_id == $this->session->userdata('saas_id') && ($system_user->finger_config == '1')) { ?>
                        <option value="<?= $system_user->id ?>"><?= htmlspecialchars($system_user->first_name) ?> <?= htmlspecialchars($system_user->last_name) ?></option>
                    <?php }
                    } ?>
                  </select>
                </div>
              <?php } ?>

              <div class="form-group mb-3">
                <label class="required"><?= $this->lang->line('type') ? $this->lang->line('type') : 'Type' ?></label>
                <select class="form-control select2" name="type_add" id="type_add">
                  <option value=""><?= $this->lang->line('select_type') ? $this->lang->line('select_type') : 'Select Type' ?></option>
                  <?php foreach ($leaves_types as $leaves) { ?>
                    <option value="<?= $leaves['id'] ?>"><?= $leaves['name'] ?></option>
                  <?php
                  } ?>
                </select>
              </div>

              <?php if ($this->ion_auth->in_group(1) || permissions('leaves_view_all') || permissions('leaves_view_selected')) { ?>
                <div class="form-group mb-3">
                  <label><?= $this->lang->line('paid_unpaid') ? $this->lang->line('paid_unpaid') : 'Paid / Unpaid Leave' ?></label>
                  <select name="paid" id="paidUnpaid" class="form-control select2">
                    <option value="0"><?= $this->lang->line('paid') ? $this->lang->line('paid') : 'Paid Leave' ?></option>
                    <option value="1"><?= $this->lang->line('unpaid') ? $this->lang->line('unpaid') : 'Unpaid Leave' ?></option>
                  </select>
                </div>
              <?php } ?>

              <div class="form-group form-check form-check-inline col-md-6 md-3 mb-3">
                <input class="form-check-input" type="checkbox" id="half_day" name="half_day">
                <label class="form-check-label text-danger" for="half_day"><?= $this->lang->line('half_day') ? $this->lang->line('half_day') : 'Half Day' ?></label>
              </div>

              <div class="form-group form-check form-check-inline col-md-5 mb-3">
                <input class="form-check-input" type="checkbox" id="short_leave" name="short_leave">
                <label class="form-check-label text-danger" for="short_leave"><?= $this->lang->line('short_leave') ? $this->lang->line('short_leave') : 'Short Leave' ?></label>
              </div>

              <div id="date_fields">
                <div id="full_day_dates" class="row">
                  <div class="col-md-6 form-group mb-3">
                    <label><?= $this->lang->line('starting_date') ? $this->lang->line('starting_date') : 'Starting Date' ?><span class="text-danger">*</span></label>
                    <input type="text" id="starting_date_create" name="starting_date" class="form-control datepicker-default required" required="">
                  </div>
                  <div class="col-md-6 form-group mb-3">
                    <label><?= $this->lang->line('ending_date') ? $this->lang->line('ending_date') : 'Ending Date' ?><span class="text-danger">*</span></label>
                    <input type="text" id="ending_date_create" name="ending_date" class="form-control datepicker-default required" required="">
                  </div>
                </div>
                <div id="half_day_date" class="row" style="display: none;">
                  <div class="col-md-6 form-group mb-3">
                    <label><?= $this->lang->line('date') ? $this->lang->line('date') : 'Date' ?><span class="text-danger">*</span></label>
                    <input type="text" id="date_half" name="date_half" class="form-control datepicker-default required" required="">
                  </div>
                  <div class="col-md-6 form-group mb-3">
                    <label><?= $this->lang->line('time') ? $this->lang->line('time') : 'Time' ?><span class="text-danger">*</span></label>
                    <select name="half_day_period" class=" form-group form-control select2">
                      <option value="0">First Time</option>
                      <option value="1">Second Time</option>
                    </select>
                  </div>
                </div>
                <div id="short_leave_dates" class="row" style="display: none;">
                  <div class="col-md-4 form-group mb-3">
                    <label><?= $this->lang->line('date') ? $this->lang->line('date') : 'Date' ?><span class="text-danger">*</span></label>
                    <input type="text" id="date" name="date" class="form-control datepicker-default required" required="">
                  </div>
                  <div class="col-md-4 form-group mb-3">
                    <label><?= $this->lang->line('starting_time') ? $this->lang->line('starting_time') : 'Starting Time' ?><span class="text-danger">*</span></label>
                    <input type="text" name="starting_time" id="starting_time_create" class="form-control timepicker" required="">
                  </div>
                  <div class="col-md-4 form-group mb-3">
                    <label><?= $this->lang->line('ending_time') ? $this->lang->line('ending_time') : 'Ending Time' ?><span class="text-danger">*</span></label>
                    <input type="text" name="ending_time" id="ending_time_create" class="form-control timepicker" required="">
                  </div>
                </div>
              </div>

              <div class="form-group mb-3">
                <div class="mb-3">
                  <label><?= $this->lang->line('Document') ? $this->lang->line('Document') : 'Document' ?> <i class="fas fa-question-circle" data-toggle="tooltip" data-placement="right" title="<?= $this->lang->line('if_any_leave_document') ? $this->lang->line('if_any_leave_document') : "If any Document according to leave/s." ?>"></i></label>
                  <input class="form-control" type="file" id="formFile">
                </div>
              </div>
              <div class="form-group mb-3">
                <label><?= $this->lang->line('leave_reason') ? $this->lang->line('leave_reason') : 'Leave Reason' ?><span class="text-danger">*</span></label>
                <textarea type="text" name="leave_reason" class="form-control" required=""></textarea>
              </div>

              <div id="leaves_count" class="row text-center">
                <div class="col-md-4 form-group mb-3">
                  <label><?= $this->lang->line('total_leaves') ? $this->lang->line('total_leaves') : 'Total Leaves' ?><i class="fas fa-question-circle" data-toggle="tooltip" data-placement="right" title="<?= $this->lang->line('the_total_leaves_are_in_year_and_are_from_1st_Jan_to_31st_Dec_of_this_year') ? $this->lang->line('the_total_leaves_are_in_year_and_are_from_1st_Jan_to_31st_Dec_of_this_year') : "The Total leaves are in year and are from 1st Jan to 31st Dec of this year." ?>"></i></label>
                  <input type="number" style="border: none;" id="total_leaves" name="total_leaves" class="form-control text-center" required="" readonly>
                </div>
                <div class="col-md-4 form-group mb-3">
                  <label><?= $this->lang->line('consumed_leaves') ? $this->lang->line('consumed_leaves') : 'Consumed Leaves' ?></label>
                  <input type="number" style="border: none;" id="consumed_leaves" name="consumed_leaves" class="form-control text-center" required="" readonly>
                </div>
                <div class="col-md-4 form-group mb-3">
                  <label><?= $this->lang->line('remaining_leaves') ? $this->lang->line('remaining_leaves') : 'Remaining Leaves' ?></label>
                  <input style="border: none;" type="number" id="remaining_leaves" name="remaining_leaves" class="form-control text-center" required="" readonly>
                </div>
              </div>
            </div>
            <div class="modal-footer d-flex justify-content-center">
              <div class="col-lg-4">
                <button type="submit" class="btn btn-create btn-block btn-primary">Create</button>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
    <!--**********************************
	Content body end
***********************************-->
  </div>
  <?php $this->load->view('includes/scripts'); ?>
  <script>
    $(document).ready(function() {
      setFilter();
      $(document).on('change', '#leave_type, #status, #employee_id,#dateFilter, #from,#too', function() {
        setFilter();
      });

      function setFilter() {
        var employee_id = $('#employee_id').val();
        var leave_type = $('#leave_type').val();
        var filterOption = $('#dateFilter').val();
        var status = $('#status').val();

        const today = new Date();
        const year = today.getFullYear();
        const month = today.getMonth();
        const day = today.getDate();

        let fromDate, toDate;

        switch (filterOption) {
          case "today":
            fromDate = new Date(year, month, day);
            toDate = new Date(year, month, day);
            break;
          case "ystdy":
            fromDate = new Date(year, month, day - 1);
            toDate = new Date(year, month, day - 1);
            break;
          case "tweek":
            fromDate = new Date(year, month, day - today.getDay());
            toDate = new Date(year, month, day);
            break;
          case "lweek":
            fromDate = new Date(year, month, day - today.getDay() - 7);
            toDate = new Date(year, month, day - today.getDay() - 1);
            break;
          case "tmonth":
            fromDate = new Date(year, month, 1);
            toDate = today;
            break;
          case "lmonth":
            fromDate = new Date(year, month - 1, 1);
            toDate = new Date(year, month, 0);
            break;
          case "tyear":
            fromDate = new Date(year, 0, 1);
            toDate = today;
            break;
          case "lyear":
            fromDate = new Date(year - 1, 0, 1);
            toDate = new Date(year - 1, 11, 31);
            break;
          default:
            console.error("Invalid filter option:", filterOption);
            return null;
        }

        // Format dates as strings
        var formattedFromDate = formatDate(fromDate, "Y-m-d");
        var formattedToDate = formatDate(toDate, "Y-m-d");
        ajaxCall(employee_id, leave_type, status, formattedFromDate, formattedToDate);
      }

      function ajaxCall(employee_id, leave_type, status, from, too) {
        $.ajax({
          url: '<?= base_url('leaves/get_leaves') ?>',
          type: 'GET',
          data: {
            user_id: employee_id,
            leave_type: leave_type,
            status: status,
            from: from,
            too: too
          },
          success: function(response) {
            var tableData = JSON.parse(response);
            console.log(tableData);
            showTable(tableData);
          },
          complete: function() {},
          error: function(error) {
            console.error(error);
          }
        });
      }

      function showTable(data) {
        var table = $('#leave_list');
        if ($.fn.DataTable.isDataTable(table)) {
          table.DataTable().destroy();
        }
        emptyDataTable(table);
        var thead = table.find('thead');
        var theadRow = '<tr>';
        theadRow += '<th style="font-size: 15px;">ID</th>';
        theadRow += '<th style="font-size: 15px;">Employee Name</th>';
        theadRow += '<th style="font-size: 15px;">Type</th>';
        theadRow += '<th style="font-size: 15px;">Reason</th>';
        theadRow += '<th style="font-size: 15px;">Duration</th>';
        theadRow += '<th style="font-size: 15px;">Starting Time</th>';
        theadRow += '<th style="font-size: 15px;">Ending Time</th>';
        theadRow += '<th style="font-size: 15px;">Paid</th>';
        theadRow += '<th style="font-size: 15px;">Status</th>';
        theadRow += '<th style="font-size: 15px;">Action</th>';
        theadRow += '</tr>';
        thead.html(theadRow);

        // Add table body
        var tbody = table.find('tbody');

        data.forEach(user => {
          var userRow = '<tr>';
          userRow += '<td style="font-size:13px;">' + user.user_id + '</td>';
          userRow += '<td style="font-size:13px;">' + user.user + '</td>';
          userRow += '<td style="font-size:13px;">' + user.name + '</td>';
          userRow += '<td style="font-size:13px;">' + user.leave_reason + '</td>';
          userRow += '<td style="font-size:13px;">' + user.leave_duration + '</td>';
          userRow += '<td style="font-size:13px;">' + user.starting_date + ' ' + user.starting_time + '</td>';
          userRow += '<td style="font-size:13px;">' + user.ending_date + ' ' + user.ending_time + '</td>';
          userRow += '<td style="font-size:13px;">' + user.paid + '</td>';
          userRow += '<td style="font-size:13px;">' + user.status + '</td>';
          userRow += '<td>';
          userRow += '<div class="d-flex">';
          userRow += '<span class="badge light badge-primary"><a href="javascript:void()" class="text-primary" data-bs-toggle="tooltip" data-placement="top" title="Edit"><i class="fa fa-pencil color-muted"></i></a></span>';
          userRow += '<span class="badge light badge-danger ms-2"><a href="javascript:void()" class="text-danger" data-bs-toggle="tooltip" data-placement="top" title="Delete"><i class="fas fa-trash"></i></a></span>';
          userRow += '</div>';
          userRow += '</td>';
          userRow += '</tr>';
          tbody.append(userRow);
        });
        table.DataTable({
          "paging": true,
          "searching": false,
          "language": {
            "paginate": {
              "next": '<i class="fa fa-angle-double-right" aria-hidden="true"></i>',
              "previous": '<i class="fa fa-angle-double-left" aria-hidden="true"></i>'
            }
          },
          "info": false,
          "dom": '<"top"i>rt<"bottom"lp><"clear">',
          "lengthMenu": [5, 10, 20],
          "pageLength": 5
        });
      }

      function emptyDataTable(table) {
        table.find('thead').empty();
        table.find('tbody').empty();
      }

      function formatDate(date, format) {
        const options = {
          year: 'numeric',
          month: '2-digit',
          day: '2-digit'
        };
        const formattedDate = date.toLocaleDateString('en-US', options);
        return format
          .replace("Y", date.getFullYear())
          .replace("m", formattedDate.slice(0, 2))
          .replace("d", formattedDate.slice(3, 5));
      }
      $('#half_day').change(function() {
        if ($(this).is(':checked')) {
          $('#full_day_dates').hide();
          $('#short_leave').prop('checked', false);
          $('#short_leave_dates').hide();
          $('#half_day_date').show();
        } else {
          $('#full_day_dates').show();
          $('#half_day_date').hide();
        }
      });

      $('#short_leave').change(function() {
        if ($(this).is(':checked')) {
          $('#full_day_dates').hide();
          $('#half_day').prop('checked', false);
          $('#half_day_date').hide();
          $('#short_leave_dates').show();
        } else {
          $('#full_day_dates').show();
          $('#short_leave_dates').hide();
        }
      });

    });
  </script>

  <script>
    $(document).ready(function() {

      $('select[name="user_id_add"]').on('change', function() {
        updateLeaveCounts();
      });

      $('select[name="type_add"]').on('change', function() {
        updateLeaveCounts();
      });

      $('.btn-create').on('click', function() {
        updateLeaveCounts();
      });

      function updateLeaveCounts() {

        var type = $('select[name="type_add"]').val();
        var user_id = $('select[name="user_id_add"]').val();

        $.ajax({
          url: '<?= base_url('leaves/get_leaves_count') ?>',
          method: 'POST',
          dataType: 'json',
          data: {
            user_id: user_id,
            type: type
          },
          success: function(response) {
            console.log(response);
            var totalLeaves = response.total_leaves;
            var consumedLeaves = response.consumed_leaves;
            var remainingLeaves = response.remaining_leaves;
            var query = response.query;

            $('#total_leaves').val(totalLeaves);
            $('#consumed_leaves').val(consumedLeaves);
            if (remainingLeaves == 0) {
              $('#paidUnpaid').prop('disabled', true);
              $('#paidUnpaid').val('1');
              $("#paidUnpaid").trigger("change");
            } else {
              $('#paidUnpaid').prop('disabled', false);
              $('#paidUnpaid').val('0');
              $("#paidUnpaid").trigger("change");
            }
            $('#remaining_leaves').val(remainingLeaves);
          },
        });
      }

      updateLeaveCounts();

    });
    // leaves
$("#basicModal").on('click', '.submitFormBtn', function(e) {
    var modal = $('#basicModal');
    var form = $('#modal-add-leaves-part');
    var formData = new FormData(form);
    $.ajax({
        type: 'POST',
        url: form.attr('action'),
        data: formData,
        cache: false,
        contentType: false,
        processData: false,
        dataType: "json",
        success: function(result) {
            if (result['error'] == false) {
                location.reload();
            } else {
                console.log(result['as']);
                console.log(result['starting_time']);
                modal.find('.modal-body').append('<div class="alert alert-danger">' + result['message'] + '</div>');
            }
            modal.find('.modal-body').find('.alert').delay(4000).fadeOut();
        }
    });

    e.preventDefault();
});
  </script>

</body>

</html>