@extends('layouts.app')

@section('title', 'Depósito')

@section('content')
    <div class="container theme-text">
        <div class="card shadow mb-4 theme-bg theme-text">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <x-account-summary :balance="$balance" />

                    <a href="{{ route('account.statement') }}" class="btn theme-btn">
                        Ver Extrato
                    </a>
                </div>

                <h4 class="mb-3">Realizar Depósito</h4>

                <div id="deposit-alert" class="alert d-none"></div>

             <form id="deposit-form" action="{{ route('deposit.process') }}" method="POST">

                    @csrf
                    <div class="mb-3">
                        <label for="amount" class="form-label">Valor</label>
                        <input type="number" step="0.01" name="amount" id="amount" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição (opcional)</label>
                        <input type="text" name="description" id="description" class="form-control">
                    </div>

                    <div class="text-lg-end mt-4 pt-2">
                        <button type="submit" class="btn theme-btn">Depositar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).ready(function() {
                const alert = $('#deposit-alert');

                $('#deposit-form').on('submit', function(e) {
                    e.preventDefault();

                    const amount = parseFloat($('#amount').val());
                    const description = $('#description').val().trim();

                    alert.addClass('d-none').removeClass('alert-success alert-danger');

                    if (isNaN(amount) || amount <= 0) {
                        showAlert('Informe um valor válido para depósito.');
                        return;
                    }

                    $.ajax({
                        url: "{{ route('deposit.process') }}",
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            _token: '{{ csrf_token() }}',
                            amount: amount,
                            description: description,
                        }),
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sucesso',
                                    text: response.message ||
                                        'Depósito realizado.',
                                }).then(() => {
                                    window.location.href =
                                        "{{ route('account.statement') }}";
                                });
                            } else {
                                Swal.fire('Erro', response.message ||
                                    'Falha no depósito.', 'error');
                            }
                        },
                        error: function(xhr) {
                            const json = xhr.responseJSON;
                            const msg = json?.message || 'Erro na requisição.';
                            Swal.fire('Erro', msg, 'error');
                        }
                    });
                });

                function showAlert(message, type = 'danger') {
                    alert.removeClass('d-none alert-success alert-danger alert-warning');
                    alert.addClass(`alert alert-${type}`);
                    alert.text(message);
                }
            });
        });
    </script>
@endsection
