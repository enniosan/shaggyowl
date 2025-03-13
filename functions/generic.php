<?php


function dd( ...$args ){
    echo '<pre>';

    foreach( $args as $arg ){
        var_dump( $arg );
    }

    echo '</pre>';
    die();
}
