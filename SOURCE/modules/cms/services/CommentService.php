<?php

namespace app\modules\cms\services;

use app\modules\cms\models\AuthUser;
use app\modules\cms\models\Comment;
use Yii;
use yii\db\Query;

class CommentService
{
    public static $STATUS = [
        'ACTIVE' => 1,
        'PENDING' => 0
    ];

    public static $DELETE = [
        'ALIVE' => 1,
        'DELETED' => 0
    ];

    public static $RESPONSES = [
        'COMMENT_SUCCESS' => 'Comment successfully! We will publish later',
        'COMMENT_ERROR' => 'Can not submit comment'
    ];

    public static function GetComments($object_type, $object_id, $page, $perpage) {
        list($limit, $offset) = SiteService::GetLimitAndOffset($page, $perpage);
        $comments = (new Query())
                ->select(['c.*', 'u.fullname author', 'u.avatar author_avatar', 'u.slug author_slug'])
                ->from('comment as c')
                ->leftJoin('auth_user as u', 'c.created_by = u.id')
                ->where(['and', ['c.status' => self::$STATUS['ACTIVE']], ['c.delete' => self::$DELETE['ALIVE']]])
                ->andWhere(['and', ['c.object_type' => $object_type], ['c.object_id' => $object_id]])
                ->orderBy('c.created_at desc')
                ->limit($limit)->offset($offset)->all();

        $total = (new Query())
                ->select(['COUNT(*)'])
                ->from('comment')
                ->where(['and', ['status' => self::$STATUS['ACTIVE']], ['delete' => self::$DELETE['ALIVE']]])
                ->andWhere(['and', ['object_type' => $object_type], ['object_id' => $object_id]])
                ->column();

        return [$comments, $total[0]];
    }

    public static function Comment($object_type, $object_id, $content) {
        $userid = Yii::$app->user->id;
        $comment = new Comment([
            'content' => $content,
            'object_type' => $object_type,
            'object_id' => $object_id,
            'status' => self::$STATUS['PENDING'],
            'delete' => self::$DELETE['ALIVE'],
            'created_by' => $userid
        ]);

        if($comment->save()) {
            return true;
        }

        return false;
    }
} 