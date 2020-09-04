<?php
function user(){
    return isset($_SESSION['user']) ? $_SESSION['user'] : false;
}

function go($url, $message){
    echo "<script>";
    echo "alert('$message');";
    echo "location.href='$url';";
    echo "</script>";
    exit;
}

function back($message){
    echo "<script>";
    echo "alert('$message');";
    echo "history.back();";
    echo "</script>";
    exit;
}

function view($viewName, $data = []){
    extract($data);
    $filePath = _VIEW."/$viewName.php";
    
    if(is_file($filePath)){
        require _VIEW."/layouts/header.php";
        require $filePath;
        require _VIEW."/layouts/footer.php";
    }
}

function json_response($data){
    header("Content-Type: application/json");
    echo json_encode($data);
}

function checkInput(){
    foreach($_POST as $input){
        if(trim($input) === "") back("모든 정보를 입력해 주세요.");
    }
    foreach($_FILES as $file){
        if(!is_file($file['tmp_name'])) back("모든 파일을 업로드해 주세요.");
    }
}

function extname($file){
    return substr($file['name'], strrpos($file['name'], "."));
}