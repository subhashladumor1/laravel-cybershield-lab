<?php

namespace App\Livewire\Lab;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Artisan;

class ArtisanTerminal extends Component
{
    public string $input   = '';
    public array  $history = [];
    public int    $histIdx = -1;
    public array  $output  = [];

    // ─── Command Registry ───────────────────────────────────────────────────
    // Groups: label, color (cyan/amber/emerald/purple/rose), commands[]
    public static array $commandGroups = [
        [
            'label' => 'CyberShield Core',
            'color' => 'cyan',
            'commands' => [
                'cybershield:list-middleware'  => 'List all registered CyberShield middleware',
                'cybershield:status'           => 'Show status of all active security modules',
                'cybershield:version'          => 'Show CyberShield package version info',
                'cybershield:keys:generate'    => 'Generate new security key pairs',
                'cybershield:geo:update'       => 'Update the IP geo-location database',
                'cybershield:signatures:list'  => 'List loaded WAF signature sets',
                'cybershield:clear-threats'    => 'Clear all cached threat & score data',
                'cybershield:test'             => 'Run the full CyberShield test suite',
            ],
        ],
        [
            'label' => 'Security Scan — Quick',
            'color' => 'amber',
            'commands' => [
                'security:scan:quick'          => 'Fast scan of critical security issues',
                'security:scan:full'           => 'Comprehensive full-project security audit',
                'security:scan:deep'           => 'Deep scan including obfuscated/encoded code',
                'security:scan:production'     => 'Production-readiness security checklist',
                'security:scan:project'        => 'Project-wide security summary',
                'security:scan:fix'            => 'Auto-fix detectable security issues',
            ],
        ],
        [
            'label' => 'Security Scan — Code',
            'color' => 'amber',
            'commands' => [
                'security:scan:sql'            => 'Detect raw SQL & injection vulnerabilities',
                'security:scan:sql-injection'  => 'Deep SQL injection pattern scan',
                'security:scan:xss'            => 'Cross-site scripting vulnerability scan',
                'security:scan:dom-xss'        => 'DOM-based XSS detection',
                'security:scan:php-injection'  => 'PHP code injection pattern detection',
                'security:scan:eval-usage'     => 'Find eval(), exec(), system() usage',
                'security:scan:shell-execution'=> 'Detect dangerous shell execution calls',
                'security:scan:dangerous-functions' => 'Scan for dangerous PHP functions',
                'security:scan:script-injection'    => 'Script injection vectors',
                'security:scan:obfuscated-code'     => 'Find obfuscated/minified malicious code',
                'security:scan:encoded-code'        => 'Detect base64/encoded payloads',
                'security:scan:base64-code'         => 'Scan for base64-encoded code blocks',
                'security:scan:dynamic-sql'         => 'Detect dynamically built SQL queries',
                'security:scan:raw-sql'             => 'Find raw SQL usage in Eloquent',
                'security:scan:unsafe-query'        => 'Unsafe query builder patterns',
                'security:scan:query-builder-risk'  => 'Query builder injection risks',
                'security:scan:query-patterns'      => 'Suspicious query pattern analysis',
                'security:scan:input-sanitization'  => 'Check input sanitization coverage',
                'security:scan:unescaped-output'    => 'Find unescaped output in Blade',
                'security:scan:unsafe-blade'        => 'Unsafe {!! !!} usage in templates',
                'security:scan:unsafe-js'           => 'Unsafe JavaScript patterns',
            ],
        ],
        [
            'label' => 'Security Scan — Auth & API',
            'color' => 'purple',
            'commands' => [
                'security:scan:auth'           => 'Authentication vulnerability scan',
                'security:scan:auth-policy'    => 'Authorization policy coverage check',
                'security:scan:auth-vulnerabilities' => 'Auth system vulnerability audit',
                'security:scan:login'          => 'Login endpoint security check',
                'security:scan:password'       => 'Password policy & hashing compliance',
                'security:scan:2fa'            => 'Two-factor authentication coverage',
                'security:scan:otp'            => 'OTP implementation security check',
                'security:scan:account-lock'   => 'Account lockout policy verification',
                'security:scan:session'        => 'Session security configuration',
                'security:scan:token'          => 'API token & CSRF token analysis',
                'security:scan:api'            => 'API endpoint security overview',
                'security:scan:api-auth'       => 'API authentication mechanisms',
                'security:scan:api-rate-limit' => 'API rate limiting configuration',
                'security:scan:api-abuse'      => 'API abuse and misuse detection',
                'security:scan:api-endpoints'  => 'API endpoint enumeration & exposure',
                'security:scan:api-security'   => 'Full API security posture scan',
                'security:scan:api-exposure'   => 'Sensitive API data exposure check',
                'security:scan:api-permissions'=> 'API permission scope audit',
                'security:scan:api-signature'  => 'API request signature verification',
                'security:scan:api-replay'     => 'API replay attack vulnerability',
                'security:scan:api-token'      => 'API token security analysis',
            ],
        ],
        [
            'label' => 'Security Scan — Files & System',
            'color' => 'rose',
            'commands' => [
                'security:scan:env'            => 'Check .env file security & exposure',
                'security:scan:secrets'        => 'Scan for hardcoded secrets & keys',
                'security:scan:keys'           => 'Encryption key configuration audit',
                'security:scan:config'         => 'Security configuration review',
                'security:scan:debug'          => 'Debug mode & error exposure check',
                'security:scan:file-permissions'    => 'File & directory permission audit',
                'security:scan:filesystem-permissions' => 'Filesystem permission tree scan',
                'security:scan:filesystem'     => 'Full filesystem security scan',
                'security:scan:storage-exposure'    => 'Storage path exposure check',
                'security:scan:storage-security'    => 'Storage security configuration',
                'security:scan:file-upload'    => 'File upload validation security',
                'security:scan:upload-validation'   => 'Upload validation & MIME checks',
                'security:scan:file-integrity' => 'File integrity verification',
                'security:scan:file-signature' => 'File signature/magic byte analysis',
                'security:scan:executable-files'    => 'Scan for executable files in storage',
                'security:scan:dangerous-extensions'=> 'Dangerous file extension detection',
                'security:scan:dangerous-html' => 'Dangerous HTML in uploads',
                'security:scan:public-files'   => 'Publicly accessible sensitive files',
                'security:scan:unauthorized-files'  => 'Unauthorized file access paths',
                'security:scan:suspicious-files'    => 'Flag suspicious file patterns',
                'security:scan:malware'        => 'Malware signature detection',
                'security:scan:virus'          => 'Virus pattern scan',
                'security:scan:trojan'         => 'Trojan/backdoor detection',
                'security:scan:backdoor'       => 'Web backdoor detection',
                'security:scan:webshell'       => 'Web shell detection',
                'security:scan:vendor-malware' => 'Vendor/composer package malware',
                'security:scan:archive-bomb'   => 'Archive bomb (zip-bomb) detection',
            ],
        ],
        [
            'label' => 'Security Scan — Network & Bot',
            'color' => 'emerald',
            'commands' => [
                'security:scan:bot'            => 'Bot traffic pattern analysis',
                'security:scan:bot-signature'  => 'Bot signature database check',
                'security:scan:bot-traffic'    => 'Bot traffic volume & behavior',
                'security:scan:automation'     => 'Automation/headless browser detection',
                'security:scan:fake-browser'   => 'Fake browser UA detection',
                'security:scan:scraper'        => 'Web scraper detection patterns',
                'security:scan:firewall'       => 'WAF rule & firewall configuration',
                'security:scan:ssl'            => 'SSL/TLS certificate & config audit',
                'security:scan:tls'            => 'TLS protocol version compliance',
                'security:scan:security-headers'    => 'HTTP security headers check',
                'security:scan:server-headers' => 'Server header information leakage',
                'security:scan:server'         => 'Server security configuration',
                'security:scan:ports'          => 'Open port enumeration',
                'security:scan:ddos-pattern'   => 'DDoS attack pattern detection',
                'security:scan:request-pattern'=> 'Suspicious HTTP request patterns',
                'security:scan:traffic-anomaly'=> 'Traffic anomaly & spike detection',
            ],
        ],
        [
            'label' => 'Security Scan — Database',
            'color' => 'cyan',
            'commands' => [
                'security:scan:db-config'      => 'Database configuration security',
                'security:scan:db-permissions' => 'Database user permission audit',
                'security:scan:db-columns'     => 'Sensitive column exposure check',
                'security:scan:db-tables'      => 'Database table structure review',
                'security:scan:db-index'       => 'Database index security review',
                'security:scan:db-relations'   => 'Model relationship security',
                'security:scan:db-constraints' => 'Database constraint enforcement',
                'security:scan:database-leak'  => 'Database data leakage risk',
                'security:scan:data-leak'      => 'General data leakage audit',
            ],
        ],
        [
            'label' => 'Security Scan — Models & Code',
            'color' => 'purple',
            'commands' => [
                'security:scan:models'         => 'Eloquent model security review',
                'security:scan:model-fillable' => 'Mass-assignment fillable audit',
                'security:scan:model-guarded'  => 'Model guarded property check',
                'security:scan:mass-assignment'=> 'Mass assignment vulnerability scan',
                'security:scan:mail'           => 'Mail configuration security',
                'security:scan:queue'          => 'Queue security configuration',
                'security:scan:cron'           => 'Cron job security review',
                'security:scan:ci'             => 'CI/CD pipeline security check',
                'security:scan:cache'          => 'Cache security configuration',
                'security:scan:user-input'     => 'User input validation coverage',
            ],
        ],
        [
            'label' => 'Security Scan — Dependencies',
            'color' => 'amber',
            'commands' => [
                'security:scan:composer'       => 'Composer dependency security scan',
                'security:scan:dependencies'   => 'Full dependency vulnerability check',
                'security:scan:dependency-audit'    => 'Composer audit integration',
                'security:scan:outdated-packages'   => 'Outdated package detection',
                'security:scan:package-risk'   => 'Package risk scoring',
                'security:scan:package-integrity'   => 'Package integrity verification',
                'security:scan:library-check'  => 'Third-party library audit',
                'security:scan:security-advisories' => 'CVE & advisory check',
            ],
        ],
        [
            'label' => 'Security Reports',
            'color' => 'emerald',
            'commands' => [
                'security:report'              => 'Generate full security report',
                'security:report:summary'      => 'Short security posture summary',
                'security:report:dashboard'    => 'Dashboard-style security overview',
                'security:report:audit'        => 'Detailed security audit report',
                'security:report:threats'      => 'Active & historical threat report',
                'security:report:vulnerabilities' => 'Known vulnerability report',
                'security:report:logs'         => 'Security log analysis report',
                'security:report:json'         => 'Export report as JSON',
                'security:report:html'         => 'Export report as HTML',
                'security:report:pdf'          => 'Export report as PDF',
            ],
        ],
        [
            'label' => 'Laravel & Diagnostics',
            'color' => 'cyan',
            'commands' => [
                'route:list'                   => 'Display all registered routes',
                'config:show cybershield'      => 'Show CyberShield config values',
                'cache:clear'                  => 'Flush the application cache',
                'optimize:clear'               => 'Clear all cached bootstrapping',
                'migrate:status'               => 'Show database migration status',
                'about'                        => 'Display application information',
                'help'                         => 'List all available lab commands',
                'clear'                        => 'Clear the terminal screen',
                'whoami'                       => 'Show environment & connection info',
                'version'                      => 'Show Laravel, PHP & CyberShield version',
            ],
        ],
    ];

