@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="mb-4">
    <h1 class="h3 mb-1">Settings</h1>
    <p class="text-muted">Configure AdMob switching strategies</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Switching Rules Configuration</h5>
                <form id="settingsForm">
                    <div class="mb-3">
                        <label class="form-label">Select App</label>
                        <select class="form-select" id="app_id" onchange="loadSwitchingRule()">
                            <option value="">Select an app to configure</option>
                        </select>
                    </div>
                    
                    <div id="ruleConfig" style="display:none;">
                        <div class="mb-3">
                            <label class="form-label">Switching Strategy</label>
                            <select class="form-select" id="strategy">
                                <option value="weighted_random">Weighted Random</option>
                                <option value="sequential">Sequential Rotation</option>
                                <option value="geographic">Geographic Targeting</option>
                                <option value="time_based">Time-Based</option>
                            </select>
                            <small class="text-muted">How AdMob accounts are switched</small>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Rotation Interval</label>
                            <select class="form-select" id="rotation_interval">
                                <option value="hourly">Hourly</option>
                                <option value="daily">Daily</option>
                                <option value="weekly">Weekly</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="fallback_enabled">
                                <label class="form-check-label" for="fallback_enabled">
                                    Enable Fallback Account
                                </label>
                            </div>
                            <small class="text-muted">Use backup account if primary fails</small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="ab_testing_enabled">
                                <label class="form-check-label" for="ab_testing_enabled">
                                    Enable A/B Testing
                                </label>
                            </div>
                            <small class="text-muted">Test different account configurations</small>
                        </div>
                        
                        <button type="button" onclick="saveSettings()" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">API Documentation</h5>
                <p class="text-muted small">Endpoints for Android apps:</p>
                <hr>
                <h6 class="small">Configuration</h6>
                <code class="small">GET /api/v1/config/:packageName</code>
                <hr>
                <h6 class="small">Analytics</h6>
                <code class="small">POST /api/v1/analytics/admob</code>
                <hr>
                <h6 class="small">Device Registration</h6>
                <code class="small">POST /api/v1/device/register</code>
                <hr>
                <h6 class="small">Notifications</h6>
                <code class="small">GET /api/v1/notifications/pending</code>
                <p class="text-muted small mt-3">
                    <i class="bi bi-info-circle me-1"></i>
                    See full documentation in README
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    loadApps();
});

function loadApps() {
    fetch('/api/admin/apps', { credentials: 'include' })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById('app_id');
            select.innerHTML = '<option value="">Select an app to configure</option>' + 
                data.apps.map(app => `<option value="${app.id}">${app.app_name}</option>`).join('');
        }
    });
}

function loadSwitchingRule() {
    const appId = document.getElementById('app_id').value;
    if (!appId) {
        document.getElementById('ruleConfig').style.display = 'none';
        return;
    }
    
    document.getElementById('ruleConfig').style.display = 'block';
    
    fetch(`/api/admin/switching-rules/${appId}`, { credentials: 'include' })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.rule) {
            document.getElementById('strategy').value = data.rule.strategy || 'weighted_random';
            document.getElementById('rotation_interval').value = data.rule.rotation_interval || 'daily';
            document.getElementById('fallback_enabled').checked = data.rule.fallback_enabled || false;
            document.getElementById('ab_testing_enabled').checked = data.rule.ab_testing_enabled || false;
        }
    });
}

function saveSettings() {
    const appId = document.getElementById('app_id').value;
    const data = {
        app_id: appId,
        strategy: document.getElementById('strategy').value,
        rotation_interval: document.getElementById('rotation_interval').value,
        fallback_enabled: document.getElementById('fallback_enabled').checked,
        ab_testing_enabled: document.getElementById('ab_testing_enabled').checked,
        geographic_rules: null
    };
    
    fetch('/api/admin/switching-rules', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        credentials: 'include',
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Settings saved successfully!');
        }
    });
}
</script>
@endsection
