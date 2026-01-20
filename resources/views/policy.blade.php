@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')
<style>
    .policy-wrapper {
        background: linear-gradient(to bottom, #f9f9f9, #ffffff);
        padding: 40px 20px;
        min-height: 100vh;
        font-family: 'Inter', sans-serif; /* Modern font; fallback in body if not loaded */
    }

    .policy-card {
        background: #ffffff;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        padding: 30px;
        margin-bottom: 30px;
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
    }

    .policy-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
    }

    .policy-title {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 15px;
        color: #1a202c;
        text-align: center;
    }

    .policy-description {
        font-size: 15px;
        color: #4a5568;
        line-height: 1.8;
        word-wrap: break-word;
        overflow-wrap: break-word;
        hyphens: auto;
    }

    .acknowledgement-list {
        margin-top: 15px;
        padding-left: 20px;
        list-style-type: decimal;
    }

    .acknowledgement-list li {
        margin-bottom: 10px;
    }

    .btn-accept {
        min-width: 120px;
        font-weight: 600;
        background-color: #3182ce;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        transition: background-color 0.2s;
    }

    .btn-accept:hover {
        background-color: #2b6cb0;
    }

    hr.dashed {
        border-top: 2px dashed #e2e8f0;
        margin: 30px 0;
    }

    .policy-container {
        position: relative;
        padding: 40px 20px;
        font-size: 14px;
        line-height: 1.8;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
    }

    /* Responsive adjustments for mobile */
    @media (max-width: 768px) {
        .policy-wrapper {
            padding: 20px 10px;
        }

        .policy-card {
            padding: 20px;
            border-radius: 12px;
        }

        .policy-title {
            font-size: 20px;
        }

        .policy-description {
            font-size: 14px;
        }

        .policy-container {
            padding: 60px 20px 40px 20px; /* Adjusted for smaller screens */
        }

        .acknowledgement-list {
            padding-left: 15px;
        }

        /* Ensure no text overflow */
        body, p, li, div {
            word-wrap: break-word;
            overflow-wrap: break-word;
            hyphens: auto;
        }

        img {
            max-width: 100%;
            height: auto;
        }
    }

    /* Additional modern touches */
    .modal-content {
        border-radius: 12px;
        padding: 20px;
    }

    .btn-primary {
        background-color: #4299e1;
        border: none;
    }

    .btn-secondary {
        background-color: #a0aec1;
        border: none;
    }
</style>
<form action="{{ url('account/employees') }}" id="myForm" method="GET">
    <x-filters.filter-box>
        {{-- Filters go here --}}
    </x-filters.filter-box>
</form>
@endsection

@section('content')

@if(!$emp->sign_file)
<!-- Signature Modal -->
<div class="modal fade show" id="signatureModal" style="display:block;" tabindex="-1" aria-modal="true" role="dialog">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content p-4">
            <h4 class="mb-3">Please Provide Your Signature</h4>
            <canvas id="signature-pad" style="border:1px solid #ccc; width:100%; height:300px;"></canvas>
            <div class="mt-3 text-end">
                <button class="btn btn-secondary" onclick="clearPad()">Clear</button>
                <button class="btn btn-primary" onclick="submitSignature()">Save Signature</button>
            </div>
        </div>
    </div>
</div>
@endif

<form id="signature-form" method="POST" action="{{ route('signature.save') }}">
    @csrf
    <input type="hidden" name="signature" id="signature-input">
</form>

