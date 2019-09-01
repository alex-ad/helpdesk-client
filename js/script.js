//
// скрипт основной страницы : подача заявки
//

// очистка всех строк ввода text input
$('input[type=text]').each(function(i, elem) {
	if ( elem.value.length > 0 )
		elem.value = '';
});

// очиста переключателей checkbox`
$('input[type=checkbox]').each(function(i, elem) {
	elem.checked = false;
});

// очистка выпадающего списка с бизнес-единицей
$('.new_company').val('');

// событие при изменении ИТ-ресурса в выпадающем списке
function changeResource(cl) {
	$('#btnNewAdd').toggleClass('disabled', true);
	var s = 'tr:nth-of-type('+cl+') td:nth-of-type(4) > input.new_form';
	cl = '.tck_' + cl;
	var res = $(cl+' td:nth-of-type(1) select').val().substr(8);
	$(cl+' input[name="new_res_id"]').val(res);
	$(s).val('');
	var form_org = $('tr:first-of-type td:nth-of-type(4) > input.new_form').val();
	var params = {};
	params.data = res;
	params.func = 'getServiceFromResource';
	var v1 = {};
	v1.v1 = $('.new_division option:selected').val();
	v1.v2 = ( form_org.length > 0 ) ? form_org : '-1';
	params.v1 = JSON.stringify(v1);
	// запрос: получить список Услуг в зависимости от выбранного Ресурса
	$.post("/modules/ajax.request.php", params, function(data){
		data = IsJsonString(params.func, data);
		if (data.error) {
			$('.panel-body').html("<div class='alert alert-danger center' role='alert' style='margin:15px 15px 15px 15px;'><strong>Ошибка</strong><br><br>"+data.error+": "+data.msg+"<br><br>Обратитесь за помощью в Отдел технического обслуживания (т. 74-62-04)</div>");
			console.log(data.error + ' ::: ' + data.msg);
		} else {
			$(cl+' .new_service').html('<option></option>');
			$(cl+' .new_service').removeClass('multiselect');
			$(cl+' .new_service').removeAttr('multiple');
			$(cl+' .new_service').select2({
				language: 'ru',
				placeholder: 'Выберите услугу'
			});
			for ( var i=0; i<data.length; i++ ) {
				var srv_id = data[i].id;
				$(cl+' .new_service').append('<option id="id_'+i+'_'+data[i].role_list_type+'" class="new_srv_'+data[i].id+'" value="new_srv_'+srv_id+'">'+data[i].name+'</option>');
			}
			if ( data.length == 1 ) $(cl+' select.new_service option:last').attr('selected','selected').trigger('change');
		}
	}, "text");
};

