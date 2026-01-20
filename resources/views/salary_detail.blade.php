@extends('layouts.app')

@push('datatable-styles')
    @include('sections.datatable_css')
@endpush

@section('filter-section')
<style>
.search-status {
    display: flex;
}  
.search-status .btn-primary.rounded.f-14.p-2.mr-3 {
    margin-left: 20px;
}


/* Style inputs with type="text", select elements and textareas */
input[type=text], select, textarea {
  width: 100%; /* Full width */
  padding: 12px; /* Some padding */ 
  border: 1px solid #ccc; /* Gray border */
  border-radius: 4px; /* Rounded borders */
  box-sizing: border-box; /* Make sure that padding and width stays in place */
  margin-top: 6px; /* Add a top margin */
  margin-bottom: 16px; /* Bottom margin */
  resize: vertical /* Allow the user to vertically resize the textarea (not horizontally) */
}

.fm-cont {
  width: 100%; /* Full width */
  padding: 12px; /* Some padding */ 
  border: 1px solid #ccc; /* Gray border */
  border-radius: 4px; /* Rounded borders */
  box-sizing: border-box; /* Make sure that padding and width stays in place */
  margin-top: 6px; /* Add a top margin */
  margin-bottom: 16px; /* Bottom margin */
  resize: vertical /* Allow the user to vertically resize the textarea (not horizontally) */
}

/* Style the submit button with a specific background color etc */
input[type=submit] {
  background-color: #04AA6D;
  color: white;
  padding: 12px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

/* When moving the mouse over the submit button, add a darker green color */
input[type=submit]:hover {
  background-color: #45a049;
}

/* Add a background color and some padding around the form */
.container {
  border-radius: 5px;
  background-color: #f2f2f2;
  padding: 20px;
}
</style>
    <form action="{{url('account/employees')}}" id='myForm' method="GET"> 
    <x-filters.filter-box>
     


    </x-filters.filter-box>

@endsection

@section('content')


<div class="container">
  <form action="{{url('make_salary_slip')}}" method="POST">
      @csrf
    <div class="form-row">
        <!--<div class="col-md-6">-->
        <!--    <label for="fname">Name *</label>-->
        <!--    <input type="text" id="fname" name="name" placeholder="Your name..">-->
        <!--</div>-->
        <div class="col-md-6">
            <label for="fname">Employee Id *</label>
            <input type="text" id="fname" name="id" placeholder="Your name.." value="{{ old('id') }}">
        </div>
        
        <div class="col-md-6">
            <label for="month">Month</label>
            <select class="form-control fm-cont" id="month" value="{{ old('month') }}" name="month">
              <option value="01">January</option>
              <option value="02">February</option>
              <option value="03">March</option>
              <option value="04">April</option>
              <option value="05">May</option>
              <option value="06">June</option>
              <option value="07">July</option>
              <option value="08">August</option>
              <option value="09">September</option>
              <option value="10">October</option>
              <option value="11">November</option>
              <option value="12">December</option>
            </select>
        </div>
        <div class="col-md-6">
            <label for="year">Year</label>
            <select class="form-control fm-cont" id="year" name="year">
              <!-- You can dynamically generate years based on your requirements -->
              <option value="2020">2020</option>
              <option value="2021">2021</option>
              <option value="2022">2022</option>
              <option value="2023">2023</option>
              <option value="2024" selected>2024</option>
              <!-- Add more years as needed -->
            </select>
        </div>
        <div class="col-md-6">
            <label for="fname">Bank Name *</label>
            <input type="text" id="fname" name="bank" value="{{ old('bank') }}" placeholder="Your name..">
        </div>
        
        <div class="col-md-6">
            <label for="fname">Account Number*</label>
            <input type="text" id="fname" name="accn" value="{{ old('accn') }}" placeholder="Your name..">
        </div>
        
        <div class="col-md-3">
            <label for="fname">Number Of Day Worked*</label>
            <input type="number" id="fname" class="fm-cont" value="{{ old('day_worked') }}" name="day_worked" placeholder="Your name..">
        </div>
        
        <div class="col-md-3">
            <label for="fname">Annual CTC *</label>
            <input type="number" id="fname"class="fm-cont" value="{{ old('ctc') }}" name="ctc" placeholder="Your name..">
        </div>
        
        <!--<div class="col-md-6">-->
        <!--    <label for="fname">Employee Id *</label>-->
        <!--    <input type="text" id="fname" name="firstname" placeholder="Your name..">-->
        <!--</div>-->
        
        
        
        <input type="submit" value="Generate">
    </div>
  </form>
</div>


@endsection