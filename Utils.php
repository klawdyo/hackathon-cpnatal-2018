<?php
    /**
     * Verifica se um valor está entre 2 outros valores
     * 
     * @param $value Valor a ser verificado
     * @param $valueStart Valor Inicial
     * @param $valueEnd Valor Final
     * @return bool True caso o valor seja maior ou igual a $valueStart e menor ou igual a $valueEnd
     */
    function between( $value, $valueStart, $valueEnd ){
        return $value >= $valueStart && $value <= $valueEnd;
    }

    /**
      * ternary()
      * Devolve $var caso ela exista. Se não existir, devolve o valor padrão $default
      * É um atalho menos chato para as milhões de verificações que precisamos fazer
      * para não dar erro ao usar variável inexistente
      *
      * @param $var Variável a ser verificada
      * @param $default Caso não esteja definida, define-a e retorna este valor inicial
      *
      */
      function ternary(&$var, $default = false){
        return $var = isset($var) ? $var : $default;
    }

