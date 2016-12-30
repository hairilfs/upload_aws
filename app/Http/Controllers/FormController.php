<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Filesystem\Filesystem;
use App\Http\Requests;

use Image;

class FormController extends Controller
{
    public function index()
    {
    	return view('upload');
    }

    public function upload(Request $request)
    {
    	if ($request->hasFile('pic')) 
    	{
    		$new_path = public_path('images/');
    		$new_title = $request->title.'.'.$request->file('pic')->getClientOriginalExtension();

    		$image = file_get_contents($request->file('pic'));
    		$request->file('pic')->move($new_path, $new_title);

    		$img_z = Image::make($image)->resize(300, 300, function($constraint) {
    			$constraint->aspectRatio();
    		});
    		
    		$s3 = \Storage::disk('s3');
    		$s3->put('cms_laravel/'.$new_title, $img_z->stream()->__toString());

    	}

    	return redirect('/upload');
    }

    public function check()
    {
    	return 'Here';
    }
}
