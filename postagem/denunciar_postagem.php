<?php
session_start();
require '../etc/banco.php';


if (!isset($_SESSION['id_usuario'])) {
    header("Location:../etc/login.php");
}
$id_usuario = $_SESSION['id_usuario'];
$id_postagem = $_REQUEST['id_postagem'];
// o mesmo usuário não deveria denunciar mais de uma vez
    
$pdo = Banco::conectar();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql =  "SELECT COUNT(id_denuncia_postagem) AS num FROM denuncia_postagem WHERE id_usuario LIKE '$id_usuario' AND id_postagem LIKE '$id_postagem'";
$query = $pdo->prepare($sql);
$query->execute([$sql]);
$number_of_rows = $query->fetchColumn();
Banco::desconectar();


    //Sobe o número de denúncias na tabela postagem
    if ($number_of_rows == 0) {
    $pdo = Banco::conectar();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "UPDATE postagem set denuncias=denuncias+1 where id_postagem = ".$id_postagem;
    $q = $pdo->prepare($sql);
    $q->execute(array($id_postagem));
    Banco::desconectar();
    }
    
//Pega o número de denúncias, variável na tabela postagem
$pdo = Banco::conectar();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql = "SELECT * from postagem where id_postagem = ".$id_postagem;
$q = $pdo->prepare($sql);
$q->execute(array($id_postagem));
$data = $q->fetch(PDO::FETCH_ASSOC);
$denuncias = $data['denuncias'];
$id_postagem_criador  =  $data['id_usuario'];
Banco::desconectar();
echo $denuncias;
header("Location: ../postagem/postagem.php?id_postagem=".$id_postagem."#denunciaSucesso");

//Conferir o número de usuários para o select seguinte
$pdo = Banco::conectar();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$sql =  "SELECT COUNT(id_usuario) AS num FROM usuario";
$query = $pdo->prepare($sql);
$query->execute([$sql]);
$number_of_users = $query->fetchColumn();
Banco::desconectar();

//Define a quantidade de denúncias baseada na quantidade (em porcentagem) de usuários. Valor atual: 20%.
$how_much = $number_of_users * (20/100);

//A partir do select anterior vai deixar a postagem privada se denúncias >= how_much
if ($denuncias >= $how_much) {
    $pdo = Banco::conectar();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "UPDATE usuario set denuncias=denuncias+1 where id_usuario = ".$id_postagem_criador;
    $q = $pdo->prepare($sql);
    $q->execute(array($id_postagem_criador));
    Banco::desconectar();

    $pdo = Banco::conectar();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $sql = "UPDATE postagem set privado=1 where id_postagem = ".$id_postagem;
    $q = $pdo->prepare($sql);
    $q->execute(array($id_postagem));
Banco::desconectar();
  //    header("Location: ../usuario/perfil.php");

}

//Cadastra a denúncia na tabela denuncia_postagem
$pdo = Banco::conectar();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $sql = "INSERT INTO denuncia_postagem (id_usuario, id_postagem) VALUES(?,?)";
        $q = $pdo->prepare($sql);
        $q->execute(array($id_usuario, $id_postagem));
        Banco::desconectar();
      //  header("Location: ../etc/login.php");

    ?>
 