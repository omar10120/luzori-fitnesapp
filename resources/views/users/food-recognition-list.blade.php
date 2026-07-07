@if( count($data) > 0 )
    @foreach ($data as $history)
        <tr>
            <td><img src="{{ getSingleMedia($history, 'food_recognition_image') }}" alt="food-image" class="bg-soft-primary rounded img-fluid avatar-40 me-3"></td>
            <td>{{ $history->top_food_name ?? '-' }}</td>
            <td>{{ ucfirst($history->status) }}</td>
            <td>{{ optional($history->created_at)->format('Y-m-d H:i') }}</td>
        </tr>
    @endforeach
@else
    <tr>
        <td colspan="4">
            {{ __('message.not_found_entry', [ 'name' => __('message.data') ]) }}
        </td>
    </tr>
@endif
