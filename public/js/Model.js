class Model {
    sendAjax(ajaxObject, that=''){
       $.ajax({
           uri:'/smartform_client',
           type:'post',
           dataType:ajaxObject.dataType,
           data:{objectToSend:JSON.stringify(ajaxObject)},
           success:function (data) {
               if(ajaxObject.collBackFunction != undefined){
                   ajaxObject.collBackFunction(data, that)
               }
               if(ajaxObject.modelCollBackFunction != undefined){
                   ajaxObject.modelCollBackFunction(ajaxObject, data, that)
               }
           }
       })

    }
    getOneQuestionForm(requestObject, responseData, that){
        // if(responseData == "0") {
            let m = new Model(),
            ajaxObject = {
                'func': 'saveUserAnswersFormsObject',
                // 'collBackFunction': that.view.getOneQuestionForm,
                // 'modelCollBackFunction':that.model.getOneQuestionForm,
                'userAnswersObjectForSave':window.answersQuestionSaveObject,
                'dataType':'json'
            };
        window.answersQuestionSaveObject = {};
            m.sendAjax(ajaxObject)
        // }
    }
    simpleFormSaveBuildObject(that){
        let formContainer = that.closest('.form_container'),
            questinnationCrypt = formContainer.attr('questinnation_crypt'),
            simpleFormCrypt = formContainer.attr('simple_form_crypt'),
            isBack = formContainer.attr('is_back')||'0',
            curentFormOrder = that.attr('form_order'),
            fields = {};
        $.each(formContainer.find('.form_container_body input'), function (key, val) {
            let curentInput = $(this),
                curentContainer = curentInput.closest('.form_element_box'),
                fieldCrypt = curentContainer.attr('field_crypt'),
                elementType = curentInput.attr('element_type'),
                elementTitle = curentContainer.find('h5').text();
            fields[fieldCrypt]={
                'title':elementTitle,
                'type':elementType,
                'value':curentInput.val()
            }
        });
        let formObj={
            'questinnationCrypt': questinnationCrypt,
            'simpleFormCrypt':simpleFormCrypt,
            'lastOrder': curentFormOrder,
            'fields':fields,
            'isBack': isBack
        }
        let ajaxObject = {
            'func': 'simpleFormSave',
            'formObj': formObj,
            'dataType':'json'
        }
        return ajaxObject;


    }
}