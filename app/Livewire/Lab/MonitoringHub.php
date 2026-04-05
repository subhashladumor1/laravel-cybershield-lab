<?php

namespace App\Livewire\Lab;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use CyberShield\Models\ThreatLog;

class MonitoringHub extends Component
{
    use WithPagination;

    public string $searchIp       = '';
    public string $filterSeverity = '';
    public string $filterModule   = '';
    public bool   $isGenerating   = false;
    public bool   $autoRefresh    = false;

    protected $listeners = ['refreshLogs' => '$refresh'];

    public function updatingSearchIp(): void     { $this->resetPage(); }
    public function updatingFilterSeverity(): void { $this->resetPage(); }
    public function updatingFilterModule(): void  { $this->resetPage(); }

    public function generateTraffic(): void
    {
        $this->isGenerating = true;

        $scenarios = [
            ['ip' => '203.0.113.45', 'type' => 'SQL_INJECTION',       'sev' => 'critical', 'module' => 'WAF',          'payload' => "' UNION SELECT username,pass FROM users--",     'ua' => 'sqlmap/1.7'],
            ['ip' => '185.220.101.1','type' => 'TOR_EXIT_NODE',        'sev' => 'critical', 'module' => 'NETWORK',       'payload' => '185.220.101.1: TOR exit node detected',           'ua' => 'curl/7.88'],
            ['ip' => '45.227.253.10','type' => 'XSS_ATTACK',           'sev' => 'high',     'module' => 'WAF',          'payload' => "<script>document.cookie='stolen'</script>",       'ua' => 'Mozilla/5.0'],
            ['ip' => '198.51.100.25','type' => 'RATE_LIMIT_EXCEEDED',  'sev' => 'medium',   'module' => 'RATE_LIMITER', 'payload' => '1200 req/min (limit: 100)',                        'ua' => 'python-requests/2.28'],
            ['ip' => '192.0.2.100', 'type' => 'BOT_DETECTED',          'sev' => 'high',     'module' => 'BOT',          'payload' => 'HeadlessChrome/110.0.0.0 detected',               'ua' => 'HeadlessChrome/110'],
            ['ip' => '103.43.141.1','type' => 'GEO_BLOCK_CN',          'sev' => 'critical', 'module' => 'NETWORK',       'payload' => 'Country CN is geo-blocked',                       'ua' => 'Mozilla/5.0'],
            ['ip' => '91.108.4.1',  'type' => 'LFI_ATTEMPT',           'sev' => 'critical', 'module' => 'WAF',          'payload' => '../../../../etc/shadow',                          'ua' => 'Nikto/2.1.6'],
            ['ip' => '198.51.100.5','type' => 'CREDENTIAL_STUFFING',   'sev' => 'high',     'module' => 'AUTH',         'payload' => '500 failed login attempts from single IP',        'ua' => 'python-requests/2.28'],
            ['ip' => '203.0.113.8', 'type' => 'API_ABUSE',             'sev' => 'medium',   'module' => 'API',          'payload' => 'API key fingerprint mismatch',                    'ua' => 'PostmanRuntime/7.32'],
            ['ip' => '45.33.22.11', 'type' => 'RCE_ATTEMPT',           'sev' => 'critical', 'module' => 'WAF',          'payload' => "eval(base64_decode('cGhwaW5mbygpOw=='))",        'ua' => 'curl/7.88'],
        ];

        foreach ($scenarios as $s) {
            ThreatLog::create([
                'ip'          => $s['ip'],
                'threat_type' => $s['type'],
                'severity'    => $s['sev'],
                'details'     => [
                    'module'         => $s['module'],
                    'payload'        => $s['payload'],
                    'user_agent'     => $s['ua'],
                    'request_method' => rand(0, 1) ? 'GET' : 'POST',
                    'request_uri'    => '/api/v1/' . strtolower($s['module']),
                ],
                'created_at' => now()->subMinutes(rand(0, 60)),
            ]);
        }

        $this->resetPage();
        $this->isGenerating = false;
    }

    public function clearLogs(): void
    {
        ThreatLog::truncate();
        $this->resetPage();
    }

    #[Layout('layouts.app')]
    #[Title('Threat Monitor')]
    public function render()
    {
        $query = ThreatLog::query()
            ->when($this->searchIp,       fn($q) => $q->where('ip', 'like', "%{$this->searchIp}%"))
            ->when($this->filterSeverity, fn($q) => $q->where('severity', $this->filterSeverity))
            ->when($this->filterModule,   fn($q) => $q->where('threat_type', 'like', "%{$this->filterModule}%"))
            ->latest();

        $stats = [
            'total'    => ThreatLog::count(),
            'critical' => ThreatLog::where('severity', 'critical')->count(),
            'high'     => ThreatLog::where('severity', 'high')->count(),
            'medium'   => ThreatLog::where('severity', 'medium')->count(),
            'low'      => ThreatLog::where('severity', 'low')->count(),
        ];

        return view('livewire.lab.monitoring-hub', [
            'logs'  => $query->paginate(15),
            'stats' => $stats,
        ]);
    }
}