// событие при изменении ИТ-услуги в выпадающем списке
function changeService(cl) {
	$('#btnNewAdd').toggleClass('disabled', true);
	if ( $('.tck_'+cl+' .new_service').select2('data') ) {
		if ( $('.tck_'+cl+' .new_service').select2('data').length < 1 ) return false;
	} else return false;
	var id_ = cl;
	var cl = '.tck_' + cl;
	if ($(cl+' .new_service').select2('data')[0].selected == false) {
		return false;
	}
	var res = $(cl+' .new_service').select2('data')[0].id.substr(8);
	var sz = $(cl+' .new_service').select2('data').length;
	var id = '';
	for ( var i=0; i<sz; i++ ) {
		var tail = ( (i+1) == sz ) ? '' : ',';
		id = id + $(cl+' .new_service').select2('data')[i].element.className.substr(8) + tail;
	}
	$(cl+' input[name="new_srv_id"]').val(id);
	var list_type = $(cl+' td:nth-of-type(2) select option:selected').attr('id').slice(-1);
	var params = {};
	var role_type = res;
	params.data = role_type;
	params.func = 'getRoleFromService';
	params.v1 = $('.new_division option:selected').val();
	// запрос: получить список Ролей в зависимости от выбранной Услуги
	$.post("/modules/ajax.request.php", params, function(data){
        data = IsJsonString(params.func, data);
		if ( list_type == '0' ) {
			// установка выпадающего списка с Ролями, если можно выбирать несколько ролей
			$(cl+' .new_role').html('<option></option>');
			$(cl+' .new_role').removeClass('multiselect');
			$(cl+' .new_role').removeAttr('multiple');
			$(cl+' .new_role').select2({
				language: "ru",
				placeholder: 'Выберите роль'
			});
		} else {
			// установка выпадающего списка с Ролями, если можно выбрать только одну роль
			$(cl+' .new_role').html('');
			$(cl+' .new_role').addClass('multiselect');
			$(cl+' .new_role').select2({language: "ru"});
			$(cl+' .new_role').select2MultiCheckboxes({
				minimumResultsForSearch: 1,
				templateSelection: function(selected, total) {
					var elem = $(cl+' td:nth-of-type(3) select option:selected');
						if ( elem.length > 0 ) {
							var place = '';
							for ( var i=0; i<elem.length; i++ ) {
								var tail = ( (i+1)==elem.length ) ? '' : ', ';
								place = place + elem[i].text + tail;
							}
							return selected.length + ': ' + place;
						} else return 'Выберите роль';
				}
			});
		}
		for ( var i=0; i<data.length; i++ ) {
			$(cl+' .new_role').append('<option class="new_role_id_'+data[i].id+'" value="new_role_'+data[i].id+'">'+data[i].name+'</option>');
		}
		if ( data.length == 1 ) $(cl+' select.new_role option:last').attr('selected','selected').trigger('change');
		params.data = id;
		params.func = 'getServiceOwner';
		// запрос: получить "владельца услуги"
		$.post("/modules/ajax.request.php", params, function(data){
            data = IsJsonString(params.func, data);
			if (data.error) {
				$('.panel-body').html("<div class='alert alert-danger center' role='alert' style='margin:15px 15px 15px 15px;'><strong>Ошибка</strong><br><br>"+data.error+": "+data.msg+"<br><br>Обратитесь за помощью в Отдел технического обслуживания (т. 74-62-04)</div>");
				console.log(data.error + ' ::: ' + data.msg);
			} else {
				var n = "";
				var f = "0";
				var d = "";
				if ( data[0]) if ( data[0].name ) if ( data[0].name.length>0 ) var n = data[0].name;
				if ( data[0]) if ( data[0].id ) if ( data[0].id.length>0 ) var d = data[0].id;
				if ( data[0]) if ( data[0].form_org ) if ( data[0].form_org.length>0 ) var f = data[0].form_org;
				$(cl+' .new_own_name').val(n);
				$(cl+' .new_own_id').val(d);
				$(cl+' .new_form').val(f);
				if ( f > 0 )  {
					$('#new_newuser').attr('checked',null);
					$('#alert_new_user').toggleClass('hidden',true);
				} else {
					$('#alert_new_user').toggleClass('hidden',false);
				}
			}
		}, "text");
		params.data = id;
		params.func = 'getServiceVizier';
		// запрос: получить "владельца информации" выбранной услуги
		$.post("/modules/ajax.request.php", params, function(data){
            data = IsJsonString(params.func, data);
			if (data.error) {
				$('.panel-body').html("<div class='alert alert-danger center' role='alert' style='margin:15px 15px 15px 15px;'><strong>Ошибка</strong><br><br>"+data.error+": "+data.msg+"<br><br>Обратитесь за помощью в Отдел технического обслуживания (т. 74-62-04)</div>");
				console.log(data.error + ' ::: ' + data.msg);
			} else {
				var n = "";
				var d = "";
				if ( data[0]) if ( data[0].name ) if ( data[0].name.length>0 ) var n = data[0].name;
				if ( data[0]) if ( data[0].id ) if ( data[0].id.length>0 ) var d = data[0].id;
				$(cl+' .new_vise_name').val(n);
				$(cl+' .new_vise_id').val(d);
			}
		}, "text");
	}, "text");
};

