-- =====================================================
--  Banco de Dados SAFE_ZONE
-- =====================================================
CREATE DATABASE IF NOT EXISTS SAFE_ZONE;
USE SAFE_ZONE;

-- =====================================================
--  TABELA USUARIO
-- =====================================================
CREATE TABLE IF NOT EXISTS USUARIO (

  ID_USUARIO         INT AUTO_INCREMENT PRIMARY KEY,
  NOME               VARCHAR(100)   NOT NULL,
  SOBRENOME          VARCHAR(100)   NOT NULL,
  EMAIL              VARCHAR(100) UNIQUE NOT NULL,
  DATA_NASCIMENTO    DATE           NOT NULL,
  CPF                VARCHAR(11)    NOT NULL,
  SENHA              VARCHAR(100)   NOT NULL,
  TELEFONE_CELULAR   VARCHAR(11)    NOT NULL,
  RAZAO_SOCIAL       VARCHAR(100),
  CNPJ               VARCHAR(20),
  TIPO_USUARIO       ENUM('ADMINISTRADOR','USUARIO') NOT NULL
);

ALTER TABLE USUARIO ADD COLUMN FOTO_PERFIL VARCHAR(255) NULL;

CREATE TABLE IF NOT EXISTS CONTATO (
	ID_CONTATO		INT AUTO_INCREMENT PRIMARY KEY,
    NOME_COMPLETO	VARCHAR(255)	NOT NULL,
    EMAIL			VARCHAR(100)	NOT NULL,
    ASSUNTO			ENUM('DUVIDA','SUGESTAO','REPORTAR PROBLEMA','PARCERIA','OUTRO') NOT NULL,
    MENSAGEM		VARCHAR(500)	NOT NULL
);

-- =====================================================
--  TABELA NOTIFICACAO
-- =====================================================
CREATE TABLE IF NOT EXISTS NOTIFICACAO (
  ID_NOTIFICACAO     INT AUTO_INCREMENT PRIMARY KEY,
  TITULO             VARCHAR(255)   NOT NULL,
  CONTEUDO           VARCHAR(500)   NOT NULL,
  DATA_POST          DATE,
  DATA_ENVIO         DATE,
  STATUS             VARCHAR(10)    NOT NULL
);

