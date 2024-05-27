<?php defined('BASEPATH') or exit('No direct script access allowed');
class Events extends CI_Controller
{
    public $data = [];

    public function __construct()
    {
        parent::__construct();
    }
    public function index()
    {
        if ($this->ion_auth->logged_in() && !is_saas_admin() && !$this->ion_auth->in_group(4) && is_module_allowed('team_members') && ($this->ion_auth->is_admin() || permissions('user_view'))) {
            $this->data['page_title'] = 'Events - ' . company_name();
            $this->data['main_page'] = 'Events';
            $this->data['current_user'] = $this->ion_auth->user()->row();
            $this->load->view('events', $this->data);
        } else {
            redirect('auth', 'refresh');
        }
    }
    public function get_events()
    {
        $saas_id = $this->session->userdata('saas_id');
        $this->db->select('*');
        $this->db->from('events');
        $this->db->where('saas_id', $saas_id);
        $query = $this->db->get();
        $events = $query->result_array();
        foreach ($events as $value) {
            $id = $value["id"];
            $title = $value["title"];
            $start = $value["start"];
            $end = $value["end"];
            $array[] = [
                'id' => $id,
                'title' => $title,
                'start' => $start,
                'end' => date('Y-m-d', strtotime($end . ' +1 day')),
                'className' => "bg-primary"
            ];
        }
        echo json_encode($array);
    }
    public function create()
    {
        if ($this->ion_auth->logged_in() && !is_saas_admin() && !$this->ion_auth->in_group(4) && is_module_allowed('team_members') && ($this->ion_auth->is_admin() || permissions('user_view'))) {
            $this->form_validation->set_rules('start', 'Start Date', 'trim|required|strip_tags|xss_clean');
            $this->form_validation->set_rules('end', 'End Date', 'trim|required|strip_tags|xss_clean');
            $this->form_validation->set_rules('title', 'Title', 'trim|required|strip_tags|xss_clean');
            if ($this->form_validation->run() == TRUE) {
                $data = array(
                    'saas_id' => $this->session->userdata('saas_id'),
                    'title' => $this->input->post('title'),
                    'start' => format_date($this->input->post('start'), "Y-m-d"),
                    'end' => format_date($this->input->post('end'), "Y-m-d"),
                );
                $id = $this->events_model->create($data);
                if ($id) {
                    $this->session->set_flashdata('message', $this->lang->line('created_successfully') ? $this->lang->line('created_successfully') : "Created successfully.");

                    push_notifications('event', [
                        'saas_id' => $this->session->userdata('saas_id'),
                    ]);

                    $this->session->set_flashdata('message_type', 'success');
                    $this->data['error'] = false;
                    $this->data['message'] = $this->lang->line('created_successfully') ? $this->lang->line('created_successfully') : "Created successfully.";
                    echo json_encode($this->data);
                } else {
                    $this->data['error'] = true;
                    $this->data['message'] = $this->lang->line('access_denied') ? $this->lang->line('access_denied') : "Access Denied";
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
    public function edit()
    {
        if ($this->ion_auth->logged_in() && !is_saas_admin() && !$this->ion_auth->in_group(4) && is_module_allowed('team_members') && ($this->ion_auth->is_admin() || permissions('user_view'))) {
            $this->form_validation->set_rules('update_id', 'Id', 'trim|required|strip_tags|xss_clean');
            $this->form_validation->set_rules('start', 'Start Date', 'trim|required|strip_tags|xss_clean');
            $this->form_validation->set_rules('end', 'End Date', 'trim|required|strip_tags|xss_clean');
            $this->form_validation->set_rules('title', 'Title', 'trim|required|strip_tags|xss_clean');
            if ($this->form_validation->run() == TRUE) {
                $data = array(
                    'title' => $this->input->post('title'),
                    'start' => format_date($this->input->post('start'), "Y-m-d"),
                    'end' => format_date($this->input->post('end'), "Y-m-d"),
                );
                $update_id = $this->input->post('update_id');
                $update = $this->events_model->edit($update_id, $data);
                if ($update) {
                    $this->session->set_flashdata('message', $this->lang->line('updated_successfully') ? $this->lang->line('updated_successfully') : "Updated successfully.");
                    $this->session->set_flashdata('message_type', 'success');
                    $this->data['error'] = false;
                    $this->data['message'] = $this->lang->line('updated_successfully') ? $this->lang->line('updated_successfully') : "Updated successfully.";
                    echo json_encode($this->data);
                } else {
                    $this->data['error'] = true;
                    $this->data['message'] = $this->lang->line('access_denied') ? $this->lang->line('access_denied') : "Access Denied";
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

    public function delete($id = '')
    {
        if ($this->ion_auth->logged_in() && !is_saas_admin() && !$this->ion_auth->in_group(4) && is_module_allowed('team_members') && ($this->ion_auth->is_admin() || permissions('user_view'))) {
            if (empty($id)) {
                $id = $this->uri->segment(3) ? $this->uri->segment(3) : '';
            }
            if (!empty($id) && is_numeric($id) && $this->events_model->delete($id)) {
                $this->session->set_flashdata('message', $this->lang->line('deleted_successfully') ? $this->lang->line('deleted_successfully') : "Deleted successfully.");
                $this->session->set_flashdata('message_type', 'success');
                $this->data['error'] = false;
                $this->data['id'] = $id;
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
}
