@extends('layouts.app')

@section('title', 'Apps')

@section('content')
<div>
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">Apps</h1>
        <button onclick="showCreateModal()" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Add New App
        </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">App Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Package Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Devices</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody id="apps-table" class="bg-white divide-y divide-gray-200">
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">Loading apps...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
function loadApps() {
    fetch('/api/admin/apps')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const tbody = document.getElementById('apps-table');
                if (data.apps.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="5" class="px-6 py-4 text-center text-gray-500">No apps found</td></tr>';
                    return;
                }
                
                tbody.innerHTML = data.apps.map(app => `
                    <tr>
                        <td class="px-6 py-4">${app.app_name}</td>
                        <td class="px-6 py-4">${app.package_name}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded ${app.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800'}">
                                ${app.status}
                            </span>
                        </td>
                        <td class="px-6 py-4">${app.devices_count || 0}</td>
                        <td class="px-6 py-4">
                            <a href="/admin/apps/${app.id}" class="text-blue-600 hover:text-blue-800 mr-3">View</a>
                            <button onclick="deleteApp('${app.id}')" class="text-red-600 hover:text-red-800">Delete</button>
                        </td>
                    </tr>
                `).join('');
            }
        })
        .catch(error => console.error('Error loading apps:', error));
}

function showCreateModal() {
    alert('Create app modal - implement as needed');
}

function deleteApp(id) {
    if (confirm('Are you sure you want to delete this app?')) {
        fetch(`/api/admin/apps/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
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

document.addEventListener('DOMContentLoaded', loadApps);
</script>
@endsection
