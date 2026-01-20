<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <title>Niftel Offer Letter</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Times+New+Roman&display=swap" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

    <style>
      body {
        font-family: 'Times New Roman', Times, serif;
        background: url('target001-overlay (1).png') no-repeat center top;
        background-size: cover;
        margin: 0;
        padding: 0;
      }

      .content {
        position: relative;
        z-index: 10;
        padding: 120px 60px 180px 60px; /* Adjust top and bottom to avoid header/footer overlap */
        max-width: 900px;
        margin: auto;
      }

      @media print {
        body {
          background-size: 100% auto;
        }
        .no-print {
          display: none;
        }
      }
    </style>
  </head>
  <body id="body">
    <div class="content text-[17px] leading-relaxed">
      <!-- Contact & Meta -->
      <div class="flex justify-between items-start text-sm mb-2">
        <div class="mt-[-10px] ml-2">
          <span class="font-sans text-orange-400 text-xs tracking-wide">Performance Speaks</span>
        </div>
        <div class="flex flex-col items-end gap-y-1">
          <div class="flex items-center gap-x-2">
            <i class="fas fa-phone-alt text-orange-400"></i>
            <span class="text-gray-900 font-semibold">+91 7985788784</span>
          </div>
          <div class="flex items-center gap-x-2">
            <i class="fas fa-envelope text-orange-400"></i>
            <span class="text-gray-900 font-semibold">info@niftel.com</span>
          </div>
          <div class="flex items-center gap-x-2">
            <i class="fas fa-globe text-orange-400"></i>
            <span class="text-gray-900 font-semibold">www.niftel.com</span>
          </div>
        </div>
      </div>

      <!-- Date & Emp Code -->
      <div class="flex justify-between mt-4 mb-2">
        <span class="font-semibold">DATE: <span class="font-normal">09-04-2025</span></span>
        <span class="font-semibold">EMP CODE: <span class="font-normal">NIF0425272</span></span>
      </div>

      <!-- Title -->
      <h1 class="text-center font-extrabold text-3xl underline tracking-wide">OFFER LETTER</h1>

      <!-- Body -->
      <div>
        <p class="mt-4">Dear <span class="font-semibold">Arpan Bera</span>,</p>
        <p>
          We are pleased to offer you the position of <span class="font-bold">Customer Service Associate at Niftel Communications Pvt. Ltd.</span> The terms and conditions of the offer are mentioned below:
        </p>
        <ol class="list-decimal ml-6 mt-2 marker:text-black">
          <li class="mb-2">Your date of joining would be <b>08-04-2025</b> at our office based at <br /><b>14th Floor, Saltee Tech Park, DN-18, Sector-V, Salt Lake City, Plot No.18, P.S. - Electronic Complex, A.D.S.R, Bidhannagar, Kolkata-700091</b></li>
          <li class="mb-2">The monthly salary for this position is <b>INR 15,500</b> and is to be paid on a <b>monthly basis</b> in your Bank account.</li>
          <li class="mb-2">Your employment will be on an at-will basis, and the company can terminate the relationship at any time for any reason.</li>
          <li class="mb-2">You will be on a Probation Period for <b>Six months</b>. Confirmation depends on assessment.</li>
          <li class="mb-2"><b>You are required to serve a Notice Period of at least Thirty (30) working days before withdrawing your employment. Failing which, you must pay an amount equal to one month's salary.</b></li>
          <li class="mb-2">If any information provided is found false, services can be terminated without notice.</li>
          <li class="mb-2">Please review the <b>Employee Handbook</b> for full clarity.</li>
          <li class="mb-2">Terms and conditions are subject to periodic revision without prior notice.</li>
          <li class="mb-2">Your compensation and benefits are confidential and attached separately.</li>
        </ol>
        <p class="mt-4 mb-8">Please return a signed copy of this letter indicating your acceptance. We are excited to have you join us. Reach out for any queries.</p>
      </div>

      <!-- Signature -->
      <div class="mt-10">
        <!-- <img src="https://images.pexels.com/photos/459225/pexels-photo-459225.jpeg?auto=compress&w=100&h=40&fit=clip" alt="stamp" class="w-28 h-20 object-cover mb-2 opacity-85" draggable="false" /> -->
        <p class="font-bold">Sakshi Singh<br /><span class="font-semibold">Human Resources<br />Niftel Communications Pvt. Ltd.</span></p>
      </div>

      <!-- Declaration -->
      <div class="mt-10">
        <h2 class="text-center font-bold text-lg underline mb-2">DECLARATION</h2>
        <p class="mb-4">I willingly accept the offer, agreeing to the terms and conditions of employment specified in this document. By affixing my signature below, I commit to abide by these terms.</p>
        <div class="flex justify-between font-semibold text-base">
          <span>Date-</span>
          <span>Place-</span>
          <span>Candidate's Signature-</span>
        </div>
        <div class="flex justify-start mt-2 font-semibold text-base">
          <span>Candidate's Name-</span>
        </div>
      </div>
    </div>
    <button type="button" class="btn btn-light" style="margin-left: 700px;" onclick="printPage()">Print</button>
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