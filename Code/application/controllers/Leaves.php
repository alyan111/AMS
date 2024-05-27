<?php defined('BASEPATH') or exit('No direct script access allowed');

class Leaves extends CI_Controller
{
	public $data = [];

	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		if ($this->ion_auth->logged_in()  && is_module_allowed('leaves') && ($this->ion_auth->in_group(1) || permissions('leaves_view'))) {
			$this->data['page_title'] = 'Leaves - ' . company_name();
			$this->data['main_page'] = 'Leaves Application';
			$this->data['current_user'] = $this->ion_auth->user()->row();
			if ($this->ion_auth->is_admin() || permissions('leaves_view_all')) {
				$this->data['system_users'] = $this->ion_auth->members()->result();
			} elseif (permissions('leaves_view_selected')) {
				$selected = selected_users();
				foreach ($selected as $user_id) {
					$users[] = $this->ion_auth->user($user_id)->row();
				}
				$users[] = $this->ion_auth->user($this->session->userdata('user_id'))->row();
				$this->data['system_users'] = $users;
			}
			$saas_id = $this->session->userdata('saas_id');
			$this->db->where('saas_id', $saas_id);
			$query = $this->db->get('leaves_type');
			$this->data['leaves_types'] = $query->result_array();
			// echo json_encode($this->data["leaves_types"]);
			$this->load->view('leaves', $this->data);
		} else {
			redirect('auth', 'refresh');
		}
	}

	public function delete($id = '')
	{
		if ($this->ion_auth->logged_in() && ($this->ion_auth->in_group(1) || permissions('leaves_view'))) {

			if (empty($id)) {
				$id = $this->uri->segment(3) ? $this->uri->segment(3) : '';
			}

			if (!empty($id) && is_numeric($id) && $this->leaves_model->delete($id)) {
				$this->session->set_flashdata('message', $this->lang->line('deleted_successfully') ? $this->lang->line('deleted_successfully') : "Deleted successfully.");
				$this->session->set_flashdata('message_type', 'success');
				$this->data['error'] = false;
				$this->data['message'] = $this->lang->line('deleted_successfully') ? $this->lang->line('deleted_successfully') : "Deleted successfully.";
				echo json_encode($this->data);
			} else {

				$this->data['error'] = true;
				$this->data['message'] = $this->lang->line('something_wrong_try_again') ? $this->lang->line('something_wrong_try_again') : "Something wrong! Try again.";
				echo json_encode($this->data);
			}
		} else {
			$this->data['error'] = true;
			$this->data['message'] = $this->lang->line('access_denied') ? $this->lang->line('access_denied') : "Access Denied";
			echo json_encode($this->data);
		}
	}


	public function edit()
	{
		if ($this->ion_auth->logged_in() && ($this->ion_auth->in_group(1) || permissions('leaves_view'))) {
			$this->form_validation->set_rules('update_id', 'Leave ID', 'trim|required|strip_tags|xss_clean|is_numeric');
			$this->form_validation->set_rules('leave_reason', 'Leave Reason', 'trim|required|strip_tags|xss_clean');

			if ($this->form_validation->run() == TRUE) {

				$employeeIdQuery = $this->db->select('employee_id')->get_where('users', array('id' => $this->input->post('user_id') ? $this->input->post('user_id') : $this->session->userdata('user_id')));
				if ($employeeIdQuery->num_rows() > 0) {
					$employeeIdRow = $employeeIdQuery->row();
					$employeeId = $employeeIdRow->employee_id;
					$data['user_id'] = $employeeId;
				}
				$data['leave_reason'] = $this->input->post('leave_reason');
				$data['type'] = $this->input->post('type');
				$data['paid'] = $this->input->post('paid');

				$this->db->where('leave_id',  $this->input->post('update_id'));
				$this->db->order_by('level', 'desc');
				$this->db->limit(1);
				$query = $this->db->get('leave_logs');
				$leave = $query->row();
				$step = $leave->level;

				/*
			*
			*	highest role??
			*
			*/
				$this->db->where('saas_id', $this->session->userdata('saas_id'));
				$this->db->order_by('step_no', 'desc');
				$this->db->limit(1);
				$heiQuery = $this->db->get('leave_hierarchy');
				$heiResult = $heiQuery->row();
				$highStep = $heiResult->step_no;
				/*
			*
			* 	current step Approver/Recommender
			*/
				$this->db->where('saas_id', $this->session->userdata('saas_id'));
				$this->db->where('step_no', $step);
				$this->db->limit(1);
				$AppQuery = $this->db->get('leave_hierarchy');
				$appResult = $AppQuery->row();
				$AppOrRec = $appResult->recomender_approver;
				if ($highStep == $step || $AppOrRec == 'approver') {
					$data['status'] = $this->input->post('status');
				} else {
					$data['status'] = '0';
				}

				$shiftIdQuery = $this->db->select('shift_id')->get_where('users', array('id' => $this->input->post('user_id') ? $this->input->post('user_id') : $this->session->userdata('user_id')));
				$shiftIdRow = $shiftIdQuery->row();
				$shiftId = $shiftIdRow->shift_id;

				if ($shiftId !== '0') {
					$shiftQuery = $this->db->get_where('shift', array('id' => $shiftId));
					$shiftRow = $shiftQuery->row();
					$checkInDept = $shiftRow->starting_time;
					$breakEndDept = $shiftRow->break_end;
					$breakStartDept = $shiftRow->break_start;
					$checkOutDept = $shiftRow->ending_time;
				} else {
					$defaultShiftQuery = $this->db->get_where('shift', array('id' => 1));
					$defaultShiftRow = $defaultShiftQuery->row();
					$checkInDept = $defaultShiftRow->starting_time;
					$breakEndDept = $defaultShiftRow->break_end;
					$breakStartDept = $defaultShiftRow->break_start;
					$checkOutDept = $defaultShiftRow->ending_time;
				}
				$timeValidated = true;
				if ($this->input->post('half_day')) {
					$half_day_period = $this->input->post('half_day_period');
					$startingTime = $half_day_period === "0" ? $checkInDept : $breakEndDept;
					$endingTime = $half_day_period === "0" ? $breakStartDept : $checkOutDept;
					$half_day_period = $half_day_period === "0" ? "First Time" : "Second Time";
					$data['starting_date'] = date("Y-m-d", strtotime($this->input->post('date_half')));
					$data['ending_date'] = date("Y-m-d", strtotime($this->input->post('date_half')));
					$data['starting_time'] = date("H:i:s", strtotime($startingTime));
					$data['ending_time'] = date("H:i:s", strtotime($endingTime));
					$data['leave_duration'] = $half_day_period . " Half Day";
					if (strtotime($checkInDept) > strtotime($checkOutDept)) {
						$tempStartingDate = date("Y-m-d", strtotime($this->input->post('date_half')));
						$tempEndingDate = date("Y-m-d", strtotime("+1 day", strtotime($tempStartingDate)));

						if ($half_day_period === "Second Time" && strtotime($startingTime) >= strtotime('00:00:00', strtotime($tempStartingDate))) {
							$startingDate = $tempEndingDate;
						}

						if (strtotime($endingTime) >= strtotime('00:00:00', strtotime($tempStartingDate))) {
							$endingDate = $tempEndingDate;
						}
						$data['starting_date'] = $startingDate;
						$data['ending_date'] = $endingDate;
					}
				} elseif ($this->input->post('short_leave')) {
					if (date("H:i:s", strtotime($this->input->post('starting_time'))) < date("H:i:s", strtotime($this->input->post('ending_time')))) {
						$data['starting_date'] = date("Y-m-d", strtotime($this->input->post('date')));
						$data['ending_date'] = date("Y-m-d", strtotime($this->input->post('date')));
						$data['starting_time'] = date("H:i:s", strtotime($this->input->post('starting_time')));
						$data['ending_time'] = date("H:i:s", strtotime($this->input->post('ending_time')));
						$startingTime = strtotime($this->input->post('starting_time'));
						$endingTime = strtotime($this->input->post('ending_time'));
						$durationSeconds = $endingTime - $startingTime;
						$durationHours = floor($durationSeconds / 3600);
						$durationMinutes = floor(($durationSeconds % 3600) / 60);
						$data['leave_duration'] = $durationHours . " hrs " . $durationMinutes . " mins " . " Short Leave";

						if (strtotime($checkInDept) > strtotime($checkOutDept)) {
							$startingDate = date("Y-m-d", strtotime($this->input->post('date')));
							$endingDate = date("Y-m-d", strtotime($this->input->post('date')));
							$tempEndingDate = date("Y-m-d", strtotime("+1 day", strtotime($startingDate)));

							if ($startingTime >= strtotime('00:00:00', strtotime($tempEndingDate))) {
								$startingDate = $tempEndingDate;
							}

							if ($endingTime >= strtotime('00:00:00', strtotime($startingDate))) {
								$endingDate = $tempEndingDate;
							}

							if ($startingTime < $endingTime) {
								$startingDate = $tempEndingDate;
								$endingDate = $tempEndingDate;
							}


							if ($endingTime < $startingTime) {
								$startOfDay = strtotime('00:00:00', strtotime($data['starting_date']));
								$durationFirstDay = strtotime('23:59:59', strtotime($data['starting_date'])) - $startingTime;

								$endOfDay = strtotime('23:59:59', strtotime($data['ending_date']));
								$durationSecondDay = $endingTime - $startOfDay;

								$durationSeconds = $durationFirstDay + $durationSecondDay;
							} else {
								$durationSeconds = $endingTime - $startingTime;
							}
							$durationHours = floor($durationSeconds / 3600);
							$durationMinutes = floor(($durationSeconds % 3600) / 60);
							$data['leave_duration'] = $durationHours . " hrs " . $durationMinutes . " mins " . " Short Leave";
							$data['starting_date'] = $startingDate;
							$data['ending_date'] = $endingDate;
						}
						if (($durationHours < 3) || ($durationHours == 3 && $durationMinutes == 0)) {
							$timeValidated = true;
						} else {
							$timeValidated = false;
						}
					} else {
						$timeValidated = false;
					}
				} else {
					$starting_date = $this->input->post('starting_date');
					$ending_date = $this->input->post('ending_date');
					$data['starting_date'] = date("Y-m-d", strtotime($starting_date));
					$data['ending_date'] = date("Y-m-d", strtotime($ending_date));
					$data['starting_time'] = format_date($checkInDept, "H:i:s");
					$data['ending_time'] = format_date($checkOutDept, "H:i:s");

					$diffInSeconds = strtotime($data['ending_date']) - strtotime($data['starting_date']);
					$leave_duration = 1 + round(abs($diffInSeconds) / 86400);
					$data['leave_duration'] = $leave_duration . ($leave_duration > 1 ? " Full Days" : " Full Day");


					if (strtotime($checkInDept) > strtotime($checkOutDept)) {
						$data['ending_date'] = date("Y-m-d", strtotime("+1 day", strtotime($starting_date)));
					}

					$missing_finger_days = 0;
					$finger_count = 0;
					$holiday_count = 0;
					$current_date = new DateTime($starting_date);
					$end_date = new DateTime($ending_date);
					$user_id = $this->input->post('user_id') ? $this->input->post('user_id') : $this->session->userdata('user_id');
					$employee_id_query = $this->db->query("SELECT employee_id FROM users WHERE id = $user_id");
					$employee_id_result = $employee_id_query->row_array();
					$employee_id = $employee_id_result['employee_id'];
					while ($current_date < $end_date) {
						$formatted_date = $current_date->format('Y-m-d');
						$execution = false;

						$holidayQuery = $this->db->query("SELECT * FROM holiday");
						$holidays = $holidayQuery->result_array();


						foreach ($holidays as $value4) {
							$startDate = $value4["starting_date"];
							$endDate = $value4["ending_date"];
							$apply = $value4["apply"];
							$startDateTimestamp  = strtotime($startDate);
							$endDateTimestamp  = strtotime($endDate);
							$dateToCheckTimestamp  = strtotime($formatted_date);
							if ($apply == '1' && $dateToCheckTimestamp >= $startDateTimestamp && $dateToCheckTimestamp <= $endDateTimestamp) {
								$departments = json_decode($value4["department"]);
								foreach ($departments as $department) {
									$user_ids_query = $this->db->query("SELECT * FROM users WHERE department = $department AND employee_id= $employee_id");
									$user_ids_result = $user_ids_query->result_array();
									if (count($user_ids_result) > 0) {
										if (!$execution) {
											$missing_finger_days++;
											$execution = true;
										}
									}
								}
							} elseif ($apply == '2' && $dateToCheckTimestamp >= $startDateTimestamp && $dateToCheckTimestamp <= $endDateTimestamp) {
								$holidayUsers = json_decode($value4["users"]);
								foreach ($holidayUsers as $holidayUser) {
									$user_ids_query = $this->db->query("SELECT * FROM users WHERE id = $holidayUser AND employee_id= $employee_id");
									$user_ids_result = $user_ids_query->result_array();
									if (count($user_ids_result) > 0) {
										if (!$execution) {
											$missing_finger_days++;
											$execution = true;
										}
									}
								}
							}
						}


						$current_date->modify('+1 day');
					}
					$data['leave_duration'] = ($data['leave_duration'] - $missing_finger_days) . " Full Day/s";
				}


				if ($timeValidated) {
					if ($this->input->post('document') && !empty($_FILES['documents']['name'])) {
						if (file_exists('assets/uploads/leaves/' . $this->input->post('document'))) {
							$file_upload_path = 'assets/uploads/leaves/' . $this->input->post('document');
						} else {
							$file_upload_path = 'assets/uploads/f' . $this->session->userdata('saas_id') . '/leaves/' . $this->input->post('document');
							unlink($file_upload_path);
						}
					}
					if (!empty($_FILES['documents']['name'])) {
						$upload_path = 'assets/uploads/f' . $this->session->userdata('saas_id') . '/leaves/';

						if (!is_dir($upload_path)) {
							mkdir($upload_path, 0775, true);
						}
						$config['upload_path'] = $upload_path;
						$config['allowed_types'] = '*';
						$config['overwrite'] = false;
						$config['max_size'] = 0;
						$config['max_width'] = 0;
						$config['max_height'] = 0;
						$this->load->library('upload', $config);
						if ($this->upload->do_upload('documents')) {
							$uploaded_data = $this->upload->data('file_name');
							$data['document'] = $uploaded_data;
						}
					}
					if ($this->leaves_model->edit($this->input->post('update_id'), $data)) {
						if (($this->ion_auth->is_admin() || permissions('leaves_status')) && $this->input->post('remarks')) {

							$roler = $this->session->userdata('user_id');
							$group = $this->ion_auth->get_users_groups($roler)->result();
							$group_id = $group[0]->id;
							$this->db->where('group_id', $group_id);
							$getCurrentGroupStep = $this->db->get('leave_hierarchy');
							$heiCurrentGroupStepResult = $getCurrentGroupStep->row();
							$Step = $heiCurrentGroupStepResult->step_no;
							$log = [
								'leave_id' => $this->input->post('update_id'),
								'group_id' => $group_id,
								'remarks' => $this->input->post('remarks'),
								'status' => $this->input->post('status'),
								'level' => ($this->input->post('status') == 1) ? $Step + 1 : $Step,
							];
							$this->data['log'] = $log;
							$this->leaves_model->createLog($log);
							$CreateNotifications = $this->CreateNotification($Step + 1, $data['user_id']);

							$this->data['CreateNotifications'] = $CreateNotifications;
							$leave_emp = $data['user_id'];
							$leave_user_id = get_user_id_from_employee_id($leave_emp);

							$users_id_query = $this->db->query("SELECT * FROM users WHERE id = $leave_user_id");
							$employee_id_result = $users_id_query->row_array();

							foreach ($CreateNotifications as $CreateNotification) {
								if (($this->session->userdata('saas_id') == $CreateNotification->saas_id && $CreateNotification->user_id != $this->session->userdata('user_id')) && $CreateNotification->active == 1) {
									$to_user = $this->ion_auth->user($CreateNotification->user_id)->row();
									$template_data = array();
									$template_data['EMPLOYEE_NAME'] = $employee_id_result['first_name'] . ' ' . $employee_id_result['last_name'];
									$template_data['NAME'] = $to_user->first_name . ' ' . $to_user->last_name;
									$type = $this->input->post('type');
									$template_data['LEAVE_TYPE'] = '';
									$querys = $this->db->query("SELECT * FROM leaves_type");
									$leaves = $querys->result_array();
									if (!empty($leaves)) {
										foreach ($leaves as $leave) {
											if ($type == $leave['id']) {
												$template_data['LEAVE_TYPE'] = $leave['name'];
											}
										}
									}
									$template_data['STARTING_DATE'] = $data['starting_date'] . ' ' . $data['starting_time'];
									$template_data['REASON'] = $this->input->post('leave_reason');
									$template_data['DUE_DATE'] = $data['ending_date'] . ' ' . $data['ending_time'];
									$template_data['LEAVE_REQUEST_URL'] = base_url('leaves');
									$email_template = render_email_template('leave_request', $template_data);
									send_mail($to_user->email, $email_template[0]['subject'], $email_template[0]['message']);
									$notification_data = array(
										'notification' => 'Leave request received',
										'type' => 'leave_request',
										'type_id' => $this->input->post('update_id'),
										'from_id' => $this->input->post('user_id') ? $this->input->post('user_id') : $this->session->userdata('user_id'),
										'to_id' => $CreateNotification->user_id,
									);
									$notification_id = $this->notifications_model->create($notification_data);
								}
							}
						}

						$this->data['template_data'] = $notification_data;
						$this->data['error'] = false;
						$this->session->set_flashdata('message', $this->lang->line('updated_successfully') ? $this->lang->line('updated_successfully') : "Updated Successfully.");
						$this->session->set_flashdata('message_type', 'success');
						$this->data['message'] = $this->lang->line('updated_successfully') ? $this->lang->line('updated_successfully') : "Updated successfully.";
						echo json_encode($this->data);
					} else {
						$this->data['error'] = true;
						$this->data['message'] = $this->lang->line('something_wrong_try_again') ? $this->lang->line('something_wrong_try_again') : "Something wrong! Try again.";
						echo json_encode($this->data);
					}
				} else {
					$this->data['error'] = true;
					$this->data['message'] = $this->lang->line('check_times_manualy') ? $this->lang->line('check_times_manualy') : "Check times manualy Or Short leave time exceed from 3 hours";
					echo json_encode($this->data);
				}
			} else {
				$this->data['error'] = true;
				$this->data['message'] = validation_errors();
				echo json_encode($this->data);
			}
		} else {

			$this->data['error'] = true;
			$this->data['message'] = $this->lang->line('access_denied') ? $this->lang->line('access_denied') : "Access Denied";
			echo json_encode($this->data);
		}
	}

	public function get_leaves_by_id()
	{
		if ($this->ion_auth->logged_in() && ($this->ion_auth->in_group(1) || permissions('leaves_view'))) {
			$this->form_validation->set_rules('id', 'id', 'trim|required|strip_tags|xss_clean|is_numeric');

			if ($this->form_validation->run() == TRUE) {
				$data = $this->leaves_model->get_leaves_by_id($this->input->post('id'));
				$this->data['error'] = false;
				$this->data['data'] = $data ? $data : '';
				$this->data['message'] = "Success";
				echo json_encode($this->data);
			} else {
				$this->data['error'] = true;
				$this->data['message'] = validation_errors();
				echo json_encode($this->data);
			}
		} else {
			$this->data['error'] = true;
			$this->data['message'] = $this->lang->line('access_denied') ? $this->lang->line('access_denied') : "Access Denied";
			echo json_encode($this->data);
		}
	}

	public function get_leaves()
	{

		if ($this->ion_auth->logged_in() && ($this->ion_auth->in_group(1) || permissions('leaves_view'))) {
			echo json_encode($this->leaves_model->get_leaves());
		} else {
			$this->data['error'] = true;
			$this->data['message'] = $this->lang->line('access_denied') ? $this->lang->line('access_denied') : "Access Denied";
			echo json_encode($this->data);
		}
	}

	public function create()
	{
		if ($this->ion_auth->logged_in() && ($this->ion_auth->in_group(1) || permissions('leaves_view'))) {
			$this->form_validation->set_rules('starting_date', 'Starting Date', 'trim|strip_tags|xss_clean');
			$this->form_validation->set_rules('ending_date', 'Ending Date', 'trim|strip_tags|xss_clean');
			$this->form_validation->set_rules('leave_reason', 'Leave Reason', 'trim|required|strip_tags|xss_clean');
			$this->form_validation->set_rules('type_add', 'Leave Type', 'trim|required|strip_tags|xss_clean');
			if ($this->form_validation->run() == TRUE) {
				if ($this->input->post('remaining_leaves') == 0) {
					$paidUnpaid = 1;
				} else {
					$paidUnpaid = 0;
				}
				$data = array(
					'saas_id' => $this->session->userdata('saas_id'),
					'leave_reason' => $this->input->post('leave_reason'),
					'type' => $this->input->post('type_add'),
					'paid' => $paidUnpaid
				);
				$employeeIdQuery = $this->db->select('employee_id')->get_where('users', array('id' => $this->input->post('user_id_add') ? $this->input->post('user_id_add') : $this->session->userdata('user_id')));
				if ($employeeIdQuery->num_rows() > 0) {
					$employeeIdRow = $employeeIdQuery->row();
					$employeeId = $employeeIdRow->employee_id;
					$data['user_id'] = $employeeId;
				}

				$shiftIdQuery = $this->db->select('shift_id')->get_where('users', array('id' => $this->input->post('user_id_add') ? $this->input->post('user_id_add') : $this->session->userdata('user_id')));
				$shiftIdRow = $shiftIdQuery->row();
				$shiftId = $shiftIdRow->shift_id;
				if ($shiftId !== '0') {
					$shiftQuery = $this->db->get_where('shift', array('id' => $shiftId));
					$shiftRow = $shiftQuery->row();
					$checkInDept = $shiftRow->starting_time;
					$breakEndDept = $shiftRow->break_end;
					$breakStartDept = $shiftRow->break_start;
					$checkOutDept = $shiftRow->ending_time;
				} else {
					$defaultShiftQuery = $this->db->get_where('shift', array('id' => 1));
					$defaultShiftRow = $defaultShiftQuery->row();
					$checkInDept = $defaultShiftRow->starting_time;
					$breakEndDept = $defaultShiftRow->break_end;
					$breakStartDept = $defaultShiftRow->break_start;
					$checkOutDept = $defaultShiftRow->ending_time;
				}
				$data['document'] = '';
				if (!empty($_FILES['documents']['name'])) {
					$upload_path = 'assets/uploads/f' . $this->session->userdata('saas_id') . '/leaves/';

					if (!is_dir($upload_path)) {
						mkdir($upload_path, 0775, true);
					}
					$config['upload_path'] = $upload_path;
					$config['allowed_types'] = '*';
					$config['overwrite'] = false;
					$config['max_size'] = 0;
					$config['max_width'] = 0;
					$config['max_height'] = 0;
					$this->load->library('upload', $config);

					if ($this->upload->do_upload('documents')) {
						$uploaded_data = $this->upload->data('file_name');
						$data['document'] = $uploaded_data;
					}
				}
				$timeValidated = true;
				if ($this->input->post('half_day')) {
					$half_day_period = $this->input->post('half_day_period');
					$startingTime = $half_day_period === "0" ? $checkInDept : $breakEndDept;
					$endingTime = $half_day_period === "0" ? $breakStartDept : $checkOutDept;
					$half_day_period = $half_day_period === "0" ? "First Time" : "Second Time";
					$startingDate = date("Y-m-d", strtotime($this->input->post('date_half')));
					$data['starting_date'] = date("Y-m-d", strtotime($this->input->post('date_half')));
					$data['ending_date'] = date("Y-m-d", strtotime($this->input->post('date_half')));
					$data['starting_time'] = date("H:i:s", strtotime($startingTime));
					$data['ending_time'] = date("H:i:s", strtotime($endingTime));
					$data['leave_duration'] = $half_day_period . " Half Day";

					if (strtotime($checkInDept) > strtotime($checkOutDept)) {
						$tempStartingDate = date("Y-m-d", strtotime($this->input->post('date_half')));
						$tempEndingDate = date("Y-m-d", strtotime("+1 day", strtotime($tempStartingDate)));

						if ($half_day_period === "Second Time" && strtotime($startingTime) >= strtotime('00:00:00', strtotime($tempStartingDate))) {
							$startingDate = $tempEndingDate;
						}

						if (strtotime($endingTime) >= strtotime('00:00:00', strtotime($tempStartingDate))) {
							$endingDate = $tempEndingDate;
						}
						$data['starting_date'] = $startingDate;
						$data['ending_date'] = $endingDate;
					}
					$timeValidated = true;
				} elseif ($this->input->post('short_leave')) {
					if (date("H:i:s", strtotime($this->input->post('starting_time'))) < date("H:i:s", strtotime($this->input->post('ending_time')))) {
						$data['starting_date'] = date("Y-m-d", strtotime($this->input->post('date')));
						$data['ending_date'] = date("Y-m-d", strtotime($this->input->post('date')));
						$data['starting_time'] = date("H:i:s", strtotime($this->input->post('starting_time')));
						$data['ending_time'] = date("H:i:s", strtotime($this->input->post('ending_time')));
						$startingTime = strtotime($this->input->post('starting_time'));
						$endingTime = strtotime($this->input->post('ending_time'));
						$durationSeconds = $endingTime - $startingTime;
						$durationHours = floor($durationSeconds / 3600);
						$durationMinutes = floor(($durationSeconds % 3600) / 60);
						$data['leave_duration'] = $durationHours . " hrs " . $durationMinutes . " mins " . " Short Leave";

						if (strtotime($checkInDept) > strtotime($checkOutDept)) {
							$startingDate = date("Y-m-d", strtotime($this->input->post('date')));
							$endingDate = date("Y-m-d", strtotime($this->input->post('date')));
							$tempEndingDate = date("Y-m-d", strtotime("+1 day", strtotime($startingDate)));

							if ($startingTime >= strtotime('00:00:00', strtotime($tempEndingDate))) {
								$startingDate = $tempEndingDate;
							}

							if ($endingTime >= strtotime('00:00:00', strtotime($startingDate))) {
								$endingDate = $tempEndingDate;
							}

							if ($startingTime < $endingTime) {
								$startingDate = $tempEndingDate;
								$endingDate = $tempEndingDate;
							}


							if ($endingTime < $startingTime) {
								$startOfDay = strtotime('00:00:00', strtotime($data['starting_date']));
								$durationFirstDay = strtotime('23:59:59', strtotime($data['starting_date'])) - $startingTime;

								$endOfDay = strtotime('23:59:59', strtotime($data['ending_date']));
								$durationSecondDay = $endingTime - $startOfDay;

								$durationSeconds = $durationFirstDay + $durationSecondDay;
							} else {
								$durationSeconds = $endingTime - $startingTime;
							}
							$durationHours = floor($durationSeconds / 3600);
							$durationMinutes = floor(($durationSeconds % 3600) / 60);
							$data['leave_duration'] = $durationHours . " hrs " . $durationMinutes . " mins " . " Short Leave";
							$data['starting_date'] = $startingDate;
							$data['ending_date'] = $endingDate;
						}
						if (($durationHours < 3) || ($durationHours == 3 && $durationMinutes == 0)) {
							$timeValidated = true;
						} else {
							$timeValidated = false;
						}
					} else {
						$timeValidated = false;
					}
				} else {
					$starting_date = $this->input->post('starting_date');
					$ending_date = $this->input->post('ending_date');
					$data['starting_date'] = date("Y-m-d", strtotime($starting_date));
					$data['ending_date'] = date("Y-m-d", strtotime($ending_date));
					$data['starting_time'] = format_date($checkInDept, "H:i:s");
					$data['ending_time'] = format_date($checkOutDept, "H:i:s");

					$data['leave_duration'] = 1 + round(abs(strtotime($this->input->post('ending_date')) - strtotime($this->input->post('starting_date'))) / 86400) . " Full Day/s";
					if (strtotime($checkInDept) > strtotime($checkOutDept)) {
						$data['ending_date'] = date("Y-m-d", strtotime("+1 day", strtotime($starting_date)));
					}


					$user_id = $this->input->post('user_id_add') ? $this->input->post('user_id_add') : $this->session->userdata('user_id');

					$missing_finger_days = 0;
					$finger_count = 0;
					$holiday_count = 0;
					$current_date = new DateTime($starting_date);
					$end_date = new DateTime($ending_date);
					$employee_id_query = $this->db->query("SELECT employee_id FROM users WHERE id = $user_id");
					$employee_id_result = $employee_id_query->row_array();
					$employee_id = $employee_id_result['employee_id'];


					while ($current_date < $end_date) {
						$formatted_date = $current_date->format('Y-m-d');
						$execution = false;

						$holidayQuery = $this->db->query("SELECT * FROM holiday");
						$holidays = $holidayQuery->result_array();
						foreach ($holidays as $value4) {
							$startDate = $value4["starting_date"];
							$endDate = $value4["ending_date"];
							$apply = $value4["apply"];
							$startDateTimestamp  = strtotime($startDate);
							$endDateTimestamp  = strtotime($endDate);
							$dateToCheckTimestamp  = strtotime($formatted_date);
							if ($apply == '1' && $dateToCheckTimestamp >= $startDateTimestamp && $dateToCheckTimestamp <= $endDateTimestamp) {
								$departments = json_decode($value4["department"]);
								foreach ($departments as $department) {
									$user_ids_query = $this->db->query("SELECT * FROM users WHERE department = $department AND employee_id= $employee_id");
									$user_ids_result = $user_ids_query->result_array();
									if (count($user_ids_result) > 0) {
										if (!$execution) {
											$missing_finger_days++;
											$execution = true;
										}
									}
								}
							} elseif ($apply == '2' && $dateToCheckTimestamp >= $startDateTimestamp && $dateToCheckTimestamp <= $endDateTimestamp) {
								$holidayUsers = json_decode($value4["users"]);
								foreach ($holidayUsers as $holidayUser) {
									$user_ids_query = $this->db->query("SELECT * FROM users WHERE id = $holidayUser AND employee_id= $employee_id");
									$user_ids_result = $user_ids_query->result_array();
									if (count($user_ids_result) > 0) {
										if (!$execution) {
											$missing_finger_days++;
											$execution = true;
										}
									}
								}
							}
						}
						$current_date->modify('+1 day');
					}
					$data['leave_duration'] = ($data['leave_duration'] - $missing_finger_days) . " Full Day/s";
				}
				if ($timeValidated) {
					$data['document'] = '';
					if (!empty($_FILES['documents']['name'])) {
						$upload_path = 'assets/uploads/f' . $this->session->userdata('saas_id') . '/leaves/';

						if (!is_dir($upload_path)) {
							mkdir($upload_path, 0775, true);
						}
						$config['upload_path'] = $upload_path;
						$config['allowed_types'] = '*';
						$config['overwrite'] = false;
						$config['max_size'] = 0;
						$config['max_width'] = 0;
						$config['max_height'] = 0;
						$this->load->library('upload', $config);

						if ($this->upload->do_upload('documents')) {
							$uploaded_data = $this->upload->data('file_name');
							$data['document'] = $uploaded_data;
						}
					}
					$leave_id = $this->leaves_model->create($data);
					if ($leave_id) {
						$group = get_notifications_group_id();
						$system_admins = $this->ion_auth->users($group)->result();

						$roler = $this->session->userdata('user_id');
						$group = $this->ion_auth->get_users_groups($roler)->result();
						$group_id = $group[0]->id;
						$this->db->where('group_id', $group_id);
						$getCurrentGroupStep = $this->db->get('leave_hierarchy');
						$heiCurrentGroupStepResult = $getCurrentGroupStep->row();
						$heiCurrentGroupStep_number = $heiCurrentGroupStepResult->step_no;
						$step = $this->leaves_model->leaveStep($heiCurrentGroupStep_number, $data["user_id"]);
						$log[] = [
							'leave_id' => $leave_id,
							'group_id' => $group_id,
							'remarks' => $this->input->post('leave_reason'),
							'status' => 0,
							'level' => $step
						];
						foreach ($log as $value) {
							$this->leaves_model->createLog($value);
						}
						$CreateNotifications = $this->CreateNotification($step, $data['user_id']);
						$user_id = $this->input->post('user_id_add') ? $this->input->post('user_id_add') : $this->session->userdata('user_id');
						$employee_id_query = $this->db->query("SELECT * FROM users WHERE id = $user_id");
						$employee_id_result = $employee_id_query->row_array();
						foreach ($CreateNotifications as $system_user) {
							$template_data = array();
							$template_data['EMPLOYEE_NAME'] = $employee_id_result['first_name'] . ' ' . $employee_id_result['last_name'];
							$template_data['NAME'] = $system_user->first_name . ' ' . $system_user->last_name;
							$type = $this->input->post('type_add');
							$template_data['LEAVE_TYPE'] = '';
							$querys = $this->db->query("SELECT * FROM leaves_type");
							$leaves = $querys->result_array();
							if (!empty($leaves)) {
								foreach ($leaves as $leave) {
									if ($type == $leave['id']) {
										$template_data['LEAVE_TYPE'] = $leave['name'];
									}
								}
							}
							$template_data['STARTING_DATE'] = $data['starting_date'] . ' ' . $data['starting_time'];
							$template_data['REASON'] = $this->input->post('leave_reason');
							$template_data['DUE_DATE'] = $data['ending_date'] . ' ' . $data['ending_time'];
							$template_data['LEAVE_REQUEST_URL'] = base_url('leaves');
							$email_template = render_email_template('leave_request', $template_data);
							send_mail($system_user->email, $email_template[0]['subject'], $email_template[0]['message']);

							$notification_data = array(
								'notification' => 'Leave request received',
								'type' => 'leave_request',
								'type_id' => $leave_id,
								'from_id' => $this->input->post('user_id') ? $this->input->post('user_id') : $this->session->userdata('user_id'),
								'to_id' => $system_user->user_id,
							);
							$notification_id = $this->notifications_model->create($notification_data);
						}

						$this->session->set_flashdata('message', $this->lang->line('created_successfully') ? $this->lang->line('created_successfully') : "Created successfully.");
						$this->session->set_flashdata('message_type', 'success');
						$this->data['template_data'] = $template_data;
						$this->data['CreateNotification'] = $CreateNotifications;
						$this->data['data'] = $data;
						$this->data['error'] = false;
						$this->data['message'] = $this->lang->line('created_successfully') ? $this->lang->line('created_successfully') : "Created successfully.";
						echo json_encode($this->data);
					}
				} else {
					$this->data['error'] = true;
					$this->data['message'] = $this->lang->line('check_times_manualy') ? $this->lang->line('check_times_manualy') : "Check times manualy Or Short leave time exceed from 3 hours";
					echo json_encode($this->data);
				}
			} else {
				$this->data['error'] = true;
				$this->data['message'] = validation_errors();
				echo json_encode($this->data);
			}
		} else {
			$this->data['error'] = true;
			$this->data['message'] = $this->lang->line('access_denied') ? $this->lang->line('access_denied') : "Access Denied";
			echo json_encode($this->data);
		}
	}



	public function CreateNotification($step, $employee_id)
	{
		$user_id = get_user_id_from_employee_id($employee_id);
		$saas_id = $this->session->userdata('saas_id');
		$this->db->where('saas_id', $saas_id);
		$this->db->where('step_no', $step);
		$query = $this->db->get('leave_hierarchy');
		$rows = $query->result();
		foreach ($rows as &$row) {
			$step_group = $row->group_id;
			$step_groupArray[] = $row->group_id;
			$group = $this->ion_auth->group($step_group)->row();
			$groups_users[] = $this->ion_auth->users($step_group)->result();
		}
		$flattenedArray = [];
		foreach ($groups_users as $users) {
			$flattenedArray = array_merge($flattenedArray, $users);
		}
		return $flattenedArray;
	}
	public function get_leaves_count()
	{
		if ($this->ion_auth->logged_in() && ($this->ion_auth->in_group(1) || permissions('leaves_view'))) {
			$user_id = $this->input->post('user_id');
			$result = [
				'user_id' => $user_id,
			];

			$leaveReport = $this->leaves_model->get_leaves_count($result);

			echo json_encode($leaveReport);
		} else {
			return '';
		}
	}
	public function manage($id)
	{
		if ($this->ion_auth->logged_in()  && is_module_allowed('leaves') && ($this->ion_auth->in_group(1) || permissions('leaves_view'))) {
			$this->data['page_title'] = 'Leaves - ' . company_name();
			$this->data['main_page'] = 'Leaves Application';
			$this->data['current_user'] = $this->ion_auth->user()->row();
			if ($this->ion_auth->is_admin() || permissions('leaves_view_all')) {
				$this->data['system_users'] = $this->ion_auth->members()->result();
			} elseif (permissions('leaves_view_selected')) {
				$selected = selected_users();
				foreach ($selected as $user_id) {
					$users[] = $this->ion_auth->user($user_id)->row();
				}
				$users[] = $this->ion_auth->user($this->session->userdata('user_id'))->row();
				$this->data['system_users'] = $users;
			}
			$saas_id = $this->session->userdata('saas_id');
			$this->db->where('saas_id', $saas_id);
			$query = $this->db->get('leaves_type');
			$this->data['leave'] = $this->leaves_model->get_leaves_by_id($id);
			$this->data['leaves_types'] = $query->result_array();
			$this->db->where('leave_id', $id);
			$logs_query = $this->db->get('leave_logs');
			$leaves_logs = $logs_query->result_array();
			foreach ($leaves_logs as &$leaves_log) {
				$leaves_log["created"] = $this->getTimeAgo($leaves_log["created"]);
				$group_id = $leaves_log["group_id"];
				$group = $this->ion_auth->group($group_id)->row();
				if ($leaves_log["status"] == -1) {
					$leaves_log["status"] = '' . $group->description . ' <strong class="text-info">Create</strong>';
					$leaves_log["class"] = 'info';
				} elseif ($leaves_log["status"] == 1) {
					$leaves_log["status"] = '' . $group->description . ' <strong class="text-success">Approve</strong>';
					$leaves_log["class"] = 'success';
				} else if ($leaves_log["status"] == 0) {
					$leaves_log["status"] = '' . $group->description . ' <strong class="text-primary">Pending</strong>';
					$leaves_log["class"] = 'primary';
				} else {
					$leaves_log["status"] = '' . $group->description . ' <strong class="text-danger">Reject</strong>';
					$leaves_log["class"] = 'danger';
				}
			}
			$this->data['leaves_logs'] = $leaves_logs;
			// echo json_encode($this->data);
			$this->load->view('leaves-edit', $this->data);
		} else {
			redirect('auth', 'refresh');
		}
	}
	public function create_leave()
	{

		if ($this->ion_auth->logged_in()  && is_module_allowed('leaves') && ($this->ion_auth->in_group(1) || permissions('leaves_view'))) {
			$this->data['page_title'] = 'Leaves - ' . company_name();
			$this->data['main_page'] = 'Leaves Application';
			$this->data['current_user'] = $this->ion_auth->user()->row();
			if ($this->ion_auth->is_admin() || permissions('leaves_view_all')) {
				$this->data['system_users'] = $this->ion_auth->members()->result();
			} elseif (permissions('leaves_view_selected')) {
				$selected = selected_users();
				foreach ($selected as $user_id) {
					$users[] = $this->ion_auth->user($user_id)->row();
				}
				$users[] = $this->ion_auth->user($this->session->userdata('user_id'))->row();
				$this->data['system_users'] = $users;
			}
			$saas_id = $this->session->userdata('saas_id');
			$this->db->where('saas_id', $saas_id);
			$query = $this->db->get('leaves_type');
			$this->data['leaves_types'] = $query->result_array();
			$this->load->view('leaves-create', $this->data);
		} else {
			redirect('auth', 'refresh');
		}
	}
	public function getTimeAgo($timestamp)
	{
		$timestampDateTime = new DateTime($timestamp);
		$currentDateTime = new DateTime();

		$interval = $currentDateTime->diff($timestampDateTime);

		if ($interval->y > 0) {
			return $interval->format("%y years ago");
		} elseif ($interval->m > 0) {
			return $interval->format("%m months ago");
		} elseif ($interval->d > 0) {
			return $interval->format("%d days ago");
		} elseif ($interval->h > 0) {
			return $interval->format("%h hours ago");
		} elseif ($interval->i > 0) {
			return $interval->format("%i minutes ago");
		} else {
			return "just now";
		}
	}
}