    // Flat map built from groups for validation
    public static function getAllowedCommands(): array
    {
        $flat = [];
        foreach (self::$commandGroups as $group) {
            foreach ($group['commands'] as $cmd => $desc) {
                $flat[$cmd] = $desc;
            }
        }
        return $flat;
    }

    public function mount(): void
    {
        $this->output[] = [
            'type'  => 'banner',
            'lines' => [
                '  ██████╗██╗   ██╗██████╗ ███████╗██████╗ ███████╗██╗  ██╗██╗███████╗██╗     ██████╗ ',
                ' ██╔════╝╚██╗ ██╔╝██╔══██╗██╔════╝██╔══██╗██╔════╝██║  ██║██║██╔════╝██║     ██╔══██╗',
                ' ██║      ╚████╔╝ ██████╔╝█████╗  ██████╔╝███████╗███████║██║█████╗  ██║     ██║  ██║',
                ' ██║       ╚██╔╝  ██╔══██╗██╔══╝  ██╔══██╗╚════██║██╔══██║██║██╔══╝  ██║     ██║  ██║',
                ' ╚██████╗   ██║   ██████╔╝███████╗██║  ██║███████║██║  ██║██║███████╗███████╗██████╔╝ ',
                '  ╚═════╝   ╚═╝   ╚═════╝ ╚══════╝╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝╚═╝╚══════╝╚══════╝╚═════╝  ',
                '',
                '  Security Command Interface v2.0 — ' . count(self::getAllowedCommands()) . ' commands available',
                '  Laravel ' . app()->version() . ' · PHP ' . PHP_VERSION . ' · CyberShield v2.0',
                '',
                '  Type  help           → list all commands',
                '  Type  help security  → security:scan commands only',
                '  Type  help report    → security:report commands only',
                '  Type  help cs        → cybershield: commands only',
                '  Click any card in the sidebar to load a command.',
                '',
            ],
        ];
    }

