<?php
require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');

// Ensure no HTML or errors are printed before the JSON response
ob_start();

$response = ['success' => false];

if (isset($_POST['payment_id'])) {
    $payment_id = $_POST['payment_id'];

    // Prepare SQL query for updating booking and payment statuses
    $query = "UPDATE `booking` 
              SET `booking_status` = 'cancelled' 
              WHERE `payment_id` = ?";
    $query2 = "UPDATE `payment` 
               SET `trans_status` = 'cancelled' 
               WHERE `payment_id` = ?";

    // Prepare and execute the first query
    $stmt1 = $con->prepare($query);
    $stmt1->bind_param('i', $payment_id);
    $stmt1->execute();

    // Prepare and execute the second query
    $stmt2 = $con->prepare($query2);
    $stmt2->bind_param('i', $payment_id);
    $stmt2->execute();

    // Check if both updates were successful
    if ($stmt1->affected_rows > 0 && $stmt2->affected_rows > 0) {
        $response['success'] = true;
    } else {
        $response['message'] = 'Failed to cancel the booking.';
    }

    // Close the prepared statements
    $stmt1->close();
    $stmt2->close();
} else {
    $response['message'] = 'Payment ID is missing.';
}

// Clean output buffer
ob_end_clean();

// Send the JSON response back to the client
echo json_encode($response);
?>
