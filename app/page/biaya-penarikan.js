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
				trashData("../routes/web/biaya-penarikan.php", id, table);
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
				restoreData("../routes/web/biaya-penarikan.php", id, table);
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
				deleteData("../routes/web/biaya-penarikan.php", id, table);
			}
		});
	});

	$(document).on('click', '.accepted', function(e) {
		e.preventDefault();
		var id = $(this).data('id');

		const data = new FormData();
		data.append('id', id);
		data.append('function', 'getUpdateData');

		Swal.fire({
			title: 'Peringatan',
			text: 'Apakah Anda yakin ingin menyetujui data biaya penarikan ini ?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonColor: '#3085D6',
			cancelButtonColor: '#D33',
			confirmButtonText: 'Ya, Setuju!',
			cancelButtonText: 'Tidak'
		}).then((result) => {
			if(result.isConfirmed) {
				$.ajax({
					type: "POST",
					url: "../routes/web/biaya-penarikan.php",
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
		"url": "../routes/web/biaya-penarikan.php",
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
		"aTargets": [0, 7]
	}],
	"aoColumns": [{"sClass": "text-center"}, {"sClass": "text-left"}, {"sClass": "text-left"}, {"sClass": "text-left"}, {"sClass": "text-right"}, {"sClass": "text-center"}, {"sClass": "text-left"}, {"sClass": "text-center"}],
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