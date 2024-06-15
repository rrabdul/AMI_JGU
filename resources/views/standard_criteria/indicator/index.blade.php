@extends('layouts.master')
@section('title', 'Indicator')

@section('css')
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-responsive-bs5/responsive.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-checkboxes-jquery/datatables.checkboxes.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/datatables-buttons-bs5/buttons.bootstrap5.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/sweetalert2.css')}}">
<link rel="stylesheet" href="{{asset('assets/vendor/libs/select2/select2.css')}}" />
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

</style>
@endsection

@section('breadcrumb-title')
<h3>@yield('title')</h3>
@endsection

@section('breadcrumb-items')
@endsection


@section('content')
    <div class="col-md-12">
        <ul class="nav nav-pills flex-column flex-sm-row mb-4">
        <li class="nav-item"><a class="nav-link " href="{{ route('standard_criteria.criteria') }}"><i
                        class="bx bx-add-to-queue me-1"></i>
                    Data Standard</a></li>    
        <li class="nav-item"><a class="nav-link active" href="{{ route('standard_criteria.indicator') }}"><i
                        class="bx bx-chart me-1"></i>
                    Indicator</a></li>
        </ul>
    </div>

<div class="card">
    <div class="card-datatable table-responsive">
        <div class="card-header flex-column flex-md-row pb-0">
            <div class="row">
                <div class="col-12 pt-3 pt-md-0">
                    <div class="col-12">
                        <div class="row">
                            <div class=" col-md-3">
                                <select id="select_gender" class="select2 form-select" data-placeholder="Gender">
                                    <option value="">Gender</option>
                                    <option value="M">Male</option>
                                    <option value="F">Female</option>
                                </select>
                            </div>
                <div class="col-md d-flex justify-content-center justify-content-md-end">
                    <a class="btn btn-primary btn-block btn-mail" title="Add new"
                        href="{{ route('add.indicator')}}">
                        <i data-feather="plus"></i>+ Add
                    </a>
                </div>
        <table class="table table-hover table-sm" id="datatable" width="100%">
            <thead>
                <tr>
                    <th width="20px">Code Id</th>
                    <th width="40px">Criteria</th>
                    <th width="40px">Category</th>
                    <th width="40px">Action</th>
                </tr>
            </thead>
        </table>
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
                url: "{{ route('standard_criteria.data') }}",
                data: function (d) {
                    d.category = $('#Select_1').val(),
                    d.status = $('#Select_2').val(),
                        d.search = $('input[type="search"]').val()
                },
            },
            columns: [{
                    render: function (data, type, row, meta) {
                        var no = (meta.row + meta.settings._iDisplayStart + 1);
                        return no;
                    },
                    orderable: false,
                    className: "text-center"
                },
                {
                    render: function (data, type, row, meta) {
                        var x = row.category.description;
                        return x;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        var x = row.title;
                        return x;
                    },
                },
                {
                    render: function (data, type, row, meta) {
                        var x = row.id;
                        var html =
                            `<a class="text-warning" title="Edit" href="{{ url('standard_criteria/criteria_edit/` +
                            row.link + `') }}"><i class="bx bx-pencil"></i></a> 
                            <a class="text-primary" title="Delete" onclick="DeleteId(` +
                            x + `)" ><i class="bx bx-trash"></i></a>`;
                        return html;
                    },
                    orderable: false,
                    className: "text-end"
                }
            ]
        });
        $('#Select_2').change(function () {
            table.draw();
        });
    });

    function DeleteId(id) {
        swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
            })
            .then((willDelete) => {
                if (willDelete) {
                    $.ajax({
                        url: "",
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
