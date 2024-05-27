<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Home_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_home_attendance_for_admin($date, $present, $absent, $leave)
    {
        $get = [
            "from" => $date,
            "too" => $date,
            "present" => $present,
            "absent" => $absent,
            "leave" => $leave,
        ];

        $get_data = $this->get_attendance_for_admin($get);
        $format = $this->formated_data($get_data, $get);
        return $format;
    }

    public function get_attendance_for_admin($get)
    {
        if (isset($get['user_id']) && !empty($get['user_id'])) {
            $user = $this->ion_auth->user($get['user_id'])->row();
            $employee_id = $user->employee_id;
            $where = " WHERE attendance.user_id = " . $employee_id;
        } else {
            if ($this->ion_auth->is_admin() || permissions('attendance_view_all')) {
                $where = " WHERE attendance.id IS NOT NULL ";
            } else {
                $selected = selected_users();
                if (!empty($selected)) {
                    foreach ($selected as $assignee) {
                        $sel[] = get_employee_id_from_user_id($assignee);
                    }
                    $userIdsString = implode(',', $sel);
                    $where = " WHERE attendance.user_id IN ($userIdsString)";
                }
            }
        }
        if (isset($get['department']) && !empty($get['department'])) {
            $department = $get['department'];
            $where .= " AND users.department = '$department'";
        }
        if (isset($get['shifts']) && !empty($get['shifts'])) {
            $shifts = $get['shifts'];
            $where .= " AND users.shift_id = '$shifts'";
        }
        if (isset($get['from']) && !empty($get['from']) && isset($get['too']) && !empty($get['too'])) {
            $where .= " AND DATE(attendance.finger) BETWEEN '" . format_date($get['from'], "Y-m-d") . "' AND '" . format_date($get['too'], "Y-m-d") . "' ";
        }
        $leftjoin = "LEFT JOIN users ON attendance.user_id = users.employee_id";
        $where .= " AND users.saas_id=" . $this->session->userdata('saas_id') . " ";
        $query = $this->db->query("SELECT attendance.*, CONCAT(users.first_name, ' ', users.last_name) AS user
        FROM attendance " . $leftjoin . $where . " AND users.active=1 AND users.finger_config=1");
        $results = $query->result_array();
        return $results;
    }
    public function formated_data($attendance, $get)
    {
        $from = $get["from"];
        $too = $get["too"];
        $dateArray = [$from];
        $formattedData = [];
        $leaveArray = [];
        $absentArray = [];

        foreach ($attendance as $entry) {
            $userId = $entry['user_id'];
            $user = $entry['user'];
            $finger = $entry['finger'];
            $createdDate = date("Y-m-d", strtotime($finger));
            $createdTime = date("H:i:s", strtotime($finger));

            if (!isset($formattedData[$userId])) {
                $formattedData[$userId] = [
                    'user_id' => $userId,
                    'user' => '<a href="javascript:void(0);" onclick="openChildWindow(' . $userId . ')">' . $userId . '</a>',
                    'name' => '<a href="javascript:void(0);" onclick="openChildWindow(' . $userId . ')">' . $user . '</a>',
                    'dates' => [],
                ];
                $presentArray[$userId] = [
                    'user_id' => $userId,
                    'user' => '<a href="javascript:void(0);" onclick="openChildWindow(' . $userId . ')">' . $userId . '</a>',
                    'name' => '<a href="javascript:void(0);" onclick="openChildWindow(' . $userId . ')">' . $user . '</a>',
                    'dates' => [],
                ];
            }

            if (!isset($formattedData[$userId]['dates'][$createdDate])) {
                $formattedData[$userId]['dates'][$createdDate] = [];
            }
            $formattedData[$userId]['dates'][$createdDate][] = date('H:i', strtotime($createdTime));
            $presentArray[$userId]['dates'][$createdDate][] = date('H:i', strtotime($createdTime));
        }

        if ($this->ion_auth->is_admin() || permissions('attendance_view_all')) {
            $system_users = $this->ion_auth->members()->result();
        } else {
            $selected = selected_users();
            foreach ($selected as $user_id) {
                $users[] = $this->ion_auth->user($user_id)->row();
            }
            $users[] = $this->ion_auth->user($this->session->userdata('user_id'))->row();
            $system_users = $users;
        }
        foreach ($system_users as $user) {
            if ($user->finger_config == '1' && $user->active == '1' && $user->join_date && strtotime($user->join_date) <= strtotime($from)) {

                if (!isset($formattedData[$user->employee_id])) {
                    $formattedData[$user->employee_id] = [
                        'user_id' => $user->employee_id,
                        'user' => '<a href="javascript:void(0);" onclick=openChildWindow('.$user->employee_id.')>' . $user->employee_id . '</a>',
                        'name' => '<a href="javascript:void(0);" onclick=openChildWindow('.$user->employee_id.')>' . $user->first_name . ' ' . $user->last_name . '</a>',
                        'dates' => [],
                    ];
                }

                if (!isset($formattedData[$user->employee_id]['dates'][$from])) {
                    if ($this->checkLeave($user->employee_id, $from)) {
                        $formattedData[$user->employee_id]['dates'][$from][] = '<span class="text-success">L</span>';
                        $leaveArray[$user->employee_id] = [
                            'user_id' => $user->employee_id,
                            'user' => '<a href="javascript:void(0);" onclick="openChildWindow('.$user->employee_id.')">' . $user->employee_id . '</a>',
                            'name' => '<a href="javascript:void(0);" onclick="openChildWindow('.$user->employee_id.')">' . $user->first_name . ' ' . $user->last_name . '</a>',
                            'dates' => [],
                        ];
                        $leaveArray[$user->employee_id]['dates'][$from][] = '<span class="text-success">L</span>';
                    } elseif ($this->attendance_model->holidayCheck($user->employee_id, $from)) {
                        $formattedData[$user->employee_id]['dates'][$from][] = '<span class="text-info">H</span>';
                        $absentArray[$user->employee_id] = [
                            'user_id' => $user->employee_id,
                            'user' => '<a href="javascript:void(0);" onclick="openChildWindow('.$user->employee_id.')">' . $user->employee_id . '</a>',
                            'name' => '<a href="javascript:void(0);" onclick="openChildWindow('.$user->employee_id.')">' . $user->first_name . ' ' . $user->last_name . '</a>',
                            'dates' => [],
                        ];
                        $absentArray[$user->employee_id]['dates'][$from][] = '<span class="text-info">H</span>';
                    } else {
                        $formattedData[$user->employee_id]['dates'][$from][] = '<span class="text-danger">A</span>';
                        $absentArray[$user->employee_id] = [
                            'user_id' => $user->employee_id,
                            'user' => '<a href="javascript:void(0);" onclick="openChildWindow('.$user->employee_id.')">' . $user->employee_id . '</a>',
                            'name' => '<a href="javascript:void(0);" onclick="openChildWindow('.$user->employee_id.')">' . $user->first_name . ' ' . $user->last_name . '</a>',
                            'dates' => [],
                        ];
                        $absentArray[$user->employee_id]['dates'][$from][] = '<span class="text-danger">A</span>';
                    }
                }
            }
        }
        if ($get["absent"] && $get["absent"] == 1) {
            $resultArray = array_values($absentArray);
        } elseif ($get["leave"] && $get["leave"] == 1) {
            $resultArray = array_values($leaveArray);
        } elseif ($get["present"] && $get["present"] == 1) {
            $resultArray = array_values($presentArray);
        } else {
            $resultArray = array_values($formattedData);
        }

        return $resultArray;
    }
    public function filter_count_abs($date)
    {
        $abs = 0;
        $leaves = 0;
        $present = 0;
        if ($this->ion_auth->is_admin() || permissions('attendance_view_all')) {
            $where = " WHERE DATE(attendance.finger) = '" . $date . "' ";
        } elseif (permissions('attendance_view_selected')) {
            $selected = selected_users();
            if (!empty($selected)) {
                foreach ($selected as $assignee) {
                    $sel[] = get_employee_id_from_user_id($assignee);
                }
                $sel[] = $this->session->userdata('user_id');
                $userIdsString = implode(',', $sel);
                $where = " WHERE DATE(attendance.finger) = '" . $date . "' AND attendance.user_id IN ($userIdsString)";
            }
        }

        $leftjoin = " LEFT JOIN users ON attendance.user_id = users.employee_id";
        $query = $this->db->query("SELECT attendance.*, CONCAT(users.first_name, ' ', users.last_name) AS user 
            FROM attendance " . $leftjoin . $where);

        $results = $query->result_array();
        if ($this->ion_auth->is_admin() || permissions('attendance_view_all')) {
            $system_users = $this->ion_auth->members()->result();
        } elseif (permissions('attendance_view_selected')) {
            $selected = selected_users();
            foreach ($selected as $user_id) {
                $users[] = $this->ion_auth->user($user_id)->row();
            }
            $system_users = $users;
        }

        foreach ($system_users as $user) {
            $userPresent = false;
            if ($user->finger_config == '1' && $user->active == '1' && $user->join_date && strtotime($user->join_date) <= strtotime($date)) {
                foreach ($results as $attendance) {
                    if ($user->employee_id == $attendance["user_id"]) {
                        $present++;
                        $userPresent = true;
                        break;
                    }
                }
                if (!$userPresent) {
                    if ($this->checkLeave($user->employee_id, $date)) {
                        $leaves++;
                    } else {
                        $abs++;
                    }
                }
            }
        }

        return [
            "abs" => $abs,
            "leave" => $leaves,
            "present" => $present,
        ];
    }

    /*
    *
    *
    * user site Attendance
    *
    *
    */
    public function get_home_attendance_for_user($date, $present, $absent, $leave)
    {
        $get = [
            "from" => $date,
            "too" => $date,
            "present" => $present,
            "absent" => $absent,
            "leave" => $leave,
        ];

        $get_data = $this->get_attendance_for_user($get);
        $format = $this->formated_data_user($get_data, $get);
        return $format;
    }
    public function get_attendance_for_user($get)
    {
        $user_id = $this->session->userdata('user_id');
        if ($user_id) {
            $user = $this->ion_auth->user($user_id)->row();
            $employee_id = $user->employee_id;
            $where = " WHERE attendance.user_id = " . $employee_id;
        } else {
            $where = " WHERE attendance.id IS NOT NULL ";
        }
        if (isset($get['department']) && !empty($get['department'])) {
            $department = $get['department'];
            $where .= " AND users.department = '$department'";
        }
        if (isset($get['shifts']) && !empty($get['shifts'])) {
            $shifts = $get['shifts'];
            $where .= " AND users.shift_id = '$shifts'";
        }
        if (isset($get['from']) && !empty($get['from']) && isset($get['too']) && !empty($get['too'])) {
            $where .= " AND DATE(attendance.finger) BETWEEN '" . format_date($get['from'], "Y-m-d") . "' AND '" . format_date($get['too'], "Y-m-d") . "' ";
        }
        $leftjoin = "LEFT JOIN users ON attendance.user_id = users.employee_id";
        $where .= " AND users.saas_id=" . $this->session->userdata('saas_id') . " ";
        $query = $this->db->query("SELECT attendance.*, CONCAT(users.first_name, ' ', users.last_name) AS user
        FROM attendance " . $leftjoin . $where . " AND users.active=1 AND users.finger_config=1");
        $results = $query->result_array();
        return $results;
    }
    public function formated_data_user($attendance, $get)
    {
        $from = $get["from"];
        $too = $get["too"];
        $dateArray = [$from];
        $formattedData = [];
        $leaveArray = [];
        $absentArray = [];

        foreach ($attendance as $entry) {
            $userId = $entry['user_id'];
            $user = $entry['user'];
            $finger = $entry['finger'];
            $createdDate = date("Y-m-d", strtotime($finger));
            $createdTime = date("H:i:s", strtotime($finger));

            if (!isset($formattedData[$userId])) {
                $formattedData[$userId] = [
                    'user_id' => $userId,
                    'user' => '<a href="' . base_url('attendance/user_attendance/' . $userId) . '">' . $userId . '</a>',
                    'name' => '<a href="' . base_url('attendance/user_attendance/' . $userId) . '">' . $user . '</a>',
                    'dates' => [],
                ];
                $presentArray[$userId] = [
                    'user_id' => $userId,
                    'user' => '<a href="' . base_url('attendance/user_attendance/' . $userId) . '">' . $userId . '</a>',
                    'name' => '<a href="' . base_url('attendance/user_attendance/' . $userId) . '">' . $user . '</a>',
                    'dates' => [],
                ];
            }

            if (!isset($formattedData[$userId]['dates'][$createdDate])) {
                $formattedData[$userId]['dates'][$createdDate] = [];
            }
            $formattedData[$userId]['dates'][$createdDate][] = date('h:i A', strtotime($createdTime));
            $presentArray[$userId]['dates'][$createdDate][] = date('h:i A', strtotime($createdTime));
        }

        $system_users = [$this->ion_auth->user()->row()];
        foreach ($system_users as $user) {
            if ($user->finger_config == '1' && $user->active == '1') {
                if (!isset($formattedData[$user->employee_id])) {
                    $formattedData[$user->employee_id] = [
                        'user_id' => $user->employee_id,
                        'user' => '<a href="' . base_url('attendance/user_attendance/' . $user->employee_id) . '">' . $user->employee_id . '</a>',
                        'name' => '<a href="' . base_url('attendance/user_attendance/' . $user->employee_id) . '">' . $user->first_name . ' ' . $user->last_name . '</a>',
                        'dates' => [],
                    ];
                }

                if (!isset($formattedData[$user->employee_id]['dates'][$from])) {
                    if ($this->checkLeave($user->employee_id, $from)) {
                        $formattedData[$user->employee_id]['dates'][$from][] = '<span class="text-success">Leave</span>';
                        $leaveArray[$user->employee_id] = [
                            'user_id' => $user->employee_id,
                            'user' => '<a href="' . base_url('attendance/user_attendance/' . $user->employee_id) . '" >' . $user->employee_id . '</a>',
                            'name' => '<a href="' . base_url('attendance/user_attendance/' . $user->employee_id) . '">' . $user->first_name . ' ' . $user->last_name . '</a>',
                            'dates' => [],
                        ];
                        $leaveArray[$user->employee_id]['dates'][$from][] = '<span class="text-success">Leave</span>';
                    } elseif ($this->attendance_model->holidayCheck($user->employee_id, $from)) {
                        $formattedData[$user->employee_id]['dates'][$from][] = '<span class="text-info">Holiday</span>';
                        $absentArray[$user->employee_id] = [
                            'user_id' => $user->employee_id,
                            'user' => '<a href="' . base_url('attendance/user_attendance/' . $user->employee_id) . '">' . $user->employee_id . '</a>',
                            'name' => '<a href="' . base_url('attendance/user_attendance/' . $user->employee_id) . '">' . $user->first_name . ' ' . $user->last_name . '</a>',
                            'dates' => [],
                        ];
                        $absentArray[$user->employee_id]['dates'][$from][] = '<span class="text-info">Holiday</span>';
                    } else {
                        $formattedData[$user->employee_id]['dates'][$from][] = '<span class="text-danger">Absent</span>';
                        $absentArray[$user->employee_id] = [
                            'user_id' => $user->employee_id,
                            'user' => '<a href="' . base_url('attendance/user_attendance/' . $user->employee_id) . '" >' . $user->employee_id . '</a>',
                            'name' => '<a href="' . base_url('attendance/user_attendance/' . $user->employee_id) . '">' . $user->first_name . ' ' . $user->last_name . '</a>',
                            'dates' => [],
                        ];
                        $absentArray[$user->employee_id]['dates'][$from][] = '<span class="text-danger">Absent</span>';
                    }
                }
            }
        }
        if ($get["absent"] && $get["absent"] == 1) {
            $resultArray = array_values($absentArray);
        } elseif ($get["leave"] && $get["leave"] == 1) {
            $resultArray = array_values($leaveArray);
        } elseif ($get["present"] && $get["present"] == 1) {
            $resultArray = array_values($presentArray);
        } else {
            $resultArray = array_values($formattedData);
        }

        return $resultArray;
    }


    public function filter_count_abs_for_user($date)
    {
        $abs = 0;
        $leaves = 0;
        $present = 0;

        $user = $this->ion_auth->user()->row();
        $employee_id = get_employee_id_from_user_id($user->id);
        $where = " WHERE DATE(attendance.finger) BETWEEN '" . date('Y-m-01') . "' AND '" . $date . "' AND users.employee_id =" . $employee_id;
        $leftjoin = " LEFT JOIN users ON attendance.user_id = users.employee_id";
        $query = $this->db->query("SELECT attendance.*, CONCAT(users.first_name, ' ', users.last_name) AS user 
        FROM attendance " . $leftjoin . $where);

        $results = $query->result_array();
        $start_date = date('Y-m-01');
        $end_date = $date;
        $date_array = array();
        $unique_dates = array();

        $current_date = $start_date;

        while ($current_date <= $end_date) {
            $date_array[] = $current_date;
            $current_date = date('Y-m-d', strtotime($current_date . ' + 1 day'));
        }
        foreach ($date_array as $date) {
            $absentForDate = true;
            foreach ($results as $attendance) {
                $finger = date("Y-m-d", strtotime($attendance["finger"]));
                if ($date == $finger) {
                    $absentForDate = false;
                    if (in_array($finger, $date_array) && !in_array($finger, $unique_dates)) {
                        $unique_dates[] = $finger;
                        $present++;
                    } else {
                        if ($this->checkLeave($employee_id, $date) && !in_array($finger, $unique_dates)) {
                            $leaves++;
                        }
                    }
                }
            }
            if ($absentForDate) {
                if ($this->attendance_model->holidayCheck($employee_id, $date)) {
                    $unique_dates[] = $date;
                } else {
                    $abs++;
                }
            }
        }

        return [
            "results" => $results,
            "abs" => $abs,
            "leave" => $leaves,
            "present" => $present,
        ];
    }

    public function holidayCheck($user_id, $date)
    {
        $this->db->select('*');
        $this->db->from('holiday');
        $this->db->where('starting_date <=', $date);
        $this->db->where('ending_date >=', $date);

        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            $holidays = $query->result_array();
            foreach ($holidays as $holiday) {
                if ($holiday["apply"] == '0') {
                    return true;
                } elseif ($holiday["apply"] == '1') {
                    $user = $this->ion_auth->user($user_id)->row();
                    $department = $user->department;
                    $appliedDepart =  json_decode($holiday["department"]);
                    if (in_array($department, $appliedDepart)) {
                        return true;
                    }
                } elseif ($holiday["apply"] == '2') {
                    $appliedUser =  json_decode($holiday["users"]);
                    if (in_array($user_id, $appliedUser)) {
                        return true;
                    }
                }
            }
        } else {
            $dayOfWeek = date('N', strtotime($date));
            if ($dayOfWeek == 6 || $dayOfWeek == 7) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function checkLeave($user_id, $date)
    {
        $this->db->select('*');
        $this->db->from('leaves');
        $this->db->where('user_id', $user_id);
        $this->db->where('starting_date <=', $date);
        $this->db->where('ending_date >=', $date);
        $this->db->where('paid', 0);
        $this->db->where('status', 1);
        $this->db->where('leave_duration NOT LIKE', '%Half%');
        $this->db->where('leave_duration NOT LIKE', '%Short%');
        $query = $this->db->get();
        if ($query->num_rows() > 0) {
            return true;
        } else {
            return false;
        }
    }
    public function Get_events()
    {
        $currentDate = date('Y-m-d');
        $nextTwoMonths = date('Y-m-d', strtotime('+3 months', strtotime($currentDate)));
        $array = [];
        $system_users = $this->ion_auth->members_all()->result();
        foreach ($system_users as $user) {
            if ($user->finger_config == '1' && $user->active == '1') {
                $join_date = $user->join_date;
                $probation_date = $user->probation;
                $date_of_birth = $user->DOB;

                // Check if any event falls within the next two months
                if (
                    (date('m-d', strtotime($date_of_birth)) >= date('m-d', strtotime($currentDate)) &&
                        date('m-d', strtotime($date_of_birth)) <= date('m-d', strtotime($nextTwoMonths))) ||
                    (date('m-d', strtotime($join_date)) >= date('m-d', strtotime($currentDate)) &&
                        date('m-d', strtotime($join_date)) <= date('m-d', strtotime($nextTwoMonths))) ||
                    (date('Y-m-d', strtotime($probation_date)) >= date('Y-m-d', strtotime($currentDate)) &&
                        date('Y-m-d', strtotime($probation_date)) <= date('Y-m-d', strtotime($nextTwoMonths)))
                ) {
                    $events = [];

                    // Check for Birthday event
                    if (
                        date('m-d', strtotime($date_of_birth)) >= date('m-d', strtotime($currentDate)) &&
                        date('m-d', strtotime($date_of_birth)) <= date('m-d', strtotime($nextTwoMonths))
                    ) {
                        $events[] = [
                            'user' => $user->first_name . ' ' . $user->last_name,
                            'profile' => $user->profile,
                            'short' => strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)),
                            'event' => 'Birthday',
                            'date' => date('j F', strtotime($date_of_birth))
                        ];
                    }

                    // Check for Anniversary event
                    if (
                        date('m-d', strtotime($join_date)) >= date('m-d', strtotime($currentDate)) &&
                        date('m-d', strtotime($join_date)) <= date('m-d', strtotime($nextTwoMonths))
                    ) {
                        $events[] = [
                            'user' => $user->first_name . ' ' . $user->last_name,
                            'profile' => $user->profile,
                            'short' => strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)),
                            'event' => 'Anniversary',
                            'date' => date('j F', strtotime($join_date))
                        ];
                    }

                    // Check for Probation Ending event
                    if (
                        date('Y-m-d', strtotime($probation_date)) >= date('Y-m-d', strtotime($currentDate)) &&
                        date('Y-m-d', strtotime($probation_date)) <= date('Y-m-d', strtotime($nextTwoMonths))
                    ) {
                        $events[] = [
                            'user' => $user->first_name . ' ' . $user->last_name,
                            'profile' => $user->profile,
                            'short' => strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)),
                            'event' => 'Probation Ending',
                            'date' => date('j F', strtotime($probation_date))
                        ];
                    }

                    $array = array_merge($array, $events);
                }
            }
        }
        // events 
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
            if (
                (date('Y-m-d', strtotime($start)) >= date('Y-m-d', strtotime($currentDate)) && date('Y-m-d', strtotime($end)) <= date('Y-m-d', strtotime($nextTwoMonths))) ||
                (date('Y-m-d', strtotime($start)) <= date('Y-m-d', strtotime($currentDate)) && date('Y-m-d', strtotime($end)) <= date('Y-m-d', strtotime($nextTwoMonths)) && date('Y-m-d', strtotime($end)) >= date('Y-m-d', strtotime($currentDate)))
            ) {
                if (date('j F', strtotime($start)) != date('j F', strtotime($end))) {
                    $array[] = [
                        'user' => $title,
                        'profile' => '',
                        'short' => strtoupper(substr($title, 0, 2)),
                        'event' => 'Event',
                        'date' => date('j F', strtotime($start)) . '-' . date('j F', strtotime($end))
                    ];
                } else {
                    $array[] = [
                        'user' => $title,
                        'profile' => '',
                        'short' => strtoupper(substr($title, 0, 2)),
                        'event' => 'Event',
                        'date' => date('j F', strtotime($start))
                    ];
                }
            }
        }

        usort($array, function ($a, $b) {
            return strtotime($a['date']) - strtotime($b['date']);
        });
        return $array;
    }
}
