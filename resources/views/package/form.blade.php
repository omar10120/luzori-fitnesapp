@php
    $id = $id ?? null;
    $packageExercise = $packageExercise ?? (isset($data) ? ($data->packageExercise ?? null) : null);
    $peType = old('pe_type', isset($packageExercise) && $packageExercise->type != null ? $packageExercise->type : 'sets');
    $peBased = old('pe_based', isset($packageExercise) && $packageExercise->based != null ? $packageExercise->based : 'reps');
@endphp
@push('scripts')
    <script>
        (function($) {
            $(document).ready(function(){
                tinymceEditor('.tinymce-description',' ',function (ed) {
                }, 450)

                if ($('.tinymce-pe-instruction').length) {
                    tinymceEditor('.tinymce-pe-instruction',' ',function (ed) {}, 450)
                }
                if ($('.tinymce-pe-tips').length) {
                    tinymceEditor('.tinymce-pe-tips',' ',function (ed) {}, 450)
                }

                var videoType = $('select[name=pe_video_type]').val();
                togglePeVideoFields(videoType);

                $('select[name=pe_video_type]').on('change', function () {
                    togglePeVideoFields(this.value);
                });

                function togglePeVideoFields(type) {
                    if (type === 'url') {
                        $('.pe-video-url').removeClass('d-none');
                    } else {
                        $('.pe-video-url').addClass('d-none');
                    }
                }

                var row = 0;
                $('#pe_add_button').on('click', function () {
                    var tableBody = $('#pe_table_list').find('tbody');
                    var trLast = tableBody.find('tr:last');
                    var trNew = trLast.clone();
                    row = parseInt(trNew.attr('row') || 0, 10);
                    row++;

                    trNew.attr('id', 'pe_row_' + row).attr('data-id', 0).attr('row', row);
                    trNew.find('input').val('');
                    trNew.find('[id^="pe_remove_"]').attr('id', 'pe_remove_' + row).attr('row', row);

                    trLast.after(trNew);
                });

                $(document).on('click', '.pe-removebtn', function () {
                    var rowId = $(this).attr('row');
                    var deleteRow = $('#pe_row_' + rowId);
                    var totalRow = $('#pe_table_list tbody tr').length;
                    var userResponse = confirm("{{ __('message.delete_msg') }}");
                    if (!userResponse) {
                        return false;
                    }

                    if (totalRow == 1) {
                        $(document).find('#pe_add_button').trigger('click');
                    }
                    deleteRow.remove();
                });

                var peType = @json($peType);
                changePeTabValue(peType);

                if (peType == 'sets') {
                    var peBased = @json($peBased);
                    togglePeBasedColumns(peBased);
                }

                $('#pe-exercise-pills-tab').on('show.bs.tab', function (e) {
                    changePeTabValue($(e.target).attr('data-type'));
                });

                $(document).on('change', 'input[name="pe_based"]', function () {
                    togglePeBasedColumns($(this).val());
                });

                function changePeTabValue(type) {
                    $('input[name=pe_type]').val(type);
                }

                function togglePeBasedColumns(based) {
                    if (based === 'time') {
                        $('.pe-reps-time-row').addClass('d-none');
                    } else {
                        $('.pe-reps-time-row').removeClass('d-none');
                    }
                }

                $(document).on('click', '#pe_sets_clear', function () {
                    $('#pe_table_list tbody').find('input').val('');
                });

                $(document).on('click', '#pe_duration_clear', function () {
                    $('#pe_hours').val(null).trigger('change');
                    $('#pe_minute').val(null).trigger('change');
                    $('#pe_second').val(null).trigger('change');
                });
            });
        })(jQuery);
    </script>
@endpush

