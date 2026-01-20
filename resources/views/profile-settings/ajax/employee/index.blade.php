@php
$addDocumentPermission = user()->permission('add_documents');
$viewDocumentPermission = user()->permission('view_documents');
$deleteDocumentPermission = user()->permission('delete_documents');
$editDocumentPermission = user()->permission('edit_documents');
@endphp

<style>
    .file-action {
        visibility: hidden;
    }

    .file-card:hover .file-action {
        visibility: visible;
    }
</style>

<!-- TAB CONTENT START -->
<div class="col-xl-12 col-lg-12 col-md-12 ntfcn-tab-content-left w-100 p-4">
    @if ($addDocumentPermission == 'all')

        <div class="row">
            <div class="col-md-12">
                <a class="f-15 f-w-500" href="javascript:;" id="add-task-file"><i
                        class="icons icon-plus font-weight-bold mr-1"></i>@lang('app.add')
                    @lang('app.file')</a>
            </div>
        </div>
    @endif
    
    @php
        $employee = DB::table('employee_details')
            ->where('user_id', user()->id)
            ->select('offer_salary_month', 'joining_date')
            ->first();
        
        $requiredDocs = [
            'Adhar Card',
            'Pan Card',
            '12th Marksheet',
            '10th Marksheet',
            'Passport Size Photo',
            'Acknowledgment Letter'
        ];
        
        if ($employee) {
            // If offer salary is present, add Offer/Appointment Letter
            if (!empty($employee->offer_salary_month)) {
                $requiredDocs[] = 'Offer Letter/Appointment Letter';
            }
        
            // If joining date is today, add Passbook
            if (!empty($employee->joining_date) && date('Y-m-d', strtotime($employee->joining_date)) == date('Y-m-d')) {
                $requiredDocs[] = 'Passbook';
            }
        }

        
    
        $uploadedDocs = $user->documents->pluck('name')->toArray();
        $missingDocs = array_diff($requiredDocs, $uploadedDocs);
    @endphp
    
    @if (!empty($missingDocs))
        <div class="required-docs" style="padding: 10px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 15px; font-size: 14px;">
            <strong>Missing Documents:</strong>
            @foreach ($missingDocs as $doc)
                <span style="margin-left: 10px;">{{ $doc }},</span>
            @endforeach
        </div>
    @endif
    @if (in_array('Acknowledgment Letter', $missingDocs))
        <div class="ack-alert" style="padding: 12px; background: #fff3cd; border: 1px solid #ffeeba; border-radius: 5px; color: #856404; font-size: 15px; margin-top: 10px; display: flex; align-items: center; gap: 10px;">
            <span>You need to upload your signed <strong>Acknowledgment Letter</strong>.</span>
            <a href="{{url('account/ViewMyack')}}" target="_blank" style="color: #856404; text-decoration: none;" title="Download Acknowledgment Letter">
                📄 Click To Download 
            </a>
        </div>
    @endif
    
    <!--@php   @endphp-->
    @if (in_array('Offer Letter/Appointment Letter', $missingDocs) && $employee->offer_salary_month != '')
        <div class="ack-alert" style="padding: 12px; background: #fff3cd; border: 1px solid #ffeeba; border-radius: 5px; color: #856404; font-size: 15px; margin-top: 10px; display: flex; align-items: center; gap: 10px;">
            <span>You need to upload your signed <strong>Offer Letter</strong>.</span>
            <a href="{{url('account/ViewMyOl')}}" target="_blank" style="color: #856404; text-decoration: none;" title="Download Offer Letter">
                📄 Click To Print 
            </a>
        </div>
    @endif



    

@if(session('missing_docs'))
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    Swal.fire({
        icon: 'error',
        title: 'Missing Documents',
        html: 'Please upload the following documents:<br><b>{{ session("missing_docs") }}</b>',
        confirmButtonText: 'OK'
    });
