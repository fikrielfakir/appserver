@extends('layouts.app')

@section('title', 'Analytics')

@section('content')
<div class="mb-4">
    <h1 class="h3 mb-1">Analytics</h1>
    <p class="text-muted">Monitor AdMob performance metrics</p>
</div>

<!-- Stats Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">Total Impressions</h6>
                <h2 class="mb-0" id="total-impressions">0</h2>
                <small class="text-muted">All time</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">Total Clicks</h6>
                <h2 class="mb-0" id="total-clicks">0</h2>
                <small class="text-muted">All time</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">Total Revenue</h6>
                <h2 class="mb-0">$<span id="total-revenue">0.00</span></h2>
                <small class="text-muted">All time</small>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card">
            <div class="card-body">
                <h6 class="card-subtitle text-muted mb-2">CTR</h6>
                <h2 class="mb-0"><span id="ctr">0.00</span>%</h2>
                <small class="text-muted">Click-through rate</small>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Impressions & Clicks Trend</h5>
                <canvas id="impressionsChart" height="60"></canvas>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Ad Unit Distribution</h5>
                <canvas id="adUnitsChart" height="120"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('total-impressions').textContent = '1,245,890';
    document.getElementById('total-clicks').textContent = '12,458';
    document.getElementById('total-revenue').textContent = '8,456.78';
    document.getElementById('ctr').textContent = '1.00';

    // Impressions Chart
    new Chart(document.getElementById('impressionsChart'), {
        type: 'line',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Impressions',
                data: [45000, 51000, 48000, 62000, 71000, 68000, 59000],
                borderColor: 'rgb(13, 110, 253)',
                backgroundColor: 'rgba(13, 110, 253, 0.1)',
                tension: 0.4
            }, {
                label: 'Clicks',
                data: [450, 510, 480, 620, 710, 680, 590],
                borderColor: 'rgb(25, 135, 84)',
                backgroundColor: 'rgba(25, 135, 84, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            }
        }
    });

    // Ad Units Chart
    new Chart(document.getElementById('adUnitsChart'), {
        type: 'doughnut',
        data: {
            labels: ['Banner', 'Interstitial', 'Rewarded', 'Native'],
            datasets: [{
                data: [45, 30, 15, 10],
                backgroundColor: [
                    'rgb(13, 110, 253)',
                    'rgb(25, 135, 84)',
                    'rgb(255, 193, 7)',
                    'rgb(220, 53, 69)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endsection
