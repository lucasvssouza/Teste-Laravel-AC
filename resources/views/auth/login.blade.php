@extends('layouts.app')

@section('title', 'Login')

@section('content')
    <div class="row justify-content-center w-100">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card shadow theme-bg theme-text">
                <div class="card-body">
                    <h2 class="mb-4 text-center mt-4 theme-text">Login</h2>

                    <div id="login-alert" class="alert mx-4 d-none"></div>

                    <form id="login-form">
                        @csrf

                        <div class="mb-3 mx-4">
                            <label for="email" class="form-label theme-text">E-mail</label>
                            <input type="email" id="email" name="email" class="form-control" required autofocus>
                        </div>

                        <div class="mb-3 mx-4">
                            <label for="password" class="form-label theme-text">Senha</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>

                        <div class="mb-3 d-flex justify-content-end mx-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label theme-text" for="remember">Lembrar-me</label>
                            </div>
                        </div>

                        <div class="text-lg-end mt-4 pt-2 mx-4">
                            <button type="submit" class="btn theme-btn btn-lg">
                                Login
                            </button>
                        </div>

                        <div class="text-center mt-3 mb-4">
                            <p class="theme-text">
                                Não tem uma conta?
                                <a href="{{ route('register.form') }}" class="theme-text">Cadastre-se</a>
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
            $(document).ready(function() {
                const alert = $('#login-alert');
                $('#login-form').on('submit', function(e) {
                    e.preventDefault();

                    const email = $('#email').val().trim();
                    const password = $('#password').val();
                    const remember = $('#remember').is(':checked');

                    if (email === '' || password === '') {
                        showAlert('Preencha todos os campos.');
                        return;
                    }

                    $.ajax({
                        url: "{{ route('login.submit') }}",
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            email: email,
                            password: password,
                            remember: remember,
                            _token: '{{ csrf_token() }}'
                        }),
                        success: function(response) {
                            if (response.status === 'success') {
                                window.location.href = response.redirect_to ||
                                    "{{ route('account.statement') }}";
                            } else {
                                Swal.fire('Erro', response.message || 'Falha no login.',
                                    'error');
                            }
                        },
                        error: function(xhr) {
                            const json = xhr.responseJSON;
                            const msg = json?.message || 'Erro na requisição.';
                            showAlert(msg);
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
