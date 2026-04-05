<?php

namespace App\Livewire\Lab;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use CyberShield\Models\ThreatLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Request;

class NetworkLab extends Component
{
    public string  $simulatedIp    = '127.0.0.1';
    public bool    $geoBlockOn     = true;
    public bool    $torBlockOn     = true;
    public bool    $vpnBlockOn     = false;
    public bool    $proxyBlockOn   = true;
    public ?array  $result         = null;
    public bool    $isLoading      = false;
    public array   $analysisLog    = [];
    public string  $currentIp      = '';
    public array   $ipAnalysis     = [];
    public bool    $isDemoBlocked  = false;

    public static array $testIps = [
        '127.0.0.1'       => ['label' => 'Localhost (Trusted)',          'country' => 'LOCAL', 'asn' => 'Private', 'type' => 'private',    'tor' => false, 'vpn' => false, 'proxy' => false, 'threat' => 0],
        '8.8.8.8'         => ['label' => 'Google DNS (US)',              'country' => 'US',    'asn' => 'AS15169 Google LLC', 'type' => 'datacenter', 'tor' => false, 'vpn' => false, 'proxy' => false, 'threat' => 5],
        '185.220.101.1'   => ['label' => 'Known TOR Exit Node (DE)',     'country' => 'DE',    'asn' => 'AS4245 Tor Project', 'type' => 'tor',        'tor' => true,  'vpn' => false, 'proxy' => false, 'threat' => 95],
        '103.43.141.100'  => ['label' => 'Chinese Data Centre (CN)',     'country' => 'CN',    'asn' => 'AS45090 Tencent',   'type' => 'datacenter', 'tor' => false, 'vpn' => false, 'proxy' => false, 'threat' => 60],
        '185.100.87.1'    => ['label' => 'Russian Anonymous Proxy',      'country' => 'RU',    'asn' => 'AS197695 Reg.ru',   'type' => 'proxy',      'tor' => false, 'vpn' => false, 'proxy' => true,  'threat' => 80],
        '91.108.4.1'      => ['label' => 'N. Korea DPRK IP (KP)',        'country' => 'KP',    'asn' => 'AS131279 Star JV',  'type' => 'hostile',    'tor' => false, 'vpn' => false, 'proxy' => false, 'threat' => 90],
        '45.152.64.1'     => ['label' => 'Commercial VPN (Netherlands)', 'country' => 'NL',    'asn' => 'AS60458 NordVPN',   'type' => 'vpn',        'tor' => false, 'vpn' => true,  'proxy' => false, 'threat' => 35],
        '192.168.1.100'   => ['label' => 'Private LAN (Internal)',       'country' => 'PRIV',  'asn' => 'RFC1918 Private',   'type' => 'private',    'tor' => false, 'vpn' => false, 'proxy' => false, 'threat' => 0],
    ];

    public static array $blockedCountries = ['CN', 'RU', 'KP', 'IR', 'SY'];

    public function mount(): void
    {
        $this->currentIp = Request::ip();
        $this->analyzeIp($this->currentIp);
    }

    public function selectIp(string $ip): void
    {
        $this->simulatedIp = $ip;
        $this->result      = null;
        $this->analysisLog = [];
        $this->analyzeIp($ip);
    }

    public function analyzeIp(string $ip): void
    {
        $profile = self::$testIps[$ip] ?? null;
        if (!$profile) {
            $profile = ['label' => 'Unknown IP', 'country' => 'XX', 'asn' => 'UNKNOWN', 'type' => 'unknown', 'tor' => false, 'vpn' => false, 'proxy' => false, 'threat' => 0];
        }
        $this->ipAnalysis = array_merge(['ip' => $ip], $profile);
    }

