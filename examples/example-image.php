ds
<?php
    require '../lib/DrawSideViews.php';

    // Inicia a classe passando a largura, altura e cor de fundo da imagem como parâmetro
    $img = new DrawSideViews(840, 1240, 'FAFAFA');

    // Encerre, caso não venha nada via POST
    if( empty($_POST['field']) ){
        return;
    }

    // Espaçamento em pixels das colunas
    $img->columnSpacement = 1;

    // Define o valor cadastrado para a profundidade do poço em metro
    // Este valor é calculado automaticamente caso não seja definido
    // por setDepth().
    if( !empty( $_POST['depth'] ) ) $img->setDepth( $_POST['depth'] );

    // Envia os dados dos perfis
    $img->setData($_POST['field']);

    // Recebe a imagem
    $img->show();

    // Elimina as variáveis
    $img = null;
?>
    
