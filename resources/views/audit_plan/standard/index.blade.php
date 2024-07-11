@extends('layouts.master')
@section('content')
@section('title', 'Auditor Standard')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/sweetalert2.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
@endsection

@section('style')
<style>
    table.dataTable tbody td {
        vertical-align: middle;
    }

    table.dataTable td:nth-child(2) {
        max-width: 120px;
    }

    table.dataTable td:nth-child(3) {
        max-width: 100px;
    }

    table.dataTable td {
        white-space: nowrap;
        text-overflow: ellipsis;
        overflow: hidden;
    }

    .badge-icon {
        display: inline-block;
        font-size: 1em;
        padding: 0.4em;
        margin-right: 0.1em;
    }

    .icon-white
    {
        color: white;
    }

    .auditor-name {
    display: flex;
    align-items: center;
    }

    .auditor-name a.text-danger {
        color: #c00f0f; /* Mengubah warna teks */
        font-family: 'Arial', sans-serif; /* Mengubah jenis font */
        font-weight: bold; /* Mengubah ketebalan font */
        font-size: 15px; /* Mengubah ukuran font */
        margin-right: 10px; /* Menambahkan jarak antara nama auditor dan nomor telepon */
        text-decoration: none; /* Menghapus garis bawah dari link */
    }

    .auditor-name a.text-muted {
        color: #6c757d; /* Warna teks untuk nomor telepon */
        font-size: 0.8em; /* Ukuran font untuk nomor telepon */
        text-decoration: none; /* Menghapus garis bawah dari link */
    }

    .auditor-name a.text-danger:hover,
    .auditor-name a.text-muted:hover {
        color: #d81515; /* Mengubah warna teks saat hover */
    }

</style>
@endsection

<div class="card">
    <div class="card-datatable table-responsive">
        <div class="card-header flex-column flex-md-row pb-0">
            <div class="row">
                <div class="col-12 pt-3 pt-md-0">
                    <div class="col-12">
                        <div class="row">
                <div class="row">
                {{-- <div class="col-md-3">
                    <select id="select_auditee" name="select2" class="select form-select" data-placeholder="Date Start">
                        <option value="">Select Auditee</option>
                        @foreach($auditee as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div> --}}
                <div class="container">
                    <table class="table" id="datatable">
                        {{-- <div class="col-md d-flex justify-content-center justify-content-md-end">
                            <a class="btn btn-primary btn-block btn-mail" title="Add Audit Plan"
                                href="{{ route('audit_plan.add')}}">
                                <i data-feather="plus"></i>+ Add
                            </a>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                    <thead>
                        <tr>
                            <th width="5%"><b>No</b></th>
                            {{-- <th width="15%"><b>Auditee</b></th> --}}
                            <th width="35%"><b>Auditor</b></th>
                            {{-- <th width="10%"><b>Category</b></th> --}}
                            <th width="5%"><b>Action</b></th>
                        </tr>
                    </thead>
                </table>
            </div>

@endsection

@section('script')
<script src="{{asset('assets/vendor/libs/datatables/jquery.dataTables.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables-bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables.responsive.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/responsive.bootstrap5.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables.checkboxes.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables-buttons.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/buttons.bootstrap5.js')}}"></script>
<script src="{{asset('assets/js/sweetalert.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/momentjs/latest/locale/id.js"></script> <!-- Memuat lokal Indonesia untuk moment.js -->
@if(session('msg'))
<script type="text/javascript">
    //swall message notification
    $(document).ready(function () {
        swal(`{!! session('msg') !!}`, {
            icon: 'success',
            customClass: {
                confirmButton: 'btn btn-success'
            }
        });
    });

</script>
@endif

<script type="text/javascript">
    $(document).ready(function () {
        var table = $('#datatable').DataTable({
            responsive: true,
            processing: true,
            serverSide: true,
            ordering: false,
            language: {
                searchPlaceholder: 'Search data..'
            },
            ajax: {
                url: "{{ route('audit_plan.data_auditor') }}",
                data: function (d) {
                    d.search = $('input[type="search"]').val(),
                    d.select_auditee = $('#select_auditee').val()
                },
            },
            columnDefs: [{
                "defaultContent": "-",
                "targets": "_all"
            }],
            columns: [{
                    render: function (data, type, row, meta) {
                        var no = (meta.row + meta.settings._iDisplayStart + 1);
                        return no;
                    },
                    className: "text-center"
                },
                {
                    render: function (data, type, row, meta) {
                        var html = `<a class="text-danger" title="` + row.auditor.name +
                            `" href="{{ url('setting/manage_account/users/edit/` +
                            row.idd + `') }}">` + row.auditor.name + `</a>`;

                        if (row.no_phone) {
                            html += `<br><a href="tel:` + row.no_phone + `" class="text-muted" style="font-size: 0.8em;">` +
                                    `<i class="fas fa-phone-alt"></i> ` + row.no_phone + `</a>`;
                        }

                        return html;
                    },
                },
                // {
                //     data: 'auditors',
                //     name: 'auditors',
                //     render: function (data, type, row, meta) {
                //         var auditors = data.split(', ');
                //         var html = '';
                //         auditors.forEach(function (auditor) {
                //             html += `<div class="auditor-name">
                //                         <a class="text-danger" title="${auditor}" 
                //                         href="{{ url('setting/manage_account/users/edit/${row.idd}') }}">
                //                         ${auditor}
                //                         </a>`;
                //             if (row.no_phone) {
                //                 html += ` <a href="tel:${row.no_phone}" class="text-muted" style="font-size: 0.8em;">
                //                             <i class="fas fa-phone-alt"></i> ${row.no_phone}
                //                         </a>`;
                //             }
                //             html += '</div>';
                //         });
                //         return html;
                //     },
                // },
                // {
                //     render: function (data, type, row, meta) {
                //     // Check if row.category exists and has an id
                //     if (row.category && row.category.id) {
                //         var html = `<a class="text-primary" title="${row.category.id}" href="">${row.category.id}</a>`;
                //         return html;
                //   } else {
                //         return '';
                //   }
                //   },
                // },
                {
                    render: function (data, type, row, meta) {
                        var html =
                            `<a class="badge bg-danger badge-icon" title="Create Standar" href="{{ url('audit_plan/standard/create/') }}/${row.id}">
                            <i class="bx bx-plus"></i></a>`;
                        return html;
                    },
                    "orderable": false,
                    className: "text-md-center"
                }
            ]
        });
        $('#select_auditee').change(function () {
            table.draw();
        });
    });

    function DeleteId(id, data) {
        swal({
                title: "Apa kamu yakin?",
                text: "Setelah dihapus, data ("+data+") tidak dapat dipulihkan!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "{{ route('audit_plan.delete') }}",
                        type: "DELETE",
                        data: {
                            "id": id,
                            "_token": $("meta[name='csrf-token']").attr("content"),
                        },
                        success: function (data) {
                            if (data['success']) {
                                swal(data['message'], {
                                    icon: "success",
                                });
                                $('#datatable').DataTable().ajax.reload();
                            } else {
                                swal(data['message'], {
                                    icon: "error",
                                });
                            }
                        }
                    })
                }
            })
    }

</script>

@endsection