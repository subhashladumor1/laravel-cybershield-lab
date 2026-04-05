<div class="animate-fade-in">
    <div class="card" style="margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 2rem;">
            <div style="flex: 1; min-width: 300px;">
                <h2 style="font-weight: 800; font-size: 1.75rem; color: var(--text-main); margin-bottom: 0.5rem; letter-spacing: -0.025em;">🔬 Project Security Auditor</h2>
                <p style="color: var(--text-muted); font-size: 1rem; line-height: 1.6;">Run a deep static analysis audit across your entire Laravel codebase.</p>
            </div>
            
            <div class="stat-card" style="display: flex; gap: 1rem; align-items: center; background: rgba(0,0,0,0.2); border-color: rgba(255,255,255,0.05); min-width: 450px; padding: 1.5rem;">
                <div style="flex: 1;">
                    <label style="display: block; font-size: 0.75rem; font-weight: 700; color: var(--text-muted); margin-bottom: 0.5rem; text-transform: uppercase;">Scan Depth Engine</label>
                    <select wire:model="scanDepth" class="btn btn-outline" style="width: 100%; border-color: rgba(255,255,255,0.1); background: rgba(0,0,0,0.3); appearance: none; padding-right: 2.5rem; background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%20width%3D%2224%22%20height%3D%2224%20viewBox%3D%220%200%2024%2024%22%20fill%3D%22none%22%20stroke%3D%22%2394a3b8%22%20stroke-width%3D%222%22%20stroke-linecap%3D%22round%22%20stroke-linejoin%3D%22round%22%3E%3Cpolyline%20points%3D%226%209%2012%2015%2018%209%22%3E%3C/polyline%3E%3C/svg%3E'); background-repeat: no-repeat; background-position: right 1rem center; background-size: 1.2rem;">
                        <option value="quick" style="background: #0f172a;">Quick (Surface vulnerabilities)</option>
                        <option value="deep" style="background: #0f172a;">Deep (API & Dependencies)</option>
                        <option value="paranoid" style="background: #0f172a;">Paranoid (Hidden Heuristics)</option>
                    </select>
                </div>
                <button wire:click="runScan" wire:loading.attr="disabled" class="btn btn-primary" style="padding-inline: 1.5rem; display: flex; align-items: center; justify-content: center; gap: 0.5rem; height: 100%; margin-top: 1.25rem;">
                    <span wire:loading.remove wire:target="runScan">
                        <span style="display: flex; align-items: center; gap: 0.5rem;">
                            <i data-lucide="shield-search" style="width: 18px; height: 18px;"></i> Scan
                        </span>
                    </span>
                    <span wire:loading wire:target="runScan">
                        <span style="display: flex; align-items: center; gap: 0.5rem;">
                            <i data-lucide="loader-2" class="animate-spin" style="width: 18px; height: 18px;"></i> ...
                        </span>
                    </span>
                </button>
            </div>
        </div>

        @if($scanResults)
            <div class="grid animate-fade-in" style="grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 1.5rem;">
                @foreach($scanResults as $category => $issues)
                    <div class="stat-card" style="border-top: 4px solid {{ count($issues) > 0 ? '#ef4444' : '#10b981' }}; padding: 1.5rem; background: rgba(0,0,0,0.2);">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem;">
                            <h4 style="font-weight: 800; font-size: 1.05rem; color: #f8fafc; display: flex; align-items: center; gap: 0.5rem;">
                                <i data-lucide="{{ count($issues) > 0 ? 'alert-triangle' : 'check-circle' }}" style="color: {{ count($issues) > 0 ? 'var(--danger)' : 'var(--success)' }}; width: 18px; height: 18px;"></i>
                                {{ $category }}
                            </h4>
                            @if(count($issues) > 0)
                                <span class="badge badge-danger" style="font-size: 0.7rem; padding: 0.25rem 0.6rem;">{{ count($issues) }} ISSUES</span>
                            @else
                                <span class="badge badge-success" style="font-size: 0.7rem; padding: 0.25rem 0.6rem;">CLEAN</span>
                            @endif
                        </div>
                        
                        @if(count($issues) > 0)
                            <div style="background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.1); border-radius: 0.75rem; padding: 1rem;">
                                <ul style="font-size: 0.85rem; color: #cbd5e1; padding-left: 1.25rem; margin: 0; line-height: 1.6;">
                                    @foreach($issues as $issue)
                                        <li style="margin-bottom: 0.5rem;">
                                            @if(is_array($issue))
                                                <span style="font-family: monospace; color: #fca5a5;">{{ implode(', ', $issue) }}</span>
                                            @else
                                                {{ $issue }}
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div style="padding: 1rem; background: rgba(16, 185, 129, 0.05); border: 1px dashed rgba(16, 185, 129, 0.2); border-radius: 0.75rem; display: flex; align-items: center; gap: 0.5rem; color: #6ee7b7; font-size: 0.85rem; font-weight: 600;">
                                <i data-lucide="shield-check" style="width: 16px; height: 16px;"></i> No vulnerabilities detected.
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <div class="stat-card" style="text-align: center; padding: 6rem 2rem; border: 2px dashed rgba(56, 189, 248, 0.2); border-radius: 1.5rem; background: rgba(56, 189, 248, 0.02); display: flex; flex-direction: column; align-items: center;">
                <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, rgba(56, 189, 248, 0.1), transparent); display: flex; align-items: center; justify-content: center; margin-bottom: 1.5rem; border: 1px solid rgba(56, 189, 248, 0.2); box-shadow: 0 0 30px rgba(56, 189, 248, 0.1);">
                    <i data-lucide="shield-search" style="width: 40px; height: 40px; color: var(--primary);"></i>
                </div>
                <h3 style="font-weight: 800; font-size: 1.5rem; margin-bottom: 0.75rem; color: #f8fafc; letter-spacing: -0.025em;">Ready to Scan</h3>
                <p style="color: var(--text-muted); font-size: 0.95rem; max-width: 500px; margin: 0 auto; line-height: 1.6;">
                    CyberShield will analyze your models, controllers, configuration, and dependencies for security misconfigurations and dangerous patterns.
                </p>
            </div>
        @endif
    </div>
</div>
