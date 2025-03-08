<?php
require('inc/essentials.php');
adminLogin();
require('inc/db_config.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Rooms</title>
    <?php require('inc/links.php') ?>
</head>

<body>
    <?php require('inc/header.php'); ?>

    <div class="container-fluid" id="main-content">
        <div class="row">
            <div class="col-lg-10 ms-auto p-4 overflow-hidden">
                <h3 class="mb-4 h-font">ROOMS</h3>

                <div class="card boder-0 shadow-sm mb-4">
                    <div class="card-body">

                        <div class="text-end mb-4">
                            <button type="button" class="btn btn-dark shadow-none btn-sm" data-bs-toggle="modal"
                                data-bs-target="#add-room">
                                <i class="bi bi-plus-square"></i> Add
                            </button>
                        </div>

                        <div class="table-responsive-lg" style="height: 450px; overflow-y: scroll;">
                            <table class="table table-hover border text-center">
                                <thead>
                                    <tr class="bg-dark text-light">
                                        <th scope="col">#</th>
                                        <th scope="col">Name</th>
                                        <th scope="col">Guest</th>
                                        <th scope="col">Price</th>
                                        <th scope="col">Status</th>
                                        <th scope="col">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="room-data">
                                </tbody>
                            </table>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Room Model -->
    <div class="modal fade" id="add-room" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog model-lg">
            <form id="add_room_form" autocomplete="off">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title" id="staticBackdropLabel">Add Room</h1>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class=" col-md-6 mb-3">
                                <label for="form-label fw-bold">Name</label>
                                <input type="text" name="name" class="form-control shadow-none" required>
                            </div>

                            <div class=" col-md-6 mb-3">
                                <label for="form-label fw-bold">Price</label>
                                <input type="number" min="1" name="price" class="form-control shadow-none" required>
                            </div>

                            <div class=" col-md-6 mb-3">
                                <label for="form-label fw-bold">Adult (Max.)</label>
                                <input type="number" min="1" name="adult" class="form-control shadow-none" required>
                            </div>

                            <div class=" col-md-6 mb-3">
                                <label for="form-label fw-bold">Children (Max.)</label>
                                <input type="number" min="1" name="children" class="form-control shadow-none" required>
                            </div>

                            <!-- <div class="col-12 mb-3">
                                <label for="form-label fw-bold">Features</label>
                                <div class="row">
                                    <div class="col-md-3 mb-1">
                                        <label>
                                            <input type="checkbox" name="features" value="1"
                                                class="form-check-input shadow-none" required>
                                            Balcony
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="form-label fw-bold">Facilities</label>
                                <div class="row">
                                    <div class="col-md-3 mb-1">
                                        <label>
                                            <input type="checkbox" name="features" value="1"
                                                class="form-check-input shadow-none">
                                            AC
                                        </label>
                                    </div>
                                </div>
                            </div> -->

                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Description</label>
                                <textarea name="description" class="form-control shadow-none" rows="10"
                                    required></textarea>
                            </div>

                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn text-secondary shadow-none"
                            data-bs-dismiss="modal">CANCEL</button>
                        <button type="submit" class="btn custom-bg text-white shadow-none">SAVE</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- edit Room Model -->
    <div class="modal fade" id="edit-room" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog model-lg">
            <form id="edit_room_form" autocomplete="off">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title" id="staticBackdropLabel">Edit Room</h1>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class=" col-md-6 mb-3">
                                <label for="form-label fw-bold">Name</label>
                                <input type="text" name="name" class="form-control shadow-none" required>
                            </div>

                            <div class=" col-md-6 mb-3">
                                <label for="form-label fw-bold">Price</label>
                                <input type="number" min="1" name="price" class="form-control shadow-none" required>
                            </div>

                            <div class=" col-md-6 mb-3">
                                <label for="form-label fw-bold">Adult (Max.)</label>
                                <input type="number" min="1" name="adult" class="form-control shadow-none" required>
                            </div>

                            <div class=" col-md-6 mb-3">
                                <label for="form-label fw-bold">Children (Max.)</label>
                                <input type="number" min="1" name="children" class="form-control shadow-none" required>
                            </div>

                            <!-- <div class="col-12 mb-3">
                                <label for="form-label fw-bold">Features</label>
                                <div class="row">
                                    <div class="col-md-3 mb-1">
                                        <label>
                                            <input type="checkbox" name="features" value="1"
                                                class="form-check-input shadow-none" required>
                                            Balcony
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="form-label fw-bold">Facilities</label>
                                <div class="row">
                                    <div class="col-md-3 mb-1">
                                        <label>
                                            <input type="checkbox" name="features" value="1"
                                                class="form-check-input shadow-none">
                                            AC
                                        </label>
                                    </div>
                                </div>
                            </div> -->

                            <div class="col-12 mb-3">
                                <label class="form-label fw-bold">Description</label>
                                <textarea name="description" class="form-control shadow-none" rows="10"
                                    required></textarea>
                            </div>

                            <input type="hidden" name="room_id">
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="reset" class="btn text-secondary shadow-none"
                            data-bs-dismiss="modal">CANCEL</button>
                        <button type="submit" class="btn custom-bg text-white shadow-none">SAVE</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <!-- Room images Model -->
    <div class="modal fade" id="room-images" data-bs-backdrop="static" data-bs-keyboard="true" tabindex="-1"
        aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form id="add_image_form">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title" id="staticBackdropLabel">Room name</h1>
                        <button type="reset" class="btn-close shadow-none" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="image-alert"></div>
                        <div class="border-bottom border-3 pb-3 mb-3">
                            <form class="add_image_form">
                                <label class="form-label fw-bold">Add Image</label>
                                <input type="file" name="image" accept=" .jpg, .png, .webp, .jpeg"
                                    class="form-control shadow-none mb-3" required>
                                <button type="reset" class="btn text-secondary shadow-none"
                                    data-bs-dismiss="modal">CANCEL</button>
                                <button type="submit" class="btn custom-bg text-white shadow-none">ADD</button>
                                <input type="hidden" name="room_id">
                            </form>
                        </div>

                        <div class="table-responsive-lg" style="height: 350px; overflow-y: scroll;">
                            <table class="table table-hover border text-center">
                                <thead>
                                    <tr class="bg-dark text-light sticky-top">
                                        <th scope="col">Image</th>
                                        <th scope="col">Thumb</th>
                                        <th scope="col">Delete</th>
                                    </tr>
                                </thead>
                                <tbody id="room-image-data">
                                </tbody>
                            </table>
                        </div>

                    </div>
            </form>
        </div>
    </div>

    <?php require('inc/scripts.php') ?>

    <script src="scripts/rooms.js"></script>
</body>

</html>