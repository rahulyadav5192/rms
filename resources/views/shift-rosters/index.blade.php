@extends('layouts.app')

@push('styles')
    <style>
        .table .thead-light th,
        .table tr td,
        .table h5 {
            font-size: 12px;
        }
        .shift-request-change-count {
            left: 28px;
            top: -9px !important;
        }

        .change-shift {
            padding: 1rem 0.25rem !important;
        }

        #week-end-date, #week-start-date {
            z-index: 0;
        }

        #shift-loader {
            min-height: 300px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        #shift-loader .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 0.3em;
        }

        #shift-loader p {
            margin-top: 1rem;
            font-size: 14px;
            color: #6c757d;
        }

        .pagination-info {
            font-size: 14px;
            color: #6c757d;
            font-weight: 500;
        }

        .shift-pagination {
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
            margin-top: 20px;
        }

</style>

    @if ($manageEmployeeShifts != 'all')
        <style>
            .change-shift {
                cursor: unset !important;
            }
        </style>
    @endif
@endpush

@section('filter-section')
    <x-filters.filter-box>
        <div class="select-box d-flex py-2 pr-2 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.employee')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="user_id" id="user_id" data-live-search="true"
                        data-size="8">
                    @if ($employees->count() > 1)
                        <option value="all">@lang('app.all')</option>
                    @endif
                    @forelse ($employees as $item)
                        <x-user-option :user="$item" :selected="request('employee_id') == $item->id"/>
                    @empty
                        <x-user-option :user="user()"/>
                    @endforelse
                </select>
            </div>
        </div>

        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.menu.department')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="department" id="department" data-live-search="true"
                    data-size="8">
                    <option value="all">@lang('app.all')</option>
                    @foreach ($departments as $department)
                        <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>{{ ucfirst($department->team_name) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        @if(isset($branches) && $branches->count() > 0)
            <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
                <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('Branch')</p>
                <div class="select-status">
                    <select class="form-control select-picker" name="branch" id="branch" data-live-search="true" data-size="8">
                        <option value="all">@lang('app.all')</option>
                        @foreach($branches as $br)
                            <option value="{{ $br->id }}" {{ request('branch') == $br->id ? 'selected' : '' }}>
                                {{ $br->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        @if(isset($designations) && $designations->count() > 0)
            <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
                <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.designation')</p>
                <div class="select-status">
                    <select class="form-control select-picker" name="designation" id="designation" data-live-search="true" data-size="8">
                        <option value="all">@lang('app.all')</option>
                        @foreach($designations as $des)
                            <option value="{{ $des->id }}" {{ request('designation') == $des->id ? 'selected' : '' }}>
                                {{ $des->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        @endif

        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0 input-with-icon">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.name')</p>
            <div class="select-status position-relative">
                <input type="text" class="form-control" name="name" id="name" 
                       placeholder="Name Or Id" value="{{ request('name') }}">
            </div>
        </div>

        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.status')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="active_status" id="active_status">
                    <option value="" {{ request('active_status') === '' ? 'selected' : '' }}>@lang('app.all')</option>
                    <option value="0" {{ request('active_status') == '0' ? 'selected' : '' }}>@lang('app.active')</option>
                    <option value="1" {{ request('active_status') == '1' ? 'selected' : '' }}>@lang('app.inactive')</option>
                </select>
            </div>
        </div>

        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Employee Type</p>
            <div class="select-status">
                <select class="form-control select-picker" name="employee_type" id="employee_type">
                    <option value="all" {{ request('employee_type') == 'all' || !request('employee_type') ? 'selected' : '' }}>All</option>
                    <option value="csa" {{ request('employee_type') == 'csa' ? 'selected' : '' }}>CSA</option>
                    <option value="non_csa" {{ request('employee_type') == 'non_csa' ? 'selected' : '' }}>Non-CSA</option>
                </select>
            </div>
        </div>

        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <div class="select-status">
                <select class="form-control select-picker" name="view_type" id="view_type" data-live-search="true"
                    data-size="8">
                    <option value="week" {{ request('view_type') == 'week' ? 'selected' : '' }}>@lang('app.weekly') @lang('app.view')</option>
                    <option value="month" {{ request('view_type') == 'month' || !request('view_type') ? 'selected' : '' }}>@lang('app.monthly') @lang('app.view')</option>
                </select>
            </div>
        </div>

        <input type="hidden" name="month" id="month" value="{{ $month }}">
        <input type="hidden" name="year" id="year" value="{{ $year }}">
        <input type="hidden" name="week_start_date" id="week_start_date" value="{{ now(company()->timezone)->toDateString() }}">

        <!-- RESET START -->
        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
        <!-- RESET END -->

    </x-filters.filter-box>
@endsection

@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper px-4">

        <div class="d-flex">
            <div id="table-actions" class="flex-grow-1 align-items-center">
                <x-forms.link-primary :link="route('shifts.create')" class="mr-3 openRightModal float-left"
                icon="plus">
                    @lang('modules.attendance.bulkShiftAssign')
                </x-forms.link-primary>
                <x-forms.link-secondary :link="route('shifts-change.import_shift')" class="mr-3 float-left"
                icon="">
                    @lang('Import')
                </x-forms.link-primary>
                <x-forms.button-secondary id="export-all" class="mr-3 mb-2 mb-lg-0" icon="file-export">
                    @lang('app.exportExcel')
                </x-forms.button-secondary>
            </div>

            <div class="btn-group" role="group">
                <a href="{{ route('shifts.index') }}" class="btn btn-secondary f-14 btn-active" data-toggle="tooltip"
                    data-original-title="@lang('app.summary')"><i class="side-icon bi bi-list-ul"></i></a>

                <a href="{{ route('shifts-change.index') }}" class="btn btn-secondary f-14" data-toggle="tooltip"
                    data-original-title="@lang('modules.attendance.shiftChangeRequests')"><i
                        class="side-icon bi bi-hourglass-split"></i>
                    @if ($employeeShiftChangeRequest->request_count > 0)
                        <span
                            class="badge badge-primary shift-request-change-count position-absolute">{{ $employeeShiftChangeRequest->request_count }}</span>
                    @endif
                </a>

            </div>

        </div>

        <!-- Task Box Start -->
        <x-cards.data class="mt-3">
            <div class="row">
                <div class="col-md-12" id="attendance-data">
                    <div id="shift-loader" class="text-center py-5" style="display: none;">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="sr-only">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading shift data...</p>
                    </div>
                </div>
            </div>
        </x-cards.data>
        <!-- Task Box End -->
    </div>
    <!-- CONTENT WRAPPER END -->
@endsection

@push('scripts')
    <script>
        var manageEmployeeShiftPermission = "{{ $manageEmployeeShifts }}";

        $('#user_id, #department, #branch, #designation, #active_status, #employee_type, #view_type').on('change', function() {
            var hasFilter = $('#user_id').val() != "all" || 
                           $('#department').val() != "all" || 
                           ($('#branch').length && $('#branch').val() != "all") ||
                           ($('#designation').length && $('#designation').val() != "all") ||
                           $('#active_status').val() != "" ||
                           $('#employee_type').val() != "all" ||
                           $('#name').val() != "";
            
            if (hasFilter) {
                $('#reset-filters').removeClass('d-none');
            } else {
                $('#reset-filters').addClass('d-none');
            }
            showTable();
        });

        $('#name').on('keypress', function(e) {
            if (e.which === 13) {
                var hasFilter = $('#user_id').val() != "all" || 
                               $('#department').val() != "all" || 
                               ($('#branch').length && $('#branch').val() != "all") ||
                               ($('#designation').length && $('#designation').val() != "all") ||
                               $('#active_status').val() != "" ||
                               $('#employee_type').val() != "all" ||
                               $('#name').val() != "";
                
                if (hasFilter) {
                    $('#reset-filters').removeClass('d-none');
                } else {
                    $('#reset-filters').addClass('d-none');
                }
                showTable();
            }
        });

        $('#attendance-data').on('click', '.change-month', function() {
            $("#month").val($(this).data('month'));
            showTable();
        });

        $('#attendance-data').on('change', '#change-month', function() {
            $("#month").val($(this).val());
            showTable();
        });

        $('#attendance-data').on('change', '#change-year', function() {
            $("#year").val($(this).val());
            showTable();
        });

        $('#reset-filters').click(function() {
            $('#filter-form')[0].reset();
            $('#name').val('');
            $('#user_id').val('all');
            $('#department').val('all');
            if ($('#branch').length) $('#branch').val('all');
            if ($('#designation').length) $('#designation').val('all');
            $('#active_status').val('');
            $('#employee_type').val('all');
            $('#view_type').val('month');
            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
        });


        $('#attendance-data').on('click', '#week-start-date', function() {
            $("#week_start_date").val($(this).data('date'));
            showTable();
        });

        $('#attendance-data').on('click', '#week-end-date', function() {
            $("#week_start_date").val($(this).data('date'));
            showTable();
        });

        function showTable(loading = true, page = 1) {

            var year = $('#year').val();
            var month = $('#month').val();
            var weekStartDate = $('#week_start_date').val();

            var userId = $('#user_id').val();
            var department = $('#department').val();
            var branch = $('#branch').length ? $('#branch').val() : 'all';
            var designation = $('#designation').length ? $('#designation').val() : 'all';
            var name = $('#name').val() || '';
            var activeStatus = $('#active_status').val() !== null ? $('#active_status').val() : '';
            var employeeType = $('#employee_type').val() || 'all';
            var viewType = $('#view_type').val();

            // Show loader - create if doesn't exist
            var loaderHtml = '<div id="shift-loader" class="text-center py-5"><div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;"><span class="sr-only">Loading...</span></div><p class="mt-3 text-muted">Loading shift data...</p></div>';
            if ($('#shift-loader').length === 0) {
                $('#attendance-data').html(loaderHtml);
            } else {
                $('#shift-loader').show();
                $('#attendance-data').find('.table-responsive, .shift-pagination').hide();
            }

            //refresh counts
            var url = "{{ route('shifts.index') }}";

            var token = "{{ csrf_token() }}";

            $.easyAjax({
                data: {
                    '_token': token,
                    year: year,
                    month: month,
                    department: department,
                    branch: branch,
                    designation: designation,
                    name: name,
                    active_status: activeStatus,
                    employee_type: employeeType,
                    userId: userId,
                    view_type: viewType,
                    week_start_date: weekStartDate,
                    page: page,
                },
                url: url,
                blockUI: false,
                container: '.content-wrapper',
                success: function(response) {
                    $('#attendance-data').html(response.data);
                    $('#attendance-data #change-year').selectpicker("refresh");
                    $('#attendance-data #change-month').selectpicker("refresh");
                },
                error: function() {
                    // Show error message
                    var errorHtml = '<div class="alert alert-danger mb-3">Error loading data. Please try again.</div>' + loaderHtml;
                    $('#attendance-data').html(errorHtml);
                }
            });

        }

        // Pagination clicks inside ajax content
        $('#attendance-data').on('click', '.shift-pagination a', function (e) {
            e.preventDefault();
            const href = $(this).attr('href');
            if (!href) return;

            const urlObj = new URL(href, window.location.origin);
            const page = urlObj.searchParams.get('page') || 1;
            showTable(true, page);
        });

        $('#attendance-data').on('click', '.view-attendance', function() {
            var attendanceID = $(this).data('attendance-id');
            var url = "{{ route('attendances.show', ':attendanceID') }}";
            url = url.replace(':attendanceID', attendanceID);

            $(MODAL_XL + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_XL, url);
        });

        if (manageEmployeeShiftPermission == 'all') {
            $('#attendance-data').on('click', '.change-shift', function(event) {
                var attendanceDate = $(this).data('attendance-date');
                var userData = $(this).closest('tr').children('td:first');
                var userID = $(this).data('user-id');
                var year = $('#year').val();
                var month = $('#month').val();

                var url = "{{ route('shifts.mark', [':userid', ':day', ':month', ':year']) }}";
                url = url.replace(':userid', userID);
                url = url.replace(':day', attendanceDate);
                url = url.replace(':month', month);
                url = url.replace(':year', year);

                $(MODAL_DEFAULT + ' ' + MODAL_HEADING).html('...');
                $.ajaxModal(MODAL_DEFAULT, url);
            });

            $('#attendance-data').on('click', '.change-shift-week', function(event) {
                var attendanceDate = $(this).data('attendance-date');
                var splitAttendance = attendanceDate.split('-');
                attendanceDate = splitAttendance[2];
                var userData = $(this).closest('tr').children('td:first');
                var userID = $(this).data('user-id');
                var year = splitAttendance[0];
                var month = splitAttendance[1];

                var url = "{{ route('shifts.mark', [':userid', ':day', ':month', ':year']) }}";
                url = url.replace(':userid', userID);
                url = url.replace(':day', attendanceDate);
                url = url.replace(':month', month);
                url = url.replace(':year', year);

                $(MODAL_DEFAULT + ' ' + MODAL_HEADING).html('...');
                $.ajaxModal(MODAL_DEFAULT, url);
            });
        }

        showTable(true);

        $('#export-all').click(function() {
            var year = $('#year').val();
            var month = $('#month').val();
            var department = $('#department').val();
            var userId = $('#user_id').val();
            var startDate = $('#week_start_date').val();
            var viewType = $('#view_type').val();

            var url =
                "{{ route('shifts.export_all', [':year', ':month', ':userId', ':department', ':startDate', ':viewType']) }}";
            url = url.replace(':year', year).replace(':month', month).replace(':userId', userId).replace(':department', department).replace(':startDate', startDate).replace(':viewType', viewType);
            window.location.href = url;

        });

        $('body').on('click', '.approve-request', function() {
            var id = $(this).data('request-id');
            var url = "{{ route('shifts-change.approve_request', ':id') }}";
            url = url.replace(':id', id);
            var token = '{{ csrf_token() }}';
            $.easyAjax({
                url: url,
                type: "POST",
                blockUI: true,
                container: '.content-wrapper',
                data: {
                    id: id,
                    _token: token
                },
                success: function(data) {
                    showTable();
                    $(MODAL_DEFAULT).modal('hide');
                }
            })

        });

        $('body').on('click', '.decline-request', function() {
            var id = $(this).data('request-id');
            var url = "{{ route('shifts-change.decline_request', ':id') }}";
            url = url.replace(':id', id);
            var token = '{{ csrf_token() }}';
            $.easyAjax({
                url: url,
                type: "POST",
                blockUI: true,
                container: '.content-wrapper',
                data: {
                    id: id,
                    _token: token
                },
                success: function(data) {
                    showTable();
                    $(MODAL_DEFAULT).modal('hide');
                }
            })

        });
    </script>
@endpush
