class View{
    constructor(){

    }
    getOneQuestionForm(data, that){
        if(data == '0'){
            $('.left_container .form_container').html('sended');
        //    TODO create Form PROCESSING ===============================
        }else {
            $('.left_container .form_container').html(data);
        }
    }
    simpleFormObjectSave(data, that){

    }

}