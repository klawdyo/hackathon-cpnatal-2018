<?php
require 'DrawException.php';
require 'Utils.php';
require 'Filesystem.php';

class Draw{

    /*
        @var
        Dados do banco
    */
    public $data = [];

    /*
        @var
        Resource da imagem
    */
    public $img = [];

    /*
        @var
        Largura
    */
    public $width = 640;

    /*
        @var
        Altura
    */
    public $height = 640;

    /*
        @var
        Fundo da Imagem
    */
    public $backgroundColor = 'ffffff';

    /*
        @var
        Altura
    */
    public $allocatedColors = [];

    /*
      @var
      Fonte padrão, relativo a /public/fonts
     */
    public $font = 'arial_narrow_7.ttf';

    /*
      @var
      Current
     */
    public $current;

    /*
      @var
      Dados das Formas geométricas
     */
    public $forms;

    /*
      @var
      Fonte padrão, relativo a /public/fonts
     */
    public $fontSize = '13';

    /*
        @var $fileType
        Define se é png, jpg ou gif
     */
    private $fileType = 'PNG';

    /*
        @var $errors
        Armazena erros
     */
    protected $errors = [];

    /*
     * Construtor
     *
     */
    public function __construct($width = 640, $height = 640, $backgroundColor = 'FFFFFF'){
        $this->width = ternary($width, 640);
        $this->height = ternary($height, 640);
        $this->backgroundColor = ternary($backgroundColor, 'ffffff');
        //Cria a imagem
        $this->create();
    }

    /*
     * cria um quadrado
     */
    public function rectangle( $x, $y, $width, $height, $color = null, $filled = true ){
        //define a função
        $func = $filled ? 'imagefilledrectangle' : 'imagerectangle';

        //cria o retângulo
        $func( $this->img, $x, $y, $x + $width, $y + $height, $this->getColor( $color ) );

        //retorna o objeto para permitir concatenação
        return $this;
    }

    /*
     * Escreve um texto
     *
     * @param $text
     * @param $x
     * @param $y
     * @param $hex Cor em hexadecimal
     * @param $size Tamanho da fonte
     * @param $angle Ângulo de rotação da imagem
     * @param $font Fonte padrão relativo a /public/fonts
     */
    public function text($text, $x, $y, $hex = '000000', $size = 12, $angle = 0, $font = null){
        $base = '../public/fonts/';

        if(empty($font)){$font = $this->font;}
        if(empty($size)){$size = $this->fontSize;}

        imagettftext($this->img, $size, $angle, $x, $y, $this->getColor($hex), $base.$font, $text);

        return $this;
    }

    /*
     * Desenha uma linha.
     * O PHP traça uma linha recebendo as suas coordenadas inicial e final. 
     * 
     * 
     * @param $x Posição x do início da linha
     * @param $y Posição y do início da linha
     * @param $size Tamanho da linha em pixels
     * @param $angle Ângulo de inclinação da linha
     * @param $color Cor em hexadecimal
     * @param $dashed Define se é uma linha pontilhada
     * @param $colorSpacement Tamanho em pixels da cor do pontilhado
     * @param $noColorSpacement Tamanho em pixels do espaçamento do pontilhado
     * 
     * @return object Retorna o próprio objeto para permitir o encadeamento de métodos  
     */
    public function line($x, $y, $size, $angle = 0, $color = '000000', $dashed = false, $colorSpacement = 2, $noColorSpacement = 2){

        //ângulo e tamanho
        $angle = 360 - $angle;
        
        $cos = round(cos(deg2rad($angle)), 2);
        $sin = round(sin(deg2rad($angle)), 2);
        
        //X2. São as coordenadas finais da linha. Tem que ser calculada baseando-se no ponto inicial,
        $x2 = ($cos * $size ) + $x;
        $y2 = ($sin * $size ) + $y;
        
        
        $color = $this->getColor($color);
        
        if($dashed){
            // Cria um array no estilo [ red, red, transparent, transparent ] de acordo com as configurações
            // em $colorLength e $spaceLength e passa como estilo para a linha gerada
            $style =  array_merge( array_fill(0, $colorSpacement, $color ), array_fill( 0, $noColorSpacement, IMG_COLOR_TRANSPARENT ) );
            
            imageantialias($this->img, false);                          // Desativa o antialiasing para conseguir uma linha pontilhada
            imagesetstyle($this->img, $style);                          // Cria o estilo da linha
            imageline($this->img, $x, $y, $x2, $y2, IMG_COLOR_STYLED);  // Cria a linha
            imageantialias($this->img, true);                           // Reativa o antialiasing
        }
        else{
            imageline($this->img, $x, $y, $x2, $y2, $color);
        }

        return $this;
    }

