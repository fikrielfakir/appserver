@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mb-4">
    <h1 class="h3 mb-1">Dashboard</h1>
    <p class="text-muted">Overview of your Android platform control system</p>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-4 col-xl-2">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">Total Apps</h6>
                <h2 class="mb-0" id="total-apps">0</h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-2">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">Total Devices</h6>
                <h2 class="mb-0" id="total-devices">0</h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-2">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">AdMob Accounts</h6>
                <h2 class="mb-0" id="total-accounts">0</h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-2">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">Notifications</h6>
                <h2 class="mb-0" id="active-notifications">0</h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-2">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">Today Revenue</h6>
                <h2 class="mb-0">$<span id="today-revenue">0.00</span></h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-4 col-xl-2">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">Impressions</h6>
                <h2 class="mb-0" id="today-impressions">0</h2>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Chart -->
<div class="card mb-4">
    <div class="card-body">
        <h5 class="card-title">Revenue Trend (Last 7 Days)</h5>
        <canvas id="revenueChart" height="80"></canvas>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-4">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-app-indicator me-2"></i>Apps</h5>
                <p class="card-text">Manage your Android applications</p>
                <a href="/admin/apps" class="btn btn-primary btn-sm">Manage Apps</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-cash-stack me-2"></i>AdMob</h5>
                <p class="card-text">Configure AdMob accounts</p>
                <a href="/admin/admob-accounts" class="btn btn-primary btn-sm">Manage Accounts</a>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-bell me-2"></i>Notifications</h5>
                <p class="card-text">Send push notifications</p>
                <a href="/admin/notifications" class="btn btn-primary btn-sm">Manage Notifications</a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fetch dashboard stats
    fetch('/api/admin/dashboard/stats', {
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('total-apps').textContent = data.stats.totalApps;
            document.getElementById('total-devices').textContent = data.stats.totalDevices;
            document.getElementById('total-accounts').textContent = data.stats.totalAccounts;
            document.getElementById('active-notifications').textContent = data.stats.activeNotifications;
            document.getElementById('today-revenue').textContent = data.stats.todayRevenue.toFixed(2);
            document.getElementById('today-impressions').textContent = data.stats.todayImpressions;
        }
    })
    .catch(error => console.error('Error loading stats:', error));

    // Revenue Chart
    const ctx = document.getElementById('revenueChart');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Revenue ($)',
                data: [42, 51, 48, 62, 71, 68, 59],
                borderColor: 'rgb(13, 110, 253)',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            }
        }
    });
});
</script>
@endsection
