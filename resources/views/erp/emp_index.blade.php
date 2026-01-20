@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')

@php
$editEmployeePermission = user()->permission('edit_employees');
@endphp

<style>

    .hidden {
    display: none;
}
</style>

<div class="container card pt-3">
    
    <!-- Button to trigger modal -->
<button type="button" class="btn btn-primary" style="width: 150px;" data-toggle="modal" data-target="#addReferenceModal">
  Add Reference
</button>


    <div class="modal fade" id="addReferenceModal" tabindex="-1" aria-labelledby="addReferenceModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content m-2">
          <div class="modal-header">
            <h5 class="modal-title" id="addReferenceModalLabel">Add Reference</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <form id="addReferenceForm" method="POST" action="{{ url('/account/employees/my_erp_add') }}">
              @csrf
    
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="referenceName">Reference Name *</label>
                  <input type="text" class="form-control" id="referenceName" name="reference_name" required>
                </div>
                <div class="form-group col-md-6">
                  <label for="contact">Contact *</label>
                  <input type="text" class="form-control" id="contact" name="contact" required>
                </div>
                
              </div>
    
              <div class="form-row">
                
                <div class="form-group col-md-6">
                  <label for="contact">Alt. Contact *</label>
                  <input type="text" class="form-control" id="contact" name="alt_contact" >
                </div>
                <div class="form-group col-md-6">
                  <label for="batchNo">Email</label>
                  <input type="text" class="form-control" id="batchNo" name="email">
                </div>
              </div>
    
              <div class="form-row">
                
                <div class="form-group col-md-6">
                  <label for="employmentStatus">Employee Type:</label>
                    <select id="employmentStatus" class="form-control" name="employmentStatus">
                        <option value="0">Fresher</option>
                        <option value="1">Experienced</option>
                    </select>
                </div>
                <div class="form-group col-md-6 hidden" id="previousCompanyContainer">
                  <label for="previousCompany">Previous Company:</label>
                    <input type="text" id="previousCompany" class="form-control" name="previousCompany" placeholder="">
                </div>
                
              </div>
    
           
    
              <!--<div class="form-row">-->
              <!--  <div class="form-group col-md-6">-->
              <!--    <label for="tenurity">Tenurity</label>-->
              <!--    <input type="text" class="form-control" id="tenurity" name="tenurity">-->
              <!--  </div>-->
              <!--  <div class="form-group col-md-6">-->
              <!--    <label for="inMonths">In Months</label>-->
              <!--    <input type="text" class="form-control" id="inMonths" name="in_months">-->
              <!--  </div>-->
              <!--</div>-->
    
              <!--<div class="form-row">-->
              <!--  <div class="form-group col-md-6">-->
              <!--    <label for="exitDate">Exit Date</label>-->
              <!--    <input type="date" class="form-control" id="exitDate" name="exit_date">-->
              <!--  </div>-->
              <!--  <div class="form-group col-md-6">-->
              <!--    <label for="bonusStatus">Bonus Status</label>-->
              <!--    <select class="form-control" id="bonusStatus" name="bonus_status">-->
              <!--      <option value="1">Pending</option>-->
              <!--      <option value="0">Transferred</option>-->
              <!--      <option value="3">Not Eligible</option>-->
              <!--    </select>-->
              <!--  </div>-->
              <!--</div>-->
    
              <!--<div class="form-group">-->
              <!--  <label for="bonusAmount">Bonus Amount</label>-->
              <!--  <input type="text" class="form-control" id="bonusAmount" name="bonus_amount">-->
              <!--</div>-->
    
              <button type="submit" class="btn btn-primary">Add Reference</button>
            </form>
          </div>
        </div>
      </div>
    </div>




