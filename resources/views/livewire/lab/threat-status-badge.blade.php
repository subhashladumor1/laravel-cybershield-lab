<div wire:poll.10s>
    @if($hasHighThreat)
        <div style="padding: 0.5rem 1.25rem; border-radius: 9999px; background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.5); color: #f87171; font-size: 0.75rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem; animation: pulseGlow 2s infinite; box-shadow: 0 0 15px rgba(239, 68, 68, 0.3);">
            <i data-lucide="shield-alert" style="width: 16px; height: 16px;"></i>
            THREAT DETECTED
        </div>
    @else
        <div style="padding: 0.5rem 1.25rem; border-radius: 9999px; background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.3); color: #6ee7b7; font-size: 0.75rem; font-weight: 800; display: flex; align-items: center; gap: 0.5rem;">
            <i data-lucide="shield-check" style="width: 16px; height: 16px;"></i>
            SECURE
        </div>
    @endif
</div>
