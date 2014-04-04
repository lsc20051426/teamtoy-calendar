<?php
/*** 
TeamToy extenstion info block
##name Calendar
##folder_name calendar
##author L
##email lsc20051426@163.com
##reversion 1
##desp Calender 将全体成员的TODO显示在日历上。 
##update_url http://tt2net.sinaapp.com/?c=plugin&a=update_package&name=calendar 
##reverison_url http://tt2net.sinaapp.com/?c=plugin&a=latest_reversion&name=calendar 
***/
// calendar
// a flow view of all todos
if( !defined('IN') ) die('bad request');

//创建TODO时间表
if( !my_sql("SHOW COLUMNS FROM `todo_timetable`") )
{
	$sql = "CREATE TABLE IF NOT EXISTS `todo_timetable` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL,
  `priority` varchar(10) NOT NULL DEFAULT 'HIGH',
  `exp_start_time` datetime NOT NULL,
  `act_start_time` datetime,
  `exp_finish_time` datetime NOT NULL,
  `act_finish_time` datetime,
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8";

	run_sql( $sql );

}


$plugin_lang = array();
$plugin_lang['zh_cn'] = array
(
	'PL_CALENDAR_TITLE' => 'TODO日历',
	'PL_CALENDAR_TODO_TIME' => '最后活动时间 - %s',
	'PL_CALENDAR_NO_TODO_NOW' => '暂无TODO',
	'PL_CALENDAR_EXPECT_FINISH_TIME' => '预期完成时间',
	'PL_CALENDAR_ACTUAL_FINISH_TIME' => '实际完成时间',
	'PL_CALENDAR_EXPECTE_START_TIME' => '预期开始时间',
	'PL_CALENDAR_ACTUAL_START_TIME' => '实际开始时间',
	'PL_CALENDAR_PRIORITY' => '优先级',
	'PL_CALENDAR_PRIORITY_HIGH' => '高',
	'PL_CALENDAR_PRIORITY_MEDIUM' => '中',
	'PL_CALENDAR_PRIORITY_LOW' => '低',
	'PL_CALENDAR_SAVE' => '保存',
	'PL_CALENDAR_SAVE_SUCCESS' => '保存成功',
	'PL_CALENDAR_START_TODO' => '开始这个TODO',
	'PL_CALENDAR_TODO_INPROGRESS' => 'TODO正在进行当中'
	
);

$plugin_lang['zh_tw'] = array
(
	
	'PL_CALENDAR_TITLE' => 'TODO日曆',
	'PL_CALENDAR_TODO_TIME' => '最後活動時間- %s',
	'PL_CALENDAR_NO_TODO_NOW' => '暫無TODO'
);

$plugin_lang['us_en'] = array
(
	'PL_CALENDAR_TITLE' => 'TODO Calendar',
	'PL_CALENDAR_TODO_TIME' => 'Last active at %s',
	'PL_CALENDAR_NO_TODO_NOW' => 'No TODO'
);

plugin_append_lang( $plugin_lang );


//在菜单栏添加一个链接
add_action( 'UI_NAVLIST_LAST' , 'calendar_icon' );
function calendar_icon()
{
	?><li <?php if( g('c') == 'plugin' && g('a') == 'calendar' ): ?>
		class="active"<?php endif; ?>>
		<a href="?c=plugin&a=calendar" title="<?=__('PL_CALENDAR_TITLE')?>" >
		<div><img src="plugin/calendar/calendar.png"/></div></a>
	</li>
	<?php
}

//引入CSS文件
add_action( 'UI_HEAD' , 'calendar_css' );
function calendar_css()
{
	echo '<link rel="stylesheet" href="plugin/calendar/view/css/bootstrap-datetimepicker.min.css">';
	echo '<link rel="stylesheet" href="plugin/calendar/view/css/calendar.css">';
}

