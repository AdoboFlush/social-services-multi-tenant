<?php

namespace App\Http\Controllers;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use App\Services\Tag\TagFacade;
use Illuminate\Support\Facades\Log;

class TagController extends Controller
{
    private $tagFacade;

	public function __construct(TagFacade $tagFacade)
	{
		$this->tagFacade = $tagFacade;
	}

	public function index(Request $request)
    {
        if($request->ajax()){
            $tags = $this->tagFacade::getAll($request);
            $total = $this->tagFacade::getTotalCount($request);
            return response()->json([
                'data'=> $tags,
                'recordsTotal' => $total,
                'recordsFiltered' => $total,
                'start' => $request->start,
                'length' => $request->length,
                'draw' => $request->draw,
            ]);
        }else{
            return view('backend.tag.list');
        }
    }

    public function create(Request $request)
    {
        $tags = $this->tagFacade::get();
        return view('backend.tag.modal.create', compact('tags'));
    }

    public function store(Request $request)
    {
        return $this->tagFacade::store($request);
    }

    public function edit(Request $request)
    {
        $tag = $this->tagFacade::getById($request);
        $tags = $this->tagFacade::get();
        return view('backend.tag.modal.edit', compact('tag', 'tags'));
    }

    public function show(Request $request)
    {
        $tag = $this->tagFacade::getById($request);
        $tag = $tag->toArray();
        return view('backend.tag.modal.show', compact('tag'));
    }

    public function update(Request $request)
    {
        return $this->tagFacade::update($request);
    }

    public function delete(Request $request)
    {
        return response($this->tagFacade::deleteMultiple($request));
    }

    public function fetch()
    {
        $this->tagFacade::fetch();
    }

    public function getChildTags(Request $request)
    {
        return response($this->tagFacade::get('', $request->parent_id));
    }

    public function getChildTagsByParentName(Request $request)
    {
        return response($this->tagFacade::getByParentName($request->parent_name));
    }

}
