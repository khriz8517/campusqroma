var app = new Vue({
    el: '#app',
    delimiters: ['{(', ')}'],
    data(){
        return{
            menu: false,
            cursospaginate: [],
            pages: 0,
            marginLeftBaner: 0,
            banerPoint: 1,
            user: {
                photo: '',
                name: '',
                points: 0,
                dateReg: ''
            },
            cursos: [
                {
                    img: '/local/qroma_front/img/cursos.jpg',
                    title: 'Titulo del Primer curso',
                    pais: 'PERÚ',
                    flat: '/local/qroma_front/img/pais/peru.jpg',
                    porcent: '0'
                },
                {
                    img: '/local/qroma_front/img/cursos.jpg',
                    title: 'Titulo del Segundo curso',
                    pais: 'PERÚ',
                    flat: '/local/qroma_front/img/pais/peru.jpg',
                    porcent: '0'
                },
                {
                    img: '/local/qroma_front/img/cursos.jpg',
                    title: 'Titulo del Tercero curso',
                    pais: 'PERÚ',
                    flat: '/local/qroma_front/img/pais/peru.jpg',
                    porcent: '0'
                },
                {
                    img: '/local/qroma_front/img/cursos.jpg',
                    title: 'Titulo del Tercero curso',
                    pais: 'PERÚ',
                    flat: '/local/qroma_front/img/pais/peru.jpg',
                    porcent: '0'
                },
                {
                    img: '/local/qroma_front/img/cursos.jpg',
                    title: 'Titulo del Tercero curso',
                    pais: 'PERÚ',
                    flat: '/local/qroma_front/img/pais/peru.jpg',
                    porcent: '0'
                }
            ],
            ranking: [
                {
                    name: 'Comercial',
                    points: 158,
                },
                {
                    name: 'Comercial',
                    points: 158,
                },
                {
                    name: 'Comercial',
                    points: 158,
                },
                {
                    name: 'Comercial',
                    points: 158,
                }
            ],
            misCursos:[
                {
                    name: 'Nombre completo del primer curso de prueba con dos filas de text para nombres largos',
                    photo: '/local/qroma_front/img/cursos.jpg',
                    url: '#',
                    porcent: 0,
                    dateEnd: {
                        day: 15,
                        mount: "Abril",
                        year: 2020
                    }
                },
                {
                    name: 'Nombre completo del primer curso de prueba con dos filas de text para nombres largos',
                    photo: '/local/qroma_front/img/cursos.jpg',
                    url: '#',
                    porcent: 0,
                    dateEnd: {
                        day: 15,
                        mount: "Abril",
                        year: 2020
                    }
                },
                {
                    name: 'Nombre completo del primer curso de prueba con dos filas de text para nombres largos',
                    photo: '/local/qroma_front/img/cursos.jpg',
                    url: '#',
                    porcent: 0,
                    dateEnd: {
                        day: 15,
                        mount: "Abril",
                        year: 2020
                    }
                },
                {
                    name: 'Nombre completo del primer curso de prueba con dos filas de text para nombres largos',
                    photo: '/local/qroma_front/img/cursos.jpg',
                    url: '#',
                    porcent: 0,
                    dateEnd: {
                        day: 15,
                        mount: "Abril",
                        year: 2020
                    }
                },
                {
                    name: 'Nombre completo del primer curso de prueba con dos filas de text para nombres largos',
                    photo: '/local/qroma_front/img/cursos.jpg',
                    url: '#',
                    porcent: 0,
                    dateEnd: {
                        day: 15,
                        mount: "Abril",
                        year: 2020
                    }
                },
                {
                    name: 'Nombre completo del primer curso de prueba con dos filas de text para nombres largos',
                    photo: '/local/qroma_front/img/cursos.jpg',
                    url: '#',
                    porcent: 0,
                    dateEnd: {
                        day: 15,
                        mount: "Abril",
                        year: 2020
                    }
                },
            ],
            btnBefore: false,
            btnAfter: false,
        }
    },
    created(){
        this.sizeWeb();
        window.onresize = this.sizeWeb;

    },
    mounted(){
        this.pages = Math.ceil(this.cursos.length/4);
        this.cursospaginate = new Array(this.pages);
        this.subCategoryFormat();
        this.obtenerUsuario();
    },
    methods: {
        obtenerUsuario: function(){
            let frm = new FormData();
            frm.append('request_type','obtenerUsuario');
            axios.post('../local/qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    // handle success
                    let data = response.data.data;
                    this.user.photo = data.photo;
                    this.user.name = data.name;
                    this.user.levelImage = data.levelImage;
                    this.user.points = data.points;
                    this.user.dateReg = data.dateReg;
                });
        },
        prevBaner: function() {
            let marginLeft = 0;
            this.marginLeftBaner += 100;
            if(marginLeft < this.marginLeftBaner) {
                this.marginLeftBaner = (100 * this.cursospaginate.length - 100)*-1;
            }
            if(this.banerPoint == 1) {
                this.banerPoint = this.cursospaginate.length;
            } else {
                this.banerPoint -= 1;
            }
            $('.cursos01').animate({'margin-left': this.marginLeftBaner+"%"}, 500);
        },
        nextBaner: function() {
            let marginLeft = (100 * this.cursospaginate.length - 100)*-1;
            this.marginLeftBaner -= 100;
            if(marginLeft > this.marginLeftBaner) {
                this.marginLeftBaner = 0;
            }
            if(this.banerPoint <  this.cursospaginate.length) {
                this.banerPoint += 1;
            } else if(this.banerPoint ==  this.cursospaginate.length) {
                this.banerPoint = 1;
            }
            $('.cursos01').animate({'margin-left': this.marginLeftBaner+"%"}, 500);
        },
        changeBaner: function(index){
            this.banerPoint = index + 1;
            if(index == 0) {
                this.marginLeftBaner = -100 * index;
                $('.cursos01').animate({'margin-left': this.marginLeftBaner+"%"}, 500);
            } else {
                let moveSign = 1;
                if(this.banerPoint > index+1) {
                    moveSign = -1;
                }
                this.marginLeftBaner = -100 * index * moveSign;
                $('.cursos01').animate({'margin-left': this.marginLeftBaner+"%"}, 500);
            }
        },
        menuBtn: function(){
            console.log("menu");
            if(this.menu == false){
                this.menu = true;
            } else{
                this.menu = false;
            }
        },
        subCategoryFormat: function(){
            let listView = $("#list-tabs").width();
            let items    = $("#list-tabs .list .item");
            let widthAll = 0;
            for (let i = 0; i < items.length; i++) {
                // const element = items[i];
                // console.log(element);
                widthAll += items[i].offsetWidth+20;
                // console.log(widthAll);
            }

            let listCont = widthAll-20;
            // $("#list-tabs .list").width();
            // console.log(listView);
            // console.log(listCont);
            if(listView > listCont){
                $("#list-tabs .list").css({"justify-content":"center"});
            } else{
                $("#list-tabs .list").css({"justify-content":"flex-start"});
                $("#list-tabs .list").width(`${widthAll-20}px`);
                this.btnAfter = true;
            }
        },
        sizeWeb: function(){
            this.subCategoryFormat();
            if (window.innerWidth < 768)
                this.menu = false;
            else
                this.menu = true;
        },
        changeTabs: function(obj){
            console.log(obj);
            console.log($('#tabs-header .item'));
            $('#tabs-header .item').removeClass('active');
            $('#tabs-header .item:nth-child('+obj+')').addClass('active');
        }
    }
});
