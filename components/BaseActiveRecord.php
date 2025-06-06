<?php
namespace bilipl\modules\UserManagement\components;

use bilipl\modules\UserManagement\helpers\LittleBigHelper;
use bilipl\modules\UserManagement\helpers\Singleton;
use webvimark\image\Image;
use yii\caching\TagDependency;
use yii\db\ActiveRecord;
use Yii;
use yii\db\Query;
use yii\helpers\FileHelper;
use yii\helpers\StringHelper;
use yii\validators\Validator;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

class BaseActiveRecord extends ActiveRecord
{
	/**
	 * For how long store cache in mainCache() function
	 */
	const COMMON_CACHE_TIME = 2592000; // 1 month

	/**
	 * If true, than afterSave() and afterDelete() cache dependency with static::getCacheTag() will be invalidated
	 *
	 * @var bool
	 */
	protected $_enable_common_cache = false;

	// ================= Timestamps config starts =================

	/**
	 * Replacement of the TimestampBehavior
	 *
	 * @var bool
	 */
	protected $_timestamp_enabled = false;

	/**
	 * Replacement of the TimestampBehavior
	 *
	 * @var array
	 */
	protected $_timestamp_attributes = [
		'create_attribute'  => 'created_at',
		'update_attribute'  => 'updated_at',
	];

	// ----------------- Timestamps config ends -----------------


	// ================= Multilingual config starts =================

	protected $_i18n_enabled = false;

	/**
	 * @var array
	 */
	protected $_i18n_attributes = [];

	/**
	 * @var array
	 */
	protected $_i18n_admin_routes = [];

	/**
	 * @var string
	 */
	protected $_i18n_table = 'ml_translations';

	// ----------------- Multilingual config ends -----------------


	/**
	 * "thumbDir"=>["dimensions"]
	 * If "dimensions" is not array, then image will be saved without resizing (in original size)
	 *
	 * @var array
	 */
	public $thumbs = [
		'full'   => null,
		'medium' => [300, 300],
		'small'  => [50, 50]
	];

	/**
	 * Used in flush cache after save and delete. So you can always can be sure that your cache is valid
	 *
	 * @return string
	 */
	public static function getCacheTag()
	{
		return get_called_class() . '_common_cache';
	}

	/**
	 * Reset class-specific cache by tag
	 */
	public static function resetCache()
	{
		TagDependency::invalidate(Yii::$app->cache, static::getCacheTag());
	}

	/**
	 * @param callable $callable
	 * @param array    $tags
	 *
	 * @return mixed
	 */
	public static function mainCache(callable $callable, $tags = [])
	{
		if ( $tags === [] )
		{
			$tags = static::getCacheTag();
		}

		return static::getDb()->cache($callable, static::COMMON_CACHE_TIME, new TagDependency(['tags'=>$tags]));
	}

	/**
	 * @inheritdoc
	 */
	public function __set($name, $value)
	{
		if ( $this->_i18n_enabled && in_array($name, $this->_getI18NAttributes()) )
			$this->$name = $value;
		else
			parent::__set($name, $value);
	}

	/**
	 * @inheritdoc
	 */
	public function attributes()
	{
		if ( $this->_i18n_enabled )
		{
			return array_merge(parent::attributes(), $this->_getI18NAttributes());
		}

		return parent::attributes();
	}


	public function init()
	{
		parent::init();

		$this->_I18NInitialization();
	}


	/**
	 * TimestampBehavior replacement
	 */
	protected function _timeStampBehavior()
	{
		if ( $this->_timestamp_enabled )
		{
			$this->{$this->_timestamp_attributes['update_attribute']} = time();

			if ( $this->isNewRecord )
			{
				$this->{$this->_timestamp_attributes['create_attribute']} = time();
			}
		}
	}

	// ================= I18N methods starts here =================

	/**
	 * @param boolean $insert
	 */
	private function _preventI18NOverwritingInNonAdminRoutes($insert)
	{
		if ( $this->_i18n_enabled && !$insert && !in_array(Yii::$app->requestedRoute, $this->_i18n_admin_routes) )
		{
			foreach ($this->_i18n_attributes as $i18nAttribute)
			{
				if ( isset($this->oldAttributes[$i18nAttribute]) )
				{

				}
			}

		}
	}

