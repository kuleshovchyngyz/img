<?php

namespace App\Jobs;

use App\Models\Image;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPodcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $data;
    /**
     * Create a new job instance.
     *
     * @return void
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
        $filename = $this->data['filename'] ?? null;
        $this->compress( $this->data['folder_name'],$this->data['lossy'],$this->data['size'],$this->data['resize_width'],$this->data['resize_height'],$this->data['subfolder'],$filename);
    }

    public function compress($folder_name,$lossy,$size,$resize_width,$resize_height,$sub_folder_name,$filename)
    {
        \ShortPixel\ShortPixel::setOptions(array(   
            'resize' =>3,
            "base_path" => public_path(),
        ));

        if($size==0){
            $result =\LaravelShortPixel::fromFolder( 'shortpixel/'.$folder_name.'/'.$sub_folder_name.'/', 'shortpixel/compressed/'.$folder_name, $compression_level = $lossy);
        }
        if($size==1){
            $result =\LaravelShortPixel::fromFolder( 'shortpixel/'.$folder_name.'/'.$sub_folder_name.'/', 'shortpixel/compressed/'.$folder_name, $compression_level = $lossy,$width = 800, $height = 950, $maxDimension = true);
        }
        if($size==3){
            // getimagesize
            // $path = public_path().'shortpixel/'.$folder_name.'/'.$sub_folder_name.'/'.$filename;
            // getimagesize()
            $result =\LaravelShortPixel::fromFolder( 'shortpixel/'.$folder_name.'/'.$sub_folder_name, 'shortpixel/compressed/'.$folder_name, $compression_level = $lossy,$width = $resize_width, $height = $resize_height);
        }


      //  $result =\LaravelShortPixel::fromFolder( 'shortpixel/'.$folder_name.'/'.$sub_folder_name.'/', 'shortpixel/compressed/'.$folder_name, $compression_level = 2, $width = 200, $height = 200, $maxDimension = true);
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
    }
    public function file_sizes($folder, $filname)
    {
        $fileSize = \File::size(public_path('shortpixel/'.$folder.'/'.$filname));
        return $fileSize;

    }
}
