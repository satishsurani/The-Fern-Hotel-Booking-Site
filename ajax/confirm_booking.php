<?php
session_start();
require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');

date_default_timezone_set("Asia/Calcutta");

if (isset($_POST['check_availability'])) {
    $frm_data = filteration($_POST);
    $status = '';
    $result = '';

    $today_date = new DateTime(date('Y-m-d'));
    $checkin_date = new DateTime($frm_data['check_in']);
    $checkout_date = new DateTime($frm_data['check_out']);

    if ($checkin_date == $checkout_date) {
        $status = 'Check-in and Check-out date not be same';
        $result = json_encode(["status" => $status]);
    } elseif ($checkout_date < $checkin_date) {
        $status = 'invalid Check-in and Check-out date';
        $result = json_encode(["status" => $status]);
    } 

    if ($status) {
        echo $result;
    } else {
        $count_day = $checkin_date->diff($checkout_date)->days;
        $payment = $_SESSION['room']['price'] * $count_day;

        $_SESSION['room']['payment'] = $payment;
        $_SESSION['room']['available'] = true;
        $_SESSION['room']['checkin'] = $checkin_date;
        $_SESSION['room']['checkout'] = $checkout_date;
        $_SESSION['user']['name'] = $frm_data['name'];
        $_SESSION['user']['email'] = $frm_data['email'];
        $_SESSION['user']['number'] = $frm_data['number'];

        $result = json_encode([
            "status" => 'available',
            "days" => $count_day,
            "payment" => $payment,
            "name" => $frm_data['name'],
            "email" => $frm_data['email'],
            "number" => $frm_data['number'],
        ]);
        echo $result;
    }
}
?>