<?php
require_once(__DIR__.'/../MODEL/model_usuario.php');

// Verifica se 'tipo_acao' está definido
if (!isset($_GET['tipo_acao'])) {
    die('Ação não especificada!');
}

$usuarioModel = new Usuario();

if ($_GET['tipo_acao'] === 'cadastro') {
    // Campos obrigatórios comuns
    $required_fields = ['nome', 'sobrenome', 'email', 'DATA_NASCIMENTO', 'CPF', 'telefone', 'senha'];
    $missing_fields = [];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $missing_fields[] = $field;
        }
    }
    if (!empty($missing_fields)) {
        die('Campos obrigatórios faltando: ' . implode(', ', $missing_fields));
    }

    // Dados básicos
    $nome              = $_POST['nome'];
    $sobrenome         = $_POST['sobrenome'];
    $email             = $_POST['email'];
    $data_nascimento   = $_POST['DATA_NASCIMENTO'];
    $cpf               = $_POST['CPF'];
    $telefone          = $_POST['telefone'];
    $senha             = $_POST['senha'];

    // Verifica se é administrador (campo opcional)
    $razao_social = !empty($_POST['razao_social']) ? $_POST['razao_social'] : null;
    $cnpj = !empty($_POST['cnpj']) ? $_POST['cnpj'] : null;
    $tipo_usuario = (!empty($cnpj) && !empty($razao_social)) ? 'ADMINISTRADOR' : 'USUARIO';

    // Executa inserção
    try {
        $resultado = $usuarioModel->inserirUsuario(
            $nome,
            $sobrenome,
            $email,
            $data_nascimento,
            $cpf,
            $senha,
            $telefone,
            $razao_social,
            $cnpj,
            $tipo_usuario
        );
    } catch (InvalidArgumentException $e) {
        die('Erro: ' . $e->getMessage());
    }

    if ($resultado) {
        header('Location: ../VIEW/cadastrar.php');
        exit;
    } else {
        die('Erro ao cadastrar usuário.');
    }

} 
elseif ($_GET['tipo_acao'] === 'login') {
    // Validação dos campos do login
    if (empty($_POST['email']) || empty($_POST['senha'])) {
        die('Email e senha são obrigatórios!');
    }

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $usuario = $usuarioModel->validarLogin($email, $senha);
    if ($usuario) {
        session_start();
        $_SESSION['usuario'] = $usuario;
        header('Location: ../VIEW/index.php?id='.$usuario["ID_USUARIO"]);
        exit;
    } else {
    header('Location: ../VIEW/cadastrar.php?erro=1');
    exit;}

    

} 
elseif ($_GET['tipo_acao'] === 'editar') {
    // Verifica se o ID foi fornecido
    if (empty($_POST['id'])) {
        die('ID do usuário não fornecido!');
    }

    $id                = $_POST['id'];
    $nome              = $_POST['nome'] ?? null;
    $sobrenome         = $_POST['sobrenome'] ?? null;
    $email             = $_POST['email'] ?? null;
    $data_nascimento   = $_POST['DATA_NASCIMENTO'] ?? null;
    $cpf               = $_POST['CPF'] ?? null;
    $telefone          = $_POST['telefone'] ?? null;
    $senha             = $_POST['senha'] ?? null;
    $razao_social      = $_POST['razao_social'] ?? null;
    $cnpj              = $_POST['cnpj'] ?? null;
    $tipo_usuario      = (!empty($cnpj) || !empty($razao_social)) ? 'ADMINISTRADOR' : 'USUARIO';

    try {
       $resultado = $usuarioModel->editaUsuario

(
            $id,
            $nome,
            $sobrenome,
            $email,
            $data_nascimento,
            $cpf,
            $senha,
            $telefone,
            $razao_social,
            $cnpj,
            $tipo_usuario
        );
    } catch (Exception $e) {
        die('Erro ao editar usuário: ' . $e->getMessage());
    }

    if ($resultado) {
        header('Location: ../VIEW/index.php?id='.$usuario["ID_USUARIO"]);
        exit;
    } else {
        die('Falha ao atualizar usuário.');
    }

} 
elseif ($_GET['tipo_acao'] === 'excluir') {
    // Verifica se o ID foi fornecido
    if (empty($_GET['id'])) {
        die('ID do usuário não fornecido para exclusão!');
    }

    $id = $_GET['id'];

    try {
       $resultado = $usuarioModel->excluirUsuarioId($id);

    } catch (Exception $e) {
        die('Erro ao excluir usuário: ' . $e->getMessage());
    }

    if ($resultado) {
        header('Location: ../VIEW/index.php?id='.$usuario["ID_USUARIO"]);
        exit;
    } else {
        die('Falha ao excluir usuário.');
    }
}


else {
    die('Ação inválida!');
}
