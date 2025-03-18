<?php


function dd( ...$args ){


    echo "<pre style='margin: 0; padding: 10px; background-color:#012; color: #FFF;'>
    <h4>Dump&Die</h4><hr>";   
    
    foreach( $args as $x => $arg ){
        
        var_dump( $arg );
        echo "<hr>";
    }

    echo "<h4>Backtrace</h4><hr>";   
    
    $backtraces = debug_backtrace();
    
    foreach( $backtraces as $x => $backtrace){
        
        if( $x == 0 ) continue;
        
        echo "Funzione chiamante: " . $backtrace['function'] . "<br>";
        echo "File chiamante: "     . $backtrace['file'] . "<br>";
        echo "Linea chiamante: "    . $backtrace['line'] . "<br>";
        
    }
    
    


    echo '</pre>';
    die();
}
