<?php
require_once('../MODEL/model_usuario.php');
require_once('../MODEL/model_historico.php');
require_once('../MODEL/model_SENSORES.php'); // Garantindo que este include esteja aqui

$historico = new Historico();
$localFiltro = $_GET['filtro_local'] ?? 'Todos';
$id = $_GET['id'] ?? '';
$dataFiltro = $_GET['data'] ?? '';
$tipoFiltro = $_GET['tipo'] ?? '';

// LÓGICA CORRIGIDA: Aplicar filtros em sequência
if ($localFiltro !== 'Todos') {
    // Primeiro filtra por localização
    $dadosBase = $historico->BuscarPorLocalizacao($localFiltro);
} else {
    // Busca todos os dados
    $dadosBase = $historico->BuscarDadosGrafico();
}

$dadosGrafico = $dadosBase; 

// Aplica filtros adicionais (data, tipo) no PHP
if (!empty($dataFiltro)) {
    $dadosGrafico = array_filter($dadosGrafico, function($item) use ($dataFiltro) {
        return strpos($item['DATA_HORA_COLETA'], $dataFiltro) !== false;
    });
}

if (!empty($tipoFiltro)) {
    $dadosGrafico = array_filter($dadosGrafico, function($item) use ($tipoFiltro) {
        return $item['TIPO_SENSOR'] === $tipoFiltro;
    });
}


$localNome = ($localFiltro === "Todos") ? "Todas as Localizações" : $localFiltro;

// paginação
$itensPorPagina = 20;
$totalRegistros = count($dadosGrafico);
$totalPaginas = ceil($totalRegistros / $itensPorPagina);

$paginaAtual = isset($_GET['pagina']) && is_numeric($_GET['pagina']) ? intval($_GET['pagina']) : 1;
if ($paginaAtual < 1) $paginaAtual = 1;
if ($paginaAtual > $totalPaginas) $paginaAtual = $totalPaginas;

$inicio = ($paginaAtual - 1) * $itensPorPagina;
$dadosPaginados = array_slice($dadosGrafico, $inicio, $itensPorPagina);

session_start();

// Verificar se o logout foi solicitado
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    header("Location: ../VIEW/index.php");
    exit;
}

// Verificar usuário logado
if(isset($_GET["id"])){
    $id = $_GET["id"]; 

    $usuarioModel = new Usuario();
    $usuario = $usuarioModel->getUsuarioId($id);

    if (!empty($usuario) && isset($usuario[0])) {
        $usu = $usuario[0];
    } else {
        $usu = [ "FOTO_PERFIL" => "" ];
    }
}

$historicoModel = new Historico();
$sensorModel = new SensorModel(); 

$filtroAtivo = false;
$tipoFiltrado = $tipoFiltro ?: ($tipo ?? '');


if (!empty($_GET['data'])) {
    $data = $_GET['data'];
    $tipo = $_GET['tipo'] ?? null;

    // se houver filtro de localização, filtra primeiro por ela
    if ($localFiltro !== 'Todos') {
        $dadosBase = $historico->BuscarPorLocalizacao($localFiltro);
    } else {
        $dadosBase = $historico->BuscarDadosGrafico();
    }

    $dadosGrafico = array_filter($dadosBase, function($item) use ($data, $tipo) {
        $passouData = strpos($item['DATA_HORA_COLETA'], $data) !== false;

        if (!$tipo) return $passouData;

        return $passouData && $item['TIPO_SENSOR'] === $tipo;
    });

    $filtroAtivo = true;
} 



$statusSensoresLocal = [
    'Temperatura' => 'Ativo',
    'Umidade' => 'Ativo',
    'Gases' => 'Ativo',
    'Ultrassonico' => 'Ativo',
];