<x-app-layout :assets="$assets ?? []">
    <div>
        <?php
            $id = $id ?? null;
            $packageExercise = $packageExercise ?? ($data->packageExercise ?? null);
            $selectedUsers = old('users', isset($data) ? $data->users()->pluck('users.id')->toArray() : []);
            $peDuration = isset($packageExercise) && $packageExercise->duration != null ? explode(':', $packageExercise->duration) : null;
            $peType = old('pe_type', isset($packageExercise) && $packageExercise->type != null ? $packageExercise->type : 'sets');
            $peBased = old('pe_based', isset($packageExercise) && $packageExercise->based != null ? $packageExercise->based : 'reps');
            $peSets = old('pe_reps')
                ? collect(old('pe_reps'))->map(function ($reps, $i) {
                    return [
                        'reps' => $reps,
                        'time' => old('pe_time.' . $i),
                        'weight' => old('pe_weight.' . $i),
                        'rest' => old('pe_rest.' . $i),
                    ];
                })->all()
                : (isset($packageExercise) ? ($packageExercise->sets ?? []) : []);
        ?>
        @if(isset($id))
            {{ html()->modelForm($data, 'PATCH', route('package.update', $id) )->attribute('enctype', 'multipart/form-data')->open() }}
            @if($packageExercise)
                {{ html()->hidden('pe_type', $peType) }}
            @endif
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
                                {{ html()->label(__('message.exercise'))->class('form-control-label') }}
                                {{ html()->select('exercise_id', $exercises, old('exercise_id'))->class('form-control select2js')->attribute('required', 'required') }}
                                @if(isset($id))
                                    <small class="text-muted">{{ __('message.package_exercise_replace_note') }}</small>
                                @else
                                    <small class="text-muted">{{ __('message.package_exercise_clone_note') }}</small>
                                @endif
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

                        @if(isset($id) && $packageExercise)
                            <hr>
                            <h5 class="mb-3">{{ __('message.package_exercise') }}</h5>
                            <div class="row">
                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.title'), 'pe_title')->class('form-control-label') }}
                                    {{ html()->text('pe_title', old('pe_title', $packageExercise->title))->class('form-control')->placeholder(__('message.title')) }}
                                </div>
                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.video_type'), 'pe_video_type')->class('form-control-label') }}
                                    {{ html()->select('pe_video_type', ['url' => __('message.url'), 'upload_video' => __('message.upload_video')], old('pe_video_type', $packageExercise->video_type))->class('form-control select2js') }}
                                </div>
                                <div class="form-group col-md-4 pe-video-url">
                                    {{ html()->label(__('message.video_url'), 'pe_video_url')->class('form-control-label') }}
                                    {{ html()->text('pe_video_url', old('pe_video_url', $packageExercise->video_url))->class('form-control')->placeholder(__('message.video_url')) }}
                                </div>
                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.status'), 'pe_status')->class('form-control-label') }}
                                    {{ html()->select('pe_status', ['active' => __('message.active'), 'inactive' => __('message.inactive')], old('pe_status', $packageExercise->status))->class('form-control select2js') }}
                                </div>
                                <div class="form-group col-md-4">
                                    {{ html()->label(__('message.is_premium'), 'pe_is_premium')->class('form-control-label') }}
                                    <div class="form-check">
                                        {!! html()->hidden('pe_is_premium', 0)->class('form-check-input') !!}
                                        {!! html()->checkbox('pe_is_premium', (bool) old('pe_is_premium', $packageExercise->is_premium), 1)->id('pe_is_premium')->class('form-check-input') !!}
                                    </div>
                                </div>
                            </div>

                            <h5 class="text-danger mt-3"><i><u>{{ __('message.notes')}}:</u></i> {{ __('message.exercise_info') }}</h5>
                            <hr>

                            <ul class="d-flex nav nav-pills nav-fill mb-3 text-center exercise-tab" id="pe-exercise-pills-tab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link {{ $peType == 'sets' ? 'active show' : '' }}" data-bs-toggle="tab" href="#pe-exercise-sets" data-type="sets" role="tab" aria-selected="{{ $peType == 'sets' ? 'true' : 'false' }}">{{ __('message.sets') }}</a>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <a class="nav-link {{ $peType == 'duration' ? 'active show' : '' }}" data-bs-toggle="tab" href="#pe-exercise-duration" data-type="duration" role="tab" aria-selected="{{ $peType == 'duration' ? 'true' : 'false' }}" tabindex="-1">{{ __('message.duration') }}</a>
                                </li>
                            </ul>

                            <div class="exercise-content tab-content">
                                <div id="pe-exercise-sets" class="tab-pane fade {{ $peType == 'sets' ? 'active show' : '' }}" role="tabpanel">
                                    <div class="row pe-normal-row">
                                        <div class="col-md-2">
                                            <h5 class="mb-3">{{__('message.sets')}}</h5>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-check">
                                                <div class="custom-control custom-radio d-inline-block col-4">
                                                    <label class="form-check-label" for="pe-based-reps"> {{__('message.reps')}}(x)</label>
                                                    {{ html()->radio('pe_based', $peBased == 'reps', 'reps')->class('form-check-input')->id('pe-based-reps') }}
                                                </div>
                                                <div class="custom-control custom-radio d-inline-block col-4">
                                                    <label class="form-check-label" for="pe-based-time"> {{__('message.time')}}(s)</label>
                                                    {{ html()->radio('pe_based', $peBased == 'time', 'time')->class('form-check-input')->id('pe-based-time') }}
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group pe-reps-time-row">
                                                <div class="input-group input-group-sm">
                                                    {{ html()->number('pe_seconds_per_rep', old('pe_seconds_per_rep', $packageExercise->seconds_per_rep))->placeholder(__('message.seconds_per_rep'))->class('form-control')->attribute('min', 0) }}
                                                    <span class="input-group-text text-danger" data-bs-toggle="tooltip" title="{{ __('message.seconds_per_rep_help') }}">
                                                        <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M16.334 2.75H7.665C4.644 2.75 2.75 4.889 2.75 7.916V16.084C2.75 19.111 4.635 21.25 7.665 21.25H16.333C19.364 21.25 21.25 19.111 21.25 16.084V7.916C21.25 4.889 19.364 2.75 16.334 2.75Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                            <path d="M11.9946 16V12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                            <path d="M11.9896 8.2041H11.9996" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" id="pe_add_button" class="btn btn-sm btn-primary float-end me-2">{{ __('message.add',['name' => '']) }}</button>
                                            <a id="pe_sets_clear" class="float-end me-2" href="javascript:void(0)" title="{{ __('message.clear_sets') }}">{{ __('message.l_clear') }}</a>
                                        </div>
                                        <div class="col-md-12">
                                            <table id="pe_table_list" class="table table-responsive">
                                                <thead>
                                                    <tr>
                                                        <th class="col-md-3">{{ __('message.reps') }}<span>(x)</span></th>
                                                        <th class="col-md-3">{{ __('message.time') }}(s)</th>
                                                        <th class="col-md-3 weight">{{ __('message.weight') }}<span>(kg)</span></th>
                                                        <th class="col-md-3">{{ __('message.rest') }}<span>(s)</span></th>
                                                        <th class="col-md-1">{{ __('message.action') }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @if(!empty($peSets) && count($peSets) > 0)
                                                        @foreach($peSets as $key => $field)
                                                            <tr id="pe_row_{{ $key }}" row="{{ $key }}" data-id="{{ $key }}">
                                                                <td>
                                                                    <div class="form-group">
                                                                        {{ html()->number('pe_reps[]', $field['reps'] ?? null)->placeholder(__('message.reps'))->class('form-control')->attribute('min', 0) }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        {{ html()->number('pe_time[]', $field['time'] ?? null)->placeholder(__('message.time'))->class('form-control')->attribute('min', 0) }}
                                                                    </div>
                                                                </td>
                                                                <td class="weight">
                                                                    <div class="form-group">
                                                                        {{ html()->number('pe_weight[]', $field['weight'] ?? null)->placeholder(__('message.weight'))->class('form-control')->attribute('min', 0) }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <div class="form-group">
                                                                        {{ html()->number('pe_rest[]', $field['rest'] ?? null)->placeholder(__('message.rest'))->class('form-control')->attribute('min', 0) }}
                                                                    </div>
                                                                </td>
                                                                <td>
                                                                    <a href="javascript:void(0)" id="pe_remove_{{ $key }}" class="pe-removebtn btn btn-sm btn-icon btn-danger" row="{{ $key }}">
                                                                        <span class="btn-inner">
                                                                            <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor">
                                                                                <path d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                                <path d="M20.708 6.23975H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                                <path d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.80258 6.23973 7.01758 6.23973" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                            </svg>
                                                                        </span>
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr id="pe_row_0" row="0" data-id="0">
                                                            <td>
                                                                <div class="form-group">
                                                                    {{ html()->number('pe_reps[]', null)->placeholder(__('message.reps'))->class('form-control')->attribute('min', 0) }}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">
                                                                    {{ html()->number('pe_time[]', null)->placeholder(__('message.time'))->class('form-control')->attribute('min', 0) }}
                                                                </div>
                                                            </td>
                                                            <td class="weight">
                                                                <div class="form-group">
                                                                    {{ html()->number('pe_weight[]', null)->placeholder(__('message.weight'))->class('form-control')->attribute('min', 0) }}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="form-group">
                                                                    {{ html()->number('pe_rest[]', null)->placeholder(__('message.rest'))->class('form-control')->attribute('min', 0) }}
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <a href="javascript:void(0)" id="pe_remove_0" class="pe-removebtn btn btn-sm btn-icon btn-danger" row="0">
                                                                    <span class="btn-inner">
                                                                        <svg width="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" stroke="currentColor">
                                                                            <path d="M19.3248 9.46826C19.3248 9.46826 18.7818 16.2033 18.4668 19.0403C18.3168 20.3953 17.4798 21.1893 16.1088 21.2143C13.4998 21.2613 10.8878 21.2643 8.27979 21.2093C6.96079 21.1823 6.13779 20.3783 5.99079 19.0473C5.67379 16.1853 5.13379 9.46826 5.13379 9.46826" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                            <path d="M20.708 6.23975H3.75" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                            <path d="M17.4406 6.23973C16.6556 6.23973 15.9796 5.68473 15.8256 4.91573L15.5826 3.69973C15.4326 3.13873 14.9246 2.75073 14.3456 2.75073H10.1126C9.53358 2.75073 9.02558 3.13873 8.87558 3.69973L8.63258 4.91573C8.47858 5.68473 7.01758 6.23973" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
                                                                        </svg>
                                                                    </span>
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div id="pe-exercise-duration" class="tab-pane fade {{ $peType == 'duration' ? 'active show' : '' }}" role="tabpanel">
                                    <div class="row pe-duration-row">
                                        <h5 class="mb-3">{{__('message.duration')}}
                                            <a id="pe_duration_clear" class="float-end" href="javascript:void(0)" title="{{ __('message.clear_duration') }}">{{ __('message.l_clear') }}</a>
                                        </h5>
                                        <div class="form-group col-md-2">
                                            {{ html()->label(__('message.hours'))->class('form-control-label')}}
                                            {{ html()->select('pe_hours', isset($peDuration[0]) ? [ $peDuration[0] => $peDuration[0] ] : [], old('pe_hours', $peDuration[0] ?? null))
                                                ->id('pe_hours')
                                                ->class('form-control select2js')
                                                ->attribute('data-placeholder', __('message.select_name', ['select' => __('message.hours')]))
                                                ->attribute('data-ajax--url', route('ajax-list', ['type' => 'hours']))
                                            }}
                                        </div>
                                        <div class="form-group col-md-2">
                                            {{ html()->label(__('message.minute'))->class('form-control-label')}}
                                            {{ html()->select('pe_minute', isset($peDuration[1]) ? [$peDuration[1] => $peDuration[1]] : [], old('pe_minute', $peDuration[1] ?? null))
                                                ->id('pe_minute')
                                                ->class('form-control select2js')
                                                ->attribute('data-placeholder', __('message.select_name', ['select' => __('message.minute')]))
                                                ->attribute('data-ajax--url', route('ajax-list', ['type' => 'minute']))
                                            }}
                                        </div>
                                        <div class="form-group col-md-2">
                                            {{ html()->label(__('message.second'))->class('form-control-label')}}
                                            {{ html()->select('pe_second', isset($peDuration[2]) ? [$peDuration[2] => $peDuration[2]] : [], old('pe_second', $peDuration[2] ?? null))
                                                ->id('pe_second')
                                                ->class('form-control select2js')
                                                ->attribute('data-placeholder', __('message.select_name', ['select' => __('message.second')]))
                                                ->attribute('data-ajax--url', route('ajax-list', ['type' => 'second']))
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <div class="form-group col-md-12">
                                {{ html()->label(__('message.instruction'), 'pe_instruction')->class('form-control-label') }}
                                {{ html()->textarea('pe_instruction', old('pe_instruction', $packageExercise->instruction))->class('form-control tinymce-pe-instruction') }}
                            </div>
                            <div class="form-group col-md-12">
                                {{ html()->label(__('message.tips'), 'pe_tips')->class('form-control-label') }}
                                {{ html()->textarea('pe_tips', old('pe_tips', $packageExercise->tips))->class('form-control tinymce-pe-tips') }}
                            </div>
                        @endif

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
