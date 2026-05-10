<?php
// Force strict error reporting for debugging, but output will be caught
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Start output buffering to catch any accidental output
ob_start();

// Set JSON content type header first
header('Content-Type: application/json');

try {
    require_once 'db_connect.php';
    
    $action = $_GET['action'] ?? '';
    
    switch($action) {
        case 'stats':
            getStats($conn);
            break;
        case 'logs':
            getLogs($conn, $_GET['limit'] ?? 100);
            break;
        case 'archive_logs':
            getArchiveLogs($conn, $_GET['limit'] ?? 100);
            break;
        case 'inside':
            getInside($conn);
            break;
        case 'users':
            getUsers($conn);
            break;
        case 'delete_all_logs':
            deleteAllLogs($conn);
            break;
        case 'delete_log':
            deleteLog($conn, $_GET['id'] ?? 0);
            break;
        case 'restore_log':
            restoreLog($conn, $_GET['id'] ?? 0);
            break;
        case 'restore_all_logs':
            restoreAllLogs($conn);
            break;
        case 'clear_archive':
            clearArchive($conn);
            break;
        case 'reset_inside_status':
            resetInsideStatus($conn);
            break;
        case 'manual_exit':
            manualExit($conn);
            break;
        case 'export_logs':
            exportLogs($conn);
            break;
        case 'reset_user_state':
            resetUserState($conn);
            break;
        case 'reset_all_states':
            resetAllStates($conn);
            break;
        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    ob_clean();
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

ob_end_flush();

// ---------- Helper functions ----------

function getStats($conn) {
    $stats = [];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM users");
    if (!$result) throw new Exception("DB error: " . $conn->error);
    $stats['total_users'] = $result->fetch_assoc()['count'];
    
    $result = $conn->query("SELECT COUNT(*) as count FROM current_status WHERE is_inside = TRUE");
    $stats['inside_count'] = $result ? $result->fetch_assoc()['count'] : 0;
    
    $result = $conn->query("SELECT COUNT(*) as count FROM access_logs WHERE DATE(timestamp) = CURDATE()");
    $stats['total_access_today'] = $result ? $result->fetch_assoc()['count'] : 0;
    
    $result = $conn->query("SELECT COUNT(*) as count FROM access_logs WHERE status = 'DENIED' AND DATE(timestamp) = CURDATE()");
    $stats['denied_today'] = $result ? $result->fetch_assoc()['count'] : 0;
    
    // Add archive count
    $result = $conn->query("SELECT COUNT(*) as count FROM access_logs_archive");
    $stats['archive_count'] = $result ? $result->fetch_assoc()['count'] : 0;
    
    echo json_encode($stats);
}

function getLogs($conn, $limit) {
    $stmt = $conn->prepare("SELECT id, uid, name, status, timestamp FROM access_logs ORDER BY timestamp DESC LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $logs = [];
    while($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
    echo json_encode($logs);
}

function getArchiveLogs($conn, $limit) {
    $stmt = $conn->prepare("SELECT id, uid, name, status, timestamp, deleted_at, deleted_by FROM access_logs_archive ORDER BY deleted_at DESC LIMIT ?");
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $logs = [];
    while($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
    echo json_encode($logs);
}

function getInside($conn) {
    $sql = "SELECT cs.uid, cs.name, cs.last_access 
            FROM current_status cs 
            WHERE cs.is_inside = TRUE 
            ORDER BY cs.last_access DESC";
    $result = $conn->query($sql);
    if (!$result) throw new Exception("DB error: " . $conn->error);
    
    $inside = [];
    while($row = $result->fetch_assoc()) {
        $inside[] = $row;
    }
    echo json_encode($inside);
}

function getUsers($conn) {
    $result = $conn->query("SELECT id, name, uid, role, created_at FROM users ORDER BY id");
    if (!$result) throw new Exception("DB error: " . $conn->error);
    
    $users = [];
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    echo json_encode($users);
}

function deleteAllLogs($conn) {
    $conn->query("START TRANSACTION");
    
    try {
        // Move all logs to archive before deleting
        $conn->query("INSERT INTO access_logs_archive (uid, name, status, timestamp, deleted_at, deleted_by) 
                      SELECT uid, name, status, timestamp, NOW(), 'web_interface' FROM access_logs");
        
        // Delete all access logs
        $conn->query("DELETE FROM access_logs");
        
        // Reset current status to FALSE for everyone
        $conn->query("UPDATE current_status SET is_inside = FALSE");
        
        $conn->query("COMMIT");
        echo json_encode(['success' => true, 'message' => 'All logs moved to archive. All users reset to OUTSIDE.']);
    } catch (Exception $e) {
        $conn->query("ROLLBACK");
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function deleteLog($conn, $id) {
    // First, get the log details before deleting
    $stmt = $conn->prepare("SELECT uid, status, name, timestamp FROM access_logs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $log = $result->fetch_assoc();
    
    if ($log) {
        $conn->query("START TRANSACTION");
        
        try {
            // Move to archive
            $stmt = $conn->prepare("INSERT INTO access_logs_archive (uid, name, status, timestamp, deleted_at, deleted_by) 
                                    VALUES (?, ?, ?, ?, NOW(), 'web_interface')");
            $stmt->bind_param("ssss", $log['uid'], $log['name'], $log['status'], $log['timestamp']);
            $stmt->execute();
            
            // Delete from main table
            $stmt = $conn->prepare("DELETE FROM access_logs WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            // Recalculate user state based on most recent remaining log
            $stmt = $conn->prepare("SELECT status FROM access_logs WHERE uid = ? ORDER BY timestamp DESC LIMIT 1");
            $stmt->bind_param("s", $log['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            $last_log = $result->fetch_assoc();
            
            if ($last_log) {
                $new_status = ($last_log['status'] == 'ENTER') ? 1 : 0;
                $stmt = $conn->prepare("UPDATE current_status SET is_inside = ? WHERE uid = ?");
                $stmt->bind_param("is", $new_status, $log['uid']);
                $stmt->execute();
            } else {
                $stmt = $conn->prepare("UPDATE current_status SET is_inside = FALSE WHERE uid = ?");
                $stmt->bind_param("s", $log['uid']);
                $stmt->execute();
            }
            
            $conn->query("COMMIT");
            echo json_encode(['success' => true, 'message' => 'Log moved to archive. User state updated.']);
        } catch (Exception $e) {
            $conn->query("ROLLBACK");
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Log not found']);
    }
}

function restoreLog($conn, $id) {
    // Restore a log from archive back to main table
    $stmt = $conn->prepare("SELECT uid, name, status, timestamp FROM access_logs_archive WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $log = $result->fetch_assoc();
    
    if ($log) {
        $conn->query("START TRANSACTION");
        
        try {
            // Insert back to main logs
            $stmt = $conn->prepare("INSERT INTO access_logs (uid, name, status, timestamp) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $log['uid'], $log['name'], $log['status'], $log['timestamp']);
            $stmt->execute();
            
            // Delete from archive
            $stmt = $conn->prepare("DELETE FROM access_logs_archive WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            // Recalculate user state
            $stmt = $conn->prepare("SELECT status FROM access_logs WHERE uid = ? ORDER BY timestamp DESC LIMIT 1");
            $stmt->bind_param("s", $log['uid']);
            $stmt->execute();
            $result = $stmt->get_result();
            $last_log = $result->fetch_assoc();
            
            if ($last_log) {
                $new_status = ($last_log['status'] == 'ENTER') ? 1 : 0;
                $stmt = $conn->prepare("UPDATE current_status SET is_inside = ? WHERE uid = ?");
                $stmt->bind_param("is", $new_status, $log['uid']);
                $stmt->execute();
            }
            
            $conn->query("COMMIT");
            echo json_encode(['success' => true, 'message' => 'Log restored from archive.']);
        } catch (Exception $e) {
            $conn->query("ROLLBACK");
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Log not found in archive']);
    }
}

function restoreAllLogs($conn) {
    // Restore ALL logs from archive back to main table
    $conn->query("START TRANSACTION");
    
    try {
        $conn->query("INSERT INTO access_logs (uid, name, status, timestamp) 
                      SELECT uid, name, status, timestamp FROM access_logs_archive");
        $conn->query("DELETE FROM access_logs_archive");
        
        // Recalculate all user states (simplified - set all to outside)
        $conn->query("UPDATE current_status SET is_inside = FALSE");
        
        $conn->query("COMMIT");
        echo json_encode(['success' => true, 'message' => 'All logs restored from archive.']);
    } catch (Exception $e) {
        $conn->query("ROLLBACK");
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
}

function clearArchive($conn) {
    // Permanently delete all archived logs
    $conn->query("DELETE FROM access_logs_archive");
    echo json_encode(['success' => true, 'message' => 'Archive cleared permanently.']);
}

function resetInsideStatus($conn) {
    $conn->query("UPDATE current_status SET is_inside = FALSE");
    echo json_encode(['success' => true, 'message' => 'All users reset to OUTSIDE.']);
}

function manualExit($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $uid = $data['uid'] ?? '';
    $name = $data['name'] ?? '';
    
    if (!$uid) throw new Exception("Missing UID");
    
    $stmt = $conn->prepare("UPDATE current_status SET is_inside = FALSE WHERE uid = ?");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    
    $stmt = $conn->prepare("INSERT INTO access_logs (uid, name, status) VALUES (?, ?, 'EXIT')");
    $stmt->bind_param("ss", $uid, $name);
    $stmt->execute();
    
    echo json_encode(['success' => true, 'message' => "$name marked as EXITED."]);
}

function exportLogs($conn) {
    $result = $conn->query("SELECT id, uid, name, status, timestamp FROM access_logs ORDER BY timestamp DESC");
    if (!$result) throw new Exception("DB error: " . $conn->error);
    
    $logs = [];
    while($row = $result->fetch_assoc()) {
        $logs[] = $row;
    }
    echo json_encode($logs);
}

function resetUserState($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    $uid = $data['uid'] ?? '';
    if (!$uid) throw new Exception("Missing UID");
    
    $stmt = $conn->prepare("UPDATE current_status SET is_inside = FALSE WHERE uid = ?");
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    echo json_encode(['success' => true]);
}

function resetAllStates($conn) {
    $conn->query("UPDATE current_status SET is_inside = FALSE");
    echo json_encode(['success' => true, 'message' => 'All users reset to OUTSIDE.']);
}
?>