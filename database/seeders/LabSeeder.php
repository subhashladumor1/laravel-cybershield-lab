<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LabSeeder extends Seeder
{
    public function run()
    {
        $ips = ['192.168.1.10', '10.0.0.5', '172.16.0.1', '5.4.3.2', '8.8.8.8'];
        $threats = ['SQL_INJECTION', 'XSS_ATTACK', 'PATH_TRAVERSAL', 'BOT_DETECTION', 'RATE_LIMIT_EXCEEDED'];
        $severities = ['low', 'medium', 'high', 'critical'];

        for ($i = 0; $i < 20; $i++) {
            DB::table('cybershield_threat_logs')->insert([
                'ip' => $ips[array_rand($ips)],
                'threat_type' => $threats[array_rand($threats)],
                'severity' => $severities[array_rand($severities)],
                'details' => json_encode([
                    'payload' => 'Simulated attack payload...',
                    'user_agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36...',
                    'method' => 'POST',
                    'uri' => '/api/v1/login'
                ]),
                'created_at' => Carbon::now()->subMinutes(rand(1, 1440))
            ]);
        }
    }
}
