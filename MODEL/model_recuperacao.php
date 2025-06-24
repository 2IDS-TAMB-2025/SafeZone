<?php
require_once 'conecta_banco.php';

class RecuperacaoModel {
    private $conn;

    public function __construct() {
        $this->conn = DataBase::getConnection();
    }

    // Criar código de recuperação
    public function criarCodigoRecuperacao($idUsuario, $codigo, $expiracao) {
        $sql = "INSERT INTO RECUPERACAO_SENHA (FK_ID_USUARIO, CODIGO, DATA_EXPIRACAO) 
                VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("iss", $idUsuario, $codigo, $expiracao);
        return $stmt->execute();
    }

    // Verificar código válido
    public function verificarCodigo($idUsuario, $codigo) {
        $sql = "SELECT * FROM RECUPERACAO_SENHA 
                WHERE FK_ID_USUARIO = ? 
                AND CODIGO = ? 
                AND UTILIZADO = FALSE 
                AND DATA_EXPIRACAO > NOW()";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("is", $idUsuario, $codigo);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Marcar código como utilizado
    public function marcarComoUtilizado($idRecuperacao) {
        $sql = "UPDATE RECUPERACAO_SENHA SET UTILIZADO = TRUE WHERE ID_RECUPERACAO = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $idRecuperacao);
        return $stmt->execute();
    }

    // Buscar usuário por email
    public function buscarUsuarioPorEmail($email) {
        $sql = "SELECT ID_USUARIO, NOME, EMAIL FROM USUARIO WHERE EMAIL = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    // Atualizar senha
    public function atualizarSenha($idUsuario, $novaSenha) {
        $sql = "UPDATE USUARIO SET SENHA = ? WHERE ID_USUARIO = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("si", $novaSenha, $idUsuario);
        return $stmt->execute();
    }

    public function podeSolicitarCodigo($email) {
        $sql = "SELECT SOLICITACOES_HOJE, ULTIMA_SOLICITACAO 
                FROM RECUPERACAO_SENHA 
                WHERE FK_ID_USUARIO = (SELECT ID_USUARIO FROM USUARIO WHERE EMAIL = ?)
                AND DATE(ULTIMA_SOLICITACAO) = CURDATE()";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            return $row['SOLICITACOES_HOJE'] < 2;
        }
        return true;
    }

    public function registrarSolicitacao($email, $codigo) {
        // Verifica se já existe registro hoje
        $sql = "SELECT ID_RECUPERACAO FROM RECUPERACAO_SENHA 
                WHERE FK_ID_USUARIO = (SELECT ID_USUARIO FROM USUARIO WHERE EMAIL = ?)
                AND DATE(ULTIMA_SOLICITACAO) = CURDATE()";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            // Atualiza existente
            $sql = "UPDATE RECUPERACAO_SENHA 
                    SET CODIGO = ?, 
                        DATA_CRIACAO = NOW(),
                        DATA_EXPIRACAO = DATE_ADD(NOW(), INTERVAL 1 MINUTE),
                        SOLICITACOES_HOJE = SOLICITACOES_HOJE + 1,
                        UTILIZADO = FALSE
                    WHERE FK_ID_USUARIO = (SELECT ID_USUARIO FROM USUARIO WHERE EMAIL = ?)";
        } else {
            // Cria novo
            $sql = "INSERT INTO RECUPERACAO_SENHA 
                    (FK_ID_USUARIO, CODIGO, DATA_CRIACAO, DATA_EXPIRACAO, SOLICITACOES_HOJE, ULTIMA_SOLICITACAO)
                    VALUES (
                        (SELECT ID_USUARIO FROM USUARIO WHERE EMAIL = ?),
                        ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 MINUTE), 1, NOW()
                    )";
        }
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $email, $codigo);
        return $stmt->execute();
    }

}
?>