<?php

namespace app\modules\cms\services;

use app\modules\cms\models\AuthUser;
use app\modules\cms\models\Comment;
use app\modules\cms\models\Map;
use app\modules\cms\models\PointCloud;
use app\modules\cms\models\VietnamDistricts;
use Yii;
use yii\db\Query;

class MapService
{
    public static $STATUS = [
        'ACTIVE' => 1,
        'PENDING' => 0
    ];

    public static $DELETE = [
        'ALIVE' => 1,
        'DELETED' => 0
    ];

    public static $PUBLISH_TYPE = [
        'PUBLIC' => 1,
        'PRIVATE' => 0
    ];

    public static $SORT_MAP_DECODE = [
        'most-recent' => ['column' => 'm.created_at', 'sort' => 'DESC'],
        'most-view' => ['column' => 'm.count_view', 'sort' => 'DESC'],
    ];

    public static $RESPONSES = [
        'ERROR' => 'Action error',
        'SAVE_SUCCESS' => 'Save map successfully',
        'CAN_NOT_SAVE' => 'Can not save this map',
        'NOT_FOUND' => 'Map not found',
        'CHANGE_PUBLISH_TYPE_SUCCESS' => 'Change publish type successfully',
        'DELETE_SUCCESS' => 'Delete map successfully'
    ];

    public static function GetMaps($publishType = null, $page = 1, $perpage = 9, $title = '', $sort = 'most-recent') {
        $query = (new Query())
                ->select(['m.*', 'u.fullname', 'u.slug'])
                ->from('map m')
                ->leftJoin('auth_user u', 'm.created_by = u.id')
                ->where(['and', ['m.status' => self::$STATUS['ACTIVE']], ['m.delete' => self::$DELETE['ALIVE']]]);
        if($publishType !== null) {
            $query->andWhere(['m.publish_type' => $publishType]);
        }

        if($title != '') {
            $title = mb_strtolower($title);
            $query->andWhere(['like', 'LOWER(m.title)', $title]);
        }

        $total = $query->select('COUNT(*)')->column();
        list($limit, $offset) = SiteService::GetLimitAndOffset($page, $perpage);

        $maps = $query->select(['m.*', 'u.fullname author', 'u.slug author_slug'])
                    ->orderBy(self::$SORT_MAP_DECODE[$sort]['column'].' '.self::$SORT_MAP_DECODE[$sort]['sort'])
                    ->limit($limit)
                    ->offset($offset)
                    ->all();
        $pagination = SiteService::CreatePaginationMetadata($total[0], $page, $perpage, count($maps));
        return [$maps, $pagination];
    }

    public static function GetMapsByUserID($userid = null, $publishType = null) {
        $userid = $userid ? $userid : Yii::$app->user->id;
        $query = (new Query())
                ->select(['m.*', 'u.fullname author', 'u.slug author_slug'])
                ->from('map m')
                ->leftJoin('auth_user u', 'm.created_by = u.id')
                ->where(['and', ['m.status' => self::$STATUS['ACTIVE']], ['m.delete' => self::$DELETE['ALIVE']]])
                ->andWhere(['m.created_by' => $userid]);
        if($publishType !== null) {
            $query->andWhere(['m.publish_type' => $publishType]);
        }

        $maps = $query->orderBy('m.created_at desc')->all();
        return $maps;
    }

    public static function GetMapByID($mapid) {
        $currentUserId = Yii::$app->user->id;
        $map = (new Query())
                ->select('*')
                ->from('map')
                ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
                ->andWhere(['id' => $mapid])
                ->one();
        return $map['created_by'] == $currentUserId ? $map : null;
    }

    public static function GetMapBySlug($slug) {
        $map = (new Query())
                ->select(['m.*', 'u.fullname author', 'u.slug author_slug'])
                ->from('map m')
                ->leftJoin('auth_user u', 'm.created_by = u.id')
                ->where(['and', ['m.status' => self::$STATUS['ACTIVE']], ['m.delete' => self::$DELETE['ALIVE']]])
                ->andWhere(['m.slug' => $slug])
                ->one();

        if($map) {
            $map['base_layers'] = json_decode($map['base_layers'], true);
            $map['overlay_layers'] = json_decode($map['overlay_layers'], true);
            $map['pointcloud_ids'] = json_decode($map['pointcloud_ids'], true);
        }
        
        return $map;
    }

    public static function GetTotalMapsOfUser($userid) {
        $total = Map::find()
            ->select('COUNT(*)')
            ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
            ->andWhere(['created_by' => $userid])
            ->column();
        return $total[0];
    }

    public static function ChangePublishType($data){
        $userid = Yii::$app->user->id;
        $mapid = $data['mapid'];
        $map = Map::findOne($mapid);

        if ($map && (AuthService::IsAdmin() || $map->created_by == $userid)) {
            $map->publish_type = $map->publish_type ? 0 : 1;
            if ($map->save()) {
                SiteService::WriteLog($userid, SiteService::$ACTIVITIES['CHANGE_PUBLISH_TYPE_MAP'], $map->id, $map->className(), $map->title);
                return true;
            }
        }
        return false;
    }

    public static function Delete($data) {
        $userid = Yii::$app->user->id;
        $mapid = $data['mapid'];
        $map = Map::findOne($mapid);

        if ($map && (AuthService::IsAdmin() || $map->created_by == $userid)) {
            $map->delete = self::$DELETE['DELETED'];
            if ($map->save()) {
                SiteService::WriteLog($userid, SiteService::$ACTIVITIES['DELETE_MAP'], $map->id, $map->className(), $map->title);
                return true;
            }
        }
        return false;
    }

    public static function SaveMap($model, $data) {
        $model->title = $data['title'];
        $model->description = $data['description'];
        $model->base_layers = $data['base_layers'];
        $model->overlay_layers = $data['overlay_layers'];
        $model->pointcloud_ids = $data['pointcloud_ids'];
        $model->publish_type = $data['publish_type'];
        $model->thumbnail = $data['thumbnail'];
        $model->hash = $model->hash ? $model->hash : uniqid();
        $model->slug = SiteService::ConvertStringToSlug($model->title) .'-'. $model->hash;
        $model->created_by = Yii::$app->user->id;
        $model->status = self::$STATUS['ACTIVE'];
        $model->delete = self::$DELETE['ALIVE'];
        $model->count_view = $model->count_view ? $model->count_view : 0;
        if($model->save()) return true;
        else return $model->getErrorSummary(true)[0];
    }

    public static function GetDistrictsGeojson($districtids)
    {
        $districts = VietnamDistricts::find()->select(['ST_AsGeojson(geom) as geom', 'ten', 'tinh_thanh', 'ma'])->where(['ma' => $districtids])->asArray()->all();

        return self::GeojonsToFeatureCollection($districts);
    }

    public static function GeojonsToFeatureCollection($data)
    {
        $collection = [
            'type' => 'FeatureCollection',
            'features' => []
        ];

        foreach ($data as $d) {
            $name = $d['ten'] . (isset($d['tinh_thanh']) ? ' - ' . $d['tinh_thanh'] : '');
            $geojson = json_decode($d['geom'], true);
            array_push($collection['features'], [
                'type' => 'Feature',
                'properties' => ['name' => $name, 'id' => $d['ma']],
                'geometry' => $geojson
            ]);
        }

        return $collection;
    }
} 