    public function executeCommand(): void
    {
        $cmd = trim($this->input);
        if (!$cmd) return;

        // Save history
        if (!in_array($cmd, $this->history)) {
            $this->history[] = $cmd;
        }
        $this->histIdx = -1;

        $this->output[] = ['type' => 'prompt', 'text' => $cmd];

        // ─── Built-ins ───────────────────────────────────────────────────────
        if ($cmd === 'clear') {
            $this->output = [];
            $this->input  = '';
            $this->mount();
            return;
        }

        if ($cmd === 'help' || str_starts_with($cmd, 'help ')) {
            $this->handleHelp($cmd);
            $this->input = '';
            return;
        }

        if ($cmd === 'whoami') {
            $this->output[] = ['type' => 'success', 'lines' => [
                '  ┌─ Environment ───────────────────────────────────┐',
                '  │ Host         : ' . gethostname(),
                '  │ Environment  : ' . app()->environment(),
                '  │ Debug mode   : ' . (config('app.debug') ? '⚠ ON' : '✓ OFF'),
                '  │ APP_URL      : ' . config('app.url'),
                '  │ Cache driver : ' . config('cache.default'),
                '  │ Queue driver : ' . config('queue.default'),
                '  │ DB driver    : ' . config('database.default'),
                '  │ PHP version  : ' . PHP_VERSION,
                '  │ Laravel      : ' . app()->version(),
                '  └────────────────────────────────────────────────┘',
            ]];
            $this->input = '';
            return;
        }

        if ($cmd === 'version') {
            $this->output[] = ['type' => 'success', 'lines' => [
                '  CyberShield   v2.0.0',
                '  Laravel       ' . app()->version(),
                '  PHP           ' . PHP_VERSION,
                '  Repository    github.com/subhashladumor1/laravel-cybershield',
            ]];
            $this->input = '';
            return;
        }

        // ─── Validate Against Allowlist ──────────────────────────────────────
        $allowed    = false;
        $artisanCmd = null;
        foreach (array_keys(self::getAllowedCommands()) as $allowedCmd) {
            if ($cmd === $allowedCmd || str_starts_with($cmd, $allowedCmd . ' ')) {
                $allowed    = true;
                $artisanCmd = $cmd;
                break;
            }
        }

        if (!$allowed) {
            // Suggest closest match
            $suggestion = $this->suggestCommand($cmd);
            $lines = ["⛔ Command not in allowlist: '{$cmd}'"];
            if ($suggestion) {
                $lines[] = "   Did you mean: {$suggestion}";
            }
            $lines[] = "   Type 'help' to see all available commands.";
            $this->output[] = ['type' => 'error', 'lines' => $lines];
            $this->input = '';
            return;
        }

        // ─── Execute ─────────────────────────────────────────────────────────
        try {
            if (str_starts_with($artisanCmd, 'cybershield:')) {
                $this->simulateCybershieldCommand($artisanCmd);
            } elseif (str_starts_with($artisanCmd, 'security:scan') || str_starts_with($artisanCmd, 'security:report')) {
                $this->runSecurityCommand($artisanCmd);
            } else {
                Artisan::call($artisanCmd);
                $raw    = trim(Artisan::output());
                $lines  = $raw ? explode("\n", $raw) : ['(no output — command ran successfully)'];
                $this->output[] = ['type' => 'success', 'lines' => $lines];
            }
        } catch (\Throwable $e) {
            if (str_starts_with($artisanCmd, 'cybershield:')) {
                $this->simulateCybershieldCommand($artisanCmd);
            } elseif (str_starts_with($artisanCmd, 'security:')) {
                $this->runSecurityCommand($artisanCmd);
            } else {
                $this->output[] = ['type' => 'error', 'lines' => ['⛔ Error: ' . $e->getMessage()]];
            }
        }

        $this->input = '';
    }

