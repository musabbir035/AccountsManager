<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    public function definition()
    {
        $start = mktime(0, 0, 0, 1, 1, 2022);
        $now = strtotime(date('y-m-d'));
        $bool = $this->faker->boolean(50);
        $rand = rand(1, 10000);
        return [
            'customer_id' => $this->faker->boolean(50) ? null : Customer::all()->random()->id,
            'description' => $this->faker->text(40),
            'type' => $bool ? Transaction::$EXPENSE : Transaction::$INCOME,
            'date' => date('Y-m-d', rand($start, $now)),
            'amount' => $bool ? $rand * -1 : $rand,
            'added_by_id' => User::where('role', 1)->first()
        ];
    }
}