-- =====================================================
--  RELAÇÃO NOTIFICACAO_USUARIO
-- =====================================================
CREATE TABLE IF NOT EXISTS NOTIFICACAO_USUARIO (
  ID                  INT AUTO_INCREMENT PRIMARY KEY,
  FK_ID_NOTIFICACAO   INT NOT NULL,
  FK_ID_USUARIO       INT NOT NULL,
  FOREIGN KEY (FK_ID_NOTIFICACAO)
    REFERENCES NOTIFICACAO(ID_NOTIFICACAO)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (FK_ID_USUARIO)
    REFERENCES USUARIO(ID_USUARIO)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- =====================================================
--  TABELA BOTAO_EMERGENCIA
-- =====================================================
CREATE TABLE IF NOT EXISTS BOTAO_EMERGENCIA (
  ID_EMERGENCIA       INT AUTO_INCREMENT PRIMARY KEY,
  HORA                TIME           NOT NULL,
  DATA_EMERGENCIA     DATE           NOT NULL,
  LOCALIZACAO         VARCHAR(200)   NOT NULL,
  NUMERO_TELEFONE     VARCHAR(11)    NOT NULL,
  REGISTRO_HISTORICO  VARCHAR(300)   NOT NULL,
  FK_ID_USUARIO       INT            NOT NULL,
  FOREIGN KEY (FK_ID_USUARIO)
    REFERENCES USUARIO(ID_USUARIO)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- =====================================================
--  TABELA SENSORES
-- =====================================================
CREATE TABLE IF NOT EXISTS SENSORES (
  ID_SENSOR           INT AUTO_INCREMENT PRIMARY KEY,
  TIPO_SENSOR         VARCHAR(50)    NOT NULL,
  LOCALIZACAO         VARCHAR(300)   NOT NULL,
  DATA_INSTALACAO     DATE           NOT NULL,
  STATUS_SENSOR       VARCHAR(50)    NOT NULL
);

-- =====================================================
--  TABELA HISTORICO DOS SENSORES
-- =====================================================
CREATE TABLE IF NOT EXISTS HISTORICO (
  ID_HISTORICO        INT AUTO_INCREMENT PRIMARY KEY,
  ID_SENSOR           INT            NOT NULL,
  DADOS               VARCHAR(4000)  NOT NULL,
  UNIDADE_MEDIDA      VARCHAR(200)   NOT NULL,
  LATITUDE            DECIMAL(9,6)   NOT NULL,
  LONGITUDE           DECIMAL(9,6)   NOT NULL,
  DATA_COLETA         DATE           NOT NULL,
  HORA_COLETA         TIME           NOT NULL,
  FOREIGN KEY (ID_SENSOR)
    REFERENCES SENSORES(ID_SENSOR)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- =====================================================
--  TABELA AREA_RISCO
-- =====================================================
CREATE TABLE IF NOT EXISTS AREA_RISCO (
  ID_RISCO            INT AUTO_INCREMENT PRIMARY KEY,
  TIPO_RISCO          VARCHAR(200)   NOT NULL,
  DESCRICAO           VARCHAR(500)   NOT NULL,
  RUA                 VARCHAR(200)   NOT NULL,
  NUMERO              VARCHAR(10)    NOT NULL,
  CIDADE              VARCHAR(50)    NOT NULL,
  ESTADO              VARCHAR(50)    NOT NULL,
  PAIS                VARCHAR(50)    NOT NULL,
  LATITUDE            DECIMAL(9,6)   NOT NULL,
  LONGITUDE           DECIMAL(9,6)   NOT NULL
);

-- =====================================================
--  RELAÇÃO SENSOR_AREA_RISCO
-- =====================================================
CREATE TABLE IF NOT EXISTS SENSOR_AREA_RISCO (
  ID                   INT AUTO_INCREMENT PRIMARY KEY,
  FK_ID_SENSOR         INT NOT NULL,
  FK_ID_RISCO          INT NOT NULL,
  FOREIGN KEY (FK_ID_SENSOR)
    REFERENCES SENSORES(ID_SENSOR)
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  FOREIGN KEY (FK_ID_RISCO)
    REFERENCES AREA_RISCO(ID_RISCO)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

-- TABELA PARA RECUPERAÇÃO DE SENHA
CREATE TABLE IF NOT EXISTS RECUPERACAO_SENHA (
  ID_RECUPERACAO INT AUTO_INCREMENT PRIMARY KEY,
  FK_ID_USUARIO INT NOT NULL,
  CODIGO VARCHAR(6) NOT NULL,
  DATA_CRIACAO DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  DATA_EXPIRACAO DATETIME NOT NULL,
  UTILIZADO BOOLEAN NOT NULL DEFAULT FALSE,
  FOREIGN KEY (FK_ID_USUARIO)
    REFERENCES USUARIO(ID_USUARIO)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

ALTER TABLE RECUPERACAO_SENHA 
ADD COLUMN SOLICITACOES_HOJE INT DEFAULT 0,
ADD COLUMN ULTIMA_SOLICITACAO DATE;

-- =====================================================
--  INSERTS DE EXEMPLO
-- =====================================================

-- 0) CONTATO
INSERT INTO CONTATO
    (NOME_COMPLETO, EMAIL, ASSUNTO, MENSAGEM)
VALUES
    ('João Silva', 'joao.silva@email.com', 'DUVIDA', 'Gostaria de saber como posso acessar os dados históricos de temperatura da minha região.'),
    ('Maria Oliveira', 'maria.oliveira@empresa.com', 'PARCERIA', 'Temos interesse em estabelecer uma parceria comercial com a Safe Zone para monitoramento ambiental.'),
    ('Carlos Santos', 'carlos.santos@escola.edu', 'SUGESTAO', 'Sugiro a inclusão de um gráfico comparativo entre diferentes anos no mesmo período.'),
    ('Ana Pereira', 'ana.pereira@email.com', 'REPORTAR PROBLEMA', 'O sensor da região centro está apresentando dados inconsistentes desde ontem.'),
    ('Pedro Costa', 'pedro.costa@tech.com', 'OUTRO', 'Gostaria de informações sobre oportunidades de trabalho na Safe Zone.'),
    ('Fernanda Lima', 'fernanda.lima@universidade.br', 'DUVIDA', 'Os dados de qualidade do ar são atualizados em tempo real ou há algum delay?'),
    ('Ricardo Alves', 'ricardo.alves@ong.org', 'SUGESTAO', 'Seria útil ter um alerta por e-mail quando os parâmetros ultrapassarem limites seguros.'),
    ('Juliana Martins', 'juliana.martins@email.com', 'REPORTAR PROBLEMA', 'Não consigo visualizar os dados do último mês no mapa interativo.'),
    ('Lucas Gonçalves', 'lucas.goncalves@empresa.com', 'PARCERIA', 'Gostaríamos de integrar os dados da Safe Zone ao nosso sistema de monitoramento.'),
    ('Amanda Souza', 'amanda.souza@email.com', 'DUVIDA', 'Qual é a frequência de calibração dos sensores da rede?');

-- 1) USUÁRIOS
INSERT INTO USUARIO
  (NOME, SOBRENOME, EMAIL, DATA_NASCIMENTO, CPF, SENHA, TELEFONE_CELULAR, RAZAO_SOCIAL, CNPJ, TIPO_USUARIO)
VALUES
  ('Ana','Lima','ana.lima@gmail.com','1990-05-14','12345678901','ana12345','41999998888',NULL, NULL,'USUARIO'),
  ('Carlos','Mendes','carlos.mendes@empresa.com','1985-09-21','10987654321','carlos85','41999997777','Mendes Tecnologia Ltda','12345678000195','ADMINISTRADOR'),
  ('Bruna','Silva','bruna.silva@gmail.com','1995-03-10','23456789012','bru@2023','41988887777',NULL, NULL,'USUARIO'),
  ('Daniel','Ferreira','daniel.ferreira@geo.com','1988-07-18','21098765432','daniel88','41977776666','Geo Monitoramento Ambiental','98765432000123','ADMINISTRADOR'),
  ('Eduarda','Pereira','eduarda.pereira@gmail.com','2000-11-05','34567890123','duda1234','41966665555',NULL, NULL,'USUARIO'),
  ('Fabio','Costa','fabio.costa@safeinfra.com','1982-02-28','43210987654','fabioinfra','41955554444','SafeInfra Soluções','33445566000178','ADMINISTRADOR'),
  ('Giovana','Ribeiro','giovana.ribeiro@gmail.com','1999-12-25','56789012345','gio@2024','41944443333',NULL, NULL,'USUARIO'),
  ('Hugo','Almeida','hugo.almeida@tectec.com','1990-08-17','65432109876','hugo1234','41933332222','TecTec Monitoramento','44556677000189','ADMINISTRADOR'),
  ('Isabela','Souza','isabela.souza@gmail.com','1998-04-09','78901234567','isa2023','41922221111',NULL, NULL,'USUARIO'),
  ('João','Martins','joao.martins@globalalert.com','1987-01-30','87654321098','joao321','41911110000','Global Alert Sistemas','55667788000190','ADMINISTRADOR');



-- 3) NOTIFICACAO
INSERT INTO NOTIFICACAO (TITULO, CONTEUDO, DATA_POST, DATA_ENVIO, STATUS) VALUES
('Alerta: Chuvas Fortes', 'Chuva intensa nas próximas 24h.', '2024-01-10','2024-01-10','enviado'),
('Risco de Deslizamento', 'Evite áreas de encosta.',      '2024-02-17','2024-02-17','enviado'),
('Tempestade Severa',     'Procure abrigo seguro.',       '2024-03-03','2024-03-03','enviado'),
('Inundação Imediata',    'Evacue áreas baixas.',         '2024-04-01','2024-04-01','pendente'),
('Alerta: Queimadas',     'Alto risco de incêndios.',     '2024-05-15','2024-05-15','enviado'),
('Aviso de Tornado',      'Procure abrigo resistente.',   '2024-06-28','2024-06-28','enviado'),
('Terremoto Registrado',  'Magnitude 6.5 detectada.',     '2024-07-12','2024-07-12','pendente'),
('Tsunami Previsto',      'Evacue para áreas elevadas.',  '2024-08-05','2024-08-05','enviado'),
('Calor Extremo',         'Evite exposição ao sol.',      '2024-09-20','2024-09-20','enviado'),
('Nevasca Intensa',       'Permaneça em locais aquecidos.','2024-11-15','2024-11-15','enviado');

-- 4)NOTIFICACAO_USUARIO
INSERT INTO NOTIFICACAO_USUARIO (FK_ID_NOTIFICACAO, FK_ID_USUARIO) VALUES
(1,1), (2,2), (3,3), (4,4), (5,5),
(6,6), (7,7), (8,8), (9,9), (10,10);

-- 5)BOTAO_EMERGENCIA
INSERT INTO BOTAO_EMERGENCIA (HORA, DATA_EMERGENCIA, LOCALIZACAO, NUMERO_TELEFONE, REGISTRO_HISTORICO, FK_ID_USUARIO) VALUES
('08:15:00','2024-02-01','Rua A, 100','11987654321','Maremoto detectado',         1),
('09:30:00','2024-03-05','Av. B, 200','21998765432','Deslizamento de terra',      2),
('10:45:00','2024-04-10','Praça C, 300','31912345678','Incêndio em mata',           3),
('12:00:00','2024-05-12','Rodovia D, Km45','41923456789','Gases tóxicos acumulados',4),
('13:20:00','2024-06-18','Bairro E, Rua5','51934567890','Tremor moderado',            5),
('14:55:00','2024-07-22','Centro F, Av10','61945678901','Inundação severa',           6),
('16:10:00','2024-08-30','Zona G, Estrada20','71956789012','Explosão industrial',     7),
('17:25:00','2024-09-14','Lote H, Rua15','81967890123','Queda de barragem',          8),
('18:40:00','2024-10-02','Parque I, Trilha','91978901234','Avalanche em serra',        9),
('20:00:00','2024-11-19','Ilha J, Ponto X','01989012345','Tsunami secundário',        10);

