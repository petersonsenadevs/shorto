<?php 
declare(strict_types=1);
namespace App\Services\Url;
class ListAllUrlByUserService
{
    use UrlUtilsTrait;

    public function listAllUrlByUserId(string $userId): array {
        $urls = $this->listUrlsByUserId($userId);
        if (empty($urls)) {
            return [];
        }
        return $urls;
    }
}