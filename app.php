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

add_action( 'UI_HEAD' , 'calendar_css' );
function calendar_css()
{
	echo '<link rel="stylesheet" href="plugin/calendar/view/css/bootstrap-datetimepicker.min.css">';
	echo '<link rel="stylesheet" href="plugin/calendar/view/css/calendar.css">';
}


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

add_action( 'UI_TODO_DETAIL_COMMENTBOX_AFTER' , 'calendar_input' );
function calendar_input($data)
{
	echo render_html($data, dirname(__FILE__).DS.'view'.DS.'calendar_input.tpl.html');
}

add_action( 'PLUGIN_CALENDAR_UPDATE' , 'plugin_calendar_update' );
function plugin_calendar_update()
{
	#TODO: get parameter from request and set in params 
	$params = array(
		'exp_start_time'=> v('exp_start_time'),
		'exp_finish_time'=> v('exp_finish_time'),
		'priority'=> v('priority')
	);
	if($content = send_request( "todo_calendar_update" ,  $params , token()  ))
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

add_action( 'API_TODO_CALENDAR_UPDATE' , 'api_todo_calendar_update' );
function api_todo_calendar_update()
{
	#TODO: create and update
	$params = array(
		'exp_start_time'=> v('exp_start_time'),
		'exp_finish_time'=> v('exp_finish_time'),
		'priority'=> v('priority')
	);
	return apiController::send_result($params);
}

add_action( 'PLUGIN_CALENDAR' , 'calendar_view' );
function calendar_view()
{
	$data['top'] = $data['top_title'] = __('PL_CALENDAR_TITLE');
	return render( $data , 'web' , 'plugin' , 'calendar' );
}
