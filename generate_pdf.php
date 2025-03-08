<?php
require('admin/inc/essentials.php');
require('admin/inc/db_config.php');
require_once 'vendor/fpdf/fpdf.php';


session_start();

if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect("index.php");
}

if (isset($_GET['gen_pdf']) && isset($_GET['id'])) {
    $frm_data = filteration($_GET);

    // Sanitize and escape the booking ID
    $booking_id = mysqli_real_escape_string($con, $frm_data['id']);

    // Define the query
    $query = "SELECT bo.*, bd.*, uc.email FROM `payment` bo
    INNER JOIN `booking` bd ON bo.payment_id = bd.payment_id
    INNER JOIN `users` uc ON bd.user_id = uc.id
    WHERE ((bd.booking_status='confirmed')
    OR (bd.booking_status='cancelled')
    OR (bd.booking_status='failed'))
    AND (bd.booking_id='$booking_id')";

    // Execute the query
    $res = mysqli_query($con, $query);

    // Check for query failure
    if (!$res) {
        die('Query Failed: ' . mysqli_error($con));  // Output the error message
    }

    $total_rows = mysqli_num_rows($res);

    if ($total_rows == 0) {
        header('location: index.php');
        exit;
    }

    $data = mysqli_fetch_assoc($res);

    $date = date("d-m-Y | h:ia", strtotime($data['datentime']));
    $checkin = date("d-m-Y", strtotime($data['checkin']));
    $checkout = date("d-m-Y", strtotime($data['checkout']));

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, 'The Fern', 0, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(0, 10, "Order ID: {$data['order_id']}", 1, 1);
    $pdf->Cell(0, 10, "Booking Date: $date", 1, 1);
    $pdf->Cell(0, 10, "Status: {$data['booking_status']}", 1, 1);
    $pdf->Cell(0, 10, "Name: {$data['user_name']}", 1, 1);
    $pdf->Cell(0, 10, "Email: {$data['email']}", 1, 1);
    $pdf->Cell(0, 10, "Phone Number: {$data['phonenum']}", 1, 1);
    $pdf->Cell(0, 10, "Room Name: {$data['room_name']}", 1, 1);
    $pdf->Cell(0, 10, "Check In: $checkin", 1, 1);
    $pdf->Cell(0, 10, "Check Out: $checkout", 1, 1);

    if ($data['booking_status'] == 'failed') {
        $pdf->Cell(0, 10, "Transaction Amount: {$data['trans_amt']}", 1, 1);
    } else {
        $pdf->Cell(0, 10, "Room Number: {$data['room_id']}", 1, 1);
        $pdf->Cell(0, 10, "Amount Paid: {$data['trans_amt']}", 1, 1);
    }
    $pdf->Output($data['order_id'] . '.pdf', 'D');

} else {
    header('location: index.php');
}
?>