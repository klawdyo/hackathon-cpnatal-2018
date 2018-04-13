<?php
ob_end_clean();
require 'Draw.php';
class DrawSideViews extends Draw{
    /*
      @var
      Fonte padrão, relativo a /public/fonts
     */
    public $font = 'Hack-Regular.ttf';

    /*  @var
        Armazena os dados
     */
    public $data = [];

    /*  @var
        Armazena os dados da maior profundidade informada nos perfis
     */
    public $maxHeight = 0;

    /*  @var
        Armazena os dados da profundidade informada no cadastro do ponto
     */
    public $depth = 0;

    /*  @var
        Armazena os dados da legenda inferior
     */
    public $labels = [];

    /*
       @var
       $ratio é a proporção entre os metros do poço e o tamanho disponível
       para desenho na imagem
     */
    public $ratio = 1;

    /*
        @var
        Armazena os erros
    */
    public $errors = false;
    /*
        @var
        Profundidade maior. Ou é o perfil mais fundo informado ou a profundidade nominal do ponto
     */
    public $maxDepth;

    /*
     * setData
     * Define os dados
     */
    public function setData($data){
        $this->data = $data;
    }

    /*
     * setData
     * Define os dados
     */
    public function setDepth($depth){
        $this->depth = $depth;
    }

    /*
     * setData
     * Define os dados
     */
    public function setMaxHeight( $maxHeight = null ){

        if( $maxHeight ){
            $this->maxHeight = $maxheight;
        }
        else{
            $maxHeight = 0;

            foreach( $this->data as $data ){
                if( $data['m_final'] > $maxHeight ) $maxHeight = $data['m_final'];
            }
        }

        
    }

    public function numBR( $n ){
        return number_format( $n, 2, ',','' );
    }

    /*
     * Desenha a profundidade
     */
    public function drawDepth(){
        if(!empty($this->depth)){
            $this->text('Profundidade do Ponto: ' . $this->numBR($this->depth) . 'm', $this->horizontalMargin, 20, '666666', 10);
        }
    }

    /*
     * setRatio define o tamanho da proporção do poço baseando-se no tamanho máximo deste
     * @param $maxHeight É a maior profundidade do poço.
     */
    public function setRatio(){
        if( empty( $this->depth ) ) $this->depth = 0;
        if( empty( $this->maxHeight ) ) $this->maxHeight = 0;
        //Se profundidade for maior, pegue-a. Senão, pegue maxHeight que é o maior profundidade dos perfis
        $this->maxDepth = $this->depth > $this->maxHeight ? $this->depth : $this->maxHeight;

        $this->maxDepth = empty( $this->maxDepth ) ? 1 : $this->maxDepth;

        //
        //$this->text('Depth:' . $this->maxDepth, 50, 10, '000000', 10);
        return $this->ratio = $this->availableHeight / $this->maxDepth;
    }





    public $verticalMargin        = 30;       // margem superior e inferior
    public $horizontalMargin      = 30;       // margin esquerda e direita.

    public $topLabelsHeight       = 75;       // altura em pixels das legendas superiores
    public $availableHeight       = 450;      // altura disponível para manejar os perfis
    public $columnWidth           = 20;       // Largura de cada coluna no desenho
    public $slotLabelsWidth       = 130;      // Espaço sufiente para desenhar a legenda das ranhuras dos filtros

    public $waterWidth;                       // Largura do canal em pixels. Calculada por drawWater()
    public $topFilterLabelX;                  // X inicial da legenda superior do filtro. Calculada por drawFilter()
    public $topFilterColor        = 'AAAAAA'; // Cor usada para exibir a legenda superior do filtro.

    public $xStartCoating;                    // X do início do revestimento
    public $widthCoating;                     // Largura do revestimento
    public $topCoatingColor       = '0059B2'; // Cor usada para exibir a legenda superior

    public $xStartComplementary;              // X do início do revestimento
    public $widthComplementary;               // Largura do revestimento
    public $topComplementaryColor = '006600'; // Cor usada para exibir a legenda superior

