@section('page-title', 'Rate Limiter Lab')

{{--
    Variables available:
    $strategy        = string key e.g. 'sliding_window'  (Livewire public property)
    $activeStrategy  = resolved array ['name', 'middleware', 'desc', 'use_case']  (from render())
    $allowed, $throttled, $maxRequests, $windowSecs, $burstSize, $timeline, $lastResult (Livewire props)
    $usagePercent    = int from render()
--}}

@php
    $total      = $allowed + $throttled;
    $fillPct    = $total > 0 ? min(100, ($total / max(1, $maxRequests + $burstSize)) * 100) : 0;
    $meterColor = match(true) {
        $throttled > 0               => 'var(--rose)',
        $total >= $maxRequests * 0.7 => 'var(--amber)',
        default                      => 'var(--emerald)',
    };
@endphp

<div class="anim-fade-up">
    <div class="page-header">
        <h1>⚡ Rate Limiter Lab</h1>
        <p class="subtitle">Test all 6 rate limiting strategies, simulate DDoS flood attacks, and learn how CyberShield enforces traffic quotas at the middleware level.</p>
    </div>

    <div style="display: grid; grid-template-columns: 300px 1fr; gap: 20px;">

        <!-- LEFT — Strategy Selector -->
        <div style="display: flex; flex-direction: column; gap: 16px;">

            <div class="card">
                <div class="section-title" style="margin-bottom: 10px;">Rate Limit Strategy</div>
                @foreach(\App\Livewire\Lab\RateLimiterLab::$strategies as $key => $strat)
                    <div wire:click="$set('strategy', '{{ $key }}')"
                         class="attack-card {{ $strategy === $key ? 'active' : '' }}"
                         style="margin-bottom: 8px;">
                        <div style="font-size:13px; font-weight:700; color:var(--text-primary);">{{ $strat['name'] }}</div>
                        <div style="font-size:11px; color:var(--text-muted); margin-top: 2px; font-family:'JetBrains Mono',monospace;">{{ $strat['middleware'] }}</div>
                    </div>
                @endforeach
            </div>

            <!-- Configuration -->
            <div class="card">
                <div class="section-title" style="margin-bottom: 10px;">Parameters</div>
                <div class="form-group">
                    <label class="form-label">Max Requests</label>
                    <input type="number" wire:model.live="maxRequests" min="1" max="200" class="form-input">
                    <div class="form-hint">Requests allowed before throttling</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Time Window (seconds)</label>
                    <input type="number" wire:model.live="windowSecs" min="10" max="3600" class="form-input">
                </div>
                <div class="form-group">
                    <label class="form-label">Burst Size (for burst sim)</label>
                    <input type="number" wire:model.live="burstSize" min="1" max="100" class="form-input">
                    <div class="form-hint">Extra requests beyond the limit to simulate</div>
                </div>
            </div>

            <!-- Stats -->
            <div class="card">
                <div class="section-title" style="margin-bottom: 10px;">Session Stats</div>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:8px; margin-bottom:12px;">
                    <div style="text-align:center; padding:12px 8px; background:rgba(16,185,129,0.08); border-radius:8px; border:1px solid rgba(16,185,129,0.2);">
                        <div style="font-size:24px; font-weight:800; color:var(--emerald);">{{ $allowed }}</div>
                        <div style="font-size:10px; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-top:2px;">Allowed</div>
                    </div>
                    <div style="text-align:center; padding:12px 8px; background:rgba(244,63,94,0.08); border-radius:8px; border:1px solid rgba(244,63,94,0.2);">
                        <div style="font-size:24px; font-weight:800; color:var(--rose);">{{ $throttled }}</div>
                        <div style="font-size:10px; color:var(--text-muted); font-weight:700; text-transform:uppercase; margin-top:2px;">Throttled</div>
                    </div>
                </div>

                <div style="font-size:11px; color:var(--text-muted); font-weight:600; text-transform:uppercase; margin-bottom:5px;">
                    Request Meter — {{ $total }} / {{ $maxRequests }}
                </div>
                <div class="rate-meter">
                    <div class="rate-meter-fill" style="width: {{ $fillPct }}%; background: {{ $meterColor }};"></div>
                </div>
                <div style="display:flex; justify-content:space-between; font-size:10px; color:var(--text-muted); margin-top:3px;">
                    <span>0</span>
                    <span>{{ $maxRequests }} limit</span>
                </div>
            </div>
        </div>

        <!-- RIGHT — Controls & Results -->
        <div style="display: flex; flex-direction: column; gap: 16px;">

            <!-- Active Strategy Info -->
            <div class="card" style="border-left: 3px solid var(--amber);">
                <div style="display:flex; align-items:flex-start; gap:14px;">
                    <div class="icon-wrap icon-amber" style="width:42px; height:42px; border-radius:10px; flex-shrink:0;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                    </div>
                    <div>
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:4px;">
                            <h3 style="font-size:15px; font-weight:800; color:var(--text-primary);">{{ $activeStrategy['name'] }}</h3>
                            <span class="badge badge-info" style="font-size:9px;">{{ $activeStrategy['use_case'] }}</span>
                        </div>
                        <p style="font-size:13px; color:var(--text-secondary);">{{ $activeStrategy['desc'] }}</p>
                        <div style="margin-top:6px;">
                            <span style="font-size:11px; color:var(--text-muted);">Middleware: </span>
                            <code style="font-family:'JetBrains Mono',monospace; font-size:11px; color:var(--cyan);">{{ $activeStrategy['middleware'] }}</code>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="display:flex; gap:10px; flex-wrap:wrap;">

                {{-- SEND ONE REQUEST --}}
                <button wire:click="sendRequest"
                        wire:loading.attr="disabled"
                        wire:target="sendRequest,simulateBurst,resetLab"
                        class="btn btn-primary btn-lg"
                        style="flex:1; justify-content:center; min-width:160px;">
                    <span wire:loading.remove wire:target="sendRequest">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px"><path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5"/></svg>
                        Send One Request
                    </span>
                    <span wire:loading wire:target="sendRequest" style="display:none; align-items:center; gap:6px; width:100%; justify-content:center;">
                        <div class="spinner"></div> Sending...
                    </span>
                </button>

                {{-- DDOS FLOOD --}}
                <button wire:click="simulateBurst"
                        wire:loading.attr="disabled"
                        wire:target="sendRequest,simulateBurst,resetLab"
                        class="btn btn-danger btn-lg"
                        style="flex:1; justify-content:center; min-width:160px;">
                    <span wire:loading.remove wire:target="simulateBurst">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5"/></svg>
                        Simulate DDoS Flood
                    </span>
                    <span wire:loading wire:target="simulateBurst" style="display:none; align-items:center; gap:6px; width:100%; justify-content:center;">
                        <div class="spinner"></div> Flooding {{ $maxRequests + $burstSize }} requests...
                    </span>
                </button>

                {{-- RESET --}}
                <button wire:click="resetLab"
                        wire:loading.attr="disabled"
                        wire:target="sendRequest,simulateBurst,resetLab"
                        class="btn btn-ghost btn-lg">
                    <span wire:loading.remove wire:target="resetLab">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                        Reset
                    </span>
                    <span wire:loading wire:target="resetLab" style="display:none;">
                        <div class="spinner"></div>
                    </span>
                </button>
            </div>

            <!-- Last Result Banner -->
            @if($lastResult)
                <div class="result-banner {{ $lastResult['throttled'] ? 'blocked' : 'allowed' }} anim-fade-up">
                    <div class="result-icon">
                        @if($lastResult['throttled'])
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        @endif
                    </div>
                    <div class="result-body">
                        <h4>{{ $lastResult['throttled'] ? 'HTTP 429 — Rate Limit Exceeded' : 'HTTP 200 — Request Allowed' }}</h4>
                        <p>
                            {{ $lastResult['throttled'] ? 'The rate limiter blocked this request.' : 'Request passed through within the allowed limit.' }}
                            Request #{{ $lastResult['count'] }} of {{ $lastResult['limit'] }} limit.
                            @if(!empty($lastResult['burst_sim']))
                                Burst simulation: sent {{ $allowed + $throttled }} total requests ({{ $throttled }} blocked).
                            @endif
                        </p>
                        <div style="margin-top:10px; padding:10px 14px; background:rgba(0,0,0,0.4); border-radius:6px; border:1px solid rgba(0,212,255,0.12);">
                            <code style="font-family:'JetBrains Mono',monospace; font-size:11px; color:var(--cyan); white-space:pre-line;">HTTP/1.1 {{ $lastResult['status'] }} {{ $lastResult['throttled'] ? 'Too Many Requests' : 'OK' }}
