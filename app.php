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
  `priority` varchar(10) NOT NULL DEFAULT 'HIGH'
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
	'PL_CALENDAR_NO_TODO_NOW' => '暂无TODO'
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

add_action( 'PLUGIN_CALENDAR' , 'calendar_view' );
function calendar_view()
{	$data['top'] = $data['top_title'] = __('PL_CALENDAR_TITLE');
	return render( $data , 'web' , 'plugin' , 'calendar' );
}

