let add_room_form = document.getElementById('add_room_form');

add_room_form.addEventListener('submit', function (e) {
    e.preventDefault();
    add_room();
})

let edit_room_form = document.getElementById('edit_room_form');

edit_room_form.addEventListener('submit', function (e) {
    e.preventDefault();
    submit_edit_room();
})


function add_room() {
    const name = add_room_form.elements['name'].value;
    const price = add_room_form.elements['price'].value;
    const adult = add_room_form.elements['adult'].value;
    const children = add_room_form.elements['children'].value;
    const description = add_room_form.elements['description'].value;

    const params = new URLSearchParams();

    params.append('add_room', '');
    params.append('name', name);
    params.append('price', price);
    params.append('adult', adult);
    params.append('children', children);
    params.append('description', description);

    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/rooms.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
    xhr.onload = function () {
        if (this.responseText == 1) {
            alert('success', 'Room Added Successfully');
            add_room_form.reset();
            get_all_rooms();
            let modalElement = document.getElementById('add-room');
            let modal = bootstrap.Modal.getInstance(modalElement);
            if (!modal) {
                modal = new bootstrap.Modal(modalElement);
            }
            modal.hide();
        } else {
            alert('error', 'Error adding room');
        }
    }; xhr.send(params.toString());
}


function submit_edit_room() {
    const room_id = edit_room_form.elements['room_id'].value;
    const name = edit_room_form.elements['name'].value;
    const price = edit_room_form.elements['price'].value;
    const adult = edit_room_form.elements['adult'].value;
    const children = edit_room_form.elements['children'].value;
    const description = edit_room_form.elements['description'].value;

    const params = new URLSearchParams();
    params.append('edit_room', '');
    params.append('room_id', room_id);
    params.append('name', name);
    params.append('price', price);
    params.append('adult', adult);
    params.append('children', children);
    params.append('description', description);

    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/rooms.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {

        if (this.responseText == 1) {
            alert('success', 'Room data edited!');
            edit_room_form.reset();
            get_all_rooms();
            let modalElement = document.getElementById('edit-room');
            let modal = bootstrap.Modal.getInstance(modalElement);
            if (!modal) {
                modal = new bootstrap.Modal(modalElement);
            }
            modal.hide();
        }
    };

    xhr.send(params.toString());
}


function get_all_rooms() {
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/rooms.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        document.getElementById('room-data').innerHTML = this.responseText;
    }
    xhr.send('get_all_rooms');
}


function edit_details(id) {
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/rooms.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        let data = JSON.parse(this.responseText);

        edit_room_form.elements['name'].value = data.name;
        edit_room_form.elements['price'].value = data.price;
        edit_room_form.elements['adult'].value = data.adult;
        edit_room_form.elements['children'].value = data.children;
        edit_room_form.elements['description'].value = data.description;
        edit_room_form.elements['room_id'].value = data.id;
    }

    xhr.send('get_room=' + id);
}


function toggle_status(id, val) {
    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/rooms.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        if (this.responseText == 1) {
            alert('success', 'status toggled!');
            get_all_rooms();
        } else {
            alert('success', 'server down!');
        }
    }
    xhr.send('toggle_status=' + id + '&value=' + val);
}


let add_image_form = document.getElementById('add_image_form');

add_image_form.addEventListener('submit', function (e) {
    e.preventDefault();
    add_image();
})

function add_image() {
    let data = new FormData();
    data.append('image', add_image_form.elements['image'].files[0]);
    data.append('room_id', add_image_form.elements['room_id'].value);
    data.append('add_image', '');

    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/rooms.php', true);

    xhr.onload = function () {
        if (this.responseText == 'inv_img') {
            alert('error', 'Only JPG, WEBP or PNG images are allowed!', 'image-alert');
        }
        else if (this.responseText == 'inv_size') {
            alert('error', 'Images should be less than 2MB!', 'image-alert');
        }
        else if (this.responseText == 'upd_failed') {
            alert('error', 'Image upload failed. Server Down!', 'image-alert');
        }
        else {
            alert('success', 'New Image added!', 'image-alert');
            room_images(add_image_form.elements['room_id'].value, document.querySelector('#room-images .modal-title').innerText)
            add_image_form.reset();
        }
    }
    xhr.send(data);
}

function room_images(id, rname) {
    document.querySelector('#room-images .modal-title').innerText = rname;
    add_image_form.elements['room_id'].value = id;
    add_image_form.elements['image'].value = '';

    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/rooms.php', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    xhr.onload = function () {
        document.getElementById('room-image-data').innerHTML = this.responseText;
    };

    // Correctly format the POST data
    xhr.send('get_room_images=' + id);
}

function rem_image(img_id, room_id) {
    let data = new FormData();
    data.append('image_id', img_id);
    data.append('room_id', room_id);
    data.append('rem_image', '');

    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/rooms.php', true);

    xhr.onload = function () {
        if (this.responseText == 1) {
            alert('success', '  Image added!', 'image-alert');
            room_images(room_id, document.querySelector('#room-images .modal-title').innerText)
        } else {
            alert('error', 'Image removal failed!', 'image-alert');
        }
    }
    xhr.send(data);

}

function thumb_image(img_id, room_id) {
    let data = new FormData();
    data.append('image_id', img_id);
    data.append('room_id', room_id);
    data.append('thumb_image', '');

    let xhr = new XMLHttpRequest();
    xhr.open('POST', 'ajax/rooms.php', true);

    xhr.onload = function () {
        if (this.responseText == 1) {
            alert('success', '  Image thumbnail change!', 'image-alert');
            room_images(room_id, document.querySelector('#room-images .modal-title').innerText)
        } else {
            alert('error', 'Thumbnail update failed!', 'image-alert');
        }
    }
    xhr.send(data);

}

function remove_room(room_id) {
    if (confirm("Are you sure, you want to delete this room?")) {
        let data = new FormData();
        data.append('room_id', room_id);
        data.append('remove_image', '');
        let xhr = new XMLHttpRequest();
        xhr.open('POST', 'ajax/rooms.php', true);

        xhr.onload = function () {
            if (this.responseText == 1) {
                alert('success', 'Room Removed!');
                get_all_rooms();
            } else {
                alert('error', 'Room removal failed!', 'image-alert');
            }
        }
        xhr.send(data);
    }
}

window.onload = function () {
    get_all_rooms();
}