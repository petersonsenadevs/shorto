<?php
declare(strict_types=1);
namespace App\Services\Url;

use App\Exceptions\UrlNotFoundException;
use App\Models\Url;
use Illuminate\Support\Facades\DB;

class DeleteUrlByShortCodeService{


    public function deleteUrlById(string $shortUrl): void
    {
       $url= Url::where('shortened_url', $shortUrl)->first();
       if(!$url){
        throw new UrlNotFoundException();
       }
       DB::transaction(function () use ($url) {
        $url->delete();
       });
    }
}