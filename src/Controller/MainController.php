<?php
namespace Controller;

use App\DB;

class MainController {
    function indexPage(){
        view("index");
    }
    function storePage(){
        view("store");
    }

    // 온라인 집들이
    function partyPage(){
        $knowhows = DB::fetchAll("SELECT DISTINCT K.*, user_name, user_id, IFNULL(score, 0) score, M.kid reviewed
                                FROM knowhows K
                                LEFT JOIN users U ON U.id = K.uid
                                LEFT JOIN (SELECT FLOOR(AVG(score)) score, kid FROM knowhow_reviews GROUP BY kid) R ON R.kid = K.id
                                LEFT JOIN (SELECT kid FROM knowhow_reviews WHERE uid = ?) M ON M.kid = K.id
                                ORDER BY K.id", [user()->id]);
        view("party", ["knowhows" => $knowhows]);
    }
    
    function writeKnowhow(){
        checkInput();
        extract($_POST);
        extract($_FILES);

        $before_name = "before_".time().extname($before_img);
        $after_name = "after_".time().extname($after_img);

        move_uploaded_file($before_img['tmp_name'], _UPLOADS."/knowhows/$before_name");
        move_uploaded_file($after_img['tmp_name'], _UPLOADS."/knowhows/$after_name");

        DB::query("INSERT INTO knowhows(uid, contents, before_img, after_img) VALUES (?, ?, ?, ?)", [user()->id, $contents, $before_name, $after_name]);

        go("/online-party", "새로운 글이 작성되었습니다.");
    }

    function reviewKnowhow(){
        extract($_POST);

        DB::query("INSERT INTO knowhow_reviews(uid, kid, score) VALUES (?, ?, ?)", [user()->id, $kid, $score]);
        $data = DB::fetch("SELECT FLOOR(AVG(score)) score FROM knowhow_reviews WHERE kid = ? GROUP BY kid", [$kid]);

        json_response(["score" => $data->score]);
    }

    // 시공 견적
    function estimatePage(){
        $reqList = DB::fetchAll("SELECT DISTINCT Q.*, user_name, user_id, IFNULL(cnt, 0) cnt, M.qid sended
                                FROM requests Q
                                LEFT JOIN users U ON U.id = Q.uid
                                LEFT JOIN (SELECT COUNT(*) cnt, qid FROM responses GROUP BY qid) S ON S.qid = Q.id
                                LEFT JOIN (SELECT qid FROM responses WHERE uid = ?) M ON M.qid = Q.id
                                ORDER BY Q.id", [user()->id]);

        $resList = DB::fetchAll("SELECT DISTINCT S.*, user_name, user_id, contents, start_date, sid
                                FROM responses S
                                LEFT JOIN requests Q ON Q.id = S.qid
                                LEFT JOIN users U ON U.id = Q.uid
                                WHERE S.uid = ? ORDER BY S.id", [user()->id]);
        view("estimate", ["reqList" => $reqList, "resList" => $resList]);
    }

    function writeRequest(){
        checkInput();
        extract($_POST);
        
        DB::query("INSERT INTO requests(uid, start_date, contents) VALUES (?, ?, ?)", [user()->id, $start_date, $contents]);
        go("/estimates", "견적을 요청했습니다.");
    }

    function writeResponse(){
        checkInput();
        extract($_POST);

        DB::query("INSERT INTO responses(uid, qid, price) VALUES (?, ?, ?)", [user()->id, $qid, $price]);
        go("/estimates", "견적을 보냈습니다.");
    }

    function getResponses(){
        if(isset($_GET['id'])){
            $req = DB::find("requests", $_GET['id']);
            $list = DB::fetchAll("SELECT S.*, user_name, user_id FROM responses S LEFT JOIN users U ON U.id = S.uid WHERE S.qid = ?", [$req->id]);
            json_response(["req" => $req, "list" => $list]);
        }
    }

    function pickEstimate(){
        checkInput();
        extract($_POST);

        DB::query("UPDATE requests SET sid = ? WHERE id = ?", [$sid, $qid]);
        go("/estimates", "선택되었습니다.");
    }
}