-- 6) SENSORES
INSERT INTO SENSORES (TIPO_SENSOR, LOCALIZACAO, DATA_INSTALACAO, STATUS_SENSOR) VALUES
('Acelerômetro','Ponte X, RJ',             '2022-01-01','ATIVO'),
('Sensor de Gases','Refinaria Y, SP',      '2021-02-15','ATIVO'),
('Sensor de Umidade do Solo','Cerrado Z, GO', '2023-03-20','ATIVO'),
('Sensor Ultrassônico','BR-040, MG',       '2020-04-05','INATIVO'),
('Sensor de Temperatura','Amazônia W, AM', '2021-05-10','ATIVO'),
('Pressão','Barragem V, MG',               '2022-06-12','ATIVO'),
('Vibração','Zona Sísmica U, CE',          '2021-07-18','ATIVO'),
('Nível de Água','Rio T, RO',              '2023-08-22','ATIVO'),
('Vento','Serra S, ES',                    '2019-09-30','INATIVO'),
('Radiação','Estação R, SP',               '2020-10-25','ATIVO');


-- 7) HISTORICO
INSERT INTO HISTORICO (ID_SENSOR, DADOS, UNIDADE_MEDIDA, LATITUDE, LONGITUDE, DATA_COLETA, HORA_COLETA) VALUES
(1,'0.05','m/s²',  -22.900000, -43.200000,'2024-01-15','08:00:00'),
(2,'40','ppm',    -23.550000, -46.633000,'2024-02-20','09:30:00'),
(3,'60','%',      -16.680000, -49.250000,'2024-03-25','10:45:00'),
(4,'5.5','m',     -19.920000, -43.940000,'2024-04-30','12:00:00'),
(5,'28','°C',     -3.100000,  -60.000000,'2024-05-05','13:20:00'),
(6,'1018','hPa',  -20.310000, -40.300000,'2024-06-10','14:55:00'),
(7,'0.12','mm/s', -3.720000,  -38.520000,'2024-07-15','16:10:00'),
(8,'4.8','m',     -8.760000,  -63.900000,'2024-08-20','17:25:00'),
(9,'15','km/h',   -22.910000, -43.200000,'2024-09-25','18:40:00'),
(10,'0.02','µSv/h',-23.550000, -46.633000,'2024-10-30','20:00:00');

