@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')

@php
$editEmployeePermission = user()->permission('edit_employees');
@endphp

    <div class="p-2 d-flex">
        @if ($editEmployeePermission == 'all')
           <button type="button" class="btn btn-outline-primary mr-2" data-toggle="modal" data-target="#editModal">Edit</button>
            <button type="button" class="btn btn-outline-secondary" data-toggle="modal" data-target="#uploadModal">Upload</button>
        @endif
    </div>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
<div class="d-flex pt-3" style="justify-content: space-around;">
               

    <div>
        
        <div class="card">
            <div class="card-body">
                <!--<i class="fa fa-pen fa-xs edit"></i>-->
                <table class="table-m">
                    <tbody>
                        <tr>
                            <td>BGV Status</td>
                            <td>:</td>
                            <td>{{$elsm->bgv}}</td>
                        </tr>
                        <tr>
                            <td>Induction Date</td>
                            <td>:</td>
                            <td>{{$elsm->induaction_date}}</td>
                        </tr>
                        <tr>
                            <td>LOI Yes</td>
                            <td>:</td>
                            <td>{{$elsm->loi}}</td>
                        </tr>
                        <tr>
                            <td>LOI Date</td>
                            <td>:</td>
                            <td>{{$elsm->loi_date}}</td>
                        </tr>
                        <tr>
                            <td>Document Status</td>
                            <td>:</td>
                            <td>{{$elsm->document_status}}</td>
                        </tr>
                        <tr>
                            <td>Biometric Yes/No</td>
                            <td>:</td>
                            <td>{{$elsm->biometric}}</td>
                        </tr>
                        <tr>
                            <td>Handover To Training</td>
                            <td>:</td>
                            <td>{{$elsm->handover_training_date}}</td>
                        </tr>
                        <tr>
                            <td>Batch Number (HR)</td>
                            <td>:</td>
                            <td>{{$elsm->batch_no	}}</td>
                        </tr>
                        <!--<tr>-->
                        <!--    <td>Batch Number (Training)</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>{{$elsm->bgv}}</td>-->
                        <!--</tr>-->
                        <tr>
                            <td>Training Start Date</td>
                            <td>:</td>
                            <td>{{$elsm->training_start}}</td>
                        </tr>
                        <tr>
                            <td>Training End Date</td>
                            <td>:</td>
                            <td>{{$elsm->training_end}}</td>
                        </tr>
                        <tr>
                            <td>CLT Certification Date</td>
                            <td>:</td>
                            <td>{{$elsm->clt_date}}</td>
                        </tr>
                        <tr>
                            <td>CLT Certification Status</td>
                            <td>:</td>
                            <td>{{$elsm->clt_status}}</td>
                        </tr>
                        <tr>
                            <td>Offer Letter Yes/No</td>
                            <td>:</td>
                            <td>{{$elsm->offer_letter}}</td>
                        </tr>
                        <tr>
                            <td>Offer Letter Date</td>
                            <td>:</td>
                            <td>{{$elsm->offer_later_date}}</td>
                        </tr>
                        <tr>
                            <td>Bank Account Status</td>
                            <td>:</td>
                            <td>{{$elsm->bank_acc_status}}</td>
                        </tr>
                        <!--<tr>-->
                        <!--    <td>ID Card Issue</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>{{$elsm->bgv}}</td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>ID Card Issue Date</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>{{$elsm->bgv}}</td>-->
                        <!--</tr>-->
                        <tr>
                            <td>OJT Start Date</td>
                            <td>:</td>
                            <td>{{$elsm->ojt_start}}</td>
                        </tr>
                        <tr>
                            <td>OJT End Date</td>
                            <td>:</td>
                            <td>{{$elsm->ojt_end}}</td>
                        </tr>
                        <tr>
                            <td>OJT Certified Date</td>
                            <td>:</td>
                            <td>{{$elsm->bgv}}</td>
                        </tr>
                        <tr>
                            <td>OJT Certification Status</td>
                            <td>:</td>
                            <td>{{$elsm->ojt_cert_status}}</td>
                        </tr>
                        <!--<tr>-->
                        <!--    <td>Extended OJT Start Date</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>{{$elsm->ojt_cert_date}}</td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>Extended OJT End Date</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>{{$elsm->bgv}}</td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>Extended OJT Certified Date</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>{{$elsm->bgv}}</td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>Extended OJT Certification Status</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>{{$elsm->bgv}}</td>-->
                        <!--</tr>-->
                        <tr>
                            <td>Handover to Production</td>
                            <td>:</td>
                            <td>{{$elsm->handover_ops_date}}</td>
                        </tr>
                        <!--<tr>-->
                        <!--    <td>Appointment Letter</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>{{$elsm->bgv}}</td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>Appointment Letter Date</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>{{$elsm->bgv}}</td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>Warning 1</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>No</td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>Warning 1 Date</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>N/A</td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>Reason</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>N/A</td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>Warning 2</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>No</td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>Warning 2 Date</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>N/A</td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>Reason</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>N/A</td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>Movement From</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>Department A</td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>Movement To</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>Department B</td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>Movement Date</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>2023-08-01</td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>Movement Type</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>Transfer</td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>Promotion Type</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>Internal</td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>Promoted Designation</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>Senior Developer</td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td>Promotion Date</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>2023-09-01</td>-->
                        <!--</tr>-->
                        <tr>
                            <td>Current Status</td>
                            <td>:</td>
                            <td>{{$elsm->current_status}}</td>
                        </tr>
                        <!--<tr>-->
                        <!--    <td>LWD</td>-->
                        <!--    <td>:</td>-->
                        <!--    <td>{{$elsm->bgv}}</td>-->
                        <!--</tr>-->
                        <tr>
                            <td>Reason For Leaving</td>
                            <td>:</td>
                            <td>{{$elsm->reason_for_leav}}</td>
                        </tr>
                        <tr>
                            <td>Notice Period</td>
                            <td>:</td>
                            <td>{{$elsm->notice_period_days}}</td>
                        </tr>
                        <tr>
                            <td>Notice Period Served in Days</td>
                            <td>:</td>
                            <td>{{$elsm->notice_period_served}}</td>
                        </tr>
                        <tr>
                            <td>Notice Served</td>
                            <td>:</td>
                            <td>{{$elsm->notice_served}}</td>
                        </tr>
                        <tr>
                            <td>Remarks</td>
                            <td>:</td>
                            <td>{{$elsm->remark}}</td>
                        </tr>
                        <tr>
                            <td>FnF Eligibility</td>
                            <td>:</td>
                            <td>{{$elsm->fnf_elg}}</td>
                        </tr>
                        <tr>
                            <td>FnF Status</td>
                            <td>:</td>
                            <td>{{$elsm->fnf_status}}</td>
                        </tr>
                        <tr>
                            <td>FnF Date</td>
                            <td>:</td>
                            <td>{{$elsm->fnf_date}}</td>
                        </tr>
                        <tr>
                            <td>Experience Letter Status</td>
                            <td>:</td>
                            <td>{{$elsm->exp_letter}}</td>
                        </tr>
                        <tr>
                            <td>Attrition Email</td>
                            <td>:</td>
                            <td>{{$elsm->att_email}}</td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>


    <!--<div class="">  -->
    <!--    <section>-->
    <!--        <div class="rt-container">-->
    <!--              <div class="col-rt-12">-->
    <!--                  <div class="Scriptcontent">-->
                      
               <!-- Stepper HTML -->
    <!--    <div class="step">-->
    <!--      <div>-->
    <!--        <div class="circle"><i class="fa fa-check"></i></div>-->
    <!--      </div>-->
    <!--      <div>-->
    <!--        <div class="title">First Step</div>-->
    <!--        <div class="caption">Optional</div>-->
    <!--      </div>-->
    <!--    </div>-->
    <!--    <div class="step step-active">-->
    <!--      <div>-->
    <!--        <div class="circle">2</div>-->
    <!--      </div>-->
    <!--      <div>-->
    <!--        <div class="title">Second Step</div>-->
    <!--          <div class="caption">This is description of second step.</div>-->
    <!--      </div>-->
    <!--    </div>-->
    <!--    <div class="step">-->
    <!--      <div>-->
    <!--        <div class="circle">3</div>-->
    <!--      </div>-->
    <!--      <div>-->
    <!--        <div class="title">Third Step</div>-->
    <!--          <div class="caption">Some text about Third step. </div>-->
    <!--      </div>-->
    <!--    </div>-->
                          
    <!--    <div class="step">-->
    <!--      <div>-->
    <!--        <div class="circle">4</div>-->
    <!--      </div>-->
    <!--      <div>-->
    <!--        <div class="title">Finish</div>-->
    <!--      </div>-->
    <!--    </div>-->
        <!-- End Stepper HTML -->
            		
                   
    <!--        		</div>-->
    <!--    		</div>-->
    <!--        </div>-->
    <!--    </section>-->
             
    <!--</div>-->
    
    
