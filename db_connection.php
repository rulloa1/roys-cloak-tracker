<?php
// Database connection helper for Roy's Cloak Tracker
// Uses PostgreSQL with environment variables for configuration

function getDbConnection() {
    // Get database URL from environment (Render provides this)
    $database_url = getenv('DATABASE_URL');
    
    if (!$database_url) {
        // Fallback for local development
        $database_url = 'postgresql://localhost:5432/cloak_tracker';
    }
    
    try {
        // Parse the database URL
        $url = parse_url($database_url);
        
        $host = $url['host'] ?? 'localhost';
        $port = $url['port'] ?? 5432;
        $dbname = ltrim($url['path'], '/');
        $username = $url['user'] ?? '';
        $password = $url['pass'] ?? '';
        
        // Create PDO connection
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";
        $pdo = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        
        return $pdo;
        
    } catch (PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        throw new Exception("Database connection failed");
    }
}

// Initialize database tables if they don't exist
function initializeDatabase() {
    $pdo = getDbConnection();
    $sql = file_get_contents(__DIR__ . '/database.sql');
    
    try {
        $pdo->exec($sql);
        return true;
    } catch (PDOException $e) {
        error_log("Database initialization failed: " . $e->getMessage());
        return false;
    }
}
?>