    public $xStartLythologic;                 // X do início do revestimento
    public $widthLythologic;                  // Largura do revestimento
    public $topLythologicColor    = 'B20000'; // Cor usada para exibir a legenda superior

    public $xStartDiameter;                   // X do início do diâmetro

    /*      [id] => 57
            [side_views_types_id] => 57
            [water_points_id] => 4
            [material_name] => 8
            [m_initial] => 0
            [m_final] => 17
            [diameter] =>
            [slot] =>
            [optic_data] =>
            [geophisics_file] =>
            [type] => coating
            [type_name] => Perfil Construtivo do Revestimento
            [col_num] => 1
            [background_color] = FFACCA
            [material_name] = Filtro PVC
        )
    */

    public function drawDiameter(){
        //X do início do revestimento
        $this->xStartDiameter = $this->columnWidth + $this->xStartLythologic + $this->widthLythologic + 50;

        //Pega só o que for revestimento e elimina os vazios
         $data = $this->getByType( 'diameter' );

        //terminando se estiver vazio
        if( empty( $data ) ) return;
        $y = $this->verticalMargin + $this->topLabelsHeight;

        //Loop pelas linhas
        foreach( $data as $row ){
            $yIni = $y + ( $row[ 'm_initial' ] * $this->ratio );

            //$yIni = $y + $this->ratio * $row['m_initial'];
            $yEnd = $y + $this->ratio * $row[ 'm_final' ];
            $x = $this->xStartDiameter + $this->columnWidth;
            //Legenda
                $this->line( $x, $yIni, 20, 0, '000000' )
                     ->line( $x, $yEnd, 20, 0, '000000' )
                     ->text( $this->numBR($row['m_initial']) . 'm' , $x + 25, $yIni + 5, '000000', 7 )
                     ->text( $this->numBR($row['m_final'])   . 'm' , $x + 25, $yEnd + 5, '000000', 7 )
            ;

            $ySymbol = $yIni + ($yEnd - $yIni) / 2;
            $this->diameterSymbol( $x + 50, $ySymbol, 12 )
                 ->text( $this->numBR($row['diameter']) . 'pol', $x + 70, $ySymbol + 10, '000000', 7 )
            ;
        }

        //Criando a linha que divide a linha do diâmetro
        $this->line($x, $y - 20, $this->availableHeight + 20, 270, '000000')
             ->text("Diâmetro da\nPerfuração", $x, $y - 20, '000000', 8 , 45 )
             ;



    }


    public function drawLythologic(){
        //X do início do revestimento
        $this->xStartLythologic = $this->columnWidth + $this->xStartComplementary + $this->widthComplementary;

        //Pega só o que for revestimento e elimina os vazios
         $data = $this->getByType( 'lythologic' );

         //Número de colunas
         $columnsNumber = 1;

         //terminando se estiver vazio
         if( empty( $data ) ) return;

         foreach( $data as $row ){  if( $row['col_num'] > $columnsNumber ) $columnsNumber = $row['col_num'];  }

         //Largura da Litologia
         $this->widthLythologic = $this->columnWidth * $columnsNumber;

         //Loop pelas linhas
         foreach( $data as $row ){
             //Adiciona o canal às legendas inferiores
             $this->addMaterial( $row['material_name'], $row['background_color'] );

             //Calcula os posicionamentos do retângulo
             $x = $this->xStartLythologic + ( $row[ 'col_num'   ] * $this->columnWidth );
             $y = $this->verticalMargin   + $this->topLabelsHeight + ( $row[ 'm_initial' ] * $this->ratio );
             $w = $this->columnWidth;
             $h =  ( $row[ 'm_final' ] - $row[ 'm_initial' ] ) * $this->ratio;

             $this->rectangle( $x, $y, $w, $h, $row['background_color'] );
         }

         //Desenha o label superior do revestimento
         $topLythologicLabelY       = $this->verticalMargin + $this->topLabelsHeight;

         $this//linhas verticais inferiores
              ->line( $this->xStartLythologic + $this->columnWidth, $topLythologicLabelY, 10, 90, $this->topLythologicColor )
              ->line( $this->xStartLythologic + $this->columnWidth + ( $this->columnWidth * $columnsNumber ), $topLythologicLabelY, 10, 90, $this->topLythologicColor )
              //linha horizontal
              ->line( $this->xStartLythologic + $this->columnWidth, $topLythologicLabelY - 10, $this->columnWidth * $columnsNumber, 0, $this->topLythologicColor )
         ;

         //posicionamento da linha e do texto da legenda superior
         $centerX =   $this->columnWidth+( $this->columnWidth * $columnsNumber ) / 2;
         $this->line( $this->xStartLythologic + $centerX, $topLythologicLabelY - 10, 10, 90, $this->topLythologicColor )
              ->text( 'Litologia',  $this->xStartLythologic + $centerX, $topLythologicLabelY - 20, $this->topLythologicColor, 8, 45)
         ;
    }


