<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FERN Hotel - Bookings Details</title>
    <?php require('inc/links.php') ?>
</head>

<body class="bg-light">
    <?php
    require('inc/header.php');
    if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
        redirect('index.php');
    }
    ?>

    <div class="container">
        <div class="row">
            <div class="col-12 my-5 px-4">
                <h2 class="fw-bold h-font">BOOKINGS</h2>
                <div style="font-size:14px;">
                    <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
                    <span class="text-secondary"> > </span>
                    <a href="#" class="text-secondary text-decoration-none">BOOKINGS</a>
                </div>
            </div>

            <?php
            $query = "SELECT bo.*, bd.* 
                      FROM `payment` bo
                      INNER JOIN `booking` bd ON bo.payment_id = bd.payment_id
                      WHERE (bd.booking_status IN ('confirmed', 'cancelled'))
                      AND (bd.user_id = ?)
                      ORDER BY bd.booking_id DESC";

            $result = select($query, [$_SESSION['id']], 'i');

            while ($data = mysqli_fetch_assoc($result)) {
                $checkin = date("d-m-y", strtotime($data['checkin']));
                $checkout = date("d-m-y", strtotime($data['checkout']));
                $date = date("d-m-y | h:ia", strtotime($data['datentime']));

                $status_bg = "";
                $btn = "";
                $cancelbtn = "";

                // Check booking status to determine button visibility
                if ($data['booking_status'] == 'confirmed') {
                    $status_bg = "bg-success";
                    $btn = "<a href='generate_pdf.php?gen_pdf&id=$data[booking_id]' class='btn btn-dark btn-sm shadow-none'>Download PDF</a>";
                    $cancelbtn = "<button class='btn btn-danger btn-sm shadow-none cancel-booking' data-payment-id='$data[payment_id]'>Cancel Booking</button>";
                } else if ($data['booking_status'] == 'pending') {
                    $status_bg = "bg-warning";
                } else if ($data['booking_status'] == 'cancelled') {
                    $status_bg = "bg-danger";
                } else {
                    $status_bg = "bg-info";
                }

                echo <<<bookings
                    <div class='col-md-4 px-4 mb-4'>
                        <div class='bg-white rounded shadow-md' style="padding: 12px;">
                            <h5 class='fw-bold'>$data[room_name]</h5>
                            <p>
                                <b>Check in: </b> $checkin <br>
                                <b>Check out: </b> $checkout
                            </p>
                            <p>
                                <b>Amount: </b> ₹$data[trans_amt] <br>
                                <b>Order ID: </b> $data[order_id]<br>
                                <b>Date: </b> $date
                            </p>
                            <p>
                                <span class='badge $status_bg' id='status'>$data[booking_status]</span>
                            </p>
                            $btn
                            $cancelbtn
                        </div>
                    </div>
                bookings;
            }
            ?>
        </div>
    </div>

    <?php require('inc/footer.php'); ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Add event listener for all Cancel Booking buttons
            const cancelButtons = document.querySelectorAll('.cancel-booking');
            cancelButtons.forEach(button => {
                button.addEventListener('click', function () {
                    const paymentId = this.getAttribute('data-payment-id');
                    if (confirm('Are you sure you want to cancel this booking?')) {
                        cancelBooking(paymentId);
                    }
                });
            });

            // Cancel Booking Function
            function cancelBooking(paymentId) {
                let xhr = new XMLHttpRequest();
                xhr.open("POST", "ajax/cancel_booking.php", true);
                xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

                xhr.onload = function () {
                    console.log('Server Response:', this.responseText);  // Log the raw response for debugging

                    if (this.status === 200) {
                        try {
                            // Try parsing the response as JSON
                            const response = JSON.parse(this.responseText);

                            if (response.success) {
                                showAlert('success', 'Booking cancelled successfully!');
                                const bookingCard = document.querySelector(`[data-payment-id='${paymentId}']`).closest('.bg-white');
                                const badge = bookingCard.querySelector('.badge');
                                badge.classList.replace('bg-warning', 'bg-danger');
                                badge.classList.replace('bg-success', 'bg-danger');
                                badge.textContent = 'Cancelled';


                                const cancelButton = bookingCard.querySelector('.cancel-booking');
                                cancelButton.remove();

                                // Remove Download PDF button as well
                                const downloadButton = bookingCard.querySelector('.btn-dark');
                                if (downloadButton) {
                                    downloadButton.remove();
                                }
                            } else {
                                showAlert('danger', response.message || 'Failed to cancel booking.');
                            }
                        } catch (error) {
                            console.error('Error parsing JSON:', error);
                            showAlert('danger', 'An error occurred while processing the response.');
                        }
                    } else {
                        showAlert('danger', 'Server error. Please try again later.');
                    }
                };


                xhr.send('payment_id=' + paymentId);
            }

        });
    </script>
</body>

</html>