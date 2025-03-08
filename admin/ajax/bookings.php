<?php

require('../inc/db_config.php');
require('../inc/essentials.php');
adminLogin();

if (isset($_POST['get_bookings'])) {
    $frm_data = filteration($_POST);
    $limit = 15;
    $page = isset($frm_data['page']) ? $frm_data['page'] : 1;
    $start = ($page - 1) * $limit;

    // Default filter (last 30 days)
    $date_filter = " AND bo.datentime >= CURDATE() - INTERVAL 30 DAY"; 

    if (isset($frm_data['date_filter'])) {
        if ($frm_data['date_filter'] == '30days') {
            $date_filter = " AND bo.datentime >= CURDATE() - INTERVAL 30 DAY";
        } elseif ($frm_data['date_filter'] == '90days') {
            $date_filter = " AND bo.datentime >= CURDATE() - INTERVAL 90 DAY";
        } elseif ($frm_data['date_filter'] == '1year') {
            $date_filter = " AND bo.datentime >= CURDATE() - INTERVAL 1 YEAR";
        } elseif ($frm_data['date_filter'] == 'all') {
            $date_filter = '';  // No filter for all time
        }
    }

    // Query to fetch bookings with the date filter applied
    $query = "SELECT bo.order_id, bd.user_name, bd.phonenum, bd.room_name, bo.trans_amt, bd.booking_status, bo.datentime, bd.checkin, bd.checkout
              FROM `payment` bo
              INNER JOIN `booking` bd ON bo.payment_id = bd.payment_id
              WHERE (bd.booking_status = 'confirmed' 
              OR bd.booking_status = 'cancelled'
              OR bd.booking_status = 'failed') " . $date_filter . "
              ORDER BY bd.booking_id DESC";

    $res = select($query, [], '');

    // Apply pagination limit
    $limit_query = $query . " LIMIT ?, ?";
    $values = [$start, $limit];
    $datatypes = 'ii';
    $limit_res = select($limit_query, $values, $datatypes);

    $i = 1;
    $table_data = "";
    $total_rows = mysqli_num_rows($res);
    $total_pages = ceil($total_rows / $limit);

    if ($total_rows == 0) {
        echo json_encode([
            'table_data' => "<b>No Data Found!</b>",
            'pagination' => ''
        ]);
        exit;
    }

    while ($data = mysqli_fetch_assoc($limit_res)) {
        $date = date("d-m-Y | H:i", strtotime($data['datentime']));
        $checkin = date("d-m-y", strtotime($data['checkin']));
        $checkout = date("d-m-y", strtotime($data['checkout']));

        $status_bg = '';
        $order_bg = '';
        if ($data['booking_status'] == 'confirmed') {
            $status_bg = 'bg-success';
            $order_bg = 'bg-success';
        } else if ($data['booking_status'] == 'cancelled') {
            $status_bg = 'bg-danger';
            $order_bg = 'bg-danger';
        } else {
            $status_bg = 'bg-warning text-dark';
            $order_bg = 'bg-warning text-dark';
        }

        $table_data .= "
            <tr>
                <td>$i</td>
                <td>
                    <span class='badge $order_bg'>
                        Order ID: $data[order_id]
                    </span>
                    <br>
                    <b>Name: </b> $data[user_name] <br>
                    <b>Phone No: </b> $data[phonenum] <br>
                </td>
                <td>
                    <b>Room: </b> $data[room_name] <br>
                    <b>Check-In: </b> $data[checkin] <br>
                    <b>Check-Out: </b> $data[checkout] <br>
                </td>
                <td>
                    <b>Amount: </b> ₹$data[trans_amt] <br>
                    <b>Date: </b> $date <br>
                </td>
                <td>
                    <span class='badge $status_bg'>$data[booking_status]</span>
                </td>
            </tr>
        ";
        $i++;
    }

    // Pagination
    $pagination = '';
    if ($total_pages > 1) {
        $pagination .= '<ul class="pagination justify-content-center">';
        for ($i = 1; $i <= $total_pages; $i++) {
            $pagination .= "<li class='page-item'><a class='page-link' href='javascript:get_bookings($i, \"$frm_data[date_filter]\")'>$i</a></li>";
        }
        $pagination .= '</ul>';
    }

    echo json_encode([
        'table_data' => $table_data,
        'pagination' => $pagination
    ]);
    exit;
}
?>
