<?php

namespace App\View\Components;

use Illuminate\View\Component;

class AccountSummary extends Component
{
    public float $balance;

    public function __construct(float $balance)
    {
        $this->balance = $balance;
    }

    public function render()
    {
        return view('components.account-summary');
    }
}