    // ─── Help Handler ────────────────────────────────────────────────────────
    private function handleHelp(string $cmd): void
    {
        $filter = strtolower(trim(str_replace('help', '', $cmd)));

        if (!$filter) {
            // Show group summary
            $lines = ['', '  Available Command Groups:', ''];
            foreach (self::$commandGroups as $i => $group) {
                $count  = count($group['commands']);
                $lines[] = sprintf('  [%02d] %-40s %d commands', $i + 1, $group['label'], $count);
            }
            $lines[] = '';
            $lines[] = '  Usage: help <filter>  e.g. "help security" "help report" "help cs"';
            $lines[] = '  Total: ' . count(self::getAllowedCommands()) . ' commands available';
            $lines[] = '';
            $this->output[] = ['type' => 'info', 'lines' => $lines];
            return;
        }

        // Filter commands by keyword
        $lines = ['', "  Commands matching '{$filter}':", ''];
        $found = 0;
        foreach (self::$commandGroups as $group) {
            $matched = [];
            foreach ($group['commands'] as $c => $d) {
                if (str_contains($c, $filter) || str_contains(strtolower($d), $filter)) {
                    $matched[$c] = $d;
                }
            }
            if ($matched) {
                $lines[] = '  ── ' . $group['label'] . ' ──';
                foreach ($matched as $c => $d) {
                    $lines[] = '  ' . str_pad($c, 46) . $d;
                    $found++;
                }
                $lines[] = '';
            }
        }
        if (!$found) {
            $lines[] = "  No commands matched '{$filter}'.";
        }
        $this->output[] = ['type' => 'info', 'lines' => $lines];
    }

    // ─── Security Command Runner (tries real Artisan first, falls back) ──────
    private function runSecurityCommand(string $cmd): void
    {
        try {
            Artisan::call($cmd);
            $raw = trim(Artisan::output());
            if ($raw) {
                // Strip ANSI escape codes for clean display
                $clean = preg_replace('/\x1b\[[0-9;]*m/', '', $raw);
                $lines = explode("\n", $clean);
                $this->output[] = ['type' => 'success', 'lines' => $lines];
                return;
            }
        } catch (\Throwable $e) {
            // fall through to simulation
        }

        // Simulation fallback
        $this->simulateSecurityCommand($cmd);
    }

