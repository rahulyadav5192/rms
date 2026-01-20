<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Niftel | Acknowledgment Letter</title>
    <style>
        /* Import Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Times+New+Roman:wght@400;700&display=swap');

        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            line-height: 1.4;
            background: #f5f5f5;
            padding: 10px;
        }

        /* Main container */
        .ack-letter-container {
            position: relative;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            height: 1122px; /* A4 height at 96dpi */
        }

        /* Background image */
        .background-image {
            position: absolute;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: 0;
            /*opacity: 0.1;*/
        }

        /* Content wrapper */
        .content-wrapper {
            position: relative;
            z-index: 1;
            width: 100%;
            height: 100%;
            padding: 80px 70px;
            box-sizing: border-box;
        }

        /* Header section */
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .date {
            font-size: 13px;
            font-weight: bold;
        }

        .title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 20px 0;
            text-decoration: underline;
        }

        /* Employee details */
        .employee-details {
            margin-bottom: 20px;
            font-size: 14px;
        }

        /* Main content */
        .main-content {
            margin-bottom: 20px;
        }

        .acknowledgement-text {
            margin-bottom: 15px;
            text-align: justify;
            font-size: 14px;
        }

        /* Terms list */
        .terms-list {
            list-style-type: none;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .terms-list li {
            margin-bottom: 8px;
            text-align: justify;
            position: relative;
            padding-left: 20px;
        }

        .terms-list li:before {
            content: "•";
            position: absolute;
            left: 5px;
            font-weight: bold;
            font-size: 16px;
        }

        /* Signature section */
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature-box {
            width: 250px;
            text-align: center;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin: 50px auto 10px;
            width: 200px;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
        }

        /* Print styles */
        @media print {
            body {
                padding: 0;
                background: white;
            }

            .ack-letter-container {
                box-shadow: none;
                height: auto;
                page-break-after: avoid;
                page-break-inside: avoid;
            }

            .background-image {
                /*opacity: 0.05;*/
            }
        }

        /* Print button */
        .print-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #2c3e50;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            z-index: 100;
        }

        .print-btn:hover {
            background-color: #1a252f;
        }

        @media print {
            .print-btn {
                display: none;
            }
        }
       
        
    </style>
</head>
<body>
    <div class="ack-letter-container">
        <img src="https://rms.niftel.com/public/bg_ack_new.png" alt="Background" class="background-image">
        
        <div class="content-wrapper">
            <!-- Header Section -->
            <div class="header">
                <div class="date">DATE: {{ date('d/m/Y', strtotime($emp->joining_date)) }}</div>
            </div>

            <h1 class="title">ACKNOWLEDGEMENT LETTER</h1>

            <!-- Employee Details -->
            <div class="employee-details">
                <p>I <strong>{{ $emp->name }}</strong>, son/daughter of <strong>
                    @if($emp->father_name)
                        {{ $emp->father_name }}
                    @elseif($emp->mother_name)
                        {{ $emp->mother_name }}
                    @elseif($emp->guardian_name)
                        {{ $emp->guardian_name }}
                    @else
                        N/A
                    @endif
                </strong>,</p>
                <p>hereby acknowledge the following:</p>
            </div>

            <!-- Main Content -->
            <div class="main-content">
                <ol class="terms-list">
                    @if($isORM)
                        <!-- ORM Department content -->
                        <li>You are joining as a <strong>{{ $emp->designations_name }}</strong> on <strong>{{ date('d/m/Y', strtotime($emp->joining_date)) }}</strong>.</li>
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
                    
                    @elseif($isInternational)
                        @if($isCSA)
                            <!-- International CSA content -->
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
                        
                        @else
                            <!-- International Non-CSA content -->
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
                        @endif
                    
                    @else
                        @if($isCSA)
                            <!-- Domestic CSA content -->
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
                        
                        @else
                            <!-- Domestic Non-CSA content -->
                            <li>You are joining as a <strong>{{ $emp->designations_name }}</strong> on <strong>{{ date('d/m/Y', strtotime($emp->joining_date)) }}</strong> </li>
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
                        @endif
                        <li>I hereby acknowledge that I am medically fit for the role. Should I have any medical conditions that could impact my ability to perform the required duties, I will promptly provide the necessary medical documentation for review at the time of my joining.</li>
                    @endif
                </ol>
            </div>

            <!-- Signature Section -->
            <div class="signature-section">
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <p>Employee Signature</p>
                </div>
                <div class="signature-box">
                    <div class="signature-line"></div>
                    <p>Authorized Signatory</p>
                </div>
            </div>

            <!-- Footer -->
            <!--<div class="footer">-->
            <!--    <p>3rd Floor, Surajdeep Complex, Jopling Road, Hazratganj, Lucknow-226001</p>-->
            <!--    <p>5/113, Opp. PNB Lane, Vikas Nagar, Lucknow-226022</p>-->
            <!--</div>-->
        </div>
    </div>

    <button class="print-btn" onclick="window.print()">Print Letter</button>

    <script>
        // Adjust content to fit on one page
        function adjustContent() {
            const container = document.querySelector('.ack-letter-container');
            const list = document.querySelector('.terms-list');
            
            // Check if content is overflowing
            if (container.scrollHeight > container.offsetHeight) {
                // Reduce font size slightly
                const currentSize = parseFloat(window.getComputedStyle(list).fontSize);
                list.style.fontSize = (currentSize - 0.5) + 'px';
                
                // Check again after adjustment
                setTimeout(adjustContent, 100);
            }
        }

        // Run on load and before print
        window.addEventListener('load', adjustContent);
        window.addEventListener('beforeprint', adjustContent);
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<script>
    window.onload = function() {
      const element = document.body; // 👈 directly use whole body

      const opt = {
        margin: 0.5,
        filename: "Acknowledgement_Letter.pdf",
        image: { type: "jpeg", quality: 0.98 },
        html2canvas: { 
          scale: 2,
          backgroundColor: "#ffffff" // makes rest page white
        },
        jsPDF: { unit: "in", format: "a3", orientation: "portrait" },
        pagebreak: { mode: ['avoid-all', 'css', 'legacy'] }
      };
        element.style.backgroundColor = "#ffffff";
      html2pdf().set(opt).from(element).save();
    }
  </script>

</body>
</html>