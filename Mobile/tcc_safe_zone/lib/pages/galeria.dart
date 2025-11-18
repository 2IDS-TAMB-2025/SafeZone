import 'package:flutter/material.dart';
import 'package:tcc_safe_zone/pages/dados_cadastrados.dart';
import 'package:tcc_safe_zone/pages/criar.conta.dart';
import 'package:tcc_safe_zone/pages/graficos.dart';
import 'package:tcc_safe_zone/pages/mapa.dart';
import 'package:tcc_safe_zone/pages/principal.dart';
import 'package:tcc_safe_zone/controllers/auth_controller.dart';
import 'package:tcc_safe_zone/services/photo_service.dart';
import 'package:tcc_safe_zone/widgets/profile_avatar_widget.dart';

class GaleriaPage extends StatelessWidget {
  final String usuario;

  GaleriaPage({required this.usuario, Key? key}) : super(key: key);

  final List<String> imageUrls = [
    'assets/images/1.png',
    'assets/images/2.png',
    'assets/images/3.png',
    'assets/images/4.png',
    'assets/images/5.png',
    'assets/images/6.png',
    'assets/images/7.png',
    'assets/images/8.png',
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
              GridView.builder(
                shrinkWrap: true,
                physics: const NeverScrollableScrollPhysics(),
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 2,
                  crossAxisSpacing: 10,
                  mainAxisSpacing: 10,
                  childAspectRatio: 0.8,
                ),
                itemCount: imageUrls.length,
                itemBuilder: (context, index) {
                  return GestureDetector(
                    onTap: () {},
                    child: Container(
                      decoration: BoxDecoration(
                        borderRadius: BorderRadius.circular(12),
                        boxShadow: [
                          BoxShadow(
                            color: Colors.grey.withOpacity(0.3),
                            spreadRadius: 2,
                            blurRadius: 5,
                            offset: const Offset(0, 3),
                          ),
                        ],
                      ),
                      child: ClipRRect(
                        borderRadius: BorderRadius.circular(12),
                        child: Image.asset(
                          imageUrls[index],
                          fit: BoxFit.cover,
                          width: double.infinity,
                          height: double.infinity,
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