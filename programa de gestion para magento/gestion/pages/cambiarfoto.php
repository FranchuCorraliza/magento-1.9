<?php
session_start();
if(isset($_SESSION['email'])||isset($_COOKIE['login'])) {
    include'./html/header.php';
    ?>

    <?php
    include'./html/cabecerasubirCambiarFotos.php';
    ?>
   <div class="container">

        <div class="tab-content">
            <div id="search" class="tab-pane fade in active">
                <form action="mostrarProductos.php">
                    <div class="form-group">
                        <label for="Albaran">Albaran</label>
                        <input type="text" id="albaranB" class="form-control" aria-describedby="Albaran" placeholder="Albaran" name="albaran">
                    </div>
                    <div class="form-group">
                        <label for="Marca">Marca</label>
                        <input type="text" id="marcaB" class="form-control" aria-describedby="Marca" placeholder="Marca" name="marca">
                    </div>
                    <div class="form-group">
                        <label for="Referencia">Referencia</label>
                        <input type="text" id="referenciaB" class="form-control" aria-describedby="Referencia" placeholder="Referencia" name="referencia">
                    </div>
                    <div class="form-group">
                        <label for="Nombre">Nombre</label>
                        <input type="text" id="nombreB" class="form-control" aria-describedby="Nombre" placeholder="Nombre" name="nombre">
                    </div>
                    <div class="form-group">
                        <label for="Tipo">Tipo</label>
                        <input type="text" id="tipoB" class="form-control" aria-describedby="Tipo" placeholder="Tipo" name="tipo">
                    </div>
                    <div class="form-group">
                        <label for="Temporada">Temporada</label>
                        <input type="text" id="temporadaB" class="form-control" aria-describedby="Temporada" placeholder="Temporada" name="temporada">
                    </div>
                    <div class="form-group">
                        <label for="Codigo">Codigo</label>
                        <input type="text" id="codigoB" class="form-control" aria-describedby="Codigo" placeholder="Codigo" name="codigo">
                    </div>
                    <div class="form-group col-xs-12 col-sm-3">
                        <label for="editor_pics">Editor Pics</label>
                        <input type='checkbox' aria-describedby="editor_pics" placeholder="editor_pics" name='editor_pics' value='1'>
                    </div>
                    <div class="form-group col-xs-12 col-sm-3">
                        <label for="instock">En Stock</label>
                        <input type='checkbox' aria-describedby="instock" placeholder="instock" name='instock' value='1'>
                    </div>
                    <div class="form-group col-xs-12 col-sm-3">
                        <label for="publicado">Publicados</label>
                        <input type='checkbox' aria-describedby="publicado" placeholder="publicado" name='publicado' value='1'>
                    </div>
                    <button type="submit" class="btn btn-primary" id="buscadorB">Buscar</button>
                </form>
                <div class="table-responsive">
                    <table class="table">

                    </table>
                </div>
            </div>
        </div>
    </div>
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