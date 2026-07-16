<?php

namespace App\DataTables;

use App\Models\Program;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;
use App\Traits\DataTableTrait;

class ProgramDataTable extends DataTable
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
            ->editColumn('price', function ($query) {
                return $query->price !== null ? number_format((float) $query->price, 2) : '-';
            })
            ->editColumn('diet.title', function ($query) {
                return optional($query->diet)->title ?? '-';
            })
            ->editColumn('advice.name', function ($query) {
                return optional($query->advice)->name ?? '-';
            })
            ->editColumn('status', function ($query) {
                $checked = $query->status ? 'checked' : '';
                return '<div class="form-group mb-0">
                    <div class="form-check form-switch">
                        <input class="form-check-input change_status" type="checkbox" data-type="program" data-name="status" data-id="' . $query->id . '" id="program-status-' . $query->id . '" ' . $checked . ' value="' . $query->id . '">
                        <label class="form-check-label" for="program-status-' . $query->id . '"></label>
                    </div>
                </div>';
            })
            ->editColumn('created_at', function ($query) {
                return dateAgoFormate($query->created_at, true);
            })
            ->editColumn('updated_at', function ($query) {
                return dateAgoFormate($query->updated_at, true);
            })
            ->addColumn('action', function ($program) {
                $id = $program->id;
                return view('program.action', compact('program', 'id'))->render();
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
            ->rawColumns(['action', 'status']);
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Program $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Program $model)
    {
        $model = Program::query()->with(['diet', 'advice']);
        return $this->applyScopes($model);
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
            ->setTableId('programs-table')
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
            ['data' => 'name', 'name' => 'name', 'title' => __('message.name')],
            ['data' => 'price', 'name' => 'price', 'title' => __('message.price')],
            ['data' => 'diet.title', 'name' => 'diet.title', 'title' => __('message.diet'), 'orderable' => false],
            ['data' => 'advice.name', 'name' => 'advice.name', 'title' => __('message.advice'), 'orderable' => false],
            ['data' => 'duration', 'name' => 'duration', 'title' => __('message.duration')],
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
