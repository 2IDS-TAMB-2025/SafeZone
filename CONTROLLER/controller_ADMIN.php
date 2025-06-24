<?php
require_once '../MODEL/model_usuario.php';
// Verifica se 'tipo_acao' está definido
if (!isset($_GET['tipo_acao'])) {
    die('Ação não especificada!');
}

elseif ($_GET['tipo_acao'] == 'login') {
    // Validação dos campos do login
    if (empty($_POST['email']) || empty($_POST['senha'])) {
        die('Razão social e CNPJ são obrigatórios!');
    }

    $email = $_POST['email'];
    $senha = $_POST['senha'];

    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->validarLogin($email, $senha);
    if ($usuario) {
        session_start();
        $_SESSION['usuario'] = $usuario;
        header(`Location: ../VIEW/gerenciar_sensores.php?id= . $id`);
        exit;
    } else {
        die('Razão social ou CNPJ incorretos!');
    }

}elseif ($_GET['tipo_acao'] == 'empresa') {
    $razao_social = $_POST['razao_social'];
    $CNPJ = $_POST['CNPJ'];
    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->validarAdmin($razao_social, $CNPJ);
    if ($usuario) {
        session_start();
        $_SESSION['usuario'] = $usuario;
       $id = $usuario['ID_USUARIO'];
        header("Location: ../VIEW/gerenciar_sensores.php?id=$id");
        exit;

    } header('Location: ../VIEW/graficos.php?erro=1');
    exit;
    } 

else {
    die('Ação inválida!');
}