    /*
     * Cria um círculo
     * 
     * imagefilledellipse ( resource $image , int $cx , int $cy , int $width , int $height , int $color )
     * O PHP usa x e y para criar o círculo a partir do seu centro, mas Draw usa as extremidades direita
     * e esquerda para manter a padronização com as demais funções
     * 
     * @param $x X da extremidade direta do círculo para o lado esquerdo da imagem
     * @param $y Y da extremidade superior do círculo para o lado superior da imagem
     * @param $w Diâmetro da circunferência
     * @param $color Cor em hexadecimal
     * @param optional $filled Define se o círculo deve ou não deve ser preenchido
     * 
     */
    public function circle($x, $y, $w, $color, $filled = true){
        return $this->ellipse( $x, $y, $w, $w, $color, $filled );
    }

    /*
     * Cria uma elipse
     * 
     * imagefilledellipse ( resource $image , int $cx , int $cy , int $width , int $height , int $color )
     * O PHP usa x e y para criar o círculo a partir do seu centro, mas Draw usa as extremidades direita
     * e esquerda para manter a padronização com as demais funções
     * 
     * @param $x X da extremidade direta do círculo para o lado esquerdo da imagem
     * @param $y Y da extremidade superior do círculo para o lado superior da imagem
     * @param $w Largura da elipse
     * @param $h Atura da elipse
     * @param $color Cor em hexadecimal
     * @param optional $filled Define se o círculo deve ou não deve ser preenchido
     * 
     */
    public function ellipse($x, $y, $w, $h, $color, $filled = true){
        $func = ($filled) ? 'imagefilledellipse' : 'imageellipse';

        $func($this->img, $x + ($w/2), $y + ($h/2), $w, $h, $this->getColor($color));

        return $this;
    }

    /**
     * Desenha um polígono regular de n lados
     * 
     * 
     * @param int $x
     * @param int $y
     * @param int $w
     * @param int $h não precisa de altura pois é regular
     * @param int $n Número de lados, sendo maior ou igual a 3
     * @param string $color Cor em hexadecimal
     * @param optional boolean $filled
     */
    public function polygon( $x, $y, $w, $n, $color, $filled = true ){
        try{
            $radius = $w/2;
            $y += $radius;
            $x += $radius;

            $points = [];
            for($a = 0;$a <= 360; $a += 360/$n)
            {
                $points[] = $x + $radius * cos(deg2rad($a));
                $points[] = $y + $radius * sin(deg2rad($a));
            }

            $func = $filled ? 'imagefilledpolygon' : 'imagepolygon';

            $func($this->img, $points, floor(count($points)/2), $this->getColor($color)  );
        }
        catch( Exception $e ){
            $this->setError( $e->getMessage() );
        }

        return $this;
    }

    /** TO DO
     * Desenha uma forma livre
     */
    /*public function freeForm( $points, $color, $filled = true ){
        try{

        }
        catch( Exception $e ){
            $this->setError( $e->getMessage() );
        }

        return $this;
    }//*/

