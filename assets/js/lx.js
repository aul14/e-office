/**
 * 
 */

	var ajaxURI = '/lx_man/home/ajax';
	var assetsPath = '/lx_man/assets/';
	
	var SEOoptions = { 
		"translitarate": false, 
		"uppercase": false, 
		"lowercase": true, 
		"divider": '_' 
	}
	
	function setSEO(baseID, targetID) {
		$('#' + targetID).val($('#' + baseID).val().seoURL(SEOoptions));
	}

	function BrowseServer(startupPath, functionData) {
		var finder = new CKFinder();
	
		finder.basePath = finderBasePath;
		finder.startupPath = startupPath;
		finder.selectActionFunction = SetFileField;
		finder.selectActionData = functionData;
		finder.popup();
	}

	function SetFileField(fileUrl, data) {
		$("#" + data["selectActionData"] ).val(fileUrl);
	}
	
	var popUpWidth = 800;
	var popUpHeight = 600;

	var leftPos = Math.round(screen.width / 2) - Math.round(popUpWidth / 2);
	var topPos =  Math.round(screen.height / 2) - Math.round(popUpHeight / 2);

	function randNum(){
		return ((Math.floor( Math.random()* (1+40-20) ) ) + 20)* 1200;
	}

	function randNum2(){
		return ((Math.floor( Math.random()* (1+40-20) ) ) + 20) * 500;
	}

	function randNum3(){
		return ((Math.floor( Math.random()* (1+40-20) ) ) + 20) * 300;
	}

	function randNum4(){
		return ((Math.floor( Math.random()* (1+40-20) ) ) + 20) * 100;
	}

	function validEmail(v) {
		var r = new RegExp("[a-z0-9!#$%&'*+/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&'*+/=?^_`{|}~-]+)*@(?:[a-z0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?");
		return (v.match(r) == null) ? false : true;
	}

	function sendData(fe) {
//		$('#freeDialog .modal-title').html('Prosessing Data');
//		$('#freeDialog').modal('show');
	/*
		if(CKEDITOR.instances != 'undefined') {
			for ( instance in CKEDITOR.instances ) {
				CKEDITOR.instances[instance].updateElement();
			} 
		}
	*/
		if(validateData(fe)) {

			$.ajax({
				type: "POST",
				url: fe.attr('action'),
				data: fe.serialize()
			})
			.done(function(data){
				$('#freeDialog').modal('hide');
				if(typeof(data.error) != 'undefined') {
					if(data.error != '') {
						bootbox.alert(data.message);
					} else {
						bootbox.alert(data.message);
						eval(data.execute);
					}
				} else {
					bootbox.alert("Data transfer error!");
				}
			}); 
		} 
		return false;
	}

	function validateData(fe) {

		var check = '';
		fe.find('.required').each(function(n) {
			if($(this).val() == '') {
				$(this).parent().parent().addClass('has-error');
				check += "Kolom " + $(this).attr('data-input-title') + " harus diisi. <br/>";
			} else {
				$(this).parent().parent().removeClass('has-error');
			}
		});
		
		fe.find('.validate-email').each(function(n) {
			if(!validEmail($(this).val())) {
				$(this).parent().parent().addClass('has-error');
				check += "Kolom " + $(this).attr('data-input-title') + " tidak sesuai format email. <br/>";
			} else {
				$(this).parent().parent().removeClass('has-error');
			}
		});
		
		if(check != '') {
//			noty({"text":check, "layout":"top", "type":"error", "closeButton":"true"});
			bootbox.alert(check);
			return false;
		}
		return true;
	}
	
	$(document).ready(function() {
		
		$('.validate-email').on('blur', function() {
			if(!validEmail($(this).val())) {
				$(this).parent().parent().addClass('has-error');
			} else {
				$(this).parent().parent().removeClass('has-error');
			}
		});
		
		$('.validate-number').on('keyup', function() {
			if(isNaN($(this).val())) {
				$(this).val( isNaN(parseInt($(this).val())) ? '0' : parseInt($(this).val()) );
			}
		});
		
	});
