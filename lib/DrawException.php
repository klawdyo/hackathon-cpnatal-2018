<?php
    class DrawException extends Exception{}


    /**
     * Define o gerenciador de erro
     */
    function error_handler($errno, $errstr, $errfile, $errline){
        throw new DrawException($errstr, $errno);
    }

    set_error_handler('error_handler');
