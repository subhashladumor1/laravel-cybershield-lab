<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use CyberShield\Models\ThreatLog;
use Illuminate\Support\Facades\Cache;

class BotLab extends Component
{
    public string  $selectedBot    = 'googlebot';
    public bool    $honeypotOn     = true;
    public bool    $headlessOn     = true;
    public bool    $scraperOn      = true;
    public bool    $uaCheckOn      = true;
    public ?array  $result         = null;
    public bool    $isLoading      = false;
    public array   $detectionLog   = [];
    public int     $botsBlocked    = 0;
    public int     $botsAllowed    = 0;

    public static array $bots = [
        'googlebot' => [
            'name'      => 'Googlebot 2.1',
            'type'      => 'Crawler / Search Engine',
            'ua'        => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
            'risk'      => 'low',
            'detection' => 'is_crawler',
            'helper'    => 'is_crawler()',
            'middleware'=> 'DetectCrawlerMiddleware',
            'desc'      => 'Legitimate search engine crawler. Identifiable by UA. CyberShield can allow or restrict crawlers.',
            'expected'  => 'Could be ALLOWED (whitelisted) or DETECTED depending on site policy.',
        ],
        'scraperbot' => [
            'name'      => 'Python Scraper',
            'type'      => 'Data Scraper',
            'ua'        => 'python-requests/2.31.0',
            'risk'      => 'high',
            'detection' => 'is_scraper',
            'helper'    => 'is_scraper()',
            'middleware'=> 'DetectScraperBotMiddleware',
            'desc'      => 'Automated data harvesting bot using the Python requests library. Often used for price scraping, content theft.',
            'expected'  => 'BLOCKED — UA matches scraper signatures.',
        ],
        'headless_chrome' => [
            'name'      => 'HeadlessChrome 110',
            'type'      => 'Headless Browser',
            'ua'        => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 HeadlessChrome/110.0.0.0 Safari/537.36',
            'risk'      => 'critical',
            'detection' => 'is_headless',
            'helper'    => 'is_headless()',
            'middleware'=> 'DetectHeadlessBrowserMiddleware',
            'desc'      => 'ChromeDP/Puppeteer-driven headless browser for automated scraping and form submission. Contains "HeadlessChrome" in UA.',
            'expected'  => 'BLOCKED — "HeadlessChrome" keyword detected in UA string.',
        ],
        'curl_attack' => [
            'name'      => 'cURL Attack Tool',
            'type'      => 'Automation / API Abuse',
            'ua'        => 'curl/8.2.0',
            'risk'      => 'high',
            'detection' => 'is_curl',
            'helper'    => 'is_curl()',
            'middleware'=> 'DetectAutomationScriptMiddleware',
            'desc'      => 'Raw cURL requests — a hallmark of automated scripts, vulnerability scanners, and API abuse.',
            'expected'  => 'BLOCKED — cURL UA prefix detected.',
        ],
        'sqlmap_scanner' => [
            'name'      => 'SQLMap Scanner',
            'type'      => 'Malicious Hacking Tool',
            'ua'        => 'sqlmap/1.7.8#stable (https://sqlmap.org)',
            'risk'      => 'critical',
            'detection' => 'is_malicious_user_agent',
            'helper'    => 'is_malicious_user_agent()',
            'middleware'=> 'DetectMaliciousUserAgentMiddleware',
            'desc'      => 'SQLMap is an open-source SQL injection testing tool. Its UA is explicitly blacklisted in CyberShield.',
            'expected'  => 'BLOCKED IMMEDIATELY — Matches known attack tool signature.',
        ],
        'nikto_scanner' => [
            'name'      => 'Nikto Web Scanner',
            'type'      => 'Vulnerability Scanner',
            'ua'        => 'Mozilla/5.00 (Nikto/2.1.6) (Evasions:None) (Test:Port Check)',
            'risk'      => 'critical',
            'detection' => 'is_malicious_user_agent',
            'helper'    => 'is_malicious_user_agent()',
            'middleware'=> 'DetectMaliciousUserAgentMiddleware',
            'desc'      => 'Nikto is a web server vulnerability scanner. Automatically detected and blocked by CyberShield.',
            'expected'  => 'BLOCKED IMMEDIATELY — Nikto signature in User-Agent.',
        ],
        'selenium_bot' => [
            'name'      => 'Selenium WebDriver',
            'type'      => 'Browser Automation',
            'ua'        => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36',
            'risk'      => 'high',
            'detection' => 'is_selenium',
            'helper'    => 'is_selenium()',
            'middleware'=> 'DetectAutomationScriptMiddleware',
            'desc'      => 'Selenium-driven WebDriver. The UA looks like a real browser but CyberShield checks for WebDriver headers.',
            'expected'  => 'May PASS UA check but BLOCKED via X-WebDriver header detection.',
        ],
        'honeypot_filler' => [
            'name'      => 'Form Filling Bot',
            'type'      => 'Spam / Form Abuse Bot',
            'ua'        => 'Mozilla/5.0 FormFillerPro/2.0',
            'risk'      => 'medium',
            'detection' => 'honeypot_triggered',
            'helper'    => '@secureHoneypot directive',
            'middleware'=> 'DetectHoneypotBotMiddleware',
            'desc'      => 'Spam bot that fills out all form fields, including hidden honeypot fields that humans never touch.',
            'expected'  => 'BLOCKED — Honeypot field was filled, exposing bot behavior.',
        ],
    ];

