<!DOCTYPE html>
<html lang='en'>

<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>FERN Hotel</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <?php require('inc/links.php') ?>
    
    <style>
        .availability-form {
            margin-top: -50px;
            z-index: 2;
            position: relative;
        }

        @media screen and (max-width:575px) {
            .availability-form {
                margin-top: 25px;
                padding: 0 35px;
            }
        }
    </style>
</head>

<body class='bg-light'>


    <?php 
    require('inc/header.php'); 
    $today = date('Y-m-d');
    ?>



    <!-- Carousel -->

    <div class='container-fluid px-lg-4 mt-4'>
        <div class='swiper swiper-container'>
            <div class='swiper-wrapper'>
                <div class='swiper-slide'>
                    <img src='images/carousel/1.png' class='w-100 d-block' />
                </div>
                <div class='swiper-slide'>
                    <img src='images/carousel/2.png' class='w-100 d-block' />
                </div>
                <div class='swiper-slide'>
                    <img src='images/carousel/3.png' class='w-100 d-block' />
                </div>
                <div class='swiper-slide'>
                    <img src='images/carousel/4.png' class='w-100 d-block' />
                </div>
                <div class='swiper-slide'>
                    <img src='images/carousel/5.png' class='w-100 d-block' />
                </div>
                <div class='swiper-slide'>
                    <img src='images/carousel/6.png' class='w-100 d-block' />
                </div>
            </div>
        </div>
    </div>

    <!-- Check Availability Form -->

    <div class='container availability-form'>
        <div class='row'>
            <div class='col-lg-12 bg-white p-4 rounded shadow'>
                <h5 class='mb-4 h-font'>Check Booking Availability</h5>
                <form action='rooms.php'>
                    <div class='row align-items-end'>
                        <div class='col-lg-3 mb-3'>
                            <label for='form-label' style='font-weight:500;'>Check-in</label>
                            <input type='date' class='form-control shadow-none' min="<?php echo $today; ?>" name="checkin" required>
                        </div>
                        <div class='col-lg-3 mb-3'>
                            <label for='form-label' style='font-weight:500;'>Check-out</label>
                            <input type='date' class='form-control shadow-none' min="<?php echo $today; ?>" name="checkout" required>
                        </div>
                        <div class='col-lg-3 mb-3'>
                            <label for='form-label' style='font-weight:500;'>Adults</label>
                            <select class='form-select shadow-none' name="adult">
                                <?php
                                    $guests_q = mysqli_query($con,"SELECT MAX(adult) AS `max_adult`, MAX(children) AS `max_children` FROM `rooms` WHERE `status`='1'");

                                    $guests_res = mysqli_fetch_assoc($guests_q);

                                    for($i=1;$i<=$guests_res['max_adult'];$i++)
                                    {
                                        echo"<option value='$i'>$i</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <div class='col-lg-2 mb-3'>
                            <label for='form-label' style='font-weight:500;'>Children</label>
                            <select class='form-select shadow-none' name="children">
                                <?php
                                    for($i=1;$i<=$guests_res['max_children'];$i++)
                                    {
                                        echo"<option value='$i'>$i</option>";
                                    }
                                ?>
                            </select>
                        </div>
                        <input type="hidden" name="check_availability">
                        <div class='col-lg-1 mb-lg-3 mt-2'>
                            <button type='submit' class='btn text-white shadow-none custom-bg'>Check</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- our Rooms  -->
    <h2 class='mt-5 pt-4 mb-4 text-center fw-bold h-font'>OUR ROOMS</h2>

    <div class='container'>
        <div class='row'>

            <?php
            $room_res = select('SELECT * FROM `rooms` WHERE `status`=? ORDER BY `id` DESC LIMIT 3', [1], 'i');

            while ($room_data = mysqli_fetch_assoc($room_res)) {

                $room_thumb = ROOMS_IMG_PATH . 'thumbnail.jpg';
                $thumb_q = mysqli_query($con, 'SELECT * FROM `room_images` WHERE `room_id`=\'' . $room_data['id'] . '\' AND `thumb`=\'1\'');

                if (mysqli_num_rows($thumb_q) > 0) {
                    $thumb_res = mysqli_fetch_assoc($thumb_q);
                    $room_thumb = ROOMS_IMG_PATH . $thumb_res['image'];
                }
                $login = 0;
                if(isset($_SESSION['login']) && $_SESSION['login'] == true){
                    $login =1;
                }
                $book_btn = '<button class="btn btn-sm text-white custom-bg shadow-none" onclick="checkLoginToBook(' . $login . ', ' . $room_data['id'] . ')">Book Now</button>';

                echo<<<data
                        <div class='col-lg-4 col-md-6 my-3'>
                             <div class='card border-0 shadow' style='max-width: 350px; margin:auto;'>
                                    <img src='$room_thumb' class='card-img-top'>
                                

                                <div class='card-body'>
                                    <h5>$room_data[name]</h5>
                                    <h6 class='mb-4'> ₹$room_data[price] per night</h6>

                                    <div class='features mb-4'>
                                        <h6 class='mb-1'>Features</h6>
                                        <span class='badge rounded-pill bg-light text-dark text-wrap'>
                                            2 Rooms
                                        </span>
                                        <span class='badge rounded-pill bg-light text-dark text-wrap'>
                                            3 Bathrrom
                                        </span>
                                        <span class='badge rounded-pill bg-light text-dark text-wrap'>
                                            1 Balcony
                                        </span>
                                        <span class='badge rounded-pill bg-light text-dark text-wrap'>
                                            3 sofa
                                        </span>
                                    </div>

                                    <div class='facilities mb-4'>
                                        <h6 class='mb-1'>Facilities</h6>
                                        <span class='badge rounded-pill bg-light text-dark text-wrap'>
                                            Wifi
                                        </span>
                                        <span class='badge rounded-pill bg-light text-dark text-wrap'>
                                            Television
                                        </span>
                                        <span class='badge rounded-pill bg-light text-dark text-wrap'>
                                            AC
                                        </span>
                                        <span class='badge rounded-pill bg-light text-dark text-wrap'>
                                            Room Heater
                                        </span>
                                    </div>

                                    <div class='guests mb-4'>
                                        <h6 class='mb-1'>guests</h6>
                                        <span class='badge rounded-pill bg-light text-dark text-wrap'>
                                             $room_data[adult] Adults
                                        </span>
                                        <span class='badge rounded-pill bg-light text-dark text-wrap'>
                                            $room_data[children] Children
                                        </span>
                                    </div>
                                    <div class='rating mb-4'>
                                        <h6 class='mb-1'>Rating</h6>
                                        <span class='badge rounded-pill bg-light'>
                                            <i class='bi bi-star-fill text-warning'></i>
                                            <i class='bi bi-star-fill text-warning'></i>
                                            <i class='bi bi-star-fill text-warning'></i>
                                            <i class='bi bi-star-fill text-warning'></i>
                                        </span>
                                    </div>
                                    <div class='d-flex justify-content-evenly mb-2'>
                                        $book_btn
                                        <a href='room_details.php?id=$room_data[id]' class='btn btn-sm btn-outline-dark shadow-none'>More Details</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    data;
            }
            ?>

            <div class='col-lg-12 text-center mt-5'>
                <a href='rooms.php' class='btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none'>More Rooms >>></a>
            </div>

        </div>
    </div>

    <!-- our facility  -->
    <h2 class='mt-5 pt-4 mb-4 text-center fw-bold h-font'>OUR FACILITIES</h2>

    <div class='container'>
        <div class='row justify-content-evenly px-lg-0 px-md-0 px-5'>
            <div class='col-lg-2 col-md-2 text-center bg-white rounded shadow py-4 my-3'>
                <img src='images/facilities/IMG_27079.svg' alt='' width='80px'>
                <h5 class='mt-3'>Geyser</h5>
            </div>
            <div class='col-lg-2 col-md-2 text-center bg-white rounded shadow py-4 my-3'>
                <img src='images/facilities/IMG_41622.svg' alt='' width='80px'>
                <h5 class='mt-3'>TV</h5>
            </div>
            <div class='col-lg-2 col-md-2 text-center bg-white rounded shadow py-4 my-3'>
                <img src='images/facilities/IMG_43553.svg' alt='' width='80px'>
                <h5 class='mt-3'>WiFi</h5>
            </div>
            <div class='col-lg-2 col-md-2 text-center bg-white rounded shadow py-4 my-3'>
                <img src='images/facilities/IMG_49949.svg' alt='' width='80px'>
                <h5 class='mt-3'>AC</h5>
            </div>
            <div class='col-lg-12 text-center mt-5'>
                <a href='' class='btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none'>More Facilities >>></a>
            </div>
        </div>
    </div>


    <!-- Testimonials  -->
    <h2 class='mt-5 pt-4 mb-4 text-center fw-bold h-font'>TESTIMONIALS</h2>

    <div class='container mt-5'>
        <div class='swiper swiper-testimonials'>
            <div class='swiper-wrapper mb-5'>

                <div class='swiper-slide bg-white mb-3 px-4'>
                    <div class='profile d-flex align-items-center p-4'>
                        <i class="bi bi-person-circle"></i>
                        <h6 class='m-0 ms-2'>Random user1</h6>
                    </div>
                    <p>
                    I had an amazing experience at The Fern! The rooms were luxurious, the staff was incredibly friendly, and the food was exceptional. I highly recommend it to anyone looking for a perfect getaway.
                    </p>
                    <div class='rating mb-3'>
                        <i class='bi bi-star-fill text-warning'></i>
                        <i class='bi bi-star-fill text-warning'></i>
                        <i class='bi bi-star-fill text-warning'></i>
                        <i class='bi bi-star-fill text-warning'></i>
                    </div>
                </div>

                <div class='swiper-slide bg-white mb-3 px-4'>
                    <div class='profile d-flex align-items-center p-4'>
                        <i class="bi bi-person-circle"></i>
                        <h6 class='m-0 ms-2'>Random user1</h6>
                    </div>
                    <p>
                    I stayed at The Fern for a business trip, and I was impressed by the excellent amenities and top-notch service. The hotel offered a peaceful environment and great facilities, making it ideal for both work and relaxation.
                    </p>
                    <div class='rating mb-3'>
                        <i class='bi bi-star-fill text-warning'></i>
                        <i class='bi bi-star-fill text-warning'></i>
                        <i class='bi bi-star-fill text-warning'></i>
                        <i class='bi bi-star-fill text-warning'></i>
                    </div>
                </div>

                <div class='swiper-slide bg-white mb-3 px-4'>
                    <div class='profile d-flex align-items-center p-4'>
                        <i class="bi bi-person-circle"></i>
                        <h6 class='m-0'>Random user1</h6>
                    </div>
                    <p>
                    From the moment I arrived, I felt welcomed at The Fern. The staff went out of their way to ensure my comfort, and the rooms were beautifully designed. I'll definitely be coming back for my next stay!
                    </p>
                    <div class='rating mb-3'>
                        <i class='bi bi-star-fill text-warning'></i>
                        <i class='bi bi-star-fill text-warning'></i>
                        <i class='bi bi-star-fill text-warning'></i>
                        <i class='bi bi-star-fill text-warning'></i>
                    </div>
                </div>

                <div class='swiper-slide bg-white mb-3 px-4'>
                    <div class='profile d-flex align-items-center p-4'>
                        <i class="bi bi-person-circle"></i>
                        <h6 class='m-0 ms-2'>Random user1</h6>
                    </div>
                    <p>
                    The Fern made my stay unforgettable! The cleanliness, attention to detail, and level of service were outstanding. I've stayed at many hotels, but none have made me feel as relaxed and pampered as this one
                    </p>
                    <div class='rating mb-3'>
                        <i class='bi bi-star-fill text-warning'></i>
                        <i class='bi bi-star-fill text-warning'></i>
                        <i class='bi bi-star-fill text-warning'></i>
                        <i class='bi bi-star-fill text-warning'></i>
                    </div>
                </div>
                <!-- Repeat other testimonial divs as needed -->
            </div>
            <div class='swiper-pagination'></div>
        </div>
        <div class='col-lg-12 text-center mt-5'>
            <a href='' class='btn btn-sm btn-outline-dark rounded-0 fw-bold shadow-none'>Know More >>></a>
        </div>
    </div>

    <!-- Reach us  -->
    <h2 class='mt-5 pt-4 mb-4 text-center fw-bold h-font'>REACH US</h2>

    <div class='container'>
        <div class='row'>
            <div class='col-lg-6 col-md-12'>
                <div class='mapouter'>
                    <div class='gmap_canvas'>
                    <iframe width='600' height='500' id='gmap_canvas'
                            src='https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1833.6717046966885!2d72.61491110226554!3d23.19415240827781!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395c2bb20766a565%3A0x723f438ea4fc0821!2sThe%20Fern%20Residency%2C%20Gandhinagar!5e0!3m2!1sen!2sin!4v1737796188785!5m2!1sen!2sin'
                            frameborder='0' scrolling='no' marginheight='0' marginwidth='0'></iframe>
                    </div>
                </div>
            </div>
            <div class='col-lg-6 col-md-12'>
                <div class='bg-white shadow-sm p-4'>
                    <h4 class='fw-bold'>Contact us</h4>
                    <p class='mt-3'>Get in touch with us at The Fern for inquiries, bookings, or assistance - we're here to help!</p>

                    <form action=''>

                        <div class='mb-3'>
                            <label for='exampleFormControlInput1' class='form-label'>Email</label>
                            <input type='email' class='form-control' id='exampleFormControlInput1'>
                        </div>
                        <div class='mb-3'>
                            <label for='exampleFormControlInput1' class='form-label'>Name</label>
                            <input type='text' class='form-control' id='exampleFormControlInput1'>
                        </div>
                        <div class='mb-3'>
                            <label for='exampleFormControlTextarea1' class='form-label'>Message</label>
                            <textarea class='form-control' id='exampleFormControlTextarea1' rows='3'></textarea>
                        </div>
                        <div class='text-end'>
                            <button type='submit' class='btn btn-sm custom-bg text-white'>Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <?php require('inc/footer.php') ?>

    <script src='https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js'></script>
    <script>
        var swiper = new Swiper(".swiper-container", {
            spaceBetween: 30,
            effect: "fade",
            loop: true,
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            }
        });

        var swiper = new Swiper(".swiper-testimonials", {
            effect: "coverflow",
            grabCursor: true,
            centeredSlides: true,
            slidesPerView: "auto",
            slidesPerView: "4",
            coverflowEffect: {
                rotate: 50,
                stretch: 0,
                depth: 100,
                modifier: 1,
                slideShadows: false,
            },
            pagination: {
                el: ".swiper-pagination",
            },
            loop: true,
            autoplay: {
                delay: 2500,
                disableOnInteraction: false,
            },
            breakpoints: {
                320: {
                    slidesPerView: 1,
                },
                640: {
                    slidesPerView: 2,
                },
                768: {
                    slidesPerView: 3,
                },
                1024: {
                    slidesPerView: 3,
                },
            },
        });
        </script>
</body>

</html>