-- 8) AREA_RISCO
INSERT INTO AREA_RISCO (TIPO_RISCO, DESCRICAO, RUA, NUMERO, CIDADE, ESTADO, PAIS, LATITUDE, LONGITUDE) VALUES
('Deslizamento','Encosta instável',      'Rua A','10','Petrópolis','RJ','Brasil',  -22.505000, -43.178000),
('Inundação','Margem de rio',            'Av. Beira-Rio','20','Porto Alegre','RS','Brasil',-30.027700, -51.228700),
('Queimadas','Vegetação seca',            'Estrada Pantanal','S/N','Corumbá','MS','Brasil',-19.000000, -57.000000),
('Tsunami','Litoral exposto',             'Orla Mar','55','Recife','PE','Brasil',         -8.060000,  -34.880000),
('Furacão','Zona costeira',               'Alameda Ventos','100','Natal','RN','Brasil', -5.795000,  -35.209000),
('Seca','Região árida',                  'Estrada Sertão','200','Juazeiro','BA','Brasil',-9.416667,  -40.503333),
('Erosão','Falésias frágeis',             'Av. do Mar','88','Fortaleza','CE','Brasil',   -3.717000,  -38.543000),
('Enxurrada','Drenagem pobre',            'Rua das Águas','199','Belo Horizonte','MG','Brasil',-19.920000, -43.940000),
('Terremoto','Falha geológica',           'Travessa Sísmica','45','Sobral','CE','Brasil',-3.689667,  -40.349083),
('Tempestade','Clima extremo',            'Av. Tempestades','72','Curitiba','PR','Brasil', -25.428400, -49.273300);