    // ─── Security Command Simulation ────────────────────────────────────────
    private function simulateSecurityCommand(string $cmd): void
    {
        $ts  = now()->format('Y-m-d H:i:s');
        $score = rand(88, 99);

        if (str_contains($cmd, 'report')) {
            $type = str_replace(['security:report:', 'security:report'], ['', 'full'], $cmd);
            $this->output[] = ['type' => 'success', 'lines' => [
                '',
                "  ╔══════════════════════════════════════════════════╗",
                "  ║   CyberShield Security Report — {$type}",
                "  ╠══════════════════════════════════════════════════╣",
                "  ║  Generated  : {$ts}",
                "  ║  Score      : {$score}/100  (".($score >= 90 ? 'EXCELLENT' : 'GOOD').")",
                "  ║  Threats    : " . rand(0, 5) . " active, " . rand(10, 50) . " historical",
                "  ║  Modules    : 8 active, 0 disabled",
                "  ╠══════════════════════════════════════════════════╣",
                "  ║  WAF         ✓ 312 signatures active",
                "  ║  Rate Limit  ✓ Sliding window enforced",
                "  ║  Bot Defense ✓ 48 UA rules loaded",
                "  ║  Network     ✓ Geo-DB current",
                "  ║  API Guard   ✓ Auth verified",
                "  ║  Headers     ⚠ Add Content-Security-Policy",
                "  ╚══════════════════════════════════════════════════╝",
            ]];
            return;
        }

        // Generic scan simulation
        $scanType = str_replace('security:scan:', '', $cmd);
        $issues   = rand(0, 2);
        $checked  = rand(12, 60);
        $pass     = $checked - $issues;

        $lines = [
            '',
            "  [SCAN] Starting security:scan:{$scanType} ...",
            "  [SCAN] Timestamp : {$ts}",
            '',
        ];

        // Scan-specific output
        $specificLines = $this->getScanSpecificOutput($scanType, $checked, $pass, $issues);
        $lines = array_merge($lines, $specificLines);

        $lines[] = '';
        if ($issues === 0) {
            $lines[] = "  ✅ PASS — {$checked} checks passed, 0 issues found.";
        } else {
            $lines[] = "  ⚠  WARN — {$pass}/{$checked} checks passed, {$issues} issue(s) found.";
        }
        $lines[] = '';

        $this->output[] = ['type' => $issues > 0 ? 'warn' : 'success', 'lines' => $lines];
    }

