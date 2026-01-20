@extends('layouts.app')


@section('filter-section')
<style>
.search-status {
    display: flex;
}  
.search-status .btn-primary.rounded.f-14.p-2.mr-3 {
    margin-left: 20px;
}
</style>
    <form action="{{url('account/my_team')}}/{{$this_de}}" id='myForm' method="GET"> 
    <x-filters.filter-box>
        <!-- Branch START -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Branch</p>
            <div class="select-status">
                <select class="form-control" name="branch" id="branches">
                    
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
            <!--<p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Department</p>-->
            <!--<div class="select-status">-->
            <!--    <select class="form-control" name="department" id="department">-->
            <!--        @if(isset($_GET['department']) && $_GET['department'] == 'all')-->
                    
            <!--        <option selected value="all">@lang('app.all')</option>-->
            <!--        @else-->
            <!--        <option selected value="all">@lang('app.all')</option>-->
            <!--        @endif-->
                    
                    
            <!--        @foreach ($departments as $department)-->
            <!--            @if(isset($_GET['department']) && $_GET['department'] == $department->id)-->
            <!--                <option selected value="{{ $department->id }}">{{ ucfirst($department->team_name) }}</option> -->
            <!--            @else-->
            <!--            <option value="{{ $department->id }}">{{ ucfirst($department->team_name) }}</option>-->
            <!--            @endif-->
            <!--        @endforeach-->
            <!--    </select>-->
            <!--</div>-->
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
        <!--<div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">-->
        <!--    <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">@lang('app.designation')</p>-->
        <!--    <div class="select-status">-->
        <!--        <select class="form-control" name="designation" id="designation">-->
                    
        <!--             @if(isset($_GET['designation']) && $_GET['designation'] == 'all')-->
                    
        <!--            <option selected value="all">@lang('app.all')</option>-->
        <!--            @else-->
        <!--            <option selected value="all">@lang('app.all')</option>-->
        <!--            @endif-->
        <!--            @foreach ($designations as $designation)-->
        <!--                @if(isset($_GET['designation']) && $_GET['designation'] == $designation->id)-->
        <!--                    <option selected value="{{ $designation->id }}">{{ ucfirst($designation->name) }}</option> -->
        <!--                @else-->
        <!--                <option value="{{ $designation->id }}">{{ ucfirst($designation->name) }}</option>-->
        <!--                @endif-->
        <!--            @endforeach-->
        <!--        </select>-->
        <!--    </div>-->
        <!--</div>-->
        
        <script>
          $('#designation').on('change', function () {
          var form = document.getElementById('myForm');
          form.submit();
            //   $('#submit').click();
          });
                          
        </script>
        
        <!-- DESIGNATION END -->




        <!-- DESIGNATION START -->
        <div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Status</p>
            <div class="select-status">
                <select class="form-control" name="status" id="status">
                    
                    <!--@if(isset($_GET['status']) && $_GET['status'] == 'all')-->
                    
                    <!--<option selected value="all">@lang('app.all')</option>-->
                    <!--@else-->
                    <!--<option selected value="all">@lang('app.all')</option>-->
                    <!--@endif-->
                    <option value="active" @if(isset($_GET['status']) && $_GET['status'] == 'active') selected @endif >Active</option>
                    <option value="deactive" @if(isset($_GET['status']) && $_GET['status'] == 'deactive') selected @endif>Inactive</option>
                </select>
            </div>
        </div>
        
        <script>
          $('#status').on('change', function () {
          var form = document.getElementById('myForm');
          form.submit();
            //   $('#submit').click();
          });
                          
        </script>
        
        <!-- DESIGNATION END -->

        <div class="text-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Search</p>
            <div class="search-status">
                <input type="text" class="form-control" name="search" @if(isset($_GET['search'])) value="{{$_GET['search']}}" @endif id="search" placeholder="EMP ID Or Name">

                <input type="submit" value="Search" class="btn-primary rounded f-14 p-2 mr-3"/>
            </div>
        </div>


    </x-filters.filter-box>

@endsection

@php
    $addEmployeePermission = user()->permission('add_employees');
    $addDesignationPermission = user()->permission('add_designation');
    $viewDesignationPermission = user()->permission('view_designation');
@endphp

@section('content')
    <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <!-- Add Task Export Buttons Start -->
        <div class="d-flex justify-content-between action-bar">

            <div id="table-actions" class="d-block d-lg-flex align-items-center">
                @if ($addEmployeePermission == 'all')
                    <!--<x-forms.link-primary :link="route('employees.create')" class="mr-3 openRightModal" icon="plus">-->
                    <!--    @lang('app.add')-->
                    <!--    @lang('app.employee')-->
                    <!--</x-forms.link-primary>-->
                @endif

                <!--@if ($addEmployeePermission == 'all')
                    <x-forms.link-secondary :link="route('employees.import')" class="mr-3 openRightModal mb-2 mb-lg-0"
                                            icon="file-upload">
                        @lang('app.importExcel')
                    </x-forms.link-secondary>
                @endif -->
<form action="{{ url('account/employees/excel') }}" method="GET">
    @foreach (request()->query() as $key => $value)
        <input type="hidden" name="{{ $key }}" value="{{ $value }}">
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
            <table class="table">
              <thead>
                <tr>
                  <th>Employee ID</th>
                  <th>Branch</th>
                  <th>Department</th>
                  <th>Designation</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Status</th>
                  <th>Doc</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>

                @foreach($employees as $key=>$emp)
                <tr>
                  <td>{{$emp->employee_id}}</td>
                  <td>{{$emp->branche_name}}</td>
                  <td>{{$emp->deapartment}}</td>
                  <td>{{$emp->designation_name}}</td>
                  <td>{{$emp->employee_name}}</td>
                  <td>{{$emp->email}}</td>
                  <td>{{$emp->status}}</td>
@php
    $documents = DB::table('employee_docs')->where('user_id', $emp->user_iddd)->get();
    $hasAdharCard = false;
    $hasPanCard = false;
    $has10th = false;
    $has12th = false;
    $hasPhoto = false;

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
    }

    $isPending = count($documents) < 1;
    $isPartial = !$hasAdharCard || !$hasPanCard || !$has10th || !$has12th || !$hasPhoto;
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

                  <td><a href="{{url('account/employees')}}/{{$emp->user_id}}" class="dropdown-item"><svg class="svg-inline--fa fa-eye fa-w-18 mr-2" aria-hidden="true" focusable="false" data-prefix="fa" data-icon="eye" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg=""><path fill="currentColor" d="M572.52 241.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z"></path></svg><!-- <i class="fa fa-eye mr-2"></i> Font Awesome fontawesome.com -->View</a>
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
