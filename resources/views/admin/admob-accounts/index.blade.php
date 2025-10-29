@extends('layouts.app')

@section('title', 'AdMob Accounts')

@section('content')
<div>
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold">AdMob Accounts</h1>
        <button onclick="showCreateModal()" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Add AdMob Account
        </button>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Account Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">App</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Weight</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody id="accounts-table" class="bg-white divide-y divide-gray-200">
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">Loading accounts...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<script>
function loadAccounts() {
    fetch('/api/admin/admob-accounts', {
        credentials: 'include'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const tbody = document.getElementById('accounts-table');
            if (data.accounts.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No accounts found</td></tr>';
                return;
            }
            
            tbody.innerHTML = data.accounts.map(account => `
                <tr>
                    <td class="px-6 py-4 font-medium">${account.account_name}</td>
                    <td class="px-6 py-4">${account.app ? account.app.app_name : 'N/A'}</td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs rounded ${getStatusColor(account.status)}">
                            ${account.status}
                        </span>
                    </td>
                    <td class="px-6 py-4">${account.priority || 0}</td>
                    <td class="px-6 py-4">${account.weight || 0}%</td>
                    <td class="px-6 py-4">
                        <button onclick="viewAccount('${account.id}')" class="text-blue-600 hover:text-blue-800 mr-3">View</button>
                        <button onclick="deleteAccount('${account.id}')" class="text-red-600 hover:text-red-800">Delete</button>
                    </td>
                </tr>
            `).join('');
        }
    })
    .catch(error => console.error('Error loading accounts:', error));
}

function getStatusColor(status) {
    const colors = {
        'active': 'bg-green-100 text-green-800',
        'paused': 'bg-yellow-100 text-yellow-800',
        'disabled': 'bg-gray-100 text-gray-800'
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
}

function showCreateModal() {
    alert('Create AdMob account modal - implement as needed');
}

function viewAccount(id) {
    alert('View account details - implement as needed');
}

function deleteAccount(id) {
    if (confirm('Are you sure you want to delete this account?')) {
        fetch(`/api/admin/admob-accounts/${id}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            credentials: 'include'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadAccounts();
            }
        })
        .catch(error => console.error('Error deleting account:', error));
    }
}

document.addEventListener('DOMContentLoaded', loadAccounts);
</script>
@endsection