    public function selectBot(string $key): void
    {
        $this->selectedBot = $key;
        $this->result      = null;
        $this->detectionLog = [];
    }

    public function simulate(): void
    {
        $this->isLoading = true;
        $bot = self::$bots[$this->selectedBot] ?? self::$bots['googlebot'];
        $ua  = $bot['ua'];

        $this->detectionLog = [
            "[" . now()->format('H:i:s') . "] Incoming request simulation",
            "[" . now()->format('H:i:s') . "] User-Agent: " . $ua,
            "[" . now()->format('H:i:s') . "] Detection method: {$bot['detection']}",
            "[" . now()->format('H:i:s') . "] Running CyberShield bot detection pipeline...",
        ];

        // Run detection logic
        $uaLower   = strtolower($ua);
        $detected  = false;
        $reason    = '';
        $defenseOn = true;

        // Check if the relevant defense is enabled
        if ($this->selectedBot === 'honeypot_filler' && !$this->honeypotOn) {
            $defenseOn = false;
        } elseif (in_array($this->selectedBot, ['headless_chrome']) && !$this->headlessOn) {
            $defenseOn = false;
        } elseif (in_array($this->selectedBot, ['scraperbot', 'curl_attack']) && !$this->scraperOn) {
            $defenseOn = false;
        } elseif (in_array($this->selectedBot, ['sqlmap_scanner', 'nikto_scanner', 'selenium_bot', 'googlebot']) && !$this->uaCheckOn) {
            $defenseOn = false;
        }

        if (!$defenseOn) {
            $this->detectionLog[] = "[" . now()->format('H:i:s') . "] DEFENSE DISABLED → Bot passed through undetected!";
            $this->result = [
                'type'       => 'danger',
                'headline'   => '⚠ Bot NOT Detected — Defense Disabled',
                'message'    => "The {$bot['type']} bot was NOT detected because the relevant CyberShield defense is toggled OFF. The bot successfully accessed your application.",
                'bot'        => $bot,
                'lesson'     => "Enable the `{$bot['middleware']}` or update your config to protect against this bot type.",
            ];
            $this->botsAllowed++;
        } else {
            // Simulate detection using same logic as helpers
            if ($bot['detection'] === 'is_malicious_user_agent') {
                $detected = (bool) preg_match('/(nikto|acunetix|sqlmap|dirbuster|metasploit|burp|nessus|zgrab)/i', $ua);
                $reason   = 'User-Agent matches known malicious tool signature';
            } elseif ($bot['detection'] === 'is_headless') {
                $detected = str_contains($uaLower, 'headless');
                $reason   = '"headless" keyword found in User-Agent string';
            } elseif ($bot['detection'] === 'is_scraper') {
                $detected = (bool) preg_match('/(scraper|guzzle|curl|wget|python|php|ruby|java|go-http-client)/i', $ua);
                $reason   = 'User-Agent matches scraper/automation tool pattern';
            } elseif ($bot['detection'] === 'is_crawler') {
                $detected = (bool) preg_match('/(bot|google|bing|yahoo|slurp|crawler|spider|archive)/i', $ua);
                $reason   = 'User-Agent matches known crawler/spider pattern';
            } elseif ($bot['detection'] === 'is_curl') {
                $detected = str_starts_with($uaLower, 'curl');
                $reason   = 'User-Agent starts with "curl" — raw automation detected';
            } elseif ($bot['detection'] === 'is_selenium') {
                $detected = str_contains($uaLower, 'selenium') || str_contains($uaLower, 'webdriver');
                $reason   = 'WebDriver header or Selenium UA detected (in real scenario checks X-WebDriver header)';
            } elseif ($bot['detection'] === 'honeypot_triggered') {
                $detected = true;
                $reason   = 'Honeypot form field was filled — only bots fill hidden fields';
            }

            if ($detected) {
                $this->detectionLog[] = "[" . now()->format('H:i:s') . "] ✓ BOT DETECTED — Reason: {$reason}";
                $this->detectionLog[] = "[" . now()->format('H:i:s') . "] Blocking with HTTP 403...";
                $this->result = [
                    'type'       => 'blocked',
                    'headline'   => '✓ Bot Detected & Blocked',
                    'message'    => "{$bot['type']} identified. Reason: {$reason}. The request was rejected with HTTP 403.",
                    'bot'        => $bot,
                    'lesson'     => "The helper `{$bot['helper']}` returns true for this request. Use `{$bot['middleware']}` on your routes to block automatically.",
                ];
                $this->botsBlocked++;

                ThreatLog::create([
                    'ip'          => request()->ip(),
                    'threat_type' => 'BOT_DETECTED_' . strtoupper(str_replace(' ', '_', $bot['type'])),
                    'severity'    => $bot['risk'] === 'critical' ? 'critical' : ($bot['risk'] === 'high' ? 'high' : 'medium'),
                    'details'     => [
                        'module'     => 'BOT',
                        'bot_name'   => $bot['name'],
                        'bot_ua'     => $ua,
                        'detection'  => $bot['detection'],
                        'middleware' => $bot['middleware'],
                        'user_agent' => request()->userAgent(),
                        'request_method' => 'POST',
                        'request_uri' => '/lab/bot',
                    ],
                ]);
            } else {
                $this->detectionLog[] = "[" . now()->format('H:i:s') . "] ! Could not fully confirm bot — may need additional signals";
                $this->result = [
                    'type'       => 'warning',
                    'headline'   => 'Bot May Have Passed Detection',
                    'message'    => "The detection did not trigger with certainty. This may indicate the UA mimics a real browser. Additional behavioral signals (mouse movement, timing, JavaScript challenges) would be needed.",
                    'bot'        => $bot,
                    'lesson'     => "For sophisticated bots, combine `{$bot['middleware']}` with `DetectBrowserFingerprintMismatchMiddleware` and `DetectMouseMovementAbsenceMiddleware`.",
                ];
                $this->botsAllowed++;
            }
        }

        $this->isLoading = false;
    }

    public function render()
    {
        return view('livewire.lab.bot-lab')
            ->layout('layouts.app')
            ->title('Bot Defense Lab');
    }
}
