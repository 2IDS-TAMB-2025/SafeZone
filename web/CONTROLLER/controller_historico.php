<?php
require_once('../MODEL/model_historico.php');

$acao = $_GET['acao'] ?? 'listar';
$historicoModel = new Historico();

switch ($acao) {
    // POST
    case 'inserir':
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $data = json_decode(file_get_contents("php://input"), true);

        $id_sensor   = $data['id_sensor'] ?? null;
        $dados       = $data['dados'] ?? null;
        $unidade     = $data['unidade_medida'] ?? null;

        $result = [];

        if ($id_sensor !== null && $dados !== null && $unidade !== null) {
            $result[] = $historicoModel->InserirHistorico(
                $id_sensor,
                $dados,
                $unidade
            );
        }


        echo json_encode([
            "status" => "sucesso",
            "mensagem" => $result
        ]);
    }
    break;


        

    case 'atualizar':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);

            $id           = $data['id'] ?? null;
            $fk_id_sensor = $data['fk_id_sensor'] ?? null;
            $valor        = $data['valor'] ?? null;
            $unidade      = $data['unidade'] ?? null;
            $tipo_medicao = $data['tipo_medicao'] ?? null;

            $historicoModel->AtualizarHistorico($id, $fk_id_sensor, $valor, $unidade, $tipo_medicao);
            echo json_encode(["status" => "sucesso", "mensagem" => "Histórico atualizado com sucesso"]);
        }
        break;

        

    case 'deletar':
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = json_decode(file_get_contents("php://input"), true);

            $id = $data['id'] ?? null;

            $historicoModel->DeletarHistorico($id);
            echo json_encode(["status" => "sucesso", "mensagem" => "Histórico deletado com sucesso"]);
        }
        break;

    // GET
    case 'listar':
default:
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        
        $sensoresAtivos = $historicoModel->BuscarSensoresAtivos();
        
        
        if (empty($sensoresAtivos)) {
            echo json_encode([
                "status" => "sucesso",
                "dados" => [],
                "sensores" => [],
                "graficos" => []
            ]);
            break;
        }
        
       
        $idsSensoresAtivos = array_column($sensoresAtivos, 'ID_SENSOR');
        
       
        $dados = $historicoModel->BuscarDadosGrafico($idsSensoresAtivos);
        
        
        $labelsTipo = [];
        $mediasTipo = [];
        $dadosSensor = [];
        $labelsUnidade = [];
        $mediasUnidade = [];

        foreach ($dados as $row) {
            if (isset($row['TIPO_SENSOR'])) {
                
                $labelsTipo[]    = $row['TIPO_SENSOR'];
                $mediasTipo[]    = $row['DADOS'];
                $dadosSensor[]   = $row['DADOS'];
                $labelsUnidade[] = $row['UNIDADE_MEDIDA'] ?? '';
                $mediasUnidade[] = $row['DADOS'];
            }
        }

        echo json_encode([
            "status" => "sucesso",
            "dados" => $dados,
            "sensores" => $sensoresAtivos,
            "graficos" => [
                "labelsTipo"    => $labelsTipo,
                "mediasTipo"    => $mediasTipo,
                "dadosSensor"   => $dadosSensor,
                "labelsUnidade" => $labelsUnidade,
                "mediasUnidade" => $mediasUnidade
            ]
        ]);
    }
    break;

        case 'filtrar':
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $data = $_GET['data'] ?? null;
        $tipo = $_GET['tipo'] ?? null;

        if ($data) {
            $dados = $historicoModel->BuscarPorDataETipo($data, $tipo ?: null);
        } else {
            $dados = $historicoModel->BuscarDadosGrafico();
        }

        echo json_encode([
            "status" => "sucesso",
            "dados"  => $dados
        ]);
    }
    break;

    case 'filtrar_local':
    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $local = $_GET['local'] ?? null;
        
        $localNome = ($local === "Todos" || $local === null) ? "Todas as Localizações" : $local;
        
        if ($local && $local !== 'Todos') {
            $dados = $historicoModel->BuscarPorLocalizacao($local);
        } else {
            $dados = $historicoModel->BuscarDadosGrafico();
        }

        echo json_encode([
            "status" => "sucesso",
            "dados"  => $dados,
            "local_nome" => $localNome
        ]);
    }
    break;


}
?>
