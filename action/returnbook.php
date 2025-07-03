<?php
    include'../koneksi.php';
    $id = isset($_GET['id']) ? $_GET['id'] : "";
    $idx = isset($_GET['idx']) ? $_GET['idx'] : "";
    if(!$id == ""){
        $dml = "UPDATE borrowdetails 
                SET borrow_status = '0', date_return = now() WHERE borrow_details_id = '$id'";
        mysqli_query($db, $dml);

        header("location:../index.php?p=editborrow&id=".$idx);
    }else{
        echo "Paramteter id ga ada bro";
    }
?>