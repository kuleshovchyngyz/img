<?php

namespace App\Jobs;


use App\Models\Image;
use App\Models\Project;
use App\Models\Test;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


use File;
use Input;

use ShortPixel;

class DropzoneCompress implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $this->compress( $this->data['folder_name'],$this->data['lossy'],$this->data['size'],$this->data['resize_width'],$this->data['resize_height'],$this->data['subfolder']);
    }

    public function compress($folder_name,$lossy,$size,$resize_width,$resize_height,$sub_folder_name)
    {
        \ShortPixel\ShortPixel::setOptions(array(
            "lossy" => $lossy,
            "resize" => $size,
            //'wait' =>$this->countImages($folder_name)*5,
            "resize_width" => $resize_width,
            "resize_height" => $resize_height,
            "persist_type" => 'text',
            "base_path" => public_path(),
        ));



//        $images = Project::where('folder',$folder_name)->first()->images;
//        foreach ($images as $image){
//            ShortPixel\fromFile('shortpixel/'.$folder_name.'/'.$image->name)->toFiles('shortpixel/compressed/'.$folder_name);
//        }
//        foreach ($images as $image){
//            $image->new_size = $this->file_sizes('compressed/'.$folder_name,$image->name);
//            $image->save();
//        }
        //dd(555);
        $result =\LaravelShortPixel::fromFolder( 'shortpixel/'.$folder_name.'/'.$sub_folder_name, 'shortpixel/compressed/'.$folder_name);
        $p = Project::where ('folder',$folder_name)->get();
        Project::find( $p[0]->id)->update([
            'status'=>true
        ]);
        $x = Image::where('project_id',$p[0]->id)->where('new_size',null)->where('path',$folder_name.'/'.$sub_folder_name)->get();
        foreach ($x as $i){
            if(file_exists(public_path('shortpixel/compressed/'.$folder_name.'/'.$i->name))){
                $i->new_size = $this->file_sizes('compressed/'.$folder_name,$i->name);
                $i->save();
            }

        }


//        $stop = false;
//            while(!$stop) {
//                //wait($this->countImages($folder_name)*10)->
//                $ret = ShortPixel\fromFolder('shortpixel/'.$folder_name.'/'.$sub_folder_name)->toFiles('shortpixel/compressed/'.$folder_name);
//                if(count($ret->failed) + count($ret->same) + count($ret->pending) == 0) {
//                    $stop = true;
//                    $p = Project::where ('folder',$folder_name)->get();
//                    Project::find( $p[0]->id)->update([
//                        'status'=>true
//                    ]);
//                    $x = Image::where('project_id',$p[0]->id)->where('new_size',null)->where('path',$folder_name.'/'.$sub_folder_name)->get();
//                    foreach ($x as $i){
//                        if(file_exists(public_path('shortpixel/compressed/'.$folder_name.'/'.$i->name))){
//                            $i->new_size = $this->file_sizes('compressed/'.$folder_name,$i->name);
//                            $i->save();
//                        }
//
//                    }
//                }
//            }


    }
    public function file_sizes($folder, $filname)
    {
        $fileSize = \File::size(public_path('shortpixel/'.$folder.'/'.$filname));
        return $fileSize;

    }
}
