<?php
/** @var Omeka_View $view */
$view = get_view();

?>
<div class="language-selector">
    <form method="GET" name="language_selector">
        <label for="language"><?= __('Select language') ?></label>
        <?= $view->formSelect( 'language', $current_language, [ 'onchange' => "this.form.submit()"],
            $languages
            )  ?>
    </form>
</div>