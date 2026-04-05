@section('page-title', 'WAF Firewall Lab')

@php
    $attackDefs = \App\Livewire\Lab\WafLab::$attacks;
    $active     = $attackDefs[$activeAttack] ?? $attackDefs['sqli'];
    $colorMap   = ['rose' => 'rose', 'amber' => 'amber', 'purple' => 'purple', 'cyan' => 'cyan', 'emerald' => 'green'];
    $colorClass = $colorMap[$active['color']] ?? 'rose';
@endphp

<div class="anim-fade-up">

    <!-- Header -->
    <div class="page-header">
        <h1>
            <span style="color: var(--rose);">⛊</span>
            WAF Firewall Lab
        </h1>
        <p class="subtitle">
            Simulate real attack payloads against the CyberShield <code style="font-family:'JetBrains Mono',monospace; font-size:12px; color:var(--cyan); background:var(--bg-elevated); padding:2px 6px; border-radius:4px;">WAFEngine</code>
            and observe how injection patterns are detected and blocked.
        </p>
    </div>

    <div style="display: grid; grid-template-columns: 280px 1fr; gap: 20px;">

        <!-- ============================================================
             LEFT PANEL — Attack Selector + WAF Config
        ============================================================ -->
        <div style="display: flex; flex-direction: column; gap: 16px;">

            <!-- Attack Type Selector -->
            <div class="card" style="padding: 16px;">
                <div class="section-title" style="margin-bottom: 10px;">Select Attack Vector</div>

                @foreach($attackDefs as $key => $atk)
                    <div wire:click="selectAttack('{{ $key }}')"
                         class="attack-card {{ $activeAttack === $key ? 'active' : '' }}"
                         style="margin-bottom: 8px;">
                        <div style="display: flex; align-items: center; justify-content: space-between;">
                            <div class="attack-name">{{ $atk['name'] }}</div>
                            <span class="badge {{ $toggles[$key] ?? true ? 'badge-allowed' : 'badge-blocked' }}" style="font-size:9px;">
                                {{ $toggles[$key] ?? true ? 'ON' : 'OFF' }}
                            </span>
                        </div>
                        <div class="attack-desc">{{ $atk['cve'] }}</div>
                    </div>
                @endforeach
            </div>

            <!-- WAF Toggle Panel -->
            <div class="card" style="padding: 16px;">
                <div class="section-title" style="margin-bottom: 10px;">
                    WAF Rule Toggles
                    <span style="font-size:10px; color:var(--text-muted); text-transform:none; font-weight:400; letter-spacing:0;">Toggle OFF to simulate bypassed protection</span>
                </div>

                @foreach($toggles as $key => $enabled)
                    <div class="toggle-row">
                        <div class="toggle-info">
                            <div class="toggle-label" style="font-size:12px;">{{ strtoupper($key) }} Rule</div>
                        </div>
                        <label class="switch">
                            <input type="checkbox" {{ $enabled ? 'checked' : '' }} wire:click="toggleProtection('{{ $key }}')">
                            <span class="switch-slider"></span>
                        </label>
                    </div>
                @endforeach
            </div>

            <!-- Score Board -->
            <div class="card" style="padding: 16px;">
                <div class="section-title" style="margin-bottom: 10px;">Session Score</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                    <div style="text-align:center; padding: 12px; background: rgba(16,185,129,0.08); border-radius: 8px; border: 1px solid rgba(16,185,129,0.2);">
                        <div style="font-size:22px; font-weight:800; color:var(--emerald);">{{ $totalBlocked }}</div>
                        <div style="font-size:10px; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Blocked</div>
                    </div>
                    <div style="text-align:center; padding: 12px; background: rgba(244,63,94,0.08); border-radius: 8px; border: 1px solid rgba(244,63,94,0.2);">
                        <div style="font-size:22px; font-weight:800; color:var(--rose);">{{ $totalBypassed }}</div>
                        <div style="font-size:10px; color:var(--text-muted); font-weight:600; text-transform:uppercase;">Bypassed</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ============================================================
             RIGHT PANEL — Attack Console
        ============================================================ -->
        <div style="display: flex; flex-direction: column; gap: 16px;">

            <!-- Attack Info Card -->
            <div class="card" style="border-left: 3px solid var(--{{ $active['color'] === 'emerald' ? 'success' : $active['color'] }});">
                <div style="display: flex; align-items: flex-start; gap: 16px;">
                    <div class="icon-wrap icon-{{ $colorClass }}" style="width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
                        </svg>
                    </div>
                    <div style="flex: 1;">
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom:4px;">
                            <h3 style="font-size:16px; font-weight:800; color:var(--text-primary);">{{ $active['name'] }}</h3>
                            <span class="badge badge-blocked">{{ $active['cve'] }}</span>
                        </div>
                        <p style="font-size:13px; color:var(--text-secondary);">{{ $active['desc'] }}</p>
                        <div style="margin-top:8px; display:flex; gap:8px; flex-wrap:wrap;">
                            <span style="font-size:11px; color:var(--text-muted);">Middleware:</span>
                            <code style="font-family:'JetBrains Mono',monospace; font-size:11px; color:var(--cyan);">{{ $active['middleware'] }}</code>
                            <span style="color:var(--text-muted);">·</span>
                            <span style="font-size:11px; color:var(--text-muted);">Helper:</span>
                            <code style="font-family:'JetBrains Mono',monospace; font-size:11px; color:var(--cyan);">{{ $active['helper'] }}</code>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Configuration Row -->
            <div class="card">
                <div class="card-header" style="margin-bottom: 14px;">
                    <div class="card-title">
                        <div class="icon-wrap icon-cyan"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg></div>
                        Attack Configuration
                    </div>
                </div>

                <div class="grid-2" style="gap: 12px; margin-bottom: 12px;">
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Payload Severity</label>
                        <select wire:model.live="sensitivity" class="form-select">
                            <option value="critical">Critical — Maximum Severity</option>
                            <option value="high">High — Advanced Evasion</option>
                            <option value="medium">Medium — Standard Attack</option>
                            <option value="low">Low — Subtle / Encoded</option>
                        </select>
                        <div class="form-hint">Higher severity = more dangerous payloads, harder to detect at low sensitivity</div>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label class="form-label">Custom Payload (Optional)</label>
                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                            <label class="switch" style="flex-shrink:0;">
                                <input type="checkbox" wire:model.live="useCustomPayload">
                                <span class="switch-slider"></span>
                            </label>
                            <span style="font-size:12px; color:var(--text-muted);">Enable custom payload</span>
                        </div>
                        <input type="text" wire:model.live="customPayload"
                               class="form-input"
                               placeholder="Enter your own attack payload..."
                               {{ !$useCustomPayload ? 'disabled' : '' }}
                               style="{{ !$useCustomPayload ? 'opacity:0.4;' : '' }}">
                    </div>
                </div>

                <!-- Live Payload Preview -->
                <div>
                    <div style="font-size:11px; font-weight:600; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em; margin-bottom:6px;">
                        Active Payload Preview
                    </div>
                    <div class="payload-box">
                        @php
                            $previewPayload = ($useCustomPayload && $customPayload)
                                ? $customPayload
                                : ($active['payloads'][$sensitivity] ?? $active['payloads']['high']);
                        @endphp
                        {{ $previewPayload }}
                    </div>
                </div>
            </div>

            <!-- Fire Button -->
            <div style="display: flex; gap: 10px; align-items: center;">
                <button wire:click="triggerAttack"
                        wire:loading.attr="disabled"
                        wire:loading.class="opacity-50"
                        class="btn btn-danger btn-lg"
                        style="flex: 1; justify-content: center;"
                        {{ !($toggles[$activeAttack] ?? true) ? '' : '' }}>
                    <span wire:loading.remove wire:target="triggerAttack">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:18px;height:18px"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/></svg>
                        Launch {{ $active['name'] }} Attack
                    </span>
                    <span wire:loading wire:target="triggerAttack" style="display:none; align-items:center; gap:8px;">
                        <div class="spinner"></div>
                        WAF Engine Processing...
                    </span>
                </button>

                @if($result)
                    <button wire:click="resetLab" class="btn btn-ghost">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                        Reset
                    </button>
                @endif
            </div>

            <!-- Execution Log -->
            @if(count($executionLog) > 0)
                <div class="terminal">
                    <div class="terminal-header">
                        <div class="terminal-dots">
                            <span class="red"></span>
                            <span class="yellow"></span>
                            <span class="green"></span>
                        </div>
                        <span class="terminal-label">cybershield-waf-engine.log</span>
                    </div>
                    @foreach($executionLog as $line)
                        <div>
                            @if(str_contains($line, 'BLOCKED') || str_contains($line, '✓'))
                                <span class="success-line">{{ $line }}</span>
                            @elseif(str_contains($line, 'BYPASSED') || str_contains($line, 'RESULT:') && str_contains($line, 'passed'))
                                <span class="error-line">{{ $line }}</span>
                            @elseif(str_contains($line, 'ERROR'))
                                <span class="warn-line">{{ $line }}</span>
                            @else
                                <span class="output">{{ $line }}</span>
                            @endif
                        </div>
                    @endforeach
                    @if($isLoading)
                        <div class="prompt cursor-blink">Waiting for response...</div>
                    @endif
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

                        <!-- Payload used -->
                        <div style="margin-top: 10px; padding: 8px 12px; background: rgba(0,0,0,0.3); border-radius: 6px; border-left: 2px solid rgba(255,255,255,0.15);">
                            <div style="font-size:10px; color:var(--text-muted); margin-bottom:3px; font-weight:700; text-transform:uppercase;">Payload Used</div>
                            <code style="font-family:'JetBrains Mono',monospace; font-size:11.5px; color:#ff8099; word-break:break-all;">{{ htmlspecialchars(substr($result['payload'], 0, 150)) }}</code>
                        </div>

                        <!-- Implementation Lesson -->
                        <div style="margin-top: 10px; padding: 8px 12px; background: rgba(0,212,255,0.06); border-radius: 6px; border-left: 2px solid rgba(0,212,255,0.3);">
                            <div style="font-size:10px; color:var(--cyan); margin-bottom:3px; font-weight:700; text-transform:uppercase;">💡 How to Implement in Your App</div>
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
                <div style="font-size:12px; color:var(--text-muted); margin-bottom:10px;">Use these in your Laravel routes and controllers to protect against <strong style="color:var(--text-primary);">{{ $active['name'] }}</strong>:</div>

                <div class="terminal" style="font-size:12px;">
                    <div><span class="comment">// 1. Route Middleware (routes/web.php or api.php)</span></div>
                    <div><span class="prompt">Route::</span><span class="cmd">middleware(['cybershield.{{ strtolower(str_replace('Middleware','',str_replace('Detect','detect.',str_replace('Validate','validate.',$active['middleware'])))) }}'])</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="cmd">->group(function () {</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span class="prompt">Route::</span><span class="cmd">post('/submit', [FormController::class, 'store']);</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="cmd">});</span></div>
                    <br>
                    <div><span class="comment">// 2. Helper function in controller</span></div>
                    <div><span class="prompt">if </span><span class="cmd">({{ $active['helper'] }}) {</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="error-line">abort(403, 'Malicious input detected.');</span></div>
                    <div><span class="cmd">}</span></div>
                    <br>
                    <div><span class="comment">// 3. Blade directive for conditional rendering</span></div>
                    <div><span class="prompt">@</span><span class="cmd">secureRequestValid</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">{{-- Your protected content --}}</span></div>
                    <div><span class="prompt">@</span><span class="cmd">endsecureRequestValid</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
