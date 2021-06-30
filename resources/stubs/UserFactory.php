<?php

namespace DummyFactoryNamespace;

use DummyModelNamespace\DummyModelClass;
use Illuminate\Database\Eloquent\Factories\Factory;

class DummyFactoryClass extends Factory
{
    protected $model = DummyModelClass::class;

    public function definition()
    {
        return app($this->model)->definition($this->faker);
    }

    public function unverified()
    {
        return $this->state(function (array $attributes) {
            return [
                'email_verified_at' => null,
            ];
        });
    }
}
