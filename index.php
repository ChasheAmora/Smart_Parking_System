<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RFID Access System i like <menu></menu></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        .header {
            background: white;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        h1 {
            color: #333;
            margin-bottom: 10px;
        }
        
        .button-group {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            font-weight: bold;
        }
        
        .btn-primary {
            background: #667eea;
            color: white;
        }
        
        .btn-primary:hover {
            background: #5a67d8;
            transform: translateY(-2px);
        }
        
        .btn-danger {
            background: #e53e3e;
            color: white;
        }
        
        .btn-danger:hover {
            background: #c53030;
            transform: translateY(-2px);
        }
        
        .btn-success {
            background: #48bb78;
            color: white;
        }
        
        .btn-success:hover {
            background: #38a169;
            transform: translateY(-2px);
        }
        
        .btn-warning {
            background: #ed8936;
            color: white;
        }
        
        .btn-warning:hover {
            background: #dd6b20;
            transform: translateY(-2px);
        }
        
        .btn-info {
            background: #4299e1;
            color: white;
        }
        
        .btn-info:hover {
            background: #3182ce;
            transform: translateY(-2px);
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #667eea;
        }
        
        .stat-label {
            color: #666;
            margin-top: 10px;
        }
        
        .tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }
        
        .tab {
            background: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 5px;
            font-size: 16px;
            transition: all 0.3s;
        }
        
        .tab.active {
            background: #667eea;
            color: white;
        }
        
        .tab-content {
            display: none;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .tab-content.active {
            display: block;
        }
        
        .table-container {
            overflow-x: auto;
            max-height: 500px;
            overflow-y: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background: #f5f5f5;
            color: #333;
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .status-enter {
            color: green;
            font-weight: bold;
        }
        
        .status-exit {
            color: orange;
            font-weight: bold;
        }
        
        .status-denied {
            color: red;
            font-weight: bold;
        }
        
        .inside {
            color: green;
        }
        
        .outside {
            color: red;
        }
        
        .refresh-btn {
            background: #667eea;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
            margin-right: 10px;
        }
        
        .refresh-btn:hover {
            background: #5a67d8;
        }
        
        .reset-btn {
            background: #ed8936;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
        }
        
        .reset-btn:hover {
            background: #dd6b20;
        }
        
        .action-buttons {
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 10px;
            width: 400px;
            text-align: center;
        }
        
        .modal-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
            justify-content: center;
        }
        
        .notification {
            position: fixed;
            top: 20px;
            right: 20px;
            padding: 15px 20px;
            border-radius: 5px;
            z-index: 2000;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            animation: slideIn 0.3s ease-out;
        }
        
        .notification-success {
            background-color: #48bb78;
            color: white;
        }
        
        .notification-error {
            background-color: #e53e3e;
            color: white;
        }
        
        .notification-info {
            background-color: #4299e1;
            color: white;
        }
        
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        
        .loading {
            animation: pulse 1s infinite;
        }
        
        @media print {
            .button-group, .tabs, .refresh-btn, .action-buttons, .btn {
                display: none;
            }
            body {
                background: white;
                padding: 0;
            }
            .stat-card {
                box-shadow: none;
                border: 1px solid #ddd;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div>
                <h1>🚪 RFID Access Control System</h1>
                <p>Real-time monitoring and management</p>
            </div>
            <div class="button-group">
                <button class="btn btn-primary" onclick="window.print()">🖨️ Print Report</button>
                <button class="btn btn-danger" onclick="showDeleteModal()">🗑️ Delete All Logs</button>
                <button class="btn btn-info" onclick="resetAllStates()">🔄 Reset All States</button>
                <button class="btn btn-success" onclick="refreshAll()">🔄 Refresh All</button>
            </div>
        </div>
        
        <div class="stats" id="stats">
            <div class="stat-card loading">Loading...</div>
        </div>
        
        <div class="tabs">
            <button class="tab active" onclick="switchTab('logs', event)">📋 Access Logs</button>
            <button class="tab" onclick="switchTab('inside', event)">🏠 Currently Inside</button>
            <button class="tab" onclick="switchTab('users', event)">👥 Users</button>
            <button class="tab" onclick="switchTab('archive', event)">📦 Archive (Deleted Logs)</button>
        </div>
        
        <div id="logs" class="tab-content active">
            <div class="action-buttons">
                <button class="refresh-btn" onclick="loadLogs()">🔄 Refresh Logs</button>
                <button class="btn btn-warning" onclick="exportToCSV()">📊 Export to CSV</button>
                <button class="btn btn-info" onclick="resetAllStates()">🔄 Reset ALL User States</button>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>UID</th>
                            <th>Status</th>
                            <th>Time</th>
                        </tr>
                    </thead>
                    <tbody id="logs-body"></tbody>
                </table>
            </div>
        </div>
        
        <div id="inside" class="tab-content">
            <button class="refresh-btn" onclick="loadInside()">🔄 Refresh Status</button>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>UID</th>
                            <th>Status</th>
                            <th>Last Access</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="inside-body"></tbody>
                </table>
            </div>
        </div>
        
        <div id="users" class="tab-content">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>UID</th>
                            <th>Role</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody id="users-body"></tbody>
                </table>
            </div>
        </div>
        
        <div id="archive" class="tab-content">
            <div class="action-buttons">
                <button class="refresh-btn" onclick="loadArchive()">🔄 Refresh Archive</button>
                <button class="btn btn-success" onclick="restoreAllLogs()">📥 Restore All Logs</button>
                <button class="btn btn-danger" onclick="clearArchive()">🗑️ Permanently Delete Archive</button>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>UID</th>
                            <th>Status</th>
                            <th>Timestamp</th>
                            <th>Deleted At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="archive-body"></tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>⚠️ Confirm Delete</h3>
            <p>Are you sure you want to delete ALL access logs?</p>
            <p style="color: orange; font-size: 12px;">⚠️ Logs will be MOVED to ARCHIVE, not permanently deleted!</p>
            <p style="color: red; font-size: 12px;">This will reset ALL users to OUTSIDE state!</p>
            <div class="modal-buttons">
                <button class="btn btn-danger" onclick="confirmDelete()">Yes, Move to Archive</button>
                <button class="btn btn-primary" onclick="closeModal()">Cancel</button>
            </div>
        </div>
    </div>
    
    <script>
        let autoRefresh = true;
        
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification notification-${type}`;
            notification.innerHTML = message;
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        
        function switchTab(tabName, event) {
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.tab').forEach(btn => {
                btn.classList.remove('active');
            });
            
            document.getElementById(tabName).classList.add('active');
            if (event && event.target) event.target.classList.add('active');
            
            if (tabName === 'logs') loadLogs();
            else if (tabName === 'inside') loadInside();
            else if (tabName === 'users') loadUsers();
            else if (tabName === 'archive') loadArchive();
        }
        
        function refreshAll() {
            loadStats();
            loadLogs();
            loadInside();
            loadUsers();
            if (document.getElementById('archive').classList.contains('active')) loadArchive();
            showNotification('All data refreshed!', 'success');
        }
        
        function resetAllStates() {
            if (confirm('Reset ALL user states to OUTSIDE? This means every user will start with ENTER on their next scan.')) {
                fetch('api.php?action=reset_all_states', { method: 'POST' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification('✅ ALL users reset! Next scan will be ENTER for everyone.', 'success');
                            refreshAll();
                        }
                    });
            }
        }
        
        function showDeleteModal() {
            document.getElementById('deleteModal').style.display = 'block';
        }
        
        function closeModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
        
        function confirmDelete() {
            fetch('api.php?action=delete_all_logs', { method: 'POST' })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('✅ All logs moved to archive! All users reset to OUTSIDE.', 'success');
                        closeModal();
                        refreshAll();
                    } else {
                        showNotification('Error: ' + data.error, 'error');
                    }
                });
        }
        
        function resetUserState(uid, name) {
            if (confirm(`Reset ${name}'s state to OUTSIDE? Next time they scan, it will be ENTER.`)) {
                fetch('api.php?action=reset_user_state', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ uid: uid })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(`${name} reset to OUTSIDE. Next scan will be ENTER.`, 'success');
                        loadInside();
                        loadStats();
                    }
                });
            }
        }
        
        function manualExit(uid, name) {
            if (confirm(`Force ${name} to exit? This will mark them as OUTSIDE.`)) {
                fetch('api.php?action=manual_exit', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ uid: uid, name: name })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(`${name} marked as EXITED. Next scan will be ENTER.`, 'success');
                        loadInside();
                        loadStats();
                        loadLogs();
                    }
                });
            }
        }
        
        function exportToCSV() {
            fetch('api.php?action=export_logs')
                .then(response => response.json())
                .then(data => {
                    let csv = "ID,Name,UID,Status,Timestamp\n";
                    data.forEach(log => {
                        csv += `${log.id},${log.name || 'Unknown'},${log.uid},${log.status},${log.timestamp}\n`;
                    });
                    
                    const blob = new Blob([csv], { type: 'text/csv' });
                    const url = window.URL.createObjectURL(blob);
                    const a = document.createElement('a');
                    a.href = url;
                    a.download = `rfid_logs_${new Date().toISOString().slice(0,19)}.csv`;
                    a.click();
                    window.URL.revokeObjectURL(url);
                    showNotification('CSV exported!', 'success');
                });
        }
        
        function loadStats() {
            fetch('api.php?action=stats')
                .then(response => response.json())
                .then(data => {
                    const statsHtml = `
                        <div class="stat-card">
                            <div class="stat-number">${data.total_users}</div>
                            <div class="stat-label">Total Users</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">${data.inside_count}</div>
                            <div class="stat-label">Currently Inside</div>
                            <button class="reset-btn" style="margin-top: 10px;" onclick="resetAllStates()">Reset All States</button>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">${data.total_access_today}</div>
                            <div class="stat-label">Access Today</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">${data.denied_today}</div>
                            <div class="stat-label">Denied Today</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number">${data.archive_count || 0}</div>
                            <div class="stat-label">Archived Logs</div>
                            <button class="btn-info" style="margin-top: 10px; padding:5px 10px;" onclick="switchTab('archive', null)">View Archive</button>
                        </div>
                    `;
                    document.getElementById('stats').innerHTML = statsHtml;
                })
                .catch(error => {
                    console.error('Error loading stats:', error);
                    document.getElementById('stats').innerHTML = '<div class="stat-card">Error loading stats</div>';
                });
        }
        
        function loadLogs() {
            fetch('api.php?action=logs&limit=100')
                .then(response => response.json())
                .then(data => {
                    const logsBody = document.getElementById('logs-body');
                    if (data.length === 0) {
                        logsBody.innerHTML = '<tr><td colspan="5">No access logs found</td></tr>';
                    } else {
                        logsBody.innerHTML = data.map(log => `
                            <tr>
                                <td>${log.id}</td>
                                <td>${log.name || 'Unknown'}</td>
                                <td>${log.uid}</td>
                                <td class="status-${log.status.toLowerCase()}">${log.status}</td>
                                <td>${log.timestamp}</td>
                            </tr>
                        `).join('');
                    }
                })
                .catch(error => {
                    console.error('Error loading logs:', error);
                });
        }
        
        function loadInside() {
            fetch('api.php?action=inside')
                .then(response => response.json())
                .then(data => {
                    const insideBody = document.getElementById('inside-body');
                    if (data.length === 0) {
                        insideBody.innerHTML = '<tr><td colspan="5">No one is currently inside</td></tr>';
                    } else {
                        insideBody.innerHTML = data.map(user => `
                            <tr>
                                <td>${user.name}</td>
                                <td>${user.uid}</td>
                                <td class="inside">✅ Inside</td>
                                <td>${user.last_access}</td>
                                <td>
                                    <button class="delete-btn" style="background:#e53e3e; color:white; border:none; padding:5px 10px; border-radius:3px; cursor:pointer;" onclick="manualExit('${user.uid}', '${user.name}')">Force Exit</button>
                                    <button class="reset-btn" onclick="resetUserState('${user.uid}', '${user.name}')">Reset State</button>
                                </td>
                            </tr>
                        `).join('');
                    }
                })
                .catch(error => {
                    console.error('Error loading inside:', error);
                });
        }
        
        function loadUsers() {
            fetch('api.php?action=users')
                .then(response => response.json())
                .then(data => {
                    const usersBody = document.getElementById('users-body');
                    if (data.length === 0) {
                        usersBody.innerHTML = '<tr><td colspan="5">No users found</td></tr>';
                    } else {
                        usersBody.innerHTML = data.map(user => `
                            <tr>
                                <td>${user.id}</td>
                                <td>${user.name}</td>
                                <td>${user.uid}</td>
                                <td>${user.role}</td>
                                <td>${user.created_at}</td>
                            </tr>
                        `).join('');
                    }
                })
                .catch(error => {
                    console.error('Error loading users:', error);
                });
        }
        
        function loadArchive() {
            fetch('api.php?action=archive_logs&limit=100')
                .then(response => response.json())
                .then(data => {
                    const archiveBody = document.getElementById('archive-body');
                    if (data.length === 0) {
                        archiveBody.innerHTML = '<tr><td colspan="7">No logs in archive</td></tr>';
                    } else {
                        archiveBody.innerHTML = data.map(log => `
                            <tr>
                                <td>${log.id}</td>
                                <td>${log.name || 'Unknown'}</td>
                                <td>${log.uid}</td>
                                <td class="status-${log.status.toLowerCase()}">${log.status}</td>
                                <td>${log.timestamp}</td>
                                <td>${log.deleted_at}</td>
                                <td>
                                    <button class="btn-success" style="background:#48bb78; color:white; border:none; padding:5px 10px; border-radius:3px; cursor:pointer;" onclick="restoreLog(${log.id})">Restore</button>
                                </td>
                            </tr>
                        `).join('');
                    }
                })
                .catch(error => {
                    console.error('Error loading archive:', error);
                });
        }
        
        function restoreLog(id) {
            if (confirm('Restore this log back to main access logs?')) {
                fetch(`api.php?action=restore_log&id=${id}`, { method: 'POST' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification('Log restored from archive!', 'success');
                            loadArchive();
                            loadLogs();
                            loadStats();
                        } else {
                            showNotification('Error: ' + data.error, 'error');
                        }
                    });
            }
        }
        
        function restoreAllLogs() {
            if (confirm('Restore ALL logs from archive back to main access logs?')) {
                fetch('api.php?action=restore_all_logs', { method: 'POST' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification('All logs restored from archive!', 'success');
                            loadArchive();
                            loadLogs();
                            loadStats();
                        } else {
                            showNotification('Error: ' + data.error, 'error');
                        }
                    });
            }
        }
        
        function clearArchive() {
            if (confirm('⚠️ PERMANENTLY DELETE all archived logs? This cannot be undone!')) {
                fetch('api.php?action=clear_archive', { method: 'POST' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showNotification('Archive cleared permanently!', 'success');
                            loadArchive();
                            loadStats();
                        } else {
                            showNotification('Error: ' + data.error, 'error');
                        }
                    });
            }
        }
        
        // Auto-refresh every 3 seconds
        setInterval(() => {
            if (autoRefresh) {
                loadStats();
                if (document.getElementById('logs').classList.contains('active')) loadLogs();
                if (document.getElementById('inside').classList.contains('active')) loadInside();
                if (document.getElementById('archive').classList.contains('active')) loadArchive();
            }
        }, 3000);
        
        // Initial load
        loadStats();
        loadLogs();
        loadUsers();
    </script>
</body>
</html>