-- 9) SENSOR_AREA_RISCO
INSERT INTO SENSOR_AREA_RISCO (FK_ID_SENSOR, FK_ID_RISCO) VALUES
(1,1),  (2,2),  (3,3),  (4,4),  (5,5),
(6,6),  (7,7),  (8,8),  (9,9),  (10,10);



-- SELECTS -- 

-- =========================
-- TABELA USUARIO
-- =========================
SELECT * FROM CONTATO;
SELECT * FROM USUARIO;
SELECT ID_USUARIO FROM USUARIO;
SELECT NOME FROM USUARIO;
SELECT SOBRENOME FROM USUARIO;
SELECT EMAIL FROM USUARIO;
SELECT DATA_NASCIMENTO FROM USUARIO;
SELECT CEP FROM USUARIO;
SELECT SENHA FROM USUARIO;
SELECT TELEFONE_CELULAR FROM USUARIO;
SELECT RAZAO_SOCIAL FROM USUARIO;
SELECT CNPJ FROM USUARIO;
SELECT TIPO_USUARIO FROM USUARIO;


-- =========================
-- TABELA NOTIFICACAO
-- =========================
SELECT * FROM NOTIFICACAO;
SELECT ID_NOTIFICACAO FROM NOTIFICACAO;
SELECT TITULO FROM NOTIFICACAO;
SELECT CONTEUDO FROM NOTIFICACAO;
SELECT DATA_POST FROM NOTIFICACAO;
SELECT DATA_ENVIO FROM NOTIFICACAO;
SELECT STATUS FROM NOTIFICACAO;

