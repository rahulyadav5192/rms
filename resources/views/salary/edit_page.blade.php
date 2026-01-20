@extends('layouts.app')

@section('content')
<div class="container">
    <h3 class="mb-3">Edit Salary Slip – {{ $slip->employee_name }}</h3>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    <form action="{{ route('salarySlip.update', $slip->id) }}" method="POST">
        @csrf

        <div class="row">

            {{-- Month & Year --}}
            <!--<div class="col-md-3 mb-3">-->
            <!--    <label>Month</label>-->
            <!--    <input type="text" name="month" class="form-control" value="{{ $slip->month }}">-->
            <!--</div>-->

            <!--<div class="col-md-3 mb-3">-->
            <!--    <label>Month Name</label>-->
            <!--    <input type="text" name="month_name" class="form-control" value="{{ $slip->month_name }}">-->
            <!--</div>-->

            <!--<div class="col-md-3 mb-3">-->
            <!--    <label>Year</label>-->
            <!--    <input type="text" name="year" class="form-control" value="{{ $slip->year }}">-->
            <!--</div>-->

            {{-- CTC --}}
            <div class="col-md-3 mb-3">
                <label>CTC (Year)</label>
                <input type="text" name="ctc_year" class="form-control" value="{{ $slip->ctc_year }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>Days in Month</label>
                <input type="text" name="days_in_month" class="form-control" value="{{ $slip->days_in_month }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>Payable Days</label>
                <input type="text" name="payable_days" class="form-control" value="{{ $slip->payable_days }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>CTC / Month</label>
                <input type="text" name="ctc_month" class="form-control" value="{{ $slip->ctc_month }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>CTC as per Payable Days</label>
                <input type="text" name="ctc_as_per_payable_days" class="form-control" value="{{ $slip->ctc_as_per_payable_days }}">
            </div>

            {{-- Salary Components --}}
            <div class="col-md-3 mb-3">
                <label>Basic Salary</label>
                <input type="text" name="bas_month" class="form-control" value="{{ $slip->bas_month }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>HRA</label>
                <input type="text" name="hra_month" class="form-control" value="{{ $slip->hra_month }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>Convenience Allowance</label>
                <input type="text" name="convenience" class="form-control" value="{{ $slip->convenience }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>VSA</label>
                <input type="text" name="vsa" class="form-control" value="{{ $slip->vsa }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>Special Allowance</label>
                <input type="text" name="specialAllowance" class="form-control" value="{{ $slip->specialAllowance }}">
            </div>

            {{-- Deductions --}}
            <div class="col-md-3 mb-3">
                <label>Deduction Keys</label>
                <input type="text" name="deduction_keys" class="form-control" value="{{ $slip->deduction_keys }}">
            </div>

            <div class="col-md-3 mb-3">
    <label>PF Deduction</label>
    <select name="pf_deduct" class="form-control">
        <option value="1" {{ $slip->pf_deduct == 1 ? 'selected' : '' }}>No</option>
        <option value="0" {{ $slip->pf_deduct == 0 ? 'selected' : '' }}>Yes</option>
    </select>
</div>


            <div class="col-md-3 mb-3">
                <label>Employee PF</label>
                <input type="text" name="employeePF" class="form-control" value="{{ $slip->employeePF }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>Employee ESIC</label>
                <input type="text" name="employeeESIC" class="form-control" value="{{ $slip->employeeESIC }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>Employer PF</label>
                <input type="text" name="employerPF" class="form-control" value="{{ $slip->employerPF }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>Employer ESIC</label>
                <input type="text" name="employerESIC" class="form-control" value="{{ $slip->employerESIC }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>Leave Deduction</label>
                <input type="text" name="leave_deduct" class="form-control" value="{{ $slip->leave_deduct }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>Other Deduction</label>
                <input type="text" name="other_deduction" class="form-control" value="{{ $slip->other_deduction }}">
            </div>

            {{-- Overtime --}}
            <div class="col-md-3 mb-3">
                <label>Overtime Amount</label>
                <input type="text" name="overtime_amount" class="form-control" value="{{ $slip->overtime_amount }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>TA (Travel Allowance)</label>
                <input type="text" name="ta" class="form-control" value="{{ $slip->ta }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>CECL</label>
                <input type="text" name="cecl" class="form-control" value="{{ $slip->cecl }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>OT Incentive (Client Driven)</label>
                <input type="text" name="ot_incentive_client_driven" class="form-control" value="{{ $slip->ot_incentive_client_driven }}">
            </div>

            {{-- Salary Totals --}}
            <div class="col-md-3 mb-3">
                <label>Gross Salary</label>
                <input type="text" name="grossSalary" class="form-control" value="{{ $slip->grossSalary }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>Total Deduction</label>
                <input type="text" name="total_deduction" class="form-control" value="{{ $slip->total_deduction }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>Net Take Home</label>
                <input type="text" name="netTakehome" class="form-control" value="{{ $slip->netTakehome }}">
            </div>

            <!--<div class="col-md-6 mb-3">-->
            <!--    <label>Net Take Home (In Words)</label>-->
            <!--    <input type="text" name="net_tak_home_word" class="form-control" value="{{ $slip->net_tak_home_word }}">-->
            <!--</div>-->

            <div class="col-md-3 mb-3">
                <label>Arrear</label>
                <input type="text" name="arrear" class="form-control" value="{{ $slip->arrear }}">
            </div>

            <div class="col-md-3 mb-3">
                <label>Final Pay</label>
                <input type="text" name="final_pay" class="form-control" value="{{ $slip->final_pay }}">
            </div>

            <!--<div class="col-md-6 mb-3">-->
            <!--    <label>Final Pay (In Words)</label>-->
            <!--    <input type="text" name="final_pay_word" class="form-control" value="{{ $slip->final_pay_word }}">-->
            <!--</div>-->

            {{-- Bank Details --}}
            <div class="col-md-4 mb-3">
                <label>Bank</label>
                <input type="text" name="bank" class="form-control" value="{{ $slip->bank }}">
            </div>

            <div class="col-md-4 mb-3">
                <label>IFSC</label>
                <input type="text" name="ifsc" class="form-control" value="{{ $slip->ifsc }}">
            </div>

            <div class="col-md-4 mb-3">
                <label>Account No</label>
                <input type="text" name="account" class="form-control" value="{{ $slip->account }}">
            </div>

        </div>

        <button class="btn btn-primary">Update Slip</button>
        <a href="{{ url()->previous() }}" class="btn btn-secondary">Back</a>
    </form>
</div>
@endsection
