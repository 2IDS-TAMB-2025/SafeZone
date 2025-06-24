<?php
include_once '../CONTROLLER/conecta_banco.php';

class notificacaoUsuario {

    public function inserirSensorAreaRisco($ID, $FK_ID_SENSOR, $FK_ID_RISCO) {
        $conn = DataBase::getConnection();
        $stmt = $conn->prepare("INSERT INTO Sensor_Area_Risco (ID, FK_ID_SENSOR, FK_ID_RISCO) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $ID, $FK_ID_SENSOR, $FK_ID_RISCO);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }

    public function getSensorAreaRiscoById($ID) {
        $conn = DataBase::getConnection();
        $stmt = $conn->prepare("SELECT * FROM Sensor_Area_Risco WHERE ID = ?");
        $stmt->bind_param("i", $ID);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getSensorAreaRiscoBySensor($FK_ID_SENSOR) {
        $conn = DataBase::getConnection();
        $stmt = $conn->prepare("SELECT * FROM Sensor_Area_Risco WHERE FK_ID_SENSOR = ?");
        $stmt->bind_param("i", $FK_ID_SENSOR);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function editaSensorAreaRisco($ID, $FK_ID_SENSOR, $FK_ID_RISCO) {
        $conn = DataBase::getConnection();
        $stmt = $conn->prepare("UPDATE Sensor_Area_Risco SET FK_ID_SENSOR = ?, FK_ID_RISCO = ? WHERE ID = ?");
        $stmt->bind_param("iii", $FK_ID_SENSOR, $FK_ID_RISCO, $ID);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
}
?>