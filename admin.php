<?php
require_once 'db_connection.php';

// ==== Simple Admin Panel ====
// 🔐 Basic token check
$access_key = 'mySuperSecretKey2024';
if ($_GET['key'] !== $access_key) {
    http_response_code(403);
    exit('Access Denied');
}

function renderTable($query, $headers = []) {
    $pdo = getDbConnection();
    $stmt = $pdo->query($query);
    $rows = $stmt->fetchAll();
    
    if (empty($rows)) {
        echo "<p>No records found.</p>";
        return;
    }

    echo "<h2>Tracking Logs</h2><table border='1' cellpadding='6' cellspacing='0' style='font-family: monospace; font-size: 13px; background: #fff'>";
    if ($headers) {
        echo "<tr style='background:#eee'>";
        foreach ($headers as $h) echo "<th>$h</th>";
        echo "</tr>";
    }

    foreach ($rows as $row) {
        echo "<tr>";
        foreach ($row as $cell) {
            echo "<td>" . htmlspecialchars($cell) . "</td>";
        }
        echo "</tr>";
    }
    echo "</table><br><br>";
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Roy's Tracker Admin Panel</title>
  <style>
    body {
      background: #111;
      color: #eee;
      font-family: monospace;
      padding: 40px;
    }
    h1, h2 { color: #00f2ff; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #333; padding: 6px; }
    th { background: #222; color: #f0f0f0; }
    td { color: #0f0; }
    a { color: #0ff; }
  </style>
</head>
<body>
<h1>🧠 Roy's Cloak Tracker Admin</h1>
<p><b>Time:</b> <?= date('Y-m-d H:i:s') ?></p>

<?php
try {
    // Initialize database if needed
    initializeDatabase();
    
    // Display tracking logs
    renderTable(
        'SELECT fingerprint, ip, user_agent, screen, lang, timezone, referrer, country, city, isp, logged_at FROM tracking_logs ORDER BY logged_at DESC LIMIT 100',
        ['Fingerprint', 'IP', 'User Agent', 'Screen', 'Lang', 'TZ', 'Referrer', 'Country', 'City', 'ISP', 'Logged']
    );
    
    // Display redirect logs
    echo "<h2>Redirect Logs</h2>";
    renderTable(
        'SELECT fingerprint, ip, screen, lang, referrer, timezone, logged_at FROM redirect_logs ORDER BY logged_at DESC LIMIT 100',
        ['Fingerprint', 'IP', 'Screen', 'Lang', 'Referrer', 'TZ', 'Logged']
    );
    
    // Display some statistics
    echo "<h2>Statistics</h2>";
    $pdo = getDbConnection();
    $total_logs = $pdo->query('SELECT COUNT(*) FROM tracking_logs')->fetchColumn();
    $total_redirects = $pdo->query('SELECT COUNT(*) FROM redirect_logs')->fetchColumn();
    $unique_ips = $pdo->query('SELECT COUNT(DISTINCT ip) FROM tracking_logs')->fetchColumn();
    $top_countries = $pdo->query('SELECT country, COUNT(*) as count FROM tracking_logs GROUP BY country ORDER BY count DESC LIMIT 5')->fetchAll();
    
    echo "<p><b>Total Logs:</b> $total_logs</p>";
    echo "<p><b>Total Redirects:</b> $total_redirects</p>";
    echo "<p><b>Unique IPs:</b> $unique_ips</p>";
    echo "<p><b>Top Countries:</b></p>";
    echo "<ul>";
    foreach ($top_countries as $country) {
        echo "<li>" . htmlspecialchars($country['country']) . ": " . $country['count'] . "</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>

</body>
</html>
