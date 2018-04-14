<?php
require 'Draw.php';

( new Draw(100, 50 ) )->tiles( 1000, 50, './tiles/water.png', 'c0c0c0' );

function tiles(){
    // Imagem vazia com uma cor de fundo
    $im = (new Draw(50, 800, '8888c0'))->img;

    // $stamp = imagecreatefromxxx('./tiles/claudio.jpeg');
    $stamp = imagecreatefromxxx('./tiles/water.png');

    // Dimensões da imagem de background
    $bgWidth    = imagesx($im);
    $bgHeight   = imagesy($im);
    // Dimensões do ladrilho
    $tileWidth  = imagesx($stamp);
    $tileHeight = imagesy($stamp);
    // Linhas e Colunas a serem repetidas
    $cols = $bgWidth  > $tileWidth  ? ceil( $bgWidth/$tileWidth )   : 1;
    $rows = $bgHeight > $tileHeight ? ceil( $bgHeight/$tileHeight ) : 1;
    //
    for( $i = 0; $i < $cols; $i++ ){
        for( $j = 0;$j < $rows; $j++ ){
            imagecopymerge_alpha( $im, $stamp, $i * $tileWidth, $j * $tileHeight, 0,0, imagesx($stamp), imagesy($stamp), 100 );
        }
    }

    header('Content-type: image/png');
    imagepng($im);
    imagedestroy($im);
}





function imagecreatefromxxx($path){
    $extension = extension($path);
    switch( $extension ){
        case 'png' : return imagecreatefrompng( $path ); break;
        case 'jpeg' : return imagecreatefromjpeg( $path ); break;
        case 'jpg' : return imagecreatefromjpeg( $path ); break;
    }
    return extension($path) === 'png' ? imagecreatefrompng( $path ) : imagecreatefromjpeg( $path );
}

function extension($path){
    return array_reverse( explode( '.', $path ) )[0];
}

function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){ 
    // creating a cut resource 
    $cut = imagecreatetruecolor($src_w, $src_h); 

    // copying relevant section from background to the cut resource 
    imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h); 
    
    // copying relevant section from watermark to the cut resource 
    imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h); 
    
    // insert cut resource to destination image 
    imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct); 
} 