if ($localFiltro !== 'Todos') {
    
    $sensoresNoLocal = $sensorModel->getSensoresPorLocalizacao($localFiltro);
    
    $mapeamentoSensores = [
        'Temperatura' => ['Sensor de Temperatura'],
        'Umidade' => ['Sensor de Umidade'],
        'Gases' => ['Sensor de Gases'],
        'Ultrassonico' => ['Sensor Ultrassonico', 'Ultrassonico'], 
    ];

    foreach ($mapeamentoSensores as $chaveInterna => $nomesDB) {
        $statusEncontrado = 'Ativo';
        
        foreach ($sensoresNoLocal as $sensor) {
            if (in_array($sensor['TIPO_SENSOR'], $nomesDB)) {
                
                if ($sensor['STATUS_SENSOR'] === 'Inativo' || $sensor['STATUS_SENSOR'] === 'Manutenção') {
                    $statusEncontrado = $sensor['STATUS_SENSOR'];
                    break; 
                }
            }
        }
        $statusSensoresLocal[$chaveInterna] = $statusEncontrado;
    }
}
$dadosTemperatura = [];
$dadosUmidade = [];
$dadosGases = [];
$dadosUltrassonico = [];

$estatisticas = [
    'temperatura' => ['max' => null, 'min' => null, 'media' => null],
    'umidade' => ['max' => null, 'min' => null, 'media' => null],
    'gases' => ['max' => null, 'min' => null, 'media' => null],
    'ultrassonico' => ['max' => null, 'min' => null, 'media' => null]
];

$contagemTipo = [];

foreach ($dadosGrafico as $row) {
    $sensor = $row['TIPO_SENSOR'];
    $valor = floatval($row['DADOS']);

    if (stripos($sensor, 'temperatura') !== false) {
        $dadosTemperatura[] = $valor;
        $contagemTipo['Temperatura'] = ($contagemTipo['Temperatura'] ?? 0) + 1;
        
        // Calcular estatísticas
        if ($estatisticas['temperatura']['max'] === null || $valor > $estatisticas['temperatura']['max']) {
            $estatisticas['temperatura']['max'] = $valor;
        }
        if ($estatisticas['temperatura']['min'] === null || $valor < $estatisticas['temperatura']['min']) {
            $estatisticas['temperatura']['min'] = $valor;
        }
    }
    elseif (stripos($sensor, 'umidade') !== false) {
        $dadosUmidade[] = $valor;
        $contagemTipo['Umidade'] = ($contagemTipo['Umidade'] ?? 0) + 1;
        
        // Calcular estatísticas
        if ($estatisticas['umidade']['max'] === null || $valor > $estatisticas['umidade']['max']) {
            $estatisticas['umidade']['max'] = $valor;
        }
        if ($estatisticas['umidade']['min'] === null || $valor < $estatisticas['umidade']['min']) {
            $estatisticas['umidade']['min'] = $valor;
        }
    }
    elseif (stripos($sensor, 'gases') !== false) {
        $dadosGases[] = $valor;
        $contagemTipo['Gases'] = ($contagemTipo['Gases'] ?? 0) + 1;
        
        // Calcular estatísticas
        if ($estatisticas['gases']['max'] === null || $valor > $estatisticas['gases']['max']) {
            $estatisticas['gases']['max'] = $valor;
        }
        if ($estatisticas['gases']['min'] === null || $valor < $estatisticas['gases']['min']) {
            $estatisticas['gases']['min'] = $valor;
        }
    }
    elseif (stripos($sensor, 'ultrassonico') !== false || stripos($sensor, 'Ultrassonico') !== false) {
        $dadosUltrassonico[] = $valor;
        $contagemTipo['Ultrassônico'] = ($contagemTipo['Ultrassônico'] ?? 0) + 1;
        
        // Calcular estatísticas
        if ($estatisticas['ultrassonico']['max'] === null || $valor > $estatisticas['ultrassonico']['max']) {
            $estatisticas['ultrassonico']['max'] = $valor;
        }
        if ($estatisticas['ultrassonico']['min'] === null || $valor < $estatisticas['ultrassonico']['min']) {
            $estatisticas['ultrassonico']['min'] = $valor;
        }
    }
}

// Calcular médias
if (!empty($dadosTemperatura)) {
    $estatisticas['temperatura']['media'] = array_sum($dadosTemperatura) / count($dadosTemperatura);
}
if (!empty($dadosUmidade)) {
    $estatisticas['umidade']['media'] = array_sum($dadosUmidade) / count($dadosUmidade);
}
if (!empty($dadosGases)) {
    $estatisticas['gases']['media'] = array_sum($dadosGases) / count($dadosGases);
}
if (!empty($dadosUltrassonico)) {
    $estatisticas['ultrassonico']['media'] = array_sum($dadosUltrassonico) / count($dadosUltrassonico);
}

$labelsPizza = array_keys($contagemTipo);
$dadosPizza = array_values($contagemTipo);


