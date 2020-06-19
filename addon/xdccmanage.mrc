/*
###########################################################################
#####                                                                   
#####                 XdccManage ADD ON Final Version
#####
##### L'addon XdccManage mette a disposizione i seguenti comandi da
##### dare in chan:
#####
##### !vetrina : tramite  questo  comando  l'utente  potrà  consultare
##### la vetrina direttamente dal canale
#####
##### !ultimi : visualizza gli ultimi inserimenti delle vetrine
#####
##### !version : visualizza la versione in uso (solo da @ in su)
#####
##### !grado : da i gradi impostati ai vostri bots (solo da @ in su) 
#####
##### !rileggi: Esegue una rilettura delle vetrine (stessa cosa del 
##### tasto "Rileggi Vetrine"). Effettuabile solo dai founder
##### del chan (nick con ~)
#####
##### Come caricare l'add on:
##### copia questo file nella root (cartella) del tuo mIRC e poi scrivi:
##### /load -rs xdccmanage.mrc
#####
##### Basato sul primo xdccmanage.mrc di puccio
##### Coder: TSoft 
#####
##### Ogni modifica e/o riproduzione è severamente vietata. Anche modificare
##### i colori non è gradito e/o la forma grafica dell'output.
#####
##### Per suggerimenti scrivete a tsoft@email.it
##### Rispettare il lavoro altrui è segno di buona educazione.
##### Si consiglia di leggere il documento Leggimi.pdf in allegato !
#####
##### Ringraziamenti a: puccio, ^Manu`, seby82, pipino, Luciano e a chi
##### mi ha supportato e sopportato XD          
############################################################################
*/


##### NON TOCCARE IL CODICE SEGUENTE SE NON SAI QUELLO CHE STAI FACENDO !!!
##### NON TOCCARE IL CODICE SEGUENTE SE NON SAI QUELLO CHE STAI FACENDO !!!
##### NON TOCCARE IL CODICE SEGUENTE SE NON SAI QUELLO CHE STAI FACENDO !!!

### Version

alias XMtmpv return { Build 5.3.140116 }

alias XMver return { XdccManage ADD ON Final Version - $XMtmpv (public) }
alias -l XMlastb return { $XMtmpv (public) }


### Menu

menu status { 
  Xdccmanage : dialog -m xdccm xdccm
}

### Dialog

dialog xdccm {
  title "Xdccmanage Final Version"
  size -1 -1 428 140
  option dbu
  edit "", 1, 48 86 368 30, autohs limit 300
  edit "", 2, 48 8 140 10, autohs limit 30
  edit "", 3, 48 26 140 10, autohs limit 60
  edit "", 4, 48 43 140 10, autohs limit 60
  text "Link Lista File", 6, 2 27 42 8
  text "Link Sito (Banner)", 7, 2 43 42 8
  text "Testo Extra", 8, 2 87 42 8
  edit "", 10, 307 8 16 10, autohs limit 2
  text "Attiva si/no", 11, 2 97 42 8
  radio "si", 12, 2 105 18 10
  radio "no", 13, 24 105 20 10
  edit "", 14, 48 58 186 10, autohs limit 250
  text "Togli estenzioni", 15, 2 59 42 8
  button "Salva/Chiudi", 16, 380 123 37 12, ok
  button "Avvia", 17, 335 123 37 12
  button "Canale", 5, 2 10 42 8
  button "Timer", 9, 257 10 42 8
  button "Rileggi Vetrine", 18, 5 123 37 12
  text "min", 19, 328 9 26 8
  text "Rispondi a !list", 20, 257 26 42 8, center
  text "Nome index", 23, 258 43 42 8, center
  edit "", 24, 307 43 109 10, autohs limit 50
  check "si", 25, 308 24 16 10
  check "no", 21, 328 24 16 10
  button "Ferma", 22, 290 123 37 12
  text "AutoAvvio on join", 26, 258 59 43 8
  check "", 27, 308 58 9 10
  text "Stampa colorata", 28, 48 124 43 8, center
  check "", 29, 97 123 12 10
  text "Pubblicità Add on", 30, 171 124 48 8, center
  check "", 31, 225 123 10 10
  text "Data in Vetrina", 32, 111 124 39 8, center
  check "", 33, 157 123 9 10
  text "AutoGrado", 34, 258 72 43 8, center
  check "", 35, 308 71 8 10
  edit "", 36, 317 70 56 10, autohs limit 35
  combo 37, 378 70 38 47, size limit 4 drop
  button "Check Hosting", 38, 195 25 39 12
  text "Log on", 39, 240 124 21 8
  check "", 40, 265 123 9 10
  text "Stampa Random/Cronologica", 41, 2 72 71 8
  combo 42, 85 70 64 41, size drop
}

## Errore Versione Mirc Dialog

dialog wrongver {
  title "Warning"
  size -1 -1 231 56
  option dbu
  text "STAI UTILIZZANDO UNA VERSIONE VECCHIA DI MIRC. QUESTO POTREBBE DARTI DEI PROBLEMI CON QUESTO ADD ON. L'ADD ON FUNZIONERA' COMUNQUE LO STESSO. TI CONSIGLIO PERO' DI AGGIORNARE IL TUO MIRC A UNA VERSIONE PIU' NUOVA. (CONSIGLIATA 6.35)", 1, 2 6 227 37, center
  button "CHIUDI", 2, 193 43 37 12, ok cancel
  text "QUESTO AVVISO SI CHIUDE IN:", 3, 61 47 78 8
  text " 30", 4, 139 47 15 8, center
  text "SECONDI", 5, 156 47 35 8
}

### Gestione Dialog Principale

