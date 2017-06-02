<?php
session_start();
if(isset($_SESSION['email'])||isset($_COOKIE['login'])) {

    include'./html/header.php';
	include'./html/cabeceraContenidos.php';
	
?>
	<iframe style="width:100%;height:5000px;border:none;" src="https://www.elitestores.com/scripts/updateDescriptions/"></iframe>
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