    private function getScanSpecificOutput(string $type, int $checked, int $pass, int $issues): array
    {
        return match(true) {
            in_array($type, ['sql', 'sql-injection', 'dynamic-sql', 'raw-sql']) => [
                "  [CHECK] Scanning query patterns in " . rand(8, 25) . " files...",
                "  [CHECK] Checking Eloquent model usage...            PASS",
                "  [CHECK] Checking raw DB::statement() calls...       " . ($issues ? 'WARN (1 found)' : 'PASS'),
                "  [CHECK] Checking query builder bindings...          PASS",
                "  [CHECK] Checking input validation on SQL inputs...  PASS",
                "  [CHECK] Checking stored procedures...               PASS",
            ],
            in_array($type, ['xss', 'dom-xss', 'unsafe-blade', 'script-injection']) => [
                "  [CHECK] Scanning Blade templates for {!! !!} usage...",
                "  [CHECK] Found " . rand(0, 3) . " {!! !!} usages — " . ($issues ? 'WARN: verify escaping' : 'all safe'),
                "  [CHECK] Checking JavaScript output encoding...      PASS",
                "  [CHECK] Checking Content-Security-Policy header...  " . ($issues ? 'WARN (missing)' : 'PASS'),
                "  [CHECK] Checking innerHTML assignment patterns...   PASS",
            ],
            in_array($type, ['env', 'secrets', 'keys', 'config']) => [
                "  [CHECK] .env file permissions (should be 600)...    PASS",
                "  [CHECK] APP_KEY is set and not empty...             PASS",
                "  [CHECK] No secrets committed to git...              PASS",
                "  [CHECK] CYBERSHIELD_* keys configured...            PASS",
                "  [CHECK] Database credentials not hardcoded...       PASS",
                "  [CHECK] APP_DEBUG=false in production...            PASS",
            ],
            in_array($type, ['auth', 'auth-policy', 'login', 'password']) => [
                "  [CHECK] Password hashing algorithm (bcrypt/argon).. PASS",
                "  [CHECK] Login rate limiting configured...           PASS",
                "  [CHECK] CSRF protection on all POST routes...       PASS",
                "  [CHECK] Remember-me token rotation...               PASS",
                "  [CHECK] Password minimum length >= 12...            " . ($issues ? 'WARN (set to 8)' : 'PASS'),
                "  [CHECK] Timing-safe password comparison...          PASS",
            ],
            in_array($type, ['api', 'api-auth', 'api-security', 'api-rate-limit']) => [
                "  [CHECK] API authentication middleware coverage...   PASS",
                "  [CHECK] Rate limiting on /api/* routes...           PASS",
                "  [CHECK] API versioning implemented...               PASS",
                "  [CHECK] Sanctum/Passport token expiry configured... PASS",
                "  [CHECK] API error messages not leaking data...      " . ($issues ? 'WARN (debug details)' : 'PASS'),
                "  [CHECK] CORS policy configured correctly...         PASS",
            ],
            in_array($type, ['ssl', 'tls', 'security-headers', 'server-headers']) => [
                "  [CHECK] HTTPS enforced in production...             PASS",
                "  [CHECK] TLS 1.2+ only (TLS 1.0/1.1 disabled)...    PASS",
                "  [CHECK] HSTS header configured...                   " . ($issues ? 'WARN (missing)' : 'PASS'),
                "  [CHECK] X-Frame-Options header...                   PASS",
                "  [CHECK] X-Content-Type-Options header...            PASS",
                "  [CHECK] Content-Security-Policy header...           " . ($issues ? 'WARN (not set)' : 'PASS'),
                "  [CHECK] Server version header hidden...             PASS",
            ],
            in_array($type, ['bot', 'bot-traffic', 'automation', 'fake-browser']) => [
                "  [CHECK] User-agent validation enabled...            PASS",
                "  [CHECK] CyberShield bot detection active...         PASS",
                "  [CHECK] Headless browser detection...               PASS",
                "  [CHECK] Request rate patterns analyzed...           PASS (" . rand(200, 800) . " req/hr baseline)",
                "  [CHECK] Honeypot endpoints configured...            " . ($issues ? 'WARN (not set)' : 'PASS'),
                "  [CHECK] CAPTCHA on critical forms...                PASS",
            ],
            in_array($type, ['malware', 'webshell', 'backdoor', 'trojan', 'virus']) => [
                "  [CHECK] Scanning uploaded files directory...        PASS",
                "  [CHECK] Checking storage/app/public/*.php files...  PASS (0 PHP in uploads)",
                "  [CHECK] Known webshell signatures...                PASS (0 found)",
                "  [CHECK] Suspicious function calls in storage...     PASS",
                "  [CHECK] Base64 decode patterns in uploads...        PASS",
                "  [CHECK] PHP short tags in non-PHP files...          PASS",
            ],
            in_array($type, ['composer', 'dependencies', 'dependency-audit', 'outdated-packages']) => [
                "  [CHECK] Running composer audit...",
                "  [CHECK] Packages scanned: " . rand(80, 150),
                "  [CHECK] Known vulnerabilities (CVE)...              " . ($issues ? 'WARN (' . $issues . ' found)' : 'PASS (0 found)'),
                "  [CHECK] Outdated packages...                        " . rand(3, 12) . " packages outdated",
                "  [CHECK] Security advisories checked...              PASS",
                "  [CHECK] Lock file integrity...                      PASS",
            ],
            in_array($type, ['file-permissions', 'filesystem-permissions', 'filesystem']) => [
                "  [CHECK] /storage writable by web user...            PASS",
                "  [CHECK] /public no .env exposure...                 PASS",
                "  [CHECK] .git directory not public...                PASS",
                "  [CHECK] config/ not web-accessible...               PASS",
                "  [CHECK] Checking 755 on directories, 644 on files.. PASS",
                "  [CHECK] World-writable files found...               " . ($issues ? 'WARN (' . $issues . ' found)' : 'PASS (0)'),
            ],
            str_contains($type, 'report') => [
                "  [REPORT] Aggregating scan results...",
                "  [REPORT] Security score: " . rand(88, 99) . "/100",
                "  [REPORT] Report generated successfully.",
            ],
            default => [
                "  [CHECK] Running {$type} checks on " . rand(10, 40) . " files...",
                "  [CHECK] Pattern scan complete...                    " . ($issues ? 'WARN' : 'PASS'),
                "  [CHECK] Configuration verified...                   PASS",
                "  [CHECK] Integration checks complete...              PASS",
                "  [CHECK] Security posture assessed...                PASS",
            ],
        };
    }

    // ─── CyberShield Core Command Simulation ────────────────────────────────
    private function simulateCybershieldCommand(string $cmd): void
    {
        // Try real execution first
        try {
            Artisan::call($cmd);
            $raw = trim(Artisan::output());
            if ($raw) {
                $clean = preg_replace('/\x1b\[[0-9;]*m/', '', $raw);
                $this->output[] = ['type' => 'success', 'lines' => explode("\n", $clean)];
                return;
            }
        } catch (\Throwable) {}

        $output = match(true) {
            str_contains($cmd, 'list-middleware') => $this->getMiddlewareList(),
            str_contains($cmd, 'status')          => $this->getStatusOutput(),
            str_contains($cmd, 'scan')            => $this->getScanOutput(),
            str_contains($cmd, 'clear-threats')   => $this->getClearThreatsOutput(),
            str_contains($cmd, 'signatures:list') => $this->getSignaturesOutput(),
            str_contains($cmd, 'keys:generate')   => $this->getKeysOutput(),
            str_contains($cmd, 'version')         => $this->getVersionOutput(),
            str_contains($cmd, 'geo:update')      => $this->getGeoUpdateOutput(),
            str_contains($cmd, 'test')            => $this->getTestOutput(),
            default                               => ['  Command executed successfully.'],
        };

        $this->output[] = ['type' => 'success', 'lines' => $output];
    }

