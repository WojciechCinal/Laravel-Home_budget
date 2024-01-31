@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div id="messages">@include('layouts.messages')</div>
                <div class="card">
                    <div class="card-header">
                        <h3 class="mt-2">Lista użytkowników</h3>
                    </div>

                    <div class="card-body">
                        <table class="table table-striped align-middle table-bordered">
                            <thead class="text-center">
                                <tr class="table-dark">
                                    <th scope="col">ID</th>
                                    <th scope="col">Nazwa użytkownika</th>
                                    <th scope="col">E-mail</th>
                                    <th scope="col">Rola</th>
                                    <th scope="col">Edycja uprawnień</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                    <tr>
                                        <td>{{ $user->id_user }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td id="role-{{ $user->id_user }}">{{ $user->role->name_role }}</td>
                                        <td class="text-center">
                                            @if ($user->id_user == Auth::user()->id_user)
                                                <button type="button" class="btn btn-primary" disabled>
                                                    Zmień rolę
                                                </button>
                                            @else
                                                <button type="button" class="btn btn-primary edit-role"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#changeRoleModal{{ $user->id_user }}">
                                                    Zmień rolę
                                                </button>
                                            @endif

                                        </td>
                                    </tr>

                                    <div class="modal fade" id="changeRoleModal{{ $user->id_user }}" tabindex="-1"
                                        aria-labelledby="changeRoleModalLabel{{ $user->id_user }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="changeRoleModalLabel{{ $user->id_user }}">
                                                        Zmień uprawnienia</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <h4>{{ $user->email }}</h4>
                                                    <label for="newRole{{ $user->id_user }}">Rola</label>
                                                    <select class="form-select" id="newRole{{ $user->id_user }}">
                                                        @foreach ($roles as $role)
                                                            <option value="{{ $role->id_role }}"
                                                                {{ $role->id_role == $user->id_role ? 'selected' : '' }}>
                                                                {{ $role->name_role }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Zamknij</button>
                                                    <button type="button" class="btn btn-primary changeRoleBtn"
                                                        data-userid="{{ $user->id_user }}"
                                                        data-useremail="{{ $user->email }}">Zapisz zmiany
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.querySelectorAll('.changeRoleBtn').forEach(function(button) {
            button.addEventListener('click', function() {
                var newRole = document.getElementById('newRole' + this.getAttribute('data-userid')).value;
                var userId = this.getAttribute('data-userid');
                var responseEmail = this.getAttribute('data-useremail');

                $.ajax({
                    type: 'POST',
                    url: '/admin/changeRole/' + userId,
                    data: {
                        newRole: newRole,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        $('#changeRoleModal' + userId).modal('hide');

                        var newRoleName = response.newRoleName;

                        $('#role-' + userId).text(newRoleName);

                        $('#messages').html(
                            '<div class="alert alert-success alert-dismissible fade show" role="alert">' +
                            '<strong><i class="bi bi-check-circle-fill" style="font-size: 1rem;"></i> Rola użytkownika ' +
                            responseEmail + ' zaktualizowana pomyślnie. </strong>' +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>'
                        );
                    },
                    error: function(xhr) {
                        console.log(xhr.responseText);
                        $('#messages').html(
                            '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                            '<strong><i class="bi bi-exclamation-triangle-fill" style="font-size: 1rem;"></i> Wystąpił błąd przy zmianie roli użytkownika! </strong>' +
                            '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                            '</div>'
                        );
                    }
                });
            });
        });
    </script>
@endsection
