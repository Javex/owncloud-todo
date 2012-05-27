<ul class="list">
    <?php foreach($_['lists'] as $list): ?>
    <li class="list_item" list_id="<?php echo $list->id?>">
        <form class="list_item_form" target="?app=todo&getfile=ajax/list.update.php&format=html" method="POST">
            <a class="list_select" href="#!/list/<?php echo $list->id;?>" title="<?php echo $list->name?>"><?php echo $list->name ?></a>
            <!-- <span class="delete_list">(delete)</span>-->
            <div class="controls">
                <button class="delete_list svg display_tipsy" title="Delete List"></button>
            </div>
            <input class="list_id_input" type="hidden" name="list[id]" value="<?php echo $list->id?>" />
        </form>
    </li>
    <?php endforeach; ?>
    <form method="POST" target="?app=todo&getfile=ajax/list.add.php&format=html" id="task_addlist_form">
        <input type="text" name="list[name]" value="New List" />
        <input type="submit" value="Add List" />
    </form>
    
</ul>