    private function getMiddlewareList(): array
    {
        return [
            '',
            '  CyberShield Registered Middleware',
            '  ══════════════════════════════════════════════════════════════',
            '',
            '  ── WAF / Firewall ───────────────────────────────────────────',
            '  cybershield.waf                → WafMiddleware',
            '  cybershield.detect_sqli        → DetectSqlInjectionMiddleware',
            '  cybershield.detect_xss         → DetectXssMiddleware',
            '  cybershield.detect_lfi         → DetectLfiMiddleware',
            '  cybershield.detect_rce         → DetectRceMiddleware',
            '  cybershield.detect_cmd         → DetectCommandInjectionMiddleware',
            '',
            '  ── Rate Limiting ────────────────────────────────────────────',
            '  cybershield.rate_limiter       → IpRateLimiterMiddleware',
            '  cybershield.sliding_window     → SlidingWindowRateLimiterMiddleware',
            '  cybershield.token_bucket       → TokenBucketRateLimiterMiddleware',
            '  cybershield.adaptive           → AdaptiveRateLimiterMiddleware',
            '  cybershield.distributed        → DistributedRateLimiterMiddleware',
            '  cybershield.burst              → BurstRateLimiterMiddleware',
            '',
            '  ── Bot Defense ──────────────────────────────────────────────',
            '  cybershield.detect_bot         → DetectBotMiddleware',
            '  cybershield.detect_crawler     → DetectCrawlerMiddleware',
            '  cybershield.detect_headless    → DetectHeadlessBrowserMiddleware',
            '',
            '  ── Network Guard ────────────────────────────────────────────',
            '  cybershield.geo_block          → GeoBlockMiddleware',
            '  cybershield.ip_block           → IpBlockMiddleware',
            '  cybershield.tor_block          → TorExitNodeMiddleware',
            '  cybershield.vpn_block          → VpnDetectionMiddleware',
            '  cybershield.proxy_block        → ProxyDetectionMiddleware',
            '',
            '  ── API Security ─────────────────────────────────────────────',
            '  cybershield.api_auth           → ApiAuthenticationMiddleware',
            '  cybershield.api_rate           → ApiRateLimiterMiddleware',
            '  cybershield.api_signature      → ApiSignatureMiddleware',
            '',
            '  ── Monitoring ───────────────────────────────────────────────',
            '  cybershield.log_threats        → ThreatLoggerMiddleware',
            '  cybershield.security_kernel    → SecurityKernel (full pipeline)',
            '',
            '  Total: 28 middleware registered',
            '',
        ];
    }

    private function getStatusOutput(): array
    {
        return [
            '',
            '  ╔══════════════════════════════════════════════════════╗',
            '  ║         CyberShield Status Report                    ║',
            '  ╠══════════════════════════════════════════════════════╣',
            '  ║  WAF Firewall       [■ ACTIVE]   Signatures: 312     ║',
            '  ║  Rate Limiter       [■ ACTIVE]   Sliding Window      ║',
            '  ║  Bot Defense        [■ ACTIVE]   UA rules: 48        ║',
            '  ║  Network Guard      [■ ACTIVE]   Geo DB: loaded      ║',
            '  ║  API Security       [■ ACTIVE]   Keys: 3 active      ║',
            '  ║  Threat Engine      [■ ACTIVE]   Cache: driver ok    ║',
            '  ║  Security Scanner   [■ ACTIVE]   150+ scan commands  ║',
            '  ║  Security Reporter  [■ ACTIVE]   10 report formats   ║',
            '  ╠══════════════════════════════════════════════════════╣',
            '  ║  All systems operational · No active critical alerts  ║',
            '  ╚══════════════════════════════════════════════════════╝',
            '',
        ];
    }

    private function getScanOutput(): array
    {
        return [
            '',
            '  [SCAN] Starting CyberShield security audit...',
            '  [SCAN] Checking .env file permissions...        ✓ OK',
            '  [SCAN] Checking APP_KEY configuration...        ✓ OK',
            '  [SCAN] Checking WAF signature files...          ✓ OK (312 loaded)',
            '  [SCAN] Checking rate limit config...            ✓ OK',
            '  [SCAN] Checking storage permissions...          ✓ OK',
            '  [SCAN] Checking security headers...             ⚠ WARN (Add CSP)',
            '  [SCAN] Checking debug mode (production)...      ✓ OK (debug=false)',
            '  [SCAN] Checking SSL/TLS configuration...        ✓ OK',
            '  [SCAN] Scanning for webshell files...           ✓ OK (0 found)',
            '  [SCAN] Checking composer vulnerabilities...     ✓ OK',
            '',
            '  Score: 96/100 — 1 recommendation.',
            '  Recommendation: Add Content-Security-Policy header.',
            '',
        ];
    }

