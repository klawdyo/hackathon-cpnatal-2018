 ds
<?php

    require 'DrawSideViews.php';

    //Inicia a classe passando a largura e altura da imagem como parâmetro
    $img = new DrawSideViews(640, 640, 'FAFAFA');

    /**
     * Configurações
     * 
     * Campos do array
     * id               : ID do banco de dados, em um futuro banco.
     * type             : Tipo de perfil, podendo ser qualquer um desses abaixo:
     *      coating: Revestimento. É o revestimento do poço
     *      complementary: Anular/Complemento. É o material utilizdo para ajustar o revestimento ao solo
     *      filter: Filtro. Material utilizado para filtragem
     *      lythologic: Informações do tipo Litologia
     *      diameter: 
     *  material_name    : Nome do Material, que será utilizado na legenda
     *  background_color : Cor de fundo, que será utilizada no desenho e em sua legenda em hexadecimal
     *  m_initial        : Metragem inicial do atributo atual em metros
     *  m_final          : Metragem final do atributo atual em metros
     *  slot             : Ranhura do filtro em mm. Usado somente para os filtros
     *  diameter         : Diâmetro. Valor do diâmetro do poço de acordo com a profundidade, em polegadas.
     * 
     */

    $sideviews = [
        //Revestimento
        // Campos obrigatórios: id, type, material_name, background_color, m_initial, m_final
        [ 'id' => 1, 'type' => 'coating', 'material_name' => 'Tubo PVC', 'background_color' => '#D8D8D8', 'm_initial' => '0', 'm_final' => '3' ],
        [ 'id' => 2, 'type' => 'coating', 'material_name' => 'Cimento', 'background_color' => '#838383', 'm_initial' => '4', 'm_final' => '7' ],
        [ 'id' => 3, 'type' => 'coating', 'material_name' => 'Tubo PVC', 'background_color' => '#D8D8D8', 'm_initial' => '7', 'm_final' => '9' ],
        [ 'id' => 4, 'type' => 'coating', 'material_name' => 'Cimento', 'background_color' => '#838383', 'm_initial' => '9', 'm_final' => '10' ],
        
        //Filtro
        //Campos Obrigatórios: id, type, material_name, background_color, m_initial, m_final, slot
        [ 'id' => 5, 'type' => 'filter', 'material_name' => 'Pré-Filtro', 'background_color' => '#C16D99', 'm_initial' => '0', 'm_final' => '2', 'slot' => '1.2' ],
        [ 'id' => 6, 'type' => 'filter', 'material_name' => 'Filtro de Carvão Ativado', 'background_color' => '#90C16D', 'm_initial' => '6', 'm_final' => '7', 'slot' => '2.6' ],
        
        // Perfil Litológico
        // Campos obrigatórios: id, type, material_name, background_color, m_initial, m_final
        [ 'id' => 7, 'type' => 'lythologic', 'material_name' => 'Cascalho', 'background_color' => '#4268f4', 'm_initial' => '0', 'm_final' => '3'],
        [ 'id' => 8, 'type' => 'lythologic', 'material_name' => 'Arenito', 'background_color' => '#83f441', 'm_initial' => '3', 'm_final' => '5'],
        [ 'id' => 9, 'type' => 'lythologic', 'material_name' => 'Argila', 'background_color' => '#a6f441', 'm_initial' => '5', 'm_final' => '7'],
        [ 'id' => 10, 'type' => 'lythologic', 'material_name' => 'Calcário', 'background_color' => '#b5b72f', 'm_initial' => '7', 'm_final' => '10'],

        // Diâmetro    
        // Campos obrigatórios: id ,type, m_initial, m_final, diameter    
        [ 'id' => 10, 'type' => 'diameter', 'm_initial' => '0', 'm_final' => '5', 'diameter' => '2'],
        [ 'id' => 10, 'type' => 'diameter', 'm_initial' => '5', 'm_final' => '8', 'diameter' => '1.4'],
        [ 'id' => 10, 'type' => 'diameter', 'm_initial' => '8', 'm_final' => '10', 'diameter' => '0.8'],
        
        // Anular/Complemento
        // id, type, material_name,background_color, m_initial, m_final
        [ 'id' => 1, 'type' => 'complementary', 'material_name' => 'Areia', 'background_color' => '#b7972e', 'm_initial' => '0', 'm_final' => '7' ],
        

    ];

    if( empty($_POST['field']) ){
        return;
    }

    // Define o valor cadastrado para a profundidade do poço em metro
    // Esse valor virá do banco de dados. Ele não é calculado automaticamente para 
    // que a informação p
    if(isset($_POST['depth']) && !empty($_POST['depth'])) $img->setDepth( $_POST['depth'] );

    //Define o maior valor dentre os perfis cadastrados para fins de cálculo dos perfis
    //Basicamente é o maior profundidade registrada em qualquer dos perfis
    //$img->setMaxHeight($max_height);

    //Envia os dados dos perfis
    $img->setData($_POST['field']);

    //Recebe a imagem
    $img->show();

    //Elimina as variáveis
    $img = null;
?>
    