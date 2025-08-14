<?php
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$conn = new mysqli('localhost', 'root', '', 'whatsapp');
if ($conn->connect_error) {
    echo json_encode([ 'message' => 'Error al conectar a la base de datos' ]);
    exit;
}

switch ($uri) {
    case '/sessiones':
        switch ($method) {
            case 'GET':
                $stmt = $conn->prepare("SELECT * FROM sesiones WHERE numero_telefono = ?");
                $stmt->bind_param("s", $data['numero_telefono']);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                echo json_encode($result);
                break;
            case 'POST':
                $stmt = $conn->prepare("INSERT INTO sesiones (numero_telefono, estado) VALUES (?, ?) ON DUPLICATE KEY UPDATE estado = VALUES(estado)");
                $stmt->bind_param("ss", $data['numero_telefono'], $data['estado']);
                $stmt->execute();
                echo json_encode([ 'message' => 'Sesion creada o actualizada correctamente' ]);
                break;
            default:
                http_response_code(405);
                echo json_encode([ 'message' => 'Método no permitido' ]);
                break;
        }
    case '/alumnos':
        switch ($method) {
            case 'GET':
                break;
            case 'POST':
                $fields = ['nombre', 'apellido', 'correo_electronico', 'fecha_nacimiento', 'numero_seguro_social'];
                $updates = [];
                $params = [];
                $types = '';

                foreach ($fields as $field) {
                    if (isset($data[$field])) {
                        $updates[] = "$field = ?";
                        $params[] = $data[$field];
                        $types .= 's';
                    }
                }

                if (empty($updates) || !isset($data['numero_telefono'])) {
                    http_response_code(400);
                    echo json_encode([ 'message' => 'Datos insuficientes para actualizar' ]);
                    exit;
                }

                $sql = "UPDATE alumnos SET " . implode(', ', $updates) . " WHERE numero_telefono = ?";
                $params[] = $data['numero_telefono'];
                $types .= 's';

                $stmt = $conn->prepare($sql);
                $stmt->bind_param($types, ...$params);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    echo json_encode([ 'message' => 'Alumno actualizado correctamente' ]);
                } else {
                    echo json_encode([ 'message' => 'No se actualizó ningún registro (verifique el número de teléfono)' ]);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode([ 'message' => 'Método no permitido' ]);
                break;
        }
    case '/documentos':
        switch ($method) {
            case 'GET':
                break;
            default:
                http_response_code(405);
                echo json_encode([ 'message' => 'Método no permitido' ]);
                break;
        }
}

?>