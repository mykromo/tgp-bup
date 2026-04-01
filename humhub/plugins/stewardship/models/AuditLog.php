<?php

namespace humhub\modules\stewardship\models;

use humhub\components\ActiveRecord;
use humhub\modules\user\models\User;
use Yii;
use yii\db\ActiveQuery;

class AuditLog extends ActiveRecord
{
    public static function tableName() { return 'stewardship_audit_log'; }

    public function rules()
    {
        return [
            [['space_id', 'entity_type', 'entity_id', 'action', 'user_id', 'created_at'], 'required'],
            [['space_id', 'entity_id', 'user_id'], 'integer'],
            [['entity_type'], 'string', 'max' => 50],
            [['action'], 'string', 'max' => 30],
            [['field_changed'], 'string', 'max' => 100],
            [['old_value', 'new_value'], 'string'],
            [['ip_address'], 'string', 'max' => 45],
        ];
    }

    public function getUser(): ActiveQuery { return $this->hasOne(User::class, ['id' => 'user_id']); }

    public static function log(int $spaceId, string $entityType, int $entityId, string $action,
        ?string $field = null, $oldValue = null, $newValue = null): void
    {
        $log = new static();
        $log->space_id = $spaceId;
        $log->entity_type = $entityType;
        $log->entity_id = $entityId;
        $log->action = $action;
        $log->field_changed = $field;
        $log->old_value = $oldValue !== null ? (string) $oldValue : null;
        $log->new_value = $newValue !== null ? (string) $newValue : null;
        $log->user_id = Yii::$app->user->isGuest ? 0 : Yii::$app->user->id;
        $log->ip_address = Yii::$app->request->getUserIP();
        $log->created_at = date('Y-m-d H:i:s');
        $log->save(false);
    }
}
