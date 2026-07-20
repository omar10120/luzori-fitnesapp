<?php

namespace App\DataTables;

use App\Models\Package;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

use App\Traits\DataTableTrait;

class PackageDataTable extends DataTable
{
    use DataTableTrait;
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)

            ->editColumn('status', function($query) {
                $status = 'warning';
                switch ($query->status) {
                    case 'active':
                        $status = 'primary';
                        break;
                    case 'inactive':
                        $status = 'warning';
                        break;
                }
                return '<span class="text-capitalize badge bg-'.$status.'">'.$query->status.'</span>';
            })
            ->editColumn('diet.title', function($query) {
                return optional($query->diet)->title ?? '-';
            })
            ->editColumn('advice.name', function($query) {
                return optional($query->advice)->name ?? '-';
            })
            ->editColumn('exercise.title', function($query) {
                return optional($query->exercise)->title ?? '-';
            })
            ->editColumn('duration_unit', function($package) {
                switch ($package->duration_unit) {
                    case 'monthly':
                        $duration_unit = __('message.monthly');
                        break;
                    case 'yearly':
                        $duration_unit = __('message.yearly');
                        break;
                    default:
                        $duration_unit = $package->duration_unit;
                        break;
                }
                return $duration_unit;
            })
            ->addColumn('price', function($price){             
                $price = getPriceFormat($price->price);
                return $price;
            })
            ->addColumn('follow_up_price', function($package) {
                return getPriceFormat($package->follow_up_price);
            })
            ->addColumn('food_recognition_limit', function($package) {
                return $package->food_recognition_limit;
            })
            
            ->editColumn('created_at', function ($query) {
                return dateAgoFormate($query->created_at, true);
            })
            ->editColumn('updated_at', function ($query) {
                return dateAgoFormate($query->updated_at, true);
            })
            ->addColumn('action', function($package){
                $id = $package->id;
                return view('package.action',compact('package','id'))->render();
            })
            ->addIndexColumn()
            ->order(function ($query) {
                if (request()->has('order')) {
                    $order = request()->order[0];
                    $column_index = $order['column'];

                    $column_name = 'id';
                    $direction = 'desc';
                    if( $column_index != 0) {
                        $column_name = request()->columns[$column_index]['data'];
                        $direction = $order['dir'];
                    }
    
                    $query->orderBy($column_name, $direction);
                }
            })
            ->rawColumns(['action','status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Package $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Package $model)
    {
        return $model->newQuery();
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('DT_RowIndex')
                ->searchable(false)
                ->title(__('message.srno'))
                ->orderable(false),
            ['data' => 'name', 'name' => 'name', 'title' => __('message.name')],
            ['data' => 'duration', 'name' => 'duration', 'title' => __('message.duration')],
            ['data' => 'duration_unit', 'name' => 'duration_unit', 'title' => __('message.duration_unit')],
            // ['data' => 'diet.title', 'name' => 'diet.title', 'title' => __('message.diet'), 'orderable' => false],
            // ['data' => 'advice.name', 'name' => 'advice.name', 'title' => __('message.advice'), 'orderable' => false],
            // ['data' => 'exercise.title', 'name' => 'exercise.title', 'title' => __('message.exercise'), 'orderable' => false],
            ['data' => 'price', 'name' => 'price', 'title' => __('message.price')],
            ['data' => 'follow_up_price', 'name' => 'follow_up_price', 'title' => __('message.follow_up_price')],
            ['data' => 'food_recognition_limit', 'name' => 'food_recognition_limit', 'title' => __('message.food_recognition_limit')],
            ['data' => 'status', 'name' => 'status', 'title' => __('message.status')],
            ['data' => 'created_at', 'name' => 'created_at', 'title' => __('message.created_at')],
            ['data' => 'updated_at', 'name' => 'updated_at', 'title' => __('message.updated_at')],
            Column::computed('action')
                  ->exportable(false)
                  ->printable(false)
                  ->title(__('message.action'))
                  ->width(60)
                  ->addClass('text-center hide-search'),
        ];
    }
}