on *:dialog:xdccm:init:0:{
  dialog -t xdccm Xdccmanage Final Version $XMlastb - $iif($XMmain,Status: On,Status: Off)
  did -b xdccm 2
  did -b xdccm 10
  did -a xdccm 2 $XMchan
  did -a xdccm 3 $XMlink
  did -a xdccm 4 $XMlista
  did -a xdccm 14 $XMext
  did -a xdccm 1 $XMextra
  did -a xdccm 10 $XMtime
  if ($XMyn == no) { did -c xdccm 13 }
  else { did -c xdccm 12) }
  if ($XMlyn == no) { did -c xdccm 21 | did -b xdccm 4,1,12,13 }
  else { did -c xdccm 25 }
  if ($XMmain) { did -b xdccm 17 | did -e xdccm 22 }
  else { did -e xdccm 17 | did -b xdccm 22 }
  if (($XMlink) && ($XMchan)) { did -e xdccm 18 }
  else { did -b xdccm 18 }
  if ($did(3) == http://spazio.altervista.org/lista/) { did -r xdccm 3 | did -a xdccm 3 http://spazio.altervista.org/lista/ <<<< Modifica questo }
  if ($did(4) == http://sito.altervista.org) { did -r xdccm 4 | did -a xdccm 4 http://sito.altervista.org <<<< Modifica questo }
  did -a xdccm 24 $XMindex
  if ($XMauto == si) { did -c xdccm 27 }
  else { did -u xdccm 27 }
  if ($XMcol == si) { did -c xdccm 29 }
  else { did -u xdccm 29 }
  if ($XMvers == si) { did -c xdccm 31 }
  else { did -u xdccm 31 }
  if ($XMdata == si) { did -c xdccm 33 }
  else { did -u xdccm 33 }
  if ($XMavoice == si) { did -c xdccm 35 }
  else { did -u xdccm 35 | did -b xdccm 36,37 }
  if ($did(36) == Bot|) { did -r xdccm 36 | did -a xdccm 36 Bot| }
  else { did -a xdccm 36 %TXMbot }
  ;autovoice
  did -a xdccm 37 Voice
  did -a xdccm 37 Hop
  did -a xdccm 37 Op
  did -a xdccm 37 Protect
  if (!$XMgrado) || ($XMgrado == Voice) { did -c xdccm 37 1 | set %TXMgrado Voice }
  if ($XMgrado == Hop) {  did -c xdccm 37 2 | set %TXMgrado Hop }
  if ($XMgrado == Op) {  did -c xdccm 37 3 | set %TXMgrado Op }
  if ($XMgrado == Protect) {  did -c xdccm 37 4 | set %TXMgrado Voice }
  if (*Modifica questo* iswm $did(xdccm,3)) || (*<* iswm $did(xdccm,3)) { did -b xdccm 38 }
  if ($XMmain) { did -b xdccm 38 }
  if ($XMlog == si) { did -c xdccm 40 }
  else { did -u xdccm 40 }
  ;modus
  if ($XMmain) { did -b xdccm 42 }
  did -a xdccm 42 Random
  did -a xdccm 42 Cronologica
  if (!$XMmodus) || ($XMmodus == Random) { did -c xdccm 42 1 | set %TXMmodus Random }
  if ($XMmodus == Cronologica) {  did -c xdccm 42 2 | set %TXMmodus Cronologica }
  ;tasti quando on
  if ($XMmain) { did -b xdccm 3,4,24,5 }
}

on *:dialog:xdccm:*:*:{
  ; Canale
  if ($devent == sclick) && ($did == 5) {
    if ($did(2).enabled) { did -b xdccm 2 | update.dbar 2 Canale salvato | halt } 
    did -e xdccm 2 | update.dbar 3 Edita canale ora!
  }
  if ($devent == edit) && ($did == 2) { set %TXMchan $did(2) }
  if ($devent == edit) && ($did == 3) { if (!$did(3)) halt
    if (http:// != $left($did(3),7)) { set %TXMlink http:// $+ $did(3) }
    else { set %TXMlink $did(3) }
  }
  if ($devent == edit) && ($did == 4) {
    if (http:// != $left($did(4),7)) { set %TXMlista http:// $+ $did(4) }
    else { set %TXMlista $did(4) }
  }
  ; Timer
  if ($devent == sclick) && ($did == 9) {
    if ($did(10).enabled) { did -b xdccm 10 | update.dbar 2 Timer salvato | halt }
    did -e xdccm 10 | update.dbar 3 Edita timer ora!
    if ($XMmain) { stop.soft | button.on | update.dbar 3 Add On Fermato. Edita timer ora! }
  }
  if ($devent == edit) && ($did == 14) { set %TXMext $did(14) | if (!$did(14)) { did -a xdccm 14 .part .avi .rar .db2 .iso .zip .cd .mkv | set %TXMext .part .avi .rar .db2 .iso .zip .cd .mkv } } 
  if ($devent == edit) && ($did == 1) { set %TXMextra $did(1) | if (!$did(1)) {
      did -a xdccm 1 Clicca UNA VOLTA Su Una Pubblicità. Appena Ti Si Apre La Nuova Pagina Copia L'Indirizzo Di Quest'Ultima E Incollalo In Privato Al Nostro Bot
      set %TXMextra Clicca UNA VOLTA Su Una Pubblicità. Appena Ti Si Apre La Nuova Pagina Copia L'Indirizzo Di Quest'Ultima E Incollalo In Privato Al Nostro Bot
    }
  }
  ; Tasto Ferma
  if ($devent == sclick) && ($did == 22) { if ($XMmain) { xm.log [STOP] addon fermato | stop.soft | button.on | update.dbar 1 Add On Fermato } }
  ; File index.php
  if ($devent == edit) && ($did == 24) { set %TXMindex $did(24) | if (!$did(24)) { did -a xdccm 24 index.php | set %TXMindex index.php } } 
  ; Rileggi Vetrine  
  if ($devent == sclick) && ($did == 18) { if ($XMlink) && ($XMchan) { update.dbar 3 Rileggo Vetrine... | XM.new.vetrina } }
  if ($devent == edit) && ($did == 10) { set %TXMtime $did(10) }
  if ($devent == sclick) && ($did == 12) { set %TXMyn si | update.dbar 1 Testo Extra Attivo! }
  if ($devent == sclick) && ($did == 13) { set %TXMyn no | update.dbar 1 Testo Extra Disattivo! }
  ; Rispondi a lista si/no
  if ($devent == sclick) && ($did == 21) { set %TXMlyn no | did -u xdccm 25 | did -c xdccm 21 | did -b xdccm 4,1,12,13 | update.dbar 1 Non Rispondo Al Comando !list }
  if ($devent == sclick) && ($did == 25) { set %TXMlyn si | did -c xdccm 25 | did -u xdccm 21 | did -e xdccm 4,1,12,13 | update.dbar 1 Rispondo Al Comando !list }
  ; Tasto Salva/Chiudi
  if ($devent == sclick) && ($did == 16) { .timerupdbar off 
    if (!$did(10)) || ($did(10) !isnum) || ($did(10) < 0) || ($did(10) > 99) { set %TXMtime 10 }
    if (!$did(3)) { set %TXMlink http://spazio.altervista.org/lista/ }
    if (!$did(4)) { set %TXMlista http://sito.altervista.org }
    if (!$did(2)) { set %TXMchan #canale }
    if (!$did(36)) { set %TXMbot Bot| }
  }
  ; Tasto Avvia
  if ($devent == sclick) && ($did == 17) { if (!$did(3)) || (*Modifica questo* iswm $did(3)) { 
      beep 1
      update.dbar 1 Link errato e/o mancante!!!
      xm.log [START] link errato e/o mancante
      halt
    }
  }
  if ($devent == sclick) && ($did == 17) && ($status == disconnected) { 
    beep 1
    xm.log [START] non connesso a nessun server
    update.dbar 2 Non Sei Connesso A Nessun Server !!!
    halt
  }
  if ($devent == sclick) && ($did == 17) { if ($XMchan != #canale) && (!$XMmain) && ($XMchan) {
      if ($me ison $did(2)) { .dialog -k xdccm xdccm | xm_avvia }
      else { .join $XMchan | unset %accidenti | xm_avvia | update.dbar 1 Avvio In Corso... | .timer 1 1 .dialog -k xdccm xdccm  }
    }
    else { /beep 1 | xm.log [START] nome chan errato e/o inesistente | halt }
  }
  ; AutoAvvio on Join
  if ($devent == sclick) && ($did == 27) { if ($did(27).state == 0) { set %TXMauto no | update.dbar 1 AutoAvvio non attivo!  } | else { set %TXMauto si | update.dbar 1 AutoAvvio attivo! }
  }
  ; Stampa colorata
  if ($devent == sclick) && ($did == 29) { if ($did(29).state == 0) { set %TXMcol no | update.dbar 1 Stampa colorata non attiva! } | else { set %TXMcol si | update.dbar 1 Stampa colorata attiva! }
  }
  ; Pubblicità Add on
  if ($devent == sclick) && ($did == 31) { if ($did(31).state == 0) { set %TXMver no | update.dbar 1 Pubblicità non attiva! } | else { set %TXMver si | update.dbar 1 Pubblicità attiva! }
  }
  ; Data in Vetrina
  if ($devent == sclick) && ($did == 33) { if ($did(33).state == 0) { set %TXMdata no | update.dbar 1 Data non attiva! } | else { set %TXMdata si | update.dbar 1 Data attiva! }
  }
  ; Autogrado
  if ($devent == sclick) && ($did == 35) { if ($did(35).state == 0) { set %TXMavoice no | did -b xdccm 36,37 | update.dbar 1 Autogrado non attivo! } | else { set %TXMavoice si | did -e xdccm 36,37 | update.dbar 1 Autogrado attivo! }
  }
  if ($devent == edit) && ($did == 36) { set %TXMbot $did(36) }
  ; Autovoice
  if ($devent == sclick) && ($did == 37) { set %TXMgrado $did(xdccm,37).seltext }
  ; Modus
  if ($devent == sclick) && ($did == 42) { set %TXMmodus $did(xdccm,42).seltext }
  ; Check
  if ($devent == edit) && ($did == 3) { did -e xdccm 38 }
  if ($devent == sclick) && ($did == 38) { if (%TXMlink) && (*Modifica questo* !iswm %TXMlink) { did -r xdccm 3 | did -a xdccm 3 %TXMlink | update.dbar 6 Check In Corso. Attendere prego... | xm.check | did -b xdccm 17,38,42,3,24,5,9,16,18 | .timer 1 6 xm.check.result | .timer 1 9 xm.check.reset }
    else { beep 1 | xm.log [CHECK] link errato | update.dbar 1 Link Immesso Non Corretto !!! }
  }
  ; Log
  if ($devent == sclick) && ($did == 40) { if ($did(40).state == 0) { set %TXMlog no | update.dbar 1 Log non attivo! } | else { set %TXMlog si | update.dbar 1 Log attivo! }
  }
  ; Chiusura Dialog 
  if ($devent == close) { .timerupdbar off }
}

on *:dialog:wrongver:*:*:{
  if ($devent == close) {  .timerwe off | .timerweoff off }
  if ($devent == sclick) && ($did == 2) { .timerwe off | .timerweoff off }
}

; On Load / On Unload

on *:load:{
  if ($version < 6.35) { beep 1 | dialog -mo wrongver wrongver | wrongversion | xm.log [VERSION] versione mirc non adatta }
  unset %accidenti | unset %v3
  set %TXMchan $iif($read(insert.txt,1),$read(insert.txt,1),#canale) | set %TXMlink $iif($read(insert.txt,3),$read(insert.txt,3),http://spazio.altervista.org/lista/)
  set %TXMtime $iif($read(insert.txt,2),$read(insert.txt,2),10) | set %TXMlista $iif($read(insert.txt,4),$read(insert.txt,4),http://sito.altervista.org)
  set %TXMext $iif($read(insert.txt,5),$read(insert.txt,5),.part .avi .rar .db2 .iso .zip .cd .mkv .txt .pdf)
  set %TXMextra $iif($read(insert.txt,6),$read(insert.txt,6),Clicca UNA VOLTA Su Una Pubblicità. Appena Ti Si Apre La Nuova Pagina Copia L'Indirizzo Di Quest'Ultima E Incollalo In Privato Al Nostro Bot)
  set %TXMyn $iif($read(insert.txt,7),$read(insert.txt,7),no)
  set %TXMlyn $iif($read(insert.txt,8),$read(insert.txt,8),si)
  set %TXMindex $iif($read(insert.txt,9),$read(insert.txt,9),index.php)
  set %TXMauto $iif($read(insert.txt,10),$read(insert.txt,10),no)
  set %TXMcol $iif($read(insert.txt,11),$read(insert.txt,11),si)
  set %TXMver $iif($read(insert.txt,12),$read(insert.txt,12),no)
  set %TXMdata $iif($read(insert.txt,13),$read(insert.txt,13),si)
  set %TXMavoice $iif($read(insert.txt,14),$read(insert.txt,14),no)
  set %TXMbot $iif($read(insert.txt,15),$read(insert.txt,15),Bot|)
  set %TXMgrado $iif($read(insert.txt,16),$read(insert.txt,16),Voice)
  set %TXMlog $iif($read(insert.txt,17),$read(insert.txt,17),si)
  set %TXMmodus $iif($read(insert.txt,18),$read(insert.txt,18),Random)
  dialog -m xdccm xdccm
}

; On Unload save all settings on txt

on *:unload:{
  if ($dialog(xdccm)) { dialog -k xdccm xdccm }
  .write -l1 insert.txt %TXMchan
  .write -l2 insert.txt %TXMtime
  .write -l3 insert.txt %TXMlink
  .write -l4 insert.txt %TXMlista
  .write -l5 insert.txt %TXMext
  .write -l6 insert.txt %TXMextra
  .write -l7 insert.txt %TXMyn
  .write -l8 insert.txt %TXMlyn
  .write -l9 insert.txt %TXMindex
  .write -l10 insert.txt %TXMauto
  .write -l11 insert.txt %TXMcol
  .write -l12 insert.txt %TXMver
  .write -l13 insert.txt %TXMdata
  .write -l14 insert.txt %TXMavoice
  .write -l15 insert.txt %TXMbot
  .write -l16 insert.txt %TXMgrado
  .write -l17 insert.txt %TXMlog
  .write -l18 insert.txt %TXMmodus
  unset %TXM* | .timerlink off | unset %link.t | if ($exists(vetrina.txt)) { .remove vetrina.txt }
  unset %accidenti
  unset %v3
  unset %less
}

; Aliases avvia processo e controllo presenza vetrine

alias xm_avvia {
  XM.new.vetrina
  .timerav3 1 5 xm_avvia2
}

alias xm_avvia2 {
  if ($lines(vetrina.txt) ==  0) { 
    xm.log [SOCKET] errore vetrina | beep 3
    .dialog -mo xmnumv xmnumv
    halt
  }
  if ($me !ison $XMchan) { .timer 1 1 join $XMchan }
  .msg $XMchan 11XdccManage ADD ON Final Version (public) tentativo di avvio in corso...
  echo 9 -s XdccManage ADD ON avvio in corso in modalità " $+ $XMmodus $+ ".
  xm.log [START] avvio in modus $XMmodus
  unset %accidenti
  .timerli 1 5 link.random | .timerlink 0 $calc($XMtime * 60) link.random
}

dialog xmnumv {
  title "Avviso"
  size -1 -1 167 68
  option dbu
  text "LA LISTA NON HA VETRINE!!! L'ADDON COSI' NON PUO' FUNZIONARE E NON VERRA' AVVIATO. MI DISPIACE.", 1, 29 12 108 25, center
  button "CHIUDI", 2, 69 46 37 12, ok cancel
  box "Attenzione", 4, 5 2 156 38
}

on *:dialog:xmnumv:*:*:{
  .timer 1 10 close.numv
  if ($devent == sclick) && ($did == 2) {
    .dialog -x xmnumv xmnumv
    .timer 1 1 dialog -m xdccm xdccm
  }
  if ($devent == close) { .timer 1 1 dialog -m xdccm xdccm }
}

alias close.numv {
  if ($dialog(xmnumv)) { 
    .dialog -x xmnumv xmnumv
    .timer 1 1 dialog -m xdccm xdccm
  }
}

; Uscita dal chan - riavvio automatico

on me:*:part:#:if ($chan == $XMchan) && ($XMmain) { xm.log [SERVER] uscito dal chan | stop.soft | set %accidenti ! | if ($dialog(xdccm)) { button.on } }
on me:*:disconnect:if ($dialog(xdccm)) { xm.log [SERVER] disconnesso dal server | set %accidenti ! | button.on }
on me:*:quit:if ($dialog(xdccm)) { xm.log [SERVER] disconnesso dal server | set %accidenti ! | button.on }

; Automatismo quando il Bot joina in chan

on me:*:join:#:{
  if ($chan == $XMchan) {
    .timervoice 1 5 autovoice
    if ($timer(av3)) || ($timer(li)) { goto nothing }
    if ($XMauto == no) { goto auto }
    if (!$XMmain) && ($XMauto == si) { beep 3 | xm.log [START] AutoAvvio in esecuzione | echo -s 4AutoAvvio in progress... | xm_avvia | if ($dialog(xdccm)) { dialog -x xdccm xdccm } }
    goto nothing
  }
  :auto
  if ($XMauto == no) {
    if ($chan == $XMchan) && (!$XMmain) && (!$dialog(xmstart)) && ($XMchan != #canale) && (!$dialog(xdccm)) { dialog -mo xmstart xmstart }
    if ($chan == $XMchan) && (%accidenti) && (!$XMmain) && (!$dialog(xmstart)) && ($XMchan != #canale) && (!$dialog(xdccm)) { unset %accidenti | dialog -mo xmstart xmstart }
  }
  :nothing
}

; Qui da i gradi ai Bot

on *:join:#:{
  if ($chan == $XMchan) && ($XMmain) && (($me isop $XMchan) || ($me ishop $XMchan)) && ($XMavoice == si) && (%TXMbot isin $nick) { mode $XMchan + $+ $XMsegno $nick }
}

; Dialog avvio automatico

dialog xmstart {
  title "Avvia"
  size -1 -1 167 68
  option dbu
  text "L'Add On non è in esecuzione. Vuoi avviarlo con le ultime impostazioni funzionanti?", 1, 29 12 108 15, center
  button "SI", 2, 29 46 37 12, ok
  button "NO", 3, 100 45 37 12, ok
  box "Add On Auto Start", 4, 5 2 156 30
}
on *:dialog:xmstart:*:*:{
  if ($devent == sclick) && ($did == 2) {
    .dialog -x xmstart xmstart
    xm_avvia
  }
  if ($devent == sclick) && ($did == 3) { .dialog -x xmstart xmstart | halt }
  if ($devent == close) { .dialog -x xmstart xmstart | halt }
}

; Engine (Codice Principale)

on *:text:!vetrina:#:{
  if ($chan == $XMchan) && ($XMmain) {
    %XMlink = $XMlink
    if ( %XMlink ) {
      %XMchan = $XMchan
      if (!$regex(%XMlink,/\/$/i)) {
        %XMtemp = %XMlink $+ /
      }
      else {
        %XMtemp = %XMlink
      }
      if ($regex(%XMtemp,/http:\/\/([a-zA-Z0-9\-\.\_]+)(\/.*)/i)) {
        %XMhost = $regml(1)
        %XMpath = $regml(2) $+ vetrina.conf
      }
      .msg %XMchan 5,7/!\7,12V12,7e7,12t12,7r7,12i12,7n7,12a5,7/!\ - Digita uno dei comandi qui sotto
      sockclose XmGetVetrine
      sockopen XmGetVetrine %XMhost 80
    }
    else {
      unset %XM*
    }
  }
}
on *:sockopen:XmGetVetrine:{ 
  sockwrite -n $sockname GET %XMpath HTTP/1.1
  sockwrite -n $sockname User-Agent:Mozilla
  sockwrite -n $sockname Accept: text/html
  sockwrite -n $sockname Host: %XMhost
  sockwrite -n $sockname $crlf
}
on *:sockread:XmGetVetrine:{ 
  if ($sockerr) { xm.log [SOCKET] errore socket 
    return
  }
  else { 
    sockread %XMwebpage
    if ($regex(%XMwebpage,(.*)\|:) == 1) {
      .msg %XMchan 3! $+ $regml(1)
    }
  }
}

; Evento on text principale

on *:text:!*:*:{
  if ($chan == $XMchan) && ($XMmain) {
    if (($1 == @lista) || (!list* iswm $1)) {
      if ($XMlyn == si) { .msg $XMchan $chr(91) $+ 7 $+ $nick $+  $+ $chr(93) Per Visualizzare La Lista Vai Su <<9 $XMlista >> }
      if ($XMyn == si) { .msg $XMchan $XMextra }
    }
    if ($1 = !comandi) { .msg $XMchan Comandi disponbili: 4!vetrina Categorie a disposizione, 4!lista Visualizza il link della WebList, 4!ultimi Visualizza gli ultimi inserimenti }
  }
  if ($chan == $XMchan) && $trigger($1-) && ($XMmain) { 
    %XMlink = $XMlink
    if (%XMlink) {
      .msg $XMChan 4Lettura dati in corso...
      refresh
      %XMchan = $XMchan
      if ( !$regex(%XMlink,/\/$/i) ) {
        %XMtemp = %XMlink $+ /
      }
      else {
        %XMtemp = %XMlink
      }
      if ($regex(%XMtemp,/http:\/\/([a-zA-Z0-9\-\.\_]+)(\/.*)/i)) {
        %XMhost = $regml(1)
        %XMpath = $regml(2) $+ vetrina.conf
      }
      %XMcomando = $1-$0
      sockclose XmGetVetrina
      .timer 1 10 sockopen XmGetVetrina %XMhost 80
    }
    else {
      unset %XM*
    }
  }
  if ($chan == $XMchan) && ($1 == !ultimi) && ($XMmain) {
    ultimi
  }
  if ($chan == $XMchan) && ($XMmain) && ($nick isop $XMchan) && ($1 == !version) {
    .msg $XMchan 7 $XMver 
  }
  if ($chan == $XMchan) && ($XMmain) && ($nick isop $XMchan) && ($1 == !grado) {
    autovoice
  }
  if ($XMlink) && ($XMchan) && ($left($nick($XMchan,$nick).pnick,1) == $chr(126)) && ($1 == !rileggi) { XM.new.vetrina | .msg $XMChan 4Lettura Vetrine eseguito. }
}

; Ricerca vetrina da stampare

alias linkr {
  %XMlink = $XMlink
  if (%XMlink) {
    %XMchan = $XMchan
    if (!$regex(%XMlink,/\/$/i)) {
      %XMtemp = %XMlink $+ /
    }
    else {
      %XMtemp = %XMlink
    }
    if ($regex(%XMtemp,/http:\/\/([a-zA-Z0-9\-\.\_]+)(\/.*)/i)) {
      %XMhost = $regml(1)
      %XMpath = $regml(2) $+ vetrina.conf
    }
    %XMcomando = $1-$0
    sockclose XmGetNew
    sockclose XmGetVetrina
    .timer 1 5 .msg $XMchan 13Vetrina >>> [ $+ 8 $remove(%link.t,!)  $+ ] $iif($XMdata == si,7::: 14[8 $giorno $data 14],)
    .timer 1 5 sockopen XmGetVetrina %XMhost 80
  }
  else {
    unset %XM*
  }
}

alias link.random {
  if ($lines(vetrina.txt) ==  0) { xm.log [SOCKET] errore vetrina | beep 1 | stop }
  unset %link.t
  ; Gestione Random Cronologica
  if ($lines(vetrina.txt) > 0) && ($lines(vetrina.txt) <= 3) && ($XMmodus == Random) {
    xm.log [MAIN] forzato modo Cronologico (perché meno di 4 vetrine)
    set %TXMmodus Cronologica
    goto less
  }
  if ($lines(vetrina.txt) > 0) && ($lines(vetrina.txt) <= 3) {
    set %TXMmodus Cronologica
    goto less
  }
  if ($lines(vetrina.txt) > 0) && ($lines(vetrina.txt) > 3) && ($XMmodus == Random) { goto loop }
  if ($lines(vetrina.txt) > 0) && ($XMmodus == Cronologica) { goto less }
  ; RANDOM
  :loop
  set %link.t $read(vetrina.txt)
  if (%vecchio.link == %link.t) || (%stravecchio.link == %link.t) { goto loop }
  refresh
  linkr %link.t
  set %stravecchio.link %vecchio.link
  set %vecchio.link %link.t
  goto end
  ; CRONOLOGICA
  :less
  if (!%less) || (%less > $lines(vetrina.txt)) { set %less 1 }
  set %link.t $read(vetrina.txt,%less)
  refresh
  linkr %link.t
  inc %less
  :end
}

on *:sockopen:XmGetVetrina:{ 
  sockwrite -n $sockname GET %XMpath HTTP/1.1
  sockwrite -n $sockname User-Agent:Mozilla
  sockwrite -n $sockname Accept: text/html
  sockwrite -n $sockname Host: %XMhost
  sockwrite -n $sockname $crlf
}

on *:sockread:XmGetVetrina:{ 
  if ($sockerr) { xm.log [SOCKET] errore socket 
    return
  }
  else {
    sockread %XMwebpage
    if (*404*Not*Found* iswm %XMwebpage) { xm.log [SOCKET] errore vetrina | echo -s 4ADD ON FERMATO (errore vetrina) $fulldate  | .timerlink off | beep 2 | stop | update.dbar 2 Addon Fermato - Errore Vetrine! }
    if ($regex(%XMwebpage,(.*)\|:(.*)) == 1) {
      %XMvetrina = ! $+ $regml(1)
      if (%XMvetrina == %XMcomando) { 
        %XMnumelem = $count($regml(2),:)
        %XMi = 1
        %XMc = 3
        tokenize 32 $XMext
        while (%XMi <= %XMnumelem) {
          ;Metodo per eliminare le estenzioni solo alla fine e non in mezzo al nome file e controllo versione V2/V3
          var %regml = $replace($regml(2),$chr(32),$chr(46))
          var %mod1 = $gettok(%regml,%XMi,$asc(:))
          if (*#* !iswm %mod1) { var %mod.a = %mod1 | goto v2 }
          if (*#* iswm %mod1) { set %v3 ! }
          var %mod2 = $gettok(%mod1,2,35)
          var %mod3 = $mid(%mod2,2,$len(%mod2))
          var %mod.a = %mod3
          var %mod.data = $asctime($ctime($gettok(%mod1,1,46)),dd/mm)
          :v2
          if ($len(%mod.a) <= 5) { var %mod.b = %mod.a | goto salta }
          var %mod.b = $mid(%mod.a,1,$calc($len(%mod.a) - 5))
          var %mod.c = $right(%mod.a,5)
          .msg $XMchan 7[ $+ $regml(1) $+ ] $+ $iif(%TXMcol == si,%XMc,9)  $iif((%v3) && ($XMdata == si),%mod.data,) %mod.b $+ $remove(%mod.c,$1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19,$20,$21,$22,$23,$24,$25,$26,$27,$28,$29,$30)          
          goto salto
          :salta
          .msg $XMchan 7[ $+ $regml(1) $+ ] $+ $iif(%TXMcol == si,%XMc,9)  $iif((%v3) && ($XMdata == si),%mod.data,) %mod.b 
          :salto       
          inc %XMc | if (%XMc > 15) { %XMc = 3 }         
          inc %XMi
        }
        if ($XMvers == si) { .msg $XMchan 7 $XMver  }
        unset %v3
      } 
    }
  }
}

on *:sockclose:XmGet*:{
  unset %XM*
}

; Crea file vetrina.txt

alias XM.new.vetrina {
  unset %XV*
  if ($exists(vetrina.txt)) { .remove vetrina.txt }
  %XVlink = $XMlink
  if (%XVlink) {
    %XVchan = $XMchan
    if ( !$regex(%XVlink,/\/$/i) ) {
      %XVtemp = %XVlink $+ /
    }
    else {
      %XVtemp = %XVlink
    }
    if ($regex(%XVtemp,/http:\/\/([a-zA-Z0-9\-\.\_]+)(\/.*)/i)) {
      %XVhost = $regml(1)
      %XVpath = $regml(2) $+ vetrina.conf
    }
    sockclose XmGetNew
    if (%XVhost) { sockopen XmGetNew %XVhost 80 }
    else { xm.log [SOCKET] errore vetrina | beep 1 | stop | if ($dialog(xdccm)) { button.on | update.dbar 2 Addon Fermato - Errore Vetrine! } }
  }
  else {
    unset %XV*
  }
}

on *:sockopen:XmGetNew:{
  if ($sockerr) { xm.log [SOCKET] errore socket | return } 
  sockwrite -n $sockname GET %XVpath HTTP/1.1
  sockwrite -n $sockname User-Agent:Mozilla
  sockwrite -n $sockname Accept: text/html
  sockwrite -n $sockname Host: %XVhost
  sockwrite -n $sockname $crlf
}

on *:sockread:XmGetNew:{ 
  if ($sockerr) { xm.log [SOCKET] errore socket 
    return
  }
  else { 
    sockread %XVwebpage
    if ($regex(%XVwebpage,(.*)\|:) == 1) {
      write vetrina.txt ! $+ $regml(1)
    }
  }
}

on *:sockclose:XmGetNew:{
  unset %XV*
}

; Alias refresh (fa una visita sulla lista per aggiornare i dati)

alias refresh {
  if (!$regex($XMlink,/\/$/i)) { %XRtmp = $XMlink $+ / }
  else { %XRtmp = $XMlink }
  if ($regex(%XRtmp,/http:\/\/([a-zA-Z0-9\-\.\_]+)(\/.*)/i)) {
    %XRhost = $regml(1)
    %XRpath = $regml(2) $+ $XMindex
  }
  sockclose Xmrefresh
  sockopen Xmrefresh %XRhost 80
}

on *:sockopen:XMrefresh:{ 
  sockwrite -n $sockname GET %XRpath HTTP/1.1
  sockwrite -n $sockname User-Agent:Mozilla
  sockwrite -n $sockname Accept: text/html
  sockwrite -n $sockname Host: %XRhost
  sockwrite -n $sockname $crlf
  return
}

on *:sockclose:Xmrefresh:{
  unset %XR*
}

;Aliases di supporto

alias -l XMmain return { $iif($timer(link),$true,$false) }

alias XMchan return { %TXMchan }
alias XMlink return { %TXMlink } ; Link lista dei file
alias XMtime return { %TXMtime }
alias XMlista return { %TXMlista } ; Link sito banner
alias XMext return { %TXMext }                     
alias XMextra return { %TXMextra }
alias XMyn return { %TXMyn }
alias XMlyn return { %TXMlyn }
alias XMindex return { %TXMindex }
alias XMauto return { %TXMauto }
alias XMcol return { %TXMcol }
alias XMvers return { %TXMver }
alias XMdata return { %TXMdata }
alias XMavoice return { %TXMavoice }
alias XMgrado return { %TXMgrado }
alias XMmodus return { %TXMmodus }
alias XMsegno {
  if ($XMgrado == Voice) { return v }
  if ($XMgrado == Hop) { return h }
  if ($XMgrado == Op) { return o }
  if ($XMgrado == Protect) { return a }
}
alias XMlog return { %TXMlog }

; Alias per verifica inserimento nome vetrina

alias trigger {
  var %total = $lines(vetrina.txt)
  var %x = 1
  if (%total != 0) {
    while (%x <= %total) {
      if ($read(vetrina.txt,%x) == $1-) { return $true }
      inc %x
    }
  }
  return $false
}

; Ultime News inserite (comando !ultimi)

alias ultimi {
  .msg $XMChan 4Lettura dati in corso...
  refresh
  .timer 1 5 read.last.insert
}

alias read.last.insert {
  %tini = $lines(vetrina.txt)
  %XFlink = $XMlink
  if (%XFlink) {
    %XFchan = $XMchan
    if (!$regex(%XFlink,/\/$/i)) {
      %XFtemp = %XFlink $+ /
    }
    else {
      %XFtemp = %XFlink
    }
    if ($regex(%XFtemp,/http:\/\/([a-zA-Z0-9\-\.\_]+)(\/.*)/i)) {
      %XFhost = $regml(1)
      %XFpath = $regml(2) $+ vetrina.conf
    }
    sockclose XmGetNew
    sockclose XmScriviFile
    sockopen XmScriviFile %XFhost 80
  }
}

on *:sockopen:XmScriviFile:{ 
  sockwrite -n $sockname GET %XFpath HTTP/1.1
  sockwrite -n $sockname User-Agent:Mozilla
  sockwrite -n $sockname Accept: text/html
  sockwrite -n $sockname Host: %XFhost
  sockwrite -n $sockname $crlf
}

on *:sockread:XmScriviFile:{ 
  if ($sockerr) { xm.log [SOCKET] errore socket
    return
  }
  else { 
    var %x = 1
    sockread %XFwebpage
    :loop
    if ($regex(%XFwebpage,(.*)\|:(.*)) == 1) {
      %XFvetrina = ! $+ $regml(1)
      if (%XFvetrina == $read(vetrina.txt,%x)) { 
        %XFnumelem = 1
        %XFi = 1
        tokenize 32 $XMext
        while (%XFi <= %XFnumelem) {
          var %regml = $replace($regml(2),$chr(32),$chr(46))
          var %mod1 = $gettok(%regml,%XFi,$asc(:))
          if (*#* !iswm %mod1) { var %mod.a = %mod1 | goto v2 }
          if (*#* iswm %mod1) { set %v3 ! }
          var %mod2 = $gettok(%mod1,2,35)
          var %mod3 = $mid(%mod2,2,$len(%mod2))
          var %mod.a = %mod3
          var %mod.data = $asctime($ctime($gettok(%mod1,1,46)),dd/mm)
          :v2
          if ($len(%mod.a) <= 5) { var %mod.b = %mod.a | goto salta }
          var %mod.b = $mid(%mod.a,1,$calc($len(%mod.a) - 5))
          var %mod.c = $right(%mod.a,5)
          .msg $XMchan 7[ $+ $regml(1) $+ ]9 $iif((%v3) && ($XMdata == si),%mod.data,) %mod.b $+ $remove(%mod.c,$1,$2,$3,$4,$5,$6,$7,$8,$9,$10,$11,$12,$13,$14,$15,$16,$17,$18,$19,$20,$21,$22,$23,$24,$25,$26,$27,$28,$29,$30)
          goto salto
          :salta
          .msg $XMchan 7[ $+ $regml(1) $+ ]9 $iif((%v3) && ($XMdata == si),%mod.data,) %mod.b 
          :salto
          inc %XFi
        }
      } 
    }
    inc %x
    if (%x > %tini) { unset %v3 | halt }
    else { goto loop }
  }
}

on *:sockclose:XmScriviFile:{
  unset %XF*
  unset %tini
}

;Alias giorno e data in italiano

alias giorno {
  return $replace($day,Sunday,Domenica,Monday,Lunedì,Tuesday,Martedì,Wednesday,Mercoledì,Thursday,Giovedì,Friday,Venerdì,Saturday,Sabato)
}
alias data {
  return $replace($asctime($ctime,dd/mmmm/yyyy),/,$chr(32),January,Gennaio,February,Febbraio,March,Marzo,April,Aprile,May,Maggio,June,Giugno,July,Luglio,August,Agosto,September,Settembre,October,Ottobre,November,Novembre,December,Dicembre)
}

;Alias per Autogrado

alias autovoice {
  if (($me isop $XMchan) || ($me ishop $XMchan)) && ($XMavoice == si) { goto okey }
  else { return }
  :okey  
  var %n = 1
  var %nt = $nick($XMchan,0,r)
  while (%n < %nt) {
    if (%TXMbot isin $nick($XMchan,%n,r)) { mode $XMchan + $+ $XMsegno $nick($XMchan,%n,r) }
    inc %n
  }
}

;Aliases per Errore Versione Mirc

alias wrongversion {
  if ($dialog(wrongver)) {
    .timerwe 30 1 wrongverreps
    .timerweoff 1 30 dialog -x wrongver wrongver
  }
}
alias wrongverreps {
  if ($dialog(wrongver)) {
    ; qui il countdown da 30
    did -a wrongver 4 $timer(we).reps
  }
}

; Aliases ferma i timer

alias stop {
  .timerav3 off
  .timerli off
  .timerlink off
  h.msg
  if ($dialog(xdccm)) { .dialog -t xdccm Xdccmanage Final Version $XMlastb - $iif($XMmain,Status: On,Status: Off) }
  halt
}
alias stop.soft {
  .timerav3 off
  .timerli off
  .timerlink off
  unset %less
  h.msg
  return
}

;Engine Check

alias xm.check {
  unset %check.read
  sockclose XmCheck
  ;Il regex seguente estrae da un link http solo dns + dominio (www.dominio.com)
  if (!$regex(%TXMlink,/\/$/i)) { %XCtemp = %TXMlink $+ / }
  else { %XCtemp = %TXMlink }
  if ($regex(%XCtemp,/http:\/\/([a-zA-Z0-9\-\.\_]+)(\/.*)/i)) {
    %XChost = $regml(1)
    %XCpath = $regml(2) $+ $XMindex
    sockopen XmCheck %XChost 80
  }
}

on *:sockopen:XmCheck:{
  if ($sockerr) { set -u9 %xm.check error | return }  
  sockwrite -n $sockname User-Agent: Mozilla
  sockwrite -n $sockname Accept: text/html
  sockwrite -n $sockname Host: %XChost
  sockwrite -n $sockname $clrf
  if ($sockerr) { set -u9 %xm.check error }
  else { set -u9 %xm.check ok }
}

on *:sockwrite:XMCheck:{
  if ($sockerr)  { set -u9 %xm.check error }
  else { set -u9 %xm.check ok }
}

on *:sockread:XMCheck:{
  sockread %check.read
  if (!%check.read) { set -u9 %xm.check error }
  else { set -u9 %xm.check ok }
}

on *:sockclose:XMchek:{
  unset %XC*
}

;Dialog wrong link

dialog xm.wrong.link {
  title "Wrong"
  size -1 -1 174 49
  option dbu
  text "L'HOSTING DA TE SCELTO NON E' RAGGIUNGIBILE!!!", 1, 3 13 167 8, center
  button "Chiudi", 2, 133 32 37 12, ok cancel
}

;Dialog ok link

dialog xm.ok.link {
  title "Ok"
  size -1 -1 174 49
  option dbu
  text "L'HOSTING DA TE SCELTO E' OK", 1, 3 13 167 8, center
  button "Chiudi", 2, 133 32 37 12, ok cancel
}

;Alias Check result

alias xm.check.result {
  if (%xm.check == error) { xm.log [CHECK] link errato | dialog -mo xm.wrong.link xm.wrong.link | beep 1  }
  if (%xm.check == ok) { dialog -mo xm.ok.link xm.ok.link }
}

;Alias Check reset

alias xm.check.reset {
  if ($dialog(xdccm)) { did -e xdccm 17,38,42,3,24,5,9,16,18 }
  if ($dialog(xm.wrong.link)) { dialog -x xm.wrong.link }
  if ($dialog(xm.ok.link)) { dialog -x xm.ok.link }
}

;Alias Log System

alias xm.log {
  if (!$exists(xdcclog.txt)) { write xdcclog.txt }
  write xdcclog.txt $fulldate - $1-
}

;Alias aggiornamento barra titolo

alias update.dbar {
  if ($dialog(xdccm)) {
    dialog -t xdccm $2-
    .timerupdbar 1 $1 .dialog -t xdccm Xdccmanage Final Version $XMlastb - $iif($XMmain,Status: On,Status: Off)
    return
  }
}

;Alias Reset Tasti

alias button.on {
  if ($dialog(xdccm)) { did -b xdccm 22 | did -e xdccm 17,38,42,3,24,5,4 }
}

;Alias MSG Ferma

alias h.msg {
  if ($me ison $XMchan) { .msg $XMchan 4Addon Fermato!
  }
}
