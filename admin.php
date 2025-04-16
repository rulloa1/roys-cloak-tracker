<?php
// ==== Simple Admin Panel ====
// 🔐 Basic token check
$access_key = 'mySuperSecretKey2024';
if ($_GET['key'] !== $access_key) {
    http_response_code(403);
    exit('Access Denied');
}

function renderTable($file, $headers = []) {
    if (!file_exists($file)) {
        echo "<p><b>$file</b> not found.</p>";
        return;
    }

    $rows = array_filter(file($file));
    echo "<h2>$file</h2><table border='1' cellpadding='6' cellspacing='0' style='font-family: monospace; font-size: 13px; background: #fff'>";
    if ($headers) {
        echo "<tr style='background:#eee'>";
        foreach ($headers as $h) echo "<th>$h</th>";
        echo "</tr>";
    }

    foreach ($rows as $line) {
        echo "<tr>";
        foreach (str_getcsv($line) as $cell) {
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
renderTable('log.csv', ['Fingerprint', 'IP', 'User Agent', 'Screen', 'Lang', 'TZ', 'Referrer', 'Country', 'City', 'ISP', 'Logged']);
renderTable('redirect_log.csv', ['Fingerprint', 'IP', 'Screen', 'Lang', 'Referrer', 'TZ', 'Logged']);
?>

</body>
</html>
