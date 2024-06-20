<!DOCTYPE html>
<html lang="en">

<head>
    {{-- @include('sweetalert::alert') --}}

    <title>Ajax CRUD</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" /> 
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body>

    <div class="container">
        <div class="col-sm-12">
            <h1 class="text-center">AJAX CRUD</h1>
        </div>
        <div class="row">
            <div class="col-md-12 mt-2 mb-3">
                <button type="button" class="btn btn-primary" id="add_user">
                    Add User
                </button>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead>
                            <tr>
                                <th>UserId</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="list_User">
                            @foreach ($users as $user)
                                <tr id="user{{ $user->id }}">
                                    <td>{{ $user->id }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info ml-1" id="edit_user"
                                            data-id="{{ $user->id }}">Edit</button>
                                        <button class="btn btn-sm btn-danger ml-1 delete_user" id="delete_user"
                                            data-id="{{ $user->id }}" data-confirm-delete="true">Delete</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>

    <!-- The Modal -->
    <div class="modal" id="myModal">
        <div class="modal-dialog">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title" id="modal_title">Add User</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <form id="form_user">
                    <div class="modal-body">
                        <input type="hidden" name="id" id="id" />
                        <div class="form-group mb-3">
                            <label for="name">Name:</label>
                            <input type="text" class="form-control" id="name_user" name="name"
                                placeholder="name....">
                        </div>
                        <div class="form-group mb-3">
                            <label for="email">Email:</label>
                            <input type="email" class="form-control" id="email" name="email"
                                placeholder="email....">
                        </div>
                        <div class="form-group mb-3">
                            <label for="phone">Phone:</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                                placeholder="phone....">
                        </div>
                    </div>
                </form>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-info" id="save_user">Submit</button>
                </div>

            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#add_user").on('click', function() {
                $("#myModal").modal('show');
                $("#form_user").trigger('reset');
                $("#modal_title").html("Add User");
            });

            $("#save_user").on('click', function(e) {
                e.preventDefault();
                var formData = {
                    id: $("#id").val(),
                    name: $("#name_user").val(),
                    email: $("#email").val(),
                    phone: $("#phone").val(),
                };

                var state = $("#id").val() ? 'update' : 'add';
                var ajaxurl = state == 'update' ? 'users/' + formData.id : 'users';

                $.ajax({
                    type: state == 'update' ? 'PUT' : 'POST',
                    url: ajaxurl,
                    data: formData,
                    dataType: 'json',
                    success: function(data) {
                        var user = '<tr id="user' + data.id + '">';
                        user += '<td>' + data.id + '</td>';
                        user += '<td>' + data.name + '</td>';
                        user += '<td>' + data.email + '</td>';
                        user += '<td>' + data.phone + '</td>';
                        user +=
                            '<td><button class="btn btn-sm btn-info ml-1" id="edit_user" data-id="' +
                            data.id + '">Edit</button>';
                        user +=
                            '<button class="btn btn-sm btn-danger ml-1" id="delete_user" data-id="' +
                            data.id + '">Delete</button></td></tr>';

                        if (state == "add") {
                            $("#list_User").append(user);
                            Swal.fire({
                                icon: 'success',
                                title: 'User added successfully',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            $("#user" + data.id).replaceWith(user);
                            Swal.fire({
                                icon: 'success',
                                title: 'User updated successfully',
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }

                        $("#form_user").trigger('reset');
                        $("#myModal").modal('hide');
                    },
                    error: function(data) {
                        console.log('Error:', data);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error adding/updating user',
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            });

            $("body").on('click', '#edit_user', function() {
                var id = $(this).data('id');

                $.get('users/' + id + '/edit', function(data) {
                    $("#modal_title").html("Edit User");
                    $("#id").val(data.id);
                    $("#name_user").val(data.name);
                    $("#email").val(data.email);
                    $("#phone").val(data.phone);
                    $("#myModal").modal('show');
                });
            });


        });
        $("body").on('click', '.delete_user', function() {
    var id = $(this).data('id');
    var row = $(this).closest('tr'); 

    $.ajax({
        type: "POST",
        url: "{{ route('users.destroy') }}",
        data: {
            _method: "DELETE",
            id: id
        },
        success: function(result) {
            console.log(result);
            toastr.success('User deleted successfully');
            row.remove();
            $.ajax({
                type: "GET",
                url: "{{ url('/') }}",
                success: function(data) {
                    $('#user-table tbody').html(data); 
                }
            });
        },
        error: function(xhr, status, error) {
            console.error(xhr.responseText);
            toastr.error('Error deleting user');
        }
    });
});
    </script>

</body>

</html>



