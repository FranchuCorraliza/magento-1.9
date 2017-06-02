<?php
session_start();
if(isset($_SESSION['email'])||isset($_COOKIE['login'])!="") {
    include'./html/header.php';
    ?>

    <?php
    include'./html/cabecera.php';
    ?>


            <?php

            include'./html/paneles.php';
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