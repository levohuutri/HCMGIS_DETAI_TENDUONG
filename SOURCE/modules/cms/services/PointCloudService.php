<?php

namespace app\modules\cms\services;

use app\modules\cms\models\PointCloud;
use app\modules\cms\models\PointCloudInteractive;
use app\modules\cms\models\PointCloudStep;
use Exception;
use Throwable;
use Yii;
use yii\db\Query;

class PointCloudService
{
    public static $RESPONSES = [
        'UPLOAD_SUCCESS' => 'Your point cloud is being processed. We\'ll send you a notification when it\'s done and your point cloud is ready to view.',
        'PROCESS_SUCCESS' => 'Your point cloud is ready to view.',
        'PROCESS_ERROR' => 'Something went wrong with your point cloud. Please check and upload it again.',
        'EDIT_SUCCESS' => 'Edit point cloud successfully.',
        'DELETE_SUCCESS' => 'Delete point cloud successfully.',
        'CHANGE_PUBLISH_TYPE_SUCCESS' => 'Change publish type successfully.',
        'EMPTY_LIST' => 'Empty list',
        'ACTION_ERROR' => 'Action error'
    ];

    public static $STATUS = [
        'ACTIVE' => 1,
        'DEACTIVE' => 0
    ];

    public static $DELETE = [
        'ALIVE' => 1,
        'DELETED' => 0
    ];

    public static $TYPE = [
        'PUBLIC' => 1,
        'PRIVATE' => 0
    ];

    public static $STEP = [
        'UPLOADED' => 1,
        'RUNNING' => 2,
        'DONE_WITH_SUCCESS' => 3,
        'DONE_WITH_ERROR' => 4
    ];

    public static $INTERACTIVE = [
        'LIKE' => 1,
        'UNLIKE' => 0,
        'FOLLOW' => 1,
        'UNFOLLOW' => 0
    ];

    public static $DEFAULT = [
        'COUNTER' => [
            'like' => 0,
            'follow' => 0
        ],
        'USER_INTERACTIVE' => [
            'like' => 0,
            'follow' => 0
        ]
    ];

    public static $SORT_MAP_DECODE = [
        'by-title' => ['column' => 'p.title', 'sort' => 'ASC'],
        'most-recent' => ['column' => 'p.created_at', 'sort' => 'DESC'],
        'most-view' => ['column' => 'count_view', 'sort' => 'DESC'],
        'most-like' => ['column' => 'count_like', 'sort' => 'DESC'],
        'most-download' => ['column' => 'count_download', 'sort' => 'DESC'],
        'most-rating' => ['column' => 'avg_rating', 'sort' => 'DESC'],

    ];

    public static $VIEWER_FOLDER = 'pointclouds/';

    public static $OBJECT_TYPE = 'app/modules/cms/models/PointCloud';

