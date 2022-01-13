<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class UsersController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/Index');
    }
}
