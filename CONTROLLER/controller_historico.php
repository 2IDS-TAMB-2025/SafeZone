<?php
require_once(__DIR__.'/../MODEL/model_HISTORICO.php');

$historicoModel = new Historico();
$dados = $historicoModel->BuscarTodosHistoricos();

// Inicializa arrays para os gráficos
$dataColeta = [];
$horaColeta = [];
$dadosSensor = [];
$unidadeMedida = [];
$latitude = [];
$longitude = [];

foreach ($dados as $dado) {
    $dataColeta[] = $dado['DATA_COLETA'];
    $horaColeta[] = $dado['HORA_COLETA'];
    $dadosSensor[] = $dado['DADOS'];
    $unidadeMedida[] = $dado['UNIDADE_MEDIDA'];
    $latitude[] = $dado['LATITUDE'];
    $longitude[] = $dado['LONGITUDE'];
}

// Agrupar dados por unidade de medida
$dadosPorUnidade = [];
foreach ($dados as $dado) {
    $um = $dado['UNIDADE_MEDIDA'];
    $dadosPorUnidade[$um][] = $dado['DADOS'];
}
$mediasPorUnidade = [];
foreach ($dadosPorUnidade as $um => $valores) {
    $mediasPorUnidade[] = [
        'unidade' => $um,
        'media' => array_sum($valores) / count($valores)
    ];
}
$labelsUnidade = array_column($mediasPorUnidade, 'unidade');
$mediasUnidade = array_column($mediasPorUnidade, 'media');

// Agrupar dados por tipo de sensor
$dadosPorTipo = [];
foreach ($dados as $dado) {
    $tipo = $dado['TIPO_SENSOR'];
    $dadosPorTipo[$tipo][] = $dado['DADOS'];
}
$mediasPorTipo = [];
foreach ($dadosPorTipo as $tipo => $valores) {
    $mediasPorTipo[] = [
        'tipo' => $tipo,
        'media' => array_sum($valores) / count($valores)
    ];
}
$labelsTipo = array_column($mediasPorTipo, 'tipo');
$mediasTipo = array_column($mediasPorTipo, 'media');

?>
