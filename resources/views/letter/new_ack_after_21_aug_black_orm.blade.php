<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Niftel | Acknowledgement Letter</title>
<style> /* Import Google Fonts */ @import url('https://fonts.googleapis.com/css2?family=Times+New+Roman:wght@400;700&display=swap'); /* Reset and base styles *//* Reset and base styles */
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
        .offer-letter-container {
            position: relative;
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
            background: white;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            height: 1122px; /* Set to your image's height in px (A4 at 96dpi) */
        }

        /* Background image */
        .background-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* Content wrapper */
        .content-wrapper {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            padding: 100px 123px;
            box-sizing: border-box;
            overflow: hidden;
            
        }

        /* Header section */
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 18px;
        }

        .date, .emp-code {
            font-size: 13px;
            font-weight: bold;
        }

        .title {
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
            margin: 10px 0 18px;
        }

        /* Main content */
        .main-content {
            margin-bottom: 16px;
        }

        .salutation {
            margin-bottom: 10px;
            font-size: 13px;
        }

        .intro {
            margin-bottom: 14px;
            text-align: justify;
            font-size: 13px;
        }

        /* Terms and conditions */
        .terms-list {
            list-style-position: inside;
            margin-bottom: 14px;
            font-size: 13px;
        }

        .terms-list li {
            margin-bottom: 5px;
            text-align: justify;
            padding-left: 8px;
        }

        .terms-list li:first-child + li {
            padding-left: 8px;
        }

        /* Closing section */
        .closing {
            margin: 12px 0;
            text-align: justify;
            font-size: 13px;
        }

        /* Signature section */
        .signature {
            margin-top: 18px;
            font-size: 13px;
        }

        .signature-line {
            border-top: 1px solid #000;
            width: 120px;
            margin: 24px 0 10px;
        }

        .signature-details {
            margin-top: 4px;
        }

        /* Declaration section */
        .declaration {
            margin-top: 28px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }

        .declaration h2 {
            text-align: center;
            font-size: 15px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .declaration-text {
            margin-bottom: 14px;
            text-align: justify;
            font-size: 13px;
        }

        .signature-form {
            margin-top: 10px;
        }

        .signature-form .row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            /*font-weight: bold;*/
            font-size: 13px;
        }

        .signature-form .row span {
            flex: 1 1 0;
        }

        /* Print styles */
        @media print {
            body {
                padding: 0;
                margin: 0;
                background: white;
            }
        
            .offer-letter-container {
                width: 794px;
                height: 1123px;
                box-shadow: none;
                margin: 0;
            }
        
            .background-image {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }
        }
        @page {
            size: A4;
            margin: 0;
        }
        .term li {
            margin-bottom: 5px; 
        }
        </style>
</head>
<body>

<!-- PAGE 1 -->
<div class="offer-letter-container">
    <img src="{{url('public/Untitled design (33).png')}}" alt="Background" class="page-background">
    <div class="content-wrapper" style="font-size: x-small;">
        <h1 style="text-align:center;margin-bottom: 30px;margin-top: 10px;"><u>ACKNOWLEDGEMENT LETTER ( CSA )</u> </h1>
        I {{ $emp->name }} son/daughter of @if($emp->father_name)
                        {{ $emp->father_name }}
                    @elseif($emp->mother_name)
                        {{ $emp->mother_name }}
                    @elseif($emp->guardian_name)
                        {{ $emp->guardian_name }}
                    @else
                        N/A
                    @endif hereby acknowledge and agree to the terms and
        conditions mentioned below as part of my employment with Niftel Communication Pvt. Ltd., for the position of
        Customer Service Associate in the voice/chat support process, effective from {{ date('d/m/Y', strtotime($emp->joining_date)) }}.
        <br>
        <div style="margin-top: 15px;margin-bottom: 5px;"><b>Terms and Conditions Acknowledged:</b></div>
        <div class="term">
    <li>I understand that my appointment is in the above role and department as of <strong>{{ \Carbon\Carbon::parse($emp->joining_date)->format('d-m-Y') }}</strong>.</li>
    <li>I acknowledge that I will be employed and on the payroll of Niftel Communication Pvt. Ltd..</li>
    <li>I am aware that no joining bonus or travel allowances (pick-and-drop) will be provided by the company.</li>
    <li>I understand that certification post-training and OJT (On-the-Job Training) is mandatory for continued employment. In case of failure:<br>
        a) During training: I will receive 100% salary pro-rated for payable days.<br>
        b) During OJT: I will receive 100% salary pro-rated for payable days.
    </li>
    <li>If permitted to work from home, I shall use my own laptop and a stable broadband connection. Mobile data is not allowed. WFH is performance-based and subject to process requirements.</li>
    <li>I agree that consistent failure to log required hours or taking frequent unplanned leave may result in disciplinary proceedings or performance warnings.</li>
    <li>I accept the 24x7 rotational shift policy, which includes night shifts, six-day work weeks, and one rotational weekly off.</li>
    <li>I acknowledge the company’s Zero Tolerance Policy, which prohibits, among other things:<br>
        a) Abuse or offensive language<br>
        b) Fraudulent practices<br>
        c) Mishandling of data<br>
        d) Unauthorized data transfers (USB, personal email)<br>
        e) Assault or harassment<br>
        Violations may lead to termination, legal action, and recovery of dues as per company policy.
    </li>
    <li>If terminated for serious misconduct under the Zero Tolerance Policy, I agree to a recovery amount up to one month’s salary or cost of damage caused, whichever is higher, as permitted under applicable laws and after due process.</li>
    <li>I agree to serve a 60-day notice period upon resignation. Failing to serve notice may result in being marked absconding and liable for recovery up to one month’s salary or damages, in accordance with company policy. Final settlements (FNF) are processed within 45 days of last working day.</li>
    <li>Absence without communication for 3 consecutive working days shall be considered job abandonment. I understand this may lead to termination after inquiry and potential forfeiture of dues as per law.</li>
    <li>I acknowledge that leave during peak hours/festivals must be pre-approved with valid documentation. Unapproved leave may result in deduction or denial of pay.</li>
    <li>I agree to maintain a professional dress code in line with company policies.</li>
    <li>I understand the sandwich leave policy: continuous leaves on either side of a weekend or holiday may be counted as a single extended leave.</li>
    <li>I agree to maintain washroom and workplace etiquette, and understand that repeated violations may invite disciplinary warnings.</li>
    <li>I understand that if I am absent for 3 or more days between 1st and 15th of the month, my salary may be temporarily held (50%–100%) pending review and approval by my manager.</li>
    <li>I confirm that I have read and understood the company’s POSH (Prevention of Sexual Harassment) Policy and agree to comply. I am aware that violations will be dealt with by the Internal Complaints Committee (ICC) as per the POSH Act, 2013.</li>
    <li>I hereby acknowledge that I am medically fit for the role. Should I have any medical conditions that could impact my ability to perform the required duties, I will promptly provide the necessary medical documentation for review at the time of my joining.</li>
