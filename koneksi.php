<?php
    $server = "localhost";
    $user = "root";
    $password = "";
    $db_name = "perpustakaan_db";
    $port = 3306; // Tentukan port di sini

    // Tambahkan variabel port sebagai parameter kelima
    $db = mysqli_connect($server, $user, $password, $db_name, $port);

    if(!$db){
        die("Gagal Koneksi boss: ". mysqli_connect_error());
    } else {
        // Baris ini bisa ditambahkan untuk memastikan koneksi berhasil
        // echo "Koneksi ke port 3307 berhasil!";
    }

    $baseurl = "http://localhost:8080/native/sampelperpustakaan/";



?>