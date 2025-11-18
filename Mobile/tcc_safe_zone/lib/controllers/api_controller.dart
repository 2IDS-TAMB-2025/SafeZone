import 'dart:convert';
import 'package:http/http.dart' as http;
import '../models/usuario_model.dart';

class ApiController {
  static const String baseUrl = 'http://10.141.128.126/safe-zone-api/public';
  
  static Map<String, String> get headers => {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
  };

  // Testar se API está respondendo
  static Future<bool> testarAPI() async {
    try {
      final response = await http.get(
        Uri.parse('$baseUrl/'),
        headers: headers,
      ).timeout(Duration(seconds: 5));
      
      print('Teste API - Status: ${response.statusCode}');
      print('Teste API - Body: ${response.body}');
      
      return response.statusCode == 200;
    } catch (e) {
      print('Erro teste API: $e');
      return false;
    }
  }

  // Cadastrar usuário
  static Future<Usuario> cadastrarUsuario(Usuario usuario) async {
    try {
      print('📡 Enviando para: $baseUrl/usuarios');
      
      final response = await http.post(
        Uri.parse('$baseUrl/usuarios'),
        headers: headers,
        body: jsonEncode(usuario.toJson()),
      ).timeout(Duration(seconds: 10));

      print('📡 Status: ${response.statusCode}');
      print('📡 Resposta: ${response.body}');

      if (response.statusCode == 201) {
        final responseData = jsonDecode(response.body);
        return Usuario.fromJson(responseData['usuario']);
      } else {
        final errorData = jsonDecode(response.body);
        throw Exception(errorData['message'] ?? 'Erro ${response.statusCode}');
      }
    } catch (e) {
      print('❌ Erro completo: $e');
      throw Exception('Falha na comunicação: $e');
    }
  }

  // Login
  static Future<Map<String, dynamic>> login(String email, String senha) async {
    try {
      final response = await http.post(
        Uri.parse('$baseUrl/login'),
        headers: headers,
        body: jsonEncode({'EMAIL': email, 'SENHA': senha}),
      ).timeout(Duration(seconds: 10));

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      } else {
        final errorData = jsonDecode(response.body);
        throw Exception(errorData['message'] ?? 'Erro ${response.statusCode}');
      }
    } catch (e) {
      throw Exception('Falha na comunicação: $e');
    }
  }

  // ATUALIZAR USUÁRIO - NOVO MÉTODO
  static Future<Usuario> atualizarUsuario(Map<String, dynamic> dados) async {
    try {
      print('📡 Enviando atualização para: $baseUrl/usuarios');
      
      final response = await http.put(
        Uri.parse('$baseUrl/usuarios'),
        headers: headers,
        body: jsonEncode(dados),
      ).timeout(Duration(seconds: 10));

      print('📡 Status: ${response.statusCode}');
      print('📡 Resposta: ${response.body}');

      if (response.statusCode == 200) {
        final responseData = jsonDecode(response.body);
        return Usuario.fromJson(responseData['usuario']);
      } else {
        final errorData = jsonDecode(response.body);
        throw Exception(errorData['message'] ?? 'Erro ${response.statusCode}');
      }
    } catch (e) {
      print('❌ Erro ao atualizar usuário: $e');
      throw Exception('Falha na comunicação: $e');
    }
  }
}