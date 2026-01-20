<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" lang="" xml:lang="">
<head>
<title>Offer Letter</title>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>

<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
 <br/>
<style type="text/css">
<!--
	p {margin: 0; padding: 0;}	.ft10{font-size:14px;font-family:Times;color:#000000;}
	.ft11{font-size:12px;font-family:Times;color:#000000;}
	.ft12{font-size:28px;font-family:Times;color:#000000;}
	.ft13{font-size:28px;font-family:Times;color:#000000;}
	.ft14{font-size:25px;font-family:Times;color:#000000;}
	.ft15{font-size:14px;font-family:Times;color:#000000;}
	.ft16{font-size:12px;font-family:Times;color:#000000;}
	.ft17{font-size:12px;font-family:Times;color:#1f1f1f;}
	.ft18{font-size:16px;font-family:Times;color:#000000;}
	.ft19{font-size:12px;line-height:17px;font-family:Times;color:#000000;}
	.ft110{font-size:12px;line-height:19px;font-family:Times;color:#000000;}
	.ft111{font-size:12px;line-height:17px;font-family:Times;color:#000000;}
	.ft112{font-size:12px;line-height:24px;font-family:Times;color:#000000;}
	.ft113{font-size:16px;line-height:21px;font-family:Times;color:#000000;}
	.ft114{font-size:16px;line-height:29px;font-family:Times;color:#000000;}
-->
</style>
</head>
<body bgcolor="#A0A0A0" vlink="blue" link="blue"id="body">
<div id="page1-div" style="position:relative;width:893px;height:1263px;">
<img width="893" height="1263" src="{{url('public/target001.png')}}" alt="background image"/>
<p style="position:absolute;top:144px;left:86px;white-space:nowrap" class="ft10"><b>&#160;&#160;Date:&#160;</b></p>
<p style="position:absolute;top:145px;left:135px;white-space:nowrap" class="ft11"><b>{{ \Carbon\Carbon::parse($emp->created_at)->format('d-m-Y')}}</b></p>
<p style="position:absolute;top:135px;left:202px;white-space:nowrap" class="ft12">&#160;</p>
<p style="position:absolute;top:163px;left:86px;white-space:nowrap" class="ft13"><b>&#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160; &#160;</b></p>
<p style="position:absolute;top:165px;left:338px;white-space:nowrap" class="ft14"><b>OFFER&#160;LETTER&#160;</b></p>
<p style="position:absolute;top:132px;left:612px;white-space:nowrap" class="ft15">&#160; &#160; &#160;&#160;</p>
<p style="position:absolute;top:137px;left:633px;white-space:nowrap" class="ft11"><b>EMP&#160;CODE:&#160;{{$emp->employee_id}}&#160;</b></p>
<p style="position:absolute;top:213px;left:86px;white-space:nowrap" class="ft110"><b>&#160;<br/>&#160;<br/>&#160;</b></p>
<p style="position:absolute;top:264px;left:108px;white-space:nowrap" class="ft16">Dear&#160;<b>{{ ucwords($emp->name) }}</b></p>
<p style="position:absolute;top:264px;left:271px;white-space:nowrap" class="ft17"><b>,</b></p>
<p style="position:absolute;top:264px;left:274px;white-space:nowrap" class="ft11"><b>&#160;</b></p>
<p style="position:absolute;top:286px;left:86px;white-space:nowrap" class="ft11"><b>&#160;</b></p>
<p style="position:absolute;top:302px;left:121px;white-space:nowrap" class="ft16">We&#160;are&#160;pleased&#160;to&#160;offer&#160;you&#160;the&#160;@if($emp->employment_type == 'part_time') Part-time @else Full-time @endif&#160;position&#160;of&#160;<b>{{$emp->designations_name}}&#160;</b>at&#160;<b>Niftel&#160;Communications&#160;Pvt.&#160;Ltd</b>.&#160;The&#160;terms&#160;</p>
<p style="position:absolute;top:318px;left:121px;white-space:nowrap" class="ft16">and&#160;conditions&#160;of&#160;the&#160;offer&#160;are&#160;mentioned&#160;below</p>
<p style="position:absolute;top:316px;left:429px;white-space:nowrap" class="ft15">:&#160;</p>
<p style="position:absolute;top:341px;left:86px;white-space:nowrap" class="ft16">&#160;</p>
<p style="position:absolute;top:361px;left:115px;white-space:nowrap" class="ft11"><b>1.&#160;&#160;</b>Your&#160;&#160;date&#160;&#160;of&#160;&#160;joining&#160;&#160;would&#160;&#160;be&#160;&#160;<b>{{ \Carbon\Carbon::parse($emp->joining_date)->format('l, jS F Y') }}&#160;&#160;</b>at&#160;&#160;our&#160;&#160;office&#160;&#160;based&#160;&#160;at&#160;&#160;<b><br>&#160;&#160;&#160;&#160;&#160;&#160;@if($emp->b_address != '') {{$emp->b_address}} @else  A-Block, 3rd Floor, Surajdeep Complex, Jopling Road, Lucknow, UP – 226001
 @endif</b>
<p style="position:absolute;top:395px;left:115px;white-space:nowrap" class="ft11"><b>2.&#160;&#160;</b>The&#160;monthly&#160;&#160;salary&#160;&#160;for&#160;this&#160;&#160;position&#160;is&#160;&#160;<b>INR&#160;{{$emp->offer_salary_month}}&#160;&#160;</b>and&#160;is&#160;&#160;to&#160;be&#160;paid&#160;&#160;monthly&#160;&#160;in&#160;your&#160;Bank&#160;account.&#160;&#160;</p>

@php
    $baseTop = 412; // Starting position for the first condition
    $lineHeight = 20; // Space between lines
    $currentPoint = 3; // Start numbering from point 3
@endphp
    @php
        // Employee IDs derived from NIF
        $bypassEmployeeIds = [
                'NIF0824131',
                'NIF0624262',
                'NIF0724100',
                'NIF1124293',
                'NIF1124295',
                'NIF1124296',
                'NIF1124297',
                'NIF1124298',
                'NIF1124299',
                'NIF1124300',
                'NIF1124302',
                'NIF1124303',
                'NIF1124304',
                'NIF1124306',
                'NIF1124312',
                'NIF1124313',
                'NIF1124314',
                'NIF1124315',
                'NIF1124300'
            ];
    @endphp
    
    @if (in_array($emp->employee_id, $bypassEmployeeIds) && $emp->gender !== 'female')
    <!-- If employee ID is in the bypass list, show the div -->
        <p style="position:absolute;top:{{ $baseTop }}px;left:115px;white-space:nowrap" class="ft111">
            <b>{{ $currentPoint }}.&#160;&#160;</b>An&#160;additional&#160;night&#160;shift&#160;allowance&#160;of&#160;INR&#160;2,500.00&#160;will&#160;be&#160;provided,&#160;applicable&#160;only&#160;for&#160;the&#160;working&#160;days&#160;when&#160;<br>&#160;&#160;&#160;&#160;&#160;&#160;&#160;the&#160;employee's&#160;shift&#160;ends&#160;after&#160;11:59&#160;PM.&#160;Shift&#160;timings&#160;will&#160;be&#160;aligned&#160;according&#160;to&#160;process&#160;requirements.
        </p>
        @php
            $baseTop += 35; // Adjust top position for the next line
            $currentPoint++; // Increment the point number
        @endphp
        
    @elseif (
        $emp->gender !== 'female' &&
        in_array($emp->department_id, [28, 31]) && 
        in_array($emp->designation_id, [12,0]) && 
        \Carbon\Carbon::parse($emp->joining_date)->gt(\Carbon\Carbon::create(2024, 10, 27))
    )
        <p style="position:absolute;top:{{ $baseTop }}px;left:115px;white-space:nowrap" class="ft111">
            <b>{{ $currentPoint }}.&#160;&#160;</b>An&#160;additional&#160;night&#160;shift&#160;allowance&#160;of&#160;INR&#160;2,500.00&#160;will&#160;be&#160;provided,&#160;applicable&#160;only&#160;for&#160;the&#160;working&#160;days&#160;when&#160;<br>&#160;&#160;&#160;&#160;&#160;&#160;&#160;the&#160;employee's&#160;shift&#160;ends&#160;after&#160;11:59&#160;PM.&#160;Shift&#160;timings&#160;will&#160;be&#160;aligned&#160;according&#160;to&#160;process&#160;requirements.
        </p>
        @php
            $baseTop += 35; // Adjust top position for the next line
            $currentPoint++; // Increment the point number
        @endphp
    @endif


<p style="position:absolute;top:{{ $baseTop }}px;left:115px;white-space:nowrap" class="ft11"><b>{{ $currentPoint }}.&#160;&#160;</b>Your&#160;employment&#160;with&#160;Niftel&#160;Communications&#160;Pvt.&#160;Ltd.,&#160;will&#160;be&#160;on&#160;an&#160;at-will&#160;basis,&#160;which&#160;means&#160;you&#160;and&#160;</p>
@php
        $baseTop += $lineHeight;  // Increment the point number
        $currentPoint++;
    @endphp
<p style="position:absolute;top:{{ $baseTop }}px;left:138px;white-space:nowrap" class="ft16">the&#160;company&#160;are&#160;free&#160;to&#160;terminate&#160;the&#160;employment&#160;relationship&#160;at&#160;any&#160;time&#160;for&#160;any&#160;reason.&#160;This&#160;letter&#160;is&#160;</p>
@php
        $baseTop += $lineHeight;  // Increment the point number
    @endphp
<p style="position:absolute;top:{{ $baseTop }}px;left:138px;white-space:nowrap" class="ft16">not&#160;a&#160;contractor&#160;guarantee&#160;of&#160;employment&#160;for&#160;a&#160;definitive&#160;period.&#160;</p>
@php
        $baseTop += $lineHeight; // Increment the point number
    @endphp
<p style="position:absolute;top:{{ $baseTop }}px;left:115px;white-space:nowrap" class="ft11"><b>{{ $currentPoint }}.&#160;&#160;</b>You&#160;&#160;will&#160;be&#160;on&#160;a&#160;Probation&#160;Period&#160;for&#160;&#160;<b>Six&#160;Months</b>.&#160;Based&#160;on&#160;the&#160;three-monthly&#160;assessments,&#160;you&#160;will&#160;be&#160;</p>
@php
        $baseTop += $lineHeight; // Increment the point number
        $currentPoint++;
    @endphp
<p style="position:absolute;top:{{ $baseTop }}px;left:138px;white-space:nowrap" class="ft16">confirmed&#160;or&#160;extended&#160;if&#160;deemed&#160;necessary, at&#160;the&#160;company’s discretion.&#160;</p>
@php
        $baseTop += $lineHeight; 
    @endphp
<p style="position:absolute;top:{{ $baseTop }}px;left:115px;white-space:nowrap" class="ft11"><b>{{ $currentPoint }}.&#160;&#160;You&#160;are&#160;required&#160;to&#160;serve&#160;a&#160;Notice&#160;Period&#160;of&#160;at&#160;least&#160; @if(in_array($emp->d_id, [4,8,13,19,26,34,39,42,58,60,61,62,64,65])) Ninety&#160;(90) @elseif(in_array($emp->d_id, [23,21,36,56,69,42,43])) Sixty&#160;(60) @else Thirty &#160;(30) @endif working&#160;days&#160;before&#160;withdrawing&#160;</b></p>
@php
        $baseTop += $lineHeight; //
        $currentPoint++;
    @endphp
<p style="position:absolute;top:{{ $baseTop }}px;left:138px;white-space:nowrap" class="ft11"><b>your&#160;employment.&#160;In&#160;case&#160;of&#160;leaving&#160;the&#160;organization&#160;without&#160;serving&#160;the&#160;Notice&#160;Period,&#160;you&#160;will&#160;</b></p>
@php
        $baseTop += $lineHeight; // 
    @endphp
<p style="position:absolute;top:{{ $baseTop }}px;left:138px;white-space:nowrap" class="ft11"><b>be&#160;liable&#160;to&#160;pay&#160;the&#160;amount&#160;equivalent&#160;to&#160;the&#160;current&#160;salary&#160;of&#160;@if(in_array($emp->d_id, [4,8,13,19,26,34,39,42,58,60,61,62,64,65,])) 3  @elseif(in_array($emp->d_id, [23,21,36,56,69,])) 2 @else 1 @endif months&#160;to&#160;the&#160;organization.&#160;</b></p>
@php
        $baseTop += $lineHeight; 
    @endphp
<p style="position:absolute;top:{{ $baseTop }}px;left:115px;white-space:nowrap" class="ft11"><b>{{ $currentPoint }}.&#160;&#160;</b>If&#160;the&#160;company&#160;finds&#160;any&#160;information&#160;provided&#160;by&#160;you&#160;false&#160;or&#160;incorrect&#160;then&#160;the&#160;company&#160;shall&#160;have&#160;all&#160;the&#160;</p>
@php
        $baseTop += $lineHeight; // Adjust top position for the next line
        $currentPoint++; // Increment the point number
    @endphp
<p style="position:absolute;top:{{ $baseTop }}px;left:138px;white-space:nowrap" class="ft16">rights&#160;to&#160;terminate&#160;your&#160;services&#160;at&#160;its sole&#160;discretion&#160;without giving&#160;further&#160;notice&#160;to&#160;you.&#160;</p>
@php
        $baseTop += $lineHeight; 
    @endphp
<p style="position:absolute;top:{{ $baseTop }}px;left:115px;white-space:nowrap" class="ft111"><b>{{ $currentPoint }}.&#160;&#160;</b>For&#160;detailed&#160;information&#160;and&#160;clarity,&#160;we&#160;encourage&#160;you&#160;to&#160;thoroughly&#160;review&#160;the&#160;<b>Employee&#160;Handbook</b>.&#160;<br/>
    @php
                $currentPoint++; // 
    @endphp
    <b>{{ $currentPoint }}.&#160;&#160;</b>All&#160;&#160;terms&#160;&#160;and&#160;&#160;conditions&#160;&#160;are&#160;&#160;subject&#160;&#160;to&#160;&#160;periodic&#160;&#160;revision&#160;&#160;without&#160;&#160;prior&#160;&#160;notice&#160;&#160;at&#160;&#160;the&#160;&#160;discretion&#160;&#160;of&#160;&#160;the&#160;company</p>
@php
        $baseTop += $lineHeight; // Increment the point number
    @endphp
<p style="position:absolute;top:{{ $baseTop }}px;left:138px;white-space:nowrap" class="ft16">.&#160;</p>
@php
        $baseTop += $lineHeight; // Adjust top position for the next line
        $currentPoint++; // Increment the point number
    @endphp

<p style="position:absolute;top:{{ $baseTop }}px;left:115px;white-space:nowrap" class="ft16">Return&#160;a&#160;copy&#160;of&#160;this&#160;letter&#160;duly&#160;signed&#160;indicating&#160;your&#160;acceptance&#160;of&#160;our&#160;terms&#160;and&#160;conditions&#160;of&#160;employment.&#160;We&#160;</p>
@php
        $baseTop += $lineHeight; // Adjust top position for the next line
        $currentPoint++; // Increment the point number
    @endphp
<p style="position:absolute;top:{{ $baseTop }}px;left:115px;white-space:nowrap" class="ft16">are&#160;excited&#160;to&#160;have&#160;you&#160;join&#160;our&#160;team! If&#160;you have&#160;any&#160;questions,&#160;please&#160;feel&#160;free&#160;to&#160;reach out&#160;at&#160;any&#160;time.&#160;</p>
@php
        $baseTop += $lineHeight; // Adjust top position for the next line
        $currentPoint++; // Increment the point number
    @endphp
<p style="position:absolute;top:{{ $baseTop }}px;left:86px;white-space:nowrap" class="ft112">&#160;<br/>&#160;</p>
@php
        $baseTop += $lineHeight; // Adjust top position for the next line
        $currentPoint++; // Increment the point number
    @endphp
<p style="position:absolute;top:{{ $baseTop }}px;left:111px;white-space:nowrap" class="ft16">Sincerely,&#160;</p>
@php
        $baseTop += $lineHeight; // Adjust top position for the next line
        $currentPoint++; // Increment the point number
    @endphp
<p style="position:absolute;top:{{ $baseTop }}px;left:86px;white-space:nowrap" class="ft111">&#160;<br/>&#160;<br/>&#160;</p>
@php
        $baseTop += $lineHeight; // Adjust top position for the next line
        $currentPoint++; // Increment the point number
    @endphp
<p style="position:absolute;top:{{ $baseTop }}px;left:86px;white-space:nowrap" class="ft111">&#160;<br/>&#160;<br/>&#160;</p>
@php
        $baseTop += $lineHeight; // Adjust top position for the next line
        $currentPoint++; // Increment the point number
    @endphp
<p style="position:absolute;top:{{ $baseTop }}px;left:86px;white-space:nowrap" class="ft111">&#160;<br/>&#160;</p>
@php
        $baseTop += $lineHeight; // Adjust top position for the next line
        $currentPoint++; // Increment the point number
    @endphp
<p style="position:absolute;top:916px;left:86px;white-space:nowrap" class="ft16">&#160;</p>
<p style="position:absolute;top:934px;left:93px;white-space:nowrap" class="ft113"><b>Sakshi Singh&#160;<br/>Human&#160;Resources&#160;</b></p>
<p style="position:absolute;top:974px;left:93px;white-space:nowrap" class="ft18"><b>Niftel&#160;Communications&#160;Pvt.&#160;Ltd.&#160;</b></p>
<p style="position:absolute;top:995px;left:86px;white-space:nowrap" class="ft114"><b>&#160;<br/>&#160;</b></p>
<p style="position:absolute;top:1049px;left:399px;white-space:nowrap" class="ft11"><b>DECLARATION&#160;</b></p>
<p style="position:absolute;top:1070px;left:108px;white-space:nowrap" class="ft16">I&#160;willingly&#160;accept&#160;the&#160;offer,&#160;agreeing&#160;to&#160;the&#160;terms&#160;and&#160;conditions&#160;of&#160;employment&#160;specified&#160;in&#160;this&#160;document.&#160;By&#160;</p>
<p style="position:absolute;top:1088px;left:108px;white-space:nowrap" class="ft16">affixing&#160;my signature&#160;below,&#160;I commit&#160;to&#160;abide&#160;by these&#160;terms.&#160;</p>
<p style="position:absolute;top:1109px;left:86px;white-space:nowrap" class="ft16">&#160;</p>
<p style="position:absolute;top:1126px;left:103px;white-space:nowrap" class="ft11"><b>Date-&#160;</b></p>
<p style="position:absolute;top:1126px;left:493px;white-space:nowrap" class="ft11"><b>Place-&#160;</b></p>
<p style="position:absolute;top:1144px;left:86px;white-space:nowrap" class="ft11"><b>&#160;</b></p>
<p style="position:absolute;top:1162px;left:101px;white-space:nowrap" class="ft11"><b>Candidate’s&#160;Name-&#160;</b></p>
<p style="position:absolute;top:1162px;left:493px;white-space:nowrap" class="ft11"><b>Candidate’s&#160;Signature-&#160;</b></p>
</div>
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
