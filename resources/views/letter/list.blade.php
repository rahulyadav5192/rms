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


@php
$editEmployeePermission = user()->permission('edit_employees');
@endphp


<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
        <div class="content-wrapper">
            <!-- Add Task Export Buttons Start -->
            <div class="card  action-bar">
                @if ($editEmployeePermission == 'all')
                <a href="{{url('account/upload_oldata')}}"><button type="button" class="btn btn-warning" style="    width: 145px;" onclick="">Upload OL Data </button></a>
                @endif
                 
            @if(Session::has('message'))
                <div class="alert alert-info" role="alert">
                  {{ Session::get('message') }}
                </div>
            @endif  
            <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
            <table class="table">
              <thead>
                <tr>
                  <!--<th>S No</th>-->
                  <th>Name</th>
                  <!--<th>Desc</th>-->
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <!--<td>1</td>-->
                  <td>Letter Of Intent ( LOI )</td>
                  <!--<td id=""></td>-->
                  <td>
                      <!--<button type="button" class="btn  btn-lg" data-toggle="modal" data-target="#myModal"><i class="bi bi-download"></i></button>-->
                      <a href="{{url('account/viewMyLoi')}}"  target="_blank"><button type="button" class="btn btn-lg"><i class="bi bi-eye"></i></button></a>
                      <!--<a href="{{url('account/downloadMyLoi')}}"><button type="button" class="btn btn-lg"><i class="bi bi-download"></i></button></a>-->
                  </td>
                
                </tr>
                
                <tr>
                  <!--<td>2</td>-->
                  <td>Offer Letter</td>
                  <!--<td id=""></td>-->
                  <td>
                      <!--<button type="button" class="btn  btn-lg" data-toggle="modal" data-target="#myModal"><i class="bi bi-download"></i></button>-->
                      <!--if(!$emp->employment_type)-->
                      <!--  Employment Type Missing! Please Contact To Team Hr.-->
                      
                    @if($doc_status != 'Pending')
                        @if(!$emp->offer_salary_month)
                            Offer Salary Missing! Please Contact To Team Hr.
                        @elseif(!$emp->joining_date)
                            Joining Date Missing! Please Contact To Team Hr.
                        @else
                            <a href="{{ url('account/ViewMyOl') }}" target="_blank"><button type="button" class="btn btn-lg"><i class="bi bi-eye"></i></button></a>
                            <!--<a href="{{ url('account/downloadViewMyOl?download=1') }}"><button type="button" class="btn btn-lg"><i class="bi bi-download"></i></button></a>-->
                        @endif
                    @else 
                        Your Documents Are Not Uploaded, Please Upload Your Required Documents <a href="{{url('account/settings/profile-settings?tab=documents')}}">Here</a> , Then Get Your Offer Letter.
                    @endif


                  </td>
                
                </tr>
                
                <tr>
                  <!--<td>3</td>-->
                  <td>Acknowledgement Letter</td>
                  <!--<td id=""></td>-->
                  <td>
                      <!--<button type="button" class="btn  btn-lg" data-toggle="modal" data-target="#myModal"><i class="bi bi-download"></i></button>-->
                      <a href="{{url('account/ViewMyack')}}"  target="_blank"><button type="button" class="btn btn-lg"><i class="bi bi-eye"></i></button></a>
                  </td>
                
                </tr>
                @if(!is_null($emp->last_date))
                <tr>
                  <!--<td>3</td>-->
                  <td>Experience Letter</td>
                  <!--<td id=""></td>-->
                  <td>
                      <!--<button type="button" class="btn  btn-lg" data-toggle="modal" data-target="#myModal"><i class="bi bi-download"></i></button>-->
                      <a href="{{url('account/viewMyExp')}}"  target="_blank"><button type="button" class="btn btn-lg"><i class="bi bi-eye"></i></button></a>
                  </td>
                
                </tr>
                @endif
                
                @foreach ($letters as $key=>$letter)
                 
                <tr>
                  <!--<td>{{$key+4}}</td>-->
                  <td>Letter for Completed 11-Month Period: {{ $letter['period'] }}<br>Date of Completion: {{ $letter['date'] }}</td>
                  <!--<td id=""></td>-->
                  <td>
                      <!--<button type="button" class="btn  btn-lg" data-toggle="modal" data-target="#myModal"><i class="bi bi-download"></i></button>-->
                      <a href="{{ url('/view-letter/' . $letter['period']) }}"  target="_blank"><button type="button" class="btn btn-lg"><i class="bi bi-eye"></i></button></a>
                  </td>
                
                </tr>
                
                @endforeach
                
                
              </tbody>
            </table>
        </div>
        <!-- Task Box End -->
            </div>
        </div>
        <script>
            ClassicEditor
                .create(document.querySelector('#editor'))
                .catch(error => {
                    console.error(error);
                });
        </script>
@endsection

@push('scripts')
    
@endpush