@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 mb-1">Notifications</h1>
        <p class="text-muted">Manage push notifications</p>
    </div>
    <button onclick="showCreateModal()" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Create Notification
    </button>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="notifications-table">
                    <tr><td colspan="6" class="text-center py-4 text-muted">Loading notifications...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Create Modal -->
<div class="modal fade" id="notificationModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create Notification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="notificationForm">
                    <div class="mb-3">
                        <label class="form-label">App</label>
                        <select class="form-select" id="app_id" required>
                            <option value="">Select App</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Message</label>
                        <textarea class="form-control" id="message" rows="3" required></textarea>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Type</label>
                            <select class="form-select" id="type">
                                <option value="popup">Popup</option>
                                <option value="toast">Toast</option>
                                <option value="banner">Banner</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Priority</label>
                            <select class="form-select" id="priority">
                                <option value="normal">Normal</option>
                                <option value="high">High</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="status">
                                <option value="draft">Draft</option>
                                <option value="scheduled">Scheduled</option>
                                <option value="sent">Send Now</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Target Countries (comma-separated)</label>
                        <input type="text" class="form-control" id="target_countries" placeholder="US, UK, CA">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveNotification()">Save Notification</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let notificationModal;

document.addEventListener('DOMContentLoaded', function() {
    notificationModal = new bootstrap.Modal(document.getElementById('notificationModal'));
    loadApps();
    loadNotifications();
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

function loadNotifications() {
    fetch('/api/admin/notifications', { credentials: 'include' })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const tbody = document.getElementById('notifications-table');
            if (data.notifications.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center py-4 text-muted">No notifications found. Create your first notification!</td></tr>';
                return;
            }
            
            tbody.innerHTML = data.notifications.map(notif => `
                <tr>
                    <td><strong>${notif.title}</strong></td>
                    <td><span class="badge bg-info">${notif.type}</span></td>
                    <td><span class="badge bg-${notif.status === 'sent' ? 'success' : 'secondary'}">${notif.status}</span></td>
                    <td><span class="badge bg-warning">${notif.priority}</span></td>
                    <td>${new Date(notif.created_at).toLocaleDateString()}</td>
                    <td>
                        ${notif.status === 'draft' ? `<button onclick="sendNotification('${notif.id}')" class="btn btn-sm btn-success me-1"><i class="bi bi-send"></i></button>` : ''}
                        <button onclick="deleteNotification('${notif.id}')" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </td>
                </tr>
            `).join('');
        }
    });
}

function showCreateModal() {
    document.getElementById('notificationForm').reset();
    notificationModal.show();
}

function saveNotification() {
    const data = {
        app_id: document.getElementById('app_id').value,
        title: document.getElementById('title').value,
        message: document.getElementById('message').value,
        type: document.getElementById('type').value,
        priority: document.getElementById('priority').value,
        status: document.getElementById('status').value,
        target_countries: document.getElementById('target_countries').value ? 
            document.getElementById('target_countries').value.split(',').map(s => s.trim()) : []
    };
    
    fetch('/api/admin/notifications', {
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
            notificationModal.hide();
            loadNotifications();
        }
    });
}

function sendNotification(id) {
    if (confirm('Send this notification now?')) {
        fetch(`/api/admin/notifications/${id}/send`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            credentials: 'include'
        })
        .then(() => loadNotifications());
    }
}

function deleteNotification(id) {
    if (confirm('Delete this notification?')) {
        fetch(`/api/admin/notifications/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            credentials: 'include'
        })
        .then(() => loadNotifications());
    }
}
</script>
@endsection
