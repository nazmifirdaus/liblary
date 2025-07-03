<?php
    include'../koneksi.php';
    $idx = isset($_GET['id']) ? $_GET['id'] : "";
    if(!$idx == ""){
        $dml = "DELETE FROM member WHERE member_id = '$idx'";
        mysqli_query($db, $dml);

        header("location:../admin.php?p=listmember");
    }else{
        echo "Paramteter id ga ada bro";
    }
?>