    public function drawComplementary(){
        //X do início do revestimento
        $this->xStartComplementary = $this->columnWidth + $this->xStartCoating + $this->widthCoating;

        //Pega só o que for revestimento e elimina os vazios
         $data = $this->getByType( 'complementary' );

         //Número de colunas
         $columnsNumber = 1;

         //terminando se estiver vazio
         if( empty( $data ) ) return;

         foreach( $data as $row ){  if( $row['col_num'] > $columnsNumber ) $columnsNumber = $row['col_num'];  }

         //Largura da Anular
         $this->widthComplementary = $this->columnWidth * $columnsNumber;


         //Loop pelas linhas
         foreach( $data as $row ){
             //Adiciona o canal às legendas inferiores
             $this->addMaterial( $row['material_name'], $row['background_color'] );

             //Calcula os posicionamentos do retângulo
             $x = $this->xStartComplementary + ( $row[ 'col_num'   ] * $this->columnWidth );
             $y = $this->verticalMargin   + $this->topLabelsHeight + ( $row[ 'm_initial' ] * $this->ratio );
             $w = $this->columnWidth;
             $h =  ( $row[ 'm_final' ] - $row[ 'm_initial' ] ) * $this->ratio;

             $this->rectangle( $x, $y, $w, $h, $row['background_color'] );
         }

         //Desenha o label superior do revestimento
         $topComplementaryLabelY       = $this->verticalMargin + $this->topLabelsHeight;

         $this//linhas verticais inferiores
              ->line( $this->xStartComplementary + $this->columnWidth, $topComplementaryLabelY, 10, 90, $this->topComplementaryColor )
              ->line( $this->xStartComplementary + $this->columnWidth + ( $this->columnWidth * $columnsNumber ), $topComplementaryLabelY, 10, 90, $this->topComplementaryColor )
              //linha horizontal
              ->line( $this->xStartComplementary + $this->columnWidth, $topComplementaryLabelY - 10, $this->columnWidth * $columnsNumber, 0, $this->topComplementaryColor )
         ;

         //posicionamento da linha e do texto da legenda superior
         $centerX =   $this->columnWidth+( $this->columnWidth * $columnsNumber ) / 2;
         $this->line( $this->xStartComplementary + $centerX, $topComplementaryLabelY - 10, 10, 90, $this->topComplementaryColor )
              ->text( 'Anular',  $this->xStartComplementary + $centerX, $topComplementaryLabelY - 20, $this->topComplementaryColor, 8, 45)
         ;
    }

