<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SebastianBergmann\FileIterator\Factory;
use Kreait\Laravel\Firebase\Facades\Firebase;

class FirebaseController extends Controller
{

public function __invoke()
{
     $firebase=(new Factory)
     ->withServiceAccount(storage_path('app/firebase/firebase_credentials.json'))
     ->withDatabaseUrl('https://fit-reserve-default-rtdb.firebaseio.com/');

$database=$firebase->createDatabase();
$notifications=$database->getReference('notifications');
return $notifications->getValue();

}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
