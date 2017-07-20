<?php

namespace Ckryo\Laravel\Upload\Controllers;
use Ckryo\Laravel\App\Http\Controllers\Controller;
use Ckryo\Laravel\Auth\Auth;
use Ckryo\Laravel\Upload\Services\OSS;
use Illuminate\Http\Request;


class FileController extends Controller
{

    function store(Request $request, Auth $auth) {
        $ossKey = 'org_'.$auth->user()->org_id.'/files/'.date('YmdHis').str_random(6);
        $url = OSS::upload($ossKey, $request->file('file'));
        return response()->json($url);
    }

}