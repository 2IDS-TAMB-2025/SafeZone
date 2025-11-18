import 'package:flutter/material.dart';
import 'dart:convert';
import '../models/usuario_model.dart';
import 'package:tcc_safe_zone/pages/principal.dart';
import 'package:tcc_safe_zone/services/photo_service.dart';
import 'package:tcc_safe_zone/widgets/profile_avatar_widget.dart';
import 'package:tcc_safe_zone/controllers/api_controller.dart';

class DadosCadastradosPage extends StatefulWidget {
  final Usuario usuario;

  const DadosCadastradosPage({super.key, required this.usuario});

  @override
  State<DadosCadastradosPage> createState() => _DadosCadastradosPageState();
}

class _DadosCadastradosPageState extends State<DadosCadastradosPage> {
  bool _isLoading = false;
  bool _editando = false;
  
  // Controladores para edição
  final TextEditingController _nomeController = TextEditingController();
  final TextEditingController _sobrenomeController = TextEditingController();
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _telefoneController = TextEditingController();
  final TextEditingController _dataNascimentoController = TextEditingController();
  final TextEditingController _cpfController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _carregarDadosUsuario();
  }

  void _carregarDadosUsuario() {
    _nomeController.text = widget.usuario.nome;
    _sobrenomeController.text = widget.usuario.sobrenome;
    _emailController.text = widget.usuario.email;
    _telefoneController.text = widget.usuario.telefoneCelular;
    _dataNascimentoController.text = widget.usuario.dataNascimentoFormatada;
    _cpfController.text = widget.usuario.cpf;
  }

  void _alternarEdicao() {
    setState(() {
      _editando = !_editando;
      if (!_editando) {
        // Se cancelou a edição, recarregar dados originais
        _carregarDadosUsuario();
      }
    });
  }

  Future<void> _salvarAlteracoes() async {
    if (_isLoading) return;
    
    setState(() {
      _isLoading = true;
    });

    try {
      // Validar campos obrigatórios
      if (_nomeController.text.isEmpty || _sobrenomeController.text.isEmpty || 
          _emailController.text.isEmpty || _telefoneController.text.isEmpty) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Preencha todos os campos obrigatórios'),
            backgroundColor: Colors.red,
          ),
        );
        return;
      }

      // Validar email
      if (!_emailController.text.contains('@')) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(
            content: Text('Por favor, insira um email válido'),
            backgroundColor: Colors.red,
          ),
        );
        return;
      }

      // Preparar dados para atualização
      final dadosAtualizacao = {
        'ID_USUARIO': widget.usuario.idUsuario,
        'NOME': _nomeController.text.trim(),
        'SOBRENOME': _sobrenomeController.text.trim(),
        'EMAIL': _emailController.text.trim(),
        'TELEFONE_CELULAR': _telefoneController.text.trim(),
      };

      // Chamar API para atualizar
      final usuarioAtualizado = await ApiController.atualizarUsuario(dadosAtualizacao);
      
      // Atualizar objeto usuário localmente
      widget.usuario.nome = usuarioAtualizado.nome;
      widget.usuario.sobrenome = usuarioAtualizado.sobrenome;
      widget.usuario.email = usuarioAtualizado.email;
      widget.usuario.telefoneCelular = usuarioAtualizado.telefoneCelular;

      ScaffoldMessenger.of(context).showSnackBar(
        const SnackBar(
          content: Text('Dados atualizados com sucesso!'),
          backgroundColor: Colors.green,
        ),
      );

      setState(() {
        _editando = false;
      });

    } catch (e) {
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text('Erro ao atualizar dados: $e'),
          backgroundColor: Colors.red,
        ),
      );
    } finally {
      setState(() {
        _isLoading = false;
      });
    }
  }

  Widget _buildAvatar() {
    return ProfileAvatarWidget(
      radius: 50,
      onTap: () {
        // Não faz nada - sem opções de foto
      },
    );
  }

  Widget _buildCampoEditavel(String label, String value, TextEditingController controller, {bool enabled = true}) {
    if (_editando && enabled) {
      return Padding(
        padding: const EdgeInsets.symmetric(vertical: 8.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              '$label:',
              style: TextStyle(
                color: Colors.white.withOpacity(0.7),
                fontSize: 14,
                fontWeight: FontWeight.w500,
              ),
            ),
            const SizedBox(height: 4),
            TextFormField(
              controller: controller,
              style: const TextStyle(color: Colors.white),
              decoration: InputDecoration(
                border: OutlineInputBorder(
                  borderSide: BorderSide(color: Colors.white.withOpacity(0.5)),
                ),
                enabledBorder: OutlineInputBorder(
                  borderSide: BorderSide(color: Colors.white.withOpacity(0.5)),
                ),
                focusedBorder: const OutlineInputBorder(
                  borderSide: BorderSide(color: Colors.white),
                ),
                contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                filled: true,
                fillColor: Colors.white.withOpacity(0.1),
              ),
            ),
          ],
        ),
      );
    } else {
      return _buildInfoItem(label, value);
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: const Color(0xFF007701),
        elevation: 0,
        leading: IconButton(
          icon: const Icon(Icons.arrow_back, color: Colors.white, size: 30),
          onPressed: () => Navigator.pop(context),
        ),
        title: const Text(
          'Meus Dados',
          style: TextStyle(
            color: Colors.white,
            fontSize: 20,
            fontWeight: FontWeight.bold,
          ),
        ),
        centerTitle: true,
        actions: [
          if (!_editando)
            IconButton(
              icon: const Icon(Icons.edit, color: Colors.white),
              onPressed: _alternarEdicao,
            ),
        ],
      ),
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
        child: Column(
          children: [
            // Header com avatar
            Container(
              padding: const EdgeInsets.all(20),
              decoration: BoxDecoration(
                color: Colors.white.withOpacity(0.15),
                borderRadius: const BorderRadius.only(
                  bottomLeft: Radius.circular(20),
                  bottomRight: Radius.circular(20),
                ),
              ),
              child: Column(
                children: [
                  _buildAvatar(),
                  const SizedBox(height: 15),
                  Text(
                    '${widget.usuario.nome} ${widget.usuario.sobrenome}',
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                  const SizedBox(height: 5),
                  Text(
                    widget.usuario.email,
                    style: TextStyle(
                      color: Colors.white.withOpacity(0.8),
                      fontSize: 16,
                    ),
                  ),
                  const SizedBox(height: 10),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                    decoration: BoxDecoration(
                      color: Colors.white.withOpacity(0.2),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: Text(
                      widget.usuario.tipoUsuarioFormatado,
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 14,
                        fontWeight: FontWeight.w500,
                      ),
                    ),
                  ),
                ],
              ),
            ),
            
            Expanded(
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: ListView(
                  children: [
                    _buildInfoSection('Informações Pessoais', Icons.person, [
                      _buildCampoEditavel('Nome', widget.usuario.nome, _nomeController),
                      const SizedBox(height: 12),
                      _buildCampoEditavel('Sobrenome', widget.usuario.sobrenome, _sobrenomeController),
                      const SizedBox(height: 12),
                      _buildCampoEditavel('CPF', widget.usuario.cpfFormatado, _cpfController, enabled: false),
                      const SizedBox(height: 12),
                      _buildCampoEditavel('Data de Nascimento', widget.usuario.dataNascimentoFormatada, _dataNascimentoController, enabled: false),
                      const SizedBox(height: 12),
                      _buildCampoEditavel('E-mail', widget.usuario.email, _emailController),
                      const SizedBox(height: 12),
                      _buildCampoEditavel('Telefone', widget.usuario.telefoneFormatado, _telefoneController),
                    ]),
                    
                    const SizedBox(height: 20),
                    _buildInfoSection('Tipo de Conta', Icons.account_circle, [
                      _buildInfoItem('Tipo de Usuário', widget.usuario.tipoUsuarioFormatado),
                    ]),
                    
                    const SizedBox(height: 30),
                    _buildActionButtons(context),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildInfoSection(String title, IconData icon, List<Widget> children) {
    return Card(
      margin: const EdgeInsets.symmetric(vertical: 5),
      color: Colors.white.withOpacity(0.1),
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(12),
      ),
      child: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                Icon(icon, color: Colors.white, size: 20),
                const SizedBox(width: 8),
                Text(
                  title,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 10),
            ...children,
          ],
        ),
      ),
    );
  }

  Widget _buildInfoItem(String label, String value, {bool isActive = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8.0),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Expanded(
            flex: 2,
            child: Text(
              '$label:',
              style: TextStyle(
                color: Colors.white.withOpacity(0.7),
                fontSize: 14,
                fontWeight: FontWeight.w500,
              ),
            ),
          ),
          Expanded(
            flex: 3,
            child: isActive
                ? Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: Colors.green.withOpacity(0.2),
                      borderRadius: BorderRadius.circular(6),
                      border: Border.all(color: Colors.green),
                    ),
                    child: Text(
                      value,
                      style: const TextStyle(
                        color: Colors.green,
                        fontSize: 14,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  )
                : Text(
                    value,
                    style: const TextStyle(
                      color: Colors.white,
                      fontSize: 14,
                      fontWeight: FontWeight.w400,
                    ),
                  ),
          ),
        ],
      ),
    );
  }

  Widget _buildActionButtons(BuildContext context) {
    if (_editando) {
      return Column(
        children: [
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: _isLoading ? null : _salvarAlteracoes,
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF4CAF50),
                padding: const EdgeInsets.symmetric(vertical: 16),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10),
                ),
              ),
              child: _isLoading
                  ? SizedBox(
                      height: 20,
                      width: 20,
                      child: CircularProgressIndicator(
                        strokeWidth: 2,
                        valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                      ),
                    )
                  : const Text(
                      'SALVAR ALTERAÇÕES',
                      style: TextStyle(
                        color: Colors.white,
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
            ),
          ),
          const SizedBox(height: 10),
          SizedBox(
            width: double.infinity,
            child: OutlinedButton(
              onPressed: _isLoading ? null : _alternarEdicao,
              style: OutlinedButton.styleFrom(
                foregroundColor: Colors.white,
                side: const BorderSide(color: Colors.white),
                padding: const EdgeInsets.symmetric(vertical: 16),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10),
                ),
              ),
              child: const Text(
                'CANCELAR',
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
          ),
        ],
      );
    } else {
      return Column(
        children: [
          SizedBox(
            width: double.infinity,
            child: ElevatedButton(
              onPressed: _alternarEdicao,
              style: ElevatedButton.styleFrom(
                backgroundColor: const Color(0xFF007701),
                padding: const EdgeInsets.symmetric(vertical: 16),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10),
                ),
              ),
              child: const Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.edit, color: Colors.white),
                  SizedBox(width: 10),
                  Text(
                    'EDITAR PERFIL',
                    style: TextStyle(
                      color: Colors.white,
                      fontSize: 16,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(height: 10),
          SizedBox(
            width: double.infinity,
            child: OutlinedButton(
              onPressed: () {
                Navigator.pushReplacement(
                  context, 
                  MaterialPageRoute(
                    builder: (context) => PrincipalPage(usuario: '${widget.usuario.nome} ${widget.usuario.sobrenome}')
                  )
                );
              },
              style: OutlinedButton.styleFrom(
                foregroundColor: Colors.white,
                side: const BorderSide(color: Colors.white),
                padding: const EdgeInsets.symmetric(vertical: 16),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(10),
                ),
              ),
              child: const Text(
                'VOLTAR PARA INÍCIO',
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
          ),
        ],
      );
    }
  }
}