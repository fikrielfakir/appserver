@extends('layouts.app')

@section('title', 'Apps')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Apps</h1>
        <p class="text-muted">Manage your Android applications</p>
    </div>
    <button onclick="showCreateModal()" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add New App
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>App Name</th>
                        <th>Package Name</th>
                        <th>Status</th>
                        <th>Devices</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="apps-table">
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">Loading apps...</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="appModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New App</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="appForm">
                    <div class="mb-3">
                        <label class="form-label">App Name</label>
                        <input type="text" class="form-control" id="app_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Package Name</label>
                        <input type="text" class="form-control" id="package_name" placeholder="com.example.app" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="description" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" id="status">
                            <option value="active">Active</option>
                            <option value="paused">Paused</option>
                            <option value="disabled">Disabled</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveApp()">Save App</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let appModal;

document.addEventListener('DOMContentLoaded', function() {
    appModal = new bootstrap.Modal(document.getElementById('appModal'));
    loadApps();
});

function loadApps() {
    fetch('/api/admin/apps', {
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const tbody = document.getElementById('apps-table');
            if (data.apps.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">No apps found. Create your first app!</td></tr>';
                return;
            }
            
            tbody.innerHTML = data.apps.map(app => `
                <tr>
                    <td>${app.app_name}</td>
                    <td><code>${app.package_name}</code></td>
                    <td><span class="badge bg-${app.status === 'active' ? 'success' : 'secondary'}">${app.status}</span></td>
                    <td>${app.devices_count || 0}</td>
                    <td>
                        <button onclick="deleteApp('${app.id}')" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
            `).join('');
        }
    })
    .catch(error => console.error('Error loading apps:', error));
}

function showCreateModal() {
    document.getElementById('appForm').reset();
    appModal.show();
}

function saveApp() {
    const data = {
        app_name: document.getElementById('app_name').value,
        package_name: document.getElementById('package_name').value,
        description: document.getElementById('description').value,
        status: document.getElementById('status').value
    };
    
    fetch('/api/admin/apps', {
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
            appModal.hide();
            loadApps();
        }
    })
    .catch(error => console.error('Error saving app:', error));
}

function deleteApp(id) {
    if (confirm('Are you sure you want to delete this app?')) {
        fetch(`/api/admin/apps/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadApps();
            }
        })
        .catch(error => console.error('Error deleting app:', error));
    }
}
</script>
@endsection
