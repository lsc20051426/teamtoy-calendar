
(function($) {

	"use strict";

	var options = {
		events_source: [
			{
				"id": 293,
				"title": "TEAMTOY",
				"url": "http://example.com",
				"class": "event-important",
				"modal": "#events-modal",
				"start": 1395948567000, // Milliseconds
				"end":   1396948567000 // Milliseconds
			}
		],
		view: 'month',
		tmpl_path: 'plugin/calendar/bootstrap-calendar/',
		tmpl_cache: false,
		day: new Date().Format("yyyy-MM-dd"),
		onAfterEventsLoad: function(events) {
			if(!events) {
				return;
			}
			var list = $('#eventlist');
			list.html('');

			$.each(events, function(key, val) {
				$(document.createElement('li'))
					.html('<a href="' + val.url + '">' + val.title + '</a>')
					.appendTo(list);
			});
		},
		onAfterViewLoad: function(view) {
			$('.page-header h3').text(this.getTitle());
			$('.btn-group button').removeClass('active');
			$('button[data-calendar-view="' + view + '"]').addClass('active');
		},
		classes: {
			months: {
				general: 'label'
			}
		},
		first_day: 1,
		modal: '#events-modal',
		language: 'zh-CN'
	};

	var calendar = $('#calendar').calendar(options);

	$('.btn-group button[data-calendar-nav]').each(function() {
		var $this = $(this);
		$this.click(function() {
			calendar.navigate($this.data('calendar-nav'));
		});
	});

	$('.btn-group button[data-calendar-view]').each(function() {
		var $this = $(this);
		$this.click(function() {
			calendar.view($this.data('calendar-view'));
		});
	});

	
}(jQuery));