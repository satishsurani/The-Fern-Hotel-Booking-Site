<!-- Footer  -->
<div class="container-fluid bg-white mt-5">
    <div class="row">
        <div class="col-lg-4 p-4">
            <h3 class="h-font fw-bold fs-3 mb-2">FERN HOTEL</h3>
            <p>
            At The Fern, enjoy luxury, comfort, and exceptional service. Our modern rooms, top amenities, and serene atmosphere make it the perfect choice for business or leisure. Unwind with a fitness center, spa, and gourmet dining for an unforgettable stay.
            </p>
        </div>
        <div class="col-lg-4 p-4">
            <h5 class="mb-3 h-font">Links</h5>
            <a href="#" class="d-inline-block mb-2 text-dark text-decoration-none">Home</a><br>
            <a href="#" class="d-inline-block mb-2 text-dark text-decoration-none">Rooms</a><br>
            <a href="#" class="d-inline-block mb-2 text-dark text-decoration-none">Facilties</a><br>
            <a href="#" class="d-inline-block mb-2 text-dark text-decoration-none">Contact Us</a><br>
            <a href="#" class="d-inline-block mb-2 text-dark text-decoration-none">About</a><br>
        </div>
        <div class="col-lg-4 p-4">
            <h5 class="mb-3 h-font">Follow Us</h5>
            <a href="#" class="d-inline-block mb-3 text-dark text-decoration-none mb-2"><i
                    class="bi bi-twitter me-1"></i>Twitter</a>
            <br>
            <a href="#" class="d-inline-block mb-3 text-dark text-decoration-none mb-2"><i
                    class="bi bi-facebook me-1"></i>Facebook</a>
            <br>
            <a href="#" class="d-inline-block mb-3 text-dark text-decoration-none"><i
                    class="bi bi-instagram me-1"></i>Instagram</a>
        </div>
    </div>
</div>
<h6 class="text-center bg-dark p-3 m-0 h-font">Designed and Developed by FERN HOTEL</h6>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>

    <script src='https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js'></script>

    <script>
    function showAlert(type, message) {
        // Ensure the alert type is valid (success or danger)
        const alertType = type === 'success' ? 'success' : 'danger';

        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${alertType} alert-dismissible fade show position-fixed top-0 end-0 m-3 shadow`;
        alertDiv.style.zIndex = "1055";
        alertDiv.innerHTML = `
            <strong>${alertType === 'success' ? 'Success' : 'Error'}:</strong> ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        document.body.appendChild(alertDiv);

        // Remove the alert after 3 seconds
        setTimeout(() => alertDiv.remove(), 3000);
    }

    // Registration Form Submission
    document.getElementById('register_form').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('ajax/register.php', {
            method: 'POST',
            body: formData,
        })
            .then(response => response.text())
            .then(data => {
                console.log('Register Response:', data);  // Log the response to debug

                // If response is 'success', handle success
                if (data.trim() === 'success') {
                    showAlert('success', 'Registration successful!');

                    // Close the modal
                    const modalElement = document.getElementById('registerModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    modalInstance.hide();

                    let fileurl = window.location.href.split('/').pop().split('?').shift();

                    if(fileurl == 'bus_details.php'){
                        window.location =window.location.href;
                    }else {
                        window.location = window.location.pathname;
                    }

                    // Reset the form
                    this.reset();
                } else {
                    // Handle any other response from the server
                    showAlert('danger', data);
                }
            })
            .catch(error => {
                // Handle any network or other unexpected errors
                console.error('Error during registration:', error);
                showAlert('danger', 'An error occurred. Please try again.');
            });
    });

    // Login Form Submission
    document.getElementById('login_form').addEventListener('submit', function (e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('ajax/login.php', {
            method: 'POST',
            body: formData,
        })
            .then(response => response.text())
            .then(data => {
                console.log('Login Response:', data);  // Log the response to debug

                // If response is 'success', handle success
                if (data.trim() === 'success') {
                    showAlert('success', 'Login successful!');

                    // Close the modal
                    const modalElement = document.getElementById('loginModal');
                    const modalInstance = bootstrap.Modal.getInstance(modalElement);
                    modalInstance.hide();

                    let fileurl = window.location.href.split('/').pop().split('?').shift();

                    if(fileurl == 'room_details.php'){
                        window.location =window.location.href;
                    }else {
                        window.location = window.location.pathname;
                    }


                    this.reset();
                } else {
                    showAlert('danger', data);
                }
            })
            .catch(error => {
                showAlert('danger', 'An error occurred. Please try again.');
            });
    });

    function checkLoginToBook(status, room_id) 
    {
        if (status) {
            window.location.href = 'confirm_booking.php?id=' + room_id;
        } else {
            showAlert('danger', 'Please log in to book a room!');
        }
    }

</script>
    