-- =========================
-- TABELA NOTIFICACAO_USUARIO
-- =========================
SELECT * FROM NOTIFICACAO_USUARIO;
SELECT ID FROM NOTIFICACAO_USUARIO;
SELECT FK_ID_NOTIFICACAO FROM NOTIFICACAO_USUARIO;
SELECT FK_ID_USUARIO FROM NOTIFICACAO_USUARIO;

-- =========================
-- TABELA BOTAO_EMERGENCIA
-- =========================
SELECT * FROM BOTAO_EMERGENCIA;
SELECT ID_EMERGENCIA FROM BOTAO_EMERGENCIA;
SELECT HORA FROM BOTAO_EMERGENCIA;
SELECT DATA_EMERGENCIA FROM BOTAO_EMERGENCIA;
SELECT LOCALIZACAO FROM BOTAO_EMERGENCIA;
SELECT NUMERO_TELEFONE FROM BOTAO_EMERGENCIA;
SELECT REGISTRO_HISTORICO FROM BOTAO_EMERGENCIA;
SELECT FK_ID_USUARIO FROM BOTAO_EMERGENCIA;

-- =========================
-- TABELA SENSORES
-- =========================
SELECT * FROM SENSORES;
SELECT ID_SENSOR FROM SENSORES;
SELECT TIPO_SENSOR FROM SENSORES;
SELECT LOCALIZACAO FROM SENSORES;
SELECT DATA_INSTALACAO FROM SENSORES;
SELECT STATUS_SENSOR FROM SENSORES;

-- =========================
-- TABELA HISTORICO
-- =========================
SELECT * FROM HISTORICO;
SELECT ID_HISTORICO FROM HISTORICO;
SELECT ID_SENSOR FROM HISTORICO;
SELECT DADOS FROM HISTORICO;
SELECT UNIDADE_MEDIDA FROM HISTORICO;
SELECT LATITUDE FROM HISTORICO;
SELECT LONGITUDE FROM HISTORICO;
SELECT DATA_COLETA FROM HISTORICO;
SELECT HORA_COLETA FROM HISTORICO;

-- =========================
-- TABELA AREA_RISCO
-- =========================
SELECT * FROM AREA_RISCO;
SELECT ID_RISCO FROM AREA_RISCO;
SELECT TIPO_RISCO FROM AREA_RISCO;
SELECT DESCRICAO FROM AREA_RISCO;
SELECT RUA FROM AREA_RISCO;
SELECT NUMERO FROM AREA_RISCO;
SELECT CIDADE FROM AREA_RISCO;
SELECT ESTADO FROM AREA_RISCO;
SELECT PAIS FROM AREA_RISCO;
SELECT LATITUDE FROM AREA_RISCO;
SELECT LONGITUDE FROM AREA_RISCO;

-- =========================
-- TABELA SENSOR_AREA_RISCO
-- =========================
SELECT * FROM SENSOR_AREA_RISCO;
SELECT ID FROM SENSOR_AREA_RISCO;
SELECT FK_ID_SENSOR FROM SENSOR_AREA_RISCO;
SELECT FK_ID_RISCO FROM SENSOR_AREA_RISCO;


-- pedir para o chat tirar todos os admin e trocar por outro
-- 1. Usuário + Administrador
SELECT u.ID_USUARIO, u.NOME, a.DATA_LOGIN, a.STATUS
FROM USUARIO u
INNER JOIN ADMINISTRADOR a
  ON u.ID_USUARIO = a.FK_ID_USUARIO;

-- 2. Usuário + Notificação_Usuário + Notificação
SELECT u.NOME, n.TITULO, n.STATUS
FROM USUARIO u
INNER JOIN NOTIFICACAO_USUARIO nu
  ON u.ID_USUARIO = nu.FK_ID_USUARIO
INNER JOIN NOTIFICACAO n
  ON nu.FK_ID_NOTIFICACAO = n.ID_NOTIFICACAO;

-- 3. Botão de Emergência + Usuário
SELECT b.ID_EMERGENCIA, b.LOCALIZACAO, u.EMAIL
FROM BOTAO_EMERGENCIA b
INNER JOIN USUARIO u
  ON b.FK_ID_USUARIO = u.ID_USUARIO;

