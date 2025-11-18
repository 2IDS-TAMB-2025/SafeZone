import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import '../models/usuario_model.dart';
import 'api_controller.dart';

class AuthController {
  static Usuario? _usuarioLogado;
  static final AuthController _instance = AuthController._internal();
  
  factory AuthController() {
    return _instance;
  }

  AuthController._internal();

  // Getter para o usuário logado
  Usuario? get usuarioLogado => _usuarioLogado;

  // Getter para nome de exibição
  String get nomeExibicao {
    if (_usuarioLogado != null) {
      return _usuarioLogado!.nome;
    }
    return 'Usuário';
  }

  // Login
  Future<bool> login(String email, String senha) async {
    try {
      final response = await ApiController.login(email, senha);
      
      if (response['usuario'] != null) {
        _usuarioLogado = Usuario.fromJson(response['usuario']);
        
        // Salvar dados de login localmente
        final prefs = await SharedPreferences.getInstance();
        await prefs.setString('user_email', email);
        await prefs.setString('user_data', jsonEncode(response['usuario']));
        
        return true;
      }
      return false;
    } catch (e) {
      print('Erro no login: $e');
      return false;
    }
  }

  // Tentar auto-login
  Future<bool> tryAutoLogin() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final userData = prefs.getString('user_data');
      
      if (userData != null) {
        final usuarioMap = jsonDecode(userData);
        _usuarioLogado = Usuario.fromJson(usuarioMap);
        return true;
      }
      return false;
    } catch (e) {
      print('Erro no auto-login: $e');
      return false;
    }
  }

  // Logout
  Future<void> logout() async {
    _usuarioLogado = null;
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('user_email');
    await prefs.remove('user_data');
  }

  // Verificar se está logado
  bool get isLoggedIn => _usuarioLogado != null;
}