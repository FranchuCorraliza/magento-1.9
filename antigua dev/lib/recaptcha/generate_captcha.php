<?php
      //generate a random string
      $md5_hash = md5(rand(0,999));
      //We don’t need a 32 character long string so we trim it down to 5
      $code = substr($md5_hash, 15, 5);
      $width =120;
      $height = 40;
      $font_size = $height * 0.75;
  
      $image = imagecreate($width, $height) ;
 
      /* set the colours */
  
      $background_color = imagecolorallocate($image, 255, 255, 255);
  
      $text_color = imagecolorallocate($image, 20, 40, 100);
  
      $noise_color = imagecolorallocate($image, 100, 120, 180);
 
      /* generate random lines in background */
  
      for( $i=0; $i<($width*$height)/150; $i++ ) {
  
      imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
  
      }
       $font = 'ARLRDBD.ttf';

      /* create textbox and add text */
  
      $textbox = imagettfbbox($font_size, 0, $font, $code) ;
  
      $x = ($width - $textbox[4])/2;
  
      $y = ($height - $textbox[5])/2;
  
      imagettftext($image, $font_size, 0, $x, $y, $text_color, $font , $code) ;
  
      /* output captcha image to browser */
  
      header("Content-Type: image/jpeg");
  
      imagejpeg($image);
  
      imagedestroy($image);
  
      //save the generated security code as a session variable
  
      $_SESSION[‘captcha_code’] = $code;
  
      ?>