<div class="policy-wrapper">
    <div class="container">
        @if(request()->has('mess'))
            <script>
            $(document).ready(function() { 
                Swal.fire({
                  title: "Good job!",
                  text: "{{ request()->get('mess') }}",
                  icon: "success"
                });
                });
            </script>
            <div class="alert alert-danger">
                {{ request()->get('mess') }}
            </div>
        @endif

        @foreach($policy as $index => $p)
            @php
                $isAccepted = in_array($p->id, $policy_accepted);
            @endphp
            <div class="policy-card" id="policy-{{ $index }}" data-accepted="{{ $isAccepted ? 'yes' : 'no' }}">
                {{-- Existing title/description block --}}

                @if($p->id == 16)
                <div class="policy-container">
                    <!-- Letterhead as Background Image -->
                    <div style="
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: url('{{ asset('uploads/Web_Photo_Editor.jpg') }}') no-repeat center top;
                        background-size: cover;
                        opacity: 0.15; /* Light watermark effect */
                        z-index: 0;
                    "></div>
                
                    <!-- Content -->
                    <div style="position: relative; z-index: 1;">
                    <div class="policy-title">Acknowledgment Letter</div>
                    <div class="policy-description">
                        <p><strong>Acknowledgement Letter</strong></p>
                        <p>
                            I <strong>{{ $emp->name }}</strong>, son/daughter of
                            <strong>
                                @if($emp->father_name)
                                    {{ $emp->father_name }}
                                @elseif($emp->mother_name)
                                    {{ $emp->mother_name }}
                                @elseif($emp->guardian_name)
                                    {{ $emp->guardian_name }}
                                @else
                                    N/A
                                @endif
                            </strong>,
                            hereby acknowledge the following:
                        </p>
                        <ol class="acknowledgement-list">
                            <?php
                            // Define department and designation IDs
                            $internationalDepts = [20, 33, 24, 17, 14, 12, 35, 9, 7, 5, 4, 26, 25, 2];
                            $csaDesignations = [2, 12, 15, 78, 79, 46];
                            $ormDept = 43;
                            
                            $isInternational = in_array($emp->department_id, $internationalDepts);
                            $isCSA = in_array($emp->designation_id, $csaDesignations);
                            $isORM = ($emp->department_id == $ormDept);
                            
                            // Determine which content to show
                            if ($isORM) {
                                // ORM Department - show Acknowledgement Letter ORM.pdf content
                                ?>
                                <li>You are joining as a <strong>{{ $emp->designations_name }}</strong> on <strong>{{ date('d/m/Y', strtotime($emp->joining_date)) }}</strong> for the voice/chat support process.</li>
                                <li>You will be on the payroll of Niftel Communication Pvt. Ltd. There will be no joining bonus/incentives. The company does not provide a pick-and-drop facility.</li>
                                <li>Every selected candidate is supposed to clear the certification round after the training and their OJT certification to continue their employment with us. In case a candidate is unable to clear any of their certifications, he/she is not eligible for any payout from the company.</li>
                                <li>If a candidate fails to meet the required login hours—8 hours for Full-Timers—or takes frequent unplanned leave, their salary will be put on hold, with deductions applied for insufficient hours.</li>
                                <li>Rotational shifts apply to all employees: boys work 24/7 shifts, including evenings and nights, while girls have 7 AM to 8 PM shifts in-office and rotational shifts if working from home. This is a six-day work schedule with one rotational weekly off.</li>
                                <li>We maintain a Zero Tolerance Policy against data breaches and misconduct with staff or customers. Employees found violating these policies or marked as absconded will face separation and will not be eligible for Full and Final (FNF) settlement. Any employee falling under the ZT scenario (Organization as well as Process ZT) or being marked as absconded will result in separation and render them ineligible for Full and Final settlement (FNF).</li>
                                <li>If an employee is getting terminated on the basis of the ZT scenario (Process ZT), they will be liable to pay a recovery amount to the company.</li>
                                <li>To qualify for FNF, he/she is required to complete a 30/60/90-day notice period as per the notice period policy. If he/she fails to fulfill this requirement, he/she will be automatically marked as absconded, resulting in the forfeiture of FNF entitlements in such cases. As per policy, FNF will only be done after 45 days of the last working day. Immediate Resignation cannot be approved. If the advisor is not willing to serve a notice period, he/she will be automatically marked as absconded, resulting in the forfeiture of FNF entitlements in such cases.</li>
                                <li>During the notice period, advisors are required to be present for all scheduled working days. Any leave taken during this time may extend the end date. Unapproved or uninformed absences of three or more days may lead to misconduct, making the notice period void and rendering the advisor ineligible for settling dues with the company.</li>
                                <li>If a person is unavailable and not reporting to the office for three consecutive days without any approval or prior information via email will be absconded and will not be eligible for FNF. Leaves should be approved via email with proper documents.</li>
                                <li>No leaves will be provided, especially during festive and peak hours. Leave requests must align with process requirements. Advisors must obtain approval from their immediate supervisors before taking any leave. On-the-spot leave requests cannot be accommodated, except in cases of emergency.</li>
                                <li>Employees separated due to performance issues, behavioral concerns, or any Zero Tolerance (ZT) violation will not be eligible for Full and Final (FNF) settlement.</li>
                                <li>Employees are expected to adhere to a professional dress code that reflects the organization's standards.</li>
                                <li>Any employee who takes a half-day either immediately before or after any type of leave (including weekends, i.e., Saturday and Sunday, or company-declared holidays) will have the entire day marked as a full-day absence. In both scenarios, the sandwich leave policy will be strictly enforced. This means that any half days or leaves taken before and after a weekend or holiday will be treated as continuous leave, and all intervening non-working days will also be counted as leave.</li>
                                <li>All employees are expected to adhere to proper washroom etiquette. Any employee found violating the guidelines will be subject to a fine.</li>
                                <li>If an employee is absent for 5 or more days between the 1st and 15th of any month, it may result in a salary hold ranging from 50% to 100%, Depending on the circumstances and subject to the Manager's review and approval.</li>
                                <li>Any violation of the POSH (Prevention of Sexual Harassment) policy will lead to immediate termination, and the individual will not be entitled to any Full and Final (FNF) settlement.</li>
                                <?php
                            } elseif ($isInternational) {
                                // International Department
                                if ($isCSA) {
                                    // International CSA - show rms.niftel.com_account_ViewMyack.pdf content
                                    ?>
                                    <li>You are joining as a <strong>{{ $emp->designations_name }}</strong> on <strong>{{ date('d/m/Y', strtotime($emp->joining_date)) }}</strong> for the voice/chat support process.</li>
                                    <li>You will be on the payroll of Niftel Communication Pvt. Ltd. There will be no joining bonus/incentives. The company does not provide a pick-and-drop facility.</li>
                                    <li>Every selected candidate is supposed to clear the certification round after the training and their OJT certification to continue their employment with us. In case a candidate is unable to clear any of their certifications, he/she is not eligible for any payout from the company.</li>
                                    <li>In case a candidate is working from home, the candidate should have his/her own laptop and a stable broadband connection. Usage of mobile data for login is strictly prohibited. WFH is only applicable to a few processes or LOBs. Not all processes have WFH.</li>
                                    <li>If a candidate fails to meet the required login hours—5 hours for Part-Timers and 8 hours for Full-Timers—or takes frequent unplanned leave, their salary will be put on hold, with deductions applied for insufficient hours.</li>
                                    <li>Rotational shifts apply to all employees: boys work 24/7 shifts, including evenings and nights, while girls have 7 AM to 8 PM shifts in-office and rotational shifts if working from home. This is a six-day work schedule with one rotational weekly off.</li>
                                    <li>We maintain a Zero Tolerance Policy against data breaches and misconduct with staff or customers. Employees found violating these policies or marked as absconded will face separation and will not be eligible for Full and Final (FNF) settlement. Any employee falling under the ZT scenario (Organization as well as Process ZT) or being marked as absconded will result in separation and render them ineligible for Full and Final settlement (FNF).</li>
                                    <li>If an employee is getting terminated on the basis of ZT scenario (Process ZT) they will be liable to pay a recovery amount to the company.</li>
                                    <li>To qualify for FNF, he/she is required to complete a 30-day notice period. If he/she fails to fulfill this requirement, he/she will be automatically marked as absconded, resulting in the forfeiture of FNF entitlements in such cases. As per policy, FNF will be only done after 45 days of the last working day.</li>
                                    <li>Immediate Resignation cannot be approved. If the advisor is not willing to serve a notice period, he/she will be automatically marked as absconded, resulting in the forfeiture of FNF entitlements in such cases.</li>
                                    <li>During the notice period, advisors are required to be present for all scheduled working days. Any leave taken during this time may extend the end date. Unapproved or uninformed absences of three or more days may lead to misconduct, making the notice period void and rendering the advisor ineligible for settling dues with the company.</li>
                                    <li>If a person is unavailable and not reporting to the office for three consecutive days without any approval or prior information over the mail will be absconded and will not be eligible for FNF. Leaves should be approved over the mail with proper documents.</li>
                                    <li>Leave requests must align with process requirements, especially during festive and peak hours. Advisors must obtain approval from their immediate supervisors before taking any leave. On-the-spot leave requests cannot be accommodated, except in cases of emergency.</li>
                                    <li>Employees separated due to performance issues, behavioral concerns, or any Zero Tolerance (ZT) violation will not be eligible for Full and Final (FNF) settlement.</li>
                                    <li>Employees are expected to adhere to a professional dress code that reflects the organization's standards.</li>
                                    <?php
                                } else {
                                    // International Non-CSA - show Acknowledgement Letter International NoN CSA (1).pdf content
                                    ?>
                                    <li>You are joining as a <strong>{{ $emp->designations_name }}</strong> on <strong>{{ date('d/m/Y', strtotime($emp->joining_date)) }}</strong>.</li>
                                    <li>You will be on the payroll of Miftel Communication Pvt. Ltd. There will be no joining bonus/incentives.</li>
                                    <li>Every selected candidate is supposed to clear the certification round after the training to continue their employment with us. In case a candidate is unable to clear any of their certifications, he/she is not eligible for any payout from the company.</li>
                                    <li>Employees are required to adhere to a work schedule of 8 hours per shift, as determined by process requirements. Failure to meet the required login hours may result in deductions for insufficient hours.</li>
                                    <li>During the probationary period, leave requests are generally not accommodated. Frequent unapproved or unplanned absences may result in salary being withheld. Leave requests should align with process requirements, particularly during festive and peak periods. Advisors must obtain approval from their immediate supervisors before taking any leave. Unplanned leave requests cannot be accommodated, except in emergencies.</li>
                                    <li>We maintain a zero-tolerance policy against data breaches and misconduct with staff or customers. Employees found violating these policies or marked as absconded will face separation and will not be eligible for Full and Final (FNF) settlement. Any employee falling under the ZT scenario (Organization as well as Process ZT policies) or being marked as absconded will result in separation and render them ineligible for Full and Final settlement (FNF).</li>
                                    <li>To qualify for FNF, he/she is required to complete 30, 60 to 90 days notice period as per the notice period policy. If he/she fails to fulfill this requirement, he/she will be automatically marked as absconded, resulting in the forfeiture of FNF entitlements in such cases. As per policy, FNF will be only done after 45 days of the last working day.</li>
                                    <li>Immediate Resignation cannot be approved. If the advisor is not willing to serve a notice period, he/she will be automatically marked as absconded, resulting in the forfeiture of FNF entitlements in such cases.</li>
                                    <li>During the notice period, advisors are required to be present for all scheduled working days. Any leave taken during this time may extend the end date. Unapproved or uninformed absences of three or more days may lead to abscondment, making the notice period void and rendering the advisor ineligible for settling dues with the company.</li>
                                    <li>If a person is unavailable and not reporting to the office for three consecutive days without any approval or prior information over the mail will be absconded and will not be eligible for FNF. Leaves should be approved over the mail by your Manager with proper documents.</li>
                                    <li>Employees separated due to performance issues, behavioral concerns, or any Zero Tolerance (ZT) violation will not be eligible for Full and Final (FNF) settlement.</li>
                                    <li>Employees are expected to adhere to a professional dress code that reflects the organization's standards.</li>
                                    <li>Please ensure that you complete your document submissions on the RMS portal promptly. Failure to do so may result in your salary being withheld until all required documents are fully submitted.</li>
                                    <li>Any employee who takes a half-day either immediately before or after any type of leave (including weekends, i.e., Saturday and Sunday, or company-declared holidays) will have the entire day marked as a full-day absence. In both scenarios, the sandwich leave policy will be strictly enforced. This means that any half days or leaves taken before and after a weekend or holiday will be treated as continuous leave, and all intervening non-working days will also be counted as leave.</li>
                                    <li>No leaves will be provided especially during festive and peak hours. Leave requests must align with process requirements. Advisors must obtain approval from their immediate supervisors before taking any leave. On-the-spot leave requests cannot be accommodated, except in cases of emergency.</li>
                                    <li>All employees are expected to adhere to proper washroom etiquette. Any employee found violating the guidelines will be subject to a fine.</li>
                                    <li>If an employee is absent for 5 or more days between the 1st and 15th of any month, it may result in a salary hold ranging from 50% to 100%, depending on the circumstances and subject to the Manager's review and approval.</li>
                                    <li>Any violation of the POSH (Prevention of Sexual Harassment) policy will lead to immediate termination, and the individual will not be entitled to any Full and Final (FNF) settlement.</li>
                                    <?php
                                }
                            } else {
                                // Domestic Department
                                if ($isCSA) {
                                    // Domestic CSA - show Acknowledgement Letter Domestic 2025.pdf content
                                    ?>
                                    <li>You are joining as a <strong>{{ $emp->designations_name }}</strong> on <strong>{{ date('d/m/Y', strtotime($emp->joining_date)) }}</strong> for the voice/chat support process.</li>
                                    <li>You will be on the payroll of Niftel Communication Pvt. Ltd. There will be no joining bonus/incentives. The company does not provide a pick-and-drop facility.</li>
                                    <li>Every selected candidate is supposed to clear the certification round after the training and their OJT certification to continue their employment with us. In case a candidate is unable to clear any of their certifications, he/she is not eligible for any payout from the company.</li>
                                    <li>In case a candidate is working from home, the candidate should have his/her own laptop and a stable broadband connection. Usage of mobile data for login is strictly prohibited. Please note that WFH will be granted based on performance; otherwise, employees will need to continue working from the office. WFH is only applicable to a few processes or LOBs. Not all processes have WFH.</li>
                                    <li>If a candidate fails to meet the required login hours—5 hours for Part-Timers and 8 hours for Full-Timers—or takes frequent unplanned leave, their salary will be put on hold, with deductions applied for insufficient hours.</li>
                                    <li>Rotational shifts apply to all employees: boys work 24/7 shifts, including evenings and nights, while girls have 7 AM to 8 PM shifts in-office and rotational shifts if working from home. This is a six-day work schedule with one rotational weekly off.</li>
                                    <li>We maintain a Zero Tolerance Policy against data breaches and misconduct with staff or customers. Employees found violating these policies or marked as absconded will face separation and will not be eligible for Full and Final (FNF) settlement. Any employee falling under the ZT scenario (Organization as well as Process ZT) or being marked as absconded will result in separation and render them ineligible for Full and Final settlement (FNF).</li>
                                    <li>If an employee is getting terminated on the basis of ZT scenario (Process ZT) they will be liable to pay a recovery amount to the company.</li>
                                    <li>To qualify for FNF, he/she is required to complete a 30-day notice period. If he/she fails to fulfill this requirement, he/she will be automatically marked as absconded, resulting in the forfeiture of FNF entitlements in such cases. As per policy, FNF will be only done after 45 days of the last working day. Immediate Resignation cannot be approved. If the advisor is not willing to serve a notice period, he/she will be automatically marked as absconded, resulting in the forfeiture of FNF entitlements in such cases.</li>
                                    <li>During the notice period, advisors are required to be present for all scheduled working days. Any leave taken during this time may extend the end date. Unapproved or uninformed absences of three or more days may lead to misconduct, making the notice period void and rendering the advisor ineligible for settling due with the company.</li>
                                    <li>If a person is unavailable and not reporting to the office for three consecutive days without any approval or prior information over the mail will be absconded and will not be eligible for FNF. Leaves should be approved over the mail with proper documents.</li>
                                    <li>No leaves will be provided especially during festive and peak hours. Leave requests must align with process requirements. Advisors must obtain approval from their immediate supervisors before taking any leave. On-the-spot leave requests cannot be accommodated, except in cases of emergency.</li>
                                    <li>Employees separated due to performance issues, behavioral concerns, or any Zero Tolerance (ZT) violation will not be eligible for Full and Final (FNF) settlement.</li>
                                    <li>Employees are expected to adhere to a professional dress code that reflects the organization's standards.</li>
                                    <li>Any employee who takes a leave either immediately before or after any type of leave (including weekends, i.e., Saturday and Sunday, or company-declared holidays) will have the entire day marked as a full-day absence. In both scenarios, the sandwich leave policy will be strictly enforced. This means that any half days or leaves taken before and after a weekend or holiday will be treated as continuous leave, and all intervening non-working days will also be counted as leave.</li>
                                    <li>All employees are expected to adhere to proper washroom etiquette. Any employee found violating the guidelines will be subject to a fine.</li>
                                    <li>If an employee is absent for 5 or more days between the 1st and 15th of any month, it may result in a salary hold ranging from 50% to 100%, depending on the circumstances and subject to the Manager's review and approval.</li>
                                    <li>Any violation of the POSH (Prevention of Sexual Harassment) policy will lead to immediate termination, and the individual will not be entitled to any Full and Final (FNF) settlement.</li>
                                    <?php
                                } else {
                                    // Domestic Non-CSA - show ackn_letter_csa_domestic.pdf content
                                    ?>
                                    <li>You are joining as a <strong>{{ $emp->designations_name }}</strong> on <strong>{{ date('d/m/Y', strtotime($emp->joining_date)) }}</strong> for the voice/chat support process.</li>
                                    <li>You will be on the payroll of Niftel Communication Pvt. Ltd. There will be no joining bonus/incentives. The company does not provide a pick-and-drop facility.</li>
                                    <li>Every selected candidate is supposed to clear the certification round after the training and their OJT certification to continue their employment with us. In case a candidate is unable to clear any of their certifications, he/she is not eligible for any payout from the company.</li>
                                    <li>In case a candidate is working from home, the candidate should have his/her own laptop and a stable broadband connection. Usage of mobile data for login is strictly prohibited. WFH is only applicable to a few processes or LOBs. Not all processes have WFH.</li>
                                    <li>If a candidate fails to meet the required login hours—5 hours for Part-Timers and 8 hours for Full-Timers—or takes frequent unplanned leave, their salary will be put on hold, with deductions applied for insufficient hours.</li>
                                    <li>Rotational shifts apply to all employees: boys work 24/7 shifts, including evenings and nights, while girls have 7 AM to 8 PM shifts in-office and rotational shifts if working from home. This is a six-day work schedule with one rotational weekly off.</li>
                                    <li>We maintain a Zero Tolerance Policy against data breaches and misconduct with staff or customers. Employees found violating these policies or marked as absconded will face separation and will not be eligible for Full and Final (FNF) settlement. Any employee falling under the ZT scenario (Organization as well as Process ZT) or being marked as absconded will result in separation and render them ineligible for Full and Final settlement (FNF).</li>
                                    <li>If an employee is getting terminated on the basis of ZT scenario (Process ZT) they will be liable to pay a recovery amount to the company.</li>
                                    <li>To qualify for FNF, he/she is required to complete a 30-day notice period. If he/she fails to fulfill this requirement, he/she will be automatically marked as absconded, resulting in the forfeiture of FNF entitlements in such cases. As per policy, FNF will be only done after 45 days of the last working day.</li>
                                    <li>Immediate Resignation cannot be approved. If the advisor is not willing to serve a notice period, he/she will be automatically marked as absconded, resulting in the forfeiture of FNF entitlements in such cases.</li>
                                    <li>During the notice period, advisors are required to be present for all scheduled working days. Any leave taken during this time may extend the end date. Unapproved or uninformed absences of three or more days may lead to abscondment, making the notice period void and rendering the advisor ineligible for settling dues with the company.</li>
                                    <li>If a person is unavailable and not reporting to the office for three consecutive days without any approval or prior information over the mail will be absconded and will not be eligible for FNF. Leaves should be approved over the mail with proper documents.</li>
                                    <li>Leave requests must align with process requirements, especially during festive and peak hours. Advisors must obtain approval from their immediate supervisors before taking any leave. On-the-spot leave requests cannot be accommodated, except in cases of emergency.</li>
                                    <li>Employees separated due to performance issues, behavioral concerns, or any Zero Tolerance (ZT) violation will not be eligible for Full and Final (FNF) settlement.</li>
                                    <li>Employees are expected to adhere to a professional dress code that reflects the organization's standards.</li>
                                    <?php
                                }
                            }
                            ?>
                        </ol>
                    </div>
                    </div>
                </div>
                @elseif($p->id == 22)
                <div class="policy-container">
                    <!-- Letterhead as Background Image -->
                    <div style="
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: url('{{ asset('uploads/Web_Photo_Editor.jpg') }}') no-repeat center top;
                        background-size: cover;
                        opacity: 0.15; /* Light watermark effect */
                        z-index: 0;
                    "></div>
                
                    <!-- Content -->
                    <div style="position: relative; z-index: 1;">
                    <div class="policy-title">NON-DISCLOSURE AGREEMENT	</div>
                        <div class="policy-description">
                            <p><strong>(For Employees)</strong></p>
                            <p>
                                This Non-Disclosure and Non-Solicitation Agreement (“Agreement”) is made and entered into on this
                               {{ date('d/m/Y', strtotime($emp->joining_date)) }} (“Effective Date”), by and between:
                            </p>
                        
                            <p><strong>Niftel Communications Pvt. Ltd.</strong>, a company incorporated under the Companies Act, 2013, having its registered office at  {{ $emp->b_address }}
                            (hereinafter referred to as the “Employer” or “NIFTEL”, which expression shall, unless repugnant to the context or meaning thereof, be deemed to include its successors and assigns), of the First Part;</p>
                        
                            <p><strong>AND</strong></p>
                        
                            <p>
                                <strong>{{ $emp->name }} </strong>, residing at {{ $emp->local_add }} (hereinafter referred to as the “Employee”, which expression shall, unless repugnant to the context or meaning thereof, be deemed to include his/her heirs, legal representatives, and permitted assigns), of the Other Part.
                            </p>
                        
                            <p>NIFTEL and the Employee shall hereinafter be collectively referred to as the "Parties" and individually as a "Party".</p>
                        
                            <h4>1. CONFIDENTIAL INFORMATION</h4>
                            <h5>1.1 Definition</h5>
                            <p>“Confidential Information” means all non-public, proprietary, and sensitive information—whether oral, written, digital, or otherwise disclosed—relating to the Employer’s business, including:</p>
                            <ul>
                                <li>Internal operations, client data, business models, marketing, pricing, and financial data;</li>
                                <li>Personal or contractual data relating to employees, clients, or vendors;</li>
                                <li>All information developed by the Employee in the course of employment;</li>
                                <li>Any information disclosed to NIFTEL by third parties under confidentiality obligations.</li>
                            </ul>
                        
                            <h5>1.2 Exceptions</h5>
                            <p>Confidential Information does not include information that:</p>
                            <ul>
                                <li>Is or becomes publicly available without breach of this Agreement;</li>
                                <li>Was lawfully in the Employee’s possession prior to disclosure;</li>
                                <li>Is disclosed under compulsion of law, after prior written notice to NIFTEL (where legally permissible).</li>
                            </ul>
                        
                            <h4>2. NON-DISCLOSURE OBLIGATIONS</h4>
                            <p>The Employee shall:</p>
                            <ul>
                                <li>Maintain strict confidentiality and prevent unauthorized access;</li>
                                <li>Not disclose Confidential Information to any third party except on a strict need-to-know basis within NIFTEL;</li>
                                <li>Use Confidential Information solely for job-related purposes and not for any personal or third-party benefit;</li>
                                <li>Return or destroy all Confidential Information upon termination of employment.</li>
                            </ul>
                        
                            <h4>3. NON-SOLICITATION AND NON-COMPETE</h4>
                            <h5>3.1 Non-Solicitation</h5>
                            <p>For a period of one (1) year following termination of employment, the Employee shall not, directly or indirectly:</p>
                            <ul>
                                <li>Solicit, attempt to solicit, or engage with any client, customer, or vendor of NIFTEL with whom the Employee had dealings during the last 12 months of employment;</li>
                                <li>Solicit or attempt to induce any employee, consultant, or contractor of NIFTEL to terminate or modify their employment or contractual engagement.</li>
                            </ul>
                        
                            <h5>3.2 Non-Compete</h5>
                            <p>The Employee agrees that during employment and for a period of six (6) months after cessation of employment, they shall not:</p>
                            <ul>
                                <li>Engage, be employed by, consult for, or start any business that competes with the business of NIFTEL, within the same geographical territory or process vertical where the Employee was assigned;</li>
                                <li>Undertake any activity that may result in a conflict of interest with the Employer’s business.</li>
                            </ul>
                        
                            <h4>4. CONFLICT OF INTEREST DECLARATION</h4>
                            <p>The Employee affirms that:</p>
                            <ul>
                                <li>They are not currently engaged in, nor shall they engage in, any activity, employment, or business that conflicts with the interests of NIFTEL;</li>
                                <li>They will disclose in writing any potential conflict of interest to the management, including but not limited to: dual employment, business affiliations, vendor relations, or financial interests that may compromise impartiality or decision-making;</li>
                                <li>Any undisclosed or unresolved conflict of interest may result in disciplinary action, including termination.</li>
                            </ul>
                        
                            <h4>5. MISCELLANEOUS</h4>
                            <h5>5.1 Perpetual Confidentiality</h5>
                            <p>The confidentiality and non-disclosure obligations under this Agreement shall continue indefinitely, including after the termination of employment, irrespective of the reason for cessation.</p>
                        
                            <h5>5.2 Remedies for Breach</h5>
                            <p>The Employee acknowledges that any breach may cause irreparable harm for which monetary damages may be inadequate. NIFTEL shall be entitled to seek injunctive relief, in addition to legal damages, disciplinary action, or termination of employment.</p>
                        
                            <h5>5.3 No Waiver</h5>
                            <p>Failure by NIFTEL to enforce any right under this Agreement shall not constitute a waiver. All rights and remedies are cumulative.</p>
                        
                            <h5>5.4 Governing Law and Jurisdiction</h5>
                            <p>This Agreement shall be governed by the laws of India. The Parties agree that any dispute shall be subject to the exclusive jurisdiction of courts in India.</p>
                        
                            <h4>6. TERM</h4>
                            <p>This Agreement is effective from the Effective Date and shall remain binding during the term of employment and indefinitely thereafter with respect to confidentiality and non-solicitation clauses.</p>
                        
                            <h4>IN WITNESS WHEREOF</h4>
                            <p>The Parties have executed this Agreement on the date first above written.</p>
                        
                            <p><strong>For the Employee</strong></p>
                            <p>
                                Signature: <img src="{{ asset('signatures/'.$emp->sign_file) }}" style="max-width: 25%;" alt="signature" height="50"> <br>
                                Name: {{ $emp->name }}<br>
                                Address: {{ $emp->local_add }}<br>
                                Date: {{ date('d/m/Y', strtotime($emp->joining_date)) }}
                            </p>
                        </div>
                    </div>
                </div>
                @else
                <div class="policy-container">
                    <!-- Letterhead as Background Image -->
                    <div style="
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: url('{{ asset('uploads/Web_Photo_Editor.jpg') }}') no-repeat center top;
                        background-size: cover;
                        opacity: 0.15; /* Light watermark effect */
                        z-index: 0;
                    "></div>
                
                    <!-- Content -->
                    <div style="position: relative; z-index: 1;">
                        <div class="policy-title" style="text-align:center; font-size:20px; font-weight:bold; margin-bottom:20px;">
                            {{ $p->title }}
                        </div>
                        <div class="policy-description" style="white-space: pre-line;">
                            {!! htmlspecialchars_decode($p->policy) !!}
                        </div>
                    </div>
                </div>

                @endif

                <div class="mt-4 text-end">
                    @if(in_array($p->id, $policy_accepted))
                        <div class="d-flex align-items-center justify-content-end gap-2">
                            <span class="text-dark">Signature - </span>
                            <img src="{{ asset('signatures/'.$emp->sign_file) }}" style="max-width: 25%;" alt="signature" height="50">
                        </div>

                    @else
                        <!--<a href="{{ url('policy_accept/' . $p->id) }}" class="btn btn-primary btn-accept">Accept</a>-->
                    @endif
                </div>
            </div>
            <hr class="dashed">
        @endforeach
        
        <!--@if(!$all_accept)-->
        <div style="text-align: center;">
            <a href="{{ url('policy_accept_all') }}" class="btn btn-success">
                Accept All Policies
            </a>
        </div>
        <!--@endif-->

    </div>
