<?php

namespace App\Repositories\Affiliate;

use App\AffiliateDetails;
use Illuminate\Support\Carbon;

class AffiliateRepository implements AffiliateInterface
{
    private $model;

    public function __construct(AffiliateDetails $model)
    {
        $this->model = $model;
    }

    public function create($request)
    {
        $affiliate = $this->model->where("user_id",$request["user_id"])->first();
        if($affiliate){
            $affiliate->code = $request["code"];
            $affiliate->save();
            return $affiliate;
        } else {
            $affiliate = $this->model->create($request);
            return $affiliate;
        }
    }

    public function update($code, $request)
    {
        $affiliate = $this->model->where('code',$code)->orWhere('id',$code)->first();
        if ($affiliate) {
            $affiliate->where('code',$code)->orWhere('id',$code)->update($request);
            return $affiliate;
        }
        return false;
    }

    public function delete($code)
    {
        $affiliate = $this->model->where('code',$code)->first();
        if ($affiliate) {
            $affiliate->where('code',$code)->delete();
            return $affiliate;
        }
        return false;
    }

    public function get($code)
    {
        return $this->model->where('code',$code)->first();
    }

    public function getAll()
    {
        return $this->model->where("code","!=",null)->groupBy('user_id')->get();
    }

    public function getByUserId($user_id){
        return $this->model->where("code","!=",null)->where("user_id",$user_id)->first();
    }
}
