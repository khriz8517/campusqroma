var app = new Vue({
    el: '#app',
    data(){
        return{
            menu: false,
            general: 0, cursoTxt: '', disponibleTxt:'',
            cursos: [],
            subcategory:[],
            subsubcategory:[],
            baner: [],
            banerPoint: 0,
            marginLeftBaner: 0
        }
    },
    created() {
        this.sizeWeb();
        window.onresize = this.sizeWeb;
        console.log("course");
    },
    mounted() {
        this.loadData();
        this.banerFormat();
        this.banerMov();
        this.loadCoursesGeneralInfo();
        this.loadAllCourses();
    },
    methods: {
        loadSlider: function() {
            let frm = new FormData();
            frm.append('ferreterias',1);
            frm.append('request_type','obtenerSlider');
            axios.post('../qroma_front/api/ajax_controller_qroma.php',frm)
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
        loadData: function(){
            this.loadSlider();
        },
        banerFormat: function(){
            let frm = new FormData();
            let banerNum = 0;
            let data = {};
            frm.append('ferreterias',1);
            frm.append('request_type','obtenerSlider');
            axios.post('../qroma_front/api/ajax_controller_qroma.php',frm)
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
        loadCoursesGeneralInfo: function() {
            let frm = new FormData();
            frm.append('request_type','obtenerCursosByCat');
            frm.append('idCat',3);
            axios.post('../qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    this.general = response.data.totalCourses; this.cursoTxt = response.data.cursoTxt; this.disponibleTxt = response.data.disponibleTxt;
                });
        },
        loadAllCourses: function() {
            let frm = new FormData();
            frm.append('idCat',3);
            frm.append('request_type','obtenerSubcategoriasByCat');
            axios.post('../qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    let data = response.data.data;
                    let dataSubCats = response.data.dataSubCats;
                    let dataCourses = response.data.dataCourses;
                    let subCats = Array();
                    let subCatsFirst = Array();
                    let coursesFirst = Array();

                    if(data) {
                        Object.keys(data).forEach(key => {
                            let dataVal = data[key];
                            let id = dataVal.id;
                            let name = dataVal.name;
                            let status = dataVal.visible;
                            let btnClass = dataVal.btnClass;
                            let active = dataVal.active;
                            let totalCourses = dataVal.totalCourses;

                            let newElem = {
                                'id': id,
                                'name': name,
                                'status': status,
                                'btnClass': btnClass,
                                'active': active,
                                'totalCourses': totalCourses
                            };
                            subCats.push(newElem);
                        });
                        this.subcategory = subCats;
                    }

                    if(dataSubCats) {
                        Object.keys(dataSubCats).forEach(key => {
                            let dataVal = dataSubCats[key];
                            let id = dataVal.id;
                            let name = dataVal.name;

                            let newElem = {
                                'id': id,
                                'name': name,
                                'status': status
                            };
                            subCatsFirst.push(newElem);
                        });
                        this.subsubcategory = subCatsFirst;
                    }

                    if(dataCourses) {
                        Object.keys(dataCourses).forEach(key => {
                            let dataVal = dataCourses[key];
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
                            coursesFirst.push(newElem);
                        });
                        this.cursos = coursesFirst;
                    }
                });
        },
        getCourses: function(event) {
            let catId = event.currentTarget.dataset.catId;
            let frm = new FormData();
            frm.append('idCat',catId);
            frm.append('request_type','obtenerCursosByCat');
            axios.post('../qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    let data = response.data.data;
                    let courses = Array();

                    Object.keys(data).forEach(key => {
                        let dataVal = data[key];
                        let name = dataVal.name;
                        let url = dataVal.url;
                        let img = dataVal.img;

                        let newElem = {
                            'name': name,
                            'url': url,
                            'img': img
                        };
                        courses.push(newElem);
                    });
                    this.cursos = courses;
                });
        },
        menuBtn: function(){
            console.log("menu");
            if(this.menu == false){
                this.menu = true;
            } else{
                this.menu = false;
            }
        },
        sizeWeb: function(){

        },
    }
});