-- 4. Sensores + Histórico
SELECT s.ID_SENSOR, s.TIPO_SENSOR, h.DADOS, h.UNIDADE_MEDIDA
FROM SENSORES s
INNER JOIN HISTORICO h
  ON s.ID_SENSOR = h.ID_SENSOR;

-- 5. Sensor → Área de Risco → Sensor_Area_Risco
SELECT s.TIPO_SENSOR, ar.TIPO_RISCO, sar.ID
FROM SENSORES s
INNER JOIN SENSOR_AREA_RISCO sar
  ON s.ID_SENSOR = sar.FK_ID_SENSOR
INNER JOIN AREA_RISCO ar
  ON sar.FK_ID_RISCO = ar.ID_RISCO;

-- 6. Administrador + Notificação_Usuário + Notificação
SELECT a.ID_ADMINISTRADOR, u.NOME, n.TITULO
FROM ADMINISTRADOR a
INNER JOIN USUARIO u
  ON a.FK_ID_USUARIO = u.ID_USUARIO
INNER JOIN NOTIFICACAO_USUARIO nu
  ON u.ID_USUARIO = nu.FK_ID_USUARIO
INNER JOIN NOTIFICACAO n
  ON nu.FK_ID_NOTIFICACAO = n.ID_NOTIFICACAO;

-- 7. Histórico + Botão de Emergência (por proximidade de data)
SELECT h.ID_HISTORICO, h.DATA_COLETA, b.DATA_EMERGENCIA, b.LOCALIZACAO
FROM HISTORICO h
INNER JOIN BOTAO_EMERGENCIA b
  ON h.DATA_COLETA = b.DATA_EMERGENCIA;

-- 8. Usuário + Área de Risco (via sensor)
SELECT u.NOME, s.TIPO_SENSOR, ar.CIDADE
FROM USUARIO u
INNER JOIN BOTAO_EMERGENCIA b
  ON u.ID_USUARIO = b.FK_ID_USUARIO
INNER JOIN SENSORES s
  ON b.ID_EMERGENCIA = s.ID_SENSOR  -- exemplo de relação hipotética
INNER JOIN SENSOR_AREA_RISCO sar
  ON s.ID_SENSOR = sar.FK_ID_SENSOR
INNER JOIN AREA_RISCO ar
  ON sar.FK_ID_RISCO = ar.ID_RISCO;

-- 9. Notificação + Botão de Emergência (ambos no mesmo dia)
SELECT n.TITULO, b.LOCALIZACAO, n.DATA_ENVIO
FROM NOTIFICACAO n
INNER JOIN BOTAO_EMERGENCIA b
  ON n.DATA_ENVIO = b.DATA_EMERGENCIA;

-- 10. Usuário + Histórico + Sensores
SELECT u.EMAIL, s.LOCALIZACAO, h.DADOS
FROM USUARIO u
INNER JOIN HISTORICO h
  ON u.ID_USUARIO = h.ID_SENSOR  -- exemplo de relação hipotética
INNER JOIN SENSORES s
  ON h.ID_SENSOR = s.ID_SENSOR;

-- 11. Área de Risco + Notificação (avisos por localidade)
SELECT ar.RUA, ar.CIDADE, n.TITULO
FROM AREA_RISCO ar
INNER JOIN NOTIFICACAO_USUARIO nu
  ON ar.ID_RISCO = nu.FK_ID_NOTIFICACAO  -- exemplo hipotético
INNER JOIN NOTIFICACAO n
  ON nu.FK_ID_NOTIFICACAO = n.ID_NOTIFICACAO;

-- 12. Administrador + Botão de Emergência
SELECT a.ID_ADMINISTRADOR, u.NOME, b.DATA_EMERGENCIA
FROM ADMINISTRADOR a
INNER JOIN USUARIO u
  ON a.FK_ID_USUARIO = u.ID_USUARIO
INNER JOIN BOTAO_EMERGENCIA b
  ON u.ID_USUARIO = b.FK_ID_USUARIO;

