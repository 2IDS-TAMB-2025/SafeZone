import 'package:flutter/material.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:tcc_safe_zone/pages/dados_cadastrados.dart';
import 'package:tcc_safe_zone/pages/criar.conta.dart';
import 'package:tcc_safe_zone/pages/galeria.dart';
import 'package:tcc_safe_zone/pages/graficos.dart';
import 'package:tcc_safe_zone/pages/principal.dart';
import 'package:tcc_safe_zone/controllers/auth_controller.dart';
import 'package:tcc_safe_zone/services/photo_service.dart';
import 'package:tcc_safe_zone/widgets/profile_avatar_widget.dart';

const double FONT_TITULO = 18;
const double FONT_SUBTITULO = 16;

class MapaPage extends StatefulWidget {
  final String usuario;

  const MapaPage({required this.usuario, Key? key}) : super(key: key);

  @override
  State<MapaPage> createState() => _MapaPageState();
}

class _MapaPageState extends State<MapaPage> {
  late MapController _mapController;
  LatLng? _currentLocation;
  final List<Map<String, dynamic>> _safeZones = [
    {"local": "Zona Segura Acre: Jordão", "lati": -9.190885289471673, "long": -71.95037545063767},
    {"local": "Zona Segura Alagoas: Flexeiras", "lati": -9.277225651526502, "long": -35.723561935035804},
    {"local": "Zona Segura Bahia: Piatã", "lati": -13.151124835434377, "long": -41.77586185798134},
    {"local": "Zona Segura Amapá: Serra do Navio", "lati": 0.9018802442680867, "long": -52.00287972395402},
    {"local": "Zona Segura Amazonas: Santa Isabel do Rio Negro", "lati": -0.4148546771374403, "long": -65.01591178601711},
    {"local": "Zona Segura Ceará: Quixadá", "lati": -4.968379861365324, "long": -39.01700414680048},
    {"local": "Zona Segura Espiríto Santo: Ponto Belo", "lati": -18.12250578841321, "long": -40.538457086148846},
    {"local": "Zona Segura Góias: Santo Antônio do Descoberto", "lati": -15.951383747314996, "long": -48.28055893578819},
    {"local": "Zona Segura Maranhão: Alcântara", "lati": -2.403951418303883, "long": -44.41565062016952},
    {"local": "Zona Segura Mato Grosso: Tangará da Serra", "lati": -14.620507094091675, "long": -57.48837121979413},
    {"local": "Zona Segura Mato Grosso do Sul: Maracaju", "lati": -21.630563111000367, "long": -55.15983364581099},
    {"local": "Zona Segura Minas Gerais: Uberlândia", "lati": -18.914583700976312, "long": -48.28051746285704},
    {"local": "Zona Segura Pará: Santa Izabel do Pará", "lati": -1.2975324518270486, "long": -48.163018194661326},
    {"local": "Zona Segura Paraíba: Monteiro", "lati": -7.891702929352078, "long": -37.12456284353066},
    {"local": "Zona Segura Paraná: Maringá", "lati": -23.425949323032953, "long": -51.927373568411944},
    {"local": "Zona Segura Pernanbuco: Toritama", "lati": -8.007526529256486, "long": -36.060382739774376},
    {"local": "Zona Segura Piauí: Altos", "lati": -5.040069381145025, "long": -42.4598231813796},
    {"local": "Zona Segura Rio de Janeiro: Belford Roxo", "lati": -22.759841970548262, "long": -43.40234137732956},
    {"local": "Zona Segura Rio Grande do Norte: Natal", "lati": -5.783820172078949, "long": -35.200239032073746},
    {"local": "Zona Segura Rio Grande do Sul: Caxias do Sul", "lati": -29.1738775972273, "long": -51.19308223922427},
    {"local": "Zona Segura Rondônia: Porto Velho", "lati": -8.761287198449097, "long": -63.90023015847645},
    {"local": "Zona Segura Roraima: Boa Vista", "lati": 2.8222840772152176, "long": -60.68043008800633},
    {"local": "Zona Segura Santa Catarina: Florianópolis", "lati": -27.589464850381376, "long": -48.54862454013866},
    {"local": "Zona Segura São Paulo: São Paulo", "lati": -23.553720971375103, "long": -46.61596888173541},
    {"local": "Zona Segura Sergipe: Aracaju", "lati": -10.924396199577323, "long": -37.071199789702234},
    {"local": "Zona Segura Tocantins: Palmas", "lati": -10.24856278976787, "long": -48.32183315132372},
    {"local": "Zona Segura Distrito Federal: Brasília", "lati": -15.798804422472202, "long": -47.8989241084052},
  ];

