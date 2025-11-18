<?php
require_once(__DIR__ . '/../CONTROLLER/conecta_banco.php');

class Historico {
    private $conn;

    public function __construct() {
        $this->conn = DataBase::getConnection(); 
    }

    // Buscar dados para gráficos
    public function BuscarDadosGrafico() {
    $sql = "SELECT H.ID_SENSOR, H.DADOS, H.UNIDADE_MEDIDA, 
                   H.DATA_HORA_COLETA, S.TIPO_SENSOR
            FROM HISTORICO H
            INNER JOIN SENSORES S ON S.ID_SENSOR = H.ID_SENSOR
            ORDER BY H.DATA_HORA_COLETA ASC";

    $result = $this->conn->query($sql);

    $dados = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $dados[] = $row;
        }
    }
    return $dados;
}


    // Inserir histórico (sem latitude, longitude, data e hora)
    public function InserirHistorico($id_sensor, $dados, $unidade) {
        $stmt = $this->conn->prepare(
            "INSERT INTO HISTORICO (ID_SENSOR, DADOS, UNIDADE_MEDIDA)
             VALUES (?, ?, ?)"
        );
        $stmt->bind_param("iss", $id_sensor, $dados, $unidade);

        return $stmt->execute();
    }

    public function BuscarPorDataETipo($data, $tipoSensor = null) {
    $sql = "SELECT H.ID_SENSOR, H.DADOS, H.UNIDADE_MEDIDA, 
                   H.DATA_HORA_COLETA, S.TIPO_SENSOR
            FROM HISTORICO H
            INNER JOIN SENSORES S ON S.ID_SENSOR = H.ID_SENSOR
            WHERE DATE(H.DATA_HORA_COLETA) = ?";

    if ($tipoSensor) {
        $sql .= " AND S.TIPO_SENSOR = ?";
    }

    $stmt = $this->conn->prepare($sql);

    if ($tipoSensor) {
        $stmt->bind_param("ss", $data, $tipoSensor);
    } else {
        $stmt->bind_param("s", $data);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $dados = [];
    while ($row = $result->fetch_assoc()) {
        $dados[] = $row;
    }
    return $dados;
}
    //Busca dados para o filtro de cidade
    public function BuscarPorLocalizacao($localizacao) {
    $stmt = $this->conn->prepare("
        SELECT H.ID_SENSOR, H.DADOS, H.UNIDADE_MEDIDA, H.DATA_HORA_COLETA,
               S.TIPO_SENSOR, S.LOCALIZACAO
        FROM HISTORICO H
        INNER JOIN SENSORES S ON S.ID_SENSOR = H.ID_SENSOR
        WHERE S.LOCALIZACAO = ?
        ORDER BY H.DATA_HORA_COLETA ASC
    ");
    $stmt->bind_param("s", $localizacao);
    $stmt->execute();

    $result = $stmt->get_result();
    $dados = [];

    while ($row = $result->fetch_assoc()) {
        $dados[] = $row;
    }

    return $dados;
}


}
?>
