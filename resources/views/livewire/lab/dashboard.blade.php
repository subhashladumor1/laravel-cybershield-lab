@section('page-title', 'Dashboard')

<div class="anim-fade-up">

    <!-- Page Header -->
    <div class="page-header">
        <h1>Security Operations Dashboard</h1>
        <p class="subtitle">Real-time overview of CyberShield protection across all active modules.</p>
    </div>

    <!-- ============================================================
         STAT CARDS
    ============================================================ -->
    <div class="grid-4" style="margin-bottom: 20px;">
        <div class="stat-card stat-rose">
            <div class="stat-label">Total Threats Detected</div>
            <div class="stat-value">{{ number_format($totalThreats) }}</div>
            <div class="stat-footer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px;color:var(--rose)"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0 1 18 16.5h-2.25m-7.5 0h7.5m-7.5 0-1 3m8.5-3 1 3m0 0 .5 1.5m-.5-1.5h-9.5m0 0-.5 1.5" /></svg>
                All time across all modules
            </div>
        </div>

        <div class="stat-card stat-amber">
            <div class="stat-label">Critical Threats</div>
            <div class="stat-value">{{ $criticalCount }}</div>
            <div class="stat-footer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px;color:var(--amber)"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                Severity: Critical
            </div>
        </div>

        <div class="stat-card stat-cyan">
            <div class="stat-label">Unique IPs Tracked</div>
            <div class="stat-value">{{ $blockedIps }}</div>
            <div class="stat-footer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px;color:var(--cyan)"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5a17.92 17.92 0 0 1-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-.778.099-1.533.284-2.253" /></svg>
                In threat database
            </div>
        </div>

        <div class="stat-card stat-green">
            <div class="stat-label">Active Modules</div>
            <div class="stat-value">{{ count($moduleStatus) }}</div>
            <div class="stat-footer">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px;color:var(--emerald)"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                All systems operational
            </div>
        </div>
    </div>

    <!-- ============================================================
         MAIN GRID
    ============================================================ -->
    <div class="grid-2" style="margin-bottom: 20px;">

        <!-- Recent Threat Feed -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <div class="icon-wrap icon-rose">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                    </div>
                    Live Threat Feed
                </div>
                <button wire:click="refresh" class="btn btn-ghost btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                    Refresh
                </button>
            </div>

            @if(count($recentThreats) > 0)
                <div>
                    @foreach($recentThreats as $threat)
                        <div class="threat-feed-item">
                            <div class="threat-dot {{ $threat['severity'] == 'critical' ? 'critical' : ($threat['severity'] == 'high' ? 'high' : ($threat['severity'] == 'medium' ? 'medium' : 'low')) }}"></div>
                            <div class="threat-feed-info">
                                <div class="thr-type">{{ str_replace('_', ' ', $threat['type']) }}</div>
                                <div class="thr-meta">{{ $threat['ip'] }}</div>
                            </div>
                            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 3px;">
                                <span class="badge badge-{{ $threat['severity'] == 'critical' ? 'critical' : ($threat['severity'] == 'high' ? 'high' : ($threat['severity'] == 'medium' ? 'medium' : 'low')) }}">{{ $threat['severity'] }}</span>
                                <span class="threat-feed-time">{{ $threat['time'] }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="empty-state">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" /></svg>
                    <h4>No threats detected</h4>
                    <p>Run attack simulations in the labs to populate this feed.</p>
                </div>
            @endif
        </div>

        <!-- Right Column -->
        <div style="display: flex; flex-direction: column; gap: 18px;">

            <!-- Your IP Intelligence -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <div class="icon-wrap icon-cyan">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" /></svg>
                        </div>
                        Your Connection
                    </div>
                </div>
                <div class="ip-row">
                    <div class="ip-label">IP Address</div>
                    <div class="ip-val">{{ $currentIp }}</div>
                    <div class="ip-status"><span class="badge badge-info">Active</span></div>
                </div>
                <div class="ip-row">
                    <div class="ip-label">Reputation</div>
                    <div class="ip-val">{{ $reputation }}</div>
                    <div class="ip-status">
                        <span class="badge {{ match($reputation) { 'Trusted' => 'badge-allowed', 'Neutral' => 'badge-info', 'Suspicious' => 'badge-warning', default => 'badge-blocked' } }}">{{ $reputation }}</span>
                    </div>
                </div>
                <div class="ip-row">
                    <div class="ip-label">Threat Score</div>
                    <div class="ip-val">
                        <div style="display:flex; align-items:center; gap:10px;">
                            <span>{{ $threatScore }} / 100</span>
                            <div class="progress-bar" style="flex:1; height:5px;">
                                <div class="progress-fill" style="width: {{ $threatScore }}%; background: {{ $threatScore >= 75 ? 'var(--rose)' : ($threatScore >= 45 ? 'var(--amber)' : 'var(--emerald)') }};"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ip-row">
                    <div class="ip-label">Helper Call</div>
                    <div class="ip-val" style="font-size:11.5px; color: var(--text-code);">ip_threat_score('{{ $currentIp }}')</div>
                </div>
            </div>

            <!-- Module Status -->
            <div class="card">
                <div class="card-header">
                    <div class="card-title">
                        <div class="icon-wrap icon-green">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                        </div>
                        Module Status
                    </div>
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                    @foreach($moduleStatus as $name => $mod)
                        <div style="display:flex; align-items:center; gap:8px; padding:7px 10px; background: var(--bg-elevated); border-radius: 8px;">
                            <div style="width:7px; height:7px; border-radius:50%; background: var(--emerald); box-shadow: 0 0 6px rgba(16,185,129,0.6);"></div>
                            <span style="font-size:12px; font-weight: 600; color: var(--text-secondary);">{{ $name }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================
         Threat Distribution + Hourly Chart
    ============================================================ -->
    <div class="grid-2">

        <!-- Threats by Type -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <div class="icon-wrap icon-amber">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0 0 20.25 18V6A2.25 2.25 0 0 0 18 3.75H6A2.25 2.25 0 0 0 3.75 6v12A2.25 2.25 0 0 0 6 20.25Z" /></svg>
                    </div>
                    Top Threat Types
                </div>
            </div>
            @if(count($threatsByType) > 0)
                @php $maxCount = max($threatsByType); @endphp
                @foreach($threatsByType as $type => $count)
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 10px;">
                        <div style="font-size: 12px; font-weight: 600; width: 160px; color: var(--text-secondary); flex-shrink: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            {{ str_replace('_', ' ', $type) }}
                        </div>
                        <div class="progress-bar" style="flex: 1; height: 7px;">
                            <div class="progress-fill" style="width: {{ ($count / $maxCount) * 100 }}%; background: linear-gradient(90deg, var(--rose), var(--amber));"></div>
                        </div>
                        <div style="font-size: 12px; font-weight: 700; color: var(--text-primary); width: 30px; text-align: right;">{{ $count }}</div>
                    </div>
                @endforeach
            @else
                <div class="empty-state" style="padding: 24px 0;">
                    <p>No threat data yet. Run simulations to see distribution.</p>
                </div>
            @endif
        </div>

        <!-- Hourly Activity -->
        <div class="card">
            <div class="card-header">
                <div class="card-title">
                    <div class="icon-wrap icon-purple">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" /></svg>
                    </div>
                    12-Hour Activity
                </div>
            </div>
            @php
                $maxH = max(array_column($hourlyData, 'count') + [0]);
                if ($maxH == 0) $maxH = 1;
            @endphp
            <div style="display: flex; align-items: flex-end; gap: 4px; height: 100px; padding-bottom: 4px; margin-bottom: 6px;">
                @foreach($hourlyData as $h)
                    @php $pct = min(100, ($h['count'] / $maxH) * 100); @endphp
                    <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 2px; height: 100%;">
                        <div style="flex: 1; display: flex; align-items: flex-end; width: 100%;">
                            <div style="width: 100%; height: {{ $pct }}%; min-height: 4px; background: linear-gradient(to top, var(--cyan), var(--purple)); border-radius: 3px 3px 0 0; opacity: 0.8;" data-tip="{{ $h['count'] }} threats at {{ $h['label'] }}"></div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 10px; color: var(--text-muted); font-family: 'JetBrains Mono', monospace;">
                @foreach($hourlyData as $idx => $h)
                    @if($idx % 3 == 0)
                        <span>{{ $h['label'] }}</span>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    <!-- ============================================================
         Quick Lab Launch Cards
    ============================================================ -->
    <div style="margin-top: 20px;">
        <div class="section-title">Quick Launch Labs</div>
        <div class="grid-4">
            <a href="{{ route('lab.waf') }}" class="card" style="text-decoration:none; cursor:pointer; border-left: 3px solid var(--rose);">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
                    <div class="icon-wrap icon-rose" style="width:36px; height:36px; border-radius:10px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.362 5.214A8.252 8.252 0 0 1 12 21 8.25 8.25 0 0 1 6.038 7.047 8.287 8.287 0 0 0 9 9.601a8.983 8.983 0 0 1 3.361-6.867 8.21 8.21 0 0 0 3 2.48Z" /></svg>
                    </div>
                    <div style="font-size:14px; font-weight:700; color:var(--text-primary);">WAF Firewall Lab</div>
                </div>
                <p style="font-size:12px; color:var(--text-muted);">Test SQL Injection, XSS, LFI, RCE, Command Injection and more attack vectors against the live WAF engine.</p>
            </a>

            <a href="{{ route('lab.rate-limiter') }}" class="card" style="text-decoration:none; cursor:pointer; border-left: 3px solid var(--amber);">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
                    <div class="icon-wrap icon-amber" style="width:36px; height:36px; border-radius:10px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" /></svg>
                    </div>
                    <div style="font-size:14px; font-weight:700; color:var(--text-primary);">Rate Limiter Lab</div>
                </div>
                <p style="font-size:12px; color:var(--text-muted);">Simulate DDoS attacks, burst floods, sliding-window and token-bucket rate limiting strategies in real-time.</p>
            </a>

            <a href="{{ route('lab.bot') }}" class="card" style="text-decoration:none; cursor:pointer; border-left: 3px solid var(--purple);">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
                    <div class="icon-wrap icon-purple" style="width:36px; height:36px; border-radius:10px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 0 1-.659 1.591L5 14.5" /></svg>
                    </div>
                    <div style="font-size:14px; font-weight:700; color:var(--text-primary);">Bot Defense Lab</div>
                </div>
                <p style="font-size:12px; color:var(--text-muted);">Replay 10+ bot types: scrapers, headless browsers, credential stuffers — and watch CyberShield block each one.</p>
            </a>

            <a href="{{ route('lab.monitoring') }}" class="card" style="text-decoration:none; cursor:pointer; border-left: 3px solid var(--cyan);">
                <div style="display:flex; align-items:center; gap:12px; margin-bottom:8px;">
                    <div class="icon-wrap icon-cyan" style="width:36px; height:36px; border-radius:10px;">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75" /></svg>
                    </div>
                    <div style="font-size:14px; font-weight:700; color:var(--text-primary);">Threat Monitor</div>
                </div>
                <p style="font-size:12px; color:var(--text-muted);">Live Kibana-style threat log with filtering, search, export, and simulated traffic generation.</p>
            </a>
        </div>
    </div>

</div>