    public static function Create($data)
    {
        $transaction = Yii::$app->db->beginTransaction();
        $userid = Yii::$app->user->id;
        try {
            $metadata = self::FormatMetadata($data);

            $point = new PointCloud();
            $point->load($data);
            $point->tags = self::FormatTags($point->tags);
            $point->created_by = $userid;
            $point->status = self::$STATUS['ACTIVE'];
            $point->delete = self::$DELETE['ALIVE'];
            $point->slug = SiteService::ConvertStringToSlug($point->title) .'-'. uniqid();
            $point->count_view = 0;
            $point->count_download = 0;
            $point->metadata = json_encode($metadata);
            $point->save();

            self::UpdateStepLog($point, self::$STEP['UPLOADED']);
            self::SendDataToPointCloudServer($point);
            SiteService::WriteLog($userid, SiteService::$ACTIVITIES['UPLOAD_POINT'], $point->id, $point->className(), $point->title);

            $transaction->commit();
            return true;
        } catch (Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public static function Edit($data)
    {
    }

    public static function Delete($data)
    {
        $userid = Yii::$app->user->id;
        $pointid = $data['pointid'];
        $point = PointCloud::findOne($pointid);
        if ($point && (AuthService::IsAdmin() || $point->created_by == $userid)) {
            $point->delete = self::$DELETE['DELETED'];
            if ($point->save()) {
                SiteService::WriteLog($userid, SiteService::$ACTIVITIES['DELETE_POINT'], $point->id, $point->className(), $point->title);
                return true;
            }
        }
        return false;
    }

    public static function FormatMetadata($data) 
    {
        $params = $data['Params'];
        $metadata = [
            'params' => $params
        ];

        return $metadata;
    }

    public static function FormatTags($string)
    {
        $tags = explode(',', $string);
        $tags = array_map(function ($tag) {
            return trim($tag);
        }, $tags);
        return json_encode($tags);
    }

    public static function SendDataToPointCloudServer($point) 
    {
        $convertFilePath = self::GetFullPathOfPointCloudFile($point->point_file);
        $targetDirectory = self::CreateFolderToSavePointCloud($point->slug);
        $metadata = json_decode($point->metadata, true);

        $data = [
            'params' => $metadata['params'],
            'pointcloud' => [
                'convertFilePath' => $convertFilePath,
                'targetDirectory' => $targetDirectory,
                'slug' => $point->slug
            ],
        ];

        $url = 'http://192.168.1.61:1234/run-potree-converter';
        $ch = curl_init($url);
        $postString = http_build_query($data, '', '&');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        self::UpdateStepLog($point, self::$STEP['RUNNING']);
    }

    public static function UpdateStepLog($point, $step) {
        $stepLog = new PointCloudStep([
            'point_cloud_id' => $point->id,
            'step' => $step
        ]);
        $stepLog->save();

        $point->current_step = $step;
        if($step == self::$STEP['DONE_WITH_ERROR']) {
            $point->delete = self::$DELETE['DELETED'];
        }
        $point->save();
    }

    public static function CreateFolderToSavePointCloud($foldername)
    {
        $folder = self::$VIEWER_FOLDER . $foldername;
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }

        return SiteService::FormatPath(getcwd() . '/' . $folder);
    }

    public static function GetFullPathOfPointCloudFile($filename)
    {
        $filepath = FileService::$UPLOAD_DIR . $filename;

        return SiteService::FormatPath(getcwd() . '/' . $filepath);
    }

    public static function ChangePublishType($data)
    {
        $userid = Yii::$app->user->id;
        $pointid = $data['pointid'];

        $point = PointCloud::findOne($pointid);
        if ($point && (AuthService::IsAdmin() || $point->created_by == $userid)) {
            $point->type = $point->type ? 0 : 1;
            if ($point->save()) {
                SiteService::WriteLog($userid, SiteService::$ACTIVITIES['CHANGE_PUBLISH_TYPE_POINT'], $point->id, $point->className(), $point->title);
                return true;
            }
        }
        return false;
    }

    public static function GetTotalPointsOfUser($userid)
    {
        $total = PointCloud::find()
            ->select('COUNT(*)')
            ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
            ->andWhere(['current_step' => self::$STEP['DONE_WITH_SUCCESS']])
            ->andWhere(['created_by' => $userid])
            ->column();
        return $total[0];
    }

    public static function GetUserPoints($userid, $page, $perpage, $type = 1)
    {
        list($limit, $offset) = SiteService::GetLimitAndOffset($page, $perpage);
        $subQuery = self::InteractiveQueryBuilder();
        $query = (new Query())
            ->select(['p.*', 'i.*', 'u.fullname author', 'u.id author_id', 'u.avatar author_avatar', 'u.slug author_slug'])
            ->from(['p' => 'point_cloud'])
            ->leftJoin(['i' => $subQuery], 'p.id = i.point_cloud_id')
            ->leftJoin(['u' => 'auth_user'], 'p.created_by = u.id')
            ->where(['and', ['p.status' => self::$STATUS['ACTIVE']], ['p.delete' => self::$DELETE['ALIVE']]])
            ->andWhere(['p.current_step' => self::$STEP['DONE_WITH_SUCCESS']]);

        if ($type == self::$TYPE['PRIVATE']) {
            $query->andWhere(['and', ['p.type' => self::$TYPE['PRIVATE']], ['created_by' => Yii::$app->user->id]]);
        } else {
            $query->andWhere(['and', ['p.type' => self::$TYPE['PUBLIC']], ['created_by' => $userid]]);
        }

        $points = $query
            ->orderBy('created_at desc')
            ->limit($limit)->offset($offset)->all();

        $points = self::GetFullInformation($points);
        return $points;
    }

    public static function GetInteractedPoints($userid, $page, $perpage, $interact)
    {
        list($limit, $offset) = SiteService::GetLimitAndOffset($page, $perpage);

        $userid = Yii::$app->user->id;
        $subQuery = self::InteractiveQueryBuilder();
        $query = (new Query())
            ->select(['p.*', 'i.*', 'u.fullname author', 'u.id author_id', 'u.avatar author_avatar', 'u.slug author_slug'])
            ->from(['p' => 'point_cloud'])
            ->leftJoin(['i' => $subQuery], 'p.id = i.point_cloud_id')
            ->leftJoin(['u' => 'auth_user'], 'p.created_by = u.id')
            ->leftJoin('point_cloud_interactive pi', 'p.id = pi.point_cloud_id AND pi.auth_user_id = ' . $userid)
            ->where(['and', ['p.status' => self::$STATUS['ACTIVE']], ['p.delete' => self::$DELETE['ALIVE']]])
            ->andWhere(['p.current_step' => self::$STEP['DONE_WITH_SUCCESS']])
            ->andWhere(['p.type' => self::$TYPE['PUBLIC']]);

        if ($interact == 'LIKED') {
            $query->andWhere(['pi.is_like' => self::$INTERACTIVE['LIKE']]);
        } else if ($interact == 'FOLLOWING') {
            $query->andWhere(['pi.is_follow' => self::$INTERACTIVE['FOLLOW']]);
        }

        $points = $query
            ->orderBy('created_at desc')
            ->limit($limit)->offset($offset)->all();

        $points = self::GetFullInformation($points);
        return $points;
    }

    public static function GetPoints($page, $perpage, $keyword = '', $sort = 'most-recent', $type = 1)
    {
        $subQuery = self::InteractiveQueryBuilder();
        $query = (new Query())
            ->select(['p.*', 
                    'i.count_like', 'i.count_follow', 'i.count_rating', 'i.avg_rating', 
                    'u.fullname author', 'u.avatar author_avatar', 'u.slug author_slug'])
            ->from(['p' => 'point_cloud'])
            ->leftJoin(['i' => $subQuery], 'p.id = i.point_cloud_id')
            ->leftJoin(['u' => 'auth_user'], 'p.created_by = u.id')
            ->where(['and', ['p.status' => self::$STATUS['ACTIVE']], ['p.delete' => self::$DELETE['ALIVE']]])
            ->andWhere(['p.current_step' => self::$STEP['DONE_WITH_SUCCESS']])
            ->andWhere(['p.type' => self::$TYPE['PUBLIC']]);

        if ($keyword) {
            $keyword = mb_strtolower($keyword);
            $query->andWhere(['or', ['like', 'LOWER(p.title)', $keyword], ['like', 'LOWER(p.tags)', $keyword]]);
        }

        $total = $query->select('COUNT(*)')->column();
        list($limit, $offset) = SiteService::GetLimitAndOffset($page, $perpage);
        $sortColumn = self::$SORT_MAP_DECODE[$sort]['column'];
        $sortType = self::$SORT_MAP_DECODE[$sort]['sort'];
        $points = $query->select(['p.*', 
                'i.count_like', 'i.count_follow', 'i.count_rating', 'i.avg_rating', 
                'u.fullname author', 'u.avatar author_avatar', 'u.slug author_slug'])
            ->orderBy([new \yii\db\Expression($sortColumn. ' IS NULL ASC, ' .$sortColumn. ' '.$sortType)])
            ->limit($limit)->offset($offset)->all();

        $pagination = SiteService::CreatePaginationMetadata($total[0], $page, $perpage, count($points));
        $points = self::GetFullInformation($points);
        return [$points, $pagination];
    }

    public static function GetPointsByTag($page, $perpage, $tag, $sort = 'most-recent', $type = 1) {
        $subQuery = self::InteractiveQueryBuilder();
        $query = (new Query())
            ->select(['p.*', 
                    'i.count_like', 'i.count_follow', 'i.count_rating', 'i.avg_rating', 
                    'u.fullname author', 'u.avatar author_avatar', 'u.slug author_slug'])
            ->from(['p' => 'point_cloud'])
            ->leftJoin(['i' => $subQuery], 'p.id = i.point_cloud_id')
            ->leftJoin(['u' => 'auth_user'], 'p.created_by = u.id')
            ->where(['and', ['p.status' => self::$STATUS['ACTIVE']], ['p.delete' => self::$DELETE['ALIVE']]])
            ->andWhere(['p.current_step' => self::$STEP['DONE_WITH_SUCCESS']])
            ->andWhere(['p.type' => self::$TYPE['PUBLIC']])
            ->andWhere(['like', 'tags', $tag]);

        $total = $query->select('COUNT(*)')->column();
        list($limit, $offset) = SiteService::GetLimitAndOffset($page, $perpage);
        $sortColumn = self::$SORT_MAP_DECODE[$sort]['column'];
        $sortType = self::$SORT_MAP_DECODE[$sort]['sort'];
        $points = $query->select(['p.*', 
                'i.count_like', 'i.count_follow', 'i.count_rating', 'i.avg_rating', 
                'u.fullname author', 'u.avatar author_avatar', 'u.slug author_slug'])
            ->orderBy([new \yii\db\Expression($sortColumn. ' IS NULL ASC, ' .$sortColumn. ' '.$sortType)])
            ->limit($limit)->offset($offset)->all();

        $pagination = SiteService::CreatePaginationMetadata($total[0], $page, $perpage, count($points));
        $points = self::GetFullInformation($points);
        return [$points, $pagination];
    }

    public static function GetPointsByIds($ids) {
        $subQuery = self::InteractiveQueryBuilder();
        $points = (new Query())
            ->select(['p.*', 
                    'i.count_like', 'i.count_follow', 'i.count_rating', 'i.avg_rating', 
                    'u.fullname author', 'u.avatar author_avatar', 'u.slug author_slug'])
            ->from(['p' => 'point_cloud'])
            ->leftJoin(['i' => $subQuery], 'p.id = i.point_cloud_id')
            ->leftJoin(['u' => 'auth_user'], 'p.created_by = u.id')
            ->where(['and', ['p.status' => self::$STATUS['ACTIVE']], ['p.delete' => self::$DELETE['ALIVE']]])
            ->andWhere(['p.current_step' => self::$STEP['DONE_WITH_SUCCESS']])
            ->andWhere(['p.type' => self::$TYPE['PUBLIC']])
            ->andWhere(['p.id' => $ids])
            ->all();

        return $points;
    }

    public static function GetPointsMap($keyword, $geojson)
    {
        $containsql = !$geojson ? "COALESCE('t') as contain" : ($geojson['properties']['layerType'] == 'circle' ?
                "ST_Contains(ST_SetSRID(ST_Buffer(ST_Point(" . $geojson['geometry']['coordinates'][0] . ", " . $geojson['geometry']['coordinates'][1] . ")::geography," . $geojson['properties']['radius'] . ")::geometry,4326), ST_SetSRID(ST_Point(i.lng::float,i.lat::float),4326)) as contain" :
                "ST_Contains(ST_SetSRID(ST_GeomFromGeoJSON('" . json_encode($geojson['geometry'], true) . "'),4326), ST_SetSRID(ST_Point(i.lng::float,i.lat::float),4326)) as contain");

        $query = "SELECT p.*, u.fullname as author, u.slug as author_slug, c.contain
                    FROM point_cloud as p, auth_user as u, (SELECT $containsql, id FROM point_cloud as i) as c
                    WHERE c.id = p.id AND p.created_by = u.id AND c.contain = 't' AND lat != '' AND LOWER(title) LIKE '%$keyword%'
                    AND p.type = " . self::$TYPE['PUBLIC'] . " AND p.status = " . self::$STATUS['ACTIVE'] . " AND p.delete = " . self::$DELETE['ALIVE'] . "AND p.current_step = " . self::$STEP['DONE_WITH_SUCCESS'] . " 
                    ORDER BY p.created_at DESC";

        $points = SiteService::CommandQueryAll($query);
        return $points;
    }

    public static function GetPointCloudBySlug($slug)
    {
        $subQuery = self::InteractiveQueryBuilder();
        $point = (new Query())
            ->select(['p.title', 'p.description', 'p.lat', 'p.lng', 'p.slug', 'p.tags', 'p.count_view', 'p.count_download', 'p.count_points', 'p.type', 'p.current_step', 'p.id', 'p.created_at', 'p.collectors', 'p.reference', 
                    'i.*', 'u.fullname author', 'u.id author_id', 'u.avatar author_avatar', 'u.slug author_slug'])
            ->from(['p' => 'point_cloud'])
            ->leftJoin(['i' => $subQuery], 'p.id = i.point_cloud_id')
            ->leftJoin(['u' => 'auth_user'], 'p.created_by = u.id')
            ->where(['and', ['p.status' => self::$STATUS['ACTIVE']], ['p.delete' => self::$DELETE['ALIVE']]])
            ->andWhere(['p.current_step' => self::$STEP['DONE_WITH_SUCCESS']])
            ->andWhere(['p.slug' => $slug])
            ->one();
        return $point;
    }

    public static function GetPointCloudObjectBySlug($slug)
    {
        $point = PointCloud::find()
            ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
            ->andWhere(['slug' => $slug])
            ->asArray()
            ->one();
        return $point;
    }

    public static function InteractiveQueryBuilder()
    {
        return (new Query())
            ->select([
                'point_cloud_id',
                'COUNT(NULLIF(is_like, 0)) as count_like',
                'COUNT(NULLIF(is_follow, 0)) as count_follow',
                'COUNT(NULLIF(rating, 0)) as count_rating',
                'AVG(NULLIF(rating, 0)) as avg_rating'
            ])
            ->from('point_cloud_interactive')
            ->groupBy('point_cloud_id');
    }

    public static function GetFullInformation($points)
    {
        $ids = [];
        foreach ($points as $point) {
            array_push($ids, $point['id']);
        }

        // $clouds = self::GetClouds($ids, self::$OBJECT_TYPE);
        $currentUser = self::GetInteractivesOfCurrentUser($ids);

        foreach ($points as &$point) {
            // $point['cloud'] = isset($clouds[$point['id']]) ? $clouds[$point['id']] : '';
            $point['current_user'] = isset($currentUser[$point['id']]) ? $currentUser[$point['id']] : self::$DEFAULT['USER_INTERACTIVE'];
        }

        return $points;
    }

    public static function GetAuthors($pointids)
    {
        $authors = (new Query())
            ->select(['u.fullname', 'u.slug', 'u.avatar', 'u.id', 'p.id as point_id'])
            ->from('auth_user u')
            ->leftJoin('point_cloud p', 'p.created_by = u.id')
            ->where(['p.id' => $pointids])
            ->all();

        return SiteService::ArrayIndexBy($authors, 'point_id');
    }

    public static function GetCounterInteractive($pointids)
    {
        $counter = PointCloudInteractive::find()
            ->select(['COUNT(NULLIF(is_like, 0)) as like', 'COUNT(NULLIF(is_follow, 0)) as follow', 'point_cloud_id'])
            ->where(['point_cloud_id' => $pointids])
            ->groupBy('point_cloud_id')
            ->asArray()
            ->all();
        return SiteService::ArrayIndexBy($counter, 'point_cloud_id');
    }

    public static function GetClouds($objectids, $objecttye)
    {
        // Get cloud file here
        $clouds = [];
        return SiteService::ArrayIndexBy($clouds, 'object_id');
    }

    public static function GetInteractivesOfCurrentUser($pointids)
    {
        $userid = Yii::$app->user->id;
        $interactive = PointCloudInteractive::find()
            ->select(['point_cloud_id', 'is_like as like', 'is_follow as follow', 'rating'])
            ->where(['and', ['auth_user_id' => $userid], ['point_cloud_id' => $pointids]])
            ->asArray()
            ->all();

        return SiteService::ArrayIndexBy($interactive, 'point_cloud_id');
    }

    public static function Interactive($data)
    {
        $pointid = $data['pointid'];
        $pointname = $data['pointname'];
        $type = $data['type'];
        $userid = Yii::$app->user->id;

        $interactive = PointCloudInteractive::find()->where(['and', ['auth_user_id' => $userid], ['point_cloud_id' => $pointid]])->one();
        if (!$interactive) {
            $interactive = new PointCloudInteractive([
                'auth_user_id' => $userid,
                'point_cloud_id' => $pointid,
                'is_like' => 0,
                'is_follow' => 0,
                'rating' => 0
            ]);
        }

        switch ($type) {
            case 'LIKE':
                $interactive->is_like = self::$INTERACTIVE['LIKE'];
                $activity = SiteService::$ACTIVITIES['LIKE_POINT'];
                break;
            case 'UNLIKE':
                $interactive->is_like = self::$INTERACTIVE['UNLIKE'];
                $activity = SiteService::$ACTIVITIES['UNLIKE_POINT'];
                break;
            case 'FOLLOW':
                $interactive->is_follow = self::$INTERACTIVE['FOLLOW'];
                $activity = SiteService::$ACTIVITIES['FOLLOW_POINT'];
                break;
            case 'UNFOLLOW':
                $interactive->is_follow = self::$INTERACTIVE['UNFOLLOW'];
                $activity = SiteService::$ACTIVITIES['UNFOLLOW_POINT'];
                break;
            case 'RATING':
                $star = $data['star'];
                $interactive->rating = $star;
                $activity = SiteService::$ACTIVITIES['RATE_POINT'];
                break;
            default:
                break;
        }

        if ($interactive->save()) {
            SiteService::WriteLog($userid, $activity, $pointid, self::$OBJECT_TYPE, $pointname);
            return true;
        }
        return false;
    }

    public static function GetEPSGCodes() {
        $coords = (new Query())
            ->select(['coord_ref_sys_code', 'coord_ref_sys_name'])
            ->from('epsg_coordinatereferencesystem')
            ->orderBy('coord_ref_sys_code') 
            ->all();

        $codes = [];
        foreach($coords as $c) {
            array_push($codes, $c['coord_ref_sys_code'].' ('.$c['coord_ref_sys_name'].')');
        }
        return $codes;
    }

    public static function GenerateProj4Code($epsgCode) {
        $projinfoPath = 'C:\OSGeo4W64\bin\projinfo.exe';
        $command = $projinfoPath . ' EPSG:' . $epsgCode;
        $output = shell_exec($command);
        $arr = explode("\n", $output);
        return trim($arr[1]);
    }

    public static function GetPointsViaApi($page, $perpage) {
        $query = (new Query())
            ->select(['slug', 'title', 'thumbnail', 'description', 'created_at'])
            ->from('point_cloud')
            ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
            ->andWhere(['and', ['current_step' => self::$STEP['DONE_WITH_SUCCESS']], ['type' => self::$TYPE['PUBLIC']]]);

        $total = $query->select('COUNT(*)')->column();
        list($limit, $offset) = SiteService::GetLimitAndOffset($page, $perpage);
        
        $points = $query->select(['slug', 'title', 'thumbnail', 'description'])
            ->orderBy('created_at DESC')
            ->limit($limit)->offset($offset)->all();

        $pagination = SiteService::CreatePaginationMetadata($total[0], $page, $perpage, count($points));
        return [$points, $pagination];
    } 

    public static function FormatPointCloudsDataToResponseViaApi($points) {
        foreach($points as &$point) {
            $point['url'] = 'https://pointcloud.hcmgis.vn/app/point-cloud/detail/' . $point['slug'];
            $point['thumbnail'] = 'https://pointcloud.hcmgis.vn/uploads/' . $point['thumbnail'];
        }
        return $points;
    }
}
