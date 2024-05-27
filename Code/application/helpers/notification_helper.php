<?php
defined('BASEPATH') or exit('No direct script access allowed');


require __DIR__ . '/../../vendor/autoload.php';

use Minishlink\WebPush\WebPush;
use Minishlink\WebPush\Subscription;

function get_users_for_current_level_of_leave($leave_id, $user_id, $mode = '')
{
  $CI =& get_instance();

  // $query = $CI->db->query(
  //   "SELECT MAX(level) as a
  //     FROM leave_logs 
  //     WHERE leave_id = $leave_id"
  // );
  // $leavelOnLevel = $query->result_array();
  // $query = $CI->db->query(
  //   "SELECT MAX(step_no) as b
  //     FROM leave_hierarchy"
  // );
  // $maxLevel = $query->result_array();

  // if ($leavelOnLevel[0]['a'] == $maxLevel[0]['b']) {

  //   // leave has been approved
  // }

  $response = [];
  $saas_id = $CI->session->userdata('saas_id');

  $query = $CI->db->query(
    "SELECT g.* 
  FROM groups g
  INNER JOIN leave_hierarchy lh ON g.id = lh.group_id
  INNER JOIN leave_logs ll ON lh.step_no = ll.level
  WHERE ll.leave_id = $leave_id
  AND ll.level = (
      SELECT MAX(level) 
      FROM leave_logs 
      WHERE leave_id = $leave_id
  )
  AND g.saas_id = $saas_id
  AND (g.assigned_users = '' OR g.assigned_users LIKE '%" . $user_id . "%')"
  );
  $respectiveUsers = $query->result_array();
  $user_ids = [];
  foreach ($respectiveUsers as $value) {
    $user_ids[] = $value['id'];
  }

  $gids = implode(", ", $user_ids);

  $query = $CI->db->query(
    "select user_id from users_groups where group_id in ($gids)"
  );
  $user_ids = $query->result_array();
  $uids = [];
  foreach ($user_ids as $value) {
    $uids[] = $value['user_id'];
  }
  $uids = implode(", ", $uids);
  return $gids;
}

function test_notification()
{
  $notifications = [];
  $message = array();
  $message['msg'] = "Incoming holiday 😀";
  $message['title'] = ucfirst("Holiday");
  $message['url'] = "https://pms.mobipixels.com/attendance/user_attendance";
  $subscribers = array();
  $subscribers[] = json_decode('{"endpoint":"https:\/\/fcm.googleapis.com\/fcm\/send\/dzULip3CC_4:APA91bHMZzbdnN6UZOeKfCy_rH252mWC4IAeBz5KTaVv8F0elT69dx9BtYidNHG1LYmrKIEjQGCnsA7dgdl16CpgCp0kML7ah0QaEZvZDSH3ndDyHQXeqi1oSOb7CqThCLZrKvWlzsD0","expirationTime":null,"keys":{"p256dh":"BP3s0d-n5gFZoHFduPXKC1YaW1bNL-ct0eEcSUosAdOu25KhiGWedBd77V4qGPmqDWssjWnBLYDtKRNS5QfZYGQ","auth":"y1qsv5kDtb0nOMSrgDxz1w"}}', true);
  foreach ($subscribers as $value) {
    $subscription = Subscription::create($value);
    // $subscription = Subscription::create(json_decode($value, true));
    $notification = [
      'title' => $message['title'],
      'body' => $message['msg']
    ];
    $notifications[] = ['subscription' => $subscription, 'payload' => json_encode($notification),];
    // $notifications[] = ['subscription' => $subscription, 'payload' => '{"message":"' . $message['msg'] . '"}',];
  }

  $auth = array(
    'VAPID' => array(
      'subject' => 'AMS/PMS',
      'publicKey' => file_get_contents(__DIR__ . '/../keys/public_key.txt'),
      'privateKey' => file_get_contents(__DIR__ . '/../keys/private_key.txt'),
    ),
  );

  $webPush = new WebPush($auth, [], 6, ['verify' => false]);
  foreach ($notifications as $notification)
    $webPush->queueNotification($notification['subscription'], $notification['payload']);

  foreach ($webPush->flush() as $report) {
    $endpoint = $report->getRequest()->getUri()->__toString();

    if ($report->isSuccess()) {
      echo json_encode("[v] Message sent successfully for subscription {$endpoint}.");
    } else {
      echo json_encode("[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}");
    }
  }
}