$sensorFiltrado = '';
if ($filtroAtivo) {
    if (stripos($tipoFiltrado, 'temperatura') !== false) $sensorFiltrado = 'temperatura';
    elseif (stripos($tipoFiltrado, 'umidade') !== false) $sensorFiltrado = 'umidade';
    elseif (stripos($tipoFiltrado, 'gases') !== false) $sensorFiltrado = 'gases';
    elseif (stripos($tipoFiltrado, 'ultrassonico') !== false || stripos($tipoFiltrado, 'Ultrassonico') !== false) $sensorFiltrado = 'ultrassonico';
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Safe Zone | Gráficos</title>
    <link rel="stylesheet" href="../STYLES/graficos.css">
    <link rel="shortcut icon" href="../IMAGES/FAVICON.png" type="image/x-icon">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>

    </style>
</head>
<body>
        <div class="header">
        <header>
            <a href="<?php echo './index.php?id=' . $id; ?>"><img class="img" src="../IMAGES/LOGO2.png" alt=""></a>
            <div class="campos" id="menuLinks">
                <ul>
                    <li><a href="<?php echo './index.php?id=' . $id; ?>">Início</a></li>
                    <li><a href="<?php echo './galeria.php?id=' . $id; ?>">Galeria</a></li>
                    <?php if(!empty($id)): ?>
                    <li><a href="<?php echo './mapas.php?id=' . $id; ?>">Mapa</a></li>
                    <li><a href="<?php echo './graficos.php?id=' . $id; ?>">Gráficos</a></li>
                    <li><a href="<?php echo './contato.php?id=' . $id; ?>">Contato</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo './sobre-nos.php?id=' . $id; ?>">Sobre Nós</a></li>
                </ul>
            </div>
            <?php if(!empty($id)): ?>
            <div class="perfil">
                <a href="<?php echo './config.php?id=' . $id; ?>">
                    <img id="preview-img-profile" src="../IMAGES/PERFIL/<?php echo !empty($usu["FOTO_PERFIL"]) ? $usu["FOTO_PERFIL"] : 'PERFIL_SEM_FOTO.png'; ?>">
                </a>
            </div>
            <?php endif; ?>
            <a href="?logout=1" class="logout-btn" title="Sair">
                <i class='bx bx-log-out'></i>
            </a>
            <div class="menu-mobile" onclick="toggleMenu()">
                <i class="fa-solid fa-bars"></i>
            </div>

        </header>
        
    </div>

    <div class="verde"></div>

    <div class="main">
        <main>
            <section id="graficos"> 
                <div class="text">
                    <h2>Gráficos</h2>
                    <p>Visualização de dados coletados pelos sensores em diferentes dimensões temporais e geográficas. Os gráficos abaixo apresentam análises comparativas e tendências dos parâmetros monitorados</p>
                </div>
                
                <div>
                    <form method="GET">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                            
                            <div class="filtro-local-container">
                            <label for="filtro_local" class="filtro-local-label">Local:</label>
                            <select id="filtro_local" name="filtro_local" onchange="this.form.submit()">
                                <option value="Todos" <?php echo $localFiltro=="Todos" ? "selected":""; ?>>Todas as Localizações</option>

                                <!-- Tambaú -->
                                <option value="Tambaú - Escola Sesi" <?php echo $localFiltro=="Tambaú - Escola Sesi" ? "selected":""; ?>>Tambaú - Escola Sesi</option>
                                <option value="Tambaú - Prefeitura" <?php echo $localFiltro=="Tambaú - Prefeitura" ? "selected":""; ?>>Tambaú - Prefeitura</option>
                                <option value="Tambaú - Serra" <?php echo $localFiltro=="Tambaú - Serra" ? "selected":""; ?>>Tambaú - Serra</option>
                                <option value="Tambaú - São Lourenço" <?php echo $localFiltro=="Tambaú - São Lourenço" ? "selected":""; ?>>Tambaú - São Lourenço</option>

                                <!-- Palmeiras -->
                                <option value="Palmeiras - Prefeitura" <?php echo $localFiltro=="Palmeiras - Prefeitura" ? "selected":""; ?>>Palmeiras - Prefeitura</option>
                                <option value="Palmeiras - Jardim Santa Clara" <?php echo $localFiltro=="Palmeiras - Jardim Santa Clara" ? "selected":""; ?>>Palmeiras - Jardim Santa Clara</option>
                                <option value="Palmeiras - Vila dos Oficias" <?php echo $localFiltro=="Palmeiras - Vila dos Oficias" ? "selected":""; ?>>Palmeiras - Vila dos Oficias</option>
                                <option value="Palmeiras - Santo Antônio" <?php echo $localFiltro=="Palmeiras - Santo Antônio" ? "selected":""; ?>>Palmeiras - Santo Antônio</option>
                            </select>
                            <a href="?id=<?php echo $id; ?>" class="btn-limpar-local">Limpar Localização</a>
                            </div>
                    </form>
                </div>
                <?php
                // Verificar se há dados para exibir
                if(empty($dadosGrafico)){
                    echo "<div style='text-align: center; padding: 20px; color: #666;'>Sem dados para exibir!</div>";
                } else {
                ?>
                <!-- Filtro para gráficos e tabela -->
                <div class="filtro-container">
                    <form method="GET" action="">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <input type="hidden" name="filtro_local" value="<?php echo $localFiltro; ?>">
                        
                        <label for="data">Data:</label>
                        <input type="date" name="data" id="data" value="<?php echo isset($_GET['data']) ? $_GET['data'] : ''; ?>" required>
                        
                        <label for="tipo">Sensor:</label>
                        <select name="tipo" id="tipo">
                            <option value="">Todos</option>
                            <option value="Sensor de Temperatura" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] == 'Sensor de Temperatura') ? 'selected' : ''; ?>>Temperatura</option>
                            <option value="Sensor de Umidade" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] == 'Sensor de Umidade') ? 'selected' : ''; ?>>Umidade</option>
                            <option value="Sensor de Gases" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] == 'Sensor de Gases') ? 'selected' : ''; ?>>Gases</option>
                            <option value="Sensor Ultrassonico" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] == 'Sensor Ultrassonico') ? 'selected' : ''; ?>>Ultrassônico</option>
                        </select>

                        <button type="submit">Filtrar</button>
                        <?php if ($filtroAtivo): ?>
                            <a href="?id=<?php echo $id; ?>&filtro_local=<?php echo $localFiltro; ?>" class="btn-limpar">Limpar Filtro Data/Sensor</a>
                        <?php endif; ?>
                    </form>
                </div>

                <?php if ($filtroAtivo): ?>
                <!-- Seção para gráficos filtrados -->
                <div class="grafico-filtrado">
                    <h3 style="text-align: center; margin: 30px 0; color: #006600;">
                        Visualização Detalhada: <?php echo ucfirst($sensorFiltrado); ?>
                    </h3>

                    <!-- Cards de Estatísticas -->
                    <?php if (!empty($sensorFiltrado) && isset($estatisticas[$sensorFiltrado])): ?>
    <div class="estatisticas-container">
        <div class="card-estatistica">
            <h3>Máxima</h3>
            <div class="valor">
                <?php echo number_format($estatisticas[$sensorFiltrado]['max'], 2); ?>
            </div>
            <div class="label">Valor mais alto do dia</div>
        </div>
        <div class="card-estatistica">
            <h3>Mínima</h3>
            <div class="valor">
                <?php echo number_format($estatisticas[$sensorFiltrado]['min'], 2); ?>
            </div>
            <div class="label">Valor mais baixo do dia</div>
        </div>
        <div class="card-estatistica">
            <h3>Média</h3>
            <div class="valor">
                <?php echo number_format($estatisticas[$sensorFiltrado]['media'], 2); ?>
            </div>
            <div class="label">Média dos valores</div>
        </div>
    </div>
