import 'package:flutter/material.dart';
import 'package:fl_chart/fl_chart.dart';
import 'package:tcc_safe_zone/pages/admin.dart';
import 'package:tcc_safe_zone/pages/ajuda.dart';
import 'package:tcc_safe_zone/pages/config.dart';
import 'package:tcc_safe_zone/pages/criar.conta.dart';
import 'package:tcc_safe_zone/pages/galeria.dart';
import 'package:tcc_safe_zone/pages/mapa.dart';
import 'package:tcc_safe_zone/pages/principal.dart';

class GraficosPage extends StatelessWidget {
  final String usuario;
  GraficosPage({required this.usuario});

  @override
  Widget build(BuildContext context) {
    final tipoSensorData = [
      _ChartData('Temperatura', 25),
      _ChartData('Umidade', 60),
      _ChartData('CO2', 400),
      _ChartData('Luminosidade', 800),
    ];

    final dataColetaData = [
      _TimeSeriesData(DateTime(2023, 1, 1), 20),
      _TimeSeriesData(DateTime(2023, 1, 2), 25),
      _TimeSeriesData(DateTime(2023, 1, 3), 22),
      _TimeSeriesData(DateTime(2023, 1, 4), 28),
      _TimeSeriesData(DateTime(2023, 1, 5), 30),
    ];

    final horaColetaData = [
      _ChartData('08:00', 20),
      _ChartData('10:00', 25),
      _ChartData('12:00', 30),
      _ChartData('14:00', 22),
      _ChartData('16:00', 18),
    ];

    final unidadeMedidaData = [
      _ChartData('°C', 25),
      _ChartData('%', 60),
      _ChartData('ppm', 400),
      _ChartData('lux', 800),
    ];

    final latDadosData = [
      _ScatterData(-23.5, 20),
      _ScatterData(-23.6, 25),
      _ScatterData(-23.7, 30),
      _ScatterData(-23.8, 22),
    ];

    final longDadosData = [
      _ScatterData(-46.5, 20),
      _ScatterData(-46.6, 25),
      _ScatterData(-46.7, 30),
      _ScatterData(-46.8, 22),
    ];

    return Scaffold(
      backgroundColor: Colors.white,
      drawer: SizedBox(
        width: 250,
        child: Drawer(
          backgroundColor: Colors.white,
          child: Column(
            children: [
              DrawerHeader(
                decoration: const BoxDecoration(
                  color: Color(0xFF007701),
                ),
                margin: EdgeInsets.zero,
                padding: EdgeInsets.zero,
                child: Container(
                  width: double.infinity,
                  padding: const EdgeInsets.all(16),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      CircleAvatar(
                        radius: 30,
                        backgroundColor: Colors.white,
                        child: Icon(Icons.person, size: 40, color: Color(0xFF007701)),
                      ),
                      const SizedBox(height: 10),
                      Text(
                        'Olá, $usuario!',
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
                    _drawerItem(
                      icon: Icons.home,
                      text: 'Início',
                      onTap: () {
                        Navigator.push(context, MaterialPageRoute(builder: (context) => PrincipalPage(usuario: usuario)));
                      }
                    ),
                    _drawerItem(
                      icon: Icons.map,
                      text: 'Mapas',
                      onTap: () {
                        Navigator.push(context, MaterialPageRoute(builder: (context) => MapaPage(usuario: usuario)));
                      },
                    ),
                    _drawerItem(icon: Icons.bar_chart, text: 'Gráficos', onTap: () {
                      Navigator.push(context, MaterialPageRoute(builder: (context) => GraficosPage(usuario: usuario)));
                    }),
                    _drawerItem(icon: Icons.photo_library, text: 'Galeria', onTap: () {
                      Navigator.push(context, MaterialPageRoute(builder: (context) => GaleriaPage(usuario: usuario)));
                    }),
                    _drawerItem(icon: Icons.support_agent, text: 'Suporte', onTap: () {
                      Navigator.push(context, MaterialPageRoute(builder: (context) => const AjudaPage()));
                    }),
                    _drawerItem(icon: Icons.settings, text: 'Configurações', onTap: () {
                      Navigator.push(context, MaterialPageRoute(builder: (context) => const ConfigPage()));
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
      ),
      appBar: AppBar(
        backgroundColor: Color(0xFF007701),
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
          IconButton(
            icon: const Icon(Icons.account_circle_outlined, color: Colors.white, size: 35),
            onPressed: () {},
          ),
        ],
        bottom: PreferredSize(
          preferredSize: const Size.fromHeight(215),
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
            child: Align(
              alignment: Alignment.center,
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.center,
                children: [
                  Container(
                    height: 40,
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(25),
                    ),
                    child: TextField(
                      decoration: InputDecoration(
                        hintText: 'Pesquisar',
                        prefixIcon: Icon(Icons.search, color: Colors.grey.shade700),
                        border: InputBorder.none,
                        contentPadding: const EdgeInsets.symmetric(vertical: 10),
                      ),
                    ),
                  ),
                  const SizedBox(height: 15),
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
                          Navigator.push(context, MaterialPageRoute(builder: (context) => PrincipalPage(usuario: usuario)));
                        }),
                        CircleIcon(icon: Icons.map, label: 'Mapas', onPressed: () {
                          Navigator.push(context, MaterialPageRoute(builder: (context) => MapaPage(usuario: usuario)));
                        }),
                        CircleIcon(icon: Icons.bar_chart, label: 'Gráficos', onPressed: () {
                          Navigator.push(context, MaterialPageRoute(builder: (context) => GraficosPage(usuario: usuario)));
                        }),
                        CircleIcon(icon: Icons.photo_library, label: 'Galeria', onPressed: () {
                          Navigator.push(context, MaterialPageRoute(builder: (context) => GaleriaPage(usuario: usuario)));
                        }),
                        CircleIcon(icon: Icons.forum, label: 'Ajuda', onPressed: () {
                          Navigator.push(context, MaterialPageRoute(builder: (context) => const AjudaPage()));
                        }),
                        CircleIcon(icon: Icons.admin_panel_settings, label: 'Admin', onPressed: () {
                          Navigator.push(context, MaterialPageRoute(builder: (context) => const AdminPage()));
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
      body: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.all(16.0),
          child: Column(
            children: [
              const Text(
                'Gráficos',
                style: TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 20),
              
              // Tipo por Dados
              _buildChartCard(
                title: 'Gráfico Tipo de Sensor por Dados',
                chart: SizedBox(
                  height: 300,
                  child: Padding(
                    padding: const EdgeInsets.all(8.0),
                    child: _buildBarChart(tipoSensorData, Colors.green),
                  ),
                ),
              ),
              
              // Dados por Data de Coleta
              _buildChartCard(
                title: 'Gráfico Dados por Data de Coleta',
                chart: SizedBox(
                  height: 300,
                  child: Padding(
                    padding: const EdgeInsets.only(top: 20.0, right: 10.0),
                    child: _buildLineChart(dataColetaData),
                  ),
                ),
              ),
              
              // Dados por Hora de Coleta
              _buildChartCard(
                title: 'Dados por Hora de Coleta',
                chart: SizedBox(
                  height: 300,
                  child: Padding(
                    padding: const EdgeInsets.all(8.0),
                    child: _buildBarChart(horaColetaData, Colors.orange),
                  ),
                ),
              ),
              
              // Média de Dados por Unidade de Medida
              _buildChartCard(
                title: 'Média por Unidade de Medida',
                chart: SizedBox(
                  height: 300,
                  child: Padding(
                    padding: const EdgeInsets.all(8.0),
                    child: _buildBarChart(unidadeMedidaData, Colors.blue),
                  ),
                ),
              ),
              
              // Latitude vs Dados
              _buildChartCard(
                title: 'Latitude vs Dados',
                chart: SizedBox(
                  height: 300,
                  child: Padding(
                    padding: const EdgeInsets.all(8.0),
                    child: _buildScatterChart(latDadosData, Colors.purple),
                  ),
                ),
              ),
              
              // Longitude vs Dados
              _buildChartCard(
                title: 'Longitude vs Dados',
                chart: SizedBox(
                  height: 300,
                  child: Padding(
                    padding: const EdgeInsets.all(8.0),
                    child: _buildScatterChart(longDadosData, Colors.red),
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildChartCard({required String title, required Widget chart}) {
    return Card(
      elevation: 4,
      margin: const EdgeInsets.symmetric(vertical: 10),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
      ),
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.stretch,
          children: [
            Text(
              title, 
              style: const TextStyle(
                fontWeight: FontWeight.bold,
                fontSize: 16,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 15),
            chart,
          ],
        ),
      ),
    );
  }

  Widget _buildBarChart(List<_ChartData> data, Color color) {
    return BarChart(
      BarChartData(
        alignment: BarChartAlignment.spaceAround,
        barTouchData: BarTouchData(
          enabled: false, // Desativado o toque
        ),
        barGroups: data
            .asMap()
            .map((index, item) => MapEntry(
                  index,
                  BarChartGroupData(
                    x: index,
                    barRods: [
                      BarChartRodData(
                        toY: item.value.toDouble(),
                        color: color.withOpacity(0.7),
                        width: 22,
                        borderRadius: BorderRadius.circular(4),
                      ),
                    ],
                  ),
                ))
            .values
            .toList(),
        titlesData: FlTitlesData(
          show: true,
          bottomTitles: AxisTitles(
            sideTitles: SideTitles(
              showTitles: true,
              getTitlesWidget: (value, meta) {
                final label = data[value.toInt()].label;
                return Padding(
                  padding: const EdgeInsets.only(top: 8.0),
                  child: Text(
                    label.length > 8 ? '${label.substring(0, 8)}...' : label,
                    style: const TextStyle(fontSize: 10, color: Colors.black87),
                    textAlign: TextAlign.center,
                  ),
                );
              },
              reservedSize: 40,
            ),
          ),
          leftTitles: AxisTitles(
            sideTitles: SideTitles(
              showTitles: true,
              reservedSize: 40,
              getTitlesWidget: (value, meta) {
                return Text(
                  value.toInt().toString(),
                  style: const TextStyle(fontSize: 10, color: Colors.black87),
                );
              },
            ),
          ),
          rightTitles: AxisTitles(sideTitles: SideTitles(showTitles: false)),
          topTitles: AxisTitles(sideTitles: SideTitles(showTitles: false)),
        ),
        borderData: FlBorderData(
          show: false,
        ),
        gridData: FlGridData(
          show: true,
          drawVerticalLine: false,
          horizontalInterval: data.map((e) => e.value).reduce((a, b) => a > b ? a : b) / 5,
          getDrawingHorizontalLine: (value) => FlLine(
            color: Colors.grey[200],
            strokeWidth: 1,
          ),
        ),
      ),
    );
  }

  Widget _buildLineChart(List<_TimeSeriesData> data) {
    final maxValue = data.map((e) => e.value).reduce((a, b) => a > b ? a : b);
    final minValue = data.map((e) => e.value).reduce((a, b) => a < b ? a : b);
    
    return LineChart(
      LineChartData(
        lineTouchData: LineTouchData(
          enabled: false, // Desativado o toque
        ),
        lineBarsData: [
          LineChartBarData(
            spots: data
                .map((item) => FlSpot(
                    item.time.millisecondsSinceEpoch.toDouble(), item.value.toDouble()))
                .toList(),
            isCurved: true,
            color: Colors.blue,
            barWidth: 4,
            belowBarData: BarAreaData(
              show: false,
            ),
            dotData: FlDotData(
              show: true,
              getDotPainter: (spot, percent, barData, index) => FlDotCirclePainter(
                radius: 4,
                color: Colors.blue,
                strokeWidth: 2,
                strokeColor: Colors.white,
              ),
            ),
          ),
        ],
        minY: minValue.toDouble() - 5,
        maxY: maxValue.toDouble() + 5,
        titlesData: FlTitlesData(
          show: true,
          bottomTitles: AxisTitles(
            sideTitles: SideTitles(
              showTitles: true,
              reservedSize: 30,
              getTitlesWidget: (value, meta) {
                final date = DateTime.fromMillisecondsSinceEpoch(value.toInt());
                return Padding(
                  padding: const EdgeInsets.only(top: 8.0),
                  child: Text(
                    '${date.day}/${date.month}',
                    style: const TextStyle(fontSize: 10, color: Colors.black87),
                  ),
                );
              },
            ),
          ),
          leftTitles: AxisTitles(
            sideTitles: SideTitles(
              showTitles: true,
              reservedSize: 40,
              getTitlesWidget: (value, meta) {
                return Text(
                  value.toInt().toString(),
                  style: const TextStyle(fontSize: 10, color: Colors.black87),
                );
              },
            ),
          ),
          rightTitles: AxisTitles(sideTitles: SideTitles(showTitles: false)),
          topTitles: AxisTitles(sideTitles: SideTitles(showTitles: false)),
        ),
        borderData: FlBorderData(
          show: false,
        ),
        gridData: FlGridData(
          show: true,
          drawVerticalLine: false,
          horizontalInterval: (maxValue - minValue) / 5,
          getDrawingHorizontalLine: (value) => FlLine(
            color: Colors.grey[200],
            strokeWidth: 1,
          ),
        ),
      ),
    );
  }

  Widget _buildScatterChart(List<_ScatterData> data, Color color) {
    final maxX = data.map((e) => e.x).reduce((a, b) => a > b ? a : b);
    final minX = data.map((e) => e.x).reduce((a, b) => a < b ? a : b);
    final maxY = data.map((e) => e.y).reduce((a, b) => a > b ? a : b);
    final minY = data.map((e) => e.y).reduce((a, b) => a < b ? a : b);
    
    return ScatterChart(
      ScatterChartData(
        scatterSpots: data
            .map((item) => ScatterSpot(
                  item.x,
                  item.y,
                ))
            .toList(),
        minX: minX - 0.1,
        maxX: maxX + 0.1,
        minY: minY - 5,
        maxY: maxY + 5,
        scatterTouchData: ScatterTouchData(
          enabled: false, // Desativado o toque
        ),
        titlesData: FlTitlesData(
          show: true,
          bottomTitles: AxisTitles(
            sideTitles: SideTitles(
              showTitles: true,
              reservedSize: 30,
              getTitlesWidget: (value, meta) {
                return Text(
                  value.toStringAsFixed(1),
                  style: const TextStyle(fontSize: 10, color: Colors.black87),
                );
              },
            ),
          ),
          leftTitles: AxisTitles(
            sideTitles: SideTitles(
              showTitles: true,
              reservedSize: 40,
              getTitlesWidget: (value, meta) {
                return Text(
                  value.toInt().toString(),
                  style: const TextStyle(fontSize: 10, color: Colors.black87),
                );
              },
            ),
          ),
          rightTitles: AxisTitles(sideTitles: SideTitles(showTitles: false)),
          topTitles: AxisTitles(sideTitles: SideTitles(showTitles: false)),
        ),
        borderData: FlBorderData(
          show: false,
        ),
        gridData: FlGridData(
          show: true,
          drawHorizontalLine: true,
          drawVerticalLine: true,
          horizontalInterval: (maxY - minY) / 5,
          verticalInterval: (maxX - minX) / 5,
          getDrawingHorizontalLine: (value) => FlLine(
            color: Colors.grey[200],
            strokeWidth: 1,
          ),
          getDrawingVerticalLine: (value) => FlLine(
            color: Colors.grey[200],
            strokeWidth: 1,
          ),
        ),
      ),
    );
  }

  Widget _drawerItem({
    required IconData icon,
    required String text,
    VoidCallback? onTap,
    Color color = const Color(0xFF007701),
  }) {
    return ListTile(
      leading: Icon(icon, color: color),
      title: Text(
        text,
        style: TextStyle(fontSize: 16, color: color),
      ),
      onTap: onTap,
      contentPadding: const EdgeInsets.symmetric(horizontal: 20),
    );
  }
}

// Data classes for charts
class _ChartData {
  final String label;
  final int value;

  _ChartData(this.label, this.value);
}

class _TimeSeriesData {
  final DateTime time;
  final int value;

  _TimeSeriesData(this.time, this.value);
}

class _ScatterData {
  final double x;
  final double y;

  _ScatterData(this.x, this.y);
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
              icon: Icon(icon, size: 30, color: Color(0xFF007701)),
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