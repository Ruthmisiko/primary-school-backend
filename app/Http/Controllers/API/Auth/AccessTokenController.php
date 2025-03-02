<?php

namespace App\Http\Controllers\API\Auth;

use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Exceptions\OAuthServerException as passportOAuthServerException;
use Laravel\Passport\Http\Controllers\AccessTokenController as ATC;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;
use Response;

class AccessTokenController extends ATC
{

    public function issueToken(ServerRequestInterface $request)
    {

            try {
                $username = $request->getParsedBody()['username'];
                $tokenResponse = parent::issueToken($request);
                $content = $tokenResponse->getContent();
                $data = json_decode($content, true);

                $expiresIn = $data['expires_in'];
                $issueTime = time();
                $expirationTime = $issueTime + $expiresIn;

                if (isset($data["error"]))
                    throw new OAuthServerException('The user credentials were incorrect.', 6, 'invalid_grant', 400);

                    $user = User::where('email', $username)->orWhere('username', $username)->first();


                $data['user'] = $user;

                $data['expires_in'] =  $expirationTime;

                return Response::json($data);
            } catch (ModelNotFoundException $e) {
                return response(["message" => "User not found"], 500);
            } catch (passportOAuthServerException $e) {
                $data = [
                    "error" => "invalid_grant",
                    "error_description" => "The user credentials were incorrect.",
                    "message" => "The user credentials were incorrect."
                ];

                return response($data, 400);
            } catch (Exception $e) {
                Log::info($e);
                dd($e);
                return response(["message" => "Internal server error"], 401);
            }
    }



}
