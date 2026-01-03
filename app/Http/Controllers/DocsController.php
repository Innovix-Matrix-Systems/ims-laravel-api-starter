<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class DocsController extends Controller
{
    public function scalar()
    {
        // This method will only be called in non-production environments due to route restrictions
        return view('docs.scalar');
    }

    public function swagger()
    {
        // This method will only be called in non-production environments due to route restrictions
        return view('docs.swagger');
    }

    public function openapi()
    {
        // This method will only be called in non-production environments due to route restrictions
        $openapi = Storage::get('scribe/openapi.yaml');

        return response($openapi)
            ->header('Content-Type', 'application/x-yaml');
    }

    public function postman()
    {
        // This method will only be called in non-production environments due to route restrictions
        $postman = Storage::get('scribe/collection.json');

        return response($postman)
            ->header('Content-Type', 'application/x-json');
    }
}
