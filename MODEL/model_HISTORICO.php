<?php
require_once(__DIR__.'/../CONTROLLER/conecta_banco.php');
class Historico {

    

    //  Buscar TUDO
    public function BuscarTodosHistoricos() {
        $conn = DataBase::getConnection();
        $result = $conn->query(
            "SELECT H.*, S.TIPO_SENSOR, S.LOCALIZACAO
             FROM HISTORICO H
             JOIN SENSORES S ON H.ID_SENSOR = S.ID_SENSOR
             ORDER BY H.DATA_COLETA DESC, H.HORA_COLETA DESC"
        );

        $historicos = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $historicos[] = $row;
            }
        }

        //$conn->close();
        return $historicos;
    }

    //  Buscar histórico por ID
    public function BuscarHistoricoPorID($id_historico) {
        $conn = DataBase::getConnection();
            
        $stmt = $conn->prepare(
            "SELECT * FROM HISTORICO WHERE ID_HISTORICO = ?"
        );
        $stmt->bind_param("i", $id_historico);
        $stmt->execute();

        $result = $stmt->get_result();
        $historico = $result->fetch_assoc();

        /*$stmt->close();
        $conn->close();*/

        return $historico;
    }

    //  Buscar históricos por ID_SENSOR 
    public function BuscarPorSensor($id_sensor) {
        $conn = DataBase::getConnection();

        $stmt = $conn->prepare(
            "SELECT * FROM HISTORICO 
             WHERE ID_SENSOR = ?
             ORDER BY DATA_COLETA DESC, HORA_COLETA DESC"
        );
        $stmt->bind_param("i", $id_sensor);
        $stmt->execute();

        $result = $stmt->get_result();

        $historicos = [];
        while ($row = $result->fetch_assoc()) {
            $historicos[] = $row;
        }

       // $stmt->close();
        //$conn->close();

        return $historicos;
    }

    //ocorrencia por mês
    public function OcorrenciasPorMes($idSensor, $limite) {
        
    $conn = DataBase::getConnection();
    
    $stmt = $conn->prepare(
        "SELECT 
            DATE_FORMAT(DATA_COLETA, '%Y-%m') AS mes,
            COUNT(*) AS ocorrencias,
            S.TIPO_SENSOR,
            S.LOCALIZACAO
         FROM HISTORICO H
         JOIN SENSORES S ON H.ID_SENSOR = S.ID_SENSOR
         WHERE H.ID_SENSOR = ? AND H.DADOS > ?
         GROUP BY DATE_FORMAT(DATA_COLETA, '%Y-%m'), S.TIPO_SENSOR, S.LOCALIZACAO
         ORDER BY mes"
    );
    
    $stmt->bind_param("id", $idSensor, $limite);
    $stmt->execute();
    
    $result = $stmt->get_result();
    
    $dados = [];
    while ($row = $result->fetch_assoc()) {
        $dados[] = $row;
    }
    
//$stmt->close();
   // $conn->close();
    
    return $dados;
    }

    //  Inner Join
    public function BuscarDadosGrafico() {
    $conn = DataBase::getConnection();

    $result = $conn->query(
       "SELECT H.*, S.TIPO_SENSOR, S.LOCALIZACAO
        FROM HISTORICO H
        INNER JOIN SENSORES S ON S.ID_SENSOR = H.ID_SENSOR
        ORDER BY H.DATA_COLETA ASC, H.HORA_COLETA ASC"
    );

    $dados = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $dados[] = $row;
        }
    }

   // $conn->close();
    return $dados;
}



}
?>
