<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../database.php';
require_once '../config.php';

$db = Database::getInstance()->getConnection();

$method = $_SERVER['REQUEST_METHOD'];

session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Not authenticated']);
    exit;
}

$user_id = $_SESSION['user_id'];

switch ($method) {
    case 'GET':
        // Get user's lending records
        try {
            $sql = "SELECT l.*, b.title, b.author, b.isbn
                    FROM lending l
                    JOIN books b ON l.book_id = b.id
                    WHERE l.user_id = :user_id
                    ORDER BY l.borrow_date DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute(['user_id' => $user_id]);
            $lendings = $stmt->fetchAll();

            echo json_encode($lendings);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch lending records']);
        }
        break;

    case 'POST':
        // Borrow a book
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || !isset($data['book_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing book ID']);
            break;
        }

        try {
            // Check if book is available
            $sql = "SELECT available_copies FROM books WHERE id = :book_id";
            $stmt = $db->prepare($sql);
            $stmt->execute(['book_id' => $data['book_id']]);
            $book = $stmt->fetch();

            if (!$book || $book['available_copies'] <= 0) {
                http_response_code(400);
                echo json_encode(['error' => 'Book not available']);
                break;
            }

            // Check if user already has this book
            $sql = "SELECT id FROM lending WHERE user_id = :user_id AND book_id = :book_id AND is_returned = FALSE";
            $stmt = $db->prepare($sql);
            $stmt->execute(['user_id' => $user_id, 'book_id' => $data['book_id']]);
            if ($stmt->fetch()) {
                http_response_code(400);
                echo json_encode(['error' => 'You already have this book borrowed']);
                break;
            }

            // Calculate due date (14 days from now)
            $due_date = date('Y-m-d H:i:s', strtotime('+14 days'));

            // Insert lending record
            $sql = "INSERT INTO lending (user_id, book_id, due_date) VALUES (:user_id, :book_id, :due_date)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'user_id' => $user_id,
                'book_id' => $data['book_id'],
                'due_date' => $due_date
            ]);

            // Update available copies
            $sql = "UPDATE books SET available_copies = available_copies - 1 WHERE id = :book_id";
            $stmt = $db->prepare($sql);
            $stmt->execute(['book_id' => $data['book_id']]);

            echo json_encode(['success' => true, 'message' => 'Book borrowed successfully']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to borrow book']);
        }
        break;

    case 'PUT':
        // Return a book
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || !isset($data['lending_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing lending ID']);
            break;
        }

        try {
            // Get lending record
            $sql = "SELECT book_id FROM lending WHERE id = :lending_id AND user_id = :user_id AND is_returned = FALSE";
            $stmt = $db->prepare($sql);
            $stmt->execute(['lending_id' => $data['lending_id'], 'user_id' => $user_id]);
            $lending = $stmt->fetch();

            if (!$lending) {
                http_response_code(404);
                echo json_encode(['error' => 'Lending record not found']);
                break;
            }

            // Update lending record
            $sql = "UPDATE lending SET is_returned = TRUE, return_date = NOW() WHERE id = :lending_id";
            $stmt = $db->prepare($sql);
            $stmt->execute(['lending_id' => $data['lending_id']]);

            // Update available copies
            $sql = "UPDATE books SET available_copies = available_copies + 1 WHERE id = :book_id";
            $stmt = $db->prepare($sql);
            $stmt->execute(['book_id' => $lending['book_id']]);

            echo json_encode(['success' => true, 'message' => 'Book returned successfully']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to return book']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>