</div>




<!--// edit modal -->
  <!-- Modal -->
    <form action="{{url('/account/employees/elcm/elcm_edit')}}/{{$elsm->user_id}}" method="POST">
        @csrf
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel">Edit Data</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                        <div class="row">
                        <div class="form-group  col-md-4">
                            <label for="bgvStatus">BGV Status</label>
                            <input type="text" class="form-control" name="bgvStatus" value="{{$elsm->bgv}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="inductionDate">Induction Date</label>
                            <input type="date" class="form-control" name="inductionDate" value="{{$elsm->induaction_date}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="loiYes">LOI Yes</label>
                            <input type="text" class="form-control" name="loiYes" value="{{$elsm->loi}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="loiDate">LOI Date</label>
                            <input type="date" class="form-control" name="loiDate" value="{{$elsm->loi_date}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="documentStatus">Document Status</label>
                            <input type="text" class="form-control" name="documentStatus" value="{{$elsm->document_status}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="biometricYesNo">Biometric Yes/No</label>
                            <input type="text" class="form-control" name="biometricYesNo" value="{{$elsm->biometric}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="handoverToTraining">Handover To Training</label>
                            <input type="date" class="form-control" name="handoverToTraining" value="{{$elsm->handover_training_date}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="batchNumberHR">Batch Number (HR)</label>
                            <input type="text" class="form-control" name="batchNumberHR" value="{{$elsm->batch_no}}">
                        </div>
                        <!--<div class="form-group">-->
                        <!--    <label for="batchNumberTraining">Batch Number (Training)</label>-->
                        <!--    <input type="text" class="form-control" id="batchNumberTraining" value="{{$elsm->training_start}}">-->
                        <!--</div>-->
                        <div class="form-group col-md-4">
                            <label for="trainingStartDate">Training Start Date</label>
                            <input type="date" class="form-control" name="trainingStartDate" value="{{$elsm->training_start}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="trainingEndDate">Training End Date</label>
                            <input type="date" class="form-control" name="trainingEndDate" value="{{$elsm->training_end}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="cltCertificationDate">CLT Certification Date</label>
                            <input type="date" class="form-control" name="cltCertificationDate" value="{{$elsm->clt_date}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="cltCertificationStatus">CLT Certification Status</label>
                            <input type="text" class="form-control" name="cltCertificationStatus" value="{{$elsm->clt_status}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="offerLetterYesNo">Offer Letter Yes/No</label>
                            <input type="text" class="form-control" name="offerLetterYesNo" value="{{$elsm->offer_letter}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="offerLetterDate">Offer Letter Date</label>
                            <input type="date" class="form-control" name="offerLetterDate" value="{{$elsm->offer_later_date}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="bankAccountStatus">Bank Account Status</label>
                            <input type="text" class="form-control" name="bankAccountStatus" value="{{$elsm->bank_acc_status}}">
                        </div>
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="idCardIssue">ID Card Issue</label>-->
                        <!--    <input type="text" class="form-control" id="idCardIssue" value="{{$elsm->ojt_start}}">-->
                        <!--</div>-->
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="idCardIssueDate">ID Card Issue Date</label>-->
                        <!--    <input type="date" class="form-control" id="idCardIssueDate" value="{{$elsm->ojt_end}}">-->
                        <!--</div>-->
                        <div class="form-group col-md-4">
                            <label for="ojtStartDate">OJT Start Date</label>
                            <input type="date" class="form-control" name="ojtStartDate" value="{{$elsm->ojt_start}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="ojtEndDate">OJT End Date</label>
                            <input type="date" class="form-control" name="ojtEndDate" value="{{$elsm->ojt_end}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="ojtCertifiedDate">OJT Certified Date</label>
                            <input type="date" class="form-control" name="ojtCertifiedDate" value="{{$elsm->ojt_cert_date}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="ojtCertificationStatus">OJT Certification Status</label>
                            <input type="text" class="form-control" name="ojtCertificationStatus" value="{{$elsm->ojt_cert_status}}">
                        </div>
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="extendedOjtStartDate">Extended OJT Start Date</label>-->
                        <!--    <input type="date" class="form-control" id="extendedOjtStartDate" value="{{$elsm->handover_ops_date}}">-->
                        <!--</div>-->
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="extendedOjtEndDate">Extended OJT End Date</label>-->
                        <!--    <input type="date" class="form-control" id="extendedOjtEndDate" value="{{$elsm->handover_ops_date}}">-->
                        <!--</div>-->
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="extendedOjtCertifiedDate">Extended OJT Certified Date</label>-->
                        <!--    <input type="date" class="form-control" id="extendedOjtCertifiedDate" value="{{$elsm->handover_ops_date}}">-->
                        <!--</div>-->
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="extendedOjtCertificationStatus">Extended OJT Certification Status</label>-->
                        <!--    <input type="text" class="form-control" id="extendedOjtCertificationStatus" value="{{$elsm->handover_ops_date}}">-->
                        <!--</div>-->
                        <div class="form-group col-md-4">
                            <label for="handoverToProduction">Handover to Production</label>
                            <input type="text" class="form-control" name="handoverToProduction" value="{{$elsm->handover_ops_date}}">
                        </div>
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="appointmentLetter">Appointment Letter</label>-->
                        <!--    <input type="text" class="form-control" id="appointmentLetter" value="{{$elsm->handover_ops_date}}">-->
                        <!--</div>-->
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="appointmentLetterDate">Appointment Letter Date</label>-->
                        <!--    <input type="date" class="form-control" id="appointmentLetterDate" value="{{$elsm->handover_ops_date}}">-->
                        <!--</div>-->
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="warning1">Warning 1</label>-->
                        <!--    <input type="text" class="form-control" id="warning1" value="No">-->
                        <!--</div>-->
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="warning1Date">Warning 1 Date</label>-->
                        <!--    <input type="text" class="form-control" id="warning1Date" value="N/A">-->
                        <!--</div>-->
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="reason1">Reason</label>-->
                        <!--    <input type="text" class="form-control" id="reason1" value="{{$elsm->reason_for_leav}}">-->
                        <!--</div>-->
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="warning2">Warning 2</label>-->
                        <!--    <input type="text" class="form-control" id="warning2" value="No">-->
                        <!--</div>-->
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="warning2Date">Warning 2 Date</label>-->
                        <!--    <input type="text" class="form-control" id="warning2Date" value="N/A">-->
                        <!--</div>-->
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="reason2">Reason</label>-->
                        <!--    <input type="text" class="form-control" id="reason2" value="N/A">-->
                        <!--</div>-->
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="movementFrom">Movement From</label>-->
                        <!--    <input type="text" class="form-control" id="movementFrom" value="Department A">-->
                        <!--</div>-->
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="movementTo">Movement To</label>-->
                        <!--    <input type="text" class="form-control" id="movementTo" value="Department B">-->
                        <!--</div>-->
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="movementDate">Movement Date</label>-->
                        <!--    <input type="date" class="form-control" id="movementDate" value="2023-08-01">-->
                        <!--</div>-->
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="movementType">Movement Type</label>-->
                        <!--    <input type="text" class="form-control" id="movementType" value="Transfer">-->
                        <!--</div>-->
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="promotionType">Promotion Type</label>-->
                        <!--    <input type="text" class="form-control" id="promotionType" value="Internal">-->
                        <!--</div>-->
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="promotedDesignation">Promoted Designation</label>-->
                        <!--    <input type="text" class="form-control" id="promotedDesignation" value="Senior Developer">-->
                        <!--</div>-->
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="promotionDate">Promotion Date</label>-->
                        <!--    <input type="date" class="form-control" id="promotionDate" value="2023-09-01">-->
                        <!--</div>-->
                        <div class="form-group col-md-4">
                            <label for="currentStatus">Current Status</label>
                            <input type="text" class="form-control" name="currentStatus" value="{{$elsm->current_status}}">
                        </div>
                        <!--<div class="form-group col-md-4">-->
                        <!--    <label for="lwd">LWD</label>-->
                        <!--    <input type="text" class="form-control" id="lwd" value="{{$elsm->handover_ops_date}}">-->
                        <!--</div>-->
                        <div class="form-group col-md-4">
                            <label for="reasonForLeaving">Reason For Leaving</label>
                            <input type="text" class="form-control" name="reasonForLeaving" value="{{$elsm->reason_for_leav}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="noticePeriod">Notice Period Days</label>
                            <input type="text" class="form-control" name="noticePeriod" value="{{$elsm->notice_period_days}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="noticePeriodServedDays">Notice Period Served in Days</label>
                            <input type="text" class="form-control" name="noticePeriodServedDays" value="{{$elsm->notice_period_served}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="noticeServed">Notice Served</label>
                            <input type="text" class="form-control" name="noticeServed" value="{{$elsm->notice_served}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="remarks">Remarks</label>
                            <input type="text" class="form-control" name="remarks" value="{{$elsm->remark}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="fnfEligibility">FnF Eligibility</label>
                            <input type="text" class="form-control" name="fnfEligibility" value="{{$elsm->fnf_elg}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="fnfStatus">FnF Status</label>
                            <input type="text" class="form-control" name="fnfStatus" value="{{$elsm->fnf_status}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="fnfDate">FnF Date</label>
                            <input type="date" class="form-control" name="fnfDate" value="{{$elsm->fnf_date}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="experienceLetterStatus">Experience Letter Status</label>
                            <input type="text" class="form-control" name="experienceLetterStatus" value="{{$elsm->exp_letter}}">
                        </div>
                        <div class="form-group col-md-4">
                            <label for="experienceLetterStatus">Attrition Email</label>
                            <input type="text" class="form-control" name="att_email" id="experienceLetterStatus" value="{{$elsm->att_email}}">
                        </div>
                        </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>
    </form>


