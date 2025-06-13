<nav class="navbar navbar-expand-lg px-3 d-flex justify-content-between align-items-center theme-bg theme-text">
    <span class="navbar-brand mb-0 h1 theme-text">
        {{ config('app.name', 'Minha Aplicação') }}
    </span>

    <div class="d-flex gap-2 align-items-center">
        <button class="btn theme-toggle-btn theme-btn" id="theme-toggle">
            🌙 Tema Escuro
        </button>

        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn theme-btn">
                    🔓 Sair
                </button>
            </form>
        @endauth

    </div>
</nav>
