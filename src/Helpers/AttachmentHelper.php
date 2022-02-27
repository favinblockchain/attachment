<?php

namespace Favinblockchain\Attachment\Helpers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

class AttachmentHelper
{
    protected $media = null;

    protected function setMedia($model)
    {
        if(is_null($this->media)) {
            $this->media = $model->withMedia()->latest()->first()->media;
        }
    }

    public function deleteMediaCompletely($model)
    {
        $delete_variants_result = $this->deleteMediaVariants($model);
        $delete_media_result = $this->deleteMedia($model);
        return (object) [
            'delete_variants_result' => $delete_variants_result,
            'delete_media_result' => $delete_media_result
        ];
    }

    public function deleteMedia($model)
    {
        if ($model->media->count() != 0){
            foreach ($model->media as $media){
                $media->delete();
            }
            return true;
        }else{
            return false;
        }
    }

    public function deleteMediaVariants($model)
    {
        if ($model->media->count() != 0){
            foreach(config('attachment.image_variant_list') as $variant) {
                foreach ($model->media as $media){
                    if($media->hasVariant($variant)) {
                        Storage::disk($media->disk)->delete($media->findVariant($variant)->getDiskPath());
                        $media->findVariant($variant)->delete();
                    }
                }
            }
            return true;
        }else{
            return false;
        }
    }
}
