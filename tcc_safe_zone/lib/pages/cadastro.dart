import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:intl/intl.dart';
import 'package:tcc_safe_zone/pages/principal.dart';

class CadastroPage extends StatefulWidget {
  const CadastroPage({super.key});

  @override
  State<CadastroPage> createState() => _CadastroPageState();
}

class _CadastroPageState extends State<CadastroPage> {
  final _formKey = GlobalKey<FormState>();
  final TextEditingController _senhaController = TextEditingController();
  final TextEditingController _dataNascimentoController = TextEditingController();
  
  String _usuario = "";
  String _nome = "";
  String _sobrenome = "";
  String _razaoSocial = "";
  String _cnpj = "";
  String _email = "";
  String _cpf = "";
  String _telefone = "";
  String _senha = "";
  String _tipoUsuario = "comum";
  bool _obscurePassword = true;
  bool _isLoading = false;
  bool _mostrarCamposPJ = false;
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
    super.dispose();
  }

  void _updateDataNascimento() {
    if (_dataNascimentoController.text.isNotEmpty) {
      _dataNascimento = DateFormat('dd/MM/yyyy').parse(_dataNascimentoController.text);
    }
  }

  Future<void> _submitForm() async {
    if (_formKey.currentState!.validate()) {
      setState(() => _isLoading = true);
      await Future.delayed(const Duration(seconds: 2));
      setState(() => _isLoading = false);
      
      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (context) => PrincipalPage(usuario: _usuario),
        ),
      );
    }
  }

  Future<void> _selecionarData(BuildContext context) async {
    final DateTime? picked = await showDatePicker(
      context: context,
      initialDate: _dataNascimento ?? DateTime.now(),
      firstDate: DateTime(1900),
      lastDate: DateTime.now(),
      builder: (context, child) {
        return Theme(
          data: ThemeData.dark().copyWith(
            colorScheme: ColorScheme.dark(
              primary: const Color(0xFF4CAF50),
              onPrimary: Colors.white,
              surface: Color.fromARGB(255, 0, 183, 9),
              onSurface: Colors.white,
            ),
            dialogBackgroundColor: Colors.grey[900],
            textButtonTheme: TextButtonThemeData(
              style: TextButton.styleFrom(
                foregroundColor: Colors.white,
              ),
            ),
          ),
          child: child!,
        );
      },
    );
    
    if (picked != null) {
      setState(() {
        _dataNascimento = picked;
        _dataNascimentoController.text = DateFormat('dd/MM/yyyy').format(picked);
      });
    }
  }

  String? _validarCPF(String? value) {
    if (value == null || value.isEmpty) return "CPF é obrigatório";
    if (value.length != 14) return "CPF inválido";
    return null;
  }

  String? _validarCNPJ(String? value) {
    if (_mostrarCamposPJ && (value == null || value.isEmpty)) {
      return "CNPJ é obrigatório para PJ";
    }
    if (value != null && value.isNotEmpty && value.length != 18) {
      return "CNPJ inválido";
    }
    return null;
  }

  String? _validarTelefone(String? value) {
    if (value == null || value.isEmpty) return "Telefone é obrigatório";
    if (value.length < 14) return "Telefone inválido";
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
                        // Tipo de Usuário
                        DropdownButtonFormField<String>(
                          value: _tipoUsuario,
                          dropdownColor: const Color(0xFF4CAF50),
                          style: const TextStyle(color: Colors.white),
                          decoration: InputDecoration(
                            labelText: 'Tipo de Usuário',
                            labelStyle: const TextStyle(color: Colors.white),
                            prefixIcon: const Icon(Icons.person_outline, color: Colors.white),
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(8),
                              borderSide: BorderSide.none,
                            ),
                            filled: true,
                            fillColor: Colors.white.withOpacity(0.1),
                          ),
                          items: const [
                            DropdownMenuItem(
                              value: "comum",
                              child: Text("Usuário Comum"),
                            ),
                            DropdownMenuItem(
                              value: "admin",
                              child: Text("Administrador"),
                            ),
                          ],
                          onChanged: (value) {
                            setState(() {
                              _tipoUsuario = value!;
                            });
                          },
                        ),
                        const SizedBox(height: 16),
                        
                        // Usuário
                        TextFormField(
                          style: const TextStyle(color: Colors.white),
                          decoration: InputDecoration(
                            labelText: 'Nome de Usuário',
                            labelStyle: const TextStyle(color: Colors.white),
                            prefixIcon: const Icon(Icons.account_circle_outlined, color: Colors.white),
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
                              value == null || value.isEmpty ? "Usuário é obrigatório" : null,
                          onSaved: (value) => _usuario = value!,
                        ),
                        const SizedBox(height: 16),
                        
                        // Nome
                        TextFormField(
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
                          onSaved: (value) => _nome = value!,
                        ),
                        const SizedBox(height: 16),
                        
                        // Sobrenome
                        TextFormField(
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
                          onSaved: (value) => _sobrenome = value!,
                        ),
                        const SizedBox(height: 16),
                        
                        // CPF
                        TextFormField(
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
                            TextInputFormatter.withFunction((oldValue, newValue) {
                              final text = newValue.text;
                              if (text.length <= 3) {
                                return TextEditingValue(text: text);
                              }
                              if (text.length <= 6) {
                                return TextEditingValue(
                                  text: '${text.substring(0, 3)}.${text.substring(3)}',
                                  selection: TextSelection.collapsed(offset: newValue.text.length + 1),
                                );
                              }
                              if (text.length <= 9) {
                                return TextEditingValue(
                                  text: '${text.substring(0, 3)}.${text.substring(3, 6)}.${text.substring(6)}',
                                  selection: TextSelection.collapsed(offset: newValue.text.length + 2),
                                );
                              }
                              return TextEditingValue(
                                text: '${text.substring(0, 3)}.${text.substring(3, 6)}.${text.substring(6, 9)}-${text.substring(9)}',
                                selection: TextSelection.collapsed(offset: newValue.text.length + 3),
                              );
                            }),
                          ],
                          keyboardType: TextInputType.number,
                          validator: _validarCPF,
                          onSaved: (value) => _cpf = value!,
                        ),
                        const SizedBox(height: 16),
                        
                        // Data de Nascimento
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
                          ),
                          readOnly: true,
                          onTap: () => _selecionarData(context),
                          validator: (value) =>
                              value == null || value.isEmpty ? "Data de nascimento é obrigatória" : null,
                        ),
                        const SizedBox(height: 16),
                        
                        // Telefone Celular
                        TextFormField(
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
                            TextInputFormatter.withFunction((oldValue, newValue) {
                              final text = newValue.text;
                              if (text.isEmpty) return newValue;
                              if (text.length <= 2) {
                                return TextEditingValue(text: '($text)');
                              }
                              if (text.length <= 7) {
                                return TextEditingValue(
                                  text: '(${text.substring(0, 2)}) ${text.substring(2)}',
                                  selection: TextSelection.collapsed(offset: newValue.text.length + 3),
                                );
                              }
                              return TextEditingValue(
                                text: '(${text.substring(0, 2)}) ${text.substring(2, 7)}-${text.substring(7)}',
                                selection: TextSelection.collapsed(offset: newValue.text.length + 4),
                              );
                            }),
                          ],
                          keyboardType: TextInputType.phone,
                          validator: _validarTelefone,
                          onSaved: (value) => _telefone = value!,
                        ),
                        const SizedBox(height: 16),
                        
                        // E-mail
                        TextFormField(
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
                          onSaved: (value) => _email = value!,
                        ),
                        const SizedBox(height: 16),
                        
                        // Checkbox para Pessoa Jurídica
                        Row(
                          children: [
                            Checkbox(
                              value: _mostrarCamposPJ,
                              onChanged: (value) {
                                setState(() {
                                  _mostrarCamposPJ = value!;
                                });
                              },
                              fillColor: MaterialStateProperty.resolveWith<Color>(
                                (Set<MaterialState> states) {
                                  if (states.contains(MaterialState.selected)) {
                                    return const Color(0xFF4CAF50);
                                  }
                                  return Colors.white.withOpacity(0.1);
                                },
                              ),
                              checkColor: Colors.white,
                            ),
                            const Text(
                              'Sou Pessoa Jurídica',
                              style: TextStyle(color: Colors.white),
                            ),
                          ],
                        ),
                        
                        // Campos PJ (condicional)
                        if (_mostrarCamposPJ) ...[
                          const SizedBox(height: 16),
                          // Razão Social
                          TextFormField(
                            style: const TextStyle(color: Colors.white),
                            decoration: InputDecoration(
                              labelText: 'Razão Social',
                              labelStyle: const TextStyle(color: Colors.white),
                              prefixIcon: const Icon(Icons.business_outlined, color: Colors.white),
                              border: OutlineInputBorder(
                                borderRadius: BorderRadius.circular(8),
                                borderSide: BorderSide.none,
                              ),
                              filled: true,
                              fillColor: Colors.white.withOpacity(0.1),
                              contentPadding: const EdgeInsets.symmetric(
                                vertical: 16, horizontal: 16),
                            ),
                            onSaved: (value) => _razaoSocial = value ?? '',
                          ),
                          const SizedBox(height: 16),
                          // CNPJ
                          TextFormField(
                            style: const TextStyle(color: Colors.white),
                            decoration: InputDecoration(
                              labelText: 'CNPJ',
                              labelStyle: const TextStyle(color: Colors.white),
                              prefixIcon: const Icon(Icons.business_center_outlined, color: Colors.white),
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
                              LengthLimitingTextInputFormatter(14),
                              TextInputFormatter.withFunction((oldValue, newValue) {
                                final text = newValue.text;
                                if (text.isEmpty) return newValue;
                                if (text.length <= 2) {
                                  return TextEditingValue(text: text);
                                }
                                if (text.length <= 5) {
                                  return TextEditingValue(
                                    text: '${text.substring(0, 2)}.${text.substring(2)}',
                                    selection: TextSelection.collapsed(offset: newValue.text.length + 1),
                                  );
                                }
                                if (text.length <= 8) {
                                  return TextEditingValue(
                                    text: '${text.substring(0, 2)}.${text.substring(2, 5)}.${text.substring(5)}',
                                    selection: TextSelection.collapsed(offset: newValue.text.length + 2),
                                  );
                                }
                                if (text.length <= 12) {
                                  return TextEditingValue(
                                    text: '${text.substring(0, 2)}.${text.substring(2, 5)}.${text.substring(5, 8)}/${text.substring(8)}',
                                    selection: TextSelection.collapsed(offset: newValue.text.length + 3),
                                  );
                                }
                                return TextEditingValue(
                                  text: '${text.substring(0, 2)}.${text.substring(2, 5)}.${text.substring(5, 8)}/${text.substring(8, 12)}-${text.substring(12)}',
                                  selection: TextSelection.collapsed(offset: newValue.text.length + 4),
                                );
                              }),
                            ],
                            keyboardType: TextInputType.number,
                            validator: _validarCNPJ,
                            onSaved: (value) => _cnpj = value ?? '',
                          ),
                          const SizedBox(height: 16),
                        ],
                        
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
                          onSaved: (value) => _senha = value!,
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
                    Navigator.pop(context);
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