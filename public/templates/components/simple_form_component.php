<div class="simple_form_box">
  <div class="form_container" simple_form_crypt="<?=$form_data['crypt']?>" questinnation_crypt="<?= $form_data['questinnation_crypt'] ?>">
     <div class="form_container_header">
         <h3><?=$form_data['title']?></h3>
     </div>
      <div class="form_container_body">
          <?php foreach ($form_data['fields'] as $field_key => $field): ?>
            <div class="form_element_box" field_crypt="<?=$field_key?>">
                <h5><?=$field['title']?></h5>
                <input type="text" element_type="<?=$field['type']?>" placeholder="<?=$field['placeholder']?>">
            </div>
          <?php endforeach; ?>
      </div>
      <div class="form_container_footer ">
          <button class="simple_form_send" form_order="<?= $form_data['order']?>" up="<?= $form_data['up']?>">
              שלך
          </button>
          <button class="simple_form_back" form_order="<?= $form_data['order']?>" up="<?= $form_data['up']?>">
              back
          </button>
      </div>
  </div>
</div>