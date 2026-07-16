<x-app-layout :assets="$assets ?? []">
    <div>
        <?php $id = $id ?? null; ?>
        @if(isset($id))
            {{ html()->modelForm($data, 'PATCH', route('adviceoption.update', $id))->open() }}
        @else
            {{ html()->form('POST', route('adviceoption.store'))->open() }}
        @endif
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">{{ $pageTitle }}</h4>
                        </div>
                        <div class="card-action">
                            <a href="{{ route('adviceoption.index') }}" class="btn btn-sm btn-primary" role="button">{{ __('message.back') }}</a>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                {{ html()->label(__('message.key') . ' <span class="text-danger">*</span>', 'key')->class('form-control-label') }}
                                {{ html()->text('key')->placeholder(__('message.key'))->class('form-control')->attribute('required', 'required') }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ html()->label(__('message.label') . ' <span class="text-danger">*</span>', 'label')->class('form-control-label') }}
                                {{ html()->text('label')->placeholder(__('message.label'))->class('form-control')->attribute('required', 'required') }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ html()->label(__('message.order'), 'order')->class('form-control-label') }}
                                {{ html()->number('order')->attribute('min', '0')->placeholder(__('message.order'))->class('form-control')->value(old('order', isset($data) ? $data->order : 0)) }}
                            </div>
                            <div class="form-group col-md-6">
                                {{ html()->label(__('message.status'))->class('form-control-label') }}
                                <div class="form-check form-switch mt-2">
                                    {{ html()->hidden('is_active', 0) }}
                                    {{ html()->checkbox('is_active', old('is_active', isset($data) ? $data->is_active : 1), 1)->class('form-check-input')->id('is_active') }}
                                    <label class="form-check-label" for="is_active">{{ __('message.active') }}</label>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                {{ html()->label(__('message.description'), 'description')->class('form-control-label') }}
                                {{ html()->textarea('description', null)->class('form-control')->rows(4)->placeholder(__('message.description')) }}
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
