var app = new Vue({
    el: '#app',
    data(){
        return{
            menu: false,
            totalCourses : 0,
            cursos: [],
            desarrollo:[]
        }
    },
    created(){
        this.sizeWeb();
        window.onresize = this.sizeWeb;

    },
    mounted(){
        this.getSearchedCourses();
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
            let catId = 13;
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