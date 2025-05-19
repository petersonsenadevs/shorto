<?php 
declare(strict_types=1);
namespace App\Services\UrlGroup;

use App\Services\Group\FindGroupByUserTrait;
use App\Services\Url\UrlUtilsTrait;
use Illuminate\Support\Facades\DB;

class UnassignGroupFromUrlService{
    use FindGroupByUserTrait;
    use UrlUtilsTrait;

    public function execute(string $urlId, string $groupId, string $userId) {
        $group = $this->findGroupByIds($userId, $groupId);
        $url = $this->findUrlById($urlId, $userId);
        DB::transaction(function () use ($url, $group) {
            $url->group_id = $group->id;
            $url->save();
        });
      
    }

}