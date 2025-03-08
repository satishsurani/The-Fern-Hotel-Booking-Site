<?php
session_start();
require('../admin/inc/db_config.php');
require('../admin/inc/essentials.php');

date_default_timezone_set("Asia/Calcutta");

if (isset($_GET['fetch_rooms'])) 
{
    $chk_avail = json_decode($_GET['chk_avail'],true);

    if($chk_avail['checkin'] != '' && $chk_avail['checkout'] != '')
    {
        $today_date = new DateTime(date('Y-m-d'));
        $checkin_date = new DateTime($chk_avail['checkin']);
        $checkout_date = new DateTime($chk_avail['checkout']);
    
        if ($checkin_date == $checkout_date) {
            echo "<h3 class='text-center text-danger'>Invalid dates</h3>";
            exit;
        } elseif ($checkout_date < $checkin_date) {
            echo "<h3 class='text-center text-danger'>Invalid dates</h3>";
            exit;
        } elseif ($checkin_date < $today_date) {
            echo "<h3 class='text-center text-danger'>Invalid dates</h3>";
            exit;
        }
    }

    $guests = json_decode($_GET['guests'],true);
    $adults = ($guests['adults']!='') ? $guests['adults'] : 0;
    $children = ($guests['children']!='') ? $guests['children'] : 0;


    $count_rooms = 0;
    $output = "";

    $room_res = select("SELECT * FROM `rooms` WHERE `adult`>=? AND `children`>=? AND `status`=?", [$adults,$children,1], 'iii');

    while ($room_data = mysqli_fetch_assoc($room_res)) 
    {
        $room_thumb = ROOMS_IMG_PATH . 'thumbnail.jpg';
        $thumb_q = mysqli_query($con, "SELECT * FROM `room_images` WHERE `room_id`='$room_data[id]' AND `thumb`='1'");

        if (mysqli_num_rows($thumb_q) > 0) {
            $thumb_res = mysqli_fetch_assoc($thumb_q);
            $room_thumb = ROOMS_IMG_PATH . $thumb_res['image'];
        }

        $login = 0;
        if (isset($_SESSION['login']) && $_SESSION['login'] == true) {
            $login = 1;
        }
        $book_btn = "<button class='btn btn-sm w-100 text-white custom-bg shadow-none mb-2' onclick='checkLoginToBook(" . $login . ", " . $room_data['id'] . ")'>Book Now</button>";

        $output .= "
            <div class='card mb-4 border-0 shadow'>
                <div class='row g-0 p-3 align-item-center'>

                    <div class='col-md-5 mb-lg-0 mb-md-0 mb-3'>
                        <img src='$room_thumb' class='img-fluid rounded'>
                    </div>

                    <div class='col-md-5 px-lg-3 px-md-3 px-0'>
                        <h5 class='mb-3'>$room_data[name]</h5>

                        <div class='features mb-3'>
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

                        <div class='facilities mb-3'>
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

                        <div class='guests'>
                            <h6 class='mb-1'>guests</h6>
                            <span class='badge rounded-pill bg-light text-dark text-wrap'>
                                $room_data[adult] Adults
                            </span>
                            <span class='badge rounded-pill bg-light text-dark text-wrap'>
                                $room_data[children] Childern
                            </span>
                        </div>

                    </div>

                    <div class='col-md-2 mt-lg-0 mt-md-0 mt-4 text-center'>
                        <h6 class='mb-4'> ₹$room_data[price] per night</h6>
                        $book_btn
                        <a href='room_details.php?id=$room_data[id]' class='btn btn-sm w-100 btn-outline-dark shadow-none'>More Details</a>
                    </div>
                </div>
            </div>";
            $count_rooms++;
    }
    if ($count_rooms > 0) {
        echo $output;
    } else {
        echo "<h3 class='text-center text-danger'>No Rooms to show!</h3>";
    }
}


?>