//引入JS文件
add_action( 'UI_FOOTER_AFTER' , 'calendar_js' );
function calendar_js()
{
	echo '
	<script type="text/javascript" src="plugin/calendar/view/js/bootstrap-datetimepicker.min.js"></script>
	<script type="text/javascript" src="plugin/calendar/view/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
	<script type="text/javascript" src="plugin/calendar/view/js/underscore-min.js"></script>
	<script type="text/javascript" src="plugin/calendar/view/js/jstz.min.js"></script>
	<script type="text/javascript" src="plugin/calendar/view/js/zh-CN.js"></script>
	<script type="text/javascript" src="plugin/calendar/view/js/calendar.js"></script>
	<script type="text/javascript" src="plugin/calendar/view/js/app.js"></script>
	';
}

//CTRL_DASHBOARD_TODO_DETAIL_RENDER_FILTER hook添加数据
add_action( 'CTRL_DASHBOARD_TODO_DETAIL_RENDER_FILTER' , 'query_calendar' );
function query_calendar($data){
	$tid = $data['data']['id'];
	$sql = "select id,tid,priority,act_start_time,exp_start_time,exp_finish_time  from `todo_timetable` where tid='{$tid}'";
	$time = get_line($sql);
	$data['data']['calendar'] = $time;
	return $data;
}

//UI_TODO_DETAIL_ACTIONBOX_BEFORE hook添加按钮
add_action( 'UI_TODO_DETAIL_ACTIONBOX_BEFORE' , 'calendar_start_button' );
function calendar_start_button($data){
	echo render_html($data, dirname(__FILE__).DS.'view'.DS.'calendar_start_button.tpl.html');
}

//UI_TODO_DETAIL_ACTIONBOX_AFTER hook展示时间信息
add_action( 'UI_TODO_DETAIL_ACTIONBOX_AFTER' , 'calendar_input' );
function calendar_input($data)
{
	echo render_html($data, dirname(__FILE__).DS.'view'.DS.'calendar_input.tpl.html');
}

//调用CALENDAR_UPDATE API
add_action( 'PLUGIN_CALENDAR_UPDATE' , 'plugin_calendar_update' );
function plugin_calendar_update()
{
	return calendar_request('todo_calendar_update');
}

//CALENDAR UPDATE API(优先级，预期开始时间，预期结束时间)
add_action( 'API_TODO_CALENDAR_UPDATE' , 'api_todo_calendar_update' );
function api_todo_calendar_update()
{
	$tid = v('tid');
	$exp_start_time =  v('exp_start_time');
	$exp_finish_time = v('exp_finish_time');
	$priority = v('priority');
	$sql = "select id from `todo_timetable` where tid='{$tid}'";
	$time = get_line($sql);
	if($time){
		$timeid = $time['id'];
		$sql = "update `todo_timetable` set exp_start_time='{$exp_start_time}', exp_finish_time='{$exp_finish_time}', priority='{$priority}' where id = '{$timeid}'";
	}else{
		$sql = "insert into todo_timetable(`tid`,`priority`,`exp_start_time`,`exp_finish_time`) values({$tid}, '{$priority}', '{$exp_start_time}', '{$exp_finish_time}')";
	}
	return apiController::send_result(run_sql($sql));
}

//通过API_TODO_DONE_OUTPUT_FILTER hook在关闭todo时设置结束时间
add_action( 'API_TODO_DONE_OUTPUT_FILTER' , 'calendar_finish_todo' );
function calendar_finish_todo($data)
{
	$tid = $data['tid'];
	$sql = "update `todo_timetable` set act_finish_time='". date("Y-m-d H:i:s") ."' where tid = '{$tid}'";
	run_sql($sql);
	return $data;
}

//API_TODO_REOPEN_OUTPUT_FILTER hook在重新打开todo时清除结束时间
add_action( 'API_TODO_REOPEN_OUTPUT_FILTER' , 'calendar_reopen_todo' );
function calendar_reopen_todo($data)
{
	$tid = $data['tid'];
	$sql = "update `todo_timetable` set act_finish_time=NULL where tid = '{$tid}'";
	run_sql($sql);
	return $data;
}

//前台调用CALENDAR START TODO API
add_action( 'PLUGIN_CALENDAR_START_TODO' , 'plugin_calendar_start_todo' );
function plugin_calendar_start_todo()
{
	return calendar_request('calendar_start_todo');
}