// событие при изменении ИТ-роли в выпадающем списке
function changeRole(cl) {
	cl = '.tck_' + cl;
	if ( $(cl+' .new_role').select2('data') ) {
		if ( $(cl+' .new_role').select2('data').length < 1 ) return false;
	} else return false;
	if ($(cl+' .new_role').select2('data')[0].selected == false) {
		return false;
	}
	var role = '';
	var sz = $(cl+' .new_role').select2('data').length;
	for ( var j=0; j<sz; j++ ) {
		var spr = ( (j+1) == sz ) ? "" : ",";
		role = role + $(cl+' .new_role').select2('data')[j].element.className.substr(12) + spr;
	}
	$(cl+' input[name="new_role_id"]').val(role);
	var form_org = $('tr:first-of-type td:nth-of-type(4) > input.new_form').val();
	if ( form_org > 0 ) $('#btnNewAdd').addClass('disabled');
	else $('#btnNewAdd').toggleClass('disabled', !isResValid());
};

// событие при изменении Подразделения в выпадающем списке
$('.new_division').change(function(data, a) {
	if ($('#bl-2').hasClass('hidden')) $('#bl-2').toggleClass('hidden', !isFormValid());
	var id = this.value;
	if (id == "0") return false;
	$('#vise-div-id').text(id);
	
	if ( a !== 1 ) {
		// формирование списка Должностей в зависимости от выбранного Подразделения
		$('.new_function').html('<option></option>');
		var params = {};
		params.func = 'getFunctionList';
		params.data = id;
		$.post("/modules/ajax.request.php", params, function(data){
            data = IsJsonString(params.func, data);
			for ( var i=0; i<data.length; i++ ) {
				$('.new_function').append('<option value="' + data[i].id + '">' + data[i].func + '</option>');
			}
		}, "text");
	}
	
	var len = $('#new_tickets').children().length;
	if ( len > 1 ) {
		$('#new_tickets tr').each(function(i,elem) {
			if ( i > 0) {
				$(this).remove();
			} else {
				$('.'+$(this).context.classList.value+' td:last-of-type > div').addClass('hidden');
			}
		});
	}
	
	$('.tck_1 .new_resource').html('');
	$('.tck_1 .new_service').html('');
	$('.tck_1 .new_role').html('');
	
	// формирование списка ИТ-ресурсов в выпадающем списке
	var params = {};
	params.func = 'getResourceList';
	params.data = -1;
	$.post("/modules/ajax.request.php", params, function(data){
        data = IsJsonString(params.func, data);
		if (data.error) {
			$('.panel-body').html("<div class='alert alert-danger center' role='alert' style='margin:15px 15px 15px 15px;'><strong>Ошибка</strong><br><br>"+data.error+": "+data.msg+"<br><br>Обратитесь за помощью в Отдел технического обслуживания (т. 74-62-04)</div>");
			console.log(data.error + ' ::: ' + data.msg);
		} else {
			for ( var i=0; i<data.length; i++ ) {
				if ( data[i].visible == '1' )
					$('.tck_1').find('td:nth-of-type(1) select').append('<option class="new_res_id_' + data[i].srv_list_type + '" value="new_res_' + data[i].id + '">' + data[i].name + '</option>').trigger('change');
			}
		}
	}, "text");
});

// событие при изменении Бизнес-единицы в выпадающем списке
$('.new_company').change(function(data, a) {
	if ( a == 1 ) return false;
	if ($('#bl-2').hasClass('hidden')) $('#bl-2').toggleClass('hidden', !isFormValid());
	$('#aster-ootiz').text('* Список должностей сформирован на основании штатного расписания ООТиЗ ' + $('.new_company option:selected').text());
		
	$('.new_division').html('<option></option>');
	$('.new_function').html('<option></option>');
	var id = this.value;
	// формирование списка Подразделений в зависимости от выбранной бизнес-единицы
	var params = {};
	params.func = 'GetDivisionList';
	params.data = id;
	$.post("/modules/ajax.request.php", params, function(data){
        data = IsJsonString(params.func, data);
		for ( var i=0; i<data.length; i++ ) {
			$('.new_division').append('<option value="' + data[i].id + '">' + data[i].division + '</option>');
		}
	}, "text");
});

// показать-скрыть блок с вводом ИТ-ресурсов, услуг, ролей
$('.new_function').change(function(data) {
	if ($('#bl-2').hasClass('hidden')) $('#bl-2').toggleClass('hidden', !isFormValid());
});

