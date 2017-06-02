<?php
session_start();
if(isset($_SESSION['email'])||isset($_COOKIE['login'])) {
    include'./html/header.php';
    ?>

    <?php
    include'./html/cabecerasubir.php';
    ?>
    <form action="../classes/upload.php" method="post" enctype="multipart/form-data" name="inscripcion">
        <div class="form-group">
            <label>Selecciona la carpeta que quieres subir</label>
            <input type="file" name="archivo[]" multiple="multiple">
            <br/>
            <button type="submit" class="btn btn-info"><i class="fa fa-cloud-upload  fa-fw"></i> Subir carpeta</button>
            <?php if(isset($_SESSION['mensajes'])){
                if(isset($_SESSION['success'])=="1"){
                    session_unset('success');
                    echo '<a href="http://192.168.1.200/importador/crearItemsNuevos.php" target="_blank" class="btn btn-success" role="button">Publicar Artículos</a>';
                }
            }
            ?>
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