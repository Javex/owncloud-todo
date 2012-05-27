<?php
class OC_Todo_Todo {
    
    static $installed;
    
    public static function getLists() {
        $query = OC_DB::prepare("SELECT * FROM *PREFIX*todo_lists");
        $result = $query->execute();
        $return = array();
        while($item = $result->fetchRow())
        {
            $item = (object)$item;
            $return[$item->id] = $item;
        }
        
        return $return;
    }
    
    public static function getList($list_id = 0) {
        if(!$list_id)
            $list_id = self::getDefaultListID ();
        $query = OC_DB::prepare("SELECT * FROM *PREFIX*todo_lists WHERE id=?");
        $result = $query->execute(array($list_id));
        if(!$result)
            return false;
        return (object)$result->fetchRow();
    }
    
    public static function getItems($list_id = 1) {
        $query = OC_DB::prepare("SELECT * FROM *PREFIX*todo_entries WHERE list=? ORDER BY done ASC, id ASC");
        $result = $query->execute(array($list_id));
        $items = array();
        while($item = $result->fetchRow())
        {
            $item = (object)$item;
            $items[$item->id] = $item;
        }
        
        return $items;
    }
    
    
    public static function addTask($task) {
        $task = self::setTaskDefaults((object)$task);
        $task = self::prepareTaskAsArray($task);
        $query = OC_DB::prepare("INSERT INTO *PREFIX*todo_entries (text, description, list, due_date, done, date_done) VALUES (?,?,?,?,?,?)");
        if($query->execute($task))
            return true;
        else 
            return false;
    }
    
    public static function updateTask($task) {
        $query = OC_DB::prepare("SELECT * FROM *PREFIX*todo_entries WHERE id=?");
        $result = $query->execute(array($task["id"]));
        if(!$result)
            return false;
        else {
            $old_task = $result->fetchRow();
            foreach($task as $key=>$value)
            {
                $old_task[$key] = $value;
            }
            $query = OC_DB::prepare("UPDATE *PREFIX*todo_entries SET text=?, description=?, list=?, due_date=?, done=?, date_done=? WHERE id = ?");
            $old_task = self::prepareTaskAsArray($old_task);
            $old_task[] = $task["id"];
            if($query->execute($old_task))
                return true;
            else
                return false;
        }
    }
    
    public static function deleteTask($task_id) {
        $query = OC_DB::prepare("DELETE FROM *PREFIX*todo_entries WHERE id=?");
        if($query->execute(array($task_id)))
            return true;
        else
            return false;
    }
    
    public static function getTask($id = 0) {
        
        if(!$id) {
            $task = self::setTaskDefaults();
        } else {
            $query = OC_DB::prepare("SELECT * FROM *PREFIX*todo_entries WHERE id = ?");
            $result = $query->execute(array($id));
            $task = (object)$result->fetchRow();
        }
        return $task;
    }
    
    
    public static function getDefaultListID() {
        return 1;
    }

    public static function addList($list){
        $list = self::setListDefaults((object)$list);
        $list = self::prepareListAsArray($list);
        $query = OC_DB::prepare("INSERT INTO *PREFIX*todo_lists (name, public, default_list) VALUES (?,?,?)");
        if($query->execute($list))
            return true;
        else
            return false;
    }
    
    public static function updateList($list) {
        $query = OC_DB::prepare("SELECT * FROM *PREFIX*todo_lists WHERE id=?");
        $result = $query->execute(array($list["id"]));
        if(!$result)
            return false;
        
        $old_list = $result->fetchRow();
        foreach($list as $key=>$value) {
            $old_list[$key] = $value;
        }
        $query = OC_DB::prepare("UPDATE *PREFIX*todo_lists SET name=?, public=?, default_list=? WHERE id=?");
        $old_list = self::prepareListAsArray($old_list);
        $old_list[] = $list["id"];
        if($query->execute($old_list))
            return true;
        else
            return false;
    }
    
