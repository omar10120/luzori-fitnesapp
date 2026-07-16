<?php

namespace App\DataTables;

use App\Models\AdviceOption;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;

class AdviceOptionDataTable extends DataTable
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
            ->editColumn('description', function ($query) {
                return \Illuminate\Support\Str::limit(strip_tags($query->description), 50);
            })
            ->editColumn('is_active', function ($query) {
                $checked = $query->is_active ? 'checked' : '';
                return '<div class="form-group mb-0">
                    <div class="form-check form-switch">
                        <input class="form-check-input change_status" type="checkbox" data-type="adviceoption" data-name="is_active" data-id="' . $query->id . '" id="adviceoption-status-' . $query->id . '" ' . $checked . ' value="' . $query->id . '">
                        <label class="form-check-label" for="adviceoption-status-' . $query->id . '"></label>
                    </div>
                </div>';
            })
            ->editColumn('created_at', function ($query) {
                return dateAgoFormate($query->created_at, true);
            })
            ->editColumn('updated_at', function ($query) {
                return dateAgoFormate($query->updated_at, true);
            })
            ->addColumn('action', function ($adviceoption) {
                $id = $adviceoption->id;
                return view('adviceoption.action', compact('adviceoption', 'id'))->render();
            })
            ->addIndexColumn()
            ->order(function ($query) {
                if (request()->has('order')) {
                    $order = request()->order[0];
                    $column_index = $order['column'];

                    $column_name = 'id';
                    $direction = 'desc';
                    if ($column_index != 0) {
                        $column_name = request()->columns[$column_index]['data'];
                        $direction = $order['dir'];
                    }

                    $query->orderBy($column_name, $direction);
                }
            })
            ->rawColumns(['action', 'is_active']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\AdviceOption $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(AdviceOption $model)
    {
        return $this->applyScopes($model->newQuery());
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('advice-options-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->parameters($this->getBuilderParameters());
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
            ['data' => 'key', 'name' => 'key', 'title' => __('message.key')],
            ['data' => 'label', 'name' => 'label', 'title' => __('message.label')],
            ['data' => 'description', 'name' => 'description', 'title' => __('message.description'), 'orderable' => false],
            ['data' => 'order', 'name' => 'order', 'title' => __('message.order')],
            ['data' => 'is_active', 'name' => 'is_active', 'title' => __('message.status')],
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
