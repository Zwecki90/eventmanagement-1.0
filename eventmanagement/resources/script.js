$(document).ready(function() {
	$.ajaxSetup({
		statusCode: {
			404: function() {
				alert( "Fehler: Seite nicht gefunden!" );
			},
			501: function() {
				alert( "Fehler: Server nicht erreichbar!" );
			},

			403: function() {
				alert( "Fehler: Zugriff verweigert!" );
			}
		},
		timeout: 15000
	});
	
	$('.event-management-date-time').datetimepicker({
		lang:'de',
		i18n:{
			de:{
				months:[
					'Januar','Februar','März','April',
					'Mai','Juni','Juli','August',
					'September','Oktober','November','Dezember',
				],
				dayOfWeek:[
					"So.", "Mo", "Di", "Mi", 
					"Do", "Fr", "Sa.",
				]
			}
		},
		format:'d.m.Y H:i',
		minDate: 0,
		mask: true,
		roundTime: 'ceil'
	});
	
	// Ich hab irgendwo einen Designfehler drin, den ich leider nicht mehr gefunden habe
	// Dadurch werden im IE und im Chrome einige Leerzeilen direkt unterm body-Element eingefügt
	// Mit der Funktion entferne ich beim Start die Leerzeilen, damit es wenigstens gut aussieht.
	$(document.body).contents().filter(function() {
		return this.nodeType == 3; //Node.TEXT_NODE
	}).remove();
	
	$('.event-management-number-field').keydown(onEventManagementNumberFieldKeyDown);

    $('.event-management-body-head-login-form').submit(onEventManagementBodyHeadLoginFormSubmit);
	$('.event-management-body-head-login-logout').click(onEventManagementBodyHeadLoginLogoutClick);
	$('.event-management-body-head-login-register').click(onEventManagementBodyHeadLoginRegisterClick);
	$('.event-management-body-list-event-cancel-participation').submit(onEventManagementBodyListEventCancelParticipationSubmit);
	$('.event-management-body-list-event-participate').submit(onEventManagementBodyListEventParticipateSubmit);
	$('.event-management-body-content-manage-form').submit(onEventManagementBodyContentManageFormSubmit);
	$('.event-management-body-content-manage-form').find('input[type=reset]').click(onEventManagementBodyContentManageFormReset);
	
	$('.event-management-body-list-event-form').submit(function(e) { e.preventDefault(); e.stopPropagation(); });
	$('.event-management-body-list-event-form-edit-button').click(onEventManagementBodyListEventFormEditButton);
	$('.event-management-body-list-event-form-delete-button').click(onEventManagementBodyListEventFormDeleteButton);
});

/**
 * Realisiert die Nummernfeldfunktion im IE
 * Tasten, die keine Nummern sind, werden ignoriert
 */
function onEventManagementNumberFieldKeyDown(e)
{
	// Allow: backspace, delete, tab, escape, enter and .
	if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
		// Allow: Ctrl+A
		(e.keyCode == 65 && e.ctrlKey === true) || 
		// Allow: home, end, left, right, down, up
		(e.keyCode >= 35 && e.keyCode <= 40))
		{
			// let it happen, don't do anything
			return;
		}
	// Ensure that it is a number and stop the keypress
	if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
		e.preventDefault();
	}
}

function onEventManagementBodyHeadLoginFormSubmit(e)
{
	e.preventDefault();
	e.stopPropagation();
		
	var form = this;
	var email = $(form['email']).val();
	var password = $(form['password']).val();
	form = $(form);
		
	$.ajax({
		url: './resources/login.php',
		data: form.serialize(),
		type: 'post',
		success: function (data, status, jqXHR) {
			if (data !== '﻿success')
			{
				return alert(data);
			}
			
			location.reload();
		}
	});
}

function onEventManagementBodyHeadLoginLogoutClick(e)
{
	$.ajax({
		url: './resources/logout.php',
		type: 'post',
		success: function (data, status, jqXHR) {
			if (data !== 'success')
			{
				return alert(data);
			}
			
			location.reload();
		}
	});
}

function onEventManagementBodyHeadLoginRegisterClick(e)
{
	$.ajax({
		url: './resources/register.php',
		context: $('.event-management-body-content'),
		type: 'post',
		success: function (data, status, jqXHR) {
			this.html(data);
			
			$('.event-management-body-content-register-form').submit(onEventManagementBodyContentRegisterFormSubmit);
		}
	});
}

