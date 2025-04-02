<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Upload;
use Response;
use Auth;
use Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Encoders\WebpEncoder;
use enshrined\svgSanitize\Sanitizer;

class AizUploadController extends Controller
{
    public function index(Request $request)
    {

        $all_uploads = (auth()->user()->user_type == 'seller') ? Upload::where('user_id', auth()->user()->id) : Upload::query();
        $search = null;
        $sort_by = null;

        if ($request->search != null) {
            $search = $request->search;
            $all_uploads->where('file_original_name', 'like', '%' . $request->search . '%');
        }

        $sort_by = $request->sort;
        switch ($request->sort) {
            case 'newest':
                $all_uploads->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $all_uploads->orderBy('created_at', 'asc');
                break;
            case 'smallest':
                $all_uploads->orderBy('file_size', 'asc');
                break;
            case 'largest':
                $all_uploads->orderBy('file_size', 'desc');
                break;
            default:
                $all_uploads->orderBy('created_at', 'desc');
                break;
        }

        $all_uploads = $all_uploads->paginate(60)->appends(request()->query());


        return (auth()->user()->user_type == 'seller')
            ? view('seller.uploads.index', compact('all_uploads', 'search', 'sort_by'))
            : view('backend.uploaded_files.index', compact('all_uploads', 'search', 'sort_by'));
    }

    public function create()
    {
        return (auth()->user()->user_type == 'seller')
            ? view('seller.uploads.create')
            : view('backend.uploaded_files.create');
    }


    public function show_uploader(Request $request)
    {
        return view('uploader.aiz-uploader');
    }

