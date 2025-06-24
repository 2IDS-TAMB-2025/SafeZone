<?php

/*é realmente necessário esse? Achar uma tela para usar esse, talvez relatório?*/

include_once '../CONTROLLER/conecta_banco.php';

if (!method_exists('DataBase', 'getConnection')) {
    die("Erro: método getConnection() não encontrado na classe DataBase.");
}

class AreaRisco {

    /**
     * Insere uma nova área de risco
     */
    public function inserirAreaRisco(
        $TIPO_RISCO,
        $DESCRICAO,
        $RUA,
        $NUMERO,
        $CIDADE,
        $ESTADO,
        $PAIS,
        $LATITUDE,
        $LONGITUDE
    ) {
        $conn = DataBase::getConnection();
        $stmt = $conn->prepare(
            "INSERT INTO AREA_RISCO
             (TIPO_RISCO, DESCRICAO, RUA, NUMERO, CIDADE, ESTADO, PAIS, LATITUDE, LONGITUDE)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param(
            "sssssssss d",
            $TIPO_RISCO,
            $DESCRICAO,
            $RUA,
            $NUMERO,
            $CIDADE,
            $ESTADO,
            $PAIS,
            $LATITUDE,
            $LONGITUDE
        );
        $result = $stmt->execute();
        if (!$result) {
            error_log("Erro ao inserir Área de Risco: " . $stmt->error);
        }
        $stmt->close();
        $conn->close();
        return $result;
    }

    /* GET: retorna todas as áreas de risco */
    public function getAllAreas() {
        $conn = DataBase::getConnection();
        $stmt = $conn->prepare("SELECT * FROM AREA_RISCO");
        $stmt->execute();
        $result = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conn->close();
        return $result;
    }

    /* GET: por ID */
    public function getById($ID_RISCO) {
        $conn = DataBase::getConnection();
        $stmt = $conn->prepare("SELECT * FROM AREA_RISCO WHERE ID_RISCO = ?");
        $stmt->bind_param("i", $ID_RISCO);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        $stmt->close();
        $conn->close();
        return $row;
    }

    /* GET: por tipo */
    public function getByTipo($TIPO_RISCO) {
        $conn = DataBase::getConnection();
        $stmt = $conn->prepare("SELECT * FROM AREA_RISCO WHERE TIPO_RISCO = ?");
        $stmt->bind_param("s", $TIPO_RISCO);
        $stmt->execute();
        $rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        $conn->close();
        return $rows;
    }

    /* PUT: atualiza área de risco */
    public function editarAreaRisco(
        $ID_RISCO,
        $TIPO_RISCO,
        $DESCRICAO,
        $RUA,
        $NUMERO,
        $CIDADE,
        $ESTADO,
        $PAIS,
        $LATITUDE,
        $LONGITUDE
    ) {
        $conn = DataBase::getConnection();
        $stmt = $conn->prepare(
            "UPDATE AREA_RISCO SET
             TIPO_RISCO = ?,
             DESCRICAO = ?,
             RUA = ?,
             NUMERO = ?,
             CIDADE = ?,
             ESTADO = ?,
             PAIS = ?,
             LATITUDE = ?,
             LONGITUDE = ?
             WHERE ID_RISCO = ?"
        );
        $stmt->bind_param(
            "sssssssssdi",
            $TIPO_RISCO,
            $DESCRICAO,
            $RUA,
            $NUMERO,
            $CIDADE,
            $ESTADO,
            $PAIS,
            $LATITUDE,
            $LONGITUDE,
            $ID_RISCO
        );
        $result = $stmt->execute();
        if (!$result) {
            error_log("Erro ao editar Área de Risco: " . $stmt->error);
        }
        $stmt->close();
        $conn->close();
        return $result;
    }

    /* DELETE: excluir por ID */
    public function excluirById($ID_RISCO) {
        $conn = DataBase::getConnection();
        $stmt = $conn->prepare("DELETE FROM AREA_RISCO WHERE ID_RISCO = ?");
        $stmt->bind_param("i", $ID_RISCO);
        $result = $stmt->execute();
        if (!$result) {
            error_log("Erro ao excluir Área de Risco: " . $stmt->error);
        }
        $stmt->close();
        $conn->close();
        return $result;
    }

}

?>
