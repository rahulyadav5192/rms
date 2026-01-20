<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Niftel | Letter of Intent</title>
    <style>
        /* Import Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Times+New+Roman:wght@400;700&display=swap');

        /* Reset and base styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

           @page {
            margin: 50px 30px;
          }
          body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            background-image: url("{{ public_path('images/letterhead.png') }}");
            background-repeat: no-repeat;
            background-position: top center;
            background-size: cover;
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
                /*padding: 135px;*/
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

        /* Document list */
        .documents-list {
            list-style-position: inside;
            margin-bottom: 14px;
            font-size: 13px;
        }

        .documents-list li {
            margin-bottom: 5px;
            text-align: justify;
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

        /* Acceptance section */
        .acceptance {
            margin-top: 28px;
            border-top: 1px solid #000;
            padding-top: 10px;
        }

        .acceptance h2 {
            text-align: center;
            font-size: 15px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .acceptance-text {
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
            margin: 20px;
        }

    </style>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

</head>
<body>
    <div class="offer-letter-container" id="offer-letter-container">
        <img src="{{url('public/Untitled design (33).png')}}" alt="Letter of Intent Template" class="background-image">
        
        <div class="content-wrapper">
            <!-- Header Section -->
            <div class="header">
                <div class="date"><u>DATE: {{ \Carbon\Carbon::parse($emp->created_at)->format('d-m-Y')}}</u></div>
                <div class="emp-code"><u>EMP CODE: {{$emp->employee_id}}</u></div>
            </div>

            <h1 class="title"><u>LETTER OF INTENT</u></h1>

            <!-- Main Content -->
            <div class="main-content">
                <div class="salutation">Dear <span  style="font-weight: bold;">{{ ucwords($emp->name) }}</span>,</div>

                <div class="intro">
                    Following your interview, we are delighted to inform you of your selection for the position of <strong>{{$emp->designations_name}}</strong> at <strong>Niftel Communications Pvt. Ltd.</strong>
                </div>
                <div class="intro">
                    Your joining date has been scheduled for <strong>{{ \Carbon\Carbon::parse($emp->joining_date)->format('l, jS F Y')}}</strong>.
                </div>



                <div class="intro">
                    Kindly upload the following documents as proof of identity on the RMS portal (<a href="https://rms.niftel.com/login" style="color: blue; text-decoration: underline;">https://rms.niftel.com/login</a>):
                </div>

                <ul class="documents-list">
                    <li>Educational Qualification Marksheets (High school, Intermediate, Under-Graduation, Post-Graduation)</li>
                    <li>2 Passport Size Photographs</li>
                    <li>Experience Letter/Relieving Letter from your last employer</li>
                    <li>Last 3 Months Pay Slip</li>
                    <li>Identity Proof (Aadhar Card, PAN Card)</li>
                    <li>Address Proof</li>
                </ul>

                <div class="intro">
                    You are requested to report at our corporate office located at:
                    <strong>
                        @if($emp->b_address != '') 
                            {{$emp->b_address}} 
                        @else 
                            A-Block, 3rd Floor, Surajdeep Complex, Jopling Road, Lucknow, UP – 226001
                        @endif
                    </strong>
                </div>

                <!--<div class="closing">-->
                <!--    This letter is an expression of our intent to employ you and does not constitute a formal contract of employment. -->
                <!--    The formal offer letter will be issued upon successful verification of your documents.-->
                <!--</div>-->
            </div>

            <!-- Signature Section -->
            <div class="signature">
                <p>Sincerely,</p>
                <!--@if($emp->branch_id == 7)-->
                <div class="signatu"><img src="{{url('public/sakshi-sign.png')}}" style="width: 97px;"/></div>
                <div class="signature-details">
                    <strong><p>Sakshi Singh</p></strong>
                    <strong><p>Human Resources</p></strong>
                    <strong><p>Niftel Communications Pvt. Ltd.</p></strong>
                </div>
                <!--@else-->
                <!--<div class="signatu"><img src="{{url('public/Tabassum Sign-cropped.jpg')}}" style="width: 97px;"/></div>-->
                <!--<div class="signature-details">-->
                <!--    <strong><p>Tabassum Rasheed</p></strong>-->
                <!--    <strong><p>Human Resources</p></strong>-->
                <!--    <strong><p>Niftel Communications Pvt. Ltd.</p></strong>-->
                <!--</div>-->
                <!--@endif-->
            </div>

            <!-- Acceptance Section -->
            <!--<div class="acceptance">-->
            <!--    <h2>ACCEPTANCE</h2>-->
            <!--    <div class="acceptance-text">-->
            <!--        I acknowledge receipt of this Letter of Intent and accept the proposed terms of employment. I understand that this is not a formal contract of employment but indicates the company's intent to employ me subject to verification of my documents.-->
            <!--    </div>-->

            <!--    <div class="signature-form">-->
            <!--        <div class="row">-->
            <!--            <span>Date: ___________</span>-->
            <!--            <span>Place: ___________</span>-->
            <!--        </div>-->
            <!--        <div class="row">-->
            <!--            <span>Candidate's Name: ____________________</span>-->
            <!--            <span>Candidate's Signature: ________________</span>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
        </div>
    </div>
    <button type="button" class="btn btn-light" style="margin-left: 700px;width: 46px; border: none; color: black;" onclick="printPage()">Print</button>
    <script>
        function printPage() {
            window.print();
        }
    </script>
 <script>
    window.onload = function() {
      const element = document.getElementById("offer-letter-container");
      const opt = {
        margin: 0.5,
        filename: "Offer_Letter.pdf",
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'in', format: 'letter', orientation: 'portrait' }
      };
      html2pdf().set(opt).from(element).save();
    }
  </script>
</body>
</html>