<?php defined('BASEPATH') or exit('No direct script access allowed');

class Biometric_missing extends CI_Controller
{
	public $data = [];

	public function __construct()
	{
		parent::__construct();
	}

	public function delete($id = '')
	{
		if ($this->ion_auth->logged_in() && ($this->ion_auth->is_admin() || permissions('biometric_request_view'))) {

			if (empty($id)) {
				$id = $this->uri->segment(3) ? $this->uri->segment(3) : '';
			}
			if ($this->biometric_missing_model->delete($id)) {
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
		if ($this->ion_auth->logged_in() && ($this->ion_auth->is_admin() || permissions('biometric_request_view'))) {
			$this->form_validation->set_rules('update_id', 'Biometric ID', 'trim|required|strip_tags|xss_clean|is_numeric');
			$this->form_validation->set_rules('reason', 'Reason', 'trim|required|strip_tags|xss_clean');

			$type = 'check_in';

			if ($this->form_validation->run() == TRUE) {
				$employeeIdQuery = $this->db->select('employee_id')->get_where('users', array('id' => $this->input->post('user_id')));
				if ($employeeIdQuery->num_rows() > 0) {
					$employeeIdRow = $employeeIdQuery->row();
					$employeeId = $employeeIdRow->employee_id;
					$data['user_id'] = $employeeId;
				}
				$data['date'] = format_date($this->input->post('date'), "Y-m-d");
				$data['time'] = format_date($this->input->post('time'), "H:i:s");
				$data['reason'] = $this->input->post('reason');
				$data['type'] = $type;
				$data['status'] = $this->input->post('status');
				if ($this->input->post('status') == 1) {
					$notification_data = array(
						'notification' => 'biometric missing request accepted',
						'type' => 'biometric_request_accepted',
						'type_id' => $this->input->post('update_id'),
						'from_id' => $this->session->userdata('user_id'),
						'to_id' => $this->input->post('user_id') ? $this->input->post('user_id') : $this->session->userdata('user_id'),
					);
					$notification_id = $this->notifications_model->create($notification_data);

					$attendance_data = array(
						'user_id' => $data['user_id'],
						'finger' => $data['date'] . ' ' . $data['time'],
						'note' => "Biometric missing request"
					);

					$id = $this->attendance_model->create($attendance_data);
				} elseif ($this->input->post('status') == 2) {
					$notification_data = array(
						'notification' => 'biometric request rejected',
						'type' => 'biometric_request_rejected',
						'type_id' => $this->input->post('update_id'),
						'from_id' => $this->session->userdata('user_id'),
						'to_id' => $this->input->post('user_id') ? $this->input->post('user_id') : $this->session->userdata('user_id'),
					);
					$notification_id = $this->notifications_model->create($notification_data);
				}

				if ($this->biometric_missing_model->edit($this->input->post('update_id'), $data)) {
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

	public function get_biometric_by_id()
	{
		if ($this->ion_auth->logged_in() && ($this->ion_auth->is_admin() || permissions('biometric_request_view'))) {
			$this->form_validation->set_rules('id', 'id', 'trim|required|strip_tags|xss_clean|is_numeric');

			if ($this->form_validation->run() == TRUE) {
				$data = $this->biometric_missing_model->get_biometric_by_id($this->input->post('id'));
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

	public function get_biometric()
	{
		if ($this->ion_auth->logged_in() && ($this->ion_auth->is_admin() || permissions('biometric_request_view'))) {
			echo json_encode($this->biometric_missing_model->get_biometric());
		} else {
			$this->data['error'] = true;
			$this->data['message'] = $this->lang->line('access_denied') ? $this->lang->line('access_denied') : "Access Denied";
			echo json_encode($this->data);
		}
	}

	public function create()
	{

		if ($this->ion_auth->logged_in() && ($this->ion_auth->is_admin() || permissions('biometric_request_view'))) {
			$this->form_validation->set_rules('date', 'Date', 'trim|required|strip_tags|xss_clean');
			$this->form_validation->set_rules('time', 'Time', 'trim|required|strip_tags|xss_clean');
			$this->form_validation->set_rules('reason', 'Missing Reason', 'trim|required|strip_tags|xss_clean');

			$type = 'check_in';

			if ($this->form_validation->run() == TRUE) {
				$data = array(
					'saas_id' => $this->session->userdata('saas_id'),
					'date' => format_date($this->input->post('date'), "Y-m-d"),
					'time' => format_date($this->input->post('time'), "H:i:s"),
					'reason' => $this->input->post('reason'),
					'type' => $type,
				);

				$employeeIdQuery = $this->db->select('employee_id')->get_where('users', array('id' => $this->input->post('user_id') ? $this->input->post('user_id') : $this->session->userdata('user_id')));

				if ($employeeIdQuery->num_rows() > 0) {
					$employeeIdRow = $employeeIdQuery->row();
					$employeeId = $employeeIdRow->employee_id;
					$data['user_id'] = $employeeId;
				}

				$id = $this->biometric_missing_model->create($data);
				$this->session->set_flashdata('message', $this->lang->line('created_successfully') ? $this->lang->line('created_successfully') : "Created successfully.");
				$this->session->set_flashdata('message_type', 'success');


				$biometric_accepters_group = get_users_that_can_accept_biometric_requset();
				push_notifications('biometric_missing_request', [
					'saas_id' => $this->session->userdata('saas_id'),
					'biometric_accepters_group' => $biometric_accepters_group,
				]);

				$this->data['error'] = false;
				$this->data['message'] = $this->lang->line('created_successfully') ? $this->lang->line('created_successfully') : "Created successfully.";
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
		if ($this->ion_auth->logged_in() && is_module_allowed('biometric_missing') && ($this->ion_auth->is_admin() || permissions('biometric_request_view'))) {
			$this->data['page_title'] = 'Biometric Request - ' . company_name();
			$this->data['main_page'] = 'Biometric Request';
			$this->data['current_user'] = $this->ion_auth->user()->row();
			if ($this->ion_auth->is_admin() || permissions('biometric_request_view_all')) {
				$this->data['system_users'] = $this->ion_auth->members()->result();
			} elseif (permissions('biometric_request_view_selected')) {
				$selected = selected_users();
				foreach ($selected as $user_id) {
					$users[] = $this->ion_auth->user($user_id)->row();
				}
				$users[] = $this->ion_auth->user($this->session->userdata('user_id'))->row();
				$this->data['system_users'] = $users;
			}
			$this->load->view('biometric_missing', $this->data);
		} else {
			redirect('auth', 'refresh');
		}
	}

	public function get_shift_time()
	{
		if ($this->ion_auth->logged_in() && ($this->ion_auth->is_admin() || permissions('biometric_request_view'))) {
			$user_id = $this->input->post('user_id');
			$result = [
				'user_id' => $user_id,
			];

			$shiftReport = $this->biometric_missing_model->get_shift_time($result);

			echo json_encode($shiftReport);
		} else {
			return '';
		}
	}

}
