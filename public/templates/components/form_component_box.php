<div class="form_container question_container" question_cript="<?= $form_data['crypt']?>" parent_answer="<?=$form_data['answer_crypt']?>" form_crypt="<?=$form_data['form_data']?>">
<div class="form_container_header">
    <h4 class="question_text"><?= $form_data['value'] ?></h4>
</div>
    <div class="form_container_body">
        <div class="answers_box">
            <?php foreach($form_data['answers'] as $answer):  ?>
            <div class="one_answer" crypt="<?=$answer['crypt'] ?>">
                <div class="title"><?=$answer['tytle'] ?></div>
                <input type="<?=$answer['type'] ?>" name="<?= $answer['radio_name'] ?>" value="<?=$answer['type'] ?>" >
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="form_container_footer">
    <button type="submit" class="question_submit">שלך</button>
    <button type="submit" class="question_back">back</button>
    </div>
</div>
