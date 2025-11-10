<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Template\TemplateFacade;

class TemplateController extends Controller
{

    public function __construct(TemplateFacade $templateFacade)
    {
      $this->templateFacade = $templateFacade;
    }
    
    public function index()
    {
      $templates = $this->templateFacade::get();
      return view('backend.id_system.template.list', compact('templates'));
    }

    public function create()
    {
      return view('backend.id_system.template.create');
    }

    public function store(Request $request)
    {
      return $this->templateFacade::store($request);
    }

    public function edit(Request $request)
    {
      $template = $this->templateFacade::getById($request->id);
      return view('backend.id_system.template.edit', compact('template'));
    }

    public function update(Request $request)
    {
      return $this->templateFacade::update($request);
    }

    public function delete(Request $request)
    {
        return response($this->templateFacade::deleteMultiple($request));
    }

}