@extends('layouts.app')

@section('content')
<div class=" m-3">
    <!-- Button to Navigate to Add New Skill Page -->
    <div class="text-end mb-4">
        <a href="{{ route('skills.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Add Skill & Task
        </a>
    </div>

    <!-- Skills Data Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0">Skill Development Tracker</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <!--<th>Department</th>-->
                        <th>User</th>
                        <th>Skill</th>
                        <th>Development Type</th>
                        <th>Timeline</th>
                        <th>Action</th>
                        <th>Employee Status</th>
                        <th>Hr Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tasks as $skill)
                        <tr>
                            <td>{{ $skill->name }}</td>
                            <td>{{ $skill->skill_name }}</td>
                            <td>{{ $skill->pt_name }}</td>
                            <td>{{ $skill->timeline }}</td>
                            <td>{{ $skill->task }}</td>
                            <td>@if($skill->emp_status == 0) <span class="badge badge-primary">Not Started</span> @elseif($skill->emp_status == 1) <span class="badge badge-info">Working</span> @else <span class="badge badge-success">Submitted</span> @endif</td>
                            <td>
                                @if($skill->hr_status == 0) 
                                    <span class="badge badge-info">Uploaded</span>
                                @elseif($skill->hr_status == 1) 
                                    <span class="badge badge-success">Approved</span>
                                @elseif($skill->hr_status == 2) 
                                    <span class="badge badge-danger">Rejected</span>
                                @elseif($skill->hr_status == 3) 
                                    <span class="badge badge-warning">Need Improvement</span>
                                @else
                                    <span class="badge badge-secondary">Unknown Status</span>
                                @endif
                            </td>

                            <td>
                                <a href="{{ url('account/skills/hr') }}/{{$skill->ptid}}" 
                                   class="btn btn-sm btn-outline-warning mr-3 d-inline-flex align-items-center justify-content-center" 
                                   title="Edit">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <a href="{{ url('account/skills/hr/delete') }}/{{$skill->ptid}}" 
                                   class="btn btn-sm btn-outline-danger d-inline-flex align-items-center justify-content-center" 
                                   title="Delete">
                                    <i class="bi bi-trash-fill"></i>
                                </a>


                                
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
