class Historico {
  int? idHistorico;
  int idSensor;
  String dados;
  String unidadeMedida;
  double? latitude;
  double? longitude;
  DateTime dataHoraColeta;
  String tipoSensor;
  String localizacao;

  Historico({
    this.idHistorico,
    required this.idSensor,
    required this.dados,
    required this.unidadeMedida,
    this.latitude,
    this.longitude,
    required this.dataHoraColeta,
    required this.tipoSensor,
    required this.localizacao,
  });

  factory Historico.fromJson(Map<String, dynamic> json) {
    return Historico(
      idHistorico: json['ID_HISTORICO'] ?? json['id_historico'],
      idSensor: json['ID_SENSOR'] ?? json['id_sensor'],
      dados: json['DADOS']?.toString() ?? json['dados']?.toString() ?? '0',
      unidadeMedida: json['UNIDADE_MEDIDA'] ?? json['unidade_medida'] ?? '',
      latitude: json['LATITUDE'] != null ? double.tryParse(json['LATITUDE'].toString()) : null,
      longitude: json['LONGITUDE'] != null ? double.tryParse(json['LONGITUDE'].toString()) : null,
      dataHoraColeta: DateTime.parse(json['DATA_HORA_COLETA'] ?? json['data_hora_coleta']),
      tipoSensor: json['TIPO_SENSOR'] ?? json['tipo_sensor'] ?? '',
      localizacao: json['LOCALIZACAO'] ?? json['localizacao'] ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'ID_HISTORICO': idHistorico,
      'ID_SENSOR': idSensor,
      'DADOS': dados,
      'UNIDADE_MEDIDA': unidadeMedida,
      'LATITUDE': latitude,
      'LONGITUDE': longitude,
      'DATA_HORA_COLETA': dataHoraColeta.toIso8601String(),
      'TIPO_SENSOR': tipoSensor,
      'LOCALIZACAO': localizacao,
    };
  }

  double get valorDouble => double.tryParse(dados) ?? 0.0;
}