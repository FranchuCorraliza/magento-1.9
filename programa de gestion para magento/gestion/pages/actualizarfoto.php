<?php
session_start();
if(isset($_SESSION['email'])||isset($_COOKIE['login'])) {
    include'./html/header.php';
    ?>

    <?php
    include'./html/cabecerasubirActualizarFotos.php';
    ?>
    <form action="../classes/uploadUpdate.php" method="post" enctype="multipart/form-data" name="inscripcion">
	
        <div class="form-group">
            <label>Selecciona las imagenes de la carpeta que quieres subir</label>
            <input type="file" name="archivo[]" multiple="multiple">
            <br/>
            <button type="submit" class="btn btn-info"><i class="fa fa-cloud-upload  fa-fw"></i> Subir carpeta</button>
        </div>
    </form>
    <?php
    if(isset($_SESSION['mensajes'])){
        echo $_SESSION['mensajes'];
        session_unset('mensajes');
    }

    //include'./html/paneles.php';
    ?>
    </div>
    <?php
    include './html/sidebar.php';
    ?>
    <!-- /.row -->
    </div>
    <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->
    <?php
    include './html/footer.php';
}
else{
    $_SESSION['mensajes']="Usuario incorrecto";
    echo '<script type="text/javascript" language="JavaScript">location.href = "../index.php";</script>';
}
?>