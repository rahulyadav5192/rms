<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Niftel | Offer Letter</title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

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
            padding: 100px 91px;
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
            font-weight: bold;
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

    </style>
</head>
<body id="body">
    <div class="offer-letter-container">
        <img src="{{url('public/Untitled design (33).png')}}" alt="Offer Letter Template" class="background-image">
        
        <div class="content-wrapper">
            <!-- Header Section -->
            <div class="header">
                <div class="date">DATE: {{ \Carbon\Carbon::parse($emp->created_at)->format('d-m-Y')}}</div>
                <div class="emp-code">EMP CODE: {{$emp->employee_id}}</div>
            </div>

            <h1 class="title">OFFER LETTER</h1>

            <!-- Main Content -->
            <div class="main-content">
                <div class="salutation">Dear <span  style="font-weight: bold;">{{ ucwords($emp->name) }}</span>,</div>
                <div class="salutation" style="font-weight: bold;"></div>

                <div class="intro">
                    We are pleased to offer you the position of <strong>{{$emp->designations_name}}</strong> at <strong>Niftel Communications Pvt. Ltd.</strong> The terms and conditions of the offer are mentioned below:
                </div>

                <ol class="terms-list">
                    <li>Your date of joining would be <strong>{{ \Carbon\Carbon::parse($emp->joining_date)->format('d-m-Y')}}</strong> at our office based at:
                        <strong>@if($emp->b_address != '') {{$emp->b_address}} @else  A-Block, 3rd Floor, Surajdeep Complex, Jopling Road, Lucknow, UP – 226001
 @endif</strong>
                    </li>
                    <li>The monthly salary for this position is <strong>INR {{$emp->offer_salary_month}}</strong> and is to be paid on a <strong>monthly</strong> basis in your Bank account.</li>
                    <li>Your employment with Niftel Communications Pvt. Ltd. will be on an at-will basis, which means the company is free to terminate the employment relationship at any time for any reason.</li>
                    <li>You will be on a Probation Period for <strong>Six months.</strong>  Based on the three-monthly assessments, you will be confirmed or extended if deemed necessary, at the company's discretion.</li>
                    <li><strong>You are required to serve a Notice Period of at least @if(in_array($emp->department_id, [42,43])) Sixty&#160;(60) @else Thirty &#160;(30) @endif working days before withdrawing your employment. In case of leaving the organization without serving the Notice Period, you will be liable to pay the amount equivalent to the current salary of @if(in_array($emp->department_id, [42,43])) 2 months @else 1 month @endif to the organization.</strong></li>
                    <li>If the company finds any information provided by you false or incorrect, the company shall have all the rights to terminate your services at its sole discretion without giving further notice to you.</li>
                    <li>For detailed information and clarity, we encourage you to thoroughly review the <strong>Employee Handbook.</strong></li>
                    <li>All terms and conditions are subject to periodic revision without prior notice at the discretion of the company.</li>
                    <li>The compensation and benefits program applicable to your grade is enclosed. Please note that your compensation is a confidential matter between you and the company, and the company shall view any breach of confidentiality with utmost seriousness.</li>
                </ol>

                <div class="closing">
                    Return a copy of this letter duly signed indicating your acceptance of our terms and conditions of employment. We are excited to have you join our team! If you have any questions, please feel free to reach out at any time.
                </div>
            </div>

            <!-- Signature Section -->
            <div class="signature">
                <p>Sincerely,</p>
                <!--@if($emp->branch_id == 7)-->
                <!--<div class="signatu"><img src="{{url('public/sakshi-sign.png')}}" style="width: 97px;"/></div>-->
                <!--<div class="signature-details"> -->
                <!--    <strong><p>Sakshi Singh</p></strong>-->
                <!--    <strong><p>Human Resources</p></strong>-->
                <!--    <strong><p>Niftel Communications Pvt. Ltd.</p></strong>-->
                <!--</div>-->
                <!--@else-->
                <div class="signatu"><img src="{{url('public/sakshi-sign.png')}}" style="width: 97px;"/></div>
                <div class="signature-details">
                    <strong><p>Sakshi Singh</p></strong>
                    <strong><p>Human Resources</p></strong>
                    <strong><p>Niftel Communications Pvt. Ltd.</p></strong>
                </div>
                <!--@endif-->
            </div>

            <!-- Declaration Section -->
            <div class="declaration">
                <h2>DECLARATION</h2>
                <div class="declaration-text">
                    I willingly accept the offer, agreeing to the terms and conditions of employment specified in this document. By affixing my signature below, I commit to abide by these terms.
                </div>

                <div class="signature-form">
                    <div class="row">
                        <span>Date: ___________</span>
                        <span>Place: ___________</span>
                    </div>
                    <div class="row">
                        <span>Candidate's Name: ____________________</span>
                        <span>Candidate's Signature: ________________</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-light" style="margin-left: 700px;width: 46px;
    border: none;
    color: black;" onclick="printPage()">Print</button>
<script>
        function printPage() {
            window.print();
        }
    </script>
<script>
window.onload = function() {
  const element = document.getElementById("body");
  const opt = {
    margin: 0.5,
    filename: "Offer_Letter.pdf",
    image: { type: "jpeg", quality: 0.98 },
    html2canvas: { scale: 2 },
    jsPDF: { unit: "in", format: "a3", orientation: "portrait" } // bigger than A4
  };
  element.style.backgroundColor = "#ffffff";
  html2pdf().set(opt).from(element).save();
}
</script>

</body>
</html>