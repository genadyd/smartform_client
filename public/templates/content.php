<?php
/**
 * Created by PhpStorm.
 * User: genady
 * Date: 6/25/19
 * Time: 3:15 PM
 */
?>
<ul class="nav  navbar-expand-md bg-dark navbar-dark">
    <li class="nav-item">
        <a class="nav-link active text-light" data-toggle="pill" href="#home">Home</a>
    </li>
</ul>
<div class=" container-fluid home_container main_wrapper " >
    <div class="row">
        <div class="col-3 rigth_container">
            <ul class="forms_list">
            <?php foreach ($page_data['formsList'] as $form): ?>
            <li class="one_form" crypt="<?=$form['crypt']?>"><?=$form['form_name']?></li>
            <?php endforeach ?>
            </ul>

        </div>
        <div class="col-9 left_container">
            <div class="form_container">

            </div>
        </div>

    </div>
</div>



