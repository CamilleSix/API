<?php
/*
 *
 * Script de répartition des appels pour une crontab qui tourne toute les minutes
 * Ce script effectue des appels aux tâches de monitoring, récupère des infos vers des API et des feeds instagram
 * Le tout est géré avec un système basé sur les datetime php
 *
 */
error_reporting(E_ALL);
ini_set("display_errors", "On");

// pour les date() N = "numéro du jour de la semaine" 1 (pour Lundi) à 7 (pour Dimanche)
$crontab = [

    "instaFeed"=>[
        "isLocalScript"=> true,
        "clientList"=>[
            "COACH"=>[
                "time"=> ["1 08:00", "3 08:00", "5 08:00"], // les lundi, mercredi et vendredi à 8h du matin
                "timeFormat"=> "N H:i",
                "instagramUserName"=>"***",
                "instagramId"=>"***"

            ],
            "SECCA"=>[
                "time"=> ["1 07:00"], // tous les lundis à 7h du matin
                "timeFormat"=> "N H:i",
                "instagramUserName"=>"***",
                "instagramId"=>"***"
            ],
            "PUSSIC"=>[
                "time"=> ["2 07:00"], // tous les mardis à 7h du matin
                "timeFormat"=> "N H:i",
                "instagramUserName"=> "***",
                "instagramId"=>"***"
            ]

        ]

    ],
    "getArgus"=>[
        "isLocalScript"=> true,
        "clientList"=>[
            "PUSSIE"=>[
                "time"=> ["08:00"]
            ]

        ]

    ]
    ,
    "pingServer"=>[
        "isLocalScript"=> false,
        "clientList"=>[
            "WEMAJ"=>[
                "time"=> ["00","15","30","45"], // toutes les heures :à chaque fois que les minutes correspondent à "00" à "15", "30" et à "45"
                "timeFormat"=> "i",
                "link"=> "***"
            ],
            "AMBLA"=>[
                "time"=> ["01:00"],
                "link"=> "***"
            ]
        ]

    ]

] ;

$currentTime =  date("H:i", time()) ;
echo $currentTime." test" ;

$logs = file_get_contents(__DIR__.'/logs.txt') ;

foreach ($crontab AS $scriptName => $cron){


    foreach ($cron["clientList"] AS $websiteId => $cronData){

        if (!empty($cronData['timeFormat'])){
            $currentTime =date($cronData['timeFormat'], time()) ;
        } else {
            $currentTime =  date("H:i", time()) ;
        }
        

        if (in_array($currentTime,$cronData["time"]) || in_array("ALWAYS",$cronData["time"] )) {

            if ($cron["isLocalScript"] == true) {
                // c'est un script du serveur
                require "scripts/" . $scriptName . ".php";
                $logs = date('d/m/Y H:i', time()) . ' ' . $websiteId . ' ' . $scriptName . PHP_EOL . $logs;
            } else {
                file_get_contents($cronData['link']) ;
                $logs = date('d/m/Y H:i', time()).' '.$websiteId.' '.$scriptName.' '.$cronData['link'].PHP_EOL.$logs;
            }
        }
    }


}
file_put_contents(__DIR__.'/logs.txt', $logs) ;


?>