	/**
	 * Create string validators for virtual i18n attributes (like name_ru)
	 */
	private function _I18NInitialization()
	{
		if ( $this->_i18n_enabled )
		{
			$singletonKey = 'i18n_initialize' . get_class($this);

			$initialized = Singleton::getData($singletonKey);

			if ( !$initialized )
			{
				// Remove unnecessary "/" from admin routes
				array_walk($this->_i18n_admin_routes, function(&$val, $key) {
					$val = trim($val, '/');
				});

				foreach ($this->_getI18NAttributes() as $attribute)
				{
					$validators = $this->getActiveValidators($attribute);

					if ( empty($validators) )
					{
						$this->getValidators()
							->append(Validator::createValidator('string', $this, $attribute));
					}
				}

				Singleton::setData($singletonKey, true);
			}
		}
	}

	/**
	 * @param boolean $insert
	 */
	private function _saveI18NAttributes($insert)
	{
		if ( $this->_i18n_enabled )
		{
			foreach ($this->_getI18NAttributes() as $attribute)
			{
				if ( isset($this->$attribute) AND $this->$attribute !== null)
				{
					$tmp = explode('_', $attribute);

					$language = array_pop($tmp);

					$originalAttribute = implode('_', $tmp);

					// Update I18N attribute only if current route is in admin routes
					if ( !$insert )
					{
						$oldValue = (new Query())
							->select('value')
							->from($this->_i18n_table)
							->where([
								'table_name' => $this->getTableSchema()->fullName,
								'model_id'   => $this->primaryKey,
								'attribute'  => $originalAttribute,
								'lang'       => $language,
							])
							->limit(1)
							->scalar();

						if ( $oldValue === false AND $this->$attribute !== '' )
						{
							$this->_i18nInsertHelper($originalAttribute, $language, $attribute);
						}
						elseif ( $oldValue != $this->$attribute )
						{
							$this->_i18nUpdateHelper($originalAttribute, $language, $attribute);
						}
					}
					elseif ( $this->$attribute !== '' ) // Insert only non empty values
					{
						$this->_i18nInsertHelper($originalAttribute, $language, $attribute);
					}
				}
			}
		}
	}

	private function _i18nInsertHelper($originalAttribute, $language, $attribute)
	{
		$this->getDb()->createCommand()
			->insert($this->_i18n_table, [
				'table_name' => $this->getTableSchema()->fullName,
				'attribute'  => $originalAttribute,
				'model_id'   => $this->primaryKey,
				'lang'       => $language,
				'value'      => $this->$attribute,
			])
			->execute();
	}
	private function _i18nUpdateHelper($originalAttribute, $language, $attribute)
	{
		$this->getDb()->createCommand()
			->update($this->_i18n_table, ['value'=>$this->$attribute], [
				'table_name' => $this->getTableSchema()->fullName,
				'attribute'  => $originalAttribute,
				'model_id'   => $this->primaryKey,
				'lang'       => $language,
			])
			->execute();
	}

	/**
	 * Delete translated attributes
	 */
	private function _deleteI18NAttributes()
	{
		if ( $this->_i18n_enabled )
		{
			$this->getDb()->createCommand()
				->delete($this->_i18n_table, [
					'table_name' => $this->getTableSchema()->fullName,
					'model_id'   => $this->primaryKey,
				])
				->execute();
		}
	}

