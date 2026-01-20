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
        <!-- Branch START -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Branch</p>
            <div class="select-status">
                <select class="form-control" style="width: 90px;" name="branch" id="branches">
                    
                    @if(isset($_GET['branch']) && $_GET['branch'] == 'all')
                    
                    <option selected value="all">@lang('app.all')</option>
                    @else
                    <option selected value="all">@lang('app.all')</option>
                    @endif
                    @foreach ($branches as $branch)
                    
                        @if(isset($_GET['branch']) && $_GET['branch'] == $branch->id)
                            <option selected value="{{ $branch->id }}">{{ ucfirst($branch->name) }}</option> 
                        @else
                        <option value="{{ $branch->id }}">{{ ucfirst($branch->name) }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
        
        <script>
          $('#branches').on('change', function () {
          var form = document.getElementById('myForm');
          form.submit();
            //   $('#submit').click();
          });
                          
        </script>
        <!-- Branch END -->        


        <!-- Department START -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Department</p>
            <div class="select-status">
                <select class="form-control"  style="width: 90px;" name="department" id="department">
                    @if(isset($_GET['department']) && $_GET['department'] == 'all')
                    
                    <option selected value="all">@lang('app.all')</option>
                    @else
                    <option selected value="all">@lang('app.all')</option>
                    @endif
                    
                    
                    @foreach ($departments as $department)
                        @if(isset($_GET['department']) && $_GET['department'] == $department->id)
                            <option selected value="{{ $department->id }}">{{ ucfirst($department->team_name) }}</option> 
                        @else
                        <option value="{{ $department->id }}">{{ ucfirst($department->team_name) }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
        <script>
          $('#department').on('change', function () {
          var form = document.getElementById('myForm');
          form.submit();
            //   $('#submit').click();
          });
                          
        </script>
        <!-- Department END -->        


        <!-- DESIGNATION START -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.designation')</p>
            <div class="select-status">
                <select class="form-control" style="width: 90px;" name="designation" id="designation">
                    
                     @if(isset($_GET['designation']) && $_GET['designation'] == 'all')
                    
                    <option selected value="all">@lang('app.all')</option>
                    @else
                    <option selected value="all">@lang('app.all')</option>
                    @endif
                    @foreach ($designations as $designation)
                        @if(isset($_GET['designation']) && $_GET['designation'] == $designation->id)
                            <option selected value="{{ $designation->id }}">{{ ucfirst($designation->name) }}</option> 
                        @else
                        <option value="{{ $designation->id }}">{{ ucfirst($designation->name) }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
        <script>
          $('#designation').on('change', function () {
          var form = document.getElementById('myForm');
          form.submit();
            //   $('#submit').click();
          });
                          
        </script>
        
        
        <!-- Offer Letter START -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('Offer Letter')</p>
            <div class="select-status">
                <select class="form-control" name="offer_letter" id="offer_letter">
                    
                    <option @if(isset($_GET['offer_letter']) && $_GET['offer_letter'] == 'all') selected @endif value="all">@lang('app.all')</option>
                    <option @if(isset($_GET['offer_letter']) && $_GET['offer_letter'] == 'yes') selected @endif value="yes">@lang('Yes')</option>
                    <option @if(isset($_GET['offer_letter']) && $_GET['offer_letter'] == 'no') selected @endif value="no">@lang('No')</option>
                    
                </select>
            </div>
        </div>
        
        <script>
          $('#offer_letter').on('change', function () {
          var form = document.getElementById('myForm');
          form.submit();
            //   $('#submit').click();
          });
                          
        </script>
        
        
        <!-- biometric -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('Bio Metric')</p>
            <div class="select-status">
                <select class="form-control" name="bio_met" id="bio_met">
                    
                    <option @if(isset($_GET['bio_met']) && $_GET['bio_met'] == 'all') selected @endif value="all">@lang('app.all')</option>
                    <option @if(isset($_GET['bio_met']) && $_GET['bio_met'] == 'yes') selected @endif value="yes">@lang('Yes')</option>
                    <option @if(isset($_GET['bio_met']) && $_GET['bio_met'] == 'no') selected @endif value="no">@lang('No')</option>
                    
                </select>
            </div>
        </div>
        
        <script>
          $('#bio_met').on('change', function () {
          var form = document.getElementById('myForm');
          form.submit();
            //   $('#submit').click();
          });
                          
        </script>
        
        <!-- doc status -->
        <!--<div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">-->
        <!--    <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('Document Status')</p>-->
        <!--    <div class="select-status">-->
        <!--        <select class="form-control" name="doc" id="doc">-->
                    
        <!--            <option @if(isset($_GET['doc']) && $_GET['doc'] == 'all') selected @endif value="all">@lang('app.all')</option>-->
        <!--            <option @if(isset($_GET['doc']) && $_GET['doc'] == 'completed') selected @endif value="full">@lang('Completed')</option>-->
        <!--            <option @if(isset($_GET['doc']) && $_GET['doc'] == 'partial') selected @endif value="partial">@lang('Partial')</option>-->
        <!--            <option @if(isset($_GET['doc']) && $_GET['doc'] == 'not_uploded') selected @endif value="not_uploded">@lang('Not Uploaded')</option>-->
                    
        <!--        </select>-->
        <!--    </div>-->
        <!--</div>-->
        
        <!--<script>-->
        <!--  $('#doc').on('change', function () {-->
        <!--  var form = document.getElementById('myForm');-->
        <!--  form.submit();-->
            <!--//   $('#submit').click();-->
        <!--  });-->
                          
        <!--</script>-->




        <!-- status START -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Status</p>
            <div class="select-status">
                <select class="form-control status" name="status" id="status">
                    <option value="active" @if(isset($_GET['status']) && $_GET['status'] == 'active') selected @endif >Active</option>
                    <option value="deactive" @if(isset($_GET['status']) && $_GET['status'] == 'deactive') selected @endif>Inactive</option>
                </select>
            </div>
        </div>
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">J. Form Status</p>
            <div class="select-status">
                <select class="form-control status" name="form_filled" id="">
                    <option value="all" @if(isset($_GET['form_filled']) && $_GET['form_filled'] == 'all') selected @endif >All</option>
                    <option value="0" @if(isset($_GET['form_filled']) && $_GET['form_filled'] == '0') selected @endif >Filled</option>
                    <option value="1" @if(isset($_GET['form_filled']) && $_GET['form_filled'] == '1') selected @endif>Not Filled</option>
                </select>
            </div>
        </div>
        
        <script>
          $('.status').on('change', function () {
          var form = document.getElementById('myForm');
          form.submit();
            //   $('#submit').click();
          });
                          
        </script>
        
        <!-- type START -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Emp. Type</p>
            <div class="select-status">
                <select class="form-control" name="type" id="type">
                    
                    <!--@if(isset($_GET['status']) && $_GET['status'] == 'all')-->
                    
                    <!--<option selected value="all">@lang('app.all')</option>-->
                    <!--@else-->
                    <!--<option selected value="all">@lang('app.all')</option>-->
                    <!--@endif-->
                    <option value="all" {{ (isset($_GET['type']) && $_GET['type'] == 'all') || !isset($_GET['type']) ? 'selected' : '' }}>All</option>
                    <option value="full_time" @if(isset($_GET['type']) && $_GET['type'] == 'full_time') selected @endif >Full Type</option>
                    <option value="part_time" @if(isset($_GET['type']) && $_GET['type'] == 'part_time') selected @endif>Part Time</option>
                </select>
            </div>
        </div>
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Account Status</p>
            <div class="select-status">
                <select class="form-control" name="account_status" id="account_status">
                    <option value="all" {{ (isset($_GET['account_status']) && $_GET['account_status'] == 'all') || !isset($_GET['account_status']) ? 'selected' : '' }}>All</option>
                    <option value="completed" @if(isset($_GET['account_status']) && $_GET['account_status'] == 'completed') selected @endif>Completed</option>
                    <option value="pending" @if(isset($_GET['account_status']) && $_GET['account_status'] == 'pending') selected @endif>Pending</option>
                </select>
            </div>
        </div>
        
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
    <!--<button type="button" id="toggleDocFilter" class="btn btn-outline-primary">-->
    <!--    Document Filters-->
    <!--</button>-->
</div>


        <script>
          $('#type').on('change', function () {
          var form = document.getElementById('myForm');
          form.submit();
            //   $('#submit').click();
          });
                          
        </script>
        
        <!-- DESIGNATION END -->

        <div class="text-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Search</p>
            <div class="search-status">
                <input type="text" class="form-control" @if(isset($_GET['search']) && $_GET['search']) value="{{$_GET['search']}}" @endif name="search" id="search" placeholder="EMP ID Or Name">

                <input type="submit" value="Search" class="btn-primary rounded f-14 p-2 mr-3"/>
            </div>
        </div>


    </x-filters.filter-box>

@endsection

@php
    $addEmployeePermission = user()->permission('add_employees');
    $addDesignationPermission = user()->permission('add_designation');
    $viewDesignationPermission = user()->permission('view_designation');
    
    
                    $canEdit = in_array($addEmployeePermission, ['all', 'added']);
@endphp

@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <!-- Add Task Export Buttons Start -->
        <div class="d-flex justify-content-between action-bar">

            <div id="table-actions" class="d-block d-lg-flex align-items-center">
                @if ($addEmployeePermission == 'all')
                    <x-forms.link-primary :link="route('employees.create')" class="mr-3 openRightModal" icon="plus">
                        @lang('app.add')
                        @lang('app.employee')
                    </x-forms.link-primary>
                @endif

                @if ($addEmployeePermission == 'all')
                    <x-forms.link-secondary :link="route('employees.import')" class="mr-3 openRightModal mb-2 mb-lg-0"
                                            icon="file-upload">
                        @lang('app.importExcel')
                    </x-forms.link-secondary>
                @endif
<form action="{{ url('account/employees/excel') }}" method="GET">
    @foreach (request()->query() as $key => $value)
    @if(is_array($value))
        @foreach ($value as $item)
            <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
        @endforeach
    @else
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
    @endif
@endforeach


    <input type="submit" value="Export" class="btn-primary rounded f-14 p-2 mr-3">
</form>
<div class="counnt">
Employee Count: {{$totalEmployees}}
</div>
            </div>

            <x-datatable.actions>
                <div class="select-status mr-3 pl-3">
                    <select name="action_type" class="form-control select-picker" id="quick-action-type" disabled>
                        <option value="">@lang('app.selectAction')</option>
                        <option value="change-status">@lang('modules.tasks.changeStatus')</option>
                        <option value="delete">@lang('app.delete')</option>
                    </select>
                </div>
                <div class="select-status mr-3 d-none quick-action-field" id="change-status-action">
                    <select name="status" class="form-control select-picker">
                        <option value="deactive">@lang('app.inactive')</option>
                        <option value="active">@lang('app.active')</option>
                    </select>
                </div>
            </x-datatable.actions>

        </div>
        <!-- Add Task Export Buttons End -->
        <!-- Task Box Start -->
        <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
            <!--{!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}-->
            <table class="table">
              <thead>
                <tr>
                  <th>S.N.</th>
                  <th>Employee ID</th>
                  <th>Branch</th>
                  <th>Department</th>
                  <th>Designation</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Status</th>
                  <th>Doc (Click To View)</th>
                  <th>Account Status</th>
                  <th>Action</th>
                  <th>Attendance</th>
                </tr>
              </thead>
              <tbody>
                 <!--{!! $dataTable->table(['class' => 'table table-hover border-0 w-100']) !!}-->

                @foreach($employees as $key=>$emp)
                @php
                    $isMissingMeta =
                        empty($emp->branche_name) ||
                        empty($emp->deapartment) ||
                        empty($emp->designation_name);
                
                @endphp

                <tr>
                  <td>{{ ($employees->currentPage() - 1) * $employees->perPage() + $loop->iteration }}</td>
                  <td>{{$emp->employee_id}}</td>
                  <td>{{$emp->branche_name}}</td>
                  <td>{{$emp->deapartment}}</td>
                  <td>{{$emp->designation_name}}</td>
                  <td>
                        {{-- Employee Name --}}
                        @if($isMissingMeta && $canEdit)
                            <a href="{{ url('employees/bulk-update') }}?employee_id={{ $emp->employee_id }}"
                               title="Department / Designation / Branch missing"
                               style="color:red; font-weight:bold; text-decoration:none;">
                                {{ $emp->employee_name }}
                            </a>
                        @else
                            <span style="{{ $isMissingMeta ? 'color:red; font-weight:bold;' : '' }}">
                                {{ $emp->employee_name }}
                            </span>
                        @endif
                    
                        {{-- ❗ icon (only for users with permission) --}}
                        @if($isMissingMeta && $canEdit)
                            <a href="{{ url('employees/bulk-update') }}?employee_id={{ $emp->employee_id }}"
                               title="Department / Designation / Branch missing"
                               style="color:red; font-weight:bold; margin-left:5px; text-decoration:none;">
                                ❗
                            </a>
                        @endif
                    </td>
                  <td>{{$emp->email}}</td>
                  <td>{{$emp->status}}</td>
                    @php
                        $documents = DB::table('employee_docs')->where('user_id', $emp->user_iddd)->get();
                        $hasAdharCard = false;
                        $hasPanCard = false;
                        $has10th = false;
                        $has12th = false;
                        $hasPhoto = false;
                        $hasAck = false;
                    
                        foreach($documents as $d) {
                            if (strpos($d->name, "Adhar Card") !== false) {
                                $hasAdharCard = true;
                            }
                    
                            if (strpos($d->name, "Pan Card") !== false) {
                                $hasPanCard = true;
                            }
                    
                            if (strpos($d->name, "10th Marksheet") !== false) {
                                $has10th = true;
                            }
                    
                            if (strpos($d->name, "12th Marksheet") !== false) {
                                $has12th = true;
                            }
                    
                            if (strpos($d->name, "Passport Size Photo") !== false) {
                                $hasPhoto = true;
                            }
                            if (strpos($d->name, "Acknowledgment Letter") !== false) {
                                $hasAck = true;
                            }
                        }
                    
                        $isPending = count($documents) < 1;
                        $isPartial = !$hasAdharCard || !$hasPanCard || !$has10th || !$has12th || !$hasPhoto || !$hasAck;
                    @endphp
                    
                    <td>
                        @if($isPending)
                            <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#myModal{{$emp->employee_id}}">Pending</button>
                        @elseif($isPartial)
                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#myModal{{$emp->employee_id}}">Partial</button>
                        @else
                            <button type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal{{$emp->employee_id}}">Completed</button>
                        @endif
                    
                        <!-- Bootstrap Modal -->
                        <div class="modal fade" id="myModal{{$emp->employee_id}}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="myModalLabel">Documents</h4>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>
                                            @foreach($documents as $d)
                                                {{$d->name . " , "}}
                                            @endforeach
                                        </p>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if ($emp->account_details == 'Yes')
                            <span style="color: green; font-weight: bold;">Completed</span>
                        @else
                            <span style="color: red; font-weight: bold;">Pending</span>
                        @endif
                    </td>
                  <td>
                      <a href="employees/{{$emp->user_iddd}}" class="dropdown-item"><svg class="svg-inline--fa fa-eye fa-w-18 mr-2" aria-hidden="true" focusable="false" data-prefix="fa" data-icon="eye" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg=""><path fill="currentColor" d="M572.52 241.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z"></path></svg><!-- <i class="fa fa-eye mr-2"></i> Font Awesome fontawesome.com -->View</a>
                    <!--<a href="#" class="btn btn-primary">Edit</a>-->
                    <!--<a href="#" class="btn btn-danger">Delete</a>-->
                  </td>
                  <td>
                    @if ($emp->designation_id == 12)
                      <a href="{{url('accounts/attendance/agent')}}/{{$emp->user_iddd}}" class="dropdown-item"><svg class="svg-inline--fa fa-eye fa-w-18 mr-2" aria-hidden="true" focusable="false" data-prefix="fa" data-icon="eye" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg=""><path fill="currentColor" d="M572.52 241.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z"></path></svg><!-- <i class="fa fa-eye mr-2"></i> Font Awesome fontawesome.com -->View</a>
                    @else
                        <a href="{{url('accounts/attendance/non-csa')}}/{{$emp->user_iddd}}" class="dropdown-item"><svg class="svg-inline--fa fa-eye fa-w-18 mr-2" aria-hidden="true" focusable="false" data-prefix="fa" data-icon="eye" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg=""><path fill="currentColor" d="M572.52 241.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z"></path></svg><!-- <i class="fa fa-eye mr-2"></i> Font Awesome fontawesome.com -->View</a>
                    @endif
                    <!--<a href="#" class="btn btn-primary">Edit</a>-->
                    <!--<a href="#" class="btn btn-danger">Delete</a>-->
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
{{ $employees->appends(request()->all())->links() }}
        </div>
        <!-- Task Box End -->
    </div>
    <button class="d-none" id="submit" type="submit">
    </form>
    <!-- CONTENT WRAPPER END -->

@endsection

@push('scripts')
    @include('sections.datatable_js')

    <script>

        var startDate = null;
        var endDate = null;
        var lastStartDate = null;
        var lastEndDate = null;

        @if(request('startDate') != '' && request('endDate') != '' )
            startDate = '{{ request("startDate") }}';
        endDate = '{{ request("endDate") }}';
        @endif

            @if(request('lastStartDate') !=='' && request('lastEndDate') !=='' )
            lastStartDate = '{{ request("lastStartDate") }}';
        lastEndDate = '{{ request("lastEndDate") }}';
        @endif

        $('#employees-table').on('preXhr.dt', function (e, settings, data) {
            const status = $('#status').val();
            const employee = $('#employee').val();
            const role = $('#role').val();
            const skill = $('#skill').val();
            const designation = $('#designation').val();
            const department = $('#department').val();
            const branch = $('#branch').val();
            const searchText = $('#search-text-field').val();
            data['status'] = status;
            data['employee'] = employee;
            data['branch'] = branch;
            data['role'] = role;
            data['skill'] = skill;
            data['designation'] = designation;
            data['department'] = department;
            data['searchText'] = searchText;

            /* If any of these following filters are applied, then dashboard conditions will not work  */
            if (status == "all" || employee == "all" || role == "all" || designation == "all" || searchText == "") {
                data['startDate'] = startDate;
                data['endDate'] = endDate;
                data['lastStartDate'] = lastStartDate;
                data['lastEndDate'] = lastEndDate;
            }

        });

        const showTable = () => {
            window.LaravelDataTables["employees-table"].draw(false);
        }

        $('#employee, #status, #role, #skill, #designation, #branch, #department').on('change keyup',
            function () {
                if ($('#status').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#employee').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#role').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#designation').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#department').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else if ($('#branch').val() != "all") {
                    $('#reset-filters').removeClass('d-none');
                } else {
                    $('#reset-filters').addClass('d-none');
                }
                showTable();
            });

        $('#search-text-field').on('keyup', function () {
            if ($('#search-text-field').val() != "") {
                $('#reset-filters').removeClass('d-none');
                showTable();
            }
        });

        $('#reset-filters, #reset-filters-2').click(function () {
            $('#filter-form')[0].reset();
            $('.filter-box .select-picker').selectpicker("refresh");
            $('#reset-filters').addClass('d-none');
            showTable();
        });


        $('#quick-action-type').change(function () {
            const actionValue = $(this).val();
            if (actionValue != '') {
                $('#quick-action-apply').removeAttr('disabled');

                if (actionValue == 'change-status') {
                    $('.quick-action-field').addClass('d-none');
                    $('#change-status-action').removeClass('d-none');
                } else {
                    $('.quick-action-field').addClass('d-none');
                }
            } else {
                $('#quick-action-apply').attr('disabled', true);
                $('.quick-action-field').addClass('d-none');
            }
        });

        $('#quick-action-apply').click(function () {
            const actionValue = $('#quick-action-type').val();
            if (actionValue == 'delete') {
                Swal.fire({
                    title: "@lang('messages.sweetAlertTitle')",
                    text: "@lang('messages.recoverRecord')",
                    icon: 'warning',
                    showCancelButton: true,
                    focusConfirm: false,
                    confirmButtonText: "@lang('messages.confirmDelete')",
                    cancelButtonText: "@lang('app.cancel')",
                    customClass: {
                        confirmButton: 'btn btn-primary mr-3',
                        cancelButton: 'btn btn-secondary'
                    },
                    showClass: {
                        popup: 'swal2-noanimation',
                        backdrop: 'swal2-noanimation'
                    },
                    buttonsStyling: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        applyQuickAction();
                    }
                });

            } else {
                applyQuickAction();
            }
        });

        $('body').on('click', '.delete-table-row', function () {
            var id = $(this).data('user-id');
            Swal.fire({
                title: "@lang('messages.sweetAlertTitle')",
                text: "@lang('messages.recoverRecord')",
                icon: 'warning',
                showCancelButton: true,
                focusConfirm: false,
                confirmButtonText: "@lang('messages.confirmDelete')",
                cancelButtonText: "@lang('app.cancel')",
                customClass: {
                    confirmButton: 'btn btn-primary mr-3',
                    cancelButton: 'btn btn-secondary'
                },
                showClass: {
                    popup: 'swal2-noanimation',
                    backdrop: 'swal2-noanimation'
                },
                buttonsStyling: false
            }).then((result) => {
                if (result.isConfirmed) {
                    var url = "{{ route('employees.destroy', ':id') }}";
                    url = url.replace(':id', id);

                    var token = "{{ csrf_token() }}";

                    $.easyAjax({
                        type: 'POST',
                        url: url,
                        blockUI: true,
                        data: {
                            '_token': token,
                            '_method': 'DELETE'
                        },
                        success: function (response) {
                            if (response.status == "success") {
                                showTable();
                            }
                        }
                    });
                }
            });
        });

        const applyQuickAction = () => {
            var rowdIds = $("#employees-table input:checkbox:checked").map(function () {
                return $(this).val();
            }).get();

            var url = "{{ route('employees.apply_quick_action') }}?row_ids=" + rowdIds;

            $.easyAjax({
                url: url,
                container: '#quick-action-form',
                type: "POST",
                disableButton: true,
                buttonSelector: "#quick-action-apply",
                data: $('#quick-action-form').serialize(),
                blockUI: true,
                success: function (response) {
                    if (response.status == 'success') {
                        showTable();
                        resetActionButtons();
                        deSelectAll();
                        $('#quick-action-form').hide();
                    }
                }
            })
        };


        $('body').on('change', '.assign_role', function () {
            var id = $(this).data('user-id');
            var role = $(this).val();
            var token = "{{ csrf_token() }}";

            if (typeof id !== 'undefined') {
                $.easyAjax({
                    url: "{{ route('employees.assign_role') }}",
                    type: "POST",
                    blockUI: true,
                    container: '#employees-table',
                    data: {
                        role: role,
                        userId: id,
                        _token: token
                    },
                    success: function (response) {
                        if (response.status == "success") {
                            window.LaravelDataTables["employees-table"].draw(false);
                        }
                    }
                })
            }

        });

        $('#designation-setting').click(function () {
            const url = "{{ route('designations.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        })

        $('.department-setting').click(function () {
            const url = "{{ route('departments.create') }}";
            $(MODAL_LG + ' ' + MODAL_HEADING).html('...');
            $.ajaxModal(MODAL_LG, url);
        });
    </script>
    
    
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var duration = 1 * 1000; // 3 seconds
            var end = Date.now() + duration;
    
            (function frame() {
                confetti({
                    particleCount: 5,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0 }
                });
                confetti({
                    particleCount: 5,
                    angle: 120,
                    spread: 55,
                    origin: { x: 1 }
                });
    
                if (Date.now() < end) {
                    requestAnimationFrame(frame);
                }
            }());
        });
    </script>

@endpush