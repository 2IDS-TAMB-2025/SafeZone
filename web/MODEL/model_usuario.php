<?php
require_once(__DIR__.'/../CONTROLLER/conecta_banco.php');
if (!method_exists('DataBase', 'getConnection')) {
    die("Erro: método getConnection() não encontrado na classe DataBase.");
}


class Usuario {

    /* POST: inserir novo usuário */
    public function InserirUsuario(
        $NOME,
        $SOBRENOME,
        $EMAIL,
        $DATA_NASCIMENTO,
        $CPF,
        $SENHA,
        $TELEFONE_CELULAR,
        $RAZAO_SOCIAL = null,
        $CNPJ = null,
        $TIPO_USUARIO = 'USUARIO' // Valor padrão
    ) {
        // Se for administrador, obrigue razão social e CNPJ
        if ($TIPO_USUARIO === 'ADMINISTRADOR') {
    if (empty($RAZAO_SOCIAL) || empty($CNPJ)) {
        throw new InvalidArgumentException("RAZAO_SOCIAL e CNPJ são obrigatórios para ADMINISTRADOR");
            }
        }

        $conn = DataBase::getConnection();

        $stmt = $conn->prepare(
            "INSERT INTO USUARIO
                (NOME, SOBRENOME, EMAIL, DATA_NASCIMENTO, CPF, SENHA,
                 TELEFONE_CELULAR, RAZAO_SOCIAL, CNPJ, TIPO_USUARIO)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        // todos são strings
        $stmt->bind_param(
            "ssssssssss",
            $NOME,
            $SOBRENOME,
            $EMAIL,
            $DATA_NASCIMENTO,
            $CPF,
            $SENHA,
            $TELEFONE_CELULAR,
            $RAZAO_SOCIAL,
            $CNPJ,
            $TIPO_USUARIO
        );

        $result = $stmt->execute();
        if (!$result) {
            echo "Erro na execução: " . $stmt->error;
            die();
        }

        //$stmt->close();
        //$conn->close();
        return $result;
    }

    /* GET: validar login */
    public function ValidarLogin($EMAIL, $SENHA) {
        $conn = DataBase::getConnection();

        $stmt = $conn->prepare(
            "SELECT * FROM USUARIO WHERE EMAIL = ? AND SENHA = ?"
        );
        $stmt->bind_param("ss", $EMAIL, $SENHA);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();

        //$stmt->close();
        //$conn->close();
        return $usuario;
    }

    /* GET: validar Admin */
    public function validarAdmin($RAZAO_SOCIAL, $CNPJ) {
        $conn = DataBase::getConnection();

        $stmt = $conn->prepare(
            "SELECT * FROM USUARIO WHERE RAZAO_SOCIAL = ? AND CNPJ = ?"
        );
        $stmt->bind_param("ss", $RAZAO_SOCIAL, $CNPJ);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();

        //$stmt->close();
        //$conn->close();
        return $usuario;
    }

    /* GET: buscar usuário por ID */
    public function getUsuarioId($ID_USUARIO) {
        $conn = DataBase::getConnection();
        $stmt = $conn->prepare("SELECT * FROM USUARIO WHERE ID_USUARIO = ?");
        $stmt->bind_param("i", $ID_USUARIO);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

   

    /* GET: buscar por razão social */
    public function getUsuarioRazaoSocial($RAZAO_SOCIAL) {
        $conn = DataBase::getConnection();
        $stmt = $conn->prepare("SELECT * FROM USUARIO WHERE RAZAO_SOCIAL = ?");
        $stmt->bind_param("s", $RAZAO_SOCIAL);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /* GET: buscar por CNPJ */
    public function getUsuarioCNPJ($CNPJ) {
        $conn = DataBase::getConnection();
        $stmt = $conn->prepare("SELECT * FROM USUARIO WHERE CNPJ = ?");
        $stmt->bind_param("s", $CNPJ);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    

    /* GET: buscar por email */
    public function getUsuarioEmail($EMAIL) {
        $conn = DataBase::getConnection();
        $stmt = $conn->prepare("SELECT * FROM USUARIO WHERE EMAIL = ?");
        $stmt->bind_param("s", $EMAIL);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    

    /* GET: buscar por senha */
    public function getUsuarioSenha($SENHA) {
            $conn = DataBase::getConnection();
            $stmt = $conn->prepare("SELECT * FROM USUARIO WHERE SENHA = ?");
            $stmt->bind_param("s", $SENHA);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }

        

        /* PUT: editar usuário existente */
        public function editaUsuario($ID_USUARIO, $NOME, $SOBRENOME, $EMAIL, $DATA_NASCIMENTO,
            $CPF, $SENHA, $TELEFONE_CELULAR, $RAZAO_SOCIAL = null,
            $CNPJ = null, $TIPO_USUARIO = 'USUARIO', $FOTO_PERFIL = null) {

        $conn = DataBase::getConnection();

        $stmt = $conn->prepare(
            "UPDATE USUARIO
            SET NOME = ?, SOBRENOME = ?, EMAIL = ?, DATA_NASCIMENTO = ?, CPF = ?,
                SENHA = ?, TELEFONE_CELULAR = ?, RAZAO_SOCIAL = ?, CNPJ = ?, TIPO_USUARIO = ?, FOTO_PERFIL = ?
            WHERE ID_USUARIO = ?"
        );
        $stmt->bind_param(
            "sssssssssssi",
            $NOME, $SOBRENOME, $EMAIL, $DATA_NASCIMENTO, $CPF,
            $SENHA, $TELEFONE_CELULAR, $RAZAO_SOCIAL, $CNPJ, $TIPO_USUARIO, $FOTO_PERFIL, $ID_USUARIO
        );

        $result = $stmt->execute();
        return $result;
    }


    /* DELETE: excluir usuário por ID */
    public function excluirUsuarioId($ID_USUARIO) {
        $conn = DataBase::getConnection();
        $stmt = $conn->prepare("DELETE FROM USUARIO WHERE ID_USUARIO = ?");
        $stmt->bind_param("i", $ID_USUARIO);
        $result = $stmt->execute();
        //$stmt->close();
        //$conn->close();
        return $result;
    }

   

}
?>