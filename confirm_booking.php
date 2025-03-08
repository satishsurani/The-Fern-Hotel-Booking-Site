<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FERN Hotel - Confirm Bookings Details</title>
    <?php require('inc/links.php') ?>
</head>

<?php require('inc/header.php'); ?>

<?php
if (!isset($_GET['id'])) {
    redirect('rooms.php');
} else if (!(isset($_SESSION['login']) && $_SESSION['login'] == true)) {
    redirect('rooms.php');
}

$data = filteration($_GET);

$room_res = select("SELECT * FROM `rooms` WHERE `id`=? AND `status`=? ORDER BY `id` DESC", [$data['id'], 1], 'ii');

if (mysqli_num_rows($room_res) == 0) {
    redirect('rooms.php');
}

$room_data = mysqli_fetch_assoc($room_res);

$_SESSION['room'] = [
    "id" => $room_data['id'],
    "name" => $room_data['name'],
    "price" => $room_data['price'],
    "payment" => null,
    "available" => false
];

$user_res = select("SELECT * FROM `users` WHERE `id` = ? LIMIT 1", [$_SESSION['id']], 'i');
$user_data = mysqli_fetch_assoc($user_res);

$today = date('Y-m-d');
?>

<div class="container">
    <div class="row">
        <div class=" col-12 my-5 mb-4 px-4">
            <h2 class="fw-bold h-font">CONFIRM BOOKING</h2>
            <div style="font-size:14px;">
                <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
                <span class="text-secondary"> > </span>
                <a href="rooms.php" class="text-secondary text-decoration-none">ROOMS</a>
                <span class="text-secondary"> > </span>
                <a href="confirm_booking.php" class="text-secondary text-decoration-none">CONFIRM</a>
            </div>
        </div>

        <div class="col-lg-7 col-md-12 px-4">
            <?php
            $room_thumb = ROOMS_IMG_PATH . "thumbnail.jpg";
            $thumb_q = mysqli_query($con, "SELECT * FROM `room_images` WHERE `room_id`='$room_data[id]' AND `thumb`='1'");

            if (mysqli_num_rows($thumb_q) > 0) {
                $thumb_res = mysqli_fetch_assoc($thumb_q);
                $room_thumb = ROOMS_IMG_PATH . $thumb_res['image'];
            }

            echo <<<data
                        <div class="card p-3 shadow-sm rounded">
                            <img src=$room_thumb class="img-fluid rounded mb-3">
                            <h5>$room_data[name]</h5>
                            <h6>₹$room_data[price] per night</h6>
                        </div>
                    data;
            $_SESSION['ORDER_ID'] = 'ORD_' . $_SESSION['id'] . random_int(11111, 9999999);
            ?>
        </div>

        <div class="col-lg-5 col-md-12 px-4">
            <div class="card mb-4 border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <form id="booking_form">
                        <h6 class="mb-3">BOOKING DETAILS</h6>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label mb-1">Name</label>
                                <input type="text" name="name" id="name" value="<?php echo $user_data['name'] ?>"
                                    class="form-control shadow-none" required>
                            </div>
                            <input type="hidden" id="email" name="email" value="<?php echo $user_data['email'] ?>">

                            <div class="col-md-6 mb-3">
                                <label class="form-label mb-1">Phone Number</label>
                                <input type="text" value="<?php echo $user_data['phonenum'] ?>" id="phonenum"
                                    name="phonenum" class="form-control shadow-none" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label mb-1">Check-in</label>
                                <input type="date" min="<?php echo $today; ?>" onchange="check_availability()" name="checkin" id="checkin"
                                    class="form-control shadow-none" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label mb-1">Check-out</label>
                                <input type="date" min="<?php echo $today; ?>" onchange="check_availability()" name="checkout" id="checkout"
                                    class="form-control shadow-none" required>
                            </div>
                            <div class="col-12">
                                <div class="spinner-border text-info mb-3 d-none" id="info_loader" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <h6 class="mb-3 text-danger" id="pay_info">Provide check-in and check-out date</h6>
                                <button disabled name="pay_now" id="PayNow"
                                    class="btn w-100 custom-bg shadow-none mb-1">Pay Now
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require('inc/footer.php'); ?>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
    let booking_form = document.getElementById('booking_form');
    let info_loader = document.getElementById('info_loader');
    let pay_info = document.getElementById('pay_info');
    let payNowButton = document.getElementById('PayNow');
    let amount;

    function check_availability() {
        let checkin_val = booking_form.elements['checkin'].value;
        let checkout_val = booking_form.elements['checkout'].value;
        let name = booking_form.elements['name'].value;
        let number = booking_form.elements['phonenum'].value;
        let email = booking_form.elements['email'].value;


        // Disable pay now button until availability is confirmed
        payNowButton.setAttribute('disabled', true);

        if (checkin_val !== '' && checkout_val !== '') {
            pay_info.classList.add('d-none');
            pay_info.classList.replace('text-dark', 'text-danger');
            info_loader.classList.remove('d-none');

            let data = new FormData();
            data.append('check_availability', '1');
            data.append('check_in', checkin_val);
            data.append('check_out', checkout_val);
            data.append('name', name);
            data.append('number', number);
            data.append('email', email);


            let xhr = new XMLHttpRequest();
            xhr.open('POST', 'ajax/confirm_booking.php', true);

            xhr.onload = function () {
                let response = JSON.parse(this.responseText);

                if (response.status === 'available') {
                    amount = response.payment;
                    pay_info.innerHTML = `No. of Days: ${response.days}<br>Total Amount to Pay: ₹${response.payment}`;
                    pay_info.classList.replace('text-danger', 'text-dark');
                    payNowButton.removeAttribute('disabled');
                } else {
                    pay_info.innerText = response.status;
                }

                pay_info.classList.remove('d-none');
                info_loader.classList.add('d-none');
            };

            xhr.send(data);
        }
    }

    payNowButton.addEventListener('click', function (e) {
        e.preventDefault(); // Prevent form submission
        let paymentOption = "netbanking"; // This can be dynamic based on user choice
        let payAmount = amount;

        let requestUrl = "ajax/submitpayment.php";
        let formData = new FormData();
        formData.append('paymentOption', paymentOption);
        formData.append('payAmount', payAmount);
        formData.append('action', 'payOrder');

        fetch(requestUrl, {
            method: 'POST',
            body: formData
        })
            .then(response => response.text())  // Get the raw response as text
            .then(data => {
                console.log(data); // Log the response
                let jsonResponse = JSON.parse(data);
                let orderID = jsonResponse.payment.order_number;
                let paymentID = jsonResponse.payment.payment_id;
                let options = {
                    "key": jsonResponse.payment.razorpay_key,
                    "amount": jsonResponse.payment.amount,
                    "currency": "INR",
                    "name": "Fern Hotel",
                    "description": jsonResponse.payment.description,
                    "order_id": jsonResponse.payment.rpay_order_id,
                    "handler": function (response) {
                        window.location.replace("payment-success.php?oid=" + orderID + "&rp_payment_id=" + response.razorpay_payment_id + "&rp_signature=" + response.razorpay_signature + "&pid=" + paymentID);
                    },
                    "modal": {
                        "ondismiss": function () {
                            window.location.replace("payment-success.php?oid=" + orderID);
                        }
                    },
                    "prefill": {
                        "name": jsonResponse.payment.name,
                        "email": jsonResponse.payment.email,
                        "mobile": jsonResponse.payment.mobile
                    },
                    "notes": {
                        "address": "The Fern Hotel"
                    },
                    "theme": {
                        "color": "#3399cc"
                    }
                };

                let rzp1 = new Razorpay(options);
                rzp1.on('payment.failed', function (response) {
                    window.location.replace("payment-failed.php?oid=" + orderID + "&reason=" + response.error.description + "&paymentid=" + response.error.metadata.payment_id + "&pid=" + paymentID);
                });
                rzp1.open();


            })
    });
</script>
</body>

</html>