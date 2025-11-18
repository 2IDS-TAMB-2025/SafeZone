class Sensor {
  int idSensor;
  String tipoSensor;
  String localizacao;
  DateTime dataInstalacao;
  String statusSensor;

  Sensor({
    required this.idSensor,
    required this.tipoSensor,
    required this.localizacao,
    required this.dataInstalacao,
    required this.statusSensor,
  });

  factory Sensor.fromJson(Map<String, dynamic> json) {
    return Sensor(
      idSensor: json['ID_SENSOR'] ?? json['id_sensor'],
      tipoSensor: json['TIPO_SENSOR'] ?? json['tipo_sensor'] ?? '',
      localizacao: json['LOCALIZACAO'] ?? json['localizacao'] ?? '',
      dataInstalacao: DateTime.parse(json['DATA_INSTALACAO'] ?? json['data_instalacao']),
      statusSensor: json['STATUS_SENSOR'] ?? json['status_sensor'] ?? 'Ativo',
    );
  }
}