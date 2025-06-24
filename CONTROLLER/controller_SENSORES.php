<?php
require_once '../MODEL/model_SENSORES.php';

$sensorModel = new SensorModel();

$acao = isset($_GET['acao']) ? $_GET['acao'] : 'listar';

switch ($acao) {

    // 🔍 Listar sensores
    case 'listar':
        $sensores = $sensorModel->listarSensores();
        header('Content-Type: application/json');
        echo json_encode($sensores);
        break;

    // ➕ Adicionar sensor
    case 'adicionar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $tipo = $_POST['tipo'];
            $localizacao = $_POST['localizacao'];
            $data_instalacao = $_POST['data_instalacao'];
            $status = $_POST['status'];

            $resultado = $sensorModel->adicionarSensor($tipo, $localizacao, $data_instalacao, $status);

            if ($resultado) {
                echo json_encode(['mensagem' => 'Sensor adicionado com sucesso!']);
            } else {
                echo json_encode(['erro' => 'Falha ao adicionar sensor.']);
            }
        }
        break;

    // ✏️ Editar sensor
    case 'editar-status':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $status = $_POST['status'];

            // Primeiro busca o sensor atual
            $sensor = $sensorModel->buscarSensorPorId($id);
            
            if ($sensor) {
                // Mantém todos os dados, altera apenas o status
                $resultado = $sensorModel->editarSensor(
                    $id,
                    $sensor['TIPO_SENSOR'],
                    $sensor['LOCALIZACAO'],
                    $sensor['DATA_INSTALACAO'],
                    $status
                );

                if ($resultado) {
                    echo json_encode(['mensagem' => 'Status do sensor atualizado com sucesso!']);
                } else {
                    echo json_encode(['erro' => 'Falha ao atualizar status do sensor.']);
                }
            } else {
                echo json_encode(['erro' => 'Sensor não encontrado.']);
            }
        }
        break;

    // ❌ Excluir sensor
    case 'excluir':
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $resultado = $sensorModel->excluirSensor($id);

        if ($resultado) {
            echo json_encode(['mensagem' => 'Sensor excluído com sucesso!']);
        } else {
            echo json_encode(['erro' => 'Falha ao excluir sensor.']);
        }
    } else {
        echo json_encode(['erro' => 'ID do sensor não fornecido.']);
    }
    break;

    // 🔍 Buscar sensor por ID
    case 'buscar':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $sensor = $sensorModel->buscarSensorPorId($id);

            if ($sensor) {
                header('Content-Type: application/json');
                echo json_encode($sensor);
            } else {
                echo json_encode(['erro' => 'Sensor não encontrado.']);
            }
        }
        break;

    default:
        echo json_encode(['erro' => 'Ação inválida']);
}
?>
