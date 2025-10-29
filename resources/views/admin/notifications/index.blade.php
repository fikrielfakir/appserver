@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div>
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Notifications</h1>
        <button onclick="showCreateModal()" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Create Notification
        </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Created</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody id="notifications-table" class="bg-white divide-y divide-gray-200">
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Loading notifications...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
}

function loadNotifications() {
    const token = getCookie('auth_token');
    
    fetch('/api/admin/notifications', {
        headers: {
            'Authorization': 'Bearer ' + token
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tbody = document.getElementById('notifications-table');
                if (data.notifications.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No notifications found</td></tr>';
                    return;
                }
                
                tbody.innerHTML = data.notifications.map(notif => `
                    <tr>
                        <td class="px-6 py-4 font-medium">${notif.title}</td>
                        <td class="px-6 py-4">${notif.type}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded ${getStatusColor(notif.status)}">
                                ${notif.status}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded ${getPriorityColor(notif.priority)}">
                                ${notif.priority}
                            </span>
                        </td>
                        <td class="px-6 py-4">${new Date(notif.created_at).toLocaleDateString()}</td>
                        <td class="px-6 py-4">
                            <a href="/admin/notifications/${notif.id}" class="text-blue-600 hover:text-blue-800 mr-3">View</a>
                            <button onclick="deleteNotification('${notif.id}')" class="text-red-600 hover:text-red-800">Delete</button>
                        </td>
                    </tr>
                `).join('');
            }
        })
        .catch(error => console.error('Error loading notifications:', error));
}

function getStatusColor(status) {
    const colors = {
        'sent': 'bg-green-100 text-green-800',
        'draft': 'bg-gray-100 text-gray-800',
        'scheduled': 'bg-blue-100 text-blue-800',
        'failed': 'bg-red-100 text-red-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
}

function getPriorityColor(priority) {
    const colors = {
        'high': 'bg-red-100 text-red-800',
        'normal': 'bg-blue-100 text-blue-800',
        'low': 'bg-gray-100 text-gray-800'
    };
    return colors[priority] || 'bg-gray-100 text-gray-800';
}

function showCreateModal() {
    alert('Create notification modal - implement as needed');
}

function deleteNotification(id) {
    if (confirm('Are you sure you want to delete this notification?')) {
        const token = getCookie('auth_token');
        
        fetch(`/api/admin/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications();
            }
        })
        .catch(error => console.error('Error deleting notification:', error));
    }
}

document.addEventListener('DOMContentLoaded', loadNotifications);
</script>
@endsection