    public function drawCoating(){
        //X do início do revestimento
        $this->xStartCoating = $this->horizontalMargin + $this->slotLabelsWidth + $this->waterWidth;

        //Pega só o que for revestimento e elimina os vazios
         $data = $this->getByType( 'coating' );
         //Número de colunas
         $columnsNumber = 1;

         //terminando se estiver vazio
         if( empty( $data ) ) return;

         foreach( $data as $row ){  if( $row['col_num'] > $columnsNumber ) $columnsNumber = $row['col_num'];  }

         //Loop pelas linhas
         foreach( $data as $row ){
             //Adiciona o canal às legendas inferiores
             $this->addMaterial( $row['material_name'], $row['background_color'] );
             //Calcula os posicionamentos do retângulo
             $x = $this->xStartCoating + ( $row[ 'col_num'   ] * $this->columnWidth );
             $y = $this->verticalMargin   + $this->topLabelsHeight + ( $row[ 'm_initial' ] * $this->ratio );
             $w = $this->columnWidth;
             $h =  ( $row[ 'm_final' ] - $row[ 'm_initial' ] ) * $this->ratio;

             $this->rectangle( $x, $y, $w, $h, $row['background_color'] );
         }

         $this->widthCoating = $this->columnWidth * $columnsNumber;

         //Desenha o label superior do revestimento
         $topCoatingLabelY = $this->verticalMargin + $this->topLabelsHeight;

         $this//linhas verticais inferiores
              ->line( $this->xStartCoating + $this->columnWidth, $topCoatingLabelY, 10, 90, $this->topCoatingColor )
              ->line( $this->xStartCoating + $this->columnWidth + ( $this->columnWidth * $columnsNumber ), $topCoatingLabelY, 10, 90, $this->topCoatingColor )
              //linha horizontal
              ->line( $this->xStartCoating + $this->columnWidth, $topCoatingLabelY - 10, $this->columnWidth * $columnsNumber, 0, $this->topCoatingColor )
         ;

         //posicionamento da linha e do texto da legenda superior
         $centerX =   $this->columnWidth+( $this->columnWidth * $columnsNumber ) / 2;
         $this->line( $this->xStartCoating + $centerX, $topCoatingLabelY - 10, 10, 90, $this->topCoatingColor )
              ->text( 'Revestimento',  $this->xStartCoating + $centerX, $topCoatingLabelY - 20, $this->topCoatingColor, 8, 45)
         ;
    }

    public function drawFilter(){
        //Pega só o que for filtro, elimina os vazios
        //pr('as');
         $data = $this->getByType( 'filter' );
         //pr($data);
         //Número de colunas
         $columnsNumber = 1;

         //terminando se estiver vazio
         if( empty( $data ) ) return;

         foreach( $data as $row ){  if( $row['col_num'] > $columnsNumber ) $columnsNumber = $row['col_num'];  }
         //pr($columnsNumber);
         //Desenha a água
         $this->drawWater( $columnsNumber );

         //Armazena os y de cada um dos labels dos filtros para evitar colocar
         //2 labels em um mesmo local.
         $yLabels = [];


         //Loop pelas linhas
         foreach( $data as $row ){
             //Adiciona o canal às legendas inferiores
             $this->addMaterial( $row['material_name'], $row['background_color'] );
             //Calcula os posicionamentos do retângulo
             $x = $this->horizontalMargin + $this->slotLabelsWidth + ( $row['col_num'] * $this->columnWidth );
             $y = $this->verticalMargin   + $this->topLabelsHeight + ( $row[ 'm_initial' ] * $this->ratio );
             $w = $this->columnWidth;
             $h =  ( $row[ 'm_final' ] - $row[ 'm_initial' ] ) * $this->ratio;

             $this->rectangle( $x, $y, $w, $h, $row['background_color']   );

             //Calcula o posicionamento do texto da legenda
             $text = 'Ranhura: ' . $this->numBR($row['slot']) . 'mm';
             $l_x  = $this->horizontalMargin + 10 + ( $this->slotLabelsWidth - strlen( $text ) * 6 );
             $l_y  = $y + 7; //Mesmo $y mas um pouco abaixo

             //loop pelos y armazenados para evitar legendas sobrepostas
             foreach( $yLabels as $value ){
                //  pr($l_y . '/' . ($value-10) . '/' . ($value+10) );
                //  dump(Validation::between( $l_y, $value - 10, $value + 10 ));
                 //Se encontrar algum valor que seja 10 pra mais ou pra menos q o valor
                 //atual, então aumente alguns pixes para não ficarem sobrepostos
                 if( between( $l_y, $value - 10, $value + 10 ) )
                    $l_y += 15;
             }

             //Armazena o y da legenda para verificar nas próximas vezes
             $yLabels []= $l_y;


             $this->text( $text , $l_x, $l_y, '000000', 7 );

             //Calcula o posicionamento do retângulo da legenda
             $r_x = $this->horizontalMargin + ( $this->slotLabelsWidth ) / 2;
             $r_y = $l_y + 2;
             $r_w = $x - $r_x;
             $r_h = 1;
             $this->rectangle( $r_x, $r_y, $r_w, $r_h,  $row['background_color'] );
         }

         //Desenha o label superior do filtro
         $this->topFilterLabelX = $this->horizontalMargin + $this->slotLabelsWidth + $this->columnWidth;
         $topFilterLabelY       = $this->verticalMargin + $this->topLabelsHeight;

         $this//linhas verticais inferiores
              ->line( $this->topFilterLabelX, $topFilterLabelY, 10, 90, $this->topFilterColor )
              ->line( $this->topFilterLabelX + ( $this->columnWidth * $columnsNumber ), $topFilterLabelY, 10, 90, $this->topFilterColor )
              //linha horizontal
              ->line( $this->topFilterLabelX, $topFilterLabelY - 10, $this->columnWidth * $columnsNumber, 0, $this->topFilterColor )
         ;

         //posicionamento da linha e do texto da legenda superior
         $centerX = ( $this->columnWidth * $columnsNumber ) / 2;
         $this->line( $this->topFilterLabelX + $centerX, $topFilterLabelY - 10, 10, 90, $this->topFilterColor )
              ->text( 'Filtro',  $this->topFilterLabelX + $centerX, $topFilterLabelY - 20, $this->topFilterColor, 8, 45)
         ;
    }

