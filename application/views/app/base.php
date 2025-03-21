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
        
        <div class='modalMask hiddener' id='viewMask'>
            
            <div class='titolo'></div>
            <div class='dati'></div>
            
            <div class='service-button closeMask' id='viewCancel'>Chiudi</div>
        </div>
        
        <div class='modalMask hiddener' id='editMask'>
            <div id='editTitle'> *** TITOLO ***</div>
                
            <form id='editForm' action='' method='post'>
            
                <label for='e_nome'>Nome *</label>
                <input class='formField' type='text' id='e_nome' name='nome' placeholder='Nome' required>
                
                <label for='e_cognome'>Cognome *</label>
                <input class='formField' type='text' id='e_cognome' name='cognome' placeholder='Cognome' required>
                
                <label for='e_email'>Email*</label>
                <input class='formField' type='email' id='e_email' name='email' placeholder='Email' required>
                
                <label for='e_indirizzo'>Indirizzo</label>
                <input class='formField' type='text' id='e_indirizzo' name='indirizzo' placeholder='Indirizzo'>
                
                <label for='e_sesso'>Sesso</label>

                <select class='formField' id='e_sesso' name='sesso'>
                    <option value='' default disabled>Seleziona un'opzione</option>
                    <option value='M'>Maschio</option>
                    <option value='F'>Femmina</option>
                </select>

                <input type='hidden'    id='e_id' name='id'>
            </form>

            <div id='editActions'>
                <div class='service-button closeMask' id='editCancel'>Chiudi</div>
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
            
            <div class='title'><?= $title ?>
        </div>

            <div class='filler'>
                <img id='logout' 
                    src="<?= base_url( "/assets/icons/logout.svg" ) ?>" 
                    alt="logout" 
                    title="logout" 
                    loading=lazy width=30 height=30            
                    >
            </div>
        
        </div>

        <div id='tableContainer'>
        
            <div id='theTable'>
            
            <?php 

                #   costruzione tabella

                #   TABLE HEAD

                foreach( $app_config['campi'] as $campo ){

                    $extraClass = ( isset( $app_config['hiddenables'][$campo] ) ) ? "hiddenXs" : "";
                    
                    /*  gestione del sorting */
                    
                    $current_sort = $this -> session -> userdata()['ordinamento'][$campo]['dir'];
                    
                    
                    
                    #   sorting v2 
                    $sorting = "<div class='sorter' data-field='".$campo."' data-sort='".$current_sort."'>";
                    
                    $up = ( $current_sort == 1 ) ? ["icon" => "asc.svg", "dir" => 0 ] :  ["icon" => "asc_off.svg", "dir" => 1 ];
                    $dw = ( $current_sort == 2 ) ? ["icon" => "desc.svg", "dir" => 0 ] : ["icon" => "desc_off.svg", "dir" => 2 ];

                    $sorting .= "
                    <img 
                        src='/assets/icons/${up['icon']}' 
                        alt='asc' 
                        title='asc' 
                        loading=lazy 
                        width=20 
                        height=15 
                        class=' sorter-icon '  data-field='".$campo."' data-sort='${up['dir']}'
                    >
                    
                    <img 
                        src='/assets/icons/${dw['icon']}' 
                        alt='desc' 
                        title='desc' 
                        loading=lazy 
                        width=20 
                        height=15 
                        class=' sorter-icon '  data-field='".$campo."' data-sort='${dw['dir']}'
                    >";
                    
                    $sorting .= "</div>";

                    /*  gestione del filtro */
                            
                    $current_filter = $this -> session -> userdata()['filtri'][$campo];
                        
                    $filter = "<div class='filter' data-field='".$campo."' data-filter='".$current_filter."'>";

                    if( empty( $current_filter ) ){
                        $filter .= "<img src='/assets/icons/filter-off.svg' alt='filtro non impostato' title='filtro non imposstato' loading=lazy width=20 height=20>";
                    }else{
                        $filter .= "<img src='/assets/icons/filter-on.svg' alt='filtro impostato : " . addslashes( $current_filter ) . "' title='filter' loading=lazy width=20 height=20>";

                    }
                    
                    $filter .= "</div>
                    
                    <dialog class='filterField hiddener' id='filterField_".$campo."'>
                            
                            <input  type='text' 
                                    class='filterInput' 
                                    placeholder='Filtra ".$campo." ' value='".$current_filter."'
                                    id='filter_".$campo."'
                            >

                            <button class='filterBtn' data-field='".$campo."' id='filterBtn_".$campo."'>Filtra</button>
                            
                            <span class='filterClose' >X chiudi</span>

                        </dialog>
                    
                    ";


                    $nomeCampo = "<div>".substr($campo, 0, 3) . "<span class='hiddenXs'>" . substr($campo, 3) . "</span></div>";

                    echo "  <div class='headField $extraClass'>
                                $nomeCampo $filter $sorting
                            </div>";
                }
            
                #   azioni
                echo "<div class='headField'></div>";

                $extraClass = "";

                #   TABLE BODY


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
                    
                <div>
                    <span class='hiddenXs'>Sono presenti: </span>
                    <?= $app_config['elementi_totali']; echo "&nbsp;"; echo ($app_config['elementi_totali'] == 1) ? "elemento" : "elementi" ?> 
                </div>

                
                <?php

                if(  isset( $this -> session -> userdata()['actions']->create ) && $this -> session -> userdata()['actions']->create ){
                    
                    echo "<img src='/assets/icons/new.svg' alt='add' title='add' loading=lazy width=20 height=20 id='addAnagraficaBtn'>";
                
                }?>


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