function onEventManagementBodyContentRegisterFormSubmit(e)
{
	e.preventDefault();
	e.stopPropagation();
	
	var form = this;
	var email = $(form['email']);
	var password = $(form['password']);
	var password2 = $(form['password2']);
	form = $(form);
	
	if (password.val() !== password2.val())
	{
		password.focus();
		return alert('Die Passwörter stimmen nicht überein!');
	}
	
	$.ajax({
		url: './resources/get-key.php',
		context: $('.event-management-body-content'),
		data: form.serialize(),
		type: 'post',
		success: function (data, status, jqXHR) {
			if (data === "error_no_match")
			{
				password.focus();
				return alert('Fehler: Die Passwörter stimmen nicht überein!');
			}
			else if (data === "﻿error_email_used")
			{
				email.focus();
				return alert('Fehler: Die angegebene Email-Adresse ist bereits in Benutzung!');
			}
			
			this.html(data);
		}
	});
}

function onEventManagementBodyListEventCancelParticipationSubmit(e)
{
	e.preventDefault();
	e.stopPropagation();
	
	var form = $(this);
	
	$.ajax({
		url: './resources/cancel-part.php',
		data: form.serialize(),
		type: 'post',
		success: function (data, status, jqXHR) {
			if (data !== "﻿﻿success")
				return alert(data);
			
			location.reload();
		}
	});
}

function onEventManagementBodyListEventParticipateSubmit(e)
{
	e.preventDefault();
	e.stopPropagation();
	
	var form = $(this);
	
	$.ajax({
		url: './resources/participate.php',
		data: form.serialize(),
		type: 'post',
		success: function (data, status, jqXHR) {
			if (data === "﻿limit_reached_error")
			{
				alert('Fehler: Das Teilnehmerlimit wurde bereits erreicht!');
			}
			else if (data === "﻿already_part_error")
			{
				alert('Fehler: Sie nehmen bereits an der Veranstaltung teil!');
			}
			else if (data !== "﻿﻿success")
				return alert(data);
			
			location.reload();
		}
	});
}

function onEventManagementBodyContentManageFormSubmit(e)
{
	e.preventDefault();
	e.stopPropagation();
	
	var form = $(this);
	
	$.ajax({
		url: './admin/updateorcreate.php',
		data: form.serialize(),
		type: 'post',
		success: function (data, status, jqXHR) {
			if (data === "﻿error_no_login")
			{
				alert('Fehler: Sie sind nicht eingeloggt!');
			}
			else if (data === "﻿error_no_access")
			{
				alert('Fehler: Sie haben keinen Zugriff auf diese Funktion!');
			}
			else if (data !== "﻿﻿success")
				return alert(data);
			
			location.reload();
		}
	});
}

function onEventManagementBodyContentManageFormReset(e)
{
	e.preventDefault();
	e.stopPropagation();
	
	location.reload();
}

function onEventManagementBodyListEventFormEditButton(e)
{
	var form = this.form;
	var idField = form['eventid'];
	
	if (!idField)
		return alert('Fehler: Die Veranstaltung konnte nicht korrekt erfasst werden!');
		
	var id = $(idField).val();
	
	var manageForm = $('.event-management-body-content-manage-form');
	// Zum Element scrollen
	$('html, body').animate({
        scrollTop: manageForm.offset().top
    }, 500);
	var h2 = manageForm.children('h2');
	h2.text('Veranstaltung bearbeiten');
	var submit = manageForm.find('input[type=submit]');
	submit.val('Ändern');
	
	var title = $(form['title']).val();
	var desc = $(form['desc']).val();
	var date = $(form['date']).val();
	var maxcount = $(form['maxcount']).val();
	var pcount = $(form['pcount']).val();
	
	// ID einfügen
	var crtfield = $(manageForm[0]['id']);
	crtfield.val(id);
	// Titel einfügen
	crtfield = $(manageForm[0]['title']);
	crtfield.val(title);
	// Beschreibung einfügen
	crtfield = $(manageForm[0]['description']);
	crtfield.val(desc);
	// Datum und Uhrzeit einfügen
	crtfield = $(manageForm[0]['datetime']);
	crtfield.val(date);
	// Max. Teilnehmerzahl einfügen
	crtfield = $(manageForm[0]['maxcount']);
	crtfield.val(maxcount);
	// den minimalen Wert für das Nummernfeld "Max. Teilnehmerzahl" setzen (klappt nicht in IE!)
	crtfield.attr('min', pcount);
}

function onEventManagementBodyListEventFormDeleteButton(e)
{
	if (!confirm('Die Veranstaltung wird unwiederruflich gelöscht. Möchten Sie fortfahren?'))
		return;
		
	var form = this.form;
	var idField = form['eventid'];
	
	if (!idField)
		return alert('Fehler: Die Veranstaltung konnte nicht korrekt erfasst werden!');
	
	$.ajax({
		url: './admin/delete.php',
		data: $(form).serialize(),
		type: 'post',
		success: function (data, status, jqXHR) {
			if (data === "﻿error_no_login")
			{
				alert('Fehler: Sie sind nicht eingeloggt!');
			}
			else if (data === "﻿error_no_access")
			{
				alert('Fehler: Sie haben keinen Zugriff auf diese Funktion!');
			}
			else if (data !== "﻿success")
				return alert(data);
			
			location.reload();
		}
	});
}