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
</style>
  

@endsection

@section('content')

@if(session('show_confetti'))
<script>
document.addEventListener("DOMContentLoaded", function() {
    var duration = 3 * 1000;
    var end = Date.now() + duration;

    (function frame() {
        confetti({
            particleCount: 5,
            angle: 60,
            spread: 55,
            origin: { x: 0, y: 0 },
            startVelocity: 45
        });
        confetti({
            particleCount: 5,
            angle: 120,
            spread: 55,
            origin: { x: 1, y: 0 },
            startVelocity: 45
        });

        if (Date.now() < end) {
            requestAnimationFrame(frame);
        }
    }());
});
</script>
@endif
    <style>
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f9fafb;
        margin: 0;
        padding: 20px;
    }

    h2 {
        text-align: center;
        color: #2c3e50;
        margin-bottom: 30px;
    }

    form {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        padding: 30px;
        max-width: 1200px;
        margin: auto;
    }

    .form-section-title {
        font-size: 20px;
        font-weight: 600;
        color: #34495e;
        margin: 25px 0 10px 0;
        border-bottom: 1px solid #ddd;
        padding-bottom: 5px;
    }

    .form-row {
        display: flex;
        flex-wrap: wrap;
        margin-bottom: 20px;
        gap: 20px;
    }

    .form-row label {
        flex: 1 1 200px;
        font-weight: 500;
        margin-bottom: 5px;
        color: #555;
    }

    .form-row input,
    .form-row select {
        flex: 2 1 300px;
        padding: 10px 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .form-row input:focus,
    .form-row select:focus {
        border-color: #3498db;
        box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        outline: none;
    }

    .form-row input[disabled] {
        background-color: #f0f0f0;
        cursor: not-allowed;
    }

    input[type="submit"] {
        display: block;
        width: 100%;
        max-width: 220px;
        margin: 30px auto 0 auto;
        background-color: #2ecc71;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 6px;
        font-size: 16px;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }

    input[type="submit"]:hover {
        background-color: #27ae60;
    }

    .references-container,
    .work-experience-container {
        margin-top: 20px;
    }

    .dynamic-row {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 10px;
    }

    .add-reference-btn,
    .add-experience-btn {
        margin-top: 10px;
        background-color: #2980b9;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        font-size: 14px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    .add-reference-btn:hover,
    .add-experience-btn:hover {
        background-color: #2471a3;
    }

    @media (max-width: 768px) {
        .form-row {
            flex-direction: column;
        }

        .form-row label,
        .form-row input,
        .form-row select {
            flex: 1 1 100%;
        }

        input[type="submit"] {
            width: 100%;
        }
    }
</style>



    <!--<h2> Joining Form</h2>-->
   <div class="alert alert-warning alert-dismissible fade show" role="alert">
      <strong></strong>All fields are required 
      <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <form action="{{url('submit_join_form')}}" method="post">
        @csrf
        <div class="form-row">
            
            <label>First Name: </label>
            <input type="text" name="f_name" value="{{ $emp->f_name ?? $user->name ?? '' }}" required>
            <label>Last Name(If not available then write 'NA'):</label> 
            <input type="text" name="l_name" value="{{$emp->l_name}}" required>
        </div>

        <div class="form-row">
            <label>Employee ID:</label>
            <input type="text" disabled name="" value="{{$emp->employee_id}}" >
    
            <!--<small style="color: #666; font-size: 12px;"></small>-->
        
            <label>Centre Location - City: </label>
            <select name="center_city" class="form-control">
                <option value="">Select Center City</option>
                <option value="Lucknow" {{ $emp->center_city == 'Lucknow' ? 'selected' : '' }}>Lucknow</option>
                <option value="Gurugram" {{ $emp->center_city == 'Gurugram' ? 'selected' : '' }}>Gurugram</option>
                <option value="Kanpur" {{ $emp->center_city == 'Kanpur' ? 'selected' : '' }}>Kanpur</option>
                <option value="Mumbai" {{ $emp->center_city == 'Mumbai' ? 'selected' : '' }}>Mumbai</option>
                <option value="Delhi" {{ $emp->center_city == 'Delhi' ? 'selected' : '' }}>Delhi</option>
                <option value="Kolkata" {{ $emp->center_city == 'Kolkata' ? 'selected' : '' }}>Kolkata</option>
                <option value="Gorakhpur" {{ $emp->center_city == 'Gorakhpur' ? 'selected' : '' }}>Gorakhpur</option>
                <option value="Guwahati" {{ $emp->center_city == 'Guwahati' ? 'selected' : '' }}>Guwahati</option>
            </select>

        </div>


        <div class="form-row">
            <label>Email ID: </label>
            <input type="text" value="{{$user_data->email}}" name="email" required>
            <label>Contact No: </label>
            <input type="number" maxlength="10" value="{{$user_data->mobile}}" name="mobile" id="mobile" required>
            
        </div>
        

        
        <script>
            jQuery(document).ready(function () {
              jQuery("#mobile").keypress(function (e) {
                 var length = jQuery(this).val().length;
               if(length > 9) {
                    return false;
               } else if(e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                    return false;
               } else if((length == 0) && (e.which == 48)) {
                    return false;
               }
              });
            });
        </script>
        
        <div class="form-row">
            <label>Alternate Contact No:</label>
            <input type="text" value="{{ $emp->alt_contact_no }}" name="alt_contact_no" required>
        
            <label>Relation With Alternate No: </label>
            <select class="form-control" name="relation_with_alt_no" id="relationSelect" required>
                <option value="">-- Select Relation --</option>
                <option value="mother" {{ $emp->relation_with_alt_no == 'mother' ? 'selected' : '' }}>Mother</option>
                <option value="father" {{ $emp->relation_with_alt_no == 'father' ? 'selected' : '' }}>Father</option>
                <option value="spouse" {{ $emp->relation_with_alt_no == 'spouse' ? 'selected' : '' }}>Spouse</option>
                <option value="other" {{ $emp->relation_with_alt_no == 'other' ? 'selected' : '' }}>Other</option>
            </select>
        </div> 
        
        <div class="form-row" id="customRelationField" style="{{ $emp->relation_with_alt_no == 'other' ? '' : 'display: none;' }}">
            <div style="display: flex; margin-right: 555px;">
                <label>Name of Relation: </label> 
                <input type="text" name="relation_with_alt_name" placeholder="Enter relation" class="form-control" value="{{ $emp->relation_with_alt_name }}">
            </div>
        </div>
        
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const relationSelect = document.getElementById("relationSelect");
                const customField = document.getElementById("customRelationField");
        
                function toggleCustomField() {
                    if (relationSelect.value === "other") {
                        customField.style.display = "block";
                    } else {
                        customField.style.display = "none";
                    }
                }
        
                relationSelect.addEventListener("change", toggleCustomField);
        
                // Initial call to show/hide on page load
                toggleCustomField();
            });
        </script>



        <div class="form-row">
            
            <label>Father's Name: </label>
            <input type="text" value="{{$emp->father_name}}" name="father_name" required>
            <label>Mother's Name: </label>
            <input type="text" value="{{$emp->mother_name}}" name="mother_name" required>
        </div>

        <div class="form-row">
            
            <!--<div class="form-row">-->
                <label>Last Qualification: </label>
                <select class="form-control" name="last_education" required>
                    <option value="">-- Select Qualification --</option>
                    <option value="Doctorate" {{ $emp->last_education == 'Doctorate' ? 'selected' : '' }}>Doctorate</option>
                    <option value="Post Graduate" {{ $emp->last_education == 'Post Graduate' ? 'selected' : '' }}>Post Graduate</option>
                    <option value="Graduate" {{ $emp->last_education == 'Graduate' ? 'selected' : '' }}>Graduate</option>
                    <option value="Intermediate" {{ $emp->last_education == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                    <option value="Other" {{ $emp->last_education == 'Other' ? 'selected' : '' }}>Other</option>
                </select>

            <!--</div>-->

            <label>D.O.B.: </label>
            <input type="date" value="{{$emp->date_of_birth}}" name="date_of_birth" id="date_of_birth" oninput="calculateAge()" required>
        </div>
       
        
        <script>
            jQuery(document).ready(function () {
              jQuery("#f_contact").keypress(function (e) {
                 var length = jQuery(this).val().length;
               if(length > 9) {
                    return false;
               } else if(e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                    return false;
               } else if((length == 0) && (e.which == 48)) {
                    return false;
               }
              });
            });
            jQuery(document).ready(function () {
              jQuery("#m_contact").keypress(function (e) {
                 var length = jQuery(this).val().length;
               if(length > 9) {
                    return false;
               } else if(e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57)) {
                    return false;
               } else if((length == 0) && (e.which == 48)) {
                    return false;
               }
              });
            });
        </script>


        <script>
            function calculateAge() {
                // Get the value of the date of birth input
                var dobInput = document.getElementById('date_of_birth');
                var dobValue = dobInput.value;
        
                if (dobValue) {
                    // Calculate the age based on the entered date of birth
                    var dobDate = new Date(dobValue);
                    var currentDate = new Date();
                    var age = currentDate.getFullYear() - dobDate.getFullYear();
        
                    // Adjust the age if the birthday hasn't occurred yet this year
                    if (currentDate.getMonth() < dobDate.getMonth() || (currentDate.getMonth() === dobDate.getMonth() && currentDate.getDate() < dobDate.getDate())) {
                        age--;
                    }
        
                    // Update the age input field
                    var ageInput = document.getElementById('age');
                    ageInput.value = age;
                } else {
                    // Reset the age input if no date of birth is entered
                    var ageInput = document.getElementById('age');
                    ageInput.value = "";
                }
            }
        </script>

        <div class="form-row">
            <label>Age:</label>
            <input type="number" value="{{$emp->age}}"  id="age" name="age" required> 
            <label>Gender:</label>
            <select class="form-control" name="gender" required>
                <option value="male" @if($user_data->gender == "male") selected @endif >Male </option>
                <option value="female" @if($user_data->gender == "female") selected @endif>Female </option>
                <option value="others" @if($user_data->gender == "other") selected @endif>Other </option>
            </select>
        </div>

        <div class="form-row">
            <label>Blood Group:</label>
            <input type="text" value="{{$emp->blood_group}}" name="blood_group" >
            <label>Nationality:</label>
            <input type="text" value="{{$emp->nationality}}" name="nationality" >
        </div>
        
        <div class="form-row">
    <label>Current Address:</label>
    <input type="text" value="{{$emp->local_add}}" name="local_add" id="local_add" required>
    <label>City:</label>
    <input type="text" value="{{$emp->local_city}}" name="local_city" id="local_city" required>
</div>

<div class="form-row">
    <label>State:</label>
    <input type="text" value="{{$emp->local_state}}" name="local_state" id="local_state" required>
    <label>Pin:</label>
    <input type="text" value="{{$emp->local_pin}}" name="local_pin" id="local_pin" required>
</div>

<div class="form-row">
    <label>
        <input type="checkbox" id="sameAddressCheckbox" disabled>
        Permanent address is same as current address
    </label>
</div>

<div class="form-row">
    <label>Permanent Address: *</label>
    <input type="text" value="{{$emp->per_add}}" name="per_add" id="per_add" required>
    <label>City:</label>
    <input type="text" value="{{$emp->per_city}}" name="per_city" id="per_city" required>
</div>

<div class="form-row">
    <label>State:</label>
    <input type="text" value="{{$emp->per_state}}" name="per_state" id="per_state" required>
    <label>Pin:</label>
    <input type="text" value="{{$emp->per_code}}" name="per_code" id="per_code" required>
</div>

<div class="form-row">
    <label>Aadhaar No: *</label>
    <input type="text" value="{{$emp->aadhar_no}}" name="aadhar_no" required>
    <label>Pan No:</label>
    <input type="text" value="{{$emp->pan_no}}" name="pan_no" required>
</div>

<div class="form-row">
    <label>Driving License No:</label>
    <input type="text" value="{{$emp->driving_no}}" name="driving_no">
    <label>Medical Issues (if any):</label>
    <input type="text" value="{{$emp->medical_issue}}" name="medical_issue">
</div>

<script>
const checkbox = document.getElementById('sameAddressCheckbox');
const localFields = ['add', 'city', 'state', 'pin'];
const perFields = ['add', 'city', 'state', 'code'];

function checkLocalFieldsFilled() {
    // Enable checkbox only if all current address fields are filled
    const allFilled = localFields.every(field => {
        const val = document.getElementById(`local_${field}`).value.trim();
        return val !== '';
    });
    checkbox.disabled = !allFilled;
}

function copyAddress() {
    localFields.forEach((field, index) => {
        const local = document.getElementById(`local_${field}`);
        const per = document.getElementById(`per_${perFields[index]}`);

        if (checkbox.checked) {
            per.value = local.value;
            per.readOnly = true;
        } else {
            per.readOnly = false;
        }
    });
}

// Check if current address fields are filled initially
checkLocalFieldsFilled();

// Add event listeners for enabling checkbox dynamically
localFields.forEach(field => {
    document.getElementById(`local_${field}`).addEventListener('input', checkLocalFieldsFilled);
    document.getElementById(`local_${field}`).addEventListener('input', () => {
        if (checkbox.checked) copyAddress(); // live update
    });
});

// Checkbox listener
checkbox.addEventListener('change', copyAddress);
</script>


        <div class="form-section">
            <h3>Account Details</h3>
        
            <div class="form-row">
                <label>Bank Name: *</label>
                <input type="text" name="bank_name" value="{{ $bankDetails->bank_name ?? '' }}" required>
        
                <label>Account Holder Name: *</label>
                <input type="text" name="acc_holder_name" value="{{ $bankDetails->acc_holder_name ?? '' }}" required>
                
        
                
            </div>
        
            <div class="form-row">
                <label>Account Number: *</label>
                <input type="text" name="account_number" value="{{ $bankDetails->account_number ?? '' }}" required>
                <label>Branch: *</label>
                <input type="text" name="branch_name" value="{{ $bankDetails->branch_name ?? '' }}" required>
        
                
            </div>
            <div  class="form-row ">
                <label>Account Type:</label>
                <select name="account_type">
                    <option value="savings" {{ (isset($bankDetails) && $bankDetails->account_type == 'savings') ? 'selected' : '' }}>Savings</option>
                    <option value="current" {{ (isset($bankDetails) && $bankDetails->account_type == 'current') ? 'selected' : '' }}>Current</option>
                    <option value="salary" {{ (isset($bankDetails) && $bankDetails->account_type == 'salary') ? 'selected' : '' }}>Salary</option>
                </select>
                <label>IFSC Code: *</label>
                <input type="text" name="ifsc_code" value="{{ $bankDetails->ifsc_code ?? '' }}" required>
            </div>
        </div>
        <div class="form-row">
            <div class="references-container">
                <label>References:</label>
                <div id="references-list">
                    @foreach($refer as $r)
                    <div class="reference-row">
                        <input type="text" value="{{$r->reference_name}}" name="reference_name1[]"disabled placeholder="Name">
                        <input type="text" value="{{$r->reference_contact}}" name="reference_contact1[]"disabled placeholder="Contact">
                        <input type="text" value="{{$r->reference_relation}}" name="reference_relation1[]"disabled placeholder="Relation">
                    </div>
                    @endforeach
                </div>
                <button type="button" class="add-reference-btn" onclick="addReference()">Add Another Reference</button>
            </div>
    
            <div class="work-experience-container">
                <label>Work Experience:</label>
                <div id="experience-list">
                    @foreach($work as $r)
                    <div class="work-experience-row">
                        <input type="text" value="{{$r->designation}}" name="experience_designation1[]" disabled placeholder="Designation">
                        <input type="text" value="{{$r->org}}" name="experience_organization1[]"disabled placeholder="Organization">
                        <input type="text" value="{{$r->ctc}}" name="experience_ctc1[]" disabled placeholder="CTC">
                        <input type="text" value="{{$r->reason_leavingc}}" name="experience_reason_leaving1[]" disabled placeholder="Reason for Leaving">
                    </div>
                    @endforeach
                </div>
                <button type="button" class="add-experience-btn" onclick="addExperience()">Add Another Experience</button>
            </div>
        </div>
        
        

        
        <input type="submit" class="" value="Submit">
    </form>

    <script>
        function addReference() {
            var referencesList = document.getElementById("references-list");
            var newReferenceRow = createRow(['reference_name', 'reference_contact', 'reference_relation']);
            referencesList.appendChild(newReferenceRow);
        }

        function addExperience() {
            var experienceList = document.getElementById("experience-list");
            var newExperienceRow = createRow(['experience_designation', 'experience_organization', 'experience_ctc', 'experience_reason_leaving']);
            experienceList.appendChild(newExperienceRow);
        }

        function addDocument() {
            var documentsList = document.getElementById("documents-list");
            var newDocumentRow = createRow(['Name of Document', 'Number of Copies']);
            documentsList.appendChild(newDocumentRow);
        }

        function createRow(inputNames) {
            var newRow = document.createElement("div");
            newRow.className = inputNames[0].toLowerCase() + "-row";
            newRow.classList.add("dynamic-row");
            inputNames.forEach(function (inputName) {
                var input = document.createElement("input");
                input.type = "text";
                input.name = inputName.toLowerCase().replace(/\s+/g, "_") + "[]";
                input.placeholder = inputName;
                newRow.appendChild(input);
            });
            return newRow;
        }
    </script>

    
@endsection

@push('scripts')
    
@endpush
