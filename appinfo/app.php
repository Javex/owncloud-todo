<?php
//OC::$CLASSPATH['OC_ToDo_Hooks'] = 'apps/todo/lib/hooks.php';
OC::$CLASSPATH['OC_Todo_Todo'] = 'apps/todo/lib/todo.php';
//OCP\Util::connectHook('OC_User', 'post_deleteUser', 'OC_ToDo_Hooks', 'deleteUser');

OCP\App::register( array(
  'order' => 10,
  'id' => 'todo',
  'name' => 'Todo List' ));

OCP\App::addNavigationEntry( array(
  'id' => 'todo_index',
  'order' => 12,
  'href' => OCP\Util::linkTo( 'todo', 'index.php' ),
  'icon' => OCP\Util::imagePath( 'settings', 'users.svg' ),
  'name' => OC_L10N::get('todo')->t('ToDo') )
);


//OCP\App::registerPersonal('todo','settings');
//OCP\Util::addscript('contacts', 'loader');
//OC_Search::registerProvider('OC_Search_Provider_Contacts');
