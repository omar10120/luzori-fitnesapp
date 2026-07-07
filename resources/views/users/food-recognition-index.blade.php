<x-app-layout :assets="$assets ?? []">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <div class="header-title">
                            <h4 class="card-title">{{ $pageTitle }}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ __('message.image') }}</th>
                                        <th>{{ __('message.user') }}</th>
                                        <th>{{ __('message.title') }}</th>
                                        <th>{{ __('message.status') }}</th>
                                        <th>{{ __('message.date') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($data->count() > 0)
                                        @foreach($data as $item)
                                            <tr>
                                                <td><img src="{{ getSingleMedia($item, 'food_recognition_image') }}" alt="food-image" class="bg-soft-primary rounded img-fluid avatar-40 me-3"></td>
                                                <td>{{ optional($item->user)->display_name ?? optional($item->user)->email ?? '-' }}</td>
                                                <td>{{ $item->top_food_name ?? '-' }}</td>
                                                <td>{{ ucfirst($item->status) }}</td>
                                                <td>{{ optional($item->created_at)->format('Y-m-d H:i') }}</td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="5">{{ __('message.not_found_entry', [ 'name' => __('message.data') ]) }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $data->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
