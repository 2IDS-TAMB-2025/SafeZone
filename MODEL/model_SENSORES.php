<?php
require_once '../CONTROLLER/conecta_banco.php'; 

class SensorModel {

    private $conn;

    public function __construct() {
        $this->conn = DataBase::getConnection();
    }

    // Listar todos os sensores
    public function listarSensores() {
        $sql = "SELECT * FROM SENSORES";
        $result = $this->conn->query($sql);

        $sensores = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $sensores[] = $row;
            }
        }
        return $sensores;
    }

    // Buscar um sensor específico pelo ID
    public function buscarSensorPorId($id) {
        $sql = "SELECT * FROM SENSORES WHERE ID_SENSOR = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Adicionar novo sensor
    public function adicionarSensor($tipo, $localizacao, $data_instalacao, $status) {
        $sql = "INSERT INTO SENSORES (TIPO_SENSOR, LOCALIZACAO, DATA_INSTALACAO, STATUS_SENSOR) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssss", $tipo, $localizacao, $data_instalacao, $status);
        return $stmt->execute();
    }

    // Editar um sensor existente
    public function editarSensor($id, $tipo, $localizacao, $data_instalacao, $status) {
        $sql = "UPDATE SENSORES 
                SET TIPO_SENSOR = ?, LOCALIZACAO = ?, DATA_INSTALACAO = ?, STATUS_SENSOR = ?
                WHERE ID_SENSOR = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ssssi", $tipo, $localizacao, $data_instalacao, $status, $id);
        return $stmt->execute();
    }

    // Excluir um sensor
    public function excluirSensor($id) {
        // Primeiro verifique se o sensor existe
        $sqlCheck = "SELECT ID_SENSOR FROM SENSORES WHERE ID_SENSOR = ?";
        $stmtCheck = $this->conn->prepare($sqlCheck);
        $stmtCheck->bind_param("i", $id);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();
    
        if ($result->num_rows === 0) {
            return false; // Sensor não existe
        }

        // Se existir, proceda com a exclusão
        $sql = "DELETE FROM SENSORES WHERE ID_SENSOR = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}
?>