<!-- Modal -->
<form action="{{url('/account/employees/elcm/uploadCsv')}}" method="POST" enctype="multipart/form-data"> 
    @csrf
<div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Upload CSV</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group  col-md-12">
            <label for="bgvStatus">File</label>
            <input type="file" class="form-control" name="csv_file" accept=".csv">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Upload</button>
      </div>
    </div>
  </div>
</div>
</form>






<style>
    
/* Steps */
.step {
  position: relative;
  min-height: 1em;
  color: gray;
}
.step + .step {
  margin-top: 1.5em
}
.step > div:first-child {
  position: static;
  height: 0;
}
.step > div:not(:first-child) {
  margin-left: 1.5em;
  padding-left: 1em;
}
.step.step-active {
  color: #4285f4
}
.step.step-active .circle {
  background-color: #4285f4;
}

/* Circle */
.circle {
  background: gray;
  position: relative;
  width: 1.5em;
  height: 1.5em;
  line-height: 1.5em;
  border-radius: 100%;
  color: #fff;
  text-align: center;
  box-shadow: 0 0 0 3px #fff;
}

/* Vertical Line */
.circle:after {
  content: ' ';
  position: absolute;
  display: block;
  top: 1px;
  right: 50%;
  bottom: 1px;
  left: 50%;
  height: 100%;
  width: 1px;
  transform: scale(1, 2);
  transform-origin: 50% -100%;
  background-color: rgba(0, 0, 0, 0.25);
  z-index: -1;
}
.step:last-child .circle:after {
  display: none
}

/* Stepper Titles */
.title {
  line-height: 1.5em;
  font-weight: bold;
}
.caption {
  font-size: 0.8em;
}


.card {
    background-color: #fff;
    border-radius: 18px;
    box-shadow: 1px 1px 8px 0 grey;
    height: auto;
    margin-bottom: 20px;
    padding: 20px 0 20px 50px;
}

 .card table {
    border: none;
    font-size: 16px;
    height: 270px;
    width: 80%;
}
 table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 18px;
            text-align: left;
        }
        th, td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
        td:first-child {
            font-weight: bold;
            width: 200px;
        }
        td:nth-child(2) {
            width: 20px;
            text-align: center;
        }
</style>
@endsection