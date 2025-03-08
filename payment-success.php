<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FERN Hotel - Payment Status</title>
    <?php require('inc/links.php'); ?>
</head>

<body class="bg-light">
    <?php require('inc/header.php'); ?>

    <?php
    // Include PHPMailer
    require 'vendor/autoload.php';
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require_once('admin/inc/db_config.php');
    require('admin/inc/essentials.php');
    date_default_timezone_set("Asia/Calcutta");

    // Ensure user is logged in
    if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
        redirect('index.php');
    }

    // Check if the payment was successful
    if (isset($_GET['oid']) && isset($_GET['pid']) && isset($_GET['rp_signature'])) {
        $order_id = $_GET['oid'];
        $payment_id = $_GET['pid'];
        $booking_id = $_SESSION['bookingId'];
        $signature = $_GET['rp_signature'];

        // Fetch order details from the database
        $query = "SELECT * FROM booking WHERE payment_id = ?";
        $booking_res = select($query, [$payment_id], 'i');

        if (mysqli_num_rows($booking_res) > 0) {
            $booking_data = mysqli_fetch_assoc($booking_res);

            // Update the payment status and booking status for the order
            $update_query = "UPDATE payment 
                             SET trans_status = 'success' ,booking_id = ?
                             WHERE order_id = ?";
            update($update_query, [$booking_id, $order_id], 'is');

            $update_query = "UPDATE booking
                             SET booking_status = 'confirmed'
                             WHERE payment_id = ?";
            update($update_query, [$payment_id], 'i');

            // Prepare email to send booking confirmation
            $mail = new PHPMailer(true);
            try {
                // Server settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'luxavenu@gmail.com';
                $mail->Password = 'bloe aqhl orwb bovj';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('services@fernhotel.com', 'FERN Hotel');
                $mail->addAddress($_SESSION['user']['email'], $_SESSION['user']['name']);

                // Content
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = 'Room Booking Confirmation - FERN Hotel';

                // Create the body of the email
                $room_type = isset($booking_data['room_name']) ? $booking_data['room_name'] : 'Not specified';
                $check_in = isset($booking_data['checkin']) ? $booking_data['checkin'] : 'Not available';
                $check_out = isset($booking_data['checkout']) ? $booking_data['checkout'] : 'Not available';

                $mail->Body = "
                    <html>
                    <head>
                        <style>
                            body { font-family: 'Arial', sans-serif; color: black; background-color: #f4f4f4; padding: 0; }
                            .email-container { max-width: 600px; margin: 0 auto; background-color: #ffffff; padding: 30px; border-radius: 12px; box-shadow: 0 8px 12px rgba(0, 0, 0, 0.1); }
                            h3 { color: #AD8B3A; text-align: center; font-size: 24px; }
                            p { font-size: 16px; line-height: 1.6; margin-bottom: 20px; color: black; }
                            .booking-details { background-color: #e8f4e8; padding: 20px; margin-top: 20px; border-radius: 8px; border: 1px solid #ddd; }
                            .button { background-color: #AD8B3A; color: white; padding: 12px 20px; text-align: center; display: inline-block; text-decoration: none; border-radius: 6px; margin-top: 30px; font-size: 16px; }
                            .footer { font-size: 14px; color: black; text-align: center; margin-top: 30px; padding-top: 10px; border-top: 1px solid #ddd; }
                            .footer a { color: #AD8B3A; text-decoration: none; font-weight: bold; }
                        </style>
                    </head>
                    <body>
                        <div class='email-container'>
                            <h3>Dear " . htmlspecialchars($_SESSION['user']['name'], ENT_QUOTES, 'UTF-8') . ",</h3>
                            <p>We are pleased to inform you that your room booking has been successfully confirmed at FERN Hotel!</p>

                            <div class='booking-details'>
                                <p><strong>Booking ID:</strong> " . htmlspecialchars($order_id, ENT_QUOTES, 'UTF-8') . "</p>
                                <p><strong>Room Name:</strong> " . htmlspecialchars($room_type, ENT_QUOTES, 'UTF-8') . "</p>
                                <p><strong>Amount Paid:</strong> ₹" . htmlspecialchars($_SESSION['room']['payment'], ENT_QUOTES, 'UTF-8') . "</p>
                                <p><strong>Check-in Date:</strong> " . htmlspecialchars($check_in, ENT_QUOTES, 'UTF-8') . "</p>
                                <p><strong>Check-out Date:</strong> " . htmlspecialchars($check_out, ENT_QUOTES, 'UTF-8') . "</p>
                            </div>

                            <p>If you have any questions, feel free to reach out to us at any time.</p>

                            <p>Thank you for choosing FERN Hotel! We look forward to welcoming you.</p>

                            <div class='footer'>
                                <p>&copy; " . date("Y") . " FERN Hotel. All rights reserved.</p>
                                <p>Want to unsubscribe from our emails? <a href='#'>Click here</a></p>
                            </div>
                        </div>
                    </body>
                    </html>
                ";

                // Send the email
                $mail->send();
            } catch (Exception $e) {
                alert('failure', 'Booking successful, but email failed to send. Error: ' . $mail->ErrorInfo);
            }

            // Show success page to the user
            echo <<<data
                <div class="container">
                    <div class="row">
                        <div class="col-12 my-5 mb-3 px-4">
                            <h2 class="fw-bold h-font">Payment Success</h2>
                        </div>
                        <div class="col-12 px-4">
                            <p class="fw-bold alert alert-success">
                                <i class="bi bi-check-circle-fill"></i>
                                Payment successful! Your booking has been confirmed and the confirmation email has been sent.
                                <br><br>
                                <a href='bookings.php'>Go to My Bookings</a>
                            </p>
                        </div>
                    </div>
                </div>
            data;

        } else {
            // Handle the case where no booking is found
            $update_query = "UPDATE payment 
                             SET trans_status = 'failed' ,booking_id = ? 
                             WHERE order_id = ?";
            update($update_query, [$booking_id,$order_id], 'is');

            echo <<<data
                <div class="container">
                    <div class="row">
                        <div class="col-12 my-5 mb-3 px-4">
                            <h2 class="fw-bold h-font">Payment Failed</h2>
                        </div>
                        <div class="col-12 px-4">
                            <p class="fw-bold alert alert-danger">
                                <i class="bi bi-x-circle-fill"></i>
                                Payment failed! Your booking could not be confirmed.
                                <br><br>
                                <a href='rooms.php'>Try Again</a>
                            </p>
                        </div>
                    </div>
                </div>
            data;
        }
    } else {
        // If payment details are not present, redirect to failure page
        redirect('payment-failed.php');
    }
    ?>

    <?php require('inc/footer.php'); ?>
</body>

</html>
