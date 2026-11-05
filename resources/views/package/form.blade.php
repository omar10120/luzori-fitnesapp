@php
    $id = $id ?? null;
    $selectedUsers = old('users', isset($data) ? $data->users()->pluck('users.id')->toArray() : []);
    
    // Check if we have existing exercises (Edit mode) or old input (Validation error)
    $existingExercises = old('exercises');
    if ($existingExercises === null && isset($data)) {
        $packageExercises = $data->packageExercises ?? collect(); 

        $existingExercises = $data->packageExercises->map(function($pe) {
            $duration = $pe->duration ? explode(':', $pe->duration) : [null, null, null];
            return [
                'exercise_id' => $pe->exercise_id,
                'pe_title' => $pe->title,
                'pe_instruction' => $pe->instruction,
                'pe_tips' => $pe->tips,
                'pe_video_type' => $pe->video_type,
                'pe_video_url' => $pe->video_url,
                'pe_status' => $pe->status,
                'pe_is_premium' => $pe->is_premium,
                'pe_type' => $pe->type,
                'pe_based' => $pe->based,
                'pe_seconds_per_rep' => $pe->seconds_per_rep,
                'pe_hours' => $duration[0],
                'pe_minute' => $duration[1],
                'pe_second' => $duration[2],
                'pe_reps' => collect($pe->sets ?? [])->pluck('reps')->toArray(),
                'pe_time' => collect($pe->sets ?? [])->pluck('time')->toArray(),
                'pe_weight' => collect($pe->sets ?? [])->pluck('weight')->toArray(),
                'pe_rest' => collect($pe->sets ?? [])->pluck('rest')->toArray(),
            ];
        })->toArray();
    }
    if ($existingExercises === null) {
        $existingExercises = []; // Empty on create
    }
@endphp

