<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

// Database configuration
$host = 'localhost';
$dbname = 'codequest';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo json_encode(['error' => 'Database connection failed']);
    exit;
}

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Parse request URI
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));

// Handle different endpoints
switch ($request[0]) {
    case 'save-progress':
        if ($method == 'POST') {
            saveProgress();
        }
        break;
    
    case 'get-progress':
        if ($method == 'GET') {
            getProgress();
        }
        break;
    
    case 'leaderboard':
        if ($method == 'GET') {
            getLeaderboard();
        }
        break;
    
    case 'validate-code':
        if ($method == 'POST') {
            validateCode();
        }
        break;
    
    default:
        echo json_encode(['error' => 'Invalid endpoint']);
        break;
}


// Save user progress
function saveProgress() {
    global $pdo;
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['username']) || !isset($data['totalPoints'])) {
        echo json_encode(['error' => 'Missing required fields']);
        return;
    }
    
    try {
        $stmt = $pdo->prepare("
            INSERT INTO users (username, total_points, level, streak, language_progress) 
            VALUES (:username, :totalPoints, :level, :streak, :languageProgress)
            ON DUPLICATE KEY UPDATE 
            total_points = :totalPoints, 
            level = :level, 
            streak = :streak, 
            language_progress = :languageProgress
        ");
        
        $stmt->execute([
            ':username' => $data['username'],
            ':totalPoints' => $data['total Points'],
            ':level' => data[level],  :streak  => $data['streak'],
            ':languageProgress' => json_encode($data['languageProgress'])
            ]);
        }
}
// Get user progress
function getProgress() {
    global $pdo;
    try {
        $stmt = $pdo->query("
            SELECT username, total_points 
            FROM users 
            ORDER BY total_points DESC 
            LIMIT 10
        ");
        
        $leaderboard = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($leaderboard);
    } catch(PDOException $e) {
        echo json_encode(['error' => 'Failed to get leaderboard']);
    }
}
// Validate code (simplified)
function validateCode() {
$data = json_decode(file_get_contents('php://input'), true);
$language = $data['language'] ?? '';
$code = $data['code'] ?? '';
$challengeId = $data['challengeId'] ?? '';

// In a real implementation, you would use proper code execution/sandboxing
// This is a simplified validation

$isValid = false;

switch ($language) {
    case 'html':
        $isValid = strpos($code, '<') !== false && strpos($code, '>') !== false;
        break;
    case 'css':
        $isValid = strpos($code, '{') !== false && strpos($code, '}') !== false;
        break;
    case 'javascript':
        $isValid = strpos($code, 'function') !== false || strpos($code, '=>') !== false;
        break;
    case 'php':
        $isValid = strpos($code, '<?php') !== false || strpos($code, 'echo') !== false;
        break;
    case 'python':
        $isValid = strpos($code, 'print') !== false || strpos($code, 'def') !== false;
        break;
    case 'mern':
        $isValid = strpos($code, 'import') !== false || strpos($code, 'require') !== false;
        break;
}

echo json_encode(['valid' => $isValid]);
}

// Health check endpoint
case 'health':
    echo json_encode(['status' => 'ok', 'timestamp' => date('Y-m-d H:i:s')]);
    break;

// Create user endpoint
case 'create-user':
    if ($method == 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $username = $data['username'] ?? '';
        
        if (empty($username)) {
            echo json_encode(['error' => 'Username required']);
            return;
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO users (username, total_points, level, streak, language_progress) 
                                  VALUES (:username, 0, 1, 0, '{}') 
                                  ON DUPLICATE KEY UPDATE username = username");
            $stmt->execute([':username' => $username]);
            
            echo json_encode(['success' => true, 'username' => $username]);
        } catch(PDOException $e) {
            echo json_encode(['error' => 'Failed to create user']);
        }
    }
    break;
?>