<?php
declare(strict_types=1);
namespace App\Services\Url;
use App\Exceptions\ShortUrlExistException;
use App\Exceptions\UrlNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UpdateShortenUrlService{

    use UrlUtilsTrait;
    public function updateUrl(?string $originalUrl, ?string $shortenUrl, ?string $customAlias, ?string $password,?string $description, string $urlId,?string $groupId = null,?bool $isActive = null,string $userId): array{
           $url = $this->findUrlById($urlId, $userId);
        if(!$url){
            throw new UrlNotFoundException();
        }
        
        if($shortenUrl != null && $originalUrl != null){
            if ($this->isUrlExists($shortenUrl)) {
                throw new ShortUrlExistException;
            }
        }
     
        DB::transaction(function () use ($url, $originalUrl, $shortenUrl, $customAlias, $password, $description, $groupId, $isActive) {
            if($url != null){
            $url->original_url = $originalUrl;
        }
        if($shortenUrl != null){
            $url->shortened_url = $shortenUrl;
        }
        if($customAlias != null){
            $url->custom_alias = $customAlias;
        }
        if($password != null){
            $url->password = Hash::make($password);
        }
        if($description != null){
            $url->description = $description;
        }
        if($groupId != null){
            $url->group_id = $groupId;
        }
        if($url->is_acive != null){
            $url->is_active =  $isActive;
        }
        $url->save();  
        });
        return $url->select('shortened_url', 'original_url','custom_alias','description','is_active','id')->toArray();


      
    }
}