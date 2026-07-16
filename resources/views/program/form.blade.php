<x-app-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null; ?>
        @if(isset($id))
            {{ html()->modelForm($data, 'PATCH', route('program.update', $id))->open() }}
        @else
            {{ html()->form('POST', route('program.store'))->open() }}
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $pageTitle }}</h4>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('program.index') }}" class="btn btn-sm btn-primary" role="button">{{ __('message.back') }}</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                {{ html()->label(__('message.name') . ' <span class="text-danger">*</span>', 'name')->class('form-control-label') }}
                                {{ html()->text('name')->placeholder(__('message.name'))->class('form-control')->attribute('required', 'required') }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ html()->label(__('message.price'), 'price')->class('form-control-label') }}
                                {{ html()->number('price')->attribute('step', '0.01')->attribute('min', '0')->placeholder(__('message.price'))->class('form-control') }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ html()->label(__('message.diet'))->class('form-control-label') }}
                                {{ html()->select('diet_id', $diets ?? [], old('diet_id'))
                                    ->class('form-control select2js')
                                    ->placeholder(__('message.select_name', ['select' => __('message.diet')])) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ html()->label(__('message.advice'))->class('form-control-label') }}
                                {{ html()->select('advice_id', $advices ?? [], old('advice_id'))
                                    ->class('form-control select2js')
                                    ->placeholder(__('message.select_name', ['select' => __('message.advice')])) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ html()->label(__('message.duration'), 'duration')->class('form-control-label') }}
                                {{ html()->text('duration')->placeholder(__('message.duration'))->class('form-control') }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ html()->label(__('message.status'))->class('form-control-label') }}
                                <div class="form-check form-switch mt-2">
                                    {{ html()->hidden('status', 0) }}
                                    {{ html()->checkbox('status', old('status', isset($data) ? $data->status : 1), 1)->class('form-check-input')->id('status') }}
                                    <label class="form-check-label" for="status">{{ __('message.active') }}</label>
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
