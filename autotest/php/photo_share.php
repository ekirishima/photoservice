<?php 

try {

    $url = $_REQUEST["url"];
    header("Content-type: application/json; charset=utf-8");
    $string = "params=null";
    foreach($_REQUEST["photos"] as $photo) $string .= "&photos[]=$photo";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url."/api/user/".$_REQUEST["id"]."/share");
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$_REQUEST["header"])); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $string);
    $data = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
    $header = substr($data, 0, $header_size);
    $body = substr($data, $header_size);

    echo json_encode(array("code" => $httpCode, "response" => json_decode($body)));

    curl_close($ch);

} catch (\Throwable $th) {
    throw $th;
}