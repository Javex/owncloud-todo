<?php $break_drawn = false;?>
<h1 id="list_title"><?php echo $_['list']->name?></h1>

<form class="add_task" target="?app=todo&list_id=<?php echo $_['list_id']?>&getfile=ajax/task.add.php&format=html" method="POST" style="" id="new_task">
    <input type="text" name="task[text]" id="add_task_input" placeholder="Enter your new task here..."/>
    <!--<input type="submit" name="add_task" value="Add Task" />-->
    <input type="hidden" name="task[list]" value="<?php echo $_['list_id']?>" />
</form>
<ul class="tasks">
    <?php foreach($_['tasks'] as $task): ?>
    <?php if($task->done && !$break_drawn):?>
    <li class="done_break">
        <hr class="done_break" />
    </li>
    <?php $break_drawn = true; ?>
    <?php endif; ?>
    <li class="task_item <?php if($task->done) echo "task_done"?>" task_id="<?php echo $task->id?>">
        <form class="task_item_form" task_id="<?php echo $task->id?>" target="?app=todo&list_id=<?php echo $_['list_id']?>&getfile=ajax/task.update.php&format=html" method="POST">
            <input class="display_tipsy" title="Mark task as done" type="checkbox" name="task[done]" value="1" <?php if($task->done) echo "checked"?>/>
            <span class="task_text"><?php echo $task->text ?></span>
            <input class="task_text" name="task[text]" value="<?php echo $task->text?>" />
            <input class="hidden" type="submit" name="sumbit" value="Submit" />
            <div class="controls">
                <button class="delete_task svg" title="Delete Task"></button>
            </div>
            <input class="task_id_input" type="hidden" name="task[id]" value="<?php echo $task->id?>" />
            <input class="task_list_input" type="hidden" name="task[list]" value="<?php echo $task->list?>" />
        </form>
    </li>
    <?php endforeach; ?>
</ul>
<?php echo $this->inc("part.ajaxloader")?>