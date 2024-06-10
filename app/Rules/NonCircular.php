<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Support\Facades\DB;

class NonCircular implements DataAwareRule, ValidationRule
{

    /**
     * All of the data under validation.
     *
     * @var array<string, mixed>
     */
    protected $data = [];


    /**
     * Set the data under validation.
     *
     * @param  array<string, mixed>  $data
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {

        // Parameters from the rule implementation ($pk will likely be 'id')
        $table = array_get($value, 0);
        $pk = array_get($value, 1);
        $depth = (int) array_get($value, 2, 50);

        // The primary key value from the edited model
        $data_pk = array_get($this->data, $pk);
        $value_pk = $value;

        // If we’re editing an existing model and there is a parent value set…
        while ($data_pk && $value_pk) {

            // It’s not valid for any parent id to be equal to the existing model’s id
            if ($data_pk == $value_pk) {
                $fail('The :attribute cannot be its own parent.');
            }

            // Avoid accidental infinite loops
            if (--$depth < 0) {
                $fail('Recursion detected');
            }

            // Get the next parent id
            $value_pk = DB::table($table)->select($attribute)->where($pk, '=', $value_pk)->value($attribute);
        }


    }
}
