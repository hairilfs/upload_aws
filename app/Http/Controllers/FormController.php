<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Contracts\Filesystem\Filesystem;
use App\Http\Requests;
// use Illuminate\Support\Facades\Storage;

use Image;

class FormController extends Controller
{
    public function __construct()
    {
        $this->s3 = \Storage::disk('s3');
        $this->local = \Storage::disk('local');
    }

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

            // save to local
    		$request->file('pic')->move($new_path, $new_title);

            // crop image
    		$img_z = Image::make($image)->resize(300, 300, function($constraint) {
    			$constraint->aspectRatio();
    		});

            // save to s3
    		$this->s3->put('cms_laravel/randomcid/'.$new_title, $img_z->stream()->__toString());

    	}

    	return redirect('/upload');
    }

    public function check($filename='default.jpg')
    {
        $exist = $this->s3->exists('cms_laravel/'.$filename);
        if ($exist) 
        {
            return 1;
        }
        else
        {
            return 0;
        }
    }

    public function get($filename='default.jpg')
    {
        if($this->check($filename))
        {
            $get = $this->s3->url('cms_laravel/'.$filename);
            return '<img src='.$get.'>';
        }
        else
        {
            return "Oops, file not found!";
        }

    }

    public function delete($filename='default.jpg')
    {
        if($this->check($filename))
        {
            $delete = $this->s3->delete('cms_laravel/'.$filename);

            if ($delete)
            {
                return "File deleted!";
            }
            else
            {
                return "Oops, the file can not be deleted.";
            }

        }
        else
        {
            return "Oops, file not found!";
        }
    }

    public function delete_multiple($cid='defaultcid')
    {
        $delete = false;
        $files = [];
        $get_dir = $this->s3->listContents('cms_laravel/'.$cid, true);
        foreach ($get_dir as $dir) {
            if ($dir['type'] == 'file') 
            {
                $files[] = $dir['path'];
            }
        }

        if (count($files) >= 1) 
        {
            $delete = $this->s3->delete($files);
        }

        if ($delete)
        {
            return "Files deleted!";
        }
        else
        {
            return "Oops, files can not be deleted.";
        }
    }
}
