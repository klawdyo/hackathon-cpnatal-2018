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
    public function rectangle( $x, $y, $width, $height, $color = null ){
        //cria o retângulo
        imagefilledrectangle( $this->img, $x, $y, $x + $width, $y + $height, $this->getColor( $color ) );

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
     */
    public function line($x, $y, $size, $angle = 0, $color = '000000', $dashed = false){

        //X2. São as coordenadas finais da linha. Tem que ser calculada baseando-se no ponto inicial,
        //ângulo e tamanho
        $angle = 360 - $angle;// + 180;
        //$angle -= 180;


        $cos = round(cos(deg2rad($angle)), 2);
        $sin = round(sin(deg2rad($angle)), 2);


        $x2 = ($cos * $size ) + $x;
        $y2 = ($sin * $size ) + $y;


        /*$this->text('x2:'.$x2, 10, 20)
             ->text('y2:'.$y2, 10, 50)
             ->text('cos(' . $angle . '): ' . $cos, 10, 80)
             ->text('sin(' . $angle . '): ' . $sin, 10, 110)
             ;*/

        $color = $this->getColor($color);
        $white = $this->getColor('000000');

        //if($dashed){
            $style = array($white, $white, $white, $white, $white, $white, $white, $color, $color, $color);
            imagesetstyle($this->img, $style);
        //}
        //imageline ( resource $image , int $x1 , int $y1 , int $x2 , int $y2 , int $color )
        imageline($this->img, $x, $y, $x2, $y2, $color);


        return $this;
    }

    /*
     * Cria um círculo
     * imagefilledellipse ( resource $image , int $cx , int $cy , int $width , int $height , int $color )
     */
    public function circle($x, $y, $w, $color, $filled = true){
        $func = ($filled) ? 'imagefilledellipse' : 'imageellipse';

        $func($this->img, $x + ($w/2), $y + ($w/2), $w, $w, $this->getColor($color));

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
        //header('Content-type: image/png');
        //imagepng($im);
        //imagedestroy($im);
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



    /*
     * Retorna a imagem salva
     */
    public function getImage(){
        header('Content-Type: image/png');

        //Finaliza e exibe a imagem
        imagepng($this->img);

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
    }


    /*
     * Define se a imagem deve ser retornada como png
     */
    public function asJpg(){
        $this->fileType = 'JPG';
    }


    /*
     * Define se a imagem deve ser retornada como png
     */
    public function asGif(){
        $this->fileType = 'GIF';
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
        $hex = $this->isValidHex($hex);
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
        $obj->red =   str_pad(hexdec(substr($hex, 0, 2)), 2, '0');
        $obj->green = str_pad(hexdec(substr($hex, 2, 2)), 2, '0');
        $obj->blue =  str_pad(hexdec(substr($hex, 4, 2)), 2, '0');


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
