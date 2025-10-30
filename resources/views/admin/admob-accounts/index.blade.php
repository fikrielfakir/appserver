@extends('layouts.app')

@section('title', 'AdMob Accounts')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">AdMob Accounts</h1>
        <p class="text-muted">Manage AdMob account configurations</p>
    </div>
    <button onclick="showCreateModal()" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Account
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Account Name</th>
                        <th>App</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Weight</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="accounts-table">
                    <tr><td colspan="6" class="text-center py-4 text-muted">Loading accounts...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="accountModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add AdMob Account</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="accountForm">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Account Name</label>
                            <input type="text" class="form-control" id="account_name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">App</label>
                            <select class="form-select" id="app_id" required>
                                <option value="">Select App</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="status">
                                <option value="active">Active</option>
                                <option value="paused">Paused</option>
                                <option value="disabled">Disabled</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Priority</label>
                            <input type="number" class="form-control" id="priority" value="1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Weight (%)</label>
                            <input type="number" class="form-control" id="weight" value="50" min="0" max="100">
                        </div>
                    </div>
                    <hr class="my-4">
                    <h6 class="mb-3">Ad Unit IDs</h6>
                    <div class="mb-3">
                        <label class="form-label">Banner ID</label>
                        <input type="text" class="form-control" id="banner_id" placeholder="ca-app-pub-XXXXX/XXXXX">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Interstitial ID</label>
                        <input type="text" class="form-control" id="interstitial_id" placeholder="ca-app-pub-XXXXX/XXXXX">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Rewarded ID</label>
                        <input type="text" class="form-control" id="rewarded_id" placeholder="ca-app-pub-XXXXX/XXXXX">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">App Open ID</label>
                        <input type="text" class="form-control" id="app_open_id" placeholder="ca-app-pub-XXXXX/XXXXX">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Native ID</label>
                        <input type="text" class="form-control" id="native_id" placeholder="ca-app-pub-XXXXX/XXXXX">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveAccount()">Save Account</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let accountModal;

document.addEventListener('DOMContentLoaded', function() {
    accountModal = new bootstrap.Modal(document.getElementById('accountModal'));
    loadApps();
    loadAccounts();
});

function loadApps() {
    fetch('/api/admin/apps', { credentials: 'include' })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const select = document.getElementById('app_id');
            select.innerHTML = '<option value="">Select App</option>' + 
                data.apps.map(app => `<option value="${app.id}">${app.app_name}</option>`).join('');
        }
    });
}

function loadAccounts() {
    fetch('/api/admin/admob-accounts', { credentials: 'include' })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const tbody = document.getElementById('accounts-table');
            if (data.accounts.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No accounts found. Create your first account!</td></tr>';
                return;
            }
            
            tbody.innerHTML = data.accounts.map(account => `
                <tr>
                    <td><strong>${account.account_name}</strong></td>
                    <td>${account.app ? account.app.app_name : 'N/A'}</td>
                    <td><span class="badge bg-${account.status === 'active' ? 'success' : 'secondary'}">${account.status}</span></td>
                    <td>${account.priority || 0}</td>
                    <td>${account.weight || 0}%</td>
                    <td>
                        <button onclick="deleteAccount('${account.id}')" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }
    });
}

function showCreateModal() {
    document.getElementById('accountForm').reset();
    accountModal.show();
}

function saveAccount() {
    const data = {
        account_name: document.getElementById('account_name').value,
        app_id: document.getElementById('app_id').value,
        status: document.getElementById('status').value,
        priority: parseInt(document.getElementById('priority').value),
        weight: parseInt(document.getElementById('weight').value),
        banner_id: document.getElementById('banner_id').value,
        interstitial_id: document.getElementById('interstitial_id').value,
        rewarded_id: document.getElementById('rewarded_id').value,
        app_open_id: document.getElementById('app_open_id').value,
        native_id: document.getElementById('native_id').value
    };
    
    fetch('/api/admin/admob-accounts', {
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
            accountModal.hide();
            loadAccounts();
        }
    });
}

function deleteAccount(id) {
    if (confirm('Delete this AdMob account?')) {
        fetch(`/api/admin/admob-accounts/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            credentials: 'include'
        })
        .then(() => loadAccounts());
    }
}
</script>
@endsection
