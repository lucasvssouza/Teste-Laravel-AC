@forelse ($transactions as $transaction)
    <li class="list-group-item d-flex justify-content-between align-items-center theme-bg theme-text transaction-item">
        <div>
            {{ $transaction->created_at->format('d/m/Y H:i') }} <br>

            <small class="theme-text">
                {{ $transaction->user_id === auth()->id() && $transaction->type == 1 ? 'Enviado para' : 'Recebido de' }}
                {{ $transaction->user_id === auth()->id() && $transaction->type == 1 ? $transaction->receiver->email ?? 'N/A' : $transaction->sender->email ?? 'N/A' }}
            </small>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span
                class="badge {{ $transaction->user_id === auth()->id() && $transaction->type == 1 ? 'bg-danger' : 'bg-success' }}">
                {{ $transaction->user_id === auth()->id() && $transaction->type == 1? '-' : '+' }} R$
                {{ number_format($transaction->amount, 2, ',', '.') }}
            </span>

            <button class="btn btn-sm btn-outline-danger cancel-transaction" data-id="{{ $transaction->id }}">
                Cancelar
            </button>
        </div>
    </li>
@empty
    <li class="list-group-item text-center theme-bg theme-text extrato-vazio">
        Extrato vazio
    </li>
@endforelse
