<?php

namespace App\Http\Controllers;

use App\DataTables\AdviceDataTable;
use App\Helpers\AuthHelper;
use App\Http\Requests\AdviceRequest;
use App\Models\Advice;
use App\Models\AdviceOption;

class AdviceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AdviceDataTable $dataTable)
    {
        $pageTitle = __('message.list_form_title', ['form' => __('message.advice')]);
        $auth_user = AuthHelper::authSession();
        if (!$auth_user->can('advice-list')) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }
        $assets = ['data-table'];

        $headerAction = $auth_user->can('advice-add')
            ? '<a href="' . route('advice.create') . '" class="btn btn-sm btn-primary" role="button">' . __('message.add_form_title', ['form' => __('message.advice')]) . '</a>'
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
        if (!auth()->user()->can('advice-add')) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }

        $pageTitle = __('message.add_form_title', ['form' => __('message.advice')]);
        $options = AdviceOption::where('is_active', 1)->orderBy('order')->get();

        return view('advice.form', compact('pageTitle', 'options'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\AdviceRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdviceRequest $request)
    {
        if (!auth()->user()->can('advice-add')) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }

        $data = $request->only(['name', 'seed_text', 'status']);
        $data['status'] = $request->boolean('status');

        $advice = Advice::create($data);
        $advice->options()->sync($request->input('options', []));

        return redirect()->route('advice.index')->withSuccess(__('message.save_form', ['form' => __('message.advice')]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Advice::findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('advice-edit')) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }

        $data = Advice::with('options')->findOrFail($id);
        $pageTitle = __('message.update_form_title', ['form' => __('message.advice')]);
        $options = AdviceOption::where('is_active', 1)->orderBy('order')->get();

        return view('advice.form', compact('data', 'id', 'pageTitle', 'options'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\AdviceRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdviceRequest $request, $id)
    {
        if (!auth()->user()->can('advice-edit')) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }

        $advice = Advice::findOrFail($id);

        $data = $request->only(['name', 'seed_text', 'status']);
        $data['status'] = $request->boolean('status');

        $advice->fill($data)->update();
        $advice->options()->sync($request->input('options', []));

        if (auth()->check()) {
            return redirect()->route('advice.index')->withSuccess(__('message.update_form', ['form' => __('message.advice')]));
        }

        return redirect()->back()->withSuccess(__('message.update_form', ['form' => __('message.advice')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('advice-delete')) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }

        $advice = Advice::findOrFail($id);
        $status = 'errors';
        $message = __('message.not_found_entry', ['name' => __('message.advice')]);

        if ($advice != '') {
            $advice->options()->detach();
            $advice->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.advice')]);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}
