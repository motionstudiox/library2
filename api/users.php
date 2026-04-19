<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../database.php';
require_once '../config.php';

$db = Database::getInstance()->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        // Get current user info (requires authentication)
        session_start();
        if (!isset($_SESSION['user_id'])) {
            http_response_code(401);
            echo json_encode(['error' => 'Not authenticated']);
            break;
        }

        try {
            $sql = "SELECT id, username, email, full_name, phone, created_at FROM users WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $_SESSION['user_id']]);
            $user = $stmt->fetch();

            if ($user) {
                echo json_encode($user);
            } else {
                http_response_code(404);
                echo json_encode(['error' => 'User not found']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch user']);
        }
        break;

    case 'POST':
        // Login or Register
        $data = json_decode(file_get_contents('php://input'), true);
        $action = $_GET['action'] ?? 'login';

        if ($action === 'register') {
            // Register new user
            if (!$data || !isset($data['username']) || !isset($data['email']) || !isset($data['password']) || !isset($data['full_name'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                break;
            }

            try {
                $password_hash = password_hash($data['password'], PASSWORD_DEFAULT);

                $sql = "INSERT INTO users (username, email, password_hash, full_name, phone)
                        VALUES (:username, :email, :password, :full_name, :phone)";
                $stmt = $db->prepare($sql);
                $stmt->execute([
                    'username' => $data['username'],
                    'email' => $data['email'],
                    'password' => $password_hash,
                    'full_name' => $data['full_name'],
                    'phone' => $data['phone'] ?? ''
                ]);

                // Auto-login after registration
                session_start();
                $_SESSION['user_id'] = $db->lastInsertId();
                $_SESSION['username'] = $data['username'];

                echo json_encode(['success' => true, 'message' => 'User registered successfully']);
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to register user']);
            }
        } elseif ($action === 'login') {
            // Login
            if (!$data || !isset($data['username']) || !isset($data['password'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing username or password']);
                break;
            }

            try {
                $sql = "SELECT id, username, password_hash FROM users WHERE username = :username OR email = :username";
                $stmt = $db->prepare($sql);
                $stmt->execute(['username' => $data['username']]);
                $user = $stmt->fetch();

                if ($user && password_verify($data['password'], $user['password_hash'])) {
                    session_start();
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];

                    echo json_encode(['success' => true, 'message' => 'Login successful']);
                } else {
                    http_response_code(401);
                    echo json_encode(['error' => 'Invalid credentials']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode(['error' => 'Login failed']);
            }
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid action']);
        }
        break;

    case 'DELETE':
        // Logout
        session_start();
        session_destroy();
        echo json_encode(['success' => true, 'message' => 'Logged out']);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>