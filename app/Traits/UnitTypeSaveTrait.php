<?php

namespace App\Traits;

use App\Models\UnitType;

trait UnitTypeSaveTrait
{

    public function unitType($model)
    {
        if (is_null($model->unit_id)) {
            $type = UnitType::where('company_id', $model->company_id)->first();

            if ($type) {
                $model->unit_id = $type->id;
            }
        }

        return $model;
    }

}
