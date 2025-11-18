class EstatisticasSensor {
  double max;
  double min;
  double media;

  EstatisticasSensor({
    required this.max,
    required this.min,
    required this.media,
  });

  factory EstatisticasSensor.vazio() {
    return EstatisticasSensor(max: 0, min: 0, media: 0);
  }
}

class StatusSensoresLocal {
  String temperatura;
  String umidade;
  String gases;
  String ultrassonico;

  StatusSensoresLocal({
    required this.temperatura,
    required this.umidade,
    required this.gases,
    required this.ultrassonico,
  });

  factory StatusSensoresLocal.padrao() {
    return StatusSensoresLocal(
      temperatura: 'Ativo',
      umidade: 'Ativo',
      gases: 'Ativo',
      ultrassonico: 'Ativo',
    );
  }
}