    /*
     * Desenha a água
     */
    public function drawWater( $columnsNumber ){
        $x                  = $this->horizontalMargin + $this->slotLabelsWidth;
        $y                  = $this->verticalMargin   + $this->topLabelsHeight;
        $bgColor            = 'CCCCFF';

        $this->waterWidth   = ( 2 + $columnsNumber )  * $this->columnWidth;

        //Adiciona o canal às legendas inferiores
        $this->addMaterial( 'Canal', $bgColor );

        return $this->rectangle( $x, $y, $this->waterWidth, $this->availableHeight, $bgColor );
    }

    /*
     * Adiciona um material para poder gerar sua legenda ao final
     */
    public function addMaterial( $name, $color ){
        $this->labels[ $color ] = $name;
    }


    /*
     *
     */
    public function drawGridLines(){
        //Maior profundidade
        $depth    = ceil( $this->depth );
        $startY   = $this->verticalMargin + $this->topLabelsHeight;
        $width    = $this->width - ( 2 * $this->horizontalMargin );
        $x        = $this->horizontalMargin; //início do texto e das linhas
        $sum      = 0;
        $textSize = 7;

        //$this->text('Ratio:' . $this->ratio, 50, 30, '000000', 10);
        //Desenha a primeira linha e o 0m
        $this->line( $x, $startY, $width, 0, 'dddddd' );
        $this->text( '0m', $this->horizontalMargin, $startY, '000000', $textSize );

        for( $i = 0; $i < $this->maxDepth; $i++ ){
            //Se o ratio for muito pequeno, alterne as linhas a cada 20px
             if( $sum >= 15 ) {
                $this->line( $x, $startY, $width, 0, 'dddddd' );
                $this->text( $i . 'm', $x, $startY, '000000', $textSize );
                $sum = 0;
            }

            $sum    += $this->ratio;
            $startY += $this->ratio;
        }
        //Desenha a última linha e o m correspondente
        $this->line( $x, $startY, $width, 0, 'dddddd' );
        $this->text( $this->maxDepth . 'm', $x, $startY, '000000', $textSize );
    }





