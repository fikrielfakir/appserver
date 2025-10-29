@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div>
    <h1 class="text-3xl font-bold mb-8">Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm font-medium mb-2">Total Apps</h3>
            <p class="text-3xl font-bold" id="total-apps">0</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm font-medium mb-2">Total Devices</h3>
            <p class="text-3xl font-bold" id="total-devices">0</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm font-medium mb-2">AdMob Accounts</h3>
            <p class="text-3xl font-bold" id="total-accounts">0</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm font-medium mb-2">Active Notifications</h3>
            <p class="text-3xl font-bold" id="active-notifications">0</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm font-medium mb-2">Today Revenue</h3>
            <p class="text-3xl font-bold">$<span id="today-revenue">0.00</span></p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm font-medium mb-2">Today Impressions</h3>
            <p class="text-3xl font-bold" id="today-impressions">0</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-bold mb-4">Recent Activity</h2>
        <div class="text-gray-500">
            <p>Dashboard statistics loaded successfully.</p>
        </div>
    </div>
</div>

<script>
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

document.addEventListener('DOMContentLoaded', function() {
    const token = getCookie('auth_token');
    
    fetch('/api/admin/dashboard/stats', {
        headers: {
            'Authorization': 'Bearer ' + token
        }
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
});
</script>
@endsection
