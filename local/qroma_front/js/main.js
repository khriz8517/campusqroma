var app = new Vue({
    el: '#app',
    data(){
        return{
            menu: false,
            baner: [],
            banerPoint: 1,
            marginLeftBaner: 0,
            card: [],
            cardPonit: 1,
            marginLeftCard: 0,
            marcas: [
                {url: './local/qroma_front/img/logos/american-colors.png'},
                {url: './local/qroma_front/img/logos/cpp.png'},
                {url: './local/qroma_front/img/logos/jet.png'},
                {url: './local/qroma_front/img/logos/tekno.png'},
                {url: './local/qroma_front/img/logos/vencedor.png'},
                {url: './local/qroma_front/img/logos/fast.png'}
            ]
        };
    },
    created(){
        this.sizeWeb();
        window.onresize = this.sizeWeb;
    },
    mounted(){
        this.banerFormat();
        //this.banerMov();
        //this.testimonialMov();
        this.loadData();
        setTimeout(() => {
            var x = window.matchMedia("(max-width: 720px)")
            this.myFunction(x) // Call listener function at run time
            x.addListener(this.myFunction)
            // this.marcasMov();
        }, 300);
    },
    methods: {
        loadSlider: function() {
            let frm = new FormData();
            frm.append('ferreterias',0);
            frm.append('request_type','obtenerSlider');
            axios.post('local/qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    // handle success
                    let data = response.data.data;
                    let slides = Array();
                    let btnStyle = ['success','info','danger'];

                    Object.keys(data).forEach(key => {
                        let dataVal = data[key];
                        let title = dataVal.title;
                        let btnText = dataVal.btnText;
                        let background = dataVal.background;
                        let url = dataVal.url;

                        let newElem = {
                            'title': title,
                            'btn': btnText,
                            'btnStyle': btnStyle[key],
                            'background': background,
                            'url': url
                        };
                        slides.push(newElem);
                    });

                    this.baner = slides;
                });
        },
        loadTestimonios: function() {
            let frm = new FormData();
            frm.append('request_type','obtenerTestimonios');
            axios.post('local/qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    // handle success
                    let data = response.data.data;
                    let testimonios = Array();
                    let types = ['info','success'];

                    Object.keys(data).forEach(key => {
                        let count = parseInt(key)+1;
                        let dataVal = data[key];
                        let name = dataVal.name;
                        let content = dataVal.content;
                        let url = dataVal.url;

                        let newElem = {'title': name, 'content': content, 'url': url, 'type': types[key]};
                        testimonios.push(newElem);
                    });

                    this.card = testimonios;
                });
        },

        loadData: function(){
           this.loadSlider();
           this.loadTestimonios();
        },
        myFunction: function (x) {
            if (x.matches) { // If media query matches
                this.marcasMov();
            }
        },
        menuBtn: function(){
            if(this.menu == false){
                this.menu = true;
            } else{
                this.menu = false;
            }
        },
        sizeWeb: function(){
            if (window.innerWidth < 768)
                this.menu = false;
            else
                this.menu = true;
            this.banerFormat();
        },
        banerFormat: function(){
            let frm = new FormData();
            let banerNum = 0;
            let data = {};
            frm.append('ferreterias',0);
            frm.append('request_type','obtenerSlider');
            axios.post('local/qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    data = response.data.data;
                    banerNum = data.length;

                    let banerWidth  = $(".baner").width();
                    let newWidth    = banerWidth*banerNum;

                    $("#baner").width(`${newWidth}px`);
                    $("#baner").height(`100%`);
                    $("#baner").css({"display":"flex","position":"relative"});
                    this.banerWidth = banerWidth;

                    // let cardNum    = this.card.length;
                    // let cardWidth  = $(".testimonials").width()/2;
                    // let newWidthCard    = cardWidth*cardNum;
                    //
                    // $(".card01").width(`${newWidthCard}px`);
                    // $(".card01").height(`100%`);
                    // $(".card01").css({"display":"flex","position":"relative"});
                    // this.cardWidth = cardWidth;
                });
        },
        banerMov: function() {
            setInterval(() => {
                this.banerPoint = this.banerPoint + 1;
                let marginLeft = (100 * this.baner.length - 100)*-1;
                this.marginLeftBaner -= 100;
                if(marginLeft > this.marginLeftBaner){
                    this.marginLeftBaner = 0;
                }
                $('#baner').animate({'margin-left': this.marginLeftBaner+"%"}, 500);
                if(this.banerPoint > this.baner.length){
                    this.banerPoint = 1;
                }
            }, 10000);
        },
        prevBaner: function(){
            let marginLeft = 0;
            this.marginLeftBaner += 100;
            if(marginLeft < this.marginLeftBaner) {
                this.marginLeftBaner = (100 * this.baner.length - 100)*-1;
            }
            if(this.banerPoint == 1) {
                this.banerPoint = this.baner.length;
            } else {
                this.banerPoint -= 1;
            }
            $('#baner').animate({'margin-left': this.marginLeftBaner+"%"}, 500);
        },
        nextBaner: function(){
            let marginLeft = (100 * this.baner.length - 100)*-1;
            this.marginLeftBaner -= 100;
            if(marginLeft > this.marginLeftBaner) {
                this.marginLeftBaner = 0;
            }
            if(this.banerPoint <  this.baner.length) {
                this.banerPoint += 1;
            } else if(this.banerPoint ==  this.baner.length) {
                this.banerPoint = 1;
            }
            $('#baner').animate({'margin-left': this.marginLeftBaner+"%"}, 500);
        },
        changeBaner: function(index){
            this.banerPoint = index + 1;
            if(index == 0) {
                this.marginLeftBaner = -100 * index;
                $('#baner').animate({'margin-left': this.marginLeftBaner+"%"}, 500);
            } else {
                let moveSign = 1;
                if(this.banerPoint > index+1) {
                    moveSign = -1;
                }
                this.marginLeftBaner = -100 * index * moveSign;
                $('#baner').animate({'margin-left': this.marginLeftBaner+"%"}, 500);
            }
        },
        testimonialMov:function(){
            setInterval(() => {
                $('.card01').animate({'margin-left': this.marginLeftCard+"%"}, 500);
                let marginLeft = 0;
                this.marginLeftCard += 100;
                if(marginLeft > this.marginLeftCard){
                    this.marginLeftCard = (100 * this.baner.length - 100)*-1;
                }
                this.cardPonit = this.cardPonit + 1;
                if(this.cardPoint > this.card.length){
                    this.cardPoint = 1;
                }
            }, 10000);
        },
        prevTestimonial: function(){
            let marginLeft = (50.5 * this.baner.length - 50.5)*-1;
            this.marginLeftCard -= 50.5;
            if(marginLeft == this.marginLeftCard){
                this.marginLeftCard = 0;
            }
            this.cardPonit = this.cardPonit + 1;
            if(this.cardPoint > this.card.length){
                this.cardPoint = 1;
            }
            $('.card01').animate({'margin-left': this.marginLeftCard+"%"}, 500);
        },
        nextTestimonial: function(){
            let marginLeft = (50.5 * this.baner.length - 50.5)*-1;
            this.marginLeftCard -= 50.5;
            if(marginLeft == this.marginLeftCard){
                this.marginLeftCard = 0;
            }
            this.cardPonit = this.cardPonit + 1;
            if(this.cardPoint > this.card.length){
                this.cardPoint = 1;
            }
            $('.card01').animate({'margin-left': this.marginLeftCard+"%"}, 500);
        },
        marcasMov: function(){
            let time = this.marcas.length*0.5 *1000;
            let marcaWidth = $('#marcas').width();
            console.log(marcaWidth);
            $('#marcas').animate({'margin-left': "-"+(marcaWidth - (marcaWidth/this.marcas.length*2))+"px"}, this.marcas.length*1000);
            setInterval(() => {
                $('#marcas').animate({'margin-left': "-0px"}, this.marcas.length*1000);
                $('#marcas').animate({'margin-left': "-"+(marcaWidth - (marcaWidth/this.marcas.length*2))+"px"}, this.marcas.length*1000);
            }, time);
        }
    }
});