    /*
     *
     */
    public function getImage(){
        //Define o tamanho máximo da altura
        $this->setMaxHeight();
        //Ativa as proporções
        $this->setRatio();
        //Desenha a profundidade do poço
        $this->drawDepth();

        if(empty($this->data)){
            //$this->setError('Dados insuficientes para gerar a imagem');
            //Desenha a água
            $this->drawWater( 1 );
            //Desenha as legendas inferiores
            $this->drawInferiorLabels();
        }

        if(!$this->hasErrors()){
            //Desenha as linhas de grade
            $this->drawGridLines();
            //Desenha os filtros
            $this->drawFilter();
            //Desenha o revestimento
            $this->drawCoating();
            //Desenha o anular
            $this->drawComplementary();
            //desenha a litologia
            $this->drawLythologic();
            //desenha o diâmetro de perfuração
            $this->drawDiameter();
            //Desenha as legendas inferiores
            $this->drawInferiorLabels();
        }
        else{
            //$this->text('Erro na geração da imagem: Não há dados suficientes.', 20, 20);
            $this->getErrors();
        }

        //Devolve a imagem como PNG
        parent::getImage();
    }

    /*
    * Desenha as legendas inferiores.
    * As legendas são geradas automaticamente dependendo dos materiais
    * indicados no perfil do poço
    */
    public function drawInferiorLabels(){
        //y Inicial para as legendas
        $yIni = $this->verticalMargin + $this->topLabelsHeight + $this->availableHeight + 5;
        $xIni = $this->horizontalMargin;
        $xMax = 600;
        $count = 0;
        //Linha inferior
        //$this->line(0, $yIni, 640, 0, '000000');

        //"Legenda"
        $this->text('Legenda', $xIni, $yIni + 15, '000000', 10);

        $yIni+=25;
        foreach($this->labels as $color => $name){

            $this->rectangle($xIni + ($count * 100), $yIni, 15, 15, $color);
            $this->text($name, $xIni + ($count * 100) + 22, $yIni+12, '000000', 9);

            //Aumente o count dependendo do tamanho do nome para evitar que a legenda
            //atrapalhe a geração da cor seguinte
            $count += (int)(strlen($name) / 14);

            //Incrementa o contador
            $count++;

            //Se a contagem chegar a 6, quebre a linha e retorne para o início
            if($count % 6 == 0){$count = 0; $yIni += 20;}
        }
    }
    /*
     *
     */
    public function hasErrors(){
        return !empty($this->errors);
    }

    /*
     * public function
     */
    public function setError($text){
        $this->errors[] = $text;
        return $text;
    }

    /*
     *
     */
    public function getErrors(){
        $x = $y = 20;
        if($this->hasErrors()){
            //$this->text('Os seguintes erros aconteceram:', $x, $y, '000000', 10);
            $x += 20;
            $y += 20;

            foreach($this->errors as $text){
                $this->text($text, $x, $y, '000000', 10);
                $y += 20;
            }
        }
    }


  /*
   * compara os dados de um array com os arrays anteriores para verificar se
   * as profundidades precisam de uma outra coluna para funcionar
   */
   private function overlapCheck( $oldArray = [], $checkingArray = [], $iniCol = 1 ){
        //Se qualquer um estiver vazio ou não for array, retorne 0
        if( empty( $oldArray ) || empty( $checkingArray ) || !is_array( $oldArray ) || !is_array( $checkingArray ) ) {
            //pr('retornou vazio');
            return false;
        }

        //
        $match = false;
        // pr($checkingArray);
        foreach( $oldArray as $key => $row ){
            //Não compara com o mesmo
            if( $checkingArray['id'] == $row['id'] ) continue;
            //Só pesquisa na mesma coluna.
            if( $row['col_num'] !== $iniCol ) continue;

            //Se o valor inicial está dentro do array anterior
            if( $checkingArray[ 'm_initial' ] >= $row['m_initial'] && $checkingArray[ 'm_initial' ] < $row['m_final']){
                // pr('casou com o inicio');
                $match = true;
            }
            //Se o valor final está dentro do array anterior
            if( $checkingArray[ 'm_final' ] > $row['m_initial']   && $checkingArray[ 'm_final' ] <= $row['m_final'] ){
                // pr('casou com o final');
                $match = true;
            }
            // //se algum valor casar, ou início ou fim
            // if( $checkingArray[ 'm_final' ] == $row['m_final'] || $checkingArray[ 'm_initial' ] == $row['m_initial'] ){
            //     pr('casou com os inícios ou fins');
            //     $match = true;
            // }
            //Se casou com algum, aumenta 1 à coluna e testa novamente
            if( $match === true ){
                // pr('entrou no match');
                $iniCol++;
                return $this->overlapCheck( $oldArray, $checkingArray, $iniCol );
            }

        }
        // pr('nao casou com ninguém');
        return $iniCol;
    }