	private function _findI18NAttributes()
	{
		if ( $this->_i18n_enabled )
		{
			if ( php_sapi_name() === 'cli' || in_array(Yii::$app->requestedRoute, $this->_i18n_admin_routes) )
			{
				$translations = $this->mlGetTranslations();

				$replaceOriginalAttributes = false;
			}
			else
			{
				$translations = $this->mlGetLanguageSpecificTranslations();

				$replaceOriginalAttributes = Yii::$app->language !== Yii::$app->params['mlConfig']['default_language'];
			}

			foreach ($translations as $translate)
			{
				if ( $this->primaryKey == $translate['model_id'] )
				{
					if ( $replaceOriginalAttributes )
					{
						if ( $translate['lang'] == Yii::$app->language )
						{
							$this->{$translate['attribute']} = $translate['value'];
						}
					}
					elseif ( isset(Yii::$app->params['mlConfig']['languages'][$translate['lang']]) )
					{
						$attribute = $translate['attribute'] . '_' . $translate['lang'];

						$this->$attribute = $translate['value'];
					}
				}
			}
		}
	}

	/**
	 * @return array
	 */
	private function mlGetTranslations()
	{
		$values = Singleton::getData('_ml_' . $this->getTableSchema()->fullName);

		if ( $values === false )
		{
			$values = (new Query())
				->select(['model_id', 'attribute', 'value', 'lang'])
				->from($this->_i18n_table)
				->where([
					'table_name' => $this->getTableSchema()->fullName,
				])
				->all();

			Singleton::setData('_ml_' . $this->getTableSchema()->fullName, $values);
		}

		return $values;
	}


	/**
	 * @param null|string $language
	 *
	 * @return array
	 */
	private function mlGetLanguageSpecificTranslations($language = null)
	{
		if ( !$language )
			$language = Yii::$app->language;

		$values = Singleton::getData('_ml_' . $this->getTableSchema()->fullName . '_' . $language);

		if ( $values === false )
		{
			$values = (new Query())
				->select(['model_id', 'attribute', 'value', 'lang'])
				->from($this->_i18n_table)
				->where([
					'table_name' => $this->getTableSchema()->fullName,
					'lang'       => $language,
				])
				->all();

			Singleton::setData('_ml_' . $this->getTableSchema()->fullName . '_' . $language, $values);
		}

		return $values;
	}

	/**
	 * @return array
	 */
	private function _getI18NAttributes()
	{
		$singletonKey = '_mlAttributes_' . get_class($this);

		$mlAttributes = Singleton::getData($singletonKey);

		if ( $mlAttributes === false )
		{
			$mlAttributes = [];

			$languages = Yii::$app->params['mlConfig']['languages'];
//			unset($languages[Yii::$app->params['mlConfig']['default_language']]);

			foreach ($languages as $languageCode => $languageName)
			{
				foreach ($this->_i18n_attributes as $attribute)
				{
					$mlAttributes[] = $attribute . '_' . $languageCode;
				}
			}

			Singleton::setData($singletonKey, $mlAttributes);
		}

		return $mlAttributes;
	}


	// ----------------- I18N methods ends here -----------------

	/**
	 * @param mixed $condition
	 *
	 * @return bool
	 */
	public static function deleteIfExists($condition)
	{
		/** @var BaseActiveRecord $model */
		$model = static::findOne($condition);

		if ( $model )
		{
			$model->delete();
			return true;
		}

		return false;
	}


	/**
	 * Finds the model based on its primary key value.
	 * If the model is not found, a 404 HTTP exception will be thrown.
	 *
	 * @param mixed $condition
	 *
	 * @return static the loaded model
	 * @throws NotFoundHttpException if the model cannot be found
	 */
	public static function findOneOrException($condition)
	{
		if ( ($model = static::findOne($condition)) !== null )
		{
			return $model;
		}
		else
		{
			throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
		}
	}

	/**
	 * getUploadDir
	 *
	 * + Создаёт директории, если их нет
	 *
	 * @return string
	 */
	public function getUploadDir()
	{
		return Yii::getAlias('@webroot') . '/images/' . trim($this->tableName(), '{}%');
	}