-- 13. Sensor + Histórico + Área de Risco
SELECT s.ID_SENSOR, h.DATA_COLETA, ar.TIPO_RISCO
FROM SENSORES s
INNER JOIN HISTORICO h
  ON s.ID_SENSOR = h.ID_SENSOR
INNER JOIN SENSOR_AREA_RISCO sar
  ON s.ID_SENSOR = sar.FK_ID_SENSOR
INNER JOIN AREA_RISCO ar
  ON sar.FK_ID_RISCO = ar.ID_RISCO;

-- 14. Usuário + Notificação_Usuário + Botão de Emergência (mesmo usuário)
SELECT u.NOME, n.TITULO, b.LOCALIZACAO
FROM USUARIO u
INNER JOIN NOTIFICACAO_USUARIO nu
  ON u.ID_USUARIO = nu.FK_ID_USUARIO
INNER JOIN NOTIFICACAO n
  ON nu.FK_ID_NOTIFICACAO = n.ID_NOTIFICACAO
INNER JOIN BOTAO_EMERGENCIA b
  ON u.ID_USUARIO = b.FK_ID_USUARIO;

-- 15. Histórico + Notificação (coletas em dia de alerta)
SELECT h.ID_HISTORICO, h.DATA_COLETA, n.TITULO
FROM HISTORICO h
INNER JOIN NOTIFICACAO n
  ON h.DATA_COLETA = n.DATA_ENVIO;

-- 16. Usuário + Administrador + Notificação_Usuário
SELECT u.NOME, a.STATUS, nu.FK_ID_NOTIFICACAO
FROM USUARIO u
INNER JOIN ADMINISTRADOR a
  ON u.ID_USUARIO = a.FK_ID_USUARIO
INNER JOIN NOTIFICACAO_USUARIO nu
  ON u.ID_USUARIO = nu.FK_ID_USUARIO;

-- 17. Sensor + Botão de Emergência (por local de ocorrência)
SELECT s.TIPO_SENSOR, b.LOCALIZACAO
FROM SENSORES s
INNER JOIN BOTAO_EMERGENCIA b
  ON s.LOCALIZACAO = b.LOCALIZACAO;

-- 18. Notificação + Usuário (via notificação_usuario)
SELECT n.TITULO, u.EMAIL
FROM NOTIFICACAO n
INNER JOIN NOTIFICACAO_USUARIO nu
  ON n.ID_NOTIFICACAO = nu.FK_ID_NOTIFICACAO
INNER JOIN USUARIO u
  ON nu.FK_ID_USUARIO = u.ID_USUARIO;

-- 19. Área de Risco + Sensor_Area_Risco + Sensores + Histórico
SELECT ar.CIDADE, s.TIPO_SENSOR, h.DADOS
FROM AREA_RISCO ar
INNER JOIN SENSOR_AREA_RISCO sar
  ON ar.ID_RISCO = sar.FK_ID_RISCO
INNER JOIN SENSORES s
  ON sar.FK_ID_SENSOR = s.ID_SENSOR
INNER JOIN HISTORICO h
  ON s.ID_SENSOR = h.ID_SENSOR;

-- 20. Administrador + Usuário + Histórico (exemplo hipotético)
SELECT a.ID_ADMINISTRADOR, u.NOME, h.DATA_COLETA
FROM ADMINISTRADOR a
INNER JOIN USUARIO u
  ON a.FK_ID_USUARIO = u.ID_USUARIO
INNER JOIN HISTORICO h
  ON u.ID_USUARIO = h.ID_SENSOR;
  
  
  -- inner join para o banco --
  
  SELECT * 
  FROM HISTORICO H
  INNER JOIN SENSORES S ON (S.ID_SENSOR = H.ID_SENSOR);
 
 
 SELECT H.*, S.TIPO_SENSOR, S.LOCALIZACAO
             FROM HISTORICO H
             JOIN SENSORES S ON H.ID_SENSOR = S.ID_SENSOR
             ORDER BY H.DATA_COLETA DESC, H.HORA_COLETA DESC;