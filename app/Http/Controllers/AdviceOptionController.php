<?php

namespace App\Http\Controllers;

use App\DataTables\AdviceOptionDataTable;
use App\Helpers\AuthHelper;
use App\Http\Requests\AdviceOptionRequest;
use App\Models\AdviceOption;

class AdviceOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(AdviceOptionDataTable $dataTable)
    {
        $pageTitle = __('message.list_form_title', ['form' => __('message.advice_option')]);
        $auth_user = AuthHelper::authSession();
        if (!$auth_user->can('adviceoption-list')) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }
        $assets = ['data-table'];

        $headerAction = $auth_user->can('adviceoption-add')
            ? '<a href="' . route('adviceoption.create') . '" class="btn btn-sm btn-primary" role="button">' . __('message.add_form_title', ['form' => __('message.advice_option')]) . '</a>'
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
        if (!auth()->user()->can('adviceoption-add')) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }

        $pageTitle = __('message.add_form_title', ['form' => __('message.advice_option')]);

        return view('adviceoption.form', compact('pageTitle'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\AdviceOptionRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(AdviceOptionRequest $request)
    {
        if (!auth()->user()->can('adviceoption-add')) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }

        $data = $request->only(['key', 'label', 'description', 'order', 'is_active']);
        $data['is_active'] = $request->boolean('is_active');
        $data['order'] = $request->input('order', 0);

        AdviceOption::create($data);

        return redirect()->route('adviceoption.index')->withSuccess(__('message.save_form', ['form' => __('message.advice_option')]));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = AdviceOption::findOrFail($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('adviceoption-edit')) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }

        $data = AdviceOption::findOrFail($id);
        $pageTitle = __('message.update_form_title', ['form' => __('message.advice_option')]);

        return view('adviceoption.form', compact('data', 'id', 'pageTitle'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\AdviceOptionRequest  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(AdviceOptionRequest $request, $id)
    {
        if (!auth()->user()->can('adviceoption-edit')) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }

        $option = AdviceOption::findOrFail($id);

        $data = $request->only(['key', 'label', 'description', 'order', 'is_active']);
        $data['is_active'] = $request->boolean('is_active');
        $data['order'] = $request->input('order', 0);

        $option->fill($data)->update();

        if (auth()->check()) {
            return redirect()->route('adviceoption.index')->withSuccess(__('message.update_form', ['form' => __('message.advice_option')]));
        }

        return redirect()->back()->withSuccess(__('message.update_form', ['form' => __('message.advice_option')]));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('adviceoption-delete')) {
            $message = __('message.permission_denied_for_account');
            return redirect()->back()->withErrors($message);
        }

        $option = AdviceOption::findOrFail($id);
        $status = 'errors';
        $message = __('message.not_found_entry', ['name' => __('message.advice_option')]);

        if ($option != '') {
            $option->advices()->detach();
            $option->delete();
            $status = 'success';
            $message = __('message.delete_form', ['form' => __('message.advice_option')]);
        }

        if (request()->ajax()) {
            return response()->json(['status' => true, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}