// показать-скрыть форму с вводом всех данных в зависимости от состояния переключателя "ознакомлен с положением о комм.тайне"
$('input[name="term_agree"]').change(function() {
	$('#page-print').toggleClass('hidden');
});

// показать-скрыть поле ввода Месторасположения в зависимсоти от состояния переключателя "новый пользователь"
$('input[id="new_newuser"]').change(function() {
    $('#idlocation').toggleClass('hidden');
    $('#new_location').attr('required', !$('#idlocation').hasClass('hidden'));
});

// событие при нажатии кнопки "добавить усугу"
$('#btnNewAdd').click(function() {
	var id = $('tr:first-of-type td:nth-of-type(4) > input.new_form').val();
	$('tr:first-of-type > td > select').each(function(i, elem){
		$(this).attr('disabled', 'disabled');
	});
	if ( ( $('#btnNewAdd').hasClass('disabled') ) ||
		( $('#vise-div-id').text().length < 1 ) ||
			( id > 0 ) )
				return false;
	var num = +($('#tck-id').text()) + 1;
	var tld = num;
	if ( tld > 250 ) {
		alert('Максимальное количество строк в одной заявке - 250');
		return false;
	}
	
	var newclass = 'tck_' + num;
	$('#tck-id').text(num);
	
	var html_ = '<tr class="'+newclass+'"><td><select size="1" name="new_resource" class="new_resource" onchange="changeResource('+tld+')"><option></option></select></td><td><select size="1" name="new_service" class="new_service" onchange="changeService('+tld+')"></select></td><td><select size="1" name="new_role" class="new_role" onchange="changeRole('+tld+')"></select></td><td><input type="radio" class="switcher" name="new_act_'+tld+'" value="1" checked/>Подключить<br /><input type="radio" class="switcher" name="new_act_'+tld+'" value="0" />Отключить<input type="hidden" name="new_own_name" class="new_own_name" value=""/><input type="hidden" name="new_vise_name" class="new_vise_name" value=""/><input type="hidden" name="new_res_id" class="new_res_id" value=""/><input type="hidden" name="new_srv_id" class="new_srv_id" value=""/><input type="hidden" name="new_role_id" class="new_role_id" value=""/><input type="hidden" name="new_own_id" class="new_own_id" value=""/><input type="hidden" name="new_vise_id" class="new_vise_id" value=""/>z<input type="hidden" name="new_form" class="new_form" value=""/></td><td><div><input type="button" class="btn btn-warning btn-narrow btn-xs" id="btnNewDelete" name="btnNewDelete" value="Удалить" onclick="clickDeleteService('+num+');return false;" /></div></td></tr>';
	
	$('#new_tickets').append(html_);
		
	var id = $('tr:first-of-type td:nth-of-type(4) > input.new_form').val();
	var params = {};
	params.func = 'getResourceList';
	params.data = id;
	// формирование списка ИТ-ресурсов
	$.post("/modules/ajax.request.php", params, function(data){
        data = IsJsonString(params.func, data);
		if (data.error) {
			$('.panel-body').html("<div class='alert alert-danger center' role='alert' style='margin:15px 15px 15px 15px;'><strong>Ошибка</strong><br><br>"+data.error+": "+data.msg+"<br><br>Обратитесь за помощью в Отдел технического обслуживания (т. 74-62-04)</div>");
			console.log(data.error + ' ::: ' + data.msg);
		} else {
			for ( var i=0; i<data.length; i++ ) {
				if ( data[i].visible == '1' )
					$('.'+newclass).find('td:nth-of-type(1) select').append('<option class="new_res_id_' + data[i].srv_list_type + '" value="new_res_' + data[i].id + '">' + data[i].name + '</option>');
			}
		}
	}, "text");
	$('.'+newclass).find('td:nth-of-type(1) select').select2({language: "ru",placeholder: 'Выберите ресурс'});
	$('.'+newclass).find('td:nth-of-type(2) select').select2({language: "ru",placeholder: 'Выберите услугу'});
	$('.'+newclass).find('td:nth-of-type(3) select').select2({language: "ru",placeholder: 'Выберите роль',width: '350px'});
	$('#btnNewAdd').toggleClass('disabled', !isResValid());
});

