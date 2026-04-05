@section('page-title', 'API Reference')

<div class="anim-fade-up">
    <div class="page-header">
        <h1>📖 CyberShield API Reference</h1>
        <p class="subtitle">Complete reference for all 200+ helper functions, Blade directives, and middleware. Click any function to see its signature and usage example.</p>
    </div>

    <!-- Tabs -->
    <div class="tabs">
        <button wire:click="setTab('helpers')"  class="tab-btn {{ $activeTab === 'helpers'    ? 'active' : '' }}">Helper Functions</button>
        <button wire:click="setTab('directives')" class="tab-btn {{ $activeTab === 'directives' ? 'active' : '' }}">Blade Directives</button>
        <button wire:click="setTab('middleware')" class="tab-btn {{ $activeTab === 'middleware' ? 'active' : '' }}">Middleware Catalog</button>
        <button wire:click="setTab('config')"   class="tab-btn {{ $activeTab === 'config'     ? 'active' : '' }}">Config Reference</button>
    </div>

    <!-- ============================================================
         HELPERS TAB
    ============================================================ -->
    @if($activeTab === 'helpers')
        @php
            $groups = [
                '🌐 Network & IP Intelligence' => [
                    ['fn' => 'secure_ip()',                  'returns' => 'string', 'desc' => 'Get the sanitized client IP address via Laravel Request.'],
                    ['fn' => 'real_ip()',                    'returns' => 'string', 'desc' => 'Resolve the real IP even behind proxies (X-Forwarded-For, X-Real-IP).'],
                    ['fn' => 'ip_country_code()',            'returns' => 'string', 'desc' => 'Get ISO-2 country code from Cloudflare or custom headers.'],
                    ['fn' => 'ip_threat_score(?string $ip)', 'returns' => 'int',    'desc' => 'Get the computed threat score (0–100) for an IP from the cache.'],
                    ['fn' => 'ip_reputation(?string $ip)',   'returns' => 'string', 'desc' => 'Classify IP as: Trusted, Neutral, Suspicious, or Malicious.'],
                    ['fn' => 'ip_is_blacklisted(?string $ip)','returns'=> 'bool',  'desc' => 'Check if an IP is in the block cache.'],
                    ['fn' => 'ip_is_whitelisted(?string $ip)','returns'=> 'bool',  'desc' => 'Check if an IP is in the configured whitelist.'],
                    ['fn' => 'is_tor_ip(?string $ip)',       'returns' => 'bool',   'desc' => 'Check TOR exit node list (cached 12h). Queries torproject.org.'],
                    ['fn' => 'is_vpn_ip(?string $ip)',       'returns' => 'bool',   'desc' => 'Detect VPN usage from user-agent and network markers.'],
                    ['fn' => 'is_proxy_ip()',                'returns' => 'bool',   'desc' => 'Detect proxy headers: Via, X-Forwarded-For, X-Proxy-ID, Forwarded.'],
                    ['fn' => 'is_private_ip(?string $ip)',   'returns' => 'bool',   'desc' => 'Returns true for RFC1918 private ranges (192.168.x.x, 10.x.x.x).'],
                    ['fn' => 'check_ip_range(string $ip, $range)', 'returns' => 'bool', 'desc' => 'Check if IP is within a CIDR range or an array of ranges.'],
                    ['fn' => 'get_ip_velocity(?string $ip)', 'returns' => 'int',    'desc' => 'Get requests-per-minute count for an IP from velocity cache.'],
                    ['fn' => 'block_current_ip(string $reason)', 'returns' => 'void', 'desc' => 'Add current IP to the block cache for 7 days.'],
                    ['fn' => 'whitelist_current_ip()',       'returns' => 'void',   'desc' => 'Remove current IP from the block cache.'],
                ],
                '🤖 Bot & Automation Detection' => [
                    ['fn' => 'is_bot()',                   'returns' => 'bool', 'desc' => 'Detect known bot user-agents: curl, python, selenium, headless, etc.'],
                    ['fn' => 'is_crawler()',               'returns' => 'bool', 'desc' => 'Detect search engine crawlers: Googlebot, Bingbot, Slurp, etc.'],
                    ['fn' => 'is_scraper()',               'returns' => 'bool', 'desc' => 'Detect scraper tools: python-requests, Guzzle, wget, curl, Ruby.'],
                    ['fn' => 'is_headless()',              'returns' => 'bool', 'desc' => 'Detect HeadlessChrome in UA or X-Puppeteer-Request header.'],
                    ['fn' => 'is_selenium()',              'returns' => 'bool', 'desc' => 'Detect Selenium by "selenium" or "webdriver" in user-agent string.'],
                    ['fn' => 'is_malicious_user_agent()', 'returns' => 'bool', 'desc' => 'Detect known attack tools: nikto, sqlmap, acunetix, burp, nessus.'],
                    ['fn' => 'is_human()',                 'returns' => 'bool', 'desc' => 'Inverse of is_bot(). True if no bot signatures detected.'],
                    ['fn' => 'get_bot_type()',             'returns' => 'string','desc'=> 'Returns: Crawler, Scraper, Other Bot, or Human.'],
                    ['fn' => 'detect_automation()',        'returns' => 'bool', 'desc' => 'Check for automation: X-Automation-Id header, headless, or selenium.'],
                    ['fn' => 'get_request_fingerprint()',  'returns' => 'string','desc'=> 'SHA-256 fingerprint of UA + IP + Accept-Language + Accept-Encoding.'],
                ],
                '🔐 Cryptography & Tokens' => [
                    ['fn' => 'secure_encrypt(mixed $data)',        'returns' => 'string', 'desc' => 'Encrypt any value using Laravel\'s Crypt facade (AES-256-CBC).'],
                    ['fn' => 'secure_decrypt(string $data)',       'returns' => 'mixed',  'desc' => 'Decrypt a string encrypted by secure_encrypt(). Returns null on failure.'],
                    ['fn' => 'secure_hash(string $data)',          'returns' => 'string', 'desc' => 'HMAC-SHA256 hash using the APP_KEY as secret.'],
                    ['fn' => 'secure_hmac(string $data, ?key)',    'returns' => 'string', 'desc' => 'HMAC-SHA256 with custom key or APP_KEY if null.'],
                    ['fn' => 'secure_verify_hmac(string, string)', 'returns' => 'bool',   'desc' => 'Constant-time comparison of HMAC signature.'],
                    ['fn' => 'secure_token()',                     'returns' => 'string', 'desc' => 'Generate cryptographically secure 64-character hex token.'],
                    ['fn' => 'secure_random_string(int $len)',     'returns' => 'string', 'desc' => 'Generate a random alphanumeric string using Str::random.'],
                    ['fn' => 'secure_uuid()',                      'returns' => 'string', 'desc' => 'Generate a UUID v4 string.'],
                    ['fn' => 'secure_password_hash(string $pwd)',  'returns' => 'string', 'desc' => 'Hash a password using Laravel Hash::make (bcrypt/argon2).'],
                    ['fn' => 'secure_password_verify(string, string)', 'returns' => 'bool', 'desc' => 'Verify a password against a stored Hash::make hash.'],
                    ['fn' => 'secure_constant_time_compare(string, string)', 'returns' => 'bool', 'desc' => 'Timing-safe string comparison using hash_equals.'],
                ],
                '🎭 Data Masking (PII Protection)' => [
                    ['fn' => 'mask_email(string $email)',   'returns' => 'string', 'desc' => 'Mask email: jo*****@gmail.com. First 2 chars preserved.'],
                    ['fn' => 'mask_phone(string $p)',       'returns' => 'string', 'desc' => 'Mask phone: 123****56. Strips non-numeric and masks middle.'],
                    ['fn' => 'mask_card(string $c)',        'returns' => 'string', 'desc' => 'Mask credit card: ************3456. Last 4 digits preserved.'],
                    ['fn' => 'mask_name(string $n)',        'returns' => 'string', 'desc' => 'Mask full name: J*** D***. First character of each word preserved.'],
                    ['fn' => 'mask_ip(?string $ip)',        'returns' => 'string', 'desc' => 'Mask last octets: 192.168.***.***. Supports IPv6.'],
                    ['fn' => 'mask_token(string $t)',       'returns' => 'string', 'desc' => 'Mask token: abc123**********xyz789. First/last 6 chars preserved.'],
                    ['fn' => 'mask_ssn(string $s)',         'returns' => 'string', 'desc' => 'Mask SSN: ***-**-4321. Last 4 digits visible.'],
                ],
                '🛡 Threat & Risk Management' => [
                    ['fn' => 'get_threat_score(?string $ip)', 'returns' => 'int',    'desc' => 'Alias for ip_threat_score(). Returns 0–100.'],
                    ['fn' => 'get_risk_level(?string $ip)',   'returns' => 'string', 'desc' => 'Returns: Trusted, Neutral, Suspicious, or Malicious.'],
                    ['fn' => 'is_high_risk(?string $ip)',     'returns' => 'bool',   'desc' => 'True if threat score >= 75.'],
                    ['fn' => 'is_threat_active()',            'returns' => 'bool',   'desc' => 'True if global attack mode is enabled in cache.'],
                    ['fn' => 'log_threat_event(string, array)','returns'=> 'void',  'desc' => 'Log a security event to file and DB threat_logs table.'],
                ],
                '🧹 Sanitization & Detection' => [
                    ['fn' => 'sanitize_html(string $h)',    'returns' => 'string', 'desc' => 'Strip disallowed HTML. Permits: p, br, b, i, strong, em, ul, li.'],
                    ['fn' => 'sanitize_string(string $s)',  'returns' => 'string', 'desc' => 'PHP htmlspecialchars with ENT_QUOTES and UTF-8.'],
                    ['fn' => 'sanitize_url(string $u)',     'returns' => 'string', 'desc' => 'FILTER_SANITIZE_URL. Remove illegal chars from URL.'],
                    ['fn' => 'sanitize_filename(string $f)','returns' => 'string', 'desc' => 'Allow only safe filename chars: a-z A-Z 0-9 . _ -'],
                    ['fn' => 'is_sql_injection(string $s)', 'returns' => 'bool',   'desc' => 'Detect UNION SELECT, INSERT INTO, sleep(), DROP TABLE patterns.'],
                    ['fn' => 'is_xss_injection(string $s)', 'returns' => 'bool',   'desc' => 'Detect <script, onerror=, javascript:, eval(, expression( patterns.'],
                    ['fn' => 'is_rce_injection(string $s)', 'returns' => 'bool',   'desc' => 'Detect eval(, shell_exec(, system(, passthru(, exec( patterns.'],
                    ['fn' => 'is_lfi_injection(string $s)', 'returns' => 'bool',   'desc' => 'Detect ../  ..\\ /etc/passwd /etc/shadow C:\\Windows\\ patterns.'],
                    ['fn' => 'is_malicious_payload(string $p)', 'returns' => 'bool', 'desc' => 'Combined check: SQL + XSS + RCE + LFI. Returns true if any match.'],
                ],
                '📁 File Security' => [
                    ['fn' => 'scan_file_malware(string $f)', 'returns' => 'string', 'desc' => 'Scan file for PHP shells: eval, base64_decode, shell_exec, system, passthru.'],
                    ['fn' => 'get_real_mime(string $f)',     'returns' => '?string', 'desc' => 'Get actual MIME via finfo (not extension). Returns null if not found.'],
                    ['fn' => 'is_php_executable(string $f)','returns' => 'bool',   'desc' => 'Check for <?php, <?=, or array_map("system",...) in file content.'],
                    ['fn' => 'get_file_entropy(string $f)', 'returns' => 'float',  'desc' => 'Calculate Shannon entropy of file. High entropy may indicate obfuscation.'],
                    ['fn' => 'is_image_safe(string $f)',    'returns' => 'bool',   'desc' => 'Validate MIME starts with "image/" and contains no PHP/JS code.'],
                    ['fn' => 'is_file_secure(string $f)',   'returns' => 'bool',   'desc' => 'Combined: not PHP-executable && malware scan = Clean.'],
                ],
                '🔒 Auth & Session' => [
                    ['fn' => 'is_trusted_device()',    'returns' => 'bool',  'desc' => 'Check cs_device_id cookie exists and is confirmed in cache.'],
                    ['fn' => 'get_session_entropy()',  'returns' => 'float', 'desc' => 'Calculate Shannon entropy of the current session ID.'],
                    ['fn' => 'is_session_hijacked()',  'returns' => 'bool',  'desc' => 'Compares current IP & UA with login session. True if mismatch.'],
                ],
            ];
        @endphp

        <div style="display: flex; flex-direction: column; gap: 28px;">
            @foreach($groups as $groupName => $helpers)
                <div>
                    <div class="section-title">{{ $groupName }}</div>
                    <div class="grid-auto" style="gap: 12px;">
                        @foreach($helpers as $helper)
                            <div class="ref-card">
                                <div class="ref-card-header">
                                    <code class="fn-name">{{ $helper['fn'] }}</code>
                                    <span class="badge badge-info" style="font-size:9px;">{{ $helper['returns'] }}</span>
                                </div>
                                <div class="ref-card-body">
                                    <div class="fn-desc">{{ $helper['desc'] }}</div>
                                    <div class="code-snippet">{{ str_contains($helper['fn'], '(') ? 
                                        '$result = ' . $helper['fn'] : 
                                        '$result = ' . $helper['fn'] . ';'
                                    }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- ============================================================
         DIRECTIVES TAB
    ============================================================ -->
    @if($activeTab === 'directives')
        @php
            $directiveGroups = [
                '🔐 Authentication Directives' => [
                    ['name' => '@secureAuth ... @endsecureAuth',     'type' => 'if', 'desc' => 'Render content only for authenticated users with valid session integrity.'],
                    ['name' => '@secureGuest ... @endsecureGuest',   'type' => 'if', 'desc' => 'Content visible only to unauthenticated guests.'],
                    ['name' => '@secureAdmin ... @endsecureAdmin',   'type' => 'if', 'desc' => 'Restrict to users with role === "admin".'],
                    ['name' => '@secureRole($role)',                  'type' => 'if', 'desc' => 'Check if authenticated user has the given role.'],
                    ['name' => '@secureVerified ... @endsecureVerified', 'type' => 'if', 'desc' => 'Only for email-verified users (hasVerifiedEmail).'],
                    ['name' => '@secureTrustedDevice',               'type' => 'if', 'desc' => 'True when the device cookie matches a trusted device in cache.'],
                    ['name' => '@secureNewDevice',                   'type' => 'if', 'desc' => 'Inverse of @secureTrustedDevice. Show alerts for new devices.'],
                    ['name' => '@secureCaptchaRequired',             'type' => 'if', 'desc' => 'Show CAPTCHA when session has the captcha_required flag.'],
                ],
                '🌐 Request Security Directives' => [
                    ['name' => '@secureBot ... @endsecureBot',       'type' => 'if', 'desc' => 'Content shown only to bots (via is_bot()). Use to serve bot-specific content.'],
                    ['name' => '@secureCrawler',                     'type' => 'if', 'desc' => 'True when request is from a crawler (is_crawler()).'],
                    ['name' => '@secureHttps',                       'type' => 'if', 'desc' => 'Content only shown over HTTPS connections (is_ssl_active()).'],
                    ['name' => '@secureProxy',                       'type' => 'if', 'desc' => 'Show proxy-related warnings when proxy headers detected.'],
                    ['name' => '@secureTor',                         'type' => 'if', 'desc' => 'Content shown only when request comes from a TOR exit node.'],
                    ['name' => '@secureTrustedIp',                   'type' => 'if', 'desc' => 'Content shown only for whitelisted IP addresses.'],
                    ['name' => '@secureHighRiskIp',                  'type' => 'if', 'desc' => 'True when ip_threat_score() >= 75.'],
                    ['name' => '@secureCountry($code)',              'type' => 'if', 'desc' => 'True when ip_country_code() matches the given ISO-2 code.'],
                ],
                '⚠ Threat Detection Directives' => [
                    ['name' => '@secureThreat',                      'type' => 'if', 'desc' => 'True when threat score > 0.'],
                    ['name' => '@secureThreatHigh',                  'type' => 'if', 'desc' => 'True when threat score > 70.'],
                    ['name' => '@secureThreatCritical',              'type' => 'if', 'desc' => 'True when threat score > 90.'],
                    ['name' => '@secureAttackDetected',              'type' => 'if', 'desc' => 'True when global attack mode is active in cache.'],
                    ['name' => '@secureBlockedIp',                   'type' => 'if', 'desc' => 'True when current IP is in the blocked cache.'],
                    ['name' => '@secureFloodAttack',                 'type' => 'if', 'desc' => 'True when ip_velocity > 200 requests/min.'],
                    ['name' => '@secureSecurityAlert',               'type' => 'if', 'desc' => 'True when "security_alert" key exists in cache.'],
                ],
                '🎭 Data Masking Directives' => [
                    ['name' => '@secureMaskEmail($email)',           'type' => 'echo', 'desc' => 'Echo a masked email: jo*****@gmail.com'],
                    ['name' => '@secureMaskPhone($phone)',           'type' => 'echo', 'desc' => 'Echo a masked phone: 123****89'],
                    ['name' => '@secureMaskCard($card)',             'type' => 'echo', 'desc' => 'Echo a masked card: ************3456'],
                    ['name' => '@secureMaskToken($token)',           'type' => 'echo', 'desc' => 'Echo a masked token showing first/last 6 chars.'],
                    ['name' => '@secureEncrypt($data)',              'type' => 'echo', 'desc' => 'Echo an encrypted string using Laravel Crypt.'],
                    ['name' => '@secureDecrypt($encrypted)',         'type' => 'echo', 'desc' => 'Echo a decrypted value. Returns null on failure.'],
                    ['name' => '@secureSafeOutput($html)',           'type' => 'echo', 'desc' => 'Echo sanitized HTML (strip_tags with allowed tags).'],
                    ['name' => '@secureSanitize($value)',            'type' => 'echo', 'desc' => 'Echo htmlspecialchars-encoded string (XSS-safe output).'],
                ],
                '🛡 Form Security Directives' => [
                    ['name' => '@secureTokenField',                  'type' => 'echo', 'desc' => 'Echo a CSRF token hidden input field.'],
                    ['name' => '@secureNonceField',                  'type' => 'echo', 'desc' => 'Echo a nonce token field: <input name="_nonce" value="...">'],
                    ['name' => '@secureHoneypot',                    'type' => 'echo', 'desc' => 'Echo a hidden honeypot div+input. Bots fill it, humans don\'t.'],
                    ['name' => '@secureSecureInput($name)',          'type' => 'echo', 'desc' => 'Echo a secured input with the class "secure-input".'],
                ],
                '🖥 UI Security Directives' => [
                    ['name' => '@secureAdminPanel',                  'type' => 'if', 'desc' => 'Show content only in admin panel context (role=admin).'],
                    ['name' => '@secureInternal',                    'type' => 'if', 'desc' => 'True for private/LAN IP addresses (is_private_ip()).'],
                    ['name' => '@secureDebugMode',                   'type' => 'if', 'desc' => 'Show debug content only when APP_DEBUG is true.'],
                    ['name' => '@secureProductionOnly',              'type' => 'if', 'desc' => 'Show content only in APP_ENV=production.'],
                    ['name' => '@secureSecurityWarning',             'type' => 'if', 'desc' => 'True when threat score > 30. Use to show warning banners.'],
                    ['name' => '@secureSystemSafe',                  'type' => 'if', 'desc' => 'True when no active threat (inverse of @secureAttackDetected).'],
                ],
            ];
        @endphp

        <div style="display: flex; flex-direction: column; gap: 24px;">
            @foreach($directiveGroups as $groupName => $directives)
                <div>
                    <div class="section-title">{{ $groupName }}</div>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        @foreach($directives as $dir)
                            <div class="ref-card" style="display:flex; align-items:center; gap: 16px; padding: 0;">
                                <div class="ref-card-header" style="flex-shrink:0; width: 320px; border-bottom:none; border-right: 1px solid var(--border-subtle); border-radius:0;">
                                    <code class="fn-name" style="font-size:12px;">{{ $dir['name'] }}</code>
                                    <span class="badge {{ $dir['type'] === 'if' ? 'badge-info' : 'badge-warning' }}" style="font-size:9px;">{{ strtoupper($dir['type']) }}</span>
                                </div>
                                <div class="ref-card-body" style="padding: 10px 12px; flex:1;">
                                    <div class="fn-desc" style="margin:0;">{{ $dir['desc'] }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- ============================================================
         MIDDLEWARE TAB
    ============================================================ -->
    @if($activeTab === 'middleware')
        @php
            $middlewareGroups = [
                '🔥 WAF & Firewall' => [
                    ['m' => 'cybershield.firewall',              'class' => 'FirewallMiddleware',              'desc' => 'Full WAF pipeline via SecurityKernel.'],
                    ['m' => 'cybershield.detect_sql_injection',  'class' => 'DetectSqlInjectionMiddleware',    'desc' => 'Block SQL injection patterns (UNION SELECT, DROP TABLE, etc.).'],
                    ['m' => 'cybershield.detect_xss_attack',     'class' => 'DetectXssAttackMiddleware',       'desc' => 'Block XSS: <script>, onerror=, javascript: payloads.'],
                    ['m' => 'cybershield.detect_command_injection','class' => 'DetectCommandInjectionMiddleware','desc'=> 'Block OS command injection: ;cat, &&, | nc.'],
                    ['m' => 'cybershield.detect_path_traversal', 'class' => 'DetectPathTraversalAttackMiddleware','desc'=> 'Block LFI: ../../../../etc/passwd patterns.'],
                    ['m' => 'cybershield.detect_malicious_payload','class'=> 'DetectMaliciousPayloadMiddleware','desc'=> 'Combined payload scanner (SQL+XSS+RCE+LFI).'],
                    ['m' => 'cybershield.detect_exploit_signature','class'=> 'DetectExploitSignatureMiddleware','desc'=> 'Block known exploit signatures from signature DB.'],
                    ['m' => 'cybershield.validate_request_headers','class'=> 'ValidateRequestHeadersMiddleware','desc'=> 'Validate HTTP request headers for suspicious content.'],
                ],
                '⚡ Rate Limiting' => [
                    ['m' => 'cybershield.ip_rate_limiter',       'class' => 'IpRateLimiterMiddleware',         'desc' => 'Fixed-window IP-based rate limiting.'],
                    ['m' => 'cybershield.sliding_window_rate_limiter','class'=>'SlidingWindowRateLimiterMiddleware','desc'=>'Sliding-window rate limiting (most precise).'],
                    ['m' => 'cybershield.token_bucket_rate_limiter','class'=>'TokenBucketRateLimiterMiddleware','desc' => 'Token-bucket allowing burst with long-term limits.'],
                    ['m' => 'cybershield.adaptive_rate_limiter', 'class' => 'AdaptiveRateLimiterMiddleware',   'desc' => 'Dynamic rate limiting based on server load + threat score.'],
                    ['m' => 'cybershield.login_rate_limiter',    'class' => 'LoginRateLimiterMiddleware',      'desc' => 'Specific limits for login endpoint abuse.'],
                    ['m' => 'cybershield.api_rate_limiter',      'class' => 'ApiRateLimiterMiddleware',        'desc' => 'API-scoped rate limiting with key tracking.'],
                    ['m' => 'cybershield.burst_rate_limiter',    'class' => 'BurstRateLimiterMiddleware',      'desc' => 'Block traffic burst exceeding configurable burst threshold.'],
                ],
                '🤖 Bot Detection' => [
                    ['m' => 'cybershield.detect_bot',            'class' => 'DetectBotMiddleware',             'desc' => 'Generic bot detection via UA analysis.'],
                    ['m' => 'cybershield.detect_crawlers',       'class' => 'DetectCrawlerMiddleware',         'desc' => 'Detect search engine crawlers.'],
                    ['m' => 'cybershield.detect_scraper_bot',    'class' => 'DetectScraperBotMiddleware',      'desc' => 'Detect data scraping tools (python, curl, wget).'],
                    ['m' => 'cybershield.detect_headless_browser','class'=> 'DetectHeadlessBrowserMiddleware', 'desc' => 'Block headless Chrome/Firefox (Puppeteer/Playwright).'],
                    ['m' => 'cybershield.detect_malicious_ua',   'class' => 'DetectMaliciousUserAgentMiddleware','desc'=> 'Block known attack tool UAs: sqlmap, nikto, acunetix.'],
                    ['m' => 'cybershield.detect_honeypot_bot',   'class' => 'DetectHoneypotBotMiddleware',     'desc' => 'Block bots that fill honeypot form fields.'],
                    ['m' => 'cybershield.detect_spam_bot',       'class' => 'DetectSpamBotMiddleware',         'desc' => 'Detect comment/form spam bots.'],
                    ['m' => 'cybershield.detect_login_bot',      'class' => 'DetectLoginBotMiddleware',        'desc' => 'Detect login page automation bots.'],
                ],
                '🌍 Network Security' => [
                    ['m' => 'cybershield.detect_country_block',  'class' => 'DetectCountryBlockMiddleware',    'desc' => 'Geo-block countries in the blocked_countries config list.'],
                    ['m' => 'cybershield.detect_tor_network',    'class' => 'DetectTorNetworkMiddleware',      'desc' => 'Block TOR exit nodes by checking TOR exit lists.'],
                    ['m' => 'cybershield.detect_vpn_network',    'class' => 'DetectVpnNetworkMiddleware',      'desc' => 'Detect and optionally block VPN providers.'],
                    ['m' => 'cybershield.detect_proxy_network',  'class' => 'DetectProxyNetworkMiddleware',    'desc' => 'Block requests routed through anonymous proxies.'],
                    ['m' => 'cybershield.block_blacklisted_ip',  'class' => 'BlockBlacklistedIpMiddleware',    'desc' => 'Block IPs found in the cybershield_blocked_ips table.'],
                    ['m' => 'cybershield.allow_whitelisted_ip',  'class' => 'AllowWhitelistedIpMiddleware',    'desc' => 'Allow-list override: skip checks for trusted IPs.'],
                    ['m' => 'cybershield.detect_ip_flood',       'class' => 'DetectIpFloodMiddleware',         'desc' => 'Detect and block IP-level flood attacks.'],
                    ['m' => 'cybershield.detect_ip_reputation',  'class' => 'DetectIpReputationMiddleware',    'desc' => 'Block IPs with high threat scores from threat intel.'],
                ],
                '🔑 Authentication' => [
                    ['m' => 'cybershield.enforce_2fa',           'class' => 'EnforceTwoFactorAuthMiddleware',  'desc' => 'Force 2FA verification before accessing protected routes.'],
                    ['m' => 'cybershield.enforce_session_timeout','class'=> 'EnforceSessionTimeoutMiddleware', 'desc' => 'Terminate idle sessions after timeout period.'],
                    ['m' => 'cybershield.enforce_account_lock',  'class' => 'EnforceAccountLockMiddleware',    'desc' => 'Block locked accounts completely.'],
                    ['m' => 'cybershield.enforce_trusted_device','class' => 'EnforceTrustedDeviceMiddleware',  'desc' => 'Only allow access from fingerprinted trusted devices.'],
                    ['m' => 'cybershield.detect_brute_force',   'class' => 'DetectBruteForceAttackMiddleware','desc' => 'Detect and block brute-force login attempts.'],
                    ['m' => 'cybershield.detect_credential_stuffing','class'=>'DetectCredentialStuffingMiddleware','desc'=>'Detect automated credential stuffing attacks.'],
                ],
            ];
        @endphp

        <div style="display: flex; flex-direction: column; gap: 24px;">
            @foreach($middlewareGroups as $groupName => $mwares)
                <div>
                    <div class="section-title">{{ $groupName }}</div>
                    <div class="card" style="padding: 0; overflow: hidden;">
                        <table class="data-table">
                            <thead><tr>
                                <th>Middleware Alias</th>
                                <th>Class</th>
                                <th>Description</th>
                            </tr></thead>
                            <tbody>
                                @foreach($mwares as $mw)
                                    <tr>
                                        <td><code style="font-family:'JetBrains Mono',monospace; font-size:11.5px; color:var(--cyan);">{{ $mw['m'] }}</code></td>
                                        <td><code style="font-family:'JetBrains Mono',monospace; font-size:11px; color:var(--purple);">{{ $mw['class'] }}</code></td>
                                        <td style="color:var(--text-secondary); font-size:12.5px;">{{ $mw['desc'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <!-- ============================================================
         CONFIG TAB
    ============================================================ -->
    @if($activeTab === 'config')
        <div class="terminal" style="font-size: 12.5px; line-height: 1.8;">
            <div class="terminal-header">
                <div class="terminal-dots"><span class="red"></span><span class="yellow"></span><span class="green"></span></div>
                <span class="terminal-label">config/cybershield.php</span>
            </div>
            <div><span class="comment">// ============================================================</span></div>
            <div><span class="comment">// CyberShield Complete Configuration Reference</span></div>
            <div><span class="comment">// Run: php artisan vendor:publish --tag=cybershield-config</span></div>
            <div><span class="comment">// ============================================================</span></div>
            <br>
            <div><span class="prompt">return </span><span class="cmd">[</span></div>
            <br>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="comment">// Global enable/disable switch</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'enabled' => env('CYBERSHIELD_ENABLED', true),</span></div>
            <br>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="comment">// WAF Firewall</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="cmd">'firewall' => [</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'enabled' => true,</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'inspection_targets' => ['query', 'body', 'headers', 'uri'],</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'blocking_ttl' => ['low' => 1, 'medium' => 3, 'high' => 7, 'critical' => 30],</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="cmd">],</span></div>
            <br>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="comment">// Rate Limiting</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="cmd">'rate_limiting' => [</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'enabled' => true,</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'max_attempts' => 60,</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'decay_seconds' => 60,</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'strategy' => 'sliding_window', // fixed_window | sliding_window | token_bucket</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="cmd">],</span></div>
            <br>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="comment">// Network / Geo Security</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="cmd">'network' => [</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="cmd">'geo_blocking' => [</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'enabled' => false,</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'blocked_countries' => ['CN', 'RU', 'KP', 'IR'],</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="cmd">],</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'block_tor' => true,</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'whitelist' => ['127.0.0.1', '::1'],</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="cmd">],</span></div>
            <br>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="comment">// Bot Protection</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="cmd">'bot_protection' => [</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'enabled' => true,</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'block_scrapers' => true,</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'block_headless' => true,</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="cmd">'honeypot' => ['enabled' => true, 'field_name' => 'hp_token_id'],</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="cmd">],</span></div>
            <br>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="comment">// Threat Detection</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="cmd">'threat_detection' => [</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'enabled' => true,</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'block_on_threat' => true,</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'threat_score_threshold' => 75,</span></div>
            <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="cmd">],</span></div>
            <br>
            <div><span class="cmd">];</span></div>
        </div>
    @endif
</div>
