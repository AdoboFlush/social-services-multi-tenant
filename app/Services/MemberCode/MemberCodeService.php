<?php

namespace App\Services\MemberCode;


use App\MemberCode;
use App\Services\BaseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Traits\Generatable;

class MemberCodeService extends BaseService
{

    use Generatable;

    public function generateMemberCodes(Request $request)
    {
        try {
            DB::beginTransaction();
            foreach (range(1, $request->count) as $count) {
                MemberCode::create([
                    'code' => $this->createMemberCode(),
                    'active' => 0,
                    'expired_at' =>  Carbon::now()->addYear(100)->format("Y-m-d H:i:s"),
                ]);
            }
            DB::commit();
            return redirect(route("member_codes"))->withSuccess(_lang("Successfully generated codes"));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
            return redirect(route("member_codes"))->withErrors("Unexpected error occur. Please try again");
        }
    }

    public function generateMemberCode(int $member_id)
    {
        try {
            DB::beginTransaction();

            MemberCode::create([
                'member_id' => $member_id,
                'code' => $this->createMemberCode(),
                'active' => 0,
            ]);

            DB::commit();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            DB::rollBack();
        }
    }

    public function getAll(Request $request)
    {
        $model = new MemberCode();
        $model = $model->with(['member']);

        if ($request->has('filter')) {
            if (isset($request->filter['member_code'])) {
                $member_code = $request->filter['member_code'];
                $model = $model->where(
                    fn($query) =>
                    $query->whereHas("member", fn($q) => $q->where("code", "LIKE", "%" . $member_code . "%"))
                        ->orWhere("code", $member_code)
                );
            }

            if (isset($request->filter['first_name'])) {
                $member_code = $request->filter['first_name'];
                $model = $model->where(
                    fn($query) =>
                    $query->whereHas("member", fn($q) => $q->where("code", "LIKE", "%" . $member_code . "%"))
                        ->orWhere("code", $member_code)
                );
            }
        }

        $model = $this->buildDataTableFilter($model, $request);
        $total_count = $model->count();
        $model = $this->buildModelQueryDataTable($model, $request);
        $model = $model->orderBy("created_at", "DESC");
        return ['data' =>  $model->get(), 'total' => $total_count];
    }

    public function validateMemberRegistration(Request $request)
    {
        $valid = true;

        $code_with_member = MemberCode::where("code", $request->member_code)
            ->with('member')
            ->first();

        $member = $code_with_member->member;

        if (strtolower($request->first_name) != strtolower($member->first_name)) {
            $valid = false;
        }

        if (!empty($request->middle_name) && !empty($member->middle_name)) {
            if (strtolower($request->middle_name) != strtolower($member->middle_name)) {
                $valid = false;
            }
        }

        if (strtolower($request->last_name) != strtolower($member->last_name)) {
            $valid = false;
        }

        if ($request->birth_date != $member->birth_date) {
            $valid = false;
        }

        return $valid;
    }

    public function checkMemberCode(string $code)
    {
        return MemberCode::where("code", $code)
            ->exists();
    }

    public function isMemberCodeActive(string $code)
    {
        $member_code = MemberCode::where("code", $code)->first();

        return $member_code->active;
    }

    public function getMemberByCode(string $code)
    {
        return MemberCode::where("code", $code)
            ->with('member')
            ->first();
    }
}
