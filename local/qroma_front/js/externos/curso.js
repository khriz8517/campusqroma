var app = new Vue({
    el: '#app',
    data(){
        return{
            menu: false,
            general: 0, cursoTxt: '', disponibleTxt:'',
            cursos: [],
            subcategory:[],
            subsubcategory:[]
        }
    },
    created() {
        this.sizeWeb();
        window.onresize = this.sizeWeb;
        console.log("course");
    },
    mounted() {
        this.loadCoursesGeneralInfo();
        this.loadAllCourses();
    },
    methods: {
        loadCoursesGeneralInfo: function() {
            let frm = new FormData();
            frm.append('request_type','obtenerCursosByCat');
            frm.append('idCat',13);
            axios.post('../qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    this.general = response.data.totalCourses; this.cursoTxt = response.data.cursoTxt; this.disponibleTxt = response.data.disponibleTxt;
                });
        },
        loadAllCourses: function() {
            let frm = new FormData();
            frm.append('idCat',13);
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