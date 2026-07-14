<?php
\Auth::login(\App\Models\User::first());
$request = Illuminate\Http\Request::create('/admin/influencer-marketing', 'GET');
$request->setSession(app('session.store'));
$response = app()->handle($request);
file_put_contents('test_html.html', $response->getContent());
