import 'package:flutter/material.dart';
import 'package:tcc_safe_zone/pages/criar.conta.dart';
import 'package:tcc_safe_zone/pages/principal.dart';
import 'package:tcc_safe_zone/controllers/auth_controller.dart';

void main() async {
  WidgetsFlutterBinding.ensureInitialized();
  // REMOVIDO: await PhotoService.initialize();
  runApp(const MainApp());
}

class MainApp extends StatelessWidget {
  const MainApp({super.key});

  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      home: const SplashScreen(),
    );
  }
}

class SplashScreen extends StatefulWidget {
  const SplashScreen({super.key});

  @override
  State<SplashScreen> createState() => _SplashScreenState();
}

class _SplashScreenState extends State<SplashScreen> {
  @override
  void initState() {
    super.initState();

    Future.delayed(const Duration(seconds: 4), () {
      // Verifica se já existe um usuário logado (auto-login bem-sucedido)
      if (AuthController().usuarioLogado != null) {
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(
            builder: (context) => PrincipalPage(usuario: AuthController().nomeExibicao),
          ),
        );
      } else {
        // Se não há usuário logado, vai para a tela de login
        Navigator.pushReplacement(
          context,
          MaterialPageRoute(builder: (context) => const LoginPage()),
        );
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: DecoratedBox(
        decoration: BoxDecoration(
          gradient: LinearGradient(
            colors: [
              Color(0xFF007701),
              Color(0xFF006600),
            ],
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
          ),
        ),
        child: Stack(
          children: [
            Center(
              child:Column(
                children: [
                  SizedBox(height: 200),
                  Image.asset(
                    'assets/images/logo2.png',
                    width: 110,
                    ),
                  Image.asset(
                    'assets/images/logo.png',
                    width: 250,
                  ),
                ],
              ),
            ),
            const Align(
              alignment: Alignment.bottomCenter,
              child: Padding(
                padding: EdgeInsets.only(bottom: 16),
                child: Text(
                  'V1.0.0',
                  style: TextStyle(
                    color: Colors.white70,
                    fontSize: 16,
                    fontWeight: FontWeight.w400,
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}