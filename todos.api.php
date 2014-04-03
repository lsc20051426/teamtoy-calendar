<?php

$from = date("Y-m-d 00:00:00", int($_GET['from'])/1000);
$to = date("Y-m-d 00:00:00", int($_GET['to'])/1000);



$todos = array();

$todos[] = array(
    "id"=> 293,
    "title"=> "TEAMTOY",
    "url"=> "http://example.com",
    "class"=> "event-important",
    "modal"=> "#events-modal",
    "start"=> 1395948567000, // Milliseconds
    "end"=>   1396948567000 // Milliseconds
);

$result = array(
    "success"=>1,
    "result" => $todos
);


echo json_encode($result);


//$todo_lists = get_data("select distinct todo.id,content,status,user.name from user,todo left join todo_user on todo_user.tid=todo.id where todo.owner_uid=user.id");