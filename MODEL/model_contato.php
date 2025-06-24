<?php
require_once(__DIR__.'/../CONTROLLER/conecta_banco.php');
if (!method_exists('DataBase', 'getConnection')) {
    die("Erro: método getConnection() não encontrado na classe DataBase.");
}

class Contato {
    
    /* Método para inserir novo contato */
    public function inserirContato($nome, $email, $assunto, $mensagem) {
        $conn = DataBase::getConnection();

        // Validações básicas
        if (empty($nome) || empty($email) || empty($assunto) || empty($mensagem)) {
            throw new Exception("Todos os campos são obrigatórios.");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Por favor, insira um e-mail válido.");
        }

        if (strlen($mensagem) > 500) {
            throw new Exception("A mensagem não pode ultrapassar 500 caracteres.");
        }

        // Mapeia os valores do formulário para o ENUM do banco
        $assuntosValidos = [
            'duvida' => 'DUVIDA',
            'sugestao' => 'SUGESTAO',
            'problema' => 'REPORTAR PROBLEMA',
            'parceria' => 'PARCERIA',
            'outro' => 'OUTRO'
        ];

        $assuntoBanco = $assuntosValidos[strtolower($assunto)] ?? 'OUTRO';

        $stmt = $conn->prepare(
            "INSERT INTO CONTATO 
                (NOME_COMPLETO, EMAIL, ASSUNTO, MENSAGEM) 
             VALUES (?, ?, ?, ?)"
        );
        
        $stmt->bind_param("ssss", $nome, $email, $assuntoBanco, $mensagem);
        $result = $stmt->execute();
        
        return $result;
    }
}
?>