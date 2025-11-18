import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:intl/intl.dart';
import 'package:tcc_safe_zone/pages/criar.conta.dart';
import '../controllers/api_controller.dart';
import '../models/usuario_model.dart';

class CadastroPage extends StatefulWidget {
  const CadastroPage({super.key});

  @override
  State<CadastroPage> createState() => _CadastroPageState();
}

class _CadastroPageState extends State<CadastroPage> {
  final _formKey = GlobalKey<FormState>();
  final TextEditingController _senhaController = TextEditingController();
  final TextEditingController _dataNascimentoController = TextEditingController();
  final TextEditingController _nomeController = TextEditingController();
  final TextEditingController _sobrenomeController = TextEditingController();
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _cpfController = TextEditingController();
  final TextEditingController _telefoneController = TextEditingController();
  
  bool _obscurePassword = true;
  bool _isLoading = false;
  DateTime? _dataNascimento;

  @override
  void initState() {
    super.initState();
    _dataNascimentoController.addListener(_updateDataNascimento);
  }

  @override
  void dispose() {
    _senhaController.dispose();
    _dataNascimentoController.removeListener(_updateDataNascimento);
    _dataNascimentoController.dispose();
    _nomeController.dispose();
    _sobrenomeController.dispose();
    _emailController.dispose();
    _cpfController.dispose();
    _telefoneController.dispose();
    super.dispose();
  }

  void _updateDataNascimento() {
    if (_dataNascimentoController.text.isNotEmpty) {
      try {
        _dataNascimento = DateFormat('dd/MM/yyyy').parse(_dataNascimentoController.text);
      } catch (e) {
        // Ignora erro de parsing
      }
    }
  }

  void _formatarDataNascimento(String value) {
    // Remove todos os caracteres não numéricos
    String digitsOnly = value.replaceAll(RegExp(r'[^\d]'), '');
    
    // Limita a 8 dígitos (DDMMYYYY)
    if (digitsOnly.length > 8) {
      digitsOnly = digitsOnly.substring(0, 8);
    }

    // Aplica a formatação
    String formatted = '';
    if (digitsOnly.length >= 1) {
      formatted = digitsOnly.substring(0, 1);
    }
    if (digitsOnly.length >= 2) {
      formatted = digitsOnly.substring(0, 2);
    }
    if (digitsOnly.length >= 3) {
      formatted = '${digitsOnly.substring(0, 2)}/${digitsOnly.substring(2, 3)}';
    }
    if (digitsOnly.length >= 4) {
      formatted = '${digitsOnly.substring(0, 2)}/${digitsOnly.substring(2, 4)}';
    }
    if (digitsOnly.length >= 5) {
      formatted = '${digitsOnly.substring(0, 2)}/${digitsOnly.substring(2, 4)}/${digitsOnly.substring(4, 5)}';
    }
    if (digitsOnly.length >= 6) {
      formatted = '${digitsOnly.substring(0, 2)}/${digitsOnly.substring(2, 4)}/${digitsOnly.substring(4, 6)}';
    }
    if (digitsOnly.length >= 7) {
      formatted = '${digitsOnly.substring(0, 2)}/${digitsOnly.substring(2, 4)}/${digitsOnly.substring(4, 7)}';
    }
    if (digitsOnly.length >= 8) {
      formatted = '${digitsOnly.substring(0, 2)}/${digitsOnly.substring(2, 4)}/${digitsOnly.substring(4, 8)}';
    }

    // Atualiza o controller sem disparar o listener recursivamente
    _dataNascimentoController.removeListener(_updateDataNascimento);
    _dataNascimentoController.value = TextEditingValue(
      text: formatted,
      selection: TextSelection.collapsed(offset: formatted.length),
    );
    _dataNascimentoController.addListener(_updateDataNascimento);

    // Atualiza a data
    _updateDataNascimento();
  }

