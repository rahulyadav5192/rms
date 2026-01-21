@extends('layouts.app')

@push('styles')
    <style>
        .attendance-total {
            width: 10%;
        }

        .table .thead-light th,
        .table tr td,
        .table h5 {
            font-size: 12px;
        }

.input-with-icon {
    position: relative;
}

.input-with-icon input {
    padding-right: 30px; /* Adjust padding to make room for the icon */
}

.input-with-icon .search-icon {
    position: absolute;
    right: 10px; /* Position the icon on the right side */
    top: 50%;
    transform: translateY(-50%);
    cursor: pointer;
    display: none; /* Hide the icon initially */
}

    </style>
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
                        <x-user-option :user="$item" :selected="request('employee_id') == $item->id"></x-user-option>
                    @empty
                        <x-user-option :user="user()"></x-user-option>
                    @endforelse
                </select>
            </div>
        </div>

        
            
        @if ($viewAttendancePermission == 'all')
            <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
                <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('Branch')</p>
                <div class="select-status">
                    <select class="form-control select-picker" name="branch" id="branch" data-live-search="true"
                            data-size="8">
                        <option value="all">@lang('app.all')</option>
                        @foreach ($branch as $b)
                            <option value="{{ $b->id }}">{{ ucfirst($b->name) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
                <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.department')</p>
                <div class="select-status">
                    <select class="form-control select-picker" name="department" id="department" data-live-search="true"
                            data-size="8">
                        <option value="all">@lang('app.all')</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}">{{ ucfirst($department->team_name) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
                <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.designation')</p>
                <div class="select-status">
                    <select class="form-control select-picker" name="designation" id="designation" data-live-search="true"
                            data-size="8">
                        <option value="all">@lang('app.all')</option>
                        @foreach ($designations as $designation)
                            <option value="{{ $designation->id }}">{{ ucfirst($designation->name) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        @else 
        <input type="hidden" name="department" id="department" value="all">
            <input type="hidden" name="designation" id="designation" value="all">
        @endif

        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.month')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="month" id="month" data-live-search="true"
                        data-size="8">
                    <x-forms.months :selectedMonth="$month" fieldRequired="true"/>
                </select>
            </div>
        </div>

        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.year')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="year" id="year" data-live-search="true" data-size="8">
                    @for ($i = $year; $i >= $year - 4; $i--)
                        <option @if ($i == $year) selected @endif value="{{ $i }}">{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('modules.attendance.late')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="late" id="late">
                    <option value="all">@lang('app.all')</option>
                    <option @if (request('late') == 'yes')
                            selected
                            @endif value="yes">@lang('app.yes')</option>
                    <option value="no">@lang('app.no')</option>
                </select>
            </div>
        </div>
        
         <!-- New filters for status and name -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0   ">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.status')</p>
            <div class="select-status">
                <select class="form-control select-picker" name="active_status" id="active_status">
                    <option @if (request('status') == 'active')
                            selected
                            @endif value="0">@lang('app.active')</option>
                    <option @if (request('status') == 'inactive')
                            selected
                            @endif value="1">@lang('app.inactive')</option>
                </select>
            </div>
        </div>

        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0 input-with-icon">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.name')</p>
            <div class="select-status position-relative">
                <input type="text" class="form-control" name="name" id="name" placeholder="Name Or Id">
                <i class="fa fa-search search-icon" onclick="nameCall()"></i>
            </div>
        </div>




        <!-- RESET START -->
        <div class="select-box d-flex py-1 px-lg-2 px-md-2 px-0">
            <x-forms.button-secondary class="btn-xs d-none" id="reset-filters" icon="times-circle">
                @lang('app.clearFilters')
            </x-forms.button-secondary>
        </div>
        <!-- RESET END -->

    </x-filters.filter-box>

@endsection

@php
    $addAttendancePermission = user()->permission('add_attendance');
@endphp

@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper px-4">

        <div class="d-flex">
            <div id="table-actions" class="flex-grow-1 align-items-center">
                @if ($addAttendancePermission == 'all' || $addAttendancePermission == 'added')
                    <x-forms.link-primary :link="route('attendances.create')" class="mr-3 openRightModal float-left"
                                          icon="plus">
                        @lang('modules.attendance.markAttendance')
                    </x-forms.link-primary>
                @endif
                <x-forms.button-secondary id="export-all" class="mr-3 mb-2 mb-lg-0" icon="file-export">
                    @lang('app.exportExcel')
                </x-forms.button-secondary>
                @if ($addAttendancePermission == 'all' || $addAttendancePermission == 'added')
                    <x-forms.link-secondary :link="route('attendances.import')" class="mr-3 openRightModal float-left"
                                            icon="file-upload">
                        @lang('app.importExcel')
                    </x-forms.link-secondary>
                @endif
            </div>

            <script>
                // $(document).ready(function() {
                //     $('#department').on('change', function() {
                //         if ($(this).val() === 'all') {
                //             $('#export-all').hide();
                //         } else {
                //             $('#export-all').show();
                //         }
                //     });
                
                //     // Trigger the change event on page load to set the correct initial state
                //     $('#department').trigger('change');
                // });

            </script>
            <div class="btn-group" role="group">
                <a href="{{ route('attendances.index') }}" class="btn btn-secondary f-14 btn-active"
                   data-toggle="tooltip"
                   data-original-title="@lang('app.summary')"><i class="side-icon bi bi-list-ul"></i></a>

                <a href="{{ route('attendances.by_member') }}" class="btn btn-secondary f-14" data-toggle="tooltip"
                   data-original-title="@lang('modules.attendance.attendanceByMember')"><i
                        class="side-icon bi bi-person"></i></a>

                <!--<a href="{{ route('attendances.by_hour') }}" class="btn btn-secondary f-14" data-toggle="tooltip"-->
                <!--   data-original-title="@lang('modules.attendance.attendanceByHour')"><i class="fa fa-clock"></i></a>-->

                @if (attendance_setting()->save_current_location)
                    <a href="{{ route('attendances.by_map_location') }}" class="btn btn-secondary f-14"
                       data-toggle="tooltip" data-original-title="@lang('modules.attendance.attendanceByLocation')"><i
                            class="fa fa-map-marked-alt"></i></a>
                @endif

            </div>
        </div>

        <!-- Task Box Start -->
        <x-cards.data class="mt-3">
            <div class="row">
               <div class="col-md-12">
                    <span class="f-w-500 mr-1">@lang('app.note'):</span> <i class="fa fa-star text-warning"></i> <i
                        class="fa fa-arrow-right text-lightest f-11 mx-1"></i> @lang('app.menu.holiday') &nbsp;|&nbsp;
                    <i class="fa fa-check text-primary"></i> <i class="fa fa-arrow-right text-lightest f-11 mx-1"></i>
                    @lang('modules.attendance.present') &nbsp;|&nbsp; <i class="fa fa-star-half-alt text-primary"></i> <i
                        class="fa fa-arrow-right text-lightest f-11 mx-1"></i>
                    @lang('modules.attendance.halfDay') &nbsp;|&nbsp; <i class="fa fa-exclamation-circle text-primary"></i> <i
                        class="fa fa-arrow-right text-lightest f-11 mx-1"></i>
                    @lang('modules.attendance.late') &nbsp;|&nbsp; <i class="fa fa-times text-lightest"></i> <i
                        class="fa fa-arrow-right text-lightest f-11 mx-1"></i>
                    @lang('modules.attendance.absent') &nbsp;|&nbsp; <i class="fa fa-plane-departure text-danger"></i> <i
                    class="fa fa-arrow-right text-lightest f-11 mx-1"></i>
                    @lang('modules.attendance.leave')

                </div>
            </div>

            <div class="row">
                <div class="col-md-12" id="attendance-data"></div>
            </div>
        </x-cards.data>
        <!-- Task Box End -->
    </div>
    <!-- CONTENT WRAPPER END -->

@endsection

@push('scripts')

    <script>

        var viewAttendancePermission = '{{$viewAttendancePermission}}';

        userRoleAdmin = "{{ in_array('admin', user_roles()) }}";
        
        
        $('#name').on('input', function () {
            if ($(this).val() !== '') {
                $('.search-icon').show();
            } else {
                $('.search-icon').hide();
            }
        });
        
        $(document).ready(function() {
            $('.search-icon').hide(); // Hide the search icon initially
        });
        
        
        function nameCall(){
            $('#reset-filters').removeClass('d-none');
            showTable();
        }


        $('#active_status').on('change',function() {
            $('#reset-filters').removeClass('d-none');
            showTable(true, 1);
        });
        $('#branch').on('change',function() {
            $('#reset-filters').removeClass('d-none');
            showTable(true, 1);
        });

        $('#user_id, #department, #designation, #month, #year, #late',).on('change', function () {
            if ($('#user_id').val() != "all") {
                $('#reset-filters').removeClass('d-none');
                showTable(true, 1);
            } else if ($('#department').val() != "all") {
                $('#reset-filters').removeClass('d-none');
                showTable(true, 1);
            } else if ($('#designation').val() != "all") {
                $('#reset-filters').removeClass('d-none');
                showTable(true, 1);
            } else if ($('#month').val() != "all") {
                $('#reset-filters').removeClass('d-none');
                showTable(true, 1);
            } else if ($('#year').val() != "all") {
                $('#reset-filters').removeClass('d-none');
                showTable(true, 1);
            } else if ($('#late').val() != "all") {
                $('#reset-filters').removeClass('d-none');
                showTable(true, 1);
            } else {
                $('#reset-filters').addClass('d-none');
                showTable(true, 1);
            }
        });

        $('#reset-filters').click(function () {
            $('#filter-form')[0].reset();
            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable(true, 1);
        });

        function showTable(loading = true, page = 1) {

            var year = $('#year').val();
            var month = $('#month').val();

            var userId = $('#user_id').val();
            var department = $('#department').val();
            var branch = $('#branch').val();
            var designation = $('#designation').val();
            var late = $('#late').val();
            var active_status = $('#active_status').val();
            var name = $('#name').val();

            //refresh counts
            var url = "{{ route('attendances.index') }}";

            var token = "{{ csrf_token() }}";
            
            console.log(active_status);
            // console.log(name);
            // console.log(late);

            if (loading) {
                $('#attendance-data').html(
                    '<div class="text-center my-4" id="attendance-inline-loader">' +
                    '<i class="fa fa-spinner fa-spin mr-2"></i> Loading...' +
                    '</div>'
                );
            }
            
            $.easyAjax({
                data: {
                    '_token': token,
                    year: year,
                    month: month,
                    department: department,
                    designation: designation,
                    late: late,
                    userId: userId,
                    active_status: active_status,
                    name: name,
                    branch: branch,
                    page: page,
                },
                url: url,
                blockUI: loading,
                container: '.content-wrapper',
                success: function (response) {
                    $('#attendance-data').html(response.data);
                    $('#attendance-inline-loader').remove();
                },
                error: function (xhr) {
                    // If request fails, don't leave the screen blank
                    $('#attendance-data').html(
                        '<div class="alert alert-danger mt-3">Failed to load attendance data. Please refresh the page.</div>'
                    );
                    console.log('Attendance load failed', xhr);
                    $('#attendance-inline-loader').remove();
                }
            });

        }

        if(viewAttendancePermission == 'owned' || userRoleAdmin == true) {
            $('#attendance-data').on('click', '.view-attendance', function () {
                var attendanceID = $(this).data('attendance-id');
                var url = "{{ route('attendances.show', ':attendanceID') }}";
                url = url.replace(':attendanceID', attendanceID);

                $(MODAL_XL + ' ' + MODAL_HEADING).html('...');
                $.ajaxModal(MODAL_XL, url);
            });
        }

        $('#attendance-data').on('click', '.edit-attendance', function (event) {
            var attendanceDate = $(this).data('attendance-date');
            var userData = $(this).closest('tr').children('td:first');
            var userID = $(this).data('user-id');
            var year = $('#year').val();
            var month = $('#month').val();

            var url = "{{ route('attendances.mark', [':userid', ':day', ':month', ':year']) }}";
            url = url.replace(':userid', userID);
            url = url.replace(':day', attendanceDate);
            url = url.replace(':month', month);
            url = url.replace(':year', year);

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_XL, url);
        });

        function editAttendance(id) {
            var url = "{{ route('attendances.edit', [':id']) }}";
            url = url.replace(':id', id);

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_XL, url);
        }

        function addAttendance(userID) {
            var date = $('#date').val();
            const attendanceDate = date.split("-");
            let dayTime = attendanceDate[2];
            dayTime = dayTime.split(' ');
            let day = dayTime[0];
            let month = attendanceDate[1];
            let year = attendanceDate[0];

            var url = "{{ route('attendances.add-user-attendance', [':userid', ':day', ':month', ':year']) }}";
            url = url.replace(':userid', userID);
            url = url.replace(':day', day);
            url = url.replace(':month', month);
            url = url.replace(':year', year);

            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        }

        // Pagination clicks inside ajax content
        $('#attendance-data').on('click', '.attendance-pagination a', function (e) {
            e.preventDefault();
            const href = $(this).attr('href');
            if (!href) return;

            const urlObj = new URL(href, window.location.origin);
            const page = urlObj.searchParams.get('page') || 1;
            showTable(true, page);
        });

        // Initial load: show inline loader while data is fetched
        showTable(true, 1);

        $('#export-all').click(function () {
            var year = $('#year').val();
            var month = $('#month').val();
            var late = $('#late').val();
            var department = $('#department').val();
            var designation = $('#designation').val();
            var userId = $('#user_id').val();
            var active_status = $('#active_status').val();
            var name = $('#name').val();
            var branch = $('#branch').val();
            
            if (!name) { // Checks if name is null, undefined, or an empty string
                name = "NO";
            }
                        

            var url =
                "{{ route('attendances.export_all_attendance', [':year', ':month', ':userId', ':late', ':department', ':designation', ':name',':branch']) }}";
            url = url.replace(':year', year).replace(':month', month).replace(':userId', userId).replace(':late',
                late).replace(':department', department).replace(':designation', designation).replace(':name', name).replace(':branch', branch);
            window.location.href = url;

        });
    </script>

@endpush
