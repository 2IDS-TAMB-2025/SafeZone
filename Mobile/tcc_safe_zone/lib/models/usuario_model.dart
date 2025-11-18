import 'dart:convert';

class Usuario {
  int? idUsuario;
  String nome;
  String sobrenome;
  String email;
  String dataNascimento;
  String cpf;
  String senha;
  String telefoneCelular;
  String? fotoPerfil;
  String? tipoUsuario;

  Usuario({
    this.idUsuario,
    required this.nome,
    required this.sobrenome,
    required this.email,
    required this.dataNascimento,
    required this.cpf,
    required this.senha,
    required this.telefoneCelular,
    this.fotoPerfil,
    this.tipoUsuario,
  });

  // Getters para formatação
  String get nomeCompleto => '$nome $sobrenome';
  
  String get cpfFormatado {
    if (cpf.length == 11) {
      return '${cpf.substring(0, 3)}.${cpf.substring(3, 6)}.${cpf.substring(6, 9)}-${cpf.substring(9)}';
    }
    return cpf;
  }

  String get telefoneFormatado {
    if (telefoneCelular.length == 11) {
      return '(${telefoneCelular.substring(0, 2)}) ${telefoneCelular.substring(2, 7)}-${telefoneCelular.substring(7)}';
    }
    return telefoneCelular;
  }

  String get dataNascimentoFormatada {
    try {
      // Converte de YYYY-MM-DD para DD/MM/YYYY
      final parts = dataNascimento.split('-');
      if (parts.length == 3) {
        return '${parts[2]}/${parts[1]}/${parts[0]}';
      }
      return dataNascimento;
    } catch (e) {
      return dataNascimento;
    }
  }

  String get tipoUsuarioFormatado => tipoUsuario == 'ADMIN' ? 'Administrador' : 'Usuário';

  // Converter para Map (para enviar para API)
  Map<String, dynamic> toJson() {
    return {
      'NOME': nome,
      'SOBRENOME': sobrenome,
      'EMAIL': email,
      'DATA_NASCIMENTO': dataNascimento,
      'CPF': cpf,
      'SENHA': senha,
      'TELEFONE_CELULAR': telefoneCelular,
      'TIPO_USUARIO': 'USUARIO',
      'FOTO_PERFIL': fotoPerfil ?? '',
    };
  }

  // Criar a partir de Map (recebido da API)
  factory Usuario.fromJson(Map<String, dynamic> json) {
    return Usuario(
      idUsuario: json['ID_USUARIO'] ?? json['id'],
      nome: json['NOME'] ?? json['nome'] ?? '',
      sobrenome: json['SOBRENOME'] ?? json['sobrenome'] ?? '',
      email: json['EMAIL'] ?? json['email'] ?? '',
      dataNascimento: json['DATA_NASCIMENTO'] ?? json['data_nascimento'] ?? '',
      cpf: json['CPF'] ?? json['cpf'] ?? '',
      senha: json['SENHA'] ?? json['senha'] ?? '',
      telefoneCelular: json['TELEFONE_CELULAR'] ?? json['telefone_celular'] ?? '',
      fotoPerfil: json['FOTO_PERFIL'] ?? json['foto_perfil'],
      tipoUsuario: json['TIPO_USUARIO'] ?? json['tipo_usuario'] ?? 'USUARIO',
    );
  }
}