    /*
     * Verifica as profundidades no array passado e ajusta para definir se ficarão
     * na mesma coluna ou em colunas separadas. Adiciona a informação com o número da coluna
     * que ele deve ficar.
     *
     * A 1a. coluna é sempre a primeira. Caso a linha seguinte coincida, usa a coluna 2
     * A função espera receber um valor sequencial
     *
     * @params array $sideviews Array com os side_views no padrão [m_initial=0, m_final=1]
     *
     *
     *
     */
    private function depthFix( $sideviews ){
     //Se não for array ou for um array vazio, retorne falso
     if( !is_array( $sideviews ) || empty( $sideviews ) ) return false;

     //Array final
     $buff = [];

     foreach( $sideviews as $key => $array ){
         //Se m_initial ou m_end estiverem vazios, pule a execução e elimine do array final
         if( $array[ 'm_initial' ] === '' || $array[ 'm_final' ] === '' ) {
             //pr( 'algum vazio' );
             continue;
         }

         //A variável de saída
         $buff[ $key ] = $array;
         //Adiciona o valor inicial da coluna para comparação em overlapCheck()
         $buff[ $key ][ 'col_num' ] = 1;
         //Atualiza o valor da coluna de acordo com a checagem de overlapCheck
         $buff[ $key ][ 'col_num' ] = $this->overlapCheck( $buff, $array, 1 );
     }

     return $buff;
    }

    /*      
    [id] => 57
    [side_views_types_id] => 57
    [water_points_id] => 4
    [material_name] => 8
    [m_initial] => 0
    [m_final] => 17
    [diameter] =>
    [slot] =>
    [optic_data] =>
    [geophisics_file] =>
    [type] => coating
    [type_name] => Perfil Construtivo do Revestimento
    )
    */

    /*
     * Pega os sideviews por tipo, já eliminado os não completos e já com a verificação
     * de colunas.
     */
    public function getByType( $sideviewType ){
        $buff = [];
        foreach( $this->data as $row ){
            if ( $row['type'] != $sideviewType || !$this->hasAllFieldsFilled( $row ) ) continue;
            $buff[] = $row;
        }

        $buff = $this->depthFix( $buff );

        return $buff;
    }
    /*
     * Verifica uma linha retornada pelo banco de dados para verificar se todos
     * os campos estão preenchidos de acordo com o tipo
     *
     * @param $sideviews Array com os sideviews diretamente do banco de dados
     * @param $sideviewType Tipo do sideview. Se for null, retorne todos os tipos. Se especificar um tipo, retorne só ele
     */
    private function hasAllFieldsFilled( $sideviews ){
        $required = [
            'diameter'      => [ 'm_initial', 'm_final',   'diameter'                            , 'type' ],
            'coating'       => [ 'm_initial', 'm_final', /*'diameter',*/ 'material_name'          , 'type' ],
            'filter'        => [ 'm_initial', 'm_final', /*'diameter',*/ 'material_name',  'slot' , 'type' ],
            'complementary' => [ 'm_initial', 'm_final',                 'material_name'          , 'type' ],
            'lythologic'    => [ 'm_initial', 'm_final',                 'material_name'          , 'type' ],
        ];

        if( isset( $required[ $sideviews['type'] ] ) || !empty( $required[ $sideviews['type'] ] ) ){
            //Loop pelo array $required
            foreach( $required[ $sideviews['type'] ] as $field ){
            //Se o valor $required estiver vazio em $sideviews, encerra a função com um false
                if( $sideviews[ $field ] == null ){
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    /*
     * Desenha o símbolo de diâmetro, que só é usado neste projeto
     */
    public function diameterSymbol($x, $y, $w = 15, $color = '000000'){
        $this->circle($x, $y, $w, $color, false)
             ->line($x, $y + $w, $w * 1.5, 45, $color)
             ;

        return $this;
    }
}

