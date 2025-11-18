import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/historico_model.dart';
import '../models/sensor_model.dart';
import '../models/estatisticas_model.dart';

class GraficosController {
  static const String baseUrl = 'http://10.141.128.126/safe-zone-api/public';

  // BUSCAR DADOS DO HISTÓRICO (APENAS LEITURA)
  static Future<List<Historico>> buscarDadosGrafico({
    String? localizacao,
    String? data,
    String? tipoSensor,
  }) async {
    try {
      final Map<String, String> queryParams = {};
      if (localizacao != null && localizacao != 'Todos') {
        queryParams['localizacao'] = localizacao;
      }
      if (data != null && data.isNotEmpty) {
        queryParams['data'] = data;
      }
      if (tipoSensor != null && tipoSensor.isNotEmpty) {
        queryParams['tipo_sensor'] = tipoSensor;
      }

      final uri = Uri.parse('$baseUrl/historico').replace(queryParameters: queryParams);
       print('Errobuscar dados: $uri');
      final response = await http.get(
        uri,
        headers: {'Content-Type': 'application/json'},
      ).timeout(Duration(seconds: 30));

      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        final List<dynamic> data = responseData['data'];
        return data.map((json) => Historico.fromJson(json)).toList();
      } else {
        throw Exception('Erro ao buscar dados: ${response.statusCode}');
      }
    } catch (e) {
      print('Erro no GraficosController.buscarDadosGrafico: $e');
      throw Exception('Falha na comunicação com o servidor');
    }
  }

  // BUSCAR SENSORES (APENAS LEITURA)
  static Future<List<Sensor>> buscarSensoresPorLocalizacao(String localizacao) async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/sensores?localizacao=$localizacao'),
        headers: {'Content-Type': 'application/json'},
      ).timeout(Duration(seconds: 30));

      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        final List<dynamic> data = responseData['data'];
        return data.map((json) => Sensor.fromJson(json)).toList();
      } else {
        throw Exception('Erro ao buscar sensores: ${response.statusCode}');
      }
    } catch (e) {
      print('Erro no GraficosController.buscarSensoresPorLocalizacao: $e');
      return [];
    }
  }

  // BUSCAR LOCALIZAÇÕES
  static Future<List<String>> buscarLocalizacoes() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/localizacoes'),
        headers: {'Content-Type': 'application/json'},
      ).timeout(Duration(seconds: 30));

      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        final List<dynamic> data = responseData['data'];
        return data.map((item) => item.toString()).toList();
      } else {
        return [
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
      }
    } catch (e) {
      print('Erro no GraficosController.buscarLocalizacoes: $e');
      return [
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
    }
  }

  // CALCULAR ESTATÍSTICAS (local - não precisa de API)
  static Map<String, EstatisticasSensor> calcularEstatisticas(List<Historico> dados) {
    final estatisticas = {
      'temperatura': EstatisticasSensor.vazio(),
      'umidade': EstatisticasSensor.vazio(),
      'gases': EstatisticasSensor.vazio(),
      'ultrassonico': EstatisticasSensor.vazio(),
    };

    final dadosTemp = <double>[];
    final dadosUmid = <double>[];
    final dadosGases = <double>[];
    final dadosUltra = <double>[];

    for (var row in dados) {
      final valor = row.valorDouble;
      
      if (row.tipoSensor.toLowerCase().contains('temperatura')) {
        dadosTemp.add(valor);
      } else if (row.tipoSensor.toLowerCase().contains('umidade')) {
        dadosUmid.add(valor);
      } else if (row.tipoSensor.toLowerCase().contains('gases')) {
        dadosGases.add(valor);
      } else if (row.tipoSensor.toLowerCase().contains('ultrassonico')) {
        dadosUltra.add(valor);
      }
    }

    if (dadosTemp.isNotEmpty) {
      estatisticas['temperatura'] = EstatisticasSensor(
        max: dadosTemp.reduce((a, b) => a > b ? a : b),
        min: dadosTemp.reduce((a, b) => a < b ? a : b),
        media: dadosTemp.reduce((a, b) => a + b) / dadosTemp.length,
      );
    }

    if (dadosUmid.isNotEmpty) {
      estatisticas['umidade'] = EstatisticasSensor(
        max: dadosUmid.reduce((a, b) => a > b ? a : b),
        min: dadosUmid.reduce((a, b) => a < b ? a : b),
        media: dadosUmid.reduce((a, b) => a + b) / dadosUmid.length,
      );
    }

    if (dadosGases.isNotEmpty) {
      estatisticas['gases'] = EstatisticasSensor(
        max: dadosGases.reduce((a, b) => a > b ? a : b),
        min: dadosGases.reduce((a, b) => a < b ? a : b),
        media: dadosGases.reduce((a, b) => a + b) / dadosGases.length,
      );
    }

    if (dadosUltra.isNotEmpty) {
      estatisticas['ultrassonico'] = EstatisticasSensor(
        max: dadosUltra.reduce((a, b) => a > b ? a : b),
        min: dadosUltra.reduce((a, b) => a < b ? a : b),
        media: dadosUltra.reduce((a, b) => a + b) / dadosUltra.length,
      );
    }

    return estatisticas;
  }

  // VERIFICAR STATUS DOS SENSORES
  static Future<StatusSensoresLocal> verificarStatusSensores(String localizacao) async {
    if (localizacao == 'Todos') {
      return StatusSensoresLocal.padrao();
    }

    try {
      final sensores = await buscarSensoresPorLocalizacao(localizacao);
      final status = StatusSensoresLocal.padrao();

      final mapeamentoSensores = {
        'Temperatura': ['Sensor de Temperatura'],
        'Umidade': ['Sensor de Umidade'],
        'Gases': ['Sensor de Gases'],
        'Ultrassonico': ['Sensor Ultrassonico', 'Ultrassonico'],
      };

      for (final entry in mapeamentoSensores.entries) {
        String statusEncontrado = 'Ativo';
        
        for (final sensor in sensores) {
          if (entry.value.contains(sensor.tipoSensor)) {
            if (sensor.statusSensor == 'Inativo' || sensor.statusSensor == 'Manutenção') {
              statusEncontrado = sensor.statusSensor;
              break;
            }
          }
        }

        switch (entry.key) {
          case 'Temperatura':
            status.temperatura = statusEncontrado;
            break;
          case 'Umidade':
            status.umidade = statusEncontrado;
            break;
          case 'Gases':
            status.gases = statusEncontrado;
            break;
          case 'Ultrassonico':
            status.ultrassonico = statusEncontrado;
            break;
        }
      }

      return status;
    } catch (e) {
      print('Erro ao verificar status dos sensores: $e');
      return StatusSensoresLocal.padrao();
    }
  }
}