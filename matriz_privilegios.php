<?php
session_start();
include 'vendor/php/querys.php';

//Conexon a la base de datos
try {
  $pdo = new PDO('mysql:host=localhost;dbname=inti_sistema_ale', 'oscar','1234');
} catch (PDOException $e) {
  print "¡Error!: " . $e->getMessage() . "<br/>";
  die();
}

$sql=$query_buscar_usuarios;
$sentencia=$pdo->prepare($sql);
$sentencia->execute();
$resultado=$sentencia->fetchAll();

if(!$_GET){ //pagina por defecto 1
  header('Location:matriz_privilegios.php?pagina=1');
}

$usuarios_x_paginas=5; //Cantidad de usuarios que se visualizan por pagina
$total_usuarios_db=$sentencia->rowCount(); //Contar cantidad de usuarios en total
$paginas=$total_usuarios_db/5; //Dividir usuarios por pagina
$paginas= ceil($paginas); //Redondear para que la division sea entera

//Condicion para redirigir a la pagina por defecto si se ingresa una apgina inexistente
if($_GET['pagina']>$paginas || $_GET['pagina']<=0){
  echo 'No existe el número de pagina'; //Es comveniente crear pagina de error
}

//Comprobamos si el usuario está logueado
//Si no lo está, se le redirecciona al index
//Si lo está, definimos el botón de cerrar sesión y la duración de la sesión
if(!isset($_SESSION['usuario']) and $_SESSION['estado'] != 'Autenticado') {
	header('Location: index.php');
} else {
	$estado = $_SESSION['usuario'];
	require('vendor/php/sesiones.php');
};
               
?>

<!DOCTYPE html>
<html lang="en">

<!-- Header include-->
<?php $title = "Nuevo Cliente"; 
      echo '<link href="css/matriz-privi.css" rel="stylesheet">';
      include 'vendor/php/includes/header.php'; ?>

<body id="page-top">

  <!-- Navbar include -->
  <?php include 'vendor/php/includes/navbar.php' ?>

  <div id="wrapper">

    <!-- Sidebar include-->
    <?php include 'vendor/php/includes/sidebar.php' ?>

    <div id="content-wrapper">

      <div class="container-fluid">

        <!-- Breadcrumbs-->
        <ol class="breadcrumb">
          <li class="breadcrumb-item">
            <a href="#">Usuarios</a>
          </li>
          <li class="breadcrumb-item active">Matriz de control</li>
        </ol>

        <!-- Page Content -->
        <div class="card mb-3">
          <div class="card-header">
            <i class="fas fa-fw fa-plus-circle"></i>
            Elegir usuario y definir privilegios</div>
          <div class="card-body">
              <div class="table-wrapper">
                <div class="table-title">
                    <div class="row">
                      <div class="col-sm-5">
                        <h2>Control de usuarios</h2>
                      </div>
                      <div class="col-sm-7">
                        <a href="#" class="btn btn-primary"><i class="material-icons">&#xE147;</i> <span>Agregar usuario</span></a>						
                      </div>
                    </div>
                </div>
                <div class="table-responsive">
                  <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>						
                            <th>Alta del usuario</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Modificar</th>
                            <th>Eliminar</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                    <?php //$query_buscar_usuarios
                    $iniciar= ($_GET['pagina']-1)*$usuarios_x_paginas;
                    
                    $sql_usuarios=$query_limitar_usuarios;
                    $sentencia_usuarios= $pdo->prepare($sql_usuarios);
                    $sentencia_usuarios->bindParam(':iniciar', $iniciar,PDO::PARAM_INT);
                    $sentencia_usuarios->bindParam(':nusuarios', $usuarios_x_paginas,PDO::PARAM_INT);
                    $sentencia_usuarios->execute();
                    $resultado_usuarios= $sentencia_usuarios->fetchAll();

                    foreach($resultado_usuarios as $row): ?>
                       <tr>
                          <td><?php echo $row['id_usuario'];?></td>
                          <td><img src="imagenes/user.png" class="avatar" alt="Avatar"><?php echo $row['usuario'];?></td>
                          <td><?php echo $row['fecha_alta'];?></td>
                          <td><?php echo $row['rol'];?></td>
                          <?php    
                          if ($row['visible']){ ?> 
                             <td><span class="status text-success">&bull;</span> Activo </td>
                             <?php } else {?>
                             <td><span class="status text-danger">&bull;</span> Suspendido</td> 
                             <?php }?>
                             <td><a href="#" class="settings" title="Settings" data-toggle="tooltip"><i class="material-icons">&#xE8B8;</i></a></td>
                             <td><a href="vendor/php/borrado_logico.php?usuario= <?php echo $row['id_usuario'];?>" onclick= "return confirmation()" class="delete" title="Delete" data-toggle="tooltip"><i class="material-icons">&#xE5C9;</i></a></td>
                       </tr>
                    <?php endforeach ?> 
                  </tbody>
                </table>           
                <div class="clearfix">
                  <div class="hint-text">Mostrado <b>5</b> de <b>25</b> entradas</div>
                  <ul class="pagination">
                      <li class="page-item"><a href="matriz_privilegios.php?pagina=<?php  echo $_GET['pagina']-1 ?>">Previo</a></li>
                    <?php for($i=0;$i<$paginas;$i++): ?>
                      <li class="page-item <?php echo $_GET['pagina']==$i+1? 'active': ''  ?>"><a href="matriz_privilegios.php?pagina=<?php echo $i+1; ?>" class="page-link"><?php echo $i+1; ?></a></li>
                     <?php endfor ?>
                      <li class="page-item <?php echo $_GET['pagina']>=$paginas? 'disabled': '' ?>"><a href="matriz_privilegios.php?pagina=<?php  echo $_GET['pagina']+1 ?>">Siguiente</a></li>
                  </ul>
                </div>  
              </div>

            </div>
          </div>
        </div>
      </div>

      </div>
      <!-- /.container-fluid -->

     <!-- Footer include -->            
     <?php include 'vendor/php/includes/footer.php' ?>

    </div>
    <!-- /.content-wrapper -->

  </div>
  <!-- /#wrapper -->

  <!-- Scroll to Top Button-->
  <a class="scroll-to-top rounded" href="#page-top">
    <i class="fas fa-angle-up"></i>
  </a>

  <!-- Logout Modal include-->
  <?php include 'vendor/php/includes/logout.php'?>

  <!-- Scripts include-->
  <?php include 'vendor/php/includes/scripts.php'?>

 <!--Script Confirmacion-->
 <script type="text/javascript">
          function confirmation() 
          {
              if(confirm("Desea seguir?"))
        {
          return true;
        }
        else
        {
          return false;
        }
          }
</script>

</body>

</html>
