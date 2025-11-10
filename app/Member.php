<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Member extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
   protected $fillable = [
                    'id',
                    'first_name',
                    'middle_name',
                    'last_name',
                    'suffix',
                    'brgy',
                    'area',
                    'address',
                    'birth_date',
                    'gender',
                    'precinct',
                    'alliance',
                    'affiliation',
                    'civil_status',
                    'religion',
                    'age',
                    'contact_number',
                    'remarks',
                    'parent_id',
                    'is_voter',
                    'status',
                    'contact_person_first_name',
                    'contact_person_last_name',
                    'contact_person_middle_name',
                    'contact_person_suffix',
                    'contact_person_number',
                    'contact_person_address'

            ];

    /**
        * The attributes that should be hidden for arrays.
        *
        * @var array
        */
    protected $hidden = [];

    protected $appends = ['age', 'full_name', 'code', 'has_member_access'];

    public const VOTER_FIELDS = ['affiliation', 'alliance', 'position', 'party_list', 'sectoral', 'beneficiary'];
    
    public function id_requests()
    {
        return $this->hasMany('App\IdRequest', 'member_id');
    }

    public function member_code(): HasOne
    {
        return $this->hasOne('App\MemberCode', 'member_id');
    }

    public function voter()
    {
        return $this->belongsTo('App\Voter', 'parent_id', 'id');
    }

    public function getCodeAttribute()
    {
        if($this->member_code) {
            return $this->member_code->code;
        }
        return null;
    }

    public function getFullNameAttribute()
    {
        if(!empty($this->suffix)){
            return $this->last_name.", ".$this->first_name." ".$this->suffix." ".$this->middle_name;
        }else{
            return $this->last_name.", ".$this->first_name." ".$this->middle_name;
        }
    }

    public function getContactPersonFullNameAttribute()
    {
        if(!empty($this->contact_person_suffix)){
            return $this->contact_person_last_name.", ".$this->contact_person_first_name." ".$this->contact_person_suffix." ".$this->contact_person_middle_name;
        }else{
            return $this->contact_person_last_name.", ".$this->contact_person_first_name." ".$this->contact_person_middle_name;
        }
    }

    public function getAgeAttribute()
    {
		if(!isset($this->birth_date)) {
			return 0;
		}
        $diff = date_diff(date_create($this->birth_date), date_create(date("Y-m-d")));
        return $diff->format('%y');
    }

    public function getHasMemberAccessAttribute()
    {
		return User::where("account_number", $this->account_number)->exists();
    }

}
