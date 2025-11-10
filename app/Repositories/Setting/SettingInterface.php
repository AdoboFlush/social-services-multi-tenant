<?php

namespace App\Repositories\Setting;

use App\Repositories\BaseInterface;

interface SettingInterface extends BaseInterface
{
    public function getValueByKey($key);
    public function getByKey($key);
}
