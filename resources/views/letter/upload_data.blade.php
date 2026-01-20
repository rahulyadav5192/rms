@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')

<style>
    /* Style for form container */
#import_table {
    margin-top: 20px;
}

/* Style for form */
.form-group {
    margin-bottom: 20px;
}

/* Style for file input */
.form-control-file {
    width: 100%;
}

/* Style for submit button */
.btn-primary {
    background-color: #007bff;
    color: #fff;
    border: none;
    padding: 10px 20px;
    cursor: pointer;
    border-radius: 5px;
}

.btn-primary:hover {
    background-color: #0056b3;
}

</style>
<div class="row" id="import_table">
    @if(Session::has('mess'))
        <div class="alert alert-success" role="alert">
  {{Session::get('mess')}}
</div>
    @endif
    <div class="col-sm-12">
        <button type="button" class="btn btn-light btn-sm" data-toggle="modal" data-target="#uploadModal">Upload Data</button>

<!--modal -->
<div class="modal fade" id="uploadModal" tabindex="-1" aria-labelledby="uploadModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="uploadModalLabel">Upload Data</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form action="{{ url('account/upload_oldata') }}" method="post" enctype="multipart/form-data">
          @csrf
          <div class="form-group">
            <label for="exampleFormControlFile1">File (Should Be Like This EMPid , Salary Monthly , DOJ (2024-08-23) , Type (Part Time/Full Time)) </label>
            <input type="file" class="form-control-file" name="file" id="exampleFormControlFile1">
          </div>
          <button type="submit" class="btn btn-primary">Submit</button>
        </form>
      </div>
    </div>
  </div>
</div>

<!--end modal -->

<form action="{{url('account/upload_oldata')}}" id='myForm' method="GET"> 
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
            <p class="mb-0 pr-2 f-14 text-dark-grey d-flex align-items-center">Department</p>
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
                <input type="text" class="form-control" @if(isset($_GET['search']) && $_GET['search']) value="{{$_GET['search']}}" @endif name="search" id="search" placeholder="EMP ID Or Name">

            </div>
        </div>

            <input type="submit" value="Search" class="btn-primary rounded btn-sm f-14 p-2 mr-3 mb-3"/>

    </x-filters.filter-box>

 <div class="d-flex flex-column w-tables rounded m-3 bg-white">
            <table class="table">
              <thead>
                <tr>
                  <!--<th>S No</th>-->
                  <th>Id</th>
                  <th>Designation</th>
                  <th>Name</th>
                  <!--<th>Desc</th>-->
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                  
                 @foreach($all as $key=>$emp)
                <tr>
                  <!--<td>{{$key+1}}</td>-->
                  <td>{{$emp->employee_id}}</td>
                  <td>{{$emp->designation_name}}</td>
                  <td>{{$emp->employee_name}}</td>
                  <!--<td id=""></td>-->
                  <td>
                      <!--<button type="button" class="btn  btn-lg" data-toggle="modal" data-target="#myModal"><i class="bi bi-download"></i></button>-->
                      <a href="{{url('account/viewLoi')}}/{{$emp->user_id}}"  target="_blank"><button type="button" class="btn btn-lg"><i class="bi bi-eye"></i></button></a>
                  </td>
                
                </tr>
                @endforeach
                
              </tbody>
            </table>
            {{ $all->appends(request()->all())->links() }}
        </div>
        
        
    </div>
</div>
</form>


@endsection