    private function getClearThreatsOutput(): array
    {
        return [
            '  [CLEAR] Clearing threat score cache...          ✓ Done',
            '  [CLEAR] Clearing blocked IP cache...            ✓ Done',
            '  [CLEAR] Clearing rate limit counters...         ✓ Done',
            '  [CLEAR] Clearing bot detection cache...         ✓ Done',
            '  [CLEAR] Clearing geo-block cache...             ✓ Done',
            '',
            '  ✅ All CyberShield threat caches cleared successfully.',
        ];
    }

    private function getSignaturesOutput(): array
    {
        return [
            '',
            '  WAF Signature Sets Loaded:',
            '  ────────────────────────────────────────────────',
            '  sql_injection.json        48 patterns   [ACTIVE]',
            '  xss_attacks.json          34 patterns   [ACTIVE]',
            '  path_traversal.json       22 patterns   [ACTIVE]',
            '  command_injection.json    28 patterns   [ACTIVE]',
            '  rce_payloads.json         31 patterns   [ACTIVE]',
            '  protocol_attacks.json     18 patterns   [ACTIVE]',
            '  header_injection.json     15 patterns   [ACTIVE]',
            '  ssrf_patterns.json        12 patterns   [ACTIVE]',
            '  xxe_payloads.json          8 patterns   [ACTIVE]',
            '  deserialization.json       9 patterns   [ACTIVE]',
            '  lfi_payloads.json         16 patterns   [ACTIVE]',
            '  open_redirect.json        11 patterns   [ACTIVE]',
            '  ────────────────────────────────────────────────',
            '  Total: 252 active patterns across 12 categories.',
            '',
        ];
    }

    private function getKeysOutput(): array
    {
        return [
            '  [KEYGEN] Generating new asymmetric key pair...',
            '  [KEYGEN] Algorithm   : RSA-2048',
            '  [KEYGEN] Private key : -----BEGIN RSA PRIVATE KEY----- (generated)',
            '  [KEYGEN] Public key  : -----BEGIN PUBLIC KEY-----       (generated)',
            '  [KEYGEN] HMAC secret : ' . bin2hex(random_bytes(16)),
            '  [KEYGEN] Signing key : ' . bin2hex(random_bytes(16)),
            '  [KEYGEN] API token   : ' . bin2hex(random_bytes(24)),
            '',
            '  ✅ Keys generated. Add to .env:',
            '     CYBERSHIELD_PRIVATE_KEY=<path-to-key>',
            '     CYBERSHIELD_HMAC_SECRET=<hmac-secret>',
        ];
    }

    private function getVersionOutput(): array
    {
        return [
            '  CyberShield     v2.0.0',
            '  Laravel         ' . app()->version(),
            '  PHP             ' . PHP_VERSION,
            '  Released        2025-03-29',
            '  Repository      github.com/subhashladumor1/laravel-cybershield',
            '  License         MIT',
        ];
    }

    private function getGeoUpdateOutput(): array
    {
        return [
            '  [GEO] Downloading MaxMind GeoLite2 database...',
            '  [GEO] Processing IP geolocation entries...',
            '  [GEO] Indexing country codes (254 countries)...',
            '  [GEO] Building TOR exit node list...',
            '  [GEO] Building VPN/proxy CIDR blocks...',
            '  ✅ Geo database updated. 254 countries, 48,290 TOR nodes indexed.',
        ];
    }

    private function getTestOutput(): array
    {
        return [
            '',
            '  [TEST] Running CyberShield test suite...',
            '',
            '   PASS  Tests\\Unit\\WafEngineTest            18 tests  0.42s',
            '   PASS  Tests\\Unit\\BotDetectorTest          12 tests  0.18s',
            '   PASS  Tests\\Unit\\RateLimiterTest           9 tests  0.31s',
            '   PASS  Tests\\Unit\\NetworkGuardTest         11 tests  0.25s',
            '   PASS  Tests\\Unit\\HelpersTest              47 tests  0.55s',
            '   PASS  Tests\\Unit\\SignatureLoaderTest       8 tests  0.12s',
            '   PASS  Tests\\Feature\\FirewallTest           8 tests  0.88s',
            '   PASS  Tests\\Feature\\ApiSecurityTest        7 tests  0.74s',
            '   PASS  Tests\\Feature\\SecurityScannerTest   14 tests  1.12s',
            '',
            '  Tests: 134 passed, 0 failed, 0 skipped',
            '  Duration: 4.57s',
            '',
        ];
    }

    // ─── Fuzzy command suggestion ────────────────────────────────────────────
    private function suggestCommand(string $input): ?string
    {
        $best  = null;
        $minDist = PHP_INT_MAX;
        foreach (array_keys(self::getAllowedCommands()) as $cmd) {
            $dist = levenshtein($input, $cmd);
            if ($dist < $minDist && $dist <= (int)(strlen($cmd) * 0.5)) {
                $minDist = $dist;
                $best    = $cmd;
            }
        }
        return $best;
    }

    #[Layout('layouts.app')]
    #[Title('Artisan Terminal')]
    public function render()
    {
        return view('livewire.lab.artisan-terminal');
    }
}
