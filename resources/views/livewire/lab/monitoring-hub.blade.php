@section('page-title', 'Threat Monitor')

<div class="anim-fade-up">
    <div class="page-header" style="display:flex; align-items:flex-start; justify-content:space-between;">
        <div>
            <h1>📊 Threat Monitor</h1>
            <p class="subtitle">Real-time Kibana-style threat intelligence dashboard. All events logged by CyberShield middleware appear here.</p>
        </div>
        <div style="display:flex; gap:8px; align-items:center; flex-shrink:0; margin-top:4px;">
            <button wire:click="generateTraffic" wire:loading.attr="disabled" class="btn btn-danger btn-sm">
                <span wire:loading.remove wire:target="generateTraffic">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 0 0 6 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5"/></svg>
                    Simulate Attack Wave
                </span>
                <span wire:loading wire:target="generateTraffic" style="display:none;align-items:center;gap:6px;">
                    <div class="spinner"></div> Generating...
                </span>
            </button>
            <button wire:click="clearLogs" class="btn btn-ghost btn-sm"
                    onclick="return confirm('Clear all threat logs?')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:13px;height:13px"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                Clear Logs
            </button>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid-4" style="margin-bottom: 20px;">
        <div class="stat-card stat-rose">
            <div class="stat-label">Total Threats</div>
            <div class="stat-value">{{ number_format($stats['total']) }}</div>
        </div>
        <div class="stat-card" style="border-color: rgba(239,68,68,0.3);">
            <div class="stat-label">Critical</div>
            <div class="stat-value" style="color: var(--rose);">{{ $stats['critical'] }}</div>
        </div>
        <div class="stat-card stat-amber">
            <div class="stat-label">High</div>
            <div class="stat-value">{{ $stats['high'] }}</div>
        </div>
        <div class="stat-card" style="">
            <div class="stat-label">Medium + Low</div>
            <div class="stat-value" style="color: var(--emerald);">{{ $stats['medium'] + $stats['low'] }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card" style="margin-bottom: 16px; padding: 14px 18px;">
        <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:center;">
            <div style="display:flex; align-items:center; gap:8px; flex:1; min-width:200px;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px;color:var(--text-muted);flex-shrink:0;"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                <input type="text" wire:model.live.debounce.300ms="searchIp"
                       class="form-input" style="margin:0;"
                       placeholder="Filter by IP address...">
            </div>
            <select wire:model.live="filterSeverity" class="form-select" style="width:auto; min-width:160px;">
                <option value="">All Severities</option>
                <option value="critical">Critical</option>
                <option value="high">High</option>
                <option value="medium">Medium</option>
                <option value="low">Low</option>
            </select>
            <select wire:model.live="filterModule" class="form-select" style="width:auto; min-width:160px;">
                <option value="">All Modules</option>
                <option value="WAF">WAF Firewall</option>
                <option value="RATE_LIMIT">Rate Limiter</option>
                <option value="BOT">Bot Defense</option>
                <option value="NETWORK">Network Guard</option>
                <option value="API">API Security</option>
                <option value="AUTH">Authentication</option>
            </select>
        </div>
    </div>

    <!-- Threat Log Table -->
    <div class="card" style="padding: 0; overflow: hidden;">
        <div style="overflow-x: auto;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>IP Address</th>
                        <th>Threat Type</th>
                        <th>Severity</th>
                        <th>Module</th>
                        <th>Payload / Details</th>
                        <th>User Agent</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        @php
                            $details = is_array($log->details) ? $log->details : json_decode($log->details ?? '{}', true);
                            $rowClass = match($log->severity) {
                                'critical' => 'threat-row-critical',
                                'high'     => 'threat-row-high',
                                default    => '',
                            };
                        @endphp
                        <tr class="{{ $rowClass }}">
                            <td class="mono" style="white-space:nowrap; color:var(--text-muted); font-size:11px;">
                                {{ $log->created_at?->format('H:i:s') ?? 'N/A' }}<br>
                                <span style="font-size:10px;">{{ $log->created_at?->format('m/d') }}</span>
                            </td>
                            <td class="mono" style="color:var(--cyan); font-size:12px;">{{ $log->ip }}</td>
                            <td>
                                <span style="font-size:12px; font-weight:600; color:var(--text-primary);">
                                    {{ str_replace('_', ' ', $log->threat_type) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge badge-{{ match($log->severity) {
                                    'critical' => 'critical',
                                    'high'     => 'high',
                                    'medium'   => 'medium',
                                    default    => 'low'
                                } }}">{{ strtoupper($log->severity ?? 'low') }}</span>
                            </td>
                            <td>
                                <span class="badge badge-info" style="font-size:9px;">
                                    {{ $details['module'] ?? 'SYSTEM' }}
                                </span>
                            </td>
                            <td style="max-width:250px;">
                                <div style="font-size:11.5px; color:var(--text-secondary); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:240px;"
                                     data-tip="{{ htmlspecialchars(substr($details['payload'] ?? 'N/A', 0, 100)) }}">
                                    {{ substr($details['payload'] ?? 'N/A', 0, 50) }}{{ strlen($details['payload'] ?? '') > 50 ? '...' : '' }}
                                </div>
                            </td>
                            <td>
                                <div style="font-size:11px; color:var(--text-muted); overflow:hidden; text-overflow:ellipsis; white-space:nowrap; max-width:180px;">
                                    {{ substr($details['user_agent'] ?? 'N/A', 0, 40) }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state" style="padding: 40px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                                    <h4>No threat logs found</h4>
                                    <p>Run attack simulations from other lab pages, or click "Simulate Attack Wave" above.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div style="padding: 14px 18px; border-top: 1px solid var(--border-subtle); display:flex; align-items:center; justify-content:space-between;">
                <div style="font-size:12px; color:var(--text-muted);">
                    Showing {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }} events
                </div>
                <div style="display:flex; gap:6px;">
                    @if($logs->onFirstPage())
                        <button class="btn btn-ghost btn-sm" disabled>← Prev</button>
                    @else
                        <button wire:click="previousPage" class="btn btn-ghost btn-sm">← Prev</button>
                    @endif
                    @if($logs->hasMorePages())
                        <button wire:click="nextPage" class="btn btn-ghost btn-sm">Next →</button>
                    @else
                        <button class="btn btn-ghost btn-sm" disabled>Next →</button>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