@push('scripts')
<script>
    (function($) {
        let exerciseIndex = {{ count($existingExercises) }};
        const exerciseDataUrl = @json(url('admin/package-exercise-data'));

        // Template for a new exercise block
        const exerciseTemplate = `
            <div class="exercise-block border p-3 mb-3 bg-light rounded" data-index="__INDEX__">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 text-primary">Exercise #__NUM__</h5>
                    <button type="button" class="btn btn-sm btn-danger remove-exercise">Remove</button>
                </div>
                <div class="row">
                    <div class="form-group col-md-6">
                        <label class="form-control-label">Select Exercise <span class="text-danger">*</span></label>
                        <select name="exercises[__INDEX__][exercise_id]" class="form-control select2js exercise-select" required>
                            <option value="">Select</option>
                            @foreach($exercises as $exId => $exTitle)
                                <option value="{{ $exId }}">{{ $exTitle }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="exercise-override-section d-none mt-3">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label class="form-control-label">Title Override</label>
                            <input type="text" name="exercises[__INDEX__][pe_title]" class="form-control pe_title" placeholder="Title">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-control-label">Video Type</label>
                            <select name="exercises[__INDEX__][pe_video_type]" class="form-control pe_video_type">
                                <option value="url">URL</option>
                                <option value="upload_video">Upload Video</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4 pe-video-url">
                            <label class="form-control-label">Video URL</label>
                            <input type="text" name="exercises[__INDEX__][pe_video_url]" class="form-control pe_video_url" placeholder="Video URL">
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-control-label">Status</label>
                            <select name="exercises[__INDEX__][pe_status]" class="form-control pe_status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <div class="form-group col-md-4">
                            <label class="form-control-label d-block">Is Premium</label>
                            <div class="form-check form-switch mt-2">
                                <input type="hidden" name="exercises[__INDEX__][pe_is_premium]" value="0">
                                <input type="checkbox" name="exercises[__INDEX__][pe_is_premium]" value="1" class="form-check-input pe_is_premium">
                            </div>
                        </div>
                    </div>

                    <ul class="nav nav-pills nav-fill mb-3 exercise-tab" role="tablist">
                        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#sets___INDEX__" data-type="sets">Sets</a></li>
                        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#duration___INDEX__" data-type="duration">Duration</a></li>
                    </ul>
                    <input type="hidden" name="exercises[__INDEX__][pe_type]" class="pe_type" value="sets">

                    <div class="tab-content">
                        <!-- Sets Tab -->
                        <div id="sets___INDEX__" class="tab-pane fade show active">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check">
                                        <input type="radio" name="exercises[__INDEX__][pe_based]" value="reps" class="form-check-input pe_based" checked>
                                        <label class="form-check-label">Reps</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="exercises[__INDEX__][pe_based]" value="time" class="form-check-input pe_based">
                                        <label class="form-check-label">Time</label>
                                    </div>
                                </div>
                                <div class="col-md-4 pe-reps-time-row">
                                    <input type="number" name="exercises[__INDEX__][pe_seconds_per_rep]" class="form-control pe_seconds_per_rep" placeholder="Seconds per rep">
                                </div>
                                <div class="col-md-4 text-end">
                                    <button type="button" class="btn btn-sm btn-primary add-set-btn">Add Set</button>
                                </div>
                            </div>
                            <table class="table table-sm mt-2 sets-table">
                                <thead>
                                    <tr>
                                        <th>Reps</th>
                                        <th>Time</th>
                                        <th>Weight</th>
                                        <th>Rest</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>

                        <!-- Duration Tab -->
                        <div id="duration___INDEX__" class="tab-pane fade">
                            <div class="row">
                                <div class="col-md-4">
                                    <input type="number" name="exercises[__INDEX__][pe_hours]" class="form-control pe_hours" placeholder="Hours">
                                </div>
                                <div class="col-md-4">
                                    <input type="number" name="exercises[__INDEX__][pe_minute]" class="form-control pe_minute" placeholder="Minutes">
                                </div>
                                <div class="col-md-4">
                                    <input type="number" name="exercises[__INDEX__][pe_second]" class="form-control pe_second" placeholder="Seconds">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label class="form-control-label">Instruction</label>
                            <textarea name="exercises[__INDEX__][pe_instruction]" class="form-control tinymce-instruction"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-control-label">Tips</label>
                            <textarea name="exercises[__INDEX__][pe_tips]" class="form-control tinymce-tips"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Init block function
        function initBlock($block) {
            const index = $block.data('index');
            
            // Init TinyMCE
            tinymce.init({
                selector: '.tinymce-instruction, .tinymce-tips',
                height: 150,
                menubar: false,
                plugins: 'lists link',
                toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist'
            });

            // Init Select2
            $block.find('.select2js').select2({ theme: 'bootstrap-5' });

            // Set Tab change
            $block.find('.exercise-tab a').on('show.bs.tab', function(e) {
                $block.find('.pe_type').val($(e.target).data('type'));
            });

            // Video Type toggle
            $block.find('.pe_video_type').on('change', function() {
                if ($(this).val() === 'url') {
                    $block.find('.pe-video-url').show();
                } else {
                    $block.find('.pe-video-url').hide();
                }
            });

            // Based toggle
            $block.find('.pe_based').on('change', function() {
                if ($(this).val() === 'time') {
                    $block.find('.pe-reps-time-row').hide();
                } else {
                    $block.find('.pe-reps-time-row').show();
                }
            });

            // Add Set Row
            $block.find('.add-set-btn').on('click', function() {
                let rowHtml = `
                    <tr>
                        <td><input type="number" name="exercises[${index}][pe_reps][]" class="form-control form-control-sm" min="0"></td>
                        <td><input type="number" name="exercises[${index}][pe_time][]" class="form-control form-control-sm" min="0"></td>
                        <td><input type="number" name="exercises[${index}][pe_weight][]" class="form-control form-control-sm" min="0"></td>
                        <td><input type="number" name="exercises[${index}][pe_rest][]" class="form-control form-control-sm" min="0"></td>
                        <td><button type="button" class="btn btn-sm btn-danger remove-set">X</button></td>
                    </tr>
                `;
                $block.find('.sets-table tbody').append(rowHtml);
            });

            // Remove Set Row
            $block.on('click', '.remove-set', function() {
                $(this).closest('tr').remove();
            });

            // Fetch Exercise Data on select
            $block.find('.exercise-select').on('change', function() {
                let exId = $(this).val();
                if (!exId) {
                    $block.find('.exercise-override-section').addClass('d-none');
                    return;
                }

                $.get(exerciseDataUrl + '/' + exId, function(data) {
                    $block.find('.exercise-override-section').removeClass('d-none');
                    $block.find('.pe_title').val(data.title);
                    $block.find('.pe_video_type').val(data.video_type || 'url').trigger('change');
                    $block.find('.pe_video_url').val(data.video_url);
                    $block.find('.pe_is_premium').prop('checked', data.is_premium == 1);
                    
                    // Populate sets
                    let tbody = $block.find('.sets-table tbody');
                    tbody.empty();
                    if (data.sets && data.sets.length > 0) {
                        data.sets.forEach(function(set) {
                            let rowHtml = `
                                <tr>
                                    <td><input type="number" name="exercises[${index}][pe_reps][]" value="${set.reps || ''}" class="form-control form-control-sm"></td>
                                    <td><input type="number" name="exercises[${index}][pe_time][]" value="${set.time || ''}" class="form-control form-control-sm"></td>
                                    <td><input type="number" name="exercises[${index}][pe_weight][]" value="${set.weight || ''}" class="form-control form-control-sm"></td>
                                    <td><input type="number" name="exercises[${index}][pe_rest][]" value="${set.rest || ''}" class="form-control form-control-sm"></td>
                                    <td><button type="button" class="btn btn-sm btn-danger remove-set">X</button></td>
                                </tr>
                            `;
                            tbody.append(rowHtml);
                        });
                    } else {
                        $block.find('.add-set-btn').trigger('click');
                    }
                });
            });
        }

             // --- UPDATED: Reusable function to add a block (handles both empty and populated) ---
        function addExerciseBlock(data = null) {
            let num = exerciseIndex + 1;
            let html = exerciseTemplate.replace(/__INDEX__/g, exerciseIndex).replace(/__NUM__/g, num);
            let $block = $(html);
            $('#exercises-container').append($block);
            
            // If data is passed (Cloning), populate the fields
            if (data) {
                $block.find('.exercise-select').val(data.exercise_id);
                
                // Show override section immediately
                $block.find('.exercise-override-section').removeClass('d-none');
                
                // Main fields
                $block.find('.pe_title').val(data.pe_title);
                $block.find('.pe_video_type').val(data.pe_video_type).trigger('change');
                $block.find('.pe_video_url').val(data.pe_video_url);
                $block.find('.pe_status').val(data.pe_status);
                $block.find('.pe_is_premium').prop('checked', data.pe_is_premium == 1);
                $block.find('.pe_type').val(data.pe_type);
                $block.find('.pe_based[value="'+data.pe_based+'"]').prop('checked', true).trigger('change');
                $block.find('.pe_seconds_per_rep').val(data.pe_seconds_per_rep);
                
                // Tabs
                if (data.pe_type === 'duration') {
                    $block.find('.nav-pills a[href="#duration_' + exerciseIndex + '"]').tab('show');
                } else {
                    $block.find('.nav-pills a[href="#sets_' + exerciseIndex + '"]').tab('show');
                }
                
                // Duration Fields
                $block.find('.pe_hours').val(data.pe_hours);
                $block.find('.pe_minute').val(data.pe_minute);
                $block.find('.pe_second').val(data.pe_second);
                
                // Sets Table
                let tbody = $block.find('.sets-table tbody');
                if (data.pe_reps && data.pe_reps.length > 0) {
                    data.pe_reps.forEach(function(rep, i) {
                        let rowHtml = `
                            <tr>
                                <td><input type="number" name="exercises[${exerciseIndex}][pe_reps][]" value="${rep}" class="form-control form-control-sm"></td>
                                <td><input type="number" name="exercises[${exerciseIndex}][pe_time][]" value="${data.pe_time[i] || ''}" class="form-control form-control-sm"></td>
                                <td><input type="number" name="exercises[${exerciseIndex}][pe_weight][]" value="${data.pe_weight[i] || ''}" class="form-control form-control-sm"></td>
                                <td><input type="number" name="exercises[${exerciseIndex}][pe_rest][]" value="${data.pe_rest[i] || ''}" class="form-control form-control-sm"></td>
                                <td><button type="button" class="btn btn-sm btn-danger remove-set">X</button></td>
                            </tr>
                        `;
                        tbody.append(rowHtml);
                    });
                }
            }
            
            // Initialize Select2 and other events ONLY AFTER values are set
            initBlock($block);

            // If data is passed, manually trigger Select2 update to prevent AJAX override
            if (data) {
                $block.find('.exercise-select').trigger('change.select2');
                
                // TinyMCE Content
                setTimeout(() => {
                    if (tinymce.get($block.find('.tinymce-instruction').attr('id'))) {
                        tinymce.get($block.find('.tinymce-instruction').attr('id')).setContent(data.pe_instruction || '');
                    }
                    if (tinymce.get($block.find('.tinymce-tips').attr('id'))) {
                        tinymce.get($block.find('.tinymce-tips').attr('id')).setContent(data.pe_tips || '');
                    }
                }, 100);
            }

            exerciseIndex++;
        }

        // Add empty block on button click
        $('#add-exercise-btn').on('click', function() {
            addExerciseBlock();
        });

        // --- NEW: CLONE PACKAGE LOGIC ---
        $('#clone-package-select').on('change', function() {
            let packageId = $(this).val();
            if (!packageId) return;

            if (confirm('This will overwrite current form data. Continue?')) {
                // We use a placeholder and replace it to avoid blade parse errors if the route has params
                let cloneDataUrl = "{{ isset($id) ? '#' : route('package.full-data', ':id') }}".replace(':id', packageId);
                
                $.get(cloneDataUrl, function(res) {
                    // 1. Populate Main Fields
                    $('input[name="name"]').val(res.package.name + ' (Copy)');
                    $('select[name="duration_unit"]').val(res.package.duration_unit).trigger('change');
                    $('select[name="duration"]').val(res.package.duration).trigger('change');
                    $('input[name="price"]').val(res.package.price);
                    $('input[name="follow_up_price"]').val(res.package.follow_up_price);
                    $('input[name="food_recognition_limit"]').val(res.package.food_recognition_limit);
                    $('select[name="status"]').val(res.package.status).trigger('change');
                    $('select[name="diet_id"]').val(res.package.diet_id).trigger('change');
                    $('select[name="advice_id"]').val(res.package.advice_id).trigger('change');
                    
                    if (tinymce.get('description')) {
                        tinymce.get('description').setContent(res.package.description || '');
                    } else {
                        $('textarea[name="description"]').val(res.package.description);
                    }

                    // 2. Clear Existing Exercises safely (Destroying TinyMCE first)
                    $('.exercise-block').each(function() {
                        $(this).find('textarea').each(function() { tinymce.remove(this); });
                        $(this).remove();
                    });
                    exerciseIndex = 0; // Reset index

                    // 3. Add new exercises from cloned package
                    if (res.exercises && res.exercises.length > 0) {
                        res.exercises.forEach(function(ex) {
                            addExerciseBlock(ex);
                        });
                    } else {
                        addExerciseBlock(); // add one empty if original had none
                    }
                    
                    // Reset dropdown
                    $('#clone-package-select').val(null).trigger('change');
                });
            } else {
                $(this).val(null).trigger('change');
            }
        });

        // Remove block
        $(document).on('click', '.remove-exercise', function() {
            if(confirm('Are you sure you want to remove this exercise?')) {
                let $block = $(this).closest('.exercise-block');
                // Destroy tinymce instances before removing
                $block.find('textarea').each(function() {
                    tinymce.remove(this);
                });
                $block.remove();
            }
        });

        // Initialize existing blocks on load
        $('.exercise-block').each(function() {
            initBlock($(this));
        });

        // Trigger add button if empty on create
        if ($('#exercises-container').children().length === 0 && !{{ isset($id) ? 'true' : 'false' }}) {
            addExerciseBlock();
        }
    })(jQuery);

</script>
@endpush

<x-app-layout :assets="$assets ?? []">
    <div>
        @if(isset($id))
            {{ html()->modelForm($data, 'PATCH', route('package.update', $id) )->attribute('enctype', 'multipart/form-data')->open() }}
        @else
            {{ html()->form('POST', route('package.store'))->attribute('enctype', 'multipart/form-data')->open() }} 
        @endif
        
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $pageTitle }}</h4>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('package.index') }} " class="btn btn-sm btn-primary" role="button">{{ __('message.back') }}</a>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- MAIN PACKAGE FIELDS -->
                         @if(!isset($id))
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <div class="alert alert-light d-flex align-items-center justify-content-between">
                                    <div>
                                        <strong><i class="fas fa-copy"></i> Quick Add:</strong> Copy an existing program to use as a template.

                                    </div>
                                    <div style="width: 300px;">
                                        <select id="clone-package-select" class="form-control select2js">
                                            <option value="">-- Select a Program to Clone --</option>
                                            @foreach($existingPackages as $pkgId => $pkgName)
                                                <option value="{{ $pkgId }}">{{ $pkgName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif
                        <div class="row">
                            <div class="form-group col-md-4">
                                {{ html()->label(__('message.name') . ' <span class="text-danger">*</span>', 'name')->class('form-control-label') }}
                                {{ html()->text('name')->placeholder(__('message.name'))->class('form-control')->attribute('required','required') }}
                            </div>
                            <div class="form-group col-md-4">
                                {{ html()->label(__('message.duration_unit'))->class('form-control-label') }}
                                {{ html()->select('duration_unit',[ 'monthly' => __('message.monthly'), 'yearly' => __('message.yearly') ], old('duration_unit'))->class('form-control select2js')->attribute('required', 'required') }}
                            </div>
                            <div class="form-group col-md-4">
                                {{ html()->label(__('message.duration').' <span class="text-danger">*</span>')->class('form-control-label') }}
                                {{ html()->select('duration', ['1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '10' => '10', '11' => '11', '12' => '12' ], old('duration'))->class('form-control select2js')->attribute('required', 'required') ->id('duration') }}
                            </div>
                            <div class="form-group col-md-4">
                                {{ html()->label(__('message.diet'))->class('form-control-label') }}
                                {{ html()->select('diet_id', $diets, old('diet_id'))->class('form-control select2js')->attribute('required', 'required') }}
                            </div>
                            <div class="form-group col-md-4">
                                {{ html()->label(__('message.advice'))->class('form-control-label') }}
                                {{ html()->select('advice_id', $advices, old('advice_id'))->class('form-control select2js')->attribute('required', 'required') }}
                            </div>
                            <div class="form-group col-md-4">
                                {{ html()->label(__('message.price').' <span class="text-danger">(AED)*</span>')->class('form-control-label') }}
                                {{ html()->number('price', old('price'))->class('form-control')->attribute('min', 0)->attribute('step', 'any')->attribute('required', 'required')->placeholder(__('message.price')) }}
                            </div>
                            <div class="form-group col-md-4">
                                {{ html()->label(__('message.follow_up_price').' <span class="text-danger">(AED)*</span>')->class('form-control-label') }}
                                {{ html()->number('follow_up_price', old('follow_up_price'))->class('form-control')->attribute('min', 0)->attribute('step', 'any')->attribute('required', 'required')->placeholder(__('message.follow_up_price')) }}
                            </div>
                            <div class="form-group col-md-4">
                                {{ html()->label(__('message.users'))->class('form-control-label') }}
                                {{ html()->select('users[]', $users, $selectedUsers)->class('form-control select2js')->attribute('multiple', 'multiple') }}
                            </div>
                            <div class="form-group col-md-4">
                                {{ html()->label(__('message.food_recognition_limit').' <span class="text-danger">*</span>', 'food_recognition_limit')->class('form-control-label') }}
                                {{ html()->number('food_recognition_limit', old('food_recognition_limit'))->class('form-control')->attribute('min', 0)->attribute('step', 'any')->attribute('required', 'required')->placeholder(__('message.food_recognition_limit')) }}
                            </div>
                            <div class="form-group col-md-4">
                                {{ html()->label(__('message.status') . ' <span class="text-danger">*</span>', 'status')->class('form-control-label') }}
                                {{ html()->select('status',[ 'active' => __('message.active'), 'inactive' => __('message.inactive') ], old('status'))->class('form-control select2js')->attribute('required', 'required') }}
                            </div>
                            <div class="form-group col-md-12">
                                {{ html()->label(__('message.description'))->class('form-control-label') }}
                                {{ html()->textarea('description', null)->class('form-control tinymce-description')->placeholder(__('message.description')) }}
                            </div>
                        </div>

                        <!-- MULTIPLE EXERCISES SECTION -->
                        <hr>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h4>{{ __('message.program_exercise') }}</h4>
                            <button type="button" id="add-exercise-btn" class="btn btn-sm btn-success">+ Add Exercise</button>
                        </div>
                        
                        <div id="exercises-container">
                            <!-- Render existing exercises (Edit Mode) -->
                            @foreach($existingExercises as $index => $ex)
                                <div class="exercise-block border p-3 mb-3 bg-light rounded" data-index="{{ $index }}">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h5 class="mb-0 text-primary">Exercise #{{ $index + 1 }}</h5>
                                        <button type="button" class="btn btn-sm btn-danger remove-exercise">Remove</button>
                                    </div>
                                    <div class="row">
                                        <div class="form-group col-md-6">
                                            <label class="form-control-label">Select Exercise <span class="text-danger">*</span></label>
                                            <select name="exercises[{{ $index }}][exercise_id]" class="form-control select2js exercise-select" required>
                                                <option value="">Select</option>
                                                @foreach($exercises as $exId => $exTitle)
                                                    <option value="{{ $exId }}" {{ ($ex['exercise_id'] ?? '') == $exId ? 'selected' : '' }}>{{ $exTitle }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    
                                    <div class="exercise-override-section {{ empty($ex['exercise_id']) ? 'd-none' : '' }} mt-3">
                                        <div class="row">
                                            <div class="form-group col-md-4">
                                                <label class="form-control-label">Title Override</label>
                                                <input type="text" name="exercises[{{ $index }}][pe_title]" value="{{ $ex['pe_title'] ?? '' }}" class="form-control pe_title">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="form-control-label">Video Type</label>
                                                <select name="exercises[{{ $index }}][pe_video_type]" class="form-control pe_video_type">
                                                    <option value="url" {{ ($ex['pe_video_type'] ?? '') == 'url' ? 'selected' : '' }}>URL</option>
                                                    <option value="upload_video" {{ ($ex['pe_video_type'] ?? '') == 'upload_video' ? 'selected' : '' }}>Upload Video</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4 pe-video-url" style="{{ ($ex['pe_video_type'] ?? 'url') == 'upload_video' ? 'display:none;' : '' }}">
                                                <label class="form-control-label">Video URL</label>
                                                <input type="text" name="exercises[{{ $index }}][pe_video_url]" value="{{ $ex['pe_video_url'] ?? '' }}" class="form-control pe_video_url">
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="form-control-label">Status</label>
                                                <select name="exercises[{{ $index }}][pe_status]" class="form-control pe_status">
                                                    <option value="active" {{ ($ex['pe_status'] ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="inactive" {{ ($ex['pe_status'] ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </div>
                                            <div class="form-group col-md-4">
                                                <label class="form-control-label d-block">Is Premium</label>
                                                <div class="form-check form-switch mt-2">
                                                    <input type="hidden" name="exercises[{{ $index }}][pe_is_premium]" value="0">
                                                    <input type="checkbox" name="exercises[{{ $index }}][pe_is_premium]" value="1" class="form-check-input pe_is_premium" {{ !empty($ex['pe_is_premium']) ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>

                                        <ul class="nav nav-pills nav-fill mb-3 exercise-tab" role="tablist">
                                            <li class="nav-item"><a class="nav-link {{ ($ex['pe_type'] ?? 'sets') == 'sets' ? 'active' : '' }}" data-bs-toggle="tab" href="#sets_{{ $index }}" data-type="sets">Sets</a></li>
                                            <li class="nav-item"><a class="nav-link {{ ($ex['pe_type'] ?? '') == 'duration' ? 'active' : '' }}" data-bs-toggle="tab" href="#duration_{{ $index }}" data-type="duration">Duration</a></li>
                                        </ul>
                                        <input type="hidden" name="exercises[{{ $index }}][pe_type]" class="pe_type" value="{{ $ex['pe_type'] ?? 'sets' }}">

                                        <div class="tab-content">
                                            <div id="sets_{{ $index }}" class="tab-pane fade {{ ($ex['pe_type'] ?? 'sets') == 'sets' ? 'show active' : '' }}">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="form-check">
                                                            <input type="radio" name="exercises[{{ $index }}][pe_based]" value="reps" class="form-check-input pe_based" {{ ($ex['pe_based'] ?? 'reps') == 'reps' ? 'checked' : '' }}>
                                                            <label class="form-check-label">Reps</label>
                                                        </div>
                                                        <div class="form-check">
                                                            <input type="radio" name="exercises[{{ $index }}][pe_based]" value="time" class="form-check-input pe_based" {{ ($ex['pe_based'] ?? '') == 'time' ? 'checked' : '' }}>
                                                            <label class="form-check-label">Time</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-4 pe-reps-time-row" style="{{ ($ex['pe_based'] ?? 'reps') == 'time' ? 'display:none;' : '' }}">
                                                        <input type="number" name="exercises[{{ $index }}][pe_seconds_per_rep]" value="{{ $ex['pe_seconds_per_rep'] ?? '' }}" class="form-control pe_seconds_per_rep" placeholder="Seconds per rep">
                                                    </div>
                                                    <div class="col-md-4 text-end">
                                                        <button type="button" class="btn btn-sm btn-primary add-set-btn">Add Set</button>
                                                    </div>
                                                </div>
                                                <table class="table table-sm mt-2 sets-table">
                                                    <thead><tr><th>Reps</th><th>Time</th><th>Weight</th><th>Rest</th><th></th></tr></thead>
                                                    <tbody>
                                                        @if(!empty($ex['pe_reps']))
                                                            @foreach($ex['pe_reps'] as $k => $rep)
                                                                <tr>
                                                                    <td><input type="number" name="exercises[{{ $index }}][pe_reps][]" value="{{ $rep }}" class="form-control form-control-sm"></td>
                                                                    <td><input type="number" name="exercises[{{ $index }}][pe_time][]" value="{{ $ex['pe_time'][$k] ?? '' }}" class="form-control form-control-sm"></td>
                                                                    <td><input type="number" name="exercises[{{ $index }}][pe_weight][]" value="{{ $ex['pe_weight'][$k] ?? '' }}" class="form-control form-control-sm"></td>
                                                                    <td><input type="number" name="exercises[{{ $index }}][pe_rest][]" value="{{ $ex['pe_rest'][$k] ?? '' }}" class="form-control form-control-sm"></td>
                                                                    <td><button type="button" class="btn btn-sm btn-danger remove-set">X</button></td>
                                                                </tr>
                                                            @endforeach
                                                        @endif
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div id="duration_{{ $index }}" class="tab-pane fade {{ ($ex['pe_type'] ?? '') == 'duration' ? 'show active' : '' }}">
                                                <div class="row">
                                                    <div class="col-md-4"><input type="number" name="exercises[{{ $index }}][pe_hours]" value="{{ $ex['pe_hours'] ?? '' }}" class="form-control pe_hours" placeholder="Hours"></div>
                                                    <div class="col-md-4"><input type="number" name="exercises[{{ $index }}][pe_minute]" value="{{ $ex['pe_minute'] ?? '' }}" class="form-control pe_minute" placeholder="Minutes"></div>
                                                    <div class="col-md-4"><input type="number" name="exercises[{{ $index }}][pe_second]" value="{{ $ex['pe_second'] ?? '' }}" class="form-control pe_second" placeholder="Seconds"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row mt-3">
                                            <div class="col-md-6">
                                                <label class="form-control-label">Instruction</label>
                                                <textarea name="exercises[{{ $index }}][pe_instruction]" class="form-control tinymce-instruction">{{ $ex['pe_instruction'] ?? '' }}</textarea>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-control-label">Tips</label>
                                                <textarea name="exercises[{{ $index }}][pe_tips]" class="form-control tinymce-tips">{{ $ex['pe_tips'] ?? '' }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <hr>
                        {{ html()->submit( __('message.save') )->class('btn btn-md btn-primary float-end') }}
                    </div>
                </div>
            </div>
        </div>
        @if(isset($id))
            {{ html()->closeModelForm() }}
        @else
            {{ html()->form()->close() }}
        @endif
    </div>
</x-app-layout>