import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:tcc_safe_zone/pages/dados_cadastrados.dart';
import 'package:tcc_safe_zone/pages/criar.conta.dart';
import 'package:tcc_safe_zone/pages/galeria.dart';
import 'package:tcc_safe_zone/pages/graficos.dart';
import 'package:tcc_safe_zone/pages/mapa.dart';
import 'package:tcc_safe_zone/controllers/auth_controller.dart';
import 'package:tcc_safe_zone/services/photo_service.dart';
import 'package:tcc_safe_zone/widgets/profile_avatar_widget.dart';

class PrincipalPage extends StatefulWidget {
  final String usuario;
  
  const PrincipalPage({Key? key, required this.usuario}) : super(key: key);

  @override
  State<PrincipalPage> createState() => _PrincipalPageState();
}

class _PrincipalPageState extends State<PrincipalPage> {
  @override
  void initState() {
    super.initState();
    // Garantir que a foto está carregada
    _loadPhoto();
  }

  void _loadPhoto() async {
    // Forçar o carregamento da foto atual
    final foto = await PhotoService.getUserPhotoWithFallback();
    setState(() {});
  }

  @override
  Widget build(BuildContext context) {
    final theme = Theme.of(context);
    final screenSize = MediaQuery.of(context).size;

    return Scaffold(
      backgroundColor: Colors.white,
      drawer: _buildDrawer(context),
      appBar: _buildAppBar(context),
      body: _buildBody(context, screenSize),
    );
  }