  final List<Map<String, String>> _citiesWithRisks = [
    {"city": "Petrópolis (RJ)", "risk": "Chuvas intensas e deslizamentos", "safeZone": "Valparaíso e centro plano"},
    {"city": "Belo Horizonte (MG)", "risk": "Alagamentos e encostas instáveis", "safeZone": "Lourdes, Savassi, Funcionários"},
    {"city": "Recife (PE)", "risk": "Enchentes e desabamentos", "safeZone": "Boa Viagem, Graças, Ilha do Leite"},
    {"city": "Salvador (BA)", "risk": "Deslizamentos em morros", "safeZone": "Pituba, Itaigara, Caminho das Árvores"},
    {"city": "São Paulo (SP)", "risk": "Alagamentos na ZL e ZS", "safeZone": "Pinheiros, Butantã, Centro expandido"},
  ];

  final List<Map<String, String>> _safeZonesNearby = [
    {"city": "Petrópolis (RJ)", "safeZone": "Valparaíso e áreas centrais mais planas", "reason": "Melhor drenagem, relevo menos acidentado, infraestrutura consolidada"},
    {"city": "Belo Horizonte (MG)", "safeZone": "Lourdes, Savassi, Funcionários", "reason": "Terreno estável, longe de encostas"},
    {"city": "Recife (PE)", "safeZone": "Boa Viagem, Ilha do Leite, Graças", "reason": "Áreas planas com boa infraestrutura"},
    {"city": "Salvador (BA)", "safeZone": "Pituba, Caminho das Árvores, Itaigara", "reason": "Fora de encostas, bem urbanizadas"},
    {"city": "São Paulo (SP)", "safeZone": "Zona Oeste (Pinheiros, Butantã), Centro expandido", "reason": "Menos enchentes comparado à Zona Leste/Sul"},
    {"city": "Rio de Janeiro (RJ)", "safeZone": "Barra da Tijuca, Copacabana, Ipanema", "reason": "Planas, com infraestrutura de drenagem e contenção"},
    {"city": "Blumenau (SC)", "safeZone": "Garcia (parte alta), Escola Agrícola", "reason": "Menos sujeitos à cheia do Rio Itajaí-Açu"},
    {"city": "Manaus (AM)", "safeZone": "Adrianópolis, Parque Dez, Aleixo", "reason": "Fora das áreas ribeirinhas, boa elevação"},
  ];

