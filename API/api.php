<?php
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
$data = json_decode(file_get_contents('php://input'), true);
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// extrayendo solo la parte después de api.php porque si no no jala
$scriptName = $_SERVER['SCRIPT_NAME'];
$pathInfo = str_replace($scriptName, '', $uri);
$uri = $pathInfo ?: '/';

$conn = new mysqli('localhost', 'root', '', 'chatbot-oficial');
if ($conn->connect_error) {
    echo json_encode([ 'message' => 'Error al conectar a la base de datos' ]);
    exit;
}

switch ($uri) {
    case '/sessiones':
        switch ($method) {
            // obtener la sesion de un alumno por numero de telefono
            case 'GET':
                $numeroTelefono = $_GET['telefono'] ?? null;
                if (!$numeroTelefono) {
                    http_response_code(400);
                    echo json_encode(['message' => 'El parametro "telefono" te falto']);
                    break;
                }
                $stmt = $conn->prepare("SELECT * FROM sesiones WHERE numero_telefono = ?");
                $stmt->bind_param("s", $numeroTelefono);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                echo json_encode($result);
                break;
            // crear o actualizar la sesion
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
        break;
    case '/alumnos':
        switch ($method) {
            case 'GET':
                // obtener alumno por numero de telefono
                $numeroTelefono = $_GET['telefono'] ?? null;
                if (!$numeroTelefono) {
                    http_response_code(400);
                    echo json_encode(['message' => 'El parametro "telefono" te falto']);
                    break;
                }
                $stmt = $conn->prepare("SELECT * FROM alumnos WHERE numero_telefono = ?");
                $stmt->bind_param("s", $numeroTelefono);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                echo json_encode($result);
                break;
            case 'POST':
                // verificar si el alumno existe
                $stmt = $conn->prepare("SELECT * FROM alumnos WHERE numero_telefono = ?");
                $stmt->bind_param("s", $data['numero_telefono']);
                $stmt->execute();
                $result = $stmt->get_result()->fetch_assoc();
                if ($result) {
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
                        echo json_encode(['message' => 'Datos insuficientes para actualizar']);
                        exit;
                    }

                    $sql = "UPDATE alumnos SET " . implode(', ', $updates) . " WHERE numero_telefono = ?";
                    $params[] = $data['numero_telefono'];
                    $types .= 's';

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param($types, ...$params);
                    $stmt->execute();

                    if ($stmt->affected_rows > 0) {
                        echo json_encode(['message' => 'Alumno actualizado correctamente']);
                    } else {
                        echo json_encode(['message' => 'No se actualizó ningún registro (verifique el número de teléfono)']);
                    }
                } else {
                    // crear Alumno solo con el nombre porque es el primer dato que se envia en el flujo de whatsapp
                    $stmt = $conn->prepare("INSERT INTO alumnos (nombres, numero_telefono) VALUES (?, ?)");
                    $stmt->bind_param("ss", $data['nombres'], $data['numero_telefono']);
                    $stmt->execute();
                    echo json_encode(['message' => 'Alumno creado correctamente']);
                }
                break;
            default:
                http_response_code(405);
                echo json_encode([ 'message' => 'Método no permitido' ]);
                break;
        }
        break;
    case '/documentos':
        switch ($method) {
            case 'GET':
                echo json_encode(['message' => 'HOLA SI SIRVO :)']);
                break;
            default:
                http_response_code(405);
                echo json_encode([ 'message' => 'Método no permitido' ]);
                break;
        }
        break;
    case '/sanity':
        echo json_encode(['message' => 'HOLA SI SIRVO :)']);
        break;
    default:
        http_response_code(404);
        echo json_encode(['message' => 'Ruta no encontrada']);
        break;
}
?>