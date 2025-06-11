<?php

namespace Modules\TestModule\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TestModuleController extends Controller
{
    /**
     * Display the TestModule index page
     */
    public function index(): View
    {
        return view('testmodule::index');
    }

    /**
     * Display the about page
     */
    public function about(): View
    {
        return view('testmodule::about');
    }
}