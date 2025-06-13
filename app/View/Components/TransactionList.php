<?php
    namespace App\View\Components;

    use Illuminate\View\Component;

    class TransactionList extends Component
    {
        public $transactions;

        public function __construct($transactions)
        {
            $this->transactions = $transactions;
        }

        public function render()
        {
            return view('components.transaction-list');
        }
    }
