{{ html()->form('GET')->open() }}
    <div class="row">
        <div class="form-group col-md-3">
            {{ html()->label(__('message.select_name', ['select' => __('message.language')]), 'language')->class('form-control-label') }}
            {{ html()->select('language', isset($language) ? [$language->id => $language->language_name] : [], old('language'))
                ->class('select2Clear form-group language')
                ->attribute('data-placeholder', __('message.select_name', ['select' => __('message.language')]))
                ->attribute('data-ajax--url', route('ajax-list', ['type' => 'languagetable']))
            }}
        </div>
        <div class="form-group col-md-3">
            {{ html()->label(__('message.select_name', ['select' => __('message.keyword')]), 'keyword')->class('form-control-label') }}
            {{ html()->select('keyword', isset($keyword) ? [$keyword->keyword_id => $keyword->keyword_name] : [], old('keyword'))
                ->class('select2Clear form-group keyword')
                ->attribute('data-placeholder', __('message.select_name', ['select' => __('message.keyword')]))
                ->attribute('data-ajax--url', route('ajax-list', ['type' => 'defaultkeyword']))
            }}
        </div>
        <div class="form-group col-md-3">
            {{ html()->label(__('message.select_name', ['select' => __('message.screen')]), 'screen')->class('form-control-label') }}
            {{ html()->select('screen', isset($screen) ? [$screen->screenId => $screen->screenName] : [], old('screen'))
                ->class('select2Clear form-group screen')
                ->attribute('data-placeholder', __('message.select_name', ['select' => __('message.screen')]))
                ->attribute('data-ajax--url', route('ajax-list', ['type' => 'screen']))
            }}
        </div>        
        <div class="form-group col-md-3 mt-2"> 
            <button class="btn btn-sm btn-primary text-white mt-3 pt-2 pb-2">{{ __('message.apply_filter') }}</button>
                @if(isset($reset_file_button))
                    {!! $reset_file_button !!}
                @endif
        </div>
    </div>
@if(isset($id))
    {{ html()->closeModelForm() }}
@else
    {{ html()->form()->close() }}
@endif
