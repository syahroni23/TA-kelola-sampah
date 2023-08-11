$(document).ready(function(){
	$('#viewDataAmbil').on('show.bs.modal', function (e) {
		var id = $(e.relatedTarget).data('id');
		var isUpload = $(e.relatedTarget).data('upload');
		if(isUpload == "tidak") {
			$('#isTampilkanUpload').css("display", "none");
		}
		$.ajax({
			type : 'POST',
			url : 'modal/penarikan-sampah-ambil.php',
			data :  {'id':id},
			success : function(response){
				$('.fetch-view').html(response);
			}
		});
	});

	var mapApiLoaded = false;
	var mapApiCallback  = null;

	$('#viewData').on('show.bs.modal', function (e) {
		var id = $(e.relatedTarget).data('id');
		var status = $(e.relatedTarget).data('status');
		$.ajax({
			type : 'POST',
			url : 'modal/penarikan-sampah.php',
			data :  {'id':id},
			success : function(response){
				$('.fetch-view').html(response);

				if(status == "Tampil") {
					function initializeMap() {
						mapApiLoaded = true;
						if (typeof mapApiCallback === 'function') {
							initMap();
							mapApiCallback = null;
						}
					}
					
					if (!mapApiLoaded) {
						var googleMapsScript = document.querySelector('script[src^="https://maps.googleapis.com/maps/api/js"]');
						googleMapsScript.src += '&callback=initializeMap';
					}else {
						initializeMap();
					}
					
					mapApiCallback = initMap();
				}
			}
		});
	});

	$('#viewData').on('hidden.bs.modal', function() {
		mapApiLoaded = false;
	});
});

function actionDataTables(table) {
	$(document).on('click', '.trashed', function(e) {
		e.preventDefault();
		var id = $(this).data('id');
		Swal.fire({
			title: 'Peringatan',
			text: 'Apakah Anda yakin ingin menghapus data ini ?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085D6',
			cancelButtonColor: '#D33',
			confirmButtonText: 'Ya, Hapus!',
			cancelButtonText: 'Tidak'
		}).then((result) => {
			if(result.isConfirmed) {
				trashData("../routes/web/penarikan-sampah.php", id, table);
			}
		});
	});

	$(document).on('click', '.restored', function(e) {
		e.preventDefault();
		var id = $(this).data('id');
		Swal.fire({
			title: 'Peringatan',
			text: 'Apakah Anda yakin ingin memulihkan data ini ?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085D6',
			cancelButtonColor: '#D33',
			confirmButtonText: 'Ya, Pulihkan!',
			cancelButtonText: 'Tidak'
		}).then((result) => {
			if(result.isConfirmed) {
				restoreData("../routes/web/penarikan-sampah.php", id, table);
			}
		});
	});

	$(document).on('click', '.deleted', function(e) {
		e.preventDefault();
		var id = $(this).data('id');
		Swal.fire({
			title: 'Peringatan',
			text: 'Apakah Anda yakin ingin menghapus permanen data ini ?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085D6',
			cancelButtonColor: '#D33',
			confirmButtonText: 'Ya, Hapus!',
			cancelButtonText: 'Tidak'
		}).then((result) => {
			if(result.isConfirmed) {
				deleteData("../routes/web/penarikan-sampah.php", id, table);
			}
		});
	});

	$(document).on('click', '.taked', function(e) {
		e.preventDefault();
		var id = $(this).data('id');

		const data = new FormData();
		data.append('id', id);
		data.append('function', 'getUpdateData');

		Swal.fire({
			title: 'Peringatan',
			text: 'Apakah Anda yakin ingin mengambil penarikan sampah ini ?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085D6',
			cancelButtonColor: '#D33',
			confirmButtonText: 'Ya, Ambil!',
			cancelButtonText: 'Tidak'
		}).then((result) => {
			if(result.isConfirmed) {
				$.ajax({
					type: "POST",
					url: "../routes/web/penarikan-sampah.php",
					data: data,
					processData: false,
					contentType: false,
					success: function(response) {
						var result = JSON.parse(response);
						if(result.status_code == 200) {
							toastr.success(result.message);
							setTimeout(() => {
								location.reload();
							}, 1500);
						}else {
							toastr.error(result.message);
						}
						save_button.removeClass('btn-progress');
						$('button[type=submit]', that).removeAttr('disabled');
					}
				});
			}
		});
	});
}

var table = $('#datatables-serverside').DataTable({
	fnCreatedRow: function(nRow, aData, iDataIndex) {
		$(nRow).attr('data-id', aData[8]);
		$(nRow).addClass('table-middle viewed border-bottom cursor-pointer');
	},
	"responsive": "true",
	"serverSide": "true",
	"processing": "true",
	"paging": "true",
	"pagingType": "full",
	"order": [],
	"ajax": {
		"url": "../routes/web/penarikan-sampah.php",
		"type": "post",
		"data": function (d) {
			return $.extend({}, d, {
				"isDeleted": $('#isDeleted').val(),
				"isStatusTransaksi": $('#isStatusTransaksi').val(),
				"function": "getData"
			});
		}
	},
	drawCallback: function(settings){
		var api = this.api();
		actionDataTables(table);
		disabledHref();
	},
	"aoColumnDefs": [{
		"bSortable": false,
		"aTargets": [0, 3, 4, 7]
	}],
	"aoColumns": [{"sClass": "text-center"}, {"sClass": "text-left"}, {"sClass": "text-left"}, {"sClass": "text-center"}, {"sClass": "text-center"}, {"sClass": "text-center"}, {"sClass": "text-left"}, {"sClass": "text-center"}],
	"oLanguage": {
		"sProcessing": "Harap Menunggu",
		"sEmptyTable": "Tidak ada data",
		"sInfo": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
		"sInfoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
		"sInfoFiltered": "",
		"sZeroRecords": "Tidak ditemukan data yang sesuai",
		"oPaginate": {
			"sFirst": "<i class='fas fa-angle-double-left'></i>",
			"sLast": "<i class='fas fa-angle-double-right'></i>",
			"sNext": "Selanjutnya",
			"sPrevious": "Sebelumnya"
		},
		"sLengthMenu": "_MENU_",
		"sSearch": "Pencarian :"
	},
	fnRowCallback: function (nRow, aData, iDisplayIndex) {
		var info = $(this).DataTable().page.info();
		$("td:nth-child(1)", nRow).html((info.start + iDisplayIndex + 1).toLocaleString('id-ID'));
		return nRow;
	}
});
$(document).ready(function() {
	table.draw();

	$('#isDeleted').on('change', function(e) {
		e.preventDefault();
		table.draw();
	});

	$('#isStatusTransaksi').on('change', function(e) {
		e.preventDefault();
		table.draw();
	});
});