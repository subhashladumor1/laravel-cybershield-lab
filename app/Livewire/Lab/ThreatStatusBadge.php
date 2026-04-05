<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use CyberShield\Models\ThreatLog;
use Illuminate\Support\Facades\Cache;

class ThreatStatusBadge extends Component
{
    public int    $recentCount = 0;
    public string $level       = 'safe';

    public function mount(): void
    {
        $this->recentCount = ThreatLog::where('created_at', '>=', now()->subMinutes(5))->count();
        $this->level = match(true) {
            $this->recentCount >= 10 => 'critical',
            $this->recentCount >= 3  => 'high',
            $this->recentCount >= 1  => 'warning',
            default                  => 'safe',
        };
    }

    public function render()
    {
        return <<<'BLADE'
        <div class="topbar-pill {{ $level === 'safe' ? 'system-on' : ($level === 'warning' ? '' : 'system-off') }}"
             style="{{ $level !== 'safe' ? 'border-color: var(--rose);' : '' }}">
            <div class="dot {{ $level !== 'safe' ? 'red' : '' }}"></div>
            @if($level === 'safe')
                No Recent Threats
            @else
                {{ $recentCount }} Threat{{ $recentCount !== 1 ? 's' : '' }} (5min)
            @endif
        </div>
        BLADE;
    }
}
