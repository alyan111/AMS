<?php defined('BASEPATH') or exit('No direct script access allowed');
class Attendance extends CI_Controller
{
	public $data = [];

	public function __construct()
	{
		parent::__construct();
		// Load the library
	}


	public function in_out()
	{
		if ($this->ion_auth->logged_in() && !$this->ion_auth->in_group(3) && !$this->ion_auth->in_group(4)) {

			$is_running = $this->attendance_model->my_att_running($this->session->userdata('user_id'));
			if ($is_running) {

				$data['check_out'] = date("Y-m-d H:i:s");
				$this->attendance_model->edit($data, '', $this->session->userdata('user_id'), true);
			} else {
				$data['saas_id'] = $this->session->userdata('saas_id');
				$data['user_id'] = $this->session->userdata('user_id');
				$data['check_in'] = date("Y-m-d H:i:s");
				$data['check_out'] = NULL;
				$this->attendance_model->create($data);
			}

			$this->session->set_flashdata('message', $this->lang->line('updated_successfully') ? $this->lang->line('updated_successfully') : "Updated successfully.");
			$this->session->set_flashdata('message_type', 'success');
		} else {
			show_404();
		}

		redirect('home', 'refresh');
	}

	public function edit()
	{
		if ($this->ion_auth->logged_in() && !$this->ion_auth->in_group(3) && !$this->ion_auth->in_group(4)) {
			$this->form_validation->set_rules('update_id', 'ID', 'trim|required|strip_tags|xss_clean|is_numeric');

			if ($this->form_validation->run() == TRUE) {

				if ($this->input->post('user_id')) {
					$data['user_id'] = $this->input->post('user_id');
				}

				if ($this->input->post('check_in')) {
					$data['check_in'] = format_date($this->input->post('check_in'), "Y-m-d H:i:s");
				}

				if ($this->input->post('check_out')) {
					$data['check_out'] = format_date($this->input->post('check_out'), "Y-m-d H:i:s");
				}

				$data['note'] = $this->input->post('note') ? $this->input->post('note') : '';

				if ($this->attendance_model->edit($data, $this->input->post('update_id'))) {

					$this->session->set_flashdata('message', $this->lang->line('updated_successfully') ? $this->lang->line('updated_successfully') : "Updated successfully.");
					$this->session->set_flashdata('message_type', 'success');
					$this->data['error'] = false;
					$this->data['message'] = $this->lang->line('updated_successfully') ? $this->lang->line('updated_successfully') : "Updated successfully.";
					echo json_encode($this->data);
				} else {
					$this->data['error'] = true;
					$this->data['message'] = $this->lang->line('something_wrong_try_again') ? $this->lang->line('something_wrong_try_again') : "Something wrong! Try again.";
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

	public function get_attendance_by_id()
	{
		if ($this->ion_auth->logged_in() && !is_saas_admin() && !$this->ion_auth->in_group(4)) {
			$this->form_validation->set_rules('id', 'id', 'trim|required|strip_tags|xss_clean|is_numeric');

			if ($this->form_validation->run() == TRUE) {
				$data = $this->attendance_model->get_attendance_by_id($this->input->post('id'));
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
	public function index()
	{
		if ($this->ion_auth->logged_in() && !is_saas_admin() && !$this->ion_auth->in_group(4) && is_module_allowed('attendance') && ($this->ion_auth->is_admin() || permissions('attendance_view_all') || permissions('attendance_view'))) {
			$saas_id = $this->session->userdata('saas_id');
			$this->db->where('saas_id', $saas_id);
			$query4 = $this->db->get('shift');
			$this->data['shifts'] = $query4->result_array();
			$this->db->where('saas_id', $saas_id);
			$query3 = $this->db->get('departments');
			$this->data['departments'] = $query3->result_array();
			$this->data['page_title'] = 'Attendance - ' . company_name();
			$this->data['main_page'] = 'Attendance';
			$this->data['current_user'] = $this->ion_auth->user()->row();
			$query2 = $this->db->query("SELECT * FROM users WHERE active = '1'");
			$results2 = $query2->result_array();
			foreach ($results2 as $current_user) {
				if ($current_user["id"] == $this->session->userdata('user_id')) {
					$employee_id = $current_user["employee_id"];
					$where = " WHERE attendance.user_id = " . $employee_id;
					$where2 = " WHERE leaves.employee_id = " . $employee_id;
					$user_id = $employee_id;
				}
			}
			$this->data['user_id'] = $user_id;
			if ($this->ion_auth->is_admin() || permissions('attendance_view_all')) {
				$this->data['system_users'] = $this->ion_auth->members()->result();
			} elseif (permissions('attendance_view_selected')) {
				$selected = selected_users();
				foreach ($selected as $user_id) {
					$users[] = $this->ion_auth->user($user_id)->row();
				}
				$users[] = $this->ion_auth->user($this->session->userdata('user_id'))->row();
				$this->data['system_users'] = $users;
			}
			// echo json_encode($this->data);
			$this->load->view('attendance', $this->data);
		} else {
			redirect('auth', 'refresh');
		}
	}

	public function get_attendance()
	{
		if ($this->ion_auth->logged_in() && !is_saas_admin() && !$this->ion_auth->in_group(4)) {
			$attendanceReport = $this->attendance_model->get_attendance();
			$responseData = array(
				'report' => $attendanceReport,
			);
			return '';
		} else {
			return '';
		}
	}
	public function get_users_by_department()
	{
		if ($this->ion_auth->logged_in() && !is_saas_admin() && !$this->ion_auth->in_group(4)) {
			$department = $this->input->post('department');
			$users = $this->attendance_model->get_users_by_department($department);

			echo json_encode($users);
		} else {
			return '';
		}
	}
	public function get_users_by_shifts()
	{
		if ($this->ion_auth->logged_in() && !is_saas_admin() && !$this->ion_auth->in_group(4)) {
			$shift_id = $this->input->post('shift_id');
			$users = $this->attendance_model->get_users_by_shifts($shift_id);
			echo json_encode($users);
		} else {
			return '';
		}
	}
	public function get_user_attendance()
	{
		if ($this->ion_auth->logged_in() && !is_saas_admin() && !$this->ion_auth->in_group(4)) {
			$attendanceReport = $this->attendance_model->get_user_attendance();
			$responseData = array(
				'report' => $attendanceReport
			);
			return $responseData;
		} else {
			return '';
		}
	}

	public function get_filter_page()
	{
		if ($this->ion_auth->logged_in() && !is_saas_admin() && !$this->ion_auth->in_group(4)) {
			$user_id = $this->input->post('user_id');
			$from = $this->input->post('from');
			$too = $this->input->post('too');
			if ($this->ion_auth->is_admin()) {
				$result = [
					'user_id' => $user_id,
					'from' => $from,
				];
			} else {
				$result = [
					'from' => $from,
					'too' => $too
				];
			}

			$attendanceReport = $this->attendance_model->get_filter_page($result);

			echo json_encode($attendanceReport);
		} else {
			return '';
		}
	}
	public function get_leaves()
	{
		if ($this->ion_auth->logged_in() && !is_saas_admin() && !$this->ion_auth->in_group(4)) {
			$user_id = $this->input->post('user_id');
			$from = $this->input->post('from');
			$too = $this->input->post('too');
			if ($this->ion_auth->is_admin()) {
				$result = [
					'user_id' => $user_id,
					'from' => $from,
				];
			} else {
				$result = [
					'from' => $from,
					'too' => $too
				];
			}

			$attendanceReport = $this->attendance_model->get_leaves($result);

			echo json_encode($attendanceReport);
		} else {
			return '';
		}
	}

	public function create()
	{
		if ($this->ion_auth->logged_in() && !is_saas_admin() && !$this->ion_auth->in_group(4)) {

			$data['saas_id'] = $this->session->userdata('saas_id');
			$data['user_id'] = $this->input->post('user_id') ? $this->input->post('user_id') : $this->session->userdata('user_id');
			$data['check_in'] = $this->input->post('check_in') ? format_date($this->input->post('check_in'), "Y-m-d H:i:s") : date("Y-m-d H:i:s");
			$data['check_out'] = $this->input->post('check_out') ? format_date($this->input->post('check_out'), "Y-m-d H:i:s") : NULL;
			$data['note'] = $this->input->post('note') ? $this->input->post('note') : '';

			$id = $this->attendance_model->create($data);

			if ($id) {

				$this->session->set_flashdata('message', $this->lang->line('created_successfully') ? $this->lang->line('created_successfully') : "Created successfully.");
				$this->session->set_flashdata('message_type', 'success');
				$this->data['error'] = false;
				$this->data['data'] = $id;
				$this->data['message'] = $this->lang->line('created_successfully') ? $this->lang->line('created_successfully') : "Created successfully.";
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

	public function delete($id = '')
	{
		if ($this->ion_auth->logged_in() && !is_saas_admin() && !$this->ion_auth->in_group(4)) {

			if (empty($id)) {
				$id = $this->uri->segment(3) ? $this->uri->segment(3) : '';
			}

			if (!empty($id) && $this->attendance_model->delete($id)) {

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

	public function get_attendance_report()
	{
		if ($this->ion_auth->logged_in() && !is_saas_admin() && !$this->ion_auth->in_group(4)) {
			$user_id = $this->input->post('user_id');
			$filter = $this->input->post('filter');
			$from = $this->input->post('from');
			$too = $this->input->post('too');
			$limit = $this->input->post('limit');
			$offset = $this->input->post('offset');
			$search = $this->input->post('search');
			$departments = $this->input->post('departments');
			$active_users = $this->input->post('active_users');
			$result = [
				'user_id' => $user_id,
				'filter' => $filter,
				'from' => $from,
				'too' => $too,
				'search' => $search,
				'limit' => $limit,
				'offset' => $offset,
				'departments' => $departments,
				'active_users' => $active_users,
			];
			$attendanceReport = $this->attendance_model->get_attendance_report($result);

			echo json_encode($attendanceReport);
		} else {
			return '';
		}
	}
	public function connect()
	{
		return $this->attendance_model->connect();
	}

	// user attendance page

	public function user_attendance()
	{

		if ($this->ion_auth->logged_in() && !is_saas_admin() && !$this->ion_auth->in_group(4)) {

			$employee_id = $this->uri->segment($this->uri->total_segments());
			if ($this->ion_auth->is_admin() || permissions('attendance_view_all') || permissions('attendance_view_selected')) {
				$user_data = $this->ion_auth->user($employee_id)->row();
				$user_query = $this->db->get_where('users', array('employee_id' => $employee_id));
				$user_data = $user_query->row();
				if ($user_data) {
					$this->data['name'] = $user_data->first_name . ' ' . $user_data->last_name;
				}
			} else {
				$user_data = $this->ion_auth->user()->row();
				$this->data['name'] = $user_data->first_name . ' ' . $user_data->last_name;
			}
			$this->data['page_title'] = 'Attendance - ' . company_name();
			$this->data['main_page'] = 'Attendance';
			$this->data['current_user'] = $this->ion_auth->user()->row();
			$this->data['system_users'] = $this->ion_auth->users(array(1, 2))->result();
			$this->load->view('user-attendance', $this->data);
		} else {
			redirect('auth', 'refresh');
		}
	}
	public function get_single_user_attendance()
	{
		if ($this->ion_auth->logged_in() && !is_saas_admin() && !$this->ion_auth->in_group(4)) {
			if ($this->ion_auth->logged_in() && !is_saas_admin() && !$this->ion_auth->in_group(4)) {
				$user_id = $this->input->post('user_id');
				$from = $this->input->post('from');
				$too = $this->input->post('too');
				$result = [
					'user_id' => $user_id,
					'from' => $from,
					'too' => $too,
				];
				$attendanceReport = $this->attendance_model->get_single_user_attendance($result);

				echo json_encode($attendanceReport);
			} else {
				return '';
			}
		}
	}
	public function get_active_inactive_users()
	{
		$user = $this->input->get('value');
		$where = "";

		if ($user == 1) {
			$query = $this->db->query("SELECT * FROM users WHERE active ='1' AND finger_config = '1'" . $where);
		} elseif ($user == 2) {
			$query = $this->db->query("SELECT * FROM users WHERE active ='0'  AND finger_config = '1'" . $where);
		}
		$results = $query->result_array();
		echo json_encode($results);
	}

	public function get_user_checkin_time()
	{
		$user_id = $this->input->post('user_id');
		$currentDate = date('Y-m-d');
		$dateRanges = [];

		for ($i = 0; $i < 4; $i++) {
			$dateRanges[] = date("Y-m-d", strtotime("-{$i} day"));
		}

		$queryResults = [];

		foreach ($dateRanges as $dateRange) {
			$query = $this->db->query(
				"SELECT attendance.*, CONCAT(users.first_name, ' ', users.last_name) AS user
					FROM attendance
					LEFT JOIN users ON users.employee_id = attendance.user_id
					WHERE users.id = ? AND DATE(attendance.finger) BETWEEN ? AND ? AND finger_config = '1'",
				[$user_id, $dateRange, $dateRange]
			);

			$queryResults[] = $query->result_array();
		}

		$remind = 1;
		$user = '';

		foreach ($queryResults as $result) {
			if (count($result) == 1) {
				$user = $result[0]["user"];
				$remind = 0;
				break; // Stop checking if attendance is found
			}
		}

		$array3 = [
			'user' => $queryResults[0][0]["user"],
			'time' => date("h:i A", strtotime($queryResults[0][0]["finger"])),
			'remind' => $remind
		];
		echo json_encode($array3);
	}
	public function get_home_attendance_for_admin($date)
	{

		$get = [
			"from" => $date,
			"too" => $date
		];
		// $get_data = $this->attendance_model->get_attendance_for_admin($get);
		// $format = $this->attendance_model->formated_data($get_data,$get);
		return $get;
	}






































	public function get_filters_for_user()
	{
		if (!empty($this->input->get('employee_id'))) {
			$employee_id = $this->input->get('employee_id');
			$user = $this->ion_auth->user($employee_id)->row();
			$shift_id = $user->shift_id;
			$department_id = $user->department;
			$this->db->select('*');
			$this->db->from('shift');
			$this->db->where('id', $shift_id);
			$query = $this->db->get();
			$shift_info = $query->row();

			$this->db->select('*');
			$this->db->from('departments');
			$this->db->where('id', $department_id);
			$query2 = $this->db->get();
			$department_info = $query2->row();
			$array = [
				'shift' => [$shift_info],
				'department' => [$department_info]
			];
		} else {
			$user = $this->ion_auth->user()->row();
			$saas_id = $user->saas_id;
			$this->db->select('*');
			$this->db->from('shift');
			$this->db->where('saas_id', $saas_id);
			$query = $this->db->get();
			$shifts = $query->result();
			$this->db->select('*');
			$this->db->from('departments');
			$this->db->where('saas_id', $saas_id);
			$query2 = $this->db->get();
			$departments = $query2->result();
			$array = [
				'shift' => $shifts,
				'department' => $departments
			];
		}
		echo json_encode($array);
	}
}
