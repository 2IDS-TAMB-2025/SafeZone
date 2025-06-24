import 'package:flutter/material.dart';
import 'package:tcc_safe_zone/pages/admin.dart';
import 'package:tcc_safe_zone/pages/ajuda.dart';
import 'package:tcc_safe_zone/pages/config.dart';
import 'package:tcc_safe_zone/pages/criar.conta.dart';
import 'package:tcc_safe_zone/pages/graficos.dart';
import 'package:tcc_safe_zone/pages/mapa.dart';
import 'package:tcc_safe_zone/pages/principal.dart';

class GaleriaPage extends StatelessWidget {
  final String usuario;

  GaleriaPage({required this.usuario});

  final List<String> imageUrls = [
    'assets/images/1.png',
    'assets/images/2.png',
    'assets/images/3.png',
    'assets/images/4.png',
    'assets/images/5.png',
    'assets/images/6.png',
    'assets/images/7.png',
    'assets/images/8.png',
    'assets/images/9.png',
  ];

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
                    _drawerItem(icon: Icons.home, text: 'Início', onTap: () {
                      Navigator.push(context, MaterialPageRoute(builder: (context) => PrincipalPage(usuario: usuario)));
                    }),
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
                    _drawerItem(icon: Icons.photo_library, text: 'Galeria', onTap: () {}),
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
                        CircleIcon(icon: Icons.photo_library, label: 'Galeria', onPressed: () {}),
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
        child: Container(
          width: double.infinity,
          padding: const EdgeInsets.all(16),
          color: Colors.white,
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              const SizedBox(height: 20),
              const Text(
                'Galeria de Imagens',
                style: TextStyle(
                  fontSize: 24,
                  fontWeight: FontWeight.bold,
                ),
              ),
              const SizedBox(height: 10),
              ListView.builder(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                itemCount: imageUrls.length,
                itemBuilder: (context, index) {
                  return GestureDetector(
                    onTap: () {
                      Navigator.push(
                        context,
                        MaterialPageRoute(
                          builder: (_) => ImageFullScreen(imagePath: imageUrls[index]),
                        ),
                      );
                    },
                    child: Container(
                      margin: const EdgeInsets.symmetric(vertical: 8),
                      child: ClipRRect(
                        borderRadius: BorderRadius.circular(8),
                        child: Image.asset(
                          imageUrls[index],
                          fit: BoxFit.cover,
                        ),
                      ),
                    ),
                  );
                },
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildContactAction({
    required IconData icon,
    required String title,
    required String subtitle,
    required VoidCallback onTap,
  }) {
    return Container(
      width: 240,
      height: 100,
      padding: const EdgeInsets.all(12),
      margin: const EdgeInsets.only(right: 12),
      decoration: BoxDecoration(
        color: const Color(0xFF007701),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.center,
        children: [
          GestureDetector(
            onTap: onTap,
            child: Icon(
              icon,
              color: Colors.white,
              size: 30,
            ),
          ),
          const SizedBox(width: 12),
          Expanded(
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  title,
                  style: const TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.bold,
                    fontSize: 18,
                  ),
                  overflow: TextOverflow.ellipsis,
                ),
                const SizedBox(height: 4),
                Text(
                  subtitle,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 16,
                  ),
                  overflow: TextOverflow.ellipsis,
                  maxLines: 2,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class ImageFullScreen extends StatelessWidget {
  final String imagePath;

  const ImageFullScreen({Key? key, required this.imagePath}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.black,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
      ),
      body: Center(
        child: InteractiveViewer(
          child: Image.asset(imagePath),
        ),
      ),
      floatingActionButton: Container(
        width: 60,
        height: 60,
        decoration: BoxDecoration(
          color: Colors.red,
          shape: BoxShape.circle,
          boxShadow: [
            BoxShadow(
              color: Colors.black26,
              blurRadius: 6,
              offset: Offset(0, 3),
            ),
          ],
        ),
        child: IconButton(
          icon: Icon(Icons.warning, color: Colors.white, size: 32),
          onPressed: () {
            showDialog(
              context: context,
              builder: (BuildContext context) {
                return AlertDialog(
                  backgroundColor: Colors.white,
                  shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
                  content: Column(
                    mainAxisSize: MainAxisSize.min,
                    children: const [
                      Icon(Icons.warning, size: 48, color: Colors.red),
                      SizedBox(height: 16),
                      Text(
                        'Emergência',
                        style: TextStyle(
                          color: Colors.red,
                          fontWeight: FontWeight.bold,
                          fontSize: 22,
                        ),
                        textAlign: TextAlign.center,
                      ),
                      SizedBox(height: 12),
                      Text(
                        'Deseja ligar para a emergência?',
                        style: TextStyle(fontSize: 16),
                        textAlign: TextAlign.center,
                      ),
                    ],
                  ),
                  actionsAlignment: MainAxisAlignment.center,
                  actions: [
                    TextButton(
                      onPressed: () {
                        Navigator.of(context).pop();
                      },
                      style: TextButton.styleFrom(
                        backgroundColor: Color(0xFF007701),
                        foregroundColor: Colors.white,
                      ),
                      child: const Text('SIM'),
                    ),
                    TextButton(
                      onPressed: () => Navigator.of(context).pop(),
                      style: TextButton.styleFrom(
                        backgroundColor: Colors.red,
                        foregroundColor: Colors.white,
                      ),
                      child: const Text('NÃO'),
                    ),
                  ],
                );
              },
            );
          },
        ),
      ),
      floatingActionButtonLocation: FloatingActionButtonLocation.centerDocked,
      bottomNavigationBar: BottomAppBar(
        shape: CircularNotchedRectangle(),
        notchMargin: 10,
        color: Color(0xFF007701),
        elevation: 10,
        child: const SizedBox(height: 20),
      ),
    );
  }
}

class CircleIcon extends StatelessWidget {
  final IconData icon;
  final String label;
  final VoidCallback onPressed;

  const CircleIcon({
    Key? key,
    required this.icon,
    required this.label,
    required this.onPressed,
  }) : super(key: key);

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
    title: Text(
      text,
      style: TextStyle(fontSize: 16, color: color),
    ),
    onTap: onTap,
    contentPadding: const EdgeInsets.symmetric(horizontal: 20),
  );
}