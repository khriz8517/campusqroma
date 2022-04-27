var app = new Vue({
    el: '#app_seguimiento',
    delimiters: ['{(', ')}'],
    data(){
        return{
            cursosList: [],
            compromisoList:[],
            usuarios: [],
            act: {},
            userRole: '',
            isAdmin: '',
            // ordenamiento
            order: true,
            orderFecha: true,
            // order vista usuario
            orderUserName: true,
            orderUserEmpresa: false,
            orderUserFecha: false,
            // vistas
            users: false,
            compromiso: false,
            general: true,

            listPorcent: {},
            searchCursos: '',
            searchCompromiso: '',
            searchAlumnos: '',
            dateFirma: '',
            // searchUsers:[],

            selectUser: false,
            menu: false,
        }
    },
    created(){
        this.sizeWeb();
        window.onresize = this.sizeWeb;
    },
    mounted(){
        this.seguimientoFirmaPanel1();
        this.obtenerUsuario();
    },
    computed: {
        searchCurse: function (){
            return this.cursosList.filter((item) => item.name.includes(this.searchCursos));
        },
        searchCompromise: function (){
            return this.compromisoList.filter((item) => item.fecha.includes(this.searchCompromiso));
        },
        searchUsers: function(){
            return this.usuarios.filter((item) => item.name.includes(this.searchAlumnos));
        },
    },
    methods: {
        seguimientoFirmaPanel1: function() {
            let frm = new FormData();
            frm.append('request_type','seguimientoFirmaPanel1');
            axios.post('../local/qroma_front/api/ajax_controller_qroma.php', frm)
                .then((response) => {
                    let data = response.data.data;
                    let arrResult = Array();

                    Object.keys(data).forEach(key => {
                        let dataVal = data[key];
                        let name = dataVal.name;
                        let id = dataVal.id;
                        let compromiso = dataVal.compromiso;

                        let newElem = {
                            'name': name,
                            'id': id,
                            'compromiso': compromiso,
                        };
                        arrResult.push(newElem);
                    });
                    this.cursosList = arrResult;
                });
        },
        seguimientoFirmaPanel2: function(cursoId) {
            let frm = new FormData();

            frm.append('courseId', cursoId);
            frm.append('request_type','seguimientoFirmaPanel2');
            axios.post('../local/qroma_front/api/ajax_controller_qroma.php', frm)
                .then((response) => {
                    let data = response.data.data;
                    let arrResult = Array();

                    Object.keys(data).forEach(key => {
                        let dataVal = data[key];
                        let fecha = dataVal.fecha;
                        let compromiso = dataVal.compromiso;

                        let newElem = {
                            'fecha': fecha,
                            'compromiso': compromiso,
                        };
                        arrResult.push(newElem);
                    });
                    this.compromisoList = arrResult;
                });
        },
        seguimientoFirmaPanel3: function(cursoId, fecha) {
            let frm = new FormData();

            frm.append('courseId', cursoId);
            frm.append('fecha', fecha);
            frm.append('request_type','seguimientoFirmaPanel3');
            axios.post('../local/qroma_front/api/ajax_controller_qroma.php', frm)
                .then((response) => {
                    let data = response.data.data;
                    let arrResult = Array();

                    Object.keys(data).forEach(key => {
                        let dataVal = data[key];
                        let name = dataVal.name;
                        let empresa = dataVal.empresa;
                        let fecha = dataVal.fecha;

                        let newElem = {
                            'name': name,
                            'empresa': empresa,
                            'fecha': fecha
                        };
                        arrResult.push(newElem);
                    });
                    this.usuarios = arrResult;
                });
        },
        searchName: function(){
            if(this.searchAlumnos != ''){
                this.searchUsers = this.usuarios.filter((item) => item.name.includes(this.searchAlumnos));
            } else{
                this.searchUsers = this.usuarios;
            }
        },
        sizeWeb: function(){
            if (window.innerWidth < 768)
                this.menu = false;
            else
                this.menu = true;
        },
        changeOrder: function(){
            this.order = this.order ? false : true;
            // aqui adjuntar el codigo de ordenamiendo desde la api
        },
        obtenerUsuario: function(){
            let frm = new FormData();
            frm.append('request_type','obtenerUsuario');
            axios.post('../local/qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    // handle success
                    let data = response.data.data;
                    this.isAdmin = data.isAdmin;
                    this.userRole = data.role;
                });
        },
        // ver lista de compromisos
        viewCompromiso: function(name, id){
            this.general = false;
            this.compromiso = true;
            this.users = false;
            this.act = {
                name: name,
                courseId: id
            };
            this.seguimientoFirmaPanel2(id);
        },
        // ver lista de usuarios
        viewUser: function(date){
            this.general = false;
            this.compromiso = false;
            this.users = true;
            console.log("view users");
            this.dateFirma = date;
            this.seguimientoFirmaPanel3(this.act.courseId, date);
            // this.searchUsers = this.usuarios;
        },
        closeCompriso: function(){
            this.general = true;
            this.users = false;
            this.compromiso = false;
        },
        closeUsers: function(){
            this.general = false;
            this.users = false;
            this.compromiso = true;
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
        changeOrder: function(){
            if(this.order){
                this.cursosList.sort(function (a, b) {
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
                this.cursosList.sort(function (a, b) {
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
        changeOrderFecha: function(){
            if(this.orderFecha){
                this.compromisoList.sort(function (a, b) {
                    if (a.fecha > b.fecha) {
                        return 1;
                    }
                    if (a.fecha < b.fecha) {
                        return -1;
                    }
                    return 0;
                });
                this.orderFecha = false;
            } else{
                this.compromisoList.sort(function (a, b) {
                    if (a.fecha < b.fecha) {
                        return 1;
                    }
                    if (a.fecha > b.fecha) {
                        return -1;
                    }
                    return 0;
                });
                this.orderFecha = true;
            }
        },
        // cambiar orden vista usuario
        changeOrderName: function(){
            if(this.orderUserName){
                this.usuarios.sort(function (a, b) {
                    if (a.name > b.name) {
                        return 1;
                    }
                    if (a.name < b.name) {
                        return -1;
                    }
                    return 0;
                });
                this.orderUserName = false;
            } else{
                this.usuarios.sort(function (a, b) {
                    if (a.name < b.name) {
                        return 1;
                    }
                    if (a.name > b.name) {
                        return -1;
                    }
                    return 0;
                });
                this.orderUserName = true;
            }
        },
        changeOrderEmpresa: function(){
            if(this.orderUserEmpresa){
                this.usuarios.sort(function (a, b) {
                    if (a.empresa > b.empresa) {
                        return 1;
                    }
                    if (a.empresa < b.empresa) {
                        return -1;
                    }
                    return 0;
                });
                this.orderUserEmpresa = false;
            } else{
                this.usuarios.sort(function (a, b) {
                    if (a.empresa < b.empresa) {
                        return 1;
                    }
                    if (a.empresa > b.empresa) {
                        return -1;
                    }
                    return 0;
                });
                this.orderUserEmpresa = true;
            }
        },
        changeOrderFechaUsuario: function(){
            if(this.orderUserFecha){
                this.usuarios.sort(function (a, b) {
                    if (a.fecha > b.fecha) {
                        return 1;
                    }
                    if (a.fecha < b.fecha) {
                        return -1;
                    }
                    return 0;
                });
                this.orderUserFecha = false;
            } else{
                this.usuarios.sort(function (a, b) {
                    if (a.fecha < b.fecha) {
                        return 1;
                    }
                    if (a.fecha > b.fecha) {
                        return -1;
                    }
                    return 0;
                });
                this.orderUserFecha = true;
            }
        }
    }
});