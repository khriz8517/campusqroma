var app = new Vue({
    el: '#app',
    data(){
        return{
            menu: false,
            totalCourses : 0,
            user:{
                name: 'JOHN DOE',
                role: 'ADMIN',
                points: 123,
                progress: 60,
                dateReg: {
                    day: 22,
                    mount: 'Septiembre',
                    year: 2020
                },
                miProgress: {
                    allCurses: 254,
                    successFullCurses: 250,
                    progress: 80,
                    prize: 15,
                    valoration: 12,
                    shared: 6,
                    discution: 0
                }
            },
            cursos: [],
            desarrollo:[],
            misCursos:[
                {
                    name: 'Nombre completo del primer curso de prueba con dos filas de text para nombres largos',
                    photo: './img/cursos.jpg',
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
                    photo: './img/cursos.jpg',
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
                    photo: './img/cursos.jpg',
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
                    photo: './img/cursos.jpg',
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
                    photo: './img/cursos.jpg',
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
                    photo: './img/cursos.jpg',
                    url: '#',
                    porcent: 0,
                    dateEnd: {
                        day: 15,
                        mount: "Abril",
                        year: 2020
                    }
                },
            ],
            producto: [
                {
                    photo: './img/product.jpg',
                    name: 'Primer Producto',
                    point: 40,
                    url: '#',
                },
                {
                    photo: './img/product.jpg',
                    name: 'Primer Producto',
                    point: 40,
                    url: '#',
                },
                {
                    photo: './img/product.jpg',
                    name: 'Primer Producto',
                    point: 40,
                    url: '#',
                },
                {
                    photo: './img/product.jpg',
                    name: 'Primer Producto',
                    point: 40,
                    url: '#',
                },
            ]
        }
    },
    created(){
        this.sizeWeb();
        window.onresize = this.sizeWeb;

    },
    mounted(){
        this.getSearchedCourses();
        this.getSearchedLecciones();
        this.subCategoryFormat();
    },
    methods: {
        menuBtn: function(){
            console.log("menu");
            if(this.menu == false){
                this.menu = true;
            } else{
                this.menu = false;
            }
        },
        getSearchedCourses: function() {
            let name = new URL(location.href).searchParams.get('search');
            let catId = 1;
            let frm = new FormData();
            frm.append('idCat',catId);
            frm.append('name',name);
            frm.append('request_type','obtenerCursosBySearch');
            axios.post('../local/qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    let data = response.data.data;
                    let courses = Array();

                    Object.keys(data).forEach(key => {
                        let dataVal = data[key];
                        let name = dataVal.name;
                        let url = dataVal.url;
                        let img = dataVal.img;
                        let percentage = dataVal.percentage;

                        let newElem = {
                            'name': name,
                            'url': url,
                            'img': img,
                            'percentage': percentage
                        };
                        courses.push(newElem);
                    });
                    this.totalCourses = response.data.totalCourses;
                    this.cursos = courses;
                });
        },
        getSearchedLecciones: function() {
            let name = new URL(location.href).searchParams.get('search');
            let catId = 6;
            let frm = new FormData();
            frm.append('idCat',catId);
            frm.append('name',name);
            frm.append('request_type','obtenerCursosBySearch');
            axios.post('../local/qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    let data = response.data.data;
                    let courses = Array();

                    Object.keys(data).forEach(key => {
                        let dataVal = data[key];
                        let name = dataVal.name;
                        let url = dataVal.url;
                        let img = dataVal.img;
                        let percentage = dataVal.percentage;

                        let newElem = {
                            'name': name,
                            'url': url,
                            'img': img,
                            'percentage': percentage
                        };
                        courses.push(newElem);
                    });
                    this.totalCourses = response.data.totalCourses;
                    this.desarrollo = courses;
                });
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