</header>

<!-- 온라인 집들이 -->
<div class="container padding">
    <div class="d-between align-items-end mb-4 border-bottom">
        <div class="mb-3">
            <span class="text-muted">온라인 집들이</span>
            <div class="title">KNOWHOWS</div>
        </div>
        <button class="button-label" data-toggle="modal" data-target="#write-modal">
            글쓰기
            <i class="fa fa-pencil ml-3"></i>
        </button>
    </div>
    <div class="row">
        <?php foreach($knowhows as $knowhow):?>
        <div class="col-lg-4 col-md-6 mb-5">
            <div class="knowhow-item border">
                <div class="image">
                    <img src="/uploads/knowhows/<?=$knowhow->before_img?>" alt="Before 이미지" title="Before 이미지" class="fit-cover">
                    <img src="/uploads/knowhows/<?=$knowhow->after_img?>" alt="After 이미지" title="After 이미지" class="fit-cover">
                </div>
                <div class="py-3 px-4">
                    <div class="d-between">
                        <div>
                            <span><?=$knowhow->user_name?></span>
                            <small class="text-muted">(<?=$knowhow->user_id?>)</small>
                            <small class="text-muted ml-2"><?=date("Y-m-d", strtotime($knowhow->created_at))?></small>
                        </div>
                        <div class="text-gold score-value"><i class="fa fa-star<?=$knowhow->score == 0 ? '-o' : ''?> mr-1"></i><?=$knowhow->score?></div>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted fx-n2"><?=nl2br(htmlentities($knowhow->contents))?></p>
                    </div>
                    <?php if(!$knowhow->reviewed && user()->id !== $knowhow->uid):?>
                    <div class="score-label mt-3 d-between">
                        <small class="text-muted">이 글이 마음에 드시나요?</small>
                        <button class="fx-n3" data-id="<?=$knowhow->id?>" data-toggle="modal" data-target="#score-modal">평점 주기</button>
                    </div>
                    <?php endif;?>
                </div>
            </div>
        </div>
        <?php endforeach;?>
    </div>
</div>
<!-- /온라인 집들이 -->

<!-- 글쓰기 모달 -->
<form action="/knowhows" method="post" id="write-modal" class="modal fade" enctype="multipart/form-data">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body px-4 pt-4 pb-3">
                <div class="title text-center">
                    KNOWHOW 
                </div>
                <div class="mt-4">
                    <div class="form-group">
                        <label for="before_img">Before 사진</label>
                        <div class="custom-file">
                            <input type="file" id="before_img" class="custom-file-input" name="before_img" required>
                            <label for="before_img" class="custom-file-label">파일을 업로드 하세요</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="after_img">After 사진</label>
                        <div class="custom-file">
                            <input type="file" id="after_img" class="custom-file-input" name="after_img" required>
                            <label for="after_img" class="custom-file-label">파일을 업로드 하세요</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="contents">노하우</label>
                        <textarea name="contents" id="contents" cols="30" rows="10" class="form-control" placeholder="노하우을(를) 입력하세요" required></textarea>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="w-100 py-3">작성 완료</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- /글쓰기 모달 -->

<!-- 평점 모달 -->
<div id="score-modal" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body py-5">
                <div class="text-center text-muted">이 게시글의 평점을 매겨주세요!</div>
                <div class="mt-3 d-flex">
                    <button class="mx-3 border bg-white text-gold" data-value="1"><i class="fa fa-star"></i>1</button>
                    <button class="mx-3 border bg-white text-gold" data-value="2"><i class="fa fa-star"></i>2</button>
                    <button class="mx-3 border bg-white text-gold" data-value="3"><i class="fa fa-star"></i>3</button>
                    <button class="mx-3 border bg-white text-gold" data-value="4"><i class="fa fa-star"></i>4</button>
                    <button class="mx-3 border bg-white text-gold" data-value="5"><i class="fa fa-star"></i>5</button>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- 평점 모달 -->

<script>
    $(function(){
        let kid, score, $target;
        $("[data-target='#score-modal']").on("click", function(){
            kid = this.dataset.id;
            $target = $(this).closest(".knowhow-item");
        });

        $("#score-modal button").on("click", function(){
            score = this.dataset.value;
            $.post("/knowhows/reviews", {score, kid}, function(res){
                if(res.score){
                    $target.find(".score-value").html(`<i class="fa fa-star mr-1"></i>${res.score}`);
                    $target.find(".score-label").remove();
                    $("#score-modal").modal("hide");
                }
            });
        });
    });
</script>