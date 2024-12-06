<?
/*
EXERCUTAR AUTOMATICAMENTE A COLETA VIA CRONTAB E GRAVA NA BASE DE DADOS
*/

/* ======================================
 FUNÇÃO DE COLETA
=========================================*/
function tt_collect($local,$gc5) {

    //Define a URL de consulta
    if ($local == "brazil") {
        $url = "https://trendings.midiadigital.info/capture/brazil";
        $table = "trending_collects_brazil";
    } else {
        $url = "https://trendings.midiadigital.info/capture";
        $table = "trending_collects_world";
    }

    //Captura TT atual
    $tt_raw = null;
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl,CURLOPT_TIMEOUT,80);
    $tt_raw = curl_exec($curl);
    curl_close($curl);

    if ($tt_raw) {

        //Transforma em array
        $tt_raw_arr = json_decode($tt_raw,true);

        //Data da captura
        $date_cap = date("Y-m-d H:i:s", $tt_raw_arr['time']);

        //Percorre os TT
        if (is_array($tt_raw_arr) and isset($tt_raw_arr['trends'])) {

            foreach ($tt_raw_arr['trends'] as $tt) {

                //Verifica se já existe esse termo
                $it = 'SELECT term FROM trending_terms WHERE term="'.$tt["trending"].'";';
                $r = $gc5->conect_trending($it);

                //Caso não exista
                if (mysqli_num_rows($r) == 0) {

                    //Trata o termo
                    $clean = $gc5->term_to_string($tt["trending"]);

                    //Conta palavras e letras
                    $clean = str_replace("  "," ",trim($clean));
                    $words = count(explode(" ",$clean));
                    $letters = strlen($tt["trending"]);
                
                    //Insere o novo termo
                    $i = 'INSERT INTO trending_terms (term,url,words,letters,date_inc) 
                          VALUES ("'.$tt["trending"].'","'.$tt["url"].'",'.$words.','.$letters.',"'.$date_cap.'");';
                    $gc5->conect_trending($i);

                    //Seleciona o termo inserido
                    $r = $gc5->conect_trending($it);
                
                }
                
                //Verifica se o termo já foi gravado com essa data
                $i = 'SELECT id_trend FROM '.$table.'
                      WHERE term="'.$tt["trending"].'" and capture_date="'.$date_cap.'";';
                $r = $gc5->conect_trending($i);
                
                //Caso não exista
                if (mysqli_num_rows($r) == 0) {
                    
                    //Insere a captura
                    $i = 'INSERT INTO '.$table.' (capture_date,term,position,volume)
                          VALUES ("'.$date_cap.'","'.$tt["trending"].'",
                                  '.$tt["position"].',"'.$tt["volume"].'");';
                    $gc5->conect_trending($i);
                    
                }

            }//each

        }//isset

    }//tt_raw

}//function


/* ======================================
 REALIZA A COLETA
=========================================*/
tt_collect("brazil",$tt);
tt_collect("world",$tt);
?>