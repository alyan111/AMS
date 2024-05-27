<?php defined('BASEPATH') or exit('No direct script access allowed');
class Issues extends CI_Controller
{
    public $data = [];
    public $ion_auth;

    public function __construct()
    {
        parent::__construct();
    }
    public function tasks($project_id = '')
    {
        if ($this->ion_auth->logged_in()) {
            $this->data['page_title'] = 'Create issue - ' . company_name();
            $this->data['main_page'] = 'Create issue';
            $this->data['current_user'] = $this->ion_auth->user()->row();

            $this->db->select('*');
            $this->db->from('projects');
            $this->db->where_in('saas_id', $this->session->userdata());
            $query = $this->db->get();
            $this->data['projects'] = $query->result_array();

            $sprint_data = $this->board_model->get_running_sprint($project_id);
            $this->db->select('*');
            $this->db->from('sprints');
            $this->db->where_in('id', $sprint_data->id);
            $this->db->where_in('saas_id', $this->session->userdata());
            $query = $this->db->get();
            $this->data['sprints'] = $query->result_array();

            $this->db->select('*');
            $this->db->from('task_status');
            $query = $this->db->get();
            $this->data['statuses'] = $query->result_array();

            $this->db->select('*');
            $this->db->from('priorities');
            $query = $this->db->get();
            $this->data['priorities'] = $query->result_array();


            $this->data['is_allowd_to_create_new'] = if_allowd_to_create_new("tasks");

            if ($this->ion_auth->is_admin() || permissions('project_view_all')) {
                $this->data['system_users'] = $this->ion_auth->members()->result();
            } elseif (permissions('project_view_selected')) {
                $selected = selected_users();
                foreach ($selected as $user_id) {
                    $users[] = $this->ion_auth->user($user_id)->row();
                }
                $users[] = $this->ion_auth->user($this->session->userdata('user_id'))->row();
                $this->data['system_users'] = $users;
            }
            $this->db->select('*');
            $this->db->from('projects');
            $this->db->where_in('id', $project_id);
            $query = $this->db->get();
            $this->data['selectedproject'] = $query->row();

            $this->db->select('*');
            $this->db->from('project_users pu');
            $this->db->join('users u', 'pu.user_id = u.id', 'left');
            $this->db->where_in('project_id', $project_id);
            $query = $this->db->get();
            $project_userss = $query->result();

            $this->data["project_userss"] = $project_userss;
            $this->data["project_id"] = $project_id;
            $this->load->view('issues-create', $this->data);
        } else {
            redirect('auth', 'refresh');
        }
    }
    public function edit($id = '')
    {
        if ($this->ion_auth->logged_in()) {
            $this->data['page_title'] = 'Edit issue - ' . company_name();
            $this->data['main_page'] = 'Edit issue';
            $this->data['current_user'] = $this->ion_auth->user()->row();

            $this->db->select('*');
            $this->db->from('projects');
            $this->db->where_in('saas_id', $this->session->userdata());
            $query = $this->db->get();
            $this->data['projects'] = $query->result_array();

            $this->db->select('*');
            $this->db->from('task_status');
            $query = $this->db->get();
            $this->data['statuses'] = $query->result_array();

            $this->db->select('*');
            $this->db->from('priorities');
            $query = $this->db->get();
            $this->data['priorities'] = $query->result_array();

            $this->db->select('*');
            $this->db->from('tasks');
            $this->db->where('id', $id);
            $query2 = $this->db->get();
            $this->data['issue'] = $query2->row();

            $this->db->select('*');
            $this->db->from('tasks');
            $this->db->where('parent_task', $id);
            $query2 = $this->db->get();
            $this->data['sub_issues'] = $query2->result();

            $project_id = $query2->row()->project_id;

            $this->data['project_id'] = $project_id;

            $this->db->select('*');
            $this->db->from('projects');
            $this->db->where_in('id', $project_id);
            $query = $this->db->get();
            $this->data['issue_projects'] = $query->row();

            $this->db->select('*');
            $this->db->from('project_users');
            $this->db->where_in('project_id', $project_id);
            $query = $this->db->get();
            $project_users = $query->result_array();

            foreach ($project_users as $us) {
                $pr_users[] = $this->ion_auth->user($us["user_id"])->row();
            }
            $this->data['project_users'] = $pr_users;

            $this->db->select('*');
            $this->db->from('issues_sprint');
            $this->db->where('issue_id', $id);
            $query2 = $this->db->get();
            $this->data['issues_sprint'] = $query2->row();

            $this->db->select('*');
            $this->db->from('task_users');
            $this->db->where('task_id', $id);
            $query2 = $this->db->get();
            $this->data['issues_users'] = $query2->row();

            $sprint_data = $this->board_model->get_running_sprint($project_id);
            $this->db->select('*');
            $this->db->from('sprints');
            $this->db->where_in('id', $sprint_data->id);
            $this->db->where_in('saas_id', $this->session->userdata());
            $query = $this->db->get();
            $this->data['sprints'] = $query->result_array();

            $this->db->select('*');
            $this->db->from('projects');
            $this->db->where_in('id', $project_id);
            $query = $this->db->get();
            $this->data['selectedproject'] = $query->row();

            if ($this->ion_auth->is_admin() || permissions('project_view_all')) {
                $this->data['system_users'] = $this->ion_auth->members()->result();
            } elseif (permissions('project_view_selected')) {
                $selected = selected_users();
                foreach ($selected as $user_id) {
                    $users[] = $this->ion_auth->user($user_id)->row();
                }
                $users[] = $this->ion_auth->user($this->session->userdata('user_id'))->row();
                $this->data['system_users'] = $users;
            }
            $this->load->view('issues-edit', $this->data);
        } else {
            redirect('auth', 'refresh');
        }
    }
    public function create_issue()
    {
        if ($this->ion_auth->logged_in()) {
            $this->form_validation->set_rules('issue_type', 'Type', 'trim|required|strip_tags|xss_clean');
            $this->form_validation->set_rules('project_id', 'Project', 'trim|required|strip_tags|xss_clean');
            $this->form_validation->set_rules('title', 'Title', 'trim|required|strip_tags|xss_clean');
            $this->form_validation->set_rules('priority', 'Priority', 'trim|required|strip_tags|xss_clean');
            $this->form_validation->set_rules('description', 'description', 'trim|required|strip_tags|xss_clean');
            $this->form_validation->set_rules('user', 'Assignee', 'trim|required|strip_tags|xss_clean');
            if ($this->form_validation->run() == TRUE) {
                $due_date = format_date($this->input->post('due_date'), "Y-m-d");
                $starting_date = format_date($this->input->post('starting_date'), "Y-m-d");
                $data = [
                    'title' => $this->input->post('title'),
                    'saas_id' => $this->session->userdata('saas_id'),
                    'project_id' => $this->input->post('project_id'),
                    'issue_type' => $this->input->post('issue_type'),
                    'story_points' => $this->input->post('story_points') ? $this->input->post('story_points') : '',
                    'priority' => $this->input->post('priority'),
                    'created_by' => $this->session->userdata('user_id'),
                    'status' => $this->input->post('status') ? $this->input->post('status') : '1',
                    'description' => $this->input->post('description') ? $this->input->post('description') : '',
                    'due_date' => $due_date,
                    'starting_date' => $starting_date,
                ];
                $id = $this->issues_model->create_issue($data);
                if ($id) {
                    $this->data["id"] = $id;

                    $subTitle = $this->input->post('subTitle');
                    if (isset($subTitle) && !empty($subTitle)) {
                        $subStatus = $this->input->post('subStatus');
                        $subTypes = $this->input->post('subType');
                        foreach ($subTypes as $key => $subTypes) {
                            if ($subTitle[$key]) {
                                $subTask = [
                                    'parent_task' => $this->data["id"],
                                    'title' => $subTitle[$key],
                                    'saas_id' => $this->session->userdata('saas_id'),
                                    'project_id' => $this->input->post('project_id'),
                                    'issue_type' => $subTypes,
                                    'created_by' => $this->session->userdata('user_id'),
                                    'priority' => $this->input->post('priority'),
                                    'status' => $subStatus[$key] ? $subStatus[$key] : '1',
                                    'description' => $this->input->post('description') ? $this->input->post('description') : '',
                                ];
                                $sub_id = $this->issues_model->create_issue($subTask);
                            }
                        }
                    }
                    if ($this->input->post('sprint')) {
                        $sprintIssue = [
                            'issue_id' => $id,
                            'sprint_id' => $this->input->post('sprint')
                        ];
                        $this->issues_model->create_issue_sprint($sprintIssue);
                    }

                    if ($this->input->post('user')) {
                        $sprintIssue = [
                            'task_id' => $id,
                            'user_id' => $this->input->post('user')
                        ];
                        $this->issues_model->create_task_user($sprintIssue);
                    }

                    $this->data['data'] = $this->input->post();
                    $this->session->set_flashdata('message', $this->lang->line('created_successfully') ? $this->lang->line('created_successfully') : "Created successfully.");
                    $this->session->set_flashdata('message_type', 'success');
                    $this->data['error'] = false;

                    push_notifications('task_assignment', [
                        'saas_id' => $this->session->userdata('saas_id'),
                        'user_id' => $this->input->post('user')
                    ]);

                    $this->data['message'] = $this->lang->line('created_successfully') ? $this->lang->line('created_successfully') : "Created successfully.";
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
    public function edit_issue()
    {
        if ($this->ion_auth->logged_in()) {
            $this->form_validation->set_rules('update_id', 'ID', 'trim|required|strip_tags|xss_clean');
            $this->form_validation->set_rules('issue_type', 'Type', 'trim|required|strip_tags|xss_clean');
            $this->form_validation->set_rules('project_id', 'Project', 'trim|required|strip_tags|xss_clean');
            $this->form_validation->set_rules('title', 'Title', 'trim|required|strip_tags|xss_clean');
            $this->form_validation->set_rules('user', 'Assignee', 'trim|required|strip_tags|xss_clean');
            $this->form_validation->set_rules('priority', 'Priority', 'trim|required|strip_tags|xss_clean');
            $this->form_validation->set_rules('description', 'description', 'trim|required|strip_tags|xss_clean');
            if ($this->form_validation->run() == TRUE) {
                $data = [
                    'title' => $this->input->post('title'),
                    'saas_id' => $this->session->userdata('saas_id'),
                    'project_id' => $this->input->post('project_id'),
                    'issue_type' => $this->input->post('issue_type'),
                    'description' => $this->input->post('description') ? $this->input->post('description') : '',
                    'priority' => $this->input->post('priority') ? $this->input->post('priority') : '1',
                    'status' => $this->input->post('status') ? $this->input->post('status') : '1',
                    'story_points' => $this->input->post('story_points') ? $this->input->post('story_points') : '',
                    'due_date' => $this->input->post('due_date') ? format_date($this->input->post('due_date'), "Y-m-d") : date('Y-m-d'),
                    'starting_date' => $this->input->post('starting_date') ? format_date($this->input->post('starting_date'), "Y-m-d") : date('Y-m-d'),
                ];
                if ($this->issues_model->edit_issue($this->input->post('update_id'), $data)) {
                    if ($this->input->post('sprint')) {
                        $sprintIssue = [
                            'sprint_id' => $this->input->post('sprint')
                        ];
                        $this->issues_model->edit_issue_sprint($this->input->post('update_id'), $sprintIssue);
                    }
                    $this->session->set_flashdata('message', $this->lang->line('updated_successfully') ? $this->lang->line('updated_successfully') : "Updated successfully.");
                    $this->session->set_flashdata('message_type', 'success');
                    $this->data['data'] = $data;
                    $this->data['error'] = false;

                    push_notifications('task_completion', [
                        'saas_id' => $this->session->userdata('saas_id'),
                        'user_id' => $this->input->post('user')
                    ]);

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

    public function update_issues_sprint()
    {
        $sprintId = $this->input->post('SprintId');
        $issueId = $this->input->post('issueId');

        $this->db->where('issue_id', $issueId);
        $query = $this->db->get('issues_sprint');

        if ($query->num_rows() > 0) {
            $data = ['sprint_id' => $sprintId];
            $this->db->where('issue_id', $issueId);
            $this->db->update('issues_sprint', $data);
            $message = 'Relationship updated successfully.';
        } else {
            $data = [
                'issue_id' => $issueId,
                'sprint_id' => $sprintId
            ];
            $this->db->insert('issues_sprint', $data);
            $message = 'Relationship created successfully.';
        }

        $response = [
            'success' => true,
            'message' => $message
        ];

        echo json_encode($response);
    }
    public function update_issues_user()
    {
        $issue = $this->input->post('issue');
        $user = $this->input->post('user');

        $this->db->where('issue_id', $issue);
        $query = $this->db->get('issues_users');

        if ($query->num_rows() > 0) {
            $data = ['user_id' => $user];
            $this->db->where('issue_id', $issue);
            $this->db->update('issues_users', $data);
            $message = 'Relationship updated successfully.';
        } else {
            $data = [
                'issue_id' => $issue,
                'user_id' => $user
            ];
            $this->db->insert('issues_users', $data);
            $message = 'Relationship created successfully.';
        }

        $response = [
            'success' => true,
            'message' => $message
        ];
        echo json_encode($response);
    }
    public function update_issues_status()
    {
        $issue = $this->input->post('issue');
        $status = $this->input->post('status');
        $data = ['status' => $status];

        $this->db->where('id', $issue);
        $this->db->update('tasks', $data);

        $response = [
            'success' => true,
        ];
        echo json_encode($response);
    }

    public function delete_issue($id = '')
    {
        if ($this->ion_auth->logged_in()) {
            if (empty($id)) {
                $id = $this->uri->segment(3) ? $this->uri->segment(3) : '';
            }
            if (!empty($id) && is_numeric($id) && $this->issues_model->delete_issue($id)) {
                $this->session->set_flashdata('message', $this->lang->line('deleted_successfully') ? $this->lang->line('deleted_successfully') : "Deleted successfully.");
                $this->session->set_flashdata('message_type', 'success');
                $this->data['error'] = false;
                $this->data['message'] = $this->lang->line('started_successfully') ? $this->lang->line('started_successfully') : "Started successfully.";
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
    public function update_issues_story_points()
    {
        $issue = $this->input->post('issue');
        $story_points = $this->input->post('story_points');
        $data = ['story_points' => $story_points];

        $this->db->where('id', $issue);
        $this->db->update('tasks', $data);

        $response = [
            'success' => true,
            'issue' => $issue,
            'story_points' => $story_points,
        ];
        echo json_encode($response);
    }
    public function get_issue_by_id($id = '')
    {
        if ($this->ion_auth->logged_in()) {
            if (empty($id)) {
                $id = $this->uri->segment(3) ? $this->uri->segment(3) : '';
            }
            $data = $this->issues_model->get_issue_by_id($id);
            echo json_encode($data);
        } else {
            $this->data['error'] = true;
            $this->data['message'] = $this->lang->line('access_denied') ? $this->lang->line('access_denied') : "Access Denied";
            echo json_encode($this->data);
        }
    }
    public function edit_sub_task()
    {
        if ($this->ion_auth->logged_in()) {
            $this->form_validation->set_rules('update_id', 'ID', 'trim|required|strip_tags|xss_clean');
            $this->form_validation->set_rules('issue_type', 'Type', 'trim|required|strip_tags|xss_clean');
            $this->form_validation->set_rules('title', 'Title', 'trim|required|strip_tags|xss_clean');
            if ($this->form_validation->run() == TRUE) {
                $data = [
                    'title' => $this->input->post('title'),
                    'issue_type' => $this->input->post('issue_type'),
                    'status' => $this->input->post('status') ? $this->input->post('status') : '1',
                ];
                if ($this->issues_model->edit_issue($this->input->post('update_id'), $data)) {
                    $this->session->set_flashdata('message', $this->lang->line('updated_successfully') ? $this->lang->line('updated_successfully') : "Updated successfully.");
                    $this->session->set_flashdata('message_type', 'success');
                    $this->data['data'] = $data;
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
    public function get_project_users()
    {
        if ($this->ion_auth->logged_in()) {
            $project_id = $this->input->post('project_id');
            $data = $this->issues_model->get_project_users($project_id);
            $dash = $this->issues_model->get_project_dash($project_id);
            echo json_encode(array('users' => $data, 'dash_type' => $dash));
        } else {
            $this->data['error'] = true;
            $this->data['message'] = $this->lang->line('access_denied') ? $this->lang->line('access_denied') : "Access Denied";
            echo json_encode($this->data);
        }
    }
}
