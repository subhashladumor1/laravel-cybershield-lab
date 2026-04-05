@section('page-title', 'Bot Defense Lab')

@php
    $botDefs = \App\Livewire\Lab\BotLab::$bots;
    $activeBot = $botDefs[$selectedBot] ?? $botDefs['googlebot'];
    $riskColors = ['low' => 'allowed', 'medium' => 'warning', 'high' => 'high', 'critical' => 'critical'];
@endphp

<div class="anim-fade-up">
    <div class="page-header">
        <h1>🤖 Bot Defense Lab</h1>
        <p class="subtitle">
            Replay 8 real-world bot types — scrapers, headless browsers, vulnerability scanners, and spam bots —
            and observe how CyberShield's detection pipeline identifies and blocks each one.
        </p>
    </div>

    <div style="display: grid; grid-template-columns: 300px 1fr; gap: 20px;">

        <!-- LEFT — Bot Selector + Defense Toggles -->
        <div style="display: flex; flex-direction: column; gap: 16px;">

            <div class="card">
                <div class="section-title" style="margin-bottom: 10px;">Select Bot Type</div>
                @foreach($botDefs as $key => $bot)
                    <div wire:click="selectBot('{{ $key }}')"
                         class="bot-ua-card {{ $selectedBot === $key ? 'selected' : '' }}"
                         style="margin-bottom: 8px;">
                        <div class="bot-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0 1 12 15a9.065 9.065 0 0 1-6.23-.693L5 14.5m14.8.8 1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0 1 12 21a48.25 48.25 0 0 1-8.135-.687c-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"/></svg>
                        </div>
                        <div class="bot-info">
                            <div class="bot-name">{{ $bot['name'] }}</div>
                            <div class="bot-ua">{{ $bot['type'] }}</div>
                        </div>
                        <span class="badge badge-{{ $riskColors[$bot['risk']] ?? 'muted' }}" style="font-size:9px; margin-left:auto;">{{ strtoupper($bot['risk']) }}</span>
                    </div>
                @endforeach
            </div>

            <!-- Defense Toggles -->
            <div class="card">
                <div class="section-title" style="margin-bottom: 10px;">Defense Toggles</div>

                <div class="toggle-row">
                    <div class="toggle-info">
                        <div class="toggle-label">UA Analysis</div>
                        <div class="toggle-desc">Crawler & malicious tool detection</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" {{ $uaCheckOn ? 'checked' : '' }} wire:click="$toggle('uaCheckOn')">
                        <span class="switch-slider"></span>
                    </label>
                </div>
                <div class="toggle-row">
                    <div class="toggle-info">
                        <div class="toggle-label">Headless Detection</div>
                        <div class="toggle-desc">Puppeteer / Chrome headless</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" {{ $headlessOn ? 'checked' : '' }} wire:click="$toggle('headlessOn')">
                        <span class="switch-slider"></span>
                    </label>
                </div>
                <div class="toggle-row">
                    <div class="toggle-info">
                        <div class="toggle-label">Scraper Detection</div>
                        <div class="toggle-desc">Python, curl, wget, Guzzle</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" {{ $scraperOn ? 'checked' : '' }} wire:click="$toggle('scraperOn')">
                        <span class="switch-slider"></span>
                    </label>
                </div>
                <div class="toggle-row">
                    <div class="toggle-info">
                        <div class="toggle-label">Honeypot Trap</div>
                        <div class="toggle-desc">Hidden form field detection</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" {{ $honeypotOn ? 'checked' : '' }} wire:click="$toggle('honeypotOn')">
                        <span class="switch-slider"></span>
                    </label>
                </div>
            </div>

            <!-- Score -->
            <div class="card">
                <div class="section-title" style="margin-bottom: 10px;">Session Tally</div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px;">
                    <div style="text-align:center; padding:12px 8px; background:rgba(16,185,129,0.08); border-radius:8px; border:1px solid rgba(16,185,129,0.2);">
                        <div style="font-size:24px; font-weight:800; color:var(--emerald);">{{ $botsBlocked }}</div>
                        <div style="font-size:10px; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-top:2px;">Blocked</div>
                    </div>
                    <div style="text-align:center; padding:12px 8px; background:rgba(244,63,94,0.08); border-radius:8px; border:1px solid rgba(244,63,94,0.2);">
                        <div style="font-size:24px; font-weight:800; color:var(--rose);">{{ $botsAllowed }}</div>
                        <div style="font-size:10px; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-top:2px;">Passed</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT — Console -->
        <div style="display: flex; flex-direction: column; gap: 16px;">

            <!-- Bot Profile Card -->
            <div class="card" style="border-left: 3px solid var(--purple);">
                <div style="display: flex; align-items: flex-start; gap: 14px;">
                    <div class="icon-wrap icon-purple" style="width:44px; height:44px; border-radius:12px; flex-shrink:0;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 0 1 4.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3"/>
                        </svg>
                    </div>
                    <div style="flex: 1;">
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                            <h3 style="font-size:15px; font-weight:800; color:var(--text-primary);">{{ $activeBot['name'] }}</h3>
                            <span class="badge badge-{{ $riskColors[$activeBot['risk']] ?? 'muted' }}">{{ strtoupper($activeBot['risk']) }} RISK</span>
                        </div>
                        <p style="font-size:13px; color:var(--text-secondary);">{{ $activeBot['desc'] }}</p>
                        <div style="margin-top:8px; font-size:11px; color:var(--amber);">⚡ Expected: {{ $activeBot['expected'] }}</div>
                    </div>
                </div>

                <!-- UA Display -->
                <div style="margin-top: 12px; padding: 10px 13px; background: #010407; border: 1px solid rgba(139,92,246,0.2); border-radius: 7px;">
                    <div style="font-size:10px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:5px;">User-Agent Header Being Sent</div>
                    <code style="font-family:'JetBrains Mono',monospace; font-size:11.5px; color:var(--purple); word-break:break-all; line-height:1.6;">{{ $activeBot['ua'] }}</code>
                </div>

                <!-- Metadata Row -->
                <div style="display:flex; gap:16px; flex-wrap:wrap; margin-top:10px;">
                    <div>
                        <span style="font-size:10px; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Detection Method</span>
                        <div style="font-family:'JetBrains Mono',monospace; font-size:12px; color:var(--cyan); margin-top:2px;">{{ $activeBot['helper'] }}</div>
                    </div>
                    <div>
                        <span style="font-size:10px; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Middleware</span>
                        <div style="font-family:'JetBrains Mono',monospace; font-size:12px; color:var(--cyan); margin-top:2px;">{{ $activeBot['middleware'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Simulate Button -->
            <button wire:click="simulate"
                    wire:loading.attr="disabled"
                    class="btn btn-purple btn-lg btn-full">
                <span wire:loading.remove wire:target="simulate">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z"/></svg>
                    Simulate {{ $activeBot['name'] }} Request
                </span>
                <span wire:loading wire:target="simulate" style="display:none; align-items:center; gap:8px; width:100%; justify-content:center;">
                    <div class="spinner"></div> Running Detection Pipeline...
                </span>
            </button>

            <!-- Detection Log Terminal -->
            @if(count($detectionLog) > 0)
                <div class="terminal">
                    <div class="terminal-header">
                        <div class="terminal-dots">
                            <span class="red"></span><span class="yellow"></span><span class="green"></span>
                        </div>
                        <span class="terminal-label">cybershield-bot-detector.log</span>
                    </div>
                    @foreach($detectionLog as $line)
                        <div>
                            @if(str_contains($line, 'DETECTED') || str_contains($line, 'Blocking'))
                                <span class="success-line">{{ $line }}</span>
                            @elseif(str_contains($line, 'DISABLED') || str_contains($line, 'passed through'))
                                <span class="error-line">{{ $line }}</span>
                            @elseif(str_contains($line, '!') || str_contains($line, 'Could not'))
                                <span class="warn-line">{{ $line }}</span>
                            @else
                                <span class="output">{{ $line }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Result Banner -->
            @if($result)
                <div class="result-banner {{ $result['type'] }} anim-fade-up">
                    <div class="result-icon">
                        @if($result['type'] === 'blocked')
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        @elseif($result['type'] === 'danger')
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-3.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        @endif
                    </div>
                    <div class="result-body">
                        <h4>{{ $result['headline'] }}</h4>
                        <p>{{ $result['message'] }}</p>
                        <div style="margin-top: 10px; padding: 8px 12px; background: rgba(0,212,255,0.06); border-radius: 6px; border-left: 2px solid rgba(0,212,255,0.3);">
                            <div style="font-size:10px; color:var(--cyan); margin-bottom:3px; font-weight:700; text-transform:uppercase;">💡 Implementation</div>
                            <p style="font-size:12px; color:var(--text-secondary);">{{ $result['lesson'] }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Code Reference -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <div class="icon-wrap icon-purple"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5"/></svg></div>
                        Implementation Reference
                    </div>
                </div>
                <div class="terminal" style="font-size:12px;">
                    <div><span class="comment">// 1. Route Middleware — {{ $activeBot['middleware'] }}</span></div>
                    <div><span class="prompt">Route::</span><span class="cmd">middleware(['cybershield.detect_bot'])->group(fn() => [</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="prompt">Route::</span><span class="cmd">get('/protected', ...);</span></div>
                    <div><span class="cmd">]);</span></div>
                    <br>
                    <div><span class="comment">// 2. In Controller / Blade — using helpers</span></div>
                    <div><span class="prompt">if </span><span class="cmd">({{ $activeBot['helper'] }}) {</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="error-line">abort(403, 'Bot access denied.');</span></div>
                    <div><span class="cmd">}</span></div>
                    <br>
                    <div><span class="comment">// 3. Blade Directive — Conditional rendering</span></div>
                    <div><span class="prompt">@</span><span class="cmd">secureBot</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">{{-- Only visible to bots --}}</span></div>
                    <div><span class="prompt">@</span><span class="cmd">endsecureBot</span></div>
                    <br>
                    <div><span class="comment">// 4. Honeypot trap in Blade forms</span></div>
                    <div><span class="prompt">@</span><span class="cmd">secureHoneypot</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">{{-- Hidden field — only bots fill it --}}</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
