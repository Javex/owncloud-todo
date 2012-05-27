jQuery(document).ready(function(){
    ListApp.init();
});

var ListApp = {
    
    DEBUG: false,
    taskURL: '?app=todo&getfile=ajax/task.load.php&format=html&list_id=',
    deleteTaskURL: '?app=todo&getfile=ajax/task.delete.php&format=html&task_id=',
    listURL: '?app=todo&getfile=ajax/list.load.php&format=html',
    deleteListURL: '?app=todo&getfile=ajax/list.delete.php&format=html&list_id=',
    messages: {
        malformedJSONString: "The data recieved was malformed, please check the server returns the correct data"  
    },
    strings: {
        defaultListName: "New List"
    },
    listLoading: false,
    
    init: function() {
        ListApp.bindEvents();
        ListApp.triggerClick();
    },
    
    bindEvents: function() {
        
        //for selecting a list and loading it
        jQuery("li.list_item").off("click");
        jQuery("li.list_item").each(function(){
            jQuery(this).click(ListApp.listClick);
        });
        
        //adding a task
        jQuery(".add_task").off("submit");
        jQuery(".add_task").each(function(){
            jQuery(this).submit(ListApp.addTask);
        });
        
        //updating task (change text, mark it done/undone etc...)
        jQuery("li.task_item input[type=checkbox]").off("change");
        jQuery("li.task_item input[type=checkbox]").each(function(){
            jQuery(this).change(ListApp.updateTask);
        });
        
        //delete a task
        jQuery(".delete_task").off("click");
        jQuery(".delete_task").each(function() {
            jQuery(this).click(ListApp.deleteTask);
        });
        
        //pressing keys whilst adding a new list
        jQuery("#task_addlist_form input[name='list[name]']").off("keydown")
        jQuery("#task_addlist_form input[name='list[name]']").keydown(function(e){
            switch(e.keyCode) {
                case 27:
                    ListApp.hideAddListInput();
                    break;
                case 13:
                    if(!ListApp.listLoading)
                        ListApp.addList();
                    break;
                default:
                    return;
            }
        });
        
        //showing a field to add a new list
        jQuery("#task_controls button#tasks_addlist").off("click");
        jQuery("#task_controls button#tasks_addlist").click(ListApp.showAddListInput);
        
        jQuery("form#task_addlist_form").off("submit");
        jQuery("form#task_addlist_form").submit(ListApp.addList);
        
        jQuery(".delete_list").off("click");
        jQuery(".delete_list").each(function(){
           jQuery(this).click(ListApp.deleteList);
        });
        
        jQuery("li.task_item").off("dblclick");
        jQuery("li.task_item").each(function(){
            jQuery(this).dblclick(ListApp.changeTaskText);
        });
        
        jQuery("li.task_item input").off("blur");
        jQuery("li.task_item input").each(function() {
            jQuery(this).blur(ListApp.hideTaskTextInput);
            jQuery(this).keydown(function(e) {
                switch(e.keyCode) {
                    case 27:
                        ListApp.hideTaskTextInput();
                        break;
                    case 13:
                        //ListApp.updateTask();
                        break;
                }
            })
        });
        
        jQuery("li.task_item form").each(function() {
            jQuery(this).submit(ListApp.updateTask);
        })
        
        jQuery(".dismiss_message").each(function() {
            jQuery(this).click(ListApp.dismissMessage);
        })
        
        
        ListApp.bindTipsy();
    },
    
    bindTipsy: function() {
        jQuery(".display_tipsy").tipsy({fade:true});
        jQuery(".delete_task").tipsy({gravity : 'e'});
        jQuery("#tasks_addlist").tipsy({gravity : 's'});
    },
    
    triggerClick: function() {
        jQuery("li.list_item[list_id="+ListApp.getListID()+"]").trigger('click');
    },
    
    getListID: function(href) {
        if(href == undefined)
            href = location.hash;
        return href.match(/\/list\/(\d)+/)[1];
    },
    
    getTaskID: function(object) {
        return object.parents("form.task_item_form,li.task_item").first().find("input.task_id_input").val();
    },
    
    listClick: function() {
        var href = jQuery(this).find("a").first().attr('href');
        var list_id = ListApp.getListID(href);
        ListApp.loadTasks(list_id);
    },
    
    loadTasks: function(list_id) {
        ListApp.displayLoadingRight();
        if(list_id == undefined)
            list_id = ListApp.getListID();
        jQuery.get(ListApp.taskURL+list_id, null, ListApp.onTasksLoaded);
        
    },
    
    onTasksLoaded: function(data) {
        jQuery("#rightcontent").html(data);
        ListApp.bindEvents();
        ListApp.makeListActive();
        jQuery("#add_task_input").focus();
    },
    
    addTask: function() {
        var form = jQuery("#new_task");
        jQuery.post(form.attr('target'), form.serialize(), ListApp.onTaskAdded);
        ListApp.displayLoadingRight();
        return false;
        
    },
    
    onTaskAdded: function(data) {
        data = jQuery.parseJSON(data);
        if(data) {
            if(data["success"]) {
                (ListApp.DEBUG) ? console.log("Everything went fine, reloading task list") : null;
                ListApp.loadTasks(ListApp.getListID());
            }   
        } else {
            ListApp.error(ListApp.messages.malformedJSONString);
        }
    },
    
    error: function(message) {
        jQuery("#messagebox").addClass("error");
        ListApp.message(message);
    },
    
    message: function(message) {
        jQuery("#messagetext").text(message);
        jQuery("#messagebox").slideDown();
    },
    
    dismissMessage: function() {
        jQuery("#messagebox").slideUp('400', function(){
            jQuery("#messagetext").text("");
        });
    },
    
    updateTask: function() {
        jQuery(this).tipsy("hide");
        ListApp.displayLoadingRight();
        var form = jQuery(this);
        if(jQuery(this)[0].tagName != "FORM")
            form = jQuery(this).parents("form.task_item_form").first();
        jQuery.post(form.attr('target'), form.serialize(), ListApp.onTaskUpdated);
        return false;
    },
    
    changeTaskText: function () {
        jQuery(this).find("span.task_text").hide();
        jQuery(this).find("input.task_text").show().focus();
    },
    
    hideTaskTextInput: function() {
        jQuery(this).hide();
        jQuery(this).parents("form").first().find("span.task_text").show();
    },
    
    onTaskUpdated: function(data) {
        data = jQuery.parseJSON(data);
        if(data) {
            if(data["success"]) {
                if(data["message"])
                    ListApp.message(data["message"]);
                (ListApp.DEBUG) ? console.log("Everything went fine, reloading task list") : null;
                ListApp.loadTasks(ListApp.getListID());
            }
        } else {
            ListApp.error(ListApp.messages.malformedJSONString);
        }
    },
    
    deleteTask: function() {
        jQuery(this).tipsy("hide");
        ListApp.displayLoadingRight();
        var task_id = ListApp.getTaskID(jQuery(this));
        jQuery.get(ListApp.deleteTaskURL+task_id, null, ListApp.onTaskUpdated);
    },
    
    showAddListInput: function() {
        jQuery("#task_addlist_form").show();
        jQuery("#task_addlist_form input[name='list[name]']").focus();
        return false;
    },
    
    hideAddListInput: function() {
        console.log(jQuery("#task_addlist_form input[name='list[name]']").val(ListApp.strings.defaultListName));
        var input = jQuery("#task_addlist_form input[name='list[name]']").clone(false);
        jQuery("#task_addlist_form input[name='list[name]']").remove();
        jQuery("#task_addlist_form").prepend(input);
        jQuery("#task_addlist_form").hide();
        ListApp.bindEvents();
    },
    
    addList: function() {
        if(ListApp.listLoading)
            return false;
        ListApp.listLoading = true;
        var form = jQuery("form#task_addlist_form");
        jQuery.post(form.attr('target'), form.serialize(), ListApp.onListAdded);
        return false;
    },
    
    onListAdded: function(data) {
        data = jQuery.parseJSON(data);
        if(data) {
            if(data["success"]) {
                (ListApp.DEBUG) ? console.log("Everything went fine, reloading list list") : null;
                ListApp.loadLists();
             } else {
                 ListApp.listLoading = false;
             }   
        } else {
            ListApp.error(ListApp.messages.malformedJSONString);
            ListApp.listLoading = false;
        }
    },
    
    loadLists: function() {
        jQuery.get(ListApp.listURL, null, ListApp.onListsLoaded);
    },
    
    onListsLoaded: function(data) {
        jQuery("#leftcontent").html(data);
        ListApp.bindEvents();
        ListApp.listLoading = false;
    },
    
    updateList: function() {
        
    },
    
    onListUpdated: function(data) {
        data = jQuery.parseJSON(data);
        if(data) {
            if(data["success"]) {
                (ListApp.DEBUG) ? console.log("Everything went fine, reloading task list") : null;
                ListApp.loadLists();
            }
        } else {
            ListApp.error(ListApp.messages.malformedJSONString);
        }
    },
    
    deleteList: function() {
        var list_href = jQuery(this).parents("form").find("a.list_select").attr('href');
        var list_id = ListApp.getListID(list_href);
        jQuery.get(ListApp.deleteListURL+list_id, null, ListApp.onListUpdated);
    },

    makeListActive: function() {
        var list_id = ListApp.getListID();
        jQuery("li.list_item").removeClass("active");
        jQuery("li.list_item[list_id="+list_id+"]").addClass("active");
    },
    
    displayLoadingRight: function() {
        var circles = jQuery("#floatingCirclesG");
        jQuery("#rightcontent").html(circles);
        circles.show();
    }
};