</script>
@endif


    <div class="d-flex flex-wrap mt-3" id="task-file-list">
        @php
            $totalDocuments = ($user->documents) ? count($user->documents) : 0;
            $permission = 0; // assuming we do have permission for all uploaded files
        @endphp
        @forelse($user->documents as $file)
            @if ($viewDocumentPermission == 'all'
            || ($viewDocumentPermission == 'added' && $file->added_by == user()->id)
            || ($viewDocumentPermission == 'owned' && ($file->user_id == user()->id && $file->added_by != user()->id))
            || ($viewDocumentPermission == 'both' && ($file->added_by == user()->id || $file->user_id == user()->id)))
                <x-file-card :fileName="$file->name" :dateAdded="$file->created_at->diffForHumans()">
                    @if ($file->icon == 'images')
                        <img src="{{ $file->doc_url }}">
                    @else
                        <i class="fa {{ $file->icon }} text-lightest"></i>
                    @endif
                    <x-slot name="action">
                        <div class="dropdown ml-auto file-action">
                            <button
                                class="btn btn-lg f-14 p-0 text-lightest text-capitalize rounded  dropdown-toggle"
                                type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-ellipsis-h"></i>
                            </button>

                            <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                aria-labelledby="dropdownMenuLink" tabindex="0">
                                @if ($viewDocumentPermission == 'all'
                                || ($viewDocumentPermission == 'added' && $file->added_by == user()->id)
                                || ($viewDocumentPermission == 'owned' && ($file->user_id == user()->id && $file->added_by != user()->id))
                                || ($viewDocumentPermission == 'both' && ($file->added_by == user()->id || $file->user_id == user()->id)))

                                    <a class="cursor-pointer d-block text-dark-grey f-13 pt-3 px-3 "
                                        target="_blank" href="{{ $file->doc_url }}">@lang('app.view')</a>

                                    <a class="cursor-pointer d-block text-dark-grey f-13 py-3 px-3 "
                                        href="{{ route('employee-docs.download', md5($file->id)) }}">@lang('app.download')</a>
                                @endif


                            <!--Acknowledgment should not edit -->
                            
                            @if($file->name != 'Acknowledgment Letter')
                                @if ($editDocumentPermission == 'all'
                                || ($editDocumentPermission == 'added' && $file->added_by == user()->id)
                                || ($editDocumentPermission == 'owned' && ($file->user_id == user()->id && $file->added_by != user()->id))
                                || ($editDocumentPermission == 'both' && ($file->added_by == user()->id || $file->user_id == user()->id)))
                                    <a class="cursor-pointer d-block text-dark-grey pb-3 f-13 px-3 edit-file"
                                        href="javascript:;" data-file-id="{{ $file->id }}">@lang('app.edit')</a>
                                @endif

                                @if ($deleteDocumentPermission == 'all'
                                || ($deleteDocumentPermission == 'added' && $file->added_by == user()->id)
                                || ($deleteDocumentPermission == 'owned' && ($file->user_id == user()->id && $file->added_by != user()->id))
                                || ($deleteDocumentPermission == 'both' && ($file->added_by == user()->id || $file->user_id == user()->id)))
                                    <a class="cursor-pointer d-block text-dark-grey f-13 pb-3 px-3 delete-file"
                                        data-row-id="{{ $file->id }}"
                                        href="javascript:;">@lang('app.delete')</a>
                                @endif
                            @endif
                            </div>
                        </div>
                    </x-slot>
                </x-file-card>
            @else
                @php
                    $permission++;
                @endphp
            @endif
        @empty
            <div class="align-items-center d-flex flex-column text-lightest p-20 w-100">
                <i class="fa fa-file f-21 w-100"></i>

                <div class="f-15 mt-4">
                    - @lang('messages.noFileUploaded')
                </div>
            </div>
        @endforelse
        @if (isset($user->clientDocuments) && $totalDocuments > 0 && $totalDocuments == $permission)
            <div class="align-items-center d-flex flex-column text-lightest p-20 w-100">
                <i class="fa fa-file-excel f-21 w-100"></i>

                <div class="f-15 mt-4">
                    - @lang('messages.noFileUploaded') -
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    $('#add-task-file').click(function() {
        var url = "{{ route('employee-docs.create') }}";

        $(MODAL_DEFAULT + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_DEFAULT, url);
    });

    $('body').on('click', '.edit-file', function() {
        var fileId = $(this).data('file-id');
        var url = "{{ route('employee-docs.edit', ':id') }}";
        url = url.replace(':id', fileId);

        $(MODAL_DEFAULT + ' ' + MODAL_HEADING).html('...');
        $.ajaxModal(MODAL_DEFAULT, url);
    });

    $('#cancel-doc').click(function() {
        $('#save-document-data-form').addClass('d-none');
        $('#add-task-file').closest('.row').removeClass('d-none');
    });

    $('body').on('click', '.delete-file', function() {
        var id = $(this).data('row-id');
        Swal.fire({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.recoverRecord')",
            icon: 'warning',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: "@lang('messages.confirmDelete')",
            cancelButtonText: "@lang('app.cancel')",
            customClass: {
                confirmButton: 'btn btn-primary mr-3',
                cancelButton: 'btn btn-secondary'
            },
            showClass: {
                popup: 'swal2-noanimation',
                backdrop: 'swal2-noanimation'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                var url = "{{ route('employee-docs.destroy', ':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function(response) {
                        if (response.status == "success") {
                            $('#task-file-list').html(response.view);
                        }
                    }
                });
            }
        });
    });
</script>
<!-- TAB CONTENT END -->
