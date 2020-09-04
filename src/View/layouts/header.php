<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>내집꾸미기</title>
    <script src="./resources/jquery-3.4.1.min.js"></script>
    <link rel="stylesheet" href="./resources/bootstrap-4.3.1-dist/css/bootstrap.min.css">
    <script src="./resources/bootstrap-4.3.1-dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="./resources/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="./resources/css/style.css">
    <script>
        $(function(){
            $(".custom-file-input").on("change", function(e){
                if(this.files.length > 0){
                    $(this).siblings(".custom-file-label").text(this.files[0].name);
                } else {
                    $(this).siblings(".custom-file-label").text("파일을 업로드 하세요");
                }
            });

            $("[data-target='#sign-up']").on("click", function(){
                let canvas = $("#cap_canvas")[0];
                let ctx = canvas.getContext("2d");
                ctx.clearRect(0, 0, 450, 100);
                ctx.font = "50px 나눔스퀘어, sans-serif";
                
                let text = Math.random().toString(36).substr(2, 5).split("").map(str => parseInt(Math.random() * 10) % 2 === 0 ? str.toUpperCase() : str).join("");
                let width = ctx.measureText(text).width;
                
                ctx.fillText(text, canvas.width / 2 - width / 2, canvas.height / 2 + 25);
                $("#cap_answer").val(text);
            });
        });
    </script>
</head>
<body>
    <!-- 로그인 -->
    <form action="/sign-in" method="post" id="sign-in" class="modal fade">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body px-4 pt-4 pb-3">
                    <div class="title text-center">
                        SIGN IN
                    </div>
                    <div class="mt-4">
                        <div class="form-group">
                            <label for="login_id">아이디</label>
                            <input type="text" id="login_id" class="form-control" name="user_id" placeholder="아이디을(를) 입력하세요" required>
                        </div>
                        <div class="form-group">
                            <label for="login_pw">비밀번호</label>
                            <input type="password" id="login_pw" class="form-control" name="password" placeholder="비밀번호을(를) 입력하세요" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="w-100 py-3">로그인</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- /로그인 -->
    <!-- 회원가입 -->
    <form action="/sign-up" method="post" id="sign-up" class="modal fade" enctype="multipart/form-data">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body px-4 pt-4 pb-3">
                    <div class="title text-center">
                        SIGN UP
                    </div>
                    <div class="mt-4">
                        <div class="form-group">
                            <label for="join_id">아이디</label>
                            <input type="text" id="join_id" class="form-control" name="user_id" placeholder="아이디을(를) 입력하세요" required>
                        </div>
                        <div class="form-group">
                            <label for="join_pw">비밀번호</label>
                            <input type="password" id="join_pw" class="form-control" name="password" placeholder="비밀번호을(를) 입력하세요" required>
                        </div>
                        <div class="form-group">
                            <label for="join_name">이름</label>
                            <input type="text" id="join_name" class="form-control" name="user_name" placeholder="이름을(를) 입력하세요" required>
                        </div>
                        <div class="form-group">
                            <label for="join_photo">사진</label>
                            <div class="custom-file">
                                <input type="file" id="join_photo" class="custom-file-input" name="photo" required>
                                <label for="join_photo" class="custom-file-label">파일을 업로드 하세요</label>
                            </div>
                        </div>
                        <div class="form-group">
                            <input type="hidden" id="cap_answer" name="cap_answer">
                            <label for="cap_input">자동입력방지 문자</label>
                            <canvas class="w-100 border" id="cap_canvas" width="450" height="100"></canvas>
                            <input type="text" id="cap_input" class="form-control" name="cap_input" placeholder="상단의 문자열을 입력하세요" required>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button class="w-100 py-3">가입 완료</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- /회원가입 -->

    <!-- 헤더 영역 -->
    <header <?=$viewName === "index" ? "" : "class='no-visual'"?>>
        <!-- GNB -->
        <div id="gnb">
            <div class="d-between align-items-lg-end px-4 py-3 h-100">
                <a href="/">
                    <img src="./resources/images/logo.svg" alt="내집꾸미기" title="내집꾸미기" height="60">
                </a>
                <div class="nav position-center mt-3 d-none d-lg-flex">
                    <a href="/">
                        <span class="d-block mb-2">
                            <i class="fa fa-2x fa-home"></i>
                        </span>
                        홈
                    </a>
                    <a href="/online-party">
                        <span class="d-block mb-2">
                            <i class="fa fa-2x fa-search"></i>
                        </span>
                        온라인 집들이
                    </a>
                    <a href="/store">
                        <span class="d-block mb-2">
                            <i class="fa fa-2x fa-shopping-cart"></i>
                        </span>
                        스토어
                    </a>
                    <a href="/experts">
                        <span class="d-block mb-2">
                            <i class="fa fa-2x fa-user-secret"></i>
                        </span>
                        전문가
                    </a>
                    <a href="/estimates">
                        <span class="d-block mb-2">
                            <i class="fa fa-2x fa-file-text"></i>
                        </span>
                        시공 견적
                    </a>
                </div>
                <div class="d-flex align-items-center">
                    <div class="auth d-none d-lg-flex">
                        <?php if(user()):?>
                            <span class="fx-n2 text-gold">&lt;<?=user()->user_name?>&gt;(&lt;<?=user()->user_id?>&gt;)</span>
                            <a href="/logout">로그아웃</a>
                        <?php else:?>
                            <a href="#" data-toggle="modal" data-target="#sign-in">로그인</a>
                            <a href="#" data-toggle="modal" data-target="#sign-up">회원가입</a>
                        <?php endif;?>
                    </div>
                    <div class="menu-icon d-lg-none">
                        <div></div>
                        <div></div>
                        <div></div>
                    </div>
                    <div class="menu d-lg-none">
                        <div class="inner">
                            <div class="m-nav">
                                <a href="/">홈</a>
                                <a href="/online-party">온라인 집들이</a>
                                <a href="/store">스토어</a>
                                <a href="/experts">전문가</a>
                                <a href="/estimates">시공 견적</a>
                            </div>
                            <div class="m-auth">
                                <?php if(user()):?>
                                    <span class="fx-n2 text-gold">&lt;<?=user()->user_name?>&gt;(&lt;<?=user()->user_id?>&gt;)</span>
                                    <a href="/logout">로그아웃</a>
                                <?php else:?>
                                    <a href="#" data-toggle="modal" data-target="#sign-in">로그인</a>
                                    <a href="#" data-toggle="modal" data-target="#sign-up">회원가입</a>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- /GNB -->