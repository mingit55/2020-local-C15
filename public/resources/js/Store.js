class Store {
    constructor(){
        this.cartList = [];
        this.keyword = "";
        this.$storeList = $("#store-list");
        this.$cartList = $("#cart-list");
        this.$dropArea = $("#drop-area");

        this.init();
        this.setEvents();
    }

    async init(){
        this.products = await this.getProducts();
        this.storeUpdate();
    }

    get totalPrice(){
        return this.cartList.reduce((p, c) => p + c.totalPrice, 0);
    }

    // JSON 가져오기
    getProducts(){
        return fetch("/resources/store.json")
            .then(res => res.json())
            .then(jsonList => jsonList.map(json => new Product(this, json)));
    }

    // 스토어 갱신
    storeUpdate(){
        let viewList = this.products.map(item => item.init());
        
        if(this.keyword !== ""){
            let regex = new RegExp(this.keyword, "g");           
            viewList = viewList.filter(item => regex.test(item.json.product_name) || regex.test(item.json.brand))
                .map(item => {
                    item.product_name = item.json.product_name.replace(regex, m1 => `<span class="bg-gold text-white">${m1}</span>`);
                    item.brand = item.json.brand.replace(regex, m1 => `<span class="bg-gold text-white">${m1}</span>`);
                    return item;
                })
        }
        
        this.$storeList.html("");
        viewList.forEach(item => {
            item.storeUpdate();
            this.$storeList.append(item.$storeElem);
        });

        if(viewList.length === 0) this.$storeList.html(`<div class="w-100 py-4 text-center text-muted fx-n2">일치하는 상품이 없습니다.</div>`);
    }

    // 장바구니 갱신
    cartUpdate(){
        this.$cartList.html("");

        this.cartList.forEach(item => {
            item.cartUpdate();
            this.$cartList.append(item.$cartElem);
        });

        if(this.cartList.length === 0) this.$cartList.html(`<div class="w-100 py-4 text-center text-muted fx-n2">장바구니에 담긴 상품이 없습니다.</div>`);

        $(".total-price").text(this.totalPrice.toLocaleString());
    }

    setEvents(){
        // 상품 추가

        let dragTarget, startPoint, timeout;
        this.$storeList.on("dragstart", ".image", e => {
            e.preventDefault();

            dragTarget = e.currentTarget;
            startPoint = [e.pageX, e.pageY];
            
            $(dragTarget).css({
                position: "relative",
                zIndex: "2000",
                transition: "none"
            });
        });

        $(window).on("mousemove", e => {
            if(!dragTarget || !startPoint || e.which !== 1) return;

            $(dragTarget).css({
                left: e.pageX - startPoint[0] + "px",
                top: e.pageY - startPoint[1] + "px",
            });
        });

        $(window).on("mouseup", e => {
            if(!dragTarget || !startPoint || e.which !== 1) return;

            let {left, top} = this.$dropArea.offset();
            let width = this.$dropArea.width();
            let height = this.$dropArea.height();

            if(left <= e.pageX && e.pageX <= left + width && top <= e.pageY && e.pageY <= top + height){
                if(timeout){
                    clearTimeout(timeout);
                }
                this.$dropArea.removeClass("success");
                this.$dropArea.removeClass("error");


                let product = this.products.find(item => item.id == dragTarget.dataset.id);
                if(this.cartList.some(item => item == product)){
                    this.$dropArea.addClass("error");

                    $(dragTarget).animate({
                        left: 0,
                        top: 0,
                    }, 350, function(){
                        this.style.zIndex = 0;
                    });
                } else {
                    this.$dropArea.addClass("success");
                     
                    product.buyCount = 1;
                    this.cartList.push(product);
                    this.cartUpdate();

                    let target = dragTarget;

                    $(target).css({
                        transform: "scale(0)",
                        transition: "transform 0.35s",
                    });

                    setTimeout(() => {
                        $(target).css({
                            transform: "scale(1)",
                            left: 0,
                            top: 0,
                            zIndex: 0,
                        });
                    }, 350);
                }

                timeout = setTimeout(() => {
                    this.$dropArea.removeClass("success");
                    this.$dropArea.removeClass("error");
                }, 1500);

            } else {
                $(dragTarget).animate({
                    left: 0,
                    top: 0,
                }, 350, function(){
                    this.style.zIndex = 0;
                });
            }
            
            dragTarget = startPoint = null;
        });


        // 수량 수정
        this.$cartList.on("input", ".buy-count", e => {
            let value = parseInt(e.target.value);
            if(isNaN(value) || !value || value < 1){
                value = 1;
            }

            let product = this.cartList.find(item => item.id == e.target.dataset.id);
            product.buyCount = value;

            this.cartUpdate();
            e.target.focus();
        });

        // 장바구니 삭제
        this.$cartList.on("click", ".remove", e => {
            let idx = this.cartList.findIndex(item => item.id == e.currentTarget.dataset.id);
            if(idx >= 0 ){
                let product = this.cartList[idx];
                product.buyCount = 0;

                this.cartList.splice(idx, 1);
                this.cartUpdate();
            }
        });

        // 구매하기
        $("#buy-modal").on("submit", e => {
            e.preventDefault();
            
            const PADDING = 30;
            const TEXT_SIZE = 18;
            const TEXT_YGAP = 20;
            const TEXT_XGAP = 50;

            let canvas = document.createElement("canvas");
            let ctx = canvas.getContext("2d");
            ctx.font = `${TEXT_SIZE}px 나눔스퀘어, sans-serif`;

            let now = new Date();
            let text_time = `구매일시: ${now.getFullYear()}-${now.getMonth() + 1}-${now.getDate()} ${now.getHours()}:${now.getMinutes()}:${now.getSeconds()}`;
            let text_price = `총합계: ${this.totalPrice.toLocaleString()}원`;

            let viewList = [
                ["상품명", "가격", "수량", "합계"],
                ...this.cartList.map(item => [item.json.product_name, item.price.toLocaleString() + "원", item.buyCount.toLocaleString() + "개", item.totalPrice.toLocaleString() + "원"]),
            ];
            let widthList = viewList.map(row => row.map(text => ctx.measureText(text).width + TEXT_XGAP))
                .reduce((p, c) => c.map((width, i) => Math.max(width, p[i])));
            
            canvas.width = PADDING * 2 + widthList.reduce((p, c) => p + c);
            canvas.height = PADDING * 2 + (TEXT_SIZE + TEXT_YGAP) * (viewList.length + 2);

            ctx.fillStyle = "#fff";
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            
            ctx.fillStyle = "#ddd";
            ctx.fillRect(PADDING, PADDING + TEXT_SIZE + TEXT_YGAP / 2, canvas.width - PADDING * 2, 2);

            ctx.fillStyle = "#333";
            ctx.font = `${TEXT_SIZE}px 나눔스퀘어, sans-serif`;

            viewList.forEach((row, y) => {
                let acc = 0;
                row.forEach((text, i) => {
                    let text_x = PADDING + acc + widthList[i] / 2 - ctx.measureText(text).width / 2;
                    let text_y = PADDING + TEXT_SIZE * (y + 1) + TEXT_YGAP * y;
                    acc += widthList[i];
                    ctx.fillText(text, text_x, text_y);
                });
            });

            ctx.fillText(text_price, PADDING, canvas.height - PADDING - TEXT_SIZE - TEXT_YGAP);
            ctx.fillText(text_time, PADDING, canvas.height - PADDING);
            
            let src = canvas.toDataURL("image/jpeg");
            $("#view-modal img").attr("src", src);
            $("#view-modal").modal("show");
            $("#buy-modal").modal("hide");
            $("#buy-modal input").val("");

            this.cartList.forEach(item => item.buyCount = 0);
            this.cartList = [];
            this.cartUpdate();
        });

        $(".search input").on("input" , e => {
            this.keyword = e.target.value
                .replace(/([\^$\.+*?\[\]\(\)\\\\\\/])/g, "\\$1")
                .replace(/(ㄱ)/g, "[가-깋]")
                .replace(/(ㄴ)/g, "[나-닣]")
                .replace(/(ㄷ)/g, "[다-딯]")
                .replace(/(ㄹ)/g, "[라-맇]")
                .replace(/(ㅁ)/g, "[마-밓]")
                .replace(/(ㅂ)/g, "[바-빟]")
                .replace(/(ㅅ)/g, "[사-싷]")
                .replace(/(ㅇ)/g, "[아-잏]")
                .replace(/(ㅈ)/g, "[자-짛]")
                .replace(/(ㅊ)/g, "[차-칳]")
                .replace(/(ㅋ)/g, "[카-킿]")
                .replace(/(ㅌ)/g, "[타-팋]")
                .replace(/(ㅍ)/g, "[파-핗]")
                .replace(/(ㅎ)/g, "[하-힣]");
            this.storeUpdate();
        });
    }
}

window.onload = function(){
    window.store = new Store();
};