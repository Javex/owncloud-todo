<div id="messagebox">
    <div id="messagetext"></div>
    <button class="dismiss_message svg" title="Dismiss"></button></div>
<div id="leftcontent" class="leftcontent">
    <?php echo $this->inc("part.lists"); ?>
</div>
<div id="rightcontent" class="rightcontent" data-id="">
</div>
<div id="bottomcontent">
    <div id="task_controls">
        <form>
            <button class="svg" id="tasks_addlist" title="<?php echo $l->t('Add List'); ?>">
                <img class="svg" src="<?php echo OCP\Util::linkTo('contacts', 'img/contact-new.svg'); ?>" alt="<?php echo $l->t('Add List'); ?>"   />
            </button>
        </form>
    </div>
</div>
