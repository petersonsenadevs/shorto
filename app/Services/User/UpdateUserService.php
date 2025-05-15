<?php 
declare(strict_types=1);
namespace App\Services\User;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class UpdateUserService{

    public function updateUser(User $user, string $name, string $email, string $username): User{
      
      DB::transaction(function() use ($user, $name, $email, $username) {
          if($name != null){
            $user->name = $name;
        }
        if($email != null){
            $user->email = $email;
        }
    
        if($username != null){
            $user->username = $username;
        }
        $user->save();
      });
        
        return $user->select('name', 'email', 'username');



    }
}