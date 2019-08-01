$(function () {
    let controller = new Controller();
})

class Controller {
    constructor() {
        this.view = new View();
        this.model = new Model();
        window.answersQuestionSaveObject = {};
        this.getOneQuestionForm();
        this.questionSend();
        this.simpleFormSave();

    }
getOneQuestionForm(){
        let that = this;
      $('.rigth_container').on('click','.one_form', function () {
          let formCrypt = $(this).attr('crypt')
          let ajaxObject = {
              // 'func': 'getOneQuestionForm',
              'func': 'getOneSimpleForm',
              'collBackFunction': that.view.getOneQuestionForm,
              'questinnationCrypt':formCrypt,
              'up':'1',
              'parentAnswerCrypt':0,
              'dataType':'html'
          }
          that.model.sendAjax(ajaxObject, $(this));
      })
}
questionSend(){
       let that = this;
  $('.left_container').on('click', '.form_container_footer .question_submit', function () {
      let formContainer = $(this).closest('.question_container'),
          selectedElement = formContainer.find(':input:checked'),
          questionCrypt= formContainer.attr('question_cript'),
          formCrypt= formContainer.attr('form_crypt'),
          selectedAnswerCrypt= selectedElement.closest('.one_answer').attr('crypt'),
          perQuestionParam = 0,/*this param display is binded or no answers to question*/
          ajaxObject = {
              'func': 'getOneQuestionForm',
              'collBackFunction': that.view.getOneQuestionForm,
              'modelCollBackFunction':that.model.getOneQuestionForm,
              'questionCrypt': questionCrypt,
              'questinnationCrypt': formCrypt,
              'parentAnswerCrypt': selectedAnswerCrypt,
              'dataType':'html'
          };
          if(window.answersQuestionSaveObject['questionAnswers']== undefined) {
              window.answersQuestionSaveObject['questionAnswers'] = {};
          }
      window.answersQuestionSaveObject['formCrypt']=formCrypt;
      window.answersQuestionSaveObject['questionAnswers'][questionCrypt]=selectedAnswerCrypt;
     that.model.sendAjax(ajaxObject, $(this));


  })
}
simpleFormSave(){
    let that = this;
    $('.left_container').on('click', '.simple_form_box .form_container_footer .simple_form_send', function () {
       let ajaxObject = that.model.simpleFormSaveBuildObject($(this));
       ajaxObject['collBackFunction']= that.view.simpleFormObjectSave,
           console.log(ajaxObject);
        that.model.sendAjax(ajaxObject, $(this));

        let ajaxObjectToNextFormShow = {
            'questinnationCrypt':ajaxObject.formObj.questinnationCrypt,
            // 'lastOrder':ajaxObject.formObj.lastOrder,
            'func': 'getOneSimpleForm',
            'collBackFunction': that.view.getOneQuestionForm,
            'dataType':'html',
            'simpleFormObj':{
                'lastOrder':ajaxObject.formObj.lastOrder,
                'up':$(this).attr('up')
            }
        }
        that.model.sendAjax(ajaxObjectToNextFormShow, $(this));
    });
}
}

// let controller = new Controller();