</div>
@endsection

@push('scripts')

@if(!$emp->sign_file)
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.0.0/dist/signature_pad.umd.min.js"></script>
<script>
    const canvas = document.getElementById('signature-pad');
    const signaturePad = canvas ? new SignaturePad(canvas) : null;

    if (signaturePad) {
        resizeCanvas();

        window.addEventListener('resize', resizeCanvas);

        function resizeCanvas() {
            const ratio = Math.max(window.devicePixelRatio || 1, 1);
            canvas.width = canvas.offsetWidth * ratio;
            canvas.height = canvas.offsetHeight * ratio;
            canvas.getContext("2d").scale(ratio, ratio);
            signaturePad.clear();
        }

        window.clearPad = function () {
            signaturePad.clear();
        }

        window.submitSignature = function () {
            if (!signaturePad.isEmpty()) {
                document.getElementById('signature-input').value = signaturePad.toDataURL("image/png");
                document.getElementById('signature-form').submit();
            } else {
                alert("Please sign first.");
            }
        }
    }

    @if(!$emp->sign_file)
        // Prevent interaction with anything behind the modal
        document.body.classList.add('modal-open');
        const backdrop = document.createElement('div');
        backdrop.classList.add('modal-backdrop', 'fade', 'show');
        document.body.appendChild(backdrop);
    @endif
</script>
@endif

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const cards = document.querySelectorAll('.policy-card');
        let scrolled = false;

        cards.forEach((card) => {
            if (card.dataset.accepted === "no" && !scrolled) {
                card.scrollIntoView({ behavior: "smooth", block: "start" });
                scrolled = true;
            }
        });
    });
</script>
@if($all_accept === true)
    <script>
        if (confirm("Policies Accepted Successfully! Do you want redirect on documents page?")) {
            window.location.href = "{{ url('account/settings/profile-settings?tab=documents') }}";
        }
    </script>
@endif

@endpush