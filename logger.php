<?php
require_once 'db_connection.php';

function fetchIntel($ip) {
    $res = @file_get_contents("https://ipapi.co/{$ip}/json/");
    if (!$res) return ['country' => 'Unknown', 'org' => 'N/A', 'city' => 'N/A'];
    $data = json_decode($res, true);
    return [
        'country' => $data['country_name'] ?? 'Unknown',
        'city'    => $data['city'] ?? 'N/A',
        'org'     => $data['org'] ?? 'N/A',
        'timezone'=> $data['timezone'] ?? 'N/A'
    ];
}

try {
    // Initialize database if needed
    initializeDatabase();
    
    // 🧠 Collect & enrich data
    $data = json_decode(file_get_contents('php://input'), true);
    $data['ip'] = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $data['agent'] = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
    $data['logged_at'] = date("Y-m-d H:i:s");
    $redirected = $data['redirected'] ?? false;
    $intel = fetchIntel($data['ip']);
    
    // Get database connection
    $pdo = getDbConnection();
    
    // 💾 Log everything to database
    $stmt = $pdo->prepare("
        INSERT INTO tracking_logs 
        (fingerprint, ip, user_agent, screen, lang, timezone, referrer, country, city, isp, geo_timezone, logged_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->execute([
        $data['fingerprint'] ?? null,
        $data['ip'],
        $data['agent'],
        $data['screen'] ?? null,
        $data['lang'] ?? null,
        $data['timezone'] ?? null,
        $data['referrer'] ?? null,
        $intel['country'],
        $intel['city'],
        $intel['org'],
        $intel['timezone'],
        $data['logged_at']
    ]);
    
    // 🌀 If redirected, log to separate table
    if ($redirected) {
        $stmt2 = $pdo->prepare("
            INSERT INTO redirect_logs 
            (fingerprint, ip, screen, lang, referrer, timezone, logged_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt2->execute([
            $data['fingerprint'] ?? null,
            $data['ip'],
            $data['screen'] ?? null,
            $data['lang'] ?? null,
            $data['referrer'] ?? null,
            $data['timezone'] ?? null,
            $data['logged_at']
        ]);
    }
    
    echo json_encode(['status' => 'logged']);
    
} catch (Exception $e) {
    error_log("Logging failed: " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'Logging failed']);
}
