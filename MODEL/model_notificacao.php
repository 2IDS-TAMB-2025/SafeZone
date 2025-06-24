<?php

     include_once '../CONTROLLER/conecta_banco.php';

class Notificacao{


    /*POST*/
   public function inserirNotificacao($ID_NOTIFICACAO, $TITULO, $CONTEUDO, $DATA_POST, $DATA_ENVIO, $STATUS){
    $conn = DataBase::getConnection();
    $stmt = $conn->prepare("INSERT INTO NOTIFICACAO (ID_NOTIFICACAO, TITULO, CONTEUDO, DATA_POST, DATA_ENVIO, STATUS)
                             VALUES (?, ?, ?, ?, ?, ?)");  
    
    $stmt->bind_param("isssss", $ID_NOTIFICACAO, $TITULO, $CONTEUDO, $DATA_POST, $DATA_ENVIO, $STATUS);
    $result = $stmt->execute();

    $stmt->close();
    $conn->close();

    return $result;
}

   
   
    /*GET*/
      public function getNotificacaoId($ID_NOTIFICACAO){
            $conn = Database::getConnection();
            $stmt = $conn->prepare("SELECT * FROM USUARIO WHERE ID_NOTIFICACAO = ?");
            $stmt->bind_param("i", $ID_NOTIFICACAO);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
      }

     public function getNotificacaoTitulo($TITULO){
            $conn = Database::getConnection();
            $stmt = $conn->prepare("SELECT * FROM USUARIO WHERE TITULO = ?");
            $stmt->bind_param("s", $TITULO);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }

         public function getNotificacaoConteudo($CONTEUDO){
            $conn = Database::getConnection();
            $stmt = $conn->prepare("SELECT * FROM USUARIO WHERE CONTEUDO = ?");
            $stmt->bind_param("s", $CONTEUDO);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }

         public function getNotificacaoDataPost($DATA_POST){
            $conn = Database::getConnection();
            $stmt = $conn->prepare("SELECT * FROM USUARIO WHERE DATA_POST = ?");
            $stmt->bind_param("d", $DATA_POST);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }

         public function getNotificacaoDataEnvio($DATA_ENVIO){
            $conn = Database::getConnection();
            $stmt = $conn->prepare("SELECT * FROM USUARIO WHERE DATA_ENVIO = ?");
            $stmt->bind_param("d", $DATA_ENVIO);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }

         public function getNotificacaoStatus($STATUS){
            $conn = Database::getConnection();
            $stmt = $conn->prepare("SELECT * FROM USUARIO WHERE STATUS = ?");
            $stmt->bind_param("s", $STATUS);
            $stmt->execute();
            $result = $stmt->get_result();
            return $result->fetch_all(MYSQLI_ASSOC);
        }

       

        /*PUT*/
        public function editaNotificacao($ID_NOTIFICACAO, $TITULO, $CONTEUDO, $DATA_POST, $DATA_ENVIO, $STATUS){
                        $conn = DataBase::getConnction();
                        $stmt = $conn->prepare("UPDATE NOTIFICACAO SET ID_NOTIFICACAO = ?, TITULO = ?, CONTEUDO = ?, DATA_POST = ?, DATA_ENVIO = ?, STATUS = ?");  
                       
                        $stmt->bind_param("issdds", $ID_NOTIFICACAO, $TITULO, $CONTEUDO, $DATA_POST, $DATA_ENVIO, $STATUS);
                        $stmt->execute();
                        $stmt->close();
                        $conn->close();
                        return $result;
                    }
       

        /*POST*/
         public function excluirNotificacaoId($ID_NOTIFICACAO){
            $conn = Database::getConnection();
            $stmt = $conn->prepare("DELETE FROM USUARIO WHERE ID_NOTIFICACAO = ?");
            $stmt->bind_param("i", $ID_NOTIFICACAO);
            $stmt->execute();
            $result =$stmt->get_result();
            $stmt->close();
            $conn->close();
           return $result;
         }

         public function excluirNotificacaoTitulo($TITULO){
            $conn = Database::getConnection();
            $stmt = $conn->prepare("DELETE FROM USUARIO WHERE TITULO = ?");
            $stmt->bind_param("s", $TITULO);
            $stmt->execute();
            $result =$stmt->get_result();
            $stmt->close();
            $conn->close();
           return $result;
         }

         public function excluirNotificacaoConteudo($CONTEUDO){
            $conn = Database::getConnection();
            $stmt = $conn->prepare("DELETE FROM USUARIO WHERE CONTEUDO = ?");
            $stmt->bind_param("s", $CONTEUDO);
            $stmt->execute();
            $result =$stmt->get_result();
            $stmt->close();
            $conn->close();
           return $result;
         }

         public function excluirNotificacaoDataPost($DATA_POST){
            $conn = Database::getConnection();
            $stmt = $conn->prepare("DELETE FROM USUARIO WHERE DATA_POST = ?");
            $stmt->bind_param("d", $DATA_POST);
            $stmt->execute();
            $result =$stmt->get_result();
            $stmt->close();
            $conn->close();
           return $result;
         }

         public function excluirNotificacaoStatus($STATUS){
            $conn = Database::getConnection();
            $stmt = $conn->prepare("DELETE FROM USUARIO WHERE STATUS = ?");
            $stmt->bind_param("s", $STATUS);
            $stmt->execute();
            $result =$stmt->get_result();
            $stmt->close();
            $conn->close();
           return $result;
         }
   
}
