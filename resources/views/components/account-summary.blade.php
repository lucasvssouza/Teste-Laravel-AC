<div class="mb-4">
    <strong>Email:</strong> {{ auth()->user()->email }}<br>
    <strong>Saldo atual:</strong>
    <span class="{{ $balance < 0 ? 'text-danger' : 'text-success' }}" id="current-balance">R$ {{ number_format($balance, 2, ',', '.') }}</span>
</div>
