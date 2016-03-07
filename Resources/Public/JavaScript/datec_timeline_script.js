if (typeof jQuery === 'undefined' || typeof jQuery.ui === 'undefined') {
	console.log("DatecTimeline script depends on jQuery and jQuery-ui.");
	console.log("Note that jQuery-ui should also have the draggable and resize plugins active for all interactions.");
}
var DatecTimeline = {
	path: $(location).attr('href').split('?')[0], // base path for ajax requests
	pagetype: '2220', // pageNum for ajax requests
	timeline: null,
	currentLangCode: 'de',
	currentDateTimeFormat: $('#tx-datec-timeline-dateTimeFormat').val(),
	currentDateFormat: $('#tx-datec-timeline-dateFormat').val(),
	showTimeline: function() {
		 $('#tx-datec-timeline-canvas').fullCalendar({
			 header: {
			 	left: 'prev,next today',
			 	center: 'title',
			 	right: 'month,agendaWeek,agendaDay'
			 },
			 height: 'auto',
			 lang: DatecTimeline.currentLangCode,
			 editable: true,
			 selectable: true,
			 selectHelper: true,
			 defaultView:  $(window).width() <= 991 ? 'agendaDay' : 'agendaWeek', // for small screens: only day view
			 scroolTime: '00:00:00',
			 select: DatecTimeline.newDateForm,
			 events: DatecTimeline.loadDates,
			 eventClick: DatecTimeline.editDateForm,
			 eventResize: DatecTimeline.moveDate,
			 eventDrop: DatecTimeline.moveDate,
			 eventRender: function(date, element) {
	            element.append(date.content);
	        }
	    });		
	},
	loadDates: function(start, end, timezone, callback) {
		var data = {
    		'type': DatecTimeline.pagetype,	
    		'tx_datectimeline_timeline[controller]': "Timeline",
    		'tx_datectimeline_timeline[action]': "loadDates",
    		'tx_datectimeline_timeline[start]': start.unix(),
    		'tx_datectimeline_timeline[stop]': end.unix()
    	}
		if ($('.tx-datec-timeline-creator').length) { 
			 var creators = $('.tx-datec-timeline-creator[data-filterstate="active"]');
			 for (var i=0; i < creators.length; i++) {
				data['tx_datectimeline_timeline[creatorIds]['+i+']'] = $(creators[i]).data('creatorid');
			 }
		}
		if ($('.tx-datec-timeline-participant').length) { 
			 var participants = $('.tx-datec-timeline-participant[data-filterstate="active"]');
			 for (var i=0; i < participants.length; i++) {
				data['tx_datectimeline_timeline[participantIds]['+i+']'] = $(participants[i]).data('participantid');
			 }
		}
		$.ajax({
			async: true,
			dataType: "json",
			data: data,
		    url: DatecTimeline.path,
		    success: function(response) {
		    	console.log(response);
		    	if (typeof response.status === "undefined") {
		    		callback(response);
		    	} else {
		    		//  render flash message only on error
		    		if (response.status === "error") {
		    			DatecTimeline.addFlashMessage(response.message, '', response.status);
		    		}
		    	}
		    },
		    error: function(error) {
		        console.log(error);
		        alert('Es ist ein Fehler aufgetreten!');
		    }
		});
	}, 
	newDateForm: function(start, stop) {
    	// inital data for form
    	$.ajax({
    		url: DatecTimeline.path,
    		context: this,
    		data: {
        		'type': DatecTimeline.pagetype,	
        		'tx_datectimeline_timeline[controller]': "Timeline",
        		'tx_datectimeline_timeline[action]': "newDate",
        		'tx_datectimeline_timeline[start]': '@' + start.unix(),
        		'tx_datectimeline_timeline[stop]': '@' + stop.unix()
        	},
    		success: function(data, state, jqXHR) {
        		// open form in jQuery-ui modal dialog
    			$("#tx-datec-timeline-form-canvas").html(data).dialog({
    				modal:true,
    				buttons: {
    			        [DatecTimeline.localizeDialogButton('ok')]: function() {
    			        	DatecTimeline.createDate();
    			        	$(this).dialog('close');
    			        },
    			        [DatecTimeline.localizeDialogButton('cancel')]: function() {
    			            $(this).dialog('close');
    			            $('#tx-datec-timeline-canvas').fullCalendar('unselect');
    			        }
    			    }
    			}).dialog('open');
    			$('.datetimepicker').datetimepicker({
    				dayOfWeekStart: DatecTimeline.currentLangCode == "de" ? 1 : 0,
    				format: DatecTimeline.currentDateTimeFormat,
    				onChangeDateTime: DatecTimeline.saveDateTime,
    				step: 15,
    				minDate: 0
    			});
    			$('.datepicker').datetimepicker({
    				timepicker:false,
    				dayOfWeekStart: DatecTimeline.currentLangCode == "de" ? 1 : 0,
    				format: DatecTimeline.currentDateFormat,
    				formatDate: DatecTimeline.currentDateFormat,
    				onChangeDateTime: DatecTimeline.saveDateTime
    			});
    			DatecTimeline.saveAllDateTimes();
        	},
            error: function(error) {
            	$('#tx-datec-timeline-canvas').fullCalendar('unselect');
            	console.log(error);
		        alert('An error occured!');
            }
    	});
    },
    createDate: function() {
    	$.ajax({
            type: 'POST',
			dataType: "json",
            url: DatecTimeline.path + '?tx_datectimeline_timeline[controller]=Timeline&tx_datectimeline_timeline[action]=createDate&type=' + DatecTimeline.pagetype,
            data: $('#tx-datec-timeline-dateForm').serialize(),
            success: function(data, state, jqXHR) {     
            	if (typeof data.date !== "undefined") {
            		$('#tx-datec-timeline-canvas').fullCalendar('unselect');
            		$('#tx-datec-timeline-canvas').fullCalendar('renderEvent', data.date, false);
            	} else {
            		$('#tx-datec-timeline-canvas').fullCalendar('unselect');
            	}  
        		DatecTimeline.addFlashMessage(data.message, '', data.status);         	
            },
            error: function(error) {
            	$('#tx-datec-timeline-canvas').fullCalendar('unselect');
            	console.log(error);
		        alert('An error occured!');
            }
        });    	
    },
    editDateForm: function(date, jsEvent, view) {
    	$.ajax({
    		url: DatecTimeline.path,
    		context: this,
    		data: {
        		'type': DatecTimeline.pagetype,	
        		'tx_datectimeline_timeline[controller]': "Timeline",
        		'tx_datectimeline_timeline[action]': "editDate",
        		'tx_datectimeline_timeline[dateId]': date.id
        	},
    		success: function(data, state, jqXHR) {
        		// open form in jQuery-ui modal dialog
    			if (data.search("tx-datec-timeline-dateForm") === -1) { // is not the form
    				$("#tx-datec-timeline-show-canvas").html(data).dialog({modal:true});
    			} else {
    				$("#tx-datec-timeline-form-canvas").html(data).dialog({
        				modal:true,
        				buttons: {
        					[DatecTimeline.localizeDialogButton('ok')]: function() {
        			        	DatecTimeline.updateDate();
        			        	$(this).dialog('close');
        			        },
        			        [DatecTimeline.localizeDialogButton('cancel')]: function() {
        			            $(this).dialog('close');
        			        },
        			        [DatecTimeline.localizeDialogButton('delete')]: function() {
        			        	check = confirm(unescape("Termin l%F6schen%3F"));
        			    		if (check) {
        			    			DatecTimeline.deleteDate(date);			        	
            			            $(this).dialog('close');
        			    		}
        			        },
        			    }
        			}).dialog('open');    			
        			$('.datetimepicker').datetimepicker({
        				dayOfWeekStart: DatecTimeline.currentLangCode == "de" ? 1 : 0,
        				format:DatecTimeline.currentDateTimeFormat,
        				onChangeDateTime: DatecTimeline.saveDateTime,
        				step: 15,
        				minDate: 0										// defaults to today minimum
        			});
        			$('.datepicker').datetimepicker({
        				dayOfWeekStart: DatecTimeline.currentLangCode == "de" ? 1 : 0,
        				timepicker:false,
        				format: DatecTimeline.currentDateFormat,
        				formatDate: DatecTimeline.currentDateFormat,
        				onChangeDateTime: DatecTimeline.saveDateTime,
        				minDate: 0
        			});
        			DatecTimeline.saveAllDateTimes();
    			}
        	},
        	error: function(error) {
        		console.log(error);
		        alert('An error occured!');
            }
    	});
    },    
    updateDate: function() {
    	$.ajax({
            type: 'POST',
			dataType: "json",
            url: DatecTimeline.path + '?tx_datectimeline_timeline[controller]=Timeline&tx_datectimeline_timeline[action]=updateDate&type=' + DatecTimeline.pagetype,
            data: $('#tx-datec-timeline-dateForm').serialize(),
            success: function(data, state, jqXHR) {
            	if (typeof data.date !== "undefined") {
            		// fullcalendar is missing proto functions (_start,_stop) for updateEvent, we obviously can't have these
                	//$('#tx-datec-timeline-canvas').fullCalendar('updateEvent', data.date);
            		// we can only reload, performance issue
            		$('#tx-datec-timeline-canvas').fullCalendar('destroy');
    				DatecTimeline.showTimeline();
            	}
            	DatecTimeline.addFlashMessage(data.message, '', data.status);
            },
            error: function(error) {
            	console.log(error);
		        alert('An error occured!');
            }
        });
    },
    moveDate: function(date, delta, revertFunc) {
    	$.ajax({
            type: 'POST',
			dataType: "json",
            url: DatecTimeline.path,
            data: {
        		'type': DatecTimeline.pagetype,	
        		'tx_datectimeline_timeline[controller]': "Timeline",
        		'tx_datectimeline_timeline[action]': "updateDate",
        		'tx_datectimeline_timeline[dateId]': date.id,
        		'tx_datectimeline_timeline[date][start]': '@' + date.start.unix(),
        		'tx_datectimeline_timeline[date][stop]': '@' + date.end.unix()
        	},
            success: function(data, state, jqXHR) {
            	if (typeof data.date === "undefined") {
            		revertFunc();
            	}
            	DatecTimeline.addFlashMessage(data.message, '', data.status);
            },
            error: function(error) {
            	revertFunc();
            	console.log(error);
		        alert('An error occured!');
            }
        });
    },
    deleteDate: function(date) {
    	$.ajax({
            type: 'POST',
			dataType: "json",
            url: DatecTimeline.path,
            data: {
        		'type': DatecTimeline.pagetype,	
        		'tx_datectimeline_timeline[controller]': "Timeline",
        		'tx_datectimeline_timeline[action]': "deleteDate",
        		'tx_datectimeline_timeline[dateId]': date.id,
        	},
            success: function(data, state, jqXHR) {
            	$('#tx-datec-timeline-canvas').fullCalendar('removeEvents',date.id);
            },
            error: function(error) {
            	revertFunc();
            	console.log(error);
		        alert('An error occured!');
            }
        });
    },
	buildLangOptions: function() {
		// build the language selector's options
		$.each($.fullCalendar.langs, function(langCode) {
			$('#tx-datec-timeline-lang-selector').append(
				$('<option/>')
					.attr('value', langCode)
					.prop('selected', langCode == DatecTimeline.currentLangCode)
					.text(langCode)
			);
		});
		// rerender the calendar when the selected option changes
		$('#tx-datec-timeline-lang-selector').on('change', function() {
			if (this.value) {
				DatecTimeline.currentLangCode = this.value;
				$('#tx-datec-timeline-canvas').fullCalendar('destroy');
				DatecTimeline.showTimeline();
				$.datetimepicker.setLocale(DatecTimeline.currentLangCode);
			}
		});
	},
	saveDateTime: function(currentDateTime) {
		// gets datetime values as UTC for form
		if (currentDateTime != null) {
			$('#tx-date-timeline-date-'+arguments[1].data('datetime')).val(currentDateTime.toISOString());
		}
	},
	saveAllDateTimes: function() {
		// saves all datemes as UTC for form
		$('.datetimepicker, .datepicker').each(function() {
			if ($(this).val() != '') {
				$('#tx-date-timeline-date-'+$(this).data('datetime')).val(moment($(this).val(), 'DD.MM.YYYY HH:mm').toISOString());
			}
		});			
	},
    addFlashMessage: function(msg, title, severity) {
    	if ($('.typo3-messages').length === 0) {
    		$('#tx-datec-timeline-info').append('<div class="typo3-messages"></div>');
    	}
    	if (title !== '') {
    		title = '<div class="message-header">'+title+'</div>';
    	}
    	$('.typo3-messages').append('<div class="typo3-message message-'+severity+'">'+title+'<div class="message-body">'+msg+'</div></div>');
		setTimeout(function() {
			DatecTimeline.emptyFlashMessages();
		}, 10000);
    },
    emptyFlashMessages: function() {
    	$('#tx-datec-timeline-info').empty();
    },
    localizeDialogButton: function(button) {
    	return DatecTimeline.buttonsLL[DatecTimeline.currentLangCode][button];
    },
    buttonsLL: {
		'de': {'ok':'OK','cancel':'Abbrechen','delete':'Löschen'},
		'en': {'ok':'OK','cancel':'Cancel','delete':'Delete'}    		
	}
}
$().ready(function() {
	if ($('#tx-datec-timeline-canvas').length) {
		$.fullCalendar.langs.de.allDayText = "GT"; // whole day adjusted for mobile view
		if ($('#tx-datec-timeline-lang-selector').length) {
			DatecTimeline.buildLangOptions();
		}
		DatecTimeline.showTimeline();
		$.datetimepicker.setLocale(DatecTimeline.currentLangCode);
		if ($('.tx-datec-timeline-creator').length) { 
			$('.tx-datec-timeline-creator').click(function() {
				if ($(this).attr('data-filterstate') == 'active' && $('.tx-datec-timeline-creator[data-filterstate="active"]').length > 1) { // cannot filter less than 1
					$(this).attr('data-filterstate', 'inactive');
				} else {
					$(this).attr('data-filterstate', 'active');
				}
				$('#tx-datec-timeline-canvas').fullCalendar('refetchEvents');
			});
		}
		if ($('.tx-datec-timeline-participant').length) { 
			$('.tx-datec-timeline-participant').click(function() {
				if ($(this).attr('data-filterstate') == 'active') {
					$(this).attr('data-filterstate', 'inactive');
				} else {
					$(this).attr('data-filterstate', 'active');
				}
				$('#tx-datec-timeline-canvas').fullCalendar('refetchEvents');
			});
		}
		if ($('.tx-datec-timeline-refresh').length) { 
			$('.tx-datec-timeline-refresh').click(function() {
				$('#tx-datec-timeline-canvas').fullCalendar('refetchEvents');
			});
		}
		window.addEventListener("hashchange", function(e) {
			  if ($('.ui-dialog:visible').length) {
				  e.preventDefault();
				  $("#tx-datec-timeline-form-canvas").dialog('close');
			  }
		});
	}
});