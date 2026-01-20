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
                
            
                <form class="m-4" action="{{url('att_dis_save')}}" method="POST">
                    @csrf
                  <div class="form-row">
                    <div class="form-group col">
                      <label for="inputEmail4">Date</label>
                      <input type="date" class="form-control" name="date" id="inputEmail4" placeholder="" required>
                      @error('date')
                        <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="form-group col">
                      <label for="inputPassword4">Login Time</label>
                      <input type="time" class="form-control" name="log_in_time" id="inputPassword4" placeholder="" required>
                      @error('log_in_time')
                        <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="form-group col">
                      <label for="inputEmail4">Logout Date</label>
                      <input type="date" class="form-control" name="out_date" id="inputEmail4" placeholder="" required>
                      @error('out_date')
                        <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>
                    <div class="form-group col">
                      <label for="inputPassword4">Logout Time</label>
                      <input type="time" class="form-control" name="log_out_time" id="inputPassword4" placeholder="" required>
                      @error('log_out_time')
                        <div class="text-danger">{{ $message }}</div>
                      @enderror
                    </div>
                  </div>
                  <div class="form-group">
                    <label for="inputAddress">Description</label>
                    <textarea class="form-control" id="" column="50" name="des" placeholder="Describe Your Dispute " style="height: 143px;" required></textarea>
                    @error('des')
                        <div class="text-danger">{{ $message }}</div>
                      @enderror
                  </div>
                  <button type="submit" class="btn btn-primary">Submit</button>
                </form>
                <hr class="dashed" style="border-top: 3px dashed #bbb;">
                
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th scope="col">Id</th>
                      <!--<th scope="col">Name (ID)</th>-->
                      <th scope="col">Date Of</th>
                      <th scope="col">Login Time</th>
                      <th scope="col">Log Out Time</th>
                      <th scope="col">Description </th>
                      <th scope="col">Created On </th>
                      <th scope="col">Status </th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($dispute as $key=>$d)
                    @php 
                     $d->created_at = new DateTime($d->created_at);
                     $d->date = new DateTime($d->date);
                    @endphp
                    <tr>
                      <th scope="row">{{$key+1}}</th>
                      <!--<td>{{$d->name}} ({{$d->username}})</td>-->
                      <td>{{$d->date->format('d-m-Y')}}</td>
                      <td>{{$d->log_in_time}}</td>
                      <td>{{$d->log_out_time}}</td>
                      <td style="max-width: 489px;">{{$d->desc}}</td>
                      <td>{{ $d->created_at->format('d-m-Y') }}</td>
                      <td>
                          @if($d->solve_status == 0)
                          <span class="text-info">Raised</span>
                          @elseif($d->solve_status == 2)
                            <span class="text-success">Approved</span>
                          @else
                          <span class="text-danger">Rejected</span>
                          @endif
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
               
            
            </div>
        </div>
       
@endsection

@push('scripts')
    
@endpush