// событие при нажатии кнопки "удалить" (в строке с ИТ-ресурсами)
function clickDeleteService(id) {
	$('.tck_'+id).remove();
	var len = $('#new_tickets').children().length;
	if ( len == 1 ) {
		$('tr:first-of-type > td > select').each(function(i, elem){
			$(this).attr('disabled', null);
		});
	}
	$('#btnNewAdd').toggleClass('disabled', !isResValid());
}

// событие при нажатии кнопки "вернуться" (после формирования бланка заявки)
function clickPrev() {
	$('#page-2').addClass('hidden');
	$('#page-1').removeClass('hidden');
	$('#page-2 .panel-body').html('');
}

// инициализация события изменения ФИО пользователя
var input = document.getElementById('user-data');
input.oninput = function() {
	changeName(0);
};

// событие при изменении в поле ввода ФИО
function changeName(uid) {
	if ( ($('#new_fullname').val().match(/^([а-яА-ЯёЁ]{2,})+\s+([а-яА-ЯёЁ]{2,})+\s+([а-яА-ЯёЁ]{2,})+./)) && (uid.length > 0) && (uid != 0) ) {
		var params = {};
		params.func = 'getUserInfo';
		params.data = uid;
		// автоматическое заполнение всех данных пользователя (если он присутствует в базе)
		$.post("/modules/ajax.request.php", params, function(data){
			data =  IsJsonString(params.func, data);
			if (!data.error)
				if (data.length>0) {
					$('.new_company').html('<option></option>');
					// получение Бизнес-единицы пользователя
					var params2 = {};
					params2.func = 'getCompanyList';
					params2.data = '';
					$.post("/modules/ajax.request.php", params2, function(data2){
						data2 =  IsJsonString(params2.func, data2);
						for ( var i=0; i<data2.length; i++ ) {
							$('.new_company').append('<option value="' + data2[i].id + '">' + data2[i].company + '</option>');
						}
						$('.new_company [value="'+data[0].company+'"]').attr('selected', 'selected').trigger('change', 1);
						$('.new_division').html('<option></option>');
						var id = data[0].company;
						// получение Подразделения пользователя
						var params3 = {};
						params3.func = 'GetDivisionList';
						params3.data = id;
						$.post("/modules/ajax.request.php", params3, function(data3){
							data3 =  IsJsonString(params3.func, data3);
							for ( var i=0; i<data3.length; i++ ) {
								$('.new_division').append('<option value="' + data3[i].id + '">' + data3[i].division + '</option>');
							}
							$('.new_division [value="'+data[0].division+'"]').attr('selected', 'selected').trigger('change', 1);
							$('.new_function').html('<option></option>');
							var id = data[0].division;
							// получение Должности пользователя
							var params4 = {};
							params4.func = 'getFunctionList';
							params4.data = id;
							$.post("/modules/ajax.request.php", params4, function(data4){
								data4 =  IsJsonString(params4.func, data4);
								for ( var i=0; i<data4.length; i++ ) {
									$('.new_function').append('<option value="' + data4[i].id + '">' + data4[i].func + '</option>');
								}
								$('.new_function [value="'+data[0].func+'"]').attr('selected', 'selected').trigger('change', 1);
							}, "text");
						}, "text");
						$('#new_phone').val(data[0].phone);
						$('#new_email').val(data[0].email);
						$('#new_location').val(data[0].location);
						$('#new_tnumber').val(data[0].tnumber);
					}, "text");
				}
		}, "text");
	} else {
		$('#bl-2').toggleClass('hidden', !isFormValid());
	}
}

