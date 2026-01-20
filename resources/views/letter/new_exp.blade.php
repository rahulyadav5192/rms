<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Niftel | Experience Letter</title>
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
            text-align: center;
        }
        .intro {
            margin-bottom: 14px;
            text-align: justify;
            font-size: 13px;
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
        <img src="{{url('public/Letter Head Niftel (2)_page-0001.jpg')}}" alt="Experience Letter Template" class="background-image">
        <div class="content-wrapper">
            <!-- Header Section -->
            <div class="header">
                <div class="date"><u>Date: {{ \Carbon\Carbon::now()->format('d-m-Y') }}</u></div>
                <div class="emp-code"><u>EMP CODE: {{ $employee->employee_id }}</u></div>
            </div>
            <h1 class="title"><u>EXPERIENCE LETTER</u></h1>
            <!-- Main Content -->
            <div class="main-content">
                <div class="salutation">TO WHOMSOEVER IT MAY CONCERN</div>
                @php
                    $pronoun = 'his/her';
                    $him = 'him/her';
                    $gen = "He/She";
                    if ($employee->gender == 'male') {
                        $pronoun = 'his';
                        $gen = "He";
                        $him = 'him';
                    } elseif ($employee->gender == 'female') {
                        $pronoun = 'her';
                        $gen = "She";
                        $him = 'her';
                    }
                @endphp
                
                <div class="intro">
                    This is to certify that <b>{{ ucwords($employee->name) }}</b> worked with Niftel Communications Pvt. Ltd., as a <b>{{ $employee->designations_name }}</b> at our Corporate office located at 
                    <b>@if($employee->b_address != '') 
                        {{ $employee->b_address }} 
                    @else  
                        A-Block, 3rd Floor, Surajdeep Complex, Jopling Road, Lucknow, UP – 226001 
                    @endif </b>
                    starting from <b>{{ \Carbon\Carbon::parse($employee->joining_date)->format('l, jS F Y') }}</b> to <b>{{ \Carbon\Carbon::parse($employee->last_date)->format('l, jS F Y') }}</b>. 
                    <br><br>
                
                    During {{ $pronoun }} tenure with Niftel Communications Pvt. Ltd., we found <b>{{ ucwords($employee->name) }}</b> honest, hardworking & responsible. 
                    <br><br>
                    {{ ucfirst($gen) }} has done an exemplary job working with us and has always maintained professional relations with the team and colleagues.
                    
                    <!--{{ ucfirst($pronoun) }} contribution was exemplary and {{ $pronoun }} behavior towards colleagues and team was always professional. -->
                    <br><br>
                
                    We wish {{ $him }} all the best in {{ $pronoun }} future endeavors.
                </div>

            </div>
            <!-- Signature Section -->
            <div class="signature">
                <p>Sincerely,</p>
                <div class="signature-line"></div>
                <!--@if($employee->branch_id == 7)-->
                    <div class="signatu"><img src="{{url('public/sakshi-sign.png')}}" style="width: 97px;"/></div>
                    <div class="signature-details">
                        <strong><p>Sakshi Singh</p></strong>
                        <strong><p>Human Resources</p></strong>
                        <strong><p>Niftel Communications Pvt. Ltd.</p></strong>
                    </div>
                <!--    @else-->
                <!--    <div class="signatu"><img src="{{url('public/Tabassum Sign-cropped.jpg')}}" style="width: 97px;"/></div>-->
                <!--    <div class="signature-details">-->
                <!--        <strong><p>Tabassum Rasheed</p></strong>-->
                <!--        <strong><p>Human Resources</p></strong>-->
                <!--        <strong><p>Niftel Communications Pvt. Ltd.</p></strong>-->
                <!--    </div>-->
                <!--@endif-->
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