  Future<void> _submitForm() async {
    if (_formKey.currentState!.validate()) {
      setState(() => _isLoading = true);
      
      try {
        // Formata a data de DD/MM/YYYY para YYYY-MM-DD (formato do banco)
        String dataNascimentoFormatada;
        if (_dataNascimento != null) {
          dataNascimentoFormatada = DateFormat('yyyy-MM-dd').format(_dataNascimento!);
        } else {
          // Tenta converter do formato brasileiro
          final parts = _dataNascimentoController.text.split('/');
          if (parts.length == 3) {
            dataNascimentoFormatada = '${parts[2]}-${parts[1]}-${parts[0]}';
          } else {
            throw Exception('Formato de data inválido');
          }
        }

        final usuario = Usuario(
          nome: _nomeController.text,
          sobrenome: _sobrenomeController.text,
          email: _emailController.text,
          dataNascimento: dataNascimentoFormatada,
          cpf: _cpfController.text,
          senha: _senhaController.text,
          telefoneCelular: _telefoneController.text,
        );

        final usuarioCadastrado = await ApiController.cadastrarUsuario(usuario);
        
        // MOSTRAR MENSAGEM DE SUCESSO
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: const Text('Cadastro realizado com sucesso!'),
            backgroundColor: Colors.green,
            duration: const Duration(seconds: 2),
          ),
        );
        
        // VOLTAR PARA TELA CRIAR.CONTA.DART APÓS UM PEQUENO DELAY
        await Future.delayed(const Duration(milliseconds: 1500));
        
        // Navegar de volta para a tela criar.conta.dart
        Navigator.pushAndRemoveUntil(
          context,
          MaterialPageRoute(builder: (context) => const LoginPage()),
          (route) => false, // Remove todas as rotas anteriores
        );
        
      } catch (e) {
        ScaffoldMessenger.of(context).showSnackBar(
          SnackBar(
            content: Text('Erro: $e'),
            backgroundColor: Colors.red,
          ),
        );
      } finally {
        setState(() => _isLoading = false);
      }
    }
  }

  String? _validarDataNascimento(String? value) {
    if (value == null || value.isEmpty) {
      return "Data de nascimento é obrigatória";
    }

    // Verifica se a data está completa (DD/MM/YYYY)
    if (value.length != 10) {
      return "Data incompleta (DD/MM/AAAA)";
    }

    try {
      final parts = value.split('/');
      if (parts.length != 3) {
        return "Formato inválido. Use DD/MM/AAAA";
      }

      final dia = int.parse(parts[0]);
      final mes = int.parse(parts[1]);
      final ano = int.parse(parts[2]);

      // Validações básicas
      if (dia < 1 || dia > 31) return "Dia inválido";
      if (mes < 1 || mes > 12) return "Mês inválido";
      if (ano < 1900 || ano > DateTime.now().year) return "Ano inválido";

      // Tenta criar a data para validação adicional
      final data = DateTime(ano, mes, dia);
      if (data.year != ano || data.month != mes || data.day != dia) {
        return "Data inválida";
      }

      // Verifica se é uma data futura
      if (data.isAfter(DateTime.now())) {
        return "Data não pode ser futura";
      }

      return null;
    } catch (e) {
      return "Data inválida. Use o formato DD/MM/AAAA";
    }
  }

  String? _validarCPF(String? value) {
    if (value == null || value.isEmpty) return "CPF é obrigatório";
    if (value.length != 11) return "CPF deve ter 11 dígitos";
    return null;
  }

  String? _validarTelefone(String? value) {
    if (value == null || value.isEmpty) return "Telefone é obrigatório";
    if (value.length < 10) return "Telefone deve ter pelo menos 10 dígitos";
    return null;
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Container(
        decoration: const BoxDecoration(
          gradient: LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [
              Color(0xFF007701),
              Color(0xFF006600),
            ],
          ),
        ),
        child: SafeArea(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(24),
            child: Column(
              children: [
                // Cabeçalho
                Column(
                  children: [
                    Image.asset(
                      'assets/images/logo2.png',
                      height: 80,
                      width: 80,
                      color: Colors.white.withOpacity(0.9),
                    ),
                    const SizedBox(height: 16),
                    const Text(
                      'Cadastro Completo',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 28,
                        fontWeight: FontWeight.bold,
                        letterSpacing: 1.2,
                      ),
                    ),
                    const SizedBox(height: 8),
                    const Text(
                      'Preencha todos os campos obrigatórios',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 16,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 32),
                
                // Formulário
                Container(
                  padding: const EdgeInsets.all(24),
                  decoration: BoxDecoration(
                    color: Colors.white.withOpacity(0.15),
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(
                      color: Colors.white.withOpacity(0.1),
                    ),
                  ),
                  child: Form(
                    key: _formKey,
                    child: Column(
                      children: [
                        // Nome
                        TextFormField(
                          controller: _nomeController,
                          style: const TextStyle(color: Colors.white),
                          decoration: InputDecoration(
                            labelText: 'Nome',
                            labelStyle: const TextStyle(color: Colors.white),
                            prefixIcon: const Icon(Icons.badge_outlined, color: Colors.white),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(8),
                              borderSide: BorderSide.none,
                            ),
                            filled: true,
                            fillColor: Colors.white.withOpacity(0.1),
                            contentPadding: const EdgeInsets.symmetric(
                              vertical: 16, horizontal: 16),
                          ),
                          validator: (value) =>
                              value == null || value.isEmpty ? "Nome é obrigatório" : null,
                        ),
                        const SizedBox(height: 16),
                        
                        // Sobrenome
                        TextFormField(
                          controller: _sobrenomeController,
                          style: const TextStyle(color: Colors.white),
                          decoration: InputDecoration(
                            labelText: 'Sobrenome',
                            labelStyle: const TextStyle(color: Colors.white),
                            prefixIcon: const Icon(Icons.badge_outlined, color: Colors.white),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(8),
                              borderSide: BorderSide.none,
                            ),
                            filled: true,
                            fillColor: Colors.white.withOpacity(0.1),
                            contentPadding: const EdgeInsets.symmetric(
                              vertical: 16, horizontal: 16),
                          ),
                          validator: (value) =>
                              value == null || value.isEmpty ? "Sobrenome é obrigatório" : null,
                        ),
                        const SizedBox(height: 16),
                        
                        // CPF
                        TextFormField(
                          controller: _cpfController,
                          style: const TextStyle(color: Colors.white),
                          decoration: InputDecoration(
                            labelText: 'CPF',
                            labelStyle: const TextStyle(color: Colors.white),
                            prefixIcon: const Icon(Icons.credit_card_outlined, color: Colors.white),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(8),
                              borderSide: BorderSide.none,
                            ),
                            filled: true,
                            fillColor: Colors.white.withOpacity(0.1),
                            contentPadding: const EdgeInsets.symmetric(
                              vertical: 16, horizontal: 16),
                          ),
                          inputFormatters: [
                            FilteringTextInputFormatter.digitsOnly,
                            LengthLimitingTextInputFormatter(11),
                          ],
                          keyboardType: TextInputType.number,
                          validator: _validarCPF,
                        ),
                        const SizedBox(height: 16),
                        
                        // Data de Nascimento (CAMPO DE TEXTO NORMAL)
                        TextFormField(
                          controller: _dataNascimentoController,
                          style: const TextStyle(color: Colors.white),
                          decoration: InputDecoration(
                            labelText: 'Data de Nascimento',
                            labelStyle: const TextStyle(color: Colors.white),
                            prefixIcon: const Icon(Icons.calendar_today_outlined, color: Colors.white),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(8),
                              borderSide: BorderSide.none,
                            ),
                            filled: true,
                            fillColor: Colors.white.withOpacity(0.1),
                            contentPadding: const EdgeInsets.symmetric(
                              vertical: 16, horizontal: 16),
                            hintText: 'DD/MM/AAAA',
                            hintStyle: TextStyle(color: Colors.white.withOpacity(0.6)),
                          ),
                          // Campo de texto normal - sem teclado numérico específico
                          keyboardType: TextInputType.text,
                          inputFormatters: [
                            // Permite apenas números e a barra
                            FilteringTextInputFormatter.allow(RegExp(r'[\d/]')),
                            LengthLimitingTextInputFormatter(10), // DD/MM/YYYY = 10 caracteres
                          ],
                          onChanged: _formatarDataNascimento,
                          validator: _validarDataNascimento,
                        ),
                        const SizedBox(height: 16),
                        
                        // Telefone Celular
                        TextFormField(
                          controller: _telefoneController,
                          style: const TextStyle(color: Colors.white),
                          decoration: InputDecoration(
                            labelText: 'Telefone Celular',
                            labelStyle: const TextStyle(color: Colors.white),
                            prefixIcon: const Icon(Icons.phone_android_outlined, color: Colors.white),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(8),
                              borderSide: BorderSide.none,
                            ),
                            filled: true,
                            fillColor: Colors.white.withOpacity(0.1),
                            contentPadding: const EdgeInsets.symmetric(
                              vertical: 16, horizontal: 16),
                          ),
                          inputFormatters: [
                            FilteringTextInputFormatter.digitsOnly,
                            LengthLimitingTextInputFormatter(11),
                          ],
                          keyboardType: TextInputType.phone,
                          validator: _validarTelefone,
                        ),
                        const SizedBox(height: 16),
                        
                        // E-mail
                        TextFormField(
                          controller: _emailController,
                          style: const TextStyle(color: Colors.white),
                          keyboardType: TextInputType.emailAddress,
                          decoration: InputDecoration(
                            labelText: 'E-mail',
                            labelStyle: const TextStyle(color: Colors.white),
                            prefixIcon: const Icon(Icons.email_outlined, color: Colors.white),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(8),
                              borderSide: BorderSide.none,
                            ),
                            filled: true,
                            fillColor: Colors.white.withOpacity(0.1),
                            contentPadding: const EdgeInsets.symmetric(
                              vertical: 16, horizontal: 16),
                          ),
                          validator: (value) {
                            if (value == null || value.isEmpty) return "E-mail é obrigatório";
                            if (!value.contains('@')) return "E-mail inválido";
                            return null;
                          },
                        ),
                        const SizedBox(height: 16),
                        
                        // Senha
                        TextFormField(
                          controller: _senhaController,
                          style: const TextStyle(color: Colors.white),
                          obscureText: _obscurePassword,
                          decoration: InputDecoration(
                            labelText: 'Senha',
                            labelStyle: const TextStyle(color: Colors.white),
                            prefixIcon: const Icon(Icons.lock_outline, color: Colors.white),
                            suffixIcon: IconButton(
                              icon: Icon(
                                _obscurePassword ? Icons.visibility_outlined : Icons.visibility_off_outlined,
                                color: Colors.white,
                              ),
                              onPressed: () {
                                setState(() => _obscurePassword = !_obscurePassword);
                              },
                            ),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(8),
                              borderSide: BorderSide.none,
                            ),
                            filled: true,
                            fillColor: Colors.white.withOpacity(0.1),
                            contentPadding: const EdgeInsets.symmetric(
                              vertical: 16, horizontal: 16),
                          ),
                          validator: (value) {
                            if (value == null || value.isEmpty) return "Senha é obrigatória";
                            if (value.length < 8) return "Mínimo de 8 caracteres";
                            return null;
                          },
                        ),
                        const SizedBox(height: 24),
                        
                        // Botão de Cadastro
                        SizedBox(
                          width: double.infinity,
                          child: ElevatedButton(
                            onPressed: _isLoading ? null : _submitForm,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: const Color(0xFF009903),
                              padding: const EdgeInsets.symmetric(vertical: 18),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(8),
                              ),
                              elevation: 0,
                            ),
                            child: _isLoading
                                ? const SizedBox(
                                    height: 20,
                                    width: 20,
                                    child: CircularProgressIndicator(
                                      color: Colors.white,
                                      strokeWidth: 2,
                                    ),
                                  )
                                : const Text(
                                    'COMPLETAR CADASTRO',
                                    style: TextStyle(
                                      color: Colors.white,
                                      fontSize: 16,
                                      fontWeight: FontWeight.bold,
                                      letterSpacing: 1.2,
                                    ),
                                  ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 24),
                
                // Link para login
                TextButton(
                  onPressed: () {
                    Navigator.pushAndRemoveUntil(
                      context,
                      MaterialPageRoute(builder: (context) => const LoginPage()),
                      (route) => false,
                    );
                  },
                  child: const Text(
                    'Já possui uma conta? Faça login',
                    style: TextStyle(
                      color: Colors.white70,
                      fontSize: 14,
                    ),
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}