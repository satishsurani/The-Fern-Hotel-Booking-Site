<?php
session_start();
ob_clean(); // Clear any previous output
require_once('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');

use Razorpay\Api\Api;

require('../vendor/autoload.php');


header('Content-Type: application/json');

// Payment processing
if (isset($_POST['action']) && $_POST['action'] == 'payOrder') {

    $user_id = $_SESSION['id'];
    $room_id = $_SESSION['room']['id'];
    $checkin = $_SESSION['room']['checkin'];
    $checkout = $_SESSION['room']['checkout'];
    $payAmount = $_POST['payAmount'];


    // Generate Order ID for the session
    $order_id = 'ORD_' . uniqid();
    $_SESSION['ORDER_ID'] = $order_id;

    // Insert booking data into the database
    $query1 = "INSERT INTO payment(trans_amt, order_id) VALUES (?, ?)";
    insert($query1, [ $payAmount, $order_id], 'is');

    $payment_id = mysqli_insert_id($con);

    // Insert details into booking_details table
    $query2 = "INSERT INTO booking(payment_id, user_id, room_id, room_name, user_name, phonenum, email, checkin, checkout) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    insert($query2, [
        $payment_id,
        $user_id, 
        $room_id,
        $_SESSION['room']['name'],
        $_SESSION['user']['name'],
        $_SESSION['user']['number'],
        $_SESSION['user']['email'],
        $checkin->format('Y-m-d'),  // Convert to string
        $checkout->format('Y-m-d')  // Convert to string
    ], 'iiissssss');

    $booking_id = mysqli_insert_id($con);
    $_SESSION['bookingId'] = $booking_id;

    // Razorpay API credentials
    $razorpay_key = 'rzp_test_dt8ARo16LbgcBt'; // Your Razorpay Key ID
    $razorpay_secret = 'uEkLzjMFQIgSmMcwsFjg2TKy'; // Your Razorpay Secret Key

    // Initialize Razorpay API client
    $api = new Api($razorpay_key, $razorpay_secret);

    // Payment order details
    $orderData = [
        'amount' => $payAmount * 100, // Amount in paise (Razorpay expects amount in paise)
        'currency' => 'INR',
        'receipt' => $order_id,
        'notes' => [
            'note_key_1' => 'Payment for booking',
        ]
    ];

    try {
        // Create the order using Razorpay API
        $order = $api->order->create($orderData);
        
        // If order creation is successful, send response back to the client
        echo json_encode([
            'booking' => [
                'res' => 'success',
                'message' => 'Booking details inserted successfully'
            ],
            'payment' => [
                'res' => 'success',
                'razorpay_key' => $razorpay_key,
                'order_number' => $order_id,
                'payment_id' => $payment_id,
                'amount' => $payAmount,
                'description' => 'Payment for booking',
                'rpay_order_id' => $order->id,
                'name' => $_SESSION['user']['name'],
                'email' => $_SESSION['user']['email'],
                'number' => $_SESSION['user']['number']
            ]
        ]);
    } catch (Exception $e) {
        // In case of failure, return the error message
        echo json_encode([
            'res' => 'failure',
            'info' => 'Payment Request Failed: ' . $e->getMessage()
        ]);
    }
}
?>
