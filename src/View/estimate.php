</header>

<div class="container padding">
    <div class="sticky-top pt-4 bg-white">
        <div class="d-between">
            <div>
                <span class="text-muted">시공 견적 요청</span>
                <div class="title">REQUESTS</div>
            </div>
            <button class="button-label" data-target="#request-modal" data-toggle="modal">
                견적 요청
                <i class="fa fa-angle-right ml-3"></i>
            </button>
        </div>
        <div class="table-head mt-3">
            <div class="cell-10">상태</div>
            <div class="cell-40">내용</div>
            <div class="cell-15">요청자</div>
            <div class="cell-15">시공일</div>
            <div class="cell-10">견적 개수</div>
            <div class="cell-10">+</div>
        </div>
    </div>
    <div class="list">
        <?php foreach($reqList as $req):?>
            <div class="table-item">
                <div class="cell-10">
                    <?php if(!$req->sid):?>
                        <span class="rounded-pill bg-gold text-white fx-n3 py-2 px-3">진행 중</span>
                    <?php else: ?>
                        <span class="rounded-pill bg-gold text-white fx-n3 py-2 px-3">완료</span>
                    <?php endif;?>
                </div>
                <div class="cell-40">
                    <p class="text-muted fx-n2"><?=nl2br(htmlentities($req->contents))?></p>
                </div>
                <div class="cell-15">
                    <span><?=$req->user_name?></span>
                    <small class="text-muted">(<?=$req->user_id?>)</small>
                </div>
                <div class="cell-15">
                    <span><?=$req->start_date?></span>
                </div>
                <div class="cell-10">
                    <span><?=$req->cnt?></span>
                </div>
                <div class="cell-10">
                    <?php if($req->uid === user()->id):?>
                        <button data-toggle="modal" data-target="#view-modal" data-id="<?=$req->id?>" class="fx-n3 p-2">견적 보기</button>
                    <?php elseif(!$req->sended && !$req->sid && user()->auth):?>
                        <button data-toggle="modal" data-target="#response-modal" data-id="<?=$req->id?>" class="fx-n3 p-2">견적 보내기</button>
                    <?php else :?>
                        -
                    <?php endif;?>
                </div>
            </div>
        <?php endforeach;?>
    </div>
</div>


<?php if(user()->auth):?>
    <div class="bg-gray">
        <div class="container padding">
            <div class="sticky-top pt-4 bg-gray">
                <div>
                    <span class="text-muted">보낸 견적</span>
                    <div class="title">RESPONSES</div>
                </div>
                <div class="table-head mt-3">
                    <div class="cell-10">선택 여부</div>
                    <div class="cell-40">내용</div>
                    <div class="cell-15">요청자</div>
                    <div class="cell-15">시공일</div>
                    <div class="cell-10">입력한 비용</div>
                    <div class="cell-10">+</div>
                </div>
            </div>
            <div class="list">
                <?php foreach($resList as $res):?>
                    <div class="table-item">
                        <div class="cell-10">
                            <?php if(!$res->sid):?>
                                <span class="rounded-pill bg-gold text-white fx-n3 py-2 px-3">진행 중</span>
                            <?php elseif($res->sid == $res->id): ?>
                                <span class="rounded-pill bg-gold text-white fx-n3 py-2 px-3">선택</span>
                            <?php else :?>
                                <span class="rounded-pill bg-gold text-white fx-n3 py-2 px-3">미선택</span>
                            <?php endif;?>
                        </div>
                        <div class="cell-40">
                            <p class="text-muted fx-n2"><?=nl2br(htmlentities($res->contents))?></p>
                        </div>
                        <div class="cell-15">
                            <span><?=$res->user_name?></span>
                            <small class="text-muted">(<?=$res->user_id?>)</small>
                        </div>
                        <div class="cell-15">
                            <span><?=$res->start_date?></span>
                        </div>
                        <div class="cell-10">
                            <span><?=$res->price?></span>
                            <small class="text-muted">원</small>
                        </div>
                        <div class="cell-10">
                            -
                        </div>
                    </div>
                <?php endforeach;?>
            </div>
        </div>
    </div>
<?php endif;?>

<!-- 견적 요청 -->
<form action="/requests" id="request-modal" class="modal fade" method="post">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body px-4 pt-4 pb-3">
                <div class="title text-center">
                    REQUEST
                </div>
                <div class="mt-4">
                    <div class="form-group">
                        <label for="start_date">시공일</label>
                        <input type="date" id="start_date" class="form-control" name="start_date" required>
                    </div>
                    <div class="form-group">
                        <label for="contents">내용</label>
                        <textarea name="contents" id="contents" cols="30" rows="10" class="form-control" placeholder="내용을(를) 입력하세요" required></textarea>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="w-100 py-3">작성 완료</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- /견적 요청 -->

<!-- 견적 보내기 -->
<form action="/responses" id="response-modal" class="modal fade" method="post">
    <input type="hidden" id="qid" name="qid">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body px-4 pt-4 pb-3">
                <div class="title text-center">
                    RESPONSE
                </div>
                <div class="mt-4">
                    <div class="form-group">
                        <label for="price">비용</label>
                        <input type="number" id="price" class="form-control" min="0" value="10000" name="price" required>
                    </div>
                </div>
                <div class="mt-3">
                    <button class="w-100 py-3">입력 완료</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- /견적 보내기 -->

<!-- 견적 보기 -->
<form action="/estimates/pick" id="view-modal" class="modal fade" method="post">
    <input type="hidden" id="pick_qid" name="qid">
    <input type="hidden" id="pick_sid" name="sid">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body px-4 pt-4 pb-3">
                <div class="title text-center">
                    ESTIMATES
                </div>
                <div class="table-head mt-3">
                    <div class="cell-30">전문가 정보</div>
                    <div class="cell-40">비용</div>
                    <div class="cell-30">+</div>
                </div>
                <div class="list">
                    
                </div>
            </div>
        </div>
    </div>
</form>
<!-- /견적 보기 -->

<script>
    $(function(){
        $("[data-target='#response-modal']").on("click", function(){
            $("#qid").val(this.dataset.id);
        });


        $("[data-target='#view-modal']").on("click", function(){
            $("#pick_qid").val(this.dataset.id);

            $.getJSON("/responses?id="+this.dataset.id, res => {
                if(res.req && res.list){
                    $("#view-modal .list").html("");
                    res.list.forEach(item => {
                        $("#view-modal .list").append(`<div class="table-item">
                                                            <div class="cell-30">
                                                                <span>${item.user_name}</span>
                                                                <small class="text-muted">(${item.user_id})</small>
                                                            </div>
                                                            <div class="cell-40">
                                                                <span>${parseInt(item.price).toLocaleString()}</span>
                                                                <small class="text-muted">원</small>
                                                            </div>
                                                            <div class="cell-30">
                                                                ${res.req.sid ? '' : `<button class="fx-n3" data-id="${item.id}">선택</button>`}
                                                            </div>
                                                        </div>`);
                    });
                    if(res.list.length == 0) $("#view-modal .list").html(`<div class="w-100 py-4 text-muted text-center">받은 견적이 없습니다.</div>`);
                }
            });
        });

        $("#view-modal .list").on("click", "button", function(){
            $("#pick_sid").val(this.dataset.id);
        });
    });
</script>