<table class="table table-striped">
  <thead>
    <tr>
      <th scope="col">S. No</th>
      <!--<th scope="col">Reference Name </th>-->
      <!--<th scope="col">Batch No</th>-->
      <!--<th scope="col">Referee CODE</th>-->
      <th scope="col">Referee Name </th>
      <th scope="col">Int. Status </th>
      <th scope="col">Status </th>
      <th scope="col">Contact </th>
       <th scope="col">Email </th>
      <th scope="col">Process </th>
      <!--<th scope="col">Joining Date  </th>-->
      <th scope="col">Tenurity </th>
      
      <!--<th scope="col">Action </th>-->
      <!--<th scope="col">Contact </th>-->
      
    </tr>
  </thead>
  <tbody>
    @foreach($erp as $key=>$e)
    <tr>
      <th scope="row">{{$key+1}}</th>
      <!--<th>{{$e->batch_no}}</th>-->
      <td>{{$e->name}} @if(isset($e->emp_id)) ( {{$e->employee_id}} ) @endif</td>
      <!--<td>@if($e->reference_id != NULL) <a href="{{ url('account/employees')}}/{{$e->reference_id}}">{{$e->reference_name}}</a> @else {{$e->reference_name}} @endif</td>-->
      <td> @if($e->int_status == 0) <span class="text-info">Lineup</span>  @elseif($e->int_status == 1)  <span class="text-danger">Rejected</span>  @else  <span class="text-success">Selected</span> @endif</td>
      <td> @if(isset($e->status) && $e->status == 'active') <span class="text-success">Active</span> @elseif(isset($e->status) && $e->status == 'deactive') <span class="text-danger">Deactive</span> @else  <span class="text-danger"> Not Selected </span>  @endif </td>
      <td>{{$e->contact}}</td>
      <td>{{$e->email}}</td>
      <td>{{$e->process}}</td>
      <!--<td>{{ \Carbon\Carbon::parse($e->emp_joining_date)->format('d-m-Y')}}</td>-->
      <td>{{$e->tenurity}}</td>
      
      
      <td>
          <!--<div class="d-flex">-->
              
          <!--    <button type="button" class="btn btn-sm btn-light" data-toggle="modal" data-target="#editModal{{$e->id}}">-->
          <!--        <i class="bi bi-pen"></i>-->
          <!--    </button>-->
          <!--</div>-->
      </td>
      
      
      <!-- Edit Modal -->
      <div class="modal fade" id="editModal{{$e->id}}" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel">Edit Employee Details</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                <form id="editForm" method="POST" action="{{ url('/account/employees/erp_update') }}/{{$e->id}}">
                  @csrf
                  <!--@method('PUT')-->
                  <input type="hidden" name="id" id="editId" value="{{ $e->id }}">
        
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="editBatchNo">Batch No</label>
                      <input type="text" class="form-control" id="editBatchNo" name="batch_no" value="{{ $e->batch_no }}">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="editEmpCode">Employee Code</label>
                      <input type="text" class="form-control" id="editEmpCode" name="employee_id" value="{{ $e->employee_id }}">
                    </div>
                  </div>
        
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="editName">Name</label>
                      <input type="text" class="form-control" id="editName" name="name" value="{{ $e->name }}">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="editContact">Contact</label>
                      <input type="text" class="form-control" id="editContact" name="contact" value="{{ $e->contact }}">
                    </div>
                  </div>
        
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="editProcess">Process Shortlisted For</label>
                      <input type="text" class="form-control" id="editProcess" name="process" value="{{ $e->process }}">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="editReferenceName">Reference Name</label>
                      <input type="text" class="form-control" id="editReferenceName" disabled name="" value="{{ $e->reference_name }}">
                    </div>
                  </div>
        
                  <!--<div class="form-row">-->
                  <!--  <div class="form-group col-md-6">-->
                  <!--    <label for="editReferenceEmpId">Reference Emp ID</label>-->
                  <!--    <input type="text" class="form-control" id="editReferenceEmpId" name="reference_emp_id" value="{{ $e->reference_emp_id }}">-->
                  <!--  </div>-->
                  <!--  <div class="form-group col-md-6">-->
                  <!--    <label for="editReferenceId">Reference ID</label>-->
                  <!--    <input type="text" class="form-control" id="editReferenceId" name="reference_id" value="{{ $e->reference_id }}">-->
                  <!--  </div>-->
                  <!--</div>-->
        
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="editCampaignName">Campaign Name</label>
                      <input type="text" class="form-control" id="editCampaignName" name="campaign_name" value="{{ $e->campaign_name }}">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="editJoiningDate">Joining Date</label>
                      <input type="date" class="form-control" id="editJoiningDate" name="joining_date" value="{{ $e->joining_date }}">
                    </div>
                  </div>
        
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="editTenurity">Tenurity</label>
                      <input type="text" class="form-control" id="editTenurity" name="tenurity" value="{{ $e->tenurity }}">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="editInMonths">In Months</label>
                      <input type="text" class="form-control" id="editInMonths" name="in_months" value="{{ $e->in_months }}">
                    </div>
                  </div>
        
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="editExitDate">Exit Date</label>
                      <input type="date" class="form-control" id="editExitDate" name="exit_date" value="{{ $e->exit_date }}">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="editBonusStatus">Bonus Status</label>
                      <select class="form-control" id="editBonusStatus" name="bonus_status">
                        <option value="1" {{ $e->bonus_status == 1 ? 'selected' : '' }}>Pending</option>
                        <option value="0" {{ $e->bonus_status == 0 ? 'selected' : '' }}>Transferred</option>
                        <option value="3" {{ $e->bonus_status == 3 ? 'selected' : '' }}>Not Eligible</option>
                      </select>
                    </div>
                  </div>
        
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="editBonusAmount">Bonus Amount</label>
                      <input type="text" class="form-control" id="editBonusAmount" name="bonus_amount" value="{{ $e->bonus_amount }}">
                    </div>
                  </div>
        
                  <button type="submit" class="btn btn-primary">Save changes</button>
                </form>
              </div>
            </div>
          </div>
        </div>


      <!--<td>{{$e->batch_no}}</td>-->
      <!--<td>{{$e->batch_no}}</td>-->
      <!--<td>{{$e->batch_no}}</td>-->
      
    </tr>
    @endforeach
  </tbody>
</table>

</div>


<script>

   document.addEventListener('DOMContentLoaded', function() {
    const employmentStatusSelect = document.getElementById('employmentStatus');
    const previousCompanyContainer = document.getElementById('previousCompanyContainer');

    employmentStatusSelect.addEventListener('change', function() {
        if (employmentStatusSelect.value == 1) {
            // console.log('Working');
            previousCompanyContainer.classList.remove('hidden');
        } else {
            previousCompanyContainer.classList.add('hidden');
        }
    });
});

</script>














@endsection