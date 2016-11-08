<?php

namespace App\Http\Controllers;


use App\Requests\Request;
use App\Tokens\Token;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Http\Response;

class RequestController extends Controller
{

    public function create(HttpRequest $req)
    {
        $token = Token::uuid($req->uuid);

        $request = Request::create([
            'ip' => $req->ip(),
            'hostname' => $req->getHost(),
            'method' => $req->getMethod(),
            'user_agent' => $req->header('User-Agent'),
            'content' => file_get_contents('php://input'),
            'headers' => $req->headers->all(),
            'url' => $req->fullUrl(),
        ]);

        $request->save();

        $statusCode = (empty($req->statusCode) ? $token->default_status : (int)$req->statusCode);

        return new Response(
            $token->default_content,
            $statusCode,
            ['Content-Type' => $token->default_content_type]
        );
    }

}