    public static function deleteList($list_id) {
        $query = OC_DB::prepare("DELETE FROM *PREFIX*todo_lists WHERE id=?");
        if($query->execute(array($list_id)))
            return true;
        else
            return false;
    }
    
    
    public static function setDefaults($item, $defaults) {
        foreach($defaults as $key=>$value) {
            if(!isset($item->$key))
                $item->$key = $value;
        }
        return $item;
    }
    
    public static function setListDefaults($list = null) {
        if($list === null)
            $list = new stdClass();
        $defaults = array(
            "name" => "",
            "public" => false,
            "default_list" => false
        );
        return self::setDefaults($list, $defaults);
    }
    
    public static function setTaskDefaults($task = null) {
        if($task === null)
            $task = new stdClass();
        $defaults = array(
            "text" => "",
            "description" => "",
            "list" => self::getDefaultListID(),
            "due_date" => 0,
            "done" => false,
            "date_done" => 0
        );
        return self::setDefaults($task, $defaults);
        
        return $task;
    }
    
    public static function prepareAsArray($item, $option_map) {
        $return_item = array();
        if(!is_object($item))
            $item = (object)$item;
        foreach($option_map as $key=>$value) {
            $return_item[$key] = $item->$value;
        }
        return $return_item;
    }
    
    public static function prepareListAsArray($list) {
        $option_map = array("name", "public", "default_list");
        return self::prepareAsArray($list, $option_map);
    }
    
    public static function prepareTaskAsArray($task) {
        $option_map = array("text", "description", "list", "due_date", "done", "date_done");
        return self::prepareAsArray($task, $option_map);
    }
    
    public static function install() {
        if(!self::$installed['lists']) {
            $query = OC_DB::prepare('CREATE TABLE "*PREFIX*todo_lists" (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(100),
                public TINYINT,
                default_list TINYINT
            )');
            $result = $query->execute();
            if(!$result)
            {
                //do error reporting, do not die!!
                OC_Log::write('todo', "Could not create table todo_lists", OC_Log::ERROR);
            } else {
                $query = OC_DB::prepare('INSERT INTO *PREFIX*todo_lists (id, name) VALUES (1, "Default List")');
                if(!$query->execute())
                    OC_Log::write('todo', "Could not create dummy data for lists. This is not critical but should be looked into!", OC_Log::ERROR);
            }
        }
        
        if(!self::$installed['entries']) {
            $query = OC_DB::prepare('CREATE TABLE "*PREFIX*todo_entries" (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                text TEXT,
                description MEDIUMTEXT,
                list INTEGER,
                due_date INTEGER,
                done TINYINT,
                date_done INTEGER
            )');
            $result = $query->execute();
            if(!$result)
            {
                //do error reporting, do not die!!
                OC_Log::write('todo', "Could not create table todo_entries", OC_Log::ERROR);
            } else {
                $query = OC_DB::prepare('INSERT INTO *PREFIX*todo_entries (text, list) VALUES ("First test", 1)');
                if(!$query->execute())
                    OC_Log::write('todo', "Could not create dummy data for entries. This is not critical but should be looked into!", OC_Log::ERROR);
            }
        }
    }
    
    public static function check_installed() {
        $return = true;
        $query = OC_DB::prepare("SELECT name FROM sqlite_master WHERE type='table' AND name='*PREFIX*todo_lists'");
        $result = $query->execute();
        if($result)
        {
            if(!$result->fetchOne()) {
                self::$installed['lists'] = false;
                $return = false;
            }
        }
        
        $query = OC_DB::prepare("SELECT name FROM sqlite_master WHERE type='table' AND name='*PREFIX*todo_entries'");
        $result = $query->execute();
        if($result)
        {
            if(!$result->fetchOne()) {
                self::$installed['entries'] = false;
                $return = false;
            }
        }
        
        return $return;
    }
}

?>