    /**
     * Desenha um arco. O arco é desenhado em sentido horário seguindo o contorno de uma elipse
     * com tamanho definido em $w e $h e posicionada em $x e $y. O arco inicia desenhando do angulo
     * inicial em sentido horário e vai até o ângulo final.
     * http://php.net/manual/en/function.imagefilledarc.php
     * http://php.net/manual/en/function.imagearc.php
     * 
     * Se for um arco preenchido, o parâmetro $style poderá conter os seguintes valores:
     * IMG_ARC_PIE    Conecta as pontas seguindo o contorno da elipse que gerou o arco em sentido horário. 
     *                Se for usado junto com NOFILL, não conecta pelo centro, deixa o arco aberto.
     *                Não pode ser usado junto com IMG_ARC_CHORD.
     * IMG_ARC_CHORD  Conecta as pontas com uma linha reta. Não pode ser usado junto com IMG_ARC_PIE.
     * IMG_ARC_NOFILL Define que não terá preenchimento, só linhas.
     * IMG_ARC_EDGED  Define que as pontas ficarão ligadas pelo centro.
     * 
     * @param int  $x       X esquerdo da elipse que gerará o arco
     * @param int  $y       Y superior esquerdo da elipse que gerará o arco
     * @param int  $w       Largura da elipse que gerará o arco
     * @param int  $h       Altura da elipse que gerará o arco
     * @param int  $start   Ângulo inicial de onde o arco começará a ser desenhado, em graus. 0º representa o ponteiro das horas às 3:00
     * @param int  $end     Ângulo final até onde o arco será desenhado, em graus
     * @param int  $color   Cor em hexadecimal
     * @param bool $filled  Define se será preenchido ou só a linha
     * @param string $style Define o estilo do arco, conforme descrição acima
     */
    public function arc( $x, $y, $w, $h, $start, $end, $color, $filled = true, $style = IMG_ARC_PIE ){
        $func = $filled ? 'imagefilledarc' : 'imagearc';
        //imageantialias($this->img, true);
        if( $filled ){
            switch( $style ){
                case 'pie'    : $style = IMG_ARC_PIE;    break; // Preenche e liga as pontas ao centro, gerando uma pizza
                case 'chord'  : $style = IMG_ARC_CHORD;  break; // Somente conecta os pontos de início e fim do arco, enquanto PIE gera a linha arredondada
                case 'nofill' : $style = IMG_ARC_NOFILL; break; // Indica para não preencher, deixar só a linha 
                case 'edged'  : $style = IMG_ARC_EDGED;  break; // Se usado nunto com nofill, conecta a linha ao centro e deixa parecido com o PIE não preenchido
            }
            imagefilledarc( $this->img, $x + ( $w / 2 ), $y + ( $h / 2 ), $w, $h, $start, $end, $this->getColor( $color ), $style );
        }
        else{
            imagearc( $this->img, $x + ( $w / 2 ), $y + ( $h / 2 ), $w, $h, $start, $end, $this->getColor( $color ) );
        }

        return $this;
    }

    /**
     * Cria um retângulo com uma cor de fundo e uma imagem que se repete, como textura, ladrilho, telha, etc
     * Se a imagem for transparente, a cor de fundo ficará misturada com ela e dará a impressão de textura
     * 
     * @param $x Posição x do retângulo gerado na imagem base
     * @param $y Posição y do retângulo gerado na imagem base
     * @param $w Largura do retângulo
     * @param $h Altura do retângulo
     * @param $path Caminho da imagem da textura
     * @param $bgColor Cor de fundo em hexadecimal
     */
    public function addTexture( $x, $y, $w, $h, $path, $bgColor = '#FFFFFF' ){
        // Imagem
        $texture = $this->tiles( $w, $h, $path, $bgColor );
        
        // Mescla a imagem texturada com a imagem base
        $this->imageMerge( $this->img, $texture, $x, $y, 0,0,imagesx( $texture ), imagesy($texture), 100 );

        return $this;
    }
    
