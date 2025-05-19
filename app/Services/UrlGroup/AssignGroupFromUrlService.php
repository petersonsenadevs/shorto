<?php
namespace App\Services\UrlGroup;

use App\Exceptions\UrlNotFoundException;
use App\Services\Group\FindGroupByUserTrait;
use App\Services\Url\UrlUtilsTrait;
use Illuminate\Support\Facades\DB;

class AssignGroupFromUrlService{
    use FindGroupByUserTrait;
    use UrlUtilsTrait;
    public function execute(string $groupId, string $urlId, string $userId): void{
        $group = $this->findGroupByIds($userId, $groupId);
        
        $url = $this->findUrlById($urlId, $userId);
        if (!$url) {
            throw new UrlNotFoundException();
        }
        DB::transaction(function () use ($url, $group) {
            $url->group_id = $group->id;
            $url->save();
        });
      
    }
}