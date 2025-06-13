@extends('layouts.app')

@section('title', 'Transferência')

@section('content')
    <div class="container theme-bg theme-text">
        <div class="card shadow mb-4 theme-bg theme-text">
            <div class="card-body m-4 theme-bg theme-text">

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <x-account-summary :balance="$balance" />

                    <a href="{{ route('account.statement') }}" class="btn theme-btn">
                        Ver Extrato
                    </a>
                </div>

                <h4 class="mb-4">Transferência entre Contas</h4>

                <div id="transfer-alert" class="alert d-none"></div>

                <form id="transfer-form" method="POST" action="{{ route('transfer.process') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="to_user" class="form-label">Destinatário (ID ou E-mail)</label>
                        <input type="text" class="form-control" id="to_user" name="to_user" required>
                    </div>

                    <div class="mb-3">
                        <label for="amount" class="form-label">Valor da Transferência</label>
                        <input type="number" step="0.01" min="0.01" class="form-control" id="amount"
                            name="amount" required>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição (opcional)</label>
                        <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                    </div>

                    <div class="text-lg-end mt-4 pt-2">
                        <button type="submit" class="btn theme-btn">Transferir</button>
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
                const alert = $('#transfer-alert');

                $('#transfer-form').on('submit', function(e) {
                    e.preventDefault();

                    const toUser = $('#to_user').val().trim();
                    const amount = parseFloat($('#amount').val());
                    const description = $('#description').val();

                    alert.addClass('d-none').removeClass('alert-success alert-danger');

                    if (!toUser || isNaN(amount) || amount <= 0) {
                        showAlert('Preencha todos os campos corretamente.');
                        return;
                    }

                    $.ajax({
                        url: "{{ route('transfer.process') }}",
                        method: 'POST',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            _token: '{{ csrf_token() }}',
                            to_user: toUser,
                            amount: amount,
                            description: description,
                        }),
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Sucesso',
                                    text: response.message ||
                                        'Transferência realizada.',
                                }).then(() => {
                                    window.location.href =
                                        "{{ route('account.statement') }}";
                                });
                            } else {
                                Swal.fire('Erro', response.message ||
                                    'Falha na transferência.',
                                    'error');
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
