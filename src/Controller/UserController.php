<?php
namespace Controller;

use App\DB;

class UserController {
    function signIn(){
        checkInput();
        extract($_POST);

        $user = DB::who($user_id);
        if(!$user || $user->password !== hash('sha256', $password)) back("아이디 또는 비밀번호가 일치하지 않습니다.");
        $_SESSION['user'] = $user;

        go("/", "로그인 되었습니다.");
    }
    function signUp(){
        checkInput();
        extract($_POST);

        $user = DB::who($user_id);
        if($user) back("중복되는 아이디입니다. 다른 아이디를 사용해주세요.");
        if($cap_input !== $cap_answer) back("자동입력방지 문자를 잘못 입력하였습니다.");

        $photo = $_FILES['photo'];
        $filename = time() . extname($photo);
        move_uploaded_file($photo['tmp_name'], _UPLOADS."/users/$filename");
        
        DB::query("INSERT INTO users(user_id, password, user_name, photo) VALUES (?, ?, ?, ?)", [$user_id, hash("sha256", $password), $user_name, $filename]);
        go("/", "회원가입 되었습니다.");
    }
    function logout(){
        unset($_SESSION['user']);
        go("/", "로그아웃 되었습니다.");
    }

    // 전문가 페이지
    function expertPage(){
        view("expert", [
            "experts" => DB::fetchAll("SELECT DISTINCT U.*, IFNULL(score, 0) score
                                        FROM users U
                                        LEFT JOIN (SELECT FLOOR(AVG(score)) score, eid FROM expert_reviews GROUP BY eid) R ON R.eid = U.id
                                        WHERE U.auth = 1 ORDER BY U.id"),
            "reviews" => DB::fetchAll("SELECT DISTINCT R.*, U.user_name, U.user_id, E.user_name e_name, E.user_id e_id
                                        FROM expert_reviews R
                                        LEFT JOIN users U ON U.id = R.uid
                                        LEFT JOIN users E ON E.id = R.eid
                                        ORDER BY R.id")
        ]);
    }

    function reviewExpert(){
        checkInput();
        extract($_POST);

        DB::query("INSERT INTO expert_reviews(uid, eid, score, price, contents) VALUES (?, ?, ?, ?, ?)", [user()->id, $eid, $score, $price, $contents]);
        go("/experts", "후기가 작성되었습니다.");
    }
}