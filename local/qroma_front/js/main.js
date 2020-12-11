var app = new Vue({
    el: '#app',
    data(){
        return{
            title: 'Hello Vue!',
            menu: false,
            baner: [],
            banerPoint: 0,
            marginLeftBaner: 0,
            card: [],
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
        this.loadData();
        this.banerFormat();
        this.banerMov();
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
           console.log('load data 2');
           this.loadSlider();
           this.loadTestimonios();
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
                });
        },
        banerMov: function(orint) {
            $('#baner .item:nth-child(1)').fadeIn();
            setInterval(() => {
                this.banerPoint = this.banerPoint + 1;
                $('#baner').animate({'margin-left': this.marginLeftBaner+"%"}, 500);
                let marginLeft = (100 * this.baner.length - 100)*-1;
                this.marginLeftBaner -= 100;
                if(marginLeft > this.marginLeftBaner){
                    this.marginLeftBaner = 0;
                }
                if(this.banerPoint > this.baner.length){
                    this.banerPoint = 1;
                }
            }, 5000);
        },
        prevBaner: function(){

        },
        nextBaner: function(){

        },
        prevTestimonial: function(){

        },
        nextTestimonial: function(){

        }
    }
});