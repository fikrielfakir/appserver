@extends('layouts.app')

@section('title', 'Analytics')

@section('content')
<div>
    <h1 class="text-3xl font-bold mb-8">Analytics</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm font-medium mb-2">Total Impressions</h3>
            <p class="text-3xl font-bold" id="total-impressions">0</p>
            <p class="text-sm text-gray-500 mt-1">All time</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm font-medium mb-2">Total Clicks</h3>
            <p class="text-3xl font-bold" id="total-clicks">0</p>
            <p class="text-sm text-gray-500 mt-1">All time</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm font-medium mb-2">Total Revenue</h3>
            <p class="text-3xl font-bold">$<span id="total-revenue">0.00</span></p>
            <p class="text-sm text-gray-500 mt-1">All time</p>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-gray-500 text-sm font-medium mb-2">CTR</h3>
            <p class="text-3xl font-bold"><span id="ctr">0.00</span>%</p>
            <p class="text-sm text-gray-500 mt-1">Click-through rate</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Recent Events</h2>
            <div class="space-y-2">
                <p class="text-gray-500 text-sm">Analytics data visualization would go here</p>
                <p class="text-gray-400 text-xs">Connect to analytics API to view real-time data</p>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-4">Top Performing Apps</h2>
            <div class="space-y-2">
                <p class="text-gray-500 text-sm">App performance metrics would go here</p>
                <p class="text-gray-400 text-xs">Based on revenue and impressions</p>
            </div>
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
    document.getElementById('total-impressions').textContent = '0';
    document.getElementById('total-clicks').textContent = '0';
    document.getElementById('total-revenue').textContent = '0.00';
    document.getElementById('ctr').textContent = '0.00';
});
</script>
@endsection
