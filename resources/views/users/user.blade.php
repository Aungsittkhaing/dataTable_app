<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>User Lists</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('datatables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bootstrap-icons/font/bootstrap-icons.css') }}">
</head>

<body>
    <div class="container py-5">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">DataTable implementation at Laravel</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped datatable">
                        <thead>
                            <tr>
                                <th>Sl.</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Created_at</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        {{-- <tbody>
                            @forelse ($users as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->phone_number }}</td>
                                    <td>{{ $user->created_at }}</td>
                                @empty
                                    <td colspan="4">
                                        <div class="alert alert-danger">No user found</div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody> --}}
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ asset('js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('jquery.min.js') }}"></script>
    <script src="{{ asset('datatables.min.js') }}"></script>
    <script>
        //initializing datatables
        $(document).ready(function() {
            const table = $('.datatable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route('users.index') }}'
                },
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'email',
                        name: 'email'
                    },
                    {
                        data: 'phone_number',
                        name: 'phone_number'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
            //delete function
            $('table').on('click', '.delete-user', function() {
                const userId = $(this).data('id');
                if (userId) {
                    if (confirm('Are you sure you want to delete this user?')) {
                        $.ajax({
                            url: `{{ url('users/delete') }}/${userId}`,
                            method: 'DELETE',
                            data: {
                                _token: '{{ csrf_token() }}'
                            },
                            success: function(response) {
                                if (response.status == 'success') {
                                    table.ajax.reload(null, false); //callback, boolean
                                } else {
                                    alert(response.message);
                                }
                            },
                            error: function(e) {
                                alert('Something went wrong');
                            }
                        });
                    }
                }
            });

            const editTableColumns = [1, 2, 3];
            let currentEditTableRow = null;

            //edit function
            $('table').on('click', '.edit-user', function() {
                const userId = $(this).data('id');
                const currentRow = $(this).closest('tr');

                //check if currentRow is editable, this will reset the other rows editTable
                if (currentEditTableRow && currentEditTableRow !== currentRow) {
                    resetEditTableRow(currentEditTableRow);
                }
                //call makEditRow function
                makeEditRow(currentRow);

                //updating the current row to editTable
                currentEditTableRow = currentRow;

                //appending action buttons in the last column
                currentRow.find('td:last').html(
                    `<div class="d-flex gap-2">
                    <button class="btn btn-primary btn-sm btn-update" data-id="${userId}">Update</button>
                    <button data-id="' . $user->id . '" class="btn btn-dark btn-sm delete-user">
                    <i class="bi bi-trash2"></i></button>
                    </div>
                    `
                );
            });

            //build makeEditRow function
            function makeEditRow(currentRow) {
                currentRow.find('td').each(function(index) {
                    const currentCell = $(this);
                    const currentText = currentCell.text().trim();

                    if (editTableColumns.includes(index)) {
                        currentCell.html(
                            `<input type="text" class="form-control editable-input" value="${currentText}"/>`
                        );
                    }
                });
            }
            //reset current Row EditTable function
            function resetEditTableRow() {
                currentEditTableRow.find('td').each(function(index) {
                    const currentCell = $(this);

                    if (editTableColumns.includes(index)) {
                        const currentValue = currentCell.find('input').val();
                        currentCell.html(`${currentValue}`);
                    }
                });
                const userId = currentEditTableRow.find('.btn-update').data('id');
                currentEditTableRow.find('td:last').html(
                    `<div class="d-flex gap-2">
                    <button class="btn btn-success btn-edit btn-sm" data-id="${userId}">
                    <i class="bi bi-pencil-square"></i></button>
                    <button data-id="${userId}" class="btn btn-dark btn-sm delete-user">
                    <i class="bi bi-trash2"></i></button>
                    </div>
                    `
                );
            }
            //update function
            $('table').on('click', '.btn-update', function() {
                const userId = $(this).data('id');
                const currentRow = $(this).closest('tr');
                const updatedUserData = {};

                currentRow.find('td').each(function(index) {
                    if (editTableColumns.includes(index)) {
                        const inputValue = $(this).find('input').val();

                        if (index === 1)
                            updatedUserData.name = inputValue;
                        if (index === 2)
                            updatedUserData.email = inputValue;
                        if (index === 3)
                            updatedUserData.phoneNumber = inputValue;
                    }
                });

                // console.log(updatedUserData);
                // return;
                $.ajax({
                    url: '{{ route('users.update') }}',
                    method: 'PUT',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: userId,
                        name: updatedUserData.name,
                        email: updatedUserData.email,
                        phone_number: updatedUserData.phoneNumber
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            table.ajax.reload(null, false); //callback, boolean
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(e) {
                        alert(e.message);
                    }
                });
            })
        });
    </script>
</body>

</html>
