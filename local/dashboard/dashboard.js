var app = new Vue({
    el: '#app',
    delimiters: ['{(', ')}'],
    data(){
        return{
            //panel 01
            menu: false,
            menu2: false,
            divSize: 0,
            cursospaginate: [],
            pages: 0,
            marginLeftBaner: 0,
            banerPoint: 1,
            user: {
                id: '',
                photo: '',
                name: '',
                points: 0,
                dateReg: '',
                progress: 50,
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
            misCursos:[],
            btnBefore: false,
            btnAfter: false,
            //Seguimiento
            cursosList: [],
            usuarios: [],
            act: {},
            users: false,
            general: true,
            listPorcent: {},
            searchCursos: '',
            searchAlumnos: '',
            searchUsers:[],
            backIds: '',
            textMails: '',
            selectedUser: '',
            selectedUsers: [],
            textMailsSingle: '',
            loadingUsers: false,
            cursosExcel: [],

            // ordenamiento
            order: true,
            orderUser: false,
            orderGeren: false,
            orderArea: false,

            //PANEL 02
            areaListp2: [],
            cursosListp2: [],
            usuariosp2: [],

            // ordenamiento
            orderp2: true,
            orderPorcentp2: false,
            orderCursosp2: false,
            orderGerenp2: false,
            orderAreap2: false,
            orderUserp2: false,
            orderPorcent2p2: false,

            // vistas
            cursosp2: false,
            generalp2: true,
            usersp2: false,
            searchCursosp2: '',
            searchAreasp2: '',
            searchAlumnosp2: '',
            checkedAreap2: false,
            checkedUserp2: false,
            menup2: false,
            menu2p2: false,
            actualAreap2: '',

            //PANEL 03
            cursosListp3: [
                {
                    id: '1',
                    name: 'Ergonomia 2020',
                    numEstu: 751,
                    progress: 90
                },
                {
                    id: '1',
                    name: 'IPERC',
                    numEstu: 751,
                    progress: 90
                },
                {
                    id: '1',
                    name: 'Riesgo en instalaciones 2020',
                    numEstu: 751,
                    progress: 90
                },
                {
                    id: '1',
                    name: 'Material desing',
                    numEstu: 751,
                    progress: 90
                },
                {
                    id: '1',
                    name: 'Inducción SSOMA',
                    numEstu: 751,
                    progress: 90
                },
                {
                    id: '1',
                    name: 'Habilidades bladas',
                    numEstu: 751,
                    progress: 90
                },
                {
                    id: '1',
                    name: 'Operacciones pesqueras',
                    numEstu: 751,
                    progress: 90
                },
                {
                    id: '1',
                    name: 'Habilidades bladas',
                    numEstu: 751,
                    progress: 90
                },
                {
                    id: '1',
                    name: 'Operacciones pesqueras',
                    numEstu: 751,
                    progress: 90
                },
                {
                    id: '1',
                    name: 'Ergonomia 2020',
                    numEstu: 751,
                    progress: 90
                },
            ],
            direccionListp3: [
                {name: "Dirección general", director: "Juan Matías Rodriguez", progress: 90},
                {name: "Dirección GH", director: "Felipe Quispe Alvarez", progress: 90},
                {name: "Dirección TI", director: "Julieta Toro Ramirez", progress: 90},
                {name: "Dirección Mantenimiento", director: "Khatia Gutierrez", progress: 90},
                {name: "Dirección Legal", director: "Matías Lorenzo Lamas", progress: 90},
                {name: "Dirección Operaciones", director: "Paolo Batallas", progress: 90},
                {name: "Dirección general", director: "Juan Matías Rodriguez", progress: 90},
                {name: "Dirección general", director: "Juan Matías Rodriguez", progress: 90},
            ],
            usuariosp3: [
                {
                    name: 'Alfredo Mendoza Rodriguez',
                    gerencia: "Otros",
                    area: "Almacen",
                    progress: 5
                },
                {
                    name: 'Juan Matias Rodriguez',
                    gerencia: "Gerencia GH",
                    area: "logistica",
                    progress: 10
                },
                {
                    name: 'Juan Matias Rodriguez',
                    gerencia: "Gerencia GH",
                    area: "logistica",
                    progress: 20
                },
                {
                    name: 'Juan Matias Rodriguez',
                    gerencia: "Gerencia GH",
                    area: "logistica",
                    progress: 60
                },
                {
                    name: 'Juan Matias Rodriguez',
                    gerencia: "Gerencia GH",
                    area: "logistica",
                    progress: 80
                },
                {
                    name: 'Juan Matias Rodriguez',
                    gerencia: "Gerencia GH",
                    area: "logistica",
                    progress: 40
                },
                {
                    name: 'Juan Matias Rodriguez',
                    gerencia: "Gerencia GH",
                    area: "logistica",
                    progress: 30
                },
                {
                    name: 'Juan Matias Rodriguez',
                    gerencia: "Gerencia GH",
                    area: "logistica",
                    progress: 10
                },
                {
                    name: 'Juan Matias Rodriguez',
                    gerencia: "Gerencia GH",
                    area: "logistica",
                    progress: 5
                },
            ],
            areaListp3: [
                {name: "Comercial", direccion: "Dirección general", progress: 90},
                {name: "Bienestar", direccion: "Dirección GH", progress: 90},
                {name: "Área 1", direccion: "Dirección TI", progress: 70},
                {name: "Área 2", direccion: "Dirección mantenimiento", progress: 90},
                {name: "Área 3", direccion: "Dirección general", progress: 18},
                {name: "Área 5", direccion: "Dirección general", progress: 90},
                {name: "Área 6", direccion: "Dirección general", progress: 90},
                {name: "Área 7", direccion: "Dirección general", progress: 90},
                {name: "Área 8", direccion: "Dirección general", progress: 90},
            ],

            // vistas
            cursosp3: false,
            generalp3: true,
            areap3: false,
            usersp3: false,

            listPorcentp3: {},
            searchDireccionp3: '',
            searchCursosp3: '',
            searchAreasp3: '',
            searchAlumnosp3: '',
            // searchUsersp3:[],
            // controlador de checkbox
            checkedAreap3: false,
            checkedUserp3: false,

            menup3: false,
            menu2p3: false,

            // ordenamiento
            orderp3: true,
            orderPorcentp3: false,
            orderCursosp3: false,
            orderGerenp3: false,
            orderAreap3: false,
            orderUserp3: false,
            orderPorcent2p3: false,

        };
    },
    created(){
        this.sizeWeb();
        window.onresize = this.sizeWeb;

    },
    mounted(){
        this.subCategoryFormat();
        this.obtenerUsuario();
        this.obtenerCursosPendientes();
        this.obtenerTotalCursos();
        this.obtenerCursosPanel();
        this.obtenerCursosExcel();
        this.obtenerAreasPanel2();
        this.obtenerCursosPanel3();
    },
    computed: {
        searchCurse: function (){
            return this.cursosList.filter((item) => item.name.includes(this.searchCursos));
        },

        //panel 02
        searchAreap2: function (){
            return this.areaListp2.filter((item) => item.name.includes(this.searchAreasp2));
        },
        searchCursep2: function (){
            return this.cursosListp2.filter((item) => item.name.includes(this.searchCursosp2));
        },
        searchUsersp2: function (){
            setTimeout(function(){
                $('.circlechart').circlechart();
            }, 150);
            return this.usuariosp2.filter((item) => item.name.includes(this.searchAlumnosp2));
        },

        //panel 03
        searchDirectionp3: function (){
            return this.direccionListp3.filter((item) => item.name.includes(this.searchDireccionp3));
        },
        searchCursep3: function (){
            return this.cursosListp3.filter((item) => item.name.includes(this.searchCursosp3));
        },
        searchAreap3: function (){
            return this.areaListp3.filter((item) => item.name.includes(this.searchAreasp3));
        },
        searchUsersp3: function (){
            setTimeout(function(){
                $('.circlechart').circlechart();
            }, 150);
            return this.usuariosp3.filter((item) => item.name.includes(this.searchAlumnosp3));
        },
    },
    methods: {

        //DASHBOARD

        obtenerCursosExcel: function() {
            let frm = new FormData();
            frm.append('request_type','obtenerCursosExcel');
            axios.post('../local/qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    // handle success
                    let data = response.data.data;
                    let cursos = [];

                    Object.keys(data).forEach(key => {
                        let dataVal = data[key];
                        let id = dataVal.id;
                        let nombre = dataVal.nombre;

                        let newElem = {
                            'id': id,
                            'nombre': nombre
                        };
                        cursos.push(newElem);
                    });
                    this.cursosExcel = cursos;
                });
        },
        obtenerUsuario: function(){
            let frm = new FormData();
            frm.append('request_type','obtenerUsuario');
            axios.post('../local/qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    // handle success
                    let data = response.data.data;
                    this.user.id = data.id;
                    this.user.photo = data.photo;
                    this.user.name = data.name;
                    this.user.levelImage = data.levelImage;
                    this.user.points = data.points;
                    this.user.dateReg = data.dateReg;
                    this.user.progress = 50;
                    this.user.isAdmin = data.isAdmin;
                    this.user.role = data.role;
                });
        },
        obtenerCursosPendientes: function () {
            let frm = new FormData();
            frm.append('request_type','obtenerCursosPendientes');
            axios.post('../local/qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    // handle success
                    let data = response.data.data;
                    let cursos = [];
                    
                    Object.keys(data).forEach(key => {
                        let dataVal = data[key];
                        let img = dataVal.img;
                        let title = dataVal.title;
                        let pais = dataVal.pais;
                        let flag = dataVal.flag;
                        let url = dataVal.url;
                        let progress = dataVal.progress;

                        let newElem = {
                            'img': img,
                            'title': title,
                            'pais': pais,
                            'flag': flag,
                            'url': url,
                            'progress': progress
                        };
                        cursos.push(newElem);
                    });
                    this.cursos = cursos;
                    this.divSize = this.cursos.length * 300 + 'px';
                    this.pages = Math.ceil(this.cursos.length/4);
                    this.cursospaginate = new Array(this.pages);
                });
        },
        obtenerTotalCursos: function () {
            let frm = new FormData();
            frm.append('request_type','obtenerTotalCursos');
            axios.post('../local/qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    // handle success
                    let data = response.data.data;
                    let cursos = [];

                    Object.keys(data).forEach(key => {
                        let dataVal = data[key];
                        let img = dataVal.img;
                        let title = dataVal.title;
                        let url = dataVal.url;
                        let dateEnd = dataVal.dateEnd;
                        let progress = dataVal.progress;

                        let newElem = {
                            'img': img,
                            'title': title,
                            'url': url,
                            'dateEnd': dateEnd,
                            'progress': progress
                        };
                        cursos.push(newElem);
                    });
                    this.misCursos = cursos;
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
                widthAll += items[i].offsetWidth+20;
            }

            let listCont = widthAll-20;
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
            $('#tabs-header .item').removeClass('active');
            $('#tabs-header .item:nth-child('+obj+')').addClass('active');
        },
        searchName: function(){
            if(this.searchAlumnos != ''){
                this.searchUsers = this.usuarios.filter((item) => item.name.includes(this.searchAlumnos));
            } else{
                this.searchUsers = this.usuarios;
            }
            $('.circlechart').circlechart();
        },
        filterGerencia: function(name){
            this.searchUsers = this.usuarios.filter((item) => item.gerencia.includes(name));
            $('.circlechart').circlechart();
        },
        filterArea: function(name){
            this.searchUsers = this.usuarios.filter((item) => item.area.includes(name));
            $('.circlechart').circlechart();
        },
        filterZona: function(name){
            this.searchUsers = this.usuarios.filter((item) => item.zona.includes(name));
            $('.circlechart').circlechart();
        },
        changeOrder: function(){
            this.order = this.order ? false : true;
            this.cursosList = this.cursosList.slice().reverse();
        },
        close: function(){
            this.general = true;
            this.users = false;
        },
        activeOptions: function(key){
            if(!document.querySelector('#option_'+key).classList.contains('active')){
                document.querySelector('#option_'+key).classList.add('active');
            } else{
                document.querySelector('#option_'+key).classList.remove('active');
            }
        },
        activeSubmenu: function(elem){
            if(!document.querySelector('#'+elem).classList.contains('show')){
                document.querySelector('#'+elem).classList.add('show');
            } else{
                document.querySelector('#'+elem).classList.remove('show');
            }
        },


        //PANELES

        //PANEL 01

        obtenerCursosPanel: function() {
            let frm = new FormData();
            frm.append('request_type','panelUserCursos');
            axios.post('../local/qroma_front/api/ajax_controller_qroma.php', frm)
                .then((response) => {
                    let data = response.data.data;
                    let courses = Array();

                    Object.keys(data).forEach(key => {
                        let dataVal = data[key];
                        let id = dataVal.id;
                        let name = dataVal.name;
                        let numEstu = dataVal.numEstu;
                        let date = dataVal.date;
                        let progress = dataVal.progress;
                        let userIdsMail = dataVal.userIdsMail;

                        let newElem = {
                            'id': id,
                            'name': name,
                            'numEstu': numEstu,
                            'date': date,
                            'progress': progress,
                            'userIdsMail': userIdsMail
                        };
                        courses.push(newElem);
                    });
                    this.cursosList = courses;
                });
        },
        viewUser: function(cursoId){
            this.general = false;
            this.users = true;
            this.loadingUsers = true;

            let frm = new FormData();
            frm.append('courseId', cursoId);
            frm.append('request_type','getUsuariosByCurso');
            axios.post('../local/qroma_front/api/ajax_controller_qroma.php', frm)
                .then((response) => {
                    let usuarios = Array();

                    this.act = {
                        name: response.data.nombreCurso
                    };

                    // let gerenciasList = response.data.gerenciasList;
                    // let areasList = response.data.areasList;
                    // let zonasList = response.data.zonasList;

                    let data = response.data.data;

                    Object.keys(data).forEach(key => {
                        let dataVal = data[key];
                        let id = dataVal.id;
                        let name = dataVal.name;
                        let direccion = dataVal.direccion;
                        let area = dataVal.area;
                        let progress = dataVal.progress;

                        let newElem = {
                            'id': id,
                            'name': name,
                            'direccion': direccion,
                            'area': area,
                            'progress': progress
                        };
                        usuarios.push(newElem);
                    });
                    this.loadingUsers = false;
                    this.usuarios = usuarios;
                    this.searchUsers = this.usuarios;
                    this.gerenciasList = [];
                    this.areasList = [];
                    this.zonasList = [];
                });
        },
        enviarCorreos: function() {
            let frm = new FormData();
            frm.append('idUsersAll', this.backIds);
            frm.append('message', this.textMails);
            axios.post('../my/email.php', frm)
                .then((response) => {
                    alert('Mensaje enviado');
                });
        },
        enviarCorreosSingle: function() {
            let frm = new FormData();
            frm.append('idUser', this.selectedUsers);
            frm.append('message', this.textMailsSingle);
            axios.post('../my/email.php', frm)
                .then((response) => {
                    alert('Mensaje enviado');
                });
        },
        closeModal: function(){
            document.querySelector(".back").style.display = "none";
        },
        closeModal2: function(){
            document.querySelector(".back-single").style.display = "none";
        },
        showModal: function(userIdsMail){
            document.querySelector(".back").style.display = "flex";
            this.backIds = userIdsMail;
        },
        selectUserClick: function(id) {
            var check = this.selectedUsers.includes(id);
            if(check) {
                this.selectedUsers.pop(id);
            } else {
                this.selectedUsers.push(id);
            }
        },
        showModal2: function(){
            document.querySelector(".back-single").style.display = "flex";
        },


        //PANEL 02
        obtenerAreasPanel2: function() {
            let frm = new FormData();
            frm.append('request_type','obtenerAreasPanel2');
            axios.post('../local/qroma_front/api/ajax_controller_qroma.php', frm)
                .then((response) => {
                    let data = response.data.data;
                    let areaList = Array();

                    Object.keys(data).forEach(key => {
                        let dataVal = data[key];
                        let area = dataVal.area;
                        let progress = dataVal.progress;

                        let newElem = {
                            'name': area,
                            'progress': progress,
                        };
                        areaList.push(newElem);
                    });
                    this.areaListp2 = areaList;
                });
        },
        viewCursop2: function(area){
            this.generalp2 = false;
            this.cursosp2 = true;
            this.usersp2 = false;

            this.actp2 = area;
            this.actualAreap2 = area;

            let frm = new FormData();
            frm.append('area', area);
            frm.append('request_type','panelUserCursos2');
            axios.post('../local/qroma_front/api/ajax_controller_qroma.php', frm)
                .then((response) => {
                    let data = response.data.data;
                    let courses = Array();

                    Object.keys(data).forEach(key => {
                        let dataVal = data[key];
                        let id = dataVal.id;
                        let name = dataVal.name;
                        let numEstu = dataVal.numEstu;
                        let date = dataVal.date;
                        let progress = dataVal.progress;
                        let userIdsMail = dataVal.userIdsMail;

                        let newElem = {
                            'id': id,
                            'name': name,
                            'numEstu': numEstu,
                            'date': date,
                            'progress': progress,
                            'userIdsMail': userIdsMail
                        };
                        courses.push(newElem);
                    });
                    this.cursosListp2 = courses;
                });
        },
        viewUserp2: function(curse){
            this.generalp2 = false;
            this.cursosp2 = false;
            this.usersp2 = true;

            this.actp2 = curse;

            let frm = new FormData();
            frm.append('courseId', curse);
            frm.append('area', this.actualAreap2);
            frm.append('request_type','obtenerUsuariosPanel2');
            axios.post('../local/qroma_front/api/ajax_controller_qroma.php', frm)
                .then((response) => {
                    let data = response.data.data;
                    let usuarios = Array();

                    Object.keys(data).forEach(key => {
                        let dataVal = data[key];
                        let id = dataVal.id;
                        let name = dataVal.name;
                        let direccion = dataVal.direccion;
                        let area = dataVal.area;
                        let progress = dataVal.progress;

                        let newElem = {
                            'id': id,
                            'name': name,
                            'direccion': direccion,
                            'area': area,
                            'progress': progress
                        };
                        usuarios.push(newElem);
                    });
                    this.usuariosp2 = usuarios;
                });
        },
        selectAreap2: function() {
            let check = document.getElementsByClassName("checkbox");
            if(!this.checkedArea){
                for (let i = 0; i < check.length; i++) {
                    if(check[i].getAttribute('data') < 100){
                        check[i].checked = true;
                    }
                }
            } else{
                for (let i = 0; i < check.length; i++) {
                    check[i].checked = false;
                }
            }
        },
        selectUserp2: function() {
            let check = document.getElementsByClassName("checkbox-2");
            if(!this.checkedUserp2){
                for (let i = 0; i < check.length; i++) {
                    if(check[i].getAttribute('data') < 100){
                        check[i].checked = true;
                    }
                }
            } else{
                for (let i = 0; i < check.length; i++) {
                    check[i].checked = false;
                }
            }
        },
        closep2: function(){
            this.generalp2 = true;
            this.cursosp2 = false;
            this.usersp2 = false;
        },
        closeUserp2: function(){
            this.generalp2 = false;
            this.cursosp2 = true;
            this.usersp2 = false;
        },
        changeOrderp2: function(){
            if(this.orderp2){
                this.areaList.sort(function (a, b) {
                    if (a.name > b.name) {
                        return 1;
                    }
                    if (a.name < b.name) {
                        return -1;
                    }
                    return 0;
                });
                this.orderp2 = false;
            } else{
                this.areaList.sort(function (a, b) {
                    if (a.name < b.name) {
                        return 1;
                    }
                    if (a.name > b.name) {
                        return -1;
                    }
                    return 0;
                });
                this.orderp2 = true;
            }
        },
        changeOrderPorcentp2: function(){
            if(this.orderPorcentp2){
                this.areaListp2.sort(function (a, b) {
                    if (a.progress > b.progress) {
                        return 1;
                    }
                    if (a.progress < b.progress) {
                        return -1;
                    }
                    return 0;
                });
                this.orderPorcentp2 = false;
            } else{
                this.areaListp2.sort(function (a, b) {
                    if (a.progress < b.progress) {
                        return 1;
                    }
                    if (a.progress > b.progress) {
                        return -1;
                    }
                    return 0;
                });
                this.orderPorcentp2 = true;
            }
        },
        changeOrderCursosp2: function(){
            if(this.orderCursosp2){
                this.cursosListp2.sort(function (a, b) {
                    if (a.name > b.name) {
                        return 1;
                    }
                    if (a.name < b.name) {
                        return -1;
                    }
                    return 0;
                });
                this.orderCursosp2 = false;
            } else{
                this.cursosListp2.sort(function (a, b) {
                    if (a.name < b.name) {
                        return 1;
                    }
                    if (a.name > b.name) {
                        return -1;
                    }
                    return 0;
                });
                this.orderCursosp2 = true;
            }
        },
        changeOrderUserp2: function(){
            if(this.orderUserp2){
                this.usuariosp2.sort(function (a, b) {
                    if (a.name > b.name) {
                        return 1;
                    }
                    if (a.name < b.name) {
                        return -1;
                    }
                    return 0;
                });
                this.orderUserp2 = false;
            } else{
                this.usuariosp2.sort(function (a, b) {
                    if (a.name < b.name) {
                        return 1;
                    }
                    if (a.name > b.name) {
                        return -1;
                    }
                    return 0;
                });
                this.orderUserp2 = true;
            }
        },
        changeOrderGerenciap2: function(){
            if(this.orderGerenp2){
                this.usuariosp2.sort(function (a, b) {
                    if (a.gerencia > b.gerencia) {
                        return 1;
                    }
                    if (a.gerencia < b.gerencia) {
                        return -1;
                    }
                    return 0;
                });
                this.orderGerenp2 = false;
            } else{
                this.usuariosp2.sort(function (a, b) {
                    if (a.gerencia < b.gerencia) {
                        return 1;
                    }
                    if (a.gerencia > b.gerencia) {
                        return -1;
                    }
                    return 0;
                });
                this.orderGerenp2 = true;
            }
        },
        changeOrderAreap2: function(){
            if(this.orderAreap2){
                this.usuariosp2.sort(function (a, b) {
                    if (a.area > b.area) {
                        return 1;
                    }
                    if (a.area < b.area) {
                        return -1;
                    }
                    return 0;
                });
                this.orderAreap2 = false;
            } else{
                this.usuariosp2.sort(function (a, b) {
                    if (a.area < b.area) {
                        return 1;
                    }
                    if (a.area > b.area) {
                        return -1;
                    }
                    return 0;
                });
                this.orderAreap2 = true;
            }
        },
        changeOrderPorcent2p2: function(){
            if(this.orderPorcent2p2){
                this.usuariosp2.sort(function (a, b) {
                    if (a.progress > b.progress) {
                        return 1;
                    }
                    if (a.progress < b.progress) {
                        return -1;
                    }
                    return 0;
                });
                this.orderPorcent2p2 = false;
            } else{
                this.usuariosp2.sort(function (a, b) {
                    if (a.progress < b.progress) {
                        return 1;
                    }
                    if (a.progress > b.progress) {
                        return -1;
                    }
                    return 0;
                });
                this.orderPorcent2p2 = true;
            }
        },



        //PANEL 03
        obtenerCursosPanel3: function() {
            let frm = new FormData();
            frm.append('request_type','obtenerCursosPanel3');
            axios.post('../local/qroma_front/api/ajax_controller_qroma.php', frm)
                .then((response) => {
                    let data = response.data.data;
                    let areaList = Array();

                    Object.keys(data).forEach(key => {
                        let dataVal = data[key];
                        let area = dataVal.area;
                        let progress = dataVal.progress;

                        let newElem = {
                            'name': area,
                            'progress': progress,
                        };
                        areaList.push(newElem);
                    });
                    this.areaListp2 = areaList;
                });
        },
        selectAreap3: function() {
            let check = document.getElementsByClassName("checkbox");
            if(!this.checkedAreap3){
                for (let i = 0; i < check.length; i++) {
                    if(check[i].getAttribute('data') < 100){
                        check[i].checked = true;
                    }
                }
            } else{
                for (let i = 0; i < check.length; i++) {
                    check[i].checked = false;
                }
            }
        },
        selectUserp3: function() {
            let check = document.getElementsByClassName("checkbox-2");
            if(!this.checkedUserp3){
                for (let i = 0; i < check.length; i++) {
                    if(check[i].getAttribute('data') < 100){
                        check[i].checked = true;
                    }
                }
            } else{
                for (let i = 0; i < check.length; i++) {
                    check[i].checked = false;
                }
            }
        },
        viewAreap3: function(area){
            this.generalp3 = false;
            this.areap3 = true;
            this.cursosp3 = false;
            this.usersp3 = false;

            this.actp3 = area;
        },
        viewCursop3: function(curse){
            this.generalp3 = false;
            this.areap3 = false;
            this.cursosp3 = true;
            this.usersp3 = false;

            this.actp3 = curse;
            // this.usuarios = AQUI LO DE LA API
        },
        viewUserp3: function(curse){
            this.generalp3 = false;
            this.areap3 = false;
            this.cursosp3 = false;
            this.usersp3 = true;

            this.actp3 = curse;

            // this.usuarios = Aqui los valores de la variable para setear
            setTimeout(function(){
                $('.circlechart').circlechart();
            }, 150);
        },
        closep3: function(){
            this.generalp3 = false;
            this.areap3 = true;
            this.cursosp3 = false;
            this.usersp3 = false;
        },
        closeDireccionp3: function(){
            this.generalp3 = true;
            this.areap3 = false;
            this.cursosp3 = false;
            this.usersp3 = false;
        },
        closeUserp3: function(){
            this.generalp3 = false;
            this.cursosp3 = true;
            this.usersp3 = false;
        },
        changeOrderp3: function(){
            if(this.order){
                this.areaListp3.sort(function (a, b) {
                    if (a.name > b.name) {
                        return 1;
                    }
                    if (a.name < b.name) {
                        return -1;
                    }
                    return 0;
                });
                this.order = false;
            } else{
                this.areaListp3.sort(function (a, b) {
                    if (a.name < b.name) {
                        return 1;
                    }
                    if (a.name > b.name) {
                        return -1;
                    }
                    return 0;
                });
                this.order = true;
            }
        },
        changeOrderPorcentp3: function(){
            if(this.orderPorcentp3){
                this.areaList.sort(function (a, b) {
                    if (a.progress > b.progress) {
                        return 1;
                    }
                    if (a.progress < b.progress) {
                        return -1;
                    }
                    return 0;
                });
                this.orderPorcentp3 = false;
            } else{
                this.areaListp3.sort(function (a, b) {
                    if (a.progress < b.progress) {
                        return 1;
                    }
                    if (a.progress > b.progress) {
                        return -1;
                    }
                    return 0;
                });
                this.orderPorcentp3 = true;
            }
        },
        changeOrderCursosp3: function(){
            if(this.orderCursosp3){
                this.cursosListp3.sort(function (a, b) {
                    if (a.name > b.name) {
                        return 1;
                    }
                    if (a.name < b.name) {
                        return -1;
                    }
                    return 0;
                });
                this.orderCursosp3 = false;
            } else{
                this.cursosListp3.sort(function (a, b) {
                    if (a.name < b.name) {
                        return 1;
                    }
                    if (a.name > b.name) {
                        return -1;
                    }
                    return 0;
                });
                this.orderCursosp3 = true;
            }
        },
        changeOrderUserp3: function(){
            if(this.orderUserp3){
                this.usuariosp3.sort(function (a, b) {
                    if (a.name > b.name) {
                        return 1;
                    }
                    if (a.name < b.name) {
                        return -1;
                    }
                    return 0;
                });
                this.orderUserp3 = false;
            } else{
                this.usuariosp3.sort(function (a, b) {
                    if (a.name < b.name) {
                        return 1;
                    }
                    if (a.name > b.name) {
                        return -1;
                    }
                    return 0;
                });
                this.orderUserp3 = true;
            }
        },
        changeOrderGerenciap3: function(){
            if(this.orderGerenp3){
                this.usuariosp3.sort(function (a, b) {
                    if (a.gerencia > b.gerencia) {
                        return 1;
                    }
                    if (a.gerencia < b.gerencia) {
                        return -1;
                    }
                    return 0;
                });
                this.orderGerenp3 = false;
            } else{
                this.usuarios.sort(function (a, b) {
                    if (a.gerencia < b.gerencia) {
                        return 1;
                    }
                    if (a.gerencia > b.gerencia) {
                        return -1;
                    }
                    return 0;
                });
                this.orderGerenp3 = true;
            }
        },
        changeOrderAreap3: function(){
            if(this.orderAreap3){
                this.usuariosp3.sort(function (a, b) {
                    if (a.area > b.area) {
                        return 1;
                    }
                    if (a.area < b.area) {
                        return -1;
                    }
                    return 0;
                });
                this.orderAreap3 = false;
            } else{
                this.usuariosp3.sort(function (a, b) {
                    if (a.area < b.area) {
                        return 1;
                    }
                    if (a.area > b.area) {
                        return -1;
                    }
                    return 0;
                });
                this.orderAreap3 = true;
            }
        },
        changeOrderPorcent2: function(){
            if(this.orderPorcent2){
                this.usuarios.sort(function (a, b) {
                    if (a.progress > b.progress) {
                        return 1;
                    }
                    if (a.progress < b.progress) {
                        return -1;
                    }
                    return 0;
                });
                this.orderPorcent2 = false;
            } else{
                this.usuarios.sort(function (a, b) {
                    if (a.progress < b.progress) {
                        return 1;
                    }
                    if (a.progress > b.progress) {
                        return -1;
                    }
                    return 0;
                });
                this.orderPorcent2 = true;
            }
        }
    }
});

$('.circlechart').circlechart();