<?php else: ?>
    <p style="color:red;text-align:center;margin-top:20px;">
        Nenhum sensor selecionado ou sensor não possui dados hoje.
    </p>
<?php endif; ?>

                    <div class="grafico-boxes">
                        <!-- Gráfico de Linha -->
                        <div class="grafico-wrapper">
                            <div class="grafico-card">
                                <div class="grafico-title"><?php echo ucfirst($sensorFiltrado); ?> - Tendência Temporal</div>
                                <div class="grafico-container">
                                    <canvas id="graficoLinhaFiltrado"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Gráfico de Barras -->
                        <div class="grafico-wrapper">
                            <div class="grafico-card">
                                <div class="grafico-title"><?php echo ucfirst($sensorFiltrado); ?> - Distribuição</div>
                                <div class="grafico-container">
                                    <canvas id="graficoBarrasFiltrado"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Gráficos normais (só mostrados quando não há filtro específico) -->
                <div class="graficos-normais <?php echo $filtroAtivo ? 'hidden' : ''; ?>">
    <div class="grafico-boxes">
        
        <div class="grafico-wrapper">
            <?php 
            $classeCardTempUmid = '';
            $mensagemStatusTempUmid = '';
            
            if ($localFiltro !== 'Todos') {
                $statusTemp = $statusSensoresLocal['Temperatura'];
                $statusUmid = $statusSensoresLocal['Umidade'];
                
                if ($statusTemp !== 'Ativo' || $statusUmid !== 'Ativo') {
                    $classeCardTempUmid = ' card-indisponivel';
                    
                    if ($statusTemp === 'Inativo' || $statusUmid === 'Inativo') {
                         $mensagemStatusTempUmid = "Sensor(es) Inativo(s). Sem dados por indisponibilidade técnica.";
                    } else {
                         $mensagemStatusTempUmid = "Sensor(es) em Manutenção. Sem dados por intervenção técnica.";
                    }
                }
            }
            ?>
            <div class="grafico-card<?php echo $classeCardTempUmid; ?>">
                <div class="grafico-title">Temperatura e Umidade - <?php echo $localNome; ?></div>
                <div class="grafico-container">
                    <?php if ($classeCardTempUmid !== ''): ?>
                        <div class="alerta-sensor-inativo">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p><?php echo $mensagemStatusTempUmid; ?></p>
                        </div>
                    <?php else: ?>
                        <canvas id="graficoTempUmid"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="grafico-wrapper">
            <?php 
            $classeCardGases = '';
            $mensagemStatusGases = '';
            if ($localFiltro !== 'Todos' && $statusSensoresLocal['Gases'] !== 'Ativo') {
                $statusGases = $statusSensoresLocal['Gases'];
                $classeCardGases = ' card-indisponivel';
                $mensagemStatusGases = $statusGases === 'Inativo' 
                                       ? "Sensor Inativo. Sem dados por indisponibilidade técnica." 
                                       : "Sensor em Manutenção. Sem dados por intervenção técnica.";
            }
            ?>
            <div class="grafico-card<?php echo $classeCardGases; ?>">
                <div class="grafico-title">Gases - <?php echo $localNome; ?></div>
                <div class="grafico-container">
                    <?php if ($classeCardGases !== ''): ?>
                        <div class="alerta-sensor-inativo">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p><?php echo $mensagemStatusGases; ?></p>
                        </div>
                    <?php else: ?>
                        <canvas id="graficoGases"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="grafico-boxes">
        <div class="grafico-wrapper">
            <?php 
            $classeCardUltrassonico = '';
            $mensagemStatusUltrassonico = '';
            if ($localFiltro !== 'Todos' && $statusSensoresLocal['Ultrassonico'] !== 'Ativo') {
                $statusUltrassonico = $statusSensoresLocal['Ultrassonico'];
                $classeCardUltrassonico = ' card-indisponivel';
                $mensagemStatusUltrassonico = $statusUltrassonico === 'Inativo' 
                                              ? "Sensor Inativo. Sem dados por indisponibilidade técnica." 
                                              : "Sensor em Manutenção. Sem dados por intervenção técnica.";
            }
            ?>
            <div class="grafico-card<?php echo $classeCardUltrassonico; ?>">
                <div class="grafico-title">Ultrassônico - <?php echo $localNome; ?></div>
                <div class="grafico-container">
                    <?php if ($classeCardUltrassonico !== ''): ?>
                        <div class="alerta-sensor-inativo">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p><?php echo $mensagemStatusUltrassonico; ?></p>
                        </div>
                    <?php else: ?>
                        <canvas id="graficoUltrassonico"></canvas>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="grafico-wrapper">
            <div class="grafico-card">
                <div class="grafico-title">Proporção de Leituras</div>
                <div class="grafico-container">
                    <canvas id="graficoPizza"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

                <div class="tabela-container">
                    <table border="1" cellpadding="8" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Sensor</th>
                                <th>Valor</th>
                                <th>Unidade</th>
                                <th>Data/Hora</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($dadosPaginados)): ?>
                                <?php foreach ($dadosPaginados as $row): ?>
                                    <tr>
                                        <td><?php echo $row['TIPO_SENSOR']; ?></td>
                                        <td><?php echo $row['DADOS']; ?></td>
                                        <td><?php echo $row['UNIDADE_MEDIDA']; ?></td>
                                        <td><?php echo date("d/m/Y H:i", strtotime($row['DATA_HORA_COLETA'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4">Nenhum registro encontrado.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>

                    <!-- Paginação -->
                    <?php if ($totalPaginas > 1): ?>
                        <div class="paginacao" style="text-align:center; margin-top:20px;">
                            <?php if ($paginaAtual > 1): ?>
                                <a href="?id=<?php echo $id; ?>&data=<?php echo urlencode($dataFiltro); ?>&tipo=<?php echo urlencode($tipoFiltro); ?>&pagina=<?php echo $paginaAtual - 1; ?>" class="btn-pag">Anterior</a>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                                <a href="?id=<?php echo $id; ?>&data=<?php echo urlencode($dataFiltro); ?>&tipo=<?php echo urlencode($tipoFiltro); ?>&pagina=<?php echo $i; ?>" 
                                class="btn-pag <?php echo $i == $paginaAtual ? 'ativo' : ''; ?>">
                                <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php if ($paginaAtual < $totalPaginas): ?>
                                <a href="?id=<?php echo $id; ?>&data=<?php echo urlencode($dataFiltro); ?>&tipo=<?php echo urlencode($tipoFiltro); ?>&pagina=<?php echo $paginaAtual + 1; ?>" class="btn-pag">Próxima</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
                <?php
                    }
                ?>



                <!-- Botão Editar como Admin -->
                <?php if(isset($usu['TIPO_USUARIO']) && $usu['TIPO_USUARIO'] === 'ADMINISTRADOR'): ?>
                    <div class="admin-button-container">
                       <a href="./gerenciar_sensores.php<?php echo !empty($id) ? '?id='.$id : ''; ?>" class="admin-button" style="text-decoration: none;">
                            <i class="fas fa-cog"></i> Editar como Admin
                        </a>
                    </div>
                <?php endif; ?>
            </section>
        </main>
    </div>

       <div class="conteudo2">
        <div class="bloco">
            <i class='bx bxs-phone'></i>
            <div class="texto">
                <p style="font-weight: bold;">Ligue para nós</p>
                <p>+55 (19) 9935-7890</p>
            </div>
        </div>
        <div class="bloco">
            <i class='bx bxs-envelope' ></i>
            <div class="texto">
                <p style="font-weight: bold;">Envie um e-mail</p>
                <p>safezone@gmail.com</p>
            </div>
        </div>
        <div class="bloco">
            <i class='bx bxs-time' ></i>
            <div class="texto">
                <p style="font-weight: bold;">Seg.-Sáb.:</p>
                <p>08h00 – 18h00</p>
            </div>
        </div>
        <div class="bloco">
            <i class="fa-solid fa-location-dot"></i>
            <div class="texto">
                <p style="font-weight: bold;">Venha nos visitar:</p>
                <p>Rua Safe Zone, 123 – <br> Centro, Tambaú</p>
            </div>
        </div>
    </div>

    <div class="footer">
        <footer>
            <div class="sobre">
                <div class="texts">
                    <h4>Sobre Nós</h4>
                </div>
                <p style="text-align: justify;">O Safe Zone é um espaço seguro dedicado a fornecer suporte, informações e recursos para aqueles que precisam de ajuda. Nossa missão é criar uma comunidade acolhedora e acessível para todos.</p>
            </div>
            <div class="links">
                <h4>Links Úteis</h4>
                <ul>
                    <li><a href="./sobre-nos.php<?php echo !empty($id) ? '?id='.$id : ''; ?>">Quem Somos</a></li>
                    <?php if(!empty($id)): ?>
                    <li><a href="./contato.php<?php echo !empty($id) ? '?id='.$id : ''; ?>">Contato</a></li>
                    <?php endif; ?>
                    <li><a href="#" id="abrirPolitica">Políticas de Privacidade</a></li>
                </ul>

                <!-- Modal escondido -->
                <div id="modalPolitica" class="modal">
                <div class="modal-content">
                    <span class="fechar">&times;</span>
                    <h2>Políticas de Privacidade – SafeZone</h2>

                    <p>A SafeZone respeita a sua privacidade e está comprometida em proteger os dados pessoais dos usuários e as informações coletadas pelos nossos sistemas de monitoramento.</p>

                    <h3>1. Coleta de Dados</h3>
                    <ul>
                    <li>Utilizamos sensores de <strong>gases, temperatura e umidade</strong> para monitorar o ambiente em tempo real, especialmente em áreas de risco de queimadas e desastres ambientais.</li>
                    <li>Em nosso site e aplicativo, poderemos coletar informações fornecidas voluntariamente pelos usuários, como nome, e-mail e dados de contato para cadastro, acesso à plataforma e envio de notificações.</li>
                    </ul>

                    <h3>2. Uso das Informações</h3>
                    <p>As informações coletadas são utilizadas para:</p>
                    <ul>
                    <li>Monitorar e analisar riscos ambientais em tempo real.</li>
                    <li>Emitir alertas de segurança e recomendações preventivas.</li>
                    <li>Melhorar os serviços oferecidos pela SafeZone.</li>
                    <li>Entrar em contato com usuários, quando necessário, em situações de risco ou suporte técnico.</li>
                    </ul>

                    <h3>3. Compartilhamento de Dados</h3>
                    <ul>
                    <li>Não compartilhamos dados pessoais dos usuários com terceiros sem consentimento, exceto em casos legais ou para proteção da segurança pública.</li>
                    <li>Informações ambientais coletadas pelos sensores poderão ser utilizadas em relatórios, estudos e parcerias com órgãos de monitoramento e defesa civil.</li>
                    </ul>

                    <h3>4. Segurança das Informações</h3>
                    <ul>
                    <li>Adotamos medidas técnicas e organizacionais adequadas para proteger seus dados contra acessos não autorizados, uso indevido ou divulgação indevida.</li>
                    <li>O acesso às informações é restrito apenas a pessoas autorizadas e treinadas.</li>
                    </ul>

                    <h3>5. Direitos dos Usuários</h3>
                    <ul>
                    <li>Solicitar acesso, correção ou exclusão de seus dados pessoais.</li>
                    <li>Revogar o consentimento para uso das informações, quando aplicável.</li>
                    <li>Obter informações claras sobre o tratamento dos dados coletados.</li>
                    </ul>

                    <h3>6. Alterações nesta Política</h3>
                    <p>A SafeZone poderá atualizar esta Política de Privacidade periodicamente para refletir melhorias ou mudanças em nossos serviços. Recomendamos que os usuários revisem este documento regularmente.</p>

                    <h3>7. Contato</h3>
                    <p>Em caso de dúvidas sobre esta Política de Privacidade ou sobre o uso de seus dados, entre em contato com nossa equipe através do canal oficial de suporte.</p>

                </div>
                </div>
            </div>
            <div class="not-empresa">
                <h4>Notícias da Empresa</h4>
                <h4>Grande Evento</h4>
                <p>Terça , 24 junho 2025</p>
                <p>Após muito esforço, chegou a hora! Convidamos você para a apresentação do nosso Trabalho de Conclusão de Curso (TCC).</p>
            </div>
        </footer>
    </div>
    <div class="rodape">
        <p>Safe Zone &copy; 2025 - Todos os Direitos Reservados</p>
    </div>

    <button class="back-to-top" title="Voltar ao topo">
        <i class="fas fa-arrow-up"></i>
    </button>

<script>
    const modal = document.getElementById("modalPolitica");
    const btn = document.getElementById("abrirPolitica");
    const fechar = document.querySelector(".fechar");

    // abre o modal
    btn.onclick = function(e) {
        e.preventDefault(); // evita reload da página
        modal.style.display = "block";
    }

    // fecha ao clicar no X
    fechar.onclick = function() {
        modal.style.display = "none";
    }

    // fecha ao clicar fora da janela
    window.onclick = function(e) {
        if (e.target === modal) {
        modal.style.display = "none";
        }
    }
    </script>
    
    <script src="../SCRIPTS/jquery.min.js"></script>
    <script src="../SCRIPTS/jquery.backtotop.js"></script>
    <script src="../SCRIPTS/jquery.mobilemenu.js"></script>
    <script src="../SCRIPTS/graficos.js"></script>

    <script>
        const dadosTemperatura = <?php echo json_encode($dadosTemperatura); ?>;
        const dadosUmidade = <?php echo json_encode($dadosUmidade); ?>;
        const dadosGases = <?php echo json_encode($dadosGases); ?>;
        const dadosUltrassonico = <?php echo json_encode($dadosUltrassonico); ?>;
        const labelsPizza = <?php echo json_encode($labelsPizza); ?>;
        const dadosPizza = <?php echo json_encode($dadosPizza); ?>;
        const filtroAtivo = <?php echo $filtroAtivo ? 'true' : 'false'; ?>;
        const sensorFiltrado = '<?php echo $sensorFiltrado; ?>';

        // Dados para o sensor filtrado
        let dadosFiltrados = [];
        switch(sensorFiltrado) {
            case 'temperatura': dadosFiltrados = dadosTemperatura; break;
            case 'umidade': dadosFiltrados = dadosUmidade; break;
            case 'gases': dadosFiltrados = dadosGases; break;
            case 'ultrassonico': dadosFiltrados = dadosUltrassonico; break;
        }

        // Gráficos normais (só criados se não houver filtro)
        if (!filtroAtivo) {
            // Temperatura + Umidade
            const maxLen = Math.max(dadosTemperatura.length, dadosUmidade.length);
            new Chart(document.getElementById('graficoTempUmid'), {
                type: 'line',
                data: {
                    labels: Array.from({ length: maxLen }, (_, i) => i + 1),
                    datasets: [
                        { 
                            label: 'Temperatura', 
                            data: dadosTemperatura, 
                            borderColor: 'red', 
                            backgroundColor: 'rgba(255,0,0,0.2)', 
                            fill: true, 
                            tension: 0.3 
                        },
                        { 
                            label: 'Umidade', 
                            data: dadosUmidade, 
                            borderColor: 'blue', 
                            backgroundColor: 'rgba(0,0,255,0.2)', 
                            fill: true, 
                            tension: 0.3 
                        }
                    ]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Gases
            new Chart(document.getElementById('graficoGases'), {
                type: 'bar',
                data: {
                    labels: dadosGases.map((_, i) => i + 1),
                    datasets: [{ label: 'Gases', data: dadosGases, backgroundColor: 'green' }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Ultrassônico
            new Chart(document.getElementById('graficoUltrassonico'), {
                type: 'line',
                data: {
                    labels: dadosUltrassonico.map((_, i) => i + 1),
                    datasets: [{ label: 'Distância', data: dadosUltrassonico, borderColor: 'purple', backgroundColor: 'rgba(128,0,128,0.2)', fill: true }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            // Pizza
            new Chart(document.getElementById('graficoPizza'), {
                type: 'pie',
                data: {
                    labels: labelsPizza,
                    datasets: [{
                        data: dadosPizza,
                        backgroundColor: ['#FF6384', '#36A2EB', '#FFCE56', '#4CAF50']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        }

        // Gráficos filtrados (só criados se houver filtro)
        if (filtroAtivo && dadosFiltrados.length > 0) {
            // Gráfico de Linha
            new Chart(document.getElementById('graficoLinhaFiltrado'), {
                type: 'line',
                data: {
                    labels: dadosFiltrados.map((_, i) => `Leitura ${i + 1}`),
                    datasets: [{ 
                        label: sensorFiltrado.charAt(0).toUpperCase() + sensorFiltrado.slice(1),
                        data: dadosFiltrados, 
                        borderColor: '#006600',
                        backgroundColor: 'rgba(0,102,0,0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Valor'
                            }
                        }
                    }
                }
            });

            // Gráfico de Barras
            new Chart(document.getElementById('graficoBarrasFiltrado'), {
                type: 'bar',
                data: {
                    labels: dadosFiltrados.map((_, i) => `Leitura ${i + 1}`),
                    datasets: [{ 
                        label: sensorFiltrado.charAt(0).toUpperCase() + sensorFiltrado.slice(1),
                        data: dadosFiltrados, 
                        backgroundColor: '#006600',
                        borderColor: '#004d00',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Valor'
                            }
                        }
                    }
                }
            });
        }
    </script>
    <script>
function toggleMenu() {
    const menu = document.getElementById("menuLinks");
    menu.classList.toggle("ativo");
}
</script>

</body>
</html>