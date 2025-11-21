<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../controllers/UserController.php';

$controller = new UserController();
$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

preg_match('/\/api\/users\/(\d+)/', $path, $matches);
$id = $matches[1] ?? null;

try {
    switch($method) {
        case 'GET':
            if ($id) {
                $user = $controller->getUserById($id);
                echo json_encode($user ?: ['error' => 'Utilisateur non trouvé']);
            } else {
                echo json_encode($controller->getAllUsers());
            }
            break;

        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            $newId = $controller->createUser($data);
            echo json_encode(['success' => true, 'id' => $newId]);
            break;

        case 'PUT':
            if (!$id) throw new Exception('ID manquant');
            $data = json_decode(file_get_contents('php://input'), true);
            $controller->updateUser($id, $data);
            echo json_encode(['success' => true]);
            break;

        case 'DELETE':
            if (!$id) throw new Exception('ID manquant');
            $controller->deleteUser($id);
            echo json_encode(['success' => true]);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Méthode non autorisée']);
    }
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>