    ##########################################################################################
    ##
    ##  MÉTODOS DE CONFIGURAÇÃO INDIVIDUAL
    ##  Métodos usados para configurar individualmente um elemento
    ##  Ex.: rotate(), dashed(), alpha(), dimensions(), position()
    ##  $img->rotate(45)->alpha(50)->position(0,0)->dimensions(10, 50)->rectangle() //Cria um retângulo 
    ##  rotacionado e com  transparência de 50% com o tamanho e as coordenadas especificadas
    ##
    ##########################################################################################
    
    /**
     * TO DO
     * Usado antes de um elemento, aplica uma transparência a ele
     * 
     * @param int $alpha Transparência a ser aplicada a um elemento
     */
    // public function alpha( $alpha ){ return $this; }

    /**
     * Define um ângulo para o elemento. Apelido para angle()
     */
    // public function rotate( $angle ){ return $this; }

    /**
     * Define um ângulo para o elemento
     */
    // public function angle( $angle ){ return $this; }

    /**
     * Aplica uma imagem sobre o elemento
     */
    // public function texture( $path ){ return $this; }

    /**
     * Define uma linha como tracejada
     */
    // public function dashed( $color, $colorSpacement, $noColorSpacement ){ return $this; }

    /**
     * Apelido para dashed( 1,1 ), onde o tracejado tem 1 pixel de cor 1 
     * 1 pixel de espaço
     */
    // public function dotted( $color ){ return $this; }

    /**
     * Define as dimensões da forma
     * Se $height for null, use o mesmo $width
     */
    // public function dimensions( $width, $height = null ){ return $this; }

    /**
     * Define a posição da forma
     */
    // public function position( $x,$y ){ return $this; }

    /**
     * 
     */
    // public function filled( $color ){ return $this; }

    ##########################################################################################
    ##
    ##  MÉTODOS DE RETORNO
    ##  Usados no retorno da imagem ao usuário, tipo exibição, download ou salvo em um diretório
    ##
    ##########################################################################################


    /*
     * Retorna a imagem salva
     */
    public function show(){
        $type = strtolower( $this->fileType );

        switch( $type ){
            case 'png':
                $mime = 'image/png';
                $func = 'imagepng';
            break;
            case 'jpg':
                $mime = 'image/jpeg';
                $func = 'imagejpeg';
            break;
            case 'gif':
                $mime = 'image/gif';
                $func = 'imagegif';
            break;
        }

        // Tratamento de erros
        $this->getErrors();
        
        // Envia o cabeçalho do tipo da imagem
        header('Content-Type: ' . $mime );
        
        // Finaliza e exibe a imagem
        $func($this->img);

        // Libera memória
        $this->destroy();
    }


    /**
     * Força o download da imagem
     * 
     * @param optional string $name Nome da imagem
     * @return $this 
     */
    public function download( $name = null ){
        $name = $name ? $name : 'image.' . strtolower( $this->fileType );
        header('Content-Disposition:attachment;filename="'. $name .'"');
        $this->show();

        return $this;
    }


    ##########################################################################################
    ##
    ##  MÉTODOS DE CONFIGURAÇÃO INICIAL
    ##
    ##########################################################################################

    /*
     * Define se a imagem deve ser retornada como png
     */
    public function asPng() {
        $this->fileType = 'PNG';
        
        return $this;
    }

    /*
     * Define se a imagem deve ser retornada como jpg
     */
    public function asJpg(){
        $this->fileType = 'JPG';
        
        return $this;
    }

    /*
     * Define se a imagem deve ser retornada como gif
     */
    public function asGif(){
        $this->fileType = 'GIF';
        
        return $this;
    }

    ##########################################################################################
    ##
    ##  MÉTODOS AUXILIARES
    ##
    ##########################################################################################


    /*
     * Cria o objeto da imagem com as configurações iniciais
     */
    protected function create(){
        // Crio a imagem inicial
        $this->img = imagecreatetruecolor($this->width, $this->height);
        
        // Crio uma imagem preenchida
        imagefill($this->img, 0, 0, $this->getColor( $this->backgroundColor ));
        
        // Defino o antialiasing como true
        imageantialias($this->img, true);
        
        // Defino o suporte a transparência como true
        imageAlphaBlending($this->img, true);
        imageSaveAlpha($this->img, true);
    }

