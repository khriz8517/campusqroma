var app = new Vue({
    el: '#app',
    data(){
        return {
            nombre: '',
            user: '',
            vistas: '',
            link: '',
            gestor: false,
            creado: '',
            estado_aprobacion: 0,
            comentarios: [],
            comentTxt: ''
        };
    },
    created(){
    },
    mounted(){
        this.obtenerQromateca();
        this.actualizarVistas();
        this.cargarComentarios();
    },
    methods: {
        obtenerQromateca: function() {
            let frm = new FormData();
            let uri = window.location.search.substring(1);
            let params = new URLSearchParams(uri);
            frm.append('id', params.get("id"));
            frm.append('request_type','obtenerQromateca');
            axios.post('../qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    if(response.data.status) {
                        let data = response.data.data;
                        this.nombre = data.nombre;
                        this.user = data.user;
                        this.vistas = data.vistas;
                        this.link = data.link;
                        this.gestor = data.gestor;
                        this.creado = data.creado;
                        this.estado_aprobacion = data.estado_aprobacion;
                    } else {
                        window.location.href = 'index.php';
                    }
                });
        },
        actualizarVistas: function() {
            let frm = new FormData();
            let uri = window.location.search.substring(1);
            let params = new URLSearchParams(uri);
            frm.append('id', params.get("id"));
            frm.append('request_type','actualizarVistas');
            axios.post('../qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    console.log('updated');
                });
        },
        cargarComentarios: function() {
            let frm = new FormData();
            let uri = window.location.search.substring(1);
            let params = new URLSearchParams(uri);
            frm.append('id', params.get("id"));
            frm.append('request_type','cargarComentarios');
            axios.post('../qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    let data = response.data.data;
                    let comentarios = Array();

                    if(data) {
                        Object.keys(data).forEach(key => {
                            let dataVal = data[key];
                            let id = dataVal.id;
                            let user = dataVal.user;
                            let comentario = dataVal.comentario;
                            let date = dataVal.date;
                            let comentario_user_id = dataVal.comentario_user_id;
                            let current_user_id = dataVal.current_user_id;

                            let newElem = {
                                'id': id,
                                'comentario': comentario,
                                'user': user,
                                'date': date,
                                'comentario_user_id': comentario_user_id,
                                'current_user_id': current_user_id
                            };
                            comentarios.push(newElem);
                        });
                        this.comentarios = comentarios;
                    }
                });
        },
        aprobar: function() {
            let frm = new FormData();
            let uri = window.location.search.substring(1);
            let params = new URLSearchParams(uri);
            frm.append('id', params.get("id"));
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
        desaprobar: function() {
            document.querySelector(".back-des").style.display = "flex";
        },
        desaprobarAction: function() {
            let frm = new FormData();
            let uri = window.location.search.substring(1);
            let params = new URLSearchParams(uri);
            frm.append('id', params.get("id"));
            frm.append('request_type','desaprobar');
            axios.post('../qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    location.reload();
                });
        },
        comentar: function() {
            if(this.comentTxt === '') {
                alert('Debe agregar contenido en el comentario');
                return false;
            }
            let frm = new FormData();
            let uri = window.location.search.substring(1);
            let params = new URLSearchParams(uri);
            frm.append('qromatecaId', params.get("id"));
            frm.append('comentTxt', this.comentTxt);
            frm.append('request_type','crearComentario');
            axios.post('../qroma_front/api/ajax_controller_qroma.php', frm)
                .then((response) => {
                if(response.data.status) {
                    alert('Comentario agregado satisfactoriamente');
                    location.reload();
                }
            });
        },
        eliminarComentario: function(id) {
            let frm = new FormData();
            frm.append('id', id);
            frm.append('request_type','eliminarComentario');
            axios.post('../qroma_front/api/ajax_controller_qroma.php',frm)
                .then((response) => {
                    alert('El comentario fue eliminado satisfactoriamente');
                    location.reload();
                });
        },
        closeModal: function(){
            document.querySelector(".back").style.display = "none";
            document.querySelector(".back-des").style.display = "none";
        },
        continuar: function(){
            location.reload();
        }
    }
});