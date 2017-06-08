<?php

/* getStudentInfo();*/
echo "<pre>";
/* echo file_get_contents("README.md");*/
echo "</pre>";
//getStudentInfo();
parseReadmeWithGitHub();

function parseReadmeWithGitHub(){
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL,"https://api.github.com/markdown");
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, 
                http_build_query(array('text' => json_encode('README.md'))));
    curl_setopt($ch, CURLOPT_USERAGENT, "tanwind");

    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type: application/json'
    ));

    // receive server response ...
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec ($ch);

    curl_close ($ch);

    // further processing ....
    if ($server_output == "OK") {  } else {  }

    echo urldecode($server_output);
}

function getStudentInfo(){

    $url = 'https://api.github.com/markdown/raw';
    $data = array("text" => "Hello world github/linguist#1 **cool**, and #1!",
                  "mode" => "gfm",
                  "context" => "github/gollum");

    // use key 'http' even if you send the request to https://...
    $options = array(
        'http' => array(
            'header'  => "Content-Type: text/plain\r\n",
            'method'  => 'POST',
            'content' => http_build_query($data)
        )
    );
    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) { /* Handle error */ }

    var_dump($result);


?>



<div id ="info-content" class="container">
</div>


<?php
}


?>