    /**
     * Destrói a imagem
     */
    protected function destroy(){
        imagedestroy($this->img);
        $this->img = null;
    }

    /**
     * Cria uma imagem a partir de outra imagem de qualquer tipo.
     * 
     * @param string path Endereço da imagem
     * @return resource da imagem
     */
    protected function createFromXxx( $path ) {
        // Pega a extensão do arquivo passado
        $extension = Filesystem::extension($path);
        
        // Seleciona a função correta de imagem.
        switch( $extension ){
            case 'png'  : return imagecreatefrompng ( $path ); break;
            case 'jpeg' : return imagecreatefromjpeg( $path ); break;
            case 'jpg'  : return imagecreatefromjpeg( $path ); break;
        }
    }

    /*
     * Pega uma cor alocada
     * 
     * @param $hex Cor em hexadecimal
     * @return Objeto no padrão usado pelo PHP-GD
     */
    private function getColor($hex){
        $hex = $this->isValidHex($hex);
        $this->allocateColor($hex);

        return $this->allocatedColors[$hex];
    }

    /*
     * Aloca um cor
     * 
     * @param $hex Cor em hexadecimal
     */
    private function allocateColor($hex){
        $hex   = $this->isValidHex($hex);
        $color = $this->toRGB($hex);
        $this->allocatedColors[$hex] = imagecolorallocate($this->img, $color->red, $color->green, $color->blue);
    }

    /**
     * Cria uma imagem colorida de dimensões específicas e aplica uma segunda imagem como 
     * ladrilho para elas. A imagem não é adicionada à imagem original, é apenas uma imagem
     * separada para ser usada em outras formas.
     * 
     * @param $w Tamanho da imagem
     * @param $h Altura da imagem
     * @param $path Caminho da imagem usada como padrão
     * @param $backgroundColor Cor de fundo em hexadecimal
     */
    protected function tiles(  $w, $h, $path, $backgroundColor ){
        // Imagem vazia com uma cor de fundo
        $im = ( new Draw( $w, $h, $backgroundColor ) )->img;
        
        // Imagem de ladrilho
        $stamp = $this->createFromXxx( $path );
        
        // Dimensões do ladrilho
        $tileWidth  = imagesx($stamp);
        $tileHeight = imagesy($stamp);
        
        // Linhas e Colunas a serem repetidas
        $cols = $w > $tileWidth  ? ceil( $w / $tileWidth  ) : 1;
        $rows = $h > $tileHeight ? ceil( $h / $tileHeight ) : 1;
        
        // Loop pelas linhas e colunas
        for( $i = 0; $i < $cols; $i++ ){
            for( $j = 0;$j < $rows; $j++ ){
                $this->imageMerge( $im, $stamp, $i * $tileWidth, $j * $tileHeight, 0,0, imagesx($stamp), imagesy($stamp), 100 );
            }
        }
        
        return $im;
    }    
    
    
    /**
     * Adaptação do imagecopymerge() com suporte a imagens transparentes
     */
    function imageMerge($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){ 
        // creating a cut resource 
        $cut = imagecreatetruecolor($src_w, $src_h);
    
        // copying relevant section from background to the cut resource 
        imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h); 
        
