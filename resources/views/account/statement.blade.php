@extends('layouts.app')

@section('title', 'Extrato')


@section('content')
    <div class="container theme-text">
        <div class="card shadow mb-4 theme-bg theme-text">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <h4 class="theme-text">Extrato Bancário</h4>

                    <div>
                        <a href="{{ route('deposit.form') }}" class="btn theme-btn me-2">Depósito</a>

                        <a href="{{ route('transfer.form') }}" class="btn theme-btn">
                            Transferência
                        </a>
                    </div>
                </div>

                <x-account-summary :balance="$balance" />

                <h5 class="mb-3 theme-text">Extrato</h5>

                <ul class="list-group" id="transaction-list">
                </ul>

                <div id="loading" class="text-center my-3 d-none">
                    <div class="spinner-border text-primary" role="status"></div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            $(document).ready(function() {
                let page = 1;
                let loading = false;
                let endReached = false;
                let currentBalance= "{{ number_format($balance, 2, ',', '.') }}";

                loadTransactions();

                function loadTransactions() {
                    if (loading || endReached) return;

                    loading = true;
                    $('#loading').removeClass('d-none');

                    $.ajax({
                        url: '{{ route('transactions.fetch') }}',
                        type: 'GET',
                        data: {
                            page: page
                        },
                        success: function(response) {
                            if (response.html.includes(
                                    'extrato-vazio') && page == 1) {
                                $('#transaction-list').append(response.html);
                                const themeText = localStorage.getItem('theme-text') || 'light';
                                const themeBg = localStorage.getItem('theme-bg') || 'dark';
                                applyTheme(themeText, themeBg);
                                endReached = true;
                                return true;
                            }

                            if (response.html.trim() === '' || response.html.includes(
                                    'extrato-vazio')) {
                                endReached = true;
                                return true;
                            } else {
                                $('#transaction-list').append(response.html);
                                const themeText = localStorage.getItem('theme-text') || 'light';
                                const themeBg = localStorage.getItem('theme-bg') || 'dark';
                                applyTheme(themeText, themeBg);
                            }
                            makeEvent();
                            page++;
                        },
                        complete: function() {
                            loading = false;
                            $('#loading').addClass('d-none');
                        }
                    });
                }

                $(window).on('scroll', function() {
                    if ($(window).scrollTop() + $(window).height() >= $(document).height() - 300) {
                        loadTransactions();
                    }
                });

                function makeEvent() {
                    $('.cancel-transaction').off();
                    $('.cancel-transaction').on('click', function() {
                        const transactionId = $(this).data('id');
                        const transactionRow = $(this).closest('.transaction-item');

                        Swal.fire({
                            title: 'Cancelar transferência?',
                            text: "Você tem certeza que deseja cancelar esta transação?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#d33',
                            cancelButtonColor: '#3085d6',
                            confirmButtonText: 'Sim, cancelar',
                            cancelButtonText: 'Não'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                $.ajax({
                                    url: `/transacoes/${transactionId}/cancelar`,
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}'
                                    },
                                    success: function(response) {
                                        Swal.fire('Sucesso', response.message,
                                            'success');

                                        transactionRow.fadeOut(300, function() {
                                            $(this).remove();


                                            if ($('.transaction-item')
                                                .length === 0) {
                                                $('#transaction-list')
                                                    .append(`
                                      <li class="list-group-item text-center theme-bg theme-text extrato-vazio">Extrato vazio</li>
                                `);

                                                const themeText =
                                                    localStorage
                                                    .getItem(
                                                        'theme-text') ||
                                                    'light';
                                                const themeBg =
                                                    localStorage
                                                    .getItem(
                                                        'theme-bg') ||
                                                    'dark';
                                                applyTheme(themeText,
                                                    themeBg);
                                            }
                                        });


                                        if (response.new_balance !==
                                            undefined) {
                                            $('#current-balance')
                                                .removeClass(
                                                    'text-success text-danger')
                                                .addClass(response.new_balance <
                                                    0 ? 'text-danger' :
                                                    'text-success')
                                                .text('R$ ' + response
                                                    .new_balance);
                                            currentBalance = response
                                                .new_balance;
                                        }
                                    },
                                    error: function(xhr) {
                                        const msg = xhr.responseJSON?.message ||
                                            'Erro ao cancelar transação.';
                                        Swal.fire('Erro', msg, 'error');
                                    }
                                });
                            }
                        });
                    });
                }

                function observeBalanceChanges() {
                    setInterval(() => {
                        $.ajax({
                            url: '{{ route('account.balance') }}',
                            type: 'GET',
                            success: function(response) {
                                const newBalance = response.balance;

                                if (newBalance !== currentBalance) {
                                    currentBalance = newBalance;

                                    $('#current-balance')
                                        .removeClass('text-success text-danger')
                                        .addClass(newBalance.includes(
                                                '-') ? 'text-danger' :
                                            'text-success')
                                        .text('R$ ' + newBalance)

                                    page = 1;
                                    endReached = false;
                                    $('#transaction-list').empty();
                                    loadTransactions();
                                }
                            }
                        });
                    }, 10000);
                }

                observeBalanceChanges();
            });
        });
    </script>
@endsection
