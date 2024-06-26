@extends('layouts.master')
@section('title', 'Standard Categories')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/sweetalert2.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
@section('style')

@endsection

@section('breadcrumb-title')
<h3>@yield('title')</h3>
@endsection

@section('breadcrumb-items')
@endsection

@section('content')
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
                                <select id="Select_2" class="form-control input-sm select2" data-placeholder="Status">
                                    <option value="">Status</option>
                                    <option value='true'>ON</option>
                                    <option value='false'>OFF</option>
                                </select>
                            </div>
                        </div>
                    </div>
            <div class="container">
                <table class="table" id="datatable">
                    <div class="col-md d-flex justify-content-center justify-content-md-end">
                        <a class="btn btn-primary btn-block btn-mail" title="Add new"
                            href="{{ route('standard_category.category_add')}}">
                            <i data-feather="plus"></i>+ Add
                        </a>
                    </div>
                        <div class="container">
                            <thead>
                                <tr>
                                    <th scope="col" width="60px" class="text-center"><b>Code ID</b></th>
                                    <th scope="col"><b>Title</b></th>
                                    <th scope="col"><b>Description</b></th>
                                    <th scope="col" width="50px"><b>Status</b></th>
                                    <th scope="col" width="50px"><b>Required</b></th>
                                    <th scope="col" width="65px"><b>Action</b></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script src="{{asset('assets/vendor/libs/datatables/jquery.dataTables.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/datatables-buttons.js')}}"></script>
<script src="{{asset('assets/vendor/libs/datatables/buttons.bootstrap5.js')}}"></script>
<script src="{{asset('assets/js/sweetalert.min.js')}}"></script>
<script src="{{asset('assets/vendor/libs/select2/select2.js')}}"></script>
<script src="{{asset('assets/js/sweet-alert/sweetalert.min.js')}}"></script>
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
                searchPlaceholder: 'Search...',
                sSearch: '_INPUT_ &nbsp;',
                lengthMenu: '<span>Show:</span> _MENU_',
            },
            ajax: {
                url: "{{ route('standard_category.data') }}",
                data: function (d) {
                    d.status = $('#Select_2').val(),
                    d.search = $('input[type="search"]').val()
                },
            },
            columns: [{
                    render: function (data, type, row, meta) {
                        var x =  row.id ;
                        return x;
                    },
                    className: "text-center"
                },
                {
                    render: function (data, type, row, meta) {
                        return row.title;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        return row.description;
                    },
                },
                {
                    render: function(data, type, row, meta) {
                        var html =
                            `<span class="badge bg-${row.status.color}">${row.status.title}</span>`;
                        return html;
                    }
                },
                {
                    render: function (data, type, row, meta) {
                        var x = "";
                        if (row.is_required) {
                            x = '<i class="bx bx-check text-warning"></i>';
                        }
                        else{
                            x = '<i class="bx bx-x text-danger"></i>';
                        }
                        return x;
                    },
                    className: "text-center"
                },
                {
                    render: function(data, type, row, meta) {
                        var html =
                            `<a class="text-warning" title="Edit" style="cursor:pointer" href="{{ url('setting/manage_standard/category/category_edit/') }}/${row.id}">
                            <i class="bx bx-pencil"></i></a>
                            <a class="text-danger" title="Hapus" style="cursor:pointer"
                            onclick="DeleteId(\'` + row.id + `\',\'` + row.description + `\')" >
                            <i class="bx bx-trash"></i></a>`;
                        return html;
                    },
                    "orderable": false,
                    className: "text-end"
                }
            ]
        });
        $('#Select_2').change(function () {
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
                        url: "{{ route('standard_category.delete') }}",
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
