<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	$uri = Request::path();
	if ($uri!="login" && !Auth::check())
	{
		return View::make('masters.login');
	}
});


App::after(function($request, $response)
{
	/* if(Session::has("email_message")){
		$data = Session::get("email_message");
		Mail::queue('emails.welcome', $data, function($message)
		{
			$subject = "ETM APPLICATION TRANSACTIONS ON : ".date("d-m-Y");
			$message->to('rayisatyanarayana22@gmail.com', 'Satya')->subject($subject);
		});
		Session::forget('email_message');
	} */
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest())
	{
		if (Request::ajax())
		{
			return Response::make('Unauthorized', 401);
		}
		else
		{
			return Redirect::guest('login');
		}
	}
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() !== Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

Route::filter('invalidate-browser-cache', function($request, $response)
{
	$response->headers->set('Cache-Control','nocache, no-store, max-age=0, must-revalidate');
	$response->headers->set('Pragma','no-cache');
	$response->headers->set('Expires','Fri, 01 Jan 1990 00:00:00 GMT');
});
