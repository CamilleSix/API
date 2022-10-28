<?php

/* identifiant de connexion ftp ARGUS */
// ne pas oublier d'activer php_ftp dans le php.ini

$ftpHost ="ftp.publicationvo.com";
$ftpLogin="***";
$ftpPassword= "***";


$ftp = ftp_connect($ftpHost, 21);
ftp_login($ftp, $ftpLogin, $ftpPassword);

$savePath= "***";

$carsCsv ="***";
$carsPictures ="photos.txt.zip";
// les deux fichiers a récupérer

ftp_pasv($ftp, true) ;
ftp_get($ftp, $savePath.$carsCsv,"./datas/".$carsCsv, FTP_BINARY, 0);
// ftp_get($ftp, $savePath.$carsPictures,"./datas/".$carsPictures, FTP_BINARY);


//$savedCsv = str_getcsv($savedCsv, ';') ;
// j'aime autant décomposer en plusieurs étapes plutôt que d'utiliser fgetcsv() qui ne me permettrais pas de debug un éventuel soucis d'encodage
// le decode automatique csv vers php marche pas, surement parce que le caractère d'encodage est un ; du coup on parse gentiment tout seul

$savedCsv = file_get_contents($savePath.$carsCsv) ;
$savedCsv = str_replace('"', '',$savedCsv) ; // on supprime tous les " du fichier, on en a pas besoin pour le .json
$savedCsv = explode(PHP_EOL,$savedCsv ) ;// coupe à chaque saut de ligne

//la première ligne correspond forcement à l'entête
$csvHeader = explode(';',$savedCsv[0]) ;

// crée un tableau vide pour save le résultat
$csvResult = [] ;

$saveFilterKey = ['VehiculeCategorie', 'VehiculeMarque', 'VehiculeBoite', 'VehiculeEnergie'] ;
$savedFilterTable = [] ;
foreach ($savedCsv AS $key => $csvLine){
    if ($key >0) {

        $lineArray = explode(';', $csvLine); // découpe chaque ligne avec les ;
        if (!empty($lineArray[1])) {


            // on vérifie que la ligne n'est pas vide avec le second champ qui est obligatoirement rempli
            foreach ($csvHeader as $position => $columnName) {
                $csvResult[$key][$columnName] = $lineArray[$position];
            }
            $csvResult[$key]['VehiculeModele'] = str_replace(' ', '-', $csvResult[$key]['VehiculeModele']);
            // comme c'est le champ utilisé dans l'url pour retrouvé la voiture, on supprime les espaces

        }
    }

    foreach ($saveFilterKey AS $filterName){
        if (!empty($csvResult[$key][$filterName])){
            $savedFilterTable[$filterName][$csvResult[$key][$filterName]] = $csvResult[$key][$filterName] ;
        }
    }
}



file_put_contents($savePath.$websiteId."-get-argus-cars.json", json_encode($csvResult, JSON_PRETTY_PRINT)) ;
file_put_contents($savePath.$websiteId."-get-argus-filters.json", json_encode($savedFilterTable, JSON_PRETTY_PRINT)) ;

?>
