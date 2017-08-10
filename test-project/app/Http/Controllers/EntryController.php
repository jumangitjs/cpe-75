<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Entry;
use JWTAuth;

class EntryController extends Controller
{
    public function getEntries()
    {
        $entries = Entry::all();

        return response()->json([
            'entries' => $entries
        ], 200);
    }

    /*
     *
     * ADMIN RESTRICTION FOR ENTRY
     * MODIFICATION/CREATIONS
     *
     * */

    private function checkRole()
    {
        $account = JWTAuth::parseToken()->toUser();
        return $account->role;
    }

    private function unauthorizedResponse()
    {
        return response()->json([
            'message' => 'Unauthorized access!'
        ], 401);
    }

    public function postEntry(Request $request)
    {
        if(!$this->checkRole())
        {
            return $this->unauthorizedResponse();
        }
        
        $entry = new Entry();

        $entry->title = $request->input('title');
        $entry->img_src = $request->input('img_src');

        $entry->save();

        return response()->json([
            'entry' => $entry,
        ], 201);
    }

    public function putEntry(Request $request, $id)
    {
        if(!$this->checkRole())
        {
            return $this->unauthorizedResponse();
        }

        $entry = Entry::find($id);
        if(!$entry)
        {
            return response()->json([
                'message' => 'Entry not found'
            ], 404);
        }

        $this->transferData($request, $entry);

        $entry->save();

        return response()->json([
            'entry' => $entry
        ], 200);
    }

    private function transferData(Request $request, Entry $entry)
    {
        if($request->input('title'))
            $entry->title = $request->input('title');
        if($request->input('img_src'))
            $entry->img_src = $request->input('img_src');
    }

    public function deleteEntry($id)
    {
        if(!$this->checkRole())
        {
            return $this->unauthorizedResponse();
        }

        $entry = Entry::find($id);
        $entry->delete();

        return response()->json([
            'message' => 'Delete Successful'
        ], 200);
    }
}