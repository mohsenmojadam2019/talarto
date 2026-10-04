<?php
namespace App\Support;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class UploadImage {
 public static function store(?UploadedFile $file,string $folder):?string {
  if(!$file)return null;
  $name=Str::uuid().'.'.strtolower($file->getClientOriginalExtension() ?: 'jpg');
  $path=$file->storeAs('uploads/'.$folder,$name,'public');
  return Storage::url($path);
 }
 public static function delete(?string $url):void {
  if(!$url || !str_starts_with($url,'/storage/'))return;
  Storage::disk('public')->delete(ltrim(substr($url,strlen('/storage/')),'/'));
 }
}
