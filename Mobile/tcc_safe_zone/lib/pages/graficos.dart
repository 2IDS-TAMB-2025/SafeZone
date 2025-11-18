import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import 'package:tcc_safe_zone/pages/dados_cadastrados.dart';
import 'package:tcc_safe_zone/pages/criar.conta.dart';
import 'package:tcc_safe_zone/pages/galeria.dart';
import 'package:tcc_safe_zone/pages/mapa.dart';
import 'package:tcc_safe_zone/pages/principal.dart';
import 'package:tcc_safe_zone/controllers/auth_controller.dart';
import 'package:tcc_safe_zone/controllers/graficos_controller.dart';
import 'package:tcc_safe_zone/models/historico_model.dart';
import 'package:tcc_safe_zone/models/estatisticas_model.dart';
import 'package:tcc_safe_zone/services/photo_service.dart';
import 'package:tcc_safe_zone/widgets/profile_avatar_widget.dart';

class GraficosPage extends StatefulWidget {
  final String usuario;
  const GraficosPage({required this.usuario, Key? key}) : super(key: key);

  @override
  State<GraficosPage> createState() => _GraficosPageState();
}

class _GraficosPageState extends State<GraficosPage> {
  List<Historico> _dadosCompletos = [];
  List<Historico> _dadosFiltrados = [];
  
  String _localFiltro = 'Todos';
  String _dataFiltro = '';
  String _tipoFiltro = '';
  
  bool _carregando = true;
  bool _filtroAtivo = false;
  
  List<String> _localizacoes = [];
  StatusSensoresLocal _statusSensores = StatusSensoresLocal.padrao();
  
  Map<String, EstatisticasSensor> _estatisticas = {};
  Map<String, List<double>> _dadosPorTipo = {
    'temperatura': [],
    'umidade': [],
    'gases': [],
    'ultrassonico': [],
  };

