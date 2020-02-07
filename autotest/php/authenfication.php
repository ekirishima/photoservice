<?php 

    try {

        header("Content-type: application/json; charset=utf-8");

        function c_send($name, $url, $post = false, $fields = false) {

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $_REQUEST["url"].$url);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            if($post) curl_setopt($ch, CURLOPT_POST, true);
            if($fields) curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
            $data = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            // Get Data
            $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
            $header = substr($data, 0, $header_size);
            $body = substr($data, $header_size);

            // Success
            $success = true;
            if($httpCode != 403) {
                $result = "danger";
                $success = false;
            }

            // Send JSON data
            return array("name" => $name, "success" => $success, "code" => $httpCode, "response" => json_decode($body));
        };

        echo json_encode(array(
            "logout" => c_send("Выход", "/api/logout", true),
            "photo.upload" => c_send("Загрузка фотографии", "/api/photo", true),
            "photo.edit" => c_send("Изменение фотографии", "/api/photo/1", true, array("_method" => "patch")),
            "photos.get" => c_send("Получение фотографий", "/api/photo", false),
            "photo.get" => c_send("Получение одной фотографии", "/api/photo/1", false),
            "photo.delete" => c_send("Удаление фотографии", "/api/photo/1", true, array("_method" => "delete")),
            "photo.share" => c_send("Шаринг фотографий", "/api/user/1/share", true),
            "user.search" => c_send("Поиск пользователей", "/api/user", false)
        ));

    } catch (\Throwable $th) {
        throw $th;
    }