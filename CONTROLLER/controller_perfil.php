<?php
require_once(__DIR__ . '/../MODEL/model_usuario.php');

session_start();

// Verificação básica de ação
if(!isset($_GET["id"]) && !isset($_GET["tipo_acao"]) && !isset($_GET['acao'])){
    header('Location: ../VIEW/index.php');
    exit;
}

$usuarioModel = new Usuario();

// Obter usuário por ID se fornecido
if(isset($_GET["id"])){
    $id = $_GET["id"];
    $usuario = $usuarioModel->getUsuarioId($id);
}

// Ação: Editar perfil
if(isset($_GET["tipo_acao"]) && $_GET["tipo_acao"] == "editar"){
    // Processamento de upload de imagem
    $foto_perfil = null;
    if(isset($_FILES['foto_perfil']) && $_FILES['foto_perfil']['error'] == UPLOAD_ERR_OK){
        $pasta_destino = "../IMAGES/PERFIL/";
       
        if (!is_dir($pasta_destino)) {
            mkdir($pasta_destino, 0777, true);
        }
        
        $extensao = strtolower(pathinfo($_FILES['foto_perfil']['name'], PATHINFO_EXTENSION));
        $extensoes_validas = ['jpg', 'jpeg', 'png', 'gif'];
        
        if(in_array($extensao, $extensoes_validas)){
            $nome_arquivo = uniqid('perfil_', true) . '.' . $extensao;
            $caminho_completo = $pasta_destino . $nome_arquivo;

            if (move_uploaded_file($_FILES['foto_perfil']['tmp_name'], $caminho_completo)) {
                $foto_perfil = $nome_arquivo;
            }
        }
    }

    // Validação dos campos obrigatórios
    $campos_obrigatorios = ["id", "nome", "sobrenome", "email", "data_nascimento", 
                           "cpf", "senha", "telefone", "tipo_usuario"];
    
    $dados_validos = true;
    foreach($campos_obrigatorios as $campo){
        if(!isset($_POST[$campo]) || empty($_POST[$campo])){
            $dados_validos = false;
            break;
        }
    }

    if($dados_validos){
        // Verifica se o email já existe (para outro usuário)
        $usuario_com_email = $usuarioModel->getUsuarioEmail($_POST["email"]);
        if(!empty($usuario_com_email) && $usuario_com_email[0]['ID_USUARIO'] != $_POST["id"]){
            $_SESSION['erro'] = "Este email já está sendo usado por outro usuário!";
            header('Location: ../VIEW/config.php?id='.$_POST["id"]);
            exit;
        }

        // Mantém foto atual se nova não foi enviada
        $usuario_atual = $usuarioModel->getUsuarioId($_POST["id"]);
        $foto_atual = !empty($usuario_atual[0]["FOTO_PERFIL"]) ? $usuario_atual[0]["FOTO_PERFIL"] : null;
        
        if ($foto_perfil === null && $foto_atual !== null) {
            $foto_perfil = $foto_atual;
        }

        // Campos opcionais para PJ
        $razao_social = isset($_POST["razao_social"]) ? $_POST["razao_social"] : null;
        $cnpj = isset($_POST["cnpj"]) ? $_POST["cnpj"] : null;

        $resultado = $usuarioModel->editaUsuario(
            $_POST["id"], 
            $_POST["nome"], 
            $_POST["sobrenome"], 
            $_POST["email"],
            $_POST["data_nascimento"], 
            $_POST["cpf"], 
            $_POST["senha"], 
            $_POST["telefone"],
            $razao_social,
            $cnpj, 
            $_POST["tipo_usuario"], 
            $foto_perfil
        );

        if($resultado){
            // Atualiza dados na sessão se for o próprio usuário
            if(isset($_SESSION['usuario']['ID_USUARIO']) && $_SESSION['usuario']['ID_USUARIO'] == $_POST["id"]){
                $_SESSION['usuario'] = $usuarioModel->getUsuarioId($_POST["id"])[0];
            }
            
            $_SESSION['sucesso'] = "Perfil atualizado com sucesso!";
            header('Location: ../VIEW/config.php?id='.$_POST["id"]);
            exit;
        } else {
            $_SESSION['erro'] = "Erro ao atualizar perfil!";
            header('Location: ../VIEW/config.php?id='.$_POST["id"]);
            exit;
        }
    } else {
        $_SESSION['erro'] = "Preencha todos os campos obrigatórios!";
        header('Location: ../VIEW/config.php?id='.$_POST["id"]);
        exit;
    }
}

// Ação: Excluir conta
if(isset($_GET["tipo_acao"]) && $_GET["tipo_acao"] == "excluir"){
    // Obtém ID de GET ou SESSION
    $id = isset($_GET["id"]) ? $_GET["id"] : (isset($_SESSION['usuario']['ID_USUARIO']) ? $_SESSION['usuario']['ID_USUARIO'] : null);
    
    if($id){
        // Verifica se o usuário existe antes de excluir
        $usuario = $usuarioModel->getUsuarioId($id);
        
        if(!empty($usuario)){
            $resultado = $usuarioModel->excluirUsuarioId($id);
            
            if($resultado){
                // Se estava excluindo a própria conta, encerra a sessão
                if(isset($_SESSION['usuario']['ID_USUARIO']) && $_SESSION['usuario']['ID_USUARIO'] == $id){
                    session_destroy();
                }
                
                $_SESSION['sucesso'] = "Conta excluída com sucesso!";
                header('Location: ../VIEW/index.php');
                exit;
            } else {
                $_SESSION['erro'] = "Erro ao excluir conta!";
                header('Location: ../VIEW/index.php?id='.$id);
                exit;
            }
        } else {
            $_SESSION['erro'] = "Usuário não encontrado!";
            header('Location: ../VIEW/index.php');
            exit;
        }
    } else {
        $_SESSION['erro'] = "ID do usuário não fornecido!";
        header('Location: ../VIEW/index.php');
        exit;
    }
}

// Ações via AJAX
$acao = isset($_GET['acao']) ? $_GET['acao'] : '';

switch ($acao) {
    case 'visualizar':
        $id = $_SESSION['usuario']['ID_USUARIO'] ?? null;

        if (!$id) {
            echo json_encode(['erro' => 'Usuário não autenticado.']);
            exit;
        }

        $dados = $usuarioModel->getUsuarioId($id);
        
        if(!empty($dados)){
            echo json_encode($dados[0]);
        } else {
            echo json_encode(['erro' => 'Usuário não encontrado.']);
        }
        exit;

    case 'verificar_email':
        if(isset($_GET['email'])){
            $email = $_GET['email'];
            $usuario_com_email = $usuarioModel->getUsuarioEmail($email);
            
            // Verifica se o email existe e não pertence ao usuário atual (se houver ID)
            $id_usuario = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : null;
            $disponivel = true;
            
            if(!empty($usuario_com_email)){
                if($id_usuario && $usuario_com_email[0]['ID_USUARIO'] == $id_usuario){
                    $disponivel = true; // É o próprio usuário
                } else {
                    $disponivel = false; // Email já existe para outro usuário
                }
            }
            
            echo json_encode(['disponivel' => $disponivel]);
        } else {
            echo json_encode(['erro' => 'Email não fornecido.']);
        }
        exit;

    default:
        // Nenhuma ação específica
        break;
}
?>