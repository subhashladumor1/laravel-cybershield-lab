<?php

namespace App\Livewire\Lab;

use Livewire\Component;
use CyberShield\Security\Project\ProjectScanner;

class ScannerLab extends Component
{
    public $scanResults = null;
    public $isScanning = false;
    public $scanDepth = 'quick';

    public function runScan()
    {
        $this->isScanning = true;
        
        // ProjectScanner normally runs via CLI, but we can use it here
        $scanner = new ProjectScanner();
        
        // We'll simulate the scan steps for better UI feedback (Livewire doesn't support streaming easily here without extra setup)
        // But for the lab, we'll just run it and show results.
        
        $baseResults = [
            'Malware' => $scanner->scanMalware(),
            'SQL Injection' => $scanner->scanSqlInjection(),
            'XSS' => $scanner->scanXss(),
            'Data Models' => $scanner->scanModels(),
            'File Security' => $scanner->scanFileUploads(),
            'Configuration' => $scanner->scanConfig(),
            'Bot Security' => $scanner->scanBots(),
            'API Security' => $scanner->scanApi(),
            'Auth' => $scanner->scanAuth(),
            'Dependencies' => $scanner->scanDependencies(),
            'Infrastructure' => $scanner->scanInfrastructure(),
        ];
        
        // Inject Educational Simulation Based on Depth
        // To vividly demonstrate the value of Deep vs Paranoid scans in the lab environment
        $baseResults['Configuration'][] = "Exposed APP_DEBUG signature found via environment heuristic (Simulated)";

        if ($this->scanDepth === 'deep' || $this->scanDepth === 'paranoid') {
            $baseResults['API Security'][] = "Missing Rate Limiter configuration on API fallback route (Simulated)";
        }

        if ($this->scanDepth === 'paranoid') {
            $baseResults['File Security'][] = "Unrestricted zip extraction path traversal in 3rd-party integration package (Simulated Hidden Vulnerability)";
        }

        $this->scanResults = $baseResults;

        $this->isScanning = false;
    }

    public function render()
    {
        return view('livewire.lab.scanner-lab')->layout('layouts.app');
    }
}