  Widget _buildDrawer(BuildContext context) {
    return SizedBox(
      width: 250,
      child: Drawer(
        backgroundColor: Colors.white,
        child: Column(
          children: [
            _buildDrawerHeader(context),
            Expanded(
              child: ListView(
                padding: EdgeInsets.zero,
                children: [
                  _drawerItem(icon: Icons.home, text: 'Início', onTap: () {
                    Navigator.push(context, MaterialPageRoute(builder: (context) => PrincipalPage(usuario: widget.usuario)));
                  }),
                  _drawerItem(
                    icon: Icons.map,
                    text: 'Mapas',
                    onTap: () {
                      Navigator.push(context, MaterialPageRoute(builder: (context) => MapaPage(usuario: widget.usuario)));
                    },
                  ),
                  _drawerItem(icon: Icons.bar_chart, text: 'Gráficos', onTap: () {
                    Navigator.push(context, MaterialPageRoute(builder: (context) => GraficosPage(usuario: widget.usuario)));
                  }),
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

  Widget _buildDrawerHeader(BuildContext context) {
    return DrawerHeader(
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
            // AVATAR COM FOTO - USANDO O NOVO WIDGET SIMPLIFICADO
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
                  ).then((_) {
                    // Quando voltar da tela de configurações, recarregar a foto
                    _loadPhoto();
                  });
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
    );
  }

  AppBar _buildAppBar(BuildContext context) {
    return AppBar(
      backgroundColor: const Color(0xFF007701),
      elevation: 0,
      leading: Builder(
        builder: (context) => IconButton(
          icon: const Icon(
            Icons.menu,
            color: Colors.white,
            size: 35,
          ),
          onPressed: () => Scaffold.of(context).openDrawer(),
        ),
      ),
      title: Image.asset(
        'assets/images/logo.png',
        width: 200,
      ),
      centerTitle: true,
      actions: [
        // ÍCONE DO PERFIL - USANDO O NOVO WIDGET SIMPLIFICADO
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
              ).then((_) {
                // Quando voltar da tela de configurações, recarregar a foto
                _loadPhoto();
              });
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
          child: Column(
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
              _buildQuickActions(context),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildQuickActions(BuildContext context) {
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      child: Row(
        children: [
          _buildCircleIcon(
            icon: Icons.home,
            label: 'Início',
            onPressed: () {},
          ),
          _buildCircleIcon(
            icon: Icons.map,
            label: 'Mapas',
            onPressed: () => _navigateTo(context, MapaPage(usuario: widget.usuario)),
          ),
          _buildCircleIcon(
            icon: Icons.bar_chart,
            label: 'Gráficos',
            onPressed: () => _navigateTo(context, GraficosPage(usuario: widget.usuario)),
          ),
          _buildCircleIcon(
            icon: Icons.photo_library,
            label: 'Galeria',
            onPressed: () => _navigateTo(context, GaleriaPage(usuario: widget.usuario)),
          ),
        ],
      ),
    );
  }

  Widget _buildCircleIcon({
    required IconData icon,
    required String label,
    required VoidCallback onPressed,
  }) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 12),
      child: Column(
        children: [
          CircleAvatar(
            radius: 30,
            backgroundColor: Colors.white,
            child: IconButton(
              icon: Icon(
                icon,
                size: 30,
                color: const Color(0xFF007701),
              ),
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

  Widget _buildBody(BuildContext context, Size screenSize) {
    return SingleChildScrollView(
      child: Container(
        width: double.infinity,
        padding: const EdgeInsets.all(16),
        color: Colors.white,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            _buildQuickContactActions(),
            const SizedBox(height: 20),
            _buildMissionStatement(),
            const SizedBox(height: 20),
            _buildInfoBlocks(),
          ],
        ),
      ),
    );
  }

  Widget _buildQuickContactActions() {
    return SingleChildScrollView(
      scrollDirection: Axis.horizontal,
      child: Row(
        children: [
          _buildContactAction(
            icon: Icons.phone,
            title: 'Ligue para nós:',
            subtitle: '+55 (19) 9935-7890',
            onTap: () {},
          ),
          _buildContactAction(
            icon: Icons.email,
            title: 'Enviar E-mail:',
            subtitle: 'safezone@hotmail.com',
            onTap: () {},
          ),
          _buildContactAction(
            icon: Icons.access_time,
            title: 'Seg. - Sáb.:',
            subtitle: '08:00 - 18:00',
            onTap: () {},
          ),
          _buildContactAction(
            icon: Icons.location_on,
            title: 'Venha nos visitar:',
            subtitle: 'Safe Zone, 123 - Centro, Tambaú',
            onTap: () {},
          ),
        ],
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

  Widget _buildMissionStatement() {
    return const Text(
      'Monitorar, Prevenir E Preservar: Nosso Compromisso Com O Meio Ambiente.',
      style: TextStyle(
        fontSize: 18,
        fontWeight: FontWeight.w700,
      ),
      textAlign: TextAlign.center,
    );
  }

  Widget _buildInfoBlocks() {
    return Column(
      children: const [
        InfoBlock(
          icon: Icons.factory,
          title: 'Poluição Do Ar',
          description: 'Conscientizar sobre seus impactos e incentivar ações por um ar mais limpo.',
        ),
        InfoBlock(
          icon: Icons.park,
          title: 'Desmatamento',
          description: 'Alertar sobre os danos às florestas e promover sua preservação.',
        ),
        InfoBlock(
          icon: Icons.local_fire_department,
          title: 'Incêndios Florestais',
          description: 'Informar sobre causas e prevenir queimadas com respeito à natureza.',
        ),
        InfoBlock(
          icon: Icons.pets,
          title: 'Fauna Em Risco',
          description: 'Proteger a vida animal e estimular a preservação das espécies.',
        ),
        InfoBlock(
          icon: Icons.warning,
          title: 'Áreas De Risco',
          description: 'Mostrar os perigos da ocupação desordenada e defender soluções.',
        ),
        InfoBlock(
          icon: Icons.eco,
          title: 'Soluções Sustentáveis',
          description: 'Promover atitudes que contribuam para um futuro mais equilibrado.',
        ),
      ],
    );
  }

  void _navigateTo(BuildContext context, Widget page) {
    Navigator.push(context, MaterialPageRoute(builder: (context) => page));
  }
}

class InfoBlock extends StatelessWidget {
  final IconData icon;
  final String title;
  final String description;

  const InfoBlock({
    Key? key,
    required this.icon,
    required this.title,
    required this.description,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      width: MediaQuery.of(context).size.width - 32,
      padding: const EdgeInsets.all(12),
      margin: const EdgeInsets.symmetric(vertical: 6),
      decoration: BoxDecoration(
        border: Border.all(color: const Color(0xFF007701)),
        borderRadius: BorderRadius.circular(12),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Align(
            alignment: Alignment.topRight,
            child: Container(
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: const Color(0xFF007701),
                borderRadius: BorderRadius.circular(6),
              ),
              child: Icon(icon, color: Colors.white, size: 20),
            ),
          ),
          const SizedBox(height: 10),
          Text(
            title,
            style: const TextStyle(
              fontWeight: FontWeight.bold,
              fontSize: 18,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            description,
            style: const TextStyle(
              fontSize: 16,
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