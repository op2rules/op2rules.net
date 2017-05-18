<?php
session_start();
$randNum = rand(1,6);

function genChars($length = 6) {
    // Available characters
    $chars = '234qwertasdfzxcv';

    $Code  = '';
    // Generate code
    for ($i = 0; $i < $length; ++$i)
    {
        $Code .= substr($chars, (((int) mt_rand(0,strlen($chars))) - 1),1);
    }
return $Code;
}

// Usage
$scramble = genChars($randNum);

$captcha = imagecreatefrompng("./captcha.png"); 

$rand1 = rand(0,255);
$rand2 = rand(0,255);
$rand3 = rand(0,255);
$rand4 = rand(0,255);
$rand5 = rand(0,255);
$rand6 = rand(0,255);

$black = imagecolorallocate($captcha, 0, 0, 0); 
$line1 = imagecolorallocate($captcha,$rand1,$rand2,$rand3); 
$line2 = imagecolorallocate($captcha,$rand6,$rand5,$rand4); 
$line3 = imagecolorallocate($captcha,$rand1,$rand1,$rand6);

$rand7 = rand(0,175);
$rand8 = rand(0,55);

// bool imageline ( resource $image , int $x1 , int $y1 , int $x2 , int $y2 , int $color )
imageline($captcha,$rand7,$rand1,$rand1,$rand7,$line1); 
imageline($captcha,40,$rand8,$rand4,$rand8,$line2);
imageline($captcha,$rand8,$rand7,64,$rand8,$line3);

$fonts = array("./ff.ttf", "./choco.ttf");
$font = $fonts[rand(0,count($fonts) - 1)];

$rotation = rand(-10,12);
$x = 20;
if($rotation > 0) { $y = 40; } elseif($rotation < 0) { $y = 30; } else { $y = 35; }
imagettftext($captcha, 25, $rotation, $x, $y, $black, $font, $scramble);


imageline($captcha,$rand7,0,$rand1,55,$line1); 
imageline($captcha,40,$rand8,55,$rand2,$line2);
imageline($captcha,$rand8,$rand7,64,$rand3,$line3);


$_SESSION['key'] = md5($scramble); 

header("Content-type: image/png");  
imagepng($captcha);
imagedestroy($captcha);

?>