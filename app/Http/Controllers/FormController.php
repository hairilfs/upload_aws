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
        $files = $this->list_contents('cms_laravel/');
        $data = [
            'files'     => $files,
        ];

    	return view('upload', $data);
    }

    public function upload(Request $request)
    {
    	if ($request->hasFile('pic')) 
    	{
    		$new_path = public_path('images/');
    		$title = $request->file('pic')->getClientOriginalName();

    		$image = file_get_contents($request->file('pic'));

            // save to local
    		$request->file('pic')->move($new_path, $title);
            $this->local->put($title, $image);

            // crop image
    		$img_z = Image::make($image)->resize(300, 300, function($constraint) {
    			$constraint->aspectRatio();
    		});

            // save to s3
    		$this->s3->put('cms_laravel/randomcid/'.$title, $img_z->stream()->__toString());

    	}

    	return redirect('/upload');
    }

    public function check($filename='default.jpg')
    {
        $exist = $this->s3->exists('cms_laravel/randomcid/'.$filename);
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
            $get = $this->s3->url('cms_laravel/randomcid/'.$filename);
            return '<img src='.$get.'>';
        }
        else
        {
            return "Oops, file not found!";
        }

    }

    public function delete($filename='default.jpg')
    {
        $filename = urldecode($filename);
        if($this->check($filename))
        {
            $delete = $this->s3->delete($filename);

            if ($delete)
            {
                // $this->local->delete($filename);
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

    public function delete_multiple($cid='randomcid')
    {
        $cid = urldecode($cid);
        $delete = false;
        $files = $this->list_contents($cid);

        if (count($files) >= 1) 
        {
            // $this->local->deleteDirectory($cid);
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

    public function list_contents($cid='randomcid')
    {
        $files = [];
        $get_dir = $this->s3->listContents($cid, true);
        foreach ($get_dir as $dir) {
            if ($dir['type'] == 'file') 
            {
                $files[] = $dir['path'];
            }
        }

        return $files;
    }
}
