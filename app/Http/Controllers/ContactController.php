<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(){
        $contacts = Contact::all();
        return response()->json([
            'contacts' => $contacts,
            'status' => true
        ]);
    }
    public function store(ContactRequest $request)
    {
        $validatedData = $request->validated();

        Contact::create($validatedData);

        return response()->json([
            'status' => true,
            'message' => 'Contact Message send successfully',
        ]);
    }
    public function show(Request $request)
    {
        $contact = Contact::find($request->id);
        return response()->json([
            'contact' => $contact,
            'status' => true
        ]);
    }

    public function destroy(Request $request)
    {
        $contact = Contact::find($request->id);
        $contact->delete();
        return response()->json([
            'status' => true,
            'message' => 'Contact Delete successfully',
        ]);
    }
}