    public function simulate(): void
    {
        $this->isLoading = true;
        $ip      = $this->simulatedIp;
        $profile = self::$testIps[$ip] ?? ['country' => 'XX', 'type' => 'unknown', 'tor' => false, 'vpn' => false, 'proxy' => false, 'threat' => 50];

        $this->analysisLog = [
            "[" . now()->format('H:i:s') . "] Incoming connection from: {$ip}",
            "[" . now()->format('H:i:s') . "] Country code: {$profile['country']}",
            "[" . now()->format('H:i:s') . "] ASN: " . ($profile['asn'] ?? 'Unknown'),
            "[" . now()->format('H:i:s') . "] IP type: " . strtoupper($profile['type']),
            "[" . now()->format('H:i:s') . "] TOR exit node: " . ($profile['tor'] ? 'YES' : 'NO'),
            "[" . now()->format('H:i:s') . "] VPN detected: "  . ($profile['vpn']  ? 'YES' : 'NO'),
            "[" . now()->format('H:i:s') . "] Proxy detected: ". ($profile['proxy'] ? 'YES' : 'NO'),
            "[" . now()->format('H:i:s') . "] Threat score: {$profile['threat']}/100",
            "[" . now()->format('H:i:s') . "] Running NetworkGuard policy checks...",
        ];

        $blocked = false;
        $reasons = [];

        // Check 1: Geo-blocking
        if ($this->geoBlockOn && in_array($profile['country'], self::$blockedCountries)) {
            $this->analysisLog[] = "[" . now()->format('H:i:s') . "] ✓ GEO-BLOCK: Country '{$profile['country']}' is in blocked list";
            $blocked = true;
            $reasons[] = "Country '{$profile['country']}' is geo-blocked (DetectCountryBlockMiddleware)";
        }

        // Check 2: TOR
        if ($this->torBlockOn && $profile['tor']) {
            $this->analysisLog[] = "[" . now()->format('H:i:s') . "] ✓ TOR-BLOCK: IP is a known TOR exit node";
            $blocked = true;
            $reasons[] = "TOR exit node detected (DetectTorNetworkMiddleware)";
        }

        // Check 3: VPN
        if ($this->vpnBlockOn && $profile['vpn']) {
            $this->analysisLog[] = "[" . now()->format('H:i:s') . "] ✓ VPN-BLOCK: Commercial VPN provider detected";
            $blocked = true;
            $reasons[] = "VPN provider identified (DetectVpnNetworkMiddleware)";
        }

        // Check 4: Proxy
        if ($this->proxyBlockOn && $profile['proxy']) {
            $this->analysisLog[] = "[" . now()->format('H:i:s') . "] ✓ PROXY-BLOCK: Anonymous proxy detected";
            $blocked = true;
            $reasons[] = "Anonymous proxy detected (DetectProxyNetworkMiddleware)";
        }

        if (!$blocked) {
            $this->analysisLog[] = "[" . now()->format('H:i:s') . "] All checks passed — connection allowed";
        } else {
            $this->analysisLog[] = "[" . now()->format('H:i:s') . "] Connection DROPPED — returning HTTP 403";
        }

        if ($blocked) {
            $this->result = [
                'type'     => 'blocked',
                'headline' => '✓ Connection Blocked by NetworkGuard',
                'message'  => "The request from {$ip} was terminated. Block reasons: " . implode('; ', $reasons),
                'reasons'  => $reasons,
                'profile'  => $profile,
                'ip'       => $ip,
            ];

            ThreatLog::create([
                'ip'          => request()->ip(),
                'threat_type' => 'NETWORK_BLOCK_' . strtoupper($profile['type']),
                'severity'    => $profile['threat'] >= 75 ? 'critical' : ($profile['threat'] >= 50 ? 'high' : 'medium'),
                'details'     => [
                    'module'          => 'NETWORK',
                    'simulated_ip'    => $ip,
                    'country'         => $profile['country'],
                    'block_reasons'   => $reasons,
                    'user_agent'      => request()->userAgent(),
                    'request_method'  => 'GET',
                    'request_uri'     => '/lab/network',
                ],
            ]);
        } else {
            $this->result = [
                'type'     => 'allowed',
                'headline' => 'Connection Permitted',
                'message'  => "The IP {$ip} ({$profile['label']}) passed all active network security policies and was allowed through.",
                'reasons'  => [],
                'profile'  => $profile,
                'ip'       => $ip,
            ];
        }

        $this->isLoading = false;
    }

    public function blockDemoIp(): void
    {
        Cache::put('cybershield:blocked:' . $this->simulatedIp, 'Blocked via Lab demo', 3600);
        $this->isDemoBlocked = true;
    }

    public function unblockDemoIp(): void
    {
        Cache::forget('cybershield:blocked:' . $this->simulatedIp);
        $this->isDemoBlocked = false;
    }

    #[Layout('layouts.app')]
    #[Title('Network Guard Lab')]
    public function render()
    {
        return view('livewire.lab.network-lab');
    }
}
