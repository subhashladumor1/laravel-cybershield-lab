@section('page-title', 'Artisan Terminal')

<div class="anim-fade-up">
    <div class="page-header">
        <h1>⌨ Artisan Terminal</h1>
        <p class="subtitle">
            Execute CyberShield, security:scan, security:report, and Laravel artisan commands directly from the browser.
            <strong style="color:var(--cyan);">{{ count(\App\Livewire\Lab\ArtisanTerminal::getAllowedCommands()) }} commands</strong> available — type <code style="color:var(--amber);">help</code> to explore.
        </p>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 280px; gap: 20px; align-items: start;">

        {{-- ════════════════════════════════════════════
             TERMINAL PANEL
        ════════════════════════════════════════════ --}}
        <div>
            <div class="terminal" style="min-height: 560px; display:flex; flex-direction:column;">

                {{-- Output Area --}}
                <div style="flex: 1; overflow-y: auto; max-height: 490px; padding-right: 4px;" id="terminal-output">
                    @foreach($output as $block)

                        {{-- ASCII banner --}}
                        @if($block['type'] === 'banner')
                            @foreach($block['lines'] as $line)
                                <div style="color: var(--cyan); font-size:11px; font-weight: 600; white-space: pre; line-height:1.5;">{{ $line }}</div>
                            @endforeach

                        {{-- Prompt echo --}}
                        @elseif($block['type'] === 'prompt')
                            <div style="margin-top: 14px; display:flex; gap:8px; align-items:center;">
                                <span style="color: var(--emerald); font-weight:700; white-space:nowrap;">root@cybershield:~$</span>
                                <span style="color: var(--text-primary); font-weight:600;">{{ $block['text'] }}</span>
                            </div>

                        {{-- Success / command output --}}
                        @elseif($block['type'] === 'success')
                            @foreach($block['lines'] ?? [$block['text'] ?? ''] as $line)
                                <div style="color: var(--emerald); white-space: pre-wrap; line-height: 1.7; font-size:12.5px;">{{ $line }}</div>
                            @endforeach

                        {{-- Warning --}}
                        @elseif($block['type'] === 'warn')
                            @foreach($block['lines'] ?? [$block['text'] ?? ''] as $line)
                                <div style="color: var(--amber); white-space: pre-wrap; line-height: 1.7; font-size:12.5px;">{{ $line }}</div>
                            @endforeach

                        {{-- Info / help text --}}
                        @elseif($block['type'] === 'info')
                            @foreach($block['lines'] ?? [] as $line)
                                <div style="color: var(--cyan); white-space: pre-wrap; line-height: 1.7; font-size:12.5px;">{{ $line }}</div>
                            @endforeach

                        {{-- Error --}}
                        @elseif($block['type'] === 'error')
                            @foreach($block['lines'] ?? [$block['text'] ?? ''] as $line)
                                <div style="color: var(--rose); white-space: pre-wrap; line-height:1.7; font-size:12.5px;">{{ $line }}</div>
                            @endforeach

                        @endif
                    @endforeach
                </div>

                {{-- Input Row --}}
                <div style="display: flex; align-items: center; gap: 10px; margin-top: 16px;
                            border-top: 1px solid rgba(0,212,255,0.15); padding-top: 14px;">
                    <span style="color: var(--emerald); font-weight: 700; font-size: 13px; white-space: nowrap; flex-shrink:0;">
                        root@cybershield:~$
                    </span>
                    <input type="text"
                           wire:model="input"
                           wire:keydown.enter="executeCommand"
                           id="terminal-input"
                           class="terminal-input"
                           placeholder="Type a command and press Enter…"
                           autocomplete="off"
                           spellcheck="false">
                    <button wire:click="executeCommand"
                            wire:loading.attr="disabled"
                            wire:target="executeCommand"
                            class="btn btn-primary btn-sm"
                            style="flex-shrink:0; min-width:56px; justify-content:center;">
                        <span wire:loading.remove wire:target="executeCommand">Run</span>
                        <span wire:loading wire:target="executeCommand" style="display:none;">
                            <div class="spinner" style="width:12px;height:12px;border-width:2px;"></div>
                        </span>
                    </button>
                </div>
            </div>

            {{-- Quick-copy help hints --}}
            <div style="margin-top:10px; display:flex; gap:8px; flex-wrap:wrap;">
                @foreach(['help', 'cybershield:status', 'cybershield:list-middleware', 'security:scan:quick', 'security:scan:full', 'security:report:summary', 'whoami', 'clear'] as $hint)
                    <span wire:click="$set('input', '{{ $hint }}')"
                          style="font-family:'JetBrains Mono',monospace; font-size:10.5px; color:var(--cyan);
                                 background:var(--cyan-dim); border:1px solid rgba(0,212,255,0.2);
                                 padding:3px 10px; border-radius:4px; cursor:pointer; transition:all 0.18s;"
                          onmouseover="this.style.borderColor='var(--cyan)'"
                          onmouseout="this.style.borderColor='rgba(0,212,255,0.2)'">
                        {{ $hint }}
                    </span>
                @endforeach
            </div>
        </div>

        {{-- ════════════════════════════════════════════
             RIGHT SIDEBAR — Command Groups
        ════════════════════════════════════════════ --}}
        <div style="display:flex; flex-direction:column; gap:12px; max-height: 840px; overflow-y:auto; padding-right:4px;">

            @php
                $colorMap = [
                    'cyan'   => ['text' => 'var(--cyan)',    'bg' => 'var(--cyan-dim)',    'badge' => 'rgba(0,212,255,0.15)'],
                    'amber'  => ['text' => 'var(--amber)',   'bg' => 'rgba(245,158,11,0.08)', 'badge' => 'rgba(245,158,11,0.15)'],
                    'emerald'=> ['text' => 'var(--emerald)', 'bg' => 'rgba(16,185,129,0.08)', 'badge' => 'rgba(16,185,129,0.15)'],
                    'purple' => ['text' => 'var(--purple)',  'bg' => 'rgba(139,92,246,0.08)', 'badge' => 'rgba(139,92,246,0.15)'],
                    'rose'   => ['text' => 'var(--rose)',    'bg' => 'rgba(244,63,94,0.08)',  'badge' => 'rgba(244,63,94,0.15)'],
                ];
            @endphp

            @foreach(\App\Livewire\Lab\ArtisanTerminal::$commandGroups as $group)
                @php $col = $colorMap[$group['color']] ?? $colorMap['cyan']; @endphp
                <div class="card" style="padding:0; overflow:hidden;">
                    {{-- Group Header (collapsible toggle via Alpine) --}}
                    <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
                        <div @click="open = !open"
                             style="display:flex; align-items:center; justify-content:space-between;
                                    padding:9px 12px; cursor:pointer; transition:background 0.15s;"
                             :style="open ? 'background:{{ $col['bg'] }};' : ''"
                             class="scan-module-header">
                            <div style="display:flex; align-items:center; gap:8px;">
                                <span style="width:7px; height:7px; border-radius:50%; background:{{ $col['text'] }}; flex-shrink:0; display:inline-block;"></span>
                                <span style="font-size:11.5px; font-weight:700; color:{{ $col['text'] }};">{{ $group['label'] }}</span>
                            </div>
                            <div style="display:flex; align-items:center; gap:6px;">
                                <span style="font-size:9.5px; font-weight:700; background:{{ $col['badge'] }};
                                             color:{{ $col['text'] }}; padding:1px 6px; border-radius:3px;">
                                    {{ count($group['commands']) }}
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"
                                     style="width:12px;height:12px;color:var(--text-muted);transition:transform 0.2s;"
                                     :style="open ? 'transform:rotate(180deg)' : ''">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                                </svg>
                            </div>
                        </div>

                        {{-- Command List --}}
                        <div x-show="open" x-collapse style="border-top:1px solid var(--border-subtle);">
                            @foreach($group['commands'] as $cmd => $desc)
                                <div wire:click="$set('input', '{{ $cmd }}')"
                                     style="display:flex; flex-direction:column; padding:7px 12px;
                                            border-bottom:1px solid var(--border-subtle); cursor:pointer;
                                            transition:background 0.15s;"
                                     onmouseover="this.style.background='{{ $col['bg'] }}'"
                                     onmouseout="this.style.background='transparent'">
                                    <code style="font-family:'JetBrains Mono',monospace; font-size:10px;
                                                 color:{{ $col['text'] }}; font-weight:600; word-break:break-all;">{{ $cmd }}</code>
                                    <span style="font-size:9.5px; color:var(--text-muted); margin-top:1px;">{{ $desc }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach

            {{-- Security note --}}
            <div class="card" style="padding:12px;">
                <div class="section-title" style="margin-bottom:6px;">🔒 Sandboxed</div>
                <p style="font-size:11px; color:var(--text-muted); line-height:1.6;">
                    Only pre-approved commands run here. Arbitrary shell commands are disabled.
                    Security:scan commands attempt real execution and fall back to simulation.
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-scroll output + refocus input on every Livewire update
    document.addEventListener('livewire:updated', () => {
        const out = document.getElementById('terminal-output');
        if (out) out.scrollTop = out.scrollHeight;
        setTimeout(() => {
            const inp = document.getElementById('terminal-input');
            if (inp) inp.focus();
        }, 50);
    });
    document.addEventListener('DOMContentLoaded', () => {
        const inp = document.getElementById('terminal-input');
        if (inp) inp.focus();
    });
</script>
@endpush
