###################
Demo ShaggyOwl
###################

Applicazione demo per la gestione di un'anagrafica con filtri e ordinamento basata su codeigniter 3.


*************
Live demo
*************

L'applicazione è visibile all'indirizzo http://storiedinatale.it/
Chiedo scusa per il nome... ma avevo questo dominio libero e l'ho sfruttato


*******************
Accessi
*******************

Ci sono due diversi tipi di ruolo, ognuno con permessi diversi:

Amministratori ( hanno accesso a tutte le funzionalità )
fabrizio : q1w2e3r4
giuseppe: q1w2e3r4 

Operatori ( non hanno diritto a cancellare )
ennio : q1w2e3r4


**************************
Features
**************************

Cercando di rimanere fedele alla traccia fornita ho cercato di realizzare un'applicazione completa sia dal punto di vista funzionale che dal punto di vista dell'usabilità.

Stack:
Ubuntu 20.04 / Nginx / PHP 8.3 / MariaDB 10.11.8

Backend: Codeigniter 3
Frontend: html5 / vanilla js

L'applicazione dovrebbe girare senza problemi su qualsiasi server con php 7 o superiore.
Non ho utilizzato librerie esterne per il frontend, ho scritto tutto in vanilla js per mantenere il codice il più leggero possibile.


**************************
Funzionalità
**************************

# Ordinamento:

Per ordinare i campi basa premere sulle freccettte vicino a ciascun titolo.
La freccetta si illumina e indica che il campo è ordinato.
Per togliere l'ordinamento basta cliccare sulla freccia illuminata.

Il sistema ricorda la sequenza di ordinamento.
Se si seleziona prima l'ordinamento per sesso e poi per cognome questo verrà ricordato.


# Filtro:
Per filtrare i campi basta cliccare sull'icona filtro presente di fianco al titolo.
Si aprirà una finestra con un campo testo da utilizzare per filtrare.
Gli unici caratteri validi sono caratteri alfanumerici minuscoli.

In base ai filtri impostati verrà aggiornato anche il dato del conteggio record
Se il numero di record alla pagina visualizzata non è compatibile con il numero di elementi in pagina il sistema resetterà le pagine alla prima.


# azioni:
Per ogni riga ci sono delle operazioni che possono essere eseguite sul record se l'utente che ha fatto l'accesso ne ha diritto.
le azioni sono: 
 + mostra: apre il dettaglio completo dell'utente
 + edita: apre la maschera per modificare l'utente
 + elimina: elimina l'utente dopo aver mostrato la finestra di conferma

per aggiungere un nuovo utente basta cliccare sul tasto + in fondo alla pagina, vicino al numero dei record presenti.



**************************
Note
**************************

L'applicazione è stata sviluppata in circa 20 ore.
Non ho avuto tempo di fare un refactoring completo del codice, ma ho cercato di mantenere il codice il più pulito possibile.
Il codice è stato sviluppato utilizzando differenti tecniche e stili con lo scopo di mostrare flessibilità.
Non sono un esperto di codeigniter 3 ma ho cercato di utilizzarne le funzionalità principali  ( security, hooks, library, routing ecc.. )

Spero che il mio lavoro sia di vostro gradimento e spero che questa finestra, seppur parziale, sulle mie capacità sia per voi sufficiente per valutare la mia candidatura.

Grazie.