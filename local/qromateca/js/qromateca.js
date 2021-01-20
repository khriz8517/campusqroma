var app = new Vue({
    el: '#app',
    data(){
        return{
            menu: false,
            tabActive: 1,
            qromaType: 1,
            disabledButton: 0,
            firstpage: true,
            secondpage: false,
            thirdpage: false,
            desarrollo: [],
            nombre: '',
            link: '',
            qromaFile: {},
            qromaFileDoc: {},
        };
    },
    created(){
        this.sizeWeb();
        window.onresize = this.sizeWeb;

    },
    mounted(){
        this.subCategoryFormat();
        this.obtenerQromatecas();
    },
    methods: {
        changeFiles: function () {
            this.qromaFile = this.$refs.miarchivo.files[0];
        },
        changeFiles2: function () {
            this.qromaFileDoc = this.$refs.miarchivo2.files[0];
        },
        share: function () {
            if(this.qromaType == 1) {
                if(this.nombre === '' || this.qromaFile.name == undefined || this.qromaFileDoc.name == undefined) {
                    alert('Debe agregar contenido en los campos vacíos');
                    return false;
                }
            } else {
                if(this.nombre === '' || this.link === '' || this.qromaFile.name == undefined) {
                    alert('Debe agregar contenido en los campos vacíos');
                    return false;
                }
            }

            this.disabledButton = 1;

            let frm = new FormData();
            frm.append('request_type','guardarQromateca');
            frm.append('nombre',this.nombre);
            frm.append('link',this.link);
            frm.append('type',this.qromaType);
            frm.append('qromaFile',this.qromaFile);
            frm.append('qromaFileDoc',this.qromaFileDoc);

            axios.post('../qroma_front/api/ajax_controller_qroma.php', frm, {
                header:{
                    'Content-Type' : 'multipart/form-data'
                }
            }).then((response) => {
                if(response.data.status) {
                    this.secondpage = false;
                    this.thirdpage = true;
                }
            });
        },
        selectType: function (event) {
          this.qromaType = event.target.value;
          this.firstpage = false;
          this.secondpage = true;
        },
        obtenerQromatecas: function() {
            let frm = new FormData();
            frm.append('request_type','obtenerQromatecas');
            axios.post('../qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    let data = response.data.data;
                    let qromatecas = Array();

                    Object.keys(data).forEach(key => {
                        let dataVal = data[key];
                        let id = dataVal.id;
                        let nombre = dataVal.nombre;
                        let img = dataVal.img;
                        let icon = dataVal.icon;
                        let user = dataVal.user;
                        let vistas = dataVal.vistas;
                        let gestor = dataVal.gestor;
                        let estado_aprobacion = dataVal.estado_aprobacion;
                        let cant_comentarios = dataVal.cant_comentarios;

                        let newElem = {
                            'id': id,
                            'nombre': nombre,
                            'img': img,
                            'icon': icon,
                            'user': user,
                            'vistas': vistas,
                            'gestor': gestor,
                            'estado_aprobacion': estado_aprobacion,
                            'cant_comentarios': cant_comentarios
                        };
                        qromatecas.push(newElem);
                    });
                    this.desarrollo = qromatecas;
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
        aprobar: function(id) {
            let frm = new FormData();
            frm.append('id', id);
            frm.append('request_type','aprobar');
            axios.post('../qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    if(response.data.status) {
                        document.querySelector(".back").style.display = "flex";
                    } else {
                        alert('Hubo un error en la aprobación');
                    }
                });
        },
        desaprobar: function(id) {
            document.querySelector(".back-des").style.display = "flex";
            document.querySelector(".back-des").id = id;
        },
        desaprobarAction: function() {
            let frm = new FormData();
            frm.append('id', document.querySelector(".back-des").id);
            frm.append('request_type','desaprobar');
            axios.post('../qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    location.reload();
                });
        },
        onChangeSort: function(event) {
            let id = event.target.value;
            let frm = new FormData();
            frm.append('id', id);
            frm.append('request_type','obtenerQromatecasSorted');
            axios.post('../qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    let data = response.data.data;
                    let qromatecas = Array();

                    Object.keys(data).forEach(key => {
                        let dataVal = data[key];
                        let id = dataVal.id;
                        let nombre = dataVal.nombre;
                        let img = dataVal.img;
                        let icon = dataVal.icon;
                        let user = dataVal.user;
                        let vistas = dataVal.vistas;
                        let gestor = dataVal.gestor;
                        let estado_aprobacion = dataVal.estado_aprobacion;
                        let cant_comentarios = dataVal.cant_comentarios;

                        let newElem = {
                            'id': id,
                            'nombre': nombre,
                            'img': img,
                            'icon': icon,
                            'user': user,
                            'vistas': vistas,
                            'gestor': gestor,
                            'estado_aprobacion': estado_aprobacion,
                            'cant_comentarios': cant_comentarios
                        };
                        qromatecas.push(newElem);
                    });
                    this.desarrollo = qromatecas;
                });
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
            this.tabActive = obj;
            $('#tabs-header .item').removeClass('active');
            $('#tabs-header .item:nth-child('+obj+')').addClass('active');
        },
        closeModal: function(){
            document.querySelector(".back").style.display = "none";
            document.querySelector(".back-des").style.display = "none";
        },
        closeModalAprobado: function () {
            document.querySelector(".back").style.display = "none";
            document.querySelector(".back-des").style.display = "none";
            location.reload();
        },
        continuar: function(){
            location.reload();
        }
    }
});