<?php

namespace App\Http\Controllers;

use App\Services\MemberCode\MemberCodeFacade;
use Illuminate\Http\Request;

class MemberCodeController extends Controller
{

    private $memberCodeFacade;

    public function __construct(MemberCodeFacade $memberCodeFacade)
    {
        $this->memberCodeFacade = $memberCodeFacade;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $result = $this->memberCodeFacade::getAll($request);
            $data = $result["data"];
            $total = $result["total"];
            return response()->json([
                "data" => $data,
                "recordsTotal" => $total,
                "recordsFiltered" => $total,
                "start" => $request->start,
                "length" => $request->length,
                "draw" => $request->draw,
            ]);
        } else {
            return view("backend.member_code.list");
        }
    }

    public function create(Request $request)
    {
        return view('backend.member_code.modal.create');
    }

    public function generate(Request $request)
    {
        return $this->memberCodeFacade::generateMemberCodes($request);
    }
}
