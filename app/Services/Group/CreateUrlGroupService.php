<?php 
namespace App\Services\Group;

use App\Models\Group;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateUrlGroupService {

    public function execute(User $user, string $name, ?string $description) {
        DB::transaction(function () use ($user, $name, $description) {
             Group::create([
           'user_id' => $user->id,
           'name' => $name,
           'description' => $description??''
       ]);
        });
     
    }
}