<?php
require '../lib/Draw.php';
$img = new Draw( 640, 640, '#ffffff' );


// $img->ellipse( 10, 10, 100,50, '#ff0000' );             // Elipse preenchida vermelha
// $img->ellipse( 10, 120, 100,50, '#ff0000', false );      // Elipse vazada com as linhas vermelhas

// $img->circle( 10, 10, 100, '#ff0000' );                 // Círculo preenchido vermelho
// $img->circle( 10, 120, 100, '#ff0000', false );          // Círculo vazado com as linhas vermelhas

// $img->rectangle( 10, 10, 100, 50, '#ff0000' );          // Retângulo preenchido vermelho
// $img->rectangle( 150, 10, 100, 50, '#ffff00', false );  // Retângulo vazado com as linhas vermelhas

// $img->line( 10, 10, 100, 0, '#ff0000', false )          // Retângulo preenchido vermelho
//     ->line( 150, 10, 100, 0, '#ff0000', true )          // Retângulo vazado com as linhas vermelhas
//     ->line( 150, 50, 100, 0, '#ff0000', true, 1, 1 )    // Retângulo vazado com as linhas vermelhas
//     ->line( 150, 100, 100, 0, '#ff0000', true, 5, 5 )   // Retângulo vazado com as linhas vermelhas
//     ->line( 150, 150, 100, 0, '#ff0000', true, 2, 10 )  // Retângulo vazado com as linhas vermelhas
//     ->line( 150, 200, 100, 0, '#ff0000', false );       // Retângulo preenchido vermelho

$img->addTexture( 10, 10, 50, 100, '../public/tiles/carvaoativado.png',  '#00f' );
$img->addTexture( 70, 10, 50, 100, '../public/tiles/carvaoativado.png',  '#f00' );
$img->addTexture( 130, 10, 50, 100, '../public/tiles/carvaoativado.png', '#ff0' );
$img->addTexture( 10, 120, 50, 100, '../public/tiles/carvaoativado.png', '#0ff' );
$img->addTexture( 70, 120, 50, 100, '../public/tiles/carvaoativado.png', '#0f0' );
$img->addTexture( 130, 120, 50, 100, '../public/tiles/carvaoativado.png','#f0f' );


$img
    //->circle( 100, 100, 100,             '#ff0000', false )
    // ->arc(    100, 100, 500, 70, 0, 270, '#ff0000', true, IMG_ARC_NOFILL  )
    // ->arc(    100, 200, 500, 70, 0, 270, '#ff0000', true, IMG_ARC_EDGED|IMG_ARC_NOFILL  )
    // ->arc(    100, 300, 500, 70, 0, 270, '#ff0000', true, IMG_ARC_CHORD|IMG_ARC_NOFILL  )
    // ->arc(    100, 400, 500, 70, 0, 270, '#ff0000', true, IMG_ARC_EDGED  )
    // ->arc(    100, 500, 500, 70, 0, 270, '#ff0000', false )


    // ->circle( 0, 0, 100, '#000' )
    // ->polygon( 0, 10, 100, 3, '#f00' , true  )
    // ->polygon( 0, 10, 100, 4, '#f00' , false )
    // ->polygon( 0, 10, 100, 5, '#f00' , false )
    // ->polygon( 0, 10, 100, 6, '#f00' , false )
    // ->polygon( 0, 10, 200, 7, '#f00' , false )
    // ->polygon( 0, 10, 300, 8, '#f00' , false )
    // ->polygon( 0, 10, 300, 9, '#f00' , false )
    // ->polygon( 0, 10, 300, 10, '#f00', false )
    // ->polygon( 0, 10, 300, 11, '#f00', false )
    // ->polygon( 0, 10, 300, 12, '#f00', false )
;

    $img
    // ->asGIF()
    // ->download()
    ->show()
    ;