X-RateLimit-Limit: {{ $maxRequests }}
X-RateLimit-Remaining: {{ max(0, $maxRequests - $lastResult['count']) }}
X-RateLimit-Reset: {{ now()->addSeconds($windowSecs)->timestamp }}
Retry-After: {{ $windowSecs }}s</code>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Request Timeline Grid -->
            @if(count($timeline) > 0)
                <div class="card">
                    <div class="card-header">
                        <div class="card-title">
                            <div class="icon-wrap icon-cyan">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/></svg>
                            </div>
                            Request Timeline
                            <span style="font-size:11px; color:var(--text-muted); font-weight:400; margin-left:4px;">
                                <span style="color:var(--emerald);">■</span> allowed &nbsp;
                                <span style="color:var(--rose);">■</span> throttled (429) &nbsp;
                                <span style="color:var(--amber);">■</span> near limit
                            </span>
                        </div>
                        <div style="font-size:11px; color:var(--text-muted);">{{ count($timeline) }} requests</div>
                    </div>
                    <div class="req-timeline">
                        @foreach($timeline as $req)
                            @php
                                $dotClass = match(true) {
                                    $req['status'] === 429              => 'bad',
                                    $req['count'] >= $maxRequests * 0.8 => 'warn',
                                    default                             => 'ok',
                                };
                            @endphp
                            <div class="req-dot {{ $dotClass }}"
                                 data-tip="#{{ $req['count'] }} — {{ $req['status'] === 429 ? '429 Blocked' : '200 OK' }} at {{ $req['time'] }}">
                                {{ $req['status'] === 429 ? '✕' : '✓' }}
                            </div>
                        @endforeach
                    </div>
                    <div style="margin-top: 8px; font-size: 11.5px; color: var(--text-muted);">
                        Limit of {{ $maxRequests }} rps enforced by <strong style="color:var(--cyan);">{{ $activeStrategy['middleware'] }}</strong>
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
                    <div><span class="comment">// {{ $activeStrategy['name'] }} — Applied as route middleware</span></div>
                    <br>
                    <div><span class="prompt">Route::</span><span class="cmd">middleware(['cybershield.rate_limiter'])</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="cmd">->group(function () {</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="prompt">Route::</span><span class="cmd">post('/api/login', ...);</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="cmd">});</span></div>
                    <br>
                    <div><span class="comment">// Or the specific {{ $activeStrategy['middleware'] }}</span></div>
                    <div><span class="prompt">Route::</span><span class="cmd">middleware(['cybershield.sliding_window_rate_limiter'])</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="cmd">->post('/api/endpoint', ...);</span></div>
                    <br>
                    <div><span class="comment">// Config: config/cybershield.php</span></div>
                    <div><span class="cmd">'rate_limiting' => [</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'max_attempts' => {{ $maxRequests }},</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'decay_seconds' => {{ $windowSecs }},</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'strategy' => '{{ $strategy }}',</span></div>
                    <div><span class="cmd">]</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
