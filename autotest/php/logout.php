<?php 

try {

    $url = $_REQUEST["url"];
    header("Content-type: application/json; charset=utf-8");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url."/api/logout");
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$_REQUEST["header"])); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    
    echo json_encode(array("code" => $httpCode));

    curl_close($ch);

} catch (\Throwable $th) {
    throw $th;
}