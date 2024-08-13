@extends('layouts.master')
@section('content')
@section('title', 'Auditing')

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
    .modal-header .modal-title {
            font-weight: normal; /* Ensure the modal title is not bold */
    }
    .modal-body {
        font-weight: normal; /* Ensure modal body text is not bold */
    }
    .modal-footer {
        font-weight: normal; /* Ensure modal footer text is not bold */
    }
    body, h1, h2, h3, h4, h5, h6, p, span, a, div {
        font-weight: normal; /* Ensure these elements are not bold */
    }

    table.dataTable tbody tr {
        height: 50px; /* Adjust the height as needed */
    }
    /* Increase padding for table cells */
    table.dataTable tbody td {
        padding: 0.6rem; /* Adjust padding for more space within cells */
        vertical-align: middle; /* Vertically align text */
    }
    /* Optional: Adjust font size if necessary */
    table.dataTable tbody td, table.dataTable tbody th {
        font-size: 0.97rem; /* Increase font size if the text appears too small */
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
</style>
@endsection

    <div class="col-12 col-lg-12 order-2 order-md-3 order-lg-2 mb-4">
        <div class="card">
            <div class="card-datatable table-responsive">
                <div class="card-header flex-column flaex-md-row pb-0">
                    <div class="row">
                        <div class="col-12 pt-3 pt-md-0">
                            <div class="col-12">
                                <div class="row">
                                    <div class="offset-md-0 col-md-0 text-md-end text-center pt-3 pt-md-0">
                                    </div>
                                </div>
                                </div>
                <div class="col-md-3">
                    <select id="select_auditee" class="form-control input-sm select2" data-placeholder="Auditee">
                        <option value="">Select Auditee</option>
                        @foreach($auditee as $d)
                        <option value="{{ $d->id }}">{{ $d->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="container-fluid flex-grow-1 container-p-y">
                    <table class="table table-hover table-sm" id="datatable" width="100%">
                        <thead>
                            <tr>
                                <th><b>No</b></th>
                                <th width="15%"><b>Auditee</b></th>
                                <th width="25%"><b>Schedule</b></th>
                                <th width="15%"><b>Location</b></th>
                                <th width="15%"><b>Auditor</b></th>
                                <th width="10%"><b>Status</b></th>
                                <th><b>Action</b></th>
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
<script src="https://cdn.jsdelivr.net/momentjs/latest/locale/id.js"></script>
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
<script>
    "use strict";
    setTimeout(function () {
        (function ($) {
            "use strict";
            $(".select2").select2({
                allowClear: true,
                minimumResultsForSearch: 7
            });
        })(jQuery);
    }, 350);
</script>

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
                url: "{{ route('observations.data') }}",
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
                        var html = `<a class="text-primary" title="` + row.auditee.name +
                            `" href="{{ url('setting/manage_account/users/edit/` +
                            row.idd + `') }}" style="display: block; margin-bottom: 0.1em;">` + row.auditee.name + `</a>`;

                        if (row.auditee.no_phone) {
                            html += `<a href="tel:` + row.auditee.no_phone + `" class="text-muted" style="font-size: 0.8em;">` +
                                    `<i class="fas fa-phone-alt"></i> ` + row.auditee.no_phone + `</a>`;
                        }
                        return html;
                    },
                },
                {
                    data: null,  // Kita akan menggabungkan date_start dan date_end, jadi tidak ada sumber data spesifik
                    render: function (data, type, row, meta) {
                        // Menggunakan moment.js untuk memformat tanggal
                        var formattedStartDate = moment(row.date_start).format('DD MMMM YYYY, HH:mm');
                        var formattedEndDate = moment(row.date_end).format('DD MMMM YYYY, HH:mm');
                        return formattedStartDate + ' - ' + formattedEndDate;
                    }
                },
                {
                    render: function (data, type, row, meta) {

                            return row.location;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        var html = '';
                        if (row.auditor) {
                            row.auditor.forEach(function (auditor) {
                                if (auditor.auditor) {
                                    html += `<code><span title="` + auditor.auditor.name +
                                        `" style="font-size: 1.2em;">` + auditor.auditor.name + `</span></code><br>`;

                                    if (auditor.auditor.no_phone) {
                                        html += `<a href="tel:` + auditor.auditor.no_phone + `" class="text-muted" style="font-size: 0.8em;">` +
                                                `<i class="fas fa-phone-alt"></i> ` + auditor.auditor.no_phone + `</a><br>`;
                                    }
                                }
                            });
                        }
                        return html;
                    },
                },
                {
                    render: function(data, type, row, meta) {
                        var html =
                            `<span class="badge bg-${row.auditstatus.color}">${row.auditstatus.title}</span>`;
                        return html;
                    }
                },
                // {
                //     render: function (data, type, row, meta) {
                //         var x = "";
                //         if (row.doc_path != null) {
                //             x += `<a class="text-dark" title="Documents" target="_blank" href="{{ url('` + row.doc_path + `') }}"><i class="bx bx-file"></i></a> `;
                //         }
                //         if (row.link != null) {
                //             x += `<a class="text-primary" title="Link Drive" target="_blank" href="` + row.link + `"><i class="bx bx-link"></i></a>`;
                //         }
                //         return x;
                //     },
                // },
                {
                    render: function(data, type, row, meta) {
                        var x = '';

                        // Check if auditstatus is '1' or '2'
                        if (row.auditstatus.id === 11 ) {
                            x = `<a class="badge bg-warning" title="Remark Make Report" href="{{ url('observations/remark_doc/${row.id}') }}">
                                        <i class="bx bx-pencil"></i></a>`;
                        }
                        // Check if auditstatus is '10'
                        else if (row.auditstatus.id === 3 ) {
                            x = `<a class="badge bg-primary" title="Auditing" href="{{ url('observations/create/${row.id}') }}">
                                        <i class="bx bx-search-alt"></i></a>`;
                        }
                        else if (row.auditstatus.id === 6 ) {
                            x = `
                                <a class="badge bg-primary" title="Print Make Report" href="{{ url('observations/edit/${row.id}') }}">
                                        <i class="bx bx-printer"></i></a>`;
                        }
                        else if (row.auditstatus.id === 8 ) {
                            x = `<a class="badge bg-warning" title="Remark Make Report" href="{{ url('observations/remark/${row.id}') }}">
                            <i class="bx bx-pencil"></i></a>`;
                        }
                        else if (row.auditstatus.id === 10 ) {
                            x = `
                                <a class="badge bg-warning" title="Remark RTM" href="{{ url('observations/remark_rtm/${row.id}') }}">
                                        <i class="bx bx-pencil"></i></a>`;
                        }
                        else if(row.auditstatus.id === 14){
                            x = `<a class="badge bg-danger" title="Print RTM" href="{{ url('observations/rtm/${row.id}') }}">
                                <i class="bx bx-printer"></i></a>`
                        }
                        return x;
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
</script>
@endsection
<!-- <a class="badge bg-warning" title="Remark Make Report" href="{{ url('observations/remark/${row.id}') }}">
                                        <i class="bx bx-pencil"></i></a> -->
