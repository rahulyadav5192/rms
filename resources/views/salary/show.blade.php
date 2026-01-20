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

.form-control {
        background-color: #fff;
    border-color: #e8eef3;
    font-size: 14px;
    padding: 0.5rem;
}
.client-list-filter {
    align-items: center;
}
.select-box {
        margin-top: 11px;
}
</style>
    <form action="{{url('account/employees')}}" id='myForm' method="GET"> 
    <x-filters.filter-box>
     


    </x-filters.filter-box>

@endsection

@section('content')



<style>
    .search-status {
        display: flex;
    }  
    .search-status .btn-primary.rounded.f-14.p-2.mr-3 {
        margin-left: 20px;
    }
    .filter-box {
    display: none; /* Hide the filter box by default */
    /* Add other styling as needed */
}
    
    @media print {
        /* Hide the URL */
        .url {
            display: none !important;
        }
    
        /* Add more rules to hide other elements if needed */
    }
</style>
   
    <form action="{{route('show_slip')}}" id='myForm' method="GET"> 
    
    <x-filters.filter-box>
        <!-- Branch START -->
        <div class="select-box  py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-2 pr-2 f-14 text-dark-grey d-flex align-items-center">Branch</p>
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
        //   $('#branches').on('change', function () {
        //   var form = document.getElementById('myForm');
        //   form.submit();
        //   });
                $(document).ready(function() {
    $("#filter-btnn").click(function() {
        $(".filter-box").slideToggle("slow", function() {
            // Toggle the visibility of filter-box with a smooth sliding effect
            $("#filter-btn").hide(); // Hide the filter button after opening the filter box
        });
    });
});



         
        </script>
        <!-- Branch END -->        


        <!-- Department START -->
        <div class="select-box  py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-2 pr-2 f-14 text-dark-grey align-items-center">Department</p>
            <div class="select-status">
                <select class="form-control" name="department" id="department">
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
        //   $('#department').on('change', function () {
        //   var form = document.getElementById('myForm');
        //   form.submit();
        //   });
                          
        </script>
        <!-- Department END -->        


        <!-- DESIGNATION START -->
        <div class="select-box  py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-2 pr-2 f-14 text-dark-grey align-items-center">@lang('app.designation')</p>
            <div class="select-status">
                <select class="form-control" name="designation" id="designation">
                    
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
        //   $('#designation').on('change', function () {
        //   var form = document.getElementById('myForm');
        //   form.submit();
        //   });
                          
        </script>
        
        <!-- DESIGNATION END -->


        <!--<div class="select-box d-flex py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">-->
        <!--    <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Status</p>-->
        <!--    <div class="select-status">-->
        <!--        <select class="form-control" name="status" id="status">-->
        <!--            <option value="active" @if(isset($_GET['status']) && $_GET['status'] == 'active') selected @endif >Active</option>-->
        <!--            <option value="deactive" @if(isset($_GET['status']) && $_GET['status'] == 'deactive') selected @endif>Inactive</option>-->
        <!--        </select>-->
        <!--    </div>-->
        <!--</div>-->

       <div class="col-lg-3 col-md-6">
            <x-forms.select fieldId="pf_deduction" :fieldLabel="__('PF Deduction')" fieldName="pf_deduction" search="true">
                <option value="all" {{ request()->get('pf_deduction') == 'all' ? 'selected' : '' }}>@lang('--')</option>
                <option value="0" {{ request()->get('pf_deduction') == '0' ? 'selected' : '' }}>@lang('app.yes')</option>
                <option value="1" {{ request()->get('pf_deduction') == '1' ? 'selected' : '' }}>@lang('app.no')</option>
            </x-forms.select>
        </div>
        
         <div class="col-lg-3 col-md-6 d-flex">
            <x-forms.select fieldId="month" :fieldLabel="__('app.month')" fieldName="month" search="true" fieldRequired="true">
                <option value="all" {{ request()->get('month') == 'all' ? 'selected' : '' }}>@lang('--')</option>
                @php
                    $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
                @endphp
                @foreach ($months as $index => $month)
                    <option value="{{ $index + 1 }}" >{{ $month }}</option>
                @endforeach
            </x-forms.select>
             <x-forms.select fieldId="year" :fieldLabel="__('app.year')" fieldName="year" search="true" fieldRequired="true">
                <option value="all" {{ request()->get('year') == 'all' ? 'selected' : '' }}>@lang('--')</option>
                @for ($i = $year; $i >= $year - 10; $i--)
                    <option value="{{ $i }}" {{ request()->get('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                @endfor
            </x-forms.select>
            <span id="year_error" class="text-danger"></span>
        </div>
        
        <div class="col-lg-3 col-md-6">
           
        </div>

        
        
        
        
        <script>
        //   $('#status').on('change', function () {
        //   var form = document.getElementById('myForm');
        //   form.submit();
        //   });
                          
        </script>
        
        <!-- DESIGNATION END -->

        <div class="text-box py-2 px-lg-2 px-md-2 px-0 border-right-grey border-right-grey-sm-0">
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Search</p>
            <div class="search-status">
                <input type="text" class="form-control" @if(isset($_GET['search']) && $_GET['search']) value="{{$_GET['search']}}" @endif name="search" id="search" placeholder="EMP ID Or Name">

                <input type="submit" value="Search" class="btn-primary rounded f-14 p-2 mr-3"/>
            </div>
        </div>


    </x-filters.filter-box>

 </form>
 <button id="filter-btnn" style="margin-left: 28px; font-size: large;"> Filter 
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-filter-left" viewBox="0 0 16 16">
          <path d="M2 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5m0-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5m0-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5"/>
        </svg>
    </button>
 <!-- CONTENT WRAPPER START -->
    <div class="content-wrapper">
        <!-- Add Task Export Buttons Start -->
        <!--<div class="d-flex justify-content-between action-bar">-->

        <!--    <div id="table-actions" class="d-block d-lg-flex align-items-center">-->
        <!--       -->


        <!--   
        <!--    </div>-->

        <!--    <x-datatable.actions>-->
        <!--        <div class="select-status mr-3 pl-3">-->
        <!--            <select name="action_type" class="form-control select-picker" id="quick-action-type" disabled>-->
        <!--                <option value="">@lang('app.selectAction')</option>-->
        <!--                <option value="change-status">@lang('modules.tasks.changeStatus')</option>-->
        <!--                <option value="delete">@lang('app.delete')</option>-->
        <!--            </select>-->
        <!--        </div>-->
        <!--        <div class="select-status mr-3 d-none quick-action-field" id="change-status-action">-->
        <!--            <select name="status" class="form-control select-picker">-->
        <!--                <option value="deactive">@lang('app.inactive')</option>-->
        <!--                <option value="active">@lang('app.active')</option>-->
        <!--            </select>-->
        <!--        </div>-->
        <!--    </x-datatable.actions>-->

        <!--</div>-->
        
        
        
        
        <!-- Add Task Export Buttons End -->
        <!-- Task Box Start -->
        <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
            <table class="table">
              <thead>
                <tr>
                  <th>Employee ID</th>
                  <!--<th>Branch</th>-->
                  <th>Name</th>
                  <th>Department</th>
                  <!--<th>Designation</th>-->
                  <th>Month</th>
                  <th>Working Days</th>
                  <th>Payable Days</th>
                  <th>CTC</th>
                  <th>PF Ded.</th>
                  <!--<th>Gross Salary</th>-->
                  <th>Net Take Home </th>
                  
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($slips as $key=>$emp)
                <tr>
                  <td>{{$emp->username}}</td>
                  <td>{{$emp->employee_name}}</td>
                  <td>{{$emp->deapartment}}</td>
                  <!--<td>{{$emp->designation_name}}</td>-->
                  <td>{{$emp->month_name}} {{$emp->year}}</td>
                  <td>{{$emp->days_in_month}}</td>
                  <td>{{$emp->payable_days}}</td>
                  <td>{{($emp->salary != NULL) }}</td>
                  <td>{{($emp->pf_deduct == 1) ? 'No' : 'Yes' }}</td>
                  <!--<td>{{$emp->grossSalary}}</td>-->
                  <td>{{$emp->netTakehome}}</td>
                  <!--<td><a href="{{ route('salarySlip.edit', $emp->id) }}" class="btn btn-sm btn-info ">Edit </a></td>-->

                  <td><a href="{{url('view_slip')}}/{{$emp->slip_id}}" class="dropdown-item"><svg class="svg-inline--fa fa-eye fa-w-18 mr-2" aria-hidden="true" focusable="false" data-prefix="fa" data-icon="eye" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" data-fa-i2svg=""><path fill="currentColor" d="M572.52 241.4C518.29 135.59 410.93 64 288 64S57.68 135.64 3.48 241.41a32.35 32.35 0 0 0 0 29.19C57.71 376.41 165.07 448 288 448s230.32-71.64 284.52-177.41a32.35 32.35 0 0 0 0-29.19zM288 400a144 144 0 1 1 144-144 143.93 143.93 0 0 1-144 144zm0-240a95.31 95.31 0 0 0-25.31 3.79 47.85 47.85 0 0 1-66.9 66.9A95.78 95.78 0 1 0 288 160z"></path></svg><!-- <i class="fa fa-eye mr-2"></i> Font Awesome fontawesome.com View</a>
                    <!--<a href="#" class="btn btn-primary">Edit</a>-->
                    <!--<a href="#" class="btn btn-danger">Delete</a>-->
                  </td>
                  <td><a href="{{ route('salarySlip.edit', $emp->id) }}" class="btn btn-sm btn-info ">Edit </a></td>
                </tr>
                @endforeach
              </tbody>
            </table>
            {{ $slips->appends(request()->all())->links() }}
      </div>
        <!-- Task Box End -->
    </div>
    
    <!-- CONTENT WRAPPER END -->






@endsection 