//START TODO API,设置开始时间
add_action( 'API_CALENDAR_START_TODO' , 'api_calendar_start_todo' );
function api_calendar_start_todo()
{
	$tid = v('tid');
	$sql = "update `todo_timetable` set act_start_time='". date("Y-m-d H:i:s") ."' where tid = '{$tid}'";
	return apiController::send_result( run_sql($sql) ); 
}

//日历插件页面，显示日历
add_action( 'PLUGIN_CALENDAR' , 'calendar_view' );
function calendar_view()
{
	$data['top'] = $data['top_title'] = __('PL_CALENDAR_TITLE');
	return render( $data , 'web' , 'plugin' , 'calendar' );
}


//用于CALENDAR页面展示TODO列表 API
add_action( 'PLUGIN_TODO_LIST' , 'api_todo_list' );
function api_todo_list()
{
	$from = date("Y-m-d H:i:s", intval($_GET['from'])/1000);
	$to = date("Y-m-d H:i:s", intval($_GET['to'])/1000);

	$todos = array();
	$sql = "select todo.id,todo.content,todo.timeline,
		user.name,
		todo_user.status, todo_user.last_action_at,
		todo_timetable.priority,
		todo_timetable.exp_start_time,
		todo_timetable.act_start_time,
		todo_timetable.exp_finish_time,
		todo_timetable.act_finish_time
		from todo
		left join todo_timetable on todo.id = todo_timetable.tid 
		left join todo_user on todo_user.tid = todo.id
		left join user on todo.owner_uid=user.id
	";
	$data = get_data($sql);

	foreach($data as $todo){
		$calendar_todo = array(
			"id"=> $todo['id'],
			"modal"=> "#events-modal",
			"title"=> "[".$todo['name']."]" . $todo['content'],
		);
		$calendar_todo["url"] =  "http://example.com";
		if(isset($todo['exp_start_time'])){
			$calendar_todo["start"] = strtotime($todo['exp_start_time'])*1000;
		}else{
			$calendar_todo["start"] = strtotime($todo['timeline'])*1000;
		}
		if(isset($todo['exp_finish_time'])){
			$calendar_todo["end"] = strtotime($todo['exp_finish_time'])*1000;
		}else{
			$calendar_todo["end"] = strtotime($todo['last_action_at'])*1000;
		}
		if(isset($todo['act_finish_time'])){
			$calendar_todo["end"] = strtotime($todo['act_finish_time'])*1000;
		}

		if($todo['status']==3){
			$calendar_todo["class"] = "event-success";
		}else if($todo['status']==1){
			if(isset($todo['priority'])){
				if($todo['priority']=="HIGH"){
					$calendar_todo["class"] = "event-important";
				}else if($todo['priority']=="MEDIUM"){
					$calendar_todo["class"] = "event-warning";
				}else if($todo['priority']=="LOW"){
					$calendar_todo["class"] = "event-info";
				}else{
					$calendar_todo["class"] = "event-simple";
				}
			}else{
				$calendar_todo["class"] = "event-simple";
			}
			if(isset($todo['exp_finish_time'])&&strtotime($todo['exp_finish_time'])<time()){
				$calendar_todo["class"] = "event-important";
			}
		} else{
			$calendar_todo["class"] = "event-simple";
		}
	
		$calendar_todo["url"] =  "http://example.com";
		if($calendar_todo['start'])
		$todos[] = $calendar_todo;
	}
	
	$result = array(
		"success"=>1,
		"result" => $todos
	);
	
	echo json_encode($result);
}




function calendar_request($action){
	if($content = send_request( $action ,  array() , token()  ))
	{
		$data = json_decode($content , 1);
		if( $data['err_code'] == 0 )
		{
			return render( array( 'code' => 0 , 'data' =>  $data['data'] ) , 'rest' );
		}
		else
			return render( array( 'code' => 100002 , 'message' => 'can not save data' ) , 'rest' );
		//return render( array( 'code' => 0 , 'data' => $data['data'] ) , 'rest' );
	}
	return render( array( 'code' => 100001 , 'message' => 'can not get api content' ) , 'rest' );
}