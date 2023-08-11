function onlyAlphabet(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode
	if ((charCode < 65 || charCode > 90)&&(charCode < 97 || charCode > 122)&&charCode>32&&charCode > 57)
		return false;
	return true;
}

function onlyNumber(evt) {
	var charCode = (evt.which) ? evt.which : event.keyCode
	if (charCode > 31 && (charCode > 57))
		return false;
	return true;
}

function autoFormInput(data, tipe) {
	for(var key in data) {
		$('[name="'+key+'"]').val(data[key]);
		if(tipe == "viewed") {
			$('[name="'+key+'"]').attr("disabled", 'disabled');
		}
	}
}

function formatPhoneNumber() {
	var cleavePN = new Cleave('.phone-number', {
		phone: true,
		phoneRegionCode: 'id'
	});
}

$('.phone-number').toArray().forEach(function(field){
    new Cleave(field, {
        delimiter: '-',
        phone: true,
        phoneRegionCode: 'id'
    });
});

$('.currency').toArray().forEach(function(field){
    new Cleave(field, {
        numeral: true,
        numeralThousandsGroupStyle: 'thousand'
    });
});

$('.select2').select2();

$('.select2bs4').select2({
    theme: 'bootstrap4'
});

$('.data-tables').DataTable({
	responsive: true,
	"oLanguage": {
		"sEmptyTable": "Tidak ada data",
		"sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
		"sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
		"sInfoFiltered": "",
		"sZeroRecords": "Tidak ditemukan data yang sesuai",
		"oPaginate": {
			"sFirst": "Pertama",
			"sLast": "Terakhir",
			"sNext": "Selanjutnya",
			"sPrevious": "Sebelumnya"
		},
		"sLengthMenu": "_MENU_",
		"sSearch": "Pencarian :"
	},
	drawCallback: function(settings){
		var api = this.api();
		$('[data-toggle="tooltip"]').tooltip();
	},
	fnDrawCallback: function () {
		$(".chocolat").Chocolat({
			className: 'chocolat',
			imageSelector: '.chocolat-item',
			imageSize: 'contain'
		});
	}
});

function trashData(url, id, table) {
	$.ajax({
		type: "POST",
		url: url,
		data: {'id':id, 'function':'trashData'},
		success: function(response) {
			var result = JSON.parse(response);
            if(result.status_code == 200) {
                toastr.success(result.message);
                setTimeout(() => {
                    table.draw();
                }, 1500);
            }else {
                toastr.error(result.message);
            }
		}
	});
}

function restoreData(url, id, table) {
	$.ajax({
		type: "POST",
		url: url,
		data: {'id':id, 'function':'restoreData'},
		success: function(response) {
			var result = JSON.parse(response);
            if(result.status_code == 200) {
                toastr.success(result.message);
                setTimeout(() => {
                    table.draw();
                }, 1500);
            }else {
                toastr.error(result.message);
            }
		}
	});
}

function deleteData(url, id, table) {
	$.ajax({
		type: "POST",
		url: url,
		data: {'id':id, 'function':'deleteData'},
		success: function(response) {
			var result = JSON.parse(response);
            if(result.status_code == 200) {
                toastr.success(result.message);
                setTimeout(() => {
                    table.draw();
                }, 1500);
            }else {
                toastr.error(result.message);
            }
		}
	});
}

$(document).on('click','#user-logout', function(e) {
	e.preventDefault();
	var id = 1;
	Swal.fire({
		title: 'Peringatan',
		text: 'Apakah Anda yakin ingin keluar ?',
		icon: 'warning',
		showCancelButton: true,
  		confirmButtonColor: '#3085d6',
  		cancelButtonColor: '#d33',
  		confirmButtonText: 'Ya, Keluar!',
  		cancelButtonText: 'Tidak'
	}).then((result) => {
  		if(result.isConfirmed) {
			$.ajax({
				type: "POST",
				url: "http://localhost/kelola-sampah-v2/routes/web/site.php",
				data: {'id':id, 'function':'logout'},
				success: function(response) {
					var result = JSON.parse(response);
					if(result.status_code == 200) {
                        toastr.success(result.message);
                        setTimeout(() => {
							window.location.href = 'http://localhost/kelola-sampah-v2/app/'
                        }, 1500);
					}else {
                        toastr.error(result.message);
					}
				}
			});
  		}
	});
});

function removeElements() {
	$('.no-access').remove();
}

removeElements();

function disabledHref() {
	$('.is-disabled-button').on('click', function(e) {
		e.preventDefault();
		return false;
	});
	$('.is-disabled-button').removeAttr('href');
	$('.is-disabled-button').removeClass('trashed');
}

disabledHref();