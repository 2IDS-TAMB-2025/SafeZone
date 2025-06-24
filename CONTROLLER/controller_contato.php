<?php
require_once(__DIR__.'/../MODEL/model_contato.php');

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 405 Method Not Allowed");
    exit;
}

// Verifica campos obrigatórios
$required_fields = ['name', 'email', 'subject', 'message'];
foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode([
            'success' => false,
            'message' => 'Por favor, preencha todos os campos obrigatórios.'
        ]);
        exit;
    }
}

$contatoModel = new Contato();

try {
    $resultado = $contatoModel->inserirContato(
        trim($_POST['name']),
        trim($_POST['email']),
        $_POST['subject'],
        trim($_POST['message'])
    );

    if ($resultado) {
        echo json_encode([
            'success' => true,
            'message' => 'Mensagem enviada com sucesso! Em breve entraremos em contato.'
        ]);
    } else {
        throw new Exception("Ocorreu um erro ao enviar sua mensagem.");
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>