</div>


        <hr>
        <div class="footer-section">
            I confirm that I have read, understood, and voluntarily accept all the above points and policies of Niftel Communication Pvt. Ltd. I understand that the company reserves the right to revise or amend any of the above terms in accordance with applicable laws and policy updates.
        </div>

        <div class="signature-form" style="margin-top:20px;">
            <div class="row">
                <span>Process: __________________</span>
                <span>Batch No: __________________</span>
                <span>Signature of the Employee: __________________</span>
            </div>
            <!--<div class="row">-->
            <!--</div>-->
        </div>
    </div>
</div>

<!-- PAGE 2 -->
<!--<div class="offer-letter-container">-->
<!--    <img src="{{url('public/Untitled design (33).png')}}" alt="Background" class="page-background">-->
<!--    <div class="content-wrapper">-->
<!--       <li>I acknowledge the company's Zero Tolerance Policy which includes, but is not limited to, rude/profane behavior towards customers or colleagues, fraudulent reporting, data fraud, unauthorized data transfer, physical assault, or any act constituting an offense involving moral turpitude during employment. Breach of this policy may lead to termination, legal action, and forfeiture of dues, following a proper inquiry.</li>-->
<!--                        <li>If terminated under the Zero Tolerance policy, I will be liable to pay a recovery amount equal to one month’s salary or the cost of damages caused, whichever is higher.</li>-->
<!--                        <li>I agree to serve a 30-day notice period upon separation. Failing to do so will result in being marked as absconded and liable for recovery of damages or one month’s salary, whichever is higher. Final settlement (FNF) will be processed 45 days after the last working day, provided the notice period is duly served. Immediate resignations are not accepted.</li>-->
<!--                        <li>Uninformed absence for three consecutive days without written approval via email will be considered absconding and may result in termination of our employment. In this case the company reserves the right to forfeit all our dues as per policy.</li>-->
<!--                        <li>I acknowledge that leaves during festivals or business-critical periods are not permitted unless approved. All leave must be pre-approved by my supervisor with valid documentation.</li>-->
<!--                        <li>I agree to follow a professional dress code in accordance with the company's standards.</li>-->
<!--                        <li>I understand that the sandwich leave policy will be enforced, and continuous leaves around weekends or holidays will result in all days being marked as leave.</li>-->
<!--                        <li>I agree to follow proper washroom etiquette, and understand that violations may result in a fine.</li>-->
<!--                        <li>If I remain absent for three (3) or more working days between the 1st and 15th of any month without sufficient justification, salary may be held (50–100%), subject to managerial discretion and internal policy.</li>-->
<!--                        <li>I acknowledge and agree to comply with the company’s POSH Policy (Prevention of Sexual Harassment at Workplace). Any violation will be referred to the Internal Complaints Committee (ICC) and may lead to termination, prosecution, or other appropriate legal consequences.</li>-->
<!--                        <li>I acknowledge that during and after my employment with Niftel, I am strictly prohibited from using or disclosing any confidential information or trade secrets of the company—including but not limited to intellectual property, data, software, processes, technology, and designs—without prior written authorization, except when required to perform my official duties. I understand that any violation of this clause may lead to legal action.</li>-->
                    
<!--    </div>-->
<!--</div>-->

<button type="button" class="btn btn-light" style="margin-left: 700px;width: 46px;border: none;color: black;" onclick="printPage()">Print</button>

<!--<script>-->
<!--function printPage() { window.print(); }-->
<!--document.addEventListener('DOMContentLoaded', function () { window.print(); });-->
<!--</script>-->
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
