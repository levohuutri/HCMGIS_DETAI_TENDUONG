<?php

namespace app\modules\cms\services;

use app\modules\cms\models\AuthUser;
use app\modules\cms\models\AuthUserInfo;
use app\modules\cms\models\FileRepo;
use Exception;
use Yii;
use yii\db\Query;

class FileService
{
    public static $UPLOAD_DIR = 'uploads/';
    public static $FILE_DELETE = [
        'ALIVE' => 1,
        'DELETED' => 0
    ];

    public static function Upload($file_tmp, $filename) {
        $fileInfor = self::InitFileInformation($filename);
        $path = self::$UPLOAD_DIR . $fileInfor['path'];
        if (move_uploaded_file($file_tmp, $path)) {
            $file = new FileRepo([
                'name' => $fileInfor['name'],
                'slug' => $fileInfor['slug'],
                'path' => $fileInfor['path'],
                'type' => $fileInfor['type'],
                'delete' => self::$FILE_DELETE['ALIVE'],
                'created_by' => Yii::$app->user->id,
            ]);

            if ($file->save()) {
                return $file;
            }
        }

        return false;
    }

    public static function InitFileInformation($name)
    {
        list($newname, $ext) = self::ParseFileNameToNameAndExtension($name);
        $slug = $newname . '_'. uniqid();

        $fileInfor = [
            'name' => $name,
            'slug' => $slug,
            'path' => $slug . '.' . $ext,
            'type' => $ext,
        ];

        return $fileInfor;
    }

    public static function ParseFileNameToNameAndExtension($name) {
        $parseImgname = explode('.', $name);
        $ext = end($parseImgname);
        array_pop($parseImgname);
        $newname = implode('_', $parseImgname);
        $newname = SiteService::ConvertStringToSlug($newname, '_');
        return [$newname, $ext];
    }

    public static function ScaleImageToThumbnail($path) {
        try {
            $img = new \Imagick(realpath($path));
            $img->scaleImage(600, 400);
            $img->writeImage(realpath($path));
            return true;
        } catch(Exception $e) {
            return false;
        }
    }

    public static function DeleteFile($file) {
        $path = self::$UPLOAD_DIR . $file;
        if(file_exists($path)) {
            if(unlink($path)) {
                $fileRepo = FileRepo::findOne(['path' => $file]);
                $fileRepo->delete();
            }
        }
    }

    public static function SaveFile($slug, $ext) {
        $file = new FileRepo([
            'name' => "$slug.$ext",
            'slug' => $slug,
            'path' => "$slug.$ext",
            'type' => $ext,
            'delete' => self::$FILE_DELETE['ALIVE'],
            'created_by' => Yii::$app->user->id,
        ]);
        if($file->save()) { return $file->path; }
        return null;
    }
}