// событие при нажатии кнопки "сформировать бланк заявки"
function submitNewTicket() {
	if ( !($('input[name="term_agree"]').prop("checked")) ) {
		return false;
	}
	var valid = isResValid();
	if ( (!valid) && (!($('input[name="new_newuser"]').prop("checked"))) ) {
		alert('Укажите все значения Ресурсов, Услуг, Ролей');
		return false;
	}
	
	var len = ($('#new_tickets').children().length);

	var tickets = {};
	var count = 0;
	// заполнение tickets общими данными
	tickets.name = trim($('input[name="new_fullname"]').val().replace(/(\s+)+(\(.+\))/, ''));
	tickets.company = $('.new_company option:selected').val();
	tickets.division = $('.new_division option:selected').val();
	tickets.func = $('.new_function option:selected').val();
	tickets.phone = trim($('input[name="new_phone"]').val());
	tickets.email = trim($('input[name="new_email"]').val());
	tickets.tnumber = trim($('input[name="new_tnumber"]').val());
	tickets.location = trim($('input[name="new_location"]').val());
	tickets.chief_name = $('#vise-div-id').text();
	tickets.comment = $('#new_comment')[0].value;
	
	tickets.resource = '';
	tickets.service = '';
	tickets.role = '';
	tickets.switch0 = '';
	tickets.own_name = '';
	tickets.vise_name = '';
	tickets.form_org = '';
	tickets.ess_name = $('#vise-sec-0-id').text();
	if ( $('input[name="new_newuser"]').prop("checked") ) {
		// добавление ресурса "новое АРМ" , если выбрана опция "новый пользователь"
		var tail = ( valid ) ? '|' : '';
		tickets.resource = '5' + tail;
		tickets.service = '143' + tail;
		tickets.role = '32' + tail;
		tickets.switch0 = '1' + tail;
		tickets.own_name = '' + tail;
		tickets.vise_name = '' + tail;
		tickets.form_org = '0' + tail;
	}

	// заполнение tickets данными о выбранных ИТ-ресурсах 
	if ( valid ) {
		for ( var i=0; i<len; i++ ) {
			var tail = ( (i+1) == len ) ? "" : "|";
			var tck = '#new_tickets > tr:nth-of-type('+(i+1)+')';
			tickets.resource = tickets.resource + $(tck + ' input[name="new_res_id"]').val() + tail;
			var sz = $(tck + ' .new_service').select2('data').length;
			for ( var j=0; j<sz; j++ ) {
				var spr = ( (j+1) == sz ) ? "" : ",";
				tickets.service = tickets.service + $(tck + ' .new_service').select2('data')[j].element.className.substr(8) + spr;
			}
			tickets.service = tickets.service + tail;
			
			var sz = $(tck + ' .new_role').select2('data').length;
			for ( var j=0; j<sz; j++ ) {
				var spr = ( (j+1) == sz ) ? "" : ",";
				tickets.role = tickets.role + $(tck + ' .new_role').select2('data')[j].element.className.substr(12) + spr;
			}
			tickets.role = tickets.role + tail;
			tickets.switch0 = tickets.switch0 + $(tck + ' input[type="radio"]:checked').val() + tail;
			
			if ( $(tck + ' input[type="radio"]:checked').val() == '1' ) {
				var own_name = $(tck + ' .new_own_id').val();
				var vise_name = $(tck + ' .new_vise_id').val();
				count++;
			} else {
				var own_name = '';
				var vise_name = '';
			}
			
			var form_ = $(tck + ' input[name="new_form"]').val();
			tickets.form_org = tickets.form_org + form_ + tail;			
			tickets.own_name = tickets.own_name + own_name + tail;
			tickets.vise_name = tickets.vise_name + vise_name + tail;
		}
	}
	
	$('#page-1').addClass('hidden');
	$('#page-2').removeClass('hidden');
	$('#page-2 .panel-body').html("<p style='text-align:center; padding-top:15px;'>Ваш запрос обрабатывается. Пожалуйста, подождите...</p><div id='processing'></div>");
	// отправка данных о новой заявке
	var params = {};
	params.func = 'newTicket';
	params.data = JSON.stringify(tickets);
    if ( $('input[name="new_newuser"]').prop("checked") ) {
    	params.v1 = 'user_add';
	}
	$.post("/modules/ajax.request.php", params, function(data){
		data =  IsJsonString(params.func, data);
		if (data.error) {
			$('.panel-body').html("<div class='alert alert-danger center' role='alert' style='margin:15px 15px 15px 15px;'><strong>Ошибка</strong><br><br>"+data.error+": "+data.msg+"<br><br>Обратитесь за помощью в Отдел технического обслуживания (т. 74-62-04)</div>");
			console.log(data.error + ' ::: ' + data.msg);
		} else {
			$('#page-2 .panel-body').html("<div class='alert alert-success center' role='alert' style='margin:15px 15px 15px 15px;'>Бланк заявки успешно сформирован.</div>");
			$('#page-2 .panel-body').append("<div class='alert alert-info center' role='alert' style='margin:15px 15px 15px 15px;'><strong>Ваши дальнейшие шаги:</strong><ul><li>Сохраните бланк заявки (кнопка Сохранить заявку)</li><li>Распечатайте на принтере</li><li>Соберите все необходимые подписи</li><li>Отправьте подписанную заявку в Дирекцию информационных технологий ОАО «КМЗ» в г. Волгограде</li></ul></div>");
			$('#page-2 .panel-body').append('<p><button class="btn btn-primary" style="width:232px; margin-left:15px" onclick="clickPrev();return false;"><<< Вернуться назад</button><a class="btn btn-success" href="'+data.flink+'" role="button" style="width:232px; margin-left:15px" target="_blank" download>Сохранить заявку</a></p>');
		}
	}, "text");
}