  @override
  void initState() {
    super.initState();
    _mapController = MapController();
    _currentLocation = const LatLng(-23.5505, -46.6333); 
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      drawer: SizedBox(
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
                      Navigator.push(context, MaterialPageRoute(
                          builder: (context) => PrincipalPage(usuario: widget.usuario)));
                    }),
                    _drawerItem(icon: Icons.map, text: 'Mapas', onTap: () {}),
                    _drawerItem(icon: Icons.bar_chart, text: 'Gráficos', onTap: () {
                      Navigator.push(context, MaterialPageRoute(
                          builder: (context) => GraficosPage(usuario: widget.usuario)));
                    }),
                    _drawerItem(icon: Icons.photo_library, text: 'Galeria', onTap: () {
                      Navigator.push(context, MaterialPageRoute(
                          builder: (context) => GaleriaPage(usuario: widget.usuario)));
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
                        Navigator.push(context, MaterialPageRoute(
                            builder: (context) => const LoginPage()));
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
                        CircleIcon(
                          icon: Icons.home,
                          label: 'Início',
                          onPressed: () {
                            Navigator.push(context, MaterialPageRoute(
                                builder: (context) => PrincipalPage(usuario: widget.usuario)));
                          }),
                        CircleIcon(
                          icon: Icons.map,
                          label: 'Mapas',
                          onPressed: () {}),
                        CircleIcon(
                          icon: Icons.bar_chart,
                          label: 'Gráficos',
                          onPressed: () {
                            Navigator.push(context, MaterialPageRoute(
                                builder: (context) => GraficosPage(usuario: widget.usuario)));
                          }),
                        CircleIcon(
                          icon: Icons.photo_library,
                          label: 'Galeria',
                          onPressed: () {
                            Navigator.push(context, MaterialPageRoute(
                                builder: (context) => GaleriaPage(usuario: widget.usuario)));
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
        child: Column(
          children: [
            SizedBox(
              height: 400,
              child: _currentLocation == null
                  ? const Center(child: CircularProgressIndicator())
                  : FlutterMap(
                      mapController: _mapController,
                      options: MapOptions(
                        center: _currentLocation,
                        zoom: 13.0,
                      ),
                      children: [
                        TileLayer(
                          urlTemplate: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                          subdomains: const ['a', 'b', 'c'],
                        ),
                        MarkerLayer(
                          markers: [
                            Marker(
                              point: _currentLocation!,
                              width: 80,
                              height: 80,
                              builder: (ctx) => const Icon(
                                Icons.location_on,
                                color: Colors.red,
                                size: 40,
                              ),
                            ),
                            ..._safeZones.map((zone) => Marker(
                                  point: LatLng(zone['lati'], zone['long']),
                                  width: 80,
                                  height: 80,
                                  builder: (ctx) => const Icon(
                                    Icons.location_on,
                                    color: Colors.green,
                                    size: 40,
                                  ),
                                )),
                          ],
                        ),
                      ],
                    ),
            ),
            const Padding(
              padding: EdgeInsets.all(16.0),
              child: Text(
                'Cidades com Riscos Ambientais e Zonas Seguras',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                textAlign: TextAlign.center,
              ),
            ),
            ListView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              itemCount: _citiesWithRisks.length,
              itemBuilder: (context, index) {
                return _buildRiskCard(_citiesWithRisks[index]);
              },
            ),
            const Padding(
              padding: EdgeInsets.all(16.0),
              child: Text(
                'Zonas mais seguras próximas a cidades de alto risco',
                style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold),
                textAlign: TextAlign.center,
              ),
            ),
            ListView.builder(
              shrinkWrap: true,
              physics: const NeverScrollableScrollPhysics(),
              itemCount: _safeZonesNearby.length,
              itemBuilder: (context, index) {
                return _buildSafeZoneCard(_safeZonesNearby[index]);
              },
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildRiskCard(Map<String, String> cityData) {
    return Container(
      width: double.infinity,
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(10),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.3),
            spreadRadius: 2,
            blurRadius: 5,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            cityData['city']!,
            style: const TextStyle(
              fontWeight: FontWeight.bold,
              fontSize: 18,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            'Riscos: ${cityData['risk']}',
            style: const TextStyle(fontSize: 16),
          ),
          const SizedBox(height: 8),
          Text(
            'Zona segura: ${cityData['safeZone']}',
            style: const TextStyle(fontSize: 16),
          ),
        ],
      ),
    );
  }

  Widget _buildSafeZoneCard(Map<String, String> zoneData) {
    return Container(
      width: double.infinity,
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(10),
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.3),
            spreadRadius: 2,
            blurRadius: 5,
            offset: const Offset(0, 3),
          ),
        ],
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            zoneData['city']!,
            style: const TextStyle(
              fontWeight: FontWeight.bold,
              fontSize: 18,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            'Zona segura: ${zoneData['safeZone']}',
            style: const TextStyle(fontSize: 16),
          ),
          const SizedBox(height: 8),
          Text(
            'Por quê? ${zoneData['reason']}',
            style: const TextStyle(fontSize: 16),
          ),
        ],
      ),
    );
  }
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