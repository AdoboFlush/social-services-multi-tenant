<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Traits\TenantConnects;


class Voter extends Model
{

    use SoftDeletes;
    use TenantConnects;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 
        'middle_name', 
        'last_name', 
        'suffix',
        'full_name',
        'brgy',
        'address',
        'birth_date',
        'gender',
        'precinct',
        'alliance' ,
        'affiliation',
        'contact_number',
        'civil_status',
        'religion',
        'last_update_by',
        'voter_id',
        'alliance_subgroup',
        'alliance_1',
        'alliance_1_subgroup',
        'affiliation_subgroup',
        'affiliation_1',
        'affiliation_1_subgroup',
        'sectoral',
        'sectoral_1',
        'sectoral_subgroup',
        'sectoral_1_subgroup',
        'organization',
        'is_deceased',
        'remarks',
        'party_list',
        'party_list_1',
        'position',
        'beneficiary',
        'is_former_voter',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [];

    protected $appends = ['age', 'alliances'];

    protected $table = 'voter_tag_details'; // new table for voters

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['deleted_at'];

    public function getAgeAttribute()
    {
        if (isset($this->attributes['birth_date']) && !empty($this->attributes['birth_date'])) {
            $birthDate = $this->attributes['birth_date'];
            // Validate date format YYYY-MM-DD
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $birthDate)) {
                $dateParts = explode('-', $birthDate);
                if (checkdate((int)$dateParts[1], (int)$dateParts[2], (int)$dateParts[0])) {
                    $diff = date_diff(date_create($birthDate), date_create(date("Y-m-d")));
                    return $diff->format('%y');
                }
            }
        }
        return 0;
    }

    public function getAlliancesAttribute()
    {
        $alliance_arr = [];
        if(!empty($this->alliance)) {
            $alliance_arr[] = $this->alliance;
        }
        if(!empty($this->alliance_1)) {
            $alliance_arr[] = $this->alliance_1;
        }
        return implode(", ", $alliance_arr);
    }

    public function voter_tag_detail()
    {
        return $this->hasOne(VoterTagDetail::class, "voter_id");
    }

    public function upsertVoterTagDetail()
    {
        return VoterTagDetail::updateOrCreate(["voter_id" => $this->id], [
            "alliance" => $this->alliance,
            "affiliation" => $this->affiliation,
            "contact_number" => $this->contact_number,
            "civil_status" => $this->civil_status,
            "religion" => $this->religion,
            "last_update_by" => Auth::user() ? Auth::user()->id : 0,
        ]);
    }
}
