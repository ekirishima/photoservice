<?php 

    try {

        $url = $_REQUEST["url"];
        header("Content-type: application/json; charset=utf-8");

        $photo = array();
        if(isset($_REQUEST["photo"])) $photo = array("_method" => $_REQUEST["photo"]["_method"]);
        if(isset($_REQUEST["photo"]["name"])) $photo["name"] = $_REQUEST["photo"]["name"];
        if(isset($_REQUEST["photo"]["photo"])) $photo["photo"] = $_REQUEST["photo"]["photo"];

        $ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url."/api/photo/".$_REQUEST["id"]);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer '.$_REQUEST["header"])); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $photo);
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