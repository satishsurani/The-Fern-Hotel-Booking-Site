<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FERN Hotel - Rooms Details</title>
    <?php require('inc/links.php') ?>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>


</head>

<body class="bg-light">

    <?php require('inc/header.php'); ?>

    <?php
    if (!isset($_GET['id'])) {
        redirect('rooms.php');
    }

    $data = filteration($_GET);

    $room_res = select("SELECT * FROM `rooms` WHERE `id`=? AND `status`=? ORDER BY `id` DESC", [$data['id'], 1], 'ii');

    if (mysqli_num_rows($room_res) == 0) {
        redirect('rooms.php');
    }

    $room_data = mysqli_fetch_assoc($room_res);
    ?>

    <div class="container">
        <div class="row">

            <div class=" col-12 my-5 mb-4 px-4">
                <h2 class="fw-bold h-font"><?php echo $room_data['name']; ?></h2>
                <div style="font-size:14px;">
                    <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
                    <span class="text-secondary"> > </span>
                    <a href="rooms.php" class="text-secondary text-decoration-none">ROOMS</a>
                </div>
            </div>

            <div class="col-lg-7 col-md-12 px-4">
                <div class="swiper-container room-carousel">
                    <div class="swiper-wrapper">
                        <?php
                        $room_img = ROOMS_IMG_PATH . "thumbnail.jpg";
                        $img_q = mysqli_query($con, "SELECT * FROM `room_images` WHERE `room_id`='$room_data[id]'");

                        if (mysqli_num_rows($img_q) > 0) {
                            while ($img_res = mysqli_fetch_assoc($img_q)) {
                                echo "
                                    <div class='swiper-slide'>
                                        <img src='" . ROOMS_IMG_PATH . $img_res['image'] . "' class='d-block w-100'>
                                    </div>";
                            }
                        } else {
                            echo "
                                <div class='swiper-slide'>
                                    <img src='$room_img' class='d-block w-100 rounded'>
                                </div>";
                        }
                        ?>
                        <div class="swiper-button-next"></div>
                        <div class="swiper-button-prev"></div>
                    </div>                    
                </div>

            </div>

            <div class="col-lg-5 col-md-12 px-4">
                <div class="card mb-4 border-0 shadow-sm rounded-3">
                    <div class="card-body">
                        <?php
                        $login = 0;
                        if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
                            $login = 1;
                        }
                        $book_btn = '<button class="btn btn-sm w-100 text-white custom-bg shadow-none mb-1" onclick="checkLoginToBook(' . $login . ', ' . $room_data['id'] . ')">Book Now</button>';

                        echo <<<price
                                    <h4> ₹$room_data[price] per night</h4>
                                price;

                        echo <<<rating
                                    <div class="mb-3">
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                        <i class="bi bi-star-fill text-warning"></i>
                                    </div>
                                rating;

                        echo <<<guests
                                    <div class="mb-3">
                                        <h6 class="mb-1">guests</h6>
                                        <span class="badge rounded-pill bg-light text-dark text-wrap">
                                            $room_data[adult] Adults
                                        </span>
                                        <span class="badge rounded-pill bg-light text-dark text-wrap">
                                            $room_data[children] Childern
                                        </span>
                                    </div>
                                guests;

                        echo <<<area
                                    <div class="mb-3">
                                        <h6 class="mb-1">Area</h6>
                                        <span class="badge rounded-pill bg-light text-dark text-wrap me-1 mb-1">
                                            200 sq. ft.
                                        </span>
                                    </div>
                                area;

                        echo <<<book
                                    $book_btn
                                book;
                        ?>
                    </div>
                </div>
            </div>

            <div class="col-12 mt-4 px-4">
                <h5>Description</h5>
                <p>
                    <?php echo $room_data['description'] ?>
                </p>
            </div>
            <div></div>
        </div>
    </div>


    <?php require('inc/footer.php'); ?>

    <script>
        var swiper = new Swiper('.room-carousel', {
            spaceBetween: 30,
            loop: true,
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            effect: 'fade',
        });
    </script>

</body>

</html>