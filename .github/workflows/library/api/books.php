<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../database.php';
require_once '../config.php';

$db = Database::getInstance()->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$search = isset($_GET['search']) ? $_GET['search'] : '';

switch ($method) {
    case 'GET':
        // Get books with optional search
        try {
            $sql = "SELECT * FROM books WHERE title LIKE :search OR author LIKE :search OR isbn LIKE :search ORDER BY title";
            $stmt = $db->prepare($sql);
            $stmt->execute(['search' => '%' . $search . '%']);
            $books = $stmt->fetchAll();

            echo json_encode($books);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch books']);
        }
        break;

    case 'POST':
        // Add new book
        $data = json_decode(file_get_contents('php://input'), true);

        if (!$data || !isset($data['title']) || !isset($data['author']) || !isset($data['isbn'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            break;
        }

        try {
            $sql = "INSERT INTO books (isbn, title, author, description, publisher, publication_year, genre, total_copies, available_copies)
                    VALUES (:isbn, :title, :author, :description, :publisher, :year, :genre, :total, :available)";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'isbn' => $data['isbn'],
                'title' => $data['title'],
                'author' => $data['author'],
                'description' => $data['description'] ?? '',
                'publisher' => $data['publisher'] ?? '',
                'year' => $data['year'] ?? null,
                'genre' => $data['genre'] ?? '',
                'total' => $data['total_copies'] ?? 1,
                'available' => $data['available_copies'] ?? 1
            ]);

            echo json_encode(['success' => true, 'id' => $db->lastInsertId()]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to add book']);
        }
        break;

    case 'PUT':
        // Update book
        $data = json_decode(file_get_contents('php://input'), true);
        $id = $_GET['id'] ?? null;

        if (!$id || !$data) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing book ID or data']);
            break;
        }

        try {
            $sql = "UPDATE books SET title = :title, author = :author, isbn = :isbn,
                    description = :description, publisher = :publisher, publication_year = :year,
                    genre = :genre, total_copies = :total, available_copies = :available
                    WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute([
                'id' => $id,
                'title' => $data['title'] ?? '',
                'author' => $data['author'] ?? '',
                'isbn' => $data['isbn'] ?? '',
                'description' => $data['description'] ?? '',
                'publisher' => $data['publisher'] ?? '',
                'year' => $data['year'] ?? null,
                'genre' => $data['genre'] ?? '',
                'total' => $data['total_copies'] ?? 1,
                'available' => $data['available_copies'] ?? 1
            ]);

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to update book']);
        }
        break;

    case 'DELETE':
        // Delete book
        $id = $_GET['id'] ?? null;

        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing book ID']);
            break;
        }

        try {
            $sql = "DELETE FROM books WHERE id = :id";
            $stmt = $db->prepare($sql);
            $stmt->execute(['id' => $id]);

            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to delete book']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
        break;
}
?>