	/**
	 * saveImage
	 *
	 * @param UploadedFile $file
	 * @param string        $imageName
	 */
	public function saveImage($file, $imageName)
	{
		if ( ! $file )
			return;

		$uploadDir = $this->getUploadDir();

		$this->prepareUploadDir($uploadDir);

		if ( is_array($this->thumbs) AND !empty($this->thumbs) )
		{

			foreach ($this->thumbs as $dir => $size)
			{
				$img = Image::factory($file->tempName);

				// If $size is array of dimensions - resize, else - just save
				if ( is_array($size) )
					$img->resize(implode(',', $size))->save($uploadDir . '/'. $dir . '/' . $imageName);
				else
					$img->save($uploadDir . '/'. $dir . '/' . $imageName);
			}

			@unlink($file->tempName);
		}
		else
		{
			$file->saveAs($uploadDir . '/' . $imageName);

		}
	}

	/**
	 * Delete image from all directories
	 *
	 * @param string $image
	 */
	public function deleteImage($image)
	{
		$uploadDir = $this->getUploadDir();

		if ( is_array($this->thumbs) AND !empty($this->thumbs) )
		{
			foreach (array_keys($this->thumbs) as $thumbDir)
				@unlink($uploadDir.'/'.$thumbDir.'/'.$image);
		}
		else
		{
			@unlink($uploadDir.'/'.$image);
		}

		// Delete all cropped images from this object
		if ( is_dir($uploadDir . '/_cropped') )
		{
			$croppedImages = FileHelper::findFiles($uploadDir.'/_cropped', [
				'only'   => ['*_|_' . $image],
			]);

			foreach ($croppedImages as $croppedImage)
			{
				@unlink($croppedImage);
			}
		}
	}

	/**
	 * Provide array of image fields (like: ['logo', 'image'])
	 * then $model->$imageField will be deleted from all thumb directories (or main dir if there are no thumbs)
	 *
	 * @param array $imageFields
	 */
	public function bulkDeleteImages($imageFields)
	{
		foreach ($imageFields as $imageField)
		{
			$this->deleteImage($this->$imageField);
		}
	}

	/**
	 * getImageUrl
	 *
	 * @param string|null $dir
	 * @param string $attr
	 * @return string
	 */
	public function getImageUrl($dir = 'full', $attr = 'image')
	{
		if ( $dir )
			return Yii::$app->request->baseUrl . '/images/'.trim($this->tableName(), '{}%')."/{$dir}/".$this->{$attr};
		else
			return Yii::$app->request->baseUrl . '/images/'.trim($this->tableName(), '{}%').'/'.$this->{$attr};
	}

	/**
	 * getImagePath
	 *
	 * @param string|null $dir
	 * @param string $attr
	 * @return string
	 */
	public function getImagePath($dir = 'full', $attr = 'image')
	{
		if ( $dir )
			return $this->getUploadDir() . "/{$dir}/".$this->{$attr};
		else
			return $this->getUploadDir() . '/'.$this->{$attr};
	}

	/**
	 * @param integer $width
	 * @param integer $height
	 * @param string  $attr
	 *
	 * @return string
	 */
	public function getCroppedImagePath($width, $height, $attr = 'image')
	{
		$dir = $this->getUploadDir() . '/_cropped';

		if (! is_dir($dir))
		{
			mkdir($dir, 0777, true);
			chmod($dir, 0777);
		}

		return $dir . '/' . $width . '_' . $height . '_|_' . $this->{$attr};
	}

	/**
	 * @param integer       $width
	 * @param integer     $height
	 * @param string $dir
	 * @param string $attr
	 *
	 * @return string
	 */
	public function getCroppedImage($width, $height, $dir = 'full', $attr = 'image')
	{
		if ( !is_file($this->getCroppedImagePath($width, $height, $attr)) && is_file($this->getImagePath($dir, $attr)) )
		{
			$image = Image::factory($this->getImagePath($dir, $attr));

			$old_aspect = $image->width / $image->height;
			$new_aspect = $width / $height;

			if ($old_aspect == 1)
			{
				if ($width > $height)
				{
					$image->resize($width, $height, Image::WIDTH);
				}
				else
				{
					$image->resize($width, $height, Image::HEIGHT);
				}
			}
			elseif ($old_aspect < $new_aspect)
			{
				$image->resize($width, $height, Image::WIDTH);
			}
			else
			{
				$image->resize($width, $height, Image::HEIGHT);
			}

			$image->crop($width, $height);
			$image->save($this->getCroppedImagePath($width, $height, $attr));
		}

		return Yii::$app->request->baseUrl . '/images/' . trim($this->tableName(), '{}%') . '/_cropped/' .  $width . '_' . $height . '_|_' . $this->{$attr};
	}

