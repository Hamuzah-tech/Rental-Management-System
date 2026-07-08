<?php

namespace App\Services;

use App\Models\Property;

class PropertyService
{


    public function create(array $data): Property
    {

        return Property::create($data);

    }



    public function update(Property $property,array $data): bool
    {

        return $property->update($data);

    }



    public function delete(Property $property): bool
    {

        return $property->delete();

    }


}