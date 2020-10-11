<?php

namespace common\models;

use OAuth2\Storage\UserCredentialsInterface;
use tecnocen\oauth2server\models\OauthAccessTokens;
use tecnocen\oauth2server\Module;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;



/**
 * This is the model class for table "user".
 *
 * @property int $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property int $status
 * @property int $created_at
 * @property int $updated_at
 *
 * @property AccessToken[] $accessTokens
 * @property AudiobookRating[] $audiobookRatings
 * @property UserSsoProvider[] $auths
 */
class User extends ActiveRecord implements IdentityInterface, UserCredentialsInterface
{
    /**
     * @var string the class name of the [[identity]] object.
     */
    public $identityClass;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                ['username', 'auth_key', 'password_hash', 'password_reset_token', 'email'],
                'required'
            ],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['username', 'auth_key', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::className(),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getAccessTokens()
    {
        return $this->hasMany(AccessToken::className(), ['id_user' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getAudiobookRatings()
    {
        return $this->hasMany(AudiobookRating::className(), ['id_user' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getAuths()
    {
        return $this->hasMany(UserSsoProvider::className(), ['user_id' => 'id']);
    }

    public function getIdentity()
    {

    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface the identity object that matches the given ID.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentity($id)
    {
        $model = User::findOne($id);
        if (!$model) {
            return null;
        } else {
            return $model;
        }
        // TODO: Implement findIdentity() method.
    }

    /**
     * Finds an identity by the given token.
     * @param mixed $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * For example, [[\yii\filters\auth\HttpBearerAuth]] will set this parameter to be `yii\filters\auth\HttpBearerAuth`.
     * @return IdentityInterface the identity object that matches the given token.
     * Null should be returned if such an identity cannot be found
     * or the identity is not in an active state (disabled, deleted, etc.)
     */
    public static function findIdentityByAccessToken($accessToken, $type = null)
    {
        /** @var Module $module */
//        $module = Yii::$app->getModule('oauth2');
//        $module->initOauth2Server();
//        $oauthServer = $module->getServer();

        $accessTokenFound = OauthAccessTokens::find()
                                             ->where([
                                                 'access_token' => $accessToken,
                                             ])
                                             ->andWhere('expires > NOW()')
                                             ->one();
        if ($accessTokenFound) {
            return static::findIdentity($accessTokenFound->user_id);
        }
        // @TODO extend token lifetime if found

        return null;
    }

    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     *
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     *
     * The space of such keys should be big enough to defeat potential identity attacks.
     *
     * This is required if [[User::enableAutoLogin]] is enabled. The returned key will be stored on the
     * client side as a cookie and will be used to authenticate user even if PHP session has been expired.
     *
     * Make sure to invalidate earlier issued authKeys when you implement force user logout, password change and
     * other scenarios, that require forceful access revocation for old sessions.
     *
     * @return string a key that is used to check the validity of a given identity ID.
     * @see validateAuthKey()
     */
    public function getAuthKey()
    {
        // TODO: Implement getAuthKey() method.
    }

    /**
     * Validates the given auth key.
     *
     * This is required if [[User::enableAutoLogin]] is enabled.
     * @param string $authKey the given auth key
     * @return bool whether the given auth key is valid.
     * @see getAuthKey()
     */
    public function validateAuthKey($authKey)
    {
        // TODO: Implement validateAuthKey() method.
    }

    /**
     * Grant access tokens for basic user credentials.
     *
     * Check the supplied username and password for validity.
     *
     * You can also use the $client_id param to do any checks required based
     * on a client, if you need that.
     *
     * Required for OAuth2::GRANT_TYPE_USER_CREDENTIALS.
     *
     * @param $username
     * Username to be check with.
     * @param $password
     * Password to be check with.
     *
     * @return
     * TRUE if the username and password are valid, and FALSE if it isn't.
     * Moreover, if the username and password are valid, and you want to
     *
     * @see http://tools.ietf.org/html/rfc6749#section-4.3
     *
     * @ingroup oauth2_section_4
     */
    public function checkUserCredentials($username, $password)
    {
        $model = User::findOne(['username' => $username]);
        if ($model && Yii::$app->getSecurity()->validatePassword($password, $model->password_hash)) {
            return true;
        }
        return false;
    }

    /**
     * @param string $username - username to get details for
     * @return array|false     - the associated "user_id" and optional "scope" values
     *                           This function MUST return FALSE if the requested user does not exist or is
     *                           invalid. "scope" is a space-separated list of restricted scopes.
     * @code
     *     return array(
     *         "user_id"  => USER_ID,    // REQUIRED user_id to be stored with the authorization code or access token
     *         "scope"    => SCOPE       // OPTIONAL space-separated list of restricted scopes
     *     );
     * @endcode
     */
    public function getUserDetails($username)
    {
        $model = User::findOne(['username' => $username]);
        if (!$model) {
            return false;
        }
        return [
            'user_id' => $model->id,
        ];
    }
}
