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
                
            
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th scope="col">Id</th>
                      <th scope="col">Name (ID)</th>
                      <th scope="col">Date Of</th>
                      <th scope="col">Login Time</th>
                      <th scope="col">Log Out Time</th>
                      <th scope="col">Description </th>
                      <th scope="col">Created On </th>
                      <th scope="col">Action </th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($dispute as $key=>$d)
                    @php 
                     $d->created_at = new DateTime($d->created_at);
                     $d->date = new DateTime($d->date);
                     $d->logout_date = new DateTime($d->logout_date);
                    @endphp
                    <tr>
                      <th scope="row">{{$key+1}}</th>
                      <td>{{$d->name}} ({{$d->username}})</td>
                      <td>{{$d->date->format('d-m-Y')}}</td>
                      <td>{{$d->log_in_time}}</td>
                      <td>{{$d->log_out_time}}</td>
                      <td style="max-width: 489px;">{{$d->desc}}</td>
                      <td>{{ $d->created_at->format('d-m-Y') }}</td>
                      <td>
                          @if($d->solve_status == 0)
                          <div class="d-flex">
                            <button class="btn-sm btn-success mr-2"  data-toggle="modal" data-target="#approve{{$key}}">Approve</button>
                            <a href="{{url('reject_att_dis_hr')}}/{{$d->id}}"><button class="btn-sm btn-danger" style="    height: 48px;">Reject</button></a>
                            
                            <!-- Approve Modal -->
                            <form class="m-4" action="{{url('att_dis_approve_hr')}}/{{$d->user_id}}/{{$d->id}}" method="POST">
                            <div class="modal fade" id="approve{{$key}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                              <div class="modal-dialog modal-lg" role="document">
                                <div class="modal-content">
                                  <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel">Approve</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                      <span aria-hidden="true">&times;</span>
                                    </button>
                                  </div>
                                  <div class="modal-body">
                                        @csrf
                                      <div class="form-row">
                                        <div class="form-group col-6">
                                          <label for="inputEmail4">Date</label>
                                          <input type="date" class="form-control" name="date" value="{{$d->date->format('Y-m-d')}}" id="inputEmail4" placeholder="" required>
                                          
                                        </div>
                                        <div class="form-group col-6">
                                          <label for="inputPassword4">Login Time</label>
                                          <input type="time" class="form-control" name="log_in_time"  value="{{$d->log_in_time}}" id="inputPassword4" placeholder="" required>
                                          
                                        </div>
                                        <div class="form-group col-6">
                                          <label for="inputEmail4">Logout Date</label>
                                          <input type="date" class="form-control" name="logout_date" value="{{$d->logout_date->format('Y-m-d')}}" id="inputEmail4" placeholder="" required>
                                          
                                        </div>
                                        <div class="form-group col-6">
                                          <label for="inputPassword4">Logout Time</label>
                                          <input type="time" class="form-control" name="log_out_time"   value="{{$d->log_out_time}}" id="inputPassword4" placeholder="" required>
                                          
                                        </div>
                                        
                                        <div class="form-group col-6">
                                          <label for="inputPassword4">Late</label>
                                          <select  class="form-control" name="late"required>
                                              <option value="yes">Yes</option>
                                              <option value="no" selected>No</option>
                                          </select>
                                          
                                        </div>
                                        <div class="form-group col-6">
                                          <label for="inputPassword4">Half Day</label>
                                          <select  class="form-control" name="half_day" required>
                                              <option value="yes">Yes</option>
                                              <option value="no" selected>No</option>
                                          </select>
                                          
                                        </div>
                                        <!--<div class="form-group col">-->
                                        <!--  <label for="inputPassword4">Work From </label>-->
                                        <!--  <select  class="form-control" name="half_day" required>-->
                                        <!--      <option value="yes">Yes</option>-->
                                        <!--      <option value="no" selected>No</option>-->
                                        <!--  </select>-->
                                          
                                        <!--</div>-->
                                      </div>
                                      
                                      <!--<button type="submit" class="btn btn-primary">Submit</button>-->
                                    
                                  </div>
                                  <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Save changes</button>
                                  </div>
                                </div>
                              </div>
                            </div>
                            </form>
                          </div>
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
                <!--<hr class="dashed" style="border-top: 3px dashed #bbb;">-->
               
            
            </div>
        </div>
       
@endsection

@push('scripts')
    
@endpush