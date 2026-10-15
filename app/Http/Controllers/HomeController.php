<?php

namespace App\Http\Controllers;

use App\Support\SamplePosts;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'packages' => config('packages'),
            'posts' => SamplePosts::all()->take(4),
        ]);
    }
}
