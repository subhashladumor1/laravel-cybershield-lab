@section('page-title', 'Network Guard Lab')

@php
    $testIps = \App\Livewire\Lab\NetworkLab::$testIps;
    $blockedCountries = \App\Livewire\Lab\NetworkLab::$blockedCountries;
    $riskColor = match(true) {
        ($ipAnalysis['threat'] ?? 0) >= 75 => 'rose',
        ($ipAnalysis['threat'] ?? 0) >= 40 => 'amber',
        default => 'emerald',
    };
@endphp

<div class="anim-fade-up">
    <div class="page-header">
        <h1>🌐 Network Guard Lab</h1>
        <p class="subtitle">
            Simulate connections from TOR exit nodes, geo-blocked countries, VPN providers, and anonymous proxies.
            Watch CyberShield's NetworkGuard module apply real-time IP intelligence and country-level access policies.
        </p>
    </div>

    <div style="display: grid; grid-template-columns: 310px 1fr; gap: 20px;">

        <!-- LEFT — IP Selector + Policy Config -->
        <div style="display: flex; flex-direction: column; gap: 16px;">

            <!-- IP Selector -->
            <div class="card">
                <div class="section-title" style="margin-bottom: 10px;">Simulated Origin IP</div>
                @foreach($testIps as $ip => $profile)
                    <div wire:click="selectIp('{{ $ip }}')"
                         class="attack-card {{ $simulatedIp === $ip ? 'active' : '' }}"
                         style="margin-bottom: 8px;">
                        <div style="display:flex; align-items:center; justify-content:space-between;">
                            <div>
                                <div style="font-size:12px; font-weight:700; font-family:'JetBrains Mono',monospace; color:var(--text-primary);">{{ $ip }}</div>
                                <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">{{ $profile['label'] }}</div>
                            </div>
                            <span class="badge badge-{{ in_array($profile['country'], $blockedCountries) || $profile['tor'] ? 'blocked' : ($profile['threat'] > 50 ? 'warning' : 'allowed') }}">
                                {{ $profile['country'] }}
                            </span>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Policy Configuration -->
            <div class="card">
                <div class="section-title" style="margin-bottom: 10px;">Active Block Policies</div>

                <div class="toggle-row">
                    <div class="toggle-info">
                        <div class="toggle-label">Geo-Blocking</div>
                        <div class="toggle-desc">Block: {{ implode(', ', $blockedCountries) }}</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" {{ $geoBlockOn ? 'checked' : '' }} wire:click="$toggle('geoBlockOn')">
                        <span class="switch-slider"></span>
                    </label>
                </div>

                <div class="toggle-row">
                    <div class="toggle-info">
                        <div class="toggle-label">TOR Exit Node Block</div>
                        <div class="toggle-desc">DetectTorNetworkMiddleware</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" {{ $torBlockOn ? 'checked' : '' }} wire:click="$toggle('torBlockOn')">
                        <span class="switch-slider"></span>
                    </label>
                </div>

                <div class="toggle-row">
                    <div class="toggle-info">
                        <div class="toggle-label">VPN Block</div>
                        <div class="toggle-desc">DetectVpnNetworkMiddleware</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" {{ $vpnBlockOn ? 'checked' : '' }} wire:click="$toggle('vpnBlockOn')">
                        <span class="switch-slider"></span>
                    </label>
                </div>

                <div class="toggle-row">
                    <div class="toggle-info">
                        <div class="toggle-label">Proxy Detection</div>
                        <div class="toggle-desc">DetectProxyNetworkMiddleware</div>
                    </div>
                    <label class="switch">
                        <input type="checkbox" {{ $proxyBlockOn ? 'checked' : '' }} wire:click="$toggle('proxyBlockOn')">
                        <span class="switch-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- RIGHT — Analysis + Results -->
        <div style="display: flex; flex-direction: column; gap: 16px;">

            <!-- IP Intelligence Panel -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <div class="icon-wrap icon-cyan"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5a17.92 17.92 0 0 1-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-.778.099-1.533.284-2.253"/></svg></div>
                        IP Intelligence Report
                        <span style="font-size:12px; font-weight:400; color:var(--text-muted);">— {{ $simulatedIp }}</span>
                    </div>
                    <div style="display:flex; gap:8px;">
                        @if(!$isDemoBlocked)
                            <button wire:click="blockDemoIp" class="btn btn-danger btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                                Block IP
                            </button>
                        @else
                            <button wire:click="unblockDemoIp" class="btn btn-success btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                                Unblock IP
                            </button>
                        @endif
                    </div>
                </div>

                <div style="display:grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 14px;">
                    <div class="ip-row" style="flex-direction:column; gap:3px;">
                        <div class="ip-label">IP Address</div>
                        <div class="ip-val">{{ $ipAnalysis['ip'] ?? $simulatedIp }}</div>
                    </div>
                    <div class="ip-row" style="flex-direction:column; gap:3px;">
                        <div class="ip-label">Country</div>
                        <div class="ip-val">
                            {{ $ipAnalysis['country'] ?? '?' }}
                            @if(in_array($ipAnalysis['country'] ?? '', $blockedCountries))
                                <span class="badge badge-blocked" style="margin-left:6px;">GEO-BLOCKED</span>
                            @endif
                        </div>
                    </div>
                    <div class="ip-row" style="flex-direction:column; gap:3px;">
                        <div class="ip-label">Network / ASN</div>
                        <div class="ip-val">{{ $ipAnalysis['asn'] ?? 'Unknown' }}</div>
                    </div>
                    <div class="ip-row" style="flex-direction:column; gap:3px;">
                        <div class="ip-label">IP Type</div>
                        <div class="ip-val">{{ strtoupper($ipAnalysis['type'] ?? 'unknown') }}</div>
                    </div>
                    <div class="ip-row" style="flex-direction:column; gap:3px;">
                        <div class="ip-label">Threat Score</div>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <span class="ip-val">{{ $ipAnalysis['threat'] ?? 0 }}/100</span>
                            <div class="progress-bar" style="flex:1; height:5px;">
                                <div class="progress-fill" style="width:{{ $ipAnalysis['threat'] ?? 0 }}%; background:var(--{{ $riskColor }});"></div>
                            </div>
                        </div>
                    </div>
                    <div class="ip-row" style="flex-direction:column; gap:3px;">
                        <div class="ip-label">Status</div>
                        <div>
                            @if($isDemoBlocked)
                                <span class="badge badge-blocked">Manually Blocked</span>
                            @else
                                <span class="badge badge-allowed">Active</span>
                            @endif

                            @if($ipAnalysis['tor'] ?? false)  <span class="badge badge-critical" style="margin-left:4px;">TOR</span> @endif
                            @if($ipAnalysis['vpn'] ?? false)  <span class="badge badge-warning" style="margin-left:4px;">VPN</span>  @endif
                            @if($ipAnalysis['proxy'] ?? false) <span class="badge badge-high" style="margin-left:4px;">PROXY</span> @endif
                        </div>
                    </div>
                </div>

                <!-- Helpers Reference -->
                <div style="margin-top: 4px; padding: 10px 12px; background: var(--bg-elevated); border-radius: 7px; border: 1px solid var(--border-subtle);">
                    <div style="font-size:10px; font-weight:700; color:var(--text-muted); text-transform:uppercase; margin-bottom:6px;">Helper Functions for This IP</div>
                    <div style="display:flex; gap:8px; flex-wrap:wrap;">
                        <code style="font-family:'JetBrains Mono',monospace; font-size:11px; color:var(--cyan); background:rgba(0,212,255,0.06); padding:3px 8px; border-radius:4px;">ip_country_code()</code>
                        <code style="font-family:'JetBrains Mono',monospace; font-size:11px; color:var(--cyan); background:rgba(0,212,255,0.06); padding:3px 8px; border-radius:4px;">ip_threat_score()</code>
                        <code style="font-family:'JetBrains Mono',monospace; font-size:11px; color:var(--cyan); background:rgba(0,212,255,0.06); padding:3px 8px; border-radius:4px;">is_tor_ip()</code>
                        <code style="font-family:'JetBrains Mono',monospace; font-size:11px; color:var(--cyan); background:rgba(0,212,255,0.06); padding:3px 8px; border-radius:4px;">is_vpn_ip()</code>
                        <code style="font-family:'JetBrains Mono',monospace; font-size:11px; color:var(--cyan); background:rgba(0,212,255,0.06); padding:3px 8px; border-radius:4px;">ip_is_blacklisted()</code>
                    </div>
                </div>
            </div>

            <!-- Simulate Button -->
            <button wire:click="simulate"
                    wire:loading.attr="disabled"
                    class="btn btn-primary btn-lg btn-full">
                <span wire:loading.remove wire:target="simulate">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:16px;height:16px"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3"/></svg>
                    Simulate Connection from {{ $simulatedIp }}
                </span>
                <span wire:loading wire:target="simulate" style="display:none; align-items:center; gap:8px; width:100%; justify-content:center;">
                    <div class="spinner"></div> Running NetworkGuard checks...
                </span>
            </button>

            <!-- Analysis Log -->
            @if(count($analysisLog) > 0)
                <div class="terminal">
                    <div class="terminal-header">
                        <div class="terminal-dots"><span class="red"></span><span class="yellow"></span><span class="green"></span></div>
                        <span class="terminal-label">cybershield-network-guard.log</span>
                    </div>
                    @foreach($analysisLog as $line)
                        <div>
                            @if(str_contains($line, 'BLOCK') || str_contains($line, 'DROPPED'))
                                <span class="error-line">{{ $line }}</span>
                            @elseif(str_contains($line, 'passed') || str_contains($line, 'allowed'))
                                <span class="success-line">{{ $line }}</span>
                            @else
                                <span class="output">{{ $line }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Result -->
            @if($result)
                <div class="result-banner {{ $result['type'] }} anim-fade-up">
                    <div class="result-icon">
                        @if($result['type'] === 'blocked')
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636"/></svg>
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/></svg>
                        @endif
                    </div>
                    <div class="result-body">
                        <h4>{{ $result['headline'] }}</h4>
                        <p>{{ $result['message'] }}</p>

                        @if(!empty($result['reasons']))
                            <div style="margin-top: 8px; display:flex; flex-wrap:wrap; gap:6px;">
                                @foreach($result['reasons'] as $reason)
                                    <span class="badge badge-blocked">{{ $reason }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Code Reference -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <div class="icon-wrap icon-cyan"><svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5"/></svg></div>
                        Implementation Reference
                    </div>
                </div>
                <div class="terminal" style="font-size:12px;">
                    <div><span class="comment">// Geo-blocking via middleware</span></div>
                    <div><span class="prompt">Route::</span><span class="cmd">middleware(['cybershield.detect_country_block'])->group(...);</span></div>
                    <br>
                    <div><span class="comment">// TOR + VPN + Proxy protection stack</span></div>
                    <div><span class="prompt">Route::</span><span class="cmd">middleware([</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'cybershield.detect_tor_network',</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'cybershield.detect_vpn_network',</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'cybershield.detect_proxy_network',</span></div>
                    <div><span class="cmd">])->group(...);</span></div>
                    <br>
                    <div><span class="comment">// Manual block / unblock via helpers</span></div>
                    <div><span class="cmd">block_current_ip('Suspicious activity');</span></div>
                    <div><span class="cmd">whitelist_current_ip();</span></div>
                    <br>
                    <div><span class="comment">// Config: cybershield.php</span></div>
                    <div><span class="cmd">'network' => [</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'geo_blocking' => ['enabled' => true, 'blocked_countries' => ['CN', 'RU', 'KP']],</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'block_tor' => true,</span></div>
                    <div>&nbsp;&nbsp;&nbsp;&nbsp;<span class="output">'block_vpn' => false,</span></div>
                    <div><span class="cmd">]</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
