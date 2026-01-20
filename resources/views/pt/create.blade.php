@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-sm">
        <!-- Display Success Message -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Display Error Message -->
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Add Skill & Task</h5>
        </div>
        <div class="card-body">
            <!-- Form to Add New Skill & Task -->
            <form action="{{ route('skills.store') }}" method="POST" id="taskForm">
                @csrf

                <!-- Department and User Selection (Only once) -->
                <div class="row mb-3">
                    <div class="col-lg-3 col-md-6">
                        <x-forms.select fieldId="department_id" :fieldLabel="__('app.department')"
                            fieldName="department_id[]" search="true">
                            <option value="0">--</option>
                            @foreach ($departments as $team)
                                <option value="{{ $team->id }}">{{ mb_ucwords($team->team_name) }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-md-9">
                        <div class="form-group my-3">
                            <x-forms.label fieldId="selectEmployee" :fieldLabel="__('app.menu.employees')"
                                fieldRequired="true">
                            </x-forms.label>
                            <x-forms.input-group>
                                <select class="form-control multiple-users" required multiple name="user_id[]"
                                    id="selectEmployee" data-live-search="true" data-size="8">
                                    @foreach ($employees as $item)
                                        <x-user-option :user="$item" :pill="true"/>
                                    @endforeach
                                </select>
                            </x-forms.input-group>
                        </div>
                    </div>
                </div>

                <!-- Tasks Container (Task Rows) -->
                <div id="tasks-container">
                    <!-- Initial Task Row -->
                    <div class="task-section">
                        <div class="row mb-3">
                            <!-- Skill Selection -->
                            <div class="col-lg-6 col-md-6">
                                <div class="form-group">
                                    <label for="skill_id" class="form-label">@lang('Skill')</label>
                                    <select class="form-control" name="skill_id[]" required>
                                        <option selected disabled>Choose a skill...</option>
                                        @foreach ($skillsList as $skill)
                                            <option value="{{ $skill->id }}">{{ $skill->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Development Type -->
                            <div class="col-lg-6 col-md-6">
                                <div class="form-group">
                                    <label for="development_type" class="form-label">@lang('Development Type')</label>
                                    <select class="form-control" name="perc_id[]" required>
                                        <option selected disabled>Choose a development type...</option>
                                        @foreach ($developmentTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->pt_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Timeline -->
                            <div class="col-lg-6 col-md-6">
                                <div class="form-group">
                                    <label for="timeline" class="form-label">@lang('Timeline')</label>
                                    <input type="text" class="form-control" name="timeline[]" required>
                                </div>
                            </div>

                            <!-- Action -->
                            <div class="col-lg-6 col-md-6">
                                <div class="form-group">
                                    <label for="action" class="form-label">@lang('Task')</label>
                                    <input type="text" class="form-control" name="task[]" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <!-- Notes -->
                            <div class="col-lg-6 col-md-6">
                                <div class="form-group">
                                    <label for="notes" class="form-label">@lang('Notes')</label>
                                    <input type="text" class="form-control" name="notes[]">
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-lg-6 col-md-6">
                                <x-forms.select fieldId="status" :fieldLabel="__('Status')" fieldName="hr_status[]" search="true" fieldRequired="true">
                                    <option value="Pending">@lang('Pending')</option>
                                    <option value="Completed">@lang('Completed')</option>
                                </x-forms.select>
                            </div>
                        </div>

                        <!-- Remove Task Button -->
                        <div class="remove-task-btn text-center">
                            <button type="button" class="btn btn-sm btn-danger remove-task" style="background: transparent; border: none;">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Add Task Button -->
                <div class="text-center mt-4">
                    <button type="button" id="add-task" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i>@lang('Add Another Task')
                    </button>
                </div>

                <!-- Submit Button -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success px-4">
                        <i class="fas fa-save me-2"></i>@lang('Save Skill & Task')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Add new task row
        $('#add-task').click(function() {
            var taskSection = $('.task-section').first().clone();
            $('#tasks-container').append(taskSection);
            taskSection.find('input').val(''); // Clear input fields in the cloned section
            taskSection.find('.remove-task').show(); // Show the remove button in the cloned section
        });

        // Remove task row
        $(document).on('click', '.remove-task', function() {
            $(this).closest('.task-section').remove();
        });

        // Initialize selectpicker for multiple users
        $("#selectEmployee").selectpicker({
            actionsBox: true,
            selectAllText: "{{ __('modules.permission.selectAll') }}",
            deselectAllText: "{{ __('modules.permission.deselectAll') }}",
            multipleSeparator: " ",
            selectedTextFormat: "count > 8",
            countSelectedText: function(selected, total) {
                return selected + " {{ __('app.membersSelected') }} ";
            }
        });

        // Department change for dynamic employee list
        $('#department_id').change(function() {
            var id = $(this).val();
            var url = "{{ route('employees.by_department', ':id') }}";
            url = url.replace(':id', id);

            $.easyAjax({
                url: url,
                container: '#save-attendance-data-form',
                type: "GET",
                blockUI: true,
                data: $('#save-attendance-data-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        $('#selectEmployee').html(response.data);
                        $('#selectEmployee').selectpicker('refresh');
                    }
                }
            });
        });
    });
</script>
@endsection
