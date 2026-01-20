<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helper\Reply;
use App\Models\BaseModel;
use App\Models\LeaveType;
use App\Models\Branch;
use App\Models\EmployeeDetails;
use Illuminate\Http\Request;
use App\DataTables\BranchDataTable;
use App\Http\Requests\Branch\StoreRequest;
use App\Http\Requests\Branch\UpdateRequest;

class BranchController extends AccountBaseController
{
    public $arr = [];

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = __('app.menu.branch');
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('employees', $this->user->modules));
            return $next($request);
        });
    }

    public function index(BranchDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_Designation');
        abort_403(!in_array($viewPermission, ['all']));

        // get all Branchs
        $this->branches = Branch::all();
        return $dataTable->render('branch.index', $this->data);
    }

    public function create()
    {
        $this->branches = Branch::all();

        if (request()->ajax()) {
            $html = view('branch.ajax.create', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'branch.ajax.create';

        return view('branch.create', $this->data);
    }

    /**
     * @param StoreRequest $request
     * @return array
     */
    public function store(StoreRequest $request)
    {
        $group = new Branch();
        $group->name = $request->name;
        $group->parent_id = $request->parent_id ? $request->parent_id : null;
        $group->save();

        $redirectUrl = urldecode($request->redirect_url);

        if ($redirectUrl == '') {
            $redirectUrl = route('branches.index');
        }


        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => $redirectUrl]);
    }

    public function show($id)
    {
        $this->branch = Branch::findOrFail($id);
        $this->parent = Branch::where('id', $this->branch->parent_id)->first();

        if (request()->ajax())
        {
            $html = view('branch.ajax.show', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'branch.ajax.show';
        return view('branch.create', $this->data);
    }

    public function edit($id)
    {
        $this->branch = Branch::findOrFail($id);
        $this->branches = Branch::all();

        if (request()->ajax())
        {
            $html = view('branch.ajax.edit', $this->data)->render();
            return Reply::dataOnly(['status' => 'success', 'html' => $html, 'title' => $this->pageTitle]);
        }

        $this->view = 'branch.ajax.edit';
        return view('branch.create', $this->data);

    }

    /**
     * @param UpdateRequest $request
     * @param int $id
     * @return array
     * @throws \Froiden\RestAPI\Exceptions\RelatedResourceNotFoundException
     */

    public function update(UpdateRequest $request, $id)
    {
        $editBranch = user()->permission('edit_Branch');
        abort_403($editBranch != 'all');

        $group = Branch::findOrFail($id);

        if($request->parent_id != null)
        {
            $parent = Branch::findOrFail($request->parent_id);

            if($id == $parent->parent_id)
            {
                $parent->parent_id = $group->parent_id;
                $parent->save();
            }
        }

        $group->name = strip_tags($request->Branch_name);
        $group->parent_id = $request->parent_id ? $request->parent_id : null;
        $group->save();

        $redirectUrl = route('branches.index');
        return Reply::successWithData(__('messages.branchUpdated'), ['redirectUrl' => $redirectUrl]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $deletePermission = user()->permission('delete_Branch');
        abort_403($deletePermission != 'all');

        EmployeeDetails::where('branch_id', $id)->update(['branch_id' => null]);
        $branch = Branch::where('parent_id', $id)->get();
        $parent = Branch::findOrFail($id);

        if(count($branch) > 0)
        {
            foreach($branch as $branch)
            {
                $child = Branch::findOrFail($branch->id);
                $child->parent_id = $parent->parent_id;
                $child->save();
            }
        }

        Branch::destroy($id);

        $redirectUrl = route('Branchs.index');
        return Reply::successWithData(__('messages.deleteSuccess'), ['redirectUrl' => $redirectUrl]);
    }

    public function applyQuickAction(Request $request)
    {

        if ($request->action_type === 'delete') {
            $this->deleteRecords($request);
            return Reply::success(__('messages.deleteSuccess'));
        }

        return Reply::error(__('messages.selectAction'));

    }

    protected function deleteRecords($request)
    {
        $deletePermission = user()->permission('delete_department');
        abort_403($deletePermission != 'all');

        $rowIds = explode(',', $request->row_ids);

        if (($key = array_search('on', $rowIds)) !== false) {
            unset($rowIds[$key]);
        }

        foreach ($rowIds as $id) {
            EmployeeDetails::where('Branch_id', $id)->update(['Branch_id' => null]);
            $branch = Branch::where('parent_id', $id)->get();
            $parent = Branch::findOrFail($id);

            if(count($branch) > 0)
            {
                foreach($branch as $branch)
                {
                    $child = Branch::findOrFail($branch->id);
                    $child->parent_id = $parent->parent_id;
                    $child->save();
                }
            }
        }

        Branch::whereIn('id', explode(',', $request->row_ids))->delete();
    }

    public function hierarchyData()
    {
        $viewPermission = user()->permission('view_Branch');
        abort_403($viewPermission != 'all');

        $this->pageTitle = 'Branch Hierarchy';
        $this->chartBranchs = Branch::get(['id','name','parent_id']);
        $this->Branchs = Branch::with('childs')->where('parent_id', null)->get();

        if(request()->ajax())
        {
            return Reply::dataOnly(['status' => 'success', 'Branchs' => $this->Branchs]);
        }

        return view('Branchs-hierarchy.index', $this->data);
    }

    public function changeParent()
    {
        $editPermission = user()->permission('edit_Branch');
        abort_403($editPermission != 'all');

        $child_ids = request('values');
        $parent_id = request('newParent') ? request('newParent') : request('parent_id');

        $branch = Branch::findOrFail($parent_id);
        // Root node again
        if(request('newParent') && $branch)
        {
            $branch->parent_id = null;
            $branch->save();
        }
        else if ($branch) // update child Node
        {
            foreach ($child_ids as $child_id)
            {
                $child = Branch::findOrFail($child_id);

                if ($child)
                {
                    $child->parent_id = $parent_id;
                    $child->save();
                }

            }
        }

        $this->chartBranchs = Branch::get(['id','name','parent_id']);
        $this->Branchs = Branch::with('childs')->where('parent_id', null)->get();

        $html = view('Branchs-hierarchy.chart_tree', $this->data)->render();
        $organizational = view('Branchs-hierarchy.chart_organization', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'html' => $html,'organizational' => $organizational]);

    }

    public function searchFilter()
    {
        $text = request('searchText');

        if($text != '' && strlen($text) > 2)
        {
            $searchParent = Branch::with('childs')->where('name', 'like', '%' . $text . '%')->get();

            $id = [];

            foreach($searchParent as $item)
            {
                array_push($id, $item->parent_id);
            }

            $item = $searchParent->whereIn('id', $id)->pluck('id');
            $this->chartDepartments = $searchParent;

            if($text != '' && !is_null($item)){
                foreach($this->chartDepartments as $item){
                    $item['parent_id'] = null;
                }
            }

            $parent = array();

            foreach($this->chartDepartments as $branch)
            {
                array_push($parent, $branch->id);

                if ($branch->childs)
                {
                    $this->child($branch->childs);
                }
            }

            $this->children = Branch::whereIn('id', $this->arr)->get(['id','name','parent_id']);
            $this->parents = Branch::whereIn('id', $parent)->get(['id','name']);
            $this->chartBranchs = $this->parents->merge($this->children);

            $this->Branchs = Branch::with('childs')
                ->where('name', 'like', '%' . $text . '%')
                ->get();
        }
        else
        {
            $this->chartBranchs = Branch::get(['id','name','parent_id']);
            $this->Branchs = Branch::with('childs')->where('parent_id', null)->get();
        }

        $html = view('Branchs-hierarchy.chart_tree', $this->data)->render();
        $organizational = view('Branchs-hierarchy.chart_organization', $this->data)->render();

        return Reply::dataOnly(['status' => 'success', 'html' => $html,'organizational' => $organizational]);

    }

    public function child($child)
    {
        foreach($child as $item)
        {
            array_push($this->arr, $item->id);

            if ($item->childs)
            {
                $this->child($item->childs);
            }
        }


    }

}
