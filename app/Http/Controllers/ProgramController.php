<?php

namespace App\Http\Controllers;

use App\DataTables\ProgramDataTable;
use App\Helpers\AuthHelper;
use App\Http\Requests\ProgramRequest;
use App\Models\Advice;
use App\Models\Diet;
use App\Models\Program;

class ProgramController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(ProgramDataTable $dataTable)
    {
        $pageTitle = __('message.list_form_title', ['form' => __('message.program')]);
        $auth_user = AuthHelper::authSession();
        if (!$auth_user->can('program-list')) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }
        $assets = ['data-table'];

        $headerAction = $auth_user->can('program-add')
            ? '<a href="' . route('program.create') . '" class="btn btn-sm btn-primary" role="button">' . __('message.add_form_title', ['form' => __('message.program')]) . '</a>'
            : '';

        return $dataTable->render('global.datatable', compact('pageTitle', 'auth_user', 'assets', 'headerAction'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('program-add')) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }

        $pageTitle = __('message.add_form_title', ['form' => __('message.program')]);
        $diets = Diet::where('status', 'active')->orderBy('title')->pluck('title', 'id');
        $advices = Advice::where('status', 1)->orderBy('name')->pluck('name', 'id');

        return view('program.form', compact('pageTitle', 'diets', 'advices'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ProgramRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProgramRequest $request)
    {
        if (!auth()->user()->can('program-add')) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }

        $data = $request->only(['name', 'price', 'diet_id', 'advice_id', 'duration', 'status']);
        $data['status'] = $request->boolean('status');

        Program::create($data);

        return redirect()->route('program.index')->withSuccess(__('message.save_form', ['form' => __('message.program')]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Program::findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('program-edit')) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }

        $data = Program::findOrFail($id);
        $pageTitle = __('message.update_form_title', ['form' => __('message.program')]);
        $diets = Diet::where('status', 'active')->orderBy('title')->pluck('title', 'id');
        $advices = Advice::where('status', 1)->orderBy('name')->pluck('name', 'id');

        return view('program.form', compact('data', 'id', 'pageTitle', 'diets', 'advices'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ProgramRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProgramRequest $request, $id)
    {
        if (!auth()->user()->can('program-edit')) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }

        $program = Program::findOrFail($id);

        $data = $request->only(['name', 'price', 'diet_id', 'advice_id', 'duration', 'status']);
        $data['status'] = $request->boolean('status');

        $program->fill($data)->update();

        if (auth()->check()) {
            return redirect()->route('program.index')->withSuccess(__('message.update_form', ['form' => __('message.program')]));
        }

        return redirect()->back()->withSuccess(__('message.update_form', ['form' => __('message.program')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('program-delete')) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }

        $program = Program::findOrFail($id);
        $status = 'errors';
        $message = __('message.not_found_entry', ['name' => __('message.program')]);

        if ($program != '') {
            $program->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.program')]);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}
