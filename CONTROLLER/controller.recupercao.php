<?php
require_once '../MODEL/model_recuperacao.php';

class RecuperacaoController {
    private $model;

    public function __construct() {
        $this->model = new RecuperacaoModel();
    }

    public function solicitarCodigo($email) {
        if (!$this->model->podeSolicitarCodigo($email)) {
            return [
                'sucesso' => false,
                'mensagem' => 'Você já atingiu o limite de 2 solicitações hoje. Tente novamente amanhã.'
            ];
        }

        $codigo = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

        if ($this->model->registrarSolicitacao($email, $codigo)) {
            return [
                'sucesso' => true,
                'codigo' => $codigo,
                'expiracao' => date('H:i:s', strtotime('+1 minute'))
            ];
        }

        return ['sucesso' => false, 'mensagem' => 'Erro ao gerar código'];
    }

    public function verificarCodigo($email, $codigo) {
        if ($this->model->verificarCodigo($email, $codigo)) {
            return ['sucesso' => true];
        }
        return ['sucesso' => false, 'mensagem' => 'Código inválido ou expirado'];
    }

    public function atualizarSenha($email, $novaSenha) {
        $usuario = $this->model->buscarUsuarioPorEmail($email);
        if (!$usuario) {
            return ['sucesso' => false, 'mensagem' => 'Usuário não encontrado'];
        }

        if ($this->model->atualizarSenha($usuario['ID_USUARIO'], $novaSenha)) {
            return ['sucesso' => true];
        }
        return ['sucesso' => false, 'mensagem' => 'Erro ao atualizar senha'];
    }
}

// Rotas
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller = new RecuperacaoController();
    $acao = $_POST['acao'] ?? '';
    
    header('Content-Type: application/json');
    
    switch ($acao) {
        case 'solicitar':
            $email = $_POST['email'] ?? '';
            echo json_encode($controller->solicitarCodigo($email));
            break;
            
        case 'verificar':
            $email = $_POST['email'] ?? '';
            $codigo = $_POST['codigo'] ?? '';
            echo json_encode($controller->verificarCodigo($email, $codigo));
            break;
            
        case 'atualizar':
            $email = $_POST['email'] ?? '';
            $novaSenha = $_POST['novaSenha'] ?? '';
            echo json_encode($controller->atualizarSenha($email, $novaSenha));
            break;
            
        default:
            echo json_encode(['sucesso' => false, 'mensagem' => 'Ação inválida']);
    }
    exit;
}
?>