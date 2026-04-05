<?php

namespace App\Livewire\Lab;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use CyberShield\Models\ThreatLog;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class RateLimiterLab extends Component
{
    public string $strategy    = 'sliding_window';
    public int    $maxRequests = 10;
    public int    $windowSecs  = 60;
    public int    $burstSize   = 5;

    public array  $timeline    = [];
    public int    $allowed     = 0;
    public int    $throttled   = 0;
    public bool   $isLoading   = false;
    public ?array $lastResult  = null;

    public static array $strategies = [
        'sliding_window' => [
            'name' => 'Sliding Window',
            'middleware' => 'SlidingWindowRateLimiterMiddleware',
            'desc' => 'Tracks requests in a rolling time window. The most precise general-purpose limiter. Each request shifts the window forward.',
            'use_case' => 'API endpoints, login forms',
        ],
        'token_bucket' => [
            'name' => 'Token Bucket',
            'middleware' => 'TokenBucketRateLimiterMiddleware',
            'desc' => 'Tokens refill at a fixed rate. Allows brief burst traffic while enforcing long-term limits. Ideal for APIs.',
            'use_case' => 'REST APIs with burst tolerance',
        ],
        'fixed_window' => [
            'name' => 'Fixed Window',
            'middleware' => 'IpRateLimiterMiddleware',
            'desc' => 'Fixed time buckets (e.g., per minute). Simple and performant but susceptible to boundary attacks.',
            'use_case' => 'General traffic limiting',
        ],
        'adaptive' => [
            'name' => 'Adaptive Rate Limiter',
            'middleware' => 'AdaptiveRateLimiterMiddleware',
            'desc' => 'Dynamically adjusts limits based on server load and threat score. Tightens limits during attacks.',
            'use_case' => 'High-security APIs',
        ],
        'distributed' => [
            'name' => 'Distributed Limiter',
            'middleware' => 'DistributedRateLimiterMiddleware',
            'desc' => 'Enforces consistent rate limits across multiple server instances using shared Redis state.',
            'use_case' => 'Load-balanced, multi-node apps',
        ],
        'burst' => [
            'name' => 'Burst Limiter',
            'middleware' => 'BurstRateLimiterMiddleware',
            'desc' => 'Allows a configurable burst of concurrent requests then blocks additional traffic immediately.',
            'use_case' => 'DDoS mitigation, flood protection',
        ],
    ];

    public function sendRequest(): void
    {
        $this->isLoading = true;
        $cacheKey = 'lab:rate_limit:' . $this->strategy . ':' . request()->ip();
        $current  = (int) Cache::get($cacheKey, 0) + 1;
        Cache::put($cacheKey, $current, $this->windowSecs);

        $isThrottled = $current > $this->maxRequests;
        $status      = $isThrottled ? 429 : 200;

        $this->addToTimeline($status, $current);
        $this->lastResult = [
            'status'   => $status,
            'count'    => $current,
            'limit'    => $this->maxRequests,
            'throttled'=> $isThrottled,
        ];

        if ($isThrottled) {
            $this->throttled++;
            ThreatLog::create([
                'ip'          => request()->ip(),
                'threat_type' => 'RATE_LIMIT_EXCEEDED',
                'severity'    => 'medium',
                'details'     => [
                    'module'    => 'RATE_LIMITER',
                    'strategy'  => $this->strategy,
                    'count'     => $current,
                    'limit'     => $this->maxRequests,
                    'user_agent'=> request()->userAgent(),
                    'request_method' => 'GET',
                    'request_uri' => '/lab/probe/rate-limit',
                ],
            ]);
        } else {
            $this->allowed++;
        }

        $this->isLoading = false;
    }

    public function simulateBurst(): void
    {
        $this->isLoading = true;

        // Reset the counter first for clean demo
        $cacheKey = 'lab:rate_limit:' . $this->strategy . ':' . request()->ip();
        Cache::forget($cacheKey);
        $this->timeline  = [];
        $this->allowed   = 0;
        $this->throttled = 0;

        $total = $this->maxRequests + $this->burstSize;
        for ($i = 1; $i <= $total; $i++) {
            $current     = (int) Cache::get($cacheKey, 0) + 1;
            Cache::put($cacheKey, $current, $this->windowSecs);
            $isThrottled = $current > $this->maxRequests;
            $status      = $isThrottled ? 429 : 200;
            $this->addToTimeline($status, $current);
            if ($isThrottled) {
                $this->throttled++;
            } else {
                $this->allowed++;
            }
        }

        $this->lastResult = [
            'status'    => 429,
            'count'     => $total,
            'limit'     => $this->maxRequests,
            'throttled' => true,
            'burst_sim' => true,
        ];

        $this->isLoading = false;
    }

    public function resetLab(): void
    {
        $cacheKey = 'lab:rate_limit:' . $this->strategy . ':' . request()->ip();
        Cache::forget($cacheKey);
        $this->timeline   = [];
        $this->allowed    = 0;
        $this->throttled  = 0;
        $this->lastResult = null;
    }

    private function addToTimeline(int $status, int $count): void
    {
        $this->timeline[] = [
            'status' => $status,
            'count'  => $count,
            'time'   => now()->format('H:i:s'),
        ];
        // Keep last 50
        if (count($this->timeline) > 50) {
            $this->timeline = array_slice($this->timeline, -50);
        }
    }

    #[Layout('layouts.app')]
    #[Title('Rate Limiter Lab')]
    public function render()
    {
        $activeStrategy = self::$strategies[$this->strategy] ?? self::$strategies['sliding_window'];
        $usagePercent = $this->maxRequests > 0
            ? min(100, (($this->allowed + $this->throttled) / max(1, $this->maxRequests + 1)) * 100)
            : 0;

        return view('livewire.lab.rate-limiter-lab', compact('activeStrategy', 'usagePercent'));
    }
}