	//=========== Rules ===========

	public function purgeXSS($attr)
	{
		$this->$attr = htmlspecialchars($this->$attr, ENT_QUOTES);
	}

	//----------- Rules -----------



	/**
	 * prepareUploadDir
	 *
	 * @param string $dir
	 */
	public function prepareUploadDir($dir)
	{
		if (! is_dir($dir))
		{
			mkdir($dir, 0777, true);
			chmod($dir, 0777);
		}

		// Если нужны папки с thumbs
		if ( is_array($this->thumbs) AND !empty($this->thumbs) )
		{
			foreach (array_keys($this->thumbs) as $thumbDir)
			{
				if (! is_dir($dir.'/'.$thumbDir))
				{
					mkdir($dir.'/'.$thumbDir, 0777, true);
					chmod($dir.'/'.$thumbDir, 0777);
				}
			}
		}
	}

	/**
	 * @param UploadedFile $file
	 *
	 * @return string
	 */
	public function generateFileName($file)
	{
		return uniqid() . '_' . LittleBigHelper::slug($file->baseName, '_') . '.' . $file->extension;
	}


	/**
	 * Check if some attributes uploaded via fileInput field
	 * and assign them with UploadedFile
	 *
	 * @inheritdoc
	 */
	public function setAttributes($values, $safeOnly = true)
	{
		parent::setAttributes($values, $safeOnly);

		// Looking only for file attributes (and fix null error on fly)
		if ( is_array($values) )
		{
			$attributes = array_flip($safeOnly ? $this->safeAttributes() : $this->attributes());

			$class = StringHelper::basename(get_called_class());

			foreach ($values as $name => $value)
			{
				if ( isset( $attributes[$name] ) )
				{
					if ( isset($_FILES[$class]['name'][$name]) )
					{
						$uploadedFile = UploadedFile::getInstance($this, $name);

						if ( $uploadedFile )
						{
							$this->$name = $uploadedFile;
						}
						elseif ( ! $this->isNewRecord )
						{
							$this->$name = $this->oldAttributes[$name];
						}
					}
				}
			}
		}
	}

	// ================= Events =================


	/**
	 * @inheritdoc
	 */
	public function beforeSave($insert)
	{
		if ( parent::beforeSave($insert) )
		{
			$this->_timeStampBehavior();

//			$this->_preventI18NOverwritingInNonAdminRoutes($insert);

			foreach ($this->attributes as $name => $val)
			{
				if ( $val instanceof UploadedFile )
				{
					if ( $val->name AND !$val->hasError )
					{
						$fileName = $this->generateFileName($val);

						if ( !$this->isNewRecord )
						{
							$this->deleteImage($this->oldAttributes[$name]);
						}

						$this->saveImage($val, $fileName);

						$this->$name = $fileName;
					}
					elseif ( !$this->isNewRecord )
					{
						$this->$name = $this->oldAttributes[$name];
					}
				}
			}

			return true;
		}

		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function afterDelete()
	{
		$this->_deleteI18NAttributes();

		if ( $this->_enable_common_cache )
		{
			TagDependency::invalidate(Yii::$app->cache, static::getCacheTag());
		}

		parent::afterDelete();
	}

	/**
	 * @inheritdoc
	 */
	public function afterSave($insert, $changedAttributes)
	{
		$this->_saveI18NAttributes($insert);

		if ( $this->_enable_common_cache )
		{
			TagDependency::invalidate(Yii::$app->cache, static::getCacheTag());
		}

		parent::afterSave($insert, $changedAttributes);
	}

	/**
	 * @inheritdoc
	 */
	public function afterFind()
	{
		$this->_findI18NAttributes();

		parent::afterFind();
	}

}