        // copying relevant section from watermark to the cut resource 
        imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h); 
        
        // insert cut resource to destination image 
        imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct); 
    } 


    /*
     * Converte uma cor em hexadecimal em suas componentes rgb
     *
     * @var string $hex
     * @return object ->red, ->green e ->blue
     */
    private function toRGB($hex){
        // Normaliza a cor hexadecimal
        $hex = $this->isValidHex($hex);
        
        // Gera a classe padrão
        $obj = new stdClass();

        // Pega de 2 em 2 caracteres e transforma para decimal
        $obj->red   = str_pad( hexdec( substr( $hex, 0, 2 ) ), 2, '0' );
        $obj->green = str_pad( hexdec( substr( $hex, 2, 2 ) ), 2, '0' );
        $obj->blue  = str_pad( hexdec( substr( $hex, 4, 2 ) ), 2, '0' );

        return $obj;
    }

    /*
     * Verifica se um hex é válido e normaliza
     *
     * @param $hex Cor em hexadecimal
     * @return string Cor normalizada em hexadecimal
     */
    private function isValidHex($hex){
        try{
            // Se começa com '#', retire
            if($hex[0] == '#'){
                $hex = substr($hex, 1);
            }

            // Se tem 3 letras, repita-as no formato AABBCC
            if( strlen( $hex ) === 3 ) $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2]; 
            
            // Se tem 6 letras, continue;
            if(strlen($hex) != 6){ $this->setError('Cor "' . $hex . '" informada com '. strlen($hex) .' letras'); }

            // Se só tem caracteres hexadecimais
            if(preg_match('/^[0-9a-fA-F]{6}$/', $hex) == false){
                $this->setError('Caracteres não hexadecimais encontrados na cor "' . $hex . '"');
            }
            
            return strtoupper($hex);
        }   
        catch( Exception $e ){
            $this->setError($e->message);
        }   
    }


    ##########################################################################################
    ##
    ##  TRATAMENTO DE ERROS
    ##
    ##########################################################################################
    
    /*
     * Verifica se existem erros na geração da imagem
     *
     * @return bool
     */
    public function hasErrors(){
        return !empty($this->errors);
    }

    /* 
     * Incluir um erro na propriedade de erros
     * 
     * @param $text Mensagem de erro
     * @return string Retorna o $erro inserido
     */
    public function setError($text){
        $this->errors[] = $text;
        return $text;
    }

    /*
     * Pega os erros adicionados e os insere dentro de um retângulo vermelho na imagem gerada
     * 
     * @todo Deixar o retângulo semi transparente
     */
    public function getErrors(){
        $x = 10;
        $y = 10;
        $lineHeight = 15;
        
        if($this->hasErrors()){
            array_unshift( $this->errors, 'Atenção! Ocorreram os seguintes erros:' );
            
            // Pega o número de erros e aplica em um quadrado vermelho sobre todos os outros
            $this->rectangle( 0, 0, $this->width, count( $this->errors ) * $lineHeight+20, '#000' );
           
            $y += $lineHeight;

            foreach($this->errors as $text){
                $this->text($text, $x, $y, 'FFF', 10);
                $y += $lineHeight;
            }
        }
    }

    /** TO DO
     * Verifica se faltam parâmetros obrigatórios em cada tipo de forma
     * formato:
     *  $fields = [
     *      'x' => 'rules' => [ 'required', 'integer' ], 'value' => 34
     * ];
     */
    /*protected function validate( $fields = [] ){
        $errors = [];
        if( is_array( $fields ) && !empty( $fields ) ){

                //Transforme as regras em array, caso não sejam
                $fields['rules'] = !is_array( $fields['rules'] ) ? [ $fields['rules'] ] : $fields['rules'];
                //Loop pelas regras
                foreach( $fields as $key => $array  ){
                    $rules = is_array( $array ) && isset( $array['rules'] ) ? $array['rules'] : [];

                    if( $idx = array_search( 'integer', $rules  ) > -1 && !is_int( $array[ 'value' ] ) ) $errors[] = $key . 'not integer';
                    if( $idx = array_search( 'required', $rules ) > -1 && empty(   $array[ 'value' ] ) ) $errors[] = $key . ' empty'     ;
                }
        }
        else{
            $this->setError( '$fields está vazio ou não é um array' );
        }
    }//*/

    /* TO DO
     * Pega os dados do campo atual 
     */
    /*protected function current(){
        if( $this->current === null ) 
            return $this->current = count($this->forms);
        return $this->current;
    }//*/
    

}



