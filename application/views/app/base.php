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
    
    <link rel="stylesheet" type="text/css" href="/assets/css/app.css" >
    
    <script src="/assets/js/app.js" defer></script>
    

        

    <!-- csrf   -->
    <meta name="csrf-token-name" content="<?php echo $this->security->get_csrf_token_name(); ?>">
    <meta name="csrf-token-hash" content="<?php echo $this->security->get_csrf_hash(); ?>">

    <script>
        toaster = false;

        <?php
        
        if(  isset( $this -> session -> flashdata()['esitoForm'] )  ){
            echo "toaster = { type : '" . $this -> session -> flashdata()['esitoForm']['status'] . "', message : '" . addslashes( $this -> session -> flashdata()['esitoForm']['message'] ) . "' };";
        }
        ?>
    </script>

</head> 

<body>
    
    <div id='toaster' class='hiddener'>
        <img id='toaster-ico' src='/assets/icons/ko.svg' alt='ko' title='ko' loading=lazy width=30 height=30> 
        <div id='toaster-message'>Message</div>
    </div>

    <div id='modalContainer' class='hiddener'>
        <div class='modalMask hiddener' id='editMask'>
            <div id='editTitle'> *** TITOLO ***</div>
                
            <form id='editForm' action='' method='post'>
            
                <label for='e_nome'>Nome *</label>
                <input type='text' id='e_nome' name='nome' placeholder='Nome' required>
                
                <label for='e_cognome'>Cognome *</label>
                <input type='text' id='e_cognome' name='cognome' placeholder='Cognome' required>
                
                <label for='e_email'>Email*</label>
                <input type='email'id='e_email' name='email' placeholder='Email'>
                
                <label for='e_indirizzo'>Indirizzo</label>
                <input type='text' id='e_indirizzo' name='indirizzo' placeholder='Indirizzo'>
                
                <label for='e_sesso'>Sesso</label>

                <select id='e_sesso' name='sesso'>
                    <option value='' default disabled>Seleziona un'opzione</option>
                    <option value='M'>Maschio</option>
                    <option value='F'>Femmina</option>
                </select>

                <input type='hidden'    id='e_id' name='id'>


            </form>

            <div id='editActions'>
                <div class='service-button' id='editCancel'>Chiudi</div>
                <div class='action-button' id='editSave' >Salva</div>
            </div>

        </div>

        <div class='modalMask hiddener' id='waitMask'>
            
            <img src='/assets/icons/admin.svg' alt='wait' title='wait' loading=lazy width=100 height=100 id='waitingIcon'>

        </div>

        <div class='modalMask hiddener' id='errorMask'>
            <h3>Si è verifcato un errore.</h3>
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
                    $sorting = "<div class='sorter' data-field=\"$campo\" data-sort=0>
                    
                    <img src=/assets/icons/neutro.svg alt='neutro' title='neutro' loading=lazy width=20 height=20>
                    
                    </div>";

                    if( $app_config['ordinamento'] == $campo ){

                        if( $app_config['verso'] == 2){
                            $sorting = "<div class='sorter' data-field=\"$campo\" data-sort=1>
                                <img src='/assets/icons/desc.svg' alt='clicca per cambiare ordinamento' title='clicca per cambiare ordinamento' loading=lazy width=20 height=20>
                    
                            </div>"; //  desc
                        }else{
                            $sorting = "<div class='sorter' data-field=\"$campo\" data-sort=2>
                                <img src='/assets/icons/asc.svg' alt='clicca per cambiare ordinamento' title='clicca per cambiare ordinamento' loading=lazy width=20 height=20>
                    
                            </div>"; //  asc
                        }
                    }

                    echo "<div class='headField $extraClass'>$campo $sorting</div>";
                
                }
            
                #   azioni
                echo "<div class='headField'>
                        
                </div>";

                $extraClass = "";

                foreach( $content as $idx => $anagrafica ){

                    #   diegno i campi

                    foreach( $app_config['campi'] as $campo ){

                        $extraClass = ( isset( $app_config['hiddenables'][$campo] ) ) ? "hiddenXs" : "";

                        echo "<div class='commonField row_".$idx." $extraClass' data-row='" . $idx . "' > " . $anagrafica -> $campo ."</div>";
                    }

                    #   disegno le azioni
                    
                    echo "<div class='actions commonField row_".$idx." ' data-row='" . $idx . "' >";

                    /*                        
                    if( $this -> session -> userdata()['actions']->show )
                        echo "<img width=20 height=20 class='action-icon'src='/assets/icons/view.svg' title='edit' name='edit' data-action='view' data-id='" . $anagrafica -> id . "'>";
                    /** */

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

                    for( $i = 0 ; $i < $app_config['pagine_totali']; $i++){

                        $p = $i + 1;

                        if( $i == $app_config['pagina_corrente'] ){
                            echo "<b>$p</b> ";                        
                        }else{
                            echo "<a href=/app?p=$i>$p</a> ";
                        }
                    }   
                ?>

            </div>

                 
            <div id='totale'>
                <span class='hiddenXs'>Sono presenti: </span>
                <?= $app_config['elementi_totali']; echo "&nbsp;"; echo ($app_config['elementi_totali'] == 1) ? "elemento" : "elementi" ?> 

                <img src='/assets/icons/new.svg' alt='add' title='add' loading=lazy width=30 height=30 id='addButton'>

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

</html>