function save_notifications($type, $notification, $from_id = '', $to_id)
{
  $CI =& get_instance();
  // $query = $CI->db->query("insert into notifications(notification, type, type_id, from_id, to_id) values ('$notification', '$type', 888, $to_id) ");

  if ($type === 'holiday') {
    $saas_id = $CI->session->userdata('saas_id');
  }

  $CI->db->insert("notifications", [
    "notification" => $notification,
    "type" => $type,
    "type_id" => "111",
    "from_id" => "222",
    "to_id" => $to_id
  ]);

  return "done";
}

function generate_notification_message($type, $data = [])
{
  $response = [];
  if ($type == 'holiday') {
    $response['msg'] = "Incoming holiday 😀";
    $response['title'] = ucfirst($type);
    $response['url'] = "https://pms.mobipixels.com/attendance/user_attendance";
  } else if ($type == 'event') {
    $response['msg'] = "Holiday for an upcomming event 😀";
    $response['title'] = 'Upcoming ' . ucfirst($type);
    $response['url'] = "https://pms.mobipixels.com/attendance/user_attendance";
  } else if ($type == "project") {
    $response['msg'] = "You have been added to a new project";
    $response['title'] = "New " . ucfirst($type);
    $response['url'] = "https://pms.mobipixels.com/projects";
  } else if ($type == "task_assignment") {
    $response['msg'] = "New task assigned to you.";
    $response['title'] = "New Task";
    $response['url'] = "https://pms.mobipixels.com/projects";
  } else if ($type == "task_completion") {
    $response['msg'] = "Your task has been marked as completed.";
    $response['title'] = "Task completed";
    $response['url'] = "https://pms.mobipixels.com/projects";
  } else if ($type == "leave_forwarded_to") {
    $response['msg'] = "Your leave has been forwareded. ";
    $response['title'] = ucfirst($type);
    $response['url'] = "https://pms.mobipixels.com/leaves";
  } else if ($type == "leave_approved") {
    $response['msg'] = "Your leave have been approved.";
    $response['title'] = ucfirst($type);
    $response['url'] = "https://pms.mobipixels.com/leaves";
  } else if ($type == "biometric_missing_request") {
    $response['msg'] = "New attendence request recieved.";
    $response['title'] = ucfirst($type);
    $response['url'] = "https://pms.mobipixels.com/biometric_missing";
  } else if ($type == "biometric_missing_request_approved") {
    $response['msg'] = "You attendence request has been approved.";
    $response['title'] = ucfirst($type);
    $response['url'] = "https://pms.mobipixels.com/biometric_missing";
  } else if ($type == "leave_requested") {
    $response['msg'] = "You have a new leave request";
    $response['title'] = "Leave Requested";
    $response['url'] = "https://pms.mobipixels.com/leaves";
  }
  return $response;
}

