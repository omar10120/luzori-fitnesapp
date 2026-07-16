<x-app-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null; ?>
        @if(isset($id))
            {{ html()->modelForm($data, 'PATCH', route('advice.update', $id))->open() }}
        @else
            {{ html()->form('POST', route('advice.store'))->open() }}
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $pageTitle }}</h4>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('advice.index') }}" class="btn btn-sm btn-primary" role="button">{{ __('message.back') }}</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                {{ html()->label(__('message.name') . ' <span class="text-danger">*</span>', 'name')->class('form-control-label') }}
                                {{ html()->text('name')->placeholder(__('message.name'))->class('form-control')->attribute('required', 'required') }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ html()->label(__('message.status'))->class('form-control-label') }}
                                <div class="form-check form-switch mt-2">
                                    {{ html()->hidden('status', 0) }}
                                    {{ html()->checkbox('status', old('status', isset($data) ? $data->status : 1), 1)->class('form-check-input')->id('status') }}
                                    <label class="form-check-label" for="status">{{ __('message.active') }}</label>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                {{ html()->label(__('message.seed_text'), 'seed_text')->class('form-control-label') }}
                                {{ html()->textarea('seed_text', null)->class('form-control')->rows(5)->placeholder(__('message.seed_text')) }}
                            </div>
                            <div class="form-group col-md-12">
                                {{ html()->label(__('message.needed_advices'))->class('form-control-label') }}
                                <div class="row mt-2">
                                    @forelse($options as $option)
                                        @php
                                            $checked = old('options')
                                                ? in_array($option->id, (array) old('options'))
                                                : (isset($data) ? $data->options->contains($option->id) : false);
                                        @endphp
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                {{ html()->checkbox('options[]', $checked, $option->id)->class('form-check-input')->id('option-'.$option->id) }}
                                                <label class="form-check-label" for="option-{{ $option->id }}">
                                                    {{ $option->label }}
                                                    @if($option->description)
                                                        <small class="text-muted d-block">{{ $option->description }}</small>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12">
                                            <p class="text-muted mb-0">{{ __('message.not_found_entry', ['name' => __('message.needed_advices')]) }}</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                        <hr>
                        {{ html()->submit(__('message.save'))->class('btn btn-md btn-primary float-end') }}
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
