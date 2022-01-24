<?php

namespace App\Http\Controllers;
use App\Jobs\ProcessPodcast;
use App\Jobs\GetPositions;
use App\Models\Image;
use App\Models\Test;
use App\Models\Project;

use App\Models\User;
use Carbon\Carbon;
use File;
use Input;
use Illuminate\Http\Request;
use ShortPixel;
use Spatie\Permission\Traits\HasRoles;

class DropZoneController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function uploadTiny(Request $request){


        $filedata = $request->filedata;
        $path = public_path('shortpixel').'/compressed/'.$request->folder_id;
        if (!file_exists($path)) {
            \File::makeDirectory(public_path('shortpixel').'/compressed/'.$request->folder_id);
            // path does not exist
        }
        $this->base64_to_jpeg($filedata,public_path('shortpixel').'/compressed/'.$request->folder_id.'/'.$request->name);
        return true;
    }
    public function getB64Type($str) {
        // $str should start with 'data:' (= 5 characters long!)
        return substr($str, 5, strpos($str, ';')-5);
    }
    public function base64_to_jpeg($base64_string, $output_file) {
        // open the output file for writing
        $ifp = fopen( $output_file, 'wb' );

        // split the string on commas
        // $data[ 0 ] == "data:image/png;base64"
        // $data[ 1 ] == <actual base64 string>
        $data = explode( ',', $base64_string );

        // we could add validation here with ensuring count( $data ) > 1
        fwrite( $ifp, base64_decode( $data[ 1 ] ) );

        // clean up the file resource
        fclose( $ifp );

        return $output_file;
    }
    public function dropzoneCompress(Request $request)
    {


        $folder_name = $request->folder_id;
        //return  $this->countImages($folder_name);
        $lossy = $request->compression;
        $size = $request->size;
        $estimated_time = $this->countImages($folder_name)*100;
        $resize_width = null;
        $resize_height =null;
        if($size =="random"){
            $size = 3;
            $resize_width = $request->width;
            $resize_height = $request->height;
        }


//return "asd";
        $this->compress($folder_name,$lossy,$size,$resize_width,$resize_height,$estimated_time);


    }

    public function dz_download_one(Request $request)
    {
        $folder_name = $request->folder_id;
        $image_name = $request->name;
//        dd(1);
        $file = 'shortpixel/compressed/'.$folder_name.'/'.$image_name;
        return response()->download($file);
    }

    public function check($id)
    {
        $p = Project::where('folder',$id)->get();
        return $p[0]->status;
    }
    public function compress($folder_name,$lossy,$size,$resize_width,$resize_height,$estimated_time)
    {



        $data = array();
        $data['folder_name'] = $folder_name;
        $data['lossy'] = $lossy;
        $data['size'] = $size;
        $data['resize_width'] = $resize_width;
        $data['resize_height'] = $resize_height;
        $folder_loop=true;
        $sub_folder_name = 0;
        while($folder_loop){
            $data['subfolder'] = $sub_folder_name;
            Test::create([
                'date' => Carbon::now(),
                'text' => $sub_folder_name
            ]);
            ProcessPodcast::dispatch($data);
//            $filesInFolder = \File::files(public_path('shortpixel/'.$folder_name.'/'. $sub_folder_name));
//            foreach($filesInFolder as $path) {
//                $file = pathinfo($path);
//                $data['filename'] = $file['basename'];
//                ProcessPodcast::dispatch($data);
//            }
            $sub_folder_name++;
            if(!file_exists(public_path('shortpixel/'.$folder_name.'/'. $sub_folder_name))){
                $folder_loop=false;
            }

        }

    }
    public function shortpixel_file($folder)
    {
        $content = File::get(public_path('shortpixel/compressed/'.$folder.'/.shortpixel'));
        $arr = explode("\n", $content);
        foreach ($arr as $key => $r){
            if($r!="") {
                $parts = preg_split('/\s+/', $r);
                if($parts!=""){
                    $arr[$key] = $parts;
                }
            }
        }
        $image=array();

        foreach ($arr as $a){
            if($a && $a[1]=='success'){
                $image[$a[12]]=['new_size'=>$a[9],'percent'=>$a[8]];
            }
        }
        return $image;

    }
    public function progress_percent($folder)
    {
        if(!file_exists(public_path('shortpixel/compressed/'.$folder.'/.shortpixel'))){
            return 0;
        }
        $content = File::get(public_path('shortpixel/compressed/'.$folder.'/.shortpixel'));
        $arr = explode("\n", $content);
        foreach ($arr as $key => $r){
            if($r!="") {
                $parts = preg_split('/\s+/', $r);
                if($parts!=""){
                    $arr[$key] = $parts;
                }
            }
        }
        $success = 0;
        $pending = 0;
        foreach ($arr as $a){
            if($a && $a[1]=='success'){
                $success++;
            }
            if($a && $a[1]=='pending'){
                $pending++;
            }
        }
        return floor(100*$success/($success+$pending));
    }
    public function insert_new_sizes($folder_name)
    {
        $p = Project::where('folder',$folder_name)->get();
        Project::find( $p[0]->id)->update([
            'status'=>true
        ]);
        $x = Image::where('project_id',$p[0]->id)->get();
        foreach ($x as $i){
            $i->new_size = $this->file_sizes('compressed/'.$folder_name,$i->name);
            $i->save();
        }
    }
    public function test($folder_name)
    {
        $p = Project::where('folder',$folder_name)->get();

        $x = Image::where('project_id',$p[0]->id)->get();
        foreach ($x as $i){
            $i->new_size = $this->file_sizes('compressed/'.$folder_name,$i->name);
            $i->save();
        }
        return $x;
    }
    public function file_sizes($folder, $filname)
    {
        $fileSize = \File::size(public_path('shortpixel/'.$folder.'/'.$filname));
        return $fileSize;

    }
    public function dropzoneStrore(Request $request)
    {

        $folder_name = $request->toArray()['folder_id'];
        $path = public_path('shortpixel').'/compressed/'.$folder_name;
        if (!file_exists($path)) {
            \File::makeDirectory(public_path('shortpixel').'/compressed/'.$folder_name);
            // path does not exist
        }
        $image = $request->file('file');
        $imageName = $image->getClientOriginalName();
        $p = Project::where('folder',$folder_name)->get();
        if(count($p)==0){
            $p = Project::create([
                'folder'=>$folder_name,
                'status'=>false,
                'user_id'=>auth()->user()->id
            ]);
            $project = $p->id;
        }else{
            $project = $p[0]->id;
        }
        $number_of_images_uploaded = Image::where('project_id', $project)->count();
        $subfolder = intval($number_of_images_uploaded/9);
        $file_name = $this->check_file_exists($folder_name.'/'.$subfolder,$imageName);
        $image->move(public_path('shortpixel/'.$folder_name.'/'.$subfolder),$file_name);
        Image::create([
            'project_id' =>$project,
            'name' => $file_name,
            'path' => $folder_name.addslashes('/').$subfolder,
            'size' => $this->file_sizes($folder_name.'/'.$subfolder,$file_name)
        ]);
        $number_of_images_uploaded = Image::where('project_id', $project)->count();
        return response()->json([
            'success' => $imageName,
            'original_name'=>$image->getClientOriginalName(),
            'changed_name'=>$imageName,
            "request"=>$file_name,
            'number_of_images_uploaded'=>$number_of_images_uploaded
        ]);
    }
    public function download_zip($folder_name)
    {//60a52156c2e2d
        $zip_file = 'shortpixel/compressed/'.$folder_name.'.zip';
        $zip = new \ZipArchive();
        $zip->open($zip_file, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $path = public_path('shortpixel/compressed/'.$folder_name);
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path));
        foreach ($files as $name => $file)
        {
            if (!$file->isDir()) {
                $filePath     = $file->getRealPath();
                $relativePath =  substr($filePath, strlen($path) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
        $zip->close();
        return response()->download($zip_file);
    }
    public function index()
    {


    }
    public function get_post_image_sizes($folder){
        $p = Project::where('folder',$folder)->get();
        $x = Image::where('project_id',$p[0]->id)->get();

        $image = array();
        foreach ($x as $w){
            $o = $w->size;
            $n = $w->new_size;
            $image[$w->name] = ['new_size'=>$w->new_size,'percent'=>floor(100*($o-$n)/$o)];
        }
        return $image;
    }
    public function get_post_image_sizes_after_compressed($folder){
        $p = Project::where('folder',$folder)->get();
        $new_size_sum = Image::where('project_id',$p[0]->id)->sum('new_size');
        $old_size_sum = Image::where('project_id',$p[0]->id)->sum('size');

        return ['size_sum'=>$old_size_sum,'new_size_sum'=>$new_size_sum,'percentage'=>floor(100*($old_size_sum-$new_size_sum)/$old_size_sum)];
    }
    public function check_file_exists($folder,$filename)
    {
        if(!file_exists(public_path('shortpixel/'.$folder.'/'. $filename))){
            return $filename;
        }else{
            $file_parts = $this->split_file_name($filename);
            return $this->check_file_exists($folder,$file_parts['file_name'].'-1'.$file_parts['extension']);
        }
    }
    public function split_file_name($filename = "")
    {
        $ar = [];
        $root = substr($filename, 0 , (strrpos($filename, ".")));
        $extenstion = str_replace($root, "",$filename);
        $ar["file_name"] = $root;
        $ar["extension"] = $extenstion;
        return $ar;
    }

    public function status($folder_name){
        $ret = \ShortPixel\ShortPixel::getClient()->apiStatus('CF77SMOOchr3377rrDXA');
        dump($ret);
    }
    public function perm(){
        User::find(3)->assignRole('admin');
    }



    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    public function countImages($folder)
    {
        $files = File::files(public_path('shortpixel/'.$folder));
        $filecount = 0;
        if ($files !== false) {
            $filecount = count($files);
        }
        return $filecount;
    }
    public function countCompressed($folder){
        if(!file_exists(public_path('shortpixel/compressed/'.$folder))){
            return 0;
        }
        $p = Project::where('folder',$folder)->get();
        $x = Image::where('project_id',$p[0]->id)->get();
        //return $x;

        $success = 0;
        foreach ($x as $file){
            if(file_exists(public_path('shortpixel/compressed/'.$folder.'/'.$file->name))){
                $fileSize = File::size(public_path('shortpixel/compressed/'.$folder.'/'.$file->name));
                $file->new_size = $fileSize;
                $file->save();
                $success++;
            }
        }
        return floor(100*$success/(count($x)));
    }
    public function dropzoneDelete(Request $request)
    {
        $p = Project::where('folder',$request->folder_id)->get();
        $x = Image::where('project_id',$p[0]->id)
            ->where('name',$request->name)
            ->select('path')
            ->first();

        $file_path = 'shortpixel/'.$x->path.'/'.$request->name;
        $x = Image::where('project_id',$p[0]->id)
            ->where('name',$request->name)
            ->delete();
        unlink($file_path);
        return "deleted";
    }
    
}
