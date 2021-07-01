<?php

namespace DummyModelNamespace;

use Bastinald\LaravelAutomaticMigrations\Traits\HasHashes;
use Faker\Generator;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class DummyModelClass extends Authenticatable
{
    use HasFactory, HasHashes, Notifiable;

    protected $guarded = [];
    protected $hashes = ['password'];
    protected $hidden = ['password', 'remember_token'];
    protected $casts = ['email_verified_at' => 'datetime'];

    public function migration(Blueprint $table)
    {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('password');
        $table->rememberToken();
        $table->string('timezone')->nullable();
        $table->timestamp('email_verified_at')->nullable();
        $table->timestamp('created_at')->nullable();
        $table->timestamp('updated_at')->nullable();
    }

    public function definition(Generator $faker)
    {
        return [
            'name' => $faker->firstName,
            'email' => $faker->unique()->safeEmail,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'timezone' => $faker->timezone,
            'email_verified_at' => now(),
            'created_at' => $faker->dateTimeBetween(now()->subMonth(), now()),
        ];
    }
}
