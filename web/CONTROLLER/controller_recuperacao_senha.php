<?php
require_once(__DIR__."/conecta_banco.php");

// Atualizar banco
$conn = DataBase::getConnection();
$stmt = $conn->prepare("UPDATE USUARIO SET SENHA = ? WHERE EMAIL = ?");
$stmt->bind_param("ss", $_GET["senhaTemp"], $_GET["email"]);
$stmt->execute();

header("Location: ../VIEW/cadastrar.php?sucesso=2");
exit();




