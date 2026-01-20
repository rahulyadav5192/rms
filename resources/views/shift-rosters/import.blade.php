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
    <div class="content-wrapper">
            <!-- Add Task Export Buttons Start -->
            <div class="card  action-bar">
                @if(Session::has('message'))
                <div class="alert alert-info" role="alert">
                   {{ Session::get('message') }}
                </div>
                @endif
                
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                
            
                <form class="m-4" action="{{url('account/shifts/import_shift')}}" method="POST" enctype="multipart/form-data">
                    @csrf
                  <div class="form-row">
                    <div class="form-group col">
                      <label for="inputEmail4">File</label>
                      <input type="file" class="form-control" accept=".csv" name="file" id="inputEmail4" placeholder="" required>
                      <!--@error('file')-->
                      <!--  <div class="text-danger">{{ $message }}</div>-->
                      <!--@enderror-->
                    </div>
                    <!--<div class="form-group col">-->
                    <!--  <label for="inputPassword4">Login Time</label>-->
                    <!--  <input type="time" class="form-control" name="log_in_time" id="inputPassword4" placeholder="" required>-->
                    <!--  @error('log_in_time')-->
                    <!--    <div class="text-danger">{{ $message }}</div>-->
                    <!--  @enderror-->
                    <!--</div>-->
                    <!--<div class="form-group col">-->
                    <!--  <label for="inputEmail4">Logout Date</label>-->
                    <!--  <input type="date" class="form-control" name="out_date" id="inputEmail4" placeholder="" required>-->
                    <!--  @error('out_date')-->
                    <!--    <div class="text-danger">{{ $message }}</div>-->
                    <!--  @enderror-->
                    <!--</div>-->
                    <!--<div class="form-group col">-->
                    <!--  <label for="inputPassword4">Logout Time</label>-->
                    <!--  <input type="time" class="form-control" name="log_out_time" id="inputPassword4" placeholder="" required>-->
                    <!--  @error('log_out_time')-->
                    <!--    <div class="text-danger">{{ $message }}</div>-->
                    <!--  @enderror-->
                    <!--</div>-->
                  </div>
                  <!--<div class="form-group">-->
                  <!--  <label for="inputAddress">Description</label>-->
                  <!--  <textarea class="form-control" id="" column="50" name="des" placeholder="Describe Your Dispute " style="height: 143px;" required></textarea>-->
                  <!--  @error('des')-->
                  <!--      <div class="text-danger">{{ $message }}</div>-->
                  <!--    @enderror-->
                  <!--</div>-->
                  <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                <hr class="dashed" style="border-top: 3px dashed #bbb;">
                
                <!--// resources/views/import.blade.php-->
                @if(session('errors'))
                    <div class="alert alert-danger">
                        <ul>
                            @foreach(session('errors') as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            
            </div>
        </div>
       
@endsection

@push('scripts')
    
@endpush