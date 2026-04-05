<?php

namespace App\Livewire\Lab;

use Livewire\Component;

class SecurityReference extends Component
{
    public string $activeTab   = 'helpers';
    public string $searchQuery = '';

    public function setTab(string $tab): void
    {
        $this->activeTab   = $tab;
        $this->searchQuery = '';
    }

    public function render()
    {
        return view('livewire.lab.security-reference')
            ->layout('layouts.app')
            ->title('API Reference');
    }
}
