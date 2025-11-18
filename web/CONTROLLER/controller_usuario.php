<?php
require_once(__DIR__.'/../MODEL/model_usuario.php');


if (!isset($_GET['tipo_acao'])) {
    die('Ação não especificada!');
}

$usuarioModel = new Usuario();


if ($_GET['tipo_acao'] === 'cadastro') {
   
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

        $isApi = isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false;

        if ($resultado) {
            if ($isApi) {
                echo json_encode(['success' => true, 'message' => 'Usuário cadastrado com sucesso!']);
            } else {
                header('Location: ../VIEW/cadastrar.php?sucesso=1');
            }
            exit;
        } else {
            if ($isApi) {
                echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar usuário.']);
            } else {
                header('Location: ../VIEW/cadastrar.php?erro=1');
            }
            exit;
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
    
    // DEBUG - Mostrar todos os dados
    echo "<pre>";
    echo "DEBUG - Dados do Upload:\n";
    echo "FILES: "; print_r($_FILES);
    echo "POST: "; print_r($_POST);
    echo "</pre>";
    
    if (empty($_POST['id'])) {
        die('ID do usuário não fornecido!');
    }

    $id = $_POST['id'];
    
    // DEBUG - Verificar se o arquivo está chegando
    if (isset($_FILES['foto_perfil'])) {
        echo "<p>Arquivo recebido: " . $_FILES['foto_perfil']['name'] . "</p>";
        echo "<p>Erro: " . $_FILES['foto_perfil']['error'] . "</p>";
        echo "<p>Tamanho: " . $_FILES['foto_perfil']['size'] . " bytes</p>";
    }

    if (empty($_POST['id'])) {
        die('ID do usuário não fornecido!');
    }

    $id          = $_POST['id'];
    $nome        = $_POST['nome'] ?? '';
    $sobrenome   = $_POST['sobrenome'] ?? '';
    $email       = $_POST['email'] ?? '';
    $senha       = $_POST['senha'] ?? '';
    $telefone    = $_POST['telefone'] ?? '';

    // Recupera dados atuais
    $dados = $usuarioModel->getUsuarioId($id)[0];
    $data_nascimento = $dados['DATA_NASCIMENTO'];
    $cpf             = $dados['CPF'];
    $razao_social    = $dados['RAZAO_SOCIAL'];
    $cnpj            = $dados['CNPJ'];
    $tipo_usuario    = $dados['TIPO_USUARIO'];
    $foto_perfil     = $dados['FOTO_PERFIL'];

    // --- UPLOAD SIMPLIFICADO ---
    if (isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] === UPLOAD_ERR_OK) {
        
        $uploadDir = "../IMAGES/PERFIL/";
        
        // Gera nome único
        $extensao = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
        $novoNome = uniqid("FOTO_") . "." . $extensao;
        $destino = $uploadDir . $novoNome;
        
        // Move o arquivo
        if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $destino)) {
            $foto_perfil = $novoNome;
        }
    }

    // --- UPDATE ---
    try {
        $resultado = $usuarioModel->editaUsuario(
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
            $tipo_usuario,
            $foto_perfil
        );
    } catch (Exception $e) {
        die("Erro ao editar usuário: " . $e->getMessage());
    }

    if ($resultado) {
        header("Location: ../VIEW/config.php?id=$id&ok=1");
        exit;
    } else {
        die("Falha ao atualizar usuário.");
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

