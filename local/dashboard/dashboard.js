var app = new Vue({
    el: '#app',
    delimiters: ['{(', ')}'],
    data(){
        return{
            menu: false,
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
            gereniasList: [
                {name:"Gerencia de mantenimiento"},
                {name:"Gerencia de operaciones"},
                {name:"Gerencia GH"},
                {name:"Gerencia finanzas"},
                {name:"Gerencia general"},
                {name:"Gerencia TI"},
            ],
            areasList: [
                {name:"Contabilidad"},
                {name:"Operaciones"},
                {name:"Auditoria"},
                {name:"Administracion y finanzas"},
                {name:"TI"},
                {name:"Mantenimiento"},
                {name:"logistica"}
            ],
            zonasList:[
                {name:"Zona Este"},
                {name:"Zona Este"},
                {name:"Corporativo"},
            ],
            act: {},
            order: true,
            orderUser: true,
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
            loadingUsers: false
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
    },
    computed: {
        searchCurse: function (){
            return this.cursosList.filter((item) => item.name.includes(this.searchCursos));
        },
        // searchUsers: function(){
        //   return this.usuarios.filter((item) => item.name.includes(this.searchAlumnos));
        // },
    },
    methods: {
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
        sizeWeb: function(){
            if (window.innerWidth < 768)
                this.menu = false;
            else
                this.menu = true;
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

        //Seguimiento

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
                        let gerencia = 'test gerencia';
                        let area = 'test area';
                        let zona = 'test zona';
                        let progress = dataVal.progress;

                        let newElem = {
                            'id': id,
                            'name': name,
                            'gerencia': gerencia,
                            'area': area,
                            'zona': zona,
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

    }
});
