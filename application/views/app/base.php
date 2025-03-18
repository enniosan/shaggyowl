<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title><?= isset($title) ? $title : 'Dashboard' ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Raleway:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

    <link rel="stylesheet" type="text/css" href="/assets/css/style.css" await>

    <!-- csrf   -->
    <meta name="csrf-token-name" content="<?php echo $this->security->get_csrf_token_name(); ?>">
    <meta name="csrf-token-hash" content="<?php echo $this->security->get_csrf_hash(); ?>">

</head>

</head> 

<body>
        
    <div id='modalContainer' style='display:none'>
        <div id='editMask'>
            <div id='editTitle'> *** TITOLO ***</div>
                
            <form id='editForm'>
                
                <input type='hidden' id='editId' name='id'>
                <input type='text' id='editName' name='nome' placeholder='Nome'>
                <input type='text' id='editSurname' name='cognome' placeholder='Cognome'>
                
                <input type='email' id='editEmail' name='email' placeholder='Email'>
                <input type='text' id='editAddress' name='indirizzo' placeholder='Indirizzo'>
                
                <select id='editGender'>
                    <option value='' default disabled></option>
                    <option value='M'>Maschio</option>
                    <option value='F'>Femmina</option>
                    <option value='N'>Non voglio specificarlo</option>
                </select>
            </form>

            <div id='editActions'>
                <div class='button' id='editCancel'>Annulla</div>
                <div class='button' id='editSave'>Salva</div>
            </div>

        </div>
    </div>

    <div id='appContainer'>

        <div id='header'>
            <img src="<?= base_url( "/assets/icons/" . strtolower( $user['role'] ) . ".svg" ) ?>" alt="<?= $user['role'] ?>" title="<?= $user['role'] ?>" loading=lazy width=40 height=40>
            
            <h4 class='username'>
                <?= $user['name'] ?>
                <br><small class='lastaccess' >Ultimo accesso: <?= $user['last_access'] ?></small>
            </h4>
            
            <div class='title'><?= $title ?></div>

            <div class='filler'>
                <img id='logout' 
                    src="<?= base_url( "/assets/icons/logout.svg" ) ?>" 
                    alt="logout" 
                    title="logout" 
                    loading=lazy width=30 height=30            
                    >
            </div>
        
        </div>

        <div id='theTable'>
            
                <?php 

                    #   costruzione tabella

                    foreach( $app_config['campi'] as $campo ){

                        /*
                            questa l'ho messa solo perchè era uscita al colloquio e stavo pensando
                            ad un modo per implementarla qui ! :-)

                        */
                        $extraClass = ( isset( $app_config['hiddenables'][$campo] ) ) ? "hiddenXs" : "";

                        /*  gestione del sorting */
                        $sorting = "<div class='sorter' data-field=\"$campo\" data-sort=0>-</div>";

                        if( $app_config['ordinamento'] == $campo ){

                            if( $app_config['verso'] == 2){
                                $sorting = "<div class='sorter' data-field=\"$campo\" data-sort=1>▼</div>"; //  desc
                            }else{
                                $sorting = "<div class='sorter' data-field=\"$campo\" data-sort=2>▲</div>"; //  asc
                            }

                        }

                        echo "<div class='headField $extraClass'>$campo $sorting</div>";
                    
                    }
                
                    #   azioni
                    echo "<div class='headField'></div>";

                    $extraClass = "";

                    foreach( $content as $idx => $anagrafica ){

                        #   diegno i campi

                        foreach( $app_config['campi'] as $campo ){

                            $extraClass = ( isset( $app_config['hiddenables'][$campo] ) ) ? "hiddenXs" : "";
    
                            echo "<div class='commonField row_".$idx." $extraClass' data-row='" . $idx . "' > " . $anagrafica -> $campo ."</div>";
                        }

                        #   disegno le azioni
                        
                        echo "<div class='actions commonField row_".$idx." ' data-row='" . $idx . "' >";

                        
                        if( $this -> session -> userdata()['actions']->show )
                            echo "<img width=20 height=20 class='action-icon'src='/assets/icons/view.svg' title='edit' name='edit' data-action='view' data-id='" . $anagrafica -> id . "'>";

                        if( $this -> session -> userdata()['actions']->edit )
                            echo "<img width=20 height=20 class='action-icon'src='/assets/icons/edit.svg' title='edit' name='edit' data-action='edit' data-id='" . $anagrafica -> id . "'>";
                        
                        if( $this -> session -> userdata()['actions']->delete ){
                            
                            echo "<img 
                                class='action-icon' 
                                src='/assets/icons/trash.svg' 
                                title='delete' 
                                name='delete' 
                                
                                data-action='delete' 
                                data-id='" . $anagrafica -> id . "' >";
                          
                        }

                        echo "</div>";
                    }
                      
                ?>
            

        </div>

        <div id='navibar'>
            <div id='paginazione'>
                <span class='hiddenXs'>Pagina:</span>
                <?php

                    for( $i = 1 ; $i <= $app_config['pagine_totali']; $i++){

                        if( $i == ( $app_config['pagina_corrente'] + 1 ) )
                            echo "<b>$i</b> ";
                        else
                            echo "<a href=/app?p=$i>$i</a> ";
                    }   
                ?>

            </div>

                 
            <div id='totale'>
                <span class='hiddenXs'>Sono presenti: </span>
                <?= $app_config['elementi_totali']; echo "&nbsp;"; echo ($app_config['elementi_totali'] == 1) ? "elemento" : "elementi" ?> 
            </div>

            <div id='elementipp'>
                
                Record <span class='hiddenXs'>per pagina</span>: 

                <select id='changeIpp'>

                    <?php
                    
                        foreach( $app_config['elementi_per_pagina'] as $epp ){
                            $sel = "";
                        
                            if( $epp == $app_config['default_elementi_per_pagina'] )
                                $sel = "selected";

                            echo "<option $sel>" . $epp . "</option>";
                        }   
                    ?>

                </select>
            </div>

        </div>
    </div>

</body>

<script src="/assets/js/app.js" defer></script>
<link rel="stylesheet" type="text/css" href="/assets/css/app.css" await>



</html>
