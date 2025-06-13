@extends('layouts.app')

@section('title', 'Cadastro')

@section('content')
    <div class="row justify-content-center w-100">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card shadow theme-bg theme-text">
                <div class="card-body">
                    <h2 class="mb-4 text-center mt-4 theme-text">Cadastro</h2>

                    <div id="register-alert" class="alert mx-4 d-none"></div>

                    <form id="register-form">
                        @csrf

                        <div class="mb-3 mx-4">
                            <label for="name" class="form-label theme-text">Nome</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>

                        <div class="mb-3 mx-4">
                            <label for="email" class="form-label theme-text">E-mail</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>

                        <div class="mb-3 mx-4">
                            <label for="password" class="form-label theme-text">Senha</label>
                            <input type="password" id="password" name="password" class="form-control" required minlength="6">
                            <small class="form-text theme-text">A senha deve ter pelo menos 6 caracteres.</small>
                        </div>

                        <div class="mb-3 mx-4">
                            <label for="password_confirmation" class="form-label theme-text">Confirmar Senha</label>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                        </div>

                        <div class="text-lg-end mt-4 pt-2 mx-4">
                            <button type="submit" class="btn theme-btn btn-lg">Cadastrar</button>
                        </div>

                        <div class="text-center mt-3 mb-4">
                            <p class="theme-text">
                                Já tem uma conta?
                                <a href="{{ route('login') }}" class="theme-text">Fazer login</a>
                            </p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const alert = $('#register-alert');
            $(document).ready(function() {
                $('#register-form').on('submit', function(e) {
                    e.preventDefault();

                    const name = $('#name').val().trim();
                    const email = $('#email').val().trim();
                    const password = $('#password').val();
                    const confirm = $('#password_confirmation').val();

                    if (password.length < 6) {
                        showAlert('A senha deve ter pelo menos 6 caracteres.');
                        return;
                    }

                    if (password !== confirm) {
                        showAlert('As senhas não coincidem.');
                        return;
                    }

                    $.ajax({
                        url: "{{ route('register.submit') }}",
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            name: name,
                            email: email,
                            password: password,
                            password_confirmation: confirm,
                            _token: '{{ csrf_token() }}'
                        }),
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Cadastro realizado!',
                                    text: 'Você será redirecionado para o login.',
                                    timer: 2000,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.location.href =
                                        "{{ route('login') }}";
                                });
                            } else {
                                Swal.fire('Erro', response.message ||
                                    'Erro ao cadastrar.',
                                    'error');
                            }
                        },
                        error: function(response) {
                            const json = response.responseJSON;
                            const msg = json?.message || 'Erro na requisição.';
                            Swal.fire('Erro', msg, 'error');
                        }
                    });
                });

                function showAlert(message, type = 'danger') {
                    alert.removeClass('d-none alert-success alert-warning alert-danger alert-info');
                    alert.addClass(`alert-${type}`);
                    alert.text(message);
                }
            });
        })
    </script>
@endsection