  // Controlador para o campo de data
  final TextEditingController _dataController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _carregarDadosIniciais();
  }

  @override
  void dispose() {
    _dataController.dispose();
    super.dispose();
  }

  Future<void> _carregarDadosIniciais() async {
    await _carregarLocalizacoes();
    await _aplicarFiltros();
  }

  Future<void> _carregarLocalizacoes() async {
    try {
      final localizacoes = await GraficosController.buscarLocalizacoes();
      setState(() {
        _localizacoes = localizacoes;
      });
    } catch (e) {
      print('Erro ao carregar localizações: $e');
      setState(() {
        _localizacoes = [
          'Todos',
          'Tambaú - Escola Sesi',
          'Tambaú - Prefeitura',
          'Tambaú - Serra',
          'Tambaú - São Lourenço',
          'Palmeiras - Prefeitura',
          'Palmeiras - Jardim Santa Clara',
          'Palmeiras - Vila dos Oficias',
          'Palmeiras - Santo Antônio',
        ];
      });
    }
  }

  // Função para converter data brasileira para formato ISO
  String _converterDataParaISO(String dataBrasileira) {
    try {
      if (dataBrasileira.isEmpty) return '';
      
      // Remove qualquer caractere não numérico exceto a barra
      final cleanedData = dataBrasileira.replaceAll(RegExp(r'[^0-9/]'), '');
      
      // Verifica se tem o formato DD/MM/YYYY
      if (cleanedData.length == 10 && cleanedData.contains('/')) {
        final partes = cleanedData.split('/');
        if (partes.length == 3) {
          final dia = partes[0].padLeft(2, '0');
          final mes = partes[1].padLeft(2, '0');
          final ano = partes[2];
          
          // Retorna no formato YYYY-MM-DD
          return '$ano-$mes-$dia';
        }
      }
      
      return dataBrasileira; // Retorna original se não conseguir converter
    } catch (e) {
      print('Erro ao converter data: $e');
      return dataBrasileira;
    }
  }

  // Função para validar e formatar a data enquanto digita
  String _formatarDataDigitada(String input) {
    // Remove tudo que não é número
    String cleaned = input.replaceAll(RegExp(r'[^0-9]'), '');
    
    if (cleaned.length > 8) {
      cleaned = cleaned.substring(0, 8);
    }
    
    if (cleaned.length <= 2) {
      return cleaned;
    } else if (cleaned.length <= 4) {
      return '${cleaned.substring(0, 2)}/${cleaned.substring(2)}';
    } else {
      return '${cleaned.substring(0, 2)}/${cleaned.substring(2, 4)}/${cleaned.substring(4)}';
    }
  }

  Future<void> _aplicarFiltros() async {
    setState(() {
      _carregando = true;
    });

    try {
      // Converte a data do formato brasileiro para ISO
      final dataISO = _converterDataParaISO(_dataFiltro);
      
      print('🔄 Aplicando filtros... Local: $_localFiltro, Data: $_dataFiltro (ISO: $dataISO), Tipo: $_tipoFiltro');
      
      final dados = await GraficosController.buscarDadosGrafico(
        localizacao: _localFiltro != 'Todos' ? _localFiltro : null,
        data: dataISO.isNotEmpty ? dataISO : null,
        tipoSensor: _tipoFiltro.isNotEmpty ? _tipoFiltro : null,
      );

      print('📊 Dados recebidos: ${dados.length} registros');

      if (_localFiltro != 'Todos') {
        _statusSensores = await GraficosController.verificarStatusSensores(_localFiltro);
      } else {
        _statusSensores = StatusSensoresLocal.padrao();
      }

      _processarDados(dados);
      
      setState(() {
        _dadosCompletos = dados;
        _dadosFiltrados = dados;
        _filtroAtivo = _dataFiltro.isNotEmpty || _tipoFiltro.isNotEmpty;
        _carregando = false;
      });

    } catch (e) {
      print('❌ Erro ao aplicar filtros: $e');
      setState(() {
        _carregando = false;
      });
      
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Erro ao carregar dados: $e'),
          backgroundColor: Colors.red,
        ),
      );
    }
  }

  void _processarDados(List<Historico> dados) {
    _dadosPorTipo.forEach((key, value) => value.clear());

    for (var row in dados) {
      final valor = row.valorDouble;
      
      if (row.tipoSensor.toLowerCase().contains('temperatura')) {
        _dadosPorTipo['temperatura']!.add(valor);
      } else if (row.tipoSensor.toLowerCase().contains('umidade')) {
        _dadosPorTipo['umidade']!.add(valor);
      } else if (row.tipoSensor.toLowerCase().contains('gases')) {
        _dadosPorTipo['gases']!.add(valor);
      } else if (row.tipoSensor.toLowerCase().contains('ultrassonico')) {
        _dadosPorTipo['ultrassonico']!.add(valor);
      }
    }

    _estatisticas = GraficosController.calcularEstatisticas(dados);
  }

  Widget _buildFiltroLocal() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.2),
            blurRadius: 4,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Localização',
            style: TextStyle(
              fontWeight: FontWeight.bold,
              fontSize: 16,
              color: Color(0xFF007701),
            ),
          ),
          const SizedBox(height: 8),
          DropdownButtonFormField<String>(
            value: _localFiltro,
            items: _localizacoes.map((local) {
              return DropdownMenuItem(
                value: local,
                child: Text(local),
              );
            }).toList(),
            onChanged: (String? novoLocal) {
              if (novoLocal != null) {
                setState(() {
                  _localFiltro = novoLocal;
                });
                _aplicarFiltros();
              }
            },
            decoration: const InputDecoration(
              border: OutlineInputBorder(),
              contentPadding: EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildFiltrosAvancados() {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.2),
            blurRadius: 4,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Filtros Avançados',
            style: TextStyle(
              fontWeight: FontWeight.bold,
              fontSize: 16,
              color: Color(0xFF007701),
            ),
          ),
          const SizedBox(height: 12),
          TextFormField(
            controller: _dataController,
            decoration: const InputDecoration(
              labelText: 'Data (DD/MM/AAAA)',
              border: OutlineInputBorder(),
              prefixIcon: Icon(Icons.calendar_today),
              hintText: 'DD/MM/AAAA',
            ),
            keyboardType: TextInputType.datetime,
            onChanged: (value) {
              // Formata a data enquanto o usuário digita
              final formattedValue = _formatarDataDigitada(value);
              if (formattedValue != value) {
                _dataController.value = TextEditingValue(
                  text: formattedValue,
                  selection: TextSelection.collapsed(offset: formattedValue.length),
                );
              }
              setState(() {
                _dataFiltro = formattedValue;
              });
            },
          ),
          const SizedBox(height: 12),
          DropdownButtonFormField<String>(
            value: _tipoFiltro.isEmpty ? null : _tipoFiltro,
            items: const [
              DropdownMenuItem(value: '', child: Text('Todos os Sensores')),
              DropdownMenuItem(value: 'Sensor de Temperatura', child: Text('Temperatura')),
              DropdownMenuItem(value: 'Sensor de Umidade', child: Text('Umidade')),
              DropdownMenuItem(value: 'Sensor de Gases', child: Text('Gases')),
              DropdownMenuItem(value: 'Sensor Ultrassonico', child: Text('Ultrassônico')),
            ],
            onChanged: (String? novoTipo) {
              setState(() {
                _tipoFiltro = novoTipo ?? '';
              });
            },
            decoration: const InputDecoration(
              labelText: 'Tipo de Sensor',
              border: OutlineInputBorder(),
              prefixIcon: Icon(Icons.sensors),
            ),
          ),
          const SizedBox(height: 16),
          Row(
            children: [
              Expanded(
                child: ElevatedButton(
                  onPressed: _aplicarFiltros,
                  style: ElevatedButton.styleFrom(
                    backgroundColor: const Color(0xFF007701),
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 12),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                  ),
                  child: const Text('APLICAR FILTROS'),
                ),
              ),
              const SizedBox(width: 8),
              if (_filtroAtivo)
                Expanded(
                  child: TextButton(
                    onPressed: () {
                      setState(() {
                        _dataFiltro = '';
                        _tipoFiltro = '';
                        _dataController.clear();
                      });
                      _aplicarFiltros();
                    },
                    child: const Text('LIMPAR'),
                  ),
                ),
            ],
          ),
        ],
      ),
    );
  }

  Widget _buildCardGrafico(String titulo, Widget grafico) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.2),
            blurRadius: 4,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        children: [
          Text(
            titulo,
            style: const TextStyle(
              fontWeight: FontWeight.bold,
              fontSize: 16,
              color: Color(0xFF007701),
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 12),
          SizedBox(height: 200, child: grafico),
        ],
      ),
    );
  }

  Widget _buildGraficoTemperaturaUmidade() {
    final dadosTemp = _dadosPorTipo['temperatura'] ?? [];
    final dadosUmid = _dadosPorTipo['umidade'] ?? [];
    
    if (dadosTemp.isEmpty && dadosUmid.isEmpty) {
      return const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.bar_chart, size: 48, color: Colors.grey),
            SizedBox(height: 8),
            Text(
              'Sem dados de temperatura/umidade',
              style: TextStyle(color: Colors.grey),
            ),
          ],
        ),
      );
    }

    final spotsTemp = dadosTemp.asMap().entries.map((e) => FlSpot(e.key.toDouble(), e.value)).toList();
    final spotsUmid = dadosUmid.asMap().entries.map((e) => FlSpot(e.key.toDouble(), e.value)).toList();

    return LineChart(
      LineChartData(
        lineTouchData: LineTouchData(enabled: true),
        gridData: FlGridData(show: true),
        titlesData: FlTitlesData(
          bottomTitles: AxisTitles(
            sideTitles: SideTitles(showTitles: true, reservedSize: 22),
          ),
          leftTitles: AxisTitles(
            sideTitles: SideTitles(showTitles: true, reservedSize: 40),
          ),
          rightTitles: const AxisTitles(),
          topTitles: const AxisTitles(),
        ),
        borderData: FlBorderData(
          show: true,
          border: Border.all(color: const Color(0xff37434d), width: 1),
        ),
        minX: 0,
        maxX: spotsTemp.isNotEmpty ? spotsTemp.length - 1.toDouble() : 10,
        minY: 0,
        maxY: 100,
        lineBarsData: [
          if (spotsTemp.isNotEmpty)
            LineChartBarData(
              spots: spotsTemp,
              isCurved: true,
              color: Colors.red,
              barWidth: 3,
              isStrokeCapRound: true,
              dotData: const FlDotData(show: false),
              belowBarData: BarAreaData(show: false),
            ),
          if (spotsUmid.isNotEmpty)
            LineChartBarData(
              spots: spotsUmid,
              isCurved: true,
              color: Colors.blue,
              barWidth: 3,
              isStrokeCapRound: true,
              dotData: const FlDotData(show: false),
              belowBarData: BarAreaData(show: false),
            ),
        ],
      ),
    );
  }

  Widget _buildGraficoGases() {
    final dados = _dadosPorTipo['gases'] ?? [];
    if (dados.isEmpty) {
      return const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.bar_chart, size: 48, color: Colors.grey),
            SizedBox(height: 8),
            Text(
              'Sem dados de gases',
              style: TextStyle(color: Colors.grey),
            ),
          ],
        ),
      );
    }

    final barGroups = dados.asMap().entries.map((e) {
      return BarChartGroupData(
        x: e.key,
        barRods: [
          BarChartRodData(
            toY: e.value,
            color: const Color.fromARGB(255, 0, 180, 3),
            width: 16,
            borderRadius: BorderRadius.circular(4),
          ),
        ],
      );
    }).toList();

    return BarChart(
      BarChartData(
        alignment: BarChartAlignment.spaceAround,
        barTouchData: BarTouchData(enabled: true),
        titlesData: FlTitlesData(
          bottomTitles: AxisTitles(
            sideTitles: SideTitles(showTitles: true, reservedSize: 22),
          ),
          leftTitles: AxisTitles(
            sideTitles: SideTitles(showTitles: true, reservedSize: 40),
          ),
          rightTitles: const AxisTitles(),
          topTitles: const AxisTitles(),
        ),
        gridData: const FlGridData(show: true),
        borderData: FlBorderData(
          show: true,
          border: Border.all(color: const Color(0xff37434d), width: 1),
        ),
        barGroups: barGroups,
      ),
    );
  }

  Widget _buildGraficoUltrassonico() {
    final dados = _dadosPorTipo['ultrassonico'] ?? [];
    if (dados.isEmpty) {
      return const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.bar_chart, size: 48, color: Colors.grey),
            SizedBox(height: 8),
            Text(
              'Sem dados ultrassônicos',
              style: TextStyle(color: Colors.grey),
            ),
          ],
        ),
      );
    }

    final spots = dados.asMap().entries.map((e) => FlSpot(e.key.toDouble(), e.value)).toList();

    return LineChart(
      LineChartData(
        lineTouchData: LineTouchData(enabled: true),
        gridData: const FlGridData(show: true),
        titlesData: FlTitlesData(
          bottomTitles: AxisTitles(
            sideTitles: SideTitles(showTitles: true, reservedSize: 22),
          ),
          leftTitles: AxisTitles(
            sideTitles: SideTitles(showTitles: true, reservedSize: 40),
          ),
          rightTitles: const AxisTitles(),
          topTitles: const AxisTitles(),
        ),
        borderData: FlBorderData(
          show: true,
          border: Border.all(color: const Color(0xff37434d), width: 1),
        ),
        minX: 0,
        maxX: spots.isNotEmpty ? spots.length - 1.toDouble() : 10,
        minY: 0,
        maxY: dados.isNotEmpty ? dados.reduce((a, b) => a > b ? a : b) + 10 : 100,
        lineBarsData: [
          LineChartBarData(
            spots: spots,
            isCurved: true,
            color: Colors.purple,
            barWidth: 3,
            isStrokeCapRound: true,
            dotData: const FlDotData(show: false),
            belowBarData: BarAreaData(show: false),
          ),
        ],
      ),
    );
  }

  Widget _buildMensagemSensorInativo(String statusTemp, String statusUmid) {
    String mensagem = '';
    if (statusTemp == 'Inativo' || statusUmid == 'Inativo') {
      mensagem = "Sensor(es) Inativo(s)";
    } else {
      mensagem = "Sensor(es) em Manutenção";
    }

    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.warning, size: 48, color: Colors.orange),
          const SizedBox(height: 8),
          Text(
            mensagem,
            textAlign: TextAlign.center,
            style: const TextStyle(color: Colors.grey),
          ),
        ],
      ),
    );
  }

  Widget _buildMensagemSensorInativoUnico(String status) {
    String mensagem = status == 'Inativo' 
        ? "Sensor Inativo"
        : "Sensor em Manutenção";

    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.warning, size: 48, color: Colors.orange),
          const SizedBox(height: 8),
          Text(
            mensagem,
            textAlign: TextAlign.center,
            style: const TextStyle(color: Colors.grey),
          ),
        ],
      ),
    );
  }

  Widget _buildTabelaDados() {
    if (_dadosFiltrados.isEmpty) {
      return Container();
    }

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(12),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.2),
            blurRadius: 4,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Últimos Registros',
            style: TextStyle(
              fontWeight: FontWeight.bold,
              fontSize: 16,
              color: Color(0xFF007701),
            ),
          ),
          const SizedBox(height: 12),
          ..._dadosFiltrados.take(10).map((dado) {
            return Container(
              padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 12),
              margin: const EdgeInsets.only(bottom: 8),
              decoration: BoxDecoration(
                color: Colors.grey[50],
                borderRadius: BorderRadius.circular(8),
              ),
              child: Row(
                children: [
                  Icon(
                    _getIconPorTipo(dado.tipoSensor),
                    color: const Color(0xFF007701),
                    size: 20,
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(
                          dado.tipoSensor,
                          style: const TextStyle(fontWeight: FontWeight.bold),
                        ),
                        Text(
                          '${dado.dados} ${dado.unidadeMedida}',
                          style: const TextStyle(color: Colors.grey),
                        ),
                      ],
                    ),
                  ),
                  Text(
                    _formatarData(dado.dataHoraColeta),
                    style: const TextStyle(color: Colors.grey, fontSize: 12),
                  ),
                ],
              ),
            );
          }).toList(),
        ],
      ),
    );
  }

  IconData _getIconPorTipo(String tipoSensor) {
    if (tipoSensor.toLowerCase().contains('temperatura')) {
      return Icons.thermostat;
    } else if (tipoSensor.toLowerCase().contains('umidade')) {
      return Icons.water_drop;
    } else if (tipoSensor.toLowerCase().contains('gases')) {
      return Icons.air;
    } else if (tipoSensor.toLowerCase().contains('ultrassonico')) {
      return Icons.sensors;
    }
    return Icons.device_unknown;
  }

  String _formatarData(DateTime data) {
    return '${data.day.toString().padLeft(2, '0')}/${data.month.toString().padLeft(2, '0')} ${data.hour.toString().padLeft(2, '0')}:${data.minute.toString().padLeft(2, '0')}';
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: const Color(0xFFf5f5f5),
      drawer: _buildDrawer(context),
      appBar: AppBar(
        backgroundColor: const Color(0xFF007701),
        elevation: 0,
        leading: Builder(
          builder: (context) => IconButton(
            icon: const Icon(Icons.menu, color: Colors.white, size: 35),
            onPressed: () => Scaffold.of(context).openDrawer(),
          ),
        ),
        title: Image.asset('assets/images/logo.png', width: 200),
        centerTitle: true,
        actions: [
          // ÍCONE DO PERFIL ATUALIZADO
          ProfileAvatarWidget(
            radius: 18,
            onTap: () {
              final authController = AuthController();
              if (authController.usuarioLogado != null) {
                Navigator.push(
                  context,
                  MaterialPageRoute(
                    builder: (context) => DadosCadastradosPage(usuario: authController.usuarioLogado!),
                  ),
                );
              } else {
                ScaffoldMessenger.of(context).showSnackBar(
                  const SnackBar(
                    content: Text('Nenhum usuário logado encontrado'),
                    backgroundColor: Colors.red,
                  ),
                );
              }
            },
          ),
        ],
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(160),
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
            child: Align(
              alignment: Alignment.center,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.center,
                children: [
                  const SizedBox(height: 10),
                  const Text(
                    'Monitoramento Inteligente',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 23,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 15),
                  SingleChildScrollView(
                    scrollDirection: Axis.horizontal,
                    child: Row(
                      children: [
                        CircleIcon(icon: Icons.home, label: 'Início', onPressed: () {
                          Navigator.push(context, MaterialPageRoute(builder: (context) => PrincipalPage(usuario: widget.usuario)));
                        }),
                        CircleIcon(icon: Icons.map, label: 'Mapas', onPressed: () {
                          Navigator.push(context, MaterialPageRoute(builder: (context) => MapaPage(usuario: widget.usuario)));
                        }),
                        CircleIcon(icon: Icons.bar_chart, label: 'Gráficos', onPressed: () {}),
                        CircleIcon(icon: Icons.photo_library, label: 'Galeria', onPressed: () {
                          Navigator.push(context, MaterialPageRoute(builder: (context) => GaleriaPage(usuario: widget.usuario)));
                        }),
                      ],
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
      body: _carregando
          ? const Center(child: CircularProgressIndicator())
          : SingleChildScrollView(
              child: Column(
                children: [
                  _buildFiltroLocal(),
                  _buildFiltrosAvancados(),
                  
                  // Gráficos
                  _buildCardGrafico(
                    'Temperatura e Umidade - ${_localFiltro == 'Todos' ? 'Todas as Localizações' : _localFiltro}',
                    _statusSensores.temperatura != 'Ativo' || _statusSensores.umidade != 'Ativo'
                        ? _buildMensagemSensorInativo(_statusSensores.temperatura, _statusSensores.umidade)
                        : _buildGraficoTemperaturaUmidade(),
                  ),
                  
                  _buildCardGrafico(
                    'Nível de Gases - ${_localFiltro == 'Todos' ? 'Todas as Localizações' : _localFiltro}',
                    _statusSensores.gases != 'Ativo'
                        ? _buildMensagemSensorInativoUnico(_statusSensores.gases)
                        : _buildGraficoGases(),
                  ),
                  
                  _buildCardGrafico(
                    'Sensor Ultrassônico - ${_localFiltro == 'Todos' ? 'Todas as Localizações' : _localFiltro}',
                    _statusSensores.ultrassonico != 'Ativo'
                        ? _buildMensagemSensorInativoUnico(_statusSensores.ultrassonico)
                        : _buildGraficoUltrassonico(),
                  ),
                  
                  _buildTabelaDados(),
                ],
              ),
            ),
    );
  }

  Widget _buildDrawer(BuildContext context) {
    return SizedBox(
      width: 250,
      child: Drawer(
        backgroundColor: Colors.white,
        child: Column(
          children: [
            DrawerHeader(
              decoration: const BoxDecoration(color: Color(0xFF007701)),
              margin: EdgeInsets.zero,
              padding: EdgeInsets.zero,
              child: Container(
                width: double.infinity,
                padding: const EdgeInsets.all(16),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // AVATAR ATUALIZADO
                    ProfileAvatarWidget(
                      radius: 30,
                      onTap: () {
                        final authController = AuthController();
                        if (authController.usuarioLogado != null) {
                          Navigator.push(
                            context,
                            MaterialPageRoute(
                              builder: (context) => DadosCadastradosPage(usuario: authController.usuarioLogado!),
                            ),
                          );
                        }
                      },
                    ),
                    const SizedBox(height: 10),
                    Text(
                      'Olá, ${widget.usuario}!',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const Text(
                      'Bem-vindo de volta',
                      style: TextStyle(color: Colors.white, fontSize: 14),
                    ),
                  ],
                ),
              ),
            ),
            Expanded(
              child: ListView(
                padding: EdgeInsets.zero,
                children: [
                  _drawerItem(icon: Icons.home, text: 'Início', onTap: () {
                    Navigator.push(context, MaterialPageRoute(builder: (context) => PrincipalPage(usuario: widget.usuario)));
                  }),
                  _drawerItem(icon: Icons.map, text: 'Mapas', onTap: () {
                    Navigator.push(context, MaterialPageRoute(builder: (context) => MapaPage(usuario: widget.usuario)));
                  }),
                  _drawerItem(icon: Icons.bar_chart, text: 'Gráficos', onTap: () {}),
                  _drawerItem(icon: Icons.photo_library, text: 'Galeria', onTap: () {
                    Navigator.push(context, MaterialPageRoute(builder: (context) => GaleriaPage(usuario: widget.usuario)));
                  }),
                  _drawerItem(icon: Icons.settings, text: 'Configurações', onTap: () {
                    final authController = AuthController();
                    if (authController.usuarioLogado != null) {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (context) => DadosCadastradosPage(usuario: authController.usuarioLogado!),
                        ),
                      );
                    } else {
                      ScaffoldMessenger.of(context).showSnackBar(
                        const SnackBar(
                          content: Text('Nenhum usuário logado encontrado'),
                          backgroundColor: Colors.red,
                        ),
                      );
                    }
                  }),
                  const Divider(),
                  _drawerItem(
                    icon: Icons.logout,
                    text: 'Sair',
                    color: Colors.red,
                    onTap: () {
                      Navigator.push(context, MaterialPageRoute(builder: (context) => const LoginPage()));
                    },
                  ),
                ],
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(12.0),
              child: Text(
                'Safe Zone © 2025',
                style: TextStyle(fontSize: 12, color: Colors.grey.shade600),
              ),
            )
          ],
        ),
      ),
    );
  }
}

Widget _drawerItem({
  required IconData icon,
  required String text,
  VoidCallback? onTap,
  Color color = const Color(0xFF007701),
}) {
  return ListTile(
    leading: Icon(icon, color: color),
    title: Text(text, style: TextStyle(fontSize: 16, color: color)),
    onTap: onTap,
    contentPadding: const EdgeInsets.symmetric(horizontal: 20),
  );
}

class CircleIcon extends StatelessWidget {
  final IconData icon;
  final String label;
  final VoidCallback onPressed;

  const CircleIcon({
    required this.icon,
    required this.label,
    required this.onPressed,
  });

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 12),
      child: Column(
        children: [
          CircleAvatar(
            radius: 30,
            backgroundColor: Colors.white,
            child: IconButton(
              icon: Icon(icon, size: 30, color: const Color(0xFF007701)),
              onPressed: onPressed,
            ),
          ),
          const SizedBox(height: 5),
          Text(
            label,
            style: const TextStyle(
              color: Colors.white,
              fontSize: 12,
              fontWeight: FontWeight.w500,
            ),
          ),
        ],
      ),
    );
  }
}