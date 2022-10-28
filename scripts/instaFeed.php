<?php

// script de récupération d'un feed instagram avec Rapid API

$curl = curl_init();

$rapidApiHost[1] = [

    "url" => "https://instagram-data1.p.rapidapi.com/user/feed?username=".$cronData['instagramUserName'],
    "header" => [
        "x-rapidapi-host: instagram-data1.p.rapidapi.com",
        "x-rapidapi-key: f81b69ce42msh9cac1569d139610p1fefe7jsnd0c5141ba97d"
    ]
] ;
$rapidApiHost[2] = [

    "url"=> "https://instagram39.p.rapidapi.com/getFeed?user_id=".$cronData['instagramId'],
    "header" => [
        "x-rapidapi-host: instagram39.p.rapidapi.com",
        "x-rapidapi-key: f81b69ce42msh9cac1569d139610p1fefe7jsnd0c5141ba97d"
    ]
] ;

$pickProvider = 2;

curl_setopt_array($curl, [
    CURLOPT_URL => $rapidApiHost[$pickProvider]['url'],
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => $rapidApiHost[$pickProvider]['header']
]);


$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    echo $response;
}

$json = json_decode($response, true);


$savedInstagramFeed = [] ;

if ($pickProvider == 1) {
    foreach ($json['collector'] as $post) {
        $savedInstagramFeed[$post["shortcode"]]['shortcode'] = $post["shortcode"];
        $savedInstagramFeed[$post["shortcode"]]['mediaSrc'] = $post["thumbnail_src"];
        $savedInstagramFeed[$post["shortcode"]]['displayText'] = $post["description"];
        $imgBlob = file_get_contents($post["thumbnail_src"]);
        file_put_contents("uploads/" . $post["shortcode"] . '.jpg', $imgBlob);
    }
} else {
    foreach ($json['data']['edges'] as $post) {
        $post = $post['node'] ;
        $savedInstagramFeed[$post["shortcode"]]['shortcode'] = $post["shortcode"];
        $savedInstagramFeed[$post["shortcode"]]['mediaSrc'] = $post["thumbnail_src"];
        $savedInstagramFeed[$post["shortcode"]]['displayText'] = $post["edge_media_to_caption"]['edges'][0]['node']['text'];
        $imgBlob = file_get_contents($post["thumbnail_src"]);
        file_put_contents("uploads/" . $post["shortcode"] . '.jpg', $imgBlob);
    }
}

if (!empty($savedInstagramFeed)){
    file_put_contents("data/".$websiteId."-instagram-feed.json", json_encode($savedInstagramFeed, JSON_PRETTY_PRINT)) ;
} else {
    /* Notifications mail ? logs ?
    Il y a eu un soucis avec le script instagram*/
}


?>