    public function upload(Request $request)
    {
        $type = [
            "jpg" => "image", "jpeg" => "image", "png" => "image", "svg" => "image", "gif" => "image",
            "webp" => "image", "mp4" => "video", "zip" => "archive", "pdf" => "document"
        ];

        if ($request->hasFile('aiz_file')) {
            $upload = new Upload;
            $extension = strtolower($request->file('aiz_file')->getClientOriginalExtension());
            
            if (!isset($type[$extension])) {
                return response()->json(["error" => "Unsupported file type"], 400);
            }

            $file_name = pathinfo($request->file('aiz_file')->getClientOriginalName(), PATHINFO_FILENAME);
            $upload->file_original_name = $file_name;
            
            if ($extension == 'svg') {
                $sanitizer = new Sanitizer();
                $cleanSVG = $sanitizer->sanitize(file_get_contents($request->file('aiz_file')));
                file_put_contents($request->file('aiz_file'), $cleanSVG);
            }
            
            $path = $request->file('aiz_file')->store('uploads/all', 'local');
            $size = $request->file('aiz_file')->getSize();
            
            // Image processing
            if ($type[$extension] == 'image' && get_setting('disable_image_optimization') != 1) {
                try {
                    $manager = new ImageManager(new Driver());
                    $img = $manager->read($request->file('aiz_file')->getRealPath());
                    
                    $webp_path = storage_path("app/public/" . str_replace(".$extension", ".webp", $path));
                    $thumbnail_path = str_replace(".webp", "_thumb.webp", $webp_path);
                    
                    if (!file_exists($webp_path)) { // Caching to avoid re-processing
                        $img = $img->resize(1500, null, function ($constraint) {
                            $constraint->aspectRatio();
                        })->encode(new WebpEncoder(quality: 80));
                        $img->save($webp_path);
                    }
                    
                    if (!file_exists($thumbnail_path)) { // Caching for thumbnails
                        $thumbnail = $img->resize(300, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                        $thumbnail->save($thumbnail_path);
                    }
                    
                    $path = str_replace(storage_path("app/public/"), "", $webp_path);
                } catch (\Exception $e) {
                    return response()->json(["error" => "Image processing failed"], 500);
                }
            }

            if (env('FILESYSTEM_DRIVER') != 'local') {
                Storage::disk(env('FILESYSTEM_DRIVER'))->put($path, file_get_contents(storage_path("app/public/" . $path)), ['visibility' => 'public']);
            }

            $upload->extension = 'webp';
            $upload->file_name = $path;
            $upload->user_id = Auth::user()->id;
            $upload->type = $type[$extension];
            $upload->file_size = $size;
            $upload->save();

            return response()->json(["success" => "File uploaded successfully", "file" => $upload]);
        }

        return response()->json(["error" => "No file uploaded"], 400);
    }

    public function get_uploaded_files(Request $request)
    {
        $uploads = Upload::where('user_id', Auth::user()->id);
        if ($request->search != null) {
            $uploads->where('file_original_name', 'like', '%' . $request->search . '%');
        }
        if ($request->sort != null) {
            switch ($request->sort) {
                case 'newest':
                    $uploads->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $uploads->orderBy('created_at', 'asc');
                    break;
                case 'smallest':
                    $uploads->orderBy('file_size', 'asc');
                    break;
                case 'largest':
                    $uploads->orderBy('file_size', 'desc');
                    break;
                default:
                    $uploads->orderBy('created_at', 'desc');
                    break;
            }
        }
        return $uploads->paginate(60)->appends(request()->query());
    }

    public function destroy($id)
    {
        $upload = Upload::findOrFail($id);

        if (auth()->user()->user_type == 'seller' && $upload->user_id != auth()->user()->id) {
            flash(translate("You don't have permission for deleting this!"))->error();
            return back();
        }
        try {
            if (env('FILESYSTEM_DRIVER') != 'local') {
                Storage::disk(env('FILESYSTEM_DRIVER'))->delete($upload->file_name);
                if (file_exists(public_path() . '/' . $upload->file_name)) {
                    unlink(public_path() . '/' . $upload->file_name);
                }
            } else {
                unlink(public_path() . '/' . $upload->file_name);
            }
            $upload->delete();
            flash(translate('File deleted successfully'))->success();
        } catch (\Exception $e) {
            $upload->delete();
            flash(translate('File deleted successfully'))->success();
        }
        return back();
    }

    public function bulk_uploaded_files_delete(Request $request)
    {
        if ($request->id) {
            foreach ($request->id as $file_id) {
                $this->destroy($file_id);
            }
            return 1;
        } else {
            return 0;
        }
    }

    public function get_preview_files(Request $request)
    {
        $ids = explode(',', $request->ids);
        $files = Upload::whereIn('id', $ids)->get();
        $new_file_array = [];
        foreach ($files as $file) {
            $file['file_name'] = my_asset($file->file_name);
            if ($file->external_link) {
                $file['file_name'] = $file->external_link;
            }
            $new_file_array[] = $file;
        }
        // dd($new_file_array);
        return $new_file_array;
        // return $files;
    }

    public function all_file()
    {
        $uploads = Upload::all();
        foreach ($uploads as $upload) {
            try {
                if (env('FILESYSTEM_DRIVER') != 'local') {
                    Storage::disk(env('FILESYSTEM_DRIVER'))->delete($upload->file_name);
                    if (file_exists(public_path() . '/' . $upload->file_name)) {
                        unlink(public_path() . '/' . $upload->file_name);
                    }
                } else {
                    unlink(public_path() . '/' . $upload->file_name);
                }
                $upload->delete();
                flash(translate('File deleted successfully'))->success();
            } catch (\Exception $e) {
                $upload->delete();
                flash(translate('File deleted successfully'))->success();
            }
        }

        Upload::query()->truncate();

        return back();
    }

    //Download project attachment
    public function attachment_download($id)
    {
        $project_attachment = Upload::find($id);
        try {
            $file_path = public_path($project_attachment->file_name);
            return Response::download($file_path);
        } catch (\Exception $e) {
            flash(translate('File does not exist!'))->error();
            return back();
        }
    }
    //Download project attachment
    public function file_info(Request $request)
    {
        $file = Upload::findOrFail($request['id']);

        return (auth()->user()->user_type == 'seller')
            ? view('seller.uploads.info', compact('file'))
            : view('backend.uploaded_files.info', compact('file'));
    }
}
