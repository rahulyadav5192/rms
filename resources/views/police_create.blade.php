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
<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>
        <div class="content-wrapper">
            <!-- Add Task Export Buttons Start -->
            <div class="card  action-bar">
                @if(isset($_GET['mess']))
                <div class="alert alert-danger" role="alert">
                   {{ $_GET['mess'] }}
                </div>
                @endif
                 <button type="button" class="col-1 btn btn-sm btn-primary btn-lg" data-toggle="modal" data-target="#add">Add</button>
                      <!-- Modal -->
                        <div id="add" class="modal fade" role="dialog">
                          <div class="modal-dialog modal-lg">
                        
                            <!-- Modal content-->
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Add</h4>
                              </div>
                              <div class="modal-body">
                                <form action="{{url('policy_add')}}" method="POST">
                                    @csrf
                                  <div class="form-group">
                                    <label for="email">Title</label>
                                    <input type="text" name="title" class="form-control" id="email" required>
                                  </div>
                                  <div class="form-group">
                                    <label for="pwd">Description</label>
                                    <textarea type="text" name="des" row="50" class="form-control editor" id="editor" ></textarea>
                                  </div>
                                  
                                  <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                              </div>
                            </div>
                        
                          </div>
                        </div>
            <div class="d-flex flex-column w-tables rounded mt-3 bg-white">
            <table class="table">
              <thead>
                <tr>
                  <th>S No</th>
                  <th>Title</th>
                  <th>Desc</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                @foreach($policy as $key=>$emp)
                <tr>
                  <td>{{$key+1}}</td>
                  <td>{{$emp->title}}</td>
                  <td id="">{{ str_limit($emp->policy, 10) }}</td>
                  <td>
                      <button type="button" class="btn btn-info btn-lg" data-toggle="modal" data-target="#myModal{{$emp->id}}"><i class="bi bi-pen"></i></button>
                      <!-- Modal -->
                        <div id="myModal{{$emp->id}}" class="modal fade" role="dialog">
                          <div class="modal-dialog modal-lg">
                        
                            <!-- Modal content-->
                            <div class="modal-content">
                              <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Edit</h4>
                              </div>
                              <div class="modal-body">
                                <form action="{{url('policy_change')}}/{{$emp->id}}" method="post">
                                    @csrf
                                  <div class="form-group">
                                    <label for="email">Title</label>
                                    <input type="text" name="title" value="{{$emp->title}}" class="form-control" id="email">
                                  </div>
                                  <div class="form-group">
                                    <label for="pwd">Description</label>
                                 
                                    <textarea type="text" name="des" row="50" class="form-control editor{{$emp->id}}" id=""> {{$emp->policy}}</textarea>
                                  </div>
                                  
                                  <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                              </div>
                            </div>
                        
                          </div>
                        </div>
                        
                        <a href="{{url('policy_delete')}}/{{$emp->id}}"><button type="button" class="btn btn-info btn-lg"><i class="bi bi-trash"></i></button></a>
                  </td>
                
                </tr>
                 <script>
                    ClassicEditor
                        .create(document.querySelector('.editor{{$emp->id}}'))
                        .catch(error => {
                            console.error(error);
                        });
                </script>
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