@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')
<style>
.search-status {
    display: flex;
}  
.search-status .btn-primary.rounded.f-14.p-2.mr-3 {
    margin-left: 20px;
}
</style>
    <form action="{{url('account/employees')}}" id='myForm' method="GET"> 
    <x-filters.filter-box>
     


    </x-filters.filter-box>

@endsection

@section('content')

<link rel="stylesheet" href="{{ asset('vendor/css/bootstrap-datepicker3.min.css') }}">

<div class="row">
    <div class="col-sm-12">
        <x-form id="save-attendance-data-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal text-capitalize border-bottom-grey">
                    Generate Slip </h4>
                <div class="row p-20">

                    <div class="col-lg-3 col-md-6">
                        <x-forms.select fieldId="department_id" :fieldLabel="__('app.department')"
                            fieldName="department_id" search="true">
                            <option value="0">--</option>
                            @foreach ($departments as $team)
                                <option value="{{ $team->id }}">{{ mb_ucwords($team->team_name) }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-md-9">
                        <div class="form-group my-3">
                            <x-forms.label fieldId="selectEmployee" :fieldLabel="__('app.menu.employees')"
                                fieldRequired="true">
                            </x-forms.label>
                            <x-forms.input-group>
                                <select class="form-control multiple-users" required multiple name="user_id[]"
                                    id="selectEmployee" data-live-search="true" data-size="8">
                                    @foreach ($employees as $item)
                                        <x-user-option :user="$item" :pill="true"/>
                                    @endforeach
                                </select>
                                <span id="user_error" class="text-danger"></span>
                            </x-forms.input-group>
                        </div>
                    </div>

                </div>
                <div class="row px-4 pb-4">
                    


                    

                    <div class="col-lg-3 col-md-6">
                        <x-forms.select fieldId="pf_deduction" :fieldLabel="__('PF Deduction')" fieldName="pf_deduction" search="true">
                            <option value="yes">@lang('app.yes')</option>
                            <option value="no">@lang('app.no')</option>
                        </x-forms.select>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <x-forms.select fieldId="month" :fieldLabel="__('app.month')" fieldName="month" search="true" fieldRequired="true">
                            <x-forms.months :selectedMonth="$month" fieldRequired="true"/>
                        </x-forms.select>
                        <div id="days_in_month"></div>
                        <span id="month_error" class="text-danger"></span>
                    </div>

                    <div class="col-lg-3 col-md-6">
                        <x-forms.select fieldId="year" :fieldLabel="__('app.year')" fieldName="year" search="true" fieldRequired="true">
                            <option value="">--</option>
                            @for ($i = $year; $i >= $year - 10; $i--)
                                <option @if ($i == $year) selected @endif
                                    value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </x-forms.select>
                        <span id="year_error" class="text-danger"></span>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="form-group my-3">
                            <x-forms.label fieldId="mark_attendance_by_month" :fieldLabel="__('Get Working Days'). ' ' . __('app.by')">
                            </x-forms.label>
                            <div class="d-flex">
                                <x-forms.radio fieldId="mark_attendance_by_machine" :fieldLabel="__('Attendance')" fieldName="mark_attendance_by" fieldValue="machine" checked="true">
                                </x-forms.radio>
                                <x-forms.radio fieldId="mark_attendance_by_custom" :fieldLabel="__('Custom')" fieldValue="custom" fieldName="mark_attendance_by"></x-forms.radio>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 wekk_off_div" >
                        <x-forms.number fieldId="wekk_off_div" :fieldLabel="__('Week Offs In Whole Month')" fieldName="week_off" fieldPlaceholder="Enter Week Offs">
                        </x-forms.number>
                    </div>
                    
                    <div class="col-lg-3 col-md-6 custom_working_days_div" style="display: none;">
                        <x-forms.number fieldId="custom_working_days" :fieldLabel="__('Working Days')" fieldName="custom_working_days" fieldPlaceholder="Enter working days">
                        </x-forms.number>
                    </div>

                </div>

                <x-form-actions>
                    <x-forms.button-primary class="mr-3" id="save-attendance-form" icon="check">@lang('app.save')
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('attendances.index')" class="border-0">@lang('app.cancel')
                    </x-forms.button-cancel>
                    <div id="success" class="ml-4 text-success"></div>
                    <div id="error" class="ml-4 text-danger"></div>
                </x-form-actions>

            </div>
        </x-form>

    </div>
</div>

<script>
    $(document).ready(function() {
        $("input[name='mark_attendance_by']").change(function() {
            if($(this).val() == 'custom') {
                $('.custom_working_days_div').show();
                $('.wekk_off_div').hide();
            } else {
                $('.custom_working_days_div').hide();
                $('.wekk_off_div').show();
            }
        });
    });
    
     $('#month').change(function() {
            var year = $('#year').val();
            var month = $(this).val();
            var daysInMonth = new Date(year, month, 0).getDate();
            $('#days_in_month').text('Total Days = '+ daysInMonth);
        });
</script>
<script src="{{ asset('vendor/jquery/bootstrap-datepicker.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $("#selectEmployee").selectpicker({
            actionsBox: true,
            selectAllText: "{{ __('modules.permission.selectAll') }}",
            deselectAllText: "{{ __('modules.permission.deselectAll') }}",
            multipleSeparator: " ",
            selectedTextFormat: "count > 8",
            countSelectedText: function(selected, total) {
                return selected + " {{ __('app.membersSelected') }} ";
            }
        });

        $('#multi_date').datepicker({
            linkedCalendars: false,
            multidate: true,
            todayHighlight: true,
            format: 'yyyy-mm-d'
        });

        $('input[type=radio][name=mark_attendance_by]').change(function() {
            if(this.value=='date') {
                // $('#multi_date').datepicker('clearDates').datepicker({
                //     linkedCalendars: false,
                //     multidate: true,
                //     todayHighlight: true,
                //     format: 'yyyy-mm-d'
                // });
            }

        });

        $('#work_from_type').change(function(){
            ($(this).val() == 'other') ? $('#other_place').show() : $('#other_place').hide();
        });

        $('#start_time, #end_time').timepicker({
            showMeridian: (company.time_format == 'H:i' ? false : true)
        });

        $('#department_id').change(function() {
            var id = $(this).val();
            var url = "{{ route('employees.by_department', ':id') }}";
            url = url.replace(':id', id);

            $.easyAjax({
                url: url,
                container: '#save-attendance-data-form',
                type: "GET",
                blockUI: true,
                data: $('#save-attendance-data-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        $('#selectEmployee').html(response.data);
                        $('#selectEmployee').selectpicker('refresh');
                    }
                }
            });
        });

        $('#save-attendance-form').click(function() {

            const url = "{{ route('generate_slip') }}";
            
            // Clear previous errors
            // $('.text-danger').html('');
    
            // var user = $('input[name="user_id"]').val();
            // var month = $('input[name="month"]').val();
            // var year = $('input[name="year"]').val();
    
            // if (user == '') {
            //     $('#user_error').html('<strong>employee is required</strong>');
            //     return;
            // }
            
            // if (month == '') {
            //     $('#month_error').html('<strong>Month is required</strong>');
            //     return;
            // }
    
            // if (year == '') {
            //     $('#year_error').html('<strong>Year is required</strong>');
            //     return;
            // }
            
            
            $.easyAjax({
                url: url,
                container: '#save-attendance-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-attendance-form",
                data: $('#save-attendance-data-form').serialize(),
                success: function (response) {
                    $('#success').text('Payroll Generated Successfully ');
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#error').text('Oops! Something Went Wrong! ');
                    console.log(jqXHR); // Log the error
                    
                    
                }
            });
        });

        $("input[name=mark_attendance_by]").click(function() {
            $(this).val() == 'date' ? $('.multi_date_div').removeClass('d-none') : $(
                '.multi_date_div').addClass('d-none');
            $(this).val() == 'date' ? $('.attendance_by_month').addClass('d-none') : $(
                '.attendance_by_month').removeClass('d-none');
        })

        init(RIGHT_MODAL);
    });
</script>
@endsection
