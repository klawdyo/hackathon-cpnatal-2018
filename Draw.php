<?php
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
      Fonte padrão, relativo a /public/fonts
     */
    public $fontSize = '13';

    /*
        @var $fileType
        Define se é png, jpg ou gif
     */
    private $fileType = 'PNG';

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
        //Força a validação da cor
        //$hex = $this->isValidHex($hex);
        //$this->allocateColor($hex);

        if(empty($font)){$font = $this->font;}
        if(empty($size)){$size = $this->fontSize;}

        // Nome da fonte
        //$font = Filesystem::path('/public/fonts/arial_narrow_7.ttf');
        //$font = Filesystem::path('/public/fonts/' . $font);

        imagettftext($this->img, $size, $angle, $x, $y, $this->getColor($hex), $font, $text);

        return $this;
    }

    /*
     * Desenha uma linha
     * 
     * 
     * @param $x
     * @param $y
     * @param $size
     * @param $angle
     * @param $color
     * @param $dashed
     * @param $colorSpacement
     * @param $noColorSpacement
     * 
     * @return object Retorna o proprio objeto para permitir o encadeamento de métodos  
     */
    public function line($x, $y, $size, $angle = 0, $color = '000000', $dashed = false, $colorSpacement = 2, $noColorSpacement = 2){

        //ângulo e tamanho
        $angle = 360 - $angle;
        //$angle -= 180;
        
        
        $cos = round(cos(deg2rad($angle)), 2);
        $sin = round(sin(deg2rad($angle)), 2);
        
        
        //X2. São as coordenadas finais da linha. Tem que ser calculada baseando-se no ponto inicial,
        $x2 = ($cos * $size ) + $x;
        $y2 = ($sin * $size ) + $y;
        
        
        $color = $this->getColor($color);
        
        if($dashed){
            // $colorLength = 2;   // Tamanho da repetição de pixels da cor
            // $spaceLength = 2;   // Tamanho da repetição de pixels transparentes

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
        // $func = ($filled) ? 'imagefilledellipse' : 'imageellipse';

        // $func($this->img, $x + ($w/2), $y + ($w/2), $w, $w, $this->getColor($color));

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
     * Desenha um polígono de n lados
     * 
     * 
     * @param int $x
     * @param int $y
     * @param int $w
     * @param int $h 
     * @param int $nSides Número de lados, sendo maior ou igual a 3
     * @param string $color Cor em hexadecimal
     * @param optional boolean $filled
     */
    public function polygon( $x, $y, $w, $h, $nSides, $color, $filled ){




        return $this;
    }

    /**
     * Desenha uma forma livre
     */
    public function freeForm(){

        return $this;
    }

    /**
     * Cria uma imagem colorida de dimensões específicas e aplica uma segunda imagem como 
     * ladrilho para elas
     */
    public function tiles(  $w, $h, $path, $backgroundColor ){
        // Imagem vazia com uma cor de fundo
        $im = ( new Draw( $w, $h, $backgroundColor ) )->img;
    
        // $stamp = imagecreatefromxxx('./tiles/claudio.jpeg');
        $stamp = $this->createFromXxx( $path );
    
        // Dimensões da imagem de background
        $bgWidth    = imagesx($im);
        $bgHeight   = imagesy($im);
        // Dimensões do ladrilho
        $tileWidth  = imagesx($stamp);
        $tileHeight = imagesy($stamp);
        // Linhas e Colunas a serem repetidas
        $cols = $bgWidth  > $tileWidth  ? ceil( $bgWidth / $tileWidth )   : 1;
        $rows = $bgHeight > $tileHeight ? ceil( $bgHeight / $tileHeight ) : 1;
        // Loop pelas linhas e colunas
        for( $i = 0; $i < $cols; $i++ ){
            for( $j = 0;$j < $rows; $j++ ){
                $this->imageMerge( $im, $stamp, $i * $tileWidth, $j * $tileHeight, 0,0, imagesx($stamp), imagesy($stamp), 100 );
            }
        }
        
        return $im;
    }    

    /**
     * Usa as tiles() para adicionar uma textura a um retângulo
     */
    public function addTexture( $x, $y, $w, $h, $path, $bgColor ){
        //imag  
        $texture = $this->tiles( $w, $h, $path, $bgColor );
        $this->imageMerge( $this->img, $texture, $x, $y, 0,0,imagesx( $texture ), imagesy($texture), 100 );

        return $this;
    }
    

    ##########################################################################################
    ##
    ##  MÉTODOS DE RETORNO
    ##
    ##########################################################################################


    /**
     * Força o download da imagem
     * 
     * @param optional string $name Nome da imagem
     * @return $this 
     */
    public function download( $name = null ){
        $name = $name ? $name : 'image.' . strtolower( $this->fileType );
        header('Content-Disposition:attachment;filename="'. $name .'"');

        return $this;
    }

    /*
     * Retorna a imagem salva
     */
    public function getImage(){
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

        // Envia o cabeçalho do tipo da imagem
        header('Content-Type: ' . $mime );

        // Finaliza e exibe a imagem
        $func($this->img);
        
        // Libera memória
        imagedestroy($this->img);
    }



    ##########################################################################################
    ##
    ##  MÉTODOS DE CONFIGURAÇÃO INICIAL
    ##
    ##########################################################################################

    /*
     * Define se a imagem deve ser retornada como png
     */
    public function asPng(){
        $this->fileType = 'PNG';
        
        return $this;
    }


    /*
     * Define se a imagem deve ser retornada como png
     */
    public function asJpg(){
        $this->fileType = 'JPG';
        
        return $this;
    }


    /*
     * Define se a imagem deve ser retornada como png
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
     *
     */
    private function create(){
        //$this->height = 1200;
        //Crio a imagem inicial
        $this->img = imagecreatetruecolor($this->width, $this->height);
        //$white = imagecolorallocate($this->img, 255, 255, 255);
        imagefill($this->img, 0, 0, $this->getColor( $this->backgroundColor ));
        imageantialias($this->img, true);
        imageAlphaBlending($this->img, true);
        imageSaveAlpha($this->img, true);
    }

    /**
     * Cria uma imagem a partir de outra de extensões jpg e png
     */
    function createFromXxx( $path ) {
        $extension = Filesystem::extension($path);
        switch( $extension ){
            case 'png'  : return imagecreatefrompng ( $path ); break;
            case 'jpeg' : return imagecreatefromjpeg( $path ); break;
            case 'jpg'  : return imagecreatefromjpeg( $path ); break;
        }
    }

    
        

    /*
     * Pega uma cor alocada
     */
    private function getColor($hex){
        $hex = $this->isValidHex($hex);
        $this->allocateColor($hex);

        return $this->allocatedColors[$hex];
    }

    /*
     * Aloca um cor
     *
     */
    private function allocateColor($hex){
        $hex   = $this->isValidHex($hex);
        $color = $this->toRGB($hex);
        $this->allocatedColors[$hex] = imagecolorallocate($this->img, $color->red, $color->green, $color->blue);
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

        $hex = $this->isValidHex($hex);

        $obj = new stdClass();

        //Pega de 2 em 2 caracteres e transforma para decimal
        $obj->red   = str_pad(hexdec(substr($hex, 0, 2)), 2, '0');
        $obj->green = str_pad(hexdec(substr($hex, 2, 2)), 2, '0');
        $obj->blue  = str_pad(hexdec(substr($hex, 4, 2)), 2, '0');


        return $obj;
    }

    /*
     * Verifica se um hex é válido e normaliza
     *
     */
    private function isValidHex($hex){
        //Se começa com '#', retire
        if($hex[0] == '#'){
            $hex = substr($hex, 1);
        }

        //se tem 6 letras, continue;
        if(strlen($hex) != 6){ trigger_error('Formato de cor inválido: "' . $hex . '"'); }


        //Se só tem caracteres hexadecimais
        if(preg_match('/^[0-9a-fA-F]{6}$/', $hex) == false){
            trigger_error('Formato de cor inválido: "' . $hex . '"');
        }

        return strtoupper($hex);
    }

}