function push_notifications($type, $recipients = [], $metadata = [])
{
  $CI =& get_instance();
  $subscribers = array();
  switch ($type) {

    // 
    // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // 
    // 
    // just the type is requried without any recipients
    case 'holiday':
      $saas_id = sanitizeDigits($recipients['saas_id']);
      $recipients_types = $recipients['recipients'];
      if ($recipients_types === 'all') {
        $query = $CI->db->query("SELECT * FROM notification_subscribers WHERE saas_id = $saas_id");
      } else if ($recipients_types == 'users') {
        $ids = implode(", ", $recipients['ids']);
        $query = $CI->db->query("SELECT * FROM notification_subscribers WHERE emp_id in ($ids)");
      } else if ($recipients_types == 'department') {
        $ids = implode(", ", $recipients['ids']);
        // echo "SELECT ns.* FROM notification_subscribers ns 
        // INNER JOIN users u ON ns.emp_id = u.employee_id
        // INNER JOIN departments d on d.id = u.department
        // where d.id in ($ids)";
        // exit;
        $query = $CI->db->query("SELECT ns.* FROM notification_subscribers ns 
        INNER JOIN users u ON ns.emp_id = u.employee_id
        INNER JOIN departments d on d.id = u.department
        where d.id in ($ids)");
      }
      $subscribers = $query->result_array();
      break;
    case 'event':
      // $saas_id = sanitizeDigits($CI->session->userdata('saas_id'));
      $saas_id = sanitizeDigits($recipients['saas_id']);
      $query = $CI->db->query("SELECT * FROM notification_subscribers WHERE saas_id = $saas_id");
      $subscribers = $query->result_array();
      break;

    //
    // // // // // // // // // // // // // // // // // // // // // // // // // // // // // // 
    // 
    case "project": // the type and list of recipients (client, and everyone that has been added to it)
      array_push($recipients['users'], $recipients['client_id'][0]);
      $ids = implode(", ", $recipients['users']);
      $query = $CI->db->query("SELECT DISTINCT * FROM notification_subscribers WHERE user_id in ($ids) and saas_id = " . $recipients['saas_id']);
      $subscribers = $query->result_array();
      break;
    case "task_completion": // the type, assigne and client
    case "task_assignment": // the type, assigne and client
      $ids = $recipients['user_id'];
      $query = $CI->db->query("SELECT * FROM notification_subscribers WHERE user_id = $ids");
      $subscribers = $query->result_array();
      break;
    case "leave_requested": // the type, forwarded to and user
      $ids = $recipients['user_id'];
      $query = $CI->db->query("SELECT * FROM notification_subscribers WHERE user_id in (96)");
      // $query = $CI->db->query("SELECT * FROM notification_subscribers WHERE user_id in ($ids)");
      $subscribers = $query->result_array();
      break;
    case "leave_forwarded_to": // the type, forwarded to and user
    case "leave_approved": // the type, forwarded to and user
    case "biometric_missing_request":
      $query = $CI->db->select('notification_subscribers.*')
        ->from('users_groups')
        ->where_in('group_id', $recipients['biometric_accepters_group'])
        ->join('notification_subscribers', 'notification_subscribers.user_id = users_groups.user_id', 'inner')
        ->get();
      $subscribers = $query->result_array();
      break; // the type, forwarded to and user
    case "biometric_missing_request_approved": // the type, forwarded to and user
      $ids = implode(", ", $recipients);
      $query = $CI->db->query("SELECT * FROM notification_subscribers WHERE user_id in ($ids)");
      $subscribers = $query->result_array();
      break;
    default:
      return false;
  }

  $notifications = [];
  $message = generate_notification_message($type);
  foreach ($subscribers as $value) {
    $subscription = Subscription::create(json_decode($value['endpoint'], true));
    $notification = [
      'title' => $message['title'],
      'body' => $message['msg'],
      // 'icon' => 'path/to/icon.png',
    ];
    $notifications[] = ['subscription' => $subscription, 'payload' => json_encode($notification),];
    // $notifications[] = ['subscription' => $subscription, 'payload' => '{"message":"' . $message['msg'] . '"}',];
  }

  $auth = array(
    'VAPID' => array(
      'subject' => 'AMS/PMS',
      'publicKey' => file_get_contents(__DIR__ . '/../keys/public_key.txt'), // don't forget that your public key also lives in app.js
      'privateKey' => file_get_contents(__DIR__ . '/../keys/private_key.txt'), // in the real world, this would be in a secret file
    ),
  );

  $webPush = new WebPush($auth, [], 6, ['verify' => false]);
  foreach ($notifications as $notification)
    $webPush->queueNotification($notification['subscription'], $notification['payload']);

  foreach ($webPush->flush() as $report) {
    $endpoint = $report->getRequest()->getUri()->__toString();

    // comment this 
    // comment this 
    // comment this 
    // comment this 
    // comment this 

    // if ($report->isSuccess()) {
    //   echo json_encode("[v] Message sent successfully for subscription {$endpoint}.");
    // } else {
    //   echo json_encode("[x] Message failed to sent for subscription {$endpoint}: {$report->getReason()}");
    // }
  }

}