// событие при вводе ФИО : автозаполнение при совпадении с базой данных
$('#new_fullname').autocomplete({
	source: '/modules/ajax.request.php?func=getUserName',
	width: 200,
	minLength: 3,
	autoFocus: true,
	select: function(event, ui) {
		$( "#new_fullname" ).val(ui.item.name);
		$( "#new_fullname-id" ).val(ui.item.id);
		return false;
	}
}).autocomplete('instance')._renderItem = function(ul, item) {
	return $('<li>').append('<div><b><span style="color:darkred">' + item.name + '</span></b><br>' + '&nbsp;&nbsp;&nbsp;' +  item.company + ' : ' + item.division + ' : ' + item.func + '</div>').appendTo(ul);
};

$("#new_fullname").bind( "autocompleteclose", function(){ changeName($( "#new_fullname-id" ).val()); });

// функция проверки валидности ввода данных пользователя
function isFormValid() {
	var ret = false;
	if ( $('#new_fullname').val().match(/^([а-яА-ЯёЁ]{2,})+\s+([а-яА-ЯёЁ]{2,})+\s+([а-яА-ЯёЁ]{2,})+./) )
		if ( $('.new_company option:selected').val() > 0 )
			if ( $('.new_division option:selected').val() > 0 )
				if ( $('.new_function option:selected').val() > 0 )
					if ( $('#new_phone').val().length > 3 )
						ret = true;

	return ret;
}

// функция проверки валидности ввода ИТ-ресурсов
function isResValid() {
	var len = ($('#new_tickets').children().length);
	for ( var i=0; i<len; i++ ) {
		var verify = false;
		var tck = '#new_tickets > tr:nth-of-type('+(i+1)+')';
		if ( $(tck+' .new_resource').select2('data') )
			if ( $(tck+' .new_resource').select2('data').length > 0 )
				if ( $(tck+' .new_service').select2('data') )
					if ( $(tck+' .new_service').select2('data').length > 0 )
						if ( $(tck+' .new_role').select2('data') )
							if ( $(tck+' .new_role').select2('data').length > 0 )
								if ( $(tck+' .new_role').select2('data')[0].text.length > 0 )
									verify = true;
		if (!verify) return verify;
	}
	return verify;
}

// функция удаления лишних символов из введенных строковый значений
function trim(str, charlist) {
	charlist = !charlist ? ' \\s\xA0' : charlist.replace(/([\[\]\(\)\.\?\/\*\{\}\+\$\^\:])/g, '\$1');
	var re = new RegExp('^[' + charlist + ']+|[' + charlist + ']+$', 'g');
	return str.replace(re, '');
}

// функция проверки на формат json
function IsJsonString(fnc, str) {
    try {
        data = JSON.parse(str);
    } catch (e) {
        $('.panel-body').html("<div class='alert alert-danger center' role='alert' style='margin:15px 15px 15px 15px;'><strong>Ошибка</strong><br><br>Функция ("+fnc+") не смогла вернуть верные данные<br><br>Обратитесь за помощью в Отдел технического обслуживания (т. 74-62-04)</div>");
		return false;
    }
    return data;
}
