<?php

namespace App\Livewire\Lab;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use CyberShield\Models\ThreatLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class Dashboard extends Component
{
    public array $recentThreats = [];
    public int   $totalThreats  = 0;
    public int   $blockedIps    = 0;
    public int   $criticalCount = 0;
    public array $moduleStatus  = [];
    public array $threatsByType = [];
    public array $hourlyData    = [];
    public string $currentIp    = '';
    public string $reputation   = 'Trusted';
    public int    $threatScore  = 0;

    public function mount(): void
    {
        $this->refresh();
    }

    public function refresh(): void
    {
        $this->currentIp   = Request::ip();
        $this->threatScore = (int) Cache::get('cybershield:threat_score:' . $this->currentIp, 0);
        $this->reputation  = match (true) {
            $this->threatScore >= 75 => 'Malicious',
            $this->threatScore >= 45 => 'Suspicious',
            $this->threatScore >= 15 => 'Neutral',
            default                  => 'Trusted',
        };

        $this->totalThreats  = ThreatLog::count();
        $this->blockedIps    = ThreatLog::distinct('ip')->count('ip');
        $this->criticalCount = ThreatLog::where('severity', 'critical')->count();

        $this->recentThreats = ThreatLog::latest()
            ->limit(10)
            ->get()
            ->map(fn($t) => [
                'id'       => $t->id,
                'ip'       => $t->ip,
                'type'     => $t->threat_type,
                'severity' => $t->severity,
                'time'     => $t->created_at?->diffForHumans() ?? 'just now',
            ])
            ->toArray();

        $this->threatsByType = ThreatLog::selectRaw('threat_type, count(*) as cnt')
            ->groupBy('threat_type')
            ->orderByDesc('cnt')
            ->limit(6)
            ->get()
            ->mapWithKeys(fn($r) => [$r->threat_type => $r->cnt])
            ->toArray();

        // Hourly data for the past 12 hours
        $this->hourlyData = [];
        for ($h = 11; $h >= 0; $h--) {
            $start = now()->subHours($h)->startOfHour();
            $end   = now()->subHours($h)->endOfHour();
            $this->hourlyData[] = [
                'label' => $start->format('H:i'),
                'count' => ThreatLog::whereBetween('created_at', [$start, $end])->count(),
            ];
        }

        $this->moduleStatus = [
            'WAF Firewall'      => ['active' => true,  'key' => 'waf'],
            'Rate Limiter'      => ['active' => true,  'key' => 'rate'],
            'Bot Detection'     => ['active' => true,  'key' => 'bot'],
            'Network Guard'     => ['active' => true,  'key' => 'network'],
            'API Security'      => ['active' => true,  'key' => 'api'],
            'Threat Engine'     => ['active' => true,  'key' => 'threat'],
            'Security Scanner'  => ['active' => true,  'key' => 'scanner'],
            'DB Guard'          => ['active' => true,  'key' => 'db'],
        ];
    }

    #[Layout('layouts.app')]
    #[Title('Dashboard')]
    public function render()
    {
        return view('livewire.lab.dashboard');
    }
}
