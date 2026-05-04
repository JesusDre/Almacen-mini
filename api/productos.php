<?php
require_once __DIR__ . '/../conexion.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

function jsonResponse(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    echo json_encode($payload, JSON_UNESCAPED_UNICODE);
    exit;
}

function readJsonBody(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return [];
    }

    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($method === 'GET') {
    if ($id !== null && $id > 0) {
        $stmt = $conn->prepare('SELECT id, nombre, cantidad, precio FROM productos WHERE id = ?');
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $producto = $result->fetch_assoc();
        $stmt->close();

        if (!$producto) {
            jsonResponse(404, ['ok' => false, 'error' => 'Producto no encontrado']);
        }

        jsonResponse(200, ['ok' => true, 'data' => $producto]);
    }

    $result = $conn->query('SELECT id, nombre, cantidad, precio FROM productos ORDER BY id DESC');
    $productos = [];

    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }

    jsonResponse(200, ['ok' => true, 'data' => $productos]);
}

if ($method === 'POST') {
    $input = readJsonBody();

    $nombre = trim($input['nombre'] ?? '');
    $cantidad = filter_var($input['cantidad'] ?? null, FILTER_VALIDATE_INT);
    $precio = filter_var($input['precio'] ?? null, FILTER_VALIDATE_FLOAT);

    if ($nombre === '' || $cantidad === false || $precio === false) {
        jsonResponse(400, ['ok' => false, 'error' => 'Datos invalidos. Requerido: nombre, cantidad, precio']);
    }

    $stmt = $conn->prepare('INSERT INTO productos (nombre, cantidad, precio) VALUES (?, ?, ?)');
    $stmt->bind_param('sid', $nombre, $cantidad, $precio);

    if (!$stmt->execute()) {
        $stmt->close();
        jsonResponse(500, ['ok' => false, 'error' => 'No se pudo crear el producto']);
    }

    $newId = $stmt->insert_id;
    $stmt->close();

    jsonResponse(201, [
        'ok' => true,
        'message' => 'Producto creado',
        'data' => [
            'id' => $newId,
            'nombre' => $nombre,
            'cantidad' => $cantidad,
            'precio' => (float) $precio,
        ],
    ]);
}

if ($method === 'PUT') {
    if ($id === null || $id <= 0) {
        jsonResponse(400, ['ok' => false, 'error' => 'Debes enviar id en query string']);
    }

    $input = readJsonBody();
    $nombre = trim($input['nombre'] ?? '');
    $cantidad = filter_var($input['cantidad'] ?? null, FILTER_VALIDATE_INT);
    $precio = filter_var($input['precio'] ?? null, FILTER_VALIDATE_FLOAT);

    if ($nombre === '' || $cantidad === false || $precio === false) {
        jsonResponse(400, ['ok' => false, 'error' => 'Datos invalidos. Requerido: nombre, cantidad, precio']);
    }

    $stmt = $conn->prepare('UPDATE productos SET nombre = ?, cantidad = ?, precio = ? WHERE id = ?');
    $stmt->bind_param('sidi', $nombre, $cantidad, $precio, $id);

    if (!$stmt->execute()) {
        $stmt->close();
        jsonResponse(500, ['ok' => false, 'error' => 'No se pudo actualizar el producto']);
    }

    if ($stmt->affected_rows === 0) {
        $stmt->close();
        jsonResponse(404, ['ok' => false, 'error' => 'Producto no encontrado o sin cambios']);
    }

    $stmt->close();
    jsonResponse(200, [
        'ok' => true,
        'message' => 'Producto actualizado',
        'data' => [
            'id' => $id,
            'nombre' => $nombre,
            'cantidad' => $cantidad,
            'precio' => (float) $precio,
        ],
    ]);
}

if ($method === 'DELETE') {
    if ($id === null || $id <= 0) {
        jsonResponse(400, ['ok' => false, 'error' => 'Debes enviar id en query string']);
    }

    $stmt = $conn->prepare('DELETE FROM productos WHERE id = ?');
    $stmt->bind_param('i', $id);

    if (!$stmt->execute()) {
        $stmt->close();
        jsonResponse(500, ['ok' => false, 'error' => 'No se pudo eliminar el producto']);
    }

    if ($stmt->affected_rows === 0) {
        $stmt->close();
        jsonResponse(404, ['ok' => false, 'error' => 'Producto no encontrado']);
    }

    $stmt->close();
    jsonResponse(200, ['ok' => true, 'message' => 'Producto eliminado']);
}

jsonResponse(405, ['ok' => false, 'error' => 'Metodo no permitido']);
